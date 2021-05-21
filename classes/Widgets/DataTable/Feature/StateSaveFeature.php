<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\DataTableInterface;

final class StateSaveFeature implements DataTableFeatureInterface
{
    /** @var bool $enabled */
    private $enabled;

    /** @var int $duration In seconds (0 = Forever) */
    private $duration;

    /**
     * @param bool $enabled
     * @param int  $duration
     */
    public function __construct($enabled = true, $duration = 0)
    {
        $this->enabled = (bool)$enabled;
        $this->duration = (int)$duration;
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
//        $options = $table->getOptions()->toArray();
//        $options['columns'] = $table->getColumns()->toArray();
//        $table->getOptions()->setOption('revision', md5(json_encode($options)));

        $table->getOptions()->setOption('stateSave', $this->enabled);
        $table->getOptions()->setOption('stateDuration', $this->duration);
    }

    /**
     * @return void
     */
    public function enable()
    {
        $this->enabled = true;
    }

    /**
     * @return void
     */
    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int $duration
     *
     * @return void
     */
    public function setDuration($duration)
    {
        $this->duration = (int)$duration;
    }
}
