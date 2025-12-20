<?php
/**
 * Plugin Name: OpenXE Ticket Portal
 * Description: Customer portal shortcode for OpenXE tickets.
 * Version: 0.1.0
 */

if (!defined('ABSPATH')) {
  exit;
}

define('OPENXE_TICKET_PORTAL_VERSION', '0.1.0');
define('OPENXE_TICKET_PORTAL_DIR', plugin_dir_path(__FILE__));
define('OPENXE_TICKET_PORTAL_URL', plugin_dir_url(__FILE__));

function openxe_ticket_portal_register_settings(): void
{
  register_setting('openxe_ticket_portal', 'openxe_ticket_portal_base_url', [
    'type' => 'string',
    'sanitize_callback' => 'openxe_ticket_portal_sanitize_url',
    'default' => '',
  ]);
  register_setting('openxe_ticket_portal', 'openxe_ticket_portal_default_verifier', [
    'type' => 'string',
    'sanitize_callback' => 'openxe_ticket_portal_sanitize_verifier',
    'default' => 'plz',
  ]);
}
add_action('admin_init', 'openxe_ticket_portal_register_settings');

function openxe_ticket_portal_sanitize_url($value): string
{
  $value = trim((string)$value);
  if ($value === '') {
    return '';
  }
  return rtrim($value, '/');
}

function openxe_ticket_portal_sanitize_verifier($value): string
{
  $value = trim((string)$value);
  $allowed = ['plz', 'email', 'code'];
  return in_array($value, $allowed, true) ? $value : 'plz';
}

function openxe_ticket_portal_get_base_url(): string
{
  $url = (string)get_option('openxe_ticket_portal_base_url', '');
  return rtrim(trim($url), '/');
}

function openxe_ticket_portal_admin_menu(): void
{
  add_options_page(
    'OpenXE Ticket Portal',
    'OpenXE Ticket Portal',
    'manage_options',
    'openxe-ticket-portal',
    'openxe_ticket_portal_settings_page'
  );
}
add_action('admin_menu', 'openxe_ticket_portal_admin_menu');

function openxe_ticket_portal_settings_page(): void
{
  $baseUrl = esc_attr(openxe_ticket_portal_get_base_url());
  $defaultVerifier = esc_attr((string)get_option('openxe_ticket_portal_default_verifier', 'plz'));
  ?>
  <div class="wrap">
    <h1>OpenXE Ticket Portal</h1>
    <form method="post" action="options.php">
      <?php settings_fields('openxe_ticket_portal'); ?>
      <table class="form-table" role="presentation">
        <tr>
          <th scope="row"><label for="openxe_ticket_portal_base_url">OpenXE Base URL</label></th>
          <td>
            <input type="text" id="openxe_ticket_portal_base_url" name="openxe_ticket_portal_base_url" value="<?php echo $baseUrl; ?>" class="regular-text">
            <p class="description">Beispiel: https://openxe.example.com</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="openxe_ticket_portal_default_verifier">Standard Verifikation</label></th>
          <td>
            <select id="openxe_ticket_portal_default_verifier" name="openxe_ticket_portal_default_verifier">
              <option value="plz" <?php selected($defaultVerifier, 'plz'); ?>>PLZ</option>
              <option value="email" <?php selected($defaultVerifier, 'email'); ?>>E-Mail</option>
              <option value="code" <?php selected($defaultVerifier, 'code'); ?>>Code</option>
            </select>
          </td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
  </div>
  <?php
}

function openxe_ticket_portal_enqueue_assets(): void
{
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
add_action('wp_enqueue_scripts', 'openxe_ticket_portal_enqueue_assets');

function openxe_ticket_portal_shortcode($atts): string
{
  wp_enqueue_style('openxe-ticket-portal');
  wp_enqueue_script('openxe-ticket-portal');

  $baseUrl = openxe_ticket_portal_get_base_url();
  $defaultVerifier = (string)get_option('openxe_ticket_portal_default_verifier', 'plz');

  $config = [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('openxe_ticket_portal'),
    'defaultVerifier' => $defaultVerifier,
    'baseUrl' => $baseUrl,
  ];
  wp_add_inline_script(
    'openxe-ticket-portal',
    'window.OpenXETwitterPortal = ' . wp_json_encode($config) . ';',
    'before'
  );

  $html = '<div class="openxe-portal" data-openxe-portal="1">';
  $html .= '<div class="oxp-card">';
  $html .= '<div class="oxp-error" style="display:none"></div>';
  $html .= '<div class="oxp-login">';
  $html .= '<h2>Ticket Portal</h2>';
  $html .= '<div class="oxp-row oxp-token-row">';
  $html .= '<label for="oxp-token">Token</label>';
  $html .= '<input id="oxp-token" class="oxp-token" type="text" autocomplete="off">';
  $html .= '</div>';
  $html .= '<div class="oxp-row">';
  $html .= '<label for="oxp-verifier">Verifikation</label>';
  $html .= '<select id="oxp-verifier" class="oxp-verifier">';
  $html .= '<option value="plz">PLZ</option>';
  $html .= '<option value="email">E-Mail</option>';
  $html .= '<option value="code">Code</option>';
  $html .= '</select>';
  $html .= '</div>';
  $html .= '<div class="oxp-row">';
  $html .= '<label for="oxp-verifier-value">Verifikationswert</label>';
  $html .= '<input id="oxp-verifier-value" class="oxp-verifier-value" type="text" autocomplete="off">';
  $html .= '</div>';
  $html .= '<button type="button" class="oxp-login-btn">Zugriff starten</button>';
  $html .= '<div class="oxp-login-msg"></div>';
  $html .= '</div>';

  $html .= '<div class="oxp-main" style="display:none">';
  $html .= '<div class="oxp-status">';
  $html .= '<div class="oxp-status-label">Status</div>';
  $html .= '<div class="oxp-status-value"></div>';
  $html .= '<div class="oxp-status-updated"></div>';
  $html .= '</div>';
  $html .= '<div class="oxp-actions">';
  $html .= '<button type="button" class="oxp-refresh">Aktualisieren</button>';
  $html .= '<a class="oxp-print" target="_blank" rel="noopener">Druckformular</a>';
  $html .= '<a class="oxp-download" target="_blank" rel="noopener">Download</a>';
  $html .= '<button type="button" class="oxp-offer-toggle">Angebot bestaetigen</button>';
  $html .= '</div>';
  $html .= '<div class="oxp-notifications">';
  $html .= '<h3>Benachrichtigungen</h3>';
  $html .= '<div class="oxp-notifications-list"></div>';
  $html .= '<button type="button" class="oxp-notifications-save">Speichern</button>';
  $html .= '<div class="oxp-notifications-msg"></div>';
  $html .= '</div>';
  $html .= '<div class="oxp-chat">';
  $html .= '<h3>Nachrichten</h3>';
  $html .= '<div class="oxp-messages"></div>';
  $html .= '<div class="oxp-row">';
  $html .= '<label for="oxp-message-text">Neue Nachricht</label>';
  $html .= '<textarea id="oxp-message-text" class="oxp-message-text" rows="3"></textarea>';
  $html .= '</div>';
  $html .= '<button type="button" class="oxp-send">Senden</button>';
  $html .= '<div class="oxp-message-msg"></div>';
  $html .= '</div>';

  $html .= '<div class="oxp-offer" style="display:none">';
  $html .= '<h3>Angebot bestaetigen</h3>';
  $html .= '<div class="oxp-row">';
  $html .= '<label for="oxp-offer-id">Angebot ID</label>';
  $html .= '<input id="oxp-offer-id" class="oxp-offer-id" type="text" autocomplete="off">';
  $html .= '</div>';
  $html .= '<div class="oxp-row">';
  $html .= '<label for="oxp-offer-action">Aktion</label>';
  $html .= '<select id="oxp-offer-action" class="oxp-offer-action">';
  $html .= '<option value="accept">Bestaetigen</option>';
  $html .= '<option value="decline">Ablehnen</option>';
  $html .= '</select>';
  $html .= '</div>';
  $html .= '<div class="oxp-row">';
  $html .= '<label for="oxp-offer-comment">Kommentar</label>';
  $html .= '<textarea id="oxp-offer-comment" class="oxp-offer-comment" rows="3"></textarea>';
  $html .= '</div>';
  $html .= '<div class="oxp-row">';
  $html .= '<label for="oxp-offer-agb">AGB Version (optional)</label>';
  $html .= '<input id="oxp-offer-agb" class="oxp-offer-agb" type="text" autocomplete="off">';
  $html .= '</div>';
  $html .= '<div class="oxp-row oxp-checkbox">';
  $html .= '<input id="oxp-offer-accept" class="oxp-offer-accept" type="checkbox" value="1">';
  $html .= '<label for="oxp-offer-accept">Ich akzeptiere die AGB fuer die Bestaetigung.</label>';
  $html .= '</div>';
  $html .= '<button type="button" class="oxp-offer-submit">Senden</button>';
  $html .= '<div class="oxp-offer-msg"></div>';
  $html .= '</div>';
  $html .= '</div>';

  $html .= '</div></div>';

  return $html;
}
add_shortcode('openxe_ticket_portal', 'openxe_ticket_portal_shortcode');

function openxe_ticket_portal_remote(string $action, array $payload): array|WP_Error
{
  $baseUrl = openxe_ticket_portal_get_base_url();
  if ($baseUrl === '') {
    return new WP_Error('openxe_portal_missing_base_url', 'OpenXE base URL is not configured.');
  }
  $url = $baseUrl . '/index.php?module=ticket&action=' . rawurlencode($action);
  $response = wp_remote_post($url, [
    'headers' => ['Content-Type' => 'application/json'],
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

function openxe_ticket_portal_proxy(string $action, array $payload): void
{
  $result = openxe_ticket_portal_remote($action, $payload);
  if (is_wp_error($result)) {
    wp_send_json_error(['message' => $result->get_error_message()], 500);
  }
  $code = (int)$result['code'];
  $data = $result['data'];
  if ($code < 200 || $code >= 300) {
    $message = is_array($data) && !empty($data['error']) ? (string)$data['error'] : 'request_failed';
    wp_send_json_error(['message' => $message, 'data' => $data], $code ?: 500);
  }
  wp_send_json_success($data);
}

function openxe_ticket_portal_ajax_session(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  $token = sanitize_text_field(wp_unslash($_POST['token'] ?? ''));
  $verifierType = sanitize_text_field(wp_unslash($_POST['verifier_type'] ?? ''));
  $verifierValue = sanitize_text_field(wp_unslash($_POST['verifier_value'] ?? ''));
  if ($token === '' || $verifierType === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_session', [
    'token' => $token,
    'verifier_type' => $verifierType,
    'verifier_value' => $verifierValue,
  ]);
}

function openxe_ticket_portal_ajax_status(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_status', ['session_token' => $sessionToken]);
}

function openxe_ticket_portal_ajax_messages(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_messages', ['session_token' => $sessionToken]);
}

function openxe_ticket_portal_ajax_message(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  $text = sanitize_textarea_field(wp_unslash($_POST['text'] ?? ''));
  if ($sessionToken === '' || $text === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_message', [
    'session_token' => $sessionToken,
    'text' => $text,
  ]);
}

function openxe_ticket_portal_ajax_offer(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  $offerId = sanitize_text_field(wp_unslash($_POST['angebot_id'] ?? ''));
  $action = sanitize_text_field(wp_unslash($_POST['offer_action'] ?? ''));
  $comment = sanitize_textarea_field(wp_unslash($_POST['comment'] ?? ''));
  $agbVersion = sanitize_text_field(wp_unslash($_POST['agb_version'] ?? ''));
  if ($sessionToken === '' || $offerId === '' || $action === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_offer', [
    'session_token' => $sessionToken,
    'angebot_id' => $offerId,
    'action' => $action,
    'comment' => $comment,
    'agb_version' => $agbVersion,
  ]);
}

function openxe_ticket_portal_ajax_notifications_get(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_notifications', ['session_token' => $sessionToken]);
}

function openxe_ticket_portal_ajax_notifications_set(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  $selectedRaw = wp_unslash($_POST['selected'] ?? '');
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  $selected = json_decode((string)$selectedRaw, true);
  if (!is_array($selected)) {
    $selected = [];
  }
  openxe_ticket_portal_proxy('portal_notification', [
    'session_token' => $sessionToken,
    'selected' => $selected,
  ]);
}

add_action('wp_ajax_nopriv_openxe_ticket_portal_session', 'openxe_ticket_portal_ajax_session');
add_action('wp_ajax_openxe_ticket_portal_session', 'openxe_ticket_portal_ajax_session');
add_action('wp_ajax_nopriv_openxe_ticket_portal_status', 'openxe_ticket_portal_ajax_status');
add_action('wp_ajax_openxe_ticket_portal_status', 'openxe_ticket_portal_ajax_status');
add_action('wp_ajax_nopriv_openxe_ticket_portal_messages', 'openxe_ticket_portal_ajax_messages');
add_action('wp_ajax_openxe_ticket_portal_messages', 'openxe_ticket_portal_ajax_messages');
add_action('wp_ajax_nopriv_openxe_ticket_portal_message', 'openxe_ticket_portal_ajax_message');
add_action('wp_ajax_openxe_ticket_portal_message', 'openxe_ticket_portal_ajax_message');
add_action('wp_ajax_nopriv_openxe_ticket_portal_offer', 'openxe_ticket_portal_ajax_offer');
add_action('wp_ajax_openxe_ticket_portal_offer', 'openxe_ticket_portal_ajax_offer');
add_action('wp_ajax_nopriv_openxe_ticket_portal_notifications_get', 'openxe_ticket_portal_ajax_notifications_get');
add_action('wp_ajax_openxe_ticket_portal_notifications_get', 'openxe_ticket_portal_ajax_notifications_get');
add_action('wp_ajax_nopriv_openxe_ticket_portal_notifications_set', 'openxe_ticket_portal_ajax_notifications_set');
add_action('wp_ajax_openxe_ticket_portal_notifications_set', 'openxe_ticket_portal_ajax_notifications_set');
