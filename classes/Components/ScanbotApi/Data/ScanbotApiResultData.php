<?php

namespace Xentral\Components\ScanbotApi\Data;

use DateTimeImmutable;

class ScanbotApiResultData
{
    /** @var string|null $iban IBAN-Nummer */
    protected $iban;

    /** @var DateTimeImmutable|null $invoiceDate Rechnungsdatum */
    protected $invoiceDate;

    /** @var string|null $invoiceNumber Rechnungsnummer */
    protected $invoiceNumber;

    /** @var float|null $totalAmount Rechnungsbetrag */
    protected $totalAmount;

    /** @var float|null $totalTax Mehrwertsteuerbetrag */
    protected $totalTax;

    /** @var string|null $currency Währung; dreistelliger ISO-Code */
    protected $currency;

    /** @var string $resultHandle Referenz-ID zum Invoice-Recognition-Task */
    protected $resultHandle;

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'currency'       => $this->currency,
            'iban'           => $this->iban,
            'invoice_date'   => $this->invoiceDate !== null ? $this->invoiceDate->format('d.m.Y') : null,
            'invoice_number' => $this->invoiceNumber,
            'result_handle'  => $this->resultHandle,
            'total_amount'   => $this->totalAmount !== null ? number_format($this->totalAmount, 2, ',', '') : null,
            'total_tax'      => $this->totalTax !== null ? number_format($this->totalTax, 2, ',', '') : null,
        ];
    }

    /**
     * @param array $data
     */
    public function SetDataFromScanbotApi(array $data)
    {
        // ID zum Zurückmelden der Ergebnisse
        if (isset($data['resultHandle']) && $data['resultHandle'] !== null) {
            $this->resultHandle = $data['resultHandle'];
        }

        // IBAN-Nummer
        if (isset($data['IBAN']) && $data['IBAN'] !== null) {
            $this->iban = (string)$data['IBAN']['value'];
        }

        // Rechnungsdatum
        if (isset($data['invoiceDate']) && $data['invoiceDate'] !== null) {
            $this->invoiceDate = new DateTimeImmutable($data['invoiceDate']['value']);
        }

        // Rechnungsnummer
        if (isset($data['invoiceNumber']) && $data['invoiceNumber'] !== null) {
            $this->invoiceNumber = (string)$data['invoiceNumber']['value'];
        }

        // Gesamtbetrag
        if (isset($data['totalAmount']) && $data['totalAmount'] !== null) {
            $this->totalAmount = (float)$data['totalAmount']['value'];
        }

        // Mehrwertsteuerbetrag
        if (isset($data['totalTax']) && $data['totalTax'] !== null) {
            $this->totalTax = (float)$data['totalTax']['value'];
        }
    }

    /**
     * @param array $data
     */
    public function SetDataFromHocrResult(array $data)
    {
        // Währung
        if ($this->currency === null && isset($data['currency']) && $data['currency'] !== null) {
            $this->currency = (string)$data['currency'];
        }

        // Rechnungsnummer
        if ($this->invoiceNumber === null && isset($data['invoice_number']) && $data['invoice_number'] !== null) {
            $this->invoiceNumber = (string)$data['invoice_number'];
        }

        // Rechnungsdatum
        if ($this->invoiceDate === null && isset($data['invoice_date']) && $data['invoice_date'] !== null) {
            $this->invoiceDate = new DateTimeImmutable($data['invoice_date']);
        }

        // Gesamtbetrag
        if ($this->totalAmount === null && isset($data['total_gross']) && $data['total_gross'] !== null) {
            $total = $data['total_gross'];
            $lastDotPos = (int)strrpos($total, '.');
            $lastCommaPos = (int)strrpos($total, ',');

            if ($lastCommaPos > $lastDotPos) {
                // Komma ist Dezimaltrenner
                $total = str_replace('.', '', $total);
                $total = str_replace(',', '.', $total);
                $this->totalAmount = (float)$total;
            } else {
                // Punkt ist Dezimaltrenner
                $total = str_replace(',', '', $total);
                $this->totalAmount = (float)$total;
            }
        }
    }
}
