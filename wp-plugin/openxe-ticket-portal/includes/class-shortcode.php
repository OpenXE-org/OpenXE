<?php
/**
 * Shortcode management for OpenXE Ticket Portal
 */

if (!defined('ABSPATH')) {
    exit;
}

class OpenXE_Ticket_Portal_Shortcode {
    /**
     * Register shortcode
     */
    public static function register(): void {
        add_shortcode('openxe_ticket_portal', [self::class, 'render']);
    }

    /**
     * Render shortcode HTML
     */
    public static function render($atts): string {
        wp_enqueue_style('openxe-ticket-portal');
        wp_enqueue_script('openxe-ticket-portal');

        $baseUrl = OpenXE_Ticket_Portal_Settings::get_base_url();
        $defaultVerifier = OpenXE_Ticket_Portal_Settings::get_default_verifier();

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

        $css = OpenXE_Ticket_Portal_Theme_Manager::get_css_variables();
        wp_add_inline_style('openxe-ticket-portal', $css);

        $configJson = esc_attr(wp_json_encode($config));
        
        ob_start();
        ?>
        <div class="openxe-portal" data-openxe-portal="1" data-openxe-config="<?php echo $configJson; ?>" role="application" aria-label="<?php esc_attr_e('OpenXE Ticket Portal', 'openxe-ticket-portal'); ?>">
            <div class="oxp-card" role="main">
                <div class="oxp-error" style="display:none" role="alert" aria-live="assertive"></div>
                
                <section class="oxp-login" aria-labelledby="oxp-login-title">
                    <h2 id="oxp-login-title"><?php _e('Ticket Portal', 'openxe-ticket-portal'); ?></h2>
                    <div class="oxp-row oxp-token-row">
                        <label for="oxp-ticket-number"><?php _e('Ticketnummer', 'openxe-ticket-portal'); ?></label>
                        <input id="oxp-ticket-number" class="oxp-ticket-number" type="text" autocomplete="off" aria-required="true">
                    </div>
                    <div class="oxp-row">
                        <label for="oxp-verifier"><?php _e('Verifikation', 'openxe-ticket-portal'); ?></label>
                        <select id="oxp-verifier" class="oxp-verifier">
                            <option value="auto"><?php _e('Automatisch', 'openxe-ticket-portal'); ?></option>
                            <option value="plz"><?php _e('PLZ', 'openxe-ticket-portal'); ?></option>
                            <option value="email"><?php _e('E-Mail', 'openxe-ticket-portal'); ?></option>
                            <option value="code"><?php _e('Code', 'openxe-ticket-portal'); ?></option>
                        </select>
                    </div>
                    <div class="oxp-row">
                        <label for="oxp-verifier-value"><?php _e('Verifikationswert', 'openxe-ticket-portal'); ?></label>
                        <input id="oxp-verifier-value" class="oxp-verifier-value" type="text" autocomplete="off" aria-required="true">
                    </div>
                    <button type="button" class="oxp-login-btn"><?php _e('Zugriff starten', 'openxe-ticket-portal'); ?></button>
                    <div class="oxp-login-msg" role="status"></div>
                </section>

                <div class="oxp-main" style="display:none" aria-hidden="true">
                    <section class="oxp-status" aria-labelledby="oxp-status-title">
                        <h3 id="oxp-status-title" class="screen-reader-text"><?php _e('Status', 'openxe-ticket-portal'); ?></h3>
                        <div class="oxp-status-label" aria-hidden="true"><?php _e('Status', 'openxe-ticket-portal'); ?></div>
                        <div class="oxp-status-value" aria-live="polite"></div>
                        <div class="oxp-status-updated"></div>
                    </section>

                    <section class="oxp-ticket-details" aria-labelledby="oxp-ticket-details-title">
                        <h3 id="oxp-ticket-details-title"><?php _e('Ticket-Details', 'openxe-ticket-portal'); ?></h3>
                        <div class="oxp-detail-row">
                            <span class="oxp-detail-label"><?php _e('Ticketnummer:', 'openxe-ticket-portal'); ?></span>
                            <span class="oxp-ticket-number-display"></span>
                        </div>
                        <div class="oxp-detail-row">
                            <span class="oxp-detail-label"><?php _e('Kunde:', 'openxe-ticket-portal'); ?></span>
                            <span class="oxp-customer-name"></span>
                        </div>
                        <div class="oxp-detail-row">
                            <span class="oxp-detail-label"><?php _e('Adresse:', 'openxe-ticket-portal'); ?></span>
                            <span class="oxp-customer-address"></span>
                        </div>
                    </section>
                    
                    <div class="oxp-actions" role="toolbar" aria-label="<?php esc_attr_e('Portal Aktionen', 'openxe-ticket-portal'); ?>">
                        <button type="button" class="oxp-refresh"><?php _e('Aktualisieren', 'openxe-ticket-portal'); ?></button>
                        <a class="oxp-print" target="_blank" rel="noopener"><?php _e('Druckformular', 'openxe-ticket-portal'); ?></a>
                        <a class="oxp-download" target="_blank" rel="noopener"><?php _e('Download', 'openxe-ticket-portal'); ?></a>
                        <button type="button" class="oxp-offer-toggle"><?php _e('Angebot bestätigen', 'openxe-ticket-portal'); ?></button>
                    </div>

                    <section class="oxp-notifications" aria-labelledby="oxp-notify-title">
                        <h3 id="oxp-notify-title"><?php _e('Benachrichtigungen', 'openxe-ticket-portal'); ?></h3>
                        <div class="oxp-notifications-list" role="group"></div>
                        <button type="button" class="oxp-notifications-save"><?php _e('Speichern', 'openxe-ticket-portal'); ?></button>
                        <div class="oxp-notifications-msg" role="status"></div>
                    </section>
                    
                    <section class="oxp-media" aria-labelledby="oxp-media-title">
                        <h3 id="oxp-media-title"><?php _e('Dokumente & Bilder', 'openxe-ticket-portal'); ?></h3>
                        <div class="oxp-media-list"></div>
                    </section>

                    <section class="oxp-chat" aria-labelledby="oxp-chat-title">
                        <h3 id="oxp-chat-title"><?php _e('Nachrichten', 'openxe-ticket-portal'); ?></h3>
                        <div class="oxp-messages" role="log" aria-live="polite"></div>
                        <div class="oxp-row">
                            <label for="oxp-message-text"><?php _e('Neue Nachricht', 'openxe-ticket-portal'); ?></label>
                            <textarea id="oxp-message-text" class="oxp-message-text" rows="3"></textarea>
                        </div>
                        <button type="button" class="oxp-send"><?php _e('Senden', 'openxe-ticket-portal'); ?></button>
                        <div class="oxp-message-msg" role="status"></div>
                    </section>

                    <section class="oxp-offer" style="display:none" aria-labelledby="oxp-offer-title">
                        <h3 id="oxp-offer-title"><?php _e('Offene Angebote', 'openxe-ticket-portal'); ?></h3>
                        <div class="oxp-offer-list"></div>
                        <h3><?php _e('Angebot bestätigen', 'openxe-ticket-portal'); ?></h3>
                        <div class="oxp-row">
                            <label for="oxp-offer-id"><?php _e('Angebot ID', 'openxe-ticket-portal'); ?></label>
                            <input id="oxp-offer-id" class="oxp-offer-id" type="text" autocomplete="off">
                        </div>
                        <div class="oxp-row">
                            <label for="oxp-offer-action"><?php _e('Aktion', 'openxe-ticket-portal'); ?></label>
                            <select id="oxp-offer-action" class="oxp-offer-action">
                                <option value="accept"><?php _e('Bestätigen', 'openxe-ticket-portal'); ?></option>
                                <option value="decline"><?php _e('Ablehnen', 'openxe-ticket-portal'); ?></option>
                            </select>
                        </div>
                        <div class="oxp-row">
                            <label for="oxp-offer-comment"><?php _e('Kommentar', 'openxe-ticket-portal'); ?></label>
                            <textarea id="oxp-offer-comment" class="oxp-offer-comment" rows="3"></textarea>
                        </div>
                        <div class="oxp-row">
                            <label for="oxp-offer-agb"><?php _e('AGB Version (optional)', 'openxe-ticket-portal'); ?></label>
                            <input id="oxp-offer-agb" class="oxp-offer-agb" type="text" autocomplete="off">
                        </div>
                        <div class="oxp-row oxp-checkbox">
                            <input id="oxp-offer-accept" class="oxp-offer-accept" type="checkbox" value="1">
                            <label for="oxp-offer-accept"><?php _e('Ich akzeptiere die AGB für die Bestätigung.', 'openxe-ticket-portal'); ?></label>
                        </div>
                        <button type="button" class="oxp-offer-submit"><?php _e('Senden', 'openxe-ticket-portal'); ?></button>
                        <div class="oxp-offer-msg" role="status"></div>
                    </section>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
