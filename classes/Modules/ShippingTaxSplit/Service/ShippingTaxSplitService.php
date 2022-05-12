<?php

namespace Xentral\Modules\ShippingTaxSplit\Service;

use function Sabre\Xml\Deserializer\keyValue;
use Xentral\Components\Database\Database;
use Xentral\Modules\ShippingTaxSplit\Gateway\ShippingTaxSplitGateway;
use Xentral\Modules\ShippingTaxSplit\Exception\InvalidArgumentException;
use erpAPI;

final class ShippingTaxSplitService implements ShippingTaxSplitServiceInterface
{
    /** @var Database $db */
    private $db;

    /** @var ShippingTaxSplitGateway $gateway */
    private $gateway;

    /** @var erpAPI $erp */
    private $erp;

    /**
     * ShippingTaxSplitService constructor.
     *
     * @param Database                $database
     * @param ShippingTaxSplitGateway $gateway
     * @param erpAPI                 $erp
     */
    public function __construct(Database $database, ShippingTaxSplitGateway $gateway, erpAPI $erp)
    {
        $this->db = $database;
        $this->gateway = $gateway;
        $this->erp = $erp;
    }

    /**
     * @param int $orderId
     *
     * @return bool
     */
    public function deleteShippingPositionsByOrderId($orderId)
    {
        $numRows = (int)$this->db->fetchAffected(
            'DELETE ap FROM auftrag_position AS ap 
      INNER JOIN artikel AS art ON ap.artikel = art.id AND art.porto = 1
      WHERE ap.auftrag = :order_id',
            ['order_id' => (int)$orderId]
        );

        return $numRows > 0;
    }

    /**
     * @param array $nonShippingPositions
     *
     * @return array
     */
    private function getShippingAmountsByTax($nonShippingPositions)
    {
        $grossPerTax = [];
        $discountsPerTax = [];
        foreach ($nonShippingPositions as $position) {
            if ($position['gross'] < 0) {
                if (empty($discountsPerTax[$position['tax']])) {
                    $discountsPerTax[$position['tax']] = 0;
                }
                $discountsPerTax[$position['tax']] -= (float)$position['net'];
            } elseif ($position['gross'] > 0) {
                if (empty($grossPerTax[$position['tax']])) {
                    $grossPerTax[$position['tax']] = 0;
                }
                $grossPerTax[$position['tax']] += (float)$position['net'];
            }
        }

        $grossPerTaxFallBack = $grossPerTax;
        $useGrossPerTax = false;
        foreach ($grossPerTax as $tax => $gross) {
            if (!empty($discountsPerTax[$tax])) {
                if ($discountsPerTax[$tax] <= $gross) {
                    $grossPerTax[$tax] -= $discountsPerTax[$tax];
                    $useGrossPerTax = true;
                } else {
                    $grossPerTax[$tax] = 0;
                }
            } else {
                $useGrossPerTax = true;
            }
        }
        if (!$useGrossPerTax) {
            $grossPerTax = $grossPerTaxFallBack;
        }

        return $grossPerTax;
    }

    /**
     * @param array $nonShippingPositions
     * @param float $tax
     *
     * @return array
     */
    private function getTaxNameByPosition($nonShippingPositions, $tax)
    {
        if ($tax === 0) {
            return ['befreit', -1];
        }
        if (empty($nonShippingPositions)) {
            return ['normal', -1];
        }
        foreach ($nonShippingPositions as $nonShippingPosition) {
            if ($nonShippingPosition['tax'] == $tax) {
                if ($nonShippingPosition['tax_normal'] == $tax) {
                    return ['normal', -1];
                }
                if ($nonShippingPosition['tax_reduced'] == $tax) {
                    return ['ermaessigt', -1];
                }
            }
        }

        return ['normal', $tax];
    }

    /**
     * @param int $articleId
     * @param int $orderId
     *
     * @return string
     */
    private function getArticleNameByOrder($articleId, $orderId)
    {
        $name = $this->db->fetchRow(
            'SELECT name_de, name_en FROM artikel WHERE id = :article_id LIMIT 1',
            ['article_id' => (int)$articleId]
        );
        $language = $this->db->fetchValue('SELECT sprache FROM auftrag WHERE id = :order_id',
            ['order_id' => (int)$orderId]);
        $languageLower = strtolower($language);
        if ($language != '' && $languageLower !== 'de' && $languageLower !== 'deutsch' && $languageLower !== 'german') {
            return !empty($name['name_en']) ? (String)$name['name_en'] : (String)$name['name_de'];
        }

        return (String)$name['name_de'];
    }

    /**
     * @param array $nonShippingPositions
     *
     * @return string
     */
    public function getCurrencyByNonSippingPositions($nonShippingPositions)
    {
        $position = reset($nonShippingPositions);

        return !empty($position['currency']) ? $position['currency'] : 'EUR';
    }

    /**
     * @param array $grossPerTax
     *
     * @return float
     */
    private function getMaxTax($grossPerTax)
    {
        if (count($grossPerTax) <= 1) {
            $returnTax = array_keys($grossPerTax);

            return reset($returnTax);
        }
        foreach($grossPerTax as $tax => $value) {
            $grossPerTax[$tax] = round($value, 4);
        }
        arsort($grossPerTax);
        $returnAmount = false;
        $returnTax = array_keys($grossPerTax);
        $returnTax = reset($returnTax);
        foreach ($grossPerTax as $tax => $amount) {
            if ($tax == 0) {
                continue;
            }
            if ($returnAmount === false) {
                $returnAmount = $amount;
                $returnTax = $tax;
            }
            elseif ($amount < $returnAmount) {
                return $returnTax;
            }
            if ($tax > $returnTax) {
                $returnTax = $tax;
            }
        }

        return $returnTax;
    }

    /**
     * @param int   $orderId
     * @param int   $articleId
     * @param float $amount
     *
     * @return bool
     */
    public function addOrReplaceShippingPositionByMaxTaxToOrder($orderId, $articleId, $amount)
    {
        if (!is_numeric($orderId) || $orderId <= 0) {
            throw new InvalidArgumentException('OrderId is not valid');
        }
        if (!$this->db->fetchCol('SELECT id FROM artikel WHERE porto = 1 AND id = :article_id',
            ['article_id' => $articleId])) {
            throw new InvalidArgumentException(sprintf('Article %d has no Shipping-Attribute', $articleId));
        }

        $withTax = $this->erp->AuftragMitUmsatzsteuer($orderId);

        if ($withTax) {
            $shippingPositions = $this->gateway->getShippingPositionsByOrderId($orderId);
        } else {
            $shippingPositions = $this->gateway->getShippingPositionsWithoutTaxByOrderId($orderId);
        }

        if ($amount == 0 && !empty($shippingPositions)) {
            return $this->deleteShippingPositionsByOrderId($orderId);
        }

        if ($withTax) {
            $nonShippingPositions = $this->gateway->getNonShippingPositionsByOrderId($orderId);
        } else {
            $nonShippingPositions = $this->gateway->getNonShippingPositionsWithoutTaxByOrderId($orderId);
        }

        if (empty($nonShippingPositions)) {
            if (empty($shippingPositions)) {
                return true;
            }

            return $this->deleteShippingPositionsByOrderId($orderId);
        }

        $tax = 0;
        if ($withTax) {
            $grossPerTax = $this->getShippingAmountsByTax($nonShippingPositions);
            //arsort($grossPerTax);
            //$tax = array_keys($grossPerTax);
            $tax = $this->getMaxTax($grossPerTax);
            /*if ($tax[0] == 0 && count($tax) > 1) {
                $tax = $tax[1];
            } else {
                $tax = reset($tax);
            }*/
        }

        $articleName = $this->getArticleNameByOrder($articleId, $orderId);
        $net = $amount / (1 + $tax / 100);
        list($taxName, $taxPosition) = $this->getTaxNameByPosition($nonShippingPositions, $tax);
        if (!$withTax) {
            $taxPosition = 0;
        }
        $currency = $this->getCurrencyByNonSippingPositions($nonShippingPositions);

        if (empty($shippingPositions)) {
            $orderPositionId = $this->erp->AddPositionManuellPreis(
                'auftrag', $orderId, $articleId, 1, $articleName, $net, $taxName
            );
            $this->db->perform(
                'UPDATE auftrag_position SET umsatzsteuer = :tax_name, waehrung = :currency WHERE id = :order_position_id',
                [
                    'tax_name'          => $taxName,
                    'order_position_id' => $orderPositionId,
                    'currency'          => $currency,
                ]
            );

            return true;
        }
        $first = true;
        foreach ($shippingPositions as $position) {
            if ($first) {
                $first = false;
                $this->db->perform(
                    'UPDATE auftrag_position SET preis = :net, steuersatz = :tax, waehrung = :currency, menge = :quantity, rabatt = 0, umsatzsteuer = :tax_name WHERE id = :order_position_id',
                    [
                        'quantity'          => $position['amount'],
                        'net'               => $net / $position['amount'],
                        'tax'               => $taxPosition,
                        'currency'          => $currency,
                        'tax_name'          => $taxName,
                        'order_position_id' => $position['order_position_id'],
                    ]
                );
            } else {
                $this->deleteOrderPosition($position['order_position_id']);
            }
        }

        return true;
    }

    /**
     * @param array $grossPerTax
     * @param float $amount
     * @param float $factor
     * @param float $taxNormal
     * @param float $taxReduced
     *
     * @return array
     */
    private function getNetShippingFromGrossPerTax($grossPerTax, $amount, $factor, $taxNormal, $taxReduced)
    {
        $netShipping = [];
        foreach ($grossPerTax as $tax => $gross) {
            if ($gross > 0) {
                $taxPosition = $tax;
                $taxname = 'normal';
                if ($tax == 0) {
                    $taxPosition = -1;
                    $taxname = 'befreit';
                } elseif ($tax == $taxReduced) {
                    $taxname = 'ermaessigt';
                    $taxPosition = -1;
                } elseif ($tax == $taxNormal) {
                    $taxPosition = -1;
                }
                $netShipping[$tax] = [
                    'gross'    => $amount * $gross / $factor,
                    'net'      => $amount * ($gross / $factor) / (1 + $tax / 100),
                    'tax'      => $taxPosition,
                    'tax_name' => $taxname,
                ];
            }
        }

        return $netShipping;
    }

    /**
     * @param array $grossPerTax
     *
     * @return array
     */
    private function getTaxFactors($grossPerTax)
    {
        $grossPerTaxFallBack = $grossPerTax;
        $useGrossPerTax = false;
        $factorFallback = 0;
        $factor = 0;
        foreach ($grossPerTax as $tax => $gross) {
            if (count($grossPerTax) > 1 && $tax == 0) {
                continue;
            }
            $factorFallback += $gross;
            if (!empty($discountsPerTax[$tax])) {
                if ($discountsPerTax[$tax] <= $gross) {
                    $grossPerTax[$tax] -= $discountsPerTax[$tax];
                    $useGrossPerTax = true;
                } else {
                    $grossPerTax[$tax] = 0;
                }
            } else {
                $useGrossPerTax = true;
            }
            $factor += $gross;
        }
        if (!$useGrossPerTax) {
            $grossPerTax = $grossPerTaxFallBack;
            $factor = $factorFallback;
        }

        return [$grossPerTax, $factor];
    }

    /**
     * @param array $shippingPositions
     *
     * @return array
     */
    public function deleteTaxDoubletes($shippingPositions)
    {
        if (empty($shippingPositions)) {
            return $shippingPositions;
        }
        $taxes = [];
        foreach ($shippingPositions as $shippingPositionKey => $shippingPosition) {
            if (in_array($shippingPosition['tax'], $taxes)) {
                $this->deleteOrderPosition($shippingPosition['order_position_id']);
                unset($shippingPositions[$shippingPositionKey]);
            } else {
                $taxes[] = $shippingPosition['tax'];
            }
        }

        return $shippingPositions;
    }

    /**
     * @param array $shippingPositions
     * @param array $taxes
     *
     * @return array
     */
    public function deleteTaxesNotInArray($shippingPositions, $taxes)
    {
        if (empty($shippingPositions)) {
            return $shippingPositions;
        }
        foreach ($shippingPositions as $shippingPositionKey => $shippingPosition) {
            if (!in_array($shippingPosition['tax'], $taxes, false)) {
                $this->deleteOrderPosition($shippingPosition['order_position_id']);
                unset($shippingPositions[$shippingPositionKey]);
            }
        }

        return $shippingPositions;
    }

    /**
     * @param array $shippingPositions
     *
     * @return array
     */
    public function getShippingPositionTaxes($shippingPositions)
    {
        $taxes = [];
        if (empty($shippingPositions)) {
            return $taxes;
        }
        foreach ($shippingPositions as $shippingPosition) {
            $taxes[] = (float)$shippingPosition['tax'];
        }

        return $taxes;
    }

    /**
     * @param int   $orderId
     * @param int   $articleId
     * @param float $amount
     *
     * @return bool
     */
    public function addOrReplaceShippingPositionToOrder($orderId, $articleId, $amount)
    {
        if (!is_numeric($orderId) || $orderId <= 0) {
            throw new InvalidArgumentException('OrderId is not valid');
        }
        if (!$this->db->fetchCol('SELECT id FROM artikel WHERE porto = 1 AND id = :article_id',
            ['article_id' => $articleId])) {
            throw new InvalidArgumentException('Article has no Shipping-Attribute');
        }

        $withTax = $this->erp->AuftragMitUmsatzsteuer($orderId);

        if ($withTax) {
            $shippingPositions = $this->gateway->getShippingPositionsByOrderId($orderId);
        } else {
            $shippingPositions = $this->gateway->getShippingPositionsWithoutTaxByOrderId($orderId);
        }

        if ($amount == 0 && !empty($shippingPositions)) {
            return $this->deleteShippingPositionsByOrderId($orderId);
        }

        if ($withTax) {
            $nonShippingPositions = $this->gateway->getNonShippingPositionsByOrderId($orderId);
        } else {
            $nonShippingPositions = $this->gateway->getNonShippingPositionsWithoutTaxByOrderId($orderId);
        }
        if (empty($nonShippingPositions)) {
            if (empty($shippingPositions)) {
                return true;
            }

            return $this->deleteShippingPositionsByOrderId($orderId);
        }

        $grossPerTax = [];
        $discountsPerTax = [];
        $taxReduced = false;
        $taxNormal = false;
        foreach ($nonShippingPositions as $position) {
            $taxReduced = $position['tax_reduced'];
            $taxNormal = $position['tax_normal'];
            if ($position['gross'] < 0) {
                if (empty($discountsPerTax[$position['tax']])) {
                    $discountsPerTax[$position['tax']] = 0;
                }
                $discountsPerTax[$position['tax']] -= (float)$position['gross'];
            } elseif ($position['gross'] > 0) {
                if (empty($grossPerTax[$position['tax']])) {
                    $grossPerTax[$position['tax']] = 0;
                }
                $grossPerTax[$position['tax']] += (float)$position['gross'];
            }
        }

        list($grossPerTax, $factor) = $this->getTaxFactors($grossPerTax);


        $netShipping = $this->getNetShippingFromGrossPerTax($grossPerTax, $amount, $factor, $taxNormal, $taxReduced);

        $name = $this->getArticleNameByOrder($articleId, $orderId);

        $shippingPositions = $this->deleteTaxDoubletes($shippingPositions);
        $netShippingTaxes = array_keys($netShipping);
        $shippingPositions = $this->deleteTaxesNotInArray($shippingPositions, $netShippingTaxes);
        $shippingPositionsTaxes = $this->getShippingPositionTaxes($shippingPositions);
        $currency = $this->getCurrencyByNonSippingPositions($nonShippingPositions);

        foreach ($netShipping as $tax => $shippingArticle) {
            if (!in_array((float)$tax, $shippingPositionsTaxes)) {
                $orderPositionId = $this->erp->AddPositionManuellPreis(
                    'auftrag', $orderId, $articleId, 1, $name, $shippingArticle['net'], $shippingArticle['tax_name']
                );
                $this->db->perform(
                    'UPDATE auftrag_position SET umsatzsteuer = :tax_name, waehrung = :currency WHERE id = :order_position_id',
                    [
                        'tax_name'          => $shippingArticle['tax_name'],
                        'order_position_id' => $orderPositionId,
                        'currency'          => $currency,
                    ]
                );
                if (!$withTax) {
                    $this->db->perform(
                        'UPDATE auftrag_position SET steuersatz = :tax WHERE id = :order_position_id',
                        [
                            'tax'               => 0,
                            'order_position_id' => $orderPositionId,
                        ]
                    );
                }
            }
        }

        foreach ($shippingPositions as $shippingKey => $shippingPosition) {
            $shippingPositionTax = $shippingPosition['tax'];
            if (!empty($netShipping[$shippingPositionTax]) && $shippingPosition['net'] != $netShipping[$shippingPosition['tax']]['net']) {
                $this->db->perform(
                    'UPDATE auftrag_position 
                     SET menge = 1, rabatt = 0, preis = :net, waehrung = :currency
                     WHERE id = :order_position_id LIMIT 1',
                    [
                        'net'               => (float)$netShipping[$shippingPositionTax]['net'],
                        'order_position_id' => (int)$shippingPosition['order_position_id'],
                        'currency'          => $currency,
                    ]
                );
            }
            if (!$withTax) {
                $this->db->perform(
                    'UPDATE auftrag_position SET steuersatz = :tax WHERE id = :order_position_id',
                    [
                        'tax'               => 0,
                        'order_position_id' => (int)$shippingPosition['order_position_id'],
                    ]
                );
            }
        }

        return true;
    }

    /**
     * @param int $orderPositionId
     */
    private function deleteOrderPosition($orderPositionId)
    {
        $this->db->perform(
            'DELETE lr 
            FROM lager_reserviert AS lr 
            INNER JOIN auftrag_position AS ap ON lr.objekt = \'auftrag\' AND lr.parameter = ap.auftrag 
            WHERE ap.id = :order_position_id',
            ['order_position_id' => $orderPositionId]
        );
        $posArr = $this->db->fetchRow(
            'SELECT sort, auftrag FROM auftrag_position  WHERE id = :order_position_id',
            ['order_position_id' => $orderPositionId]
        );
        $sort = $posArr['sort'];
        $orderId = $posArr['auftrag'];
        $this->db->perform(
            'DELETE FROM auftrag_position WHERE id = :order_position_id',
            ['order_position_id' => $orderPositionId]
        );
        $this->db->perform(
            'UPDATE auftrag_position SET sort = sort - 1 WHERE auftrag = :order_id AND sort > :sort',
            ['order_id' => $orderId, 'sort' => $sort]
        );

        $beleg_zwischenpositionensort = $this->db->fetchRow(
            "SELECT id, sort 
            FROM beleg_zwischenpositionen 
            WHERE doctype = 'auftrag' AND doctypeid = :order_id AND pos = :sort 
            ORDER BY sort 
            DESC LIMIT 1",
            ['order_id' => $orderId, 'sort' => $sort]
        );
        $offset = 0;
        if (!empty($beleg_zwischenpositionensort)) {
            $offset = 1 + $beleg_zwischenpositionensort['sort'];
        }

        $this->db->perform(
            "UPDATE beleg_zwischenpositionen 
            SET pos = pos - 1, sort = sort + :sort_offset 
            WHERE doctype = 'auftrag' AND doctypeid = :order_id AND pos = :sort",
            ['sort_offset' => $offset, 'order_id' => $orderId, 'sort' => $sort]
        );
        $this->db->perform(
            "UPDATE beleg_zwischenpositionen 
            SET pos = pos - 1 
            WHERE doctype = 'auftrag' AND doctypeid = :order_id AND pos > :sort",
            ['order_id' => $orderId, 'sort' => $sort]
        );
    }

}
