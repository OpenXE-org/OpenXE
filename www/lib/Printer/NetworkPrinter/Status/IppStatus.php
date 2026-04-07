<?php

require_once __DIR__ . '/../Util/IppEncoder.php';

/**
 * Queries printer status via IPP Get-Printer-Attributes (RFC 8011).
 */
class IppStatus
{
    /**
     * Queries the printer status via IPP.
     *
     * @param string $host     Printer hostname or IP
     * @param int    $port     IPP port (default 631)
     * @param string $username Optional HTTP auth username
     * @param string $password Optional HTTP auth password
     * @param string $path     IPP path (default /ipp/print)
     *
     * @return array|null Status array or null on failure
     */
    public function query(
        string $host,
        int $port = 631,
        string $username = '',
        string $password = '',
        string $path = '/ipp/print'
    ): ?array {
        try {
            $printerUri = sprintf('ipp://%s:%d%s', $host, $port, $path);

            $requestedAttributes = [
                'printer-name',
                'printer-make-and-model',
                'printer-state',
                'printer-state-reasons',
                'printer-is-accepting-jobs',
                'queued-job-count',
            ];

            $ippData = IppEncoder::buildGetPrinterAttributesRequest(
                $printerUri,
                $requestedAttributes
            );

            $response = IppEncoder::sendRequest(
                $host,
                $port,
                $path,
                $ippData,
                $username,
                $password,
                10
            );

            $parsed = IppEncoder::parseResponse($response);

            if (!$parsed['status_ok']) {
                return null;
            }

            $attrs = $parsed['attributes'];

            $printerStateRaw = isset($attrs['printer-state']) ? (int)$attrs['printer-state'] : 0;
            $stateMap = [
                3 => 'idle',
                4 => 'printing',
                5 => 'stopped',
            ];
            $state = isset($stateMap[$printerStateRaw]) ? $stateMap[$printerStateRaw] : 'unknown';

            $stateReasons = isset($attrs['printer-state-reasons'])
                ? $attrs['printer-state-reasons']
                : null;
            if ($stateReasons !== null && !is_array($stateReasons)) {
                $stateReasons = [$stateReasons];
            }

            $acceptingJobs = isset($attrs['printer-is-accepting-jobs'])
                ? (bool)$attrs['printer-is-accepting-jobs']
                : false;

            $queuedJobs = isset($attrs['queued-job-count'])
                ? (int)$attrs['queued-job-count']
                : 0;

            $name = isset($attrs['printer-name'])
                ? (string)$attrs['printer-name']
                : '';

            $model = isset($attrs['printer-make-and-model'])
                ? (string)$attrs['printer-make-and-model']
                : '';

            return [
                'online'         => true,
                'name'           => $name,
                'model'          => $model,
                'state'          => $state,
                'state_reasons'  => $stateReasons,
                'accepting_jobs' => $acceptingJobs,
                'queued_jobs'    => $queuedJobs,
                'source'         => 'ipp',
            ];
        } catch (\Exception $e) {
            return null;
        }
    }
}
