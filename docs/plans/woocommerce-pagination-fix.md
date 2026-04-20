# WooCommerce-Bestellimport: Pagination + `after`-Filter

**Issue:** [openxe-org/openxe#262](https://github.com/OpenXE-org/OpenXE/issues/262)
**Branch:** `fix/woocommerce-order-import-pagination`
**Base:** `development` @ `8bb13973`
**Typ:** Bugfix (stiller Datenverlust)

---

## 1. Ziel

Der WooCommerce-Shopimporter verliert Bestellungen, wenn mehr als 20 neue Aufträge
zwischen zwei Cron-Läufen eingehen. Dieser Fix macht den Bestellabruf vollständig
und verlustfrei, ohne neue Plugin-Abhängigkeiten und ohne Wechsel der API-Version
(bleibt auf `wc/v3`).

## 2. Scope

### IN-Scope

- Single-Order-Abruf mit `after`-Filter + `exclude`-Tupel-Cursor; Caller-Loop iteriert.
- `after`-Filter (ISO-8601) statt 800er-`include`-Hack
- Persistenz "letzter erfolgreicher Import-Timestamp" pro Shop
- Fallback-Logik für Erstlauf (kein Timestamp vorhanden)
- Transitions-Kompatibilität zum Legacy-Parameter `ab_nummer`

### OUT-of-Scope (eigene Issues/PRs)

- Batch-Endpoints fuer Stock-Sync (`products/batch`)
- Retry / Backoff fuer 429/5xx
- Composer-Migration des inline-WC-SDK
- Webhook-Support (#239)
- HPOS-Testmatrix
- UI-Reset-Button fuer den Import-Timestamp

## 3. Fix-Parameter (final)

| Parameter | Wert | Begruendung |
|---|---|---|
| Erstlauf-Fallback | **30 Tage** | Sinnvoller Mittelweg: holt historische Bestellungen, aber nicht unbegrenzt. UI-Override in Folge-PR moeglich. |
| Persistenz-Feld | `shopexport.einstellungen_json` → `felder.letzter_import_timestamp` | Bestehende Struktur nutzen, keine DB-Migration. |
| Timestamp-Format | ISO-8601 `Y-m-d\TH:i:s` (UTC) | Direkt an `after=` durchreichbar. |
| Caller-Cap (pre-existing) | `shopexport.maxmanuell`, default 100 | Der Batch-Cap liegt beim shopimport.php-Caller, nicht im Importer. |
| Cursor-Format | Tupel `(letzter_import_timestamp, letzter_import_order_id)` | Loest Same-Second-Kollisionen via after=ts-1 + exclude=[id]. |

## 4. Datei- und Funktions-Landkarte

Zielmodul: `www/pages/shopimporter_woocommerce.php`

| Funktion | Zeilen | Betroffen? | Aenderung |
|---|---|---|---|
| `ImportGetAuftraegeAnzahl` | ~80–135 | ja | `include`-Hack raus, `after`-Filter rein, nur noch Count via `X-WP-Total` |
| `ImportGetAuftrag` | ~121–185 | ja | Single-Order-Query mit after-Filter; Caller-Loop in shopimport.php iteriert |
| `parseOrder` | ~222–305 | nein | unveraendert (liefert weiterhin pro Order) |
| `CatchRemoteCommand('data')` | mehrfach | mittelbar | stellt `letzter_import_timestamp` aus `einstellungen_json` bereit |
| `getKonfig` | ~802–837 | nein | nicht anfassen (separates Issue #224) |
| Inline-Client (`WCClient`, `WCHttpClient`, `WCResponse`) | 1021–2370 | ggf. ja | Header-Accessor ergaenzen falls nicht vorhanden |

## 5. Implementierungsschritte

### Schritt 1 — Header-Durchreichung im inline-Client pruefen

**Aufgabe:** Klaeren, ob `WCResponse` die HTTP-Antwort-Header (`X-WP-Total`,
`X-WP-TotalPages`, `Link`) bereits als Array liefert oder ob der inline-Client
angepasst werden muss.

**Deep-Read-Ziele (nur Lesen, kein Edit):**
- `WCHttpClient::processResponse()` / aequivalent (vermutlich ~Zeile 2140–2200)
- `WCResponse`-Constructor: werden Headers gespeichert?
- `curl_setopt`-Setup: ist `CURLOPT_HEADERFUNCTION` oder `CURLOPT_HEADER` aktiv?

**Entscheidungs-Gate:**
- Wenn Headers bereits im Response-Objekt: direkt weiter mit Schritt 3.
- Wenn nicht: Schritt 2 vor Schritt 3.

**Akzeptanzkriterium:** Klarer Plan, welche Client-Aenderung noetig ist (oder dass keine noetig ist).

### Schritt 2 — Headers exponieren (bedingt)

**Nur falls Schritt 1 ergibt, dass Headers nicht zur Businesslogik durchdringen.**

- `WCHttpClient::processResponse()`: Header-String in assoziatives Array parsen.
- `WCResponse` um `getHeaders()` / `getHeader(string $name)` erweitern.
- cURL-Setup: `CURLOPT_HEADERFUNCTION` registrieren, Headers in Sammelarray schreiben.
- Case-insensitive Lookup (`strtolower`-normalisiert speichern), da WP Headers teils
  `x-wp-totalpages` vs. `X-WP-TotalPages` sendet.

**Akzeptanzkriterium:** `$response->getHeader('x-wp-totalpages')` liefert eine Zahl als
String (z.B. `"3"`).

**Risiko:** Client-Klassen werden auch an anderen Stellen instanziiert (theoretisch).
→ Mit `grep` verifizieren, dass `WCClient`/`WCResponse` nur in
`shopimporter_woocommerce.php` verwendet werden (keine Referenzen in
anderen `www/pages/*.php`-Dateien).

### Schritt 3 — Timestamp-Persistenz

**3a — Lesen:**
- In `getKonfig()` oder `CatchRemoteCommand('data')` den Wert
  `$felder['letzter_import_timestamp']` auslesen.
- Fallback: `date('Y-m-d\TH:i:s', strtotime('-30 days'))` wenn leer/null.
- Auf Klassen-Property `$this->lastImportTimestamp` ablegen.

**3b — Schreiben:**
- Nach erfolgreichem Lauf (am Ende von `ImportGetAuftrag`): den Timestamp der
  **zuletzt verarbeiteten Bestellung** (nicht `now()`) in `einstellungen_json` zurueckschreiben.
- Grund: wenn der Lauf bei Order #n+3 abbricht, beim naechsten Lauf mit Order #n+3
  weiterarbeiten, nicht mit `now()` (→ Datenverlust).
- SQL: `UPDATE shopexport SET einstellungen_json = :json WHERE id = :id`.
- Muss ueber den in `DatabaseService` vorhandenen Mechanismus laufen (named params,
  Prepared Statement — siehe `CLAUDE.md`-Projektregel).

**3c — Atomic Update:**
- Nur bei erfolgreichem Import-Ende Timestamp persistieren.
- Bei Exception mitten im Lauf: Timestamp NICHT auf den Absturzpunkt schreiben
  — besser: pro erfolgreich verarbeiteter Order einzeln fortschreiben (Progress),
  sodass ein Absturz nur den aktuellen, nicht alle bisherigen, verliert.

**Akzeptanzkriterium:**
```
SELECT einstellungen_json FROM shopexport WHERE id = <shopid>
→ enthaelt 'letzter_import_timestamp': '2026-04-20T12:34:56'
```

### Schritt 4 — Refactor `ImportGetAuftraegeAnzahl` (Count-Funktion)

**Alt (Zeile 80–135):**
- Query: `orders?status=…&include=<800 IDs>&per_page=100`.
- Liest `count($response)` als Return.

**Neu:**
- Query: `orders?status[]=<s1>&status[]=<s2>&after=<lastImportTs>&per_page=1`.
- Return: `(int) $response->getHeader('x-wp-total')`.
- `per_page=1` reicht — wir brauchen nur den Count-Header, nicht die Daten.

**Akzeptanzkriterium:**
- Bei 0 neuen Orders: liefert 0.
- Bei 250 neuen Orders: liefert 250 (nicht 100).

### Schritt 5 — Refactor `ImportGetAuftrag` (Import-Funktion)

**Alt:** Query mit `include`-Liste, Iteration ueber bis zu 20 Orders, kein Cursor.

**Neu — Single-Order-Pseudocode:**

```
afterTs = ts-1s  (falls ts gesetzt)
query   = orders?after=<afterTs>&per_page=1&orderby=date&order=asc
          (+ exclude=[last_id] wenn last_id bekannt)

if response is empty:
    return []

wcOrder = response[0]
order   = parseOrder(wcOrder)

persistLastImportCursor(order.date_created_gmt, order.id)

return [{ id: order.auftrag, sessionid: '', logdatei: '', warenkorb: ... }]
```

**Wichtig:**
- `orderby=date` + `order=asc` garantiert, dass die **aelteste** neue Order zuerst kommt.
  Dadurch kann der Progress-Timestamp monoton wachsen.
- `date_created_gmt` als Referenz (nicht `date_created` — Zeitzonen-Fallen vermeiden).
- Volume-Handling liegt beim Caller (shopimport.php), nicht im Importer.

**Akzeptanzkriterium:**
- Pro Call: exakt 1 Order zurueck (oder leer).
- Bei >100 neuen Orders: Caller-Schleife (maxmanuell-gekappt) iteriert bis Ende.

### Schritt 5a — Caller-Kontrakt

Der Caller in shopimport.php:1304-1306 ruft pro Order-Import-Iteration:
- `ImportGetAuftraegeAnzahl()` einmal fuer Count (mit maxmanuell-Cap)
- `ImportGetAuftrag()` in for-Schleife, verarbeitet `$result[0]`

Der Importer muss diesen Kontrakt einhalten: pro Call max. 1 Order.
Der after-Filter sorgt dafuer, dass jede Iteration die naechste Order
holt. Ein Crash zwischen `RemoteGetAuftrag()` und `shopimport_auftraege`-
Insert verliert max. diese eine Order.

### Schritt 5b — Migration-Helper

Um die ab_nummer → timestamp Migration auch im Count-Pfad auszufuehren
(shopimport.php ruft ImportGetAuftraegeAnzahl() BEFORE ImportGetAuftrag()):

- Extraktion in eine private Methode `migrateAbNummerIfNeeded()`.
- Aufruf am Anfang von beiden ImportGetAuftraegeAnzahl() und ImportGetAuftrag().
- Idempotent durch `$lastImportTimestampIsFallback`-Check — nach einmaliger Migration
  keine weiteren Reads.

### Schritt 6 — Transitions-Kompatibilitaet `ab_nummer`

**Ist-Zustand:** `CatchRemoteCommand('data')` liefert u.a. `ab_nummer` — die naechste
Bestell-Nummer, ab der gelesen werden soll (Legacy-Cursor).

**Uebergangsregel:**
- Wenn `letzter_import_timestamp` gesetzt → `after`-Filter nutzen, `ab_nummer` ignorieren.
- Wenn `letzter_import_timestamp` leer aber `ab_nummer` > 0 → einmalig `ab_nummer` in
  einen Timestamp uebersetzen (Query `GET orders/{ab_nummer}` → `date_created_gmt` lesen),
  als `letzter_import_timestamp` persistieren, ab dann `after`-Logik.
- Wenn beides leer → 30-Tage-Fallback (Schritt 3a).

**Akzeptanzkriterium:** Shop, der bisher mit `ab_nummer` lief, importiert nach
Update **keine Duplikate** und **keine Luecken**.

### Schritt 7 — `include`-Hack entfernen

- Loeschen:
  - Zeile ~99–113 (Count-Pfad `include`-Aufbau)
  - Zeile ~153–167 (Import-Pfad `include`-Aufbau)
  - Die beiden selbstkritischen Code-Kommentare *"fake"-Filter*
- Keine Ersatz-Struktur — `after` uebernimmt den Job komplett.

### Schritt 8 — Cleanup & Commits

**Pre-Commit-Checks:**
- `php -l www/pages/shopimporter_woocommerce.php`
- Trailing Whitespace raus (Subagent-Reste)
- CRLF-Warnungen ignorieren (autocrlf-Artefakte per Projekt-Regel)

**Commit-Struktur (atomar, auf `fix/woocommerce-order-import-pagination`):**

1. `fix(woocommerce): expose response headers from inline WC client`
   → nur Client-Aenderung (bedingt; entfaellt wenn Schritt 1 Headers schon freigibt)

2. `fix(woocommerce): persist last-import timestamp in shopexport config`
   → Timestamp-Read/Write + 30-Tage-Fallback + Progress-Update pro Order

3. `fix(woocommerce): use after-filter and pagination for order import`
   → Kernaenderung an `ImportGetAuftrag` und `ImportGetAuftraegeAnzahl`

4. `refactor(woocommerce): remove 800-id include hack`
   → Aufraeumen toter Code + Kommentare

5. `docs: add plan for woocommerce pagination fix`
   → Diese Datei (`docs/plans/woocommerce-pagination-fix.md`)

**PR-Ziel:** `openxe-org/openxe:master` (Upstream hat kein `development`).
**PR-Body:** Verweis auf Issue #262 + knappe Zusammenfassung der 4 Commits.

## 6. Test-Plan (Integration gegen `192.168.0.143`)

Keine Unit-Tests (OpenXE hat keine Test-Suite fuer Shopimporter).
Manuelle Integrationstests mit seeded Test-Orders.

### Setup
- WP-Backend: `admin:password`
- WC-REST: `consumer_key`/`consumer_secret` generieren (Admin → WooCommerce → Einstellungen → Erweitert → REST-API)
- OpenXE-Shop-Konfiguration: bestehende Testinstanz wiederverwenden

### Test-Matrix

| # | Szenario | Start-State | Aktion | Erwartung |
|---|---|---|---|---|
| T1 | Frischinstall, keine Orders | `letzter_import_timestamp` leer | Lauf starten | 0 Orders, Timestamp bleibt leer |
| T2 | Frischinstall, 10 Orders <30 Tage alt | `letzter_import_timestamp` leer | Lauf starten | 10 Orders importiert, Timestamp = neueste Order |
| T3 | Frischinstall, 10 Orders >30 Tage alt | `letzter_import_timestamp` leer | Lauf starten | 0 Orders (Fallback-Schwelle), Timestamp leer |
| T4 | Standardlauf, 30 neue Orders | Timestamp gesetzt | Lauf starten | 30 Orders, Timestamp fortgeschrieben |
| T5 | Spike, 150 neue Orders | Timestamp gesetzt | Lauf starten | 150 Orders, Caller-Schleife durch maxmanuell auf 100 gekappt; Folgelauf holt Rest |
| T6 | Rueckstand, >100 neue Orders | Timestamp gesetzt | Lauf 1 starten | 100 Orders (maxmanuell-Cap), Cursor fortgeschrieben |
| T6b | Rueckstand Teil 2 | nach T6 | Lauf 2 starten | restliche Orders bis maxmanuell-Cap, Timestamp final |
| T7 | Transition, Shop mit `ab_nummer`=12345, Timestamp leer | `ab_nummer=12345` | Lauf starten | `ab_nummer` in Timestamp uebersetzt, keine Duplikate |
| T8 | Abbruch mitten im Lauf | Exception nach Fetch, vor INSERT | neuer Lauf | bei Abbruch zwischen Fetch und Insert max. 1 Order verloren; ab naechster Order fortgesetzt |
| T9 | Idempotenz | T4 erfolgreich gelaufen | T4 nochmal starten | 0 neue Orders, keine Duplikate |
| T10 | URL-Laenge (Regression) | 800 alte Orders existieren | Lauf starten | URL bleibt kurz (<2 KB), keine `include`-Liste mehr |

### Mess-Artefakte (vor/nach)

- Anzahl HTTP-Requests pro Lauf (via Apache-Log auf Testinstanz)
- Laenge der URL im `GET /orders`-Request
- Laufzeit pro Lauf
- Anzahl importierte Orders pro Lauf

## 7. Rollout & Rueckwaerts-Kompatibilitaet

### Migration beim Update
- Keine DB-Migration noetig.
- Beim ersten Lauf nach Update:
  - Wenn `ab_nummer` gesetzt → einmalige Uebersetzung (Schritt 6).
  - Wenn nichts gesetzt → 30-Tage-Fallback.
- Kein Breaking Change fuer bestehende Shops.

### Rollback-Szenario
- Rueckkehr zum alten Verhalten: Revert der 4 Commits aus Schritt 8.
- `letzter_import_timestamp` in `einstellungen_json` stoert alte Version nicht
  (unbekannte Keys werden ignoriert).

### Kompatibilitaet mit anderen Modulen
- `class.remote.php` → nicht beruehrt.
- Andere `shopimporter_*`-Module → nicht beruehrt (nur WooCommerce-spezifisch).
- `class.erpapi.php` → nicht beruehrt.

## 8. Risiken & Mitigations

| Risiko | Wahrscheinlichkeit | Auswirkung | Mitigation |
|---|---|---|---|
| Inline-Client liefert Headers nicht durch | mittel | hoch (blockiert Schritt 4/5) | Schritt 1 als Entscheidungs-Gate, Schritt 2 bedingter Vorarbeits-Schritt |
| Shop-Timezone vs. `after`-Zeitzone | mittel | mittel (Off-by-one-Tag) | `date_created_gmt` und `after` beide in UTC, explizit testen (T4, T9) |
| Kunden ohne Timestamp + >30 Tage alte neue Orders | niedrig | mittel | Dokumentiert in PR-Body, UI-Override in Folge-PR |
| `orderby=date` Ordering nicht deterministisch bei gleichem Timestamp | niedrig | niedrig | Zusaetzlich `orderby=date&order=asc` und WC liefert stabile Sekundaersortierung nach ID |
| Andere Shopimporter erben Basisklasse-Struktur | niedrig | niedrig | Nur `shopimporter_woocommerce.php` anfassen, `ShopimporterBase` nicht anruehren |
| URL-Laengenlimits der WAF beim `status[]`-Array | sehr niedrig | niedrig | Typisch 2-3 Status-Werte, URL bleibt kurz |
| Caller-Kontrakt-Abweichung (1 vs. n Orders) | niedrig | hoch | Per Design Single-Order-Return; Caller-Loop fuer Volume-Handling |
| Timestamp-Kollision bei identischem date_created_gmt | niedrig | mittel | Adressiert durch Tupel-Cursor (ts, id) + exclude-Parameter |

## 9. Definition of Done

- [ ] Schritt 1 abgeschlossen, Entscheidung fuer/gegen Schritt 2 dokumentiert im PR-Body
- [ ] Alle geplanten Commits auf `fix/woocommerce-order-import-pagination`
- [ ] `php -l` ohne Fehler
- [ ] Testmatrix T1–T10 auf `192.168.0.143` durchgelaufen, Ergebnisse im PR-Body
- [ ] Issue #262 im PR referenziert (`Fixes #262`)
- [ ] PR gegen `openxe-org/openxe:master` eroeffnet
- [ ] Diese Plan-Datei mitversioniert im Fix-Branch

## 10. Referenzen

- Issue: https://github.com/OpenXE-org/OpenXE/issues/262
- WC REST API Docs (Orders): https://woocommerce.github.io/woocommerce-rest-api-docs/#orders
- WP-REST-API Pagination: https://developer.wordpress.org/rest-api/using-the-rest-api/pagination/
- Parallele Issues:
  - #239 Webhook Support (push statt poll)
  - #224 JSON-Error in `getKonfig`
