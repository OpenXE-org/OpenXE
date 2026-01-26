<?php
/*
 * SPDX-FileCopyrightText: 2025 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

use Xentral\Components\Http\JsonResponse;
use Xentral\Components\Http\Request;
use Xentral\Modules\Country\Gateway\CountryGateway;
use Xentral\Modules\Country\Service\CountryService;
use Xentral\Modules\Lieferschwelle\LieferschwelleGateway;

class Lieferschwelle
{
    private ApplicationCore $app;
    private LieferschwelleGateway $gateway;
    private CountryService $countryService;
    private Request $request;

    public const MODULE_NAME = 'Lieferschwelle';

    public function __construct(ApplicationCore $app, bool $intern = false)
    {
        $this->app = $app;
        if ($intern) {
            return;
        }

        if (!$this->app instanceof Application) {
            return;
        }
        $this->gateway = $this->app->Container->get('LieferschwelleGateway');
        $this->request = $this->app->Container->get('Request');
        $this->countryService = $this->app->Container->get('CountryService');

        $this->app->ActionHandlerInit($this);
        $this->app->ActionHandler('list', 'ActionList');
        $this->app->ActionHandler('add', 'ActionEdit');
        $this->app->ActionHandler('edit', 'ActionEdit');
        $this->app->ActionHandler('delete', 'ActionDelete');
        $this->app->ActionHandlerListen($app);
    }

    private function createMenu(): void
    {
        $this->app->erp->MenuEintrag('index.php?module=lieferschwelle&action=list', '&Uuml;bersicht');
    }

    public function Install() {}

    public function TableSearch(&$app, $name, $erlaubtevars): array
    {
        switch ($name) {
            case 'lieferschwelle_list':
                $heading = ['Ursprungsland', 'Empf&auml;ngerland', 'USt-ID', 'Aktiv', 'Men&uuml;'];
                $width = ['20%', '20%', '20%', '10%', '1%']; // Fill out manually later
                $findcols = ['l.ursprungsland', 'l.empfaengerland', 'l.ustid', null, null];
                $searchsql = ['l.ursprungsland', 'l.empfaengerland', 'l.ustid'];
                $menu = '<table><tr><td nowrap>'
                    . "<img class=\"vueAction\" data-action=\"edit\" data-id=\"%value%\" src=\"./themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\">&nbsp;"
                    . "<img class=\"vueAction\" data-action=\"delete\" data-id=\"%value%\" src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\">"
                    . '</td></tr></table>';
                $sql = 'SELECT SQL_CALC_FOUND_ROWS l.id, l.ursprungsland, l.empfaengerland, l.ustid, IF(l.verwenden, \'ja\', \'nein\'), l.id 
                        FROM lieferschwelle l';
                $where = '1';
                $count = "SELECT count(DISTINCT id) FROM lieferschwelle WHERE $where";
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
        $this->app->YUI->TableSearch('TAB1', 'lieferschwelle_list', 'show', '', '', basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', 'crossselling_list.tpl');
    }

    public function ActionEdit(): JsonResponse
    {
        if ($this->request->getMethod() == 'POST') {
            $json = $this->request->getJson();
            $obj = $this->jsonToObject($json);
            if ($this->gateway->Exists($obj))
                return JsonResponse::BadRequest(['error' => 'Es existiert bereits ein Datensatz für die ausgewählte Länderkombination.']);
            if ($obj->id > 0)
                $this->gateway->Update($obj);
            else
                $this->gateway->Insert($obj);
            return JsonResponse::NoContent();
        }
        $id = (int)$this->request->getGet('id');
        $obj = $this->gateway->Get($id);
        $laender = $this->app->erp->GetSelectLaenderliste();
        $obj->destinationCountry = ['isoAlpha2' => $obj->destinationCountryIso, 'nameGerman' => $laender[$obj->destinationCountryIso]];
        if ($obj->originCountryIso !== null) {
            $obj->originCountry = ['isoAlpha2' => $obj->originCountryIso, 'nameGerman' => $laender[$obj->originCountryIso]];
        }
        return new JsonResponse((array)$obj);
    }

    public function ActionDelete(): JsonResponse
    {
        $this->gateway->Delete($this->request->getJson()->id);
        return JsonResponse::NoContent();
    }

    private function jsonToObject(object $json): \Xentral\Modules\Lieferschwelle\Data\Lieferschwelle
    {
        return new \Xentral\Modules\Lieferschwelle\Data\Lieferschwelle(
            $json->destinationCountry->isoAlpha2,
            $json->originCountry->isoAlpha2 ?? null,
            $json->ustId,
            $json->active ?? false,
            id: $json->id,
        );
    }
}
