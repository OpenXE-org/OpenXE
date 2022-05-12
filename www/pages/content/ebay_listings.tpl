<div id="listingwindow" style="display:none;">
    <input type="hidden" id="listingid" value ="">
    <fieldset><legend>Artikeldaten</legend>
        <table>
            <tr>
                <td>eBay Artikelnummer: </td>
                <td colspan="3"><input type="text" id="listingitemid" value="123456" readonly="readonly"></td>
            </tr>
            <tr style="display:none;">
                <td>Startdatum: </td>
                <td><input type="text" id="startdatum" value="10.10.2010" readonly="readonly"></td>
                <td>Enddatum: </td>
                <td><input type="text" id="enddatum" value="-" readonly="readonly"></td>
            </tr>

            <tr><td colspan="4"><hr></td></tr>

            <tr>
                <td>Artikel: </td>
                <td id="artikelzelle"><input type="text" id="artikel" value="123456"></td>
                <td>SKU: </td>
                <td><input type="text" id="sku" value="123456" readonly></td>
            </tr>
            <tr style="display:none;">
                <td>Titel: </td>
                <td><input type="text" id="titel" value="123456" readonly></td>
                <td>Sprache: </td>
                <td><select><option value='-'>-</option></select></td>
            </tr>
        </table>
        <table id="variantentable">
            <tbody></tbody>
        </table>
        <div></div>
        <table style="display:none;">
            <tr><td colspan="4"><hr></td></tr>

            <tr>
                <td>Kategorie 1: </td>
                <td><input type="text" id="kategorie1" value="123456" readonly="readonly"></td>
                <td>Zahlung: </td>
                <td><select id="rahmenbedinung_zahlung"><option value='1'>-</option></select></td>
            </tr>
            <tr>
                <td>Kategorie 2: </td>
                <td><input type="text" id="kategorie2" value="123456" readonly="readonly"></td>
                <td>Versand :</td>
                <td><select id="rahmenbedinung_versand"><option value='1'>-</option></select></td>
            </tr>
            <tr>
                <td>Store Kategorie 1: </td>
                <td><select id="storekategorie1"><option value='1'>-</option></select></td>
                <td>Rückgabe: </td>
                <td><select id="rahmenbedinung_rueckgabe"><option value='1'>-</option></select></td>
            </tr>
            <tr>
                <td>Store Kategorie 2: </td>
                <td><select disabled><option value='1'>-</option></select></td>
                <td>Template: </td>
                <td><select id="templates"><option value='1'>-</option></select></td>
            </tr>

            <tr><td colspan="4"><hr></td></tr>

            <tr>
                <td>Auktionsart: </td>
                <td><select><option value='1'>FixedPrice</option></select></td>
                <td>Privatlisting: </td>
                <td><input type="checkbox" id="privatlisting"></td>
            </tr>
            <tr>
                <td>Auktionsdauer: </td>
                <td><select id="auktionsdauer">
                        <option value="0">unbegrenzt</option>
                        <option value="3">3 Tage</option>
                        <option value="5">5 Tage</option>
                        <option value="7">7 Tage</option>
                        <option value="10">10 Tage</option>
                    </select></td>
                <td>Preisvorschlag: </td>
                <td><input type="checkbox" id="preisvorschlag"></td>
            </tr>
            <tr>
                <td>Gallerbield: </td>
                <td><select><option value='1'>Standard</option></select></td>
                <td>eBay Plus: </td>
                <td><input type="checkbox" id="ebayplus"></td>
            </tr>
            <tr>
                <td>Zustand: </td>
                <td><select><option value='1'>Neu</option></select></td>
                <td>Lieferzeit: </td>
                <td><input type="text" id="lieferzeit" value="3"></td>
            </tr>

        </table>
    </fieldset>
</div>


<div id="tabs-1">
    [MESSAGE]
    <div class="row">
        <div class="row-height">
            <div class="col-xs-12 col-md-10 col-md-height">
                <div class="inside_white inside-full-height">
                    <fieldset class="white">
                        <legend>&nbsp;</legend>
                        [TAB1]
                        [TAB1NEXT]
                        <!--
                        <fieldset>
                            <legend>{|Stapelverarbeitung|}</legend>
                            <input type="checkbox" value="1" id="allemarkieren" name="allemarkieren" onchange="markierealle();" /><label for="allemarkieren">&nbsp;alle markieren&nbsp;</label>
                            <select>
                                <option value="sync">Lagerbestand synchronisieren</option>
                                <option value="export">Daten synchronisieren (Beschreibung, Bilder, Preise, etc.)</option>
                                <option value="list">Listings einstellen</option>
                                <option value="endlist">Listings beenden</option>
                                <option value="continuelist">Beendete Listings weiterführen</option>
                                <option value="relist">Beendete Listings neu einstellen</option>
                                <option value="createarticles">Fehlende Artikel in Xentral erstellen</option></select>
                            <input class="btnGreen" type="button" name="stapelverarbeitungaction" id="stapelverarbeitungaction" value="Ausführen" onclick="stapelverarbeitungaction();">
                        </fieldset>
                    </fieldset>
                    -->
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#listingwindow').dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 940,
                title:'Listing Einstellungen',
                buttons: {
                    SPEICHERN: function()
                    {
                        variants = [];
                        $("input[id^='variantid']").each(function() {
                            lineId = this.id.split('_')[1];
                            variantId = this.value;
                            articleNumber = $('#variantarticle_'+lineId).val();
                            variants.push({'id' : variantId, 'articleNumber' : articleNumber});
                        });

                        $.ajax({
                            url: 'index.php?module=ebay&action=einstellungen&cmd=savelistingdata',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                artikel: $('#artikel').val(),
                                listingid: $('#listingid').val(),
                                variants: variants
                            },
                            success: function(data) {
                                updateLiveTable();
                                $('#listingwindow').dialog('close');
                            }
                        });
                    },
                    ABBRECHEN: function() {
                        $(this).dialog('close');
                    }
                },
                close: function(event, ui){

                }
            }
        );
    });


    function openlistingwindow(listingid)
    {
        $.ajax({
            url: 'index.php?module=ebay&action=einstellungen&cmd=getlistingdata&listingid='+listingid,
            type: 'POST',
            dataType: 'json',
            data: {

            },
            success: function(data) {
                if(data.success){

                    $('#rahmenbedinung_zahlung').empty()
                    for (var i = 0; i < data.data['store']['payment_policies'].length; i++) {
                        $('#rahmenbedinung_zahlung').append("<option value='"+data.data['store']['payment_policies'][i]['profile_id_external']+"'>"+data.data['store']['payment_policies'][i]['profile_name']+"</option>");
                    }
                    $('#rahmenbedinung_zahlung').val(data.data.ebay_payment_profile_id_external);

                    $('#rahmenbedinung_versand').empty()
                    for (var i = 0; i < data.data['store']['shipping_policies'].length; i++) {
                        $('#rahmenbedinung_versand').append("<option value='"+data.data['store']['shipping_policies'][i]['profile_id_external']+"'>"+data.data['store']['shipping_policies'][i]['profile_name']+"</option>");
                    }
                    $('#rahmenbedinung_versand').val(data.data.ebay_shipping_profile_id_external);

                    $('#rahmenbedinung_rueckgabe').empty()
                    for (var i = 0; i < data.data['store']['return_policies'].length; i++) {
                        $('#rahmenbedinung_rueckgabe').append("<option value='"+data.data['store']['return_policies'][i]['profile_id_external']+"'>"+data.data['store']['return_policies'][i]['profile_name']+"</option>");
                    }
                    $('#templates').val(data.data.templates);

                    $('#templates').empty()
                    $('#templates').append("<option value='0'>-</option>");
                    for (var i = 0; i < data.data['store']['templates'].length; i++) {
                        $('#templates').append("<option value='"+data.data['store']['templates'][i]['id']+"'>"+data.data['store']['templates'][i]['template_name']+"</option>");
                    }
                    $('#templates').val(data.data.template_id);

                    $('#storekategorie1').empty()
                    for (var i = 0; i < data.data['store']['store_categories'].length; i++) {
                        $('#storekategorie1').append("<option value='"+data.data['store']['store_categories'][i]['category_id_external']+"'>"+data.data['store']['store_categories'][i]['description']+"</option>");
                    }

                    $("#artikelzelle").empty();
                    $("#artikelzelle").html('<input type="text" id="artikel" value="123456">');
                    $("#artikel").autocomplete({
                        source: "index.php?module=ajax&action=filter&filtername=artikelnummer"
                    });

                    $("#variantentable tbody").empty();
                    if(data.data['variations'].length > 0){
                        $("#variantentable tbody").append('<tr><td colspan="4">Varianten:</td></tr><tr>');
                        $.each(data.data['variations'][0]['specifics'], function( index, value ) {
                            $("#variantentable tbody").append('<td>'+index+'</td>');
                        });
                        $("#variantentable tbody").append('<td>SKU</td>');
                        $("#variantentable tbody").append('<td>Artikel</td>');
                        $("#variantentable tbody").append('</tr>');

                        for (var i = 0; i < data.data['variations'].length; i++) {
                            $("#variantentable tbody").append('<tr>');
                            $.each(data.data['variations'][i]['specifics'], function( index, value ) {
                                $("#variantentable tbody").append('<td><input type="text" value="'+value+'" readonly></td>');
                                //alert( index + ": " + value );
                            });
                            $("#variantentable tbody").append('<td><input type="text" value="'+data.data['variations'][i]['sku']+'" readonly></td>');
                            $("#variantentable tbody").append('<td><input type="text" id="variantarticle_'+i+'" value="'+data.data['variations'][i]['article']+'">' +
                                '<input type="hidden" id="variantid_'+i+'" value="'+data.data['variations'][i]['id']+'"></td>');
                            $("#variantarticle_"+i).autocomplete({
                                source: "index.php?module=ajax&action=filter&filtername=artikelnummer"
                            });

                            $("#variantentable tbody").append('</tr>');
                        }
                    }

                    addClicklupe();
                    lupeclickevent();

                    $('#listingid').val(listingid);
                    $('#listingitemid').val(data.data.item_id_external);
                    $('#startdatum').val('-');
                    $('#enddatum').val('-');
                    $('#artikel').val(data.data.article);
                    $('#sku').val(data.data.sku);
                    $('#titel').val(data.data.title);
                    $('#kategorie1').val(data.data.ebay_primary_category_id_external);
                    $('#kategorie2').val(data.data.ebay_secondary_category_id_external);
                    $('#ebayplus').prop('checked',data.data.ebay_plus);
                    $('#privatlisting').prop('checked',data.data.ebay_private_listing);
                    $('#preisvorschlag').prop('checked',data.data.ebay_price_suggestion);

                    $('#listingwindow').dialog('open');
                }else{
                    alert(data.data);
                }

            },
            beforeSend: function() {

            }
        });
    }

    function updateLiveTable() {
         var oTableL = $('#listingstable').dataTable();


        oTableL.fnFilter('a');
        oTableL.fnFilter('');
    }

    function markierealle()
    {
        var checked = $('#allemarkieren').prop('checked');
        $('#listingstable input').prop('checked', checked);
    }

</script>