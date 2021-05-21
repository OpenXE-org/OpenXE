<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

use DateTimeInterface;
use Xentral\Components\Database\SqlQuery\SelectQuery;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexData;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexIdentifier;
use Xentral\Modules\SuperSearch\SearchIndex\Data\IndexItem;

final class ArticleProvider extends AbstractBulkIndexDatabaseProvider
{
    /**
     * @inheritDoc
     */
    public function getModuleName()
    {
        return 'artikel';
    }

    /**
     * @inheritDoc
     */
    public function getIndexName()
    {
        return 'articles';
    }

    /**
     * @inheritDoc
     */
    public function getIndexTitle()
    {
        return 'Artikel';
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
                'a.nummer',
                'a.name_de',
                'a.kurztext_de',
                'a.herstellernummer',
                'a.ean',
            ])
            ->from('artikel AS a')
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
            $link = sprintf('index.php?module=artikel&action=edit&id=%d', $row['id']);

            $data = new IndexData($row['nummer'], $link, $projectId);
            $data->setSubTitle($row['name_de']);
            $data->addSearchWord($row['nummer']);
            $data->addSearchWord($row['name_de']);
            $data->addSearchWord($row['kurztext_de']);
            $data->addSearchWord($row['herstellernummer']);
            $data->addSearchWord($row['ean']);

            $identifier = new IndexIdentifier('articles', (int)$row['id']);

            return new IndexItem($identifier, $data);
        };
    }
}
