<?php
/**
 * Theme management for OpenXE Ticket Portal
 */

if (!defined('ABSPATH')) {
    exit;
}

class OpenXE_Ticket_Portal_Theme_Manager {
    /**
     * Register theme settings
     */
    public static function register(): void {
        register_setting('openxe_ticket_portal_theme', 'openxe_ticket_portal_theme_preset', [
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'default' => 'default',
        ]);
        register_setting('openxe_ticket_portal_theme', 'openxe_ticket_portal_theme_colors', [
            'type' => 'array',
            'sanitize_callback' => [self::class, 'sanitize_colors'],
            'default' => [],
        ]);
        register_setting('openxe_ticket_portal_theme', 'openxe_ticket_portal_custom_css', [
            'type' => 'string',
            'sanitize_callback' => 'wp_strip_all_tags',
            'default' => '',
        ]);
    }

    /**
     * Get theme variables for inline CSS
     */
    public static function get_css_variables(): string {
        $preset = get_option('openxe_ticket_portal_theme_preset', 'default');
        $colors = get_option('openxe_ticket_portal_theme_colors', []);
        
        $variables = self::get_preset_variables($preset);
        foreach ($colors as $key => $value) {
            if (!empty($value)) {
                $variables[$key] = $value;
            }
        }

        $css = ':root {';
        foreach ($variables as $key => $value) {
            $css .= "--oxp-{$key}: {$value};";
        }
        $css .= '}';
        
        $customCss = get_option('openxe_ticket_portal_custom_css', '');
        if ($customCss) {
            $css .= $customCss;
        }

        return $css;
    }

    /**
     * Sanitize color array
     */
    public static function sanitize_colors($value): array {
        if (!is_array($value)) return [];
        $sanitized = [];
        foreach ($value as $key => $color) {
            $key = sanitize_key($key);
            if (preg_match('/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/', $color)) {
                $sanitized[$key] = $color;
            }
        }
        return $sanitized;
    }

    /**
     * Get default variables for a preset
     */
    private static function get_preset_variables(string $preset): array {
        $presets = [
            'default' => [
                'primary' => '#2271b1',
                'primary-hover' => '#135e96',
                'bg' => '#f0f0f1',
                'card-bg' => '#ffffff',
                'text' => '#3c434a',
                'border' => '#c3c4c7',
                'radius' => '4px',
                'shadow' => '0 1px 3px rgba(0,0,0,0.1)',
            ],
            'dark' => [
                'primary' => '#72aee6',
                'primary-hover' => '#9ec2e6',
                'bg' => '#1d2327',
                'card-bg' => '#2c3338',
                'text' => '#f0f0f1',
                'border' => '#3c434a',
                'radius' => '8px',
                'shadow' => '0 4px 6px rgba(0,0,0,0.3)',
            ],
            'minimal' => [
                'primary' => '#000000',
                'primary-hover' => '#333333',
                'bg' => '#ffffff',
                'card-bg' => '#ffffff',
                'text' => '#000000',
                'border' => '#dddddd',
                'radius' => '0px',
                'shadow' => 'none',
            ]
        ];

        return $presets[$preset] ?? $presets['default'];
    }
}
