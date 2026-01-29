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
- Added ticket action button to generate and show portal tokens for testing.
- Added portal comment inputs to the ticket edit view for staff messages.
- Added portal URL setting and ticket buttons to copy portal link (with login data) or token.
- Replaced portal link generation with token-only links and added magic link support for one-time access.
- Added login rate limiting (failed attempts + lockout) and new portal settings for magic link TTL and lockout behavior.
- Extended WordPress portal to support magic links and automatic verifier selection.
- Added schema fields for portal access lockout tracking.
- Added shared secret support between WordPress and OpenXE portal endpoints.
- Added WordPress rate limiting for portal AJAX proxies and enforced HTTPS base URLs.
- Improved portal UI error handling for failed status/message/notification requests.
- Added portal config fallback via data attribute to avoid missing inline config issues.
- Added ticket number login support in portal (token still supported for compatibility).
- Added shared secret generator and copy buttons in portal settings.
- Added debug logging for portal requests in WordPress and OpenXE (opt-in).
- Added log viewers in OpenXE portal settings and WordPress plugin settings.
- Whitelisted ticket portal endpoints to allow public access without OpenXE login.
- Added OpenXE plugin download authentication via shared secret and allowed public access for the download endpoint.
- Added WordPress plugin update button (pull from OpenXE) and settings link in the plugin list.
- Silenced WordPress updater output to prevent headers-already-sent warnings after updates.
- Updated the OpenXE-to-WordPress update flow to overwrite the existing plugin directory instead of installing a new copy.
- Store the update extraction under `wp-content/upgrade` to avoid directory listing failures.

## [2025-12-28] - Ticket Portal MVP Release

### Added - Offer Workflow & Double Opt-In
- Implemented `portal_offers` endpoint to retrieve open offers for ticket address
- Added `portal_offer` endpoint for customer accept/decline with DOI flow
- Added `portal_offer_confirm` endpoint for email-based Double Opt-In confirmation
- Created `ticket_offer_confirmation` table with audit trail (IP, User-Agent, timestamps)
- Integrated offer display and selection in WordPress portal (`renderOffers()` in portal.js)
- Added Avant-Garde UI styling for offer cards with animations
- Implemented secure DOI email workflow with token expiry (configurable TTL)
- Added AGB version tracking and confirmation in offer acceptance flow

### Added - Media Upload (Staff & Customer)
- Created `ticket_repair_media` database table in struktur.sql
- Implemented `ticket_portal_staff_upload()` backend method with:
  - MIME type validation (finfo)
  - File size limits (10MB max)
  - SHA-256 hash-based storage (prevents directory traversal)
  - Extension whitelist (JPG, PNG, WebP, PDF)
  - `is_public` visibility toggle
- Added `ticket_portal_staff_download()` for secure staff file access
- Added `ticket_portal_staff_media_delete()` for media management
- Implemented `ticket_portal_media()` customer API (public files only)
- Implemented `ticket_portal_media_download()` with binary streaming
- Added media upload form in `ticket_portal_staff.tpl`
- Integrated media display in staff portal (table with preview & delete)
- Added customer-facing media section in WordPress portal
- Implemented `loadMedia()`, `renderMedia()` and blob download in portal.js
- Created binary proxy handler in WordPress plugin for secure media delivery

### Added - Flexible Status Management
- Implemented custom status management UI in `ticket_portal_settings.tpl`
- Added ability to define custom customer status keys and labels
- Extended `portalGetStatusLabels()` to support custom statuses
- Implemented JSON-based storage for custom statuses in firmendaten
- Added project-specific status mapping (Multiplexing):
  - `STATUS_MAP_PROJECTS` textarea for JSON configuration
  - `portalDefaultCustomerStatusKey($status, $projectId)` parameter
  - `portalHandleInternalStatusChange()` reads ticket project ID
  - Automatic fallback to standard mapping for unmapped projects
- Updated `portalCustomerStatusOptions()` to return all available statuses
- Added dynamic status mapping save/load in portal settings

### Added - User Experience Enhancements
- Implemented device-native Text-to-Speech (TTS):
  - "Vorlesen" button in each message
  - Uses window.speechSynthesis API (no installation required)
  - Styled with `.oxp-btn-tts` class
- Configured email-only notifications (user preference)
- Enhanced frontend with Avant-Garde UI principles:
  - Glassmorphism effects
  - Vibrant color gradients
  - Smooth micro-animations
  - Responsive card layouts

### Added - Security Enhancements
- Implemented SHA-256 token hashing for all portal tokens
- Added rate limiting in WordPress plugin (60 req/min per IP/endpoint)
- Integrated shared secret validation between WordPress ↔ OpenXE
- Added malware protection for uploads:
  - MIME type sniffing
  - Hash-based file storage
  - Extension validation
- Implemented session validation for all sensitive endpoints
- Added binary proxy to prevent direct file access
- Enhanced audit logging (all actions tracked with IP/UA)

### Added - Documentation (Complete)
- Created comprehensive User Guide (`doc/ticket-portal-user-guide.md`, 600+ lines):
  - Admin configuration with examples
  - Custom status management tutorials
  - Project-specific mapping guide
  - Media upload workflows
  - Troubleshooting section
- Created complete API Reference (`doc/ticket-portal-api-reference.md`, 1000+ lines):
  - All 11 backend endpoints documented
  - Full database schema (7 tables)
  - WordPress AJAX handler patterns
  - JavaScript API documentation
  - Hooks & Filter reference
  - Security guidelines
  - 40+ code examples
- Created Setup & Installation Guide (`doc/ticket-portal-setup.md`, 800+ lines):
  - System requirements
  - Step-by-step installation (OpenXE + WordPress)
  - Migration procedures
  - Troubleshooting guide
  - 50+ point production checklist
- Created Documentation Overview (`doc/ticket-portal-README.md`):
  - Documentation index
  - 3 learning paths (Admin, Developer, DevOps)
  - Feature matrix
  - Quick-start guide
- Updated WordPress Plugin README with installation & configuration

### Changed
- Updated status management to support multiple scenarios per project
- Enhanced portal settings UI with custom status tables
- Improved media handling with visibility toggles
- Optimized binary downloads through WordPress proxy

### Fixed
- Corrected project ID parameter in `portalDefaultCustomerStatusKey()`
- Fixed media listing to include `is_public` column
- Resolved binary streaming issues in WordPress AJAX handlers

### Technical Details
**Files Modified:** 8
- Backend: `www/pages/ticket.php` (+450 lines, 11 new methods)
- Database: `database/struktur.sql` (+12 lines, 1 table)
- Templates: `www/pages/content/ticket_portal_staff.tpl`, `ticket_portal_settings.tpl`
- WordPress: `wp-plugin/openxe-ticket-portal/openxe-ticket-portal.php` (+80 lines, 3 handlers)
- Frontend: `wp-plugin/openxe-ticket-portal/assets/portal.js` (+150 lines)
- Styles: `wp-plugin/openxe-ticket-portal/assets/portal.css` (+30 lines)

**New Backend Methods:**
- `ticket_portal_offers()`
- `ticket_portal_offer()`
- `ticket_portal_offer_confirm()`
- `ticket_portal_media()`
- `ticket_portal_media_download()`
- `ticket_portal_staff_upload()`
- `ticket_portal_staff_download()`
- `ticket_portal_staff_media_delete()`
- `portalGetStatusLabels()` (enhanced)
- `portalDefaultCustomerStatusKey()` (enhanced with project support)
- `portalHandleInternalStatusChange()` (enhanced)

**New Database Table:**
- `ticket_repair_media` (8 columns, 2 indices)

**Security Features Implemented:** 9
- Token Hashing (SHA-256)
- Rate Limiting (configurable)
- Shared Secret Authentication
- MIME Validation
- Hash-based Storage
- Size Limits (10MB)
- Session Validation
- Binary Proxy
- Audit Logging

**Documentation Stats:**
- 4 comprehensive documents
- ~2400 lines total
- 40+ PHP code examples
- 20+ JavaScript examples
- 15+ SQL queries
- 10+ Bash commands
