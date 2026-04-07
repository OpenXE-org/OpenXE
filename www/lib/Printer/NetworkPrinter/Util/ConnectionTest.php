<?php

require_once __DIR__ . '/../Status/StatusMonitor.php';

/**
 * Verbindungstest-Logik fuer den "Verbindung testen"-Button
 * in der Druckerverwaltung.
 */
class ConnectionTest
{
    /** @var StatusMonitor */
    private $statusMonitor;

    public function __construct()
    {
        $this->statusMonitor = new StatusMonitor();
    }

    /**
     * Fuehrt einen vollstaendigen Verbindungstest durch.
     *
     * @param array $settings JSON-Settings aus drucker.json
     * @return array Ergebnis mit: success, message, details
     */
    public function test(array $settings): array
    {
        $host = $settings['host'] ?? '';
        $port = (int)($settings['port'] ?? 9100);

        if ($host === '') {
            return [
                'success' => false,
                'message' => 'Keine IP-Adresse konfiguriert',
                'details' => [],
            ];
        }

        $result = [
            'success' => false,
            'message' => '',
            'details' => [],
        ];

        // 1. TCP-Connect-Check
        $startTime = microtime(true);
        $fp = @stream_socket_client(
            sprintf('tcp://%s:%d', $host, $port),
            $errno, $errstr, 3
        );
        $connectTime = round((microtime(true) - $startTime) * 1000);

        if ($fp === false) {
            $result['message'] = sprintf(
                'Drucker nicht erreichbar: %s:%d — %s (%d)',
                $host, $port, $errstr, $errno
            );
            return $result;
        }

        fclose($fp);
        $result['details']['connect_time_ms'] = $connectTime;
        $result['details']['tcp'] = 'OK';

        // 2. Status-Abfrage
        $status = $this->statusMonitor->getStatus($settings);

        $result['success'] = true;
        $result['details']['online'] = $status['online'];
        $result['details']['source'] = $status['source'];

        // Nachricht zusammenbauen
        $parts = [];
        $parts[] = 'Verbunden';

        if (!empty($status['name'])) {
            $parts[] = $status['name'];
        }
        if (!empty($status['model'])) {
            $parts[] = $status['model'];
        }
        if (!empty($status['state']) && $status['state'] !== 'unknown') {
            $stateLabels = [
                'idle' => 'Bereit',
                'printing' => 'Druckt',
                'stopped' => 'Angehalten',
                'warmup' => 'Aufwaermphase',
            ];
            $parts[] = 'Status: ' . ($stateLabels[$status['state']] ?? $status['state']);
        }

        $result['message'] = implode(' — ', $parts);

        // Toner-Info
        if (!empty($status['supplies'])) {
            $tonerParts = [];
            foreach ($status['supplies'] as $supply) {
                $tonerParts[] = sprintf('%s: %d%%', $supply['description'], $supply['percent']);
            }
            $result['details']['supplies'] = implode(', ', $tonerParts);
        }

        // Papier-Info
        if (!empty($status['paper'])) {
            $result['details']['paper'] = sprintf(
                '%d/%d (%d%%)',
                $status['paper']['level'],
                $status['paper']['max'],
                $status['paper']['percent']
            );
        }

        // Seitenzaehler
        if (isset($status['page_count'])) {
            $result['details']['page_count'] = $status['page_count'];
        }

        // SNMP-Hinweis
        if (!$status['snmp_available']) {
            $result['details']['snmp_hint'] = 'PHP-Extension snmp nicht installiert — erweiterter Status nicht verfuegbar';
        }

        return $result;
    }
}
