<?php

namespace Xentral\Modules\Report;

use SuperFPDF;
use Xentral\Modules\Report\Data\ReportData;
use Xentral\Modules\Report\Exception\InvalidArgumentException;

class ReportPdfExportService extends AbstractReportExportService
{
    /** @var string $fileExtension */
    protected $fileExtension = 'pdf';

    /**
     * @param ReportData $report
     * @param array      $parameterValues
     * @param string     $filename
     *
     * @return string
     */
    public function createPdfFileFromReport(ReportData $report, $parameterValues = [], $filename = '')
    {
        $filename = $this->generateFileName($report, $filename);
        $id = $report->getId();
        $struktur = $this->service->resolveParameters($report, $parameterValues);
        if(!$this->service->isSqlStatementAllowed($struktur))
        {
            throw new InvalidArgumentException('Resolved Query not executable.');
        }

        $colKeyMap = [];
        $colWidths = [];
        $colNames = [];
        $colAligns = [];
        $counter = 0;
        foreach ($report->getColumns() as $column) {
            $colKeyMap[$column->getKey()] = $column->getTitle();
            $colWidths[] = $column->getWidth();
            $colAligns[] = $column->getAlignment();
            if ($column->isSumColumn()) {
                $sumcolsa[] = $counter;
            }
            $counter++;
        }

        $nameaufpdf = $this->stringReadyForPdf($report->getName());
        foreach($sumcolsa as $k => $v)
        {
            $v = (int)$v;
            if($v <= 0)
            {
                unset($sumcolsa[$k]);
            }else{
                $sumcolsa[$k] = $v;
            }
        }

        define('FPDF_FONTPATH2','/../../../www/lib/pdf/font2');
        //require dirname(__DIR__).'/lib/pdf/fpdf_org.php';
        $pdfOrgPath = __DIR__.'/../../../www/lib/pdf/fpdf_org.php';

        require $pdfOrgPath;
        $pdf=new SuperFPDF();
        $pdf->AddPage();

        $pdf->SetFillColor(255,255,255);
        $pdf->SetTextColor(0);
        $pdf->SetDrawColor(0,0,0);
        $pdf->SetLineWidth(.3);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(array_sum($colWidths),7,"Bericht: $nameaufpdf (Ausdruck vom ".date("d.m.Y").")",1,0,'L',true);
        $pdf->Ln();
        $pdf->SetFont('Arial','',8);

        $sums = [];
        $prepareHeaderLine = true;
        $cw = count($colWidths);
        foreach ($this->db->yieldAll($this->service->resolveParameters($report, $parameterValues), []) as $row) {
            if($prepareHeaderLine){
                $header = [];
                foreach ($row as $columnName => $columnValue) {
                    $header[] = $colKeyMap[$columnName];
                }
                for($i=0;$i<$cw;$i++) {
                    $pdf->Cell($colWidths[$i], 7, $header[$i], 1, 0, $colAligns[$i], true);
                }
                $pdf->Ln();
                $prepareHeaderLine = false;
            }

            $columnCounter = 0;
            foreach($row as $columnKey=>$columnValue){
                $pdf->Cell($colWidths[$columnCounter],6,$this->stringReadyForPdf($columnValue),'LRTB',0,$colAligns[$columnCounter],true);
                if(!empty($sumcolsa) && in_array($columnKey+1,$sumcolsa, false))
                {
                    if(empty($sums[$columnKey])){
                        $sums[$columnKey] = 0;
                    }
                    $sums[$columnKey] += $this->sanitizeBetrag(1, $columnValue);
                }

                $columnCounter++;
            }

            for($columnCounter; $columnCounter < $cw; $columnCounter++) {
                $pdf->Cell($colWidths[$columnCounter], 6, '', 'LRTB', 0, $colAligns[$columnCounter], true);
            }
            $pdf->Ln();
        }

        if(!empty($sums)) {
            $pdf->Ln();
            for($columnCounter = 0;$columnCounter<$cw;$columnCounter++) {
                $pdf->Cell(
                    $colWidths[$columnCounter],
                    6,
                    isset($sums[$columnCounter])?$this->stringReadyForPdf(number_format($sums[$columnCounter],
                        2,
                        ',',
                        '.')):'',
                    'LRTB',
                    0,
                    $colAligns[$columnCounter],
                    true
                );
            }
        }

        //$pdf->Cell($w[1],6,$this->app->erp->LimitChar($name_de,30),'LRTB',0,'L',$fill);

        $pdf->SetFont('Arial','',8);

        $filePath = $this->genereateFilePath($filename);//sprintf('%s/%s', sys_get_temp_dir(), $filename);
        $pdf->Output($filePath, 'F');

        return $filePath;
    }

    /**
     * kopiert aus erpapi
     * @todo: prüfen ob gebraucht und ggf. auflösen
     *
     * @param      $db
     * @param      $value
     * @param null $fromform
     *
     * @return string|string[]
     */
    private function sanitizeBetrag($db,$value,$fromform = null)
    {
        // wenn . und , vorhanden dann entferne punkt
        $pos_punkt = strrpos($value, '.');
        $pos_komma = strrpos($value, ',');
        if (($pos_punkt !== false) && ($pos_komma !== false)) {
            if ($pos_punkt < $pos_komma) {
                $value = str_replace('.', '', $value);
            } else {
                $value = str_replace(',', '', $value);
            }
        }

        return str_replace(',', '.', $value);
    }

    /**
     * @param $string
     *
     * @return string
     */
    private function stringReadyForPdf($string)
    {
        return trim(
            html_entity_decode(
                str_replace(
                    ['“', '„', '–', '&rsquo;', '&apos;', 'NONBLOCKINGZERO'],
                    ['"','','-',"'","'",''],
                    $string
                ),
                ENT_QUOTES,
                'UTF-8'
            )
        );
    }
}
