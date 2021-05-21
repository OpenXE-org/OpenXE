<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use DateTimeInterface;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexData;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

final class OrderProvider extends AbstractBulkIndexDatabaseProvider
{
    /**
     * @inheritDoc
     */
    public function getModuleName()
    {
        return 'auftrag';
    }

    /**
     * @inheritDoc
     */
    public function getIndexName()
    {
        return 'orders';
    }

    /**
     * @inheritDoc
     */
    public function getIndexTitle()
    {
        return 'Aufträge';
    }

    /**
     * @inheritDoc
     */
    protected function configureBaseQuery(SelectQuery $query)
    {
        $query
            ->cols([
                'a.id',
                'a.projekt',
                'a.datum',
                'a.belegnr',
                'a.internet',
                'a.angebot', // Angebotsnummer
                'a.status',
                'a.name',
                'a.kundennummer',
                'a.internebezeichnung',
                'a.ihrebestellnummer',
                'a.gesamtsumme',
                'a.waehrung',
            ])
            ->from('auftrag AS a')
            ->where('a.belegnr != ?', '');
    }

    /**
     * @inheritDoc
     */
    protected function configureItemQuery(SelectQuery $baseQuery, $indexId)
    {
        $baseQuery->where('a.id = ?', (int)$indexId);
    }

    /**
     * @inheritDoc
     */
    protected function configureCountQuery(SelectQuery $baseQuery)
    {
        $baseQuery->cols(['COUNT(a.id)' => 'total_count']);
    }

    /**
     * @inheritDoc
     */
    protected function configureSinceQuery(SelectQuery $baseQuery, DateTimeInterface $since)
    {
        $baseQuery->where('a.logdatei > ?', $since->format('Y-m-d H:m:i'));
    }

    /**
     * @inheritDoc
     */
    protected function getRowFormatter()
    {
        return static function (array $row) {

            $projectId = (int)$row['projekt'];
            $documentDate = date('d.m.Y', strtotime($row['datum']));
            $title = $row['belegnr'];
            $link = sprintf('index.php?module=auftrag&action=edit&id=%d', $row['id']);

            $data = new IndexData($title, $link, $projectId);
            $data->setSubTitle($row['name']);
            $data->addAdditionalInfo($documentDate);
            $data->addAdditionalInfo(ucfirst($row['status']));
            $data->addAdditionalInfo(number_format($row['gesamtsumme'], 2, ',', '.') . ' ' . $row['waehrung']);
            $data->addSearchWord('auftrag');
            $data->addSearchWord($row['belegnr']);
            $data->addSearchWord($row['internet']);
            $data->addSearchWord($row['angebot']);
            $data->addSearchWord($row['name']);
            $data->addSearchWord($row['kundennummer']);
            $data->addSearchWord($row['internebezeichnung']);
            $data->addSearchWord($row['ihrebestellnummer']);

            $identifier = new IndexIdentifier('orders', (int)$row['id']);

            return new IndexItem($identifier, $data);
        };
    }
}
