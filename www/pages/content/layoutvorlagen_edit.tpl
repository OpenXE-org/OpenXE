
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">{|Layoutvorlage|}</a></li>
    <li><a href="#tabs-2">{|Positionen/Elemente|}</a></li>
    <li><a href="#tabs-3">{|Vorschau|}</a></li>
  </ul>
  
  <div id="tabs-1">

    <form action="" method="post" enctype="multipart/form-data">
      <table class="tableborder" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tbody>
          <tr valign="top">
            <td >
              <table width="100%" align="center" style="background-color:#cfcfd1;">
                <tr>
                  <td width="33%"></td>
                  <td align="center" nowrap><b style="font-size: 14pt">{|Layoutvorlage|}</b> </td>
                  <td width="33%" align="right">&nbsp; <input type="submit" name="layoutspeichern"
                      value="Speichern" onclick="this.form.action += '#tabs-1';"/> <input type="button" value="Abbrechen" onclick="window.location.href='index.php?module=layoutvorlagen&action=list';"></td>
                </tr>
              </table>
              <fieldset>
                <legend>{|Einstellungen|}</legend>
                <table width="100%">
                  <tr>
                    <td width="110">{|Name|}:</td>
                    <td><input type="text" name="name" value="[NAME]" size="40"></td>
                    <td></td>
                    <td>{|Typ|}:</td>
                    <td>
                      <select name="typ">
                        <option value="pdf">PDF</option>
                      </select>
                    </td>
                  </tr>
                  <tr>
                    <td>{|Format|}:</td>
                    <td>
                      <select name="format">
                        [FORMAT]
                      </select>
                    </td>
                    <td></td>
                    <td>{|Kategorie|}:</td>
                    <td>
                      <input type="text" name="kategorie" value="[KATEGORIE]" size="40" id="kategorie">
                    </td>
                  </tr>
                  <tr>
                    <td>{|Hintergrund|}:</td>
                    <td><input type="file" name="pdf_hintergrund">&nbsp;[PDFVORSCHAU]</td>
                    <td>{|Hintergrund l&ouml;schen|}: <input type="checkbox" name="delete_hintergrund" /></td>
                    <td>{|Projekt|}:</td>
                    <td><input type="text" name="layoutvorlagen_projekt" id="layoutvorlagen_projekt" size="40" value="[PROJEKT]"></td>
                  </tr>
                </table>
              </fieldset>
              [MESSAGE]
              <br><br>

            </td>
          </tr>

          <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
            <td width="" valign="" height="" bgcolor="" align="right" colspan="1" bordercolor="" classname="orange2" class="orange2">
              <input type="submit" name="layoutspeichern"
              value="Speichern" /> <input type="button" value="Abbrechen" onclick="window.location.href='index.php?module=layoutvorlagen&action=list';"/></td>
          </tr>
  
        </tbody>
      </table>
    </form>
 
  </div>

  <div id="tabs-2">
    <div class="row">
    <div class="row-height">
    <div class="col-xs-12 col-md-10 col-md-height">
    <div class="inside-white inside-full-height">
      [TABELLE]
    </div>
    </div>
    <div class="col-xs-12 col-md-2 col-md-height">
    <div class="inside inside-full-height">
      <fieldset>
        <legend>{|Aktionen|}</legend>
        <center>
          <input type="button" name="" class="btnGreenNew" onclick="editLayoutvorlagePosition(0);" value="&#10010; Neue Position / Element hinzufügen"><!-- $('#positionModal').dialog('open');  -->
        </center>
      </fieldset>
    </div>
    </div>
    </div>
    </div>
  </div>
  <div id="tabs-3">
    [TAB3]
  </div>
</div>

<div id="positionModal" title="Bearbeiten">
  <fieldset>
    <form action="index.php?module=layoutvorlagen&action=saveposition" method="POST" enctype="multipart/form-data" id="positionModalSubmit">
    
      <legend>{|Position / Element|}</legend>

      <input type="hidden" name="id" value="0">
      <input type="hidden" name="layoutvorlage" value="[ID]">

      <table width="800">
        <tr>
          <td class="formline formline_1" valign="top" width="50%">
            <table width="100%">
              <tbody>
                <tr class="s_always">
                  <td>{|Beschreibung|}:</td>
                  <td><input type="text" name="beschreibung" id="beschreibung" size="40"></td>
                </tr>
       
                <tr class="s_always">
                  <td>{|Interner Name|}:</td>
                  <td><input type="text" name="name" id="name" size="40"></td>
                </tr>
                <tr class="s_always">
                  <td>{|Typ|}:</td>
                  <td>
                    <select name="typ" id="typselect">
                      <option value="textfeld">Textfeld</option>
                      <option value="linie">Linie</option>
                      <option value="rechteck">Rechteck</option>
                      <option value="bild">Bild</option>
                      <option value="barcode">Barcode</option>
                    </select>
                  </td>
                </tr>
                <tr class="s_textfeld">
                  <td>{|Zeichenbegrenzung|}:</td>
                  <td>
                    <input type="checkbox" value="1" id="zeichenbegrenzung" name="zeichenbegrenzung" onchange="changezeichenbegrenzung();" />&nbsp;<span class="zeichenbegrenzung"><input type="text" name="zeichenbegrenzung_anzahl" id="zeichenbegrenzung_anzahl" /> Zeichen</span>
                  </td>    
                </tr>
                <tr class="s_always"><td colspan="2"><br></td></tr>
                <tr style="display:none;">
                  <td>{|Position Typ|}:</td>
                  <td>
                    <select name="position_typ">
                      <option value="relative">Relativ</option>
                      <option value="absolute" selected=selected>Absolut</option>
                    </select>
                  </td>
                </tr>
                <tr class="s_textfeld s_linie s_rechteck s_bild">
                  <td>{|Position X|}:</td>
                  <td><input type="text" name="position_x">&nbsp;<i>in mm</i></td>
                </tr>
                <tr class="s_textfeld s_linie s_rechteck s_bild">
                  <td>{|Position Y|}:</td>
                  <td><input type="text" name="position_y">&nbsp;<i>in mm</i></td>
                </tr>
                <tr style="display:none">
                  <td>{|Position parent|}:</td>
                  <td>
                    <select name="position_parent">
                      [POSITIONPARENT]
                    </select>
                  </td>
                </tr>
                <tr class="s_textfeld s_linie s_rechteck s_bild">
                  <td>{|Breite|}:</td>
                  <td><input type="text" name="breite">&nbsp;<i>in mm</i></td>
                </tr>
                <tr class="s_textfeld s_linie s_rechteck s_bild">
                  <td>{|Höhe|}:</td>
                  <td><input type="text" name="hoehe">&nbsp;<i>in mm</i></td>
                </tr>
                <tr class="s_textfeld">
                  <td>{|Schriftart|}:</td>
                  <td>
                    <select name="schrift_art">
                      [SCHRIFTARTEN]
                    </select>
                  </td>
                </tr>
                <tr class="s_textfeld">
                  <td>{|Schriftgr&ouml;&szlig;e|}:</td>
                  <td><input type="text" id="schrift_groesse" name="schrift_groesse"></td>
                </tr>
                <tr class="s_textfeld">
                  <td>Zeilenh&ouml;he:</td>
                  <td><input type="text" id="zeilen_hoehe" name="zeilen_hoehe"></td>
                </tr>
                <tr class="s_textfeld">
                  <td>{|Schriftstyle|}:</td>
                  <td>{|Fett|}: <input type="checkbox" name="schrift_fett" /> {|Kursiv|}: <input type="checkbox" name="schrift_kursiv" /> {|Unterstrichen|}: <input type="checkbox" name="schrift_underline" /></td>
                </tr>
                <tr class="s_textfeld">
                  <td>{|Schriftausrichtung|}:</td>
                  <td>
                    <select name="schrift_align">
                      [SCHRIFTAUSRICHTUNGEN]
                    </select>
                  </td>
                </tr>

                <tr class="s_textfeld">
                  <td>{|Schriftfarbe|}:</td>
                  <td><input type="text" name="schrift_farbe" id="schrift_farbe"></td>
                </tr>

                <tr class="s_textfeld s_rechteck">
                  <td>{|Hintergrund|}:</td>
                  <td><input type="text" name="hintergrund_farbe" id="hintergrund_farbe"></td>
                </tr>

                <tr class="s_textfeld s_linie s_rechteck s_bild">
                  <td>{|Rahmen|}:</td>
                  <td>
                    <select name="rahmen">
                      [RAHMEN]
                    </select>
                  </td>
                </tr>

                <tr class="s_textfeld s_linie s_rechteck s_bild">
                  <td>{|Rahmenfarbe|}:</td>
                  <td><input type="text" name="rahmen_farbe" id="rahmen_farbe"></td>
                </tr>

                <tr class="s_textfeld s_linie s_rechteck s_bild">
                  <td>{|Sichtbar|}:</td>
                  <td><input type="checkbox" name="sichtbar" checked="checked"></td>
                </tr>
                <tr class="s_textfeld s_linie s_rechteck s_bild">
                  <td>{|Sortierung|}:</td>
                  <td><input type="text" name="sort" id="sort" /></td>
                </tr>
              </tbody>
            </table>
          </td>
          <td class="formline formline_2" valign="top">
            <table width="100%">
              <tbody>
                <tr class="s_textfeld">
                  <td>{|Inhalt Deutsch|}:</td>
                  <td><textarea name="inhalt_deutsch" id="inhalt_deutsch" style="min-width:275px;min-height:150px;"></textarea></td>
                </tr>
                <tr class="s_textfeld">
                  <td>{|Inhalt Englisch|}:</td>
                  <td><textarea name="inhalt_englisch" id="inhalt_englisch" style="min-width:275px;min-height:150px;"></textarea></td>
                </tr>

                <tr class="s_bild">
                  <td>{|Bild Deutsch|}:</td>
                  <td><input type="file" name="bild_deutsch"></td>
                </tr>

                <tr class="s_bild">
                  <td>{|Bild Englisch|}:</td>
                  <td><input type="file" name="bild_englisch"></td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </table>
    </form>
  </fieldset>

</div>


<script type="text/javascript">

$(document).ready(function() {
    $("#positionModal").dialog({
        modal: true,
        bgiframe: true,
        closeOnEscape:false,
        autoOpen: false,
        minWidth: 860,
        buttons: {
            ABBRECHEN: function() {
                clearModal();
                $(this).dialog('close');
            },
            SPEICHERN: function() {
              if($('#name').val() == '' || $('#name').val() == undefined)
              {
                $('#name').val($('#beschreibung').val());
              }
              if($('#name').val() != '' && $('#name').val() != undefined)
              {
                $('#positionModalSubmit').submit();
              } else {
                alert('Bitte einen Namen eingeben!');
              }
            }
        }
    });

    $('#typselect').change(function() {
        var typ = $(this).val();
        setPositionEntryTyp(typ);
    });

    $('#positionModal').find('input').keypress(function(event) {
        if ( event.which == 13 ) {
            event.preventDefault();
            $('#positionModalSubmit').submit();
        }
    })

    setPositionEntryTyp('textfeld');
    //tinyMCEsetup('#inhalt_deutsch');
    //tinyMCEsetup('#inhalt_englisch');

});

function setPositionEntryTyp(typ) {

    $('td.formline > table > tbody > tr').each(function() {
        if (!$(this).hasClass('s_always')) {
            $(this).hide();
        }
    });

    switch(typ) {
        case 'textfeld':
        case 'barcode':
            $('td.formline > table > tbody > tr.s_textfeld').show();
        break;
        case 'linie':
            $('td.formline > table > tbody > tr.s_linie').show();
        break;
        case 'rechteck':
            $('td.formline > table > tbody > tr.s_rechteck').show();
        break;
        case 'bild':
            $('td.formline > table > tbody > tr.s_bild').show();
        break;
    }

}

function updateLiveTable() {
    var oTableL = $('#layoutvorlagen_edit').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);  
}


function editLayoutvorlagePosition(positionId) {
    $('.ilink').remove();
    if(positionId == 0){


        $("#positionModal").find("input[type=text], textarea").val("");
        $("#schrift_farbe").val("#000000");
        $("#schrift_groesse").val(12);
        $("#zeilen_hoehe").val(5);
        $("#schrift_farbe").change();
        $("#hintergrund_farbe").change();
        $("#rahmen_farbe").change();
        $("#typselect").val('textfeld');
        $('#typselect').trigger("change");
        /*for (edId in tinyMCE.editors){
            tinyMCE.editors[edId].setContent('');
        }*/
        $('#positionModal').find('[name="sichtbar"]').prop('checked', 'checked');
        $("#positionModal").dialog('open');
        
        changezeichenbegrenzung();
    } else {
        $.ajax({
            url: 'index.php?module=layoutvorlagen&action=getposition',
            type: 'POST',
            dataType: 'json',
            data: {
                id: positionId
            },
            success: function(data) {
                if (typeof data.row.id != 'undefined') {
                
                    $.each(data.row, function(key,value) {
                        var field = $('#positionModal').find('[name="'+key+'"]');

                        
                        if (field.is('select')) {
                        field.val(value);
                            field.find('option[value="'+value+'"]').attr('selected', 'selected');
                        } else if (field.is("input[type='checkbox']")) {
                            if(value == 1){
                                field.prop('checked', 'checked');
                               
                            } else {
                                field.prop('checked', false);
                               
                            }
                        }else{
                          field.val(value);
                        }
                    });
                    $("#schrift_farbe").change();
                    $("#hintergrund_farbe").change();
                    $("#rahmen_farbe").change();
                    /*for (edId in tinyMCE.editors){
                        if(tinyMCE.editors[edId].settings.selector == '#inhalt_deutsch')tinyMCE.editors[edId].setContent($('#inhalt_deutsch').val());
                        if(tinyMCE.editors[edId].settings.selector == '#inhalt_englisch')tinyMCE.editors[edId].setContent($('#inhalt_englisch').val());
                    }*/
                    if (typeof(data.row.typ) != 'undefined') {
                        setPositionEntryTyp(data.row.typ);
                    }

                }
                


                if (typeof(data.bilddata) != 'undefined' && data.bilddata != null) {
                    if (typeof(data.bilddata.bild_deutsch) != 'undefined') {
                        $('input[name="bild_deutsch"]').after(data.bilddata.bild_deutsch);
                    }
                    if (typeof(data.bilddata.bild_englisch) != 'undefined') {
                        $('input[name="bild_englisch"]').after(data.bilddata.bild_englisch);
                    }
                }

                changezeichenbegrenzung();

                $("#positionModal").dialog('open');
                App.loading.close();
            },
            beforeSend: function() {
                $('.ilink').remove();
                App.loading.open();
            }
        });
    }

}

function tinyMCEsetup(selector) {

    tinyMCE.init({
    selector: selector,
    mode : "textareas",
    theme: "modern",
    menubar: false,
    statusbar : false,
    toolbar_items_size: 'small',
    width : "100%",
    entity_encoding : "raw",
    element_format : "html",
    force_br_newlines : true,
    force_p_newlines : false,
    plugins: [ "textcolor" ],
    toolbar1: "bold italic underline strikethrough",
    toolbar2: "",
    toolbar3: ""
   });

}

function deleteLayoutvorlagePosition(positionId) {

    var check = confirm('Wirklich löschen?');
    if (!check) {
        return false;
    }

    $.ajax({
        url: 'index.php?module=layoutvorlagen&action=deleteposition',
        type: 'GET',
        dataType: 'json',
        data: { id: positionId },
        success: function(data) {
            if (data.status == 1) {
                updateLiveTable();
            } else {
                alert(data.statusText);
            }
            App.loading.close();
        },
        beforeSend: function() {
            App.loading.open();
        }
    });

}

function changezeichenbegrenzung()
{
if($('#zeichenbegrenzung').prop('checked'))
{
$('#zeichenbegrenzung_anzahl').show();
}else{
$('#zeichenbegrenzung_anzahl').hide();
}

}

function clearModal() {
    var layoutvorlageId = [ID];
    $('#positionModal').find('input,select').val('');
    $('#positionModal').find('input[name="id"]').val('0');
    $('#positionModal').find('input[name="layoutvorlage"]').val(layoutvorlageId);
}

</script>
