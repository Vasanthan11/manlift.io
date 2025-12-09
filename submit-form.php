<?php
// submit-form.php

// OPTIONAL: Turn off notice-level errors in production
error_reporting(E_ALL & ~E_NOTICE);

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: thank-you.html');
    exit;
}

// Helper function to sanitize text fields
function clean_input($value) {
    return trim(filter_var($value, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

// Collect and sanitize form data
$fullName = clean_input($_POST['fullName'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = clean_input($_POST['phone'] ?? '');
$location = clean_input($_POST['location'] ?? '');
$equipment = clean_input($_POST['equipment'] ?? '');
$message  = clean_input($_POST['message'] ?? '');

// Basic required field validation (Full Name + Phone)
if ($fullName === '' || $phone === '') {
    // You can customise this behaviour (redirect back with query string, etc.)
    echo "<h2>Required fields are missing.</h2>";
    echo "<p>Please go back and make sure you’ve entered your Full Name and Contact Number.</p>";
    exit;
}

// Validate email if provided
$emailHeader = '';
if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
    // Use a safe email value for the From header
    $safeEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
    $emailHeader = "Reply-To: {$safeEmail}\r\n";
}

// Email settings
$to      = 'info@manlifts.co.in'; // Replace with your actual receiving email if different
$subject = 'New Manlifts Online Rental Enquiry';

// Build the email body
$bodyLines = [
    "You have received a new online rental enquiry from the Manlifts website.",
    "",
    "Full Name: {$fullName}",
    "Email: " . ($email !== '' ? $email : 'Not provided'),
    "Contact Number: {$phone}",
    "Site Location / Area: " . ($location !== '' ? $location : 'Not provided'),
    "Equipment Type Required: " . ($equipment !== '' ? $equipment : 'Not specified'),
    "",
    "Project Details / Height & Duration:",
    $message !== '' ? $message : 'Not provided',
    "",
    "----",
    "This enquiry was sent from the online rental enquiry form."
];

$body = implode("\r\n", $bodyLines);

// Build headers
$headers   = "MIME-Version: 1.0\r\n";
$headers  .= "Content-type: text/plain; charset=UTF-8\r\n";
$headers  .= "From: Manlifts Website <no-reply@manlifts.co.in>\r\n";
$headers  .= $emailHeader;

// Send the email
$mailSent = @mail($to, $subject, $body, $headers);

// Redirect to thank you page regardless of success (optional)
// You can log errors server-side if needed.
header('Location: thank-you.html');
exit;
