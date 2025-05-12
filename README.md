# RA Ultimate Cipher Analyzer
RA Ultimate Cipher Analyzer is a web-based tool designed to analyze encrypted files and suggest the encryption algorithm used based on statistical heuristic analysis and known patterns. 

Although it is impossible to know exactly what type of encryption may have been applied to a given file, RA Ultimate Cypher Analyzer manages to perform the necessary operations to detect the type of encryption applied to a given file, obtaining very good performance in the detection of known patterns and determined by its library of patterns that can be expanded according to the documentation.

RA Ultimate Cipher Analyzer is a web-based tool designed to analyze encrypted files and suggest the encryption algorithm used based on statistical properties and known patterns. It calculates metrics such as entropy, byte distribution (chi-square test), and file size to identify characteristics of strong ciphers (e.g., AES, RSA) and weak ciphers (e.g., Caesar). The tool detects specific patterns associated with common encryption formats (e.g., OpenSSL, PGP/GPG) and handles Base64-encoded data, making it suitable for both binary and encoded files.

The tool provides a user-friendly interface for uploading files and displays detailed analysis results, including a table of expected entropy values for various ciphers. It is implemented as a PHP class (RA_UltimateCipherAnalyzer) for modularity and extensibility, making it ideal for cybersecurity professionals, developers, and researchers.


# RA Ultimate Cipher Analyzer

A web-based PHP tool to analyze encrypted files and suggest encryption algorithms using statistical metrics (entropy, chi-square, block size) and pattern recognition. Supports binary and Base64-encoded files, with a detailed entropy table for reference.

## Features
- File upload support for encrypted files (e.g., AES, RSA, ZIP).
- Entropy calculation (e.g., 7.9-8.0 for AES, 5.5-6.0 for RSA Base64).
- Chi-square byte distribution analysis.
- Block size detection (e.g., 16 bytes for AES).
- Pattern recognition (e.g., OpenSSL, RSA PKCS#1, PGP/GPG).
- Base64 handling with decoded pattern analysis.
- Educational entropy table comparing cipher types.

## Installation
1. Set up a web server with PHP 7.0+ and `file_uploads = On`.
2. Create a writable `uploads/` directory (e.g., `chmod 755 uploads`).
3. Copy `index.php`, `RA_UltimateCipherAnalyzer.php`, and `documentation.html` to the server root.
4. Access `http://<server>/index.php` to use the tool.

## Usage
- Upload an encrypted file via the web interface.
- View analysis results, including entropy, patterns, and block size.
- Compare entropy with the provided table (e.g., RSA Base64: 5.5-6.0 bits/byte).

## Documentation
See [RA_Ultimate_Cipher_Analyzer_Documentation.html](RA_Ultimate_Cipher_Analyzer_Documentation.html) for detailed setup, usage, and technical details.

## License
Licensed under the AGPL. See [LICENSE](LICENSE) for details.

**IMPORTANT: PLEASE READ CAREFULLY BEFORE USING THIS TOOL** This tool, RA Ultimate Cipher Analyzer, is provided 

**solely for educational, research, and authorized security testing purposes.** It is intended to assist security professionals and researchers in analyzing encrypted files with proper permission. 

**USE OF THIS TOOL FOR ANY ILLEGAL OR UNAUTHORIZED ACTIVITY IS STRICTLY PROHIBITED.** The developer is not responsible for any misuse of this tool. 

**YOU ASSUME FULL RESPONSIBILITY** for the consequences of using this tool. The accuracy of cipher identification is not guaranteed, and results should be interpreted with caution. By using this tool, you agree to comply with all applicable laws and regulations and acknowledge that you have read and understood this disclaimer in its entirety. If you do not agree to these terms, please do not use this tool.

## Contact
Issues or suggestions? Open an issue or contact ventics.com.
