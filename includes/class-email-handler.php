<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;  
}

class Email_Handler {

    public function send_email( $type, $recipient, $data = array() ) {
        switch ( $type ) {
            case 'new_submission':
                $subject = 'New Applicant Submission';
                $message = sprintf( 'Hello %s %s,<br><br>Thank you for submitting your application for the position of %s. We will review your application shortly.<br><br>Best regards,<br>Your Company', $data['first_name'], $data['last_name'], $data['post_name'] );
                break;

            default:
                return false;
        }

        $headers = array( 
            'Content-Type: text/html; charset=UTF-8',
            'Form: Test User <test@example.com>',
        );

        return wp_mail( $recipient, $subject, $message, $headers );
    }
}