<?php

namespace Xentral\Modules\Api\Dashboard;

use Xentral\Modules\Api\Resource\Result\AbstractResult;

final class WidgetResult extends AbstractResult
{
    /**
     * @param array $data
     * @param array $pagination
     */
    public function __construct(array $data, array $pagination = null)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = [];
        foreach ($this->data as $item) {
            /** @var WidgetData $item */
            $data[] = $item->toArray();
        }

        return $data;
    }

    /**
     * @param WidgetData $widgetData
     */
    public function addData(WidgetData $widgetData)
    {
        $this->data[] = $widgetData;
    }
}
