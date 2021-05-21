<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Data;

class StagingListingPicture
{
    /**@var int $id */
    private $id;

    /**@var int $stagingListingVariantId */
    private $stagingListingVariantId = 0;

    /** @var int $fileId */
    private $fileId = 0;

    /** @var int $stagingListingId */
    private $stagingListingId = 0;

    /** @var string $url */
    private $url;

    /**
     * @param string $url
     */
    public function __construct($url)
    {
        $this->url = $url;
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
    public function getFileId(): int
    {
        return $this->fileId;
    }

    /**
     * @param int $fileId
     */
    public function setFileId(int $fileId): void
    {
        $this->fileId = $fileId;
    }

    /**
     * @return int
     */
    public function getStagingListingId(): int
    {
        return $this->stagingListingId;
    }

    /**
     * @param int $stagingListingId
     */
    public function setStagingListingId(int $stagingListingId): void
    {
        $this->stagingListingId = $stagingListingId;
    }

    /**
     * @return int
     */
    public function getStagingListingVariantId(): int
    {
        return $this->stagingListingVariantId;
    }

    /**
     * @param int $stagingListingVariantId
     */
    public function setStagingListingVariantId(int $stagingListingVariantId): void
    {
        $this->stagingListingVariantId = $stagingListingVariantId;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
