<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class Applicant_Form_Frontend {
    public function __construct() {
        add_shortcode( 'applicant_form', array( $this, 'applicant_form_shortcode_render' ) );
        add_action('init', array($this, 'process_form_submission'));
    }

    public function applicant_form_shortcode_render() {
        ob_start();
        ?>
        <div id="applicant_form" class="max-w-2xl mx-auto p-6 border border-gray-300 bg-gray-100">
            <form action="" method="post" enctype="multipart/form-data">
                <label class="block mb-2 font-bold" for="first_name">First Name:</label>
                <input class="w-full px-3 py-2 mb-3 border rounded" type="text" id="first_name" name="first_name">

                <label class="block mb-2 font-bold" for="last_name">Last Name:</label>
                <input class="w-full px-3 py-2 mb-3 border rounded" type="text" id="last_name" name="last_name">

                <label class="block mb-2 font-bold" for="present_address">Present Address:</label>
                <textarea class="w-full px-3 py-2 mb-3 border rounded" id="present_address" name="present_address"></textarea>

                <label class="block mb-2 font-bold" for="email_address">Email Address:</label>
                <input class="w-full px-3 py-2 mb-3 border rounded" type="email" id="email_address" name="email_address">

                <label class="block mb-2 font-bold" for="mobile_no">Mobile No:</label>
                <input class="w-full px-3 py-2 mb-3 border rounded" type="text" id="mobile_no" name="mobile_no">

                <label class="block mb-2 font-bold" for="post_name">Post Name:</label>
                <input class="w-full px-3 py-2 mb-3 border rounded" type="text" id="post_name" name="post_name">

                <label class="block mb-2 font-bold" for="cv">CV:</label>
                <input class="w-full px-3 py-2 mb-3 border rounded" type="file" id="cv" name="cv">

                <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded" type="submit" name="submit_applicant_form">Submit</button>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function process_form_submission() {

        if ( isset( $_POST['submit_applicant_form'] ) ) {
            global $wpdb;

            $table_name = $wpdb->prefix . 'applicant_submissions';

            $first_name = sanitize_text_field( $_POST['first_name'] );
            $last_name = sanitize_text_field( $_POST['last_name'] );
            $present_address = sanitize_textarea_field( $_POST['present_address'] );
            $email_address = sanitize_email( $_POST['email_address'] );
            $mobile_no = sanitize_text_field( $_POST['mobile_no'] );
            $post_name = sanitize_text_field( $_POST['post_name'] );

            if (isset($_FILES['cv'])) {
                $cv = $_FILES['cv'];

                if ($cv['error'] !== UPLOAD_ERR_OK) {
                    wp_die('Upload failed with error code ' . $cv['error']);
                }

                $file_name = sanitize_file_name($cv['name']);

                $upload = wp_handle_upload($cv, array('test_form' => false));

                if ($upload && !isset($upload['error'])) {
                    $cv_url = $upload['url'];
                } else {
                    wp_die('Upload failed');
                }
            }

            $cv_url = $upload['url'];

            $wpdb->insert(
                $table_name,
                array(
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'present_address' => $present_address,
                    'email_address' => $email_address,
                    'mobile_no' => $mobile_no,
                    'post_name' => $post_name,
                    'cv' => $cv_url
                )
            );

            wp_redirect( home_url() );
        }
    }

}