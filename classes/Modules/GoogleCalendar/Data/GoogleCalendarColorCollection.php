<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Data;

use Xentral\Modules\GoogleCalendar\Exception\InvalidArgumentException;

final class GoogleCalendarColorCollection
{
    /** @var GoogleCalendarColorValue[] $eventColors */
    private $eventColors;

    /** @var GoogleCalendarColorValue[] $eventColors */
    private $calendarColors;

    /** @var string $defaultColorId */
    private $defaultColorId;

    /**
     * @param GoogleCalendarColorValue[] $eventColors
     * @param GoogleCalendarColorValue[] $calendarColors
     */
    private function __construct(array $eventColors, array $calendarColors)
    {
        $this->eventColors = $eventColors;
        $this->calendarColors = $calendarColors;
    }

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleCalendarColorCollection
     */
    public static function createFromJsonArray(array $data): GoogleCalendarColorCollection
    {
        if (!isset($data['kind'], $data['event'], $data['calendar']) || $data['kind'] !== 'calendar#colors') {
            throw new InvalidArgumentException('Invalid Json Format for this resource.');
        }
        $eventColors = [];
        foreach ($data['event'] as $index => $ec) {
            $eventColors[] = new GoogleCalendarColorValue((string)$index, $ec['background'], $ec['foreground']);
        }
        $calendarColors = [];
        foreach ($data['calendar'] as $index => $ec) {
            $calendarColors[] = new GoogleCalendarColorValue((string)$index, $ec['background'], $ec['foreground']);
        }

        return new self($eventColors, $calendarColors);
    }

    /**
     * @param string $colorId
     *
     * @return void
     */
    public function setDefaultColorId(string $colorId): void
    {
        $this->defaultColorId = $colorId;
    }

    /**
     * @return GoogleCalendarColorValue|null
     */
    public function getDefaultColor(): ?GoogleCalendarColorValue
    {
        if ($this->defaultColorId === null) {
            return null;
        }

        return $this->getCalendarColorById($this->defaultColorId);
    }

    /**
     * @param string $colorId
     *
     * @return GoogleCalendarColorValue|null
     */
    public function getEventColorById(string $colorId): ?GoogleCalendarColorValue
    {
        foreach ($this->eventColors as $color) {
            if ($color->getIdentifier() === $colorId) {
                return $color;
            }
        }

        return null;
    }

    /**
     * @return GoogleCalendarColorValue[]
     */
    public function getAllEventColors(): array
    {
        return array_values($this->eventColors);
    }

    /**
     * @param string $colorId
     *
     * @return GoogleCalendarColorValue|null
     */
    public function getCalendarColorById(string $colorId): ?GoogleCalendarColorValue
    {
        foreach ($this->calendarColors as $color) {
            if ($color->getIdentifier() === $colorId) {
                return $color;
            }
        }

        return null;
    }

    /**
     * @return GoogleCalendarColorValue[]
     */
    public function getAllCalendarColors(): array
    {
        return array_values($this->calendarColors);
    }
}
