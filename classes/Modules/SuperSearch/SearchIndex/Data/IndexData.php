<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Data;

use Xentral\Modules\SuperSearch\Exception\InvalidArgumentException;

final class IndexData
{
    /** @var int $projectId */
    private $projectId;

    /** @var string $title */
    private $title;

    /** @var string|null $subTitle */
    private $subTitle;

    /** @var array|string[] $additionalInfos */
    private $additionalInfos;

    /** @var string $link */
    private $link;

    /** @var array $words */
    private $words;

    /**
     * @param string $title
     * @param string $link
     * @param int    $projectId
     * @param array  $words
     *
     * @throws InvalidArgumentException
     */
    public function __construct($title, $link, $projectId = 0, array $words = [])
    {
        if (empty($title)) {
            $title = 'empty';
        }
        if (empty($link)) {
            throw new InvalidArgumentException('Invalid argument value. $link parameter can not be empty.');
        }
        if (!is_int($projectId)) {
            throw new InvalidArgumentException('Invalid argument type. Parameter $projectId must be type integer.');
        }

        $this->projectId = (int)$projectId;
        $this->title = (string)$title;
        $this->link = (string)$link;
        $this->addSearchWords($words);
    }

    /**
     * @param array $state
     *
     * @return self
     */
    public static function fromDbState(array $state)
    {
        return new self(
            (string)$state['title'],
            (string)$state['link'],
            (int)$state['project_id'],
            (array)$state['search_words']
        );
    }

    /**
     * @param string $subTitle
     *
     * @return void
     */
    public function setSubTitle($subTitle)
    {
        $subTitle = trim($subTitle);
        if ($subTitle === '') {
            return;
        }

        $this->subTitle = (string)$subTitle;
    }

    /**
     * @param string $additionalInfo
     *
     * @return void
     */
    public function addAdditionalInfo($additionalInfo)
    {
        $additionalInfo = trim($additionalInfo);
        if ($additionalInfo === '') {
            return;
        }

        $this->additionalInfos[] = (string)$additionalInfo;
    }

    /**
     * @param array|string[] $words
     *
     * @return void
     */
    public function addSearchWords(array $words)
    {
        foreach ($words as $word) {
            $this->addSearchWord($word);
        }
    }

    /**
     * @param string $word
     *
     * @return void
     */
    public function addSearchWord($word)
    {
        $word = trim($word);
        if ($word === '') {
            return;
        }

        $this->words[] = $word;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string|null
     */
    public function getSubTitle()
    {
        return $this->subTitle;
    }

    /**
     * @return array|string[]
     */
    public function getAdditionalInfos()
    {
        return $this->additionalInfos;
    }

    /**
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @return array
     */
    public function getWords()
    {
        return $this->words;
    }
}
