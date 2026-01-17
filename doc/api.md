# OpenXE API Reference

This document summarizes the API surface exposed by `www/api/index.php` so developers can call it without digging into the source. All paths below are relative to the API entry point (typically `https://<host>/api` or `https://<host>/www/api` depending on your web server rewrite rules).

## Authentication

- HTTP Digest auth against table `api_account`  
  - Username: `remotedomain`  
  - Password: `initkey`  
- Most routes also require a permission flag tied to the API account (see permission name in parentheses below).  
- All calls must include `Authorization: Digest ...`.

### Example

```bash
curl -u '<remotedomain>:<initkey>' \
  -H 'Accept: application/json' \
  'https://<host>/api/v1/adressen?limit=20'
```

## Common responses

- Success: JSON body matching the controller logic (varies by endpoint).  
- Errors: JSON with an `error` object when thrown via API error handling. HTTP status codes follow the `Response` object in controllers (400/401/403/404/500).

## Documentation endpoints

- `GET|POST|PUT|DELETE /` — Start page.  
- `GET|POST|PUT|DELETE /v1` — Same as root.  
- `GET|POST|PUT|DELETE /docs.html` — Static docs (requires API auth if routed through PHP).  
- `GET|POST|PUT|DELETE /assets/{assetfile}` — Doc assets (permission: `handle_assets`).

## Legacy catch-all

- `GET|POST /{action}` — Legacy controller dispatch; path segment is mapped to a legacy action.  
- `POST /v1/gobnavconnect[/]` — GobNav bridge example (`handle_navision`).  
- `GET /v1/mobileapi/dashboard` — Mobile dashboard (`mobile_app_communication`).

### Legacy action list (called via `/{ActionName}`)

All of these are reached with `POST` (or `GET` if the client sends GET) to `/api/{ActionName}`; the controller maps to `Api{ActionName}` in `www/pages/api.php`. Permissions are checked via the API account (`api_permission` records).

- Time & punch: `ServerTimeGet`, `BenutzerGetRFID`, `BenutzerList`, `StechuhrStatusGet`, `StechuhrStatusSet`, `StechuhrSummary`
- Addresses & contacts: `AdresseCreate`, `AdresseEdit`, `AdresseGet`, `LieferadresseCreate`, `LieferadresseEdit`, `AdresseKontaktCreate`, `AdresseKontaktEdit`, `AdresseKontaktGet`, `AdresseKontaktList`, `AdresseListeGet`, `AdresseAccountsGet`, `AdresseGruppenList`
- Address subscriptions: `AdresseAboGruppeCreate`, `AdresseAboGruppeEdit`, `AdresseAboGruppeGet`, `AdresseAboGruppeList`, `AdresseAboArtikelCreate`, `AdresseAboArtikelEdit`, `AdresseAboArtikelGet`, `AdresseAboArtikelList`
- Contacts: `AnsprechpartnerCreate`, `AnsprechpartnerEdit`
- Accounts/auth: `AccountList`, `AccountLogin`, `AccountCreate` (alias of `ApiAdresseAccountCreate`), `AccountEdit` (alias of `ApiAdresseAccountEdit`)
- Articles: `ArtikelList`, `ArtikelCreate`, `ArtikelEdit`, `ArtikelGet`, `ArtikelStueckliste`, `ArtikelStuecklisteCreate`, `ArtikelStuecklisteEdit`, `ArtikelStuecklisteList`, `ArtikelkontingenteGet`, `ArtikelkategorienList`
- Orders/quotes/credits/delivery/invoice/returns: `AuftragCreate`, `AuftragEdit`, `AuftragGet`, `AuftragFreigabe`, `AuftragAbschliessen`, `AuftragArchivieren`, `AuftragVersenden`, `AngebotCreate`, `AngebotEdit`, `AngebotGet`, `AngebotFreigabe`, `AngebotArchivieren`, `AngebotVersenden`, `GutschriftCreate`, `GutschriftEdit`, `GutschriftGet`, `GutschriftFreigabe`, `GutschriftArchivieren`, `GutschriftVersenden`, `LieferscheinCreate`, `LieferscheinEdit`, `LieferscheinGet`, `LieferscheinFreigabe`, `LieferscheinArchivieren`, `LieferscheinVersenden`, `RetoureCreate`, `RetoureEdit`, `RetoureGet`, `RechnungCreate`, `RechnungEdit`, `RechnungGet`, `RechnungFreigabe`, `RechnungArchivieren`, `RechnungVersendetMarkieren`, `RechnungAlsBezahltMarkieren`, `RechnungVersenden`, `BestellungCreate`, `BestellungEdit`, `BestellungGet`, `BestellungFreigabe`
- Document transitions: `WeiterfuehrenAuftragZuRechnung`, `WeiterfuehrenRechnungZuGutschrift`, `AngebotZuAuftrag`, `AuftragZuRechnung`
- Document exports: `BelegeList`, `BelegPDF`, `BelegPDFHeader`, `BerichteGet`
- File handling: `DateiList`, `DateiVorschau`, `DateiDownload`, `DateiHeader`
- Pricing/mapping: `PreiseEdit`, `MappingGet`, `MappingSet`, `ExportVorlageGet`
- Users/sessions: `BenutzerCreate`, `BenutzerEdit`, `BenutzerGet`, `SessionStart`, `SessionClose`
- Groups: `GruppeCreate`, `GruppeEdit`, `GruppeGet`, `GruppenList`
- Projects: `ProjektListe`, `ProjektGet`, `ProjektCreate`, `ProjektEdit`
- Time tracking: `ZeiterfassungGet`, `ZeiterfassungCreate`, `ZeiterfassungEdit`, `ZeiterfassungDelete`
- Misc: `RechnungVersendetMarkieren`, `ReisekostenVersenden`, `Etikettendrucker`, `Custom`, `ApiXMLTest`, legacy `shopimages`

## OpenTrans

- Dispatch notifications (`handle_opentrans`):  
  - `GET /opentrans/dispatchnotification/{id}`  
  - `GET /opentrans/dispatchnotification/orderid/{orderid}`  
  - `GET /opentrans/dispatchnotification/ordernumber/{ordernumber}`  
  - `GET /opentrans/dispatchnotification/extorder/{extorder}`  
  - `PUT` variants for the same four paths  
- Orders (`handle_opentrans`):  
  - `GET /opentrans/order/{id}`  
  - `GET /opentrans/order/ordernumber/{ordernumber}`  
  - `GET /opentrans/order/extorder/{extorder}`  
  - `POST /opentrans/order`  
  - `DELETE /opentrans/order/{id|ordernumber|extorder}`  
- Invoices (`handle_opentrans`):  
  - `GET /opentrans/invoice/{id}`  
  - `GET /opentrans/invoice/orderid/{orderid}`  
  - `GET /opentrans/invoice/ordernumber/{ordernumber}`  
  - `GET /opentrans/invoice/extorder/{extorder}`

## Shopimport (shop connector) — permission `communicate_with_shop`

- `POST /shopimport/auth`  
- `POST /shopimport/syncstorage/{articlenumber}`  
- `POST /shopimport/articletoxentral/{articlenumber}`  
- `POST /shopimport/articletoshop/{articlenumber}`  
- `POST /shopimport/ordertoxentral/{ordernumber}`  
- `GET /shopimport/articlesyncstate`  
- `GET /shopimport/statistics`  
- `GET /shopimport/modulelinks`  
- `POST /shopimport/disconnect`  
- `POST /shopimport/reconnect`  
- `GET /shopimport/status`  
- `POST /shopimport/refund`

## REST v1 resources

All routes below require digest auth and the listed permission.

### Subscriptions
- `POST /v1/aboartikel` (create_subscription)  
- `GET /v1/aboartikel` (list_subscriptions)  
- `GET /v1/aboartikel/{id}` (view_subscription)  
- `PUT /v1/aboartikel/{id}` (edit_subscription)  
- `DELETE /v1/aboartikel/{id}` (delete_subscription)

### Subscription groups
- `POST /v1/abogruppen` (create_subscription_group)  
- `GET /v1/abogruppen` (list_subscription_groups)  
- `GET /v1/abogruppen/{id}` (view_subscription_group)  
- `PUT /v1/abogruppen/{id}` (edit_subscription_group)

### Addresses
- `POST /v1/adressen` (create_address)  
- `GET /v1/adressen` (list_addresses)  
- `GET /v1/adressen/{id}` (view_address)  
- `PUT /v1/adressen/{id}` (edit_address)  
- Readonly v2: `GET /v2/adressen`, `GET /v2/adressen/{id}` (list_addresses/view_address)

### Address types
- `POST /v1/adresstyp` (create_address_type)  
- `GET /v1/adresstyp` (list_address_types)  
- `GET /v1/adresstyp/{id}` (view_address_type)  
- `PUT /v1/adresstyp/{id}` (edit_address_type)

### Articles
- `GET /v1/artikel` (list_articles)  
- `GET /v1/artikel/{id}` (view_article)

### Properties and values
- Properties: `GET /v1/eigenschaften`, `GET|PUT|DELETE /v1/eigenschaften/{id}`, `POST /v1/eigenschaften` (list/view/edit/delete/create_property)  
- Property values: `GET /v1/eigenschaftenwerte`, `GET|PUT|DELETE /v1/eigenschaftenwerte/{id}`, `POST /v1/eigenschaftenwerte` (list/view/edit/delete/create_property_value)

### Documents (`/v1/belege`)
- Offers: `GET /v1/belege/angebote`, `GET /v1/belege/angebote/{id}` (list_quotes/view_quote)  
- Sales orders: `GET /v1/belege/auftraege`, `GET /v1/belege/auftraege/{id}` (list_orders/view_order)  
- Delivery notes: `GET /v1/belege/lieferscheine`, `GET /v1/belege/lieferscheine/{id}` (list_delivery_notes/view_delivery_note)  
- Invoices: `GET /v1/belege/rechnungen`, `GET /v1/belege/rechnungen/{id}`, `DELETE /v1/belege/rechnungen/{id}` (list/view/delete_invoice)  
- Credit notes: `GET /v1/belege/gutschriften`, `GET /v1/belege/gutschriften/{id}` (list/view_credit_memo)  
- Placeholder: `GET /v1/belege`

### Reports
- `GET /v1/reports/{id}/download` (view_report)

### Files
- `POST /v1/dateien` (create_file)  
- `GET /v1/dateien` (list_files)  
- `GET /v1/dateien/{id}` (view_file)  
- `GET /v1/dateien/{id}/download` (view_file)  
- `GET /v1/dateien/{id}/base64` (view_file)

### DocScan
- `POST /v1/docscan` (create_scanned_document)  
- `GET /v1/docscan` (list_scanned_documents)  
- `GET /v1/docscan/{id}` (view_scanned_document)

### Article categories
- `POST /v1/artikelkategorien` (create_article_category)  
- `GET /v1/artikelkategorien` (list_article_categories)  
- `GET /v1/artikelkategorien/{id}` (view_article_category)  
- `PUT /v1/artikelkategorien/{id}` (edit_article_category)

### Groups
- `POST /v1/gruppen` (create_group)  
- `GET /v1/gruppen` (list_groups)  
- `GET /v1/gruppen/{id}` (view_group)  
- `PUT /v1/gruppen/{id}` (edit_group)

### CRM documents
- `POST /v1/crmdokumente` (create_crm_document)  
- `GET /v1/crmdokumente` (list_crm_documents)  
- `GET /v1/crmdokumente/{id}` (view_crm_document)  
- `PUT /v1/crmdokumente/{id}` (edit_crm_document)  
- `DELETE /v1/crmdokumente/{id}` (delete_crm_document)

### Countries
- `POST /v1/laender` (create_country)  
- `GET /v1/laender` (list_countries)  
- `GET /v1/laender/{id}` (view_country)  
- `PUT /v1/laender/{id}` (edit_country)

### Storage
- `GET /v1/lagercharge` (view_storage_batch)  
- `GET /v1/lagermhd` (view_storage_best_before)

### Delivery addresses
- `POST /v1/lieferadressen` (create_delivery_address)  
- `GET /v1/lieferadressen` (list_delivery_addresses)  
- `GET /v1/lieferadressen/{id}` (view_delivery_address)  
- `PUT /v1/lieferadressen/{id}` (edit_delivery_address)  
- `DELETE /v1/lieferadressen/{id}` (delete_delivery_address)

### Tax rates
- `POST /v1/steuersaetze` (create_tax_rate)  
- `GET /v1/steuersaetze` (list_tax_rates)  
- `GET /v1/steuersaetze/{id}` (view_tax_rate)  
- `PUT /v1/steuersaetze/{id}` (edit_tax_rate)

### Shipping methods
- `POST /v1/versandarten` (create_shipping_method)  
- `GET /v1/versandarten` (list_shipping_methods)  
- `GET /v1/versandarten/{id}` (view_shipping_method)  
- `PUT /v1/versandarten/{id}` (edit_shipping_method)

### Resubmissions (tasks)
- `POST /v1/wiedervorlagen` (create_resubmission)  
- `GET /v1/wiedervorlagen` (list_resubmissions)  
- `GET /v1/wiedervorlagen/{id}` (view_resubmission)  
- `PUT /v1/wiedervorlagen/{id}` (edit_resubmission)

### Payment methods
- `POST /v1/zahlungsweisen` (create_payment_method)  
- `GET /v1/zahlungsweisen` (list_payment_methods)  
- `GET /v1/zahlungsweisen/{id}` (view_payment_method)  
- `PUT /v1/zahlungsweisen/{id}` (edit_payment_method)

### Tracking numbers
- `POST /v1/trackingnummern` (create_tracking_number)  
- `GET /v1/trackingnummern` (list_tracking_numbers)  
- `GET /v1/trackingnummern/{id}` (view_tracking_number)  
- `PUT /v1/trackingnummern/{id}` (edit_tracking_number)

## Parameters and path notes

- Path parameters use regex constraints (e.g. `{id:\d+}`, `{ordernumber:\w+}`, `{articlenumber:.+}`).  
- Query parameters for filtering, sorting, paging depend on each controller implementation (consult controller code in `classes/Modules/Api/Controller/Version1`).  
- Requests and responses are JSON unless a controller explicitly returns files (e.g., `/dateien/.../download` or `/reports/{id}/download`).

## Testing checklist for consumers

- Verify digest auth with valid `api_account` credentials.  
- Ensure the API account has the permissions referenced above.  
- Confirm correct base path (`/api` vs `/www/api`) in your environment.  
- Use HTTPS in production; avoid sending credentials over HTTP.  
- For download endpoints, handle binary responses (`Content-Disposition: attachment`).
