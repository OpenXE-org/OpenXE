<?php
/*
 * SPDX-FileCopyrightText: 2026 OpenXE contributors
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

class Abschlagsrechnung
{
    /** @var ApplicationCore|Application */
    public $app;

    /** @var bool Static cache to prevent redundant hook registrations */
    private static bool $hooksRegistered = false;

    /**
     * @param ApplicationCore|Application $app
     * @param bool $intern
     */
    public function __construct($app, $intern = false)
    {
        $this->app = $app;
        $this->registerHooks();

        // Ensure database table exists
        if (!$this->app->DB->CheckTableExistence('abschlagsrechnung_rechnung')) {
            $this->Install();
        }

        if ($intern) {
            return;
        }

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler('list', 'AbschlagsrechnungList');
        $this->app->ActionHandler('auftraglist', 'AbschlagsrechnungAuftragList');
        $this->app->ActionHandler('teilrechnung', 'AbschlagsrechnungTeilrechnung');
        $this->app->ActionHandler('schlussrechnung', 'AbschlagsrechnungSchlussrechnung');
        $this->app->ActionHandler('delete', 'AbschlagsrechnungDelete');
        $this->app->ActionHandler('data', 'AbschlagsrechnungData');
        $this->app->ActionHandlerListen($app);
    }

    /**
     * Module installation & table creation
     */
    public function Install(): void
    {
        $this->app->DB->Query(
            "CREATE TABLE IF NOT EXISTS `abschlagsrechnung_rechnung` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `rechnung` int(11) NOT NULL DEFAULT 0,
                `auftrag` int(11) NOT NULL DEFAULT 0,
                `bezeichnung` varchar(255) NOT NULL,
                PRIMARY KEY (`id`),
                KEY `rechnung` (`rechnung`),
                KEY `auftrag` (`auftrag`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci"
        );

        if (isset($this->app->erp)) {
            $this->app->erp->addActionsToModule('abschlagsrechnung', [
                'list',
                'auftraglist',
                'teilrechnung',
                'schlussrechnung',
                'delete',
                'data'
            ]);
        }
    }

    /**
     * Ensures hooks are registered in the system database
     */
    public function registerHooks(): void
    {
        if (self::$hooksRegistered) {
            return;
        }

        if (isset($this->app->erp)) {
            $this->app->erp->RegisterHook('briefpapier_render_document_hook1', 'Abschlagsrechnung', 'BriefpapierRenderDocumentHook1', 1);
            $this->app->erp->RegisterHook('briefpapier_render_footer_hook2', 'Abschlagsrechnung', 'BriefpapierRenderFooterHook2', 1);
            self::$hooksRegistered = true;
        }
    }

    /**
     * Generate or retrieve CSRF token
     */
    public function getCsrfToken(string $key = 'abschlagsrechnung'): string
    {
        if (!headers_sent() && isset($this->app->Container) && $this->app->Container->has('Session')) {
            try {
                $session = $this->app->Container->get('Session');
                if ($session instanceof \Xentral\Components\Http\Session\Session) {
                    return (string)$session->createCsrfToken($key);
                }
            } catch (\Throwable $e) {
                // Fallback below
            }
        }

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        if (!isset($_SESSION) || !is_array($_SESSION)) {
            $_SESSION = [];
        }

        if (empty($_SESSION['csrf_' . $key])) {
            $_SESSION['csrf_' . $key] = bin2hex(random_bytes(16));
        }

        return (string)$_SESSION['csrf_' . $key];
    }

    /**
     * Validate CSRF token against Session or session array
     */
    public function isCsrfTokenValid(?string $token, string $key = 'abschlagsrechnung', bool $remove = false): bool
    {
        if (empty($token)) {
            return false;
        }

        if (!headers_sent() && isset($this->app->Container) && $this->app->Container->has('Session')) {
            try {
                $session = $this->app->Container->get('Session');
                if ($session instanceof \Xentral\Components\Http\Session\Session) {
                    if ($session->isCsrfTokenValid($key, $token, $remove)) {
                        return true;
                    }
                }
            } catch (\Throwable $e) {
                // Fallback below
            }
        }

        if (!empty($_SESSION['csrf_' . $key]) && hash_equals((string)$_SESSION['csrf_' . $key], $token)) {
            if ($remove) {
                unset($_SESSION['csrf_' . $key]);
            }
            return true;
        }

        if (!empty($_SESSION['token']) && hash_equals((string)$_SESSION['token'], $token)) {
            if ($remove) {
                unset($_SESSION['token']);
            }
            return true;
        }

        return false;
    }

    /**
     * Rotate / Invalidate CSRF token after successful mutating action
     */
    public function rotateCsrfToken(string $key = 'abschlagsrechnung'): void
    {
        if (!headers_sent() && isset($this->app->Container) && $this->app->Container->has('Session')) {
            try {
                $session = $this->app->Container->get('Session');
                if ($session instanceof \Xentral\Components\Http\Session\Session) {
                    $session->createCsrfToken($key);
                }
            } catch (\Throwable $e) {
                // Ignore
            }
        }

        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }

        if (isset($_SESSION['csrf_' . $key])) {
            unset($_SESSION['csrf_' . $key]);
        }
    }

    /**
     * Authorization and permission check
     */
    public function checkPermission(string $action, ?int $auftragId = null): bool
    {
        // CLI execution (integration tests, cronjobs, batch commands)
        if (php_sapi_name() === 'cli') {
            return true;
        }

        // Unauthenticated web calls are denied
        if (!isset($this->app->User) || (int)$this->app->User->GetID() <= 0) {
            return false;
        }

        // Admin has all rights
        if ($this->app->User->GetType() === 'admin') {
            return true;
        }

        if (isset($this->app->erp)) {
            $hasModuleRight = $this->app->erp->RechteVorhanden('abschlagsrechnung', $action);
            $isMutating = in_array($action, ['teilrechnung', 'schlussrechnung', 'delete'], true);
            $hasAuftragRight = $isMutating
                ? $this->app->erp->RechteVorhanden('auftrag', 'edit')
                : ($this->app->erp->RechteVorhanden('auftrag', 'edit') || $this->app->erp->RechteVorhanden('auftrag', 'ansehen'));

            if ($isMutating) {
                if (!$hasModuleRight || !$hasAuftragRight) {
                    return false;
                }
            } else {
                if (!$hasModuleRight && !$hasAuftragRight) {
                    return false;
                }
            }

            // Verify project access on order
            if ($auftragId !== null && $auftragId > 0) {
                $order = $this->app->DB->SelectRow(
                    sprintf("SELECT id, projekt, vertriebid FROM `auftrag` WHERE id = %d LIMIT 1", $auftragId)
                );
                if (empty($order)) {
                    return false;
                }

                $projSql = $this->app->erp->ProjektRechte('p.id', true, 'a.vertriebid');
                if (!empty($projSql)) {
                    $check = $this->app->DB->Select(
                        sprintf(
                            "SELECT a.id FROM `auftrag` AS a LEFT JOIN `projekt` AS p ON p.id = a.projekt WHERE a.id = %d AND (%s) LIMIT 1",
                            $auftragId,
                            $projSql
                        )
                    );
                    if (empty($check)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * Abschlagsrechnung Tab in Auftrag View
     */
    public function AbschlagsrechnungAuftragList(): void
    {
        $id = (int)$this->getParam('id');
        if ($id <= 0) {
            $this->app->Location->execute('index.php?module=auftrag&action=list');
            return;
        }

        if (!$this->checkPermission('auftraglist', $id)) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Keine Berechtigung f&uuml;r diesen Auftrag!</div>');
            $this->app->Location->execute('index.php?module=auftrag&action=list&msg=' . $msg);
            return;
        }

        $order = $this->app->DB->SelectRow(
            sprintf(
                "SELECT a.*, adr.name AS kundenname 
                 FROM `auftrag` AS a 
                 LEFT JOIN `adresse` AS adr ON adr.id = a.adresse 
                 WHERE a.id = %d LIMIT 1",
                $id
            )
        );

        if (empty($order)) {
            $this->app->Location->execute('index.php?module=auftrag&action=list');
            return;
        }

        $kundenname = !empty($order['kundenname']) ? $order['kundenname'] : (!empty($order['name']) ? $order['name'] : '');
        $belegnr = !empty($order['belegnr']) ? $order['belegnr'] : 'Entwurf';

        $this->setupAuftragMenu($id, $order['status'], $belegnr, $kundenname);

        $rechnungen = $this->app->DB->SelectArr(
            sprintf(
                "SELECT ar.id AS ar_id, ar.bezeichnung, ar.rechnung AS rechnung_id, 
                        r.belegnr, r.name, r.datum, r.status, r.soll, r.ist
                 FROM `abschlagsrechnung_rechnung` AS ar
                 LEFT JOIN `rechnung` AS r ON r.id = ar.rechnung
                 WHERE ar.auftrag = %d
                 ORDER BY ar.id ASC",
                $id
            )
        );

        $csrfToken = $this->getCsrfToken();
        $teilrechnungCount = 0;
        $htmlRows = '';

        if (!empty($rechnungen)) {
            foreach ($rechnungen as $r) {
                if (stripos($r['bezeichnung'], 'Teilrechnung') !== false || stripos($r['bezeichnung'], 'Abschlag') !== false) {
                    $teilrechnungCount++;
                }

                $recId = (int)$r['rechnung_id'];
                $arId = (int)$r['ar_id'];
                $belegDisplay = !empty($r['belegnr']) ? htmlspecialchars($r['belegnr']) : 'Entwurf (' . $recId . ')';
                $recLink = '<a href="index.php?module=rechnung&action=edit&id=' . $recId . '"><strong>' . $belegDisplay . '</strong></a>';
                $nameDisplay = htmlspecialchars($r['name'] ?: $kundenname);
                $datumDisplay = !empty($r['datum']) && $r['datum'] !== '0000-00-00 00:00:00' ? date('d.m.Y', strtotime($r['datum'])) : '-';
                $bezeichnungDisplay = htmlspecialchars($r['bezeichnung']);
                $statusDisplay = htmlspecialchars($r['status'] ?: 'angelegt');

                // Calculate Netto & Brutto
                $netto = (float)$this->app->erp->ANABREGSNeuberechnenGesamtsumme($recId, 'rechnung', true);
                $brutto = (float)$r['soll'];

                $theme = $this->app->Conf->WFconf['defaulttheme'] ?? 'new';
                $menu = '<table class="nopadding" cellpadding="0" cellspacing="0"><tr>';
                $menu .= '<td><a href="index.php?module=rechnung&action=edit&id=' . $recId . '" title="Rechnung bearbeiten"><img src="themes/' . $theme . '/images/edit.svg" border="0" width="16"></a></td>';
                $menu .= '<td><a href="index.php?module=rechnung&action=pdf&id=' . $recId . '" title="PDF anzeigen" target="_blank"><img src="themes/' . $theme . '/images/pdf.svg" border="0" width="16"></a></td>';
                $menu .= '<td><a href="#" onclick="deleteAbschlag(' . $arId . ', ' . $id . ');" title="Verknüpfung löschen"><img src="themes/' . $theme . '/images/delete.svg" border="0" width="16"></a></td>';
                $menu .= '</tr></table>';

                $htmlRows .= '<tr>';
                $htmlRows .= '<td>' . $recLink . '</td>';
                $htmlRows .= '<td>' . $nameDisplay . '</td>';
                $htmlRows .= '<td>' . $datumDisplay . '</td>';
                $htmlRows .= '<td>' . $bezeichnungDisplay . '</td>';
                $htmlRows .= '<td align="right">' . number_format($netto, 2, ',', '.') . ' &euro;</td>';
                $htmlRows .= '<td align="right">' . number_format($brutto, 2, ',', '.') . ' &euro;</td>';
                $htmlRows .= '<td align="center">' . $statusDisplay . '</td>';
                $htmlRows .= '<td align="center">' . $menu . '</td>';
                $htmlRows .= '</tr>';
            }
        } else {
            $htmlRows = '<tr><td colspan="8" align="center" style="padding:20px; color:#888;">Keine Abschlagsrechnungen f&uuml;r diesen Auftrag vorhanden.</td></tr>';
        }

        $defaultBezeichnung = ($teilrechnungCount + 1) . '. Teilrechnung';

        $this->app->Tpl->Set('AUFTRAG_ID', (string)$id);
        $this->app->Tpl->Set('DEFAULT_BEZEICHNUNG', htmlspecialchars($defaultBezeichnung));
        $this->app->Tpl->Set('CSRF_TOKEN', htmlspecialchars($csrfToken));
        $this->app->Tpl->Set('RECHNUNGSLISTE', $htmlRows);

        $msg = $this->getParam('msg');
        if (!empty($msg)) {
            $decoded = $this->app->erp->base64_url_decode($msg);
            $cleanMsg = strip_tags($decoded, '<div><span><b><strong><i><br>');
            $this->app->Tpl->Set('MESSAGE', $cleanMsg);
        } else {
            $this->app->Tpl->Set('MESSAGE', '');
        }

        $this->app->Tpl->Parse('PAGE', 'abschlagsrechnung_auftraglist.tpl');
    }

    /**
     * Create Teilrechnung from Auftrag
     */
    public function AbschlagsrechnungTeilrechnung(): void
    {
        $id = (int)$this->getParam('id');
        if ($id <= 0) {
            $this->app->Location->execute('index.php?module=auftrag&action=list');
            return;
        }

        // Authorization check
        if (!$this->checkPermission('teilrechnung', $id)) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Keine Berechtigung f&uuml;r diesen Auftrag!</div>');
            $this->app->Location->execute('index.php?module=auftrag&action=list&msg=' . $msg);
            return;
        }

        // CSRF Token check (POST only)
        $token = $this->getPostParam('token');
        if (!$this->isCsrfTokenValid($token)) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Ung&uuml;ltiges Sicherheitstoken (CSRF). Bitte versuchen Sie es erneut.</div>');
            $this->app->Location->execute('index.php?module=abschlagsrechnung&action=auftraglist&id=' . $id . '&msg=' . $msg);
            return;
        }

        $bezeichnung = trim($this->getPostParam('bezeichnung'));
        if ($bezeichnung === '') {
            $bezeichnung = 'Teilrechnung';
        }

        $posnull = (int)$this->getPostParam('posnull');
        $artNummer = trim($this->getPostParam('artikel_teilzahlung'));
        $artBezeichnung = trim($this->getPostParam('artikel_teilzahlung_bezeichnung'));
        $artBeschreibung = trim($this->getPostParam('artikel_teilzahlung_beschreibung'));
        $menge = (float)str_replace(',', '.', (string)($this->getPostParam('artikel_teilzahlung_menge') ?: 1));
        $preis = (float)str_replace(',', '.', (string)($this->getPostParam('artikel_teilzahlung_preis') ?: 0));

        // Create standard invoice from order
        $newRechnungId = (int)$this->app->erp->WeiterfuehrenAuftragZuRechnung($id);
        if ($newRechnungId <= 0) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Fehler beim Erstellen der Teilrechnung!</div>');
            $this->app->Location->execute('index.php?module=abschlagsrechnung&action=auftraglist&id=' . $id . '&msg=' . $msg);
            return;
        }

        if ($posnull === 1) {
            // Keep all order positions but set Menge = 0 and Rabatt = 0 so no premature tax is charged
            $this->app->DB->Update(
                sprintf(
                    "UPDATE `rechnung_position` SET `menge` = 0, `rabatt` = 0 WHERE `rechnung` = %d",
                    $newRechnungId
                )
            );
        } else {
            // Remove order positions; only the down payment item will appear
            $this->app->DB->Delete(
                sprintf(
                    "DELETE FROM `rechnung_position` WHERE `rechnung` = %d",
                    $newRechnungId
                )
            );
        }

        // Resolve down payment article details
        $artId = 0;
        $steuersatz = (float)$this->app->erp->GetSteuersatzNormal(false, $newRechnungId, 'rechnung');

        if ($artNummer !== '') {
            $artRow = $this->app->DB->SelectRow(
                sprintf(
                    "SELECT id, nummer, name_de, beschreibung_de, steuersatz 
                     FROM `artikel` 
                     WHERE `nummer` = '%s' OR `id` = '%s' 
                     LIMIT 1",
                    $this->app->DB->real_escape_string($artNummer),
                    $this->app->DB->real_escape_string($artNummer)
                )
            );
            if (!empty($artRow)) {
                $artId = (int)$artRow['id'];
                if ($artBezeichnung === '') {
                    $artBezeichnung = $artRow['name_de'];
                }
                if ($artBeschreibung === '') {
                    $artBeschreibung = $artRow['beschreibung_de'];
                }
                $steuersatz = $this->resolveArticleTaxRate($artRow['steuersatz'], $newRechnungId);
            }
        }

        if ($artBezeichnung === '') {
            $artBezeichnung = $bezeichnung;
        }

        $nextSort = 1 + (int)$this->app->DB->Select(
            sprintf(
                "SELECT MAX(sort) FROM `rechnung_position` WHERE `rechnung` = %d",
                $newRechnungId
            )
        );

        $mengeStr = number_format($menge, 4, '.', '');
        $preisStr = number_format($preis, 4, '.', '');
        $steuersatzStr = number_format($steuersatz, 2, '.', '');

        // Insert down payment line item
        $this->app->DB->Insert(
            sprintf(
                "INSERT INTO `rechnung_position` 
                 (`rechnung`, `artikel`, `bezeichnung`, `beschreibung`, `menge`, `preis`, `umsatzsteuer`, `steuersatz`, `sort`) 
                 VALUES (%d, %d, '%s', '%s', %s, %s, '%s', %s, %d)",
                $newRechnungId,
                $artId,
                $this->app->DB->real_escape_string($artBezeichnung),
                $this->app->DB->real_escape_string($artBeschreibung),
                $mengeStr,
                $preisStr,
                $this->app->DB->real_escape_string((string)$steuersatz),
                $steuersatzStr,
                $nextSort
            )
        );

        // Link in abschlagsrechnung_rechnung
        $this->app->DB->Insert(
            sprintf(
                "INSERT INTO `abschlagsrechnung_rechnung` (`rechnung`, `auftrag`, `bezeichnung`) 
                 VALUES (%d, %d, '%s')",
                $newRechnungId,
                $id,
                $this->app->DB->real_escape_string($bezeichnung)
            )
        );

        // Set subtitle
        $this->app->DB->Update(
            sprintf(
                "UPDATE `rechnung` SET `ihrebestellnummer` = '%s' WHERE `id` = %d AND (`ihrebestellnummer` = '' OR `ihrebestellnummer` IS NULL)",
                $this->app->DB->real_escape_string($bezeichnung),
                $newRechnungId
            )
        );

        $this->app->erp->RechnungNeuberechnen($newRechnungId);
        $this->app->erp->AuftragProtokoll($id, "Teilrechnung '{$bezeichnung}' angelegt");
        $this->rotateCsrfToken();

        $this->app->Location->execute('index.php?module=rechnung&action=edit&id=' . $newRechnungId);
    }

    /**
     * Create Schlussrechnung from Auftrag
     */
    public function AbschlagsrechnungSchlussrechnung(): void
    {
        $id = (int)$this->getParam('id');
        if ($id <= 0) {
            $this->app->Location->execute('index.php?module=auftrag&action=list');
            return;
        }

        // Authorization check
        if (!$this->checkPermission('schlussrechnung', $id)) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Keine Berechtigung f&uuml;r diesen Auftrag!</div>');
            $this->app->Location->execute('index.php?module=auftrag&action=list&msg=' . $msg);
            return;
        }

        // CSRF Token check (POST only)
        $token = $this->getPostParam('token');
        if (!$this->isCsrfTokenValid($token)) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Ung&uuml;ltiges Sicherheitstoken (CSRF). Bitte versuchen Sie es erneut.</div>');
            $this->app->Location->execute('index.php?module=abschlagsrechnung&action=auftraglist&id=' . $id . '&msg=' . $msg);
            return;
        }

        // Idempotency check: prevent multiple active final invoices for the same order
        $existingSchluss = (int)$this->app->DB->Select(
            sprintf(
                "SELECT ar.rechnung 
                 FROM `abschlagsrechnung_rechnung` AS ar 
                 INNER JOIN `rechnung` AS r ON r.id = ar.rechnung 
                 WHERE ar.auftrag = %d AND ar.bezeichnung LIKE '%%Schlussrechnung%%' AND r.status != 'storniert' 
                 LIMIT 1",
                $id
            )
        );
        if ($existingSchluss > 0) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Es existiert bereits eine aktive Schlussrechnung f&uuml;r diesen Auftrag!</div>');
            $this->app->Location->execute('index.php?module=abschlagsrechnung&action=auftraglist&id=' . $id . '&msg=' . $msg);
            return;
        }

        // Create standard invoice with 100% order positions
        $newRechnungId = (int)$this->app->erp->WeiterfuehrenAuftragZuRechnung($id);
        if ($newRechnungId <= 0) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Fehler beim Erstellen der Schlussrechnung!</div>');
            $this->app->Location->execute('index.php?module=abschlagsrechnung&action=auftraglist&id=' . $id . '&msg=' . $msg);
            return;
        }

        // Query all previous partial invoices for this order
        $previousInvoices = $this->app->DB->SelectArr(
            sprintf(
                "SELECT ar.*, r.belegnr, r.datum, r.soll 
                 FROM `abschlagsrechnung_rechnung` AS ar
                 INNER JOIN `rechnung` AS r ON r.id = ar.rechnung
                 WHERE ar.auftrag = %d AND r.status != 'storniert' AND ar.bezeichnung NOT LIKE '%%Schlussrechnung%%'
                 ORDER BY ar.id ASC",
                $id
            )
        );

        if (!empty($previousInvoices)) {
            $nextSort = 1 + (int)$this->app->DB->Select(
                sprintf(
                    "SELECT MAX(sort) FROM `rechnung_position` WHERE `rechnung` = %d",
                    $newRechnungId
                )
            );

            foreach ($previousInvoices as $prev) {
                $prevRecId = (int)$prev['rechnung'];
                // Get down payment lines from previous invoice
                $positions = $this->app->DB->SelectArr(
                    sprintf(
                        "SELECT * FROM `rechnung_position` WHERE `rechnung` = %d AND `menge` > 0",
                        $prevRecId
                    )
                );

                if (!empty($positions)) {
                    foreach ($positions as $pos) {
                        $rabattProzent = isset($pos['rabatt']) ? (float)$pos['rabatt'] : 0.0;
                        $nettoEinzel = (float)$pos['preis'] * (1.0 - ($rabattProzent / 100.0));
                        $deductionAmount = abs($nettoEinzel * (float)$pos['menge']);
                        $deductionTitle = !empty($pos['bezeichnung']) ? $pos['bezeichnung'] : $prev['bezeichnung'];
                        $deductionDesc = !empty($pos['beschreibung']) ? $pos['beschreibung'] : ('Abzug ' . $prev['bezeichnung']);
                        $posTaxRate = (float)($pos['steuersatz'] ?: $pos['umsatzsteuer']);

                        $this->app->DB->Insert(
                            sprintf(
                                "INSERT INTO `rechnung_position` 
                                 (`rechnung`, `artikel`, `bezeichnung`, `beschreibung`, `menge`, `preis`, `umsatzsteuer`, `steuersatz`, `sort`, `rabatt`) 
                                 VALUES (%d, %d, '%s', '%s', 1, %s, '%s', %s, %d, 0)",
                                $newRechnungId,
                                (int)$pos['artikel'],
                                $this->app->DB->real_escape_string($deductionTitle),
                                $this->app->DB->real_escape_string($deductionDesc),
                                number_format(-1.0 * $deductionAmount, 4, '.', ''),
                                $this->app->DB->real_escape_string((string)$pos['umsatzsteuer']),
                                number_format($posTaxRate, 2, '.', ''),
                                $nextSort++
                            )
                        );
                    }
                } else {
                    // Fallback: group by tax rate via steuerAusBelegArray to correctly reflect mixed tax rates
                    $taxArr = $this->app->erp->steuerAusBelegArray('rechnung', $prevRecId);
                    if (!empty($taxArr)) {
                        $nettoByTax = [];
                        foreach ($taxArr as $t) {
                            $rate = (float)$t['steuersatz'];
                            $uSteuer = $t['usteuer'] ?? 'normal';
                            $k = (string)$rate;
                            if (!isset($nettoByTax[$k])) {
                                $nettoByTax[$k] = ['netto' => 0.0, 'steuersatz' => $rate, 'umsatzsteuer' => $uSteuer];
                            }
                            $nettoByTax[$k]['netto'] += (float)$t['netto'];
                        }
                        foreach ($nettoByTax as $taxGroup) {
                            if ($taxGroup['netto'] > 0) {
                                $this->app->DB->Insert(
                                    sprintf(
                                        "INSERT INTO `rechnung_position` 
                                         (`rechnung`, `artikel`, `bezeichnung`, `beschreibung`, `menge`, `preis`, `umsatzsteuer`, `steuersatz`, `sort`, `rabatt`) 
                                         VALUES (%d, 0, '%s', '%s', 1, %s, '%s', %s, %d, 0)",
                                        $newRechnungId,
                                        $this->app->DB->real_escape_string('Abzug ' . $prev['bezeichnung']),
                                        $this->app->DB->real_escape_string('Rechnung ' . ($prev['belegnr'] ?: $prevRecId)),
                                        number_format(-1.0 * $taxGroup['netto'], 4, '.', ''),
                                        $this->app->DB->real_escape_string((string)$taxGroup['umsatzsteuer']),
                                        number_format($taxGroup['steuersatz'], 2, '.', ''),
                                        $nextSort++
                                    )
                                );
                            }
                        }
                    } else {
                        // General fallback
                        $prevNetto = (float)$this->app->erp->ANABREGSNeuberechnenGesamtsumme($prevRecId, 'rechnung', true);
                        if ($prevNetto > 0) {
                            $steuersatz = (float)$this->app->erp->GetSteuersatzNormal(false, $newRechnungId, 'rechnung');
                            $this->app->DB->Insert(
                                sprintf(
                                    "INSERT INTO `rechnung_position` 
                                     (`rechnung`, `artikel`, `bezeichnung`, `beschreibung`, `menge`, `preis`, `umsatzsteuer`, `steuersatz`, `sort`, `rabatt`) 
                                     VALUES (%d, 0, '%s', '%s', 1, %s, '%s', %s, %d, 0)",
                                    $newRechnungId,
                                    $this->app->DB->real_escape_string('Abzug ' . $prev['bezeichnung']),
                                    $this->app->DB->real_escape_string('Rechnung ' . ($prev['belegnr'] ?: $prevRecId)),
                                    number_format(-1.0 * $prevNetto, 4, '.', ''),
                                    $this->app->DB->real_escape_string((string)$steuersatz),
                                    number_format($steuersatz, 2, '.', ''),
                                    $nextSort++
                                )
                            );
                        }
                    }
                }
            }
        }

        // Link Schlussrechnung in abschlagsrechnung_rechnung
        $this->app->DB->Insert(
            sprintf(
                "INSERT INTO `abschlagsrechnung_rechnung` (`rechnung`, `auftrag`, `bezeichnung`) 
                 VALUES (%d, %d, 'Schlussrechnung')",
                $newRechnungId,
                $id
            )
        );

        // Set subtitle
        $this->app->DB->Update(
            sprintf(
                "UPDATE `rechnung` SET `ihrebestellnummer` = 'Schlussrechnung' WHERE `id` = %d AND (`ihrebestellnummer` = '' OR `ihrebestellnummer` IS NULL)",
                $newRechnungId
            )
        );

        $this->app->erp->RechnungNeuberechnen($newRechnungId);
        $this->app->erp->AuftragProtokoll($id, "Schlussrechnung angelegt");
        $this->rotateCsrfToken();

        $this->app->Location->execute('index.php?module=rechnung&action=edit&id=' . $newRechnungId);
    }

    /**
     * Delete link in abschlagsrechnung_rechnung (order-scoped and CSRF-protected)
     */
    public function AbschlagsrechnungDelete(): void
    {
        $id = (int)$this->getParam('id');
        $auftragId = (int)$this->getParam('auftrag');

        if ($auftragId <= 0) {
            $this->app->Location->execute('index.php?module=auftrag&action=list');
            return;
        }

        // Authorization check
        if (!$this->checkPermission('delete', $auftragId)) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Keine Berechtigung zum L&ouml;schen!</div>');
            $this->app->Location->execute('index.php?module=abschlagsrechnung&action=auftraglist&id=' . $auftragId . '&msg=' . $msg);
            return;
        }

        // CSRF Token check (POST only)
        $token = $this->getPostParam('token');
        if (!$this->isCsrfTokenValid($token)) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Ung&uuml;ltiges Sicherheitstoken (CSRF). Bitte versuchen Sie es erneut.</div>');
            $this->app->Location->execute('index.php?module=abschlagsrechnung&action=auftraglist&id=' . $auftragId . '&msg=' . $msg);
            return;
        }

        if ($id > 0) {
            $this->app->DB->Delete(
                sprintf(
                    "DELETE FROM `abschlagsrechnung_rechnung` WHERE `id` = %d AND `auftrag` = %d LIMIT 1",
                    $id,
                    $auftragId
                )
            );
            $this->rotateCsrfToken();
        }

        $this->app->Location->execute('index.php?module=abschlagsrechnung&action=auftraglist&id=' . $auftragId);
    }

    /**
     * AJAX endpoint for article data lookup
     */
    public function AbschlagsrechnungData(): void
    {
        if (!$this->checkPermission('data')) {
            header('HTTP/1.1 403 Forbidden');
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false, 'error' => 'Keine Berechtigung']);
            $this->app->ExitXentral();
            return;
        }

        $cmd = $this->getParam('cmd');
        if ($cmd === 'getarticle') {
            $nummer = trim($this->getParam('nummer'));
            if ($nummer !== '') {
                $row = $this->app->DB->SelectRow(
                    sprintf(
                        "SELECT id, nummer, name_de, beschreibung_de, steuersatz, verkaufspreis 
                         FROM `artikel` 
                         WHERE `nummer` = '%s' OR `id` = '%s' 
                         LIMIT 1",
                        $this->app->DB->real_escape_string($nummer),
                        $this->app->DB->real_escape_string($nummer)
                    )
                );
                if (!empty($row)) {
                    header('Content-Type: application/json; charset=utf-8');
                    echo json_encode([
                        'success' => true,
                        'id' => (int)$row['id'],
                        'nummer' => $row['nummer'],
                        'name_de' => $row['name_de'],
                        'beschreibung_de' => $row['beschreibung_de'],
                        'steuersatz' => $row['steuersatz'],
                        'preis' => number_format((float)$row['verkaufspreis'], 2, '.', '')
                    ]);
                    $this->app->ExitXentral();
                    return;
                }
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => false]);
            $this->app->ExitXentral();
            return;
        }
    }

    /**
     * Global List of Abschlagsrechnungen
     */
    public function AbschlagsrechnungList(): void
    {
        if (!$this->checkPermission('list')) {
            $msg = $this->app->erp->base64_url_encode('<div class="error">Keine Berechtigung f&uuml;r Abschlagsrechnungen!</div>');
            $this->app->Location->execute('index.php?module=auftrag&action=list&msg=' . $msg);
            return;
        }

        $this->app->Tpl->Set('KURZUEBERSCHRIFT2', 'Abschlagsrechnung');
        $this->app->erp->MenuEintrag('index.php?module=abschlagsrechnung&action=list', '&Uuml;bersicht');

        $whereClause = '1=1';
        if (isset($this->app->erp)) {
            $projSql = $this->app->erp->ProjektRechte('p.id', true, 'a.vertriebid');
            if (!empty($projSql)) {
                $whereClause .= ' AND (' . $projSql . ')';
            }
        }

        $rechnungen = $this->app->DB->SelectArr(
            "SELECT ar.id AS ar_id, ar.bezeichnung, ar.rechnung AS rechnung_id, ar.auftrag AS auftrag_id,
                    r.belegnr AS rechnung_belegnr, a.belegnr AS auftrag_belegnr,
                    r.name, r.datum, r.status, r.soll, r.ist
             FROM `abschlagsrechnung_rechnung` AS ar
             LEFT JOIN `rechnung` AS r ON r.id = ar.rechnung
             LEFT JOIN `auftrag` AS a ON a.id = ar.auftrag
             LEFT JOIN `projekt` AS p ON p.id = a.projekt
             WHERE {$whereClause}
             ORDER BY ar.id DESC"
        );

        $htmlRows = '';
        if (!empty($rechnungen)) {
            $theme = $this->app->Conf->WFconf['defaulttheme'] ?? 'new';
            foreach ($rechnungen as $r) {
                $recId = (int)$r['rechnung_id'];
                $aufId = (int)$r['auftrag_id'];
                $rBeleg = !empty($r['rechnung_belegnr']) ? htmlspecialchars($r['rechnung_belegnr']) : 'Entwurf (' . $recId . ')';
                $aBeleg = !empty($r['auftrag_belegnr']) ? htmlspecialchars($r['auftrag_belegnr']) : 'Auftrag ' . $aufId;

                $netto = (float)$this->app->erp->ANABREGSNeuberechnenGesamtsumme($recId, 'rechnung', true);
                $brutto = (float)$r['soll'];

                $menu = '<table class="nopadding" cellpadding="0" cellspacing="0"><tr>';
                $menu .= '<td><a href="index.php?module=rechnung&action=edit&id=' . $recId . '" title="Rechnung bearbeiten"><img src="themes/' . $theme . '/images/edit.svg" border="0" width="16"></a></td>';
                $menu .= '<td><a href="index.php?module=rechnung&action=pdf&id=' . $recId . '" title="PDF anzeigen" target="_blank"><img src="themes/' . $theme . '/images/pdf.svg" border="0" width="16"></a></td>';
                $menu .= '<td><a href="index.php?module=abschlagsrechnung&action=auftraglist&id=' . $aufId . '" title="Zum Auftrag"><img src="themes/' . $theme . '/images/forward.svg" border="0" width="16"></a></td>';
                $menu .= '</tr></table>';

                $htmlRows .= '<tr>';
                $htmlRows .= '<td><a href="index.php?module=rechnung&action=edit&id=' . $recId . '"><strong>' . $rBeleg . '</strong></a></td>';
                $htmlRows .= '<td><a href="index.php?module=auftrag&action=edit&id=' . $aufId . '">' . $aBeleg . '</a></td>';
                $htmlRows .= '<td>' . (!empty($r['datum']) && $r['datum'] !== '0000-00-00 00:00:00' ? date('d.m.Y', strtotime($r['datum'])) : '-') . '</td>';
                $htmlRows .= '<td>' . htmlspecialchars($r['name']) . '</td>';
                $htmlRows .= '<td>' . htmlspecialchars($r['bezeichnung']) . '</td>';
                $htmlRows .= '<td align="right">' . number_format($netto, 2, ',', '.') . ' &euro;</td>';
                $htmlRows .= '<td align="right">' . number_format($brutto, 2, ',', '.') . ' &euro;</td>';
                $htmlRows .= '<td align="center">' . htmlspecialchars($r['status'] ?: 'angelegt') . '</td>';
                $htmlRows .= '<td align="center">' . $menu . '</td>';
                $htmlRows .= '</tr>';
            }
        } else {
            $htmlRows = '<tr><td colspan="9" align="center" style="padding:20px; color:#888;">Keine Abschlagsrechnungen im System vorhanden.</td></tr>';
        }

        $this->app->Tpl->Set('RECHNUNGSLISTE', $htmlRows);
        $this->app->Tpl->Set('MESSAGE', '');
        $this->app->Tpl->Parse('PAGE', 'abschlagsrechnung_list.tpl');
    }

    /**
     * Hook: Briefpapier Render Document Hook 1
     * Appends subtitle (e.g. "1. Teilrechnung" or "Schlussrechnung") to document title
     *
     * @param Briefpapier $pdf
     */
    public function BriefpapierRenderDocumentHook1($pdf): void
    {
        if (!$pdf || $pdf->doctype !== 'rechnung' || empty($pdf->doctypeid)) {
            return;
        }

        $sub = $this->app->DB->Select(
            sprintf(
                "SELECT bezeichnung FROM `abschlagsrechnung_rechnung` WHERE `rechnung` = %d LIMIT 1",
                (int)$pdf->doctypeid
            )
        );

        if (!empty($sub)) {
            if (strpos($pdf->doctypeOrig, $sub) === false) {
                $pdf->doctypeOrig .= "\n" . $sub;
            }
        }
    }

    /**
     * Hook: Briefpapier Render Footer Hook 2
     * Renders the "Tabelle für Abschlagsrechnung" on invoice PDFs
     *
     * @param Briefpapier $pdf
     */
    public function BriefpapierRenderFooterHook2($pdf): void
    {
        if (!$pdf || $pdf->doctype !== 'rechnung' || empty($pdf->doctypeid)) {
            return;
        }

        $rechnungId = (int)$pdf->doctypeid;
        $auftragId = (int)$this->app->DB->Select(
            sprintf(
                "SELECT auftrag FROM `abschlagsrechnung_rechnung` WHERE `rechnung` = %d LIMIT 1",
                $rechnungId
            )
        );

        if ($auftragId <= 0) {
            return;
        }

        $invoices = $this->app->DB->SelectArr(
            sprintf(
                "SELECT ar.rechnung, ar.bezeichnung, r.belegnr, 
                        DATE_FORMAT(r.datum, '%%d.%%m.%%Y') AS datum_formatiert, 
                        r.soll, r.ist, r.status
                 FROM `abschlagsrechnung_rechnung` AS ar
                 INNER JOIN `rechnung` AS r ON r.id = ar.rechnung
                 WHERE ar.auftrag = %d AND r.status != 'storniert'
                 ORDER BY ar.id ASC",
                $auftragId
            )
        );

        if (empty($invoices)) {
            return;
        }

        $this->renderAbschlagTable($pdf, $invoices);
    }

    /**
     * Render the Abschlagsrechnung table onto the PDF
     *
     * @param Briefpapier $pdf
     * @param array $invoices
     */
    private function renderAbschlagTable($pdf, array $invoices): void
    {
        if ($pdf->GetY() > 220) {
            $pdf->AddPage();
            $pdf->SetY(50);
        }

        $pdf->Ln(6);
        $xStart = $pdf->GetX();
        $font = $pdf->GetFont();

        $pdf->SetFont($font, 'B', 8);
        $pdf->Cell(180, 5, $pdf->app->erp->ReadyForPDF('Tabelle für Abschlagsrechnung'), 0, 1, 'L');
        $pdf->Ln(1);

        $wBeleg = 45;
        $wNetto = 22;
        $wMwst = 22;
        $wBrutto = 23;
        $wBezahlt = 22;
        $wEntMwst = 23;
        $wOffen = 23;

        // Table Header
        $pdf->SetFont($font, 'B', 8);
        $pdf->Cell($wBeleg, 5, $pdf->app->erp->ReadyForPDF('Belegnummer'), 0, 0, 'L');
        $pdf->Cell($wNetto, 5, $pdf->app->erp->ReadyForPDF('Netto'), 0, 0, 'R');
        $pdf->Cell($wMwst, 5, $pdf->app->erp->ReadyForPDF('MwSt:'), 0, 0, 'R');
        $pdf->Cell($wBrutto, 5, $pdf->app->erp->ReadyForPDF('Brutto'), 0, 0, 'R');
        $pdf->Cell($wBezahlt, 5, $pdf->app->erp->ReadyForPDF('bezahlt'), 0, 0, 'R');
        $pdf->Cell($wEntMwst, 5, $pdf->app->erp->ReadyForPDF('ent. MwSt.'), 0, 0, 'R');
        $pdf->Cell($wOffen, 5, $pdf->app->erp->ReadyForPDF('offen'), 0, 1, 'R');

        $yLine = $pdf->GetY();
        $pdf->Line($xStart, $yLine, $xStart + 180, $yLine);

        $sumNetto = 0.0;
        $sumMwst = 0.0;
        $sumBrutto = 0.0;
        $sumBezahlt = 0.0;
        $sumEntMwst = 0.0;
        $sumOffen = 0.0;

        foreach ($invoices as $inv) {
            $recId = (int)$inv['rechnung'];
            $brutto = (float)$inv['soll'];
            $netto = (float)$pdf->app->erp->ANABREGSNeuberechnenGesamtsumme($recId, 'rechnung', true);
            $mwst = round($brutto - $netto, 2);
            $bezahlt = (float)$inv['ist'];
            $entMwst = ($brutto > 0 && $bezahlt > 0) ? round($mwst * ($bezahlt / $brutto), 2) : 0.00;
            $offen = max(0.0, round($brutto - $bezahlt, 2));

            $sumNetto += $netto;
            $sumMwst += $mwst;
            $sumBrutto += $brutto;
            $sumBezahlt += $bezahlt;
            $sumEntMwst += $entMwst;
            $sumOffen += $offen;

            $pdf->SetFont($font, '', 8);

            // Line 1: Title + amounts
            $pdf->Cell($wBeleg, 4, $pdf->app->erp->ReadyForPDF($inv['bezeichnung']), 0, 0, 'L');
            $pdf->Cell($wNetto, 4, number_format($netto, 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell($wMwst, 4, number_format($mwst, 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell($wBrutto, 4, number_format($brutto, 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell($wBezahlt, 4, number_format($bezahlt, 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell($wEntMwst, 4, number_format($entMwst, 2, ',', '.'), 0, 0, 'R');
            $pdf->Cell($wOffen, 4, number_format($offen, 2, ',', '.'), 0, 1, 'R');

            // Line 2: Belegnummer & date
            $belegSub = ($inv['belegnr'] ? $inv['belegnr'] . ' ' : '') . 'vom ' . $inv['datum_formatiert'];
            $pdf->Cell($wBeleg, 4, $pdf->app->erp->ReadyForPDF($belegSub), 0, 1, 'L');
        }

        $yLine = $pdf->GetY();
        $pdf->Line($xStart, $yLine, $xStart + 180, $yLine);

        // Total row
        $waehrung = $pdf->waehrung ?: 'EUR';
        $pdf->SetFont($font, 'B', 8);
        $pdf->Cell($wBeleg, 5, $pdf->app->erp->ReadyForPDF('alle Beträge ' . $waehrung), 0, 0, 'L');
        $pdf->Cell($wNetto, 5, number_format($sumNetto, 2, ',', '.'), 0, 0, 'R');
        $pdf->Cell($wMwst, 5, number_format($sumMwst, 2, ',', '.'), 0, 0, 'R');
        $pdf->Cell($wBrutto, 5, number_format($sumBrutto, 2, ',', '.'), 0, 0, 'R');
        $pdf->Cell($wBezahlt, 5, number_format($sumBezahlt, 2, ',', '.'), 0, 0, 'R');
        $pdf->Cell($wEntMwst, 5, number_format($sumEntMwst, 2, ',', '.'), 0, 0, 'R');
        $pdf->Cell($wOffen, 5, number_format($sumOffen, 2, ',', '.'), 0, 1, 'R');

        $pdf->Ln(2);
    }

    /**
     * Setup order navigation tabs
     */
    private function setupAuftragMenu(int $id, string $status, string $belegnr, string $kundenname): void
    {
        $this->app->Tpl->Set('KURZUEBERSCHRIFT2', htmlspecialchars("ABSCHLAGSRECHNUNG " . $kundenname . " AUFTRAG " . $belegnr));

        if ($status === 'angelegt') {
            $this->app->erp->MenuEintrag("index.php?module=auftrag&action=freigabe&id=$id", "Freigabe");
        }

        $this->app->erp->MenuEintrag("index.php?module=auftrag&action=edit&id=$id", "Details");

        $anzahldateien = $this->app->erp->AnzahlDateien('Auftrag', $id);
        $anzahldateienStr = $anzahldateien > 0 ? ' (' . $anzahldateien . ')' : '';
        $this->app->erp->MenuEintrag("index.php?module=auftrag&action=dateien&id=$id", 'Dateien' . $anzahldateienStr);

        if ($this->app->erp->Firmendaten('auftrag_eantab') == '1') {
            $this->app->erp->MenuEintrag('index.php?module=auftrag&action=ean&id=' . $id, 'Barcodescanner');
        }

        // Active tab
        $this->app->erp->MenuEintrag("index.php?module=abschlagsrechnung&action=auftraglist&id=$id", 'Abschlagsrechnung');

        $this->app->erp->MenuEintrag("index.php?module=auftrag&action=edit&id=$id", 'Zur&uuml;ck zum Auftrag');
    }

    /**
     * Resolve tax rate percentage for an article
     */
    private function resolveArticleTaxRate($steuersatzType, int $rechnungId): float
    {
        if ($steuersatzType === 'ermaessigt') {
            return (float)$this->app->erp->GetSteuersatzErmaessigt(false, $rechnungId, 'rechnung');
        }
        if ($steuersatzType === 'starkermaessigt') {
            return (float)$this->app->erp->GetSteuersatzStarkErmaessigt(false, $rechnungId, 'rechnung');
        }
        if ($steuersatzType === 'befreit') {
            return 0.0;
        }
        return (float)$this->app->erp->GetSteuersatzNormal(false, $rechnungId, 'rechnung');
    }

    private function getParam(string $name, $default = ''): string
    {
        $val = $this->app->Secure->GetGET($name);
        if ($val === '' || $val === null) {
            $val = $_GET[$name] ?? $default;
        }
        return (string)$val;
    }

    private function getPostParam(string $name, $default = ''): string
    {
        $val = $this->app->Secure->GetPOST($name);
        if ($val === '' || $val === null) {
            $val = $_POST[$name] ?? $default;
        }
        return (string)$val;
    }
}
