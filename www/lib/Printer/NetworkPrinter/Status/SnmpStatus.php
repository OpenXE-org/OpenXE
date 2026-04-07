<?php

/**
 * Fragt Druckerstatus per SNMP ab (RFC 3805 Printer MIB).
 * Erfordert ext-snmp. Graceful Degradation wenn nicht installiert.
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
     * Prueft ob ext-snmp verfuegbar ist.
     *
     * @return bool
     */
    public static function isExtensionAvailable(): bool
    {
        return extension_loaded('snmp');
    }

    /**
     * Fragt den Druckerstatus per SNMP ab.
     *
     * @param string $host      Druckerhostname oder IP-Adresse
     * @param string $community SNMP-Community-String (Default: 'public')
     * @param int    $timeout   SNMP-Timeout in Mikrosekunden (Default: 2000000)
     *
     * @return array|null Status-Array oder null bei Fehler/fehlende Extension
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
     * Fragt Toner/Tinten-Level ab (alle Farben).
     *
     * @param \SNMP $session Aktive SNMP-Session
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
     * Holt einen einzelnen OID-Wert mit Fehlerunterdrueckung.
     *
     * @param \SNMP  $session Aktive SNMP-Session
     * @param string $oid     Abzufragender OID
     *
     * @return string|null Rohwert oder null bei Fehler
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
     * Entfernt SNMP-Typ-Prefixe wie STRING:, INTEGER: etc.
     *
     * Beispiele:
     *   'STRING: "HP LaserJet"' -> 'HP LaserJet'
     *   'INTEGER: 3'            -> '3'
     *
     * @param string $value Rohwert-String
     *
     * @return string Bereinigter Wert
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
