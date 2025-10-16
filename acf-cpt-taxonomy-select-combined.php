<?php
/**
 * Plugin Name: ACF CPT & Taxonomy Select Combined
 * Plugin URI: https://github.com/Bkrap/acf-cpt-taxonomy-select-combined
 * Description: Custom ACF field type that combines post object and taxonomy term selection
 * Version: 1.0.0
 * Author: Bruno Krapljan
 * Author URI: https://github.com/Bkrap
 * Text Domain: acf-cpt-taxonomy-select-combined
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ACF_CPT_TAX_VERSION', '1.0.0');
define('ACF_CPT_TAX_PATH', plugin_dir_path(__FILE__));
define('ACF_CPT_TAX_URL', plugin_dir_url(__FILE__));

/**
 * Check if ACF is active
 */
function acf_cpt_tax_check_acf() {
    if (!class_exists('ACF')) {
        add_action('admin_notices', 'acf_cpt_tax_admin_notice');
        return false;
    }
    return true;
}

/**
 * Admin notice if ACF is not active
 */
function acf_cpt_tax_admin_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php _e('ACF CPT & Taxonomy Select Combined requires Advanced Custom Fields to be installed and active.', 'acf-cpt-taxonomy-select-combined'); ?></p>
    </div>
    <?php
}

/**
 * Include and register the custom field type
 */
function acf_cpt_tax_include_field_type() {
    if (!acf_cpt_tax_check_acf()) {
        return;
    }
    
    // Include the field type class
    require_once ACF_CPT_TAX_PATH . 'includes/class-acf-field-cpt-taxonomy-select.php';
}
add_action('acf/include_field_types', 'acf_cpt_tax_include_field_type');

/**
 * Enqueue admin styles
 */
function acf_cpt_tax_admin_enqueue_scripts() {
    wp_enqueue_style(
        'acf-cpt-tax-admin',
        ACF_CPT_TAX_URL . 'assets/css/admin.css',
        array(),
        ACF_CPT_TAX_VERSION
    );
}
add_action('acf/input/admin_enqueue_scripts', 'acf_cpt_tax_admin_enqueue_scripts');

