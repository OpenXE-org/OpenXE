<?php

declare(strict_types=1);

namespace Xentral\Modules\NetworkPrinter;

use ApplicationCore;
use Xentral\Core\DependencyInjection\ContainerInterface;

/**
 * Modul-Wrapper fuer die NetworkPrinter-Library unter www/lib/Printer/NetworkPrinter/.
 *
 * Die eigentliche Printer-Implementation bleibt update-safe dort liegen (kein
 * Code-Umzug, kein Namespace-Wechsel), weil sie als OpenXE-Drucker-Plugin von
 * pages/drucker.php::loadPrinterModul() per direkter require_once-Kette geladen
 * wird und diese Pfade nicht gebrochen werden duerfen. Zusaetzlich ist die
 * Library als Pull-Request openxe-org/openxe#257 bei upstream offen, ein
 * Code-Umzug wuerde den Merge erschweren.
 *
 * Dieser Bootstrap stellt die Klasse zusaetzlich als DI-Service zur Verfuegung,
 * damit sie aus anderen Modulen (z.B. LexwareOffice oder eine zukuenftige
 * Belegdrucker-Integration) per Container-Lookup erreichbar ist, ohne den
 * require_once-Pfad an mehreren Stellen zu duplizieren.
 */
final class Bootstrap
{
    /**
     * Absoluter Pfad zur Library-Hauptklasse im Legacy-Pfad.
     *
     * Wird in einem evtl. Integration-Branch nach classes/Modules/NetworkPrinter/lib
     * verschoben; dieser Bootstrap ist der einzige Ort, der das wissen muss.
     */
    private const LIBRARY_ENTRY = __DIR__ . '/../../../www/lib/Printer/NetworkPrinter/NetworkPrinter.php';

    /**
     * Service-Registry-Map fuer Auto-Discovery.
     *
     * @return array<string, string>
     */
    public static function registerServices(): array
    {
        return [
            'NetworkPrinterFactory' => 'onInitNetworkPrinterFactory',
        ];
    }

    /**
     * Liefert eine Factory-Closure, die fuer eine gegebene Drucker-ID eine
     * NetworkPrinter-Instanz baut. Die Klasse \NetworkPrinter erbt von
     * \PrinterBase und erwartet als Constructor-Argumente ($app, $id) — die
     * eigentlichen Verbindungsdaten (host, port, protocol, ...) zieht der
     * Drucker selbst aus der Tabelle `drucker` per id.
     *
     * \NetworkPrinter ist global namespaced (kein Xentral\...\NetworkPrinter),
     * darum hier root-namespaced referenziert.
     *
     * @return callable(int): \NetworkPrinter
     */
    public static function onInitNetworkPrinterFactory(ContainerInterface $container): callable
    {
        require_once self::LIBRARY_ENTRY;

        /** @var ApplicationCore $app */
        $app = $container->get('LegacyApplication');

        return static function (int $printerId) use ($app): \NetworkPrinter {
            return new \NetworkPrinter($app, $printerId);
        };
    }
}
