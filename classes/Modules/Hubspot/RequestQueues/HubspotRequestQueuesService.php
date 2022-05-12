<?php

namespace Xentral\Modules\Hubspot\RequestQueues;

use ApplicationCore;
use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\DatabaseExceptionInterface;
use Xentral\Modules\Hubspot\Exception\HubspotException;
use Xentral\Modules\Hubspot\HubspotEventService;
use Xentral\Modules\Hubspot\HubspotHttpResponseService;
use Xentral\Modules\Hubspot\RequestQueues\Exception\RequestQueuesException;
use RuntimeException;

final class HubspotRequestQueuesService
{

    /** @var int */
    const LOOP_WAITING_TIME = 10000000;
    /** @var int */
    const LOOP_BATCH = 1;

    /** @var HubspotRequestQueuesGateway $gateway */
    private $gateway;
    /** @var ApplicationCore $app */
    private $app;

    /** @var Database $db */
    private $db;

    /** @var array $completedIds */
    private $completedIds = [];

    /** @var HubspotEventService $eventService */
    private $eventService;

    /**
     * @param HubspotRequestQueuesGateway $gateway
     * @param Database                    $database
     * @param ApplicationCore             $app
     * @param HubspotEventService         $eventService
     */
    public function __construct(
        HubspotRequestQueuesGateway $gateway,
        Database $database,
        ApplicationCore $app,
        HubspotEventService $eventService
    ) {
        $this->gateway = $gateway;
        $this->app = $app;
        $this->db = $database;
        $this->eventService = $eventService;
    }

    /**
     * @param array $option
     *
     * @throws RequestQueuesException
     * @return int
     */
    public function addRequest($option)
    {
        $default = ['check_sum' => '', 'command' => '', 'on_after_done' => '', 'not_before' => 0, 'call_type' => ''];
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

        if (!empty($option['on_after_done']) && is_array($option['on_after_done'])) {
            $option['on_after_done'] = json_encode(
                $option['on_after_done'],
                JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
            );
        }

        $check = 'SELECT EXISTS(
            SELECT id FROM hubspot_request_queues 
            WHERE deleted=0 AND completed=0 AND check_sum=:check_sum AND runner=:runner
        )';
        if (empty(
        $this->db->fetchValue(
            $check,
            ['runner' => $option['runner'], 'check_sum' => $option['check_sum']]
        )
        )) {
            $add = 'INSERT INTO hubspot_request_queues (command, on_after_done, runner, not_before, check_sum, call_type ) 
                    VALUES (:command, :on_after_done, :runner, :not_before, :check_sum, :call_type)';
            try {
                $this->db->perform($add, $option);
            } catch (DatabaseExceptionInterface $exception) {
                throw new RequestQueuesException(json_encode(['too' => $option, $exception->getMessage()]));
            }

            return $this->db->lastInsertId();
        }

        return 0;
    }

    /**
     * @param string $callType
     *
     * @throws HubspotException
     * @throws RequestQueuesException
     *
     * @return void
     */
    public function execute(string $callType): void
    {
        $jobs = $this->gateway->getNewRequestsByCallType($callType);
        if (empty($jobs)) {
            return;
        }
        $batch_loop = self::LOOP_BATCH;
        $iCount = 0;
        $bSkippWait = count($jobs) <= 1;
        foreach ($jobs as $job) {
            $this->db->perform('UPDATE `hubspot_request_queues` SET try = try+1 WHERE id =:id', ['id' => $job['id']]);
            $oClass = $this->app->Container->get($job['runner']);
            $hCommand = json_decode($job['command'], true);
            if (!is_array($hCommand)) {
                continue;
            }
            $xArg = $hCommand['args'];
            $sMethod = $hCommand['method'];
            try {
                $response = call_user_func_array([$oClass, $sMethod], $xArg);
                if (empty($response)) {
                    continue;
                }
            } catch (RuntimeException $exception) {
                $this->db->perform(
                    'UPDATE hubspot_request_queues SET completed = 1 WHERE id = :id',
                    ['id' => $job['id']]
                );
                $this->completedIds[] = $job['id'];
                $this->eventService->add($exception->getMessage());
                continue;
            }
            $this->onAfterDone($job['id'], $response, $job['on_after_done']);

            if ($bSkippWait === false) {
                if ($iCount === $batch_loop) {
                    $batch_loop += self::LOOP_BATCH;
                    @usleep(self::LOOP_WAITING_TIME);
                }
                echo 'Script always alive... ';
            }
        }
    }

    /**
     * @param int                        $id
     * @param HubspotHttpResponseService|null $response
     * @param string                     $onAfter
     *
     * @throws HubspotException
     *
     * @return void
     */
    private function onAfterDone(int $id, ?HubspotHttpResponseService $response, string $onAfter = ''): void
    {
        $onAfterData = !empty($onAfter) ? json_decode($onAfter, true) : [];

        if (!empty($onAfterData) && array_key_exists('runner', $onAfterData) && array_key_exists(
                'method',
                $onAfterData
            ) && array_key_exists('args', $onAfterData) && $this->app->Container->has($onAfterData['runner'])) {
            $oClass = $this->app->Container->get($onAfterData['runner']);
            if (!empty($onAfterData['replace_in_args']) && in_array($response->getStatusCode(), [200, 204], true)) {
                $hasDataFetcher = !empty($onAfterData['data_fetcher']) && method_exists(
                        $this,
                        $onAfterData['data_fetcher']
                    );
                $jsonData = $hasDataFetcher === true ? $this->{$onAfterData['data_fetcher']}(
                    $response
                ) : $response->getJson();
                if (empty($jsonData)) {
                    return;
                }
                foreach ($onAfterData['args'] as &$xArg) {
                    if (is_string($xArg)) {
                        foreach ($onAfterData['replace_in_args'] as $replace_with) {
                            if (!empty($jsonData[$replace_with])) {
                                $xArg = sprintf($xArg, $jsonData[$replace_with]);
                            }
                        }
                    }
                }
            }
            call_user_func_array(
                [$oClass, $onAfterData['method']],
                $onAfterData['args']
            );
        }

        if (array_key_exists('event', $onAfterData) &&
            !empty($onAfterData['event']) &&
            is_string($onAfterData['event']) &&
            in_array($response->getStatusCode(), [200, 204], true)
        ) {
            $this->eventService->add($onAfterData['event']);
        }
        if (array_key_exists('other_event', $onAfterData)) {
            $other = $onAfterData['other_event'];
            if (!empty($other) && $this->app->Container->has($other['runner'])) {
                $otherEventClass = $this->app->Container->get($other['runner']);
                call_user_func_array(
                    [$otherEventClass, $other['method']],
                    $other['args']
                );
            }
        }

        if (is_numeric($id)) {
            $this->db->perform('UPDATE hubspot_request_queues SET completed = 1 WHERE id = :id', ['id' => $id]);
            $this->completedIds[] = $id;
        }
    }

    /**
     * @return void
     */
    public function cleanup()
    {
        foreach ($this->completedIds as $completedId) {
            echo $completedId;
            $this->db->perform(
                'DELETE FROM hubspot_request_queues WHERE id=:id AND completed=1',
                ['id' => $completedId]
            );
        }
        $this->eventService->deleteByInterval();
        unset($this->completedIds);
    }

    /**
     * @param HubspotHttpResponseService $response
     *
     * @return array
     */
    private function getEngagement(HubspotHttpResponseService $response): array
    {
        if ($response->getStatusCode() !== 200) {
            return [];
        }

        $result = $response->getJson();
        if (!array_key_exists('engagement', $result)) {
            return [];
        }

        return $result['engagement'];
    }
}
