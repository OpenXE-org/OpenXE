<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Data;

final class GoogleCalendarColorValue
{
    /** @var string $identifier */
    private $identifier;

    /** @var string $background */
    private $background;

    /** @var string $foregroud */
    private $foregroud;

    /**
     * @param string $identifier
     * @param string $background
     * @param string $foregroud
     */
    public function __construct(string $identifier, string $background, string $foregroud)
    {
        $this->identifier = $identifier;
        $this->background = $background;
        $this->foregroud = $foregroud;
    }

    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @return string color hex code
     */
    public function getBackground(): string
    {
        return $this->background;
    }

    /**
     * @return string color hex code
     */
    public function getForegroud(): string
    {
        return $this->foregroud;
    }
}
