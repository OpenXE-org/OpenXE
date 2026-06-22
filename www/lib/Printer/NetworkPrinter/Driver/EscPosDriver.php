<?php

require_once __DIR__ . '/DriverInterface.php';
require_once __DIR__ . '/../Exception/PrinterConnectionException.php';
require_once __DIR__ . '/../Exception/PrinterCommunicationException.php';

/**
 * ESC/POS Treiber fuer Bondrucker (Thermodrucker).
 * Sendet ESC/POS-Byte-Streams direkt per TCP an Port 9100.
 * Der ESC/POS-Stream wird von der bestehenden phpprint-Klasse in OpenXE erzeugt.
 */
class EscPosDriver implements DriverInterface
{
    /** @var string */
    private $host;

    /** @var int */
    private $port;

    /** @var int */
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
        $fp = @stream_socket_client($address, $errno, $errstr, 5);

        if ($fp === false) {
            throw new PrinterConnectionException(
                sprintf('Verbindung zu Bondrucker %s fehlgeschlagen: %s (%d)', $address, $errstr, $errno)
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
                            sprintf('Timeout beim Senden an Bondrucker %s', $address)
                        );
                    }
                    throw new PrinterCommunicationException(
                        sprintf('Schreibfehler an Bondrucker %s nach %d/%d Bytes', $address, $written, $dataLen)
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
            $errno, $errstr, 3
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
            'paper_width' => true,
            'auto_cut' => true,
        ];
    }
}
