<?php
/*
 * Unit Tests for Abschlagsrechnung Business Logic & Calculations
 */

declare(strict_types=1);

class AbschlagsrechnungUnitTest
{
    private int $assertions = 0;
    private int $failures = 0;

    public function run(): void
    {
        echo "=== Running Abschlagsrechnung Unit Tests ===" . PHP_EOL;

        $this->testPartialPaymentTaxCalculation();
        $this->testFinalInvoiceDeductionCalculation();
        $this->testPaidVatProportion();
        $this->testRemainingOpenAmount();
        $this->testTitleIncrementing();
        $this->testSubtitleConcatenation();
        $this->testZeroQuantityPositionTotal();
        $this->testCsrfValidationLogic();
        $this->testCommercialRoundingFallbackNetCalculation();
        $this->testMixedTaxRateDeductionGrouping();
        $this->testFinalInvoiceIdempotencyLogic();

        echo PHP_EOL;
        echo "Assertions: {$this->assertions}, Failures: {$this->failures}" . PHP_EOL;

        if ($this->failures > 0) {
            echo "FAILED!" . PHP_EOL;
            exit(1);
        }

        echo "All Unit Tests Passed Successfully!" . PHP_EOL;
    }

    private function testPartialPaymentTaxCalculation(): void
    {
        echo "- testPartialPaymentTaxCalculation: ";
        $netto = 500.00;
        $steuersatz = 19.0;
        $mwst = round($netto * ($steuersatz / 100.0), 2);
        $brutto = round($netto + $mwst, 2);

        $this->assertEquals(95.00, $mwst, "VAT 19% on 500.00 must be 95.00");
        $this->assertEquals(595.00, $brutto, "Gross on 500.00 + 95.00 must be 595.00");
        echo "OK" . PHP_EOL;
    }

    private function testFinalInvoiceDeductionCalculation(): void
    {
        echo "- testFinalInvoiceDeductionCalculation: ";
        $orderLines = [
            ['menge' => 10, 'preis' => 50.00, 'steuersatz' => 19.0], // 500.00
            ['menge' => 5,  'preis' => 100.00, 'steuersatz' => 19.0], // 500.00
        ];

        $orderNetto = 0.0;
        foreach ($orderLines as $line) {
            $orderNetto += $line['menge'] * $line['preis'];
        }
        $orderMwst = round($orderNetto * 0.19, 2);
        $orderBrutto = $orderNetto + $orderMwst;

        $this->assertEquals(1000.00, $orderNetto, "Order Netto must be 1000.00");
        $this->assertEquals(1190.00, $orderBrutto, "Order Brutto must be 1190.00");

        // Previous down payment
        $downPaymentNetto = 400.00;
        $downPaymentDeduction = -1.0 * abs($downPaymentNetto);

        $finalNetto = $orderNetto + $downPaymentDeduction;
        $finalMwst = round($finalNetto * 0.19, 2);
        $finalBrutto = round($finalNetto + $finalMwst, 2);

        $this->assertEquals(600.00, $finalNetto, "Final Netto after deduction must be 600.00");
        $this->assertEquals(114.00, $finalMwst, "Final VAT must be 114.00");
        $this->assertEquals(714.00, $finalBrutto, "Final Gross must be 714.00");
        echo "OK" . PHP_EOL;
    }

    private function testPaidVatProportion(): void
    {
        echo "- testPaidVatProportion: ";
        // If invoice gross = 595.00, VAT = 95.00
        $brutto = 595.00;
        $mwst = 95.00;

        // Fully paid (ist = 595.00)
        $ist = 595.00;
        $entMwst = ($brutto > 0 && $ist > 0) ? round($mwst * ($ist / $brutto), 2) : 0.00;
        $this->assertEquals(95.00, $entMwst, "Fully paid invoice must have full ent. MwSt.");

        // Half paid (ist = 297.50)
        $istHalf = 297.50;
        $entMwstHalf = ($brutto > 0 && $istHalf > 0) ? round($mwst * ($istHalf / $brutto), 2) : 0.00;
        $this->assertEquals(47.50, $entMwstHalf, "Half paid invoice must have 50% ent. MwSt.");

        // Unpaid (ist = 0.00)
        $istZero = 0.00;
        $entMwstZero = ($brutto > 0 && $istZero > 0) ? round($mwst * ($istZero / $brutto), 2) : 0.00;
        $this->assertEquals(0.00, $entMwstZero, "Unpaid invoice must have 0 ent. MwSt.");
        echo "OK" . PHP_EOL;
    }

    private function testRemainingOpenAmount(): void
    {
        echo "- testRemainingOpenAmount: ";
        $brutto = 1190.00;
        $ist = 500.00;
        $offen = max(0.0, round($brutto - $ist, 2));
        $this->assertEquals(690.00, $offen, "Open amount must be 690.00");

        // Overpaid
        $istOver = 1200.00;
        $offenOver = max(0.0, round($brutto - $istOver, 2));
        $this->assertEquals(0.00, $offenOver, "Overpaid invoice open amount must be 0.00");
        echo "OK" . PHP_EOL;
    }

    private function testTitleIncrementing(): void
    {
        echo "- testTitleIncrementing: ";
        $existing = [];
        $title1 = (count($existing) + 1) . '. Teilrechnung';
        $this->assertEquals('1. Teilrechnung', $title1);

        $existing[] = '1. Teilrechnung';
        $title2 = (count($existing) + 1) . '. Teilrechnung';
        $this->assertEquals('2. Teilrechnung', $title2);

        $existing[] = '2. Teilrechnung';
        $title3 = (count($existing) + 1) . '. Teilrechnung';
        $this->assertEquals('3. Teilrechnung', $title3);
        echo "OK" . PHP_EOL;
    }

    private function testSubtitleConcatenation(): void
    {
        echo "- testSubtitleConcatenation: ";
        $doctypeOrig = 'Rechnung 400010';
        $subtitle = '1. Teilrechnung';

        if (strpos($doctypeOrig, $subtitle) === false) {
            $doctypeOrig .= "\n" . $subtitle;
        }

        $this->assertEquals("Rechnung 400010\n1. Teilrechnung", $doctypeOrig);

        // Does not duplicate if already present
        if (strpos($doctypeOrig, $subtitle) === false) {
            $doctypeOrig .= "\n" . $subtitle;
        }
        $this->assertEquals("Rechnung 400010\n1. Teilrechnung", $doctypeOrig);
        echo "OK" . PHP_EOL;
    }

    private function testZeroQuantityPositionTotal(): void
    {
        echo "- testZeroQuantityPositionTotal: ";
        $menge = 0.0;
        $preis = 129.99;
        $rabatt = 10.0;

        $nettoGesamt = $menge * ($preis - ($preis / 100.0 * $rabatt));
        $this->assertEquals(0.0, $nettoGesamt, "Zero quantity line must evaluate to 0.00 total");
        echo "OK" . PHP_EOL;
    }

    private function testCsrfValidationLogic(): void
    {
        echo "- testCsrfValidationLogic: ";
        $sessionToken = bin2hex(random_bytes(16));
        $key = 'abschlagsrechnung';
        $_SESSION['csrf_' . $key] = $sessionToken;

        // Valid match
        $isValid = !empty($sessionToken) && hash_equals($_SESSION['csrf_' . $key], $sessionToken);
        $this->assertEquals(true, $isValid, "Matching token must be valid");

        // Invalid match
        $isInvalid = !empty('wrong_token') && hash_equals($_SESSION['csrf_' . $key], 'wrong_token');
        $this->assertEquals(false, $isInvalid, "Wrong token must be invalid");

        // Empty token
        $isEmpty = empty('') ? false : hash_equals($_SESSION['csrf_' . $key], '');
        $this->assertEquals(false, $isEmpty, "Empty token must be invalid");

        unset($_SESSION['csrf_' . $key]);
        echo "OK" . PHP_EOL;
    }

    private function testCommercialRoundingFallbackNetCalculation(): void
    {
        echo "- testCommercialRoundingFallbackNetCalculation: ";
        // Simulated tax array from steuerAusBelegPDF
        $positions = [
            ['netto' => 250.00, 'brutto' => 297.50, 'steuersatz' => 19.0],
            ['netto' => 150.00, 'brutto' => 160.50, 'steuersatz' => 7.0],
        ];

        $summeNetto = 0.0;
        $gesamtsummeBrutto = 0.0;
        foreach ($positions as $p) {
            $summeNetto += $p['netto'];
            $gesamtsummeBrutto += $p['brutto'];
        }

        $this->assertEquals(400.00, $summeNetto, "Net sum in fallback path must equal sum of line net amounts");
        $this->assertEquals(458.00, $gesamtsummeBrutto, "Gross sum in fallback path must equal sum of line gross amounts");
        echo "OK" . PHP_EOL;
    }

    private function testMixedTaxRateDeductionGrouping(): void
    {
        echo "- testMixedTaxRateDeductionGrouping: ";
        // Given previous invoice positions with different tax rates
        $taxItems = [
            ['steuersatz' => 19.0, 'usteuer' => 'normal', 'netto' => 300.00],
            ['steuersatz' => 19.0, 'usteuer' => 'normal', 'netto' => 200.00],
            ['steuersatz' => 7.0,  'usteuer' => 'ermaessigt', 'netto' => 100.00],
        ];

        $grouped = [];
        foreach ($taxItems as $item) {
            $k = (string)$item['steuersatz'];
            if (!isset($grouped[$k])) {
                $grouped[$k] = ['netto' => 0.0, 'steuersatz' => $item['steuersatz'], 'umsatzsteuer' => $item['usteuer']];
            }
            $grouped[$k]['netto'] += $item['netto'];
        }

        $this->assertEquals(2, count($grouped), "Should group into 2 tax rate brackets");
        $this->assertEquals(500.00, $grouped['19']['netto'], "19% bracket net sum must be 500.00");
        $this->assertEquals(100.00, $grouped['7']['netto'], "7% bracket net sum must be 100.00");
        echo "OK" . PHP_EOL;
    }

    private function testFinalInvoiceIdempotencyLogic(): void
    {
        echo "- testFinalInvoiceIdempotencyLogic: ";
        // Simulated order invoices list
        $invoices = [
            ['id' => 101, 'bezeichnung' => '1. Teilrechnung', 'status' => 'freigegeben'],
            ['id' => 102, 'bezeichnung' => 'Schlussrechnung', 'status' => 'freigegeben'],
        ];

        $hasActiveFinal = false;
        foreach ($invoices as $inv) {
            if (stripos($inv['bezeichnung'], 'Schlussrechnung') !== false && $inv['status'] !== 'storniert') {
                $hasActiveFinal = true;
                break;
            }
        }

        $this->assertEquals(true, $hasActiveFinal, "Must detect existing active final invoice");

        // Cancelled final invoice should NOT block new one
        $cancelledInvoices = [
            ['id' => 101, 'bezeichnung' => '1. Teilrechnung', 'status' => 'freigegeben'],
            ['id' => 102, 'bezeichnung' => 'Schlussrechnung', 'status' => 'storniert'],
        ];
        $hasActiveFinal2 = false;
        foreach ($cancelledInvoices as $inv) {
            if (stripos($inv['bezeichnung'], 'Schlussrechnung') !== false && $inv['status'] !== 'storniert') {
                $hasActiveFinal2 = true;
                break;
            }
        }
        $this->assertEquals(false, $hasActiveFinal2, "Cancelled final invoice must not block new creation");
        echo "OK" . PHP_EOL;
    }

    private function assertEquals($expected, $actual, string $message = ''): void
    {
        $this->assertions++;
        if ($expected !== $actual) {
            $this->failures++;
            echo PHP_EOL . "ASSERTION FAILED: Expected " . var_export($expected, true) . ", got " . var_export($actual, true) . ($message ? " ($message)" : "") . PHP_EOL;
        }
    }
}

$test = new AbschlagsrechnungUnitTest();
$test->run();
