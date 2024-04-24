<?php
/*
 * SPDX-FileCopyrightText: 2023 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

use Xentral\Components\Http\JsonResponse;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Response;
use Xentral\Modules\Article\Service\ArticleService;
use Xentral\Modules\MatrixProduct\Data\Group;
use Xentral\Modules\MatrixProduct\Data\Option;
use Xentral\Modules\MatrixProduct\Data\Translation;
use Xentral\Modules\MatrixProduct\MatrixProductService;

class Matrixprodukt
{
    private ApplicationCore $app;
    private MatrixProductService $service;
    private ArticleService $articleService;
    private Request $request;

    const MODULE_NAME = 'MatrixProduct';

    public function __construct(ApplicationCore $app, bool $intern = false)
    {
        $this->app = $app;
        if ($intern)
            return;

        if (!$this->app instanceof Application)
            return;
        $this->service = $this->app->Container->get('MatrixProductService');
        $this->request = $this->app->Container->get('Request');
        $this->articleService = $this->app->Container->get('ArticleService');

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler("list", "ActionList");
        $this->app->ActionHandler("optionenlist", "ActionOptionList");
        $this->app->ActionHandler("artikel", "ActionArticle");
        $this->app->ActionHandler("translation", "ActionTranslation");
        $this->app->ActionHandlerListen($app);
    }

    private function createMenu(): void
    {
        $this->app->erp->MenuEintrag("index.php?module=matrixprodukt&action=list", "&Uuml;bersicht");
        $this->app->erp->MenuEintrag("index.php?module=matrixprodukt&action=translation", "&Uuml;bersetzungen");
    }

    public function Install()
    {

    }

    public function TableSearch(&$app, $name, $erlaubtevars)
    {
        switch ($name) {
            case "matrixprodukt_groups":
                $allowed['matrixprodukt_list'] = array('list');
                $heading = array('', 'Name', 'Name (extern)', 'Men&uuml;');
                $width = array('1%', '30%', '30%', '1%'); // Fill out manually later
                $findcols = array('mg.id', 'mg.name', 'mg.name_ext');
                $searchsql = array('mg.name', 'mg.name_ext');
                $menu = "<table><tr><td nowrap>"
                    . "<img class=\"vueAction\" data-action=\"groupEdit\" data-group-id=\"%value%\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">&nbsp;"
                    . "<a href=\"index.php?module=matrixprodukt&action=optionenlist&id=%value%\">"
                    . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\"></a>&nbsp;"
                    . "<img class=\"vueAction\" data-action=\"groupDelete\" data-group-id=\"%value%\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">"
                    . "</td></tr></table>";
                $sql = "SELECT SQL_CALC_FOUND_ROWS mg.id, mg.id, mg.name, mg.name_ext, mg.id FROM matrixprodukt_eigenschaftengruppen mg";
                $where = "1";
                $count = "SELECT count(DISTINCT id) FROM matrixprodukt_eigenschaftengruppen WHERE $where";
                break;
            case "matrixprodukt_options":
                $id = $this->app->Secure->GetGET('id');
                $heading = array('', 'Name', 'Name (extern)', 'Men&uuml;');
                $width = array('1%', '30%', '30%', '1%');
                $findcols = array('mo.id', 'mo.name', 'mo.name_ext');
                $searchsql = array('mo.name', 'mo.name_ext');
                $menu = "<table><tr><td nowrap>"
                    . "<img class=\"vueAction\" data-action=\"optionEdit\" data-option-id=\"%value%\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">&nbsp;"
                    . "<img class=\"vueAction\" data-action=\"optionDelete\" data-option-id=\"%value%\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">"
                    . "</td></tr></table>";
                $sql = "SELECT SQL_CALC_FOUND_ROWS mo.id, mo.id, mo.name, mo.name_ext, mo.id FROM matrixprodukt_eigenschaftenoptionen mo";
                $where = "mo.gruppe = $id";
                $count = "SELECT count(DISTINCT mo.id) FROM matrixprodukt_eigenschaftenoptionen mo WHERE $where";
                break;
            case "matrixprodukt_article_groups":
                $id = $this->app->Secure->GetGET('id');
                $heading = array('', 'Name', 'Name (extern)', 'Men&uuml;');
                $width = array('1%', '30%', '30%', '1%'); // Fill out manually later
                $findcols = array('mga.id', 'mga.name', 'mga.name_ext');
                $searchsql = array('mga.name', 'mga.name_ext');
                $menu = "<table><tr><td nowrap>"
                    . "<img class=\"vueAction\" data-action=\"groupEdit\" data-group-id=\"%value%\" data-article-id=\"$id\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">&nbsp;"
                    . "<a href=\"index.php?module=matrixprodukt&action=artikel&id=$id&sid=%value%\">"
                    . "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/forward.svg\"></a>&nbsp;"
                    . "<img class=\"vueAction\" data-action=\"groupDelete\" data-group-id=\"%value%\" data-article-id=\"$id\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">"
                    . "</td></tr></table>";
                $sql = "SELECT SQL_CALC_FOUND_ROWS mga.id, mga.id, mga.name, mga.name_ext, mga.id FROM matrixprodukt_eigenschaftengruppen_artikel mga";
                $where = "mga.artikel = $id";
                $count = "SELECT count(DISTINCT mga.id) FROM matrixprodukt_eigenschaftengruppen_artikel mga WHERE $where";
                break;
            case "matrixprodukt_article_options":
                $id = $this->app->Secure->GetGET('id');
                $groupId = $this->app->Secure->GetGET('sid');
                $heading = array('', 'Name', 'Name (extern)', 'Men&uuml;');
                $width = array('1%', '30%', '30%', '1%');
                $findcols = array('moa.id', 'moa.name', 'moa.name_ext');
                $searchsql = array('moa.name', 'moa.name_ext');
                $menu = "<table><tr><td nowrap>"
                    . "<img class=\"vueAction\" data-action=\"optionEdit\" data-option-id=\"%value%\" data-article-id=\"$id\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">&nbsp;"
                    . "<img class=\"vueAction\" data-action=\"optionDelete\" data-option-id=\"%value%\" data-article-id=\"$id\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">"
                    . "</td></tr></table>";
                $sql = "SELECT SQL_CALC_FOUND_ROWS moa.id, moa.id, moa.name, moa.name_ext, moa.id FROM matrixprodukt_eigenschaftenoptionen_artikel moa";
                $where = "moa.gruppe = $groupId";
                $count = "SELECT count(DISTINCT moa.id) FROM matrixprodukt_eigenschaftenoptionen_artikel moa WHERE $where";
                break;
            case "matrixprodukt_variants":
                $id = $this->app->Secure->GetGET('id');
                $groups = $this->app->DB->SelectPairs("SELECT id, name FROM matrixprodukt_eigenschaftengruppen_artikel WHERE artikel = $id");
                $heading[] = 'Artikel';
                $width[] = '5%';
                $nameColumns = [];
                $joins = [];
                foreach ($groups as $groupId => $groupName) {
                    $heading[] = $groupName;
                    $width[] = '5%';
                    $nameColumns[] = "MAX(moa_$groupId.name)";
                    $joins[] = "LEFT JOIN matrixprodukt_eigenschaftenoptionen_artikel moa_$groupId 
                                ON mota.option_id=moa_$groupId.id AND moa_$groupId.gruppe = $groupId";
                }
                $heading[] = 'Men&uuml;';
                $width[] = '1%';
                $findcols = array_merge($nameColumns, ['nummer', 'id']);
                $searchsql = $nameColumns;
                $menu = "<table><tr><td nowrap>"
                    . "<img class=\"vueAction\" data-action=\"variantEdit\" data-variant-id=\"%value%\" data-article-id=\"$id\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">&nbsp;"
                    . "<img class=\"vueAction\" data-action=\"variantDelete\" data-variant-id=\"%value%\" data-article-id=\"$id\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">"
                    . "</td></tr></table>";
                $sqlNameCols = join(',', array_merge(['art.id', 'art.nummer'], $nameColumns, ['art.id']));
                $joinSql = join("\n", $joins);
                $sql = "SELECT SQL_CALC_FOUND_ROWS $sqlNameCols 
                        FROM artikel art
                        LEFT JOIN matrixprodukt_optionen_zu_artikel mota ON mota.artikel = art.id
                        $joinSql";
                $where = "art.variante_von = $id";
                $groupby = "GROUP BY art.id";
                $count = "SELECT count(*) FROM artikel art WHERE $where";
                break;
            case "matrixproduct_group_translations":
                $heading = array('Name', 'Name (extern)', 'Sprache', 'Übersetzung Name', 'Übersetzung Name (extern)', 'Men&uuml;');
                $width = array('20%', '20%', '5%', '20%', '20%', '1%');
                $findcols = array('mat.name_from', 'mat.name_external_from', 'mat.name_to', 'mat.name_external_to');
                $searchsql = array('mat.name_from', 'mat.name_external_from');
                $menu = "<table><tr><td nowrap>"
                    . "<img class=\"vueAction\" data-action=\"translationEdit\" data-type=\"group\" data-id=\"%value%\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">&nbsp;"
                    . "<img class=\"vueAction\" data-action=\"translationDelete\" data-type=\"group\" data-id=\"%value%\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">"
                    . "</td></tr></table>";
                $sql = "SELECT SQL_CALC_FOUND_ROWS mat.id, mat.name_from, mat.name_external_from, mat.language_to, mat.name_to, mat.name_external_to, mat.id FROM matrix_article_translation mat";
                $where = "1";
                $count = "SELECT count(DISTINCT mat.id) FROM matrix_article_translation mat WHERE $where";
                break;
            case "matrixproduct_option_translations":
                $heading = array('Name', 'Name (extern)', 'Sprache', 'Übersetzung Name', 'Übersetzung Name (extern)', 'Men&uuml;');
                $width = array('20%', '20%', '5%', '20%', '20%', '1%');
                $findcols = array('mat.name_from', 'mat.name_external_from', 'mat.name_to', 'mat.name_external_to');
                $searchsql = array('mat.name_from', 'mat.name_external_from');
                $menu = "<table><tr><td nowrap>"
                    . "<img class=\"vueAction\" data-action=\"translationEdit\" data-type=\"option\" data-id=\"%value%\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">&nbsp;"
                    . "<img class=\"vueAction\" data-action=\"translationDelete\" data-type=\"option\" data-id=\"%value%\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">"
                    . "</td></tr></table>";
                $sql = "SELECT SQL_CALC_FOUND_ROWS mat.id, mat.name_from, mat.name_external_from, mat.language_to, mat.name_to, mat.name_external_to, mat.id FROM matrix_article_options_translation mat";
                $where = "1";
                $count = "SELECT count(DISTINCT mat.id) FROM matrix_article_options_translation mat WHERE $where";
                break;
        }

        $erg = false;
        foreach ($erlaubtevars as $k => $v) {
            if (isset($$v)) {
                $erg[$v] = $$v;
            }
        }
        return $erg;
    }

    public function ActionList()
    {
        $cmd = $this->app->Secure->GetGET('cmd');
        switch ($cmd) {
            case "edit":
                $id = intval($this->app->Secure->GetGET('groupId'));
                $group = $this->service->GetGlobalGroupById($id);
                if (!$group)
                    return JsonResponse::NotFound();
                $group->project = $this->app->DB->SelectRow("SELECT id, abkuerzung, name FROM projekt WHERE id = $group->projectId");
                return new JsonResponse($group);
            case "save":
                $json = $this->request->getJson();
                $group = new Group($json->name, $json->id ?? null, $json->active ?? false, $json->nameExternal ?? '', $json->project->id ?? 0, $json->required ?? false);
                $this->service->SaveGlobalGroup($group);
                return JsonResponse::NoContent();
            case "delete":
                $json = $this->request->getJson();
                $this->service->DeleteGlobalGroup($json->groupId);
                return JsonResponse::NoContent();
            case "selectoptions":
                $result = [];
                $sql = "SELECT mg.id groupid, mg.name groupname, mo.id optionid, mo.name optionname FROM matrixprodukt_eigenschaftengruppen mg JOIN matrixprodukt_eigenschaftenoptionen mo ON mo.gruppe = mg.id WHERE mg.aktiv = 1 AND mo.aktiv = 1";
                foreach ($this->app->DB->SelectArr($sql) as $row) {
                    $groupid = $row['groupid'];
                    if (!in_array($groupid, $result)) {
                        $result[$groupid]['id'] = $groupid;
                        $result[$groupid]['name'] = $row['groupname'];
                    }
                    $result[$groupid]['options'][] = ['id' => $row['optionid'], 'name' => $row['optionname']];
                }
                return new JsonResponse(array_values($result));
        }

        $this->createMenu();
        $this->app->Tpl->Set('TABSADD', '<input type="button" class="neubutton vueAction" value="NEU" data-action="groupEdit">');
        $this->app->YUI->TableSearch('TAB1', 'matrixprodukt_groups', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "matrixprodukt_list.tpl");
    }

    public function ActionOptionList()
    {
        $this->app->erp->MenuEintrag("index.php?module=matrixprodukt&action=optionenlist", "Optionen");

        $id = $this->request->get->getInt('id');
        $cmd = $this->app->Secure->GetGET('cmd');
        switch ($cmd) {
            case "edit":
                $id = intval($this->app->Secure->GetGET('optionId'));
                $option = $this->service->GetGlobalOptionById($id);
                if (!$option)
                    return JsonResponse::NotFound();
                return new JsonResponse($option);
            case "save":
                $json = $this->request->getJson();
                $option = new Option($json->name, $json->groupId, $json->id, $json->active ?? false,
                    $json->nameExternal ?? '', $json->sort ?? 0, $json->articleNumber ?? '',
                    $json->articleNumberSuffix ?? '');
                $this->service->SaveGlobalOption($option);
                return JsonResponse::NoContent();
            case "delete":
                $json = $this->request->getJson();
                $this->service->DeleteGlobalOption($json->optionId);
                return JsonResponse::NoContent();
        }

        $group = $this->service->GetGlobalGroupById($id);
        $this->app->Tpl->Set('KURZUEBERSCHRIFT1', $group->name);
        $this->app->Tpl->Set('TABSADD', '<input type="button" class="neubutton vueAction" value="NEU" data-action="optionEdit" data-group-id="' . $id . '">');
        $this->app->YUI->TableSearch('TAB1', 'matrixprodukt_options', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "matrixprodukt_optionen_list.tpl");
    }

    public function ActionArticle()
    {
        $cmd = $this->app->Secure->GetGET('cmd');
        $articleModule = $this->app->erp->LoadModul('artikel');
        $articleModule?->ArtikelMenu();
        switch ($cmd) {
            case "addoptions":
                $json = $this->request->getJson();
                $this->service->AddGlobalOptionsForArticle($json->articleId, $json->optionIds);
                return JsonResponse::NoContent();
            case "groupedit":
                $groupId = intval($this->app->Secure->GetGET('groupId'));
                if (!$groupId)
                    return JsonResponse::NotFound();
                $group = $this->service->GetArticleGroupById($groupId);
                $group->project = $this->app->DB->SelectRow("SELECT id, abkuerzung, name FROM projekt WHERE id = $group->projectId");
                return new JsonResponse($group);
            case "groupsave":
                $json = $this->request->getJson();
                $group = new Group($json->name, $json->groupId, $json->active ?? false, $json->nameExternal ?? '',
                    $json->project->id ?? 0, $json->required ?? false, $json->articleId, $json->sort ?? 0);
                $this->service->SaveArticleGroup($group);
                return JsonResponse::NoContent();
            case "groupdelete":
                $json = $this->request->getJson();
                if (!$this->service->DeleteArticleGroup($json->groupId))
                    return JsonResponse::BadRequest(['error' => 'Die Gruppe wird noch von Variantenartikeln verwendet.']);
                return JsonResponse::NoContent();
            case "optionedit":
                $optionId = intval($this->app->Secure->GetGET('optionId'));
                $option = $this->service->GetArticleOptionById($optionId);
                if (!$option)
                    return JsonResponse::NotFound();
                return new JsonResponse($option);
            case "optionsave":
                $json = $this->request->getJson();
                $option = new Option($json->name, $json->groupId, $json->optionId, $json->active ?? false,
                    $json->nameExternal ?? '', $json->sort ?? 0, '',
                    $json->articleNumberSuffix ?? '', 0, $json->articleId);
                $this->service->SaveArticleOption($option);
                return JsonResponse::NoContent();
            case "optiondelete":
                $json = $this->request->getJson();
                if (!$this->service->DeleteArticleOption($json->optionId))
                    return JsonResponse::BadRequest(['error' => 'Die Option wird noch von Variantenartikeln verwendet.']);
                return JsonResponse::NoContent();
            case "variantedit":
                $articleId = $this->request->get->getInt('articleId');
                $variantId = $this->request->get->getInt('variantId');
                $groups = $this->service->GetArticleGroupsByArticleId($articleId);
                $options = $this->service->GetArticleOptionsByArticleId($articleId);
                $selected = $this->service->GetSelectedOptionIdsByVariantId($variantId);
                $result = [];
                foreach ($groups as $group) {
                    $result[$group->id] = [
                        'name' => $group->name,
                        'selected' => 0,
                        'options' => []
                    ];
                }
                foreach ($options as $option) {
                    $result[$option->groupId]['options'][] = ['value' => $option->id, 'name' => $option->name];
                    if (in_array($option->id, $selected))
                        $result[$option->groupId]['selected'] = $option->id;
                }
                $variant = $this->app->DB->SelectRow("SELECT id, nummer, name_de FROM artikel WHERE id = $variantId");
                return new JsonResponse([
                    'groups' => $result,
                    'variant' => $variant
                ]);
            case "variantsave":
                $json = $this->request->getJson();
                $optionIds = [];
                foreach ($json->groups as $group) {
                    if ($group->selected > 0)
                        $optionIds[] = intval($group->selected);
                }
                if (empty($optionIds))
                    return JsonResponse::BadRequest();
                $res = $this->service->SaveVariant($json->articleId, $json->variant->id, $optionIds, $json->variantId);
                if ($res === true)
                    return JsonResponse::NoContent();
                return new JsonResponse([$res], Response::HTTP_BAD_REQUEST);
            case "variantdelete":
                $json = $this->request->getJson();
                $this->service->DeleteVariant($json->variantId);
                return JsonResponse::NoContent();
            case "createMissing":
                if ($this->request->getMethod() === 'GET') {
                    $articleId = $this->request->get->getInt('articleId');
                    $groups = $this->service->GetArticleGroupsByArticleId($articleId);
                    $options = $this->service->GetArticleOptionsByArticleId($articleId);
                    foreach ($groups as $group) {
                        $result[$group->id] = [
                            'name' => $group->name,
                            'selected' => [],
                            'required' => $group->required,
                            'options' => []
                        ];
                    }
                    foreach ($options as $option) {
                        $result[$option->groupId]['options'][] = ['value' => $option->id, 'name' => $option->name];
                        $result[$option->groupId]['selected'][] = $option->id;
                    }
                    return new JsonResponse(['groups' => $result ?? []]);
                } else {
                    $json = $this->request->getJson();
                    $list = [[]];
                    foreach ($json->groups as $group) {
                        if (empty($group->selected))
                            continue;
                        $newList = [];
                        foreach ($list as $old) {
                            foreach ($group->selected as $option) {
                                $newList[] = array_merge($old, [$option]);
                            }
                        }
                        $list = $newList;
                    }
                    $oldnumber = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = $json->articleId");
                    $created = [];
                    foreach ($list as $optionSet) {
                        $variantId = $this->service->GetVariantIdByOptionSet($optionSet);
                        if ($variantId)
                            continue;
                        $number = $oldnumber.'_'.$this->service->GetSuffixStringForOptionSet($optionSet);
                        $newId = $this->articleService->CopyArticle($json->articleId, true, true, true, true, true, true, true, $number);
                        $this->service->SaveVariant($json->articleId, $newId, $optionSet);
                        $created[] = $number;
                    }
                    return new JsonResponse($created);
                }
        }

        $articleId = $this->app->Secure->GetGET('id');
        $groupId = $this->app->Secure->GetGET('sid');
        if (empty($groupId)) {
            $this->app->YUI->TableSearch('TAB1', 'matrixprodukt_article_groups', "show", "", "", basename(__FILE__), __CLASS__);
            $this->app->Tpl->Set('ADDEDITFUNCTION', 'groupEdit');
        } else {
            $this->app->erp->MenuEintrag("index.php?module=matrixprodukt&action=artikel&id=$articleId", 'Zur&uuml;ck zur Gruppen&uuml;bersicht');
            $this->app->YUI->TableSearch('TAB1', 'matrixprodukt_article_options', "show", "", "", basename(__FILE__), __CLASS__);
            $this->app->Tpl->Set('SID', $groupId);
            $this->app->Tpl->Set('ADDEDITFUNCTION', 'optionEdit');
        }
        if ($this->service->GetArticleGroupsByArticleId($articleId)) {
            $this->app->YUI->TableSearch('TAB2', 'matrixprodukt_variants', "show", "", "", basename(__FILE__), __CLASS__);
        } else {
            $this->app->Tpl->Set('TAB2HIDEACTIONS', 'style="display: none;"');
            $this->app->Tpl->Set('MESSAGE2', "<div class=\"error\">Es sind noch keine Gruppen angelegt!</div>");
        }
        $this->app->Tpl->Parse('PAGE', "matrixprodukt_article.tpl");
    }

    public function ActionTranslation() {
        $cmd = $this->app->Secure->GetGET('cmd');
        switch ($cmd) {
            case "edit":
                $id = $this->app->Secure->GetGET('id');
                $isOption = $this->app->Secure->GetGET('type') === 'option';
                $translation = $isOption ? $this->service->GetOptionTranslation($id) : $this->service->GetGroupTranslation($id);
                return new JsonResponse($translation);
            case "save":
                $json = $this->request->getJson();
                $isOption = $json->type === 'option';
                $translation = new Translation($json->nameFrom, $json->languageTo, $json->nameTo, $json->id,
                    $json->nameExternalFrom ?? '', $json->nameExternalTo ?? '');
                if ($isOption)
                    $this->service->SaveOptionTranslation($translation);
                else
                    $this->service->SaveGroupTranslation($translation);
                return JsonResponse::NoContent();
            case "delete":
                $json = $this->request->getJson();
                $isOption = $json->type === 'option';
                if ($isOption)
                    $this->service->DeleteOptionTranslation($json->id);
                else
                    $this->service->DeleteGroupTranslation($json->id);
                return JsonResponse::NoContent();
        }
        $this->createMenu();
        $this->app->YUI->TableSearch('TABGRP', 'matrixproduct_group_translations', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->YUI->TableSearch('TABOPT', 'matrixproduct_option_translations', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "matrixprodukt_translation.tpl");
    }
}