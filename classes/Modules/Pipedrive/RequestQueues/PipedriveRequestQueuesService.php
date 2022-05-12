<?php

declare(strict_types=1);

namespace Xentral\Modules\Pipedrive\RequestQueues;

use ApplicationCore;
use Exception;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Core\DependencyInjection\Exception\ServiceNotFoundException;
use Xentral\Modules\Pipedrive\Exception\PipedriveConfigurationException;
use Xentral\Modules\Pipedrive\Exception\PipedriveEventException;
use Xentral\Modules\Pipedrive\Exception\PipedriveMetaException;
use Xentral\Modules\Pipedrive\Exception\PipedriveRequestQueuesException;
use Xentral\Modules\Pipedrive\Service\PipedriveConfigurationService;
use Xentral\Modules\Pipedrive\Service\PipedriveEventService;
use Xentral\Modules\Pipedrive\Service\PipedriveServerResponseInterface;
use DateTime;
use RuntimeException;

final class PipedriveRequestQueuesService
{

    /** @var int */
    private const _WAITING_TIME = 10000000;

    /** @var int */
    private const _BATCH = 1;

    /** @var PipedriveRequestQueuesGateway $gateway */
    private $gateway;

    /** @var ApplicationCore $app */
    private $app;

    /** @var Database $db */
    private $db;

    /** @var array $completedIds */
    private $completedIds = [];

    /** @var PipedriveConfigurationService $confService */
    private $confService;

    /** @var PipedriveEventService $eventService */
    private $eventService;

    /**
     * @param PipedriveRequestQueuesGateway $gateway
     * @param Database                      $database
     * @param ApplicationCore               $app
     * @param PipedriveConfigurationService $confService
     * @param PipedriveEventService         $eventService
     */
    public function __construct(
        PipedriveRequestQueuesGateway $gateway,
        Database $database,
        ApplicationCore $app,
        PipedriveConfigurationService $confService,
        PipedriveEventService $eventService
    ) {
        $this->gateway = $gateway;
        $this->app = $app;
        $this->db = $database;
        $this->confService = $confService;
        $this->eventService = $eventService;
    }

    /**
     * @param array $option
     *
     * @throws PipedriveRequestQueuesException
     *
     * @return int
     */
    public function addRequest(array $option): int
    {
        $default = [
            'check_sum'     => '',
            'command'       => '',
            'on_after_done' => '',
            'not_before'    => 0,
            'call_type'     => 'pipedrive',
            'is_looped'     => 0,
            'setting_name'  => '',
        ];

        $hCommand = [];
        if (array_key_exists('method', $option)) {
            $hCommand['method'] = $option['method'];
            unset($option['method']);
        }
        if (array_key_exists('args', $option)) {
            $hCommand['args'] = $option['args'];
            unset($option['args']);
        }
        if (!empty($hCommand)) {
            $option['command'] = json_encode($hCommand, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT);
        }

        if (!empty($option['check_sum'])) {
            $default['check_sum'] = $option['check_sum'];
        } else {
            $default['check_sum'] = array_key_exists('command', $option) ? md5($option['command']) : '';
        }

        $option = array_merge($default, $option);

        if (!array_key_exists('runner', $option)) {
            throw new PipedriveRequestQueuesException('Runner is missing!');
        }

        if (!empty($option['on_after_done']) && is_array($option['on_after_done'])) {
            $option['on_after_done'] = json_encode(
                $option['on_after_done'],
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
            );
        }

        $check = 'SELECT EXISTS(
            SELECT prq.id FROM `pipedrive_request_queues` AS `prq`
            WHERE prq.deleted=0 AND prq.completed=0 AND prq.check_sum=:check_sum AND prq.runner=:runner
        )';
        if (empty(
        $this->db->fetchValue(
            $check,
            ['runner' => $option['runner'], 'check_sum' => $option['check_sum']]
        )
        )) {
            $add = 'INSERT INTO `pipedrive_request_queues` (`command`, `on_after_done`, `runner`, `not_before`, `check_sum`, `call_type`, `is_looped`, `setting_name`, `created_at` )
                    VALUES (:command, :on_after_done, :runner, :not_before, :check_sum, :call_type, :is_looped, :setting_name, NOW())';
            try {
                $this->db->perform($add, $option);
            } catch (DatabaseExceptionInterface $exception) {
                throw new PipedriveRequestQueuesException($exception->getMessage());
            }

            return $this->db->lastInsertId();
        }

        return 0;
    }

    /**
     * @param string|null $callType
     *
     * @throws PipedriveConfigurationException
     * @throws PipedriveEventException
     * @throws PipedriveRequestQueuesException
     * @throws PipedriveMetaException
     * @throws Exception
     *
     * @return void
     */
    public function execute(?string $callType = null): void
    {
        $callType = $callType ?? 'pipedrive';
        $jobs = $this->gateway->getNewRequestsByCallType($callType);
        if (!empty($jobs)) {
            $batch_loop = self::_BATCH;
            $iCount = 0;
            $bSkipWait = count($jobs) <= 1;
            foreach ($jobs as $job) {
                $settings = $this->confService->getSettings();

                // HANDLE CAN EXECUTE
                if (!empty($job['setting_name']) && array_key_exists(
                        $job['setting_name'],
                        $settings
                    ) && $settings[$job['setting_name']] === false) {
                    // SKIP
                    continue;
                }

                // HANDLE is_looped
                if (!empty($job['is_looped']) && !empty($job['setting_name'])) {
                    $settingInterval = sprintf('%s_interval', $job['setting_name']);
                    if (!array_key_exists($settingInterval, $settings)) {
                        // SKIP or ERROR?
                        continue;
                    }
                    $interval = $settings[$settingInterval];
                    $modifiedAt = new DateTime($job['modified_at']);
                    $iModifiedAt = $modifiedAt->getTimestamp();
                    if (time() < $iModifiedAt + $interval) {
                        // SKIP, waiting for next running time
                        continue;
                    }
                }

                if (!empty($job['is_looped'])) {
                    $this->db->perform(
                        'UPDATE `pipedrive_request_queues` SET `modified_at` = NOW() WHERE `id` = :id',
                        ['id' => $job['id']]
                    );
                } else {
                    $this->db->perform(
                        'UPDATE `pipedrive_request_queues` 
                         SET `amount_attempts` = `amount_attempts` +1 WHERE `id` = :id',
                        ['id' => $job['id']]
                    );
                }

                try {
                    $oClass = $this->app->Container->get($job['runner']);
                } catch (ServiceNotFoundException $exception) {
                    throw new PipedriveRequestQueuesException($exception->getMessage());
                }


                $hCommand = json_decode($job['command'], true);
                if (is_array($hCommand)) {
                    $xArg = $hCommand['args'];
                    $sMethod = $hCommand['method'];
                    try {
                        /** @var PipedriveServerResponseInterface $xReturn */
                        $xReturn = call_user_func_array([$oClass, $sMethod], $xArg);
                    } catch (RuntimeException $exception) {
                        $this->eventService->add($exception->getMessage());
                        continue;
                    }

                    $isLooped = !empty($job['is_looped']);

                    if (empty($job['on_after_done'])) {
                        $this->markJobAsComplete($job['id'], $isLooped);
                    } else {
                        $this->onAfterDone($job['id'], $xReturn, $job['on_after_done'], $isLooped);
                    }

                    //BATCH PROCESS CHECK - take a nap
                    if ($bSkipWait === false) {
                        if ($iCount === $batch_loop) {
                            $batch_loop += self::_BATCH;
                            @usleep(self::_WAITING_TIME);
                        }
                        echo 'Script always alive... ';
                    }
                } else {
                    continue;
                }
            }
        }
    }

    /**
     * @param int                                   $id
     * @param PipedriveServerResponseInterface|null $response
     * @param string|null                           $onAfter
     * @param bool                                  $looped
     *
     * @throws PipedriveEventException
     *
     * @return void
     */
    private function onAfterDone(
        int $id,
        ?PipedriveServerResponseInterface $response,
        ?string $onAfter = null,
        bool $looped = false
    ): void {
        $hOnAfter = !empty($onAfter) ? json_decode($onAfter, true) : [];

        if (!empty($hOnAfter) && array_key_exists('runner', $hOnAfter) && array_key_exists(
                'method',
                $hOnAfter
            ) && array_key_exists('args', $hOnAfter)) {
            $oClass = $this->app->Container->get($hOnAfter['runner']);
            if (!empty($hOnAfter['replace_in_args']) && in_array($response->getStatusCode(), [200, 201], true)) {
                $jsonData = $response->getData();
                foreach ($hOnAfter['args'] as &$xArg) {
                    if (is_string($xArg)) {
                        foreach ($hOnAfter['replace_in_args'] as $replace_with) {
                            if (!empty($jsonData[$replace_with])) {
                                $xArg = sprintf($xArg, $jsonData[$replace_with]);
                            }
                        }
                    }
                }
            }
            unset($xArg);
            try {
                call_user_func_array(
                    [$oClass, $hOnAfter['method']],
                    $hOnAfter['args']
                );

                // ADD EVENT
                if (array_key_exists('event', $hOnAfter) && !empty($hOnAfter['event']) && is_string(
                        $hOnAfter['event']
                    ) &&
                    in_array($response->getStatusCode(), [200, 201], true)) {
                    $this->eventService->add($hOnAfter['event']);
                }
            } catch (RuntimeException $exception) {
                $this->eventService->add($exception->getMessage());
            }
        }

        $this->markJobAsComplete($id, $looped);
    }

    /**
     * @param int  $id
     * @param bool $looped
     *
     * @return void
     */
    private function markJobAsComplete(int $id, bool $looped): void
    {
        if (is_numeric($id) && $looped === false) {
            $this->db->perform('UPDATE `pipedrive_request_queues` SET `completed` = 1 WHERE `id` = :id', ['id' => $id]);
            $this->completedIds[] = $id;
        }
    }

    /**
     * @return void
     */
    public function cleanup(): void
    {
        $this->db->perform('DELETE FROM `pipedrive_request_queues` WHERE `completed` = 1');
        $this->eventService->deleteByInterval();

        unset($this->completedIds);
    }
}
