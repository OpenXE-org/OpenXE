<div id="smartyoutput" style="display: none;">
    <input type="hidden" id="templatetype" />
    <input type="hidden" id="documenttype" />
    <label for="textareasmartydeliverynote">{|Template|}</label><br />
    <textarea rows="30" id="textareasmartydeliverynote"></textarea><br />

    <label for="seltemplate">{|Vorlage|}: </label><select id="seltemplate">[SELTEMPLATE]</select>
    <input type="button" id="loadxml" value="laden" />

    <label class="document deliverynote" for="deliverynote_test">{|Lieferschein|}:</label>
    <input type="text" class="document deliverynote" id="deliverynote_test" name="delievernote_test" />

    <label class="document order" for="order_test">{|Auftrag|}:</label>
    <input type="text" class="document order" id="order_test" name="order_test" />
    <label class="document offer" for="offer_test">{|Angebot|}:</label>
    <input type="text" class="document offer" id="offer_test" name="offer_test" />

    <label class="document supplierorder" for="supplierorder_test">{|Bestellung|}:</label>
    <input type="text" class="document supplierorder" id="supplierorder_test" name="supplierorder_test" />
    <label class="document invoice" for="invoice_test">{|Rechnung|}:</label>
    <input type="text" class="document invoice" id="invoice_test" name="invoice_test" />
    <label class="document creditnote" for="creditnote_test">{|Gutschrift|}:</label>
    <input type="text" class="document creditnote" id="creditnote_test" name="creditnote_test" />

    <label class="article" for="article_test">{|Artikel|}:</label>
    <input type="text" class="article" id="article_test" name="article_test" />

    <label class="fromto" for="from_test">{|von|}:</label>
    <input class="fromto" type="text" id="from_test" size="12" />
    <label class="fromto" for="to_test">{|bis|}:</label>
    <input class="fromto" type="text" id="to_test" size="12" />
    <input type="button" class="buttonsave" value="Speichern &amp; Ausf&uuml;hren" id="saveandrundoctype" />
    <input type="button" class="buttonsave" value="Speichern" id="savedoctype" />
    <br />
    <div id="smartyoutputcontent">

    </div>
</div>

<style>
    textarea {
        overflow-y: scroll; /* Vertical scrollbar */
        overflow: scroll; /* Horizontal and vertical scrollbar*/
        white-space: pre;
    }
    #smartyoutputcontent {
        height: 40vh;
        min-width: 90vw;
        overflow: scroll;
        border:grey 1px solid;
    }
    #textareasmartydeliverynote {
        max-height: 40vh;
    }
    #smartyoutput table {
        width: 100%;
    }
    #smartyoutput textarea {
        width: 100%;
    }

    fieldset.modulespecific table tr td:nth-child(1) {
        max-width: 120px;
    }

    #textareasmartyincomming {
        width: 100%;
    }

    #textareasmartyincomminginput {
        width:100%;
    }
</style>
<script type="application/javascript">
    $(document).on('ready',function(){
        $('#speichern').on('click', function(){
            $('#senddocument').removeAttr('disabled');
            $('#sendarticlestemplate').removeAttr('disabled');
            $('#sendinventorytemplate').removeAttr('disabled');
            $('#sendsalesreporttemplate').removeAttr('disabled');
            $('#sendtrackingtemplate').removeAttr('disabled');
            $('#incommingtemplate').removeAttr('disabled');
        });
        $('#loadxml').on('click', function() {
            $.ajax({
                url: 'index.php?module=uebertragungen&action=edit&cmd=loadtemplate&id=[ID]',
                type: 'POST',
                dataType: 'json',
                data: {
                    type:$('#seltemplate').val()
                },
                success: function(data) {
                    $('#textareasmartydeliverynote').val(data.html);
                }
            });
        });

        $('#saveandrundoctype').on('click', function() {
            $('#'+$('#templatetype').val()).val($('#textareasmartydeliverynote').val());
            $.ajax({
                url: 'index.php?module=uebertragungen&action=edit&cmd=savesmartydeliverynote&id=[ID]',
                type: 'POST',
                dataType: 'json',
                data: {
                    content: $('#textareasmartydeliverynote').val(),
                    deliverynote: $('#deliverynote_test').val(),
                    order: $('#order_test').val(),
                    supplyorder: $('#supplierorder_test').val(),
                    invoice: $('#invoice_test').val(),
                    creditnote: $('#creditnote_test').val(),
                    offer: $('#offer_test').val(),
                    article: $('#article_test').val(),
                    type:$('#templatetype').val(),
                    from:$('#from_test').val(),
                    to:$('#to_test').val(),
                    doctype:$('#documenttype').val()
                },
                success: function(data) {
                    $('#smartyoutputcontent').html(data.html);
                }
            });
        });


        $('#savedoctype').on('click', function() {
            $('#'+$('#templatetype').val()).val($('#textareasmartydeliverynote').val());
            $.ajax({
                url: 'index.php?module=uebertragungen&action=edit&cmd=savesmartydeliverynote&id=[ID]',
                type: 'POST',
                dataType: 'json',
                data: {
                    content: $('#textareasmartydeliverynote').val(),
                    type:$('#templatetype').val()
                },
                success: function(data) {
                    $('#smartyoutputcontent').html(data.html);
                    $('#smartyoutput').dialog('close');
                }
            });
        });
        $('#smartyoutput').dialog(
        {
            modal: true,
            autoOpen: false,
            width: 'auto',
            title:'Ergebnis',
            buttons: {
                'SCHLIESSEN': function() {
                    $(this).dialog('close');
                }
            }
        });

        $('#editsenddocument').on('click',function() {
            $('#templatetype').val('senddocument');
            $('#textareasmartydeliverynote').val($('#senddocument').val());
            $('#smartyoutput').dialog('open');
            $('label.document').show();
            $('input.document').show();
            var belegtyp = $('#belegtyp').val();
            $('#documenttype').val(belegtyp);
            if(belegtyp !== 'lieferschein') {
                $('label.deliverynote').hide();
                $('input.deliverynote').hide();
            }
            if(belegtyp !== 'auftrag') {
                $('label.order').hide();
                $('input.order').hide();
            }
            if(belegtyp !== 'bestellung') {
                $('label.supplierorder').hide();
                $('input.supplierorder').hide();
            }
            if(belegtyp !== 'rechnung') {
                $('label.invoice').hide();
                $('input.invoice').hide();
            }
            if(belegtyp !== 'gutschrift') {
                $('label.creditnote').hide();
                $('input.creditnote').hide();
            }
            if(belegtyp !== 'angebot') {
                $('label.offer').hide();
                $('input.offer').hide();
            }
            $('label.article').hide();
            $('input.article').hide();
            $('label.fromto').hide();
            $('input.fromto').hide();
            $('#seltemplate').html(
                    '<option value="senddocumentxml">Belege XML</optionvalue>' +
                    '<option value="senddocumentcsv">Belege CSV</optionvalue>'
            );
        });
        $('#editsendarticles').on('click',function() {
            $('#templatetype').val('sendarticlestemplate');
            $('#textareasmartydeliverynote').val($('#sendarticlestemplate').val());
            $('#smartyoutputcontent').html('');
            $('#smartyoutput').dialog('open');
            $('label.document').hide();
            $('input.document').hide();
            $('label.article').show();
            $('input.article').show();
            $('label.fromto').hide();
            $('input.fromto').hide();
            $('#seltemplate').html(
                    '<option value="sendarticlesxml">Artikel XML</optionvalue>' +
                    '<option value="sendarticlescsv">Artikel CSV</optionvalue>'
            );
        });
        $('#editsendinventory').on('click',function() {
            $('#templatetype').val('sendinventorytemplate');
            $('#textareasmartydeliverynote').val($('#sendinventorytemplate').val());
            $('#smartyoutputcontent').html('');
            $('#smartyoutput').dialog('open');
            $('label.document').hide();
            $('input.document').hide();
            $('label.fromto').hide();
            $('input.fromto').hide();
            $('label.article').show();
            $('input.article').show();
            $('#seltemplate').html(
                    '<option value="sendinventoryxml">Lagerzahlen XML</optionvalue>' +
                    '<option value="sendinventorycsv">Lagerzahlen CSV</optionvalue>' +
                    '<option value="sendinventorybestbeforexml">Lagerzahlen (mit MHD) XML</optionvalue>' +
                    '<option value="sendinventorybestbeforecsv">Lagerzahlen (mit MHD) CSV</optionvalue>'
            );
        });
        $('#editsendsalesreport').on('click',function() {
            $('#templatetype').val('sendsalesreporttemplate');
            $('#textareasmartydeliverynote').val($('#sendsalesreporttemplate').val());
            $('#smartyoutputcontent').html('');
            $('#smartyoutput').dialog('open');
            $('label.document').hide();
            $('input.document').hide();
            $('label.fromto').show();
            $('input.fromto').show();
            $('label.article').hide();
            $('input.article').hide();
            $('#seltemplate').html(
                    '<option value="sendsalesreportxml">Verkaufsreport XML</optionvalue>' +
                    '<option value="sendsalesreportcsv">Verkaufsreport CSV</optionvalue>'
            );
        });
        $('#editsendtracking').on('click',function() {
            $('#templatetype').val('sendtrackingtemplate');
            $('#textareasmartydeliverynote').val($('#sendtrackingtemplate').val());
            $('#smartyoutputcontent').html('');
            $('#smartyoutput').dialog('open');
            $('label.document').hide();
            $('input.document').hide();
            $('label.deliverynote').show();
            $('input.deliverynote').show();
            $('label.fromto').hide();
            $('input.fromto').hide();
            $('label.article').hide();
            $('input.article').hide();
            $('#seltemplate').html(
                    '<option value="sendtrackingxml">Tracking XML</optionvalue>' +
                    '<option value="sendtrackingcsv">Tracking CSV</optionvalue>'
            );
        });


    });
</script>
