<?php

namespace Xentral\Modules\DemoExporter;

use ApplicationCore;
use erpAPI;
use Xentral\Modules\DemoExporter\Exception\DemoExporterDateiException;

final class DemoExporterDateiService
{
    /** @var erpAPI */
    private $erp;

    /**
     *
     * @param ApplicationCore $app
     */
    public function __construct(ApplicationCore $app)
    {
        $this->erp = $app->erp;
    }

    /**
     * @param $dateiId
     *
     * @return string|string[]|null
     */
    public function tryGetDateiPfad($dateiId)
    {
        if (!is_numeric($dateiId)) {
            throw new DemoExporterDateiException('DateiId is missing! ');
        }

        return $this->erp->GetDateiPfad($dateiId);
    }

    /**
     * @return string|string[]
     */
    public function getTmpPath()
    {
        return $this->erp->GetTMP();
    }
}
