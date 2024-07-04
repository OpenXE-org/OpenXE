<?php
/*
 * SPDX-FileCopyrightText: 2023 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\MatrixProduct;

use Xentral\Components\Database\Database;
use Xentral\Modules\Article\Gateway\ArticleGateway;
use Xentral\Modules\MatrixProduct\Data\Option;
use Xentral\Modules\MatrixProduct\Data\Group;
use Xentral\Modules\MatrixProduct\Data\Translation;

final class MatrixProductService
{
  /**
   * @param Database $db
   * @param MatrixProductGateway $gateway
   * @param ArticleGateway $articleGateway
   */
  public function __construct(
      private readonly Database $db,
      private readonly MatrixProductGateway $gateway,
      private readonly ArticleGateway $articleGateway)
  { }

  //region Groups
  public function GetGlobalGroupById(int $id) : Group {
    return $this->gateway->GetGlobalGroupById($id);
  }

  public function GetArticleGroupById(int $id) : Group {
    return $this->gateway->GetArticleGroupById($id);
  }

  /**
   * @param int $articleId
   * @return Group[]
   */
  public function GetArticleGroupsByArticleId(int $articleId) : array {
    return $this->gateway->GetArticleGroupsByArticleId($articleId);
  }

  public function SaveGlobalGroup(Group $obj) : void {
    if ($obj->id > 0)
      $this->gateway->UpdateGlobalGroup($obj);
    else
      $this->gateway->InsertGlobalGroup($obj);
  }

  public function SaveArticleGroup(Group $obj) : void {
    if ($obj->id > 0) {
      $this->gateway->UpdateArticleGroup($obj);
    } else {
      $this->gateway->InsertArticleGroup($obj);
    }
  }

  public function DeleteGlobalGroup(int $id) : void {
    $this->gateway->DeleteGlobalGroup($id);
  }

  public function DeleteArticleGroup(int $id) : bool {
    $options = $this->gateway->GetArticleOptionIdsByGroupIds($id);
    $variants = $this->gateway->GetVariantIdsByOptions($options);
    if (!empty($variants))
        return false;
    $this->gateway->DeleteArticleGroup($id);
    return true;
  }
  //endregion

  //region Options
  public function GetGlobalOptionById(int $id) : Option {
    return $this->gateway->GetGlobalOptionById($id);
  }

  public function GetArticleOptionById(int $id) : Option {
    return $this->gateway->GetArticleOptionById($id);
  }

  /**
   * @param int $articleId
   * @return Option[]
   */
  public function GetArticleOptionsByArticleId(int $articleId) : array {
    return $this->gateway->GetArticleOptionsByArticleId($articleId);
  }

  public function GetSelectedOptionIdsByVariantId(int $variantId) : array {
    return $this->gateway->GetSelectedOptionIdsByVariantId($variantId);
  }

  public function SaveGlobalOption(Option $obj) : void {
    if ($obj->id > 0) {
      $this->gateway->UpdateGlobalOption($obj);
    } else {
      $this->gateway->InsertGlobalOption($obj);
    }
  }

  public function SaveArticleOption(Option $obj) : void {
    if ($obj->id > 0) {
      $this->gateway->UpdateArticleOption($obj);
    } else {
      $this->gateway->InsertArticleOption($obj);
    }
  }

  public function DeleteGlobalOption(int $id) : void {
    $this->gateway->DeleteGlobalOption($id);
  }

  public function DeleteArticleOption(int $id) : bool {
    $variants = $this->gateway->GetVariantIdsByOptions($id);
    if (!empty($variants))
        return false;
    $this->gateway->DeleteArticleOption($id);
    return true;
  }

  public function AddGlobalOptionsForArticle(int $articleId, int|array $optionIds): void
  {
    $sql = "SELECT mg.name groupname, mg.name_ext groupnameext, mg.projekt as groupprojekt, mg.pflicht as grouprequired, mo.*
            FROM matrixprodukt_eigenschaftenoptionen mo
            JOIN matrixprodukt_eigenschaftengruppen mg on mo.gruppe=mg.id
            WHERE mo.id IN (:optionIds)";
    $optionArr = $this->db->fetchAll($sql, ['optionIds' => $optionIds]);
    foreach ($optionArr as $option) {
      $groupId = $this->gateway->GetArticleGroupIdByName($articleId, $option['groupname']);
      if (!$groupId) {
        $obj = new Group($option['groupname'], nameExternal: $option['groupnameext'], projectId: $option['groupprojekt'], required: $option['grouprequired'], articleId: $articleId);
        $group = $this->gateway->InsertArticleGroup($obj);
        $groupId = $group->id;
      }
      $optionId = $this->gateway->GetArticleOptionIdByName($articleId, $groupId, $option['name']);
      if (!$optionId) {
        $obj = new Option($option['name'], $groupId, nameExternal: $option['name_ext'], sort: $option['sort'], globalOptionId: $option['id'], articleId: $articleId);
        $this->gateway->InsertArticleOption($obj);
      }
    }
  }
  //endregion

  //region Variants
  public function GetVariantIdByOptionSet(array $optionIds) : ?int {
      return $this->gateway->GetVariantIdByOptions($optionIds);
  }
  public function GetSuffixStringForOptionSet(array $optionIds) : string
  {
      return $this->gateway->GetSuffixStringForOptionSet($optionIds);
  }
  public function SaveVariant(int $articleId, int $variantId, array $optionIds, ?int $oldVariantId = null) : bool|string {
    if ($oldVariantId != null && $oldVariantId != $variantId) {
      $this->gateway->ReplaceVariant($oldVariantId, $variantId);
      $this->articleGateway->SetVariantStatus($oldVariantId, null);
    }
    $variantWithOptionSet = $this->gateway->GetVariantIdByOptions($optionIds);
    if ($variantWithOptionSet != null && $variantWithOptionSet != $variantId)
      return 'Diese Optionen wurden bereits einer anderen Variante zugewiesen';

    $existingIds = $this->gateway->GetOptionIdsByVariant($variantId);
    $toDelete = array_diff($existingIds, $optionIds);
    $toCreate = array_diff($optionIds, $existingIds);
    foreach ($toDelete as $item)
      $this->gateway->DeleteOptionFromVariant($variantId, $item);
    foreach ($toCreate as $item)
      $this->gateway->AddOptionToVariant($variantId, $item);
    $this->articleGateway->SetVariantStatus($variantId, $articleId);
    return true;
  }

  public function DeleteVariant(int $variantId) : void {
    $this->gateway->DeleteVariantById($variantId);
    $this->articleGateway->SetVariantStatus($variantId, null);
  }
  //endregion

  //region Translations
  public function GetGroupTranslation(int $id) : Translation {
      return $this->gateway->GetGroupTranslationById($id);
  }

  public function GetOptionTranslation(int $id) : Translation {
      return $this->gateway->GetOptionTranslationById($id);
  }

  public function SaveGroupTranslation(Translation $obj) : Translation {
      if ($obj->id > 0)
          return $this->gateway->UpdateGroupTranslation($obj);
      return $this->gateway->InsertGroupTranslation($obj);
  }

  public function SaveOptionTranslation(Translation $obj) : Translation {
      if ($obj->id > 0)
          return $this->gateway->UpdateOptionTranslation($obj);
      return $this->gateway->InsertOptionTranslation($obj);
  }

  public function DeleteGroupTranslation(int $id) : void {
      $this->gateway->DeleteGroupTranslation($id);
  }

  public function DeleteOptionTranslation(int $id) : void {
      $this->gateway->DeleteOptionTranslation($id);
  }
  //endregion
}