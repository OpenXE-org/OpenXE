{"time":"2025-12-22T23:12:56+00:00","message":"plugin_update_failed","context":{"message":"Die Auflistung des Verzeichnisinhaltes ist fehlgeschlagen."}}
<?php
/**
 * Plugin Name: OpenXE Ticket Portal
 * Description: Customer portal shortcode for OpenXE tickets.
 * Version: 0.2.1
 */

if (!defined('ABSPATH')) {
  exit;
}

define('OPENXE_TICKET_PORTAL_VERSION', '0.2.1');
define('OPENXE_TICKET_PORTAL_DIR', plugin_dir_path(__FILE__));
define('OPENXE_TICKET_PORTAL_URL', plugin_dir_url(__FILE__));

function openxe_ticket_portal_register_settings(): void
{
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
add_action('admin_init', 'openxe_ticket_portal_register_settings');

function openxe_ticket_portal_ends_with(string $value, string $suffix): bool
{
  if ($suffix === '') {
    return true;
  }
  return substr($value, -strlen($suffix)) === $suffix;
}

function openxe_ticket_portal_is_private_host(string $host): bool
{
  $host = strtolower($host);
  if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1') {
    return true;
  }
  if (openxe_ticket_portal_ends_with($host, '.local') || openxe_ticket_portal_ends_with($host, '.lan')) {
    return true;
  }
  if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    return !filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
  }
  if (filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
    if (strpos($host, 'fe80') === 0) {
      return true;
    }
    if (strpos($host, 'fc') === 0 || strpos($host, 'fd') === 0) {
      return true;
    }
  }
  return false;
}

function openxe_ticket_portal_sanitize_url($value): string
{
  $value = trim((string)$value);
  if ($value === '') {
    return '';
  }
  if (strpos($value, '://') === false) {
    $value = 'https://' . $value;
  }
  $parts = wp_parse_url($value);
  if (!is_array($parts) || empty($parts['host'])) {
    return '';
  }
  $scheme = strtolower((string)($parts['scheme'] ?? ''));
  if ($scheme !== 'http' && $scheme !== 'https') {
    return '';
  }
  $host = (string)$parts['host'];
  if ($scheme === 'http' && !openxe_ticket_portal_is_private_host($host)) {
    $scheme = 'https';
  }
  $port = isset($parts['port']) ? ':' . (int)$parts['port'] : '';
  $path = (string)($parts['path'] ?? '');
  return rtrim($scheme . '://' . $host . $port . $path, '/');
}

function openxe_ticket_portal_sanitize_verifier($value): string
{
  $value = trim((string)$value);
  $allowed = ['auto', 'plz', 'email', 'code'];
  return in_array($value, $allowed, true) ? $value : 'auto';
}

function openxe_ticket_portal_get_base_url(): string
{
  $url = (string)get_option('openxe_ticket_portal_base_url', '');
  return rtrim(trim($url), '/');
}

function openxe_ticket_portal_get_shared_secret(): string
{
  return (string)get_option('openxe_ticket_portal_shared_secret', '');
}

function openxe_ticket_portal_get_settings_url(): string
{
  return admin_url('options-general.php?page=openxe-ticket-portal');
}

function openxe_ticket_portal_get_plugin_download_url(): string
{
  $baseUrl = openxe_ticket_portal_get_base_url();
  if ($baseUrl === '') {
    return '';
  }
  return $baseUrl . '/index.php?module=ticket&action=portal_plugin_download';
}

function openxe_ticket_portal_log_enabled(): bool
{
  return (int)get_option('openxe_ticket_portal_log_enabled', 0) === 1;
}

function openxe_ticket_portal_get_log_path(): string
{
  $upload = wp_upload_dir();
  $base = rtrim((string)($upload['basedir'] ?? ''), '/\\');
  if ($base === '') {
    $base = WP_CONTENT_DIR;
  }
  return $base . DIRECTORY_SEPARATOR . 'openxe-ticket-portal.log';
}

function openxe_ticket_portal_read_log_tail(string $path, int $maxLines = 200, int $maxBytes = 20000): string
{
  if (!is_file($path) || !is_readable($path)) {
    return '';
  }
  $size = filesize($path);
  if ($size === false || $size <= 0) {
    return '';
  }
  $bytes = min($maxBytes, (int)$size);
  $handle = fopen($path, 'rb');
  if ($handle === false) {
    return '';
  }
  if ($bytes < $size) {
    fseek($handle, -$bytes, SEEK_END);
  }
  $data = fread($handle, $bytes);
  fclose($handle);
  if ($data === false || $data === '') {
    return '';
  }
  $lines = preg_split('/\\r\\n|\\r|\\n/', $data);
  if ($bytes < $size && !empty($lines)) {
    array_shift($lines);
  }
  if (count($lines) > $maxLines) {
    $lines = array_slice($lines, -$maxLines);
  }
  return implode("\n", $lines);
}

function openxe_ticket_portal_mask_value(string $value): string
{
  $value = (string)$value;
  $len = strlen($value);
  if ($len <= 4) {
    return '****';
  }
  return substr($value, 0, 2) . '***' . substr($value, -2);
}

function openxe_ticket_portal_sanitize_log_context($value)
{
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
  if (is_scalar($value)) {
    return (string)$value;
  }
  return $value;
}

function openxe_ticket_portal_log(string $message, array $context = []): void
{
  if (!openxe_ticket_portal_log_enabled()) {
    return;
  }
  $path = openxe_ticket_portal_get_log_path();
  $payload = [
    'time' => gmdate('c'),
    'message' => $message,
    'context' => openxe_ticket_portal_sanitize_log_context($context),
  ];
  $line = wp_json_encode($payload);
  if ($line === false) {
    return;
  }
  @file_put_contents($path, $line . PHP_EOL, FILE_APPEND);
}

function openxe_ticket_portal_get_update_notice(): ?array
{
  $status = sanitize_text_field(wp_unslash($_GET['openxe_ticket_portal_update'] ?? ''));
  if ($status === '') {
    return null;
  }
  if (!in_array($status, ['success', 'error'], true)) {
    return null;
  }
  $message = sanitize_text_field(wp_unslash($_GET['openxe_ticket_portal_update_msg'] ?? ''));
  if ($message === '') {
    $message = $status === 'success' ? 'Plugin wurde aktualisiert.' : 'Update fehlgeschlagen.';
  }
  return [
    'status' => $status,
    'message' => $message,
  ];
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

function openxe_ticket_portal_action_links(array $links): array
{
  $settingsUrl = openxe_ticket_portal_get_settings_url();
  array_unshift($links, '<a href="' . esc_url($settingsUrl) . '">Einstellungen</a>');
  return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'openxe_ticket_portal_action_links');

function openxe_ticket_portal_settings_page(): void
{
  $baseUrlRaw = openxe_ticket_portal_get_base_url();
  $sharedSecretRaw = openxe_ticket_portal_get_shared_secret();
  $baseUrl = esc_attr($baseUrlRaw);
  $sharedSecret = esc_attr($sharedSecretRaw);
  $defaultVerifier = esc_attr((string)get_option('openxe_ticket_portal_default_verifier', 'auto'));
  $logEnabled = openxe_ticket_portal_log_enabled();
  $logPath = openxe_ticket_portal_get_log_path();
  $logPathEsc = esc_html($logPath);
  $logContent = esc_textarea(openxe_ticket_portal_read_log_tail($logPath));
  $updateNotice = openxe_ticket_portal_get_update_notice();
  $downloadUrl = openxe_ticket_portal_get_plugin_download_url();
  $canUpdate = $downloadUrl !== '' && $sharedSecretRaw !== '';
  if ($downloadUrl === '') {
    $updateHint = 'Bitte eine gueltige OpenXE Base URL hinterlegen.';
  } elseif ($sharedSecretRaw === '') {
    $updateHint = 'Shared Secret in OpenXE und hier setzen, damit das Update geladen werden kann.';
  } else {
    $updateHint = 'Das Update wird direkt aus OpenXE geladen.';
  }
  ?>
  <div class="wrap">
    <h1>OpenXE Ticket Portal</h1>
    <?php if ($updateNotice) { ?>
      <?php $noticeClass = $updateNotice['status'] === 'success' ? 'notice notice-success' : 'notice notice-error'; ?>
      <div class="<?php echo esc_attr($noticeClass); ?>">
        <p><?php echo esc_html($updateNotice['message']); ?></p>
      </div>
    <?php } ?>
    <form method="post" action="options.php">
      <?php settings_fields('openxe_ticket_portal'); ?>
      <table class="form-table" role="presentation">
        <tr>
          <th scope="row"><label for="openxe_ticket_portal_base_url">OpenXE Base URL</label></th>
          <td>
            <input type="text" id="openxe_ticket_portal_base_url" name="openxe_ticket_portal_base_url" value="<?php echo $baseUrl; ?>" class="regular-text">
            <p class="description">Beispiel: https://openxe.example.com (https erforderlich, http nur fuer lokale Hosts)</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="openxe_ticket_portal_shared_secret">Shared Secret (optional)</label></th>
          <td>
            <input type="text" id="openxe_ticket_portal_shared_secret" name="openxe_ticket_portal_shared_secret" value="<?php echo $sharedSecret; ?>" class="regular-text" autocomplete="off">
            <p class="description">Muss mit dem Wert in OpenXE uebereinstimmen, wenn aktiviert.</p>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="openxe_ticket_portal_log_enabled">Debug Log aktivieren</label></th>
          <td>
            <label>
              <input type="checkbox" id="openxe_ticket_portal_log_enabled" name="openxe_ticket_portal_log_enabled" value="1" <?php checked($logEnabled); ?>>
              Fehler in Datei schreiben
            </label>
            <p class="description">Logdatei: <?php echo $logPathEsc; ?></p>
          </td>
        </tr>
        <tr>
          <th scope="row">Logauszug</th>
          <td>
            <textarea readonly rows="8" class="large-text"><?php echo $logContent; ?></textarea>
            <button type="submit" class="button" form="openxe-ticket-portal-clear-log-form">Log leeren</button>
          </td>
        </tr>
        <tr>
          <th scope="row">Verbindungstest</th>
          <td>
            <button type="button" class="button" id="openxe-ticket-portal-test">Verbindung testen</button>
            <span id="openxe-ticket-portal-test-result" style="margin-left:8px;"></span>
          </td>
        </tr>
        <tr>
          <th scope="row"><label for="openxe_ticket_portal_default_verifier">Standard Verifikation</label></th>
          <td>
            <select id="openxe_ticket_portal_default_verifier" name="openxe_ticket_portal_default_verifier">
              <option value="auto" <?php selected($defaultVerifier, 'auto'); ?>>Automatisch</option>
              <option value="plz" <?php selected($defaultVerifier, 'plz'); ?>>PLZ</option>
              <option value="email" <?php selected($defaultVerifier, 'email'); ?>>E-Mail</option>
              <option value="code" <?php selected($defaultVerifier, 'code'); ?>>Code</option>
            </select>
          </td>
        </tr>
      </table>
      <?php submit_button(); ?>
    </form>
    <form id="openxe-ticket-portal-clear-log-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <?php wp_nonce_field('openxe_ticket_portal_clear_log', 'openxe_ticket_portal_clear_log_nonce'); ?>
      <input type="hidden" name="action" value="openxe_ticket_portal_clear_log">
    </form>
    <h2>Plugin Update</h2>
    <p>Aktuelle Version: <?php echo esc_html(OPENXE_TICKET_PORTAL_VERSION); ?></p>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
      <?php wp_nonce_field('openxe_ticket_portal_update', 'openxe_ticket_portal_update_nonce'); ?>
      <input type="hidden" name="action" value="openxe_ticket_portal_update">
      <button type="submit" class="button button-secondary" <?php disabled(!$canUpdate); ?>>Update aus OpenXE holen</button>
    </form>
    <p class="description"><?php echo esc_html($updateHint); ?></p>
  </div>
  <script>
  (function () {
    var btn = document.getElementById('openxe-ticket-portal-test');
    var result = document.getElementById('openxe-ticket-portal-test-result');
    if (!btn) {
      return;
    }
    btn.addEventListener('click', function () {
      if (result) {
        result.textContent = 'Teste...';
      }
      var data = new FormData();
      data.append('action', 'openxe_ticket_portal_test');
      data.append('nonce', '<?php echo esc_js(wp_create_nonce('openxe_ticket_portal')); ?>');
      fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>', {
        method: 'POST',
        credentials: 'same-origin',
        body: data
      })
        .then(function (response) { return response.json(); })
        .then(function (resp) {
          if (!result) {
            return;
          }
          if (resp && resp.success) {
            result.textContent = (resp.data && resp.data.message) ? resp.data.message : 'Verbindung ok.';
            return;
          }
          var message = (resp && resp.data && resp.data.message) ? resp.data.message : 'Verbindung fehlgeschlagen.';
          result.textContent = message;
        })
        .catch(function () {
          if (result) {
            result.textContent = 'Verbindung fehlgeschlagen.';
          }
        });
    });
  })();
  </script>
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
  $defaultVerifier = (string)get_option('openxe_ticket_portal_default_verifier', 'auto');

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

  $configJson = esc_attr(wp_json_encode($config));
  $html = '<div class="openxe-portal" data-openxe-portal="1" data-openxe-config="'.$configJson.'">';
  $html .= '<div class="oxp-card">';
  $html .= '<div class="oxp-error" style="display:none"></div>';
  $html .= '<div class="oxp-login">';
  $html .= '<h2>Ticket Portal</h2>';
  $html .= '<div class="oxp-row oxp-token-row">';
  $html .= '<label for="oxp-ticket-number">Ticketnummer</label>';
  $html .= '<input id="oxp-ticket-number" class="oxp-ticket-number" type="text" autocomplete="off">';
  $html .= '</div>';
  $html .= '<div class="oxp-row">';
  $html .= '<label for="oxp-verifier">Verifikation</label>';
  $html .= '<select id="oxp-verifier" class="oxp-verifier">';
  $html .= '<option value="auto">Automatisch</option>';
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
  $html .= '<div class="oxp-media">';
  $html .= '<h3>Dokumente & Bilder</h3>';
  $html .= '<div class="oxp-media-list"></div>';
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
  $html .= '<h3>Offene Angebote</h3>';
  $html .= '<div class="oxp-offer-list"></div>';
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

function openxe_ticket_portal_proxy(string $action, array $payload): void
{
  $result = openxe_ticket_portal_remote($action, $payload);
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

function openxe_ticket_portal_proxy_binary(string $action, array $payload): void
{
  $url = openxe_ticket_portal_get_base_url();
  if ($url === '') {
    wp_die('Base URL not set');
  }
  $url = add_query_arg('action', $action, $url);
  $sharedSecret = (string)get_option('openxe_ticket_portal_shared_secret', '');
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

function openxe_ticket_portal_get_client_ip(): string
{
  return (string)($_SERVER['REMOTE_ADDR'] ?? '');
}

function openxe_ticket_portal_apply_rate_limit(string $action): void
{
  $limit = (int)apply_filters('openxe_ticket_portal_rate_limit', 60, $action);
  $window = (int)apply_filters('openxe_ticket_portal_rate_window', 60, $action);
  if ($limit <= 0 || $window <= 0) {
    return;
  }
  $ip = openxe_ticket_portal_get_client_ip();
  $key = 'oxp_rl_' . md5($action . '|' . $ip);
  $now = time();
  $data = get_transient($key);
  if (!is_array($data) || empty($data['start']) || ($now - (int)$data['start']) >= $window) {
    $data = ['count' => 0, 'start' => $now];
  }
  $data['count'] = (int)$data['count'] + 1;
  set_transient($key, $data, $window);
  if ($data['count'] > $limit) {
    openxe_ticket_portal_log('rate_limited', [
      'action' => $action,
      'ip' => $ip,
      'count' => $data['count'],
      'limit' => $limit,
    ]);
    wp_send_json_error(['message' => 'rate_limited'], 429);
  }
}

function openxe_ticket_portal_ajax_test(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('test');
  $result = openxe_ticket_portal_remote('portal_session', [
    'token' => 'test-connection',
    'verifier_type' => 'plz',
    'verifier_value' => '00000',
  ]);
  if (is_wp_error($result)) {
    wp_send_json_error(['message' => $result->get_error_message()], 500);
  }
  $code = (int)$result['code'];
  $data = $result['data'];
  $error = '';
  if (is_array($data) && isset($data['error'])) {
    $error = (string)$data['error'];
  }
  if ($code >= 200 && $code < 300) {
    wp_send_json_success(['message' => 'Verbindung ok.']);
  }
  if ($error === 'token_not_found') {
    wp_send_json_success(['message' => 'Verbindung ok. Portal erreichbar (Token nicht gefunden).']);
  }
  if ($error === 'portal_disabled') {
    wp_send_json_error(['message' => 'OpenXE Portal ist deaktiviert.'], 503);
  }
  if ($error !== '') {
    wp_send_json_error(['message' => 'Antwort von OpenXE: '.$error], $code ?: 500);
  }
  wp_send_json_error(['message' => 'Verbindung fehlgeschlagen.'], $code ?: 500);
}

function openxe_ticket_portal_ajax_session(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('session');
  $token = sanitize_text_field(wp_unslash($_POST['token'] ?? ''));
  $ticketNumber = sanitize_text_field(wp_unslash($_POST['ticket_number'] ?? ''));
  $verifierType = sanitize_text_field(wp_unslash($_POST['verifier_type'] ?? ''));
  $verifierValue = sanitize_text_field(wp_unslash($_POST['verifier_value'] ?? ''));
  if (($token === '' && $ticketNumber === '') || $verifierType === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  $payload = [
    'verifier_type' => $verifierType,
    'verifier_value' => $verifierValue,
  ];
  if ($token !== '') {
    $payload['token'] = $token;
  }
  if ($ticketNumber !== '') {
    $payload['ticket_number'] = $ticketNumber;
  }
  openxe_ticket_portal_proxy('portal_session', $payload);
}

function openxe_ticket_portal_ajax_magic(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('magic');
  $magicToken = sanitize_text_field(wp_unslash($_POST['magic_token'] ?? ''));
  if ($magicToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_magic', [
    'magic_token' => $magicToken,
  ]);
}

function openxe_ticket_portal_ajax_status(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('status');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_status', ['session_token' => $sessionToken]);
}

function openxe_ticket_portal_ajax_messages(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('messages');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_messages', ['session_token' => $sessionToken]);
}

function openxe_ticket_portal_ajax_message(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('message');
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

function openxe_ticket_portal_ajax_offers(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('offers');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_offers', ['session_token' => $sessionToken]);
}

function openxe_ticket_portal_ajax_offer(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('offer');
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
  openxe_ticket_portal_apply_rate_limit('notifications_get');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_notifications', ['session_token' => $sessionToken]);
}

function openxe_ticket_portal_ajax_notifications_set(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('notifications_set');
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

function openxe_ticket_portal_ajax_media(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('media');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy('portal_media', ['session_token' => $sessionToken]);
}

function openxe_ticket_portal_ajax_media_download(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('media_download');
  $sessionToken = sanitize_text_field(wp_unslash($_POST['session_token'] ?? ''));
  $mediaId = sanitize_text_field(wp_unslash($_POST['media_id'] ?? ''));
  if ($sessionToken === '' || $mediaId === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  openxe_ticket_portal_proxy_binary('portal_media_download', [
    'session_token' => $sessionToken,
    'media_id' => $mediaId,
  ]);
}

add_action('wp_ajax_nopriv_openxe_ticket_portal_session', 'openxe_ticket_portal_ajax_session');
add_action('wp_ajax_openxe_ticket_portal_session', 'openxe_ticket_portal_ajax_session');
add_action('wp_ajax_nopriv_openxe_ticket_portal_magic', 'openxe_ticket_portal_ajax_magic');
add_action('wp_ajax_openxe_ticket_portal_magic', 'openxe_ticket_portal_ajax_magic');
add_action('wp_ajax_nopriv_openxe_ticket_portal_status', 'openxe_ticket_portal_ajax_status');
add_action('wp_ajax_openxe_ticket_portal_status', 'openxe_ticket_portal_ajax_status');
add_action('wp_ajax_nopriv_openxe_ticket_portal_messages', 'openxe_ticket_portal_ajax_messages');
add_action('wp_ajax_openxe_ticket_portal_messages', 'openxe_ticket_portal_ajax_messages');
add_action('wp_ajax_nopriv_openxe_ticket_portal_message', 'openxe_ticket_portal_ajax_message');
add_action('wp_ajax_openxe_ticket_portal_message', 'openxe_ticket_portal_ajax_message');
add_action('wp_ajax_nopriv_openxe_ticket_portal_offer', 'openxe_ticket_portal_ajax_offer');
add_action('wp_ajax_openxe_ticket_portal_offer', 'openxe_ticket_portal_ajax_offer');
add_action('wp_ajax_nopriv_openxe_ticket_portal_offers', 'openxe_ticket_portal_ajax_offers');
add_action('wp_ajax_openxe_ticket_portal_offers', 'openxe_ticket_portal_ajax_offers');
add_action('wp_ajax_nopriv_openxe_ticket_portal_notifications_get', 'openxe_ticket_portal_ajax_notifications_get');
add_action('wp_ajax_openxe_ticket_portal_notifications_get', 'openxe_ticket_portal_ajax_notifications_get');
add_action('wp_ajax_nopriv_openxe_ticket_portal_notifications_set', 'openxe_ticket_portal_ajax_notifications_set');
add_action('wp_ajax_openxe_ticket_portal_notifications_set', 'openxe_ticket_portal_ajax_notifications_set');
add_action('wp_ajax_nopriv_openxe_ticket_portal_media', 'openxe_ticket_portal_ajax_media');
add_action('wp_ajax_openxe_ticket_portal_media', 'openxe_ticket_portal_ajax_media');
add_action('wp_ajax_nopriv_openxe_ticket_portal_media_download', 'openxe_ticket_portal_ajax_media_download');
add_action('wp_ajax_openxe_ticket_portal_media_download', 'openxe_ticket_portal_ajax_media_download');
add_action('wp_ajax_openxe_ticket_portal_test', 'openxe_ticket_portal_ajax_test');

function openxe_ticket_portal_clear_log_action(): void
{
  if (!current_user_can('manage_options')) {
    wp_die('forbidden');
  }
  check_admin_referer('openxe_ticket_portal_clear_log', 'openxe_ticket_portal_clear_log_nonce');
  $path = openxe_ticket_portal_get_log_path();
  if (is_file($path)) {
    @unlink($path);
  }
  wp_safe_redirect(admin_url('options-general.php?page=openxe-ticket-portal'));
  exit;
}
add_action('admin_post_openxe_ticket_portal_clear_log', 'openxe_ticket_portal_clear_log_action');

function openxe_ticket_portal_download_plugin_zip(string $url, string $sharedSecret): string|WP_Error
{
  $headers = [];
  if ($sharedSecret !== '') {
    $headers['X-OpenXE-Portal-Secret'] = $sharedSecret;
  }
  $response = wp_remote_get($url, [
    'timeout' => 60,
    'headers' => $headers,
  ]);
  if (is_wp_error($response)) {
    return $response;
  }
  $status = (int)wp_remote_retrieve_response_code($response);
  if ($status !== 200) {
    $message = 'Update-Download fehlgeschlagen (HTTP ' . $status . ').';
    return new WP_Error('openxe_ticket_portal_update_http', $message);
  }
  $body = (string)wp_remote_retrieve_body($response);
  if ($body === '' || strlen($body) < 10) {
    return new WP_Error('openxe_ticket_portal_update_empty', 'Update-Download leer oder ungueltig.');
  }
  if (substr($body, 0, 2) !== 'PK') {
    return new WP_Error('openxe_ticket_portal_update_invalid', 'Update-Download ist kein ZIP-Archiv.');
  }
  $tmp = wp_tempnam('openxe-ticket-portal.zip');
  if ($tmp === false) {
    return new WP_Error('openxe_ticket_portal_update_tmp', 'Temporaere Datei konnte nicht erstellt werden.');
  }
  if (@file_put_contents($tmp, $body) === false) {
    @unlink($tmp);
    return new WP_Error('openxe_ticket_portal_update_write', 'ZIP konnte nicht gespeichert werden.');
  }
  return $tmp;
}

function openxe_ticket_portal_prepare_update_dir(): string|WP_Error
{
  $base = rtrim(WP_CONTENT_DIR, '/\\') . DIRECTORY_SEPARATOR . 'upgrade';
  if (!is_dir($base) && !wp_mkdir_p($base)) {
    return new WP_Error('openxe_ticket_portal_update_tmp', 'Update-Verzeichnis konnte nicht erstellt werden.');
  }
  $suffix = gmdate('YmdHis') . '-' . wp_generate_password(6, false, false);
  $dir = $base . DIRECTORY_SEPARATOR . 'openxe-ticket-portal-' . $suffix;
  if (!wp_mkdir_p($dir)) {
    return new WP_Error('openxe_ticket_portal_update_tmp', 'Update-Verzeichnis konnte nicht erstellt werden.');
  }
  return $dir;
}

function openxe_ticket_portal_find_extracted_dir(string $baseDir): string|WP_Error
{
  $expected = $baseDir . DIRECTORY_SEPARATOR . 'openxe-ticket-portal' . DIRECTORY_SEPARATOR . 'openxe-ticket-portal.php';
  if (is_file($expected)) {
    return dirname($expected);
  }
  $rootFile = $baseDir . DIRECTORY_SEPARATOR . 'openxe-ticket-portal.php';
  if (is_file($rootFile)) {
    return $baseDir;
  }
  $matches = glob($baseDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'openxe-ticket-portal.php');
  if (!empty($matches)) {
    return dirname($matches[0]);
  }
  return new WP_Error('openxe_ticket_portal_update_invalid', 'Plugin-Dateien konnten im Update nicht gefunden werden.');
}

function openxe_ticket_portal_cleanup_dir(string $dir): void
{
  global $wp_filesystem;
  if ($wp_filesystem instanceof WP_Filesystem_Base) {
    if ($wp_filesystem->exists($dir)) {
      $wp_filesystem->delete($dir, true);
      return;
    }
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
          openxe_ticket_portal_cleanup_dir($path);
        } else {
          @unlink($path);
        }
      }
    }
    @rmdir($dir);
  }
}

function openxe_ticket_portal_update_from_openxe(): void
{
  if (!current_user_can('manage_options')) {
    wp_die('forbidden');
  }
  check_admin_referer('openxe_ticket_portal_update', 'openxe_ticket_portal_update_nonce');
  $redirect = openxe_ticket_portal_get_settings_url();
  $baseUrl = openxe_ticket_portal_get_base_url();
  $sharedSecret = openxe_ticket_portal_get_shared_secret();
  if ($baseUrl === '') {
    $redirect = add_query_arg([
      'openxe_ticket_portal_update' => 'error',
      'openxe_ticket_portal_update_msg' => 'OpenXE Base URL fehlt.',
    ], $redirect);
    if (headers_sent()) {
      echo esc_html($errorMessage);
      echo '<br><a href="' . esc_url($redirect) . '">Zurueck zu den Einstellungen</a>';
      exit;
    }
    wp_safe_redirect($redirect);
    exit;
  }
  if ($sharedSecret === '') {
    $redirect = add_query_arg([
      'openxe_ticket_portal_update' => 'error',
      'openxe_ticket_portal_update_msg' => 'Shared Secret fehlt.',
    ], $redirect);
    wp_safe_redirect($redirect);
    exit;
  }
  $downloadUrl = openxe_ticket_portal_get_plugin_download_url();
  if ($downloadUrl === '') {
    $redirect = add_query_arg([
      'openxe_ticket_portal_update' => 'error',
      'openxe_ticket_portal_update_msg' => 'Plugin-Download URL ist ungueltig.',
    ], $redirect);
    wp_safe_redirect($redirect);
    exit;
  }

  $tmp = openxe_ticket_portal_download_plugin_zip($downloadUrl, $sharedSecret);
  if (is_wp_error($tmp)) {
    openxe_ticket_portal_log('plugin_update_download_failed', [
      'message' => $tmp->get_error_message(),
    ]);
    $redirect = add_query_arg([
      'openxe_ticket_portal_update' => 'error',
      'openxe_ticket_portal_update_msg' => $tmp->get_error_message(),
    ], $redirect);
    wp_safe_redirect($redirect);
    exit;
  }

  require_once ABSPATH . 'wp-admin/includes/file.php';
  if (!WP_Filesystem()) {
    @unlink($tmp);
    $redirect = add_query_arg([
      'openxe_ticket_portal_update' => 'error',
      'openxe_ticket_portal_update_msg' => 'WP Filesystem konnte nicht initialisiert werden.',
    ], $redirect);
    if (headers_sent()) {
      echo 'WP Filesystem konnte nicht initialisiert werden.';
      echo '<br><a href="' . esc_url($redirect) . '">Zurueck zu den Einstellungen</a>';
      exit;
    }
    wp_safe_redirect($redirect);
    exit;
  }

  $workDir = openxe_ticket_portal_prepare_update_dir();
  if (is_wp_error($workDir)) {
    @unlink($tmp);
    $redirect = add_query_arg([
      'openxe_ticket_portal_update' => 'error',
      'openxe_ticket_portal_update_msg' => $workDir->get_error_message(),
    ], $redirect);
    if (headers_sent()) {
      echo esc_html($workDir->get_error_message());
      echo '<br><a href="' . esc_url($redirect) . '">Zurueck zu den Einstellungen</a>';
      exit;
    }
    wp_safe_redirect($redirect);
    exit;
  }

  $unzip = unzip_file($tmp, $workDir);
  @unlink($tmp);
  if (is_wp_error($unzip)) {
    openxe_ticket_portal_cleanup_dir($workDir);
    $redirect = add_query_arg([
      'openxe_ticket_portal_update' => 'error',
      'openxe_ticket_portal_update_msg' => $unzip->get_error_message(),
    ], $redirect);
    if (headers_sent()) {
      echo esc_html($unzip->get_error_message());
      echo '<br><a href="' . esc_url($redirect) . '">Zurueck zu den Einstellungen</a>';
      exit;
    }
    wp_safe_redirect($redirect);
    exit;
  }

  $sourceDir = openxe_ticket_portal_find_extracted_dir($workDir);
  if (is_wp_error($sourceDir)) {
    openxe_ticket_portal_cleanup_dir($workDir);
    $redirect = add_query_arg([
      'openxe_ticket_portal_update' => 'error',
      'openxe_ticket_portal_update_msg' => $sourceDir->get_error_message(),
    ], $redirect);
    if (headers_sent()) {
      echo esc_html($sourceDir->get_error_message());
      echo '<br><a href="' . esc_url($redirect) . '">Zurueck zu den Einstellungen</a>';
      exit;
    }
    wp_safe_redirect($redirect);
    exit;
  }

  $targetDir = rtrim(plugin_dir_path(__FILE__), '/\\');
  $copyResult = copy_dir($sourceDir, $targetDir);
  openxe_ticket_portal_cleanup_dir($workDir);
  if (is_wp_error($copyResult)) {
    $errorMessage = $copyResult->get_error_message();
    openxe_ticket_portal_log('plugin_update_failed', [
      'message' => $errorMessage,
    ]);
    $redirect = add_query_arg([
      'openxe_ticket_portal_update' => 'error',
      'openxe_ticket_portal_update_msg' => $errorMessage,
    ], $redirect);
    wp_safe_redirect($redirect);
    exit;
  }

  openxe_ticket_portal_log('plugin_update_success', [
    'version' => OPENXE_TICKET_PORTAL_VERSION,
  ]);
  $redirect = add_query_arg([
    'openxe_ticket_portal_update' => 'success',
    'openxe_ticket_portal_update_msg' => 'Plugin aktualisiert.',
  ], $redirect);
  if (headers_sent()) {
    echo 'Plugin aktualisiert. ';
    echo '<a href="' . esc_url($redirect) . '">Zurueck zu den Einstellungen</a>';
    exit;
  }
  wp_safe_redirect($redirect);
  exit;
}
add_action('admin_post_openxe_ticket_portal_update', 'openxe_ticket_portal_update_from_openxe');
