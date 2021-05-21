<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use DateTimeInterface;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexData;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

final class TrackingNumberProvider extends AbstractBulkIndexDatabaseProvider
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
        return 'trackingnumber';
    }

    /**
     * @inheritDoc
     */
    public function getIndexTitle()
    {
        return 'Trackingnummer';
    }

    /**
     * @inheritDoc
     */
    protected function configureBaseQuery(SelectQuery $query)
    {
        $query
            ->cols([
                'v.id',
                'v.tracking',
                'v.lieferschein',
                'v.projekt',
                'v.versendet_am'
            ])
            ->from('versand AS v')
            ->where('v.tracking != ?', '');
    }

    /**
     * @inheritDoc
     */
    protected function configureItemQuery(SelectQuery $baseQuery, $indexId)
    {
        $baseQuery->where('v.id = ?', (int)$indexId);
    }

    /**
     * @inheritDoc
     */
    protected function configureCountQuery(SelectQuery $baseQuery)
    {
        $baseQuery->cols(['COUNT(v.id)' => 'total_count']);
    }

    /**
     * @inheritDoc
     */
    protected function configureSinceQuery(SelectQuery $baseQuery, DateTimeInterface $since)
    {
        $baseQuery->where('v.versendet_am > ?', $since->format('Y-m-d H:m:i'));
    }

    /**
     * @inheritDoc
     */
    protected function getRowFormatter()
    {
        return static function (array $row) {

            $projectId = (int)$row['projekt'];
            $versandDatum = date('d.m.Y', strtotime($row['versendet_am']));
            $title = $row['tracking'];
            $link = sprintf('index.php?module=lieferschein&action=edit&id=%d', $row['lieferschein']);

            $data = new IndexData($title, $link, $projectId);
            $data->addSearchWord('trackingnummer');
            $data->addSearchWord($row['tracking']);

            $data->setSubTitle('');

            $data->addAdditionalInfo('Versendet: '.$versandDatum);

            $identifier = new IndexIdentifier('trackingnumber', (int)$row['id']);

            return new IndexItem($identifier, $data);
        };
    }
}
