<?php
/*
 * SPDX-FileCopyrightText: 2023 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\MatrixProduct;

use Xentral\Components\Database\Database;
use Xentral\Modules\MatrixProduct\Data\Group;
use Xentral\Modules\MatrixProduct\Data\Option;
use Xentral\Modules\MatrixProduct\Data\Translation;

final class MatrixProductGateway
{
  /**
   * @param Database $db
   */
  public function __construct(private readonly Database $db)
  {
  }

  //region Groups
  public function GetGlobalGroupById(int $id) : Group {
    $sql = "SELECT * FROM matrixprodukt_eigenschaftengruppen WHERE id = :id";
    $res = $this->db->fetchRow($sql, ['id' => $id]);
    return Group::fromDbArray($res);
  }

  public function GetArticleGroupById(int $id) : Group {
    $sql = "SELECT * FROM matrixprodukt_eigenschaftengruppen_artikel WHERE id = :id";
    $res = $this->db->fetchRow($sql, ['id' => $id]);
    return Group::fromDbArray($res);
  }

  public function GetArticleGroupIdByName(int $articleId, string $name) : int {
    $sql = "SELECT id FROM matrixprodukt_eigenschaftengruppen_artikel 
            WHERE name = :name AND artikel = :articleId";
    return $this->db->fetchValue($sql, [
      'name' => $name,
      'articleId' => $articleId
    ]);
  }

  public function GetArticleGroupsByArticleId(int $articleId) : array {
    $sql = "SELECT * FROM matrixprodukt_eigenschaftengruppen_artikel WHERE artikel = :articleId";
    $rows = $this->db->fetchAssoc($sql, ['articleId' => $articleId]);
    $res = [];
    foreach ($rows as $row) {
      $res[] = Group::fromDbArray($row);
    }
    return $res;
  }

  public function InsertGlobalGroup(Group $obj) : Group {
    $sql = "INSERT INTO matrixprodukt_eigenschaftengruppen
            (aktiv, name, name_ext, projekt, pflicht)
            VALUES 
            (:active, :name, :nameExternal, :projectId, :required)";
    print_r($obj);
    $this->db->perform($sql, (array)$obj);
    $obj->id = $this->db->lastInsertId();
    return $obj;
  }

  public function InsertArticleGroup(Group $obj) : Group {
    $sql = "INSERT INTO matrixprodukt_eigenschaftengruppen_artikel
            (artikel, aktiv, name, name_ext, projekt, sort, pflicht)
            VALUES 
            (:articleId, :active, :name, :nameExternal, :projectId, :sort, :required)";
    $this->db->perform($sql, (array)$obj);
    $obj->id = $this->db->lastInsertId();
    return $obj;
  }

  public function UpdateGlobalGroup(Group $obj) : Group {
    $sql = "UPDATE matrixprodukt_eigenschaftengruppen
            SET aktiv = :active, name = :name, name_ext = :nameExternal, projekt = :projectId, pflicht = :required
            WHERE id = :id";
    $this->db->perform($sql, (array)$obj);
    return $obj;
  }

  public function UpdateArticleGroup(Group $obj) : Group {
    $sql = "UPDATE matrixprodukt_eigenschaftengruppen_artikel 
            SET aktiv = :active, name = :name, name_ext = :nameExternal, projekt = :projectId, sort = :sort, pflicht = :required
            WHERE id = :id";
    $this->db->perform($sql, (array)$obj);
    return $obj;
  }

  public function DeleteGlobalGroup(int $id) : void {
    $sql = "UPDATE matrixprodukt_eigenschaftenoptionen_artikel moa
          JOIN matrixprodukt_eigenschaftenoptionen mo ON moa.matrixprodukt_eigenschaftenoptionen=mo.id
          JOIN matrixprodukt_eigenschaftengruppen mg ON mo.gruppe=mg.id
          SET moa.matrixprodukt_eigenschaftenoptionen=0
          WHERE mg.id = :id";
    $this->db->perform($sql, ['id' => $id]);
    $sql = "DELETE mg, mo 
            FROM matrixprodukt_eigenschaftengruppen mg
            LEFT OUTER JOIN matrixprodukt_eigenschaftenoptionen mo ON mo.gruppe = mg.id
            WHERE mg.id = :id";
    $this->db->perform($sql, ['id' => $id]);
  }

  public function DeleteArticleGroup(int $id) : void {
    $sql = "DELETE mga, moa, mota 
            FROM matrixprodukt_eigenschaftengruppen_artikel mga
            LEFT OUTER JOIN matrixprodukt_eigenschaftenoptionen_artikel moa ON moa.gruppe = mga.id
            LEFT OUTER JOIN matrixprodukt_optionen_zu_artikel mota ON mota.option_id = moa.id
            WHERE mga.id = :id";
    $this->db->perform($sql, ['id' => $id]);
  }
  //endregion

  //region Options
  public function GetGlobalOptionById(int $id) : Option {
    $sql = "SELECT * FROM matrixprodukt_eigenschaftenoptionen WHERE id = :id";
    $res = $this->db->fetchRow($sql, ['id' => $id]);
    return Option::fromDbArray($res);
  }

  public function GetArticleOptionById(int $id) : Option {
    $sql = "SELECT * FROM matrixprodukt_eigenschaftenoptionen_artikel WHERE id = :id";
    $res = $this->db->fetchRow($sql, ['id' => $id]);
    return Option::fromDbArray($res);
  }

  public function GetArticleOptionIdsByGroupIds(int|array $groupIds) : array {
      if (empty($groupIds))
          return [];
      $sql = "SELECT id FROM matrixprodukt_eigenschaftenoptionen WHERE gruppe IN (:ids)";
      return $this->db->fetchCol($sql, ['ids' => $groupIds]);
  }

  public function GetArticleOptionIdByName(int $articleId, int $groupId, string $name) : int {
    $sql = "SELECT id FROM matrixprodukt_eigenschaftenoptionen_artikel
            WHERE name = :name AND artikel = :articleId AND gruppe = :groupId";
    return $this->db->fetchValue($sql, [
      'name' => $name,
      'articleId' => $articleId,
      'groupId' => $groupId
    ]);
  }

  public function GetArticleOptionsByArticleId(int $articleId) : array {
    $sql = "SELECT * FROM matrixprodukt_eigenschaftenoptionen_artikel WHERE artikel = :articleId";
    $rows = $this->db->fetchAssoc($sql, ['articleId' => $articleId]);
    $res = [];
    foreach ($rows as $row) {
      $res[] = Option::fromDbArray($row);
    }
    return $res;
  }

  public function GetSelectedOptionIdsByVariantId(int $variantId) : array {
    $sql = "SELECT option_id FROM matrixprodukt_optionen_zu_artikel WHERE artikel = :variantId";
    return $this->db->fetchCol($sql, ['variantId' => $variantId]);
  }

  public function InsertGlobalOption(Option $obj) : Option {
    $sql = "INSERT INTO matrixprodukt_eigenschaftenoptionen
            (aktiv, name, name_ext, sort, gruppe, artikelnummer, articlenumber_suffix)
            VALUES
            (:active, :name, :nameExternal, :sort, :groupId, :articleNumber, :articleNumberSuffix)";
    $this->db->perform($sql, (array)$obj);
    $obj->id = $this->db->lastInsertId();
    return $obj;
  }

  public function InsertArticleOption(Option $obj) : Option {
    $sql = "INSERT INTO matrixprodukt_eigenschaftenoptionen_artikel
            (artikel, matrixprodukt_eigenschaftenoptionen, aktiv, name, name_ext, sort, gruppe, artikelnummer, articlenumber_suffix)
            VALUES
            (:articleId, :globalOptionId, :active, :name, :nameExternal, :sort, :groupId, :articleNumber, :articleNumberSuffix)";
    $this->db->perform($sql, (array)$obj);
    $obj->id = $this->db->lastInsertId();
    return $obj;
  }

  public function UpdateGlobalOption(Option $obj) : Option {
    $sql = "UPDATE matrixprodukt_eigenschaftenoptionen 
            SET aktiv = :active, name = :name, name_ext = :nameExternal, sort = :sort, artikelnummer = :articleNumber,
                articlenumber_suffix = :articleNumberSuffix
            WHERE id = :id";
    $this->db->perform($sql, (array)$obj);
    return $obj;
  }

  public function UpdateArticleOption(Option $obj) : Option {
    $sql = "UPDATE matrixprodukt_eigenschaftenoptionen_artikel 
            SET aktiv = :active, name = :name, name_ext = :nameExternal, sort = :sort, artikelnummer = :articleNumber,
                articlenumber_suffix = :articleNumberSuffix
            WHERE id = :id";
    $this->db->perform($sql, (array)$obj);
    return $obj;
  }

  public function DeleteGlobalOption(int $id) : void {
    $sql = "UPDATE matrixprodukt_eigenschaftenoptionen_artikel moa
          JOIN matrixprodukt_eigenschaftenoptionen mo ON moa.matrixprodukt_eigenschaftenoptionen=mo.id
          SET moa.matrixprodukt_eigenschaftenoptionen=0
          WHERE mo.id = :id";
    $this->db->perform($sql, ['id' => $id]);
    $sql = "DELETE moa
            FROM matrixprodukt_eigenschaftenoptionen mo
            WHERE mo.id = :id";
    $this->db->perform($sql, ['id' => $id]);
  }

  public function DeleteArticleOption(int $id) : void {
    $sql = "DELETE moa, mota
            FROM matrixprodukt_eigenschaftenoptionen_artikel moa
            LEFT OUTER JOIN matrixprodukt_optionen_zu_artikel mota ON mota.option_id=moa.id
            WHERE moa.id = :id";
    $this->db->perform($sql, ['id' => $id]);
  }
  //endregion

  //region Variants
  public function ReplaceVariant(int $oldId, int $newId) : void {
    $sql = "UPDATE matrixprodukt_optionen_zu_artikel SET artikel = :newId WHERE artikel = :oldId";
    $this->db->perform($sql, [
        'oldId' => $oldId,
        'newId' => $newId]
    );
  }

  /**
   * @param int $variantId
   * @return int[]
   */
  public function GetOptionIdsByVariant(int $variantId) : array {
    $sql = "SELECT option_id FROM matrixprodukt_optionen_zu_artikel WHERE artikel = :id";
    return $this->db->fetchCol($sql, ['id' => $variantId]);
  }

  public function AddOptionToVariant(int $variantId, int $optionId) : void {
    $sql = "INSERT INTO matrixprodukt_optionen_zu_artikel (artikel, option_id) VALUES (:variantId, :optionId)";
    $this->db->perform($sql, [
      'variantId' => $variantId,
      'optionId' => $optionId
    ]);
  }

  public function DeleteOptionFromVariant(int $variantId, int $optionId) : void {
    $sql = "DELETE FROM matrixprodukt_optionen_zu_artikel WHERE artikel = :variantId AND option_id = :optionId";
    $this->db->perform($sql, [
        'variantId' => $variantId,
        'optionId' => $optionId
    ]);
  }

  public function GetVariantIdByOptions(array $optionIds) : ?int {
    if (empty($optionIds))
      return null;
    sort($optionIds);
    $sql = "SELECT artikel
            FROM matrixprodukt_optionen_zu_artikel
            WHERE option_id IN (:ids) 
            GROUP BY artikel
            HAVING group_concat(option_id order by option_id separator ',') = :idList";
    $res = $this->db->fetchValue($sql, [
        'ids' => $optionIds,
        'idList' => join(',', $optionIds)
    ]);
    return $res ?: null;
  }

  public function GetVariantIdsByOptions(int|array $optionIds) : array
  {
    if (empty($optionIds))
        return [];
    $sql = "SELECT artikel FROM matrixprodukt_optionen_zu_artikel WHERE option_id IN (:ids)";
    return $this->db->fetchCol($sql, ['ids' => $optionIds]);
  }

  public function DeleteVariantById(int $variantId) : void {
    $sql = "DELETE FROM matrixprodukt_optionen_zu_artikel WHERE artikel = :id";
    $this->db->perform($sql, ['id' => $variantId]);
  }

  public function GetSuffixStringForOptionSet(array $optionIds) : string {
      $sql = "SELECT GROUP_CONCAT(IFNULL(NULLIF(mao.articlenumber_suffix,''), mao.id) ORDER BY mag.sort, mag.id SEPARATOR '_')
              FROM matrixprodukt_eigenschaftenoptionen_artikel mao
              JOIN matrixprodukt_eigenschaftengruppen_artikel mag ON mao.gruppe = mag.id
              WHERE mao.id IN (:idList)";
      $res = $this->db->fetchValue($sql, ['idList' => $optionIds]);
      return $res;
  }
  //endregion

  //region Translations
  public function GetGroupTranslationById(int $id) : Translation {
      $sql = "SELECT * FROM matrix_article_translation WHERE id = :id";
      $row = $this->db->fetchRow($sql, ['id' => $id]);
      return Translation::fromDbArray($row);
  }

  public function GetOptionTranslationById(int $id) : Translation {
      $sql = "SELECT * FROM matrix_article_options_translation WHERE id = :id";
      $row = $this->db->fetchRow($sql, ['id' => $id]);
      return Translation::fromDbArray($row);
  }

  public function InsertGroupTranslation(Translation $obj) : Translation {
      $sql = "INSERT INTO matrix_article_translation 
              (language_from, language_to, name_from, name_to, name_external_from, name_external_to)
              VALUES (:languageFrom, :languageTo, :nameFrom, :nameTo, :nameExternalFrom, :nameExternalTo)";
      $this->db->perform($sql, (array)$obj);
      $obj->id = $this->db->lastInsertId();
      return $obj;
  }

    public function InsertOptionTranslation(Translation $obj) : Translation {
        $sql = "INSERT INTO matrix_article_options_translation 
              (language_from, language_to, name_from, name_to, name_external_from, name_external_to)
              VALUES (:languageFrom, :languageTo, :nameFrom, :nameTo, :nameExternalFrom, :nameExternalTo)";
        $this->db->perform($sql, (array)$obj);
        $obj->id = $this->db->lastInsertId();
        return $obj;
    }

    public function UpdateGroupTranslation(Translation $obj) : Translation {
        $sql = "UPDATE matrix_article_translation SET language_from = :languageFrom, language_to = :languageTo, 
                name_from = :nameFrom, name_to = :nameTo, name_external_from = :nameExternalFrom, 
                name_external_to = :nameExternalTo WHERE id = :id";
        $this->db->perform($sql, (array)$obj);
        return $obj;
    }

    public function UpdateOptionTranslation(Translation $obj) : Translation {
        $sql = "UPDATE matrix_article_options_translation SET language_from = :languageFrom, language_to = :languageTo, 
                name_from = :nameFrom, name_to = :nameTo, name_external_from = :nameExternalFrom, 
                name_external_to = :nameExternalTo WHERE id = :id";
        $this->db->perform($sql, (array)$obj);
        return $obj;
    }

    public function DeleteGroupTranslation(int $id) : void {
      $sql = "DELETE FROM matrix_article_translation WHERE id = :id";
      $this->db->perform($sql, ['id' => $id]);
    }

    public function DeleteOptionTranslation(int $id) : void {
        $sql = "DELETE FROM matrix_article_options_translation WHERE id = :id";
        $this->db->perform($sql, ['id' => $id]);
    }
  //endregion
}