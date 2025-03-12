<?php
/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\CrossSelling;

use Xentral\Components\Database\Database;
use Xentral\Modules\CrossSelling\Data\CrossSellingArticle;
use Xentral\Modules\CrossSelling\Data\CrossSellingType;

final class CrossSellingGateway
{
  /**
   * @param Database $db
   */
  public function __construct(private readonly Database $db)
  {
  }

  public function Get(int $id) : CrossSellingArticle {
    $sql = 'SELECT * FROM crossselling_artikel WHERE id = :id';
    $res = $this->db->fetchRow($sql, ['id' => $id]);
    return new CrossSellingArticle(
        CrossSellingType::from($res['art']),
        $res['artikel'],
        $res['crosssellingartikel'],
        $res['aktiv'],
        $res['gegenseitigzuweisen'],
        $res['shop'],
        $res['sort'],
        $res['bemerkung'],
        $res['id']
    );
  }

  public function Insert(CrossSellingArticle $obj) : CrossSellingArticle {
    $sql = "INSERT INTO crossselling_artikel
            (art, artikel, crosssellingartikel, shop, sort, aktiv, gegenseitigzuweisen, bemerkung)
            VALUES 
            (:type, :mainArticleId, :connectedArticleId, :shopId, :sort, :active, :bidirectional, '')";
    $this->db->perform($sql, (array)$obj);
    $obj->id = $this->db->lastInsertId();
    return $obj;
  }

  public function Update(CrossSellingArticle $obj) : CrossSellingArticle {
    $sql = 'UPDATE crossselling_artikel
            SET art = :type, artikel = :mainArticleId, crosssellingartikel = :connectedArticleId, shop = :shopId,
                sort = :sort, aktiv = :active, gegenseitigzuweisen = :bidirectional
            WHERE id = :id';
    $this->db->perform($sql, (array)$obj);
    return $obj;
  }

  public function Delete(int $id) : void {
    $sql = 'DELETE FROM crossselling_artikel WHERE id = :id';
    $this->db->perform($sql, ['id' => $id]);
  }

  public function Exists(CrossSellingArticle $obj) : bool {
      $sql = 'SELECT 1 FROM crossselling_artikel WHERE art = :type 
              AND (
                  (artikel = :mainArticleId AND crosssellingartikel = :connectedArticleId) 
                  OR (artikel = :connectedArticleId AND crosssellingartikel = :mainArticleId AND (gegenseitigzuweisen = 1 OR :bidirectional))
                  )
              AND shop = :shopId
              AND id != :id';
      $res = $this->db->fetchValue($sql, (array)$obj);
      return $res == 1;
  }
}