<?php
namespace Xentral\Modules\Article\Service;

use Xentral\Components\Database\Database;
use Xentral\Modules\Article\Service\CurrencyConversionService;
use Xentral\Modules\Article\Exception\CurrencyExchangeRateNotFoundException;

final class SellingPriceService
{
    /** @var Database $db */
    private $db;

    /** @var CurrencyConversionService */
    private $currencyConversion;

    /**
     * @param Database                  $database
     * @param CurrencyConversionService $currencyConversion
     */
    public function __construct(Database $database, CurrencyConversionService $currencyConversion)
    {
        $this->db = $database;
        $this->currencyConversion = $currencyConversion;
    }

    /**
     * @param int    $articleId
     * @param float  $price
     * @param string $currencyCode
     * @param float  $quantityFrom
     */
    public function setStandardPrice($articleId, $price, $currencyCode, $quantityFrom = 0.0)
    {
        if(empty($currencyCode)) {
            $currencyCode = 'EUR';
        }
        $oldPrices = $this->db->fetchAll(
            "SELECT id, preis AS price, waehrung AS currency_code 
            FROM verkaufspreise WHERE artikel = :article_id AND art <> 'Gruppe'
                AND   (IFNULL(gueltig_bis,'0000-00-00') = '0000-00-00' OR gueltig_bis >= CURDATE())
                AND adresse = 0 AND IF(waehrung <> '', waehrung, 'EUR') = :currency_code AND ab_menge = :quantity_from
            ",

            [
                'article_id'    => (int)$articleId,
                'currency_code' => $currencyCode,
                'quantity_from' => (float)$quantityFrom
            ]
        );

        if(!empty($oldPrices)) {
            foreach($oldPrices as $oldPrice) {
                if(round($oldPrice['price'],8) === round($price, 8)) {
                    return;
                }
                $this->expire($oldPrice['id']);
            }
        }
        try {
            $conversionRate = $this->currencyConversion->tryGetEuroExchangeRateFromCurrencyCode($currencyCode);
        }
        catch (CurrencyExchangeRateNotFoundException $e) {
            $conversionRate = 0;
        }
        $this->db->perform("INSERT INTO verkaufspreise 
            (artikel, objekt, projekt, adresse, preis, waehrung, ab_menge, vpe, vpe_menge, angelegt_am, 
             firma, geloescht, art, gruppe, apichange, nichtberechnet, kurs, kursdatum)
            VALUES (:article_id, '', 0, 0, :price, :currency_code, :quantity_from, '',0, NOW(), 
                    1, 0,'Kunde', 0, 0,1, :conversion_rate, NOW())",
            [
                'article_id'      => (int)$articleId,
                'price'           => (float)$price,
                'currency_code'   => (String)$currencyCode,
                'quantity_from'   => (float)$quantityFrom,
                'conversion_rate' => $conversionRate,
            ]
        );
    }

    /**
     * @param array $priceArr
     */
    public function setStandardPriceByArray($priceArr)
    {
        if(empty($priceArr)) {
            return;
        }
        $articleIds = [];
        foreach($priceArr as $priceRow) {
            if(!in_array($priceRow['article_id'], $articleIds, false)) {
                $articleIds[] = $priceRow['article_id'];
            }
        }
        $select = $this->db->select();
        $select
            ->cols([
                    'p.id',
                    'p.preis as price',
                    'IF(p.waehrung <> \'\', p.waehrung, \'EUR\') AS currency_code',
                    'p.artikel AS article_id',
                    'p.ab_menge AS quantity_from'
                ]
            )
            ->from('verkaufspreise AS p')
            ->where("art <> 'Gruppe'AND   (IFNULL(gueltig_bis,'0000-00-00') = '0000-00-00' OR gueltig_bis >= CURDATE())
                AND adresse = 0 AND artikel IN (:articles)")
            ->bindValue('articles', $articleIds);

        $oldPrices = $this->db->fetchAll(
            $select->getStatement(),
            $select->getBindValues()
        );

        $priceTree = [];
        foreach($oldPrices as $oldPrice) {
            $priceTree[(int)$oldPrice['article_id']][$oldPrice['currency_code']][round($oldPrice['quantity_from'],8)] =
                round($oldPrice['price'],8);
        }
        unset($oldPrices);

        foreach($priceArr as $prices) {
            if($prices['currency_code'] === '') {
                $prices['currency_code'] = 'EUR';
            }
            $prices['quantity_from'] = round($prices['quantity_from'], 8);
            $prices['price'] = round($prices['price'], 8);
            $prices['article_id'] = (int)$prices['article_id'];
            $emptyArticle = empty($priceTree[$prices['article_id']]);
            $emptyCurrency = $emptyArticle|| empty($priceTree[$prices['article_id']][$prices['currency_code']]);
            if($emptyCurrency
                || empty($priceTree[$prices['article_id']][$prices['currency_code']][$prices['quantity_from']])
                || $priceTree[$prices['article_id']][$prices['currency_code']][$prices['quantity_from']]
                !== $prices['price']
            ) {
                $this->setStandardPrice(
                    $prices['article_id'],
                    $prices['price'],
                    $prices['currency_code'],
                    $prices['quantity_from']
                );
                $priceTree[$prices['article_id']][$prices['currency_code']][$prices['quantity_from']] = $prices['price'];
            }
        }
    }

    /**
     * @param int    $articleId
     * @param int    $addressId
     * @param float  $price
     * @param string $currencyCode
     * @param float  $quantityFrom
     */
    public function saveCustomerPrice($articleId, $addressId, $price, $currencyCode, $quantityFrom = 0.0)
    {
        $this->db->perform("INSERT INTO verkaufspreise 
            (artikel, objekt, projekt, adresse, preis, waehrung, ab_menge, vpe, vpe_menge, angelegt_am, 
             firma, geloescht, art, gruppe, apichange, nichtberechnet)
            VALUES (:article_id, '', 0, :address_id, :price, :currency_code, :quantity_from, '',0, NOW(), 
                    1, 0,'Kunde', 0, 0,1)",
            [
                'article_id'    => (int)$articleId,
                'address_id'    => (int)$addressId,
                'price'         => (float)$price,
                'currency_code' => $currencyCode,
                'quantity_from' => (float)$quantityFrom
            ]
        );
    }

    /**
     * @param int    $articleId
     * @param int    $groupId
     * @param float  $price
     * @param string $currencyCode
     * @param float  $quantityFrom
     */
    public function saveGroupPrice($articleId, $groupId, $price, $currencyCode, $quantityFrom = 0.0)
    {
        $this->db->perform("INSERT INTO verkaufspreise 
            (artikel, objekt, projekt, adresse, preis, waehrung, ab_menge, vpe, vpe_menge, angelegt_am, 
             firma, geloescht, art, gruppe, apichange, nichtberechnet)
            VALUES (:article_id, '', 0, 0, :price, :currency_code, :quantity_from, '',0, NOW(), 
                    1, 0, 'Gruppe', :group_id, 0,1)",
            [
                'article_id'    => (int)$articleId,
                'group_id'      => (int)$groupId,
                'price'         => (float)$price,
                'currency_code' => $currencyCode,
                'quantity_from' => (float)$quantityFrom
            ]
        );
    }

    /**
     * @param int $sellingPriceId
     */
    public function delete($sellingPriceId)
    {
        $this->db->perform(
            'DELETE FROM verkaufspreise WHERE id = :sellingprice_id',
            ['sellingprice_id' => $sellingPriceId]
        );
    }

    /**
     * @param int $aricleId
     */
    public function deleteAllByAricleId($aricleId)
    {
        $this->db->perform(
            'DELETE FROM verkaufspreise WHERE artikel = :article_id',
            ['article_id' => (int)$aricleId]
        );
    }

    /**
     * @param int $sellingPriceId
     */
    public function expire($sellingPriceId)
    {
        $this->db->perform(
            'UPDATE verkaufspreise SET gueltig_bis = DATE_SUB(CURDATE(), INTERVAL 1 DAY) WHERE id = :sellingprice_id',
            ['sellingprice_id' => $sellingPriceId]
        );
    }
}