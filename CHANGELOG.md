# Changelog

## [Unreleased]
- Added baseline project docs: `CODEX.md`, `MEMORY.md`, and `CHANGELOG.md`.
- Added ticket portal concept draft in `doc/ticket-portal-concept.md`.
- Updated `MEMORY.md` with portal scope, security, notifications, and media documentation notes.
- Extended status model and portal behavior requirements (auto-order on offer confirmation, chat history, local VTT/OCR).
- Added system settings toggles and double opt-in/AGB confirmation notes in portal concept.
- Documented portal message mirroring into `ticket_nachricht` and removed spam from customer status mapping.
- Expanded portal concept with detailed data model, Double Opt-In/AGB offer flow, and WP plugin proxy endpoints.
- Added SQL migration sketch and API draft for the portal.
- Documented technical hook for offer confirmation to create orders via ERP API.
- Added ticket portal tables, settings defaults, and portal endpoints in `www/pages/ticket.php`.
- Added portal settings UI in `www/pages/content/ticket_portal_settings.tpl`.
- Added portal token generation endpoint and schema updates for portal tables.
- Added portal print view with QR code and download option.
- Implemented QR generation in portal print and added printable/downloadable customer form.
- Added WordPress plugin/shortcode for the customer portal UI with AJAX proxy endpoints.
- Added portal settings download link + quick setup instructions for the WordPress plugin.
- Added staff portal status page and updated QR codes to link to it.
- Added portal status model configuration (labels + mapping) and notification preference endpoints/UI.
- Added status change email templates and auto-sync from internal ticket status to customer status (with notifications).
- Added ticket actions to create customers/offers and show tickets in the address CRM history.
