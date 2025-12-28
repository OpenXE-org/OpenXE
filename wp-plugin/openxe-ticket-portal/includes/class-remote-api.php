<?php
/**
 * Remote API communication for OpenXE Ticket Portal
 */

if (!defined('ABSPATH')) {
    exit;
}

class OpenXE_Ticket_Portal_Remote_API {
    /**
     * Send remote request to OpenXE
     */
    public static function remote(string $action, array $payload): array|WP_Error {
        $baseUrl = openxe_ticket_portal_get_base_url();
        if ($baseUrl === '') {
            return new WP_Error('openxe_portal_missing_base_url', 'OpenXE base URL is not configured.');
        }

        $url = $baseUrl . '/index.php?module=ticket&action=' . rawurlencode($action);
        $headers = ['Content-Type' => 'application/json'];
        $sharedSecret = openxe_ticket_portal_get_shared_secret();

        if ($sharedSecret !== '') {
            $headers['X-OpenXE-Portal-Secret'] = $sharedSecret;
        }

        $response = wp_remote_post($url, [
            'headers' => $headers,
            'body' => wp_json_encode($payload),
            'timeout' => 20,
        ]);

        if (is_wp_error($response)) {
            return $response;
        }

        $code = (int)wp_remote_retrieve_response_code($response);
        $body = (string)wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!is_array($data)) {
            $data = ['raw' => $body];
        }

        return ['code' => $code, 'data' => $data];
    }

    /**
     * Proxy request and send JSON response
     */
    public static function proxy(string $action, array $payload): void {
        $result = self::remote($action, $payload);

        if (is_wp_error($result)) {
            openxe_ticket_portal_log('remote_error', [
                'action' => $action,
                'error' => $result->get_error_message(),
            ]);
            wp_send_json_error(['message' => $result->get_error_message()], 500);
        }

        $code = (int)$result['code'];
        $data = $result['data'];

        if ($code < 200 || $code >= 300) {
            $message = is_array($data) && !empty($data['error']) ? (string)$data['error'] : 'request_failed';
            openxe_ticket_portal_log('remote_response_error', [
                'action' => $action,
                'status' => $code,
                'message' => $message,
                'payload' => $payload,
                'response' => $data,
            ]);
            wp_send_json_error(['message' => $message, 'data' => $data], $code ?: 500);
        }

        wp_send_json_success($data);
    }

    /**
     * Proxy binary download
     */
    public static function proxy_binary(string $action, array $payload): void {
        $url = openxe_ticket_portal_get_base_url();
        if ($url === '') {
            wp_die('Base URL not set');
        }

        $url = add_query_arg('action', $action, $url);
        $sharedSecret = openxe_ticket_portal_get_shared_secret();
        $headers = ['Content-Type' => 'application/json'];

        if ($sharedSecret !== '') {
            $headers['X-OpenXE-Portal-Secret'] = $sharedSecret;
        }

        $response = wp_remote_post($url, [
            'headers' => $headers,
            'body' => wp_json_encode($payload),
            'timeout' => 60,
        ]);

        if (is_wp_error($response)) {
            wp_die($response->get_error_message());
        }

        $code = (int)wp_remote_retrieve_response_code($response);
        if ($code !== 200) {
            wp_die('Download failed (HTTP ' . $code . ')');
        }

        $contentType = wp_remote_retrieve_header($response, 'content-type');
        $contentDisposition = wp_remote_retrieve_header($response, 'content-disposition');
        $body = wp_remote_retrieve_body($response);

        if ($contentType) {
            header('Content-Type: ' . $contentType);
        }
        if ($contentDisposition) {
            header('Content-Disposition: ' . $contentDisposition);
        }

        echo $body;
        exit;
    }
}
