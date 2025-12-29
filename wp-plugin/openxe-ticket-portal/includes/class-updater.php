<?php
/**
 * Update management for OpenXE Ticket Portal
 */

if (!defined('ABSPATH')) {
    exit;
}

class OpenXE_Ticket_Portal_Updater {
    /**
     * Register update actions
     */
    public static function register(): void {
        add_action('admin_post_openxe_ticket_portal_update', [self::class, 'handle_update']);
        add_action('admin_post_openxe_ticket_portal_clear_log', [self::class, 'handle_clear_log']);
    }

    /**
     * Handle the update process
     */
    public static function handle_update(): void {
        if (!current_user_can('manage_options')) {
            wp_die('forbidden');
        }
        check_admin_referer('openxe_ticket_portal_update', 'openxe_ticket_portal_update_nonce');

        $redirect = admin_url('options-general.php?page=openxe-ticket-portal');
        $baseUrl = OpenXE_Ticket_Portal_Settings::get_base_url();
        $sharedSecret = OpenXE_Ticket_Portal_Settings::get_shared_secret();

        if ($baseUrl === '') {
            self::redirect_error($redirect, 'OpenXE Base URL fehlt.');
        }
        if ($sharedSecret === '') {
            self::redirect_error($redirect, 'Shared Secret fehlt.');
        }

        $downloadUrl = $baseUrl . '/index.php?module=ticket&action=portal_plugin_download';
        $tmp = self::download_zip($downloadUrl, $sharedSecret);
        
        if (is_wp_error($tmp)) {
            openxe_ticket_portal_log('plugin_update_download_failed', ['message' => $tmp->get_error_message()]);
            self::redirect_error($redirect, $tmp->get_error_message());
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';
        if (!WP_Filesystem()) {
            @unlink($tmp);
            self::redirect_error($redirect, 'WP Filesystem konnte nicht initialisiert werden.');
        }

        $workDir = self::prepare_update_dir();
        if (is_wp_error($workDir)) {
            @unlink($tmp);
            self::redirect_error($redirect, $workDir->get_error_message());
        }

        $unzip = unzip_file($tmp, $workDir);
        @unlink($tmp);
        if (is_wp_error($unzip)) {
            self::cleanup_dir($workDir);
            self::redirect_error($redirect, $unzip->get_error_message());
        }

        $sourceDir = self::find_extracted_dir($workDir);
        if (is_wp_error($sourceDir)) {
            self::cleanup_dir($workDir);
            self::redirect_error($redirect, $sourceDir->get_error_message());
        }

        $targetDir = rtrim(plugin_dir_path(OPENXE_TICKET_PORTAL_DIR), '/\\');
        $copyResult = copy_dir($sourceDir, $targetDir);
        self::cleanup_dir($workDir);

        if (is_wp_error($copyResult)) {
            openxe_ticket_portal_log('plugin_update_failed', ['message' => $copyResult->get_error_message()]);
            self::redirect_error($redirect, $copyResult->get_error_message());
        }

        openxe_ticket_portal_log('plugin_update_success', ['version' => OPENXE_TICKET_PORTAL_VERSION]);
        wp_safe_redirect(add_query_arg(['openxe_ticket_portal_update' => 'success', 'openxe_ticket_portal_update_msg' => 'Plugin aktualisiert.'], $redirect));
        exit;
    }

    private static function download_zip(string $url, string $sharedSecret): string|WP_Error {
        $headers = $sharedSecret !== '' ? ['X-OpenXE-Portal-Secret' => $sharedSecret] : [];
        $response = wp_remote_get($url, ['timeout' => 60, 'headers' => $headers]);
        if (is_wp_error($response)) {
            return $response;
        }
        $status = (int)wp_remote_retrieve_response_code($response);
        if ($status !== 200) {
            return new WP_Error('http_error', "Update failed ($status)");
        }
        $body = (string)wp_remote_retrieve_body($response);
        if (strlen($body) < 10 || substr($body, 0, 2) !== 'PK') {
            return new WP_Error('invalid_zip', 'Invalid ZIP archive');
        }
        $tmp = wp_tempnam('openxe-ticket-portal.zip');
        if ($tmp === false || @file_put_contents($tmp, $body) === false) {
            return new WP_Error('write_error', 'Could not save ZIP');
        }
        return $tmp;
    }

    private static function prepare_update_dir(): string|WP_Error {
        $base = rtrim(WP_CONTENT_DIR, '/\\') . DIRECTORY_SEPARATOR . 'upgrade';
        if (!is_dir($base) && !wp_mkdir_p($base)) {
            return new WP_Error('mkdir_error', 'Could not create upgrade dir');
        }
        $dir = $base . DIRECTORY_SEPARATOR . 'openxe-ticket-portal-' . gmdate('YmdHis') . '-' . wp_generate_password(6, false, false);
        return wp_mkdir_p($dir) ? $dir : new WP_Error('mkdir_error', 'Could not create work dir');
    }

    private static function find_extracted_dir(string $baseDir): string|WP_Error {
        $expected = $baseDir . DIRECTORY_SEPARATOR . 'openxe-ticket-portal' . DIRECTORY_SEPARATOR . 'openxe-ticket-portal.php';
        if (is_file($expected)) {
            return dirname($expected);
        }
        $matches = glob($baseDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'openxe-ticket-portal.php');
        return !empty($matches) ? dirname($matches[0]) : new WP_Error('not_found', 'Plugin files not found in archive');
    }

    private static function cleanup_dir(string $dir): void {
        global $wp_filesystem;
        if ($wp_filesystem instanceof WP_Filesystem_Base && $wp_filesystem->exists($dir)) {
            $wp_filesystem->delete($dir, true);
            return;
        }
        if (is_dir($dir)) {
            $items = scandir($dir);
            if (is_array($items)) {
                foreach ($items as $item) {
                    if ($item === '.' || $item === '..') {
                        continue;
                    }
                    $path = $dir . DIRECTORY_SEPARATOR . $item;
                    if (is_dir($path)) {
                        self::cleanup_dir($path);
                    } else {
                        @unlink($path);
                    }
                }
            }
            @rmdir($dir);
        }
    }

    private static function redirect_error(string $url, string $msg): void {
        wp_safe_redirect(add_query_arg(['openxe_ticket_portal_update' => 'error', 'openxe_ticket_portal_update_msg' => $msg], $url));
        exit;
    }

    /**
     * Handle log clearing
     */
    public static function handle_clear_log(): void {
        if (!current_user_can('manage_options')) {
            wp_die('forbidden');
        }
        check_admin_referer('openxe_ticket_portal_clear_log', 'openxe_ticket_portal_clear_log_nonce');

        $redirect = admin_url('options-general.php?page=openxe-ticket-portal');
        $logPath = openxe_ticket_portal_get_log_path();

        if (file_exists($logPath)) {
            if (@unlink($logPath)) {
                $msg = 'Log gelöscht.';
                wp_safe_redirect(add_query_arg(['openxe_ticket_portal_log_cleared' => 'success', 'openxe_ticket_portal_log_msg' => $msg], $redirect));
            } else {
                $msg = 'Log konnte nicht gelöscht werden.';
                wp_safe_redirect(add_query_arg(['openxe_ticket_portal_log_cleared' => 'error', 'openxe_ticket_portal_log_msg' => $msg], $redirect));
            }
        } else {
            $msg = 'Keine Log-Datei vorhanden.';
            wp_safe_redirect(add_query_arg(['openxe_ticket_portal_log_cleared' => 'info', 'openxe_ticket_portal_log_msg' => $msg], $redirect));
        }
        exit;
    }
}
