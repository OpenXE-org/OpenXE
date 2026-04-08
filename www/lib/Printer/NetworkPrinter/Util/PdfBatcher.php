<?php

/**
 * PdfBatcher fasst zwei A5-Labels auf eine A4-Seite zusammen.
 *
 * Nutzt eine Datei-basierte Warteschlange pro Drucker+User. Bei jedem
 * printDocument()-Aufruf wird geprueft ob ein zweites Label innerhalb
 * des Timeouts eintrifft; wenn ja, werden beide auf einem A4-Blatt
 * kombiniert (Label 1 oben, Label 2 unten im Hochformat).
 *
 * Bei Einzellabels (oder bei abgelaufenem Timeout) wird das einzelne
 * Label allein auf der oberen Haelfte eines A4-Blattes gedruckt.
 *
 * Verwendet FPDI (bereits in OpenXE unter www/lib/pdf/) fuer PDF-Import.
 */
class PdfBatcher
{
    /** @var string Verzeichnis fuer Queue-Dateien */
    private $queueDir;

    /** @var int Timeout in Sekunden */
    private $timeout;

    /** @var int Drucker-ID */
    private $printerId;

    /** @var int User-ID */
    private $userId;

    /** @var string Rotation-Modus: auto|none|cw|ccw */
    private $rotation = 'auto';

    /**
     * @param string $queueDir Basis-Verzeichnis fuer Queue-Dateien
     * @param int    $printerId
     * @param int    $userId
     * @param int    $timeout Sekunden (Default: 30)
     */
    public function __construct(string $queueDir, int $printerId, int $userId, int $timeout = 30)
    {
        $this->queueDir = rtrim($queueDir, '/\\');
        $this->printerId = $printerId;
        $this->userId = $userId;
        $this->timeout = $timeout;
    }

    /**
     * Setzt den Rotation-Modus fuer Label-Platzierung.
     *
     * @param string $rotation auto|none|cw|ccw
     */
    public function setRotation(string $rotation): void
    {
        if (in_array($rotation, ['auto', 'none', 'cw', 'ccw'], true)) {
            $this->rotation = $rotation;
        }
    }

    /**
     * Verarbeitet ein Label und gibt den Pfad zum finalen PDF zurueck
     * (entweder ein kombiniertes A4 mit 2 Labels oder null wenn das Label
     * in die Queue gestellt wurde und noch nicht gedruckt werden soll).
     *
     * @param string $currentPdfPath Pfad zum aktuellen Label-PDF
     *
     * @return string|null Pfad zum zu druckenden PDF, oder null wenn Label gequeued wurde
     */
    public function process(string $currentPdfPath): ?string
    {
        $pendingFile = $this->getPendingFile();
        $timestampFile = $this->getTimestampFile();

        // Pruefen ob pending Label existiert
        if (is_file($pendingFile) && is_file($timestampFile)) {
            $pendingTs = (int)@file_get_contents($timestampFile);
            $age = time() - $pendingTs;

            if ($age < $this->timeout) {
                // Beide Labels kombinieren
                $combined = $this->combineTwoLabels($pendingFile, $currentPdfPath);
                $this->clearQueue();
                return $combined;
            }

            // Stale: altes Label einzeln drucken lassen, aktuelles als neues pending
            $staleSingle = $this->combineSingleLabel($pendingFile);
            $this->storePending($currentPdfPath);
            return $staleSingle;
        }

        // Kein pending: aktuelles Label in Queue legen
        $this->storePending($currentPdfPath);
        return null;
    }

    /**
     * Gibt alle abgelaufenen Queue-Eintraege als Liste zurueck und
     * leert diese Eintraege. Sollte periodisch oder per Cron aufgerufen
     * werden um verwaiste Labels nach langer Inaktivitaet zu drucken.
     *
     * @return array Liste von [printer_id, user_id, pdf_path] Arrays
     */
    public function flushStaleGlobal(): array
    {
        $flushed = [];
        if (!is_dir($this->queueDir)) {
            return $flushed;
        }

        $handle = @opendir($this->queueDir);
        if ($handle === false) {
            return $flushed;
        }

        $now = time();
        while (false !== ($file = readdir($handle))) {
            if (substr($file, -3) !== '.ts') {
                continue;
            }
            if (strpos($file, 'np_batch_') !== 0) {
                continue;
            }
            $tsPath = $this->queueDir . DIRECTORY_SEPARATOR . $file;
            $pdfPath = substr($tsPath, 0, -3) . '.pdf';

            $ts = (int)@file_get_contents($tsPath);
            if ($now - $ts < $this->timeout) {
                continue;
            }
            if (!is_file($pdfPath)) {
                @unlink($tsPath);
                continue;
            }

            // Dateiname parsen: np_batch_{printerId}_{userId}.ts
            $base = basename($file, '.ts');
            $parts = explode('_', $base);
            if (count($parts) !== 4) {
                continue;
            }

            $singlePdf = $this->combineSingleLabel($pdfPath);
            $flushed[] = [
                'printer_id' => (int)$parts[2],
                'user_id'    => (int)$parts[3],
                'pdf_path'   => $singlePdf,
            ];

            @unlink($pdfPath);
            @unlink($tsPath);
        }
        closedir($handle);

        return $flushed;
    }

    /**
     * Leert die Queue fuer den aktuellen Drucker+User.
     */
    public function clearQueue(): void
    {
        @unlink($this->getPendingFile());
        @unlink($this->getTimestampFile());
    }

    /**
     * Speichert das aktuelle Label als pending in der Queue.
     *
     * @param string $pdfPath
     */
    private function storePending(string $pdfPath): void
    {
        if (!is_dir($this->queueDir)) {
            @mkdir($this->queueDir, 0755, true);
        }
        @copy($pdfPath, $this->getPendingFile());
        @file_put_contents($this->getTimestampFile(), (string)time());
    }

    /**
     * Kombiniert zwei A5-Labels auf ein A4-Blatt (Hochformat).
     * Label 1 oben, Label 2 unten.
     *
     * @param string $pdf1 Erstes Label-PDF
     * @param string $pdf2 Zweites Label-PDF
     *
     * @return string Pfad zum kombinierten A4-PDF
     */
    private function combineTwoLabels(string $pdf1, string $pdf2): string
    {
        $pdf = $this->createFpdi();
        $pdf->AddPage('P', 'A4');

        // A4 Hochformat: 210 breit, 297 hoch
        // Obere Haelfte: (0, 0) bis (210, 148.5)
        // Untere Haelfte: (0, 148.5) bis (210, 297)
        $this->placeLabelOnPage($pdf, $pdf1, 0, 0, 210, 148.5);
        $this->placeLabelOnPage($pdf, $pdf2, 0, 148.5, 210, 148.5);

        $outPath = $this->makeOutputPath('combined');
        $pdf->Output($outPath, 'F');
        return $outPath;
    }

    /**
     * Erstellt ein A4-PDF mit einem einzelnen Label auf der oberen Haelfte.
     *
     * @param string $pdfPath Label-PDF
     *
     * @return string Pfad zum A4-PDF
     */
    private function combineSingleLabel(string $pdfPath): string
    {
        $pdf = $this->createFpdi();
        $pdf->AddPage('P', 'A4');

        $this->placeLabelOnPage($pdf, $pdfPath, 0, 0, 210, 148.5);

        $outPath = $this->makeOutputPath('single');
        $pdf->Output($outPath, 'F');
        return $outPath;
    }

    /**
     * Platziert ein importiertes PDF auf einer Zielflaeche des Dokuments.
     * Quell-PDFs im Hochformat werden um 90 Grad gedreht, damit sie in
     * eine Querformat-Zielflaeche (A5 Landscape) passen.
     *
     * @param object $pdf    FPDI-Instanz
     * @param string $srcPdf Pfad zum Quell-PDF
     * @param float  $x      Ziel-X (oben links)
     * @param float  $y      Ziel-Y (oben links)
     * @param float  $w      Ziel-Breite
     * @param float  $h      Ziel-Hoehe
     */
    private function placeLabelOnPage($pdf, string $srcPdf, float $x, float $y, float $w, float $h): void
    {
        $pageCount = $pdf->setSourceFile($srcPdf);
        if ($pageCount < 1) {
            return;
        }
        $tpl = $pdf->ImportPage(1);

        $size = $pdf->getTemplateSize($tpl);
        if (!is_array($size) || !isset($size['w'], $size['h']) || $size['w'] <= 0 || $size['h'] <= 0) {
            // Fallback ohne Groessen-Info: direkt strecken
            $pdf->useTemplate($tpl, $x, $y, $w, $h);
            return;
        }

        $srcW = (float)$size['w'];
        $srcH = (float)$size['h'];
        $srcIsPortrait = $srcH > $srcW;
        $dstIsPortrait = $h > $w;

        // Rotation-Entscheidung basierend auf User-Einstellung
        $rotationAngle = 0;
        switch ($this->rotation) {
            case 'none':
                $rotationAngle = 0;
                break;
            case 'cw':
                $rotationAngle = -90;
                break;
            case 'ccw':
                $rotationAngle = 90;
                break;
            case 'auto':
            default:
                // Auto: drehen wenn Quell-Orientierung nicht zur Zielflaeche passt
                if ($srcIsPortrait !== $dstIsPortrait) {
                    $rotationAngle = -90;
                }
                break;
        }

        $needsRotation = ($rotationAngle !== 0);

        if ($needsRotation) {
            // Nach 90-Grad-Rotation vertauschen sich Breite und Hoehe
            $effectiveSrcW = $srcH;
            $effectiveSrcH = $srcW;
        } else {
            $effectiveSrcW = $srcW;
            $effectiveSrcH = $srcH;
        }

        // Aspect Ratio beibehalten, in Zielflaeche einpassen
        $srcRatio = $effectiveSrcW / $effectiveSrcH;
        $dstRatio = $w / $h;

        if ($srcRatio > $dstRatio) {
            $useW = $w;
            $useH = $w / $srcRatio;
        } else {
            $useH = $h;
            $useW = $h * $srcRatio;
        }
        $offsetX = ($w - $useW) / 2;
        $offsetY = ($h - $useH) / 2;

        $placeX = $x + $offsetX;
        $placeY = $y + $offsetY;

        if ($needsRotation && method_exists($pdf, 'Rotate')) {
            // Rotation um den Mittelpunkt der Zielflaeche
            $centerX = $placeX + $useW / 2;
            $centerY = $placeY + $useH / 2;

            $pdf->Rotate($rotationAngle, $centerX, $centerY);

            // Nach Rotation muss das Template mit vertauschten Dimensionen
            // platziert werden (ungedreht gesehen).
            $unrotatedW = $useH;
            $unrotatedH = $useW;
            $rotatedX = $centerX - $unrotatedW / 2;
            $rotatedY = $centerY - $unrotatedH / 2;

            $pdf->useTemplate($tpl, $rotatedX, $rotatedY, $unrotatedW, $unrotatedH);

            $pdf->Rotate(0);
        } else {
            $pdf->useTemplate($tpl, $placeX, $placeY, $useW, $useH);
        }
    }

    /**
     * Erstellt eine neue FPDI-Instanz.
     * Laedt die noetigen OpenXE-PDF-Klassen falls noch nicht geladen.
     *
     * Die Klassen-Hierarchie ist:
     *   SuperFPDF -> PDF_EPS -> PDF -> PDF_Rotate -> fpdi -> fpdf_tpl -> FPDFWAWISION
     *
     * @return object
     */
    private function createFpdi()
    {
        if (!class_exists('PDF_EPS') || !class_exists('FPDFWAWISION')) {
            $rootDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
            $pdfDir = $rootDir . '/www/lib/pdf';

            // FPDFWAWISION muss vor fpdf_tpl.php geladen werden
            if (!class_exists('FPDFWAWISION')) {
                if (is_file($pdfDir . '/fpdf_3.php')) {
                    require_once $pdfDir . '/fpdf_3.php';
                } elseif (is_file($pdfDir . '/fpdf.php')) {
                    require_once $pdfDir . '/fpdf.php';
                }
            }

            // Restliche Kette: fpdf_final -> rotation -> fpdi -> fpdf_tpl
            if (!class_exists('PDF_EPS') && is_file($pdfDir . '/fpdf_final.php')) {
                require_once $pdfDir . '/fpdf_final.php';
            }
        }

        if (!class_exists('SuperFPDF')) {
            $rootDir = dirname(dirname(dirname(dirname(dirname(__DIR__)))));
            $superFpdfPath = $rootDir . '/www/lib/dokumente/class.superfpdf.php';
            if (is_file($superFpdfPath)) {
                require_once $superFpdfPath;
            }
        }

        if (class_exists('SuperFPDF')) {
            return new SuperFPDF('P', 'mm', 'A4');
        }
        if (class_exists('PDF_EPS')) {
            return new PDF_EPS('P', 'mm', 'A4');
        }

        throw new \RuntimeException('Keine FPDI-kompatible PDF-Klasse gefunden');
    }

    /**
     * @return string
     */
    private function getPendingFile(): string
    {
        return $this->queueDir . DIRECTORY_SEPARATOR
            . 'np_batch_' . $this->printerId . '_' . $this->userId . '.pdf';
    }

    /**
     * @return string
     */
    private function getTimestampFile(): string
    {
        return $this->queueDir . DIRECTORY_SEPARATOR
            . 'np_batch_' . $this->printerId . '_' . $this->userId . '.ts';
    }

    /**
     * @param string $suffix
     * @return string
     */
    private function makeOutputPath(string $suffix): string
    {
        if (!is_dir($this->queueDir)) {
            @mkdir($this->queueDir, 0755, true);
        }
        return $this->queueDir . DIRECTORY_SEPARATOR
            . 'np_out_' . $this->printerId . '_' . $this->userId
            . '_' . $suffix . '_' . uniqid('', true) . '.pdf';
    }
}
