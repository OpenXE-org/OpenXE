<?php


namespace Xentral\Modules\FiskalyApi\Data;


class Export
{
    /** @var string */
    private $uuId;

    /** @var string */
    private $type;

    /** @var string */
    private $env;

    /** @var string */
    private $tssId;

    /** @var string */
    private $state;

    /** @var string|null */
    private $href;

    /** @var int|null */
    private $timeRequest;

    /** @var int|null */
    private $timeStart;

    /** @var int|null */
    private $timeEnd;

    /**
     * Export constructor.
     *
     * @param string $uuId
     * @param string $type
     * @param string $env
     * @param string $tssId
     * @param string $state
     * @param string $href
     * @param int    $timeRequest
     * @param int    $timeStart
     * @param int    $timeEnd
     */
    public function __construct(
        string $uuId,
        string $type,
        string $env,
        string $tssId,
        string $state,
        ?string $href,
        ?int $timeRequest,
        ?int $timeStart,
        ?int $timeEnd
    ) {
        $this->uuId = $uuId;
        $this->type = $type;
        $this->env = $env;
        $this->tssId = $tssId;
        $this->state = $state;
        $this->href = $href;
        $this->timeRequest = $timeRequest;
        $this->timeStart = $timeStart;
        $this->timeEnd = $timeEnd;
    }

    /**
     * @param $apiResult
     *
     * @return static
     */
    public static function fromApiResult(object $apiResult): self
    {
        return new self(
            $apiResult->_id,
            $apiResult->_type,
            $apiResult->_env,
            $apiResult->tss_id,
            $apiResult->state,
            $apiResult->href ?? null,
            $apiResult->time_request ?? null,
            $apiResult->time_start ?? null,
            $apiResult->time_end ?? null
        );
    }

    /**
     * @return string
     */
    public function getUuId(): string
    {
        return $this->uuId;
    }

    /**
     * @param string $uuId
     */
    public function setUuId(string $uuId): void
    {
        $this->uuId = $uuId;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getEnv(): string
    {
        return $this->env;
    }

    /**
     * @param string $env
     */
    public function setEnv(string $env): void
    {
        $this->env = $env;
    }

    /**
     * @return string
     */
    public function getTssId(): string
    {
        return $this->tssId;
    }

    /**
     * @param string $tssId
     */
    public function setTssId(string $tssId): void
    {
        $this->tssId = $tssId;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return string|null
     */
    public function getHref(): ?string
    {
        return $this->href;
    }

    /**
     * @param string|null $href
     */
    public function setHref(?string $href): void
    {
        $this->href = $href;
    }

    /**
     * @return int|null
     */
    public function getTimeRequest(): ?int
    {
        return $this->timeRequest;
    }

    /**
     * @param int|null $timeRequest
     */
    public function setTimeRequest(?int $timeRequest): void
    {
        $this->timeRequest = $timeRequest;
    }

    /**
     * @return int|null
     */
    public function getTimeStart(): ?int
    {
        return $this->timeStart;
    }

    /**
     * @param int|null $timeStart
     */
    public function setTimeStart(?int $timeStart): void
    {
        $this->timeStart = $timeStart;
    }

    /**
     * @return int|null
     */
    public function getTimeEnd(): ?int
    {
        return $this->timeEnd;
    }

    /**
     * @param int|null $timeEnd
     */
    public function setTimeEnd(?int $timeEnd): void
    {
        $this->timeEnd = $timeEnd;
    }
}
