# OpenXE Ticket Portal - Dokumentations-Übersicht

Diese Dokumentation deckt alle Aspekte des OpenXE Ticket Portals ab - von der Installation über die Nutzung bis zur Entwicklung.

## 📚 Dokumentations-Index

### Für Benutzer & Administratoren

#### 1. **[Benutzerhandbuch](ticket-portal-user-guide.md)** ⭐ START HIER
- ✅ **Admin-Konfiguration**: Portal-Einstellungen, Shared Secret, Token-Laufzeiten
- ✅ **Custom Status Management**: Eigene Status definieren, umbenennen
- ✅ **Projekt-spezifisches Mapping**: Unterschiedliche Workflows pro Projekt
- ✅ **Media Upload (Staff)**: Bilder & PDFs hochladen, Sichtbarkeit steuern
- ✅ **Angebotsverwaltung**: Double-Opt-In Workflow
- ✅ **Kundenportal**: Login-Methoden, Features, Barrierefreiheit
- ✅ **Best Practices**: Do's & Don'ts, Empfehlungen
- ✅ **Troubleshooting**: Häufige Probleme & Lösungen

**Zielgruppe:** Administratoren, Support-Mitarbeiter  
**Umfang:** ~600 Zeilen  
**Schwierigkeit:** Anfänger bis Fortgeschritten

---

### Für Entwickler

#### 2. **[API-Referenz](ticket-portal-api-reference.md)** ⭐ FÜR ENTWICKLER
- ✅ **Architektur**: Komponenten-Übersicht, Request-Flow
- ✅ **Authentifizierung**: Session-Erstellung, Verifier-Types
- ✅ **11 Backend-Endpoints**: Vollständige API-Dokumentation
  - `portal_session`, `portal_status`, `portal_messages`, `portal_message`
  - `portal_offers`, `portal_offer`, `portal_offer_confirm`
  - `portal_media`, `portal_media_download`
  - `portal_notifications`, `portal_notification`
- ✅ **WordPress AJAX Handlers**: Handler-Pattern, Registrierung
- ✅ **JavaScript API**: Initialisierung, Helpers, Beispiele
- ✅ **Datenbank-Schema**: 7 Tabellen vollständig dokumentiert
- ✅ **Hooks & Filter**: WordPress & OpenXE Extension-Points
- ✅ **Sicherheit**: Token-Hashing, Rate Limiting, File Upload
- ✅ **Error Handling**: Standard-Responses, Error-Codes, Logging
- ✅ **Beispiele**: Vollständige Integration von Custom Features

**Zielgruppe:** Entwickler, System-Integratoren  
**Umfang:** ~1000 Zeilen  
**Schwierigkeit:** Fortgeschritten

---

### Für System-Administratoren

#### 3. **[Setup & Installation](ticket-portal-setup.md)** ⭐ DEPLOYMENT
- ✅ **Systemvoraussetzungen**: OpenXE, WordPress, Server
- ✅ **OpenXE Backend Setup**:
  - Datenbank-Migration (auto & manuell)
  - Berechtigungen, Upload-Verzeichnis
  - Portal-Einstellungen, Status-Konfiguration
- ✅ **WordPress Plugin Installation**:
  - Download, Upload, Konfiguration
  - Portal-Seite erstellen, URL-Verknüpfung
- ✅ **Ersteinrichtung**: Test-Workflow (Step-by-Step)
- ✅ **Migration & Updates**: Datenmigration, Rollback
- ✅ **Troubleshooting**: Connection Failed, Rate Limiting, Emails
- ✅ **Produktiv-Checkliste**: 50+ Checkpoints vor Go-Live

**Zielgruppe:** DevOps, System-Administratoren  
**Umfang:** ~800 Zeilen  
**Schwierigkeit:** Fortgeschritten

---

### Für Plugin-Nutzer (WordPress)

#### 4. **[WordPress Plugin README](../wp-plugin/openxe-ticket-portal/README.md)**
- ✅ **Features**: Überblick über alle Funktionen
- ✅ **Installation**: Quick-Start in 4 Schritten
- ✅ **Shortcode**: Verwendung & Parameter
- ✅ **Hooks & Filter**: Rate Limit, Custom CSS
- ✅ **Debugging**: Debug-Modus, Verbindungstest
- ✅ **Performance**: Caching, CDN
- ✅ **Security Best Practices**
- ✅ **Changelog & Roadmap**

**Zielgruppe:** WordPress-Administratoren  
**Umfang:** ~300 Zeilen  
**Schwierigkeit:** Anfänger bis Mittel

---

## 🚀 Quick-Start-Guide

### Ich möchte...

#### ...das Portal installieren
1. Lies: **[Setup & Installation](ticket-portal-setup.md)**
2. Folge: Schritt 1-5 (OpenXE Backend)
3. Folge: Schritt 1-5 (WordPress Plugin)
4. Durchlaufe: Test-Workflow

#### ...das Portal konfigurieren
1. Lies: **[Benutzerhandbuch](ticket-portal-user-guide.md)** → "Admin-Konfiguration"
2. Optional: "Custom Status Management" für eigene Workflows

#### ...das Portal erweitern (Development)
1. Lies: **[API-Referenz](ticket-portal-api-reference.md)** → "Architektur"
2. Siehe: "Beispiel: Vollständige Integration" (am Ende)
3. Nutze: Hooks & Filter für Extensions

#### ...ein Problem lösen
1. **Benutzerhandbuch** → "Troubleshooting"
2. **Setup-Guide** → "Troubleshooting"
3. **WordPress README** → "Debugging"
4. Falls ungelöst: Community-Forum oder Support kontaktieren

---

## 📊 Feature-Matrix

| Feature | User-Guide | API-Ref | Setup-Guide | Plugin-README |
|---------|------------|---------|-------------|---------------|
| **Installation** | ➖ | ➖ | ✅ | ✅ |
| **Konfiguration** | ✅ | ➖ | ✅ | ✅ |
| **Custom Status** | ✅ | ✅ | ➖ | ➖ |
| **Media Upload** | ✅ | ✅ | ➖ | ➖ |
| **API-Endpoints** | ➖ | ✅ | ➖ | ➖ |
| **Datenbank-Schema** | ➖ | ✅ | ➖ | ➖ |
| **Hooks & Filter** | ➖ | ✅ | ➖ | ✅ |
| **Troubleshooting** | ✅ | ➖ | ✅ | ✅ |
| **Security** | ✅ | ✅ | ✅ | ✅ |
| **Performance** | ➖ | ✅ | ➖ | ✅ |

---

## 🎯 Lernpfade

### Pfad 1: Administrator (Erste Schritte)
```
1. Setup-Guide lesen (Installation)
   ↓
2. User-Guide → Admin-Konfiguration
   ↓
3. User-Guide → Custom Status (bei Bedarf)
   ↓
4. User-Guide → Troubleshooting (bei Problemen)
```

**Geschätzte Zeit:** 2-3 Stunden  
**Voraussetzungen:** Grundkenntnisse OpenXE & WordPress

---

### Pfad 2: Entwickler (Integration)
```
1. Setup-Guide lesen (Systemarchitektur verstehen)
   ↓
2. API-Referenz → Architektur & Authentifizierung
   ↓
3. API-Referenz → Backend-Endpoints (eigene testen)
   ↓
4. API-Referenz → Hooks & Filter (Extension entwickeln)
   ↓
5. API-Referenz → Beispiele (Vollständige Integration)
```

**Geschätzte Zeit:** 1-2 Tage  
**Voraussetzungen:** PHP, JavaScript, REST APIs, MySQL

---

### Pfad 3: DevOps (Deployment)
```
1. Setup-Guide → Systemvoraussetzungen prüfen
   ↓
2. Setup-Guide → Schritt-für-Schritt Installation
   ↓
3. Setup-Guide → Test-Workflow durchlaufen
   ↓
4. Setup-Guide → Produktiv-Checkliste (50+ Punkte)
   ↓
5. Setup-Guide → Monitoring & Post-Launch
```

**Geschätzte Zeit:** 4-6 Stunden  
**Voraussetzungen:** Linux, MySQL, Apache/nginx, WordPress

---

## 🔧 Code-Beispiele nach Thema

### Status-Management
- **User-Guide:** "Custom Status Management" → Beispiel: "3D-Druck läuft"
- **API-Ref:** "Backend-Endpoints" → `portal_status`
- **API-Ref:** "Beispiel: Vollständige Integration" → Custom Status Hook

### Media Upload
- **User-Guide:** "Media Upload (Staff)" → Workflow
- **API-Ref:** "Backend-Endpoints" → `portal_media`, `portal_media_download`
- **API-Ref:** "Sicherheit" → File Upload Validation

### Angebote
- **User-Guide:** "Angebotsverwaltung" → Workflow-Übersicht
- **API-Ref:** "Backend-Endpoints" → `portal_offers`, `portal_offer`, `portal_offer_confirm`

### Benachrichtigungen
- **User-Guide:** "Admin-Konfiguration" → Benachrichtigungen
- **API-Ref:** "Backend-Endpoints" → `portal_notifications`, `portal_notification`

---

## 📖 Glossar

| Begriff | Bedeutung |
|---------|-----------|
| **DOI** | Double Opt-In (Bestätigung via Email-Link) |
| **Magic Link** | Einmal-Login-Link (aus Email) |
| **Session Token** | Temporärer Token für Portal-Zugriff |
| **Shared Secret** | Geheimes Passwort zwischen WP ↔ OpenXE |
| **Verifier** | Login-Methode (PLZ, Email, Code, Magic) |
| **Kundenstatus** | Portal-sichtbarer Status (entkoppelt vom internen) |
| **Rate Limiting** | Schutz vor Spam/Bruteforce (max. Requests/Zeit) |
| **MIME-Type** | Dateiformat-Identifikation (z.B. `image/jpeg`) |
| **Hash** | Einweg-Verschlüsselung (SHA-256) |
| **Transient** | WordPress-Cache mit Ablaufzeit |

---

## 🆘 Support-Ressourcen

### Dokumentation
- ✅ Alle 4 Dokumente in diesem Verzeichnis
- ✅ Inline-Code-Kommentare (PHPDoc)
- ✅ README im WordPress-Plugin

### Community
- **Forum:** https://forum.openxe.org
- **GitHub Issues:** https://github.com/openxe/openxe/issues
- **Discussions:** https://github.com/openxe/openxe/discussions

### Kommerzieller Support
- **Email:** support@openxe.org
- **Hotline:** +49 (0) XXX XXXXXX (Bürozeiten)
- **Enterprise:** 24/7 Support verfügbar

---

## 📝 Mitwirken

**Dokumentations-Verbesserungen:**
1. Fehler gefunden? → GitHub Issue erstellen
2. Klarstellung nötig? → Pull Request mit Verbesserung
3. Beispiel fehlt? → Contribution willkommen!

**Guidelines:**
- Markdown (.md) Format
- Deutsche Sprache (für User-Docs)
- Englisch für Code-Kommentare
- Beispiele immer mit Kontext

---

## 🎓 Weiterführende Ressourcen

### OpenXE
- **Haupt-Dokumentation:** https://docs.openxe.org
- **API-Übersicht:** https://api.openxe.org
- **Entwickler-Guide:** `../../CONTRIBUTING.md`

### WordPress
- **Plugin-Entwicklung:** https://developer.wordpress.org/plugins/
- **REST API:** https://developer.wordpress.org/rest-api/
- **Hooks Reference:** https://developer.wordpress.org/reference/hooks/

### Security
- **OWASP Top 10:** https://owasp.org/www-project-top-ten/
- **WordPress Security:** https://codex.wordpress.org/Hardening_WordPress
- **PHP Security:** https://www.phptherightway.com/#security

---

## 📅 Versionierung

| Datum | Version | Dokument | Änderungen |
|-------|---------|----------|------------|
| 2025-12-28 | 1.0 | Alle | Initial Release |
| | | | - User-Guide komplett |
| | | | - API-Referenz komplett |
| | | | - Setup-Guide komplett |
| | | | - Plugin-README komplett |

**Nächstes Update:** Q1 2026 (mit Version 1.1.0)

---

**Erstellt von:** OpenXE Development Team  
**Letzte Aktualisierung:** 2025-12-28  
**Lizenz:** GPL-3.0

---

💡 **Tipp:** Nutzen Sie die Suchfunktion Ihres Editors (Strg+F) um schnell relevante Sektionen zu finden!
