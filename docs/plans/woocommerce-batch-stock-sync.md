# WooCommerce Batch Stock Sync — Plan

Companion document to [GitHub Issue #263](https://github.com/OpenXE-org/OpenXE/issues/263)
and the `feature/woocommerce-batch-stock-sync` branch.

---

## §1 Ziel

`ImportSendListLager()` bisher: 2 HTTP-Requests pro Artikel (SKU-Lookup + PUT).
Bei 1.000 Artikeln = 2.000 Requests — langsam, rate-limit-anfällig, kein Partial-Error-Handling.

Ziel: Nutzung der offiziellen WC REST v3 Batch-Endpoints, um den Request-Count auf
`ceil(n/100)` SKU-Lookups + `ceil(n/100)` Batch-Updates zu reduzieren (~20 statt 2.000).

---

## §2 Scope

### IN
- Refactor von `ImportSendListLager()` in `www/pages/shopimporter_woocommerce.php`
- Neue private Hilfsmethode `processBatchResponse()` in derselben Klasse
- Plan-Dokument `docs/plans/woocommerce-batch-stock-sync.md`

### OUT
- Kein neuer `postBatch()`-Helper auf `WCClient` — `post()` reicht direkt
- Keine Änderungen an `ImportGetAuftrag*`, `getKonfig`, `parseOrder`
- Kein Retry-Mechanismus bei HTTP-Fehlern (separater Task)
- Kein DB-Schema-Change
- Kein PR-Open (nach Review durch Maintainer)

---

## §3 Fix-Parameter

| Parameter | Wert | Begründung |
|---|---|---|
| Batch-Size | 100 Items | WC REST v3 Maximum |
| SKU-Chunk-Size | 100 SKUs | Passend zu `per_page=100` |
| Retry | Keiner | Out-of-scope für diesen PR |
| SKU-CSV-Support | `?sku=a,b,c` | WC REST v3 akzeptiert kommaseparierte Liste |

---

## §4 Funktions-Landkarte

Nur `ImportSendListLager()` wird geändert. Neue private Methode `processBatchResponse()`.

```
ImportSendListLager()
  ├── Schritt 1: Alle SKUs + Stock-Params sammeln (pendingUpdates[])
  ├── Schritt 2: Bulk-SKU-Auflösung
  │     └── GET products?sku=<csv>&per_page=100  (je Chunk à 100 SKUs)
  │           → skuMap[sku] = {id, parent, isvariant}
  ├── Schritt 3: Gruppieren
  │     ├── simpleItems[] (für products/batch)
  │     └── variationItems[parent_id][] (für products/{id}/variations/batch)
  └── Schritt 4: Batch-Updates senden (je Chunk à 100)
        ├── POST products/batch {update: [...]}
        ├── POST products/{parent}/variations/batch {update: [...]}
        └── processBatchResponse() → Partial-Error-Logging, zählt Erfolge

processBatchResponse($response, $endpoint)
  ├── Iteriert response->update[]
  │     ├── item->error vorhanden → logger->error
  │     └── kein Fehler → successCount++
  └── Iteriert response->errors[] (WC-Fallback)
```

---

## §5 Implementierungsschritte

1. **Vorverlagerte Datensammlung:** Statt der bisherigen per-Artikel-Schleife erst alle SKUs
   und Stock-Params in `$pendingUpdates[]` sammeln.

2. **Bulk-SKU-Auflösung:** `GET products?sku=<csv>&per_page=100` in Chunks à 100 SKUs.
   Ergebnis in `$skuMap[sku]` cachen.

3. **Gruppierung:** Simple products in `$simpleItems[]`, Variations pro Parent-ID in
   `$variationItems[parent_id][]`.

4. **Batch-POST:** `$this->client->post('products/batch', ['update' => $chunk])` für
   simple products; analog für Variations.

5. **Partial-Error-Handling:** `processBatchResponse()` liest `response->update[]` und
   `response->errors[]`, loggt Fehler per Item ohne den restlichen Sync abzubrechen.

---

## §6 Test-Matrix

Testumgebung: Docker-Shop auf `192.168.0.143:8080`, WC 10.7, Consumer Keys aktiv.

| # | Szenario | Erwartetes Ergebnis |
|---|---|---|
| T1 | 1 Artikel | 1 SKU-GET + 1 Batch-POST, Lagerbestand korrekt |
| T2 | 100 Artikel | 1 SKU-GET + 1 Batch-POST (1 Chunk) |
| T3 | 250 Artikel | 3 SKU-GETs + 3 Batch-POSTs (100+100+50) |
| T4 | 1 falsche SKU in 100er-Batch | 99 korrekt updated, 1 Error geloggt, sync läuft durch |
| T5 | Variation (parent != 0) | Variation-Batch-Endpoint genutzt, nicht products/batch |
| T6 | `ausverkauft=1` | stock_quantity=0 im Batch-Item |
| T7 | `inaktiv=1` | status='private' im Batch-Item |
| T8 | `pseudolager` gesetzt | lageranzahl aus pseudolager, nicht anzahl_lager |
| T9 | Leere Artikelliste | Rückgabe 0, keine HTTP-Requests |

---

## §7 Rollout & Rückwärts-Kompatibilität

- **Interface unverändert:** `ImportSendListLager()` behält Signatur `(): int`.
- **Keine DB-Migration** erforderlich.
- **WC-Mindestversion:** REST v3 Batch-Endpoints seit WC 3.0 (2017) — unkritisch.
- **Rollback:** Git-Revert des Feature-Branch reicht.

---

## §8 Risiken

| Risiko | Mitigation |
|---|---|
| WC akzeptiert SKU-CSV nicht (`?sku=a,b`) | Verifikation in T1–T3 gegen 192.168.0.143:8080 — falls nicht, Fallback: sequentielle Einzellookups (wie vorher) |
| Batch-Response enthält keine `update`-Keys | `processBatchResponse()` defensiv mit `?? []` abgesichert |
| Hoster-seitiger `per_page`-Cap unter 100 | Derzeit nicht abgefangen; separater Task |
| Partial-Error zählt als nicht-erfolgreich | Korrekt: `$anzahl` zählt nur Items ohne Fehler |
| Variations mit unbekanntem Parent | Variation-Batch schlägt fehl → WCHttpClientException propagiert nach oben (bestehende Semantik) |
