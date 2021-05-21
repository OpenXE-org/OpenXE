<?php

namespace Xentral\Widgets\DataTable\Feature;

use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\Column\ColumnFormatter;
use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Exception\DataTableExceptionInterface;
use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;

final class RowDetailsFeature implements DataTableFeatureInterface
{
    /** @var string $ajaxUrl */
    private $ajaxUrl;

    /** @var string $ajaxMethod */
    private $ajaxMethod;

    /** @var array $ajaxParams @todo Additional AJAX parameter */
    private $ajaxParams = [];

    /**
     * Der Wert aus der id-Spalte wird als POST-Parameter `id` übergeben
     *
     * @param string        $ajaxUrl         `./index.php?module=foo&action=bar`
     * @param string        $ajaxMethod      [GET|POST]
     * @param callable|null $customFormatter @todo
     */
    public function __construct($ajaxUrl, $ajaxMethod = 'POST', $customFormatter = null)
    {
        $ajaxMethod = strtoupper($ajaxMethod);
        if (!in_array($ajaxMethod, ['GET', 'POST'])) {
            throw new InvalidArgumentException(sprintf('Invalid method "%s".', $ajaxMethod));
        }

        $this->ajaxUrl = $ajaxUrl;
        $this->ajaxMethod = $ajaxMethod;
    }

    /**
     * @param DataTableInterface $table
     *
     * @throws DataTableExceptionInterface
     *
     * @return void
     */
    public function modifyTable(DataTableInterface $table)
    {
        $table->getOptions()->setOption('rowDetails', [
            'ajax' => [
                'url'    => $this->ajaxUrl,
                'method' => $this->ajaxMethod,
                'data'   => $this->ajaxParams,
            ],
        ]);

        // Detail-Spalte erzeugen
        $newCol = Column::fixed('details', '', 'center', '20px');
        $newCol->setFormatter(ColumnFormatter::template('<span class="details" data-id="{ID}"></span>'));
        $newCol->addCssClass('dt-details');

        // Detail-Spalte vor erste Spalte einfügen
        /** @var Column $firstCol */
        $firstCol = $table->getColumns()->getByIndex(0);
        $table->getColumns()->addBefore($newCol, $firstCol->getName());
    }
}
