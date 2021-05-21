<?php

namespace Xentral\Widgets\SuperSearch\Attachment;

final class ButtonBlockAttachment extends AbstractAttachment
{
    /** @var array $buttons */
    private $buttons = [];

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::TYPE_BUTTON_BLOCK;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return $this->buttons;
    }

    /**
     * @todo Attribute validieren
     *
     * @param string $title
     * @param array  $attributes
     *
     * @return void
     */
    public function addButton($title, array $attributes = [])
    {
        $this->buttons[] = [
            'title'      => (string)$title,
            'attributes' => !empty($attributes) ? $attributes : null,
        ];
    }
}
