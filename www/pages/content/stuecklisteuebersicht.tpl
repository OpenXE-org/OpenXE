
<!-- gehort zu tabview -->


<div id="tabs">
    <ul>
        <li><a href="#tabs-1">&Uuml;bersicht</a></li>
        <!--<li><a href="#tabs-2">neue Position anlegen</a></li>-->
        <li><a href="#tabs-3">St&uuml;ckliste importieren</a></li>
    </ul>

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
  <div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-8 col-sm-height">
    <div class="inside-full-height">
      <!--<fieldset>
        <legend>{|Filter|}</legend>
        <table>
          <tr>
            <td height="43px"></td>
          </tr>
        </table>
      </fieldset>-->
      <fieldset class="white">
        <legend></legend>
        [TAB1]
      </fieldset>
    </div>
    </div>
    <div class="col-xs-12 col-sm-4 col-sm-height">
    <div class="inside-full-height">
        <div class="mlmTreeContainerRight">
          <fieldset>
            <legend>{|Suche|}</legend>
            <div class="mlmTreeSuche">Bezeichnung: <input id="search" type="text" value=""><hr></div>
          </fieldset>
          <br />
          <div id="mlmTree" class="aciTree"></div>
          <div class="mlmClear"></div>
        </div>
        <script type="text/javascript" src="js/aciTree/js/jquery.aciPlugin.min.js"></script>
        <script type="text/javascript" src="js/aciTree/js/jquery.aciTree.min.js"></script>
        <link rel="stylesheet" type="text/css" href="js/aciTree/css/aciTree.css">
        
            <style>

        .aciTree {
          padding-left:50px;
        }


        .mlmClear {
          clear: both;
        }

        .mlmTreeSuche {
          padding: 10px 10px 5px 10px;
        }

        .mlmintranet_minidetail_layer {
          width: 100%;
        }

        .searched > div {
          background-color: #E5F5D2;
        }
        </style>
        
    </div>
    </div>

  </div>
  </div>
</div>

<div id="tabs-2">
[TAB2]
</div>

<div id="tabs-3">
[TAB3]
</div>



<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->





<div id="editStuecklisteNeuePosition" style="display:none;" title="Bearbeiten">
  [MESSAGE]
  <form action="" method="post" name="eprooform" >
  [FORMHANDLEREVENT]
    <input type="hidden" id="id">
    <input type="hidden" name = "startikelid" id="startikelid" value="[ID]">

    <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
      <tbody>
        <tr valign="top" colspan="3">
          <td colspan="3">
            <fieldset>
              <legend>{|St&uuml;ckliste|}</legend>
              <table align="center" cellspacing="5" border="0" width="100%">
                <tr>
                  <td width="120">{|Artikel|}*:</td>
                  <td colspan="4"><input type="text" size="70" name="artikel" id="artikel" rule="notempty" msg="Pflichfeld!"></td>
                </tr>
                <tr>
                  <td width="120">{|Menge|}*:</td>
                  <td width="180"><input name="menge" id="menge" rule="notempty" msg="Pflichfeld!" type="text" size="20">&nbsp;</td><td width="20">&nbsp;</td><td width="150"><!--Position:--></td><td><!--<input name="sort" type="text" size="10">--></td>
                </tr>
              </table>
            </fieldset>

            <fieldset>
              <legend>{|Produktion|}</legend>
              <table align="center" cellspacing="5" border="0" width="100%">
                <tr>
                  <td width="120">{|Art|}:</td><td width="180">
                    <select id="art" name="art">
                      <option value="et">Einkaufsteil</option>
                      <option value="it">Informationsteil/Dienstleistung</option>
                      <option value="bt">Beistellung</option>
                    </select>&nbsp;</td>
                  <td width="20">&nbsp;</td><td width="150"><!--Position:--></td><td><!--<input name="sort" type="text" size="10">--></td>
                </tr>

              </table>
            </fieldset>


            <fieldset>
              <legend>{|Best&uuml;ckung|}</legend>
              <table align="center" cellspacing="5" border="0" width="100%">
                <tr>
                  <td width="120">{|Referenz|}:</td><td colspan="4"><textarea id="referenz" name="referenz" rows="3" cols="69"></textarea></td>
                </tr>
                <tr>
                  <td width="120">{|Layer|}:</td><td width="180"><select id="layer" name="layer"><option value="Top">TOP</option>
                    <option value="Bottom">BOTTOM</option></select>&nbsp;</td><td width="20">&nbsp;</td><td width="150"><!--Position:--></td><td><!--<input name="sort" type="text" size="10">--></td>
                </tr>
                <tr>
                  <td width="120">{|Platzierung|}:</td><td width="180"><select id="place" name="place"><option value="DP">platzieren</option>
                    <option value="DNP">nicht platzieren</option></select>&nbsp;</td><td width="20">&nbsp;</td><td width="150"><!--Position:--></td><td><!--<input name="sort" type="text" size="10">--></td>
                </tr>
                <tr>
                  <td>{|Wert|}:</td><td colspan="4"><input type="text" size="70" id="wert" name="wert"></td>
                </tr>
                <tr>
                  <td>{|Bauform|}:</td><td colspan="4"><input type="text" size="70" id="bauform" name="bauform"></td>
                </tr>
                <tr>
                  <td>{|Z-Achse|}:</td><td colspan="4"><input type="text" size="70" id="zachse" name="zachse"></td>
                </tr>
                <tr>
                  <td>{|X-Position|}:</td><td colspan="4"><input type="text" size="70" id="xpos" name="xpos"></td>
                </tr>
                <tr>
                  <td>{|Y-Position|}:</td><td colspan="4"><input type="text" size="70" id="ypos" name="ypos"></td>
                </tr>
<!--<tr><td>{|Interner Kommentar|}:</td><td colspan="4"><textarea name="internerkommentar" rows="3" cols="70"></textarea></td></tr>-->

              </table>
            </fieldset>
          </td>
        </tr>
        <!--<tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
          <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
            <input type="submit" value="Speichern" /> [ABBRECHEN] </td>
        </tr>-->
      </tbody>
    </table>
  </form>
</div>



<div id="editStuecklisteArtikeldoppelt" style="display:none;" title="Bearbeiten">
  [MESSAGE]
  <form action="" method="post">
    <!--<input type="hidden" id="id">
    <input type="hidden" name = "startikelid" id="startikelid" value="[ID]">-->

    <fieldset>
      Der Artikel ist bereits vorhanden. Artikel trotzdem einzeln einf&uuml;gen oder Menge bei vorhandenem Artikel erh&ouml;hen?<br /><br /><br />
      <center>
        <table>
          <tr>
            <td><input type="radio" name="doppelt" id="einfuegen"></td>
            <td>Trotzdem einzeln einf&uuml;gen</td>
          </tr>
          <tr>
            <td><input type="radio" name="doppelt" id="mengeerhoehen"></td>
            <td>Menge erh&ouml;hen</td>
          </tr>
        </table>
      </center>     
    </fieldset>
  </form>
</div>


<div id="editPartsListAlternative" style="display:none;" title="Bearbeiten">
  <form method="post">
    <input type="hidden" id="parts_list_id">
    <fieldset>
      <legend>{|Produktion|}</legend>
      <table>
        <tr>
          <td>{|Alternative|}:</td>
          <td width="500"><input type="text" name="parts_list_alternative_article" id="parts_list_alternative_article" size="40"></td>
          <td><input type="button" name="parts_list_alternative_save" id="parts_list_alternative_save" value="Speichern" onclick="PartsListAlternativeEditSave();">[ADDALTERNATIVE]</td>
        </tr>
        <tr>
          <td>{|Grund|}:</td>
          <td><input type="text" name="parts_list_reason" id="parts_list_reason" size="40"></td>
          <td><input type="hidden" name="parts_list_alternative_id" id="parts_list_alternative_id"></td>
        </tr>
      </table>
    </fieldset>
    [PARTSLISTALTERNATIVES]

</div>




<script type="text/javascript">

$(document).ready(function() {
  $('#e_name').focus();

  $("#editStuecklisteNeuePosition").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:750,
    maxHeight:700,
    autoOpen: false,
    buttons: [
      {
        id: "partslist_button_addalternative",
        text: "{|ALTERNATIVARTIKEL|}",
        click: function() {
          PartsListAlternativeEdit(document.getElementById('id').value);
        }
      },
      {
        id: "partslist_button_cancel",
        text: "{|ABBRECHEN|}",
        click: function() {
          StuecklisteNeuePositionReset();
          $(this).dialog('close');
        }
      },
      {
        id: "partslist_button_save",
        text: "{|SPEICHERN|}",
        click: function() {
          StuecklisteNeuePositionEditSave();
        }
      }
    ]
  });

  $("#editStuecklisteArtikeldoppelt").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape: false,
    minWidth:700,
    maxHeight: 700,
    autoOpen: false,
    buttons: {
      '{|ABBRECHEN|}': function(){
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function(){
        StuecklisteArtikeldoppeltEditSave();
      }
    }
  });
  $("#editStuecklisteNeuePosition").dialog({
    close: function( event, ui ) { StuecklisteNeuePositionReset();}
  });


  $("#editPartsListAlternative").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:700,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      '{|SCHLIESSEN|}': function() {
        PartsListAlternativeReset();
        $(this).dialog('close');
      }
    },
    close: function( event, ui ) { PartsListAlternativeReset();}
  });

});



function StuecklisteArtikeldoppeltEditSave(){
  $.ajax({
    url: 'index.php?module=artikel&action=editstueckliste&cmd=doppeltsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      eid: document.getElementById('id').value,
      //eid: $('#id').val(),
      estartikelid: document.getElementById('startikelid').value,
      //estartikelid: $('#startikelid').val(),
      eartikel: document.getElementById('artikel').value,
      emenge: document.getElementById('menge').value,
      eart: document.getElementById('art').value,
      //ealternative: document.getElementById('alternative').value,
      ereferenz: document.getElementById('referenz').value,
      elayer: document.getElementById('layer').value,
      eplace: document.getElementById('place').value,
      ewert: document.getElementById('wert').value,
      ebauform: document.getElementById('bauform').value,
      ezachse: document.getElementById('zachse').value,
      expos: document.getElementById('xpos').value,
      eypos: document.getElementById('ypos').value,
      eeinfuegen: $('#einfuegen').prop("checked")?1:0,
      emengeerhoehen: $('#mengeerhoehen').prop("checked")?1:0,
                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {        
        updateLiveTable();
        $("#editStuecklisteArtikeldoppelt").dialog('close');
        $("#editStuecklisteNeuePosition").dialog('close');
        Reloadtree();
      } else {
        alert(data.statusText);      
      }
    }
  });
}

function StuecklisteArtikeldoppeltReset(){
  $('#editStuecklisteArtikeldoppelt').find('#einfuegen').prop("checked", true);
  $('#editStuecklisteArtikeldoppelt').find('#mengeerhoehen').prop("checked", false);
}


function StuecklisteNeuePositionReset()
{
  $('#editStuecklisteNeuePosition').find('#artikel').val('');
  $('#editStuecklisteNeuePosition').find('#menge').val('');
  $('#editStuecklisteNeuePosition').find('#art').val('et');
  //$('#editStuecklisteNeuePosition').find('#alternative').val('');
  $('#editStuecklisteNeuePosition').find('#referenz').val('');
  $('#editStuecklisteNeuePosition').find('#layer').val('Top');
  $('#editStuecklisteNeuePosition').find('#place').val('DP');
  $('#editStuecklisteNeuePosition').find('#wert').val('');
  $('#editStuecklisteNeuePosition').find('#bauform').val('');
  $('#editStuecklisteNeuePosition').find('#zachse').val('');
  $('#editStuecklisteNeuePosition').find('#xpos').val('');
  $('#editStuecklisteNeuePosition').find('#ypos').val('');
  $("#partslist_button_addalternative").button("disable");
}

function StuecklisteNeuePositionEditSave() {
  $.ajax({
    url: 'index.php?module=artikel&action=editstueckliste&cmd=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      eid: $('#id').val(),
      estartikelid: $('#startikelid').val(),
      eartikel: $('#artikel').val(),
      emenge: $('#menge').val(),
      eart: $('#art').val(),
      //ealternative: $('#alternative').val(),
      ereferenz: $('#referenz').val(),
      elayer: $('#layer').val(),
      eplace: $('#place').val(),
      ewert: $('#wert').val(),
      ebauform: $('#bauform').val(),
      ezachse: $('#zachse').val(),
      expos: $('#xpos').val(),
      eypos: $('#ypos').val(),
                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        StuecklisteNeuePositionReset();
        updateLiveTable();
        $("#editStuecklisteNeuePosition").dialog('close');
        Reloadtree();
      } else {
        if(data.statusText != ""){
          alert(data.statusText);
        }else{
          if(data.doppelt.includes("doppelt")){
            $("#editStuecklisteArtikeldoppelt").dialog('open');
            StuecklisteArtikeldoppeltReset();
          }
        }   
      }
    }
  });


}

function StuecklisteNeuePositionEdit(id) {
  if(id > 0)
  {
    $("#partslist_button_addalternative").button("enable");
    $('#id').val(id);
    $.ajax({
      url: 'index.php?module=artikel&action=editstueckliste&cmd=edit',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editStuecklisteNeuePosition').find('#artikel').val(data.artikel);
        $('#editStuecklisteNeuePosition').find('#menge').val(data.menge);
        $('#editStuecklisteNeuePosition').find('#art').val(data.art);
        //$('#editStuecklisteNeuePosition').find('#alternative').val(data.alternative);
        $('#editStuecklisteNeuePosition').find('#referenz').val(data.referenz);
        $('#editStuecklisteNeuePosition').find('#layer').val(data.layer);
        $('#editStuecklisteNeuePosition').find('#place').val(data.place);
        $('#editStuecklisteNeuePosition').find('#wert').val(data.wert);
        $('#editStuecklisteNeuePosition').find('#bauform').val(data.bauform);
        $('#editStuecklisteNeuePosition').find('#zachse').val(data.zachse);
        $('#editStuecklisteNeuePosition').find('#xpos').val(data.xpos);
        $('#editStuecklisteNeuePosition').find('#ypos').val(data.ypos);

                
        App.loading.close();
        $("#editStuecklisteNeuePosition").dialog('open');
      }
    });
  } else {
    $('#id').val('');
    StuecklisteNeuePositionReset();
    $("#partslist_button_addalternative").button("disable");
    $("#editStuecklisteNeuePosition").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#stueckliste').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

/*function StuecklisteNeuePositionDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=artikel&action=stueckliste&cmd=delete',
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

function PartsListAlternativeReset()
{
  $('#editPartsListAlternative').find('#parts_list_alternative_article').val('');
  $('#editPartsListAlternative').find('#parts_list_reason').val('');
  $('#editPartsListAlternative').find('#parts_list_alternative_id').val('');
}

function PartsListAlternativeEditSave(){
  $.ajax({
    url: 'index.php?module=artikel&action=stueckliste&cmd=savealternative',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#parts_list_id').val(),
      alternativeId: $('#parts_list_alternative_id').val(),
      alternativeArticle: $('#parts_list_alternative_article').val(),
      reason: $('#parts_list_reason').val()
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        PartsListAlternativeReset();
        updateLiveTableAlternatives();
      } else {
        alert(data.statusText);
      }
    }
  });
}

function PartsListAlternativeEdit(id) {
  oMoreData1parts_list_alternatives = id;
  updateLiveTableAlternatives();

  if(id > 0)
  {
    $.ajax({
      url: 'index.php?module=artikel&action=stueckliste&cmd=getalternative',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editPartsListAlternative').find('#parts_list_id').val(data.id);

        App.loading.close();
        $("#editPartsListAlternative").dialog('open');
      }
    });
  } else {
    PartsListAlternativeReset();
    $("#editPartsListAlternative").dialog('open');
  }

}

function PartsListAlternativeEditEntry(id){
  if(id > 0)
  {
    $.ajax({
      url: 'index.php?module=artikel&action=stueckliste&cmd=getalternativedetails',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editPartsListAlternative').find('#parts_list_alternative_article').val(data.article);
        $('#editPartsListAlternative').find('#parts_list_reason').val(data.reason);
        $('#editPartsListAlternative').find('#parts_list_alternative_id').val(data.alternativeId);

        App.loading.close();
        $("#editPartsListAlternative").dialog('open');
      }
    });
  } else {
    PartsListAlternativeReset();
    $("#editPartsListAlternative").dialog('open');
  }
}

function updateLiveTableAlternatives(i) {
  var oTableL = $('#parts_list_alternatives').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);
}

function PartsListAlternativeDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=artikel&action=stueckliste&cmd=deletealternative',
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
          updateLiveTableAlternatives();
        } else {
          alert(data.statusText);
        }
        App.loading.close();
      }
    });
  }

  return false;
}

</script>



<script type="text/javascript">
      
    function Reloadtree()
    {
      var api = $('#mlmTree').aciTree('api');
      api.unload(null, {
          success: function() {
              this.ajaxLoad(null);
              LoadTree();
          }
      });
    }
      
    function LoadTree()
    {
      $('#mlmTree').aciTree({
          autoInit: false,
          checkboxChain: false,
          ajax: {
              url: '[URL]'
          },
          checkbox: false,
          itemHook: function(parent, item, itemData, level) {
            //console.log(itemData);
          },
            filterHook: function(item, search, regexp) {

              if (search.length) {
                  var parent = this.parent(item);

                  if (parent.length) {
                      var label = this.getLabel(parent);
                      if (regexp.test(String(label))) {
                          this.setVisible(item);
                          return true;
                      }
                      this.setVisible(item);
                  }

                  if (regexp.test(String(this.getLabel(item)))) {
                    item.addClass('searched');
                    return true;
                  } else {
                    return false;
                  }

                  //return regexp.test(String(this.getLabel(item)));
              } else {
                  return true;
              }
          }
      });
    }
      
    $(document).ready(function() {
      LoadTree();
      var api = $('#mlmTree').aciTree('api');

      $('#search').val('');
      var last = '';

      $('#search').on('keyup', function() {
          if ($(this).val() === last) {
              return;
          }

          $('.aciTreeLi').removeClass('searched');

          last = $(this).val();
          api.filter(null, {
              search: $(this).val(),
              callback: function() {

              },
              success: function(item, options) {

                  if (!options.first) {
                      //alert('No results found!');
                  }
              }
          });
      });


      $('#mlmTree').on('acitree', function(event, api, item, eventName, options){
          switch (eventName){
              case 'checked':
                      console.log('the event 1 is: ' + eventName + ' for the item ID: ' + api.getId(item));
              break;
              case 'unchecked':
                      console.log('the event 2 is: ' + eventName + ' for the item ID: ' + api.getId(item));
              break;
              case 'selected':
                /*
                var ajaxData = {
                  id: api.getId(item),
                  name: api.getLabel(item)
                }

                $.ajax({
                  url: 'index.php?module=artikel&action=stueckliste&cmd=getbaum',
                  data: ajaxData,
                  success: function(data) {
                    $('.mlmTreeContainerRight').html(data);
                    checkContainerPos();
                  }
                });*/

                break;
              default:
                  if (api.isItem(item)){
                      //console.log('the event is: ' + eventName + ' for the item ID: ' + api.getId(item));
                  } else {
                      //console.log('the event is: ' + eventName + ' for the tree ROOT');
                  }
          }
      });

        $('#mlmTree').aciTree('init');
        $(window).on('scroll', function() {
          checkContainerPos();
        });

    });

    function checkContainerPos() {
      var newContainerPos = ($(window).scrollTop() - 113);
      if (newContainerPos <= 0) {
        newContainerPos = 0;
      }
      $('.mlmintranet_minidetail_layer').css({
        top: newContainerPos
      });
    }
</script>
