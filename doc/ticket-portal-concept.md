# Ticket Portal Konzept (Entwurf)

## Ziel
- Ein Kundenportal, das Ticketstatus zeigt, Feedback und Angebotsbestaetigung erlaubt, und Kundenkommentare im Ticket speichert.
- Ein Mitarbeiter-Portal (mobil), das per QR-Code erreichbar ist, Status aendert, interne Notizen pflegt und Medien-Dokumentation erfasst.
- WordPress-Anbindung via Plugin/Shortcode (robust, nicht nur iframe).

## Rollen und Zugaenge
- Kunde: Zugriff ueber sicheren Link + Verifikation (siehe Sicherheit), kann Status sehen, Kommentare schreiben, Angebote bestaetigen/ablehnen.
- Mitarbeiter: Zugriff per Login am Handy, optional per QR-Code direkt ins Ticket, mit Schreibrechten.
- Admin: Konfiguration (Status-Mapping, Benachrichtigungen, Portal-Design, WP-Integration).

## Kundenportal (Funktionen)
- Statusanzeige: kundenfreundliche Statustexte, Datum der letzten Aenderung, naechster Schritt.
- Kundenkommentar: einfache Textantwort, wird im Ticket hinterlegt (kein Datei-Upload).
  - Kommentare bleiben fuer den Kunden als Chatverlauf sichtbar.
  - Mitarbeiterantworten erscheinen im Portal, wenn sie als kundensichtbar markiert sind.
- Angebotsbestaetigung:
  - Akzeptieren/ablehnen mit optionalem Kommentar.
  - Bei Bestaetigung wird automatisch ein Auftrag erzeugt.
  - Ergebnis wird im Ticket protokolliert und im Angebot vermerkt.
  - Jede Bestaetigung wird nachvollziehbar dokumentiert (Zeit, Kunde, IP, Inhalt).
  - Optional: Double Opt-In inkl. AGB-Bestaetigung (Verifikation + AGB-Checkbox im Portal).
- Sichtbare Kommentare vom Team: Mitarbeiter koennen "Kommentar fuer Kunden" hinterlegen, der im Portal sichtbar ist.

## Mitarbeiter-Portal (Funktionen)
- Statuswechsel + interner Kommentar.
- Medien-Dokumentation (lokal, keine Cloud-Dienste):
  - Bilder hochladen (Geraetefotos, Schaden, Belege).
  - VTT (Voice-to-Text) fuer schnelle Doku, mit Speicherung im Ticket. (spaeter)
  - Seriennummern-Erkennung (SN): OCR aus Bildern, manuelles Korrekturfeld. (spaeter)
- QR-Code fuer schnellen Zugriff auf das Ticket (mit Login).

## Statusmodell (Vorschlag)
Interne Stati existieren bereits; zusaetzlich wird ein "Kundenstatus" gepflegt.
Mapping-Beispiel (intern -> kundenfreundlich):
- neu/offen -> "Paket eingegangen"
- warten_e -> "In Bearbeitung"
- klaeren -> "Rueckfrage"
- warten_kd -> "Warten auf Ihre Rueckmeldung"
- abgeschlossen -> "Abgeschlossen"

Zusaetzliche kundenorientierte Stati (optional):
- "Paket eingegangen"
- "Versandschaden zu klaeren"
- "Warte auf Ersatzteile"
- "In Bearbeitung"
- "Angebot erstellt"
- "Angebot bestaetigt"
- "Angebot abgelehnt"
- "Reparatur begonnen"
- "Qualitaetspruefung" (inkl. Testdruck als interner Teilschritt)
- "Versandbereit"

## Benachrichtigungen
- Benachrichtigungen fuer alle Statusaenderungen (Standard).
- Optional kann der Kunde eine Auswahl treffen (nachtraeglich).
- Versandkanal: E-Mail (kein WhatsApp/Telefon).
- Benachrichtigungstexte sind konfigurierbar (Platzhalter fuer Ticket/Status/Kommentar).

## Sicherheit
- Kundenportal Zugriff ueber Token-Link + Verifikation:
  - Variante A: Token-Link + Eingabe von PLZ oder E-Mail.
  - Variante B: Token-Link + Einmalcode per E-Mail.
  - Beide Varianten sollen verfuegbar sein.
  - Optional: Double Opt-In fuer Angebotsbestaetigung inkl. AGB-Zustimmung.
- Tokens gehasht speichern, zeitlich begrenzen, revoke moeglich.
- Rate-Limit und Audit-Log fuer Portalzugriffe.
- Mitarbeiterzugriff nur mit Login, QR ist nur ein Shortcut.

## Systemeinstellungen (optional schaltbar)
- Portal global aktivierbar/deaktivierbar.
- Angebotsbestaetigung im Portal separat schaltbar.
- Kundenkommentare im Portal separat schaltbar.
- Statusbenachrichtigungen als Standardprofil aktivierbar.
- Kundenstatus Texte + Mapping interner Status -> Kundenstatus pflegbar.
- Portal je Projekt/Warteschlange aktivierbar (optional).

## WordPress-Integration (robust)
- Eigenes WP-Plugin mit Shortcode:
  - Shortcode laedt Portal-UI in WP.
  - Backend ruft OpenXE API serverseitig auf (kein direkter Token im Browser).
  - Caching + Fehlerseiten (z.B. "Ticket nicht gefunden").
- SSO optional via Token oder einmaliger Session-Token.

## Datenmodell-Erweiterungen (Vorschlag)
- ticket_portal_access: ticket_id, token_hash, scope, created_at, expires_at, revoked, last_access_at, verifier_expires_at
- ticket_portal_message: id, ticket_id, author_type (customer/staff), text, is_public, created_at
- ticket_status_log: ticket_id, status_from, status_to, changed_by, changed_at, note_public, note_internal
- ticket_repair_media: id, ticket_id, file_id, media_type, ocr_sn, created_at, created_by
- ticket_notification_pref: ticket_id, customer_id, status_key, enabled

## Datenmodell Details (Vorschlag)
- ticket_portal_access
  - id, ticket_id, token_hash, scope (customer/staff), verifier_type, verifier_hash
  - created_at, expires_at, revoked_at, last_access_at, last_access_ip, last_access_ua
  - verifier_expires_at (fuer Einmalcode)
- ticket_portal_message
  - id, ticket_id, author_type (customer/staff/system), author_id, text
  - is_public, created_at, source='portal', mirrored_message_id
- ticket_customer_status
  - ticket_id, status_key, status_label, updated_at, updated_by
- ticket_status_log
  - ticket_id, status_from, status_to, changed_by, changed_at, note_public, note_internal
- ticket_offer_confirmation
  - id, ticket_id, angebot_id, action (accept/decline), comment
  - agb_version, agb_accepted_at
  - doi_token_hash, doi_requested_at, doi_confirmed_at
  - created_at, created_by_type, created_by_id, ip, user_agent
  - order_id (bei accept)
- ticket_notification_pref
  - ticket_id, customer_id, status_key, enabled

## Portal-Nachrichten Handling
- Portal-Nachrichten werden in `ticket_portal_message` gespeichert.
- Zusaetzlich werden sie in `ticket_nachricht` gespiegelt (medium='portal'), damit der Verlauf in der Ticketliste sichtbar ist.
- Sichtbarkeit im Portal richtet sich nach `is_public`; interne Nachrichten bleiben nur intern.

## Portal-Nachrichten Spiegelung (Mapping)
- ticket_nachricht.ticket = ticket.schluessel
- ticket_nachricht.verfasser = customer/staff name (wenn vorhanden)
- ticket_nachricht.mail = customer mail (wenn bekannt), sonst leer
- ticket_nachricht.zeit = now()
- ticket_nachricht.text = portal message text
- ticket_nachricht.medium = 'portal'
- ticket_nachricht.versendet = 0

## Angebotsbestaetigung (Flow + Double Opt-In/AGB)
1) Kunde klickt "Angebot bestaetigen/ablehnen" und akzeptiert AGB (Checkbox).
2) System erstellt `ticket_offer_confirmation` mit action + comment und setzt doi_token.
3) E-Mail mit Double Opt-In-Link/Code (Variante B) wird versendet.
4) Kunde bestaetigt Double Opt-In; AGB-Version + Timestamp werden gespeichert.
5) Bei accept: Auftrag wird automatisch angelegt (angebot -> auftrag).
6) Ticketstatus und Kundenstatus werden aktualisiert; Logeintrag im Ticket.
7) E-Mail Bestaetigung an Kunde (optional).

## Auftragserstellung (technischer Hook)
- Bestehende Logik: `erpapi::WeiterfuehrenAngebotZuAuftrag($angebotId)` erzeugt Auftrag und setzt Angebot auf "beauftragt".
- Ablauf bei Double Opt-In-Bestaetigung:
  1) Lade Angebot by `angebot_id`.
  2) Abbruch wenn `angebot.auftragid` bereits gesetzt oder Status bereits "beauftragt".
  3) Aufruf `WeiterfuehrenAngebotZuAuftrag($angebotId)` -> liefert `order_id`.
  4) Schreibe `order_id` in `ticket_offer_confirmation`.
  5) Schreibe Portal- und Ticket-Logeintrag (Status + Kommentar).
  6) Optional: `SchnellFreigabe('auftrag', $order_id)` ist in der ERP-Funktion bereits enthalten.
- Fehlerfaelle (Beispiel):
  - Angebot nicht gefunden -> 404 + Ticketlog.
  - Angebot bereits beauftragt -> 409 + Ticketlog.
  - Auftragserstellung fehlgeschlagen -> 500 + Ticketlog.

## WordPress-Integration (robust, Plugin/Shortcode)
- WP-Plugin agiert als Proxy (serverseitiger Abruf von OpenXE).
- Shortcode Beispiel: [openxe_ticket_portal] (optional with token=...).
- Plugin-Settings:
  - OpenXE API Base URL
  - Shared Secret / API Key
  - Default Portal Mode (status/chat/offer)
  - AGB URL + Version (fuer Double Opt-In)
- API-Endpoints (Beispiel):
  - POST /portal/session (token + verifier -> session_token)
  - GET /portal/status (session_token)
  - GET /portal/messages (session_token)
- POST /portal/message (session_token, text)
- POST /portal/offer (session_token, action, comment, agb_version)
- POST /portal/offer/confirm (doi_token)
- POST /portal/token (staff, ticket_id -> new customer token)
- GET /portal/print (session_token, optional download=1)
- GET /portal/notifications (session_token)
- POST /portal/notification (session_token, selected/statuses)
- Statusaenderungen aus dem internen Ticket-Status koennen automatisch auf den Kundenstatus gemappt werden (falls nicht manuell ueberschrieben).

## Migrationen (Vorschlag, SQL-Skizze)
- ticket_portal_access
  - id int(11) auto_increment
  - ticket_id int(11) not null
  - token_hash varchar(255) not null
  - scope varchar(32) not null
  - verifier_type varchar(32) default null
  - verifier_hash varchar(255) default null
  - verifier_expires_at datetime default null
  - created_at datetime not null
  - expires_at datetime default null
  - revoked_at datetime default null
  - last_access_at datetime default null
  - last_access_ip varchar(64) default null
  - last_access_ua varchar(255) default null
  - primary key (id)
  - key ticket_id (ticket_id)
  - key token_hash (token_hash)
- ticket_portal_message
  - id int(11) auto_increment
  - ticket_id int(11) not null
  - author_type varchar(16) not null
  - author_id int(11) default null
  - text text not null
  - is_public tinyint(1) not null default 1
  - created_at datetime not null
  - source varchar(32) not null default 'portal'
  - mirrored_message_id int(11) default null
  - primary key (id)
  - key ticket_id (ticket_id)
  - key is_public (is_public)
- ticket_customer_status
  - ticket_id int(11) not null
  - status_key varchar(64) not null
  - status_label varchar(255) not null
  - updated_at datetime not null
  - updated_by int(11) default null
  - primary key (ticket_id)
- ticket_status_log
  - id int(11) auto_increment
  - ticket_id int(11) not null
  - status_from varchar(64) default null
  - status_to varchar(64) not null
  - changed_by int(11) default null
  - changed_at datetime not null
  - note_public text default null
  - note_internal text default null
  - primary key (id)
  - key ticket_id (ticket_id)
- ticket_offer_confirmation
  - id int(11) auto_increment
  - ticket_id int(11) not null
  - angebot_id int(11) not null
  - action varchar(16) not null
  - comment text default null
  - agb_version varchar(64) default null
  - agb_accepted_at datetime default null
  - doi_token_hash varchar(255) default null
  - doi_requested_at datetime default null
  - doi_confirmed_at datetime default null
  - created_at datetime not null
  - created_by_type varchar(16) not null
  - created_by_id int(11) default null
  - ip varchar(64) default null
  - user_agent varchar(255) default null
  - order_id int(11) default null
  - primary key (id)
  - key ticket_id (ticket_id)
  - key angebot_id (angebot_id)
- ticket_notification_pref
  - id int(11) auto_increment
  - ticket_id int(11) not null
  - customer_id int(11) not null
  - status_key varchar(64) not null
  - enabled tinyint(1) not null default 1
  - primary key (id)
  - key ticket_id (ticket_id)
  - key customer_id (customer_id)

## API Spezifikation (Draft)
- Auth
  - Portal nutzt session_token (kurzlebig) nach erfolgreicher Verifikation.
  - WP-Plugin nutzt API Key + HMAC (shared secret) fuer serverseitige Requests.
- Rate Limit (Portal)
  - 60 requests / 5 minutes pro IP, 10 failed verifications / 15 minutes.

### POST /portal/session
Request:
{
  "token": "public-token",
  "verifier_type": "email|plz|code",
  "verifier_value": "..."
}
Response:
{
  "session_token": "session-xyz",
  "expires_at": "2025-01-01T12:00:00Z"
}
Errors:
- 400 invalid_request
- 401 verification_failed
- 423 locked

### GET /portal/status
Request headers: Authorization: Bearer <session_token>
Response:
{
  "ticket_number": "202501010001",
  "status_key": "quality_check",
  "status_label": "Qualitaetspruefung",
  "updated_at": "2025-01-01T12:00:00Z",
  "public_note": "..."
}

### GET /portal/messages
Response:
{
  "messages": [
    {
      "id": 123,
      "author_type": "customer",
      "text": "Bitte um Update.",
      "created_at": "2025-01-01T12:00:00Z"
    }
  ]
}

### POST /portal/message
Request:
{
  "text": "Mein Kommentar"
}
Response:
{
  "id": 124,
  "mirrored_message_id": 9001
}

### POST /portal/offer
Request:
{
  "angebot_id": 555,
  "action": "accept|decline",
  "comment": "...",
  "agb_version": "2025-01"
}
Response:
{
  "status": "pending_doi",
  "doi_sent": true
}

### POST /portal/offer/confirm
Request:
{
  "doi_token": "doi-abc"
}
Response:
{
  "status": "confirmed",
  "order_id": 777
}

### POST /portal/token (staff)
Request:
{
  "ticket_id": 123
}
Response:
{
  "token": "public-token"
}

## API/Endpoints (Vorschlag)
- GET /portal/ticket/{token}: Portal-Ansicht fuer Kunden
- POST /portal/ticket/{token}/comment: Kundenkommentar
- POST /portal/ticket/{token}/offer: Angebot bestaetigen/ablehnen
- GET /staff/ticket/{id}: Mobile Update-Ansicht (Login)
- POST /staff/ticket/{id}/status: Statuswechsel + Notiz
- POST /staff/ticket/{id}/media: Bild/VTT hochladen

## Umsetzung in Phasen
1) MVP: Portal-Read + Kundenkommentar + Statusmapping + WP-Plugin (Shortcode).
2) Angebotsbestaetigung + Kundenbenachrichtigungen + Statuslog.
3) Medien-Doku, VTT, OCR/SN-Workflow.

## Offene Punkte (fuer Feinschliff)
- Exakte Definition der Kundenstati und der Texte.
- Angebotsbestaetigung: automatisches Erstellen eines Auftrags inkl. Dokumentationspflicht.
- Verifikation: bevorzugte Variante A oder B als Standard?
- Pflichtfelder im Kundenkommentar (z.B. Name/Email)?
