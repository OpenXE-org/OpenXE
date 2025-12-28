# OpenXE Ticket Portal - WordPress Plugin

WordPress-Integration für das OpenXE Ticket Portal. Ermöglicht Kunden den sicheren Zugriff auf Reparaturstatus, Angebote und Dokumente.

## Features

✅ **Secure Authentication**
- PLZ/Email-Verification
- Magic Links (Click-through from Email)
- Session Management mit Token-Hashing

✅ **Customer Portal**
- Real-time Ticket Status
- Message History mit TTS (Vorlesen)
- Offer Acceptance (Double Opt-In)
- Media Downloads (Images, PDFs)

✅ **Security**
- Rate Limiting (60 req/min)
- CSRF Protection (WordPress Nonces)
- Shared Secret Authentication
- Binary Proxy für sichere Downloads

✅ **Modern UI**
- Avant-Garde Design
- Responsive (Mobile-first)
- Dark Mode Ready
- Micro-Animations

## Requirements

- **WordPress:** 5.8+
- **PHP:** 7.4+ (empfohlen: 8.0+)
- **SSL/TLS:** HTTPS ist Pflicht!
- **OpenXE Backend:** 2023.x+

## Installation

### 1. Plugin hochladen

```
WordPress Admin → Plugins → Installieren
→ "Plugin hochladen"
→ openxe-ticket-portal.zip auswählen
→ "Jetzt installieren"
→ "Aktivieren"
```

### 2. Konfiguration

Navigieren Sie zu **Einstellungen → OpenXE Ticket Portal**:

```
OpenXE Base URL:
  https://openxe.ihre-firma.de

Shared Secret:
  [Aus OpenXE kopieren: Ticket → Portal Einstellungen]
  
[Verbindung testen] → Muss "Verbindung ok." zeigen
```

### 3. Portal-Seite erstellen

```
WordPress → Seiten → Erstellen
Titel: Ticket Portal
Inhalt: [openxe_ticket_portal]
→ Veröffentlichen
```

### 4. URL zurück in OpenXE

```
OpenXE → Ticket → Portal Einstellungen
Portal URL: https://ihr-wordpress.de/ticket-portal
→ Speichern
```

## Shortcode

```
[openxe_ticket_portal]
```

**Optionale Parameter:**
```
[openxe_ticket_portal verifier="plz"]
```

Verfügbare Verifier:
- `auto` (Standard): Automatische Erkennung
- `plz`: Nur PLZ-Verifikation
- `email`: Nur Email-Code
- `code`: Manueller Code-Input

## Hooks & Filter

### Rate Limit anpassen

```php
// functions.php
add_filter('openxe_ticket_portal_rate_limit', function($limit, $action) {
  if ($action === 'media_download') {
    return 30; // 30 Downloads/Minute
  }
  return $limit; // Default: 60
}, 10, 2);
```

### Rate Window ändern

```php
add_filter('openxe_ticket_portal_rate_window', function($window, $action) {
  return 120; // 2 Minuten statt 60 Sekunden
}, 10, 2);
```

### Custom CSS

```css
/* Eigenes Theme: style.css */

.openxe-portal .oxp-card {
  border-radius: 16px;
  box-shadow: 0 8px 32px rgba(0,0,0,0.1);
}

.openxe-portal .oxp-btn {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
```

## Dateistruktur

```
openxe-ticket-portal/
├── openxe-ticket-portal.php  # Haupt-Plugin-Datei
├── assets/
│   ├── portal.js             # Frontend-Logik
│   └── portal.css            # Styling
├── README.md                 # Diese Datei
└── LICENSE                   # GPL-3.0
```

## Debugging

### Debug-Modus aktivieren

```php
// wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

Logs finden Sie in: `wp-content/debug.log`

### Verbindungstest schlägt fehl

**Checkliste:**
1. Base URL korrekt? (ohne `/` am Ende)
2. Shared Secret identisch?
3. OpenXE erreichbar? (`curl -I https://openxe...`)
4. Firewall-Regeln geprüft?

**Manual Test:**
```bash
curl -X POST https://openxe.ihre-firma.de/index.php?module=ticket&action=portal_session \
  -H "Content-Type: application/json" \
  -H "X-OpenXE-Portal-Secret: IHR_SECRET" \
  -d '{"token":"test","verifier_type":"plz","verifier_value":"00000"}'
  
# Erwartete Antwort:
{"error":"token_not_found"} # = Verbindung funktioniert!
```

### Rate Limiting Probleme

**Transients löschen:**
```php
// wp-admin → Tools → Site Health → debug.php ausführen:
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_oxp_rl_%'");
```

## Performance

### Caching

Das Plugin ist kompatibel mit:
- ✅ WP Rocket
- ✅ W3 Total Cache
- ✅ WP Super Cache

**Wichtig:** Schließen Sie die Portal-Seite vom Caching aus:
```
# WP Rocket → Settings → Excluded Pages
/ticket-portal
```

### CDN

Assets können über CDN ausgeliefert werden:
```php
// functions.php
add_filter('openxe_ticket_portal_asset_url', function($url) {
  return str_replace(
    site_url(),
    'https://cdn.ihre-firma.de',
    $url
  );
});
```

## Security Best Practices

### 1. SSL/TLS erzwingen

```php
// wp-config.php
define('FORCE_SSL_ADMIN', true);

// .htaccess
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 2. Shared Secret sicher speichern

❌ **Nicht:**
```php
// Im Theme oder Plugin
$secret = 'mein-geheimes-password';
```

✅ **Besser:**
```php
// wp-config.php
define('OPENXE_PORTAL_SECRET', 'xxx...');

// Dann in Plugin-Settings:
update_option('openxe_ticket_portal_shared_secret', OPENXE_PORTAL_SECRET);
```

### 3. Rate Limiting monitoren

```bash
# Regelmäßig prüfen:
tail -f /var/www/html/wp-content/debug.log | grep "rate_limited"
```

Bei Auffälligkeiten:
→ IP blocken (Fail2Ban, Cloudflare)
→ Captcha einführen (z.B. reCAPTCHA)

## Changelog

### Version 1.0.0 (2025-12-28)

**Initial Release**
- ✅ Portal-Authentifizierung (4 Methoden)
- ✅ Status-Anzeige & Nachrichten
- ✅ Angebots-Workflow (inkl. DOI)
- ✅ Media-Downloads
- ✅ Benachrichtigungspräferenzen
- ✅ Text-to-Speech (device-native)
- ✅ Rate Limiting & Security
- ✅ Avant-Garde UI

## Roadmap

### Version 1.1.0 (Q1 2026)

- [ ] Multi-Language Support (i18n)
- [ ] Datei-Upload durch Kunden
- [ ] Live-Chat Integration
- [ ] PDF-Export (Druckansicht)

### Version 1.2.0 (Q2 2026)

- [ ] Push-Notifications (PWA)
- [ ] QR-Code Login
- [ ] Ticket-Bewertung (Rating)
- [ ] AI-basierte FAQ

## Support

**Dokumentation:**
- User Guide: `doc/ticket-portal-user-guide.md`
- API Reference: `doc/ticket-portal-api-reference.md`
- Setup Guide: `doc/ticket-portal-setup.md`

**Community:**
- Forum: https://forum.openxe.org
- GitHub Issues: https://github.com/openxe/openxe/issues

**Kommerzieller Support:**
- Email: support@openxe.org
- Hotline: +49 (0) XXX XXXXXX

## Contributing

Pull Requests sind willkommen!

**Workflow:**
1. Fork erstellen
2. Feature-Branch: `git checkout -b feature/mein-feature`
3. Commits: `git commit -m 'Add: Mein Feature'`
4. Push: `git push origin feature/mein-feature`
5. Pull Request öffnen

**Code-Standards:**
- PHP: PSR-12
- JavaScript: ESLint (Standard-Config)
- CSS: BEM-Methodology

## License

GPL-3.0 License - siehe [LICENSE](LICENSE) Datei

## Authors

**OpenXE Development Team**
- Website: https://openxe.org
- Email: development@openxe.org

**Contributors:**
- [Contributor-Liste auf GitHub](https://github.com/openxe/openxe/graphs/contributors)

---

**Made with ❤️ by the OpenXE Community**
