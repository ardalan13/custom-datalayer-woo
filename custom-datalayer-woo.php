<?php
/**
 * Plugin Name: Custom Datalayer Woo
 * Description: This plugin integrates WooCommerce with Google Analytics 4 (GA4) DataLayer to track eCommerce data. It solves the issue of missing currency (IRR or IRR Toman) in Google Analytics by ensuring that the currency is set to USD in the DataLayer, even if the store currency is set to IRR or Toman in WooCommerce.
 * Version: 1.5
 * Author: Ardalan Davoudi
 * Author URI: https://www.linkedin.com/in/ardalan-davoudi/
 * Text Domain: custom-datalayer-woo
 * License: GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Load the loader file
$loader_file = plugin_dir_path(__FILE__) . 'load-events.php';
if (file_exists($loader_file) && is_readable($loader_file)) {
    require_once $loader_file;
}

// Enqueue the nonce.js script and localize nonce for use in JS
function custom_datalayer_woo_enqueue_nonce_script() {
    $nonce_script_path = plugin_dir_url(__FILE__) . 'nonce.js';
    $nonce_script_version = file_exists(plugin_dir_path(__FILE__) . 'nonce.js') 
        ? filemtime(plugin_dir_path(__FILE__) . 'nonce.js') 
        : '1.0'; // Use file modification time or fallback to a default version

    if (file_exists(plugin_dir_path(__FILE__) . 'nonce.js')) {
        wp_enqueue_script('custom_datalayer_woo_nonce_js', $nonce_script_path, array('jquery'), $nonce_script_version, true);
        wp_localize_script('custom_datalayer_woo_nonce_js', 'customDatalayerWooNonce', array('nonce' => wp_create_nonce('custom_datalayer_woo_nonce_action')));
    }
}
add_action( 'wp_enqueue_scripts', 'custom_datalayer_woo_enqueue_nonce_script' );

// Add menu item to the WordPress admin panel under Tools
function custom_datalayer_woo_add_menu_item() {
    add_submenu_page(
        'tools.php',
        esc_html__( 'Custom Datalayer Woo', 'custom-datalayer-woo' ),
        esc_html__( 'Custom Datalayer Woo', 'custom-datalayer-woo' ),
        'manage_options',
        'custom-datalayer-woo',
        'custom_datalayer_woo_display_info'
    );
}
add_action( 'admin_menu', 'custom_datalayer_woo_add_menu_item' );

// Display plugin information in the admin menu
function custom_datalayer_woo_display_info() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Contact Me', 'custom-datalayer-woo' ); ?></h1>
        <p><strong><?php esc_html_e( 'Ardalan Davoudi', 'custom-datalayer-woo' ); ?></strong><br>
        <?php esc_html_e( 'Senior Digital Marketing Expert - Performance Marketer - Senior Google Ads Expert', 'custom-datalayer-woo' ); ?></p>
        
        <h2><?php esc_html_e( 'Plugin Description', 'custom-datalayer-woo' ); ?></h2>
        <p><?php esc_html_e( 'This plugin solves the issue of missing currency (IRR or IRR Toman) in Google Analytics by ensuring that the currency is set to USD in the DataLayer.', 'custom-datalayer-woo' ); ?></p>
        <p><?php esc_html_e( 'The currency is set to USD by default in this plugin. Please ensure that you have configured your Analytics settings to use USD for the currency.', 'custom-datalayer-woo' ); ?></p>

        <p><?php esc_html_e( 'Phone: ', 'custom-datalayer-woo' ); ?><a href="tel:09111273476"><?php echo esc_html( '09111273476' ); ?></a></p>
        <p><?php esc_html_e( 'LinkedIn: ', 'custom-datalayer-woo' ); ?><a href="<?php echo esc_url( 'https://www.linkedin.com/in/ardalan-davoudi/' ); ?>" target="_blank"><?php echo esc_html( 'https://www.linkedin.com/in/ardalan-davoudi/' ); ?></a></p>
    </div>
    <?php
}

// Add a custom action link to the Plugins page
function custom_datalayer_woo_add_plugin_action_links( $links ) {
    $contact_link = '<a href="' . esc_url( admin_url( 'tools.php?page=custom-datalayer-woo' ) ) . '">' . esc_html__( 'Contact Developer', 'custom-datalayer-woo' ) . '</a>';
    array_unshift( $links, $contact_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'custom_datalayer_woo_add_plugin_action_links' );
