<?php
/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\Lieferschwelle;

use Xentral\Modules\Lieferschwelle\Data\Lieferschwelle;
use Xentral\Components\Database\Database;

final class LieferschwelleGateway
{
  /**
   * @param Database $db
   */
  public function __construct(private readonly Database $db)
  {
  }

  public function Get(int $id) : Lieferschwelle {
    $sql = 'SELECT * FROM lieferschwelle WHERE id = :id';
    $res = $this->db->fetchRow($sql, ['id' => $id]);
    return new Lieferschwelle(
        $res['empfaengerland'],
        $res['ursprungsland'],
        $res['ustid'],
        $res['verwenden'],
        $res['id']
    );
  }

  public function Insert(Lieferschwelle $obj) : Lieferschwelle {
    $sql = "INSERT INTO lieferschwelle
            (ursprungsland, empfaengerland, ustid, verwenden)
            VALUES 
            (:originCountryIso, :destinationCountryIso, :ustId, :active)";
    $this->db->perform($sql, (array)$obj);
    $obj->id = $this->db->lastInsertId();
    return $obj;
  }

  public function Update(Lieferschwelle $obj) : Lieferschwelle {
    $sql = 'UPDATE lieferschwelle
            SET ursprungsland = :originCountryIso, 
                empfaengerland = :destinationCountryIso,
                ustid = :ustId,
                verwenden = :active
            WHERE id = :id';
    $this->db->perform($sql, (array)$obj);
    return $obj;
  }

  public function Delete(int $id) : void {
    $sql = 'DELETE FROM lieferschwelle WHERE id = :id';
    $this->db->perform($sql, ['id' => $id]);
  }

  public function Exists(Lieferschwelle $obj) : bool {
      $where = 'ursprungsland = :originCountryIso';
      if ($obj->originCountryIso === null)
          $where = 'ursprungsland IS NULL';
      $sql = "SELECT 1 FROM lieferschwelle 
              WHERE $where AND empfaengerland = :destinationCountryIso 
              AND id != :id";
      $res = $this->db->fetchValue($sql, (array)$obj);
      return $res == 1;
  }
}