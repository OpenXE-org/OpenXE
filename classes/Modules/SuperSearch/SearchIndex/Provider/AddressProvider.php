<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use DateTimeInterface;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexData;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

final class AddressProvider extends AbstractBulkIndexDatabaseProvider
{
    /**
     * @inheritDoc
     */
    public function getModuleName()
    {
        return 'adresse';
    }

    /**
     * @inheritDoc
     */
    public function getIndexName()
    {
        return 'addresses';
    }

    /**
     * @inheritDoc
     */
    public function getIndexTitle()
    {
        return 'Adressen';
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
                'a.name',
                'a.abteilung',
                'a.unterabteilung',
                'a.ansprechpartner',
                'a.strasse',
                'a.ort',
                'a.plz',
                'a.adresszusatz',
                'a.telefon',
                'a.telefax',
                'a.mobil',
                'a.email',
                'a.ustid',
                'a.kundennummer',
                'a.lieferantennummer',
                'a.mitarbeiternummer',
            ])
            ->from('adresse AS a')
            ->where('a.geloescht = ?', 0);
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
            $title = $row['name'];
            $link = sprintf('index.php?module=adresse&action=edit&id=%d', $row['id']);
            $data = new IndexData($title, $link, $projectId);
            if (!empty($row['kundennummer'])) {
                $data->addAdditionalInfo(sprintf('Kunde %s', $row['kundennummer']));
            }
            if (!empty($row['lieferantennummer'])) {
                $data->addAdditionalInfo(sprintf('Lieferant %s', $row['lieferantennummer']));
            }
            if (!empty($row['mitarbeiternummer'])) {
                $data->addAdditionalInfo(sprintf('Mitarbeiter %s', $row['mitarbeiternummer']));
            }

            $data->addSearchWord($row['name']);
            $data->addSearchWord($row['abteilung']);
            $data->addSearchWord($row['unterabteilung']);
            $data->addSearchWord($row['ansprechpartner']);
            $data->addSearchWord($row['strasse']);
            $data->addSearchWord($row['ort']);
            $data->addSearchWord($row['plz']);
            $data->addSearchWord($row['adresszusatz']);
            $data->addSearchWord($row['telefon']);
            $data->addSearchWord($row['telefax']);
            $data->addSearchWord($row['mobil']);
            $data->addSearchWord($row['email']);
            $data->addSearchWord($row['ustid']);
            $data->addSearchWord($row['kundennummer']);
            $data->addSearchWord($row['lieferantennummer']);
            $data->addSearchWord($row['mitarbeiternummer']);

            $identifier = new IndexIdentifier('addresses', (int)$row['id']);

            return new IndexItem($identifier, $data);
        };
    }
}
