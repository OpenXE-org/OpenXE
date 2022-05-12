<?php

declare(strict_types=1);

namespace Xentral\Modules\LearningDashboard\Data;

final class Task implements \JsonSerializable
{
    /** @var array */
    private $data;

    /**
     * Task constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /** @return array */
    public function getProgress(): array
    {
        $stepCount = 0;
        $completedStepCount = 0;
        foreach ($this->data['step_groups'] as $stepGroupName => $stepGroupContent) {
            $stepCount++;

            if (isset($stepGroupContent['completed']) && $stepGroupContent['completed'] === true) {
                $completedStepCount++;
            }
        }

        return [
            'complete' => $completedStepCount,
            'total'    => $stepCount,
        ];
    }

    /** @return array */
    public function jsonSerialize()
    {
        return [
            'key'                 => $this->data['key'],
            'title'               => $this->data['title'],
            'category'            => $this->data['category'],
            'sub_title'           => $this->data['sub_title'],
            'description'         => $this->data['description'],
            'missing_modules'     => $this->data['missing_modules'],
            'missing_permissions' => $this->data['missing_permissions'],
            'link'                => "index.php?module=wizard&action=ajax&cmd=set_active_wizard&key={$this->data['key']}",
            'progress'            => $this->getProgress(),
        ];
    }
}
