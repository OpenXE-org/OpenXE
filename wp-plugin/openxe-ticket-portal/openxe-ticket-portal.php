<?php
/**
 * Plugin Name: OpenXE Ticket Portal
 * Description: Customer portal shortcode for OpenXE tickets.
 * Version: 0.2.1
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define Constants
define('OPENXE_TICKET_PORTAL_VERSION', '0.2.1');
define('OPENXE_TICKET_PORTAL_DIR', plugin_dir_path(__FILE__));
define('OPENXE_TICKET_PORTAL_URL', plugin_dir_url(__FILE__));

// Register Classes
require_once OPENXE_TICKET_PORTAL_DIR . 'includes/class-settings.php';
require_once OPENXE_TICKET_PORTAL_DIR . 'includes/functions-utility.php';
require_once OPENXE_TICKET_PORTAL_DIR . 'includes/class-remote-api.php';
require_once OPENXE_TICKET_PORTAL_DIR . 'includes/class-ajax-handlers.php';
require_once OPENXE_TICKET_PORTAL_DIR . 'includes/class-shortcode.php';
require_once OPENXE_TICKET_PORTAL_DIR . 'includes/class-updater.php';
require_once OPENXE_TICKET_PORTAL_DIR . 'includes/class-theme-manager.php';

// Initialization
function openxe_ticket_portal_init(): void {
    // Admin Initialization
    if (is_admin()) {
        add_action('admin_init', [OpenXE_Ticket_Portal_Settings::class, 'register']);
        add_action('admin_init', [OpenXE_Ticket_Portal_Theme_Manager::class, 'register']);
        add_action('admin_menu', 'openxe_ticket_portal_admin_menu');
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'openxe_ticket_portal_action_links');
        OpenXE_Ticket_Portal_Updater::register();
        add_action('admin_post_openxe_ticket_portal_clear_log', 'openxe_ticket_portal_clear_log_action');
    }

    // AJAX and Shortcode Registration
    OpenXE_Ticket_Portal_AJAX::register();
    OpenXE_Ticket_Portal_Shortcode::register();

    // Assets Registration
    add_action('wp_enqueue_scripts', 'openxe_ticket_portal_enqueue_assets');
}
add_action('plugins_loaded', 'openxe_ticket_portal_init');

// --- Asset Logic ---

function openxe_ticket_portal_enqueue_assets(): void {
    wp_register_style(
        'openxe-ticket-portal',
        OPENXE_TICKET_PORTAL_URL . 'assets/portal.css',
        [],
        OPENXE_TICKET_PORTAL_VERSION
    );
    wp_register_script(
        'openxe-ticket-portal',
        OPENXE_TICKET_PORTAL_URL . 'assets/portal.js',
        [],
        OPENXE_TICKET_PORTAL_VERSION,
        true
    );
}

// --- Admin UI Logic ---

function openxe_ticket_portal_action_links(array $links): array {
    $settingsUrl = admin_url('options-general.php?page=openxe-ticket-portal');
    array_unshift($links, '<a href="' . esc_url($settingsUrl) . '">Einstellungen</a>');
    return $links;
}

function openxe_ticket_portal_admin_menu(): void {
    add_options_page(
        'OpenXE Ticket Portal',
        'OpenXE Ticket Portal',
        'manage_options',
        'openxe-ticket-portal',
        'openxe_ticket_portal_settings_page'
    );
}

function openxe_ticket_portal_settings_page(): void {
    include OPENXE_TICKET_PORTAL_DIR . 'includes/views/settings-page.php';
}

function openxe_ticket_portal_clear_log_action(): void {
    if (!current_user_can('manage_options')) wp_die('forbidden');
    check_admin_referer('openxe_ticket_portal_clear_log', 'openxe_ticket_portal_clear_log_nonce');
    $path = openxe_ticket_portal_get_log_path();
    if (is_file($path)) @unlink($path);
    wp_safe_redirect(admin_url('options-general.php?page=openxe-ticket-portal'));
    exit;
}
