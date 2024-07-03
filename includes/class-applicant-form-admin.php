<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class Applicant_Form_Admin {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_applicant_form_styles' ) );
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
        global $wpdb;
        $table_name = $wpdb->prefix . 'applicant_submissions';
    
        // Fetch all submissions
        $submissions = $wpdb->get_results( "SELECT * FROM $table_name ORDER BY submission_date DESC" );
    
        ?>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 my-6">Applicant Submissions</h1>
    
            <!-- Submissions Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">First Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Post Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CV</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submission Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php
                        if ( $submissions ) {
                            foreach ( $submissions as $submission ) {
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo $submission->id; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo esc_html( $submission->first_name ); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo esc_html( $submission->last_name ); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo esc_html( $submission->email_address ); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo esc_html( $submission->post_name ); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="<?php echo esc_url( $submission->cv ); ?>" class="text-blue-500 hover:text-blue-700">Preview</a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo esc_html( $submission->submission_date ); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            ?>
                            <tr><td colspan="7" class="px-6 py-4 whitespace-nowrap text-center">No submissions found.</td></tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
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
            ?>
            <div class="p-4 bg-white shadow rounded">
                <h2 class="text-lg font-semibold mb-5">Recent Applicant Submissions</h2>
                <ul class="divide-y divide-gray-200">
                    <?php foreach ($results as $row) : ?>
                        <li class="flex items-center justify-between py-4">
                            <div class="flex items-center space-x-4">
                                <div class="w-16 truncate font-semibold"><?php echo esc_html($row->first_name); ?></div>
                                <div class="text-sm text-gray-500"><?php echo esc_html($row->post_name); ?></div>
                                <div class="text-sm text-gray-500"><?php echo esc_html(date('Y-m-d', strtotime($row->submission_date))); ?></div>
                            </div>
                            <div>
                                <a href="<?php echo esc_url($row->cv); ?>" class="text-blue-500 hover:text-blue-700">Preview</a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php
        } else {
            echo '<p>No recent submissions.</p>';
        }
    }
    
    
    public function enqueue_applicant_form_styles() {
        wp_enqueue_style( 'tailwind', 'https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css' );
    }
}