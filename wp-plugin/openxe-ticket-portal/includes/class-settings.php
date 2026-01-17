<?php
/**
 * Settings management for OpenXE Ticket Portal
 */

if (!defined('ABSPATH')) {
    exit;
}

class OpenXE_Ticket_Portal_Settings {
    /**
     * Register plugin settings
     */
    public static function register(): void {
        register_setting('openxe_ticket_portal', 'openxe_ticket_portal_base_url', [
            'type' => 'string',
            'sanitize_callback' => 'openxe_ticket_portal_sanitize_url',
            'default' => '',
        ]);
        register_setting('openxe_ticket_portal', 'openxe_ticket_portal_shared_secret', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '',
        ]);
        register_setting('openxe_ticket_portal', 'openxe_ticket_portal_log_enabled', [
            'type' => 'boolean',
            'sanitize_callback' => 'absint',
            'default' => 0,
        ]);
        register_setting('openxe_ticket_portal', 'openxe_ticket_portal_default_verifier', [
            'type' => 'string',
            'sanitize_callback' => 'openxe_ticket_portal_sanitize_verifier',
            'default' => 'auto',
        ]);
    }

    /**
     * Get base URL
     */
    public static function get_base_url(): string {
        $url = (string)get_option('openxe_ticket_portal_base_url', '');
        return rtrim(trim($url), '/');
    }

    /**
     * Get shared secret
     */
    public static function get_shared_secret(): string {
        return (string)get_option('openxe_ticket_portal_shared_secret', '');
    }

    /**
     * Check if logging is enabled
     */
    public static function is_log_enabled(): bool {
        return (int)get_option('openxe_ticket_portal_log_enabled', 0) === 1;
    }

    /**
     * Get default verifier
     */
    public static function get_default_verifier(): string {
        return (string)get_option('openxe_ticket_portal_default_verifier', 'auto');
    }
}
