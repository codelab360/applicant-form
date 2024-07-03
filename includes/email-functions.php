<?php 

function send_mail($email_address, $type, $args = array()) {
    switch ($type) {
        case 'confirmation_email':
            return send_confirmation_email($email_address, $args);
        default:
            return false; // Handle unsupported email types
    }
}

function send_confirmation_email($email_address, $args = array()) {
    $subject = 'Application Submission Confirmation';
    $message = 'Dear ' . $args['first_name'] . ',<br><br>';
    $message .= 'Thank you for submitting your application. We have received your application and will review it shortly.<br><br>';
    $message .= 'Best regards,<br>';
    $message .= 'HR Department';

    $headers = array('Content-Type: text/html; charset=UTF-8');

    return wp_mail($email_address, $subject, $message, $headers);
}