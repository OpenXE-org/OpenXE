<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle;


interface SubscriptionCycleModuleInterface
{
    /**
     * @param $subscription
     * @param $customers
     * @param $documentType
     * @param $mailPrinter
     * @param $printerId
     * @param $simulatedDay
     *
     * @return mixed
     */
    public function generateAndSendSubscriptionCycleGroups(
        $subscription,
        $customers,
        $documentType,
        $mailPrinter,
        $printerId,
        $simulatedDay = null
    );

    /**
     * @param $subscription
     * @param $customers
     * @param $documentType
     * @param $printerId
     * @param $mailPrinter
     * @param $simulatedDay
     *
     * @return mixed
     */
    public function generateAndSendSubscriptionCycle(
        $subscription,
        $customers,
        $documentType,
        $printerId,
        $mailPrinter,
        $simulatedDay = null
    );
}
