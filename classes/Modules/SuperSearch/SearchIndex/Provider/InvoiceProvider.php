<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use DateTimeInterface;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexData;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

final class InvoiceProvider extends AbstractBulkIndexDatabaseProvider
{
    /**
     * @inheritDoc
     */
    public function getModuleName()
    {
        return 'rechnung';
    }

    /**
     * @inheritDoc
     */
    public function getIndexName()
    {
        return 'invoices';
    }

    /**
     * @inheritDoc
     */
    public function getIndexTitle()
    {
        return 'Rechnungen';
    }

    /**
     * @inheritDoc
     */
    protected function configureBaseQuery(SelectQuery $query)
    {
        $query
            ->cols([
                'r.id',
                'r.projekt',
                'r.datum',
                'r.belegnr',
                'r.auftrag',
                'r.name',
                'r.kundennummer',
                'r.internebemerkung',
                'r.ihrebestellnummer',
                'r.soll',
                'r.zahlungsstatus',
                'r.waehrung',
            ])
            ->from('rechnung AS r')
            ->where('r.belegnr != ?', '');
    }

    /**
     * @inheritDoc
     */
    protected function configureItemQuery(SelectQuery $baseQuery, $indexId)
    {
        $baseQuery->where('r.id = ?', (int)$indexId);
    }

    /**
     * @inheritDoc
     */
    protected function configureCountQuery(SelectQuery $baseQuery)
    {
        $baseQuery->cols(['COUNT(r.id)' => 'total_count']);
    }

    /**
     * @inheritDoc
     */
    protected function configureSinceQuery(SelectQuery $baseQuery, DateTimeInterface $since)
    {
        $baseQuery->where('r.logdatei > ?', $since->format('Y-m-d H:m:i'));
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
            $link = sprintf('index.php?module=rechnung&action=edit&id=%d', $row['id']);

            $data = new IndexData($title, $link, $projectId);
            $data->setSubTitle($row['name']);
            $data->addAdditionalInfo($rechnungsDatum);
            $data->addAdditionalInfo(ucfirst($row['zahlungsstatus']));
            $data->addAdditionalInfo(sprintf('%s %s', number_format($row['soll'], 2, ',', '.'), $row['waehrung']));

            $data->addSearchWord('rechnung');
            $data->addSearchWord($row['belegnr']);
            $data->addSearchWord($row['auftrag']);
            $data->addSearchWord($row['name']);
            $data->addSearchWord($row['kundennummer']);
            $data->addSearchWord($row['internebemerkung']);
            $data->addSearchWord($row['ihrebestellnummer']);

            $identifier = new IndexIdentifier('invoices', (int)$row['id']);

            return new IndexItem($identifier, $data);
        };
    }
}
