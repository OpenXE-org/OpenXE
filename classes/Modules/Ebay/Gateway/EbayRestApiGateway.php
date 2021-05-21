<?php

declare(strict_types=1);

namespace Xentral\Modules\Ebay\Gateway;

use Xentral\Components\Database\Database;
use Xentral\Modules\Ebay\Client\EbayRestApiClient;
use Xentral\Modules\Ebay\Data\AccountCredentialsData;
use Xentral\Modules\Ebay\Data\TokenData;
use Xentral\Modules\Ebay\Exception\ValueNotFoundException;

class EbayRestApiGateway
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
     * @param int $shopexportId
     *
     * @throws ValueNotFoundException
     *
     * @return AccountCredentialsData
     */
    public function getAccountCredentials(int $shopexportId): AccountCredentialsData
    {
        $settings = $this->getShopSettings($shopexportId);

        return new AccountCredentialsData(
            (string)$settings['felder']['appID'],
            (string)$settings['felder']['certID'],
            (string)$settings['felder']['ruName']
        );
    }

    protected function getShopSettings(int $shopexportId): array
    {
        $sql = 'SELECT `einstellungen_json` FROM `shopexport` WHERE `id` = :shopexport_id';
        $values = [
            'shopexport_id' => $shopexportId,
        ];
        $encodedSettings = $this->db->fetchValue($sql, $values);

        if (empty($encodedSettings)) {
            throw new ValueNotFoundException('No settings were found for given shopexport Id: ' . $shopexportId);
        }

        return json_decode($encodedSettings, true);
    }

    public function useRestApiForOrderImport(int $shopexportId): bool
    {
        $settings = $this->getShopSettings($shopexportId);

        $userEnabledSetting = (bool)$settings['felder']['useRestApiOnOrderImport'];
        $restApiTokenExists = $this->tryGetRestApiAccessTokenFromDatabase(
                $shopexportId,
                EbayRestApiClient::TOKEN_TYPE_USER
            ) !== null;

        return $userEnabledSetting && $restApiTokenExists;
    }

    /**
     * @param int    $shopexportId
     * @param string $type
     *
     * @return TokenData|null
     */
    public function tryGetRestApiAccessTokenFromDatabase(int $shopexportId, string $type): ?TokenData
    {
        $sql = "SELECT e.token, e.type, e.refresh_token, (e.valid_until > NOW()) AS valid
                FROM `ebay_rest_token` AS `e` 
                WHERE e.shopexport_id = :shopexport_id 
                  AND e.type = :type
                LIMIT 1";
        $values = [
            'shopexport_id' => $shopexportId,
            'type'          => $type,
        ];
        $data = $this->db->fetchAll($sql, $values);

        $data = reset($data);
        if (empty($data['token'])) {
            return null;
        }

        return new TokenData(
            $data['token'],
            $data['refresh_token'],
            $data['type'],
            (bool)$data['valid']
        );
    }

    /**
     * @param int $shopexportId
     *
     * @throws ValueNotFoundException
     *
     * @return int
     */
    public function getSiteId(int $shopexportId): int
    {
        $sql = 'SELECT `einstellungen_json` FROM `shopexport` WHERE `id` = :shopexport_id';
        $values = [
            'shopexport_id' => $shopexportId,
        ];
        $encodedSettings = $this->db->fetchValue($sql, $values);

        if (empty($encodedSettings)) {
            throw new ValueNotFoundException('No settings were found for given shopexport Id: ' . $shopexportId);
        }
        $settings = json_decode($encodedSettings, true);

        if (empty($settings['felder']['siteID'])) {
            throw new ValueNotFoundException('Site Id value missing for given shopexport Id:' . $shopexportId);
        }

        return (int)$settings['felder']['siteID'];
    }

    public function existsRestOrderInDatabase(string $orderId): bool
    {
        $sql = 'SELECT `id` FROM `ebay_rest_orders` WHERE `order_id` = :order_id';
        $values = [
            'order_id' => $orderId,
        ];

        return $this->db->fetchValue($sql, $values) > 0;
    }

    public function countRestOrdersToImport(int $shopexportId): int
    {
        $sql = 'SELECT COUNT(id) FROM `ebay_rest_orders` WHERE `processed` = 0 AND `shopexport_id` = :shopexport_id';
        $values = [
            'shopexport_id' => $shopexportId,
        ];

        return (int)$this->db->fetchValue($sql, $values);
    }

    public function getNextOrderToImport(int $shopexportId): string
    {
        $sql = 'SELECT `order_data` FROM `ebay_rest_orders` 
            WHERE `processed` = 0 AND `shopexport_id` = :shopexport_id 
            ORDER BY `date_of_order` ASC
            LIMIT 1';
        $values = [
            'shopexport_id' => $shopexportId,
        ];

        return (string)$this->db->fetchValue($sql, $values);
    }
}
