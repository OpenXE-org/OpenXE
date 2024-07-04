<?php
/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 * SPDX-FileCopyrightText: 2019 Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Scheduler;


use ApplicationCore;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Logger\Logger;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleJobService;
use Xentral\Modules\SubscriptionCycle\SubscriptionCycleModuleInterface;
use Xentral\Modules\SubscriptionCycle\SubscriptionModuleInterface;

final class SubscriptionCycleManualJobTask
{
  private ApplicationCore $app;
  private Database $db;
  private SubscriptionCycleJobService $cycleJobService;
  private TaskMutexServiceInterface $taskMutexService;
  private SubscriptionModuleInterface $subscriptionModule;

  /**
   * SubscriptionCycleManualJobTask constructor.
   *
   * @param ApplicationCore $app
   * @param Database $db
   * @param TaskMutexServiceInterface $taskMutexService
   * @param SubscriptionCycleJobService $cycleJobService
   * @param SubscriptionModuleInterface $subscriptionModule
   */
    public function __construct(
        ApplicationCore $app,
        Database $db,
        TaskMutexServiceInterface $taskMutexService,
        SubscriptionCycleJobService $cycleJobService,
        SubscriptionModuleInterface $subscriptionModule
    ) {
        $this->app = $app;
        $this->db = $db;
        $this->taskMutexService = $taskMutexService;
        $this->cycleJobService = $cycleJobService;
        $this->subscriptionModule = $subscriptionModule;
    }

    public function execute(): void
    {
        if ($this->taskMutexService->isTaskInstanceRunning('rechnungslauf_manual')) {
            return;
        }
        $this->taskMutexService->setMutex('rechnungslauf_manual');
        $jobs = $this->cycleJobService->listAll(100);
        foreach ($jobs as $job) {
            try {
                switch ($job['document_type']) {
                    case 'rechnung':
                        $this->subscriptionModule->CreateInvoice((int)$job['address_id']);
                        break;
                    case 'auftrag':
                        $this->subscriptionModule->CreateOrder((int)$job['address_id']);
                        break;
                }
            }
            catch (Exception $e) {
                /** @var Logger $logger */
                $logger = $this->app->Container->get('Logger');
                $logger->error($e->getMessage(), $job);
            } finally {
                $this->cycleJobService->delete((int)$job['id']);
            }
        }
    }

    public function cleanup(): void
    {
        $this->taskMutexService->setMutex('rechnungslauf_manual', false);
    }
}
