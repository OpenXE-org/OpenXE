<?php

require_once __DIR__ . '/../Exception/PrinterException.php';
require_once __DIR__ . '/../Exception/PrinterConnectionException.php';
require_once __DIR__ . '/../Exception/PrinterCommunicationException.php';

/**
 * Interface fuer alle Netzwerk-Druckertreiber.
 */
interface DriverInterface
{
    /**
     * Sendet Daten an den Drucker.
     *
     * @param string $data Dateiinhalt (PDF, ZPL, ESC/POS etc.)
     * @param array  $options Druckoptionen (anzahl, duplex, color etc.)
     *
     * @return bool true bei Erfolg
     *
     * @throws PrinterConnectionException Drucker nicht erreichbar
     * @throws PrinterCommunicationException Daten nicht vollstaendig gesendet
     */
    public function send(string $data, array $options = []): bool;

    /**
     * Prueft ob der Drucker erreichbar ist (TCP-Connect-Check).
     *
     * @return bool
     */
    public function isAvailable(): bool;

    /**
     * Gibt die unterstuetzten Optionen des Treibers zurueck.
     *
     * @return array z.B. ['duplex' => true, 'color' => true, 'tray' => true]
     */
    public function getCapabilities(): array;
}
