<?php

/*  Author: Roberto Aleman, ventics.com, version 1.0.2025
Need to build and deploy software? Write to me  in ventics.com  

IMPORTANT: PLEASE READ CAREFULLY BEFORE USING THIS TOOL This tool, RA Ultimate Cipher Analyzer, is provided 
solely for educational, research, and authorized security testing purposes.
It is intended to assist security professionals and researchers in analyzing encrypted files with proper permission. 

USE OF THIS TOOL FOR ANY ILLEGAL OR UNAUTHORIZED ACTIVITY IS STRICTLY PROHIBITED.  
The developer is not responsible for any misuse of this tool. 

YOU ASSUME FULL RESPONSIBILITY  for the consequences of using this tool. 
The accuracy of cipher identification is not guaranteed, and results should be interpreted with caution. 
By using this tool, you agree to comply with all applicable laws and regulations and acknowledge that 
you have read and understood this disclaimer in its entirety. 
If you do not agree to these terms, please do not use this tool.
.*/

class RA_UltimateCipherAnalyzer {
    // Entropy table as a class property
    private $entropyTable = [
        ["AES (128/192/256)", "Binary", "7.9 - 8.0", "Strong cipher, uniform distribution, typical of random binary data."],
        ["AES (128/192/256)", "Base64", "5.5 - 6.0", "Reduced by Base64, but high within this format's range."],
        ["DES", "Binary", "7.8 - 8.0", "Less secure than AES, but still produces high entropy."],
        ["Triple DES (3DES)", "Binary", "7.8 - 8.0", "Similar to DES, high entropy, but slower."],
        ["Blowfish", "Binary", "7.8 - 8.0", "Strong symmetric cipher, similar to AES in randomness."],
        ["RSA", "Binary", "7.8 - 8.0", "Encrypted data or keys in DER format, high randomness."],
        ["RSA", "Base64", "5.5 - 6.0", "Common for keys or encrypted messages, entropy limited by Base64."],
        ["ElGamal", "Binary", "7.8 - 8.0", "Asymmetric cipher, similar to RSA in randomness."],
        ["ElGamal", "Base64", "5.5 - 6.0", "Similar to RSA in Base64."],
        ["Caesar Cipher", "Plaintext", "4.0 - 5.0", "Weak cipher, entropy similar to plaintext (e.g., English)."],
        ["Encrypted ZIP", "Binary", "7.5 - 8.0", "Depends on internal algorithm (e.g., AES), but high entropy."],
        ["Encrypted RAR", "Binary", "7.5 - 8.0", "Similar to ZIP, high entropy due to compression and encryption."],
        ["Encrypted PDF", "Binary", "6.5 - 7.5", "Lower entropy due to metadata and PDF structure."],
        ["Plaintext (English)", "Plaintext", "4.0 - 5.0", "Low entropy due to linguistic redundancy."],
        ["Plaintext (Source Code)", "Plaintext", "5.0 - 6.0", "Higher entropy than natural text, but lower than encrypted data."],
        ["Compressed Data (Unencrypted)", "Binary", "6.5 - 7.5", "High entropy due to compression, but lower than strong ciphers."]
    ];

    // Check if data is valid Base64
    private function isBase64($data) {
        return preg_match('/^[A-Za-z0-9+\/=]+$/', $data) && base64_decode($data, true) !== false;
    }

    // Calculate entropy
    private function calculateEntropy($data) {
        $length = strlen($data);
        $frequency = array_fill(0, 256, 0);
        for ($i = 0; $i < $length; $i++) {
            $frequency[ord($data[$i])]++;
        }
        $entropy = 0.0;
        foreach ($frequency as $freq) {
            if ($freq > 0) {
                $p = $freq / $length;
                $entropy -= $p * log($p, 2);
            }
        }
        return $entropy;
    }

    // Analyze byte distribution (chi-square test)
    private function byteDistribution($data) {
        $length = strlen($data);
        $frequency = array_fill(0, 256, 0);
        for ($i = 0; $i < $length; $i++) {
            $frequency[ord($data[$i])]++;
        }
        $expected = $length / 256;
        $chiSquare = 0.0;
        foreach ($frequency as $freq) {
            $chiSquare += pow($freq - $expected, 2) / $expected;
        }
        return $chiSquare;
    }

    // Check for known patterns
    private function checkForKnownPatterns($data, $isBase64 = false) {
        $patterns = [
            'オープンSSL' => strpos($data, 'Salted__') === 0,
            'PGP/GPG' => strpos($data, "\x99\x01") === 0 || strpos($data, "\x85\x02") === 0,
            'RSA (PKCS#1)' => strpos($data, "\x30\x82") === 0,
            'PEM (Encrypted Data)' => strpos($data, '-----BEGIN ENCRYPTED DATA-----') === 0,
            'Encrypted ZIP' => strpos($data, "PK\x03\x04") === 0 && (ord($data[6]) & 1) === 1,
            'Encrypted RAR' => strpos($data, "Rar!\x1A\x07\x01\x00") === 0,
            'Encrypted PDF' => strpos($data, '%PDF-') === 0 && strpos($data, '/Encrypt') !== false,
        ];

        $suggestions = [];
        foreach ($patterns as $algorithm => $found) {
            if ($found) {
                $suggestions[] = "Possibly encrypted with $algorithm";
            }
        }

        // If Base64, decode and check for patterns
        if ($isBase64) {
            $decoded = base64_decode($data, true);
            if ($decoded !== false) {
                if (strpos($decoded, "\x30\x82") === 0) {
                    $suggestions[] = 'Possibly encrypted with RSA (PKCS#1, decoded from Base64)';
                }
            }
            $suggestions[] = 'Data in Base64 format: entropy and distribution reflect encoding, not encryption';
        }

        return empty($suggestions) ? 'No known patterns found' : implode(', ', $suggestions);
    }

    // Check file size for block ciphers
    private function checkBlockSize($fileSize, $entropy, $isBase64) {
        if ($isBase64) {
            return 'Not applicable: RSA does not use block encryption (Base64 data)';
        }
        if ($entropy > 7.9) {
            // Prioritize AES (16 bytes) if entropy is high
            if ($fileSize % 16 == 0) {
                return 'Possible block encryption with 16 bytes (likely AES)';
            }
        }
        $blockSizes = [16, 8, 32];
        foreach ($blockSizes as $size) {
            if ($fileSize % $size == 0) {
                return "Possible block encryption with $size bytes";
            }
        }
        return 'No common block size detected';
    }

    // Main analysis method
    private function analyzeFile($filePath) {
        $data = file_get_contents($filePath);
        $fileSize = filesize($filePath);

        $suggestions = [];

        // Check if data is Base64
        $isBase64 = $this->isBase64(trim($data));
        if ($isBase64) {
            $suggestions[] = 'Format detected: Base64';
        }

        // Check minimum file size
        if ($fileSize < 1024) {
            $suggestions[] = 'Warning: File is too small (<1 KB), results may be unreliable';
        }

        // Calculate entropy
        $entropy = $this->calculateEntropy($data);
        $suggestions[] = sprintf('Entropy: %.2f bits/byte', $entropy);
        if ($isBase64) {
            if ($entropy > 5.5) {
                $suggestions[] = 'High entropy for Base64: likely encrypted with a secure algorithm (e.g., RSA)';
            } else {
                $suggestions[] = 'Low entropy for Base64: may be a short message or unencrypted';
            }
        } else {
            if ($entropy > 7.9) {
                $suggestions[] = 'High entropy: likely encrypted with a secure algorithm (e.g., AES, RSA)';
            } else {
                $suggestions[] = 'Low entropy: may be unencrypted or using a weak cipher (e.g., Caesar)';
            }
        }

        // Analyze byte distribution
        if (!$isBase64) {
            $chiSquare = $this->byteDistribution($data);
            $suggestions[] = sprintf('Chi-square: %.2f', $chiSquare);
            if ($chiSquare < 293.25) {
                $suggestions[] = 'Uniform distribution: typical of strong ciphers';
            } else {
                $suggestions[] = 'Non-uniform distribution: possible weak cipher, plaintext, or small file';
            }
        } else {
            $suggestions[] = 'Non-uniform distribution (expected for Base64): not applicable for cipher analysis';
        }

        // Check for known patterns
        $pattern = $this->checkForKnownPatterns($data, $isBase64);
        if ($pattern !== 'No known patterns found') {
            $suggestions[] = $pattern;
        }

        // Check block size
        $suggestions[] = $this->checkBlockSize($fileSize, $entropy, $isBase64);

        // Show file size
        $suggestions[] = sprintf('File size: %d bytes', $fileSize);

        return $suggestions;
    }

    // Render entropy table as HTML
    private function renderEntropyTable() {
        $html = "<h2>Expected Entropy by Cipher Type</h2>";
        $html .= "<table border='1'>";
        $html .= "<tr><th>Cipher/Format</th><th>Output Format</th><th>Expected Entropy (bits/byte)</th><th>Notes</th></tr>";
        foreach ($this->entropyTable as $row) {
            $html .= "<tr><td>{$row[0]}</td><td>{$row[1]}</td><td>{$row[2]}</td><td>{$row[3]}</td></tr>";
        }
        $html .= "</table>";
        return $html;
    }

    // Process uploaded file and return HTML output
    public function processFile($file) {
        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return '<p>Error uploading file.</p>';
        }

        // Move file to temporary location
        $tempPath = $file['tmp_name'];
        $filePath = 'uploads/' . basename($file['name']);
        if (!move_uploaded_file($tempPath, $filePath)) {
            return '<p>Error moving file.</p>';
        }

        // Perform analysis
        $suggestions = $this->analyzeFile($filePath);

        // Generate HTML output
        $html = "<h2>Analysis Results:</h2>";
        $html .= "<ul>";
        foreach ($suggestions as $suggestion) {
            $html .= "<li>$suggestion</li>";
        }
        $html .= "</ul>";
        $html .= $this->renderEntropyTable();

        // Delete file after analysis
        unlink($filePath);

        return $html;
    }
}

// Process uploaded file
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $analyzer = new RA_UltimateCipherAnalyzer();
    echo $analyzer->processFile($_FILES['file']);
} else {
    echo '<p>No file uploaded.</p>';
}
?>
<style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        ul { list-style-type: disc; margin-left: 20px; }
    </style>
