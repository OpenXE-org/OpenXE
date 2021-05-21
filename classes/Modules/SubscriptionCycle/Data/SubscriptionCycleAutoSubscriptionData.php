<?php

declare(strict_types=1);

namespace Xentral\Modules\SubscriptionCycle\Data;

use Xentral\Modules\SubscriptionCycle\Exception\ValidationFailedException;

final class SubscriptionCycleAutoSubscriptionData
{
    /** @var int $id */
    private $id = 0;

    /** @var int $projectId */
    private $projectId = 0;

    /** @var int $articleId */
    private $articleId = 0;

    /** @var string $priceCycle */
    private $priceCycle = '';

    /** @var string $documentType */
    private $documentType = '';

    /** @var int $subscriptionGroupId */
    private $subscriptionGroupId = 0;

    /** @var int $position */
    private $position = 0;

    /** @var string $firstDateType */
    private $firstDateType = '';

    /** @var bool $preventAutoDispatch */
    private $preventAutoDispatch = true;

    /** @var bool $autoEmailConfirmation */
    private $autoEmailConfirmation = true;

    /** @var int $businessLetterPatternId */
    private $businessLetterPatternId = 0;

    /** @var bool $addPdf */
    private $addPdf = true;

    private function __construct()
    {
    }

    /**
     * @param array $data
     *
     * @throws ValidationFailedException
     *
     * @return SubscriptionCycleAutoSubscriptionData
     */
    public static function fromArray(array $data): SubscriptionCycleAutoSubscriptionData
    {
        $errors = self::validate($data);
        if (!empty($errors)) {
            throw ValidationFailedException::fromErrors($errors);
        }

        if (isset($data['id']) && !is_int($data['id'])) {
            $data['id'] = (int)$data['id'];
        }

        if (isset($data['project_id']) && !is_int($data['project_id'])) {
            $data['project_id'] = (int)$data['project_id'];
        }

        if (isset($data['article_id']) && !is_int($data['article_id'])) {
            $data['article_id'] = (int)$data['article_id'];
        }

        if (isset($data['subscription_group_id']) && !is_int($data['subscription_group_id'])) {
            $data['subscription_group_id'] = (int)$data['subscription_group_id'];
        }

        if (isset($data['position']) && !is_int($data['position'])) {
            $data['position'] = (int)$data['position'];
        }

        if (isset($data['prevent_auto_dispatch']) && !is_bool($data['prevent_auto_dispatch'])) {
            $data['prevent_auto_dispatch'] = (bool)$data['prevent_auto_dispatch'];
        }

        if (isset($data['auto_email_confirmation']) && !is_bool($data['auto_email_confirmation'])) {
            $data['auto_email_confirmation'] = (bool)$data['auto_email_confirmation'];
        }

        if (isset($data['business_letter_pattern_id']) && !is_int($data['business_letter_pattern_id'])) {
            $data['business_letter_pattern_id'] = (int)$data['business_letter_pattern_id'];
        }

        if (isset($data['add_pdf']) && !is_bool($data['add_pdf'])) {
            $data['add_pdf'] = (bool)$data['add_pdf'];
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

        if (!isset($data['article_id']) || empty($data['article_id'])) {
            $errors['article_id'][] = 'Article-id is not set.';
        }

        return $errors;
    }

    /**
     * @param array $data
     *
     * @return SubscriptionCycleAutoSubscriptionData
     */
    public static function fromDbState(array $data): SubscriptionCycleAutoSubscriptionData
    {
        $instance = new self();

        if (isset($data['id'])) {
            $instance->id = $data['id'];
        }

        if (isset($data['project_id'])) {
            $instance->projectId = $data['project_id'];
        }

        if (isset($data['article_id'])) {
            $instance->articleId = $data['article_id'];
        }

        if (isset($data['price_cycle'])) {
            $instance->priceCycle = $data['price_cycle'];
        }

        if (isset($data['document_type'])) {
            $instance->documentType = $data['document_type'];
        }

        if (isset($data['subscription_group_id'])) {
            $instance->subscriptionGroupId = $data['subscription_group_id'];
        }

        if (isset($data['position'])) {
            $instance->position = $data['position'];
        }

        if (isset($data['first_date_type'])) {
            $instance->firstDateType = $data['first_date_type'];
        }

        if (isset($data['prevent_auto_dispatch'])) {
            $instance->preventAutoDispatch = $data['prevent_auto_dispatch'];
        }

        if (isset($data['auto_email_confirmation'])) {
            $instance->autoEmailConfirmation = $data['auto_email_confirmation'];
        }

        if (isset($data['business_letter_pattern_id'])) {
            $instance->businessLetterPatternId = $data['business_letter_pattern_id'];
        }

        if (isset($data['add_pdf'])) {
            $instance->addPdf = $data['add_pdf'];
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
    public function getProjectId(): int
    {
        return $this->projectId;
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
    public function getPriceCycle(): string
    {
        return $this->priceCycle;
    }

    /**
     * @return string
     */
    public function getDocumentType(): string
    {
        return $this->documentType;
    }

    /**
     * @return int
     */
    public function getSubscriptionGroupId(): int
    {
        return $this->subscriptionGroupId;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @return string
     */
    public function getFirstDateType(): string
    {
        return $this->firstDateType;
    }

    /**
     * @return bool
     */
    public function getPreventAutoDispatch(): bool
    {
        return $this->preventAutoDispatch;
    }

    /**
     * @return bool
     */
    public function getAutoEmailConfirmation(): bool
    {
        return $this->autoEmailConfirmation;
    }

    /**
     * @return int
     */
    public function getBusinessLetterPatternId(): int
    {
        return $this->businessLetterPatternId;
    }

    /**
     * @return bool
     */
    public function getAddPdf(): bool
    {
        return $this->addPdf;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'                         => $this->id,
            'project_id'                 => $this->projectId,
            'article_id'                 => $this->articleId,
            'price_cycle'                => $this->priceCycle,
            'document_type'              => $this->documentType,
            'subscription_group_id'      => $this->subscriptionGroupId,
            'position'                   => $this->position,
            'first_date_type'            => $this->firstDateType,
            'prevent_auto_dispatch'      => $this->preventAutoDispatch,
            'auto_email_confirmation'    => $this->autoEmailConfirmation,
            'business_letter_pattern_id' => $this->businessLetterPatternId,
            'add_pdf'                    => $this->addPdf,
        ];
    }
}
