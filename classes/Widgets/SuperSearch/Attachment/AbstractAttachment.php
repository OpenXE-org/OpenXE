<?php

namespace Xentral\Widgets\SuperSearch\Attachment;

abstract class AbstractAttachment implements AttachmentInterface
{
    /**
     * @return string
     */
    abstract public function getType();

    /**
     * @return array
     */
    abstract public function getData();

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'type' => $this->getType(),
            'data' => $this->getData(),
        ];
    }
}
