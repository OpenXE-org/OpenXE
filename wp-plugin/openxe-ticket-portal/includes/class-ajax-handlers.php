<?php
/**
 * AJAX handlers for OpenXE Ticket Portal
 */

if (!defined('ABSPATH')) {
    exit;
}

class OpenXE_Ticket_Portal_AJAX {
    /**
     * Register AJAX actions
     */
    public static function register(): void {
        add_action('wp_ajax_nopriv_openxe_ticket_portal_session', [self::class, 'ajax_session']);
        add_action('wp_ajax_openxe_ticket_portal_session', [self::class, 'ajax_session']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_magic', [self::class, 'ajax_magic']);
        add_action('wp_ajax_openxe_ticket_portal_magic', [self::class, 'ajax_magic']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_status', [self::class, 'ajax_status']);
        add_action('wp_ajax_openxe_ticket_portal_status', [self::class, 'ajax_status']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_messages', [self::class, 'ajax_messages']);
        add_action('wp_ajax_openxe_ticket_portal_messages', [self::class, 'ajax_messages']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_message', [self::class, 'ajax_message']);
        add_action('wp_ajax_openxe_ticket_portal_message', [self::class, 'ajax_message']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_offers', [self::class, 'ajax_offers']);
        add_action('wp_ajax_openxe_ticket_portal_offers', [self::class, 'ajax_offers']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_offer', [self::class, 'ajax_offer']);
        add_action('wp_ajax_openxe_ticket_portal_offer', [self::class, 'ajax_offer']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_notifications', [self::class, 'ajax_notifications']);
        add_action('wp_ajax_openxe_ticket_portal_notifications', [self::class, 'ajax_notifications']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_notification_save', [self::class, 'ajax_notification_save']);
        add_action('wp_ajax_openxe_ticket_portal_notification_save', [self::class, 'ajax_notification_save']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_media', [self::class, 'ajax_media']);
        add_action('wp_ajax_openxe_ticket_portal_media', [self::class, 'ajax_media']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_media_download', [self::class, 'ajax_media_download']);
        add_action('wp_ajax_openxe_ticket_portal_media_download', [self::class, 'ajax_media_download']);
        add_action('wp_ajax_nopriv_openxe_ticket_portal_test', [self::class, 'ajax_test']);
        add_action('wp_ajax_openxe_ticket_portal_test', [self::class, 'ajax_test']);
    }

    public static function ajax_session(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('session');
        
        $token = sanitize_text_field(wp_unslash($_POST['token'] ?? ''));
        $ticketNumber = sanitize_text_field(wp_unslash($_POST['ticket_number'] ?? ''));
        $verifierType = OpenXE_Ticket_Portal_Settings::get_default_verifier();
        if (isset($_POST['verifier_type'])) {
            $verifierType = openxe_ticket_portal_sanitize_verifier($_POST['verifier_type']);
        }
        $verifierValue = sanitize_text_field(wp_unslash($_POST['verifier_value'] ?? ''));

        if (($token === '' && $ticketNumber === '') || $verifierType === '') {
            wp_send_json_error(['message' => 'invalid_request_params'], 400);
            return;
        }

        $payload = [
            'verifier_type' => $verifierType,
            'verifier_value' => $verifierValue,
        ];
        if ($token !== '') $payload['token'] = substr($token, 0, 128);
        if ($ticketNumber !== '') $payload['ticket_number'] = substr($ticketNumber, 0, 32);

        OpenXE_Ticket_Portal_Remote_API::proxy('portal_session', $payload);
    }

    public static function ajax_magic(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('magic');
        $magicToken = sanitize_text_field(wp_unslash($_POST['magic_token'] ?? ''));
        if ($magicToken === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_magic', ['magic_token' => $magicToken]);
    }

    public static function ajax_status(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('status');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        if ($sessionToken === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_status', ['session_token' => $sessionToken]);
    }

    public static function ajax_messages(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('messages');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        if ($sessionToken === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_messages', ['session_token' => $sessionToken]);
    }

    public static function ajax_message(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('message');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        $text = sanitize_textarea_field(wp_unslash($_POST['text'] ?? ''));
        if ($sessionToken === '' || $text === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_message', [
            'session_token' => $sessionToken,
            'text' => $text,
        ]);
    }

    public static function ajax_offers(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('offers');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        if ($sessionToken === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_offers', ['session_token' => $sessionToken]);
    }

    public static function ajax_offer(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('offer');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        $offerId = sanitize_text_field(wp_unslash($_POST['angebot_id'] ?? ''));
        $action = sanitize_text_field(wp_unslash($_POST['offer_action'] ?? ''));
        $comment = sanitize_textarea_field(wp_unslash($_POST['comment'] ?? ''));
        $agbVersion = sanitize_text_field(wp_unslash($_POST['agb_version'] ?? ''));
        if ($sessionToken === '' || $offerId === '' || $action === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_offer', [
            'session_token' => $sessionToken,
            'angebot_id' => $offerId,
            'action' => $action,
            'comment' => $comment,
            'agb_version' => $agbVersion,
        ]);
    }

    public static function ajax_notifications_get(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('notifications_get');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        if ($sessionToken === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_notifications', ['session_token' => $sessionToken]);
    }

    public static function ajax_notifications_set(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('notifications_set');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        $selectedRaw = wp_unslash($_POST['selected'] ?? '');
        if ($sessionToken ===  '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        $selected = json_decode((string)$selectedRaw, true);
        if (!is_array($selected)) $selected = [];
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_notification', [
            'session_token' => $sessionToken,
            'selected' => $selected,
        ]);
    }

    public static function ajax_media(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('media');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        if ($sessionToken ===  '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_media', ['session_token' => $sessionToken]);
    }

    public static function ajax_notifications(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('notifications');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        if ($sessionToken === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_notifications', ['session_token' => $sessionToken]);
    }

    public static function ajax_notification_save(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('notification_save');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        $selected = wp_unslash($_POST['selected'] ?? '');
        if ($sessionToken === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy('portal_notification', [
            'session_token' => $sessionToken,
            'selected' => $selected,
        ]);
    }

    public static function ajax_media_download(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('media_download');
        $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
        $mediaId = sanitize_text_field(wp_unslash($_POST['media_id'] ?? ''));
        if ($sessionToken === '' || $mediaId === '') {
            wp_send_json_error(['message' => 'invalid_request'], 400);
            return;
        }
        OpenXE_Ticket_Portal_Remote_API::proxy_binary('portal_media_download', [
            'session_token' => $sessionToken,
            'media_id' => $mediaId,
        ]);
    }

    public static function ajax_test(): void {
        check_ajax_referer('openxe_ticket_portal', 'nonce');
        openxe_ticket_portal_apply_rate_limit('test');
        $result = OpenXE_Ticket_Portal_Remote_API::remote('portal_session', [
            'token' => 'test-connection',
            'verifier_type' => 'plz',
            'verifier_value' => '00000',
        ]);
        if (is_wp_error($result)) wp_send_json_error(['message' => $result->get_error_message()], 500);
        $code = (int)$result['code'];
        $data = $result['data'];
        $error = is_array($data) ? (string)($data['error'] ?? '') : '';
        if ($code >= 200 && $code < 300) wp_send_json_success(['message' => 'Verbindung ok.']);
        if ($error === 'token_not_found') wp_send_json_success(['message' => 'Verbindung ok. Portal erreichbar (Token nicht gefunden).']);
        if ($error === 'portal_disabled') wp_send_json_error(['message' => 'OpenXE Portal ist deaktiviert.'], 503);
        if ($error !== '') wp_send_json_error(['message' => 'Antwort von OpenXE: '.$error], $code ?: 500);
        wp_send_json_error(['message' => 'Verbindung fehlgeschlagen.'], $code ?: 500);
    }
}
