<?php
/*
 * Test Suite for Abschlagsrechnung Module
 */

declare(strict_types=1);

$_SERVER['SCRIPT_FILENAME'] = dirname(__DIR__) . '/www/index.php';
require_once __DIR__ . '/../xentral_autoloader.php';
require_once __DIR__ . '/../www/eproosystem.php';
require_once __DIR__ . '/../conf/main.conf.php';

$config = \Xentral\Core\LegacyConfig\ConfigLoader::load();
$app = new erpooSystem($config);

echo "=== OpenXE Abschlagsrechnung Module Test ===" . PHP_EOL;

// 1. Check ModulVorhanden
$modulVorhanden = $app->erp->ModulVorhanden('abschlagsrechnung');
echo "1. ModulVorhanden('abschlagsrechnung'): " . ($modulVorhanden ? "PASS" : "FAIL") . PHP_EOL;
assert($modulVorhanden === true, "ModulVorhanden must return true");

// 2. Instantiate Abschlagsrechnung & verify auto-install
require_once __DIR__ . '/../www/pages/abschlagsrechnung.php';
$abschlag = new Abschlagsrechnung($app, true);
echo "2. Instantiate Abschlagsrechnung: PASS" . PHP_EOL;

$tableExists = $app->DB->CheckTableExistence('abschlagsrechnung_rechnung');
assert($tableExists === true, "Table abschlagsrechnung_rechnung must exist");
echo "   Database table existence verified: PASS" . PHP_EOL;

// 3. Verify hook registration in DB
$hook1 = (int)$app->DB->Select("SELECT COUNT(*) FROM `hook_register` WHERE `module` = 'Abschlagsrechnung' AND `function` = 'BriefpapierRenderDocumentHook1' AND `aktiv` = 1");
$hook2 = (int)$app->DB->Select("SELECT COUNT(*) FROM `hook_register` WHERE `module` = 'Abschlagsrechnung' AND `function` = 'BriefpapierRenderFooterHook2' AND `aktiv` = 1");
echo "3. Hooks in DB: Hook1=" . $hook1 . ", Hook2=" . $hook2 . " -> " . ($hook1 >= 1 && $hook2 >= 1 ? "PASS" : "FAIL") . PHP_EOL;
assert($hook1 >= 1 && $hook2 >= 1, "Hooks must be registered in hook_register");

// 4. Create Test Address and Order in DB
$app->DB->Insert("INSERT INTO `adresse` (`name`, `kundennummer`) VALUES ('Test Kunde Abschlagsrechnung', 'KD-TEST-AR')");
$adresseId = (int)$app->DB->GetInsertID();

$app->DB->Insert("INSERT INTO `auftrag` (`adresse`, `name`, `belegnr`, `status`, `datum`, `gesamtsumme`, `waehrung`, `steuersatz_normal`) VALUES ($adresseId, 'Test Kunde Abschlagsrechnung', 'AT-TEST-AR', 'freigegeben', NOW(), 1190.00, 'EUR', 19)");
$auftragId = (int)$app->DB->GetInsertID();

// Add order positions: Pos 1: 10 pcs @ 50.00 = 500.00, Pos 2: 10 pcs @ 50.00 = 500.00 (Total net 1000, gross 1190)
$app->DB->Insert("INSERT INTO `auftrag_position` (`auftrag`, `bezeichnung`, `menge`, `preis`, `umsatzsteuer`, `sort`) VALUES ($auftragId, 'Position 1', 10, 50.00, 19, 1)");
$app->DB->Insert("INSERT INTO `auftrag_position` (`auftrag`, `bezeichnung`, `menge`, `preis`, `umsatzsteuer`, `sort`) VALUES ($auftragId, 'Position 2', 10, 50.00, 19, 2)");

$app->erp->ANABREGSNeuberechnen($auftragId, 'auftrag');
echo "4. Created Test Order ID $auftragId: PASS" . PHP_EOL;

// Mock Location execute to prevent redirect exit
$savedLocation = $app->Location;
class TestLocationMock { public $target = ''; public function execute($url) { $this->target = $url; } }
$app->Location = new TestLocationMock();

// 5. CSRF Protection Test: Attempt mutating action without token
$_GET['id'] = $auftragId;
unset($_GET['token']);
$_POST['bezeichnung'] = '1. Teilrechnung';
$_POST['token'] = 'invalid_csrf_token';
$abschlag->AbschlagsrechnungTeilrechnung();
assert(strpos($app->Location->target, 'msg=') !== false, "Teilrechnung without valid token must be rejected");
echo "5. CSRF rejection on invalid token: PASS" . PHP_EOL;

// 5b. Strict POST: token via GET only must be rejected
$getOnlyToken = $abschlag->getCsrfToken();
$_GET['id'] = $auftragId;
$_GET['token'] = $getOnlyToken;
$_POST['token'] = '';
$mappingBefore = (int)$app->DB->Select("SELECT COUNT(*) FROM `abschlagsrechnung_rechnung` WHERE `auftrag` = $auftragId");
$abschlag->AbschlagsrechnungTeilrechnung();
assert(strpos($app->Location->target, 'msg=') !== false, "Teilrechnung with GET-only token must be rejected (strict POST)");
$mappingAfter = (int)$app->DB->Select("SELECT COUNT(*) FROM `abschlagsrechnung_rechnung` WHERE `auftrag` = $auftragId");
assert($mappingAfter === $mappingBefore, "GET-only token must not create a mapping");
unset($_GET['token']);
echo "5b. Strict POST (GET-only token rejected): PASS" . PHP_EOL;

// 6. Test AbschlagsrechnungTeilrechnung with valid CSRF token
$validToken = $abschlag->getCsrfToken();
$_POST['token'] = $validToken;
$_POST['posnull'] = '1';
$_POST['artikel_teilzahlung'] = '';
$_POST['artikel_teilzahlung_bezeichnung'] = 'Abschlagszahlung 50%';
$_POST['artikel_teilzahlung_beschreibung'] = '50% Anzahlung';
$_POST['artikel_teilzahlung_menge'] = '1';
$_POST['artikel_teilzahlung_preis'] = '500.00';

$abschlag->AbschlagsrechnungTeilrechnung();
echo "6. Teilrechnung executed, redirected to: " . $app->Location->target . PHP_EOL;

// Check Teilrechnung in DB
$teilrechnungLink = $app->DB->SelectRow("SELECT * FROM `abschlagsrechnung_rechnung` WHERE `auftrag` = $auftragId LIMIT 1");
assert(!empty($teilrechnungLink), "Mapping in abschlagsrechnung_rechnung must exist");
$rechnungId1 = (int)$teilrechnungLink['rechnung'];
echo "   Teilrechnung created with ID $rechnungId1, Bezeichnung: " . $teilrechnungLink['bezeichnung'] . PHP_EOL;

// Check positions of Teilrechnung: original order positions should have menge = 0, down payment line menge = 1
$positions1 = $app->DB->SelectArr("SELECT * FROM `rechnung_position` WHERE `rechnung` = $rechnungId1 ORDER BY `sort` ASC");
assert(count($positions1) === 3, "Teilrechnung should have 2 zero-qty order items + 1 down payment item");
assert((float)$positions1[0]['menge'] === 0.0, "Original item 1 must have menge = 0");
assert((float)$positions1[1]['menge'] === 0.0, "Original item 2 must have menge = 0");
assert((float)$positions1[2]['menge'] === 1.0, "Down payment item must have menge = 1");
assert((float)$positions1[2]['preis'] === 500.0, "Down payment item must have preis = 500.00");

// Check invoice totals: net 500.00, gross 595.00
$rechnung1 = $app->DB->SelectRow("SELECT * FROM `rechnung` WHERE `id` = $rechnungId1");
$netto1 = (float)$app->erp->ANABREGSNeuberechnenGesamtsumme($rechnungId1, 'rechnung', true);
$brutto1 = (float)$rechnung1['soll'];
echo "   Teilrechnung Netto: $netto1, Brutto: $brutto1 -> " . ($netto1 == 500.00 && $brutto1 == 595.00 ? "PASS" : "FAIL") . PHP_EOL;
assert($netto1 == 500.00, "Teilrechnung Netto must be 500.00");
assert($brutto1 == 595.00, "Teilrechnung Brutto must be 595.00");

// 6b. Token rotation: old token must be invalid after successful mutation
$oldTokenStillValid = $abschlag->isCsrfTokenValid($validToken);
assert($oldTokenStillValid === false, "Old CSRF token must be invalid after rotation");
echo "6b. Token rotation after Teilrechnung: PASS" . PHP_EOL;

// 7. Test AbschlagsrechnungSchlussrechnung (fresh token required after rotation)
$validToken2 = $abschlag->getCsrfToken();
$_POST['token'] = $validToken2;
$abschlag->AbschlagsrechnungSchlussrechnung();
echo "7. Schlussrechnung executed, redirected to: " . $app->Location->target . PHP_EOL;

$schlussLink = $app->DB->SelectRow("SELECT * FROM `abschlagsrechnung_rechnung` WHERE `auftrag` = $auftragId AND `bezeichnung` = 'Schlussrechnung' LIMIT 1");
assert(!empty($schlussLink), "Schlussrechnung mapping must exist");
$rechnungId2 = (int)$schlussLink['rechnung'];
echo "   Schlussrechnung created with ID $rechnungId2" . PHP_EOL;

// Check positions of Schlussrechnung: 2 original full order positions + 1 negative deduction position
$positions2 = $app->DB->SelectArr("SELECT * FROM `rechnung_position` WHERE `rechnung` = $rechnungId2 ORDER BY `sort` ASC");
assert(count($positions2) === 3, "Schlussrechnung should have 2 full order lines + 1 deduction line");

// Pos 1 & 2: menge = 10, preis = 50.00 -> total 1000.00
assert((float)$positions2[0]['menge'] === 10.0, "Pos 1 full quantity");
assert((float)$positions2[1]['menge'] === 10.0, "Pos 2 full quantity");

// Pos 3: deduction of 500.00
assert((float)$positions2[2]['preis'] === -500.0, "Pos 3 deduction price must be -500.00");
assert((float)$positions2[2]['rabatt'] === 0.0, "Deduction line rabatt column must be 0 (amount already netted)");

// Check Schlussrechnung totals: Netto = 1000 - 500 = 500.00, Brutto = 1190 - 595 = 595.00
$rechnung2 = $app->DB->SelectRow("SELECT * FROM `rechnung` WHERE `id` = $rechnungId2");
$netto2 = (float)$app->erp->ANABREGSNeuberechnenGesamtsumme($rechnungId2, 'rechnung', true);
$brutto2 = (float)$rechnung2['soll'];
echo "   Schlussrechnung Netto: $netto2, Brutto: $brutto2 -> " . ($netto2 == 500.00 && $brutto2 == 595.00 ? "PASS" : "FAIL") . PHP_EOL;
assert($netto2 == 500.00, "Schlussrechnung Netto must be 500.00");
assert($brutto2 == 595.00, "Schlussrechnung Brutto must be 595.00");

// 8. Test Idempotency: Attempt creating second Schlussrechnung for the same order
// Fresh token required: previous Schlussrechnung rotated the token, so fetch a new one
// to reach the idempotency guard (not just the CSRF guard).
$validToken3 = $abschlag->getCsrfToken();
$_POST['token'] = $validToken3;
$abschlag->AbschlagsrechnungSchlussrechnung();
assert(strpos($app->Location->target, 'msg=') !== false, "Duplicate Schlussrechnung creation must be blocked");
$schlussCount = (int)$app->DB->Select("SELECT COUNT(*) FROM `abschlagsrechnung_rechnung` WHERE `auftrag` = $auftragId AND `bezeichnung` = 'Schlussrechnung'");
assert($schlussCount === 1, "There must be exactly one active Schlussrechnung");
echo "8. Schlussrechnung Idempotency: PASS (duplicate blocked)" . PHP_EOL;

// 9. Test PDF Hook Logic: Title subtitle and Table generation
class MockPdf {
    public $doctype = 'rechnung';
    public $doctypeid;
    public $doctypeOrig = 'Rechnung 400001';
    public $waehrung = 'EUR';
    public $app;
    public $cells = [];
    public $lines = [];
    private $x = 15;
    private $y = 100;
    private $font = 'Arial';
    public function __construct($app, $id) { $this->app = $app; $this->doctypeid = $id; }
    public function GetX() { return $this->x; }
    public function GetY() { return $this->y; }
    public function SetX($x) { $this->x = $x; }
    public function SetY($y) { $this->y = $y; }
    public function GetFont() { return $this->font; }
    public function SetFont($f, $style='', $size=10) {}
    public function Ln($h=0) { $this->y += $h; }
    public function Cell($w, $h, $txt, $b=0, $ln=0, $a='L') { $this->cells[] = [$w, $h, $txt, $a]; if ($ln) $this->y += $h; }
    public function Line($x1, $y1, $x2, $y2) { $this->lines[] = [$x1, $y1, $x2, $y2]; }
}

$mockPdf = new MockPdf($app, $rechnungId1);
$abschlag->BriefpapierRenderDocumentHook1($mockPdf);
echo "9. PDF Subtitle Hook: " . json_encode($mockPdf->doctypeOrig) . " -> " . (strpos($mockPdf->doctypeOrig, "1. Teilrechnung") !== false ? "PASS" : "FAIL") . PHP_EOL;
assert(strpos($mockPdf->doctypeOrig, "1. Teilrechnung") !== false, "Document title must include 1. Teilrechnung");

$mockPdf2 = new MockPdf($app, $rechnungId2);
$abschlag->BriefpapierRenderFooterHook2($mockPdf2);
echo "10. PDF Table Hook: generated " . count($mockPdf2->cells) . " cells -> " . (count($mockPdf2->cells) >= 15 ? "PASS" : "FAIL") . PHP_EOL;
assert(count($mockPdf2->cells) >= 15, "Table should have generated headers, rows, and total cells");

// 11. Test Scoped Delete with CSRF Token (fresh token after rotations)
$arIdToDelete = (int)$teilrechnungLink['id'];
$_GET['id'] = $arIdToDelete;
$_GET['auftrag'] = $auftragId;
$validToken4 = $abschlag->getCsrfToken();
$_POST['token'] = $validToken4;
$abschlag->AbschlagsrechnungDelete();
$checkDeleted = $app->DB->Select("SELECT COUNT(*) FROM `abschlagsrechnung_rechnung` WHERE `id` = $arIdToDelete");
assert((int)$checkDeleted === 0, "Mapping must be deleted");
echo "11. Scoped Delete with CSRF: PASS" . PHP_EOL;

// 12. Clean up test records
$app->DB->Delete("DELETE FROM `abschlagsrechnung_rechnung` WHERE `auftrag` = $auftragId");
$app->DB->Delete("DELETE FROM `rechnung_position` WHERE `rechnung` IN ($rechnungId1, $rechnungId2)");
$app->DB->Delete("DELETE FROM `rechnung` WHERE `id` IN ($rechnungId1, $rechnungId2)");
$app->DB->Delete("DELETE FROM `auftrag_position` WHERE `auftrag` = $auftragId");
$app->DB->Delete("DELETE FROM `auftrag` WHERE `id` = $auftragId");
$app->DB->Delete("DELETE FROM `adresse` WHERE `id` = $adresseId");
echo "12. Test cleanup: PASS" . PHP_EOL;

echo "=== All Tests Passed Successfully! ===" . PHP_EOL;
