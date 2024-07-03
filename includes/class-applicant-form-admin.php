<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class Applicant_Form_Admin {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
    }

    public function add_admin_menu() {
        add_menu_page(
            'Applicant Submissions',
            'Applicant Submissions',
            'manage_options',
            'applicant-submissions',
            array( $this, 'render_admin_page' ),
            'dashicons-id',
            20
        );
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline">Applicant Submissions</h1>
            <table class="wp-list-table widefat striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Email</th>
                        <th>Post Name</th>
                        <th>Submission Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    global $wpdb;
                    $table_name = $wpdb->prefix . 'applicant_submissions';
                    $submissions = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY submission_date DESC" );
                    foreach ( $submissions as $submission ) {
                        ?>
                        <tr>
                            <td><?php echo $submission->id; ?></td>
                            <td><?php echo esc_html( $submission->first_name ); ?></td>
                            <td><?php echo esc_html( $submission->last_name ); ?></td>
                            <td><?php echo esc_html( $submission->email ); ?></td>
                            <td><?php echo esc_html( $submission->post_name ); ?></td>
                            <td><?php echo esc_html( $submission->submission_date ); ?></td>
                        </tr>
                        <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <?php
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