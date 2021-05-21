<style type="text/css">
  #positionaccordion h3
  {
    background-image:none;
    background-color:rgb(211,211,211);
    color:rgb(72,73,75);
  }
  #positionaccordion h3[aria-selected='true']
  {
    background-image:none;
    background-color:rgb(162, 214, 36);
    color:rgb(2, 125, 141);
  }
</style>
<script type="application/javascript">
$( document ).ready(function() {

if($('#steuersatz').val()=="")
{ 
  $('#anderersteuersatz').prop('checked', false);
  $('.steuersatz').hide();
  $("#umsatzsteuer").prop('disabled', false);
}
else
{ 
  $('.steuersatz').show();
  $('#anderersteuersatz').prop('checked', true);
  $("#umsatzsteuer").prop('disabled', 'disabled');
}


$('#anderersteuersatz').click(function() {        if (!$(this).is(':checked')) {            $('.steuersatz').hide();
            $('#steuersatz').val('');
            $("#umsatzsteuer").prop('disabled', false);
        } else {
            $('.steuersatz').show();
            $("#umsatzsteuer").prop('disabled', 'disabled');        }
    });
  $('#positionaccordion').accordion({
    collapsible:true,
    beforeActivate: function(event, ui) {
      // The accordion believes a panel is being opened
      if (ui.newHeader[ 0 ]) {
        var currHeader  = ui.newHeader;
        var currContent = currHeader.next('.ui-accordion-content');
        // The accordion believes a panel is being closed
      } else {
        var currHeader  = ui.oldHeader;
        var currContent = currHeader.next('.ui-accordion-content');
      }
      // Since we've changed the default behavior, this detects the actual status
      var isPanelSelected = currHeader.attr('aria-selected') == 'true';

      // Toggle the panel's header
      currHeader.toggleClass('ui-corner-all',isPanelSelected).toggleClass('accordion-header-active ui-state-active ui-corner-top',!isPanelSelected).attr('aria-selected',((!isPanelSelected).toString()));

      // Toggle the panel's icon
      currHeader.children('.ui-icon').toggleClass('ui-icon-triangle-1-e',isPanelSelected).toggleClass('ui-icon-triangle-1-s',!isPanelSelected);

      // Toggle the panel's content
      currContent.toggleClass('accordion-content-active',!isPanelSelected)
      if (isPanelSelected) {
        currContent.slideUp();
      }  else {
        currContent.slideDown();
      }

      return false; // Cancels the default action
    },
    create: function( event, ui ) {
      var h3s = $('#positionaccordion h3');
      if($(h3s).length > 0)
      {
        var strings = '';
        $(h3s).each(function(){
          if(strings !== '')
          {
            strings += '*|*';
          }
          strings += $(this).text();
        });
        $.ajax({
          url: 'index.php?module=gutschrift&action=positioneneditpopup&cmd=getopenaccordions',
          type: 'POST',
          dataType: 'json',
          data: {accordions: strings},
          success: function (data) {
            if(typeof data.accordions != 'undefined')
            {
              h3s = $('#positionaccordion h3');
              $(h3s).each(function(){
                var h3 = $(this);
                var found = false;
                $(data.accordions).each(function(k, v){
                  if($(h3).text() === v)
                  {
                    found = true;
                    if($(h3).attr('aria-selected')+'' != 'true')
                    {
                      $(h3).trigger('click');
                    }
                  }
                });
                if(!found && $(h3).attr('aria-selected')+'' == 'true')
                {
                  $(h3).trigger('click');
                }
              });
            }
            $('#positionaccordion h3').on('click',function()
            {
              var active = $(this).attr('aria-selected')+'';
              $.ajax({
                url: 'index.php?module=gutschrift&action=positioneneditpopup&cmd=setaccordion',
                type: 'POST',
                dataType: 'json',
                data: {name: $(this).text(),active:(active=='true'?1:0)},
                success: function (data) {

                }
              });
            });
          }
        });
      }
    }
  });
});
</script>
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]
 <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top">
        <td width="550">
<table border="0" style="padding-right:10px;" class="mkTableFormular">
            <tbody>
              <tr><td nowrap>{|Artikel-Nr|}:</td><td>[NUMMER][MSGNUMMER]</td></tr>
	      <tr><td nowrap>{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
              <tr><td>{|Beschreibung|}:</td><td>[BESCHREIBUNG][MSGBESCHREIBUNG]</td></tr>
	      <tr><td>{|Menge|}:</td><td>[MENGE][MSGMENGE]</td></tr>
	      <tr><td>{|Preis|}:</td><td>[PREIS][MSGPREIS]</td></tr>
        [VORFORMELN]
        <tr><td>{|Belege Formel Menge|}:</td><td>[FORMELMENGE][MSGFORMELMENGE]</td></tr>
        <tr><td>{|Belege Formel Preis|}:</td><td>[FORMELPREIS][MSGFORMELPREIS]</td></tr>
        [NACHFORMELN]
              <tr><td>{|Ohne Preis|}:</td><td>[OHNEPREIS][MSGOHNEPREIS]&nbsp;<i>({|kein Preis anzeigen|})</i></td></tr>
        <tr><td>{|Im PDF ausblenden|}:</td><td>[AUSBLENDEN_IM_PDF][MSGAUSBLENDEN_IM_PDF]</td></tr>
        <tr><td>{|W&auml;hrung|}:</td><td>[WAEHRUNG][MSGWAEHRUNG]&nbsp;[WAEHRUNGSBUTTON]</td></tr>

        <tr><td>{|Steuersatz|}:</td><td>[UMSATZSTEUER][MSGUMSATZSTEUER]&nbsp;

[ANDERERSTEUERSATZ][MSGANDERERSTEUERSATZ]&nbsp;individuellen Steuersatz verwenden
</td></tr>
        <tr style="display:none" class="steuersatz"><td>{|Individueller Steuersatz|}:</td><td>[STEUERSATZ][MSGSTEUERSATZ]&nbsp;in Prozent</td></tr>
        <tr><td>{|Rechtlicher Steuerhinweis|}:</td><td>
                [STEUERTEXT][MSGSTEUERTEXT]
        </td></tr>

[STARTDISABLEVERBAND]<tr><td>{|Grundrabatt|}:</td><td>[GRUNDRABATT][MSGGRUNDRABATT]</td></tr>
        <tr><td>{|Rabatt 1 - 5|}:</td><td>[RABATT1][MSGRABATT1]&nbsp;[RABATT2][MSGRABATT2]
        &nbsp;[RABATT3][MSGRABATT3]&nbsp;[RABATT4][MSGRABATT4]&nbsp;[RABATT5][MSGRABATT5]</td></tr>
        [ENDEDISABLEVERBAND]
        <tr><td>{|Rabatt festschreiben|}:</td><td>[KEINRABATTERLAUBT][MSGKEINRABATTERLAUBT]</td></tr>
        <tr><td>Rabatt[STARTDISABLEVERBAND] (wird berechnet aus Grund- und Rabatt 1 - 5)[ENDEDISABLEVERBAND]:</td><td>[RABATT][MSGRABATT]&nbsp;<i>(in Prozent z.B. 10 = 10%)</i></td></tr>

	      <tr><td>{|Einheit|}:</td><td>[EINHEIT][MSGEINHEIT]</td></tr>
	      <tr><td>{|VPE|}:</td><td>[VPE][MSGVPE]</td></tr>
	      <tr><td>{|Lieferdatum|}:</td><td>[LIEFERDATUM][MSGLIEFERDATUM][DATUM_LIEFERDATUM]&nbsp;[LIEFERDATUMKW][MSGLIEFERDATUMKW]&nbsp;KW</td></tr>
              <tr><td>Art-Nr. Kunde:</td><td>[ARTIKELNUMMERKUNDE][MSGARTIKELNUMMERKUNDE]</td></tr>
              <tr><td>{|Zolltarifnummer|}:</td><td>[ZOLLTARIFNUMMER][MSGZOLLTARIFNUMMER]</td></tr>
              <tr><td>{|Herkunftsland|}:</td><td>[HERKUNFTSLAND][MSGHERKUNFTSLAND]</td></tr>
              [FREIFELD1START]<tr><td>[FREIFELD1BEZEICHNUNG]:</td><td>[FREIFELD1][MSGFREIFELD1]</td></tr>[FREIFELD1ENDE]
              [FREIFELD2START]<tr><td>[FREIFELD2BEZEICHNUNG]:</td><td>[FREIFELD2][MSGFREIFELD2]</td></tr>[FREIFELD2ENDE]
              [FREIFELD3START]<tr><td>[FREIFELD3BEZEICHNUNG]:</td><td>[FREIFELD3][MSGFREIFELD3]</td></tr>[FREIFELD3ENDE]
              [FREIFELD4START]<tr><td>[FREIFELD4BEZEICHNUNG]:</td><td>[FREIFELD4][MSGFREIFELD4]</td></tr>[FREIFELD4ENDE]
              [FREIFELD5START]<tr><td>[FREIFELD5BEZEICHNUNG]:</td><td>[FREIFELD5][MSGFREIFELD5]</td></tr>[FREIFELD5ENDE]
              [FREIFELD6START]<tr><td>[FREIFELD6BEZEICHNUNG]:</td><td>[FREIFELD6][MSGFREIFELD6]</td></tr>[FREIFELD6ENDE]
              [FREIFELD7START]<tr><td>[FREIFELD7BEZEICHNUNG]:</td><td>[FREIFELD7][MSGFREIFELD7]</td></tr>[FREIFELD7ENDE]
              [FREIFELD8START]<tr><td>[FREIFELD8BEZEICHNUNG]:</td><td>[FREIFELD8][MSGFREIFELD8]</td></tr>[FREIFELD8ENDE]
              [FREIFELD9START]<tr><td>[FREIFELD9BEZEICHNUNG]:</td><td>[FREIFELD9][MSGFREIFELD9]</td></tr>[FREIFELD9ENDE]
              [FREIFELD10START]<tr><td>[FREIFELD10BEZEICHNUNG]:</td><td>[FREIFELD10][MSGFREIFELD10]</td></tr>[FREIFELD10ENDE]
              [FREIFELD11START]<tr><td>[FREIFELD11BEZEICHNUNG]:</td><td>[FREIFELD11][MSGFREIFELD11]</td></tr>[FREIFELD11ENDE]
              [FREIFELD12START]<tr><td>[FREIFELD12BEZEICHNUNG]:</td><td>[FREIFELD12][MSGFREIFELD12]</td></tr>[FREIFELD12ENDE]
              [FREIFELD13START]<tr><td>[FREIFELD13BEZEICHNUNG]:</td><td>[FREIFELD13][MSGFREIFELD13]</td></tr>[FREIFELD13ENDE]
              [FREIFELD14START]<tr><td>[FREIFELD14BEZEICHNUNG]:</td><td>[FREIFELD14][MSGFREIFELD14]</td></tr>[FREIFELD14ENDE]
              [FREIFELD15START]<tr><td>[FREIFELD15BEZEICHNUNG]:</td><td>[FREIFELD15][MSGFREIFELD15]</td></tr>[FREIFELD15ENDE]
              [FREIFELD16START]<tr><td>[FREIFELD16BEZEICHNUNG]:</td><td>[FREIFELD16][MSGFREIFELD16]</td></tr>[FREIFELD16ENDE]
              [FREIFELD17START]<tr><td>[FREIFELD17BEZEICHNUNG]:</td><td>[FREIFELD17][MSGFREIFELD17]</td></tr>[FREIFELD17ENDE]
              [FREIFELD18START]<tr><td>[FREIFELD18BEZEICHNUNG]:</td><td>[FREIFELD18][MSGFREIFELD18]</td></tr>[FREIFELD18ENDE]
              [FREIFELD19START]<tr><td>[FREIFELD19BEZEICHNUNG]:</td><td>[FREIFELD19][MSGFREIFELD19]</td></tr>[FREIFELD19ENDE]
              [FREIFELD20START]<tr><td>[FREIFELD20BEZEICHNUNG]:</td><td>[FREIFELD20][MSGFREIFELD20]</td></tr>[FREIFELD20ENDE]
              [FREIFELD21START]<tr><td>[FREIFELD21BEZEICHNUNG]:</td><td>[FREIFELD21][MSGFREIFELD21]</td></tr>[FREIFELD21ENDE]
              [FREIFELD22START]<tr><td>[FREIFELD22BEZEICHNUNG]:</td><td>[FREIFELD22][MSGFREIFELD22]</td></tr>[FREIFELD22ENDE]
              [FREIFELD23START]<tr><td>[FREIFELD23BEZEICHNUNG]:</td><td>[FREIFELD23][MSGFREIFELD23]</td></tr>[FREIFELD23ENDE]
              [FREIFELD24START]<tr><td>[FREIFELD24BEZEICHNUNG]:</td><td>[FREIFELD24][MSGFREIFELD24]</td></tr>[FREIFELD24ENDE]
              [FREIFELD25START]<tr><td>[FREIFELD25BEZEICHNUNG]:</td><td>[FREIFELD25][MSGFREIFELD25]</td></tr>[FREIFELD25ENDE]
              [FREIFELD26START]<tr><td>[FREIFELD26BEZEICHNUNG]:</td><td>[FREIFELD26][MSGFREIFELD26]</td></tr>[FREIFELD26ENDE]
              [FREIFELD27START]<tr><td>[FREIFELD27BEZEICHNUNG]:</td><td>[FREIFELD27][MSGFREIFELD27]</td></tr>[FREIFELD27ENDE]
              [FREIFELD28START]<tr><td>[FREIFELD28BEZEICHNUNG]:</td><td>[FREIFELD28][MSGFREIFELD28]</td></tr>[FREIFELD28ENDE]
              [FREIFELD29START]<tr><td>[FREIFELD29BEZEICHNUNG]:</td><td>[FREIFELD29][MSGFREIFELD29]</td></tr>[FREIFELD29ENDE]
              [FREIFELD30START]<tr><td>[FREIFELD30BEZEICHNUNG]:</td><td>[FREIFELD30][MSGFREIFELD30]</td></tr>[FREIFELD30ENDE]
              [FREIFELD31START]<tr><td>[FREIFELD31BEZEICHNUNG]:</td><td>[FREIFELD31][MSGFREIFELD31]</td></tr>[FREIFELD31ENDE]
              [FREIFELD32START]<tr><td>[FREIFELD32BEZEICHNUNG]:</td><td>[FREIFELD32][MSGFREIFELD32]</td></tr>[FREIFELD32ENDE]
              [FREIFELD33START]<tr><td>[FREIFELD33BEZEICHNUNG]:</td><td>[FREIFELD33][MSGFREIFELD33]</td></tr>[FREIFELD33ENDE]
              [FREIFELD34START]<tr><td>[FREIFELD34BEZEICHNUNG]:</td><td>[FREIFELD34][MSGFREIFELD34]</td></tr>[FREIFELD34ENDE]
              [FREIFELD35START]<tr><td>[FREIFELD35BEZEICHNUNG]:</td><td>[FREIFELD35][MSGFREIFELD35]</td></tr>[FREIFELD35ENDE]
              [FREIFELD36START]<tr><td>[FREIFELD36BEZEICHNUNG]:</td><td>[FREIFELD36][MSGFREIFELD36]</td></tr>[FREIFELD36ENDE]
              [FREIFELD37START]<tr><td>[FREIFELD37BEZEICHNUNG]:</td><td>[FREIFELD37][MSGFREIFELD37]</td></tr>[FREIFELD37ENDE]
              [FREIFELD38START]<tr><td>[FREIFELD38BEZEICHNUNG]:</td><td>[FREIFELD38][MSGFREIFELD38]</td></tr>[FREIFELD38ENDE]
              [FREIFELD39START]<tr><td>[FREIFELD39BEZEICHNUNG]:</td><td>[FREIFELD39][MSGFREIFELD39]</td></tr>[FREIFELD39ENDE]
              [FREIFELD40START]<tr><td>[FREIFELD40BEZEICHNUNG]:</td><td>[FREIFELD40][MSGFREIFELD40]</td></tr>[FREIFELD40ENDE]

        [STARTDISABLEMLM]<tr><td>{|MLM Punkte|}:</td><td>[PUNKTE][MSGPUNKTE]</td></tr>
        <tr><td>{|MLM Bonuspunkte|}:</td><td>[BONUSPUNKTE][MSGBONUSPUNKTE]</td></tr>
        <tr><td>{|MLM Direktpr&auml;ie|}:</td><td>[MLMDIREKTPRAEMIE][MSGMLMDIREKTPRAEMIE]</td></tr>

        [ENDEDISABLEMLM]
          </tbody></table>
        </td>
        <td width="">
          <table width="100%"><tr><td align="right"><input type="submit" value="Speichern" ></td></tr></table>
          <div id="positionaccordion">
            [ANZEIGEEINKAUFLAGER]
            <h3>{|Steuer|}</h3>
            <div class="table-responsive">
            <table>
            <tbody>
            [VORSTEUER]
            <tr><td>{|Kostenstelle|}:</td><td>[KOSTENSTELLE][MSGKOSTENSTELLE]</td></tr>
            <tr><td>{|Erl&ouml;se|}:</td><td>[ERLOESE][MSGERLOESE]</td></tr>
            <tr><td>{|festschreiben|}:</td><td>[ERLOESEFESTSCHREIBEN][MSGERLOESEFESTSCHREIBEN]</td></tr>
            [NACHSTEUER]
            </tbody>
            </table>
            </div>

            <h3>{|Bemerkung|}</h3>
            <div class="table-responsive">
            <table>
            <tbody>
            <tr><td>
            [BEMERKUNG][MSGBEMERKUNG]
            </td></tr>
            </tbody>
            </table>
            </div>

          </div>
        </td>
      </tr>
    </tbody>
  </table>
</form>
[WAEHRUNGSTABELLE]
