<?php

require_once __DIR__ . '/IppStatus.php';
require_once __DIR__ . '/SnmpStatus.php';

/**
 * Orchestrates printer status queries from multiple sources.
 *
 * Query order: IPP first (if protocol=ipp), then SNMP (always attempted),
 * then TCP socket fallback if still offline.
 */
class StatusMonitor
{
    /** @var IppStatus */
    private $ippStatus;

    /** @var SnmpStatus */
    private $snmpStatus;

    public function __construct()
    {
        $this->ippStatus  = new IppStatus();
        $this->snmpStatus = new SnmpStatus();
    }

    /**
     * Retrieves the current printer status by querying available sources.
     *
     * @param array $settings Printer settings (from drucker.json)
     *
     * @return array Status array with keys: online, name, model, state,
     *               supplies, paper, page_count, source, snmp_available
     */
    public function getStatus(array $settings): array
    {
        $host     = isset($settings['host'])     ? (string)$settings['host']     : '';
        $port     = isset($settings['port'])     ? (int)$settings['port']        : 631;
        $protocol = isset($settings['protocol']) ? (string)$settings['protocol'] : 'ipp';
        $username = '';
        $password = '';

        $username = $settings['auth_username'] ?? ($settings['auth']['username'] ?? '');
        $password = $settings['auth_password'] ?? ($settings['auth']['password'] ?? '');

        $ippPath = isset($settings['path']) ? (string)$settings['path'] : '/ipp/print';

        $result = [
            'online'         => false,
            'name'           => '',
            'model'          => '',
            'state'          => 'unknown',
            'supplies'       => [],
            'paper'          => null,
            'page_count'     => null,
            'source'         => null,
            'snmp_available' => SnmpStatus::isExtensionAvailable(),
        ];

        if ($host === '') {
            return $result;
        }

        $ippResult  = null;
        $snmpResult = null;

        // Step 1: Try IPP if protocol is ipp
        if ($protocol === 'ipp') {
            $ippResult = $this->ippStatus->query($host, $port, $username, $password, $ippPath);
            if ($ippResult !== null) {
                $result['online'] = (bool)$ippResult['online'];
                $result['name']   = (string)$ippResult['name'];
                $result['model']  = (string)$ippResult['model'];
                $result['state']  = (string)$ippResult['state'];
                $result['source'] = 'ipp';
            }
        }

        // Step 2: Always try SNMP — merges supplies/paper/page_count
        $snmpCommunity = isset($settings['snmp_community'])
            ? (string)$settings['snmp_community']
            : 'public';
        $snmpResult = $this->snmpStatus->query($host, $snmpCommunity);

        if ($snmpResult !== null) {
            // If IPP did not succeed, use SNMP for online/state/name
            if (!$result['online']) {
                $result['online'] = (bool)$snmpResult['online'];
                $result['state']  = (string)$snmpResult['state'];
                if ($result['name'] === '' && isset($snmpResult['name'])) {
                    $result['name'] = (string)$snmpResult['name'];
                }
                if ($result['source'] === null) {
                    $result['source'] = 'snmp';
                }
            }

            // Always merge SNMP-only fields
            if (!empty($snmpResult['supplies'])) {
                $result['supplies'] = $snmpResult['supplies'];
            }
            if ($snmpResult['paper'] !== null) {
                $result['paper'] = $snmpResult['paper'];
            }
            if ($snmpResult['page_count'] !== null) {
                $result['page_count'] = $snmpResult['page_count'];
            }
        }

        // Step 3: TCP fallback if still not online
        if (!$result['online']) {
            $tcpOnline = $this->checkTcpConnect($host, $port, 3);
            if ($tcpOnline) {
                $result['online'] = true;
                if ($result['source'] === null) {
                    $result['source'] = 'tcp';
                }
            }
        }

        return $result;
    }

    /**
     * Checks whether a TCP connection can be established.
     *
     * @param string $host    Hostname or IP
     * @param int    $port    TCP port
     * @param int    $timeout Timeout in seconds
     *
     * @return bool
     */
    private function checkTcpConnect(string $host, int $port, int $timeout): bool
    {
        try {
            $address = sprintf('tcp://%s:%d', $host, $port);
            $socket  = @stream_socket_client(
                $address,
                $errno,
                $errstr,
                $timeout,
                STREAM_CLIENT_CONNECT
            );
            if ($socket !== false) {
                fclose($socket);
                return true;
            }
        } catch (\Exception $e) {
            // Fall through
        }
        return false;
    }
}
