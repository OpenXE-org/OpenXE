<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Scheduler;


use ApplicationCore;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleJobService;
use Xentral\Modules\SubscriptionCycle\SubscriptionCycleModuleInterface;
use Xentral\Modules\SubscriptionCycle\SubscriptionModuleInterface;

final class SubscriptionCycleManualJobTask
{
    /** @var ApplicationCore $app */
    private $app;

    /** @var Database $db */
    private $db;

    /** @var SubscriptionCycleJobService $cycleJobService */
    private $cycleJobService;

    /** @var TaskMutexServiceInterface $taskMutexService */
    private $taskMutexService;

    /** @var SubscriptionCycleModuleInterface $subscriptionCycleModule */
    private $subscriptionCycleModule;

    /** @var SubscriptionModuleInterface $subscriptionModule */
    private $subscriptionModule;

    /** @var bool $useGroups */
    private $useGroups;

    /**
     * SubscriptionCycleManualJobTask constructor.
     *
     * @param ApplicationCore                  $app
     * @param Database                         $db
     * @param SubscriptionCycleJobService      $cycleJobService
     * @param SubscriptionCycleModuleInterface $subscriptionCycleModule
     * @param bool                             $useGroups
     */
    public function __construct(
        ApplicationCore $app,
        Database $db,
        TaskMutexServiceInterface $taskMutexService,
        SubscriptionCycleJobService $cycleJobService,
        SubscriptionCycleModuleInterface $subscriptionCycleModule,
        SubscriptionModuleInterface $subscriptionModule,
        bool $useGroups
    ) {
        $this->app = $app;
        $this->db = $db;
        $this->taskMutexService = $taskMutexService;
        $this->cycleJobService = $cycleJobService;
        $this->subscriptionCycleModule = $subscriptionCycleModule;
        $this->subscriptionModule = $subscriptionModule;
        $this->useGroups = $useGroups;
    }

    public function execute(): void
    {
        if ($this->taskMutexService->isTaskInstanceRunning('rechnungslauf_manual')) {
            return;
        }
        $this->taskMutexService->setMutex('rechnungslauf_manual');
        $jobs = $this->cycleJobService->listAll(100);
        $simulatedDays = $this->getSimulatedDates($jobs);
        if (empty($jobs)) {
            return;
        }
        foreach (['auftrag', 'rechnung'] as $doctype) {
            foreach ($simulatedDays as $simulatedDay) {
                if ($simulatedDay === '') {
                    $simulatedDay = null;
                } else {
                    try {
                        $simulatedDay = new DateTimeImmutable($simulatedDay);
                    } catch (Exception $exception) {
                        $simulatedDay = null;
                    }
                }
                $addresses = $this->getAddressesByTypeFromJobs($jobs, $doctype, $simulatedDay);
                foreach ($jobs as $job) {
                    $job = $this->cycleJobService->getJob((int)$job['id']);
                    if (empty($job)) {
                        continue;
                    }
                    if ($job['document_type'] !== $doctype) {
                        continue;
                    }
                    if ($job['simulated_day'] === null && $simulatedDay !== null) {
                        continue;
                    }
                    if (
                        $job['simulated_day'] !== null
                        && ($simulatedDay === null || $simulatedDay->format('Y-m-d') !== $job['simulated_day'])
                    ) {
                        continue;
                    }
                    if (!in_array($job['address_id'], $addresses, false)) {
                        $this->cycleJobService->delete((int)$job['id']);
                        continue;
                    }
                    $simulatedDay = null;
                    if ($job['simulated_day'] !== null) {
                        try {
                            $simulatedDay = new DateTimeImmutable($job['simulated_day']);
                        } catch (Exception $exception) {
                            $simulatedDay = null;
                        }
                    }
                    if ($this->useGroups) {
                        $this->subscriptionCycleModule->generateAndSendSubscriptionCycleGroups(
                            $this->subscriptionModule,
                            [$job['address_id']],
                            $doctype,
                            $job['job_type'],
                            $job['printer_id'],
                            $simulatedDay
                        );
                    } else {
                        $this->subscriptionCycleModule->generateAndSendSubscriptionCycle(
                            $this->subscriptionModule,
                            [$job['address_id']],
                            $doctype,
                            $job['printer_id'],
                            $job['job_type'],
                            $simulatedDay
                        );
                    }
                    $this->cycleJobService->delete((int)$job['id']);
                    if ($this->taskMutexService->isTaskInstanceRunning('rechnungslauf')) {
                        return;
                    }
                    $this->taskMutexService->setMutex('rechnungslauf_manual');
                }
            }
        }
    }

    public function cleanup(): void
    {
        $this->taskMutexService->setMutex('rechnungslauf_manual', false);
    }

    /**
     * @param array                  $jobs
     * @param string                 $documentType
     * @param DateTimeInterface|null $simulatedDay
     *
     * @return array
     */
    private function getAddressesByTypeFromJobs(
        array $jobs,
        string $documentType,
        ?DateTimeInterface $simulatedDay = null
    ): array {
        $addresses = [];
        foreach ($jobs as $job) {
            if ($job['document_type'] === $documentType) {
                $addresses[] = (int)$job['address_id'];
            }
        }

        if (empty($addresses)) {
            return [];
        }

        $addressesWithSubscriptions = array_keys(
            (array)$this->subscriptionModule->GetRechnungsArray($documentType, true)
        );

        return array_intersect($addresses, $addressesWithSubscriptions);
    }

    /**
     * get all Dates from Setting "Vergangenes Datum für Abrechnungserstellung" to calc old Subscription cycles
     *
     * @param array $jobs
     *
     * @return array
     */
    private function getSimulatedDates(array $jobs): array
    {
        $simulatedDates = [];
        foreach ($jobs as $job) {
            $simulatedDates[] = (string)$job['simulated_day'];
        }

        return array_unique($simulatedDates);
    }

}
