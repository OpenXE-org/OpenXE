<?php

namespace Xentral\Components\Document;

use Exception;

/**
 * Standardized Document Data and Calculation Service.
 *
 * Implements EN 16931 (XRechnung / ZUGFeRD) compliant arithmetic and provides a
 * unified, precalculated document data model (Single Source of Truth) for:
 * - PDF generation (FPDF)
 * - E-Invoicing (XRechnung UBL, ZUGFeRD / Factur-X CII)
 * - EDI / EDIFACT
 * - Online shop exports
 * - Remote / REST APIs
 *
 * Replaces redundant, conflicting calculation loops across disparate modules.
 */
class DocumentDataService
{
    /**
     * Supported standard document types.
     */
    public const SUPPORTED_DOCTYPES = [
        'rechnung',
        'gutschrift',
        'auftrag',
        'angebot',
        'bestellung',
        'anfrage',
        'preisanfrage',
        'proformarechnung',
    ];

    /**
     * Document type configuration registry.
     */
    public const DOCTYPE_CONFIG = [
        'rechnung' => [
            'table' => 'rechnung',
            'positions_table' => 'rechnung_position',
            'parent_field' => 'rechnung',
            'has_prices' => true,
        ],
        'gutschrift' => [
            'table' => 'gutschrift',
            'positions_table' => 'gutschrift_position',
            'parent_field' => 'gutschrift',
            'has_prices' => true,
        ],
        'auftrag' => [
            'table' => 'auftrag',
            'positions_table' => 'auftrag_position',
            'parent_field' => 'auftrag',
            'has_prices' => true,
        ],
        'angebot' => [
            'table' => 'angebot',
            'positions_table' => 'angebot_position',
            'parent_field' => 'angebot',
            'has_prices' => true,
        ],
        'bestellung' => [
            'table' => 'bestellung',
            'positions_table' => 'bestellung_position',
            'parent_field' => 'bestellung',
            'has_prices' => true,
        ],
        'anfrage' => [
            'table' => 'anfrage',
            'positions_table' => 'anfrage_position',
            'parent_field' => 'anfrage',
            'has_prices' => false,
        ],
        'preisanfrage' => [
            'table' => 'preisanfrage',
            'positions_table' => 'preisanfrage_position',
            'parent_field' => 'preisanfrage',
            'has_prices' => false,
        ],
        'proformarechnung' => [
            'table' => 'proformarechnung',
            'positions_table' => 'proformarechnung_position',
            'parent_field' => 'proformarechnung',
            'has_prices' => true,
        ],
    ];

    /**
     * Aliases mapped to canonical document types.
     */
    public const DOCTYPE_ALIASES = [
        'proforma' => 'proformarechnung',
        'creditnote' => 'gutschrift',
        'invoice' => 'rechnung',
        'order' => 'auftrag',
        'offer' => 'angebot',
        'quote' => 'angebot',
        'purchaseorder' => 'bestellung',
    ];

    /** @var \ApplicationCore */
    protected $app;

    /**
     * @param \ApplicationCore $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Check if a document type is supported by the registry.
     *
     * @param string $doctype
     * @return bool
     */
    public static function isSupportedDocType(string $doctype): bool
    {
        $doctype = strtolower(trim($doctype));
        $canonical = self::DOCTYPE_ALIASES[$doctype] ?? $doctype;
        return isset(self::DOCTYPE_CONFIG[$canonical]);
    }

    /**
     * Get list of all supported canonical document types.
     *
     * @return array
     */
    public static function getSupportedDocTypes(): array
    {
        return self::SUPPORTED_DOCTYPES;
    }

    /**
     * Retrieve complete, standardized document data including canonical calculations.
     *
     * @param string $doctype Document type ('rechnung', 'gutschrift', 'auftrag', 'angebot', 'bestellung', etc.)
     * @param int $id Document ID
     * @param array $options Optional calculation flags
     * @return array Standardized document array
     * @throws Exception If document is not found or invalid
     */
    public function getBelegData(string $doctype, int $id, array $options = []): array
    {
        $doctype = strtolower(trim($doctype));
        $doctype = self::DOCTYPE_ALIASES[$doctype] ?? $doctype;

        if ($id <= 0 || !isset(self::DOCTYPE_CONFIG[$doctype])) {
            throw new Exception("Unsupported or invalid document type: {$doctype} #{$id}");
        }

        $config = self::DOCTYPE_CONFIG[$doctype];
        $table = $config['table'];
        $positionsTable = $config['positions_table'];
        $parentField = $config['parent_field'];

        // 1. Fetch document header
        $kopf = $this->app->DB->SelectRow("SELECT * FROM `{$table}` WHERE `id` = " . (int)$id . " LIMIT 1");
        if (empty($kopf)) {
            throw new Exception("Document {$doctype} #{$id} not found.");
        }

        // 2. Fetch company / seller data (Rechnungssteller)
        $rechnungssteller = $this->getCompanyData();

        // 3. Fetch recipient / customer address (Adresse)
        $adresseId = (int)($kopf['adresse'] ?? 0);
        $adresse = [];
        if ($adresseId > 0) {
            $adresse = $this->app->DB->SelectRow("SELECT * FROM `adresse` WHERE `id` = {$adresseId} LIMIT 1") ?: [];
        }

        // 4. Fetch linked documents if available
        $auftrag = [];
        $auftragId = (int)($kopf['auftragid'] ?? ($kopf['auftrag'] ?? 0));
        if ($auftragId > 0) {
            $auftrag = $this->app->DB->SelectRow("SELECT * FROM `auftrag` WHERE `id` = {$auftragId} LIMIT 1") ?: [];
        }

        $lieferschein = [];
        $lieferscheinId = (int)($kopf['lieferschein'] ?? 0);
        if ($lieferscheinId > 0) {
            $lieferschein = $this->app->DB->SelectRow("SELECT * FROM `lieferschein` WHERE `id` = {$lieferscheinId} LIMIT 1") ?: [];
        }

        $rechnung = [];
        $rechnungId = (int)($kopf['rechnungid'] ?? ($kopf['rechnung'] ?? 0));
        if ($rechnungId > 0) {
            $rechnung = $this->app->DB->SelectRow("SELECT * FROM `rechnung` WHERE `id` = {$rechnungId} LIMIT 1") ?: [];
        }

        $angebot = [];
        $angebotId = (int)($kopf['angebotid'] ?? ($kopf['angebot'] ?? 0));
        if ($angebotId > 0) {
            $angebot = $this->app->DB->SelectRow("SELECT * FROM `angebot` WHERE `id` = {$angebotId} LIMIT 1") ?: [];
        }

        // 4b. Fetch linked clerk / contact person (Bearbeiter)
        $bearbeiter = [];
        $bearbeiterId = (int)($kopf['bearbeiterid'] ?? 0);
        if ($bearbeiterId > 0) {
            $bearbeiter = $this->app->DB->SelectRow(
                "SELECT id, name, vorname, email, telefon, telefax FROM `adresse` WHERE `id` = {$bearbeiterId} LIMIT 1"
            ) ?: [];
        }

        // Header additions for specific doctypes
        if ($doctype === 'rechnung') {
            $kopf['internet_bestellnummer'] = (string)$this->app->DB->Select(
                "SELECT a.internet FROM rechnung r LEFT JOIN auftrag a ON a.id = r.auftragid WHERE r.id = " . (int)$id . " LIMIT 1"
            );
        }

        // Map payment means code according to UNCL4461
        $zahlungsweise = strtolower(trim($kopf['zahlungsweise'] ?? ''));
        $paymentMeansCode = '58'; // default: SEPA credit transfer
        if (in_array($zahlungsweise, ['lastschrift', 'sepa_lastschrift', 'sepalastschrift', 'sepa'])) {
            $paymentMeansCode = '59'; // SEPA direct debit
        } elseif (in_array($zahlungsweise, ['kreditkarte', 'creditcard'])) {
            $paymentMeansCode = '48';
        } elseif ($zahlungsweise === 'paypal') {
            $paymentMeansCode = '68';
        } elseif (in_array($zahlungsweise, ['bar', 'cash'])) {
            $paymentMeansCode = '10';
        }
        $kopf['payment_means_code'] = $paymentMeansCode;

        // Calculate due date if available
        if (!empty($kopf['datum']) && isset($kopf['zahlungszieltage']) && $kopf['zahlungszieltage'] !== '') {
            $days = (int)$kopf['zahlungszieltage'];
            $kopf['faelligkeitsdatum'] = date('Y-m-d', strtotime($kopf['datum'] . " + {$days} days"));
        }

        // 5. Fetch positions via standardized query across all document types
        $positions = $this->app->DB->SelectArr(
            "SELECT p.*, a.ean FROM `{$positionsTable}` p LEFT JOIN `artikel` a ON a.id = p.artikel WHERE p.`{$parentField}` = " . (int)$id . " ORDER BY p.sort ASC, p.id ASC"
        ) ?: [];
        if (empty($positions)) {
            $positions = $this->app->DB->SelectArr(
                "SELECT * FROM `{$positionsTable}` WHERE `{$parentField}` = " . (int)$id . " ORDER BY sort ASC, id ASC"
            ) ?: [];
        }

        // 6. Calculate positions, taxes, and monetary totals using EN 16931 rules
        $calculated = $this->calculateDocument($doctype, $kopf, $positions, $adresse, $options);

        // 7. Assemble standardized result structure
        $result = [
            'doctype' => $doctype,
            'id' => $id,
            'rechnungssteller' => $rechnungssteller,
            'kopf' => $kopf,
            'adresse' => $adresse,
            'bearbeiter' => $bearbeiter,
            'auftrag' => $auftrag,
            'lieferschein' => $lieferschein,
            'rechnung' => $rechnung,
            'angebot' => $angebot,
            'positionen' => $calculated['positionen'],
            'steuern' => $calculated['steuern'],
            'summen' => $calculated['summen'],
            'summen_nach_steuersatz' => $calculated['summen_nach_steuersatz'],
            'rabatt_vorhanden' => $calculated['rabatt_vorhanden'],

            // Backwards-compatible aliases for legacy Smarty templates and reports
            'umsatz_brutto_gesamt' => $calculated['summen']['tax_inclusive_amount'],
            'steuer_gesamt' => $calculated['summen']['tax_total_amount'],
            'umsatz_netto_gesamt' => $calculated['summen']['line_extension_amount'],
        ];

        return $result;
    }

    /**
     * Calculate line amounts, VAT category breakdown, and document totals compliant with EN 16931.
     *
     * Semantic Rules:
     * - BT-131 (Invoice line net amount): round(quantity * (price - discount), 2)
     * - BT-106 (Sum of invoice line net amount): sum(BT-131)
     * - BT-116 (VAT category taxable amount): sum(BT-131 for category)
     * - BT-117 (VAT category tax amount): round(BT-116 * (rate / 100), 2)
     * - BT-110 (Invoice total VAT amount): sum(BT-117)
     * - BT-112 (Invoice total amount with VAT): BT-106 - allowances + charges + BT-110
     * - BT-115 (Amount due for payment): BT-112 - prepaid + rounding
     *
     * @param string $doctype
     * @param array $kopf
     * @param array $positions
     * @param array $adresse
     * @param array $options
     * @return array
     */
    public function calculateDocument(string $doctype, array $kopf, array $positions, array $adresse = [], array $options = []): array
    {
        $taxExempt = !empty($kopf['ust_befreit']) || (!empty($adresse['ust_befreit']) && empty($kopf['mitumsatzsteuer']));

        $processedPositions = [];
        $taxGroups = [];
        $lineExtensionAmount = 0.0;
        $optionalNetto = 0.0;
        $optionalSteuer = 0.0;
        $rabattused = false;

        foreach ($positions as $index => $pos) {
            $posId = (int)($pos['id'] ?? 0);
            $menge = (float)($pos['menge'] ?? 1);
            $rawPrice = (float)($pos['preis'] ?? 0);
            $rabatt = (float)($pos['rabatt'] ?? 0);
            $isOptional = !empty($pos['optional']);
            $isExcluded = !empty($pos['nicht_einrechnen']);

            // Determine tax rate and text
            $steuersatz = null;
            $steuertext = '';
            $erloes = '';
            if (isset($this->app->erp) && method_exists($this->app->erp, 'GetSteuerPosition') && $posId > 0) {
                $this->app->erp->GetSteuerPosition($doctype, $posId, $steuersatz, $steuertext, $erloes);
            }
            if ($steuersatz === null || $steuersatz === '' || $steuersatz < 0) {
                $steuersatz = (float)($pos['steuersatz'] ?? 0);
            } else {
                $steuersatz = (float)$steuersatz;
            }
            if ($taxExempt) {
                $steuersatz = 0.0;
            }

            // Determine tax category code according to EN 16931 UNCL5305
            $taxCategory = $this->determineTaxCategory($steuersatz, $pos, $kopf, $taxExempt);

            // Calculate unit net price after line discount (BT-146)
            $netPriceUnit = $rawPrice;
            if ($rabatt > 0) {
                $rabattused = true;
                $netPriceUnit = $rawPrice * (1.0 - ($rabatt / 100.0));
            }
            // Retain precision for unit price (up to 4 decimals allowed by EN 16931)
            $netPriceUnitRounded = round($netPriceUnit, 4);

            // Check if position already has a stored umsatz_netto_gesamt from DB
            if (isset($pos['umsatz_netto_gesamt']) && $pos['umsatz_netto_gesamt'] !== '' && $pos['umsatz_netto_gesamt'] !== null && (float)$pos['umsatz_netto_gesamt'] != 0.0) {
                $lineNetAmount = round((float)$pos['umsatz_netto_gesamt'], 2);
            } else {
                // Line net amount (BT-131): round(quantity * netUnitPrice, 2)
                $lineNetAmount = round($menge * $netPriceUnitRounded, 2);
            }

            // Line gross amount (informative)
            $lineTaxAmount = round($lineNetAmount * ($steuersatz / 100.0), 2);
            $lineGrossAmount = round($lineNetAmount + $lineTaxAmount, 2);

            $pos['steuersatz'] = $steuersatz;
            $pos['steuertext'] = $steuertext;
            $pos['steuer_kategorie'] = $taxCategory;
            $pos['erloes'] = $erloes ?: ($pos['erloese'] ?? '');
            $pos['unit_code'] = $this->determineUnitCode((string)($pos['einheit'] ?? ''));
            $pos['umsatz_netto_einzeln'] = $netPriceUnitRounded;
            $pos['umsatz_netto_gesamt'] = $lineNetAmount;
            $pos['umsatz_brutto_gesamt'] = $lineGrossAmount;
            $pos['steuerbetrag'] = $lineTaxAmount;

            $processedPositions[$index] = $pos;

            if ($isOptional) {
                $optionalNetto += $lineNetAmount;
                $optionalSteuer += $lineTaxAmount;
                continue;
            }

            if ($isExcluded) {
                continue;
            }

            $lineExtensionAmount += $lineNetAmount;

            // Group net amounts by tax rate & category for EN 16931 category-level tax computation
            $taxGroupKey = sprintf('%s_%.2f', $taxCategory, $steuersatz);
            if (!isset($taxGroups[$taxGroupKey])) {
                $taxGroups[$taxGroupKey] = [
                    'prozent' => $steuersatz,
                    'steuersatz' => $steuersatz,
                    'kategorie' => $taxCategory,
                    'steuertext' => $steuertext,
                    'umsatz_netto' => 0.0,
                    'steuer_betrag' => 0.0,
                    'umsatz_brutto' => 0.0,
                ];
            }
            $taxGroups[$taxGroupKey]['umsatz_netto'] += $lineNetAmount;
        }

        // EN 16931: Calculate tax amount per category on the aggregated taxable basis (BT-116 -> BT-117)
        $taxTotalAmount = 0.0;
        $taxMapByRate = [];
        foreach ($taxGroups as $key => $group) {
            $taxableBasis = round($group['umsatz_netto'], 2);
            $catTaxAmount = round($taxableBasis * ($group['prozent'] / 100.0), 2);
            $catGrossAmount = round($taxableBasis + $catTaxAmount, 2);

            $taxGroups[$key]['umsatz_netto'] = $taxableBasis;
            $taxGroups[$key]['steuer_betrag'] = $catTaxAmount;
            $taxGroups[$key]['umsatz_brutto'] = $catGrossAmount;
            $taxGroups[$key]['steuersatz'] = $group['prozent'];

            $taxTotalAmount += $catTaxAmount;

            $rateKey = (string)(float)$group['prozent'];
            if (!isset($taxMapByRate[$rateKey])) {
                $taxMapByRate[$rateKey] = 0.0;
            }
            $taxMapByRate[$rateKey] = round($taxMapByRate[$rateKey] + $catTaxAmount, 2);
        }

        $lineExtensionAmount = round($lineExtensionAmount, 2);
        $taxExclusiveAmount = $lineExtensionAmount; // Can adjust for document-level allowances/charges if added
        $taxTotalAmount = round($taxTotalAmount, 2);
        $taxInclusiveAmount = round($taxExclusiveAmount + $taxTotalAmount, 2);

        $payableRoundingAmount = 0.0;
        $prepaidAmount = (float)($kopf['angezahlt'] ?? 0.0);
        $payableAmount = round($taxInclusiveAmount - $prepaidAmount + $payableRoundingAmount, 2);

        $summen = [
            'line_extension_amount' => $lineExtensionAmount,
            'tax_basis_total_amount' => $taxExclusiveAmount,
            'tax_exclusive_amount' => $taxExclusiveAmount,
            'tax_total_amount' => $taxTotalAmount,
            'tax_inclusive_amount' => $taxInclusiveAmount,
            'payable_rounding_amount' => $payableRoundingAmount,
            'prepaid_amount' => $prepaidAmount,
            'payable_amount' => $payableAmount,

            'optional_netto' => round($optionalNetto, 2),
            'optional_steuer' => round($optionalSteuer, 2),
            'optional' => round($optionalNetto + $optionalSteuer, 2),

            // German labels
            'netto_gesamt' => $taxExclusiveAmount,
            'steuer_gesamt' => $taxTotalAmount,
            'brutto_gesamt' => $taxInclusiveAmount,
            'zahlbetrag' => $payableAmount,
        ];

        return [
            'positionen' => $processedPositions,
            'steuern' => array_values($taxGroups),
            'summen' => $summen,
            'summen_nach_steuersatz' => $taxMapByRate,
            'umsatz_brutto_gesamt' => $taxInclusiveAmount,
            'steuer_gesamt' => $taxTotalAmount,
            'umsatz_netto_gesamt' => $taxExclusiveAmount,
            'rabatt_vorhanden' => $rabattused
        ];
    }

    /**
     * Determine EN 16931 tax category code (UNCL5305).
     *
     * Standard categories:
     * - S: Standard / reduced rate (> 0%)
     * - Z: Zero rated goods (0%)
     * - E: Exempt from tax
     * - AE: VAT Reverse charge
     * - K: Intra-community supply
     * - G: Export outside the EU
     */
    protected function determineTaxCategory(float $rate, array $pos, array $kopf, bool $taxExempt): string
    {
        if ($taxExempt) {
            $ustBefreit = (int)($kopf['ust_befreit'] ?? ($pos['ust_befreit'] ?? 0));
            if ($ustBefreit === 1 && !empty($kopf['ustid'])) {
                return 'K'; // Intra-community supply (requires buyer VAT ID)
            }
            if ($ustBefreit === 2) {
                return 'G'; // Export outside the EU
            }
            if ($ustBefreit === 3) {
                return 'AE'; // VAT reverse charge
            }
            return 'E'; // Exempt from VAT
        }

        $posTaxType = strtolower(trim($pos['umsatzsteuer'] ?? ''));
        if ($posTaxType === 'befreit') {
            return 'E';
        }

        if ($rate > 0.0) {
            return 'S';
        }

        return 'Z';
    }

    /**
     * Map OpenXE line unit (einheit) to UN/ECE Recommendation 20 unit code.
     *
     * @param string $einheit
     * @return string
     */
    public function determineUnitCode(string $einheit): string
    {
        $e = strtolower(trim($einheit));
        switch ($e) {
            case 'std':
            case 'stunde':
            case 'stunden':
            case 'h':
            case 'hour':
            case 'hours':
            case 'hur':
                return 'HUR';
            case 'tag':
            case 'tage':
            case 'day':
            case 'days':
                return 'DAY';
            case 'monat':
            case 'monate':
            case 'month':
                return 'MON';
            case 'jahr':
            case 'jahre':
                return 'ANN';
            case 'kg':
            case 'kilogramm':
            case 'kilo':
                return 'KGM';
            case 'g':
            case 'gramm':
                return 'GRM';
            case 't':
            case 'tonne':
            case 'tonnen':
                return 'TNE';
            case 'm':
            case 'meter':
            case 'lfm':
                return 'MTR';
            case 'mm':
            case 'millimeter':
                return 'MMT';
            case 'cm':
            case 'zentimeter':
            case 'centimeter':
                return 'CMT';
            case 'km':
            case 'kilometer':
                return 'KMT';
            case 'qm':
            case 'm2':
            case 'quadratmeter':
                return 'MTK';
            case 'cbm':
            case 'm3':
            case 'kubikmeter':
                return 'MTQ';
            case 'l':
            case 'liter':
                return 'LTR';
            case 'ml':
            case 'milliliter':
                return 'MLT';
            case 'pauschal':
            case 'pauschale':
            case 'psch':
            case 'job':
                return 'XPP';
            case 'set':
            case 'satz':
                return 'SET';
            case 'paket':
            case 'pack':
            case 'packung':
            case 'pkg':
                return 'XPK';
            case 'flasche':
            case 'flaschen':
                return 'BO';
            case 'karton':
            case 'kiste':
            case 'box':
                return 'BX';
            case 'palette':
            case 'pal':
                return 'PX';
            case 'stk':
            case 'stck':
            case 'stück':
            case 'stueck':
            case 'stk.':
            case 'piece':
            case 'pieces':
            case 'pcs':
            case 'c62':
            default:
                return 'C62';
        }
    }

    /**
     * Collect standard company / seller data.
     */
    protected function getCompanyData(): array
    {
        $erp = $this->app->erp ?? null;
        if (!$erp) {
            return [];
        }

        return [
            'name' => (string)$erp->Firmendaten('name'),
            'strasse' => (string)$erp->Firmendaten('strasse'),
            'ort' => (string)$erp->Firmendaten('ort'),
            'plz' => (string)$erp->Firmendaten('plz'),
            'land' => (string)($erp->Firmendaten('land') ?: 'DE'),
            'steuernummer' => (string)$erp->Firmendaten('steuernummer'),
            'ustid' => (string)$erp->Firmendaten('ustid'),
            'bank' => (string)$erp->Firmendaten('bank'),
            'iban' => (string)$erp->Firmendaten('iban'),
            'bic' => (string)$erp->Firmendaten('swift'),
            'email' => (string)$erp->Firmendaten('email'),
            'telefon' => (string)$erp->Firmendaten('telefon'),
            'telefax' => (string)$erp->Firmendaten('telefax'),
            'handelsregister' => (string)$erp->Firmendaten('handelsregister'),
            'amtsgericht' => (string)$erp->Firmendaten('amtsgericht'),
            'gln' => (string)$erp->Firmendaten('gln'),
            'leitwegid' => (string)$erp->Firmendaten('leitwegid'),
            'glaeubigeridentnr' => (string)$erp->Firmendaten('glaeubigeridentnr'),
        ];
    }
}
