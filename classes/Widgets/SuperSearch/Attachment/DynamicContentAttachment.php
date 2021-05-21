<?php

namespace Xentral\Widgets\SuperSearch\Attachment;

final class DynamicContentAttachment extends AbstractAttachment
{
    /** @var string $ajaxUrl */
    private $ajaxUrl;

    /** @var array $postParams */
    private $postParams = [];

    /**
     * @param string $ajaxUrl
     * @param array  $postParams
     */
    public function __construct($ajaxUrl, array $postParams = [])
    {
        $this->ajaxUrl = (string)$ajaxUrl;
        $this->postParams = $postParams;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return self::TYPE_CONTENT_DYNAMIC;
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        return [
            'url'    => $this->ajaxUrl,
            'params' => $this->postParams,
        ];
    }
}
