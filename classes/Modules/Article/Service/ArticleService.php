<?php

namespace Xentral\Modules\Article\Service;
use ApplicationCore;
use Xentral\Components\Database\Database;

class ArticleService
{
    private ApplicationCore $app;

    public function __construct(ApplicationCore $app)
    {
        $this->app = $app;
    }

    function CopyArticle(int $id, bool $purchasePrices, bool $sellingPrices, bool $files, bool $properties,
        bool $instructions, bool $partLists, bool $customFields, string $newArticleNumber = '')
    {
        $newArticleNumber = $this->app->DB->real_escape_string($newArticleNumber);
        $this->app->DB->MysqlCopyRow('artikel','id',$id);

        $idnew = $this->app->DB->GetInsertID();

        $steuersatz = $this->app->DB->Select("SELECT steuersatz FROM artikel WHERE id = '$id' LIMIT 1");
        if($steuersatz == ''){
            $steuersatz = -1.00;
            $this->app->DB->Update("UPDATE artikel SET steuersatz = '$steuersatz' WHERE id = '$idnew' LIMIT 1");
        }

        $this->app->DB->Update("UPDATE artikel SET nummer='$newArticleNumber', matrixprodukt = 0 WHERE id='$idnew' LIMIT 1");
        if($this->app->DB->Select("SELECT variante_kopie FROM artikel WHERE id = '$id' LIMIT 1"))
            $this->app->DB->Update("UPDATE artikel SET variante = 1, variante_von = '$id' WHERE id = '$idnew' LIMIT 1");

        if($partLists){
            // wenn stueckliste
            $stueckliste = $this->app->DB->Select("SELECT stueckliste FROM artikel WHERE id='$id' LIMIT 1");
            if($stueckliste==1)
            {
                $artikelarr = $this->app->DB->SelectArr("SELECT * FROM stueckliste WHERE stuecklistevonartikel='$id'");
                $cartikelarr = $artikelarr?count($artikelarr):0;
                for($i=0;$i<$cartikelarr;$i++)
                {
                    $sort = $artikelarr[$i]['sort'];
                    $artikel = $artikelarr[$i]['artikel'];
                    $referenz = $artikelarr[$i]['referenz'];
                    $place = $artikelarr[$i]['place'];
                    $layer = $artikelarr[$i]['layer'];
                    $stuecklistevonartikel = $idnew;
                    $menge = $artikelarr[$i]['menge'];
                    $firma = $artikelarr[$i]['firma'];

                    $this->app->DB->Insert("INSERT INTO stueckliste (id,sort,artikel,referenz,place,layer,stuecklistevonartikel,menge,firma) VALUES
            ('','$sort','$artikel','$referenz','$place','$layer','$stuecklistevonartikel','$menge','$firma')");
                }
            }
        }

        if($purchasePrices){
            $einkaufspreise = $this->app->DB->SelectArr("SELECT id FROM einkaufspreise WHERE artikel = '$id'");
            if($einkaufspreise){
                foreach($einkaufspreise as $preis){
                    $neuereinkaufspreis = $this->app->DB->MysqlCopyRow("einkaufspreise", "id", $preis['id']);
                    $this->app->DB->Update("UPDATE einkaufspreise SET artikel = '$idnew' WHERE id = '$neuereinkaufspreis' LIMIT 1");
                }
            }
        }

        if($sellingPrices){
            $verkaufspreise = $this->app->DB->SelectArr("SELECT id FROM verkaufspreise WHERE artikel = '$id'");
            if($verkaufspreise){
                foreach($verkaufspreise as $preis){
                    $neuerverkaufspreis = $this->app->DB->MysqlCopyRow("verkaufspreise", "id", $preis['id']);
                    $this->app->DB->Update("UPDATE verkaufspreise SET artikel = '$idnew' WHERE id = '$neuerverkaufspreis' LIMIT 1");
                }
            }
        }

        if($files){
            $dateien = $this->app->DB->SelectArr("SELECT DISTINCT datei FROM datei_stichwoerter WHERE parameter = '$id' AND objekt = 'Artikel'");
            $datei_stichwoerter = $this->app->DB->SelectArr("SELECT id,datei FROM datei_stichwoerter WHERE parameter = '$id' AND objekt = 'Artikel'");

            if($dateien){
                foreach($dateien as $datei){
                    $titel = $this->app->DB->Select("SELECT titel FROM datei WHERE id='".$datei['datei']."' LIMIT 1");
                    $beschreibung = $this->app->DB->Select("SELECT beschreibung FROM datei WHERE id='".$datei['datei']."' LIMIT 1");
                    $nummer = $this->app->DB->Select("SELECT nummer FROM datei WHERE id='".$datei['datei']."' LIMIT 1");
                    $name = $this->app->DB->Select("SELECT dateiname FROM datei_version WHERE datei='".$this->app->DB->real_escape_string($datei['datei'])."' ORDER by version DESC LIMIT 1");
                    $ersteller = $this->app->User->GetName();
                    $tmpnewdateiid = $this->app->erp->CreateDatei($name,$titel,$beschreibung,$nummer,$this->app->erp->GetDateiPfad($datei['datei']),$ersteller);
                    $datei_mapping[$datei['datei']] = $tmpnewdateiid;
                }
            }

            if($datei_stichwoerter){
                foreach($datei_stichwoerter as $datei){
                    $neuesstichwort = $this->app->DB->MysqlCopyRow("datei_stichwoerter", "id", $datei['id']);
                    $newdatei = $datei_mapping[$datei['datei']];
                    $this->app->DB->Update("UPDATE datei_stichwoerter SET datei='$newdatei', parameter = '$idnew', objekt = 'Artikel' WHERE id = '$neuesstichwort' LIMIT 1");
                }
            }
        }

        if($properties){
            $aeigenschaften = $this->app->DB->SelectArr("SELECT id FROM artikeleigenschaftenwerte WHERE artikel = '$id'");
            if($aeigenschaften){
                foreach($aeigenschaften as $eigenschaft){
                    $this->app->DB->MysqlCopyRow("artikeleigenschaftenwerte", "id", $eigenschaft['id'], Array('artikel' => $idnew));
                }
            }
        }

        if($instructions){
            $arbeitsanweisungen = $this->app->DB->SelectArr("SELECT id FROM artikel_arbeitsanweisung WHERE artikel = '$id'");
            if($arbeitsanweisungen){
                foreach($arbeitsanweisungen as $anweisung){
                    $neue_anweisung = $this->app->DB->MysqlCopyRow("artikel_arbeitsanweisung", "id", $anweisung['id']);
                    $this->app->DB->Update("UPDATE artikel_arbeitsanweisung SET artikel = '$idnew' WHERE id = '$neue_anweisung' LIMIT 1");
                }
            }
        }

        if($customFields){
            $freifelderuebersetzungen = $this->app->DB->SelectArr("SELECT id FROM artikel_freifelder WHERE artikel = '$id'");
            if($freifelderuebersetzungen){
                $this->app->DB->Insert("INSERT INTO artikel_freifelder (artikel, sprache, nummer, wert) SELECT '$idnew', sprache, nummer, wert FROM artikel_freifelder WHERE artikel = '$id'");
            }
        }

        return $idnew;
    }
}
