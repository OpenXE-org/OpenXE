<?php
/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

use Xentral\Components\Database\Database;
use Xentral\Components\Http\JsonResponse;
use Xentral\Components\Http\Request;
use Xentral\Modules\CrossSelling\CrossSellingGateway;
use Xentral\Modules\CrossSelling\Data\CrossSellingArticle;
use Xentral\Modules\CrossSelling\Data\CrossSellingType;

class Crossselling
{
    private ApplicationCore $app;
    private CrossSellingGateway $gateway;
    private Database $database;
    private Request $request;

    public const MODULE_NAME = 'CrossSelling';

    public function __construct(ApplicationCore $app, bool $intern = false)
    {
        $this->app = $app;
        if ($intern) {
            return;
        }

        if (!$this->app instanceof Application) {
            return;
        }
        $this->gateway = $this->app->Container->get('CrossSellingGateway');
        $this->request = $this->app->Container->get('Request');
        $this->database = $this->app->Container->get('Database');

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler('list', 'ActionList');
        $this->app->ActionHandler('add', 'ActionEdit');
        $this->app->ActionHandler('edit', 'ActionEdit');
        $this->app->ActionHandler('delete', 'ActionDelete');
        $this->app->ActionHandlerListen($app);
    }

    private function createMenu(): void
    {
        $this->app->erp->MenuEintrag('index.php?module=crossselling&action=list', '&Uuml;bersicht');
    }

    public function Install() {}

    public function TableSearch(&$app, $name, $erlaubtevars): array
    {
        switch ($name) {
            case 'crossselling_list':
                $heading = ['', 'Artikel Nummer', 'Artikel Name', 'CS Artikel Nummer', 'Cross-Selling Artikel Name', 'Art', 'Men&uuml;'];
                $width = ['1%', '10%', '30%', '10%', '30%', '5%', '1%']; // Fill out manually later
                $findcols = [null, 'a.nummer', 'a.name_de', 'a2.nummer', 'a2.name_de', null];
                $searchsql = ['a.nummer', 'a.name_de', 'a2.nummer', 'a2.name_de'];
                $menu = '<table><tr><td nowrap>'
                    . "<img class=\"vueAction\" data-action=\"edit\" data-id=\"%value%\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">&nbsp;"
                    . "<img class=\"vueAction\" data-action=\"delete\" data-id=\"%value%\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">"
                    . '</td></tr></table>';
                $sql = "SELECT SQL_CALC_FOUND_ROWS csa.id, csa.id, a.nummer, a.name_de, a2.nummer, a2.name_de, 
                           CASE csa.art WHEN 1 THEN 'Ähnlich' WHEN 2 THEN 'Zubehör' END as art, csa.id 
                        FROM crossselling_artikel csa
                        JOIN artikel a ON a.id = csa.artikel
                        JOIN artikel a2 ON a2.id = csa.crosssellingartikel";
                $where = '1';
                $count = "SELECT count(DISTINCT id) FROM crossselling_artikel WHERE $where";
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

    public function ActionList(): void
    {
        $this->createMenu();
        $this->app->Tpl->Set(
            'TABSADD',
            '<input type="button" class="neubutton vueAction" value="NEU" data-action="edit">',
        );
        $this->app->YUI->TableSearch('TAB1', 'crossselling_list', 'show', '', '', basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', 'crossselling_list.tpl');
    }

    public function ActionEdit(): JsonResponse
    {
        if ($this->request->getMethod() == 'POST') {
            $json = $this->request->getJson();
            if (!$this->checkShopProjectRights($json)) {
                return JsonResponse::Forbidden();
            }
            $obj = $this->jsonToCrossSellingArticle($json);
            if ($this->gateway->Exists($obj))
                return JsonResponse::BadRequest(['error' => 'Die ausgewählten Artikel sind bereits verknüpft.']);
            if ($obj->id > 0)
                $this->gateway->Update($obj);
            else
                $this->gateway->Insert($obj);
            return JsonResponse::NoContent();
        }
        $id = (int)$this->request->getGet('id');
        $obj = $this->gateway->Get($id);
        $sql = 'SELECT id, nummer, name_de as name FROM artikel WHERE id = :id';
        $obj->mainArticle = $this->database->fetchRow($sql, ['id' => $obj->mainArticleId]);
        $obj->connectedArticle = $this->database->fetchRow($sql, ['id' => $obj->connectedArticleId]);
        if ($obj->shopId > 0) {
            $sql = 'SELECT id, bezeichnung FROM shopexport WHERE id = :id';
            $obj->shop = $this->database->fetchRow($sql, ['id' => $obj->shopId]);
        }
        return new JsonResponse((array)$obj);
    }

    public function ActionDelete(): JsonResponse
    {
        $this->gateway->Delete($this->request->getJson()->id);
        return JsonResponse::NoContent();
    }

    private function checkShopProjectRights(object $json): bool
    {
        if ($json->shop?->id == 0) {
            return true;
        }
        $sql = 'SELECT projekt FROM shopexport WHERE id = :id';
        $projektId = $this->database->fetchValue($sql, ['id' => $json->shop->id]);
        return $this->app->erp->UserProjektRecht($projektId);
    }

    private function jsonToCrossSellingArticle(object $json): CrossSellingArticle
    {
        return new CrossSellingArticle(
            CrossSellingType::from($json->type),
            $json->mainArticle->id,
            $json->connectedArticle->id,
            $json->active ?? false,
            $json->bidirectional ?? false,
            $json->shop?->id ?? 0,
            $json->sort ?? 0,
            id: $json->id,
        );
    }
}
