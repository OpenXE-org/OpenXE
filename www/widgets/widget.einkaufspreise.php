<?php
include ("_gen/widget.gen.einkaufspreise.php");

class WidgetEinkaufspreise extends WidgetGenEinkaufspreise 
{
  private $app;
  function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function Create()
  {
    if($this->app->Secure->GetPOST('gueltig_bis'))
    {
      $gueltig_bis = $this->app->erp->ReplaceDatum(true,$this->app->Secure->GetPOST('gueltig_bis'),"");
      if(!empty($gueltig_bis) && $gueltig_bis != '0000-00-00')
      {
        $check = $this->app->DB->Select("SELECT '$gueltig_bis' < date(now())");
        if($check)
        {
          $artikel = (int)$this->app->Secure->GetGET("id");
          $tagespreise = $this->app->DB->Select("SELECT tagespreise FROM artikel WHERE id = '$artikel' LIMIT 1");
          if($tagespreise)
          {
            $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Das Anlegen von alten Tagespreisen ist nicht erlaubt!</div>");
            $this->form->PrintForm();
            return;
          }
        }
      }
    }
    parent::Create();
  }
  
  function Edit()
  {
    if($this->app->Secure->GetPOST('gueltig_bis'))
    {
      $gueltig_bis = $this->app->erp->ReplaceDatum(true,$this->app->Secure->GetPOST('gueltig_bis'),"");
      if(!empty($gueltig_bis) && $gueltig_bis != '0000-00-00')
      {
        $check = $this->app->DB->Select("SELECT '$gueltig_bis' < date(now())");
        if($check)
        {
          $id = (int)$this->app->Secure->GetGET("id");
          $artikel = $this->app->DB->Select("SELECT artikel FROM einkaufspreise WHERE id='$id' LIMIT 1");
          $tagespreise = $this->app->DB->Select("SELECT tagespreise FROM artikel WHERE id = '$artikel' LIMIT 1");
          if($tagespreise)
          {
            $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Das &auml;ndern von alten Tagespreisen ist nicht erlaubt!</div>");
            $this->form->PrintForm();
            return;
          }
        }
      }
    }
    if($this->app->Secure->GetPOST('submit'))
    {
      $id = (int)$this->app->Secure->GetGET("id");
      $this->app->erp->ObjektProtokoll('einkaufspreise', $id, 'einkaufspreise_edit', 'Einkaufspreis editiert');
      $this->app->DB->Update("UPDATE einkaufspreise SET logdatei = now() WHERE id = '$id' AND logdatei = '0000-00-00 00:00:00' LIMIT 1");
    }
    parent::Edit();
  }


  
  function ExtendsForm()
  {
    $this->form->ReplaceFunction("adresse",$this,"ReplaceLieferant");
    $this->form->ReplaceFunction("vpe",$this,"ReplaceVPE");
    $this->form->ReplaceFunction("ab_menge",$this,"ReplaceAb_menge");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("gueltig_bis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("preis_anfrage_vom",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("rahmenvertrag_von",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("rahmenvertrag_bis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("datum_lagerlieferant",$this,"ReplaceDatum");

    $this->form->ReplaceFunction("preis",$this,"ReplaceBetrag");

    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->app->YUI->AutoComplete("adresse","lieferant");


   
    $this->app->YUI->DatePicker("preis_anfrage_vom"); 
    $this->app->YUI->DatePicker("gueltig_bis"); 
    $this->app->YUI->DatePicker("datum_lagerlieferant"); 

    $this->app->YUI->DatePicker("rahmenvertrag_von"); 
    $this->app->YUI->DatePicker("rahmenvertrag_bis"); 

    $action = $this->app->Secure->GetGET("action");
    if($action=="einkauf")
    { 
      // liste zuweisen
      $pid = $this->app->Secure->GetGET("id");
      $this->app->Secure->POST["artikel"]=$pid;
      $field = new HTMLInput("artikel","hidden",$pid);
      $this->form->NewField($field);
    }

    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);
    
    $field = new HTMLSelect("waehrung",0,"waehrung");
    $field->AddOption('EUR','EUR');
    $field->AddOption('USD','USD');
    $field->AddOption('CAD','CAD');
    $field->AddOption('CHF','CHF');
    $field->AddOption('GBP','GBP');
    $this->form->NewField($field);

		$this->app->Tpl->Set('VPEPREIS',"<input type=\"button\" class=\"button button-secondary\" value=\"Einzelstückpreis berechnen\" onclick=\"var wert = prompt('Gesamtpreis VPE:');
				wert = wert.replace(',', '.'); document.getElementById('preis').value=parseFloat(wert/document.getElementById('vpe').value.replace(',','.')).toFixed(8); document.getElementById('ab_menge').value=document.getElementById('vpe').value.replace(',','.') >= 1?1:0\">");
        
    $this->app->Tpl->Set('PREISTABELLE','<input type="button" class="button button-secondary" value="W&auml;hrungumrechnungstabelle" onclick="loadintotable();" />');
    $_waehrungen = $this->app->erp->GetWaehrungUmrechnungskurseTabelle('EUR');
    $waehrungen['EUR'] = 1;
    foreach($_waehrungen as $waehrung => $kurs)$waehrungen[$waehrung] = $kurs;
    if($_waehrungen)
    {

      foreach($waehrungen as $k => $v)$waehrung_felder[$k] = $k;
      $field = new HTMLSelect("waehrung",1,"waehrung");
      $field->AddOptionsAsocSimpleArray($waehrung_felder);
      $this->form->NewField($field);
      
    }
    
    $htmltabelle = "
    <script>
    var kurs = new Array();
    var waehrungen = new Array();
    ";
    
    $i = -1;
    foreach($waehrungen as $waehrung => $kurs)
    {
      $i++;
      $htmltabelle .= "kurs[".$i."] = ".$kurs.";\r\n";
      $htmltabelle .= "waehrungen[".$i."] = '".$waehrung."';\r\n";
    }
    
    $htmltabelle .= "
    
    function loadintotable() {
      var waehrung = $('#waehrung').val();
      if(waehrung == '')waehrung = 'EUR';
      var preis = parseFloat($('#preis').val().replace(',','.'));
      if (isNaN(preis))preis = 0;
      var titel = 'Umrechnung von '+preis+' '+waehrung;
      $('#preistabellediv').dialog({width: 1000, title:titel});
      changerunden();
    }
    
    function clickalle()
    {
      var prop = $('#auswahlalle').prop('checked');
      var waehrung = $('#waehrung').val();
      var preis = parseFloat($('#preis').val());
      var aktwaehrung = 'EUR';
      var aktind = -1;
      var aktkurs = 1;
      $.each(waehrungen, function(k,v){
        if(waehrung == v)
        {
          aktind = k;         
          $('#auswahl_'+k).prop('checked',false);
        } else {
          $('#auswahl_'+k).prop('checked',prop);
        }
      });
    }
    
    function anlegen()
    {
      var str = '';
      var gr = $('#gruppe').val();
      var adr = $('#adresse').val();
      var ar = $('#art').val();
      var artnr = $('#kundenartikelnummer').val();
      var ab_menge = $('#ab_menge').val().replace(',','.');
      var bezlief = $('#bezeichnunglieferant').val();
      var best = $('#best').val();
      $.each(waehrungen, function(k,v){
        if($('#auswahl_'+k).prop('checked'))
        {
          if(str != '')str = str + ';';
          str = str + $('#waehrung_'+k).html()+':'+$('#preis_'+k).val();
        }
      });
      if(str != '')
      {
        
          $.ajax({
    url: 'index.php?module=artikel&action=".$this->app->Secure->GetGET('action')."&id=".$this->app->Secure->GetGET('id')."',
    type: 'POST',
    dataType: 'json',
    data: {dat :str, newpreis:1, gruppe:gr, adresse:adr, art:ar, kundenartikelnummer:artnr, menge_ab:ab_menge, bezeichnunglieferant :bezlief, bestellnummer: best}
    }).done( function(data) {
      if (typeof data == 'undefined' || data == null || typeof data.status == 'undefined' || data.status == 0)
      {
        $('#preiserror').html('<div class=\"error\">Fehler beim Speichern der Einkaufspreise!</div>');
      } else {
        $('#preistabellediv').dialog('close');
        $('#standard').parents('.ui-tabs-panel').first().before('<div class=\"error2\" id=\"umrechnungsinfo\">Umrechnungspreise angelegt</div>');
        setTimeout(
          function(){
            $('#umrechnungsinfo').remove();
          }
          ,5000
        );
      }
    }).fail( function( jqXHR, textStatus ) {
          
   });
      }
      
      
    }
    
    function changerunden()
    {
      var waehrung = $('#waehrung').val();
      var preis = parseFloat($('#preis').val().replace(',','.'));
      if (isNaN(preis))preis = 0;
      var aktwaehrung = 'EUR';
      var aktind = -1;
      var aktkurs = 1;
      var stellen = parseInt($('#stellen').val());
      if (isNaN(stellen))
      {
        stellen = 0;
      }
      if(stellen < 0)stellen = 0;
      var isstellen = $('#runden').prop('checked');
      $.each(waehrungen, function(k,v){
        if(waehrung == v)
        {
          aktwaehrung = waehrung;
        }
      });
      $.each(waehrungen, function(k,v){
        if(aktwaehrung == v)
        {
          aktind = k;
          aktkurs = kurs[aktind];
          $('#tr_'+k).css('display','none');
        } else {
          $('#tr_'+k).css('display','');
        }
      });
      
      $.each(waehrungen, function(k,v){
        if(aktwaehrung == v)
        {

        } else {
          $('#kurs_'+k).html(kurs[k]/aktkurs);
          var neuerpreis = kurs[k]/aktkurs*preis;
          if(isstellen)neuerpreis = Math.round(neuerpreis*Math.pow(10,stellen), stellen)/Math.pow(10,stellen);
          $('#preis_'+k).val( neuerpreis);
        }
      });
      
      
    }
    
    </script>
    <div id=\"preistabellediv\" style=\"display:none;\"><div id=\"preiserror\"></div>
    <table><tr><th><input type=\"checkbox\" id=\"auswahlalle\" onchange=\"clickalle();\" /></th><th>W&auml;hrung</th><th>Kurs</th><th>umgerechnter Preis</th><th></th></tr>
    ";
    $i = -1;
    foreach($waehrungen as $waehrung => $kurs)
    {
      $i++;

      $htmltabelle .= "<tr id=\"tr_".$i."\"><td><input type=\"checkbox\" id=\"auswahl_".$i."\" /></td><td><span id=\"waehrung_".$i."\">".$waehrung."</span></td><td><span id=\"kurs_".$i."\">".$kurs."</span></td><td><input type=\"text\" id=\"preis_".$i."\" value=\"\" /></td><td></td></tr>";
    }
    
    $htmltabelle .= "<tr><td></td><td></td><td><input type=\"checkbox\" id=\"runden\" onchange=\"changerunden();\" />Runden auf </td><td><input onchange=\"changerunden();\" type=\"text\" id=\"stellen\" value=\"2\" /></td><td> Stellen <input type=\"button\" id=\"anlegen\" value=\"anlegen\" onclick=\"anlegen();\" /></td></tr></table></div>";
    
    
    $this->app->Tpl->Set('PREISTABELLEPOPUP', $htmltabelle);
    
    
  }

  function ReplaceVPE($db,$value,$fromform)
  {
    $value = str_replace(',','.',$value);
		if($fromform && $value<=0)
		return 1;
		else return $value;
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }

  function ReplaceBetrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceBetrag($db,$value,$fromform);
  }

  function ReplaceLieferant($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceLieferant($db,$value,$fromform);
  }

  function ReplaceAb_menge($db,$value,$fromform)
  {
    $value = str_replace(',','.', $value);
    return $value >= 0?$value:0;
  }
  
  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT nummer, name_de as name,barcode, id FROM einkaufspreise order by nummer");
    $table->Display($this->parsetarget);
  }



  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>
