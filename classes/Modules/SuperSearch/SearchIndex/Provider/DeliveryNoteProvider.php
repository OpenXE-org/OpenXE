<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use DateTimeInterface;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexData;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

final class DeliveryNoteProvider extends AbstractBulkIndexDatabaseProvider
{
    /**
     * @inheritDoc
     */
    public function getModuleName()
    {
        return 'lieferschein';
    }

    /**
     * @inheritDoc
     */
    public function getIndexName()
    {
        return 'deliverynote';
    }

    /**
     * @inheritDoc
     */
    public function getIndexTitle()
    {
        return 'Lieferscheine';
    }

    /**
     * @inheritDoc
     */
    protected function configureBaseQuery(SelectQuery $query)
    {
        $query
            ->cols([
                'l.id',
                'l.projekt',
                'l.datum',
                'l.belegnr',
                'l.status',
                'l.name',
                'l.kundennummer',
                'l.internebezeichnung'
            ])
            ->from('lieferschein AS l')
            ->where('l.belegnr != ?', '');
    }

    /**
     * @inheritDoc
     */
    protected function configureItemQuery(SelectQuery $baseQuery, $indexId)
    {
        $baseQuery->where('l.id = ?', (int)$indexId);
    }

    /**
     * @inheritDoc
     */
    protected function configureCountQuery(SelectQuery $baseQuery)
    {
        $baseQuery->cols(['COUNT(l.id)' => 'total_count']);
    }

    /**
     * @inheritDoc
     */
    protected function configureSinceQuery(SelectQuery $baseQuery, DateTimeInterface $since)
    {
        $baseQuery->where('l.logdatei > ?', $since->format('Y-m-d H:m:i'));
    }

    /**
     * @inheritDoc
     */
    protected function getRowFormatter()
    {
        return static function (array $row) {

            $projectId = (int)$row['projekt'];
            $lieferscheinDatum = date('d.m.Y', strtotime($row['datum']));
            $title = $row['belegnr'];
            $link = sprintf('index.php?module=lieferschein&action=edit&id=%d', $row['id']);

            $data = new IndexData($title, $link, $projectId);
            $data->addSearchWord('lieferschein');
            $data->addSearchWord($row['belegnr']);
            $data->addSearchWord($row['name']);
            $data->addSearchWord($row['kundennummer']);
            $data->addSearchWord($row['internebezeichnung']);

            $data->setSubTitle($row['name']);

            $data->addAdditionalInfo($lieferscheinDatum);
            $data->addAdditionalInfo($row['status']);


            $identifier = new IndexIdentifier('deliverynote', (int)$row['id']);

            return new IndexItem($identifier, $data);
        };
    }
}
