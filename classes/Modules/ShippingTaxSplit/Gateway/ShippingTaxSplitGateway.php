<?php

namespace Xentral\Modules\ShippingTaxSplit\Gateway;

use Xentral\Components\Database\Database;

final class ShippingTaxSplitGateway
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
     * @param int $orderId
     *
     * @return float
     */
    public function getShippingAmountWithoutTaxByOrderId($orderId)
    {
        return (float)$this->db->fetchValue(
            'SELECT 
              IFNULL(
                SUM(
                  ap.menge * ap.preis * (1-ap.rabatt / 100) 
                )
                ,0
              ) 
              FROM auftrag_position ap 
              INNER JOIN auftrag AS a ON ap.auftrag = a.id
              INNER JOIN artikel art ON ap.artikel = art.id AND art.porto = 1 
              WHERE a.id = :order_id',
            ['order_id' => $orderId]
        );
    }

    /**
     * @param int $orderId
     *
     * @return float
     */
    public function getShippingAmountByOrderId($orderId)
    {
        return (float)$this->db->fetchValue(
            'SELECT 
              IFNULL(
                SUM(
                  ap.menge * ap.preis * (1-ap.rabatt / 100) * 
                  (
                    1+
                    IF(
                      IFNULL(ap.steuersatz,-1) >= 0, 
                      ap.steuersatz, 
                      IF(ap.umsatzsteuer = \'befreit\',
                        1,
                        IF(ap.umsatzsteuer = \'ermaessigt\', 
                          a.steuersatz_ermaessigt,
                          IF(ap.umsatzsteuer <> \'\',a.steuersatz_normal,
                            IF(art.umsatzsteuer = \'befreit\', 0, 
                              IF(art.umsatzsteuer = \'ermaessigt\', 
                                a.steuersatz_normal, 
                                a.steuersatz_normal
                              )
                            ) 
                          )
                        )
                      )
                    )/ 100
                  )
                )
                ,0
              ) 
            FROM auftrag_position ap 
            INNER JOIN auftrag AS a ON ap.auftrag = a.id
            INNER JOIN artikel art ON ap.artikel = art.id AND art.porto = 1 
            WHERE a.id = :order_id',
            ['order_id' => $orderId]
        );
    }

    /**
     * @param int $orderId
     *
     * @return array
     */
    public function getShippingPositionsWithoutTaxByOrderId($orderId)
    {
        return $this->db->fetchAll(
            'SELECT artikel AS article_id,ap.menge AS amount, 
                ap.id AS order_position_id, ap.preis AS net_price, ap.rabatt AS discount,
                ap.menge * ap.preis * (1-ap.rabatt / 100) AS net, 0 AS tax,
                ap.menge * ap.preis * (1-ap.rabatt / 100) AS gross 
            FROM auftrag_position ap 
            INNER JOIN auftrag AS a ON ap.auftrag = a.id
            INNER JOIN artikel art ON ap.artikel = art.id AND art.porto = 1 
            WHERE a.id = :order_id',
            ['order_id' => $orderId]
        );
    }

    /**
     * @param int $orderId
     *
     * @return array
     */
    public function getShippingPositionsByOrderId($orderId)
    {
        return $this->db->fetchAll(
            'SELECT artikel AS article_id,ap.menge AS amount, 
            ap.id AS order_position_id, ap.preis AS net_price, ap.rabatt AS discount,
            ap.menge * ap.preis * (1-ap.rabatt / 100) AS net, IF(
                IFNULL(ap.steuersatz,-1) >= 0, 
                ap.steuersatz, 
                IF(ap.umsatzsteuer = \'befreit\',
                   0,
                   IF(ap.umsatzsteuer = \'ermaessigt\', 
                     a.steuersatz_ermaessigt,
                     IF(ap.umsatzsteuer <> \'\',a.steuersatz_normal,
                       IF(art.umsatzsteuer = \'befreit\', 0, 
                         IF(art.umsatzsteuer = \'ermaessigt\', 
                           a.steuersatz_normal, 
                           a.steuersatz_normal
                         )
                       ) 
                     )
                   )
                )
            ) AS tax,
            ap.menge * ap.preis * (1-ap.rabatt / 100) * 
            (1+
            IF(
                IFNULL(ap.steuersatz,-1) >= 0, 
                ap.steuersatz, 
                IF(ap.umsatzsteuer = \'befreit\',
                    1,
                    IF(ap.umsatzsteuer = \'ermaessigt\', 
                        a.steuersatz_ermaessigt,
                        IF(ap.umsatzsteuer <> \'\',a.steuersatz_normal,
                            IF(art.umsatzsteuer = \'befreit\', 0, 
                                IF(art.umsatzsteuer = \'ermaessigt\', 
                                    a.steuersatz_normal, 
                                    a.steuersatz_normal
                                )
                            ) 
                        )
                    )
                )
            )/ 100
            ) AS gross 
            FROM auftrag_position ap 
            INNER JOIN auftrag AS a ON ap.auftrag = a.id
            INNER JOIN artikel art ON ap.artikel = art.id AND art.porto = 1 
            WHERE a.id = :order_id',
            ['order_id' => $orderId]
        );
    }

    /**
     * @param int $orderId
     *
     * @return array
     */
    public function getNonShippingPositionsWithoutTaxByOrderId($orderId)
    {
        return $this->db->fetchAll(
            'SELECT artikel AS article_id,ap.menge AS amount, ap.waehrung AS currency,
            ap.id AS order_position_id, ap.preis AS net_price, ap.rabatt AS discount,
            ap.menge * ap.preis * (1-ap.rabatt / 100) AS net, 0 AS tax,
            ap.menge * ap.preis * (1-ap.rabatt / 100) AS gross, a.steuersatz_normal AS tax_normal, a.steuersatz_ermaessigt AS tax_reduced
            FROM auftrag_position ap 
            INNER JOIN auftrag AS a ON ap.auftrag = a.id
            INNER JOIN artikel art ON ap.artikel = art.id AND art.porto = 0 
            WHERE a.id = :order_id',
            ['order_id' => $orderId]
        );
    }

    /**
     * @param int $orderId
     *
     * @return array
     */
    public function getNonShippingPositionsByOrderId($orderId)
    {
        return $this->db->fetchAll(
            'SELECT artikel AS article_id, ap.waehrung AS currency, ap.menge AS amount, 
       ap.id AS order_position_id, ap.preis AS net_price, ap.rabatt AS discount,
       ap.menge * ap.preis * (1-ap.rabatt / 100) AS net, IF(
         IFNULL(ap.steuersatz,-1) >= 0, 
         ap.steuersatz, 
         IF(ap.umsatzsteuer = \'befreit\',
           0,
           IF(ap.umsatzsteuer = \'ermaessigt\', 
             a.steuersatz_ermaessigt,
             IF(ap.umsatzsteuer <> \'\',a.steuersatz_normal,
               IF(art.umsatzsteuer = \'befreit\', 0, 
                 IF(art.umsatzsteuer = \'ermaessigt\', 
                   a.steuersatz_normal, 
                   a.steuersatz_normal
                 )
               ) 
             )
           )
         )
        ) AS tax,
       ap.menge * ap.preis * (1-ap.rabatt / 100) * 
       (1+
       IF(
         IFNULL(ap.steuersatz,-1) >= 0, 
         ap.steuersatz, 
         IF(ap.umsatzsteuer = \'befreit\',
           1,
           IF(ap.umsatzsteuer = \'ermaessigt\', 
             a.steuersatz_ermaessigt,
             IF(ap.umsatzsteuer <> \'\',a.steuersatz_normal,
               IF(art.umsatzsteuer = \'befreit\', 0, 
                 IF(art.umsatzsteuer = \'ermaessigt\', 
                   a.steuersatz_normal, 
                   a.steuersatz_normal
                 )
               ) 
             )
           )
         )
        )/ 100
      ) AS gross, a.steuersatz_normal AS tax_normal, a.steuersatz_ermaessigt AS tax_reduced
      FROM auftrag_position ap 
      INNER JOIN auftrag AS a ON ap.auftrag = a.id
      INNER JOIN artikel art ON ap.artikel = art.id AND art.porto = 0 
      WHERE a.id = :order_id',
            ['order_id' => $orderId]
        );
    }

}