<?php

declare(strict_types=1);

namespace Xentral\Modules\LearningDashboard\Data;

final class Lesson implements \JsonSerializable
{
    /** @var string */
    private $name;

    /** @var array|Task[] */
    private $tasks;

    /**
     * Lesson constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /** @param Task $task */
    public function addTask(Task $task)
    {
        $this->tasks[] = $task;
    }

    public function getProgress()
    {
        $totalSteps = 0;
        $completedSteps = 0;

        foreach ($this->tasks as $task) {
            $taskProgress = $task->getProgress();
            $totalSteps += $taskProgress['total'];
            $completedSteps += $taskProgress['complete'];
        }

        return [
            'total'     => $totalSteps,
            'completed' => $completedSteps,
        ];
    }

    /** @return array */
    public function jsonSerialize()
    {
        return [
            'name'     => $this->name,
            'progress' => $this->getProgress(),
            'tasks'    => $this->tasks,
        ];
    }
}
