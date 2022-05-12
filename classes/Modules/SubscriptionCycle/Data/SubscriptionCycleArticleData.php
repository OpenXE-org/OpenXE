<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Data;

use Xentral\Modules\SubscriptionCycle\Exception\ValidationFailedException;

final class SubscriptionCycleArticleData
{

    /** @var int $id */
    private $id = 0;

    /** @var int $sort */
    private $sort = 0;

    /** @var int $articleId */
    private $articleId = 0;

    /** @var string $articleName */
    private $articleName = '';

    /** @var string $articleNumber */
    private $articleNumber = '';

    /** @var float $amount */
    private $amount = 0.0;

    /** @var float $price */
    private $price = 0.0;

    /** @var string $taxClass */
    private $taxClass = '';

    /** @var float $discount */
    private $discount = 0.0;

    /** @var bool $cleared */
    private $cleared = false;

    /** @var string $startDate */
    private $startDate = '0000-00-00';

    /** @var string $deliveranceDate */
    private $deliveranceDate = '0000-00-00';

    /** @var string $clearedTill */
    private $clearedTill = '0000-00-00';

    /** @var bool $repeating */
    private $repeating = false;

    /** @var int $payCycle */
    private $payCycle = 0;

    /** @var string $clearedOn */
    private $clearedOn = '0000-00-00';

    /** @var int $invoiceId */
    private $invoiceId = 0;

    /** @var int $projectId */
    private $projectId = 0;

    /** @var int $adressId */
    private $adressId = 0;

    /** @var string $status */
    private $status = 'angelegt';

    /** @var string $text */
    private $text = '';

    /** @var string $logFile */
    private $logFile = '0000-00-00';

    /** @var string $description */
    private $description = '';

    /** @var string $document */
    private $document = '';

    /** @var string $priceType */
    private $priceType = '';

    /** @var string $endDate */
    private $endDate = '0000-00-00';

    /** @var int $createdBy */
    private $createdBy = 0;

    /** @var string $createdDate */
    private $createdDate = '0000-00-00';

    /** @var bool $expert */
    private $expert = false;

    /** @var string $currency */
    private $currency = '';

    /** @var bool $replaceDescription */
    private $replaceDescription = false;

    /** @var int $subscriptionCycleGroupId */
    private $subscriptionCycleGroupId = 0;

    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @throws ValidationFailedException
     *
     * @return SubscriptionCycleArticleData
     */
    public static function fromArray(array $data): SubscriptionCycleArticleData
    {
        $errors = self::validate($data);
        if (!empty($errors)) {
            throw ValidationFailedException::fromErrors($errors);
        }

        if (isset($data['id']) && !is_int($data['id'])) {
            $data['id'] = (int)$data['id'];
        }

        if (isset($data['menge']) && !is_float($data['menge'])) {
            $data['menge'] = (float)$data['menge'];
        }

        if (isset($data['preis']) && !is_float($data['preis'])) {
            $data['preis'] = (float)$data['preis'];
        }

        if (isset($data['rabatt']) && !is_float($data['rabatt'])) {
            $data['rabatt'] = (float)$data['rabatt'];
        }

        if (isset($data['abgerechnet']) && !is_bool($data['abgerechnet'])) {
            $data['abgerechnet'] = (bool)$data['abgerechnet'];
        }

        if (isset($data['wiederholend']) && !is_bool($data['wiederholend'])) {
            $data['wiederholend'] = (bool)$data['wiederholend'];
        }

        if (isset($data['beschreibungersetzten']) && !is_bool($data['beschreibungersetzten'])) {
            $data['beschreibungersetzten'] = (bool)$data['beschreibungersetzten'];
        }

        if (isset($data['experte']) && !is_bool($data['experte'])) {
            $data['experte'] = (bool)$data['experte'];
        }

        return self::fromDbState($data);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private static function validate(array $data): array
    {
        $errors = [];

        if (!isset($data['artikel']) || empty($data['artikel'])) {
            $errors['artikel'][] = 'Article_id is not set.';
        }

        if (!isset($data['menge']) || empty($data['menge'])) {
            $errors['menge'][] = 'Amount is not set';
        }

        if (!isset($data['preis']) || empty($data['preis'])) {
            $errors['preis'][] = 'Price is not set.';
        }

        if (!isset($data['dokument']) || empty($data['dokument'])) {
            $errors['dokument'][] = 'Document is not set.';
        }

        if (!isset($data['preisart']) || empty($data['preisart'])) {
            $errors['preisart'][] = 'Price-type is not set.';
        }

        return $errors;
    }

    /**
     * @param array $data
     *
     * @return SubscriptionCycleArticleData
     */
    public static function fromDbState(array $data): SubscriptionCycleArticleData
    {
        $instance = new self();

        if (isset($data['id'])) {
            $instance->id = $data['id'];
        }
        if (isset($data['sort'])) {
            $instance->sort = $data['sort'];
        }
        if (isset($data['artikel'])) {
            $instance->articleId = $data['artikel'];
        }
        if (isset($data['bezeichnung'])) {
            $instance->articleName = $data['bezeichnung'];
        }
        if (isset($data['nummer'])) {
            $instance->articleNumber = $data['nummer'];
        }
        if (isset($data['menge'])) {
            $instance->amount = $data['menge'];
        }
        if (isset($data['preis'])) {
            $instance->price = $data['preis'];
        }
        if (isset($data['steuerklasse'])) {
            $instance->taxClass = $data['steuerklasse'];
        }
        if (isset($data['rabatt'])) {
            $instance->discount = $data['rabatt'];
        }
        if (isset($data['abgerechnet'])) {
            $instance->cleared = $data['abgerechnet'];
        }
        if (isset($data['startdatum'])) {
            $instance->startDate = $data['startdatum'];
        }
        if (isset($data['lieferdatum'])) {
            $instance->deliveranceDate = $data['lieferdatum'];
        }
        if (isset($data['abgerechnetbis'])) {
            $instance->clearedTill = $data['abgerechnetbis'];
        }
        if (isset($data['wiederholend'])) {
            $instance->repeating = $data['wiederholend'];
        }
        if (isset($data['zahlzyklus'])) {
            $instance->payCycle = $data['zahlzyklus'];
        }
        if (isset($data['abgrechnetam'])) {
            $instance->clearedOn = $data['abgrechnetam'];
        }
        if (isset($data['rechnung'])) {
            $instance->invoiceId = $data['rechnung'];
        }
        if (isset($data['projekt'])) {
            $instance->projectId = $data['projekt'];
        }
        if (isset($data['adresse'])) {
            $instance->adressId = $data['adresse'];
        }
        if (isset($data['status'])) {
            $instance->status = $data['status'];
        }
        if (isset($data['bemerkung'])) {
            $instance->text = $data['bemerkung'];
        }
        if (isset($data['logdatei'])) {
            $instance->logFile = $data['logdatei'];
        }
        if (isset($data['beschreibung'])) {
            $instance->description = $data['beschreibung'];
        }
        if (isset($data['dokument'])) {
            $instance->document = $data['dokument'];
        }
        if (isset($data['enddatum'])) {
            $instance->endDate = $data['enddatum'];
        }
        if (isset($data['angelegtvon'])) {
            $instance->createdBy = $data['angelegtvon'];
        }
        if (isset($data['angelegtam'])) {
            $instance->createdDate = $data['angelegtam'];
        }
        if (isset($data['waehrung'])) {
            $instance->currency = $data['waehrung'];
        }
        if (isset($data['beschreibungersetzten'])) {
            $instance->replaceDescription = $data['beschreibungersetzten'];
        }
        if (isset($data['gruppe'])) {
            $instance->subscriptionCycleGroupId = $data['gruppe'];
        }
        if (isset($data['preisart'])) {
            $instance->priceType = $data['preisart'];
        }
        if (isset($data['experte'])) {
            $instance->expert = $data['experte'];
        }

        return $instance;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getSort(): int
    {
        return $this->sort;
    }

    /**
     * @return int
     */
    public function getArticleId(): int
    {
        return $this->articleId;
    }

    /**
     * @return string
     */
    public function getArticleName(): string
    {
        return $this->articleName;
    }

    /**
     * @return string
     */
    public function getArticleNumber(): string
    {
        return $this->articleNumber;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return string
     */
    public function getTaxClass(): string
    {
        return $this->taxClass;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @return bool
     */
    public function isCleared(): bool
    {
        return $this->cleared;
    }

    /**
     * @return string
     */
    public function getStartDate(): string
    {
        return $this->startDate;
    }

    /**
     * @return string
     */
    public function getDeliveranceDate(): string
    {
        return $this->deliveranceDate;
    }

    /**
     * @return string
     */
    public function getClearedTill(): string
    {
        return $this->clearedTill;
    }

    /**
     * @return bool
     */
    public function isRepeating(): bool
    {
        return $this->repeating;
    }

    /**
     * @return int
     */
    public function getPayCycle(): int
    {
        return $this->payCycle;
    }

    /**
     * @return string
     */
    public function getClearedOn(): string
    {
        return $this->clearedOn;
    }

    /**
     * @return int
     */
    public function getInvoiceId(): int
    {
        return $this->invoiceId;
    }

    /**
     * @return int
     */
    public function getProjectId(): int
    {
        return $this->projectId;
    }

    /**
     * @return int
     */
    public function getAdressId(): int
    {
        return $this->adressId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getLogFile(): string
    {
        return $this->logFile;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getDocument(): string
    {
        return $this->document;
    }

    /**
     * @return string
     */
    public function getPriceType(): string
    {
        return $this->priceType;
    }

    /**
     * @return string
     */
    public function getEndDate(): string
    {
        return $this->endDate;
    }

    /**
     * @return int
     */
    public function getCreatedBy(): int
    {
        return $this->createdBy;
    }

    /**
     * @return string
     */
    public function getCreatedDate(): string
    {
        return $this->createdDate;
    }

    /**
     * @return bool
     */
    public function isExpert(): bool
    {
        return $this->expert;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return bool
     */
    public function isReplaceDescription(): bool
    {
        return $this->replaceDescription;
    }

    /**
     * @return int
     */
    public function getSubscriptionCycleGroupId(): int
    {
        return $this->subscriptionCycleGroupId;
    }

}
