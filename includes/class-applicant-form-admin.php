<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class Applicant_Form_Admin {
    public function __construct() {
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
    }

    public function add_dashboard_widget() {
        wp_add_dashboard_widget(
            'applicant_form_dashboard_widget',
            'Recent Applicant Submissions',
            array( $this, 'display_dashboard_widget' )
        );
    }

    public function display_dashboard_widget() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'applicant_submissions';
        $results = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY submission_date DESC LIMIT 5" );

        if ( $results ) {
            echo '<ul>';
            foreach ( $results as $row ) {
                echo '<li>' . esc_html( $row->first_name . ' ' . $row->last_name ) . ' - ' . esc_html( $row->post_name ) . ' (' . esc_html( $row->submission_date ) . ')</li>';
            }
            echo '</ul>';
        } else {
            echo 'No recent submissions.';
        }
    }
}