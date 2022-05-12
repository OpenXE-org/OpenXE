<?php

namespace Xentral\Modules\Voucher\Gateway;

use Xentral\Components\Database\Database;
use Xentral\Modules\Voucher\Exception\InvalidArgumentException;
use Xentral\Modules\Voucher\Exception\RuntimeException;
use DateInterval;
use DateTime;
use Exception;
use ApplicationCore;

final class VoucherGateway
{
    /** @var Database $db */
    private $db;

    /** @var ApplicationCore $app */
    private $app;

    /** @var array $validtypes */
    private $validtypes;

    /** @var array $digits */
    private $digits;

    /** @var array alphas */
    private $alphas;

    /**
     * @param Database        $database
     * @param ApplicationCore $app
     */
    public function __construct(Database $database, ApplicationCore $app)
    {
        $this->db = $database;
        $this->app = $app;
        $this->validtypes = [
            ''              => 'unbegrenzt',
            'ThisYear'      => 'Aktuelles Jahr',
            'ThisYearPlus1' => 'Aktuelles Jahr + 1',
            'ThisYearPlus2' => 'Aktuelles Jahr + 2',
            'ThisYearPlus3' => 'Aktuelles Jahr + 3',
            'Months3'       => '3 Monate',
            'Months6'       => '6 Monate',
            'Year1'         => '1 Jahr',
            'Year2'         => '2 Jahre',
            'Year3'         => '3 Jahre',
        ];
        for ($i = 0; $i < 10; $i++) {
            $this->digits[] = (String)$i;
        }
        $a = ord('A');
        for ($i = 0; $i < 26; $i++) {
            $chr = chr($a + $i);
            $this->alphas[] = $chr;
        }
    }

    /**
     * @param int   $voucherId
     * @param float $amount
     *
     * @return bool
     */
    public function changeVoucherAmount($voucherId, $amount)
    {
        $voucher = $this->getById($voucherId);
        if (empty($voucher)) {
            return false;
        }
        $possibleToChange = $this->db->fetchCol(
                sprintf(
                    'SELECT v.id FROM voucher AS v
                LEFT JOIN rechnung AS re ON v.invoice_id = re.id
                WHERE v.id = %d AND (v.invoice_id = 0 OR re.status <> \'versendet\')',
                    $voucherId
                )
            ) > 0;
        if (!$possibleToChange) {
            return false;
        }
        $this->db->perform(
            'UPDATE voucher 
            SET voucher_residual_value = :amount, voucher_original_value = :amount 
            WHERE id = :id ',
            ['amount' => $amount, 'id' => $voucherId]
        );

        return true;
    }

    /**
     * @param $voucherId
     *
     * @return array
     */
    public function getById($voucherId)
    {
        $sql = 'SELECT v.* 
                  FROM voucher AS v 
                  WHERE v.id = :voucher_id
                  LIMIT 1';
        $result = $this->db->fetchRow($sql, [
            'voucher_id' => $voucherId,
        ]);
        if (empty($result)) {
            throw new RuntimeException(sprintf('%s Vaucher not found.', $voucherId));
        }
        if ($result['tax_name'] !== 'ermaessigt' && $result['tax_name'] !== 'normal') {
            $result['tax_name'] = 'befreit';
        }

        return $result;
    }

    /**
     * @param string $voucherCode
     *
     * @return array
     */
    public function getVoucherByCode($voucherCode)
    {
        $sql = 'SELECT v.*
              FROM voucher AS v
              WHERE v.voucher_code = :voucher_code';


        $vouchers = $this->db->fetchAll($sql, [
            'voucher_code' => $voucherCode,
        ]);
        if (!empty($vouchers)) {
            foreach ($vouchers as $key => $voucher) {
                if ($voucher['tax_name'] !== 'normal' && $voucher['tax_name'] !== 'ermaessigt') {
                    $vouchers[$key]['tax_name'] = 'befreit';
                }
            }
        }

        return $vouchers;
    }


    /**
     * @param string $voucherCode
     *
     * @return bool
     */
    public function isValidVoucher($voucherCode)
    {
        if (empty($voucherCode)) {
            return false;
        }
        $sql = 'SELECT v.id 
                  FROM voucher AS v 
                  LEFT JOIN rechnung AS re ON v.invoice_id = re.id
                  WHERE v.voucher_code = :voucher_code
                  AND (IFNULL(valid_from,\'0000-00-00\') = \'0000-00-00\' OR IFNULL(valid_from,\'0000-00-00\') <= CURDATE())
                  AND (IFNULL(valid_to,\'0000-00-00\') = \'0000-00-00\' OR IFNULL(valid_to,\'0000-00-00\') >= CURDATE())
                  AND (v.invoice_id = 0 OR re.status = \'versendet\' OR re.status = \'freigegeben\')
                  LIMIT 1';
        $result = $this->db->fetchAll($sql, [
            'voucher_code' => $voucherCode,
        ]);

        return !empty($result);
    }

    /**
     * @param array|int $voucher
     * @param array     $positions
     *
     * @return bool
     */
    public function isPossibleToUseVoucher($voucher, $positions)
    {
        if (empty($voucher)) {
            return false;
        }
        if (is_int($voucher)) {
            $voucher = $this->getById($voucher);
        }
        if (!$this->isValidVoucher($voucher['voucher_code'])) {
            return false;
        }
        if (empty($positions) || !is_array($positions)) {
            return true;
        }
        $taxName = !empty($voucher['tax_name']) ? $voucher['tax_name'] : 'normal';
        if ($taxName === 'befreit') {
            return true;
        }
        $return = false;
        foreach ($positions as $position) {
            if ((int)$position['id'] === (int)$voucher['article_id']) {
                return false;
            }
            if ($taxName === !empty($position['umsatzsteuer']) ? $position['umsatzsteuer'] : 'normal') {
                $return = true;
            }
        }

        return $return;
    }


    /**
     * @refactor Order-Module
     *
     * @param int    $docId
     * @param string $doctype
     *
     * @return array Order
     */
    public function getDocumentById($docId, $doctype = 'auftrag')
    {
        if ($doctype === 'rechnung') {
            $sql = 'SELECT inv.* 
                  FROM `rechnung` AS inv 
                  WHERE inv.id = :id
                  LIMIT 1';
        } else {
            $sql = 'SELECT o.* 
                  FROM auftrag AS o 
                  WHERE o.id = :id
                  LIMIT 1';
        }
        $result = $this->db->fetchRow($sql, [
            'id' => $docId,
        ]);
        if (empty($result)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s %s does not exists.',
                    $doctype === 'rechnung' ? 'Invoice' : 'Order', $docId
                )
            );
        }

        return $result;
    }

    /**
     * @param int $orderId
     * @param int $voucherId
     *
     * @return int OrderPositionId
     */
    public function getVoucherPositionIdFromOrder($orderId, $voucherId)
    {
        $voucher = $this->getById($voucherId);
        $articleId = $voucher['article_id'];
        $sql = 'SELECT ap.id 
        FROM auftrag_position AS ap 
        WHERE ap.auftrag = :auftrag AND ap.artikel = :article_id LIMIT 1';
        $result = $this->db->fetchValue($sql, [
            'auftrag'    => $orderId,
            'article_id' => $articleId,
        ]);
        if (empty($result)) {
            throw new InvalidArgumentException(sprintf(
                'OrderPosition From Order %s not found.', $orderId
            ));
        }

        return $result;
    }

    /**
     * @param int $voucherOrderId
     *
     * @return array
     */
    public function getVoucherOrderById($voucherOrderId)
    {
        $sql = 'SELECT vo.* 
                  FROM voucher_order AS vo 
                  WHERE vo.id = :id
                  LIMIT 1';
        $result = $this->db->fetchRow($sql, [
            'id' => $voucherOrderId,
        ]);
        if (empty($result)) {
            throw new InvalidArgumentException(sprintf(
                'VocherOrder %s does not exists.', $voucherOrderId
            ));
        }

        return $result;
    }

    /**
     * @param int    $doctypeId
     * @param string $doctype
     *
     * @return array
     */
    public function getVoucherOrderByOrderId($doctypeId, $doctype = 'auftrag', $doctypePositionId = 0)
    {
        if($doctypePositionId > 0){
          $sql = 'SELECT vo.* 
                  FROM voucher_order AS vo 
                  WHERE vo.order_id = :order_id AND vo.doctype = :doctype AND vo.order_position_id = :order_position_id
                  LIMIT 1';
          $result = $this->db->fetchRow($sql, [
            'order_id' => $doctypeId,
            'doctype'  => $doctype,
            'order_position_id' => $doctypePositionId
          ]);
        }else{
          $sql = 'SELECT vo.* 
                  FROM voucher_order AS vo 
                  WHERE vo.order_id = :order_id AND vo.doctype = :doctype
                  LIMIT 1';
          $result = $this->db->fetchRow($sql, [
            'order_id' => $doctypeId,
            'doctype'  => $doctype,
          ]);
        }


        if (empty($result)) {
            throw new InvalidArgumentException(sprintf(
                'VocherOrder %s does not exists.', $doctypeId
            ));
        }

        return $result;
    }

    /**
     * @param int $voucherId
     *
     * @return float|null
     */
    public function getResidualValueFromVoucherId($voucherId)
    {
        $sql = 'SELECT v.voucher_original_value - IFNULL(SUM(ABS(vo.voucher_value)),0)
        FROM voucher AS v
        LEFT JOIN voucher_order AS vo ON v.id = vo.voucher_id
        WHERE v.id = :id
        ';

        return $this->db->fetchValue($sql, ['id' => $voucherId]);
    }

    /**
     * @param int $invoiceId
     *
     * @return array
     */
    public function getInvoiceById($invoiceId)
    {
        $sql = 'SELECT inv.* 
                  FROM rechnung AS inv 
                  WHERE inv.id = :id
                  LIMIT 1';
        $result = $this->db->fetchRow($sql, [
            'id' => $invoiceId,
        ]);
        if (empty($result)) {
            throw new InvalidArgumentException(sprintf(
                'Inovice %s does not exists.', $invoiceId
            ));
        }

        return $result;
    }

    /**
     * @param int $voucherId
     * @param int $limit
     *
     * @return array
     */
    public function getVoucherOrdersFromVoucher($voucherId, $limit = 500)
    {
        $sql = 'SELECT vo.* 
                  FROM voucher_order AS vo 
                  WHERE vo.voucher_id = :id
                  LIMIT :limit';

        return $this->db->fetchRow($sql, [
            'id'    => (int)$voucherId,
            'limit' => (int)$limit,
        ]);
    }

    /**
     * @param int|array $voucher
     * @param array     $positions
     *
     * @return float|int|mixed
     */
    public function getUsableVoucherValueByPos($voucher, $positions)
    {
        if (empty($voucher)) {
            throw new RuntimeException('No Voucher given');
        }
        if (is_int($voucher)) {
            $voucher = $this->getById($voucher);
        }
        if (!$this->isValidVoucher($voucher['voucher_code'])) {
            throw new RuntimeException(sprintf('Voucher %s is not valid', $voucher['voucher_code']));
        }
        if (empty($voucher)) {
            throw new RuntimeException('Voucher not found');
        }
        if (empty($positions) || !is_array($positions)) {
            return 0;
        }
        if ($voucher['tax_name'] !== 'normal' && $voucher['tax_name'] !== 'ermaessigt') {
            $voucher['tax_name'] = 'befreit';
        }
        $return = 0;
        $taxName = !empty($voucher['tax_name']) ? $voucher['tax_name'] : 'befreit';
        foreach ($positions as $position) {
            if ($taxName === 'befreit' || $taxName === '' || $taxName === (!empty($position['umsatzsteuer']) ? $position['umsatzsteuer'] : 'normal')) {
                $return += (float)(!empty($position['preis_brutto']) ? $position['preis_brutto'] : $position['preis']) * $position['menge'] * (1 - (!empty($position['rabatt']) ? $position['rabatt'] : 0) / 100);
            }
        }

        return $return > $voucher['voucher_residual_value'] ? $voucher['voucher_residual_value'] : $return;
    }

    /**
     * @param array $positions
     *
     * @return string
     */
    public function getVoucherTaxByPosPositions($positions)
    {
        $tax = '';
        if (!empty($positions)) {
            foreach ($positions as $position) {
                if ($position['preis_netto'] <= 0) {
                    continue;
                }
                if ($tax === '') {
                    $tax = round($position['tax'], 2);
                } elseif ($tax !== round($position['tax'], 2)) {
                    return 'befreit';
                }
            }
        }


        return empty($tax) ? 'befreit' : $tax;
    }

    /**
     * @param int|array $voucherId
     * @param int       $doctypeId
     * @param string    $doctype
     * @param float     $voucher_value
     * @param int       $positionId
     *
     * @return int voucher_order_id
     */
    public function addVoucherToOrder($voucherId, $doctypeId, $doctype, $voucher_value, $positionId = 0)
    {
        if (is_array($voucherId)) {
            $voucher = $voucherId;
            $voucherId = $voucher['id'];
        }

        if ($doctype !== 'rechnung') {
            $doctype = 'auftrag';
        }

        if (!empty($positionId)) {
            if ($doctype === 'auftrag') {
                $positionId = (int)$this->db->fetchValue(
                    'SELECT id 
                    FROM `auftrag_position` 
                    WHERE id = :position_id AND `auftrag` = :doctype_id 
                    LIMIT 1',
                    ['position_id' => $positionId, 'doctype_id' => $doctypeId]
                );
            } else {
                $positionId = (int)$this->db->fetchValue(
                    'SELECT id 
                    FROM `rechnung_position` 
                    WHERE id = :position_id AND `rechnung` = :doctype_id 
                    LIMIT 1',
                    ['position_id' => $positionId, 'doctype_id' => $doctypeId]
                );
            }
        }

        if ((float)$voucher_value === 0.0) {
            throw new InvalidArgumentException('Voucher Value must not be 0.');
        }

        if ($doctypeId <= 0) {
            throw new InvalidArgumentException(sprintf(
                '%s is not a valid %s.', $doctypeId, ($doctype === 'auftrag' ? 'OrderId' : 'InvoiceId')
            ));
        }
        if ($voucherId <= 0) {
            throw new InvalidArgumentException(sprintf(
                '%s is not a valid VoucherId.', $voucherId
            ));
        }
        if (!is_numeric($voucher_value)) {
            throw new InvalidArgumentException(sprintf(
                '%s is not a valid Voucher Value.', $voucher_value
            ));
        }

        $document = $this->getDocumentById($doctypeId, $doctype);

        if (empty($document)) {
            throw new InvalidArgumentException(sprintf(
                '%s is not a valid %s.', $doctypeId, ($doctype === 'auftrag' ? 'Order' : 'Invoice')
            ));
        }
        if (empty($voucher)) {
            $voucher = $this->getById($voucherId);
        }
        if ($voucher['voucher_residual_value'] < $voucher_value) {
            throw new RuntimeException('Voucher could not be added to order.');
        }

        if ($voucher['only_for_customer'] && (int)$voucher['address_id'] !== (int)$document['adresse']) {
            throw new RuntimeException('Voucher-Address does not match to Order.');
        }

        if ($voucher['tax_name'] !== 'normal' && $voucher['tax_name'] !== 'ermaessigt') {
            $voucher['tax_name'] = 'befreit';
        }

        /** @deprecated-block-start */
        $voucher_value_net = -abs($voucher_value);
        if ($voucher['tax_name'] === 'ermaessigt') {
            $voucher_value_net /= $this->app->erp->GetSteuersatzErmaessigt(true, $doctypeId, $doctype);
        } elseif ($voucher['tax_name'] !== 'befreit') {
            $voucher_value_net /= $this->app->erp->GetSteuersatzNormal(true, $doctypeId, $doctype);
        }
        $article_name = $this->db->fetchValue(
            'SELECT art.name_de FROM artikel AS art WHERE id = :article_id',
            ['article_id' => $voucher['article_id']]
        );
        $isPosEmpty = empty($positionId);
        if ($doctype === 'rechnung') {
            if ($isPosEmpty) {
                $positionId = $this->app->erp->AddRechnungPositionManuell($doctypeId, $voucher['article_id'],
                    $voucher_value_net, 1,
                    $article_name, $voucher['voucher_code'], $voucher['currency']);
            } else {
                $this->db->perform('UPDATE `rechnung_position` SET preis = :price WHERE id = :position_id',
                    ['price' => $voucher_value_net, 'position_id' => $positionId]
                );
            }
        } elseif ($isPosEmpty) {
            $positionId = (int)$this->app->erp->AddAuftragPositionManuell($doctypeId, $voucher['article_id'],
                $voucher_value_net, 1,
                $article_name, $voucher['voucher_code'], $voucher['currency']);
        } else {
            $this->db->perform('UPDATE `auftrag_position` SET preis = :price WHERE id = :position_id',
                ['price' => $voucher_value_net, 'position_id' => $positionId]
            );
        }
        if (in_array($voucher['tax_name'], ['befreit', 'ermaessigt', 'normal'])) {
            if ($doctype === 'auftrag') {
                $this->db->perform('UPDATE `auftrag_position` SET umsatzsteuer = :tax_name WHERE id = :id',
                    ['id' => (int)$positionId, 'tax_name' => $voucher['tax_name']]
                );
            } else {
                $this->db->perform('UPDATE `rechnung_position` SET umsatzsteuer = :tax_name WHERE id = :id',
                    ['id' => (int)$positionId, 'tax_name' => $voucher['tax_name']]
                );
            }
        }
        /** @deprecated-block-end */
        if (empty($positionId)) {
            throw new RuntimeException('Voucher could not be added to order.');
        }

        $this->db->perform(
            'UPDATE voucher SET voucher_residual_value = voucher_residual_value - :voucher_residual_value WHERE id = :id',
            ['id' => (int)$voucherId, 'voucher_residual_value' => $voucher_value]
        );

        $this->db->perform(
            'INSERT INTO voucher_order (voucher_id, order_id, voucher_value, doctype, order_position_id) 
                VALUES (:voucher_id, :order_id, :order_value, :doctype, :order_position_id)',
            [
                'voucher_id'  => (int)$voucherId,
                'order_id'    => (int)$doctypeId,
                'order_position_id' => (int)$positionId,
                'order_value' => abs((float)$voucher_value),
                'doctype'     => $doctype
            ]
        );
        $insertId = (int)$this->db->lastInsertId();
        if ($insertId === 0) {
            if ($doctype === 'rechnung') {
                $this->db->perform('DELETE FROM rechnung_position WHERE id = :id', ['id' => $positionId]);
                throw new RuntimeException('Voucher could not be added to invoice.');
            }
            $this->db->perform('DELETE FROM auftrag_position WHERE id = :id', ['id' => $positionId]);
            throw new RuntimeException('Voucher could not be added to order.');
        }
        $this->recalculateResidualValue($voucherId);
        $this->changeAddress($voucherId, $document['adresse']);

        return $insertId;
    }

    /**
     * @param int $voucherId
     * @param int $adressId
     */
    private function changeAddress($voucherId, $adressId)
    {
        $voucher = $this->getById($voucherId);
        if (empty($voucher['address_id']) || $voucher['address_id'] !== $adressId) {
            $this->db->perform(
                'UPDATE voucher SET address_id = :address_id WHERE id = :id ',
                ['address_id' => $adressId, 'id' => $voucherId]
            );
        }
    }

    /**
     * @param int    $voucherId
     * @param string $doctype
     * @param int    $doctypeId
     * @param int    $positionId
     * @param int    $reedemed
     * @param float  $redeemedValue
     * @param int    $projectId
     * @param string $username
     */
    public function addVoucherPosLog(
        $voucherId,
        $doctype,
        $doctypeId,
        $positionId,
        $reedemed,
        $redeemedValue,
        $projectId = 0,
        $username = ''
    ) {
        if ((float)$redeemedValue === 0.0) {
            throw new InvalidArgumentException('Redeemed Value must not be 0.');
        }

        if ($doctypeId <= 0) {
            throw new InvalidArgumentException(sprintf(
                '%s is not a valid %s.', $doctypeId, ($doctype === 'auftrag' ? 'OrderId' : 'InvoiceId')
            ));
        }
        if ($voucherId <= 0) {
            throw new InvalidArgumentException(sprintf(
                '%s is not a valid VoucherId.', $voucherId
            ));
        }
        if (!is_numeric($redeemedValue)) {
            throw new InvalidArgumentException(sprintf(
                '%s is not a valid Voucher Value.', $redeemedValue
            ));
        }

        $this->getById($voucherId);

        $this->db->perform('INSERT INTO voucher_pos 
        (voucher_id, project_id, redeemed, redeemed_value, doctype, doctype_id, position_id, created_by) VALUES 
        (:voucher_id, :project_id, :redeemed,:reedemed_value, :doctype,:doctype_id, :position_id, :created_by)',
            [
                'voucher_id'     => (int)$voucherId,
                'project_id'     => (int)$projectId,
                'redeemed'       => (int)$reedemed,
                'reedemed_value' => abs((float)$redeemedValue),
                'doctype'        => (String)$doctype,
                'doctype_id'     => (int)$doctypeId,
                'position_id'    => (int)$positionId,
                'created_by'     => (String)$username,
            ]
        );
    }

    /**
     * @param      $voucherOrderId
     * @param bool $with_position
     *
     * @return bool true if successful
     */
    public function deleteVoucherFromOrder($voucherOrderId, $with_position = true)
    {
        try {
            $voucherOrder = $this->getVoucherOrderById($voucherOrderId);
            $orderPositionId = $this->getVoucherPositionIdFromOrder($voucherOrder['order_id'],
                $voucherOrder['voucher_id']);
            if (!$orderPositionId) {
                return false;
            }

            if ($with_position) {
                /** @deprecated-block-start */
                $doctype = $voucherOrder['doctype'] === 'rechnung' ? 'rechnung' : 'auftrag';
                $doctypePosition = $doctype . '_position';
                $this->app->YUI->SortListEvent('del', $doctypePosition, $doctype, $voucherOrder['order_id'],
                    $orderPositionId);
                /** @deprecated-block-end */
            }

            $affectedRows = $this->db->fetchAffected(
                'UPDATE voucher SET voucher_residual_value = voucher_residual_value - :voucher_residual_value WHERE id = :id',
                ['id' => (int)$voucherOrder['voucher_id'], 'voucher_residual_value' => $voucherOrder['voucher_value']]
            );
            if ($affectedRows === 1) {
                $this->db->perform('DELETE FROM voucher_order WHERE id = :id', ['id' => $voucherOrder['id']]);
                $this->recalculateResidualValue($voucherOrder['voucher_id']);

                return true;
            }

        } catch (Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * @param string      $code
     * @param int         $articleId
     * @param float       $voucherValue
     * @param int         $invoiceId
     * @param int         $invoicePositionId
     * @param string      $taxName
     * @param string      $currency
     * @param string|null $validFrom
     * @param string|null $validTo
     * @param bool        $showCodeOnDocument
     * @param bool        $onlyForCustomer
     * @param int         $addressId
     *
     * @return int voucher_id
     */
    public function create(
        $code,
        $articleId,
        $voucherValue,
        $invoiceId,
        $invoicePositionId,
        $taxName,
        $currency = 'EUR',
        $validFrom = null,
        $validTo = null,
        $showCodeOnDocument = true,
        $onlyForCustomer = false,
        $addressId = 0
    ) {
        try {
            $voucher = $this->getVoucherByCode($code);
            if (!empty($voucher)) {
                throw new RuntimeException('Voucher with code allready exists');
            }

            if ($taxName !== 'normal' && $taxName !== 'ermaessigt') {
                $taxName = 'befreit';
            }

            $this->db->perform('INSERT INTO voucher 
            (voucher_date, article_id, voucher_code, voucher_original_value,voucher_residual_value, 
            valid_from,valid_to, invoice_id, invoice_position_id, tax_name,currency,show_code_on_document, only_for_customer,address_id) 
            VALUES (now(), :article_id ,:voucher_code, :voucher_value, :voucher_value, 
            :valid_from, :valid_to, :invoice_id, :invoice_position_id , :tax_name, :currency, :show_code_on_document,:only_for_customer,:address_id)',
                [
                    'article_id'            => $articleId,
                    'voucher_code'          => $code,
                    'voucher_value'         => (float)$voucherValue,
                    'valid_from'            => $validFrom,
                    'valid_to'              => $validTo,
                    'invoice_id'            => $invoiceId,
                    'invoice_position_id'   => $invoicePositionId,
                    'tax_name'              => (String)$taxName,
                    'currency'              => !empty($currency) ? $currency : 'EUR',
                    'show_code_on_document' => $showCodeOnDocument ? 1 : 0,
                    'only_for_customer'     => $onlyForCustomer ? 1 : 0,
                    'address_id'            => (int)$addressId,
                ]
            );

            return (int)$this->db->lastInsertId();
        } catch (Exception $e) {
            throw new RuntimeException('Could not create Voucher' . $e->getMessage());
        }
    }

    /**
     * @param int $voucherId
     *
     * @return bool
     */
    public function delete($voucherId)
    {
        $voucher_orders = $this->getVoucherOrdersFromVoucher($voucherId, 1);
        if (empty($voucher_orders)) {
            $affectedRows = $this->db->fetchAffected('DELETE FROM voucher WHERE id = :id', ['id' => $voucherId]);

            return $affectedRows === 1;
        }
        throw new RuntimeException('Voucher is allready associated with order(s)');
    }

    /**
     * @param int $voucherId
     */
    public function recalculateResidualValue($voucherId)
    {
        $residualValue = $this->getResidualValueFromVoucherId($voucherId);
        $this->db->perform('UPDATE voucher SET voucher_residual_value = :voucher_residual_value WHERE id = :id'
            , ['voucher_residual_value' => $residualValue, 'id' => $voucherId]);
    }

    /**
     * @param array $voucher
     * @param int   $projectId
     * @param float $value
     *
     * @return array
     */
    public function getArticleForPos($voucher, $projectId, $value)
    {
        $sql = '
      SELECT 
        art.id, 
        art.nummer, 
        art.projekt, 
        art.name_de AS artikel, 
        art.kurztext_de, 
        art.umsatzsteuer,
        art.rabatt AS rabattartikel,
        art.rabatt_prozent,
        art.anabregs_text,
        IF(art.seriennummern=\'keine\',\'\',IFNULL(art.seriennummern,\'\')) AS seriennummern,
        art.porto,
        ifnull(art.keinrabatterlaubt,0) AS keinrabatterlaubt
      FROM 
        artikel AS art
      WHERE 
        art.id = :article_id
      ';

        $row = $this->db->fetchRow($sql, [
            'article_id' => $voucher['article_id'],
        ]);

        $row['anabregs_text'] = $voucher['voucher_code'];
        $row['keinrabatterlaubt'] = 1;
        $row['rabatt_prozent'] = 0;
        $row['rabatt'] = 0;

        $erloes = '';
        $steuersatz = null;
        $steuertext = null;
        $umsatzsteuerpos = null;
        $this->app->erp->GetArtikelSteuer(
            $voucher['article_id'],
            0,
            false,
            $steuersatz,
            $steuertext,
            $erloes,
            $umsatzsteuerpos,
            null,
            $projectId
        );
        $row['erloes'] = $erloes;

        $row['umsatzsteuer'] = !empty($voucher['tax_name']) ? $voucher['tax_name'] : 'normal';
        if ($row['umsatzsteuer'] === 'befreit') {
            $row['tax'] = '0%';
        } elseif ($row['umsatzsteuer'] === 'ermaessigt') {
            $row['tax'] = round($this->app->erp->GetStandardSteuersatzErmaessigt($projectId), 2) . '%';
        } else {
            $row['tax'] = round($this->app->erp->GetStandardSteuersatzNormal($projectId), 2) . '%';
        }
        $row['preis'] = -$value;

        return $row;
    }

    /**
     * @return array
     */
    public function getValidTypes()
    {
        return $this->validtypes;
    }

    /**
     * @param string $validType
     *
     * @return string|null
     */
    public function getValidToFromValidType($validType)
    {
        try {
            $date = new DateTime(date('Y-m-d'));
            if (strpos($validType, 'ThisYear') === 0) {
                $date = new DateTime(date('Y-12-31'));
                $plusYears = substr($validType, 8);
                if (empty($plusYears)) {
                    return $date->format('Y-m-d');
                }
                if (strlen($plusYears) >= 5 && strpos($plusYears, 'Plus') === 0) {
                    $date->add(new DateInterval('P' . substr($plusYears, 4) . 'Y'));

                    return $date->format('Y-m-d');
                }
            } elseif (strlen($validType) > 4 && strpos($validType, 'Year') === 0) {
                $plusYears = (int)substr($validType, 4);
                if ($plusYears <= 0) {
                    return null;
                }
                $date->add(new DateInterval('P' . $plusYears . 'Y'));

                return $date->format('Y-m-d');
            } elseif (strlen($validType) > 5 && strpos($validType, 'Month') === 0) {
                $plusMonth = (int)substr($validType, 5);
                if ($plusMonth <= 0) {
                    return null;
                }
                $date->add(new DateInterval('P' . $plusMonth . 'M'));

                return $date->format('Y-m-d');
            }
        } catch (Exception $e) {

        }

        return null;
    }

    /**
     * @param string $columnname
     *
     * @return string
     */
    public function getValidTypeTranslationSql($columnname = 'vt.valid')
    {
        $sql = '';
        foreach ($this->validtypes as $validkey => $validDescriotion) {
            $sql .= sprintf('if(%s = \'%s\',\'%s\',', $columnname, $validkey, $validDescriotion);
        }
        $sql .= '\'\'' . str_repeat(')', count($this->validtypes));

        return $sql;
    }

    /**
     * @param int $voucherId
     *
     * @return string
     */
    public function getCodeForDocumentByVoucherId($voucherId)
    {
        $voucher = $this->getById($voucherId);

        return (int)$voucher['show_code_on_document'] === 1 ? $voucher['voucher_code'] : '';
    }

    /**
     * @param int $invoiceId
     * @param int $positionId
     *
     * @return array
     */
    public function findCodesForDocumentByInvoiceIdAndPositionId(int $invoiceId, int $positionId): array
    {
        $sql =
            'SELECT v.voucher_code
            FROM `voucher` AS `v`
            WHERE v.invoice_position_id = :invoice_position_id
            AND v.invoice_id = :invoice_id
            AND v.show_code_on_document = 1';

        $results = $this->db->fetchAll(
            $sql,
            [
                'invoice_position_id' => $positionId,
                'invoice_id'          => $invoiceId,
            ]
        );
        $codes = [];
        if (!empty($results)) {
            foreach ($results as $result) {
                $codes[] = $result['voucher_code'];
            }
        }

        return $codes;
    }

    /**
     * @param int $invoiceId
     *
     * @return array
     */
    public function getVouchersByInvoiceId($invoiceId)
    {
        return $this->db->fetchAll(
            'SELECT v.* FROM voucher AS v WHERE v.invoice_id = :invoice_id',
            ['invoice_id' => $invoiceId]
        );
    }

    /**
     * @param string $date
     * @param int    $projectId
     *
     * @return array
     */
    public function getPosVouchersByDate($date, $projectId = 0)
    {
        return $this->db->fetchAll(
            'SELECT v.* 
            FROM voucher AS v 
            INNER JOIN rechnung AS i ON v.invoice_id = i.id
            INNER JOIN pos_order AS po ON i.id = po.rechnung
            WHERE DATE(po.zeitstempel) = :date AND (po.projekt = :project_id OR 0 = :project_id)',
            ['date' => $date, 'project_id' => $projectId]
        );
    }

    /**
     * @param string $date
     * @param int    $projectId
     * @param string $status
     * @param string $paymentType
     *
     * @return array
     */
    public function getPosVoucherSumByDateTax($date, $projectId = 0, $status = '', $paymentType = '')
    {
        return $this->db->fetchAll(
            'SELECT SUM(
                IF(
                    ip.umsatz_brutto_gesamt <> 0,
                    ip.umsatz_brutto_gesamt,
                    ip.menge*ip.preis*(1-ip.rabatt / 100) 
                        * IF(
                            i.ust_befreit > 1 OR (i.ust_befreit = 1 AND i.ustid != \'\') 
                            OR ip.umsatzsteuer = \'befreit\' OR ip.steuersatz = 0,
                            0, IF(ip.steuersatz > 0,ip.steuersatz,IF(ip.umsatzsteuer = \'ermaessigt\', 
                                i.steuersatz_ermaessigt, i.steuersatz_normal))
                    )/ 100
                )
            ) AS amount, 
            IF(
                i.ust_befreit > 1 OR (i.ust_befreit = 1 AND i.ustid != \'\') 
                    OR ip.umsatzsteuer = \'befreit\' OR ip.steuersatz = 0,
                0, IF(ip.steuersatz > 0,ip.steuersatz,IF(ip.umsatzsteuer = \'ermaessigt\',
                    i.steuersatz_ermaessigt, i.steuersatz_normal))
            ) AS tax
            FROM voucher AS v 
            INNER JOIN rechnung AS i ON v.invoice_id = i.id AND (i.status = :status OR :status = \'\') 
                                            AND (i.zahlungsweise = :payment OR :payment = \'\')
            INNER JOIN rechnung_position AS ip ON i.id = ip.rechnung AND v.article_id = ip.artikel
            INNER JOIN pos_order AS po ON i.id = po.rechnung
            WHERE DATE(po.zeitstempel) = :date AND (po.projekt = :project_id OR 0 = :project_id)
            GROUP BY tax
            ORDER BY tax',
            ['date' => $date, 'project_id' => $projectId, 'status' => (String)$status, 'payment' => $paymentType]
        );
    }


    /**
     * @param string $date
     * @param int    $projectId
     * @param string $status
     *
     * @return float
     */
    public function getPosVoucherSumByDate($date, $projectId = 0, $status = '')
    {
        return (float)$this->db->fetchValue(
            "SELECT SUM(v.voucher_original_value) 
            FROM voucher AS v 
            INNER JOIN rechnung AS i ON v.invoice_id = i.id AND (i.status = :status OR :status = '')
            INNER JOIN pos_order AS po ON i.id = po.rechnung
            WHERE DATE(po.zeitstempel) = :date AND (po.projekt = :project_id OR 0 = :project_id)",
            ['date' => $date, 'project_id' => $projectId, 'status' => (String)$status]
        );
    }

    /**
     * @param string $date
     * @param int    $projectId
     * @param string $status
     *
     * @return float
     */
    public function getReedemedPosSumByDate($date, $projectId = 0, $status = '')
    {
        return (float)$this->db->fetchValue(
            "SELECT SUM(vo.voucher_value) 
            FROM pos_order AS po
            INNER JOIN auftrag AS o ON po.auftrag = o.id
            INNER JOIN rechnung AS i ON o.id = i.auftragid AND po.rechnung = i.id AND (i.status = :status OR :status = '')
            INNER JOIN voucher_order AS vo 
                ON (o.id = vo.order_id AND vo.doctype <> 'rechnung' OR i.id = vo.voucher_id AND vo.doctype = 'rechnung')
            WHERE DATE(po.zeitstempel) = :date AND (po.projekt = :project_id OR 0 = :project_id)",
            ['date' => $date, 'project_id' => $projectId, 'status' => (String)$status]
        );
    }

    /**
     * @param int   $invoiceId
     * @param array $positionArr
     *
     * @return array
     */
    public function createVoucherFromInvoicePositions($invoiceId, $positionArr)
    {
        if (empty($invoiceId) || empty($positionArr)) {
            return $positionArr;
        }

        $positionIdKeyArr = [];
        foreach ($positionArr as $row) {
            $positionIdKeyArr[$row['position_id']] = $row;
        }

        $neededVouchers = $this->getNeededVouchersFromInvoice($invoiceId, $positionIdKeyArr);

        if (empty($neededVouchers)) {
            return $positionArr;
        }

        foreach ($neededVouchers as $neededVoucherKey => $neededVoucher) {
            $template = $this->getTemplateFromId($neededVoucher['template_id']);
            for ($i = 1; $i <= $neededVoucher['amount']; $i++) {
                $neededVouchers[$neededVoucherKey]['voucherId'] = $this->createVoucherFromTemplate($invoiceId,
                    $neededVoucher['position_id'], $neededVoucher['price_gross'], $neededVoucher['currency'],
                    $template);
            }
        }

        return $neededVouchers;
    }

    /**
     * @param int $templateId
     *
     * @return array
     */
    public function getTemplateFromId($templateId)
    {
        $sql = 'SELECT * FROM voucher_template WHERE id = :id';

        $ret = $this->db->fetchRow($sql, ['id' => $templateId]);
        if ($ret['tax_name'] !== 'ermaessigt' && $ret['tax_name'] !== 'normal') {
            $ret['tax_name'] = 'befreit';
        }

        return $ret;
    }

    /**
     * @param int    $invoiceId
     * @param int    $invoicePositionId
     * @param float  $price
     * @param string $currency
     * @param array  $template
     *
     * @return int
     */
    public function createVoucherFromTemplate($invoiceId, $invoicePositionId, $price, $currency, $template)
    {
        if (empty($invoiceId) || empty($price) || empty($template)) {
            return 0;
        }
        $code = $this->getNewVoucherCodeFromPattern($template['code_pattern']);
        if (empty($code)) {
            return 0;
        }
        $validFrom = null;
        $validTo = $this->getValidToFromValidType($template['valid']);
        if ($validTo !== null) {
            $validFrom = date('Y-m-d');
        }
        $addressId = $this->db->fetchValue(
            'SELECT adresse FROM rechnung WHERE id = :invoice_id LIMIT 1',
            ['invoice_id' => (int)$invoiceId]
        );

        return $this->create($code, $template['article_id'], $price, $invoiceId, $invoicePositionId,
            $template['tax_name'], $currency, $validFrom, $validTo, $template['show_code_on_document'] ? true : false,
            $template['only_for_customer'] ? true : false, $addressId);
    }

    /**
     * @param int   $invoiceId
     * @param array $articleAmounts
     *
     * @return array
     */
    public function getNeededVouchersFromInvoice($invoiceId, $invoicePosData)
    {
        $neededVouchers = [];
        if (empty($invoiceId) || empty($invoicePosData)) {
            return [];
        }

        //amount_diff = invoice amount - amount of voucher codes for invoice position
        $sql =
            'SELECT 
                rp.id AS `position_id`,
                rp.artikel AS `article_id`, 
                rp.menge - IFNULL(COUNT(v.id),0) AS `amount_diff`,
                vt.id AS `template_id`
            FROM `rechnung_position` AS `rp` 
            LEFT JOIN `voucher` AS `v` ON v.invoice_position_id = rp.id
            INNER JOIN `voucher_template` AS `vt` ON vt.article_id = rp.artikel
            WHERE rp.rechnung = :invoice_id
            GROUP BY rp.id';

        $voucherData = $this->db->fetchAll($sql, ['invoice_id' => $invoiceId]);

        if (empty($voucherData)) {
            return [];
        }
        foreach ($voucherData as $data) {
            $positionId = $data['position_id'];
            $priceGross = $invoicePosData[$positionId]['price_gross'];
            $taxName = $invoicePosData[$positionId]['tax_name'];
            if ($data['amount_diff'] > 0) {
                $neededVouchers[] = [
                    'template_id' => $data['template_id'],
                    'price_gross' => $priceGross,
                    'amount'      => $data['amount_diff'],
                    'tax_name'    => $taxName,
                    'position_id' => $positionId,
                ];
            }
        }

        return $neededVouchers;
    }

    /**
     * @param int    $count
     * @param string $type
     *
     * @return null|string
     */
    private function genCode($count, $type)
    {
        if ($count <= 0) {
            return null;
        }
        $code = '';
        if ($type === 'num') {
            $chararr = $this->digits;
        } elseif ($type === 'anum') {
            $chararr = array_merge($this->digits, $this->alphas);
        } elseif ($type === 'alpha') {
            $chararr = $this->alphas;
        } else {
            return null;
        }
        $upper = count($chararr) - 1;
        for ($i = 0; $i < $count; $i++) {
            $chr = $chararr[mt_rand(0, $upper)];
            while ($i === 0 && $chr === '0') {
                $chr = $chararr[mt_rand(0, $upper)];
            }
            $code .= $chr;
        }

        return $code;
    }

    /**
     * @param string $pattern
     *
     * @return string|null
     */
    public function getNewVoucherCodeFromPattern($pattern)
    {
        $numcount = 0;
        $subpattern = '';
        $patterns = ['num', 'anum', 'alpha'];
        foreach ($patterns as $patterntype) {
            $plen = strlen($patterntype);
            if (strpos($pattern, $patterntype) === 0) {
                $numcount = (int)substr($pattern, $plen);
                $subpattern = $patterntype;
                break;
            }
        }

        if ($numcount <= 0) {
            return null;
        }

        $code = $this->genCode($numcount, $subpattern);
        if ($code === null) {
            return null;
        }

        while (
        $this->db->fetchValue('
                SELECT id 
                FROM voucher 
                WHERE voucher_code = :code',
            ['code' => $code]
        )
        ) {
            $code = $this->genCode($numcount, $subpattern);
        }

        return $code;
    }
}
