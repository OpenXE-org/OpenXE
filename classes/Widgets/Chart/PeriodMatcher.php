<?php

namespace Xentral\Widgets\Chart;

use DateInterval;
use DatePeriod;
use DateTimeInterface;

class PeriodMatcher
{
    /** @var DateTimeInterface $start */
    protected $start;

    /** @var DateTimeInterface $end */
    protected $end;

    /** @var DateInterval $interval */
    protected $interval;

    /** @var string $format */
    protected $format;

    /** @var DatePeriod $range */
    protected $range;

  /**
     * @param DateTimeInterface $start
     * @param DateTimeInterface $end
     * @param DateInterval      $interval
     * @param string            $format Formate der PHP date()-Funktion
     */
    public function __construct(
        DateTimeInterface $start,
        DateTimeInterface $end,
        DateInterval $interval,
        $format = 'Y.m.d'
    ) {
        $this->start = $start;
        $this->end = $end;
        $this->interval = $interval;
        $this->format = $format;
        $this->range = new DatePeriod($start, $interval, $end);
    }

    /**
     * @param array  $data
     * @param string $dateKey
     * @param string $valueKey
     *
     * @return array
     */
    public function matchData($data, $dateKey, $valueKey)
    {
        if ($data === null) {
            $data = [];
        }
        $dates = array_column($data, $dateKey);
        $values = array_column($data, $valueKey);

        $result = [];
        foreach ($this->getDates() as $date) {
            $matchedKey = array_search($date, $dates, true);
            $result[] = $matchedKey !== false ? (float)$values[$matchedKey] : 0.0;
        }

        return $result;
    }

    /**
     * @param string $format Datumsformat überschreiben
     *
     * @return array
     */
    public function getDates($format = null)
    {
        $result = [];

        /** @var DateTimeInterface $date */
        foreach ($this->range as $date) {
            $result[] = $date->format($format !== null ? $format : $this->format);
        }

        return $result;
    }
}
