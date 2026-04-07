<?php

require_once __DIR__ . '/DriverInterface.php';
require_once __DIR__ . '/../Exception/PrinterConnectionException.php';
require_once __DIR__ . '/../Exception/PrinterCommunicationException.php';
require_once __DIR__ . '/../Exception/PrinterProtocolException.php';

/**
 * LPR/LPD Treiber nach RFC 1179.
 * Sendet Druckauftraege an Port 515.
 * Fallback fuer aeltere Drucker die kein IPP/RAW unterstuetzen.
 */
class LprDriver implements DriverInterface
{
    /** @var string */
    private $host;
    /** @var int */
    private $port;
    /** @var int */
    private $timeout;
    /** @var string LPR-Queuename */
    private $queue;

    /**
     * @param string $host IP-Adresse oder Hostname
     * @param int    $port TCP-Port (Default: 515)
     * @param int    $timeout Timeout in Sekunden (Default: 30)
     * @param string $queue LPR-Queuename (Default: 'lp')
     */
    public function __construct(string $host, int $port = 515, int $timeout = 30, string $queue = 'lp')
    {
        $this->host = $host;
        $this->port = $port;
        $this->timeout = $timeout;
        $this->queue = $queue;
    }

    /**
     * {@inheritdoc}
     *
     * @throws PrinterConnectionException
     * @throws PrinterCommunicationException
     * @throws PrinterProtocolException
     */
    public function send(string $data, array $options = []): bool
    {
        $address = sprintf('tcp://%s:%d', $this->host, $this->port);
        $fp = @stream_socket_client($address, $errno, $errstr, 5);

        if ($fp === false) {
            throw new PrinterConnectionException(
                sprintf('LPR-Verbindung zu %s fehlgeschlagen: %s (%d)', $address, $errstr, $errno)
            );
        }

        try {
            stream_set_timeout($fp, $this->timeout);
            $jobNumber = rand(100, 999);
            $hostname = gethostname() ?: 'openxe';
            $username = 'openxe';
            $filename = 'document.pdf';

            // 1. Receive-a-printer-job
            $this->lprWrite($fp, "\x02" . $this->queue . "\n");
            $this->lprReadAck($fp);

            // 2. Control File
            $controlFile = '';
            $controlFile .= 'H' . $hostname . "\n";
            $controlFile .= 'P' . $username . "\n";
            $controlFile .= 'l' . $filename . "\n";
            $controlFile .= 'U' . 'dfA' . $jobNumber . $hostname . "\n";
            $controlFile .= 'N' . $filename . "\n";
            $controlFileName = 'cfA' . $jobNumber . $hostname;

            $this->lprWrite($fp, sprintf("\x02%d %s\n", strlen($controlFile), $controlFileName));
            $this->lprReadAck($fp);
            $this->lprWrite($fp, $controlFile . "\x00");
            $this->lprReadAck($fp);

            // 3. Data File
            $dataFileName = 'dfA' . $jobNumber . $hostname;
            $this->lprWrite($fp, sprintf("\x03%d %s\n", strlen($data), $dataFileName));
            $this->lprReadAck($fp);

            $dataLen = strlen($data);
            $written = 0;
            while ($written < $dataLen) {
                $chunk = @fwrite($fp, substr($data, $written, 8192));
                if ($chunk === false || $chunk === 0) {
                    throw new PrinterCommunicationException(
                        sprintf('LPR Schreibfehler an %s nach %d/%d Bytes', $address, $written, $dataLen)
                    );
                }
                $written += $chunk;
            }
            $this->lprWrite($fp, "\x00");
            $this->lprReadAck($fp);

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
        ];
    }

    /**
     * @param resource $fp
     * @param string   $data
     * @throws PrinterCommunicationException
     */
    private function lprWrite($fp, string $data): void
    {
        $written = @fwrite($fp, $data);
        if ($written === false || $written < strlen($data)) {
            throw new PrinterCommunicationException('LPR: Schreibfehler');
        }
    }

    /**
     * @param resource $fp
     * @throws PrinterProtocolException
     */
    private function lprReadAck($fp): void
    {
        $ack = fread($fp, 1);
        if ($ack === false || $ack === '' || ord($ack) !== 0) {
            throw new PrinterProtocolException(
                sprintf('LPR-Server lehnte Befehl ab (ACK: 0x%s)', $ack !== false ? bin2hex($ack) : 'EOF')
            );
        }
    }
}
