<?php

namespace Xentral\Widgets\SuperSearch\Attachment;

final class StaticContentAttachment extends AbstractAttachment
{
    /** @var string $content */
    private $content;

    /**
     * @param string $content
     */
    public function __construct($content)
    {
        $this->content = (string)$content;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::TYPE_CONTENT_STATIC;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [
            'content' => $this->content,
        ];
    }
}
