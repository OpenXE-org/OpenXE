<?php

namespace Xentral\Modules\Resubmission\Exception;

use DomainException;

class TaskMustBeCompletedException extends DomainException implements ResubmissionExceptionInterface
{
    /** @var string $requiredStageName */
    private $requiredStageName;

    /** @var string $currentStageName */
    private $currentStageName;

    /**
     * Exception wird geworfen wenn eine Aufgabe als "offen" angelegt werden soll
     * und die aktuelle Stage und die Einstellung in "Pflichtfeld ab Stage" das nicht zulässt.
     *
     * @param string $requiredStageName
     * @param string $currentStageName
     *
     * @return TaskMustBeCompletedException
     */
    public static function onCreation($requiredStageName, $currentStageName)
    {
        $instance = new self(sprintf(
            'The Task cannot be created. The task must be completed from stage "%s" on. ' .
            'The resubmission is currently in stage "%s".',
            $requiredStageName,
            $currentStageName
        ));

        $instance->requiredStageName = $requiredStageName;
        $instance->currentStageName = $currentStageName;

        return $instance;
    }

    /**
     * Exception wird geworfen wenn eine Aufgabe beim Bearbeiten auf "offen" angelegt werden soll
     * und die aktuelle Stage und die Einstellung in "Pflichtfeld ab Stage" das nicht zulässt.
     *
     * @param string $requiredStageName
     * @param string $currentStageName
     *
     * @return TaskMustBeCompletedException
     */
    public static function onModification($requiredStageName, $currentStageName)
    {
        $instance = new self(sprintf(
            'The Task modification is invalid. The task must be completed from stage "%s" on. ' .
            'The resubmission is currently in stage "%s".',
            $requiredStageName,
            $currentStageName
        ));

        $instance->requiredStageName = $requiredStageName;
        $instance->currentStageName = $currentStageName;

        return $instance;
    }

    /**
     * Exception wird geworfen wenn eine abgeschlossene Aufgabe zurück auf "offen" gestellt werden soll
     * und die aktuelle Stage und die Einstellung in "Pflichtfeld ab Stage" das nicht zulässt.
     *
     * @param $requiredStageName
     * @param $currentStageName
     *
     * @return TaskMustBeCompletedException
     */
    public static function onChangingStateToOpen($requiredStageName, $currentStageName)
    {
        $instance = new self(sprintf(
            'The Task cannot be changed to "open". The task needs to be completed from stage "%s" on. ' .
            'The resubmission is currently in stage "%s".',
            $requiredStageName,
            $currentStageName
        ));

        $instance->requiredStageName = $requiredStageName;
        $instance->currentStageName = $currentStageName;

        return $instance;
    }

    /**
     * @return string
     */
    public function getRequiredStageName()
    {
        return $this->requiredStageName;
    }

    /**
     * @return string
     */
    public function getCurrentStageName()
    {
        return $this->currentStageName;
    }
}
