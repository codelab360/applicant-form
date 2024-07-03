<?php
/*
Plugin Name: Applicant Form
Description: A plugin to manage applicant information and submissions.
Version: 0.1
Author: Pronob Mozumder
 */

// Direct access is not allowed
if (!defined('ABSPATH')) {
    exit;
}

require_once APPLICANT_FORM_PLUGIN_DIR . 'includes/shortcode.php';
require_once APPLICANT_FORM_PLUGIN_DIR . 'includes/email-functions.php';

add_action('wp_enqueue_scripts', 'applicant_form_enqueue_scripts');

function applicant_form_enqueue_scripts()
{
    wp_enqueue_style('tailwind_css', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css');
    wp_enqueue_script('applicant-form', plugin_dir_url(__FILE__) . 'js/applicant-form.js', array('jquery'), '0.1', true);
}

// applicant_submissions
// Create custom table on plugin activation
register_activation_hook(__FILE__, 'applicant_form_create_table');
function applicant_form_create_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'applicant_submissions';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        first_name varchar(255) NOT NULL AUTO_INCREMENT,
        last_name varchar(255) NOT NULL AUTO_INCREMENT,
        present_address text NOT NULL,
        email_address varchar(255) NOT NULL,
        mobile_number varchar(255) NOT NULL,
        post_name varchar(255) NOT NULL,
        cv_file varchar(255) NOT NULL,
        submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
}
