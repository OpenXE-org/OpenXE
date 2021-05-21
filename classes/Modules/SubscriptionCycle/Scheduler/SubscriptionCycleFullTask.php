<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Scheduler;


use Aboabrechnung;
use ApplicationCore;
use Xentral\Components\Database\Database;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleJobService;
use Xentral\Modules\SubscriptionCycle\SubscriptionModuleInterface;

final class SubscriptionCycleFullTask
{
    /** @var ApplicationCore $app */
    private $app;

    /** @var Database $db */
    private $db;

    /** @var TaskMutexServiceInterface $taskMutexService */
    private $taskMutexService;

    /** @var SubscriptionModuleInterface $subscriptionModule */
    private $subscriptionModule;

    /** @var bool $isOrdersActive */
    private $isOrdersActive;

    /** @var bool $isInvoiceActive */
    private $isInvoiceActive;

    /** @var int|null $printerId */
    private $printerId;

    /** @var string $mailPrinter */
    private $mailPrinter;

    /** @var SubscriptionCycleJobService $cycleJobService */
    private $cycleJobService;

    /**
     * SubscriptionCycleFullTask constructor.
     *
     * @param ApplicationCore             $app
     * @param Database                    $db
     * @param SubscriptionCycleJobService $cycleJobService
     * @param bool                        $isOrdersActive
     * @param bool                        $isInvoiceActive
     * @param int|null                    $printerId
     * @param string                      $mailPrinter
     */
    public function __construct(
        ApplicationCore $app,
        Database $db,
        TaskMutexServiceInterface $taskMutexService,
        SubscriptionCycleJobService $cycleJobService,
        SubscriptionModuleInterface $subscriptionModule,
        bool $isOrdersActive,
        bool $isInvoiceActive,
        ?int $printerId,
        string $mailPrinter
    ) {
        $this->app = $app;
        $this->db = $db;
        $this->taskMutexService = $taskMutexService;
        $this->subscriptionModule = $subscriptionModule;
        $this->cycleJobService = $cycleJobService;
        $this->isOrdersActive = $isOrdersActive;
        $this->isInvoiceActive = $isInvoiceActive;
        $this->printerId = $printerId;
        $this->mailPrinter = $mailPrinter;
    }

    public function execute(): void
    {
        if ($this->taskMutexService->isTaskInstanceRunning('rechnungslauf')) {
            return;
        }
        $this->taskMutexService->setMutex('rechnungslauf');

        if (empty($this->isOrdersActive) && empty($this->isInvoiceActive)) {
            return;
        }

        if ($this->isOrdersActive) {
            $orderAddresses = array_map(
                'intval',
                array_keys((array)$this->subscriptionModule->GetRechnungsArray('auftrag'))
            );
            $addressIdsInJobs = $this->cycleJobService->getAddressIdsByDocumentType('auftrag');
            $orderAddresses = array_diff($orderAddresses, $addressIdsInJobs);
            foreach ($orderAddresses as $addressToAdd) {
                $this->cycleJobService->create($addressToAdd, 'auftrag', $this->mailPrinter, $this->printerId);
            }
            unset($orderAddresses);
        }
        if ($this->isInvoiceActive) {
            $invoiceAddresses = array_map(
                'intval',
                array_keys((array)$this->subscriptionModule->GetRechnungsArray('rechnung'))
            );
            $addressIdsInJobs = $this->cycleJobService->getAddressIdsByDocumentType('rechnung');
            $invoiceAddresses = array_diff($invoiceAddresses, $addressIdsInJobs);
            foreach ($invoiceAddresses as $addressToAdd) {
                $this->cycleJobService->create($addressToAdd, 'rechnung', $this->mailPrinter, $this->printerId);
            }
        }
        if (empty($this->isInvoiceActive)) {
            return;
        }
    }

    public function cleanup(): void
    {
        $this->taskMutexService->setMutex('rechnungslauf', false);
    }
}
