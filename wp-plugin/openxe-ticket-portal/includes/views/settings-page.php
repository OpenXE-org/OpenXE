<?php
/**
 * Admin Settings Page View for OpenXE Ticket Portal
 */

if (!defined('ABSPATH')) {
    exit;
}

$baseUrl = esc_attr(OpenXE_Ticket_Portal_Settings::get_base_url());
$sharedSecret = esc_attr(OpenXE_Ticket_Portal_Settings::get_shared_secret());
$defaultVerifier = esc_attr(OpenXE_Ticket_Portal_Settings::get_default_verifier());
$logEnabled = OpenXE_Ticket_Portal_Settings::is_log_enabled();

$activeTab = $_GET['tab'] ?? 'general';
$themePreset = get_option('openxe_ticket_portal_theme_preset', 'default');
$themeColors = get_option('openxe_ticket_portal_theme_colors', []);
$customCss = get_option('openxe_ticket_portal_custom_css', '');

$logPath = openxe_ticket_portal_get_log_path();
$logContent = esc_textarea(openxe_ticket_portal_read_log_tail($logPath));

function openxe_ticket_portal_get_update_notice(): ?array {
    $status = sanitize_text_field(wp_unslash($_GET['openxe_ticket_portal_update'] ?? ''));
    if ($status === '' || !in_array($status, ['success', 'error'], true)) return null;
    $message = sanitize_text_field(wp_unslash($_GET['openxe_ticket_portal_update_msg'] ?? ''));
    if ($message === '') $message = $status === 'success' ? 'Plugin wurde aktualisiert.' : 'Update fehlgeschlagen.';
    return ['status' => $status, 'message' => $message];
}

$updateNotice = openxe_ticket_portal_get_update_notice();
$downloadUrl = OpenXE_Ticket_Portal_Settings::get_base_url() . '/index.php?module=ticket&action=portal_plugin_download';
$canUpdate = $downloadUrl !== '' && $sharedSecret !== '';

// Log clear notice
$logClearStatus = sanitize_text_field(wp_unslash($_GET['openxe_ticket_portal_log_cleared'] ?? ''));
$logClearMsg = sanitize_text_field(wp_unslash($_GET['openxe_ticket_portal_log_msg'] ?? ''));
$logClearNotice = null;
if ($logClearStatus !== '' && in_array($logClearStatus, ['success', 'error', 'info'], true)) {
    $logClearNotice = ['status' => $logClearStatus, 'message' => $logClearMsg];
}

?>
<div class="wrap">
    <h1>OpenXE Ticket Portal</h1>
    
    <nav class="nav-tab-wrapper">
        <a href="?page=openxe-ticket-portal&tab=general" class="nav-tab <?php echo $activeTab === 'general' ? 'nav-tab-active' : ''; ?>">Allgemein</a>
        <a href="?page=openxe-ticket-portal&tab=theme" class="nav-tab <?php echo $activeTab === 'theme' ? 'nav-tab-active' : ''; ?>">Layout</a>
    </nav>

    <?php if ($updateNotice) : ?>
        <div class="notice notice-<?php echo $updateNotice['status'] === 'success' ? 'success' : 'error'; ?> is-dismissible">
            <p><?php echo esc_html($updateNotice['message']); ?></p>
        </div>
    <?php endif; ?>

    <?php if ($logClearNotice) : ?>
        <div class="notice notice-<?php echo $logClearNotice['status'] === 'error' ? 'error' : ($logClearNotice['status'] === 'info' ? 'info' : 'success'); ?> is-dismissible">
            <p><?php echo esc_html($logClearNotice['message']); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="options.php">
        <?php 
        if ($activeTab === 'general') :
            settings_fields('openxe_ticket_portal');
            ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="openxe_ticket_portal_base_url">OpenXE Base URL</label></th>
                    <td>
                        <input type="text" id="openxe_ticket_portal_base_url" name="openxe_ticket_portal_base_url" value="<?php echo $baseUrl; ?>" class="regular-text">
                        <p class="description">Beispiel: https://openxe.example.com</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="openxe_ticket_portal_shared_secret">Shared Secret (optional)</label></th>
                    <td>
                        <input type="text" id="openxe_ticket_portal_shared_secret" name="openxe_ticket_portal_shared_secret" value="<?php echo $sharedSecret; ?>" class="regular-text" autocomplete="off">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="openxe_ticket_portal_log_enabled">Debug Log aktivieren</label></th>
                    <td>
                        <label>
                            <input type="checkbox" id="openxe_ticket_portal_log_enabled" name="openxe_ticket_portal_log_enabled" value="1" <?php checked($logEnabled); ?>>
                            Fehler in Datei schreiben
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Logauszug</th>
                    <td>
                        <textarea readonly rows="8" class="large-text"><?php echo $logContent; ?></textarea>
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
            <?php 
        else : 
            settings_fields('openxe_ticket_portal_theme');
            ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="openxe_ticket_portal_theme_preset">Design-Vorlage</label></th>
                    <td>
                        <select id="openxe_ticket_portal_theme_preset" name="openxe_ticket_portal_theme_preset">
                            <option value="default" <?php selected($themePreset, 'default'); ?>>Standard (OpenXE)</option>
                            <option value="dark" <?php selected($themePreset, 'dark'); ?>>Dark Mode</option>
                            <option value="minimal" <?php selected($themePreset, 'minimal'); ?>>Minimalist</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Farben (Override)</th>
                    <td>
                        <label>Primary: <input type="color" name="openxe_ticket_portal_theme_colors[primary]" value="<?php echo esc_attr($themeColors['primary'] ?? ''); ?>"></label><br><br>
                        <label>Background: <input type="color" name="openxe_ticket_portal_theme_colors[bg]" value="<?php echo esc_attr($themeColors['bg'] ?? ''); ?>"></label><br><br>
                        <label>Text: <input type="color" name="openxe_ticket_portal_theme_colors[text]" value="<?php echo esc_attr($themeColors['text'] ?? ''); ?>"></label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="openxe_ticket_portal_custom_css">Eigenes CSS</label></th>
                    <td>
                        <textarea id="openxe_ticket_portal_custom_css" name="openxe_ticket_portal_custom_css" rows="10" class="large-text code"><?php echo esc_textarea($customCss); ?></textarea>
                    </td>
                </tr>
            </table>
            <?php 
        endif; 
        submit_button(); 
        ?>
    </form>

    <?php if ($activeTab === 'general') : ?>
        <h2>Log Verwaltung</h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" onsubmit="return confirm('Log wirklich löschen?');">
            <?php wp_nonce_field('openxe_ticket_portal_clear_log', 'openxe_ticket_portal_clear_log_nonce'); ?>
            <input type="hidden" name="action" value="openxe_ticket_portal_clear_log">
            <button type="submit" class="button button-secondary">Log löschen</button>
        </form>
        
        <h2>Plugin Update</h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('openxe_ticket_portal_update', 'openxe_ticket_portal_update_nonce'); ?>
            <input type="hidden" name="action" value="openxe_ticket_portal_update">
            <button type="submit" class="button button-secondary" <?php disabled(!$canUpdate); ?>>Update aus OpenXE holen</button>
        </form>
    <?php endif; ?>
</div>

<script>
(function () {
    var btn = document.getElementById('openxe-ticket-portal-test');
    var result = document.getElementById('openxe-ticket-portal-test-result');
    if (!btn) return;
    btn.addEventListener('click', function () {
        result.textContent = 'Teste...';
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
            if (!result) return;
            var msg = (resp && resp.data && resp.data.message) ? resp.data.message : (resp.success ? 'Verbindung ok.' : 'Verbindung fehlgeschlagen.');
            result.textContent = msg;
        })
        .catch(function () {
            if (result) result.textContent = 'Verbindung fehlgeschlagen.';
        });
    });
})();
</script>
