<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Service;

use DateTime;
use Xentral\Components\Database\Database;
use Xentral\Modules\Ebay\Data\TokenData;
use Xentral\Modules\Ebay\Exception\InvalidArgumentException;

class EbayRestApiService
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param int   $shopexportId
     * @param array $ebayResponse
     */
    public function saveRestApiAccessToken(int $shopexportId, array $ebayResponse): void
    {
        if (empty($shopexportId)) {
            throw new InvalidArgumentException('Value for shopexport Id must not be empty');
        }
        if (empty($ebayResponse)) {
            throw new InvalidArgumentException('eBay Response was empty');
        }
        $token = $ebayResponse['access_token'];
        $refreshToken = $ebayResponse['refresh_token'];
        $expiresInMinutes = $ebayResponse['expires_in'] / 60 - 5;
        $validUntil = new DateTime();
        $validUntil->modify(sprintf('+%d minutes', $expiresInMinutes));

        if (empty($token)) {
            throw new InvalidArgumentException('Value for token must not be empty');
        }

        $this->deleteRedundantToken($shopexportId, $ebayResponse['token_type']);

        $query = sprintf(
            'INSERT INTO `ebay_rest_token` (`shopexport_id`, `token`, `refresh_token`,`type`, `scope`, `valid_until`)
                    VALUES (%d, \'%s\', \'%s\', \'%s\', \'%s\',\'%s\')',
            $shopexportId,
            $token,
            $refreshToken,
            $ebayResponse['token_type'],
            '',
            $validUntil->format('Y-m-d H:i:s')

        );
        $this->db->exec($query);
    }

    public function deleteRedundantToken(int $shopexportId, string $type): void
    {
        $sql = 'DELETE FROM `ebay_rest_token` WHERE shopexport_id = :shopexport_id AND type = :type';
        $values = [
            'shopexport_id' => $shopexportId,
            'type'          => $type,
        ];
        $this->db->perform($sql, $values);
    }

    public function renewToken(int $shopexportId, int $expiresInSeconds, TokenData $tokenData): void
    {
        $expiresInMinutes = $expiresInSeconds / 60 - 5; // reduce the actual valid time by 5 minutes to avoid a very specific edge case in which the token runs out the second it gets used
        $validUntil = new DateTime();
        $validUntil->modify(sprintf('+%d minutes', $expiresInMinutes));

        $sql = 'UPDATE `ebay_rest_token` SET `token` = :token, `valid_until` = :valid_until
                        WHERE `shopexport_id` = :shopexport_id AND `type` = :type';
        $values = [
            'token'         => $tokenData->getToken(),
            'valid_until'   => $validUntil->format('Y-m-d H:i:s'),
            'shopexport_id' => $shopexportId,
            'type'          => $tokenData->getType(),
        ];
        $this->db->perform($sql, $values);
    }

    public function saveRestOrder(int $shopexportId, DateTime $orderDate, string $orderId, string $orderData): void
    {
        $sql = 'INSERT INTO `ebay_rest_orders` (`date_of_order`, `order_data`, `shopexport_id`, `order_id`, `processed`) 
                VALUES (:date_of_order, :order_data, :shopexport_id, :order_id, 0)';
        $values = [
            'date_of_order' => $orderDate->format('Y-m-d H:i:s'),
            'order_data'    => $orderData,
            'shopexport_id' => $shopexportId,
            'order_id'      => $orderId,
        ];
        $this->db->perform($sql, $values);
    }

    public function setRestOrderToProcessed(string $orderId): void
    {
        $sql = 'UPDATE `ebay_rest_orders` SET `processed` = 1
                        WHERE `order_id` = :order_id';
        $values = [
            'order_id' => $orderId,
        ];
        $this->db->perform($sql, $values);
    }

    public function deleteRestOrderFromDatabase(string $orderId): void
    {
        $sql = 'DELETE FROM `ebay_rest_orders` WHERE `order_id` = :order_id';
        $values = [
            'order_id' => $orderId,
        ];
        $this->db->perform($sql, $values);
    }
}
