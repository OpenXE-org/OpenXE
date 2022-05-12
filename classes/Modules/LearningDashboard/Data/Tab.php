<?php

declare(strict_types=1);

namespace Xentral\Modules\LearningDashboard\Data;

final class Tab implements \JsonSerializable
{
    /** @var string */
    private $name;

    /** @var Lesson[] */
    private $lessons;

    /**
     * Tab constructor.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /** @param Lesson $lesson */
    public function addLesson(Lesson $lesson)
    {
        $this->lessons[] = $lesson;
    }

    /** @return array */
    public function jsonSerialize()
    {
        $totalSteps = 0;
        $completedSteps = 0;

        foreach ($this->lessons as $lesson) {
            $lessonProgress = $lesson->getProgress();
            $totalSteps += $lessonProgress['total'];
            $completedSteps += $lessonProgress['completed'];
        }

        return [
            'name'     => $this->name,
            'progress' => [
                'total'     => $totalSteps,
                'completed' => $completedSteps,
            ],
            'lessons'  => $this->lessons,
        ];
    }
}
