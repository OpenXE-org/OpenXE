<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Data;

final class StagingListingVariationData
{
    /** @var int $articleId */
    private $articleId = 0;

    /** @var string $sku */
    private $sku = '';

    /** @var array $specifics */
    private $specifics = [];

    /** @var array $pictures */
    private $pictures = [];

    /** @var int $id */
    private $id;

    /**
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        $this->id = $id;
    }

    /**
     * @param array $data
     * @param array $specifics
     *
     * @return StagingListingVariationData
     */
    public static function fromArray($data, $specifics): StagingListingVariationData
    {
        $stagingListing = new StagingListingVariationData($data['id']);

        $stagingListing->setSku($data['sku']);
        $stagingListing->setSpecifics($specifics);
        $stagingListing->setArticleId($data['article_id']);

        return $stagingListing;
    }

    /**
     * @param array $specifics
     */
    public function setSpecifics($specifics): void
    {
        $this->specifics = $specifics;
    }

    /**
     * @param string $property
     * @param string $value
     */
    public function addSpecifics($property, $value): void
    {
        $this->specifics[$property] = $value;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id'         => $this->getId(),
            'article_id' => $this->getArticleId(),
            'sku'        => $this->getSku(),
            'specifics'  => $this->listSpecifics(),
            'pictures'   => $this->listPictures(),
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
     * @return int
     */
    public function getArticleId(): int
    {
        return $this->articleId;
    }

    /**
     * @param int $articleId
     */
    public function setArticleId($articleId): void
    {
        $this->articleId = (int)$articleId;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku($sku): void
    {
        $this->sku = (string)$sku;
    }

    /**
     * @return array
     */
    public function listSpecifics(): array
    {
        return $this->specifics;
    }

    /**
     * @return array
     */
    public function listPictures(): array
    {
        return $this->pictures;
    }

    /**
     * @param StagingListingPicture $picture
     */
    public function addPicture(StagingListingPicture $picture): void
    {
        $pictureExists = false;
        foreach ($this->pictures as $existingPicture) {
            if ($existingPicture->getUrl() === $picture->getUrl()) {
                $pictureExists = true;
                break;
            }
        }

        if (!$pictureExists) {
            $this->pictures[] = $picture;
        }
    }
}
