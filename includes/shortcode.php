<?php

function applicant_form_shortcode() {
    ob_start();
    ?>
    <form id="applicant_form" method="post" enctype="multipart/form-data">
        <label for="first_name">First Name:</label>
        <input type="text" name="first_name" required>
        <label for="last_name">Last Name:</label>
        <input type="text" name="last_name" required>
        <label for="present_address">Present Address:</label>
        <textarea name="present_address" required></textarea>
        <label for="email_address">Email Address:</label>
        <input type="email" name="email_address" required>
        <label for="mobile_no">Mobile No:</label>
        <input type="text" name="mobile_no" required>
        <label for="post_name">Post Name:</label>
        <input type="text" name="post_name" required>
        <label for="cv">CV:</label>
        <input type="file" name="cv" required>
        <input type="submit" name="submit_applicant_form" value="Submit">
    </form>
    <?php
    return ob_get_clean();
}

add_shortcode('applicant_form', 'applicant_form_shortcode');

function handle_applicant_form_submission() {
    if (isset($_POST['submit_applicant_form'])) {
        global $wpdb;

        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $present_address = sanitize_textarea_field($_POST['present_address']);
        $email_address = sanitize_email($_POST['email_address']);
        $mobile_no = sanitize_text_field($_POST['mobile_no']);
        $post_name = sanitize_text_field($_POST['post_name']);
        
        // Handle CV upload
        if (isset($_FILES['cv'])) {
            $cv = $_FILES['cv'];
            
            // Check if there was an error during file upload
            if ($cv['error'] !== UPLOAD_ERR_OK) {
                // Handle upload error
                wp_die('Upload failed with error code ' . $cv['error']);
            }

            // Sanitize the file name to prevent directory traversal
            $file_name = sanitize_file_name($cv['name']);

            // Upload the file using WordPress function
            $upload = wp_handle_upload($cv, array('test_form' => false));

            // Check if the upload was successful
            if ($upload && !isset($upload['error'])) {
                $cv_url = $upload['url'];
            } else {
                // Handle upload error
                wp_die('Upload failed: ' . $upload['error']);
            }
        } else {
            // Handle case where CV file is not uploaded
            wp_die('No CV file uploaded.');
        }

        $table_name = $wpdb->prefix . 'applicant_submissions';
        $wpdb->insert($table_name, array(
            'first_name' => $first_name,
            'last_name' => $last_name,
            'present_address' => $present_address,
            'email_address' => $email_address,
            'mobile_no' => $mobile_no,
            'post_name' => $post_name,
            'cv' => $cv_url,
        ));

         // Send confirmation email
         $email_sent = send_mail($email_address, 'confirmation_email', array(
            'first_name' => $first_name,
            'post_name' => $post_name,
        ));

        if ($email_sent) {
            echo "Confirmation email sent successfully!";
        } else {
            echo "Failed to send confirmation email. Please try again later.";
        }
        
        wp_redirect(add_query_arg('submitted', 'true', wp_get_referer()));
        exit;
    }
}
add_action('template_redirect', 'handle_applicant_form_submission');