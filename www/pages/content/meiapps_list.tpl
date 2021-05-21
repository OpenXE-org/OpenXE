<script type="text/javascript">
$(function() {
    
   $("input[name='speichern']").click(function(e) {
      result=0;
      //ERSTER PARAMETER BEI AJAXVALIDATOR IST FELDNAME, ZWEITER DIE REGEL
      if($('input[name=kunde]:checked').val()=="bestehend")
        if(AjaxValidator('#adresse','kunde')) result++;

      if(AjaxValidator('#mitarbeiter','mitarbeiter')) result++;

      if($('input[name=ziel]:checked').val()=="rechnung")
        if(AjaxValidator('#artikel','artikel')) result++;

      if($('input[name=ziel]:checked').val()=="auftrag")
        if(AjaxValidator('#artikel','artikel')) result++;

      if($('input[name=ziel]:checked').val()=="rechnung")
        if(AjaxValidator('#rechbelegnr', 'rechnung')) result++;

      if($('input[name=ziel]:checked').val()=="auftrag")
        if(AjaxValidator('#aufbelegnr', 'auftrag')) result++;

      if(result>0)
        return false;
    });

});
</script>



<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]

  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-sm-height">
  <div class="inside inside-full-height">

    <fieldset>
      <legend>{|MeiApps Import|}</legend>
        <table class="mkTableFormular" width="100%">
          <tr>
            <td>XML-Datei:</td>
            <td>
              <form id="meiappsform" action="" method="post" enctype="multipart/form-data">
                <input type="file" name="meiappsxml" onChange="this.form.submit()">
              </form>
            </td>
            <td align="right">
              <form id="meiappsform2" action="" method="post" enctype="multipart/form-data">
                <input type="submit" name="speichern" value="Speichern">
            </td>
          </tr>
        </table>
    </fieldset>
  </div>
  </div>
  </div>
  </div>


  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-4 col-md-height">
  <div class="inside inside-full-height">

  <fieldset>
    <legend>{|Aktion|}</legend>
      <table class="mkTableFormular">
        <tr>
          <td>Kunde:</td>
          <td>
            <input type="radio" name="kunde" checked value="bestehend">bestehenden<br />
            <input type="radio" name="kunde" value="neu">neu anlegen
          </td>
        </tr>
        <tr>
          <td>Ziel:</td>
          <td>
            <input type="radio" name="ziel" checked value="zeitkonto">Zeitkonto<br />
            <!--<input type="radio" name="ziel" value="arbeitsnachweis">Arbeitsnachweis<br>-->
            <input type="radio" name="ziel" value="rechnung">Rechnung<br />
            <input type="radio" name="ziel" value="auftrag">Auftrag<br />
            <input type="checkbox" name="checkboxzeiterfassung" value="1">mit Zeiterfassung
          </td>
        </tr>
      </table>
  </fieldset>

  <fieldset>
    <legend>{|PDF|}</legend>
      <table class="mkTableFormular">
        <tr>
          <td>Optional PDF:</td>
          <td><input type="file" size="30" name="meiappspdf"></td>
        </tr>
        <tr>
          <td></td>
        </tr>
      </table>
  </fieldset>

  <fieldset>
    <legend>{|Kunde|}</legend>
    <table>
      <tr class="gruppebestehend">
        <td width="150">Kunde:</td>
        <td>
          <input type="text" size="30" name="adresse" id="adresse" value="[ADRESSE]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Name:</td>
        <td>
          <input type="text" name="name" size="30" value="[NAME]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Ansprechpartner:</td>
        <td>
          <input type="text" name="ansprechpartner" size="30" value="[ANSPRECHPARTNER]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Abteilung:</td>
        <td>
          <input type="text" name="abteilung" size="30" value="[ABTEILUNG]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Adresszusatz:</td>
        <td>
          <input type="text" name="adresszusatz" size="30" value="[ADRESSZUSATZ]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Strasse:</td>
        <td>
          <input type="text" name="strasse" size="30" value="[STRASSE]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Ort:</td>
        <td>
          <input type="text" name="ort" size="30" value="[ORT]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Land:</td>
        <td>
          <select name="land">[LAND]</select>
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Telefon:</td>
        <td>
          <input type="text" name="telefon" size="30" value="[TELEFON]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Mobil:</td>
        <td>
          <input type="text" name="mobil" size="30" value="[MOBIL]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">Telefax:</td>
        <td>
          <input type="text" name="telefax" size="30" value="[TELEFAX]">
        </td>
      </tr>
      <tr class="gruppeneu">
        <td width="150">E-Mail:</td>
        <td>
          <input type="text" name="email" size="30" value="[EMAIL]">
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset class="rechnung_auftrag">
    <legend>Artikel für Stundensatz</legend>
    <table>
      <tr>
        <td width="150">Artikel-Nr:</td>
        <td>
          <input type="text" size="30" name="artikel" id="artikel">
        </td>
      </tr>
    </table>
       
  </fieldset>

  <fieldset>
    <legend>{|Buchen auf Mitarbeiter|}</legend>
    <table>
      <tr>
        <td width="150">Mitarbeiter:</td>
        <td>
          <input type="text" size="30" name="mitarbeiter" id="mitarbeiter" value="[MITARBEITER]">
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset class="rechnung rechnung_auftrag">
    <legend>{|Buchen auf Rechnung|}</legend>
    <table>
      <tr>
        <td width="150">Beleg-Nr:</td>
        <td>
          <input type="text" size="30" name="rechbelegnr" id="rechbelegnr">
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset class="auftrag rechnung_auftrag">
    <legend>{|Buchen auf Auftrag|}</legend>
    <table>
      <tr>
        <td width="150">Beleg-Nr:</td>
        <td>
          <input type="text" size="30" name="aufbelegnr" id="aufbelegnr">
        </td>
      </tr>
    </table>
  </fieldset>

  </div>
  </div>
  <div class="col-xs-12 col-md-8 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
      <legend>{|Arbeitszeiten|}</legend>

      [HIDDENFIELDS]

      <table class="mkTable">
        <tr>
          <th></th>
          <th>Nr.</th>
          <th>Von</th>
          <th>Bis</th>
          <th>Tätigkeit</th>
          <th>Summe Zeit</th>
          <th>Summe Abrechnen</th>
        </tr>
      [ZEITTABELLE]
      </table>
      <br />
      <legend>{|Artikel|}</legend>
      <table class="mkTable">
        <tr>
          <th></th>
          <th>Nr.</th>
          <th>Artikelnr.</th>
          <th>Name</th>
          <th>Artikel vorhanden</th>
          <th>Menge</th>
        </tr>
        [ARTIKELTABELLE]
      </table>

    </fieldset>
  </div>
  </div>

  </div>
  </div>



  <div class="row rechnung_auftrag">
  <div class="row-height">
  <div class="col-xs-12 col-md-4 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
    <legend>{|Arbeitszeit|}</legend>
      <table class="mkTableFormular">
        <tr>
          <td>Position Zeiterfassung</td>
          <td>
            <input type="radio" name="taetigkeit" checked value="ohne">ohne T&auml;tigkeitsbeschreibung<br>
            <input type="radio" name="taetigkeit" value="mit">mit T&auml;tigkeitsbeschreibung
          </td>
        </tr>
      </table>     
    </fieldset>
  </div>
  </div>

  <div class="col-xs-12 col-md-4 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
    <legend>Optional Artikel f&uuml;r Rechnung</legend>
      <table>
        <tr>
          <td width="50">
            Menge
          </td>
          <td>
            Artikel
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge1" name="menge1" size="3" />
          </td>
          <td>
            <input type="text" id="artikel1" name="artikel1" size="50" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge2" name="menge2" size="3" />
          </td>
          <td>
            <input type="text" id="artikel2" name="artikel2" size="50" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge3" name="menge3" size="3" />
          </td>
          <td>
            <input type="text" id="artikel3" name="artikel3" size="50" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge4" name="menge4" size="3" />
          </td>
          <td>
            <input type="text" id="artikel4" name="artikel4" size="50" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge5" name="menge5" size="3" />
          </td>
          <td>
            <input type="text" id="artikel5" name="artikel5" size="50" />
          </td>
        </tr>
      </table>

    </fieldset>
  </div>
  </div>


  <div class="col-xs-12 col-md-4 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
    <legend>Optional Artikel f&uuml;r Rechnung</legend>
      <table>
        <tr>
          <td width="50">
            Menge
          </td>
          <td>
            Artikel
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge1" name="menge6" size="3" />
          </td>
          <td>
            <input type="text" id="artikel1" name="artikel6" size="50" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge2" name="menge7" size="3" />
          </td>
          <td>
            <input type="text" id="artikel2" name="artikel7" size="50" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge3" name="menge8" size="3" />
          </td>
          <td>
            <input type="text" id="artikel3" name="artikel8" size="50" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge4" name="menge9" size="3" />
          </td>
          <td>
            <input type="text" id="artikel4" name="artikel9" size="50" />
          </td>
        </tr>
        <tr>
          <td>
            <input type="text" id="menge5" name="menge10" size="3" />
          </td>
          <td>
            <input type="text" id="artikel5" name="artikel10" size="50" />
          </td>
        </tr>
      </table>

    </fieldset>
  </form>
  </div>
  </div>


  </div>
  </div>











[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>
