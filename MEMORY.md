# Memory

## Project context
- Repository: OpenXE
- Ticket system lives in `www/pages/ticket.php` and database tables `ticket`, `ticket_nachricht`, `ticket_header`, `ticket_regeln`, `ticket_vorlage`.
- Ticket status values are defined in `www/lib/class.erpapi.php` as: `neu`, `offen`, `warten_e`, `warten_kd`, `klaeren`, `abgeschlossen`, `spam`.

## Current goals
- Expand the ticket system with a customer-facing ticket status page.
- Allow customers to confirm offers or leave feedback; store feedback in tickets (no customer file uploads).
- Provide QR-based staff access to update ticket status and add internal data, including media docs (images, VTT) and SN recognition.
- Integrate the portal with WordPress via a plugin/shortcode (robust option preferred).
- Support customer security via token + verification, with multiple variants available.
- Default to notifications for all status changes (email only), with optional customer selection.
- Offer confirmation should auto-create an order and require audit documentation.
- Portal comments must remain visible as a chat-style history for customers.
- VTT/OCR must run locally (no cloud services).
- Offer confirmation may use double opt-in with AGB acceptance.
- Customer status uses "Paket eingegangen" instead of a generic "Eingegangen"; "Testdruck" is part of quality check.
- Portal features must be optional via system settings.
- Portal messages must be mirrored into `ticket_nachricht` (medium='portal').
- Offer confirmation should create an order and store AGB version + Double Opt-In audit trail.
- WP integration should use a plugin/shortcode acting as a server-side proxy to OpenXE.
- Next concept work: SQL migration sketch and API draft are documented in `doc/ticket-portal-concept.md`.
- Offer confirmation should call `erpapi::WeiterfuehrenAngebotZuAuftrag` and handle duplicate/failed creation cases.
- Ticket portal endpoints and settings live in `www/pages/ticket.php` with a simple settings UI in `www/pages/content/ticket_portal_settings.tpl`.
- New portal tables are added in `database/struktur.sql` and `upgrade/data/db_schema.json`.
- Staff can generate a new customer token via `index.php?module=ticket&action=portal_token&id=<ticket_id>`.
- Portal print view lives at `index.php?module=ticket&action=portal_print&session_token=...`, includes a QR code to the ticket edit URL, and supports optional `download=1`.
- WordPress plugin/shortcode lives at `wp-plugin/openxe-ticket-portal/` and proxies portal requests via `admin-ajax.php` (default verifier: PLZ).
- Portal settings include a plugin download link and a short setup guide.
- Staff portal status page lives at `index.php?module=ticket&action=portal_staff&id=...` and the QR code on the printout links to it.
- Portal settings also allow editing customer status labels and internal->customer status mapping.
- Notification preferences are stored per ticket in `ticket_notification_pref` and exposed via `portal_notifications` / `portal_notification` endpoints; the WP plugin renders a notification selection UI.
- Status change notification templates are configurable via `ticketportal_notify_subject` and `ticketportal_notify_body`.
- Internal ticket status changes auto-sync to customer status when no manual override is present, and can trigger notifications.
- Ticket edit includes actions to create customers/offers; tickets also appear in the address CRM history.

## Constraints and preferences
- Keep edits ASCII unless a file already uses non-ASCII.
- Avoid changes in `vendor/` unless explicitly requested.
