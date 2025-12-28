# OpenXE Ticket Portal - Benutzerhandbuch

**Version:** 1.0  
**Stand:** Dezember 2025  
**Zielgruppe:** Administratoren, Support-Mitarbeiter, End-User

---

## Inhaltsverzeichnis

1. [Übersicht](#übersicht)
2. [Admin-Konfiguration](#admin-konfiguration)
3. [Custom Status Management](#custom-status-management)
4. [Projekt-spezifisches Mapping](#projekt-spezifisches-mapping)
5. [Media Upload (Staff)](#media-upload-staff)
6. [Angebotsverwaltung](#angebotsverwaltung)
7. [Kundenportal](#kundenportal)
8. [Best Practices](#best-practices)
9. [Troubleshooting](#troubleshooting)

---

## Übersicht

Das OpenXE Ticket Portal ermöglicht Kunden einen sicheren Zugriff auf ihre Tickets, Reparaturstatus und Angebote über eine moderne Weboberfläche. Die Integration erfolgt über ein WordPress-Plugin.

### Hauptfunktionen

- **Ticket-Tracking**: Kunden sehen Echtzeit-Status ihrer Reparaturen
- **Angebotsverwaltung**: Double-Opt-In Workflow für Angebotsbestätigungen
- **Media Upload**: Mitarbeiter können Bilder und PDFs hochladen
- **Flexible Status**: Mehrstufige, projekt-spezifische Status-Workflows
- **Benachrichtigungen**: Konfigurierbare Email-Notifications
- **Barrierefreiheit**: Text-To-Speech für Nachrichten

---

## Admin-Konfiguration

### Grundeinstellungen

Navigieren Sie zu **Ticket → Portal Einstellungen** in OpenXE.

#### 1. Portal aktivieren

```
☑ Portal aktivieren
```

Aktiviert das gesamte Ticket Portal. Ohne diesen Haken sind alle Portal-Endpoints deaktiviert.

#### 2. Portal URL

```
Portal URL (WordPress): https://ihr-portal.de/tickets
```

Vollständige URL zur WordPress-Seite mit dem Shortcode `[openxe_ticket_portal]`.

#### 3. Shared Secret

```
Shared Secret: [Generieren] [Kopieren]
```

**Wichtig!** Dieses Secret muss **identisch** im WordPress-Plugin konfiguriert sein.

**Setup-Schritte:**
1. Klicken Sie auf "Generieren"
2. Klicken Sie auf "Kopieren"
3. Im WordPress-Admin → Einstellungen → OpenXE Ticket Portal
4. Fügen Sie das Secret ein
5. Speichern Sie beide Seiten

#### 4. Benachrichtigungen

**Standard-Einstellung:**
```
☑ Standard: Benachrichtigungen für alle Statusänderungen
```

Bei aktivierter Option erhalten Kunden bei JEDER Statusänderung eine Email (außer sie deaktivieren es manuell).

**Email-Templates:**

```
Betreff: Ticket #{ticket_number} Statusänderung

Text:
Der Status Ihres Tickets #{ticket_number} wurde aktualisiert.
Status: {status_label}

{public_note}

Viele Grüße
{company_name}
```

**Verfügbare Platzhalter:**
- `{ticket_number}` - Ticketnummer (z.B. 4711)
- `{ticket_id}` - Interne ID
- `{status_key}` - Technischer Statusschlüssel
- `{status_label}` - Anzeigename des Status
- `{customer_name}` - Kundenname
- `{public_note}` - Öffentliche Notiz des Mitarbeiters
- `{company_name}` - Firmenname

#### 5. Token-Laufzeiten

```
Session TTL:     60 Minuten (Standard)
Code TTL:        15 Minuten
Magic Link TTL:  30 Minuten
Double Opt-In:   120 Minuten
```

**Empfehlungen:**
- Session: 60-240 Min (abhängig von Sicherheitsbedarf)
- Code: 10-30 Min (für Einmal-Codes)
- Magic Link: 24-72 Stunden (Bequemlichkeit)
- DOI: 60-180 Min (für Angebotsbestätigungen)

#### 6. Sicherheit

```
Max. Fehlversuche: 5
Sperrzeit: 15 Minuten
☑ Portal-Log aktivieren
```

**Rate Limiting** ist automatisch aktiv:
- 60 Requests/Minute pro IP und Endpoint
- Konfigurierbar via WordPress-Filter

---

## Custom Status Management

### Übersicht

Das Portal unterstützt drei Arten von Status:

1. **Standard-Status** (12 vordefiniert)
2. **Custom Status** (beliebig erweiterbar)
3. **Projekt-Status** (projekt-spezifisch gemappt)

### Standard-Status verwalten

In **Ticket → Portal Einstellungen → Statusmodell**:

```
┌────────────────────────────────────────────────────┐
│ Standard Kundenstatus Texte                        │
├────────────────────┬───────────────────────────────┤
│ Status Key         │ Bezeichnung (Umbenennen)      │
├────────────────────┼───────────────────────────────┤
│ paket_eingegangen  │ Paket eingegangen             │
│ in_bearbeitung     │ In Bearbeitung                │
│ warte_ersatzteile  │ Warte auf Ersatzteile         │
│ ...                │ ...                           │
└────────────────────┴───────────────────────────────┘
```

**Hinweis:** Die Keys bleiben bestehen, aber Sie können die Anzeigetexte anpassen.

### Custom Status hinzufügen

#### Beispiel: "3D-Druck läuft"

1. Scrollen Sie zu **"Zusätzliche / Custom Kundenstatus"**
2. Tragen Sie in der leeren Zeile ein:
   ```
   Key:      3d_druck_laueft
   Label:    3D-Druck läuft (voraussichtlich 48h)
   Aktiv:    ☑
   ```
3. Klicken Sie auf "Speichern"

Der neue Status erscheint nun in:
- Mitarbeiter-Dropdowns
- Kunden-Portal (wenn aktiv gemappt)
- Benachrichtigungs-Einstellungen

**Best Practice:** Verwenden Sie sprechende Keys:
- ✅ `3d_druck_laueft`
- ✅ `lackierung_trocknet`
- ❌ `custom1`
- ❌ `s1`

### Interner Status → Kundenstatus Mapping

```
┌────────────────────────────────────────────────────┐
│ Mapping: Intern -> Kundenstatus (Standard)        │
├────────────────────┬───────────────────────────────┤
│ Interner Status    │ Kundenstatus                  │
├────────────────────┼───────────────────────────────┤
│ neu                │ [Dropdown: paket_eingegangen] │
│ offen              │ [Dropdown: paket_eingegangen] │
│ warten_e           │ [Dropdown: in_bearbeitung]    │
│ klaeren            │ [Dropdown: rueckfrage]        │
│ warten_kd          │ [Dropdown: warten_auf_...]    │
│ abgeschlossen      │ [Dropdown: abgeschlossen]     │
└────────────────────┴───────────────────────────────┘
```

**Wichtig:** Wenn Sie den internen Status eines Tickets ändern, wird automatisch der Kundenstatus aktualisiert (außer er wurde manuell überschrieben).

---

## Projekt-spezifisches Mapping

### Use Case

Sie haben unterschiedliche Workflows für verschiedene Projekte:

- **Projekt 1 (3D-Druck):** Detaillierte Fertigungsschritte
- **Projekt 2 (Elektronik):** Standardablauf
- **Projekt 3 (Express):** Minimale Status

### Konfiguration

Im Feld **"Multiplex / Projekt-spezifisches Mapping"**:

```json
{
  "42": {
    "neu": "3d_modell_wird_erstellt",
    "warten_e": "3d_druck_laueft",
    "klaeren": "qualitaetskontrolle_3d"
  },
  "17": {
    "neu": "express_angenommen",
    "warten_e": "express_bearbeitung",
    "abgeschlossen": "express_versandt"
  }
}
```

**Erklärung:**
- `"42"` = Projekt-ID aus OpenXE
- `"neu"` = Interner Ticket-Status
- `"3d_modell_wird_erstellt"` = Kundenstatus (muss existieren!)

**Fallback:** Wenn ein Ticket keinem konfigurierten Projekt zugeordnet ist, greift das Standard-Mapping.

### Schema-Validierung

```json
{
  "PROJEKT_ID": {
    "INTERNER_STATUS": "KUNDENSTATUS_KEY"
  }
}
```

**Fehler vermeiden:**
- ✅ Alle Kundenstatus-Keys müssen in "Custom Status" oder "Standard Status" definiert sein
- ✅ Projekt-IDs als Strings (`"42"` nicht `42`)
- ✅ Gültiges JSON (Validator: jsonlint.com)

---

## Media Upload (Staff)

### Zugriff

1. Öffnen Sie ein Ticket
2. Klicken Sie auf **"Portal (Mitarbeiter)"** (neuer Tab)
3. URL: `index.php?module=ticket&action=portal_staff&id=123`

### Dateien hochladen

```
┌────────────────────────────────────────────────────┐
│ Medien Upload (Bilder/PDF)                         │
├────────────────────────────────────────────────────┤
│ Dateiformate: JPG, PNG, WebP, PDF (max. 10MB)     │
│                                                     │
│ [Datei auswählen...]                               │
│                                                     │
│ ☐ Für Kunden im Portal sichtbar                   │
│                                                     │
│ [Datei Hochladen]                                  │
└────────────────────────────────────────────────────┘
```

**Workflow:**
1. Wählen Sie eine Datei (z.B. Reparaturfoto)
2. **Optional:** Haken setzen → Kunde sieht die Datei
3. Klicken Sie "Hochladen"

### Hochgeladene Medien verwalten

```
┌────────────────────────────────────────────────────┐
│ Hochgeladene Medien                                │
├──────────────┬──────────┬────────────┬──────┬──────┤
│ Datei        │ Größe    │ Datum      │ ✓    │ Menü │
├──────────────┼──────────┼────────────┼──────┼──────┤
│ defekt.jpg   │ 245.3 KB │ 2025-12-27 │ ☑    │ Lö...│
│ angebot.pdf  │ 89.1 KB  │ 2025-12-27 │ ☐    │ Lö...│
└──────────────┴──────────┴────────────┴──────┴──────┘
```

**Spalten:**
- **Datei**: Klickbar zum Download/Preview
- **Größe**: Dateigröße in KB
- **Datum**: Upload-Zeitstempel
- **✓**: Aktiviert = Kunde sieht die Datei
- **Menü**: "Löschen"-Link (mit Bestätigung)

**Sicherheit:**
- Dateien werden mit SHA-256 Hash gespeichert
- Original-Dateiname wird in Datenbank gespeichert
- Keine Skript-Execution möglich
- MIME-Type wird validiert

### Kunden-Sicht

Wenn "Sichtbar" aktiviert ist, erscheint im Kundenportal:

```
┌────────────────────────────────────────────────────┐
│ Dokumente & Bilder                                 │
├────────────────────────────────────────────────────┤
│ • defekt.jpg (245 KB, 27.12.2025) [Download]      │
└────────────────────────────────────────────────────┘
```

---

## Angebotsverwaltung

### Workflow-Übersicht

1. **OpenXE:** Angebot erstellen → Status "freigegeben"
2. **Portal:** Kunde sieht Angebot
3. **Kunde:** Akzeptiert oder lehnt ab
4. **Bei Accept:** Double-Opt-In Email
5. **Kunde:** Klickt Bestätigungslink
6. **OpenXE:** Angebot wird bestätigt registriert

### Konfiguration

```
☑ Angebotsbestätigung im Portal erlauben
```

**AGB-Konfiguration:**
```
AGB URL: https://ihre-firma.de/agb
AGB Version: 2025-01
```

Diese Angaben werden:
- Im Portal als Link angezeigt
- In der DOI-Email mitgeschickt
- In der Audit-Tabelle gespeichert

### Kunden-Ansicht

```
┌────────────────────────────────────────────────────┐
│ Offene Angebote                                    │
├────────────────────────────────────────────────────┤
│ ┌────────────────────────────────────────────────┐ │
│ │ Angebot #2025-001      27.12.2025             │ │
│ │ Summe: 149.90 EUR                             │ │
│ │                       [Auswählen]             │ │
│ └────────────────────────────────────────────────┘ │
└────────────────────────────────────────────────────┘
```

Nach Klick auf "Auswählen":

```
┌────────────────────────────────────────────────────┐
│ Angebot bestätigen/ablehnen                        │
├────────────────────────────────────────────────────┤
│ Ihre Entscheidung: ⦿ Akzeptieren  ○ Ablehnen     │
│                                                     │
│ Kommentar (optional):                              │
│ ┌─────────────────────────────────────────────────┐│
│ │                                                 ││
│ └─────────────────────────────────────────────────┘│
│                                                     │
│ ☑ Ich akzeptiere die AGB (Version 2025-01) [Link]│
│                                                     │
│ [ Absenden ]                                       │
└────────────────────────────────────────────────────┘
```

### Double-Opt-In Email

Nach Accept-Klick erhält der Kunde:

```
Betreff: Angebotsbestätigung bestätigen

Bitte bestätigen Sie Ihre Entscheidung:
https://openxe.example.com/index.php?module=ticket&action=portal_offer_confirm&doi_token=abc123...

AGB: https://ihre-firma.de/agb
```

**Wichtig:** Link ist nur 120 Minuten gültig (konfigurierbar).

### Audit-Trail

Alle Aktionen werden in `ticket_offer_confirmation` gespeichert:

- IP-Adresse
- User-Agent
- Zeitstempel
- Kommentar
- AGB-Version
- DOI-Token-Hash

---

## Kundenportal

### Login-Methoden

Kunden haben 4 Login-Optionen:

#### 1. **Automatisch** (Empfohlen)
```
Verifikation: Automatisch
→ Portal erkennt verfügbare Methode (PLZ oder Email aus Adresse)
```

#### 2. **PLZ**
```
Ticketnummer: 4711
Verifikation: PLZ
PLZ/Ort: 12345
```

#### 3. **Email**
```
Ticketnummer: 4711
Verifikation: E-Mail
E-Mail: kunde@example.com
→ Kunde erhält Code per Email
Code: 123456
```

#### 4. **Magic Link** (bester UX)
```
Kunde klickt Link aus Ticket-Email
→ Direkter Zugang, keine weitere Eingabe
```

### Portal-Oberfläche

```
┌────────────────────────────────────────────────────┐
│ Ticket Portal                                      │
├────────────────────────────────────────────────────┤
│ Status: In Bearbeitung                             │
│ Aktualisiert: 27.12.2025 14:30                    │
│                                                     │
│ [Aktualisieren] [Druckformular] [Download]        │
│ [Angebot bestätigen]                               │
├────────────────────────────────────────────────────┤
│ Benachrichtigungen                                 │
│ ☑ Paket eingegangen                               │
│ ☑ In Bearbeitung                                  │
│ ☐ Warte auf Ersatzteile                           │
│ [ Speichern ]                                      │
├────────────────────────────────────────────────────┤
│ Dokumente & Bilder                                 │
│ • reparatur_foto.jpg (245 KB) [Download]          │
├────────────────────────────────────────────────────┤
│ Nachrichten                                        │
│ ┌────────────────────────────────────────────────┐ │
│ │ Team · 27.12.2025 13:15        [Vorlesen]     │ │
│ │ Druckkopf wird ersetzt, dauert ca. 2 Tage.   │ │
│ └────────────────────────────────────────────────┘ │
│                                                     │
│ Neue Nachricht:                                    │
│ ┌─────────────────────────────────────────────────┐│
│ │                                                 ││
│ └─────────────────────────────────────────────────┘│
│ [ Senden ]                                         │
└────────────────────────────────────────────────────┘
```

### Barrierefreiheit: Text-to-Speech

Der "Vorlesen"-Button nutzt die native Browser-API:

```javascript
// Automatisch verfügbar in:
// ✅ Chrome/Edge (alle Plattformen)
// ✅ Safari (macOS/iOS)
// ✅ Firefox (alle Plattformen)
```

**Keine Installation nötig!** Funktioniert out-of-the-box.

---

## Best Practices

### Status-Management

**✅ DO:**
- Verwenden Sie sprechende, eindeutige Keys
- Halten Sie Status-Labels kurz (<40 Zeichen)
- Dokumentieren Sie Custom-Status intern
- Testen Sie Projekt-Mapping vor Produktivbetrieb

**❌ DON'T:**
- Keine Umlaute in Keys (nutzen Sie `ae` statt `ä`)
- Keine Leerzeichen in Keys
- Nicht zu viele Status (max. 15-20)
- Keine Status nachträglich löschen (Datenbank-Referenzen!)

### Media Upload

**✅ DO:**
- Markieren Sie nur relevante Bilder als "sichtbar"
- Komprimieren Sie große Bilder vor Upload
- Nutzen Sie aussagekräftige Dateinamen
- Löschen Sie veraltete Medien regelmäßig

**❌ DON'T:**
- Keine sensiblen Daten in öffentliche Medien
- Keine Screenshots von Kundendaten
- Keine extrem großen Dateien (>5MB wenn möglich)

### Benachrichtigungen

**✅ DO:**
- Passen Sie Email-Templates an Ihre CI/CD an
- Testen Sie Platzhalter vor Aktivierung
- Informieren Sie Kunden über Benachrichtigungsoptionen
- Monitoren Sie Email-Bounce-Rate

**❌ DON'T:**
- Nicht zu viele Notifications (Spam-Risiko)
- Keine technischen Status-Keys im Email-Text
- Keine Notifications bei internen Status-Wechseln

---

## Troubleshooting

### Problem: "Portal deaktiviert"

**Symptom:** Kunde sieht Fehlermeldung beim Login

**Lösung:**
1. OpenXE → Ticket → Portal Einstellungen
2. Prüfen: `☑ Portal aktivieren`
3. Prüfen: Portal URL korrekt
4. Speichern

### Problem: "Shared Secret Mismatch"

**Symptom:** Alle Portal-Requests geben 403-Fehler

**Lösung:**
1. OpenXE: Neues Secret generieren & kopieren
2. WordPress: Einstellungen → OpenXE Ticket Portal
3. Secret einfügen & speichern
4. Test-Verbindung klicken

### Problem: Kunde erhält keine Emails

**Checkliste:**
1. ✅ Email-Adresse im Ticket korrekt?
2. ✅ Spam-Ordner prüfen
3. ✅ OpenXE Email-Einstellungen testen
4. ✅ Portal-Log prüfen (aktiviert?)
5. ✅ Benachrichtigungs-Präferenzen des Kunden

**Debug:**
```
OpenXE → Ticket → Portal Einstellungen
→ Scroll down: "Portal-Log"
→ Suche nach: "notification_sent" oder "email_failed"
```

### Problem: Medien werden nicht angezeigt

**Kunde sieht keine Medien:**
1. Mitarbeiter: Prüfen Sie "Sichtbar"-Checkbox
2. Browser: Hard-Refresh (Ctrl+F5)
3. Portal-Log: Prüfen auf Fehler

**Staff sieht keine Upload-Form:**
1. Berechtigung: "ticket" → "edit" erforderlich
2. Browser-Console auf JavaScript-Fehler prüfen

### Problem: Projekt-Mapping funktioniert nicht

**Status wird nicht korrekt gemappt:**

**Debug-Schritte:**
1. JSON-Syntax validieren (jsonlint.com)
2. Projekt-ID des Tickets prüfen
3. Kundenstatus-Keys existieren?
4. Portal-Log: Suche "status_mapped"

**Beispiel-Fehler:**
```json
// ❌ Falsch (Nummer statt String):
{ 42: { "neu": "custom" } }

// ✅ Richtig:
{ "42": { "neu": "custom" } }
```

### Problem: "Rate Limited"

**Symptom:** Kunde erhält 429-Fehler nach mehreren Requests

**Lösung (temporär):**
```php
// WordPress functions.php oder Plugin
add_filter('openxe_ticket_portal_rate_limit', function($limit) {
    return 120; // Erhöhe auf 120/min
}, 10, 1);
```

**Langfristig:** Bot-Traffic identifizieren und blockieren.

---

## Support & Weiterführende Dokumentation

- **API-Referenz:** `doc/ticket-portal-api-reference.md`
- **Setup-Guide:** `doc/ticket-portal-setup.md`
- **Entwickler-Docs:** Siehe README.md im Plugin-Verzeichnis

**Community:**
- GitHub Issues: [Repository-Link]
- Forum: [Forum-Link]

---

**Letzte Aktualisierung:** 2025-12-28  
**Version:** 1.0  
**Autoren:** OpenXE Development Team
