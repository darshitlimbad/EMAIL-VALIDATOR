<?php
// Check if the POST request contains the 'emails' field
if (!isset($_POST['emails'])) {
    // If the 'emails' field is not provided in the POST request
    echo json_encode(['error' => 'No emails provided']);
    exit; // Exit immediately as there's no need to process further
}

// Decode the JSON data into a PHP array
$emails = json_decode($_POST['emails'], true);

// Check if the decoded data is a valid array
if (!is_array($emails)) {
    // If the JSON is not a valid array
    echo json_encode(['error' => 'The provided JSON is not a valid array of emails']);
    exit; // Exit immediately as there's no need to process further
}

$results = [];

// Loop through each email and validate
foreach ($emails as $email) {
    $formatStatus = validateFormat($email);
    $mxStatus = validateMX($email);
    $smtpStatus = validateSMTP($email);

    // Overall status: If all checks are valid, set to "valid"; otherwise, "invalid"
    $overallStatus = ($formatStatus === 'valid' && $mxStatus === 'valid' && $smtpStatus === 'valid') 
                     ? 'valid' 
                     : 'invalid';

    $results[$email] = [
        'format' => $formatStatus,
        'mx' => $mxStatus,
        'smtp' => $smtpStatus,
        'status' => $overallStatus // New overall validation status
    ];
}

// Return results as a JSON response
echo json_encode($results);

// Function to validate email format
function validateFormat($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? 'valid' : 'invalid';
}

// Function to check MX records for the email domain
function validateMX($email) {
    $domain = substr(strrchr($email, "@"), 1); // Extract domain from email
    if (!$domain) return 'invalid';
    return checkdnsrr($domain, "MX") ? 'valid' : 'invalid';
}

// Function to validate email using SMTP and check current status
function validateSMTP($email) {
    $timeout = 10;
    $from = "your-email-example@gmail.com"; // Your valid email address

    // Extract domain from email
    $domain = substr(strrchr($email, "@"), 1);
    
    // Get MX records for the domain
    $mxRecords = [];
    if (!getmxrr($domain, $mxRecords)) {
        return 'invalid'; // No MX records found
    }

    // Try connecting to the first MX server
    $smtpServer = $mxRecords[0];
    $port = 25; // Use 587 or 465 if blocked

    $socket = fsockopen($smtpServer, $port, $errno, $errstr, $timeout);
    if (!$socket) {
        return "Connection failed: $errstr ($errno)";
    }

    // Read server response
    fgets($socket, 1024);
    
    // SMTP handshake
    fwrite($socket, "HELO validator.free.nf \r\n");
    fgets($socket, 1024);

    fwrite($socket, "MAIL FROM: <$from>\r\n");
    fgets($socket, 1024);

    // Check recipient
    fwrite($socket, "RCPT TO: <$email>\r\n");
    $response = fgets($socket, 1024);

    fwrite($socket, "VRFY $email\r\n");
    $vrfyResponse = fgets($socket);
    
    // Close connection
    fwrite($socket, "QUIT\r\n");
    fclose($socket);

    // If response contains "250", the email exists
    if(strpos($response, "250") === false) return 'invalid';

    // If VRFY is disabled and response contains "250", the email exists
    if( (strpos($vrfyResponse, "502") === true) or (strpos($vrfyResponse,"550")=== true) and (strpos($response, "250") === true)) return "valid";

    if ((strpos($vrfyResponse, "252") === true) and (strpos($response, "250") === true)) return "valid";
    
    // If VRFY returns 250 all okay response contains "250", the email exists
    return (strpos($response, "250") !== false) ? 'valid' : 'invalid';
}
?>
