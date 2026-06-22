<?php

require_once __DIR__ . '/DriverInterface.php';
require_once __DIR__ . '/../Exception/PrinterConnectionException.php';
require_once __DIR__ . '/../Exception/PrinterCommunicationException.php';

/**
 * RAW/JetDirect Treiber — sendet Daten direkt per TCP an Port 9100.
 * Fuer Etikettendrucker (ZPL/EPL), Dokumentendrucker (PDF pass-through)
 * und alle Drucker die Raw-Daten auf Port 9100 akzeptieren.
 */
class RawDriver implements DriverInterface
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var int Timeout fuer Daten senden in Sekunden */
    private $timeout;

    /**
     * @param string $host IP-Adresse oder Hostname
     * @param int    $port TCP-Port (Default: 9100)
     * @param int    $timeout Timeout in Sekunden (Default: 30)
     */
    public function __construct(string $host, int $port = 9100, int $timeout = 30)
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
    }

    /**
     * {@inheritdoc}
     */
    public function send(string $data, array $options = []): bool
    {
        $address = sprintf('tcp://%s:%d', $this->host, $this->port);

        $fp = @stream_socket_client(
            $address,
            $errno,
            $errstr,
            5 // Connect-Timeout: 5 Sekunden
        );

        if ($fp === false) {
            throw new PrinterConnectionException(
                sprintf('Verbindung zu %s fehlgeschlagen: %s (%d)', $address, $errstr, $errno)
            );
        }

        try {
            stream_set_timeout($fp, $this->timeout);

            $dataLen = strlen($data);
            $written = 0;

            while ($written < $dataLen) {
                $chunk = @fwrite($fp, substr($data, $written));
                if ($chunk === false || $chunk === 0) {
                    $meta = stream_get_meta_data($fp);
                    if (!empty($meta['timed_out'])) {
                        throw new PrinterCommunicationException(
                            sprintf('Timeout beim Senden an %s nach %d/%d Bytes', $address, $written, $dataLen)
                        );
                    }
                    throw new PrinterCommunicationException(
                        sprintf('Schreibfehler an %s nach %d/%d Bytes', $address, $written, $dataLen)
                    );
                }
                $written += $chunk;
            }

            fflush($fp);

            return true;
        } finally {
            fclose($fp);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable(): bool
    {
        $fp = @stream_socket_client(
            sprintf('tcp://%s:%d', $this->host, $this->port),
            $errno,
            $errstr,
            3 // Schneller Connect-Check: 3 Sekunden
        );

        if ($fp === false) {
            return false;
        }

        fclose($fp);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCapabilities(): array
    {
        return [
            'duplex' => false,
            'color' => false,
            'tray' => false,
            'staple' => false,
        ];
    }
}
