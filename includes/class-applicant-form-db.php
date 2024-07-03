<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class Applicant_Form_DB {
    public static function create_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'applicant_submissions';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            first_name varchar(100) NOT NULL,
            last_name varchar(100) NOT NULL,
            present_address text NOT NULL,
            email_address varchar(100) NOT NULL,
            mobile_no varchar(20) NOT NULL,
            post_name varchar(100) NOT NULL,
            cv varchar(255) NOT NULL,
            submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }
}
