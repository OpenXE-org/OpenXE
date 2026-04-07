<?php

/**
 * Queries printer status via SNMP (RFC 3805 Printer MIB).
 * Requires PHP ext-snmp. Gracefully degrades if extension is not available.
 */
class SnmpStatus
{
    const OID_PRINTER_STATUS        = '1.3.6.1.2.1.25.3.5.1.1.1';
    const OID_PRINTER_NAME          = '1.3.6.1.2.1.43.5.1.1.16.1';
    const OID_SERIAL_NUMBER         = '1.3.6.1.2.1.43.5.1.1.17.1';
    const OID_MARKER_SUPPLIES_DESC  = '1.3.6.1.2.1.43.11.1.1.6.1';
    const OID_MARKER_SUPPLIES_MAX   = '1.3.6.1.2.1.43.11.1.1.8.1';
    const OID_MARKER_SUPPLIES_LEVEL = '1.3.6.1.2.1.43.11.1.1.9.1';
    const OID_INPUT_CURRENT_LEVEL   = '1.3.6.1.2.1.43.8.2.1.11.1.1';
    const OID_INPUT_MAX_CAPACITY    = '1.3.6.1.2.1.43.8.2.1.10.1.1';
    const OID_PAGE_COUNT            = '1.3.6.1.2.1.43.10.2.1.4.1.1';

    /**
     * Checks whether the PHP SNMP extension is loaded.
     *
     * @return bool
     */
    public static function isExtensionAvailable(): bool
    {
        return extension_loaded('snmp');
    }

    /**
     * Queries the printer status via SNMP.
     *
     * @param string $host      Printer hostname or IP
     * @param string $community SNMP community string (default 'public')
     * @param int    $timeout   SNMP timeout in microseconds (default 2000000)
     *
     * @return array|null Status array or null if ext unavailable or on error
     */
    public function query(
        string $host,
        string $community = 'public',
        int $timeout = 2000000
    ): ?array {
        if (!self::isExtensionAvailable()) {
            return null;
        }

        try {
            $session = new \SNMP(\SNMP::VERSION_2c, $host, $community, $timeout);
            $session->exceptions_enabled = true;

            $stateRaw = $this->snmpGetValue($session, self::OID_PRINTER_STATUS);
            $stateInt = ($stateRaw !== null) ? (int)$this->cleanSnmpValue($stateRaw) : 2;

            $stateMap = [
                1 => 'other',
                2 => 'unknown',
                3 => 'idle',
                4 => 'printing',
                5 => 'warmup',
            ];
            $state = isset($stateMap[$stateInt]) ? $stateMap[$stateInt] : 'unknown';

            $nameRaw   = $this->snmpGetValue($session, self::OID_PRINTER_NAME);
            $serialRaw = $this->snmpGetValue($session, self::OID_SERIAL_NUMBER);

            $name   = ($nameRaw !== null)   ? $this->cleanSnmpValue($nameRaw)   : '';
            $serial = ($serialRaw !== null) ? $this->cleanSnmpValue($serialRaw) : '';

            $supplies = $this->querySupplies($session);

            $paperLevel = null;
            $paperMax   = null;
            $paperRaw   = $this->snmpGetValue($session, self::OID_INPUT_CURRENT_LEVEL);
            $paperMaxRaw = $this->snmpGetValue($session, self::OID_INPUT_MAX_CAPACITY);
            if ($paperRaw !== null) {
                $paperLevel = (int)$this->cleanSnmpValue($paperRaw);
            }
            if ($paperMaxRaw !== null) {
                $paperMax = (int)$this->cleanSnmpValue($paperMaxRaw);
            }

            $paper = null;
            if ($paperLevel !== null && $paperMax !== null && $paperMax > 0) {
                $paper = [
                    'level'   => $paperLevel,
                    'max'     => $paperMax,
                    'percent' => round(($paperLevel / $paperMax) * 100, 1),
                ];
            } elseif ($paperLevel !== null) {
                $paper = [
                    'level'   => $paperLevel,
                    'max'     => $paperMax,
                    'percent' => null,
                ];
            }

            $pageCountRaw = $this->snmpGetValue($session, self::OID_PAGE_COUNT);
            $pageCount = ($pageCountRaw !== null)
                ? (int)$this->cleanSnmpValue($pageCountRaw)
                : null;

            $session->close();

            return [
                'online'     => true,
                'source'     => 'snmp',
                'state'      => $state,
                'name'       => $name,
                'serial'     => $serial,
                'supplies'   => $supplies,
                'paper'      => $paper,
                'page_count' => $pageCount,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Walks marker supply OIDs and returns an array of supply entries.
     *
     * @param \SNMP $session Active SNMP session
     *
     * @return array
     */
    private function querySupplies(\SNMP $session): array
    {
        $supplies = [];

        try {
            $descs  = @$session->walk(self::OID_MARKER_SUPPLIES_DESC);
            $maxes  = @$session->walk(self::OID_MARKER_SUPPLIES_MAX);
            $levels = @$session->walk(self::OID_MARKER_SUPPLIES_LEVEL);

            if (!is_array($descs) || empty($descs)) {
                return [];
            }

            $descValues  = array_values($descs);
            $maxValues   = is_array($maxes)  ? array_values($maxes)  : [];
            $levelValues = is_array($levels) ? array_values($levels) : [];

            foreach ($descValues as $i => $descRaw) {
                $desc  = $this->cleanSnmpValue((string)$descRaw);
                $max   = isset($maxValues[$i])
                    ? (int)$this->cleanSnmpValue((string)$maxValues[$i])
                    : null;
                $level = isset($levelValues[$i])
                    ? (int)$this->cleanSnmpValue((string)$levelValues[$i])
                    : null;

                $percent = null;
                if ($level !== null && $max !== null && $max > 0) {
                    $percent = round(($level / $max) * 100, 1);
                }

                $supplies[] = [
                    'description' => $desc,
                    'level'       => $level,
                    'max'         => $max,
                    'percent'     => $percent,
                ];
            }
        } catch (\Exception $e) {
            // Return whatever was collected
        }

        return $supplies;
    }

    /**
     * Gets a single OID value with error suppression.
     *
     * @param \SNMP  $session Active SNMP session
     * @param string $oid     OID to query
     *
     * @return string|null Raw SNMP value string or null on failure
     */
    private function snmpGetValue(\SNMP $session, string $oid): ?string
    {
        try {
            $result = @$session->get($oid);
            if ($result === false || $result === null) {
                return null;
            }
            return (string)$result;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Strips SNMP type prefixes and surrounding quotes from a value string.
     *
     * Examples:
     *   'STRING: "HP LaserJet"' -> 'HP LaserJet'
     *   'INTEGER: 3'            -> '3'
     *
     * @param string $value Raw SNMP value string
     *
     * @return string Cleaned value
     */
    private function cleanSnmpValue(string $value): string
    {
        // Remove type prefix (e.g. "STRING: ", "INTEGER: ", "Gauge32: ", etc.)
        $value = preg_replace('/^[A-Za-z0-9]+:\s*/', '', $value);
        // Remove surrounding quotes
        $value = trim($value, '"');
        return trim($value);
    }
}
