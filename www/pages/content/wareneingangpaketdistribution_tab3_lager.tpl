<form action="" enctype="multipart/form-data" method="post" name="eprooform" id="eprooform" >
  <br>
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td>
          [MESSAGE]
          <br><br>
          <table width="60%" style="background-color: #fff; border: solid 1px #000;" align="center">
            <tr>
              <td align="center">
                <br>
                <table height="200" border="0" width="450">
                <tr valign="top"><td><b>{|Artikel|}:</b></td><td><u>[NAME]</u></td></tr>
                <tr valign="top"><td><b>{|Nummer|}:</b></td><td>[NUMMER]</td></tr>
                [BILDERFASSENSTART]
                  <tr valign="top"><td><br></td><td align="center"></td></tr>
                  <tr valign="top"><td><b>{|Bild erfassen|}:</b></td><td> <input name="wareneingangartikelbild" type="file" /></td></tr>
                  <tr valign="top"><td><b>{|Bildtyp|}:</b></td><td><select name="bildtyp">[BILDTYPEN]</select></td></tr>
                [BILDERFASSENENDE]
                <tr valign="top"><td><br></td><td align="center"></td></tr>
                [SHOWIMGSTART]<tr valign="top"><td><b>{|Bild|}:</b></td><td><img src="index.php?module=dateien&action=send&id=[DATEI]" width="200"></td></tr> [SHOWIMGEND]
                <tr valign="top"><td><br></td><td align="center"></td></tr>
                <!--<tr valign="top"><td><b>{|Bemerkung|}:</b></td><td><textarea cols="35" rows="2" name="bemerkung">[BEMERKUNG]</textarea>-->
                </td></tr>
                <tr valign="top"><td><br></td><td align="center"></td></tr>

                <tr valign="top"><td nowrap><b>{|Anzahl Menge|}:</b></td><td>
                <input type="radio" name="anzahlauswahl" checked value="fix">&nbsp;<input type="text" size="5" name="anzahl_fix" value="[MENGE]" readonly>&nbsp;[ANZAHLAENDERN]<!--[ETIKETTENDRUCKEN]-->
                <!--(VPE: [VPE]).--><br>
                [SHOWANZAHLSTART]<input type="radio" name="anzahlauswahl" value="dyn">&nbsp;<input type="text" size="5" name="anzahl_dyn" value="[ANZAHL]">&nbsp;{|Anzahl Etiketten weil anders geliefert|}
                  </td></tr> [SHOWANZAHLENDE]
                [ETIKETTENDRUCKENSTART]<tr valign="top"><td><br></td><td align="center"></td></tr>
                <tr valign="top"><td><b>{|Etiketten|}:</b></td><td><select name="etiketten">[ETIKETTEN]</select></td></tr>[ETIKETTENDRUCKENENDE]
                <tr valign="top"><td><br></td><td align="center"></td></tr>
                [SHOWMHDSTART]<tr valign="top"><td><b style="color:red">{|MHD|}:</b></td><td><input type="text" value="[MHDFRM]" name="mhd" id="mhd">&nbsp;<br><i>({|Mindesthaltbarkeitsdatum|})</i></td></tr>
                <tr valign="top"><td><br></td><td align="center"></td></tr>[SHOWMHDEND]
                [SHOWCHRSTART]<tr valign="top"><td><b style="color:red">{|Charge|}:</b></td><td><input type="text" value="[CHARGEFRM]" name="charge" id="charge">&nbsp;<br><i>(Chargennummer von Hersteller)</i></td></tr>
                <tr valign="top"><td>{|Bemerkung|}:</td><td><input type="text" name="chargesnmhdbemerkung" value="[CHARGESNMHDBEMERKUNG]" id="chargesnmhdbemerkung" style="width:200px">&nbsp;<br><i>({|Infos zur Charge|})</i></td></tr>
                <tr valign="top"><td><br></td><td align="center"></td></tr>
                [SHOWCHREND]
                [SHOWSRNSTART]<tr valign="top"><td><b style="color:red">{|Seriennummern|}:</b></td><td><input type="button" onclick="seriennummern_assistent([MENGE])" value="Assistent verwenden"><br>[SERIENNUMMERN]<i>({|Pro Artikel eine Nummer|})</i></td></tr>
                <tr valign="top"><td><br></td><td align="center"></td></tr> [SHOWSRNEND]

                <tr valign="top"><td><b>{|Standardlager|}:</b></td><td><span id="standardlager">[STANDARDLAGER]</span><br><br></td></tr>
                <tr valign="top"><td><label for="lager"><b>{|Einlagern in|}:</b></label></td><td><input type="text" name="lager" id="lager" [LAGERPLACEHOLDER] value="[LAGER]" /></td></tr>
                  <!--<tr valign="top"><td></td><td><input type="text" name="lagerscan" id="lagerscan" placeholder="Lagerplatz scannen"></td></tr>
                <tr valign="top"><td><b>{|Einlagern in|}:</b></td><td><select name="lager" id="lager">[LAGER]</select>-->
                </td></tr>
                [DISPLAY_WARENEINGANG_RMA_HOOK1]
                <tr valign="top"><td><br></td><td align="left"><br>
                    <input type="submit" name="submit" value="[TEXTBUTTON]" />&nbsp;<input type="button" onclick="window.location.href='index.php?module=wareneingang&action=distriinhalt&id=[ID]'"  value="{|Abbrechen|}" /></td></tr>
                </table>
                <br>
                <br>
              </td>
            </tr>
          </table>
        <br><br>
        </td>
      </tr>
<!--
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="button" onclick="window.location.href='index.php?module=wareneingang&action=distriinhalt&id=[ID]'"
      value="Zur&uuml;ck" />
    <input type="submit" name="submit" value="[TEXTBUTTON]" /></td>
    </tr>
-->
    </tbody>
  </table>
</form>

<script type="text/javascript">
 var firstsubmit = false;

  $(document).ready(function() {
    $( "#eprooform" ).submit(function( event ) {
      if(firstsubmit)
      {
        event.preventDefault();
        return false;
      }
      firstsubmit = true;
      return true;
    });
    $('#standardlager').on('click',function(){
      if($(this).html()+'' != '' && $(this).html() != 'nicht definiert') {
        $('#lager').val($(this).html());
      }
    });
  });

  [SERIENNUMMERNENTERJUMP]


  document.getElementById('lagerscan').addEventListener('keypress', function(event) {
    if(event.keyCode == 13){
      event.preventDefault();
      Lagergescannt();
    }
  });


  function Lagergescannt(){
        
    $.ajax({
      url: 'index.php?module=wareneingang&action=distrietiketten&cmd=scan',
      data: {
        //Alle Felder die fürs editieren vorhanden sind
        elagerscan: $('#lagerscan').val()                      
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        App.loading.close();
        if (data.status == 1) {
          var inlistevorhanden = false;

          for (var i=0; i<document.getElementById('lager').options.length; i++){ 
            if(document.getElementById('lager').options[i].value == data.id){ 
              inlistevorhanden = true;
              break;
            }
          }

          if(inlistevorhanden){
            $('#lager').val(data.id);
          }
          else {
            if(typeof data.kurzbezeichnung != 'undefined') {
              $('#lager').append('<option value="'+data.id+'">'+data.kurzbezeichnung+'</option>');
              $('#lager').val(data.id);
            }
            else {
              alert('Regal nicht in Lagerplatzliste gefunden');
            }
          }
        } else {
          alert(data.statusText);
        }
      }
    });
  }
</script>
