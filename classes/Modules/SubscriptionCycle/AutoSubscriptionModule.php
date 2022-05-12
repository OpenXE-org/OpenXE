<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle;

use Xentral\Modules\SubscriptionCycle\Data\SubscriptionCycleArticleData;
use Xentral\Modules\SubscriptionCycle\Data\SubscriptionCycleAutoSubscriptionData;
use Xentral\Modules\SubscriptionCycle\Exception\AutoSubscriptionNotFoundException;
use Xentral\Modules\SubscriptionCycle\Exception\RuntimeException;
use Xentral\Modules\SubscriptionCycle\Exception\ValidationFailedException;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleArticleService;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleAutoSubscriptionGateway;
use Xentral\Modules\SubscriptionCycle\Service\SubscriptionCycleAutoSubscriptionService;
use Xentral\Modules\SubscriptionCycle\Wrapper\BusinessLetterWrapper;

final class AutoSubscriptionModule
{
    /** @var  SubscriptionCycleAutosubScriptionService $autosubscriptionService */
    private $autoSubscriptionService;

    /** @var  SubscriptionCycleAutoSubscriptionGateway $autosubscriptionGateway */
    private $autoSubscriptionGateway;

    /** @var  SubscriptionCycleArticleService $subscriptionCycleArticleService */
    private $subscriptionCycleArticleService;

    /** @var  BusinessLetterWrapper $businessLetterWrapper */
    private $businessLetterWrapper;

    /**
     * @param SubscriptionCycleAutoSubscriptionService $autoSubscriptionService
     * @param SubscriptionCycleAutoSubscriptionGateway $autoSubscriptionGateway
     * @param SubscriptionCycleArticleService          $subscriptionCycleArticleService
     * @param BusinessLetterWrapper                    $businessLetterWrapper
     */
    public function __construct(
        SubscriptionCycleAutoSubscriptionService $autoSubscriptionService,
        SubscriptionCycleAutoSubscriptionGateway $autoSubscriptionGateway,
        SubscriptionCycleArticleService $subscriptionCycleArticleService,
        BusinessLetterWrapper $businessLetterWrapper
    ) {
        $this->autoSubscriptionService = $autoSubscriptionService;
        $this->autoSubscriptionGateway = $autoSubscriptionGateway;
        $this->subscriptionCycleArticleService = $subscriptionCycleArticleService;
        $this->businessLetterWrapper = $businessLetterWrapper;
    }

    /**
     * @param SubscriptionCycleAutoSubscriptionData $autosubscription
     *
     * @throws ValidationFailedException
     */
    public function saveNewAutoSubscription(SubscriptionCycleAutoSubscriptionData $autosubscription): void
    {
        $this->autoSubscriptionService->create($autosubscription);
    }

    /**
     * @param SubscriptionCycleAutoSubscriptionData $autosubscription
     *
     * @throws ValidationFailedException
     * @throws AutoSubscriptionNotFoundException
     */
    public function updateAutoSubscription(SubscriptionCycleAutoSubscriptionData $autosubscription): void
    {
        $this->autoSubscriptionService->edit($autosubscription);
    }

    /**
     * @param int $autoSubscriptionId
     *
     * @throws AutoSubscriptionNotFoundException
     *
     * @return SubscriptionCycleAutoSubscriptionData
     */
    public function getAutoSubscriptionById(int $autoSubscriptionId): SubscriptionCycleAutoSubscriptionData
    {
        $autosubscription = $this->autoSubscriptionGateway->getById($autoSubscriptionId);

        return $autosubscription;
    }

    /**
     * @param int $autoSubscriptionId
     *
     * @throws AutoSubscriptionNotFoundException
     * @throws RuntimeException
     */
    public function deleteAutoSubscriptionById(int $autoSubscriptionId): void
    {
        $this->autoSubscriptionService->removeById($autoSubscriptionId);
    }

    /**
     * @param int $docId
     */
    public function createSubscription(int $docId): void
    {
        $data = $this->autoSubscriptionGateway->findAutoSubscriptionData($docId);
        if (!empty($data)) {
            foreach ($data as $d) {
                $subscriptionArticle = SubscriptionCycleArticleData::fromArray($d);
                $this->subscriptionCycleArticleService->create($subscriptionArticle);

                if ($d['prevent_auto_dispatch'] == 1) {
                    $this->autoSubscriptionService->preventAutoDispatch($docId);
                }
            }

            $this->businessLetterWrapper->sendBusinessLetter($data, $docId);
        }
    }

    /**
     * @param int $docId
     *
     * @return bool
     */
    public function hasDocAutoSubscription(int $docId): bool
    {
        $data = $this->autoSubscriptionGateway->findAutoSubscriptionData($docId);
        if (!empty($data)) {
            return true;
        }

        return false;
    }
}
