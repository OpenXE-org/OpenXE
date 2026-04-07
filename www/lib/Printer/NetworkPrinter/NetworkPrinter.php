<?php

require_once __DIR__ . '/Protocol.php';
require_once __DIR__ . '/PrinterType.php';
require_once __DIR__ . '/Exception/PrinterException.php';
require_once __DIR__ . '/Exception/PrinterConnectionException.php';
require_once __DIR__ . '/Exception/PrinterCommunicationException.php';
require_once __DIR__ . '/Exception/PrinterConfigException.php';
require_once __DIR__ . '/Exception/PrinterProtocolException.php';
require_once __DIR__ . '/Driver/DriverInterface.php';
require_once __DIR__ . '/Driver/IppDriver.php';
require_once __DIR__ . '/Driver/RawDriver.php';
require_once __DIR__ . '/Driver/EscPosDriver.php';
require_once __DIR__ . '/Driver/LprDriver.php';
require_once __DIR__ . '/Status/StatusMonitor.php';
require_once __DIR__ . '/Util/ConnectionTest.php';

/**
 * Netzwerkdrucker-Plugin fuer OpenXE.
 *
 * Unterstuetzt IPP, RAW/JetDirect, ESC/POS und LPR/LPD.
 * Wird von drucker.php automatisch erkannt und von loadPrinterModul() geladen.
 */
class NetworkPrinter extends PrinterBase
{
    /**
     * Anzeigename im Drucker-Auswahlmenu.
     *
     * @return string
     */
    public static function getName(): string
    {
        return 'Netzwerkdrucker (IP)';
    }

    /**
     * Definiert die Felder fuer das automatisch generierte Einstellungsformular.
     *
     * @return array
     */
    public function SettingsStructure(): array
    {
        return [
            'host' => [
                'typ'         => 'text',
                'bezeichnung' => 'IP-Adresse / Hostname',
                'placeholder' => '192.168.1.100',
                'size'        => '40',
            ],
            'port' => [
                'typ'         => 'text',
                'bezeichnung' => 'Port',
                'placeholder' => '631',
                'size'        => '10',
            ],
            'printer_type' => [
                'typ'         => 'select',
                'bezeichnung' => 'Druckertyp',
                'optionen'    => [
                    'document' => 'Dokumentendrucker',
                    'label'    => 'Etikettendrucker',
                    'receipt'  => 'Bondrucker',
                ],
            ],
            'protocol' => [
                'typ'         => 'select',
                'bezeichnung' => 'Protokoll',
                'optionen'    => [
                    'ipp'    => 'IPP (Internet Printing Protocol)',
                    'raw'    => 'RAW / JetDirect (Port 9100)',
                    'escpos' => 'ESC/POS (Bondrucker)',
                    'lpr'    => 'LPR / LPD (RFC 1179)',
                ],
            ],
            'lpr_queue' => [
                'typ'         => 'text',
                'bezeichnung' => 'LPR Queue-Name (nur LPR)',
                'placeholder' => 'lp',
                'size'        => 20,
                'default'     => 'lp',
                'info'        => 'Standard: lp',
            ],
            'timeout' => [
                'typ'         => 'text',
                'bezeichnung' => 'Timeout in Sekunden',
                'placeholder' => '30',
                'size'        => '10',
                'default'     => '30',
            ],
            'auth_username' => [
                'typ'         => 'text',
                'bezeichnung' => 'Benutzername (optional)',
                'placeholder' => '',
                'size'        => '30',
            ],
            'auth_password' => [
                'typ'         => 'text',
                'bezeichnung' => 'Passwort (optional)',
                'placeholder' => '',
                'size'        => '30',
            ],
            'duplex' => [
                'typ'         => 'checkbox',
                'bezeichnung' => 'Duplexdruck',
                'heading'     => 'Druckoptionen (nur IPP)',
            ],
            'color' => [
                'typ'         => 'checkbox',
                'bezeichnung' => 'Farbdruck',
            ],
            'tray' => [
                'typ'         => 'select',
                'bezeichnung' => 'Papierfach',
                'optionen'    => [
                    'auto'   => 'Automatisch',
                    'tray-1' => 'Fach 1',
                    'tray-2' => 'Fach 2',
                    'manual' => 'Manuell',
                ],
            ],
            'media' => [
                'typ'         => 'select',
                'bezeichnung' => 'Papierformat',
                'optionen'    => [
                    'iso_a4_210x297mm'       => 'A4',
                    'iso_a5_148x210mm'       => 'A5',
                    'iso_a6_105x148mm'       => 'A6',
                    'na_letter_8.5x11in'     => 'Letter',
                    'na_4x6_4x6in'           => '4x6 Zoll (Label)',
                    'om_100x150mm_100x150mm' => '100x150mm (Label)',
                    'om_100x200mm_100x200mm' => '100x200mm (Label)',
                ],
                'default'     => 'iso_a4_210x297mm',
            ],
            'staple' => [
                'typ'         => 'checkbox',
                'bezeichnung' => 'Heften',
            ],
            'label_language' => [
                'typ'         => 'select',
                'bezeichnung' => 'Etikettensprache',
                'heading'     => 'Etiketten-Optionen',
                'optionen'    => [
                    'zpl'  => 'ZPL (Zebra)',
                    'epl2' => 'EPL2 (Eltron)',
                ],
            ],
            'paper_width' => [
                'typ'         => 'select',
                'bezeichnung' => 'Papierbreite',
                'heading'     => 'Bondrucker-Optionen',
                'optionen'    => [
                    '80' => '80mm',
                    '58' => '58mm',
                ],
            ],
            'auto_cut' => [
                'typ'         => 'checkbox',
                'bezeichnung' => 'Automatischer Schnitt',
                'default'     => '1',
            ],
            'test_connection' => [
                'typ'      => 'custom',
                'function' => 'renderTestButton',
            ],
        ];
    }

    /**
     * Sendet ein Dokument an den Drucker.
     *
     * @param string $dokument Dateipfad oder rohe Druckdaten
     * @param int    $anzahl   Anzahl Kopien
     *
     * @return bool true bei Erfolg, false bei Fehler
     */
    public function printDocument($dokument, $anzahl): bool
    {
        try {
            $settings = $this->getResolvedSettings();
            $this->validateSettings($settings);

            $data    = $this->loadDocumentData($dokument);
            $driver  = $this->createDriver($settings);
            $options = $this->buildPrintOptions($settings, (int)$anzahl);

            $driver->send($data, $options);

            $this->logInfo(
                sprintf(
                    'Druckauftrag erfolgreich: Drucker-ID=%d, Protokoll=%s, Host=%s, Kopien=%d',
                    $this->id,
                    $settings['protocol'],
                    $settings['host'],
                    (int)$anzahl
                )
            );

            return true;

        } catch (PrinterException $e) {
            $this->logError(
                sprintf(
                    'Druckfehler (PrinterException): Drucker-ID=%d — %s',
                    $this->id,
                    $e->getMessage()
                )
            );
            return false;

        } catch (\Exception $e) {
            $this->logError(
                sprintf(
                    'Druckfehler (Exception): Drucker-ID=%d — %s',
                    $this->id,
                    $e->getMessage()
                )
            );
            return false;
        }
    }

    /**
     * Fragt den aktuellen Druckerstatus ab.
     *
     * @return array|null Status-Array oder null bei fehlender Konfiguration
     */
    public function getStatus(): ?array
    {
        $settings = $this->getResolvedSettings();
        if (empty($settings['host'])) {
            return null;
        }

        $monitor = new StatusMonitor();
        return $monitor->getStatus($settings);
    }

    /**
     * Fuehrt einen vollstaendigen Verbindungstest durch.
     *
     * @return array Ergebnis mit success, message, details
     */
    public function testConnection(): array
    {
        $settings = $this->getResolvedSettings();

        // Validierung auch im Test-Pfad erzwingen (SSRF-Schutz)
        try {
            $this->validateSettings($settings);
        } catch (PrinterConfigException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'details' => [],
            ];
        }

        $test = new ConnectionTest();
        return $test->test($settings);
    }

    /**
     * Rendert den Verbindungsstatus als Inline-Check.
     * Wird von PrinterBase::Settings() ueber typ=custom aufgerufen.
     * Prueft beim Seitenaufbau ob der Drucker per TCP erreichbar ist
     * und zeigt das Ergebnis sofort an.
     *
     * @return string HTML mit Status-Anzeige
     */
    public function renderTestButton(): string
    {
        $settings = $this->getResolvedSettings();
        $host = $settings['host'] ?? '';
        $port = (int)($settings['port'] ?? 9100);

        if ($host === '') {
            return '<div style="padding:8px;background:#fff3cd;border:1px solid #ffc107;'
                . 'border-radius:4px;color:#856404;">'
                . '<strong>Hinweis:</strong> Bitte IP-Adresse eingeben und speichern.'
                . '</div>';
        }

        // Schneller TCP-Connect-Check (max 3s)
        $startTime = microtime(true);
        $fp = @stream_socket_client(
            sprintf('tcp://%s:%d', $host, $port),
            $errno, $errstr, 3
        );
        $connectTime = round((microtime(true) - $startTime) * 1000);

        $html = '';
        if ($fp !== false) {
            fclose($fp);
            $html .= '<div style="padding:8px;background:#d4edda;border:1px solid #c3e6cb;'
                . 'border-radius:4px;color:#155724;">'
                . '<strong>&#10003; Drucker erreichbar</strong> &mdash; '
                . htmlspecialchars($host) . ':' . $port
                . ' (' . $connectTime . 'ms)'
                . '</div>';
        } else {
            $html .= '<div style="padding:8px;background:#f8d7da;border:1px solid #f5c6cb;'
                . 'border-radius:4px;color:#721c24;">'
                . '<strong>&#10007; Drucker nicht erreichbar</strong> &mdash; '
                . htmlspecialchars($host) . ':' . $port
                . '</div>';
        }

        $html .= '<div style="margin-top:4px;color:#666;font-size:11px;">'
            . 'Testdruck: Button "Testseite drucken" oben bei Anbindung verwenden.'
            . '</div>';

        return $html;
    }

    /**
     * Liest $this->settings und ergaenzt fehlende Felder mit Standardwerten.
     *
     * @return array Vollstaendige Settings
     */
    private function getResolvedSettings(): array
    {
        $s = is_array($this->settings) ? $this->settings : [];

        if (!isset($s['host'])) {
            $s['host'] = '';
        }
        if (!isset($s['protocol']) || $s['protocol'] === '') {
            $s['protocol'] = Protocol::IPP;
        }
        if (!isset($s['port']) || (int)$s['port'] === 0) {
            $s['port'] = Protocol::getDefaultPort($s['protocol']);
        }
        $s['timeout'] = (int)($s['timeout'] ?? 30);
        if ($s['timeout'] < 1 || $s['timeout'] > 300) {
            $s['timeout'] = 30;
        }
        if (!isset($s['auth_username'])) {
            $s['auth_username'] = '';
        }
        if (!isset($s['auth_password'])) {
            $s['auth_password'] = '';
        }

        return $s;
    }

    /**
     * Prueft die Mindestanforderungen an die Konfiguration.
     *
     * @param array $settings
     *
     * @throws PrinterConfigException
     */
    private function validateSettings(array $settings): void
    {
        if (empty($settings['host'])) {
            throw new PrinterConfigException('Keine IP-Adresse konfiguriert');
        }

        if (!Protocol::isValid($settings['protocol'])) {
            throw new PrinterConfigException(
                sprintf('Ungueltiges Protokoll: %s', $settings['protocol'])
            );
        }

        // Port-Validierung
        $port = (int)$settings['port'];
        if ($port < 1 || $port > 65535) {
            throw new PrinterConfigException(
                sprintf('Ungueltiger Port: %d (erlaubt: 1-65535)', $port)
            );
        }

        // Host-Validierung: Loopback und Metadaten-Endpoints blockieren
        $host = $settings['host'];

        // Bekannte Metadaten-Hostnames blockieren
        $blockedHostnames = ['metadata.google.internal', 'metadata'];
        if (in_array(strtolower($host), $blockedHostnames, true)) {
            throw new PrinterConfigException(
                sprintf('Blockierter Host: %s', $host)
            );
        }

        // Hostname zu IP aufloesen fuer Validierung
        $resolvedIp = filter_var($host, FILTER_VALIDATE_IP)
            ? $host
            : gethostbyname($host);

        // Wenn Aufloesung fehlschlaegt, gibt gethostbyname den Input zurueck
        if ($resolvedIp === $host && !filter_var($host, FILTER_VALIDATE_IP)) {
            throw new PrinterConfigException(
                sprintf('Hostname nicht aufloesbar: %s', $host)
            );
        }

        // IP normalisieren und pruefen
        $ipLong = ip2long($resolvedIp);
        if ($ipLong !== false) {
            // Loopback: 127.0.0.0/8
            if (($ipLong >> 24) === 127) {
                throw new PrinterConfigException('Loopback-Adressen sind nicht erlaubt');
            }
            // Link-local: 169.254.0.0/16
            if (($ipLong >> 16) === (169 * 256 + 254)) {
                throw new PrinterConfigException('Link-Local-Adressen sind nicht erlaubt');
            }
        } else {
            // IPv6 pruefen
            $lowerHost = strtolower($resolvedIp);
            if ($lowerHost === '::1'
                || strpos($lowerHost, '::ffff:127.') === 0
                || strpos($lowerHost, '::ffff:7f') === 0
                || $lowerHost === '::ffff:169.254.169.254'
            ) {
                throw new PrinterConfigException('Loopback/Link-Local IPv6-Adressen sind nicht erlaubt');
            }
        }
    }

    /**
     * Laedt den Inhalt des Druckdokuments.
     * Ist $dokument ein vorhandener Dateipfad, wird der Inhalt gelesen.
     * Andernfalls werden die Daten unveraendert durchgereicht.
     *
     * @param string $dokument
     *
     * @return string
     */
    private function loadDocumentData(string $dokument): string
    {
        if (is_file($dokument)) {
            // Dateien ueber 100 MB ablehnen (Speicherschutz)
            $size = filesize($dokument);
            if ($size !== false && $size > 104857600) {
                throw new PrinterConfigException(
                    sprintf('Dokument zu gross: %d Bytes (max. 100 MB)', $size)
                );
            }
            $content = file_get_contents($dokument);
            if ($content === false) {
                throw new PrinterConfigException(
                    sprintf('Dokument nicht lesbar: %s', basename($dokument))
                );
            }
            return $content;
        }

        // Rohdaten (z.B. ESC/POS-Stream direkt als String uebergeben)
        return $dokument;
    }

    /**
     * Erzeugt den passenden Treiber anhand des konfigurierten Protokolls.
     *
     * @param array $settings
     *
     * @return DriverInterface
     *
     * @throws PrinterConfigException
     */
    private function createDriver(array $settings): DriverInterface
    {
        $host     = (string)$settings['host'];
        $port     = (int)$settings['port'];
        $timeout  = (int)$settings['timeout'];
        $username = (string)$settings['auth_username'];
        $password = (string)$settings['auth_password'];

        switch ($settings['protocol']) {
            case Protocol::IPP:
                return new IppDriver($host, $port, $timeout, $username, $password);

            case Protocol::RAW:
                return new RawDriver($host, $port, $timeout);

            case Protocol::ESCPOS:
                return new EscPosDriver($host, $port, $timeout);

            case Protocol::LPR:
                $queue = $settings['lpr_queue'] ?? 'lp';
                return new LprDriver($host, $port, $timeout, $queue);

            default:
                throw new PrinterConfigException(
                    'Netzwerkdrucker: Unbekanntes Protokoll "' . $settings['protocol'] . '"'
                );
        }
    }

    /**
     * Sammelt Druckoptionen aus den Settings und der Auftragsanzahl.
     *
     * @param array $settings
     * @param int   $copies
     *
     * @return array
     */
    private function buildPrintOptions(array $settings, int $copies): array
    {
        $options = [];

        $options['copies'] = max(1, $copies);

        if (!empty($settings['duplex'])) {
            $options['duplex'] = true;
        }
        if (!empty($settings['color'])) {
            $options['color'] = true;
        }
        if (!empty($settings['media'])) {
            $options['media'] = (string)$settings['media'];
        }
        if (!empty($settings['tray']) && $settings['tray'] !== 'auto') {
            $options['tray'] = (string)$settings['tray'];
        }
        if (!empty($settings['staple'])) {
            $options['staple'] = true;
        }
        if (!empty($settings['paper_width'])) {
            $options['paper_width'] = (string)$settings['paper_width'];
        }
        if (isset($settings['auto_cut'])) {
            $options['auto_cut'] = (bool)$settings['auto_cut'];
        }

        return $options;
    }

    /**
     * Schreibt eine Info-Meldung ins Drucker-Log.
     *
     * @param string $message
     */
    private function logInfo(string $message): void
    {
        if (isset($this->app->erp) && method_exists($this->app->erp, 'LogFile')) {
            $this->app->erp->LogFile('printer_network', $message);
        }
    }

    /**
     * Schreibt eine Fehlermeldung ins Fehler-Log.
     *
     * @param string $message
     */
    private function logError(string $message): void
    {
        if (isset($this->app->erp) && method_exists($this->app->erp, 'LogFile')) {
            $this->app->erp->LogFile('printer_error', $message);
        }
    }
}
