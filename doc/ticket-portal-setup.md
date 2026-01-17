# OpenXE Ticket Portal - Setup & Installation

**Version:** 1.0  
**Stand:** Dezember 2025  
**Zielgruppe:** System-Administratoren, Deployer

---

## Inhaltsverzeichnis

1. [Systemvoraussetzungen](#systemvoraussetzungen)
2. [OpenXE Backend Setup](#openxe-backend-setup)
3. [WordPress Plugin Installation](#wordpress-plugin-installation)
4. [Ersteinrichtung](#ersteinrichtung)
5. [Migration & Updates](#migration--updates)
6. [Troubleshooting](#troubleshooting)
7. [Produktiv-Checkliste](#produktiv-checkliste)

---

## Systemvoraussetzungen

### OpenXE Backend

- **OpenXE Version:** >= 2023.x
- **PHP:** >= 7.4 (empfohlen: 8.0+)
- **MySQL/MariaDB:** >= 5.7 / >= 10.2
- **PHP Extensions:**
  - `mysqli`
  - `json`
  - `mbstring`
  - `fileinfo` (für MIME-Detection)
  - `openssl` (für Token-Hashing)
  - `curl` (optional, für Webhook-Integration)

### WordPress

- **WordPress Version:** >= 5.8
- **PHP:** >= 7.4
- **SSL/TLS:** **Pflicht!** (HTTPS)
- **Plugins:** Keine Konflikte bekannt

### Server-Anforderungen

- **Arbeitsspeicher:** Min. 512MB PHP-Memory-Limit (empfohlen: 1GB)
- **Upload-Limit:** 10MB+ (für Media Upload)
- **Execution Time:** 60s+ (für Magic Link Emails)
- **Outbound Connections:** WordPress → OpenXE erlaubt

---

## OpenXE Backend Setup

### Schritt 1: Datenbank-Schema aktualisieren

#### Option A: Automatische Migration (empfohlen)

```bash
cd /pfad/zu/openxe
php cli/migration.php --execute
```

#### Option B: Manueller Import

```bash
mysql -u root -p openxe_db < database/struktur.sql
```

**Hinweis:** Nur die neuen Tabellen werden erstellt (falls nicht vorhanden):
- `ticket_portal_access`
- `ticket_customer_status`
- `ticket_status_log`
- `ticket_portal_message`
- `ticket_offer_confirmation`
- `ticket_repair_media`
- `ticket_notification_pref`

#### Validierung

```sql
-- Prüfen, ob alle Tabellen existieren
SHOW TABLES LIKE 'ticket_%';

-- Erwartete Ausgabe (Auszug):
-- ticket_portal_access
-- ticket_customer_status
-- ticket_status_log
-- ...
```

### Schritt 2: Berechtigungen setzen

Stellen Sie sicher, dass Mitarbeiter die nötigen Rechte haben:

```sql
-- In der GUI: Einstellungen → Benutzer → Rollen
-- Rolle: "Support" oder "Mitarbeiter"
-- Berechtigung: "Ticket" → "Bearbeiten" ☑
```

### Schritt 3: Upload-Verzeichnis erstellen

```bash
cd /pfad/zu/openxe/www
mkdir -p userfiles/ticket_media
chmod 0775 userfiles/ticket_media
chown www-data:www-data userfiles/ticket_media
```

**Wichtig für SELinux:**
```bash
chcon -R -t httpd_sys_rw_content_t userfiles/ticket_media
```

### Schritt 4: Portal-Einstellungen

1. Navigieren Sie zu **Ticket → Portal Einstellungen**
2. Aktivieren Sie das Portal:
   ```
   ☑ Portal aktivieren
   ```
3. Setzen Sie die Portal-URL (später):
   ```
   Portal URL: https://ihr-wordpress.de/ticket-portal
   ```
4. **Generieren Sie ein Shared Secret:**
   - Klicken Sie "Generieren"
   - Klicken Sie "Kopieren"
   - **Notieren Sie das Secret!** (Wird im WordPress-Plugin benötigt)

5. Speichern Sie die Einstellungen

### Schritt 5: Standard-Status konfigurieren

Scrollen Sie zu **"Statusmodell"**:

```
Standard Kundenstatus Texte:
├─ paket_eingegangen → "Paket eingegangen"
├─ in_bearbeitung → "In Bearbeitung"
├─ warte_ersatzteile → "Warte auf Ersatzteile"
└─ ...

Mapping Intern → Kundenstatus:
├─ neu → [paket_eingegangen]
├─ offen → [paket_eingegangen]
├─ warten_e → [in_bearbeitung]
└─ ...
```

**Speichern Sie erneut!**

---

## WordPress Plugin Installation

### Schritt 1: Plugin-Download

#### Option A: Über OpenXE (empfohlen)

1. Im OpenXE: **Ticket → Portal Einstellungen**
2. Scrollen Sie zu "WordPress Plugin"
3. Klicken Sie "Plugin herunterladen"
4. Speichern Sie `openxe-ticket-portal.zip`

#### Option B: Von GitHub

```bash
git clone https://github.com/openxe/openxe.git
cd openxe/wp-plugin
zip -r openxe-ticket-portal.zip openxe-ticket-portal/
```

### Schritt 2: Plugin hochladen

1. WordPress Admin → **Plugins → Installieren**
2. Klicken Sie "Plugin hochladen"
3. Wählen Sie `openxe-ticket-portal.zip`
4. Klicken Sie "Jetzt installieren"
5. Klicken Sie "Aktivieren"

### Schritt 3: Plugin konfigurieren

Navigieren Sie zu **Einstellungen → OpenXE Ticket Portal**:

```
┌────────────────────────────────────────────────────┐
│ OpenXE Verbindung                                  │
├────────────────────────────────────────────────────┤
│ OpenXE Base URL:                                   │
│ https://openxe.ihre-firma.de                       │
│                                                     │
│ Shared Secret:                                     │
│ [Hier das Secret aus OpenXE einfügen]             │
│                                                     │
│ [Verbindung testen] [Änderungen speichern]        │
└────────────────────────────────────────────────────┘
```

**Wichtig:**
- Base URL **ohne** trailing slash
- Shared Secret **exakt** wie in OpenXE
- Klicken Sie "Verbindung testen" → "Verbindung ok."

### Schritt 4: Portal-Seite erstellen

1. WordPress Admin → **Seiten → Erstellen**
2. Titel: `Ticket Portal` (oder beliebig)
3. **Block einfügen:** Shortcode
4. Shortcode eintragen:
   ```
   [openxe_ticket_portal]
   ```
5. **Veröffentlichen**
6. Notieren Sie die URL, z.B.:
   ```
   https://ihr-wordpress.de/ticket-portal
   ```

### Schritt 5: URL zurück in OpenXE eintragen

1. OpenXE → **Ticket → Portal Einstellungen**
2. Feld "Portal URL":
   ```
   https://ihr-wordpress.de/ticket-portal
   ```
3. **Speichern**

---

## Ersteinrichtung

### Test-Workflow

#### 1. Test-Ticket erstellen

```
OpenXE → Ticket → Neu
├─ Schlüssel: 9999 (Testnummer)
├─ Adresse: Musterkunde (mit PLZ)
├─ Betreff: "Test Portal-Zugang"
└─ Status: offen
```

**Speichern!**

#### 2. Magic Link generieren

```bash
# OpenXE Datenbank
mysql -u root -p openxe_db

SELECT id FROM ticket WHERE schluessel = '9999'; 
-- Angenommen: id = 42

# Magic Link manuell erstellen (für Test)
INSERT INTO ticket_portal_access 
  (ticket_id, token_hash, scope, created_at, expires_at)
VALUES 
  (42, SHA2('test-magic-token', 256), 'magic', NOW(), DATE_ADD(NOW(), INTERVAL 24 HOUR));
```

**Test-URL:**
```
https://openxe.ihre-firma.de/index.php?module=ticket&action=portal_magic&magic_token=test-magic-token
```

Wenn korrekt konfiguriert:
→ Redirect zu WordPress Portal
→ Automatischer Login
→ Ticket-Details sichtbar

#### 3. Normaler Login testen

Besuchen Sie:
```
https://ihr-wordpress.de/ticket-portal
```

**Login-Formular:**
```
Ticketnummer: 9999
Verifikation: PLZ
PLZ/Ort: [PLZ aus Adresse]
```

→ Klick "Anmelden"
→ Portal-Ansicht mit Status

#### 4. Media Upload testen (Staff)

```
OpenXE → Ticket #9999 öffnen
→ Klick "Portal (Mitarbeiter)"
→ Neuer Tab:
  index.php?module=ticket&action=portal_staff&id=42
  
Upload testen:
1. Datei wählen (z.B. test.jpg)
2. ☑ Für Kunden sichtbar
3. "Hochladen"
```

Zurück im Kundenportal:
→ Reload (F5)
→ Abschnitt "Dokumente & Bilder" erscheint
→ test.jpg sichtbar & downloadbar

#### 5. Angebot testen

```
OpenXE → Angebot erstellen
├─ Adresse: Musterkunde (gleiche wie Ticket!)
├─ Betrag: 149.90 EUR
├─ Status: freigegeben
└─ Speichern
```

Kundenportal:
→ Reload
→ Abschnitt "Offene Angebote" erscheint
→ Klick "Auswählen"
→ DOI-Workflow testen

---

## Migration & Updates

### Von Konzept/Beta zu Production

#### Pre-Migration Checklist

- [ ] Backup der gesamten Datenbank
- [ ] Backup von `www/userfiles/ticket_media/`
- [ ] Plugin-Einstellungen notiert (Base URL, Secret)
- [ ] Aktive Tickets dokumentiert

#### Schritt 1: Datenbank-Migration

```bash
# Backup erstellen
mysqldump -u root -p openxe_db > backup_$(date +%Y%m%d).sql

# Schema-Update
mysql -u root -p openxe_db < database/struktur.sql
```

#### Schritt 2: Datenmigration (falls Legacy-System)

Falls Sie bereits Ticket-Daten haben:

```sql
-- Kundenstatus initialisieren für existierende Tickets
INSERT INTO ticket_customer_status (ticket_id, status_key, status_label, updated_at)
SELECT 
  t.id,
  CASE 
    WHEN t.status = 'neu' THEN 'paket_eingegangen'
    WHEN t.status = 'offen' THEN 'paket_eingegangen'
    WHEN t.status = 'warten_e' THEN 'in_bearbeitung'
    WHEN t.status = 'abgeschlossen' THEN 'abgeschlossen'
    ELSE 'in_bearbeitung'
  END,
  CASE 
    WHEN t.status = 'neu' THEN 'Paket eingegangen'
    WHEN t.status = 'offen' THEN 'Paket eingegangen'
    WHEN t.status = 'warten_e' THEN 'In Bearbeitung'
    WHEN t.status = 'abgeschlossen' THEN 'Abgeschlossen'
    ELSE 'In Bearbeitung'
  END,
  t.datum
FROM ticket t
WHERE t.id NOT IN (SELECT ticket_id FROM ticket_customer_status);
```

#### Schritt 3: Plugin-Update

```bash
# WordPress Admin → Plugins
# Falls "Update verfügbar":
1. Deaktivieren
2. Löschen
3. Neue Version hochladen
4. Aktivieren
5. Einstellungen prüfen (Secret bleibt erhalten)
```

### Rollback-Prozedur

Bei Problemen nach Update:

```bash
# 1. Datenbank zurücksetzen
mysql -u root -p openxe_db < backup_YYYYMMDD.sql

# 2. WordPress-Plugin deaktivieren
wp plugin deactivate openxe-ticket-portal

# 3. Alte Plugin-Version wieder hochladen

# 4. Fehler an Support melden
```

---

## Troubleshooting

### Problem: "Connection Failed" beim Test

**Symptom:**
```
WordPress → Einstellungen → OpenXE Ticket Portal
→ [Verbindung testen]
→ "Verbindung fehlgeschlagen"
```

**Debug-Schritte:**

#### 1. Netzwerk-Konnektivität prüfen

```bash
# Auf WordPress-Server:
curl -I https://openxe.ihre-firma.de/index.php?module=ticket&action=portal_session

# Erwartete Antwort:
HTTP/1.1 403 Forbidden (wegen fehlendem Secret = ok)
# oder
HTTP/1.1 401 Unauthorized (wegen falschem Token = ok)

# Fehlerhafte Antworten:
Connection refused → OpenXE nicht erreichbar
SSL certificate problem → Zertifikat ungültig
Timeout → Firewall blockiert
```

#### 2. Firewall-Regeln prüfen

```bash
# Auf OpenXE-Server:
sudo iptables -L -n | grep <WordPress-IP>

# Ggf. hinzufügen:
sudo iptables -A INPUT -p tcp -s <WordPress-IP> --dport 443 -j ACCEPT
```

#### 3. PHP-Fehlerlog prüfen

```bash
# WordPress:
tail -f /var/log/apache2/error.log
# oder
tail -f /var/www/html/wp-content/debug.log

# OpenXE:
tail -f /var/log/openxe/portal.log
```

#### 4. Shared Secret validieren

```bash
# OpenXE Datenbank:
mysql -u root -p openxe_db
SELECT wert FROM firmendaten WHERE schluessel = 'ticketportal_shared_secret';

# WordPress Datenbank:
mysql -u root -p wordpress_db
SELECT option_value FROM wp_options WHERE option_name = 'openxe_ticket_portal_shared_secret';

# Beide Werte MÜSSEN identisch sein!
```

---

### Problem: Portal aktiviert, aber Login schlägt fehl

**Symptom:** `{"error":"portal_disabled","code":403}`

**Lösung:**

```php
// In OpenXE: www/pages/ticket.php prüfen
private function portalGetSettingBool(string $key, bool $default = false): bool
{
  $value = $this->app->erp->Firmendaten($key);
  return (int)$value === 1;
}

// Debug:
// Ticket → Portal Einstellungen
// Checkbox "Portal aktivieren" MUSS gesetzt sein
```

**Alternativ manuell setzen:**
```sql
UPDATE firmendaten 
SET wert = '1' 
WHERE schluessel = 'ticketportal_enabled';
```

---

### Problem: Media Upload "Datei zu gross"

**Symptom:** Upload >10MB schlägt fehl

**Lösung 1: PHP-Limits erhöhen**

```ini
# /etc/php/8.0/apache2/php.ini (oder fpm/php.ini)

upload_max_filesize = 20M
post_max_size = 25M
memory_limit = 256M

# Danach:
sudo systemctl restart apache2
# oder
sudo systemctl restart php8.0-fpm
```

**Lösung 2: nginx-Limit (falls vorhanden)**

```nginx
# /etc/nginx/sites-available/openxe

http {
  client_max_body_size 20M;
}

# Danach:
sudo systemctl restart nginx
```

**Validierung:**
```bash
php -r "echo ini_get('upload_max_filesize');"
# Erwartete Ausgabe: 20M
```

---

### Problem: Rate Limiting zu streng

**Symptom:** Kunde erhält "Rate Limited" (429) nach wenigen Requests

**Temporäre Erhöhung (WordPress):**

```php
// wp-content/themes/ihr-theme/functions.php

add_filter('openxe_ticket_portal_rate_limit', function($limit, $action) {
  return 180; // 180 Requests/Minute
}, 10, 2);

add_filter('openxe_ticket_portal_rate_window', function($window) {
  return 120; // 2-Minuten-Fenster
}, 10, 1);
```

**Produktiv:** Untersuchen Sie echte Bot-Aktivität:

```bash
# WordPress error.log durchsuchen
grep "rate_limited" /var/www/html/wp-content/debug.log | awk '{print $5}' | sort | uniq -c | sort -rn

# Verdächtige IPs blocken:
# .htaccess oder iptables
```

---

### Problem: Emails werden nicht versendet

**Checkliste:**

1. **OpenXE Email-Config testen:**
   ```
   Einstellungen → Firmendaten → Email
   → Test-Email senden
   ```

2. **SMTP-Logs prüfen:**
   ```bash
   tail -f /var/log/mail.log
   # Oder bei externem SMTP (z.B. SendGrid):
   # WordPress → WP Mail SMTP → Email-Log
   ```

3. **Spam-Filter:**
   - Prüfen Sie SPF/DKIM-Records
   - Testen Sie mit mail-tester.com

4. **Debug-Modus aktivieren:**
   ```php
   // In OpenXE: www/pages/ticket.php
   private function portalSendStatusNotification(...) 
   {
     $this->portalLog('email_attempt', [
       'to' => $email,
       'subject' => $subject
     ]);
     
     $sent = $this->app->erp->MailSend(...);
     
     $this->portalLog('email_result', [
       'success' => $sent,
       'to' => $email
     ]);
   }
   ```

5. **Fallback-Test (Direktversand):**
   ```bash
   # Auf OpenXE-Server:
   echo "Test Body" | mail -s "Test Subject" kunde@example.com
   # Wenn empfangen → OpenXE-Code-Problem
   # Wenn nicht → Server-Mailsystem-Problem
   ```

---

## Produktiv-Checkliste

Vor Go-Live:

### OpenXE Backend

- [ ] **Datenbank**
  - [ ] Alle 7 Portal-Tabellen existieren
  - [ ] Indices korrekt gesetzt
  - [ ] Backup-Strategie definiert
  
- [ ] **Konfiguration**
  - [ ] Portal aktiviert
  - [ ] Shared Secret sicher (min. 32 Zeichen)
  - [ ] Portal-URL korrekt
  - [ ] Status-Mapping vollständig
  - [ ] Email-Templates angepasst (CI/CD)
  
- [ ] **Berechtigungen**
  - [ ] Mitarbeiter haben "ticket edit"
  - [ ] Kunden haben KEINE Backend-Rechte
  
- [ ] **Upload-Verzeichnis**
  - [ ] Existiert: `www/userfiles/ticket_media/`
  - [ ] Permissions: 0775, www-data:www-data
  - [ ] Disk-Space: mind. 10GB frei

### WordPress

- [ ] **Plugin**
  - [ ] Aktuelle Version installiert
  - [ ] Base URL korrekt (HTTPS!)
  - [ ] Shared Secret identisch
  - [ ] Verbindungstest erfolgreich
  
- [ ] **Portal-Seite**
  - [ ] Shortcode eingefügt
  - [ ] Veröffentlicht & erreichbar
  - [ ] SSL-Zertifikat gültig (Let's Encrypt)
  
- [ ] **Performance**
  - [ ] Caching-Plugin kompatibel (z.B. WP Rocket)
  - [ ] CDN konfiguriert (optional)

### Sicherheit

- [ ] **SSL/TLS**
  - [ ] HTTPS erzwungen (kein HTTP)
  - [ ] TLS 1.2+ aktiviert
  - [ ] HSTS-Header gesetzt
  
- [ ] **Firewall**
  - [ ] WordPress → OpenXE erlaubt
  - [ ] Rate Limiting aktiviert
  - [ ] Bruteforce-Protection (z.B. Fail2Ban)
  
- [ ] **Logs**
  - [ ] Portal-Log aktiviert
  - [ ] Log-Rotation konfiguriert
  - [ ] Monitoring-Alerts (z.B. >100 Errors/h)

### Testing

- [ ] **Funktional**
  - [ ] Login (alle 4 Verifier-Typen)
  - [ ] Media Upload & Download
  - [ ] Angebots-Workflow (inkl. DOI-Email)
  - [ ] Custom Status funktioniert
  - [ ] Benachrichtigungen erhalten
  
- [ ] **Performance**
  - [ ] Ladezeit < 2s (Google PageSpeed)
  - [ ] Concurrent Users: 10+ ohne Fehler
  - [ ] Mobile-Responsive (alle Breakpoints)
  
- [ ] **Security**
  - [ ] Penetration-Test (optional)
  - [ ] Session-Expiry funktioniert
  - [ ] Rate Limiting greift

### Dokumentation

- [ ] **Intern**
  - [ ] Mitarbeiter-Schulung durchgeführt
  - [ ] Runbooks für häufige Probleme
  - [ ] Eskalationspfad definiert
  
- [ ] **Extern**
  - [ ] Kunden über Portal informiert
  - [ ] FAQ-Seite erstellt
  - [ ] Support-Hotline aktualisiert

### Monitoring

- [ ] **Uptime**
  - [ ] External Monitor (z.B. UptimeRobot)
  - [ ] Alerting bei >5min Downtime
  
- [ ] **Logs**
  - [ ] Zentrales Logging (z.B. Graylog)
  - [ ] Error-Alerts (>50 Errors/h)
  
- [ ] **Metriken**
  - [ ] Portal-Nutzung tracken (Analytics)
  - [ ] Conversion-Rate (Login-Erfolg)

---

## Post-Launch

### Woche 1

- **Täglich:** Logs prüfen (Errors, Rate Limits)
- **Täglich:** Support-Tickets monitoren
- **Wöchentlich:** Performance-Report

### Monat 1

- **User Feedback** sammeln
- **Feature Requests** priorisieren
- **Optimierungen** basierend auf Analytics

### Langfristig

- **Quartalsweise:** Security-Audit
- **Halbjährlich:** Performance-Tuning
- **Jährlich:** Major-Update-Planung

---

## Support-Kontakte

**OpenXE Community:**
- Forum: https://forum.openxe.org
- GitHub: https://github.com/openxe/openxe

**Kommerzieller Support:**
- Email: support@openxe.org
- Hotline: +49 (0) XXX XXXXXX

**Notfall-Kontakt (24/7):**
- On-Call: +49 (0) XXX XXXXXX (nur kritische Systeme)

---

**Letzte Aktualisierung:** 2025-12-28  
**Version:** 1.0  
**Autoren:** OpenXE DevOps Team
