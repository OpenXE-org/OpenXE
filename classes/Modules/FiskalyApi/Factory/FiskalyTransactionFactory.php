<?php

declare(strict_types=1);

namespace Xentral\Modules\FiskalyApi\Factory;

use Aura\SqlQuery\Exception;
use Xentral\Components\Database\Database;
use Xentral\Modules\FiskalyApi\Data\Transaction\TransactionRequest;

class FiskalyTransactionFactory
{
    /** @var Database $database */
    private $database;

    /**
     * FiskalyTransactionFactory constructor.
     *
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->database = $db;
    }

    /**
     * @param int $projectId
     *
     * @throws Exception
     * @return array
     */
    public function getClientAndTssInfoFromProjectId(int $projectId): array
    {
        $posProjectQuery = $this->database->select()
            ->from('pos_kassierer AS p')
            ->cols(['f.tss_uuid', 'f.client_uuid', 'f.organization_id'])
            ->where('p.projekt=:project_id')
            ->leftJoin('fiskaly_pos_mapping AS f', 'f.pos_id = p.projekt')
            ->bindValue('project_id', $projectId);
        return $this->database->fetchRow($posProjectQuery->getStatement(), $posProjectQuery->getBindValues());
    }

    /**
     * @param string $cashierId
     *
     * @throws Exception
     * @return array
     */
    public function getClientAndTssInfoFromCashierId(string $cashierId): array
    {
        $posProjectQuery = $this->database->select()
            ->from('pos_kassierer AS p')
            ->cols(['f.tss_uuid', 'f.client_uuid', 'f.organization_id'])
            ->where('p.kassenkennung=:kennung')
            ->innerJoin('fiskaly_pos_mapping AS f', 'f.pos_id = p.projekt')
            ->bindValue('kennung', $cashierId);
        return $this->database->fetchRow($posProjectQuery->getStatement(), $posProjectQuery->getBindValues());
    }

    /**
     * @param string $tssId
     *
     * @throws Exception
     * @return array
     */
    public function getTssFromTssId(string $tssId): array
    {
        $posProjectQuery = $this->database->select()
            ->from('pos_kassierer AS p')
            ->cols(['f.tss_uuid', 'f.client_uuid', 'f.organization_id'])
            ->where('f.tss_uuid=:tss_uuid')
            ->innerJoin('fiskaly_pos_mapping AS f', 'f.pos_id = p.projekt')
            ->bindValue('tss_uuid', $tssId);
        return $this->database->fetchRow($posProjectQuery->getStatement(), $posProjectQuery->getBindValues());
    }

    /**
     * @param string $cashierId
     *
     * @throws Exception
     * @return TransactionRequest
     */
    public function getTransactionRequestFromPosSession(string $cashierId): TransactionRequest
    {
        $result = $this->getClientAndTssInfoFromCashierId($cashierId);
        return (new TransactionRequest('ACTIVE', $result['client_uuid']))->setTssId($result['tss_uuid']);
    }

    /**
     * @param int $projectId
     *
     * @throws Exception
     * @return TransactionRequest
     */
    public function getTransactionRequestFromProjectId(int $projectId): TransactionRequest
    {
        $result = $this->getClientAndTssInfoFromProjectId($projectId);
        return (new TransactionRequest('ACTIVE', $result['client_uuid']))->setTssId($result['tss_uuid']);
    }
}
