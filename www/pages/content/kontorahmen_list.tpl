<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<form method="post">
<div id="tabs-1">
  [MESSAGE]

  [TAB1]
   
  <fieldset>
    <legend>{|Stapelverarbeitung|}</legend>
    <input type="checkbox" id="auswahlalle" onchange="kontorahmenmarkieren();" />&nbsp;{|alle markieren|}&nbsp;
    <input type="submit" class="btnBlue" name="loeschen" id="loeschen" value="Alle markierten l&ouml;schen" />
  </fieldset>


  <!--<fieldset>
    <legend>Anlegen</legend>
    <table>
      <tr>
        <td width="40">Konto:</td><td width="170"><input type="text" name="konto" id="konto"></td>
        <td width="75">Beschriftung:</td><td width="170"><input type="text" name="beschriftung" id="beschriftung"></td>
        <td width="23">Art:</td><td width="145"><select name="art" id="art" style="width:11em">
                            <option value="0"></option>
                            <option value="1">Aufwendungen</option>
                            <option value="2">Erl&ouml;se</option>
                            <option value="3">Geldtransit</option>
                            <option value="9">Saldo</option>
                          </select></td>
        <td>Nicht sichtbar:</td><td width="40"><input type="checkbox" name="nichtsichtbar" id="nichtsichtbar" value="1"></td>
        <td><input type="submit" name="anlegen" id="anlegen" value="Anlegen"></td>
      </tr>
    </table>
  </fieldset>-->
  
  [TAB1NEXT]


    
    <!--<input type="button" class="check" onclick="kontorahmenmarkieren()" name="markieren" id="markieren" value="Alle markieren" />-->

</div>

<!-- tab view schließen -->
</div>





<div id="editKontorahmen" style="display:none;" title="Bearbeiten">
  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-12 col-md-height">
        <div class="inside inside-full-height">
          <fieldset>
            <legend>{|Kontorahmen|}</legend>
            <input type="hidden" id="editid">
            <table>
              <tr>
                <td>{|Konto|}:</td>
                <td><input type="text" name="editkonto" id="editkonto" size="40"></td>
              </tr>
              <tr>
                <td width="120">{|Beschriftung|}:</td>
                <td><input type="text" name="editbeschriftung" id="editbeschriftung" size="40"></td>
              </tr>
              <tr>
                <td>{|Art|}:</td>
                <td><select name="editart" id="editart">
                      <option value="0"></option>
                      <option value="1">Aufwendungen</option>
                      <option value="2">Erl&ouml;se</option>
                      <option value="3">Geldtransit</option>
                      <option value="9">Saldo</option>
                    </select>
                </td>
              </tr>
              <tr>
                <td>{|Bemerkung|}:</td>
                <td><textarea name="editbemerkung" id="editbemerkung" rows="5" cols="38"></textarea></td>
              </tr>
              <tr>
                <td>{|Projekt|}:</td>
                <td><input type="text" name="editprojekt" id="editprojekt" size="40"></td>
              </tr>
              <tr>
                <td>{|Nicht sichtbar|}:</td>
                <td><input type="checkbox" name="editnichtsichtbar" id="editnichtsichtbar" value="1" size="40"></td>
              </tr>
            </table>
          </fieldset>
        </div>
      </div>
    </div>
  </div>
</div>


</form>
<script type="text/javascript">

$(document).ready(function() {
    $('#editkonto').focus();

    $("#editKontorahmen").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:600,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        KontorahmenReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        Kontorahmen_EditSave();
      }
    }
  });

  $("#editKontorahmen").dialog({
    close: function( event, ui ) {KontorahmenReset();}
  });

});

function KontorahmenReset(){
  $('#editKontorahmen').find('#editid').val('');
  $('#editKontorahmen').find('#editkonto').val('');
  $('#editKontorahmen').find('#editbeschriftung').val('');
  $('#editKontorahmen').find('#editart').val('');
  $('#editKontorahmen').find('#editbemerkung').val('');
  $('#editKontorahmen').find('#editprojekt').val('');
  $('#editKontorahmen').find('#editnichtsichtbar').prop('checked', false);
}

function Kontorahmen_EditSave() {

    $.ajax({
        url: 'index.php?module=kontorahmen&action=save',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            editid: $('#editid').val(),
            editkonto: $('#editkonto').val(),
            editbeschriftung: $('#editbeschriftung').val(),
            editart: $('#editart').val(),
            editbemerkung: $('#editbemerkung').val(),
            editprojekt: $('#editprojekt').val(),
            editnichtsichtbar: $('#editnichtsichtbar').prop("checked")?1:0,
            
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                KontorahmenReset();
                updateLiveTable();
                $("#editKontorahmen").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function Kontorahmen_Edit(id) {
  if(id > 0){
    $.ajax({
        url: 'index.php?module=kontorahmen&action=edit&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editKontorahmen').find('#editid').val(data.id);
            $('#editKontorahmen').find('#editkonto').val(data.sachkonto);
            $('#editKontorahmen').find('#editbeschriftung').val(data.beschriftung);
            $('#editKontorahmen').find('#editart').val(data.art);
            $('#editKontorahmen').find('#editbemerkung').val(data.bemerkung);
            $('#editKontorahmen').find('#editprojekt').val(data.projekt);
            $('#editKontorahmen').find('#editnichtsichtbar').prop("checked",data.ausblenden==1?true:false);
            if(data.art=="" || data.art <=0 )
              $('#editKontorahmen').find('#editart').val('0');
            else 
              $('#editKontorahmen').find('#editart').val(data.art);
            App.loading.close();
            $("#editKontorahmen").dialog('open');
        }
    });
  }else{
    KontorahmenReset(); 
    $("#editKontorahmen").dialog('open');
  }
    

}

function updateLiveTable(i) {
    var oTableL = $('#kontorahmenlist').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);    
}

/*function Kontorahmen_Delete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({ 
            url: 'index.php?module=kontorahmen&action=delete',
            data: {
                id: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                if (data.status == 1) {
                    updateLiveTable();
                } else {
                    alert(data.statusText);
                }
                App.loading.close();
            }
        });
    }

    return false;

}*/


</script>







  
<script type="text/javascript">

function chkontorahmen(kid)
{
  var status = 0;
  var el = '#kontorahmen_'+kid;
  status = $(el).prop('checked');
  if(status)status = 1;
               
  if(kid)
  {
    $.ajax({
      url: 'index.php?module=kontorahmen&action=chkontorahmen',
      type: 'POST',
      dataType: 'json',
      data: {kontorahmen :kid, wert : status},
      success: function(data) {
                 
      },
      beforeSend: function() {

      }
    });
   
  }   
          
}


function alleauswaehlen()
{
  var wert = $('#auswahlalle').prop('checked');
  $('#kontorahmenlist').find(':checkbox').prop('checked',wert);

  $.ajax({
    url: 'index.php?module=kontorahmen&action=allemarkieren',
    type: 'POST',
    dataType: 'json',
    data: {markiert : checked},
    success: function(data){

    },
    beforeSend: function(){

    }
  });

}


function kontorahmenmarkieren(){
//$('.check:button').click(function(){
  var checked = !$(this).data('checked');
  $('input:checkbox').prop('checked', checked);
  $('.check:button').val(checked ? 'Alle entmarkieren' : 'Alle markieren' )
  $(this).data('checked', checked);

  $.ajax({
    url: 'index.php?module=kontorahmen&action=allemarkieren',
    type: 'POST',
    dataType: 'json',
    data: {markiert : checked},
    success: function(data){

    },
    beforeSend: function(){

    }
  });
      //});
}

</script>
