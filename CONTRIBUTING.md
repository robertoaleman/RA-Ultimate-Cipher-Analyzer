# Contributing to RA Ultimate Cipher Analyzer

Thank you for your interest in contributing! Here’s how you can help:

## Adding New Ciphers
- Update `$entropyTable` in `analyze.php` with new cipher details.
- Add patterns to `checkForKnownPatterns` (e.g., `$patterns['NewCipher'] = strpos($data, "\xFF\x00") === 0;`).
- Submit a pull request with your changes.

## Reporting Issues
- Open an issue on GitHub with details about bugs or feature requests.

## Code Style
- Follow PHP coding standards (e.g., PSR-12).
- Include comments for new methods or logic.

Questions? Contact [ventics.com].
