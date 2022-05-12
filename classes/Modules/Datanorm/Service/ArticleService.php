<?php

declare(strict_types=1);

namespace Xentral\Modules\Datanorm\Service;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Modules\Datanorm\Exception\ArticleNotFoundException;
use Xentral\Modules\Datanorm\Exception\InvalidArgumentException;


final class ArticleService
{

    /** @var Database */
    private $db;

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param array $articleArray
     *
     * @throws ArticleNotFoundException
     * @throws QueryFailureException
     * @throws InvalidArgumentException
     *
     * @return int
     */
    public function InsertUpdateArticle(array $articleArray): ?int
    {
        if (empty($articleArray['nummer'])) {
            throw new ArticleNotFoundException('No article number found.');
        }

        $articleId = $this->findArticleIdByNumber($articleArray['nummer']);

        if (empty($articleId)) {
            $articleId = $this->insertArrayIntoTable($articleArray, 'artikel');
        } else {
            $this->updateArrayIntoTable($articleArray, 'artikel', $articleId);
        }

        return $articleId;
    }

    /**
     * @param array  $data
     * @param string $table
     *
     * @throws QueryFailureException
     * @throws InvalidArgumentException
     *
     * @return int
     */
    private function insertArrayIntoTable(array $data, string $table): ?int
    {
        if (empty($data)) {
            throw new InvalidArgumentException('No data to insert into ' . $table . ' given');
        }

        $insert = $this->db->insert();
        $insert
            ->cols($data)
            ->into($table);
        $this->db->perform($insert->getStatement(), $insert->getBindValues());

        return $this->db->lastInsertId();
    }

    /**
     * @param array  $data
     * @param string $table
     * @param int    $id
     *
     * @throws QueryFailureException
     * @throws InvalidArgumentException
     */
    private function updateArrayIntoTable(array $data, string $table, int $id): void
    {
        if (empty($data)) {
            throw new InvalidArgumentException('No data to update in ' . $table . ' given');
        }

        if (empty($id)) {
            throw new InvalidArgumentException('No id for update in ' . $table . ' given');
        }

        $update = $this->db->update()
            ->table($table)
            ->cols($data)
            ->where('id=?', $id);

        $this->db->perform($update->getStatement(), $update->getBindValues());
    }

    /**
     * @param string $number
     *
     * @return int
     */
    public function findArticleIdByNumber(string $number): ?int
    {
        $select = $this->db->select()
            ->cols(['id'])
            ->from('artikel')
            ->where('nummer=?', $number)
            ->limit(1);

        $result = $this->db->fetchCol(
            $select->getStatement(),
            $select->getBindValues()
        );

        if (!empty($result)) {
            return $result[0];
        }

        return 0;
    }
}
