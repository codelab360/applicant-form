<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; 
}

class Applicant_Form_Admin {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_applicant_form_styles' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
        add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );
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
    
        // Handle search
        $search_query = isset( $_GET['s'] ) ? sanitize_text_field( $_GET['s'] ) : '';
        $where_clause = '';
        if ( ! empty( $search_query ) ) {
            $where_clause = $wpdb->prepare(
                " WHERE first_name LIKE %s OR last_name LIKE %s OR email_address LIKE %s OR post_name LIKE %s",
                '%' . $wpdb->esc_like( $search_query ) . '%',
                '%' . $wpdb->esc_like( $search_query ) . '%',
                '%' . $wpdb->esc_like( $search_query ) . '%',
                '%' . $wpdb->esc_like( $search_query ) . '%'
            );
        }
    
        // Sorting
        $orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( $_GET['orderby'] ) : 'submission_date';
        $order = isset( $_GET['order'] ) && strtoupper( $_GET['order'] ) === 'DESC' ? 'DESC' : 'ASC';
        $order_sql = "$orderby $order";
    
        // Pagination
        $items_per_page = 8;
        $current_page = isset( $_GET['paged'] ) ? absint( $_GET['paged'] ) : 1;
        $offset = ( $current_page - 1 ) * $items_per_page;
    
        // Fetch total number of submissions for pagination
        $total_items = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name $where_clause" );
        $total_pages = ceil( $total_items / $items_per_page );
    
        // Fetch submissions based on search, sorting, and pagination
        $submissions = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM $table_name $where_clause ORDER BY $order_sql LIMIT %d OFFSET %d",
            $items_per_page,
            $offset
        ) );
    
        ?>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-gray-900 my-6">Applicant Submissions</h1>
    
            <!-- Search Box -->
            <form method="get" class="mb-4">
                <div class="flex items-center">
                    <input type="hidden" name="page" value="<?php echo esc_attr( $_GET['page'] ); ?>">
                    <input type="search" id="applicant-search-input" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="Search Submissions..." class="w-full px-3 py-2 border rounded-lg mr-2 focus:outline-none focus:border-blue-500">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded focus:outline-none">Search</button>
                </div>
            </form>
    
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center">
                                    <a href="<?php echo esc_url(admin_url('admin.php') . '?page=applicant-submissions&orderby=submission_date&order=' . ($order === 'ASC' ? 'DESC' : 'ASC')); ?>" class="flex items-center">
                                        <span>Submission Date</span>
                                        <?php if ($orderby === 'submission_date') : ?>
                                            <svg class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                                                <?php if ($order === 'ASC') : ?>
                                                    <path fill-rule="evenodd" d="M8.293 6.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L10 9.414V16a1 1 0 0 1-2 0V9.414L5.707 11.707a1 1 0 1 1-1.414-1.414l4-4z" clip-rule="evenodd" />
                                                <?php else : ?>
                                                    <path fill-rule="evenodd" d="M11.707 13.707a1 1 0 0 1-1.414 0l-4-4a1 1 0 1 1 1.414-1.414L10 10.586V4a1 1 0 0 1 2 0v6.586l2.293-2.293a1 1 0 0 1 1.414 1.414l-4 4z" clip-rule="evenodd" />
                                                <?php endif; ?>
                                            </svg>
                                        <?php else : ?>
                                            <svg class="h-4 w-4 ml-1 opacity-0" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.293 6.293a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1-1.414 1.414L10 9.414V16a1 1 0 0 1-2 0V9.414L5.707 11.707a1 1 0 1 1-1.414-1.414l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        <?php endif; ?>
                                    </a>
                                </div>
                            </th>
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
    
            <!-- Pagination -->
            <div class="mt-4 text-base text-gray-500 space-x-2 ">
                <?php
                $pagination_base = esc_url(admin_url('admin.php') . '?page=applicant-submissions&s=' . urlencode($search_query) . '&orderby=' . urlencode($orderby) . '&order=' . urlencode($order) . '&paged=%#%');
                echo paginate_links( array(
                    'base' => $pagination_base,
                    'format' => '',
                    'current' => $current_page,
                    'total' => $total_pages,
                    'prev_text' => __('&laquo; Previous'),
                    'next_text' => __('Next &raquo;'),
                ) );
                ?>
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

    public function display_admin_notices() {
        // Check if WP Mail SMTP plugin is active
        if ( is_plugin_active( 'wp-mail-smtp/wp_mail_smtp.php' ) ) {
            // Check if SMTP is configured properly
            if ( wp_mail_smtp()->is_mailer_active() ) {
                return; // SMTP is configured and active, no notice needed
            } else {
                echo '<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mt-4" role="alert">
                    <p class="font-bold">Warning</p>
                    <p>Please configure WP Mail SMTP plugin to ensure reliable email delivery for the Applicant Form plugin.</p>
                </div>';
            }
        } else {
            echo '<div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mt-4" role="alert">
                <p class="font-bold">Warning</p>
                <p>Please install and activate WP Mail SMTP plugin for reliable email delivery with the Applicant Form plugin.</p>
            </div>';
        }
    }
    
}