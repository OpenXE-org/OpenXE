# OpenXE Ticket Portal - API Reference

**Version:** 1.0  
**Stand:** Dezember 2025  
**Zielgruppe:** Entwickler, System-Integratoren

---

## Inhaltsverzeichnis

1. [Architektur](#architektur)
2. [Authentifizierung](#authentifizierung)
3. [Backend-Endpoints](#backend-endpoints)
4. [WordPress AJAX Handlers](#wordpress-ajax-handlers)
5. [JavaScript API](#javascript-api)
6. [Datenbank-Schema](#datenbank-schema)
7. [Hooks & Filter](#hooks--filter)
8. [Sicherheit](#sicherheit)
9. [Error Handling](#error-handling)

---

## Architektur

### Komponenten-Übersicht

```
┌──────────────────────────────────────────────────────┐
│ Browser (Customer)                                   │
│ ├─ portal.js (Vanilla JS)                           │
│ └─ portal.css (Avant-Garde UI)                      │
└─────────────────┬────────────────────────────────────┘
                  │ AJAX (JSON)
┌─────────────────▼────────────────────────────────────┐
│ WordPress (Proxy Layer)                              │
│ ├─ openxe-ticket-portal.php                         │
│ ├─ Rate Limiting                                     │
│ ├─ Nonce Validation                                  │
│ └─ Binary Proxy (Media Downloads)                   │
└─────────────────┬────────────────────────────────────┘
                  │ HTTP POST (Shared Secret)
┌─────────────────▼────────────────────────────────────┐
│ OpenXE Backend (Business Logic)                      │
│ ├─ www/pages/ticket.php (Portal Methods)            │
│ ├─ Session Management                                │
│ ├─ Token Hashing (SHA-256)                          │
│ └─ Database Access                                   │
└──────────────────────────────────────────────────────┘
```

### Request Flow

```
1. Browser → WP AJAX Handler
   ├─ Nonce Check
   ├─ Rate Limit
   └─ sanitize_text_field()

2. WP → OpenXE Backend
   ├─ Shared Secret Header
   ├─ JSON Payload
   └─ 20s Timeout

3. OpenXE → Database
   ├─ Session Validation
   ├─ Permission Check
   └─ Business Logic

4. OpenXE → WP → Browser
   └─ JSON Response
```

---

## Authentifizierung

### Session-Erstellung

#### Endpoint: `portal_session`

**Request:**
```json
POST /index.php?module=ticket&action=portal_session
Content-Type: application/json
X-OpenXE-Portal-Secret: <shared_secret>

{
  "token": "4711",
  "verifier_type": "plz",
  "verifier_value": "12345"
}
```

**Response (Success):**
```json
{
  "session_token": "a1b2c3d4e5f6...",
  "ticket_id": 123,
  "ticket_number": "4711",
  "expires_at": "2025-12-28T03:00:00Z"
}
```

**Response (Error):**
```json
{
  "error": "invalid_verifier",
  "code": 401
}
```

### Verifier Types

| Type | Value | Use Case |
|------|-------|----------|
| `plz` | 5-digit PLZ | Auto-detection from address |
| `email` | Email address | Sends verification code |
| `code` | 6-digit code | After email verification |
| `magic` | Magic token | Click-through from email link |

### Session Validation

**Alle nachfolgenden Requests benötigen:**
```json
{
  "session_token": "a1b2c3d4e5f6..."
}
```

**Validierung im Backend:**
```php
$access = $this->portalGetSessionAccess($data);
if (!$access) {
    $this->portalJsonResponse(['error' => 'session_invalid'], 401);
}
```

---

## Backend-Endpoints

Alle Endpoints sind in `www/pages/ticket.php` als `public function ticket_portal_*()` implementiert.

### 1. portal_session

**Zweck:** Session erstellen/validieren

**Parameter:**
- `token` (string, required): Ticketnummer
- `verifier_type` (string, required): Siehe [Verifier Types](#verifier-types)
- `verifier_value` (string, required): Abhängig von Type

**Returns:**
```typescript
interface SessionResponse {
  session_token: string;
  ticket_id: number;
  ticket_number: string;
  expires_at: string; // ISO 8601
}
```

**Errors:**
- `401`: Invalid verifier
- `403`: Portal disabled
- `404`: Ticket not found
- `429`: Too many attempts (locked)

---

### 2. portal_status

**Zweck:** Aktuellen Kundenstatus abrufen

**Request:**
```json
{
  "session_token": "abc123..."
}
```

**Response:**
```json
{
  "status_key": "in_bearbeitung",
  "status_label": "In Bearbeitung",
  "updated_at": "2025-12-27T14:30:00Z"
}
```

**Implementation:**
```php
public function ticket_portal_status()
{
  $this->portalRequireSharedSecret();
  $data = $this->portalReadJsonInput();
  $access = $this->portalGetSessionAccess($data);
  
  $status = $this->app->DB->SelectRow(
    "SELECT status_key, status_label, updated_at 
     FROM ticket_customer_status 
     WHERE ticket_id = {$access['ticket_id']} 
     LIMIT 1"
  );
  
  $this->portalJsonResponse($status ?? []);
}
```

---

### 3. portal_messages

**Zweck:** Alle öffentlichen Nachrichten eines Tickets

**Request:**
```json
{
  "session_token": "abc123..."
}
```

**Response:**
```json
{
  "messages": [
    {
      "id": 42,
      "author_type": "staff",
      "text": "Druckkopf wird ersetzt",
      "created_at": "2025-12-27T13:15:00Z"
    }
  ]
}
```

**Query:**
```sql
SELECT id, author_type, text, created_at
FROM ticket_portal_message
WHERE ticket_id = ? AND is_public = 1
ORDER BY created_at ASC
```

---

### 4. portal_message

**Zweck:** Neue Nachricht vom Kunden senden

**Request:**
```json
{
  "session_token": "abc123...",
  "text": "Wann wird das Gerät fertig?"
}
```

**Response:**
```json
{
  "message_id": 43,
  "mirrored_id": 789
}
```

**Side Effects:**
- Eintrag in `ticket_portal_message` (is_public=1)
- Mirror in `ticket_nachricht` (medium='portal')
- Keine Email-Notification (nur internes Log)

---

### 5. portal_offers

**Zweck:** Offene Angebote für Ticket-Adresse

**Request:**
```json
{
  "session_token": "abc123..."
}
```

**Response:**
```json
{
  "offers": [
    {
      "id": 55,
      "belegnr": "2025-001",
      "datum": "2025-12-27",
      "gesamtsumme": "149.90",
      "waehrung": "EUR",
      "status": "freigegeben"
    }
  ]
}
```

**Query:**
```sql
SELECT id, belegnr, datum, gesamtsumme, waehrung, status
FROM angebot
WHERE adresse = ? AND status = 'freigegeben'
ORDER BY datum DESC, id DESC
```

---

### 6. portal_offer

**Zweck:** Angebot akzeptieren/ablehnen (mit DOI)

**Request:**
```json
{
  "session_token": "abc123...",
  "angebot_id": 55,
  "action": "accept",
  "comment": "Ja, gerne!",
  "agb_version": "2025-01"
}
```

**Response (Accept):**
```json
{
  "status": "pending_doi",
  "doi_sent": true
}
```

**Response (Decline):**
```json
{
  "status": "decline_recorded",
  "doi_sent": false
}
```

**Side Effects (Accept):**
1. Eintrag in `ticket_offer_confirmation` (doi_token_hash gesetzt)
2. Email mit DOI-Link an Kunde
3. Kundenstatus unverändert (bis DOI-Confirm)

**Side Effects (Decline):**
1. Eintrag in `ticket_offer_confirmation` (doi_token_hash=NULL)
2. Kundenstatus → `angebot_abgelehnt`
3. Log-Eintrag

---

### 7. portal_offer_confirm

**Zweck:** DOI-Bestätigung für Angebotsannahme

**Request:**
```json
{
  "doi_token": "xyz789..."
}
```

**Response:**
```json
{
  "status": "confirmed",
  "ticket_id": 123,
  "angebot_id": 55
}
```

**Validation:**
1. Token-Hash existiert in DB
2. DOI nicht abgelaufen (ticketportal_doi_ttl_min)
3. Noch nicht bestätigt (confirmed_at IS NULL)

**Side Effects:**
1. `confirmed_at` und `confirmed_ip` setzen
2. Kundenstatus → `angebot_bestaetigt`
3. Interne Benachrichtigung (optional)

---

### 8. portal_media

**Zweck:** Öffentliche Medien für Kunden

**Request:**
```json
{
  "session_token": "abc123..."
}
```

**Response:**
```json
{
  "media": [
    {
      "id": 7,
      "filename": "reparatur_foto.jpg",
      "mime_type": "image/jpeg",
      "file_size": 251123,
      "created_at": "2025-12-27T16:00:00Z"
    }
  ]
}
```

**Query:**
```sql
SELECT id, filename, mime_type, file_size, created_at
FROM ticket_repair_media
WHERE ticket_id = ? AND is_public = 1
ORDER BY created_at DESC
```

---

### 9. portal_media_download

**Zweck:** Binary-Download einer Mediendatei

**Request:**
```json
{
  "session_token": "abc123...",
  "media_id": 7
}
```

**Response:** Binary file stream

**Headers:**
```
Content-Type: image/jpeg
Content-Length: 251123
Content-Disposition: attachment; filename="reparatur_foto.jpg"
```

**Sicherheit:**
- Session-Validierung
- `is_public = 1` Check
- Hash-basierte Storage (kein Directory Traversal)

---

### 10. portal_notifications

**Zweck:** Benachrichtigungspräferenzen abrufen

**Request:**
```json
{
  "session_token": "abc123..."
}
```

**Response:**
```json
{
  "notifications": [
    {
      "key": "paket_eingegangen",
      "label": "Paket eingegangen",
      "enabled": true
    },
    {
      "key": "in_bearbeitung",
      "label": "In Bearbeitung",
      "enabled": true
    }
  ]
}
```

---

### 11. portal_notification

**Zweck:** Benachrichtigungspräferenzen setzen

**Request:**
```json
{
  "session_token": "abc123...",
  "selected": ["paket_eingegangen", "abgeschlossen"]
}
```

**Response:**
```json
{
  "success": true,
  "count": 2
}
```

**Side Effect:** UPDATE/INSERT in `ticket_notification_pref`

---

## WordPress AJAX Handlers

Alle Handler in `wp-plugin/openxe-ticket-portal/openxe-ticket-portal.php`.

### Handler-Pattern

```php
function openxe_ticket_portal_ajax_<action>(): void
{
  // 1. CSRF-Schutz
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  
  // 2. Rate Limiting
  openxe_ticket_portal_apply_rate_limit('<action>');
  
  // 3. Input Sanitization
  $sessionToken = sanitize_text_field(
    wp_unslash($_POST['session_token'] ?? '')
  );
  
  // 4. Validation
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  
  // 5. Proxy to Backend
  openxe_ticket_portal_proxy('portal_<action>', [
    'session_token' => $sessionToken
  ]);
}
```

### Registrierung

```php
add_action('wp_ajax_nopriv_openxe_ticket_portal_<action>', 
           'openxe_ticket_portal_ajax_<action>');
add_action('wp_ajax_openxe_ticket_portal_<action>', 
           'openxe_ticket_portal_ajax_<action>');
```

**Hinweis:** `wp_ajax_nopriv_` erlaubt nicht-eingeloggte Requests.

### Binary Proxy (Spezialfall)

```php
function openxe_ticket_portal_proxy_binary(
  string $action, 
  array $payload
): void
{
  $response = wp_remote_post($url, [
    'headers' => ['X-OpenXE-Portal-Secret' => $secret],
    'body' => wp_json_encode($payload),
    'timeout' => 60
  ]);
  
  // Stream directly to browser
  header('Content-Type: ' . $contentType);
  header('Content-Disposition: ' . $disposition);
  echo wp_remote_retrieve_body($response);
  exit;
}
```

**Verwendet für:** `portal_media_download`

---

## JavaScript API

### Initialisierung

```javascript
(function() {
  var config = JSON.parse(
    document.querySelector('[data-openxe-portal]')
               .getAttribute('data-openxe-config')
  );
  
  // config.ajaxUrl
  // config.nonce
  // config.baseUrl
  // config.defaultVerifier
})();
```

### AJAX Helper

```javascript
function portalAjax(config, action, data) {
  return fetch(config.ajaxUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded'
    },
    body: new URLSearchParams({
      action: action,
      nonce: config.nonce,
      ...flattenObject(data)
    })
  })
  .then(resp => resp.json())
  .catch(err => ({ success: false, data: { error: err.message } }));
}
```

### Beispiel: Status laden

```javascript
function loadStatus() {
  if (!sessionToken) return;
  
  portalAjax(config, 'openxe_ticket_portal_status', {
    session_token: sessionToken
  }).then(function(resp) {
    if (resp && resp.success) {
      document.querySelector('.oxp-status-value')
              .textContent = resp.data.status_label;
    }
  });
}
```

### Media Download (Blob)

```javascript
function downloadMedia(mediaId, filename) {
  var body = 'session_token=' + encodeURIComponent(sessionToken) +
             '&media_id=' + encodeURIComponent(mediaId) +
             '&nonce=' + encodeURIComponent(config.nonce) +
             '&action=openxe_ticket_portal_media_download';

  fetch(config.ajaxUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
    body: body
  })
  .then(resp => resp.blob())
  .then(blob => {
    var url = window.URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
  });
}
```

---

## Datenbank-Schema

### ticket_portal_access

**Zweck:** Sessions & Magic Links

```sql
CREATE TABLE `ticket_portal_access` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `token_hash` varchar(64) NOT NULL,
  `scope` enum('session','magic','code') NOT NULL,
  `created_at` datetime NOT NULL,
  `expires_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `token_hash` (`token_hash`),
  KEY `expires_at` (`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
```

**Scopes:**
- `session`: Normal login session
- `magic`: Magic link token
- `code`: Email verification code

---

### ticket_customer_status

**Zweck:** Kundenspezifischer Status (entkoppelt vom internen Status)

```sql
CREATE TABLE `ticket_customer_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `status_key` varchar(64) NOT NULL,
  `status_label` varchar(255) NOT NULL,
  `updated_at` datetime NOT NULL,
  `updated_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
```

---

### ticket_status_log

**Zweck:** Audit-Trail für Statusänderungen

```sql
CREATE TABLE `ticket_status_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `status_from` varchar(64) DEFAULT NULL,
  `status_to` varchar(64) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `changed_at` datetime NOT NULL,
  `note_public` text DEFAULT NULL,
  `note_internal` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
```

---

### ticket_portal_message

**Zweck:** Portal-Nachrichten (Customer ↔ Staff)

```sql
CREATE TABLE `ticket_portal_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `author_type` enum('customer','staff') NOT NULL,
  `author_id` int(11) NOT NULL,
  `text` text NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL,
  `mirrored_message_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
```

**Hinweis:** `mirrored_message_id` referenziert `ticket_nachricht.id`

---

### ticket_offer_confirmation

**Zweck:** Angebots-Bestätigungen (inkl. DOI)

```sql
CREATE TABLE `ticket_offer_confirmation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `angebot_id` int(11) NOT NULL,
  `action` enum('accept','decline') NOT NULL,
  `comment` text DEFAULT NULL,
  `agb_version` varchar(32) DEFAULT NULL,
  `doi_token_hash` varchar(64) DEFAULT NULL,
  `doi_requested_at` datetime DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `confirmed_ip` varchar(45) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `created_by_type` varchar(32) NOT NULL,
  `created_by_id` int(11) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`),
  KEY `angebot_id` (`angebot_id`),
  KEY `doi_token_hash` (`doi_token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
```

**DOI-Flow:**
1. `action='accept'` → `doi_token_hash` gesetzt, `confirmed_at` NULL
2. Click DOI-Link → `confirmed_at` & `confirmed_ip` gesetzt

---

### ticket_repair_media

**Zweck:** Hochgeladene Medien (Bilder, PDFs)

```sql
CREATE TABLE `ticket_repair_media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `mime_type` varchar(128) NOT NULL,
  `file_size` int(11) NOT NULL,
  `file_hash` varchar(64) NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ticket_id` (`ticket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
```

**Storage:** `www/userfiles/ticket_media/{file_hash}.{ext}`

---

### ticket_notification_pref

**Zweck:** Kunden-Benachrichtigungspräferenzen

```sql
CREATE TABLE `ticket_notification_pref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticket_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `status_key` varchar(64) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ticket_customer_status` 
    (`ticket_id`, `customer_id`, `status_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;
```

---

## Hooks & Filter

### WordPress Hooks

#### Rate Limit anpassen

```php
add_filter('openxe_ticket_portal_rate_limit', function($limit, $action) {
  if ($action === 'media_download') {
    return 30; // 30 Downloads/Minute
  }
  return $limit; // Default: 60
}, 10, 2);
```

#### Rate Window ändern

```php
add_filter('openxe_ticket_portal_rate_window', function($window, $action) {
  return 120; // 2-Minuten-Fenster
}, 10, 2);
```

### OpenXE Hooks (Beispiele)

**Custom Status hinzufügen:**
```php
// In www/pages/ticket.php oder Modul-Extension
private function portalGetStatusLabels(): array
{
  $labels = parent::portalGetStatusLabels();
  
  // Hook für Extensions
  $labels = $this->app->erp->RunHook(
    'ticket_portal_status_labels', 
    $labels
  );
  
  return $labels;
}
```

**Vor Statusänderung:**
```php
// Hook vor portalSetCustomerStatus()
$this->app->erp->RunHook('ticket_portal_before_status_change', [
  'ticket_id' => $ticketId,
  'old_status' => $oldKey,
  'new_status' => $newKey
]);
```

---

## Sicherheit

### Token-Hashing

**Algorithmus:** SHA-256

```php
private function portalHashToken(string $token): string
{
  return hash('sha256', $token);
}
```

**Wichtig:** Nur Hashes werden in DB gespeichert!

### Shared Secret

**Konfiguration:**
- OpenXE: Firmendaten → `ticketportal_shared_secret`
- WordPress: Options → `openxe_ticket_portal_shared_secret`

**Validation:**
```php
private function portalRequireSharedSecret(): void
{
  $expected = $this->app->erp->Firmendaten('ticketportal_shared_secret');
  if ($expected === '') return; // Optional
  
  $provided = $_SERVER['HTTP_X_OPENXE_PORTAL_SECRET'] ?? '';
  if ($provided !== $expected) {
    http_response_code(403);
    $this->portalJsonResponse(['error' => 'forbidden'], 403);
  }
}
```

### Rate Limiting (WordPress)

```php
function openxe_ticket_portal_apply_rate_limit(string $action): void
{
  $limit = 60; // requests
  $window = 60; // seconds
  $ip = $_SERVER['REMOTE_ADDR'];
  $key = 'oxp_rl_' . md5($action . '|' . $ip);
  
  $data = get_transient($key);
  $data['count'] = ($data['count'] ?? 0) + 1;
  
  if ($data['count'] > $limit) {
    wp_send_json_error(['message' => 'rate_limited'], 429);
  }
  
  set_transient($key, $data, $window);
}
```

### File Upload Validation

```php
// 1. Size Check
if ($file['size'] > 10 * 1024 * 1024) {
  return 'file_too_large';
}

// 2. MIME Type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($file['tmp_name']);
$allowed = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
if (!in_array($mime, $allowed, true)) {
  return 'invalid_mime_type';
}

// 3. Extension Check
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
if (!in_array(strtolower($ext), ['jpg','jpeg','png','webp','pdf'], true)) {
  return 'invalid_extension';
}

// 4. Hash-based Storage (no original filename in path)
$hash = hash_file('sha256', $file['tmp_name']);
$target = $uploadDir . DIRECTORY_SEPARATOR . $hash . '.' . $ext;
```

### CSRF Protection

**WordPress:**
```javascript
// Nonce in jedem AJAX Request
{
  nonce: config.nonce,
  action: 'openxe_ticket_portal_status',
  // ...
}
```

```php
// Server-Side Validation
check_ajax_referer('openxe_ticket_portal', 'nonce');
```

### SQL Injection Prevention

**Alle Queries nutzen:**
```php
// ✅ Escaped
$ticketId = (int)$ticketId;
$statusKey = $this->app->DB->real_escape_string($statusKey);

// ✅ Prepared Statements (wo verfügbar)
$stmt = $this->app->DB->prepare(
  "SELECT * FROM ticket WHERE id = ? LIMIT 1"
);
$stmt->bind_param('i', $ticketId);
```

---

## Error Handling

### Standard Error Response

```json
{
  "error": "error_code",
  "message": "Human-readable message (optional)",
  "code": 400
}
```

### Error Codes

| Code | HTTP | Bedeutung |
|------|------|-----------|
| `portal_disabled` | 403 | Portal nicht aktiviert |
| `invalid_verifier` | 401 | PLZ/Email falsch |
| `session_invalid` | 401 | Session abgelaufen/ungültig |
| `token_not_found` | 404 | Ticket existiert nicht |
| `rate_limited` | 429 | Zu viele Requests |
| `file_too_large` | 400 | Upload >10MB |
| `invalid_mime_type` | 400 | Dateiformat nicht erlaubt |
| `offer_not_found` | 404 | Angebot existiert nicht |
| `doi_expired` | 410 | DOI-Link abgelaufen |

### Logging

**Portal-Log aktivieren:**
```
Ticket → Portal Einstellungen
☑ Portal-Log aktivieren
```

**Log-Pfad:** `www/log/portal.log`

**Log-Eintrag-Beispiel:**
```
[2025-12-28 02:00:00] portal.INFO: session_created {"ticket_id":123,"ip":"192.168.1.1"}
[2025-12-28 02:01:00] portal.INFO: status_viewed {"ticket_id":123}
[2025-12-28 02:02:00] portal.ERROR: invalid_verifier {"attempts":3,"locked_until":"2025-12-28 02:17:00"}
```

**Programmatisch loggen:**
```php
$this->portalLog('custom_event', [
  'ticket_id' => $ticketId,
  'user_id' => $userId,
  'custom_data' => $data
]);
```

---

## Beispiel: Vollständige Integration

### 1. Custom Status hinzufügen

```php
// www/pages/custom_ticket_extension.php
class CustomTicketExtension extends Ticket
{
  public function __construct($app, $intern = false)
  {
    parent::__construct($app, $intern);
    
    // Hook registrieren
    $this->app->erp->RegisterHook(
      'ticket_portal_status_labels',
      [$this, 'addCustomStatuses']
    );
  }
  
  public function addCustomStatuses(array $labels): array
  {
    $labels['laser_engraving'] = 'Laser-Gravur läuft';
    $labels['quality_check_3d'] = '3D-Qualitätskontrolle';
    return $labels;
  }
}
```

### 2. Eigenen Endpoint hinzufügen

```php
// www/pages/ticket.php
public function ticket_portal_custom_stats()
{
  $this->portalRequireSharedSecret();
  $data = $this->portalReadJsonInput();
  $access = $this->portalGetSessionAccess($data);
  
  if (!$access) {
    $this->portalJsonResponse(['error' => 'session_invalid'], 401);
  }
  
  $stats = $this->app->DB->SelectRow(
    "SELECT 
       COUNT(*) as total_messages,
       MAX(created_at) as last_activity
     FROM ticket_portal_message
     WHERE ticket_id = {$access['ticket_id']}"
  );
  
  $this->portalJsonResponse($stats);
}
```

### 3. WordPress AJAX Handler

```php
// wp-plugin/openxe-ticket-portal/openxe-ticket-portal.php
function openxe_ticket_portal_ajax_custom_stats(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  openxe_ticket_portal_apply_rate_limit('custom_stats');
  
  $sessionToken = sanitize_text_field(
    wp_unslash($_POST['session_token'] ?? '')
  );
  
  if ($sessionToken === '') {
    wp_send_json_error(['message' => 'invalid_request'], 400);
  }
  
  openxe_ticket_portal_proxy('portal_custom_stats', [
    'session_token' => $sessionToken
  ]);
}

add_action('wp_ajax_nopriv_openxe_ticket_portal_custom_stats',
           'openxe_ticket_portal_ajax_custom_stats');
add_action('wp_ajax_openxe_ticket_portal_custom_stats',
           'openxe_ticket_portal_ajax_custom_stats');
```

### 4. Frontend-Integration

```javascript
// wp-plugin/.../assets/portal.js (am Ende der IIFE)

function loadCustomStats() {
  if (!sessionToken) return;
  
  portalAjax(config, 'openxe_ticket_portal_custom_stats', {
    session_token: sessionToken
  }).then(function(resp) {
    if (resp && resp.success) {
      console.log('Total Messages:', resp.data.total_messages);
      console.log('Last Activity:', resp.data.last_activity);
    }
  });
}

// In showMain() ergänzen:
loadCustomStats();
```

---

## Performance-Optimierung

### Caching (WordPress)

```php
function openxe_ticket_portal_ajax_status(): void
{
  check_ajax_referer('openxe_ticket_portal', 'nonce');
  
  $sessionToken = sanitize_text_field(
    wp_unslash($_POST['session_token'] ?? '')
  );
  
  // Transient Cache (60 Sekunden)
  $cacheKey = 'oxp_status_' . md5($sessionToken);
  $cached = get_transient($cacheKey);
  
  if ($cached !== false) {
    wp_send_json_success($cached);
  }
  
  // Fetch from backend
  $result = openxe_ticket_portal_remote('portal_status', [
    'session_token' => $sessionToken
  ]);
  
  if (!is_wp_error($result) && $result['code'] === 200) {
    set_transient($cacheKey, $result['data'], 60);
  }
  
  // Return as usual...
}
```

### Database Indexing

**Wichtige Indices (bereits vorhanden):**
```sql
ALTER TABLE ticket_portal_access 
  ADD INDEX idx_token_hash (token_hash);
  
ALTER TABLE ticket_customer_status 
  ADD INDEX idx_ticket_id (ticket_id);
  
ALTER TABLE ticket_repair_media 
  ADD INDEX idx_ticket_public (ticket_id, is_public);
```

---

## Testing

### PHPUnit Tests (Beispiel)

```php
// tests/TicketPortalTest.php
class TicketPortalTest extends TestCase
{
  public function testSessionCreation()
  {
    $ticket = new Ticket($this->app);
    
    $_SERVER['HTTP_X_OPENXE_PORTAL_SECRET'] = 'test-secret';
    $_SERVER['REQUEST_METHOD'] = 'POST';
    file_put_contents('php://input', json_encode([
      'token' => '4711',
      'verifier_type' => 'plz',
      'verifier_value' => '12345'
    ]));
    
    ob_start();
    $ticket->ticket_portal_session();
    $output = ob_get_clean();
    
    $response = json_decode($output, true);
    $this->assertArrayHasKey('session_token', $response);
    $this->assertEquals(123, $response['ticket_id']);
  }
}
```

### Integration Tests (WordPress)

```php
// tests/WordPressAJAXTest.php
class WordPressAJAXTest extends WP_UnitTestCase
{
  public function testStatusAJAX()
  {
    $_POST['nonce'] = wp_create_nonce('openxe_ticket_portal');
    $_POST['session_token'] = 'valid-session-token';
    
    try {
      openxe_ticket_portal_ajax_status();
    } catch (WPAjaxDieContinueException $e) {
      // Expected
    }
    
    $response = json_decode($this->_last_response, true);
    $this->assertTrue($response['success']);
    $this->assertArrayHasKey('status_label', $response['data']);
  }
}
```

---

## Deployment Checklist

### OpenXE Backend

- ☑ `struktur.sql` importiert (alle 7 Tabellen)
- ☑ Shared Secret generiert & gespeichert
- ☑ Portal aktiviert
- ☑ Status-Mapping konfiguriert
- ☑ Email-Templates angepasst
- ☑ Berechtigungen geprüft (`ticket` → `edit`)

### WordPress

- ☑ Plugin hochgeladen & aktiviert
- ☑ Base URL konfiguriert
- ☑ Shared Secret eingefügt (identisch!)
- ☑ Shortcode auf Seite eingefügt
- ☑ Test-Verbindung erfolgreich
- ☑ SSL-Zertifikat aktiv (HTTPS!)

### Testing

- ☑ Login-Flow getestet (alle 4 Verifier)
- ☑ Media Upload & Download
- ☑ Angebots-Workflow (inkl. DOI)
- ☑ Custom Status & Projekt-Mapping
- ☑ Email-Notifications erhalten
- ☑ Rate Limiting verifiziert

---

## Support & Beitragen

**Bug Reports:** GitHub Issues  
**Feature Requests:** GitHub Discussions  
**Pull Requests:** Willkommen!

**Kontakt:** development@openxe.org

---

**Letzte Aktualisierung:** 2025-12-28  
**Version:** 1.0  
**Lizenz:** GPL-3.0
