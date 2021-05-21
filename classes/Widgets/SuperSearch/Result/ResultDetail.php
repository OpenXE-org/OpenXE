<?php

namespace Xentral\Widgets\SuperSearch\Result;

use JsonSerializable;
use Xentral\Widgets\SuperSearch\Attachment\ButtonBlockAttachment;
use Xentral\Widgets\SuperSearch\Attachment\DynamicContentAttachment;
use Xentral\Widgets\SuperSearch\Attachment\StaticContentAttachment;
use Xentral\Widgets\SuperSearch\Exception\InvalidArgumentException;

final class ResultDetail implements JsonSerializable
{
    /** @var string $title */
    private $title = '';

    /** @var ButtonBlockAttachment|null $buttons */
    private $buttons;

    /** @var StaticContentAttachment|null $staticDescription */
    private $staticDescription;

    /** @var DynamicContentAttachment|null $dynamicDescription */
    private $dynamicDescription;

    /**
     * @return bool
     */
    public function hasTitle()
    {
        return !empty($this->title);
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        if (empty($title)) {
            throw new InvalidArgumentException('Required parameter "title" is empty.');
        }

        $this->title = $title;
    }

    /**
     * Beschreibung setzen; MiniDetail wird dann geleert!
     *
     * @param string $description
     *
     * @throws InvalidArgumentException
     */
    public function setDescription($description)
    {
        if (empty($description)) {
            throw new InvalidArgumentException('Required parameter $description is empty.');
        }

        $this->staticDescription = new StaticContentAttachment($description);
        $this->dynamicDescription = null;
    }

    /**
     * MiniDetail-URL setzen; Beschreibung wird dann geleert!
     *
     * @param string $miniDetailUrl
     * @param array  $postParams
     */
    public function setMiniDetailUrl($miniDetailUrl, array $postParams = [])
    {
        if (empty($miniDetailUrl)) {
            throw new InvalidArgumentException('Required parameter $miniDetailUrl is empty.');
        }

        $this->dynamicDescription = new DynamicContentAttachment($miniDetailUrl, $postParams);
        $this->staticDescription = null; // Entweder MiniDetail oder Beschreibungstext
    }

    /**
     * @param string      $title
     * @param string|null $href Hyperlink reference
     * @param array       $attributes
     *
     * @return void
     */
    public function addButton($title, $href = null, array $attributes = [])
    {
        if ($this->buttons === null) {
            $this->buttons = new ButtonBlockAttachment();
        }

        if ($href !== null) {
            $attributes['href'] = (string)$href;
        }

        $this->buttons->addButton($title, $attributes);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->hasTitle();
    }

    /**
     * @return array|false
     */
    public function jsonSerialize()
    {
        if (!$this->isValid()) {
            return false;
        }

        $attachments = [];
        if ($this->buttons !== null) {
            $attachments[] = $this->buttons;
        }
        if ($this->staticDescription !== null) {
            $attachments[] = $this->staticDescription;
        }
        if ($this->dynamicDescription !== null) {
            $attachments[] = $this->dynamicDescription;
        }

        return [
            'title'       => $this->title,
            'attachments' => $attachments,
        ];
    }
}
