<?php

/*
 * Copyright (c) 2026 OpenXE project
 */
class Auftrag_produktion extends Produktion {

    function AuftragProduktion(int $auftrag_id) : array {

        $auftrag = $this->app->DB->SelectArr("SELECT adresse, projekt, belegnr FROM auftrag WHERE id = '$auftrag_id'")[0];
        $produktionen = array();

        foreach ($this->app->DB->SelectArr("SELECT artikel, menge FROM auftrag_position WHERE auftrag = '$auftrag_id'") as $position) {

            $stueckliste = $this->app->DB->SelectArr("SELECT artikel, menge FROM stueckliste WHERE stuecklistevonartikel = ".$position['artikel']);
            if (empty($stueckliste)) {
                continue;
            }

            $produktion_id = $this->app->DB->InsertArr('produktion','id',array('status' => 'angelegt', 'datum' => date("Y-m-d"), 'adresse' => $auftrag['adresse'], 'projekt' => $auftrag['projekt'], 'auftragid' => $auftrag_id, 'internebezeichnung' => 'Auftrag '.$auftrag['belegnr']));

            $position_values = array();
            foreach ($stueckliste as $value) {
                $position_values[] = "($produktion_id, ".$value['artikel'].", ".($value['menge']*$position['menge']).", 0, '')";
            }

            $this->app->DB->Update("INSERT INTO produktion_position (produktion, artikel, menge, stuecklistestufe, projekt) VALUES ( $produktion_id, ".$position['artikel'].", ".$position['menge'].", 1, '".$auftrag['projekt']."'), ".implode(',',$position_values));
            $this->ProtokollSchreiben($produktion_id,"Produktion geplant (".$position['menge'].")");
            $produktionen[] = $produktion_id;
        }

        return($produktionen);
    }
}
