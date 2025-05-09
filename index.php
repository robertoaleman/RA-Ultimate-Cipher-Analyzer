<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultimate Cipher Analyzer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        ul { list-style-type: disc; margin-left: 20px; }
    </style>
</head>
<body>
<h1>Ultimate Cipher Analyzer</h1>
<p>Author: Roberto Aleman, ventics.com, version 1.0.2025</p>
    <h2>Upload an encrypted file to analyze</h2>
    <form action="RA_UltimateCipherAnalyzer.php" method="post" enctype="multipart/form-data">
        <label for="file">Select a file:</label>
        <input type="file" name="file" id="file" required>
        <br><br>
        <input type="submit" value="Analyze">
    </form>

    <p>Need to build and deploy software? Write to me  in ventics.com</p>
</body>
</html>