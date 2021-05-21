<?php

namespace Xentral\Modules\Article\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Article\Exception\ArticleNotFoundException;
use Xentral\Modules\Article\Exception\InvalidArgumentException;
use Xentral\Modules\Article\Gateway\ArticleGateway;

final class PurchasePriceService
{
    /** @var ArticleGateway $gateway */
    private $gateway;

    /** @var Database $db */
    private $db;

    /**
     * @param ArticleGateway $gateway
     * @param Database       $database
     */
    public function __construct(ArticleGateway $gateway, Database $database)
    {
        $this->gateway = $gateway;
        $this->db = $database;
    }

    /**
     * @param int       $articleId
     * @param int|float $quantity
     *
     * @throws InvalidArgumentException|ArticleNotFoundException
     *
     * @return float
     */
    public function calculateCalculatedPurchasePrice($articleId, $quantity)
    {
        $quantity = (float)$quantity;
        $articleId = $this->ensureArticleId($articleId);
        if ($quantity <= 0.0) {
            throw new InvalidArgumentException(sprintf('Quantity must be greater than 0. Actual: %s', $quantity));
        }

        $calculatedPurchasePrice = $this->db->fetchValue(
            'SELECT a.berechneterek FROM artikel AS a WHERE a.id = :article_id AND a.verwendeberechneterek = 1',
            ['article_id' => $articleId]
        );
        
        $calculatedPurchasePrice = (float)$calculatedPurchasePrice * $quantity;

        if ($calculatedPurchasePrice > 0) {
            return $calculatedPurchasePrice;
        }

        if (!$this->gateway->isPartsListArticle($articleId)) {
            return 0;
        }

        $calculatedPurchasePrice = 0;
        $partsListArticles = $this->db->fetchAll(
            'SELECT s.artikel, s.menge FROM stueckliste AS s WHERE s.stuecklistevonartikel = :article_id',
            ['article_id' => $articleId]
        );
        foreach($partsListArticles as $articles=>$article){
            $calculatedPurchasePrice += $this->calculateCalculatedPurchasePrice($article['artikel'], $article['menge']);
        }

        return $calculatedPurchasePrice;
    }

    /**
     * @param int $articleId
     *
     * @throws InvalidArgumentException
     *
     * @return int
     */
    private function ensureArticleId($articleId)
    {
        if (empty($articleId) || (int)$articleId < 1) {
            throw new InvalidArgumentException('Required argument "articleId" is empty or invalid.');
        }

        return (int)$articleId;
    }

    /**
     * @param int    $articleId
     * @param int    $addressId
     * @param float  $price
     * @param string $currencyCode
     * @param float  $quantityFrom
     * @param string $purchaseNumber
     * @param string $purchaseName
     *
     * @return int
     */
    public function setPurchasePrice($articleId,
        $addressId,
        $price,
        $currencyCode = 'EUR',
        $quantityFrom = 1.0,
        $purchaseNumber = '',
        $purchaseName = '')
    {
        $this->ensureArticleId($articleId);
        if(empty($currencyCode)) {
            $currencyCode = 'EUR';
        }
        if($quantityFrom <= 0) {
            $quantityFrom = 1.0;
        }
        $select = $this->db->select();
        $select
            ->cols(['pp.id', 'pp.preis as price','IF(pp.waehrung <> \'\', pp.waehrung, \'EUR\') AS currency_code',
                    'pp.artikel AS article_id','pp.ab_menge AS quantity_from','pp.bestellnummer AS purchase_number',
                    'pp.bezeichnunglieferant AS purcase_name'
            ])
            ->from('einkaufspreise AS pp')
            ->where("ab_menge=:quantity_from AND adresse=:address_id AND artikel=:article_id AND waehrung = :currency_code
            AND (IFNULL(gueltig_bis='0000-00-00','0000-00-00') OR gueltig_bis >= NOW()) AND geloescht!=1")
            ->bindValue('quantity_from', (float)$quantityFrom)
            ->bindValue('address_id', (int)$addressId)
            ->bindValue('article_id', (int)$articleId)
            ->bindValue('currency_code', $currencyCode);

        $oldPrice = $this->db->fetchRow(
            $select->getStatement(),
            $select->getBindValues()
        );


        if(!empty($oldPrice)) {
            $isPriceDifferent = round($oldPrice['price'],8) !== round($price,8);
            $isNumberOrNameDiffernt = (String)$oldPrice['purchase_number'] !== (String)$purchaseNumber ||
                (String)$oldPrice['purcase_name'] !== (String)$purchaseName;
            if(!$isPriceDifferent && !$isNumberOrNameDiffernt) {
                return $oldPrice['id'];
            }
            if(!$isPriceDifferent) {
                return $oldPrice['id'];
            }
            $this->db->perform(
                'UPDATE einkaufspreise SET gueltig_bis = DATE_SUB(CURDATE(), INTERVAL 1 DAY) WHERE id = :id',
                ['id'=>$oldPrice['id']]
            );
        }

        $this->db->perform('INSERT INTO einkaufspreise (artikel, adresse, objekt, projekt, preis, waehrung, ab_menge,
                 bestellnummer, bezeichnunglieferant, nichtberechnet)  
            VALUES (:article_id, :address_id, :object, :project_id, :price, :currency, :quantity_from,
                    :purchase_number,:purchase_name, 1)',
            [
                'article_id'      => (int)$articleId,
                'address_id'      => (int)$addressId,
                'object'          => '',
                'project_id'      => 0,
                'price'           => (float)$price,
                'currency'        => $currencyCode,
                'quantity_from'   => (float)$quantityFrom,
                'purchase_number' => (String)$purchaseNumber,
                'purchase_name'   => (String)$purchaseName

            ]
        );

        return $this->db->lastInsertId();
    }

    /**
     * @param array $purchasePriceArray [article_id, address_id, price, quantity_from, currency_code,purchase_number,purchase_name]
     */
    public function setPurchasePriceByArray($purchasePriceArray)
    {
        if(empty($purchasePriceArray)) {
            return;
        }

        $articleIds = [];
        $addressIds = [];

        foreach($purchasePriceArray as $purchasePrice) {
            if(!in_array($purchasePrice['article_id'], $articleIds)) {
                $articleIds[] = $purchasePrice['article_id'];
            }
            if(!in_array($purchasePrice['address_id'], $addressIds)) {
                $addressIds[] = $purchasePrice['address_id'];
            }
        }
        $select = $this->db->select();
        $select
            ->cols(
                [
                    'pp.id',
                    'pp.preis as price',
                    'IF(pp.waehrung <> \'\', pp.waehrung, \'EUR\') AS currency_code',
                    'pp.artikel AS article_id',
                    'pp.adresse AS address_id',
                    'pp.ab_menge AS quantity_from',
                    'pp.bestellnummer AS purchase_number',
                    'pp.bezeichnunglieferant AS purcase_name'
                ]
            )
            ->from('einkaufspreise AS pp')
            ->where("adresse IN (:address) AND artikel IN (:article) 
            AND (IFNULL(pp.gueltig_bis,'0000-00-00')='0000-00-00' OR pp.gueltig_bis >= CURDATE()) AND pp.geloescht!=1")
            ->bindValue('address', $addressIds)
            ->bindValue('article', $articleIds);

        $oldPrices = $this->db->fetchAll(
            $select->getStatement(),
            $select->getBindValues()
        );

        $priceTree = [];
        foreach($oldPrices as $oldPrice) {
            $articleId = (int)$oldPrice['article_id'];
            $addressId = (int)$oldPrice['address_id'];
            $currencyCode = $oldPrice['currency_code'];
            $quantityFrom = round($oldPrice['quantity_from'],8);
            $priceTree[$articleId][$addressId][$currencyCode][$quantityFrom] = $oldPrice['price'];
        }
        unset($oldPrices);

        foreach($purchasePriceArray as $purchasePrice) {
            $articleId = (int)$purchasePrice['article_id'];
            $addressId = (int)$purchasePrice['address_id'];
            $currencyCode = $purchasePrice['currency_code'];
            $quantityFrom = round($purchasePrice['quantity_from'],8);
            $price = round($purchasePrice['price'], 8);
            $purchaseNumber = !empty($purchasePrice['purchase_number'])?$purchasePrice['purchase_number']:'';
            $purchaseName = !empty($purchasePrice['purchase_name'])?$purchasePrice['purchase_name']:'';
            $aricleExists = !empty($priceTree[$articleId]);
            $addressExists = $aricleExists && !empty($priceTree[$articleId][$addressId]);
            $currencyExists = $addressExists && !empty($priceTree[$articleId][$addressId][$currencyCode]);
            $quantityFromExists = $currencyExists && !empty($priceTree[$articleId][$addressId][$currencyCode][$quantityFrom]);
            if(!$quantityFromExists || round($oldPrice['price'],8) !== $price) {
                $this->setPurchasePrice(
                    $articleId,
                    $addressId,
                    $price,
                    $currencyCode,
                    $quantityFrom,
                    $purchaseNumber,
                    $purchaseName
                );
            }
        }
    }
}
