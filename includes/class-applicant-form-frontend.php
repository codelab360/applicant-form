<?php
if (!defined('ABSPATH')) {
    exit;
}

class Applicant_Form_Frontend {
    public function __construct() {
        add_shortcode('applicant_form', array($this, 'applicant_form_shortcode_render'));
        add_action('wp_ajax_submit_applicant_form', array($this, 'process_form_submission'));
        add_action('wp_ajax_nopriv_submit_applicant_form', array($this, 'process_form_submission'));
    }

    public function applicant_form_shortcode_render() {
        $message = get_transient('applicant_form_message');
        if ($message) {
            echo '<div class="' . esc_attr($message['type']) . '">' . esc_html($message['message']) . '</div>';
            delete_transient('applicant_form_message');
        }

        ob_start();
        ?>
        <div class="max-w-2xl mx-auto p-6 border border-gray-300 bg-gray-100">
            <form id="applicant_form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" enctype="multipart/form-data">
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

                <div>
                    <input type="hidden" name="action" value="submit_applicant_form">
                    <?php wp_nonce_field('submit_applicant_form', 'applicant_form_nonce');?>
                    <button class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded" type="submit" name="submit_applicant_form">Submit</button>
                    <div id="form-message" class="mt-2"></div>
                </div>
            </form>
        </div>
        <?php
        return ob_get_clean();
    }

    public function process_form_submission() {
        check_ajax_referer('submit_applicant_form', 'applicant_form_nonce');
    
        if (!isset($_POST['action']) || $_POST['action'] !== 'submit_applicant_form') {
            wp_send_json_error(array('message' => 'Invalid action.'));
        }
    
        $errors = array();
    
        // Sanitize and validate form data
        $first_name = sanitize_text_field($_POST['first_name']);
        if (empty($first_name)) {
            $errors[] = 'First Name is required.';
        }
    
        $last_name = sanitize_text_field($_POST['last_name']);
        if (empty($last_name)) {
            $errors[] = 'Last Name is required.';
        }
    
        $present_address = sanitize_textarea_field($_POST['present_address']);
        $email_address = sanitize_email($_POST['email_address']);
        $mobile_no = sanitize_text_field($_POST['mobile_no']);
        $post_name = sanitize_text_field($_POST['post_name']);
        $cv_url = '';
    
        if (isset($_FILES['cv'])) {
            $cv = $_FILES['cv'];
    
            if ($cv['error'] !== UPLOAD_ERR_OK) {
                $errors[] = 'Upload failed with error code ' . $cv['error'];
            } else {
                require_once ABSPATH . 'wp-admin/includes/file.php';
                $upload = wp_handle_upload($cv, array('test_form' => false));
    
                if ($upload && !isset($upload['error'])) {
                    $cv_url = $upload['url'];
                } else {
                    $errors[] = 'Upload failed: ' . $upload['error'];
                }
            }
        } else {
            $errors[] = 'No file uploaded.';
        }
    
        // If errors, set transient message and exit
        if (!empty($errors)) {
            $this->set_transient_message('error', implode('<br>', $errors));
            wp_send_json_error(array('message' => implode('<br>', $errors)));
        }
    
        global $wpdb;
        $table_name = $wpdb->prefix . 'applicant_submissions';
    
        // Insert data into database
        $wpdb->insert(
            $table_name,
            array(
                'first_name' => $first_name,
                'last_name' => $last_name,
                'present_address' => $present_address,
                'email_address' => $email_address,
                'mobile_no' => $mobile_no,
                'post_name' => $post_name,
                'cv' => $cv_url,
                'submission_date' => current_time('mysql'),
            )
        );
    
        $this->set_transient_message('success', 'Application submitted successfully.');
        wp_send_json_success(array('message' => 'Application submitted successfully.'));
    }
    

    private function set_transient_message($type, $message) {
        set_transient('applicant_form_message', array('type' => $type, 'message' => $message), 30);
    }
}