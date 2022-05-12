<?php

namespace Xentral\Components\Pdf;

use Xentral\Components\Pdf\Merger\FpdiPdfMerger;
use Xentral\Components\Pdf\Merger\GhostScriptPdfMerger;

final class Bootstrap
{
    /**
     * @return array
     */
    public static function registerServices()
    {
        return [
            'PdfMerger' => 'onInitPdfMerger',
        ];
    }

    /**
     * @return PdfMerger
     */
    public static function onInitPdfMerger()
    {
        return new PdfMerger(new FpdiPdfMerger(), new GhostScriptPdfMerger());
    }
}
