<?php

namespace Xentral\Widgets\SuperSearch\Attachment;

use JsonSerializable;

interface AttachmentInterface extends JsonSerializable
{
    /** @var string TYPE_BUTTON_BLOCK */
    const TYPE_BUTTON_BLOCK = 'button_block';

    /** @var string TYPE_CONTENT_STATIC */
    const TYPE_CONTENT_STATIC = 'content_static';

    /** @var string TYPE_CONTENT_DYNAMIC */
    const TYPE_CONTENT_DYNAMIC = 'content_dynamic';

    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getData();
}
