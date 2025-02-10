# Email Validation API

Try out: http://validator.free.nf/

This project provides an Email Validation API built using PHP. It accepts an array of email addresses via a POST request in JSON format, performs multiple email validation checks (format, MX records, and SMTP validation), and returns the results in JSON format. 

The API is designed to verify whether the emails follow proper syntax, whether the domain has valid mail exchange records (MX), and whether the email can be validated via SMTP.

Additionally, a test page (HTML + JavaScript) has been created to interact with the API and visualize the results in a user-friendly manner.

## Features

- **Email Format Validation**: Checks whether the email follows proper syntax using PHP's `filter_var()` function.
- **MX Record Validation**: Verifies if the email domain has valid Mail Exchange (MX) records using PHP's `checkdnsrr()` function.
- **SMTP Validation**: Attempts to verify whether the email address is valid by connecting to an SMTP server (using PHPMailer). This step requires SMTP authentication and may not always be successful due to privacy and security restrictions.

## Installation

To use this project, you need a PHP-enabled server to run the script and handle requests. Follow these steps to set it up:

### Requirements:
- A web server (e.g., Apache, Nginx, etc.)
- PHP 7.4 or higher
- PHPMailer (for SMTP validation)

### Steps:

1. **Download the project files**:
   - Download or clone this repository to your local machine or server.

2. **Set up the PHP Script**:
   - Place the `validate_emails.php` file in the appropriate folder on your server (e.g., in the root or a subdirectory).
   - Ensure that the server has PHP installed and configured correctly.

4. **Configure SENDER EMAIL Details**:
   - Open the `validate_emails.php` script and update the SMTP credentials (email) in the `validateSMTP()` function. These credentials are required to send varification request to SMTP server for email validation.
   - add you email in $from variable at 58th line of `validate_emails.php`.

## Usage

### Using the API:

The API accepts POST requests with a JSON array of email addresses, processes them, and returns a JSON response.

- **Endpoint**: `/validate_emails.php`
- **Method**: `POST`
- **Parameters**: 
  - `emails`: A JSON array of email addresses (e.g., `["email1@example.com", "email2@example.com"]`)

Example of a valid POST request:

```bash
POST /validate_emails.php
Content-Type: application/x-www-form-urlencoded

emails=["email1@example.com", "invalid-email"]
