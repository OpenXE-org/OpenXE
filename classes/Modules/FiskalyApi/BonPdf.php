<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi;

use TCPDF;

class BonPdf
{
    public const CHAR_WIDTH = 2;

    public const FONTSIZE_NORMAL = 4;

    public const FONT_SIZE_BIG = 5;

    public const ALIGNMENT_LEFT = 0;

    public const ALIGNMENT_CENTER = 1;

    public const ALIGNMENT_RIGHT = 2;

    public const QR_SIZE = 50;

    public const MARGIN_LEFT = 20;

    public const MARGIN_TOP = 20;

    /** @var TCPDF $pdf */
    private $pdf;

    /**
     * BonPdf constructor.
     */
    public function __construct()
    {
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }

    private $column = 0;

    private $actualFontSize;

    private $isBold = false;

    private $actualAlignment;

    /**
     * @param array $bonPrinter
     */
    public function draw(array $bonPrinter): void
    {
        $this->actualFontSize = self::FONTSIZE_NORMAL;
        $this->actualAlignment = self::ALIGNMENT_LEFT;
        $this->pdf->AddPage();
        $this->pdf->SetMargins(self::MARGIN_LEFT, self::MARGIN_TOP);
        $this->pdf->SetFont('pdfacourier', $this->isBold ? 'B' : '', $this->actualFontSize);
        $this->pdf->SetX(self::MARGIN_LEFT);
        foreach($bonPrinter as $command) {
            switch($command['type']) {
                case 'text':
                    $this->drawText($command['value']);
                    break;
                case 'font':
                    $this->setBold(empty($command['value']));
                    break;
                case 'justification':
                    $this->setAlignment((int)$command['value']);
                    break;
                case 'print_mode':
                    $this->setFontWeight($command['value']);
                    break;
                case 'qr_code':
                    $this->drawQrCode($command['value']);
                    break;
            }
        }
    }

    /**
     * @return string
     */
    public function Output(): string
    {
        return $this->pdf->Output('', 'S');
    }

    /**
     * @param string $code
     */
    private function drawQrCode(string $code): void
    {
        $y = $this->pdf->GetY();
        if($y > 220) {
            $this->pdf->AddPage();
            $y = $this->pdf->GetY();
        }
        $style = [
            'border' => 0,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0,0,0),
            'bgcolor' => false,
            'module_width' => 1,
            'module_height' => 1
        ];
        $this->pdf->write2DBarcode($code, 'QRCODE,L', self::MARGIN_LEFT, $y, 70, 70, $style, 'N');
    }

    private function setAlignment(int $alignment): void
    {
        $this->actualAlignment = $alignment;
    }

    private function getAlignmentCode(): string
    {
        switch ($this->actualAlignment) {
            case self::ALIGNMENT_RIGHT:
                return 'R';
            case self::ALIGNMENT_CENTER:
                return 'C';
        }

        return 'L';
    }

    private function drawText(string $text): void
    {
        $chars = mb_str_split(str_replace("\r\n", "\r", $text), 1, 'UTF-8');
        foreach($chars as $char) {
            if($char === "\r" || $char === "\n") {
                $this->pdf->Ln(self::FONTSIZE_NORMAL);
                $this->pdf->SetX(self::MARGIN_LEFT);
                $this->column = 0;
                continue;
            }
            $this->pdf->Cell(self::CHAR_WIDTH, $this->actualFontSize, $char,0,0, $this->getAlignmentCode());
            $this->column++;
        }
    }

    /**
     * @param $fontValue
     */
    private function setFontWeight($fontValue): void
    {
        if(!empty($fontValue)) {
            $this->actualFontSize = self::FONT_SIZE_BIG;
            return;
        }
        $this->actualFontSize = self::FONTSIZE_NORMAL;
    }

    private function setBold(bool $isBold): void
    {
        $this->isBold = $isBold;
        $this->pdf->SetFont('pdfacourier', $this->isBold ? 'B' : '', $this->actualFontSize);
    }
}
