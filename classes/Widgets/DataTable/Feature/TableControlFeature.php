<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\DataTableInterface;

final class TableControlFeature implements DataTableFeatureInterface
{
    /** @var array $buttonConfigCopy */
    private static $buttonConfigCopy = [
        'extend' => 'copy',
        'text'   => 'Zwischenablage',
    ];

    /** @var array $buttonConfigCsv */
    private static $buttonConfigCsv = [
        'extend'          => 'collection',
        'text'            => 'CSV',
        'collectionTitle' => 'CSV-Export',
        'autoClose'       => true,
        'buttons'         => [
            [
                'text'   => 'Alle Seiten',
                'action' => 'export-csv-all',
            ],
            [
                'text'   => 'Aktuelle Seite',
                'action' => 'export-csv-page',
            ],
        ],
    ];

    /** @var array $buttonConfigExcel */
    private static $buttonConfigExcel = [
        'extend' => 'excel',
        'text'   => 'Excel',
    ];

    /** @var array $buttonConfigPdf */
    private static $buttonConfigPdf = [
        'extend'      => 'pdf',
        'text'        => 'PDF',
        'orientation' => 'landscape',
        'pageSize'    => 'A4',
    ];

    /** @var array $buttonConfigPrint */
    private static $buttonConfigPrint = [
        'extend' => 'print',
        'text'   => 'Drucken',
    ];

    /** @var bool $info */
    private $info = true;

    /** @var bool $paging */
    private $paging = true;

    /** @var bool $searching */
    private $searching = true;

    /** @var bool $lengthChange */
    private $lengthChange = true;

    /** @var int|null $pageLength */
    private $pageLength;

    /** @var bool $processing */
    private $processing = true;

    /** @var bool $sorting */
    private $sorting = true;

    /** @var array $buttons */
    private $buttons = [];

    /**
     */
    public function __construct()
    {
        $this->setFullMode();
    }

    /**
     * @param DataTableInterface $table
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
        $table->getOptions()->setOption('info', $this->info);
        $table->getOptions()->setOption('paging', $this->paging);
        $table->getOptions()->setOption('buttons', $this->buttons);
        $table->getOptions()->setOption('searching', $this->searching);
        $table->getOptions()->setOption('lengthChange', $this->lengthChange);
        $table->getOptions()->setOption('processing', $this->processing);
        $table->getOptions()->setOption('ordering', $this->sorting);
        if ($this->pageLength !== null && $this->pageLength > 0) {
            $table->getOptions()->setOption('pageLength', $this->pageLength);
            $table->getOptions()->setOption('lengthChange', false);
        }
    }

    /**
     * @return void
     */
    public function setFullMode()
    {
        $this->showInfo();
        $this->showButtons();
        $this->showLengthChange();
        $this->enableSearching();
        $this->enableSorting();
        $this->enablePaging();
    }

    /**
     * @return void
     */
    public function setMinimalMode()
    {
        $this->showInfo();
        $this->enablePaging();
        $this->enableSorting();

        $this->hideButtons();
        $this->hideLengthChange();
        $this->disableSearching();
    }

    /**
     * @return void
     */
    public function showInfo()
    {
        $this->info = true;
    }

    /**
     * @return void
     */
    public function hideInfo()
    {
        $this->info = false;
    }

    /**
     * @return void
     */
    public function showButtons()
    {
        $this->buttons = [
            'buttons' => [
                self::$buttonConfigCopy,
                self::$buttonConfigCsv,
                self::$buttonConfigExcel,
                self::$buttonConfigPdf,
                self::$buttonConfigPrint,
            ],
        ];
    }

    /**
     * @return void
     */
    public function hideButtons()
    {
        $this->buttons = [];
    }

    /**
     * @return void
     */
    public function showLengthChange()
    {
        $this->lengthChange = true;
    }

    /**
     * @return void
     */
    public function hideLengthChange()
    {
        $this->lengthChange = false;
    }

    /**
     * @param int $rowsPerPage
     *
     * @return void
     */
    public function setPageLength($rowsPerPage)
    {
        $this->pageLength = (int)$rowsPerPage;
        $this->hideLengthChange();
    }

    /**
     * @return void
     */
    public function showProcessingIndicator()
    {
        $this->processing = true;
    }

    /**
     * @return void
     */
    public function hideProcessingIndicator()
    {
        $this->processing = false;
    }

    /**
     * @return void
     */
    public function enableSearching()
    {
        // @todo ColumnFilter aktivieren
        $this->searching = true;
    }

    /**
     * @return void
     */
    public function disableSearching()
    {
        // @todo ColumnFilter deaktivieren
        $this->searching = false;
    }

    /**
     * @return void
     */
    public function enableSorting()
    {
        $this->sorting = true;
    }

    /**
     * @return void
     */
    public function disableSorting()
    {
        $this->sorting = false;
    }

    /**
     * @return void
     */
    public function enablePaging()
    {
        $this->paging = true;
    }

    /**
     * @return void
     */
    public function disablePaging()
    {
        $this->paging = false;
        $this->lengthChange = false;
    }
}
