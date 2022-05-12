<?php

declare(strict_types=1);

namespace Xentral\Modules\PaymentMethod\Data;


use Xentral\Modules\PaymentMethod\Exception\InvalidArgumentException;

final class PaymentMethodData
{
    /** @var int $id */
    private $id;

    /** @var int $projectId */
    private $projectId;

    /** @var string $module */
    private $module;

    /** @var string $name */
    private $name;

    /** @var string $type */
    private $type;

    /** @var bool $active */
    private $active;

    /** @var string $paymentBehavior */
    private $paymentBehavior;

    /** @var bool $autoPayed */
    private $autoPayed;

    /** @var bool $autoPayedLiability */
    private $autoPayedLiability;

    /** @var string $text */
    private $text;

    /** @var array $settings */
    private $settings;

    /** @var bool $deleted */
    private $deleted;

    /**
     * PaymentMethod constructor.
     *
     * @param int        $id
     * @param string     $module
     * @param string     $name
     * @param string     $type
     * @param bool       $active
     * @param int        $projectId
     * @param string     $paymentBehavior
     * @param bool       $autoPayed
     * @param bool       $autoPayedLiability
     * @param string     $text
     * @param array|null $settings
     *
     * @throws InvalidArgumentException
     */
    public function __construct(
        int $id,
        string $module,
        string $name,
        string $type,
        bool $active,
        int $projectId,
        string $paymentBehavior,
        bool $autoPayed,
        bool $autoPayedLiability,
        string $text,
        ?array $settings = [],
        bool $deleted = false
    ) {
        self::ensureBehavior($paymentBehavior);
        $this->setName($name);
        $this->setType($type);

        $this->id = $id;
        $this->module = $module;
        $this->name = $name;
        $this->type = $type;
        $this->active = $active;
        $this->projectId = $projectId;
        $this->paymentBehavior = $paymentBehavior;
        $this->autoPayed = $autoPayed;
        $this->autoPayedLiability = $autoPayedLiability;
        $this->text = $text;
        $this->settings = $settings;
        $this->deleted = $deleted;
    }

    /**
     * @param array $array
     *
     * @throws InvalidArgumentException
     *
     * @return PaymentMethodData
     */
    public static function fromDbState(array $array): self
    {
        $requiredProperties = [
            'id',
            'type',
            'bezeichnung',
            'verhalten',
            'aktiv',
            'projekt',
            'automatischbezahlt',
            'automatischbezahltverbindlichkeit',
            'freitext',
            'einstellungen_json',
            'geloescht',
        ];
        foreach ($requiredProperties as $propertyName) {
            if (!isset($array[$propertyName])) {
                throw new InvalidArgumentException(sprintf("Required property '%s' is missing.", $propertyName));
            }
        }

        self::ensureBehavior($array['verhalten']);
        self::ensureNotEmptyString($array['bezeichnung'], 'name');
        self::ensureNotEmptyString($array['type'], 'type');
        
        if ((string)$array['einstellungen_json'] !== '') {
            $settings = json_decode($array['einstellungen_json'], true);
            if (!is_array($settings) && $settings !== null) {
                throw new InvalidArgumentException(
                    sprintf('settings \'%s\' is no valid JSON', $array['einstellungen_json'])
                );
            }
        } else {
            $settings = null;
        }

        $self = new self(
            (int)$array['id'],
            (string)$array['modul'],
            (string)$array['bezeichnung'],
            (string)$array['type'],
            (bool)$array['aktiv'],
            (int)$array['projekt'],
            (string)$array['verhalten'],
            (bool)$array['automatischbezahlt'],
            (bool)$array['automatischbezahltverbindlichkeit'],
            (string)$array['freitext'],
            $settings,
            (bool)$array['geloescht']
        );

        return $self;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'                                => $this->id,
            'bezeichnung'                       => $this->name,
            'type'                              => $this->type,
            'aktiv'                             => (int)$this->active,
            'projekt'                           => $this->projectId,
            'verhalten'                         => $this->paymentBehavior,
            'automatischbezahlt'                => (int)$this->autoPayed,
            'automatischbezahltverbindlichkeit' => (int)$this->autoPayedLiability,
            'freitext'                          => $this->text,
            'einstellungen_json'                => (string)json_encode($this->settings),
            'geloescht'                         => (int)$this->deleted,
            'modul'                             => (string)$this->module,
        ];
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @param $projectId
     *
     * @return void
     */
    public function setProjectId($projectId): void
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function getModule(): string
    {
        return $this->module;
    }

    /**
     * @param string $module
     *
     * @return void
     */
    public function setModule(string $module): void
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setName(string $name): void
    {
        if ($name === '') {
            throw new InvalidArgumentException('Name is empty');
        }
        $this->name = $name;
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
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setType(string $type): void
    {
        if ($type === '') {
            throw new InvalidArgumentException('Type is empty');
        }
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPaymentBehavior(): string
    {
        return $this->paymentBehavior;
    }

    /**
     * @param string $paymentBehavior
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    public function setPaymentBehavior(string $paymentBehavior): void
    {
        self::ensureBehavior($paymentBehavior);
        $this->paymentBehavior = $paymentBehavior;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return void
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isAutoPayed(): bool
    {
        return $this->autoPayed;
    }

    /**
     * @param bool $autoPayed
     *
     * @return void
     */
    public function setAutoPayed(bool $autoPayed): void
    {
        $this->autoPayed = $autoPayed;
    }

    /**
     * @return bool
     */
    public function isAutoPayedLiability(): bool
    {
        return $this->autoPayedLiability;
    }


    /**
     * @param bool $autoPayedLiability
     *
     * @return void
     */
    public function setAutoPayedLiability(bool $autoPayedLiability): void
    {
        $this->autoPayedLiability = $autoPayedLiability;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     *
     * @return void
     */
    public function setText(string $text): void
    {
        $this->text = $text;
    }

    /**
     * @return array|null
     */
    public function getSettings(): ?array
    {
        return $this->settings;
    }

    /**
     * @param array|null $setting
     *
     * @return void
     */
    public function setSettings(?array $setting): void
    {
        $this->settings = $setting;
    }

    /**
     * @return bool
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     *
     * @return void
     */
    public function setDeleted(bool $deleted): void
    {
        $this->deleted = $deleted;
    }

    /**
     * @return string[]
     */
    public static function getValidBehaviourOptions(): array
    {
        return [
            '',
            'rechnung',
            'vorkasse',
            'lastschrift',
        ];
    }

    /**
     * @param string $behavior
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private static function ensureBehavior(string $behavior): void
    {
        if (!in_array($behavior, self::getValidBehaviourOptions(), true)) {
            throw new InvalidArgumentException(sprintf('Invalid Payment Behavior: %s', $behavior));
        }
    }

    /**
     * @param string $value
     * @param string $name
     *
     * @throws InvalidArgumentException
     *
     * @return void
     */
    private static function ensureNotEmptyString(string $value, string $name): void
    {
        if ($value === '') {
            throw new InvalidArgumentException(sprintf('%s is empty', $name));
        }
    }
}
