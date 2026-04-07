<?php

require_once __DIR__ . '/DriverInterface.php';
require_once __DIR__ . '/../Util/IppEncoder.php';
require_once __DIR__ . '/../Exception/PrinterConnectionException.php';
require_once __DIR__ . '/../Exception/PrinterProtocolException.php';

/**
 * IPP (Internet Printing Protocol) Treiber.
 *
 * Sendet PDF-Dokumente per IPP Print-Job an Netzwerkdrucker (Port 631).
 * Unterstuetzt Duplex, Farbdruck, Medienfach, Heftung und Seitenorientierung.
 */
class IppDriver implements DriverInterface
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var int */
    private $timeout;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $path;

    /**
     * @param string $host     IP-Adresse oder Hostname des Druckers
     * @param int    $port     TCP-Port (Default: 631)
     * @param int    $timeout  Request-Timeout in Sekunden (Default: 30)
     * @param string $username HTTP-Auth Benutzername (optional)
     * @param string $password HTTP-Auth Passwort (optional)
     * @param string $path     HTTP-Pfad zum IPP-Endpoint (Default: '/ipp/print')
     */
    public function __construct(
        string $host,
        int $port = 631,
        int $timeout = 30,
        string $username = '',
        string $password = '',
        string $path = '/ipp/print'
    ) {
        $this->host     = $host;
        $this->port     = $port;
        $this->timeout  = $timeout;
        $this->username = $username;
        $this->password = $password;
        $this->path     = $path;
    }

    /**
     * {@inheritdoc}
     *
     * Sendet ein Dokument (PDF) per IPP Print-Job an den Drucker.
     * Unterstuetzte Optionen:
     *   - copies        (int)    Anzahl Kopien
     *   - duplex        (bool)   Duplexdruck (two-sided-long-edge)
     *   - color         (bool)   Farbdruck / Monochrom
     *   - media         (string) Medienformat z.B. 'iso_a4_210x297mm'
     *   - tray          (string) Medienfach z.B. 'tray-1'
     *   - staple        (bool)   Heften
     *   - orientation   (string) 'portrait' oder 'landscape'
     *   - job-name      (string) Auftragsname
     *
     * @throws PrinterConnectionException Drucker nicht erreichbar
     * @throws PrinterProtocolException   IPP-Fehler oder ungueltiger Status
     */
    public function send(string $data, array $options = []): bool
    {
        $printerUri = sprintf('ipp://%s:%d%s', $this->host, $this->port, $this->path);

        // Build IPP header + append PDF data
        $ippHeader = IppEncoder::buildPrintJobRequest($printerUri, $options);
        $ippRequest = $ippHeader . $data;

        $response = IppEncoder::sendRequest(
            $this->host,
            $this->port,
            $this->path,
            $ippRequest,
            $this->username,
            $this->password,
            $this->timeout
        );

        $parsed = IppEncoder::parseResponse($response);

        if (!$parsed['status_ok']) {
            $statusHex = sprintf('0x%04X', $parsed['status_code']);
            throw new PrinterProtocolException(
                sprintf(
                    'IPP Print-Job fehlgeschlagen: Status %s fuer %s',
                    $statusHex,
                    $printerUri
                )
            );
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * Prueft Erreichbarkeit per TCP-Connect auf Host:Port.
     */
    public function isAvailable(): bool
    {
        $fp = @stream_socket_client(
            sprintf('tcp://%s:%d', $this->host, $this->port),
            $errno,
            $errstr,
            3
        );

        if ($fp === false) {
            return false;
        }

        fclose($fp);
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * IPP-Treiber unterstuetzt alle erweiterten Druckoptionen.
     */
    public function getCapabilities(): array
    {
        return [
            'duplex'      => true,
            'color'       => true,
            'tray'        => true,
            'staple'      => true,
            'media'       => true,
            'copies'      => true,
            'orientation' => true,
        ];
    }

    /**
     * Ruft Druckerattribute per IPP Get-Printer-Attributes ab.
     *
     * @param array $requestedAttributes Gewuenschte Attribute (leer = Standardset)
     *
     * @return array Assoziatives Array der Druckerattribute
     *
     * @throws PrinterConnectionException Drucker nicht erreichbar
     * @throws PrinterProtocolException   IPP-Fehler
     */
    public function getPrinterAttributes(array $requestedAttributes = []): array
    {
        if (empty($requestedAttributes)) {
            $requestedAttributes = [
                'printer-name',
                'printer-make-and-model',
                'printer-state',
                'printer-state-reasons',
                'printer-is-accepting-jobs',
            ];
        }

        $printerUri = sprintf('ipp://%s:%d%s', $this->host, $this->port, $this->path);

        $ippRequest = IppEncoder::buildGetPrinterAttributesRequest($printerUri, $requestedAttributes);

        $response = IppEncoder::sendRequest(
            $this->host,
            $this->port,
            $this->path,
            $ippRequest,
            $this->username,
            $this->password,
            $this->timeout
        );

        $parsed = IppEncoder::parseResponse($response);

        if (!$parsed['status_ok']) {
            $statusHex = sprintf('0x%04X', $parsed['status_code']);
            throw new PrinterProtocolException(
                sprintf(
                    'IPP Get-Printer-Attributes fehlgeschlagen: Status %s fuer %s',
                    $statusHex,
                    $printerUri
                )
            );
        }

        return $parsed['attributes'];
    }
}
