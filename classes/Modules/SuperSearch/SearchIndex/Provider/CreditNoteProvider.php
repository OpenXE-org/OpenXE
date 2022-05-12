<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use DateTimeInterface;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexData;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

final class CreditNoteProvider extends AbstractBulkIndexDatabaseProvider
{
    /**
     * @inheritDoc
     */
    public function getModuleName()
    {
        return 'gutschrift';
    }

    /**
     * @inheritDoc
     */
    public function getIndexName()
    {
        return 'creditnotes';
    }

    /**
     * @inheritDoc
     */
    public function getIndexTitle()
    {
        return 'Gutschriften';
    }

    /**
     * @inheritDoc
     */
    protected function configureBaseQuery(SelectQuery $query)
    {
        $query
            ->cols([
                'gs.id',
                'gs.projekt',
                'gs.status',
                'gs.datum',
                'gs.belegnr',
                'gs.rechnung',
                'gs.name',
                'gs.kundennummer',
                'gs.internebemerkung',
                'gs.ihrebestellnummer',
                'gs.soll',
                'gs.zahlungsstatus',
                'gs.waehrung',
            ])
            ->from('gutschrift AS gs')
            ->where('gs.belegnr != ?', '');
    }

    /**
     * @inheritDoc
     */
    protected function configureItemQuery(SelectQuery $baseQuery, $indexId)
    {
        $baseQuery->where('gs.id = ?', (int)$indexId);
    }

    /**
     * @inheritDoc
     */
    protected function configureCountQuery(SelectQuery $baseQuery)
    {
        $baseQuery->cols(['COUNT(gs.id)' => 'total_count']);
    }

    /**
     * @inheritDoc
     */
    protected function configureSinceQuery(SelectQuery $baseQuery, DateTimeInterface $since)
    {
        $baseQuery->where('gs.logdatei > ?', $since->format('Y-m-d H:m:i'));
    }

    /**
     * @inheritDoc
     */
    protected function getRowFormatter()
    {
        return static function (array $row) {

            $projectId = (int)$row['projekt'];
            $rechnungsDatum = date('d.m.Y', strtotime($row['datum']));
            $title = $row['belegnr'];
            $link = sprintf('index.php?module=gutschrift&action=edit&id=%d', $row['id']);

            $data = new IndexData($title, $link, $projectId);
            $data->setSubTitle($row['name']);
            $data->addAdditionalInfo($rechnungsDatum);
            $data->addAdditionalInfo(ucfirst($row['status']));
            $data->addAdditionalInfo(sprintf('%s %s', number_format($row['soll'], 2, ',', '.'), $row['waehrung']));

            $data->addSearchWord('gutschrift');
            $data->addSearchWord($row['belegnr']);
            $data->addSearchWord($row['rechnung']);
            $data->addSearchWord($row['status']);
            $data->addSearchWord($row['name']);
            $data->addSearchWord($row['kundennummer']);
            $data->addSearchWord($row['internebemerkung']);
            $data->addSearchWord($row['ihrebestellnummer']);

            $identifier = new IndexIdentifier('creditnotes', (int)$row['id']);

            return new IndexItem($identifier, $data);
        };
    }
}
