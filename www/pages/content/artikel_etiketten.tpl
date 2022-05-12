<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-4 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Auswahl Etiketten|}</legend>
[FORMULAR]
</fieldset>

</div>
</div>

<div class="col-xs-12 col-sm-4 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>Einstellung</legend>
<form action="" method="post">
<table width="100%">
<tr><td>Etikett Auto-Druck:</td><td><input type="checkbox" name="etikettautodruck" value="1" [ETIKETTAUTODRUCK]>&nbsp;<i>aktuell nur bei "Schneller Wareneingang"</i></td></tr>
<tr><td>Etikett für Auto-Druck:</td><td><select name="autodrucketikett">[AUTODRUCKETIKETT]</select></td></tr>
<tr><td></td><td align="right"><input type="submit" class="btnBlue" name="speichern" value="Speichern"></td></tr>
</table>
</form>
</fieldset>

</div>
</div>




<div class="col-xs-12 col-sm-4 col-sm-height">
<div class="inside inside-full-height">

<div id="abweichendelieferadressestyle">
<fieldset><legend>{|Bildvorschau|}</legend>
[BILD]
</fieldset>
</div>

</div>
</div>
</div>
</div> <!-- spalte 2 zu -->


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-10 col-md-height">
<div class="inside-white inside-full-height">
  [TAB1]
</div>
</div>
<div class="col-xs-12 col-md-2 col-md-height">
<div class="inside inside-full-height">
  <fieldset>
    <legend>{|Aktionen|}</legend>
    <input type="button" class="btnGreenNew" name="new_article_label" value="&#10010; Neues Etikett" onclick="ArticleLabelEdit(0);">
  </fieldset>
</div>
</div>
</div>
</div>



[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

<div id="editArticleLabel" style="display:none;" title="Bearbeiten">
    <form method="post">
        <input type="hidden" id="article_label_articleid" value="[ID]">
        <input type="hidden" id="article_label_id">
        <fieldset>
            <legend>{|Artikeletikett|}</legend>
            <table>
                <tr>
                    <td width="150">{|Etikett|}:</td>
                    <td><select name="article_label_name" id="article_label_name">
                            [ARTICLELABELS]
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>{|Art|}:</td>
                    <td><select name="article_label_type" id="article_label_type">
                            <option value="produktion">Produktion</option>
                            <option value="wareneingang">Wareneingang</option>
                            <option value="versandzentrum">Versandzentrum</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>{|Menge|}:</td>
                    <td><input type="text" name="article_label_amount" id="article_label_amount" size="5"></td>
                </tr>
                <tr>
                    <td>{|Drucker|}:</td>
                    <td><select name="article_label_printer" id="article_label_printer">
                            [ARTICLELABELPRINTER]
                        </select>
                    </td>
                </tr>
            </table>
        </fieldset>
    </form>
</div>

<script type="text/javascript">

    $(document).ready(function() {
        $('#article_label_name').focus();

        $("#editArticleLabel").dialog({
            modal: true,
            bgiframe: true,
            closeOnEscape:false,
            minWidth:650,
            maxHeight:700,
            autoOpen: false,
            buttons: {
                ABBRECHEN: function() {
                    ArticleLabelReset();
                    $(this).dialog('close');
                },
                SPEICHERN: function() {
                    ArticleLabelEditSave();
                }
            }
        });

        $("#editArticleLabel").dialog({
            close: function( event, ui ) { ArticleLabelReset();}
        });
    });

    function ArticleLabelReset() {
        $('#editArticleLabel').find('#article_label_id').val('');
        var labelname = document.getElementById('article_label_name');
        labelname.selectedIndex = 0;
        $('#editArticleLabel').find('#article_label_type').val('produktion');
        $('#editArticleLabel').find('#article_label_amount').val('');
        var labelprinter = document.getElementById('article_label_printer');
        labelprinter.selectedIndex = 0;
    }

    function ArticleLabelEditSave() {
        $.ajax({
            url: 'index.php?module=artikel&action=etiketten&cmd=save',
            data: {
                //Alle Felder die fürs editieren vorhanden sind
                id: $('#article_label_id').val(),
                article: $('#article_label_articleid').val(),
                label: $('#article_label_name').val(),
                type: $('#article_label_type').val(),
                amount: $('#article_label_amount').val(),
                printer: $('#article_label_printer').val()
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                App.loading.close();
                if (data.status == 1) {
                    ArticleLabelReset();
                    updateLiveTableArticleLabel();
                    $("#editArticleLabel").dialog('close');
                } else {
                    alert(data.statusText);
                }
            }
        });
    }

    function ArticleLabelEdit(id) {
        if(id > 0)
        {
            $.ajax({
                url: 'index.php?module=artikel&action=etiketten&cmd=get',
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function() {
                    App.loading.open();
                },
                success: function(data) {
                    $('#editArticleLabel').find('#article_label_id').val(data.id);
                    $('#editArticleLabel').find('#article_label_name').val(data.label);
                    $('#editArticleLabel').find('#article_label_type').val(data.type);
                    $('#editArticleLabel').find('#article_label_amount').val(data.amount);
                    $('#editArticleLabel').find('#article_label_printer').val(data.printer);

                    App.loading.close();
                    $("#editArticleLabel").dialog('open');
                }
            });
        } else {
            ArticleLabelReset();
            $("#editArticleLabel").dialog('open');
        }
    }

    function updateLiveTableArticleLabel(i) {
        var oTableL = $('#artikel_etiketten').dataTable();
        var tmp = $('.dataTables_filter input[type=search]').val();
        oTableL.fnFilter('%');
        oTableL.fnFilter(tmp);
    }

    function ArticleLabelDelete(id) {
        var conf = confirm('Wirklich löschen?');
        if (conf) {
            $.ajax({
                url: 'index.php?module=artikel&action=etiketten&cmd=delete',
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
                        updateLiveTableArticleLabel();
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

