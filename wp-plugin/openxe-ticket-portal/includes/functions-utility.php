<?php
/**
 * Utility functions for OpenXE Ticket Portal
 */

if (!defined('ABSPATH')) {
    exit;
}

function openxe_ticket_portal_ends_with(string $value, string $suffix): bool {
    if ($suffix === '') return true;
    return substr($value, -strlen($suffix)) === $suffix;
}

function openxe_ticket_portal_is_private_host(string $host): bool {
    $host = strtolower($host);
    if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1') return true;
    if (openxe_ticket_portal_ends_with($host, '.local') || openxe_ticket_portal_ends_with($host, '.lan')) return true;
    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
        return !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
    if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
        if (strpos($host, 'fe80') === 0) return true;
        if (strpos($host, 'fc') === 0 || strpos($host, 'fd') === 0) return true;
    }
    return false;
}

function openxe_ticket_portal_sanitize_url($value): string {
    $value = trim((string)$value);
    if ($value === '') return '';
    if (strpos($value, '://') === false) $value = 'https://' . $value;
    $parts = wp_parse_url($value);
    if (!is_array($parts) || empty($parts['host'])) return '';
    $scheme = strtolower((string)($parts['scheme'] ?? ''));
    if ($scheme !== 'http' && $scheme !== 'https') return '';
    $host = (string)$parts['host'];
    if ($scheme === 'http' && !openxe_ticket_portal_is_private_host($host)) $scheme = 'https';
    $port = isset($parts['port']) ? ':' . (int)$parts['port'] : '';
    $path = (string)($parts['path'] ?? '');
    return rtrim($scheme . '://' . $host . $port . $path, '/');
}

function openxe_ticket_portal_sanitize_verifier($value): string {
    $value = trim((string)$value);
    $allowed = ['auto', 'plz', 'email', 'code'];
    return in_array($value, $allowed, true) ? $value : 'auto';
}

function openxe_ticket_portal_get_log_path(): string {
    $upload = wp_upload_dir();
    $base = rtrim((string)($upload['basedir'] ?? ''), '/\\') ?: WP_CONTENT_DIR;
    return $base . DIRECTORY_SEPARATOR . 'openxe-ticket-portal.log';
}

function openxe_ticket_portal_read_log_tail(string $path, int $maxLines = 200, int $maxBytes = 20000): string {
    if (!is_file($path) || !is_readable($path)) return '';
    $size = filesize($path);
    if ($size === false || $size <= 0) return '';
    $bytes = min($maxBytes, (int)$size);
    $handle = fopen($path, 'rb');
    if ($handle === false) return '';
    if ($bytes < $size) fseek($handle, -$bytes, SEEK_END);
    $data = fread($handle, $bytes);
    fclose($handle);
    if ($data === false || $data === '') return '';
    $lines = preg_split('/\\r\\n|\\r|\\n/', $data);
    if ($bytes < $size && !empty($lines)) array_shift($lines);
    if (count($lines) > $maxLines) $lines = array_slice($lines, -$maxLines);
    return implode("\n", $lines);
}

function openxe_ticket_portal_mask_value(string $value): string {
    $len = strlen($value);
    if ($len <= 4) return '****';
    return substr($value, 0, 2) . '***' . substr($value, -2);
}

function openxe_ticket_portal_sanitize_log_context($value) {
    if (is_array($value)) {
        $sanitized = [];
        foreach ($value as $key => $item) {
            $keyLower = strtolower((string)$key);
            if (in_array($keyLower, ['token', 'magic_token', 'session_token', 'verifier_value', 'shared_secret', 'x-openxe-portal-secret'], true)) {
                $sanitized[$key] = openxe_ticket_portal_mask_value((string)$item);
            } else {
                $sanitized[$key] = openxe_ticket_portal_sanitize_log_context($item);
            }
        }
        return $sanitized;
    }
    return is_scalar($value) ? (string)$value : $value;
}

function openxe_ticket_portal_log(string $message, array $context = []): void {
    // Always use error_log for critical errors
    if (in_array($message, ['remote_error', 'remote_response_error'], true)) {
        error_log('[OpenXE Portal Log] ' . $message . ': ' . json_encode($context));
    }
    
    if (!OpenXE_Ticket_Portal_Settings::is_log_enabled()) {
        return;
    }
    
    $path = openxe_ticket_portal_get_log_path();
    $payload = [
        'time' => gmdate('c'),
        'message' => $message,
        'context' => openxe_ticket_portal_sanitize_log_context($context),
    ];
    $line = wp_json_encode($payload);
    if ($line === false) return;
    @file_put_contents($path, $line . PHP_EOL, FILE_APPEND);
}

function openxe_ticket_portal_apply_rate_limit(string $action): void {
    $limit = (int)apply_filters('openxe_ticket_portal_rate_limit', 60, $action);
    $window = (int)apply_filters('openxe_ticket_portal_rate_window', 60, $action);
    if ($limit <= 0 || $window <= 0) return;
    $ip = (string)($_SERVER['REMOTE_ADDR'] ?? '');
    $key = 'oxp_rl_' . md5($action . '|' . $ip);
    $now = time();
    $data = get_transient($key);
    if (!is_array($data) || empty($data['start']) || ($now - (int)$data['start']) >= $window) {
        $data = ['count' => 0, 'start' => $now];
    }
    $data['count'] = (int)$data['count'] + 1;
    set_transient($key, $data, $window);
    if ($data['count'] > $limit) {
        openxe_ticket_portal_log('rate_limited', ['action' => $action, 'ip' => $ip, 'count' => $data['count'], 'limit' => $limit]);
        wp_send_json_error(['message' => 'rate_limited'], 429);
    }
}

function openxe_ticket_portal_get_base_url(): string {
    return OpenXE_Ticket_Portal_Settings::get_base_url();
}

function openxe_ticket_portal_get_shared_secret(): string {
    return OpenXE_Ticket_Portal_Settings::get_shared_secret();
}
