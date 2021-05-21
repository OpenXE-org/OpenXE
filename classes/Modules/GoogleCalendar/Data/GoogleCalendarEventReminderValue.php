<?php

declare(strict_types=1);

namespace Xentral\Modules\GoogleCalendar\Data;

use Xentral\Modules\GoogleCalendar\Exception\InvalidArgumentException;

final class GoogleCalendarEventReminderValue
{
    /** @var string METHOD_EMAIL */
    public const METHOD_EMAIL = 'email';

    /** @var string METHOD_POPUP */
    public const METHOD_POPUP = 'popup';

    /** @var string $method */
    private $method;

    /** @var int $minutes */
    private $minutes;

    /**
     * @param string $method
     * @param int $minutes
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $method, int $minutes)
    {
        if ($method !== self::METHOD_EMAIL && $method !== self::METHOD_POPUP) {
            throw new InvalidArgumentException(
                'Invalid notification method; only "email" and "popup" are allowed'
            );
        }
        $this->method = $method;
        $this->minutes = $minutes;
    }

    /**
     * @param array $data
     *
     * @throws InvalidArgumentException
     *
     * @return GoogleCalendarEventReminderValue
     */
    public static function fromArray(array $data): GoogleCalendarEventReminderValue
    {
        if (!isset($data['method'], $data['minutes'])) {
            throw new InvalidArgumentException('method and minutes required for notification values.');
        }

        return new self($data['method'], $data['minutes']);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $data = [];
        $data['method'] = $this->getMethod();
        $data['minutes'] = $this->getMinutes();

        return $data;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return int
     */
    public function getMinutes(): int
    {
        return $this->minutes;
    }
}
