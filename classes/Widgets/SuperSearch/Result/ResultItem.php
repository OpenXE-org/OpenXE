<?php

namespace Xentral\Widgets\SuperSearch\Result;

use JsonSerializable;
use Xentral\Widgets\SuperSearch\Exception\InvalidArgumentException;

final class ResultItem implements JsonSerializable
{
    /** @var int|string $identifier */
    private $identifier;

    /** @var string $title */
    private $title;

    /** @var string $link */
    private $link;

    /** @var string|null $subTitle */
    private $subTitle;

    /** @var array|string[] $additionalInfos */
    private $additionalInfos = [];

    /**
     * @param int|string  $identifier Database-ID or unique keyword
     * @param string      $title
     * @param string      $link
     * @param string|null $subTitle
     * @param array|null  $additionalInfos
     */
    public function __construct($identifier, $title, $link, $subTitle = null, array $additionalInfos = null)
    {
        if (empty($identifier)) {
            throw new InvalidArgumentException('Parameter "id" is empty.');
        }
        if (empty($title)) {
            throw new InvalidArgumentException('Parameter "title" is empty.');
        }
        if (empty($link)) {
            throw new InvalidArgumentException('Parameter "link" is empty.');
        }

        $this->identifier = $identifier;
        $this->title = (string)$title;
        $this->link = (string)$link;

        $subTitle = trim($subTitle);
        if ($subTitle !== '') {
            $this->subTitle = $subTitle;
        }

        $additionalInfos = (array)$additionalInfos;
        foreach ($additionalInfos as $additionalInfo) {
            $additionalInfo = trim($additionalInfo);
            if ($additionalInfo !== '') {
                $this->additionalInfos[] = $additionalInfo;
            }
        }
    }

    /**
     * @param array $state
     *
     * @return self
     */
    public static function fromDbState(array $state)
    {
        $additionalInfos = explode(' ## ' , $state['additional_infos']);

        return new self($state['index_id'], $state['title'], $state['link'], $state['subtitle'], $additionalInfos);
    }

    /**
     * @return int
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string|null
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @return array
     */
    public function getAdditionalInfos()
    {
        return $this->additionalInfos;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'type'            => 'default',
            'identifier'      => $this->identifier,
            'title'           => $this->title,
            'link'            => $this->link,
            'subtitle'        => $this->subTitle,
            'additionalInfos' => !empty($this->additionalInfos) ? $this->additionalInfos : null,
        ];
    }
}
