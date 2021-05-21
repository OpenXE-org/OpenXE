
<script>
    /*
  $(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content"
    });

  });*/

    $( function() {
        $( "#accordion" )
            .accordion({

                header: "> div > h2",
                heightStyle: "content"
            })
            .sortable({
                axis: "y",
                handle: "h2",
                stop: function( event, ui ) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.children( "h2" ).triggerHandler( "focusout" );

                    // Refresh accordion to handle new order
                    $( this ).accordion( "refresh" );
                }
            });
    } );



    function openprojektgeplantezeiten()
    {
        $('#projektgeplantezeiten').dialog('open');
        $('#scroller').css('z-index',99);
        $('#jsddm').css('z-index',99);
        $('#addteilprojektpopup').parent().hide();
        var oTable = $('#projekt_mitarbeiterstundengeplant').DataTable( );
        oTable.ajax.reload();
    }

    function changestundensatzname(adresse, teilprojekt)
    {
        $.ajax({
            url: 'index.php?module=projekt&action=dashboard&cmd=changestundensatzname&id=[ID]',
            type: 'POST',
            dataType: 'json',
            data: { adr: adresse, tp:teilprojekt, bezeichnung:$('#stundensatzname_'+adresse).val(),stunden:$('#stunden_'+adresse).val(),stundensatz:$('#stundensatz_'+adresse).val()}
        });
    }

    function checkenddatum()
    {
        var startdatum = $('#startdatum').val();
        var abgabedatum = $('#abgabedatum').val();
        if(abgabedatum == '' && startdatum != '')$('#abgabedatum').val(startdatum);
    }

    $(function() {
        $( "#tabsbelege" ).tabs();
    });

    function editpa(pa, dummy)
    {

        $.ajax({
            url: 'index.php?module=projekt&action=dashboard&cmd=getprojektartikelliste&id=[ID]',
            type: 'POST',
            dataType: 'json',
            data: { paid: pa},
            success: function(data) {
                if(data)
                {
                    $('#paid').val(pa);
                    $('#showinmonitoring').prop('checked',data.showinmonitoring==1?true:false);
                    if(data.kalkulationbasis == 'gesamt')
                    {
                        $('#pa_gesamt').prop('checked',true);
                    }else{
                        $('#pa_prostueck').prop('checked',true);
                    }
                    $('#pa_menge_geplant').val(data.menge_geplant);
                    $('#pa_ek_geplant').val(data.ek_geplant);
                    $('#pa_vk_geplant').val(data.vk_geplant);
                    $('#pa_kommentar').val(data.kommentar);
                    if(data.vk_real)
                    {
                        $('#pa_vkausstammdaten').html(' | VK aus Stammdaten: '+data.vk_real+' '+data.vk_waehrung+' (<a href="index.php?module=artikel&action=edit&id='+data.artikel+'" target="_blank">zum Artikel</a>)');
                    }
                    if(data.ek_real)
                    {
                        $('#pa_ekausstammdaten').html(' | EK aus Stammdaten: '+data.ek_real+' '+data.ek_waehrung+' (<a href="index.php?module=artikel&action=edit&id='+data.artikel+'" target="_blank">zum Artikel</a>)');
                    }

                    $('#changeartikelliste').dialog('open');
                    $.ajax({
                        url: 'index.php?module=projekt&action=dashboard&id=[ID]',
                        type: 'POST',
                        dataType: 'json',
                        data: { setaccordion: 2},
                        success: function(data) {
                        }
                    });
                }

            },
            beforeSend: function() {

            }
        });
    }



    function addteilprojekt(projekt, teilprojekt)
    {
        $.ajax({
            url: 'index.php?module=projekt&action=dashboard&cmd=addteilprojekt&id=[ID]',
            type: 'POST',
            dataType: 'json',
            data: { id: projekt, uid: teilprojekt},
            success: function(data) {
                $('#art').val('');
                $('#uid').val(teilprojekt);
                $('#vorgaenger').val(teilprojekt);
                $('#postypkind').prop('checked',true);
                $('#cmd').val(data.cmd);
                $('#aufgabe').val('');
                $('#kosten_geplant').val('');
                $('#zeit_geplant').val('');
                $('#adresse').val('');
                $('#artikel_geplant').val('');
                $('#zeit_geplant').prop('disabled',false);
                $('#startdatum').val(data.startdatum);
                $('#abgenommen').prop('checked',false);

                $('#mitarbeiterhinweis').show();
                $('#mitarbeiterbutton').prop('disabled',true);

                $('#addteilprojektpopup').dialog('open');

            },
            beforeSend: function() {

            }
        });
        $.ajax({
            type: "POST",
            url: "index.php?module=ajax&action=autosaveuserparameter",
            data:  { name: "teilprojekt_geplante_zeiten_teilprojekt", value: $.base64Encode(teilprojekt+'') }
        });
    }

    function editartikelkommentar(projektartikel, tmp)
    {
        $.ajax({
            url: 'index.php?module=projekt&action=dashboard&cmd=getartikelkommentar&id=[ID]',
            type: 'POST',
            dataType: 'json',
            data: { id: [ID], projekt_artikel: projektartikel},
            success: function(data) {
                var wert = prompt('Kommentar',data.kommentar);

                $.ajax({
                    url: 'index.php?module=projekt&action=dashboard&cmd=saveartikelkommentar&id=[ID]',
                    type: 'POST',
                    dataType: 'json',
                    data: { id: [ID], projekt_artikel: projektartikel,kommentar:wert},
                    success: function(data) {
                        var oTable = $('#projektdashboardartikelliste').DataTable( );
                        oTable.ajax.reload();
                    }
                });
            }
        });
    }

    function aktiviereteilprojekt(projekt, teilprojekt)
    {
        $.ajax({
            url: 'index.php?module=projekt&action=dashboard&cmd=aktiviereteilprojekt&id=[ID]',
            type: 'POST',
            dataType: 'json',
            data: { id: projekt, uid: teilprojekt},
            success: function(data) {
                if(typeof data.status != 'undefined' && data.status == 1)
                {
                    $('#teilprojekttable tr').each(function(){
                        var el = this;

                        $(this).find('input[type="checkbox"]').each(function(){
                            var eins = 1;
                            var ida = this.id.split('_');
                            if(typeof ida[eins] != 'undefined' &&  ida[eins] > 0)
                            {
                                if(ida[eins] == teilprojekt)
                                {
                                    $(el).css('font-weight','bold');
                                }else{
                                    $(el).css('font-weight','');
                                }
                            }
                        });
                    });
                }
            },
            beforeSend: function() {

            }
        });
    }

    function changeteilprojektfilter()
    {
        var teilprojekt = $('#teilprojektfilter').val();
        $.ajax({
            url: 'index.php?module=projekt&action=dashboard&cmd=tpfilter&id=[ID]',
            type: 'POST',
            dataType: 'json',
            data: { uid: teilprojekt},
            success: function(data) {
                var s = $('#projektdashboardartikelliste_filter input').first();
                var s2 = $('#abrechnungszeitprojektdashboard_filter input').first();
                $(s).focus();
                $(s).val($(s).val()+' ');
                $(s).trigger('keyup');
                $(s2).focus();
                $(s2).val($(s2).val()+' ');
                $(s2).trigger('keyup');

                setTimeout(function(){
                    var s = $('#projektdashboardartikelliste_filter input').first();
                    var s2 = $('#abrechnungszeitprojektdashboard_filter input').first();
                    $(s).val(trim($(s).val()));
                    $(s2).val(trim($(s2).val()));
                },1000);

                $('#artikelfiltermsg').html(data.msg);
            },
            beforeSend: function() {

            }
        });
    }

    function zeiterfassungstatusaendern()
    {
        var zeiterfassung_zu_beleg_select = $('#zeiterfassung_zu_beleg_select').val();
        if(zeiterfassung_zu_beleg_select == 'abgerechnet' || zeiterfassung_zu_beleg_select == 'abgeschlossen')
        {
            var anz = 0;
            var items = '';
            $('#abrechnungszeitprojektdashboard td input:checked').each(function(){
                anz++;
                var ida = this.id.split('_');
                var eins = 1;
                if(typeof ida[eins] != 'undefined')
                {
                    if(items != '')items = items + ',';
                    items = items + ''+ida[eins];
                }
            });

            if(anz > 0)
            {
                $.ajax({
                    url: 'index.php?module=projekt&action=dashboard&cmd=open&id=[ID]',
                    type: 'POST',
                    dataType: 'json',
                    data: { zeitenliste: items, cmd: 'zeitstatusaendern',status: zeiterfassung_zu_beleg_select},
                    success: function(data) {
                        $('#zeiterfassung_zu_beleg_select').val('');
                        $('#abrechnungszeitprojektdashboard').DataTable( ).ajax.reload();
                    },
                    beforeSend: function() {

                    }
                });
            }else{
                alert("Keine Buchungen ausgewählt");
                $('#zeiterfassung_zu_beleg_select').val('');
            }
        }
    }



    function deleteTeilprojekt(projekt, teilprojekt)
    {
        if(confirm('Wollen Sie das Teilprojekt wirklich löschen?'))
        {
            $.ajax({
                url: 'index.php?module=projekt&action=dashboard&cmd=deleteteilprojekt&id=[ID]',
                type: 'POST',
                dataType: 'json',
                data: {  uid: teilprojekt},
                success: function(data) {
                    if(data.status == 1)window.location = window.location.href;
                }
            });
        }
    }

    function deleteartikel(artikel,dummy)
    {
        if(confirm('Wollen Sie den Artikel wirklich löschen?'))
        {
            $.ajax({
                url: 'index.php?module=projekt&action=dashboard&cmd=deleteteilprojektartikel&id=[ID]',
                type: 'POST',
                dataType: 'json',
                data: {  pa: artikel},
                success: function(data) {
                    if(data.status == 1)
                    {
                        var oTable = $('#projektdashboardartikelliste').DataTable( );
                        oTable.ajax.reload();
                    }
                }
            });
        }
    }

    function editteilprojekt(projekt, teilprojekt)
    {
        $.ajax({
            url: 'index.php?module=projekt&action=dashboard&cmd=editteilprojekt&id=[ID]',
            type: 'POST',
            dataType: 'json',
            data: { id: projekt, uid: teilprojekt},
            success: function(data) {
                $('#cmd').val(data.cmd);
                $('#beschreibung').val(data.beschreibung);
                $('#art').val(data.art);
                $('#uid').val(teilprojekt);
                $('#status').val(data.status);
                $('#aufgabe').val(data.aufgabe);
                $('#sort').val(data.sort);
                $('#kosten_geplant').val(data.kosten_geplant);
                $('#zeit_geplant').val(data.zeit_geplant);
                $('#zeit_geplant').prop('disabled',data.disablezeit?true:false);
                $('#adresse').val(data.adresse);
                $('#abgabedatum').val(data.abgabedatum);
                $('#farbe').val(data.farbe);
                $('#farbe').trigger('change');
                $('#startdatum').val(data.startdatum);
                $('#vorgaenger').val(data.vorgaenger);
                if(data.postyp == 'postypnachbar')
                {
                    $('#postypnachbar').prop('checked',true);
                }else{
                    $('#postypkind').prop('checked',true);
                }
                $('#ek_geplant').val(data.ek_geplant);
                $('#vk_geplant').val(data.vk_geplant);
                if(data.kalkulationbasis == 'pauschale')
                {
                    $('#pauschale').prop('checked',true);
                }else{
                    $('#stundenbasis').prop('checked',true);
                }
                if(data.vkkalkulationbasis == 'pauschale')
                {
                    $('#vkpauschale').prop('checked',true);
                }else{
                    $('#vkstundenbasis').prop('checked',true);
                }
                $('#projektplanausblenden').prop('checked',data.projektplanausblenden == 1?true:false);
                $('#artikel_geplant').val(data.artikel_geplant);
                $('#artikel_geplant').trigger('change');
                $('#abgenommen').prop('checked',data.abgenommen);
                if(teilprojekt)
                {
                    $('#mitarbeiterhinweis').hide();
                    $('#mitarbeiterbutton').prop('disabled',false);
                }else{
                    $('#mitarbeiterhinweis').show();
                    $('#mitarbeiterbutton').prop('disabled',true);
                }
                $('#addteilprojektpopup').dialog('open');

            },
            beforeSend: function() {

            }
        });
        $.ajax({
            type: "POST",
            url: "index.php?module=ajax&action=autosaveuserparameter",
            data:  { name: "teilprojekt_geplante_zeiten_teilprojekt", value: $.base64Encode(teilprojekt+'') }

        });
    }

    $(document).ready(function() {
        $('#auchprojektartikel').on('change',function(){
            if($(this).prop('checked'))
            {
                $('#artikel_zu_beleg_select').children('option[value="loeschen"]').remove();
            }else{
                var optl = $('#artikel_zu_beleg_select').children('option[value="loeschen"]').length;
                if(optl == 0)
                {
                    $('#artikel_zu_beleg_select').append('<option value="loeschen">Artikel l&ouml;schen</option>');
                }
            }
        });
        $('#artikel_geplant').on('focusout',function(){$('#artikel_geplant').trigger('change');});
        $('#ek_geplant').on('focus',function(){$('#artikel_geplant').trigger('change');});
        $('#vk_geplant').on('focus',function(){$('#artikel_geplant').trigger('change');});
        $('#artikel_geplant').on('change',function(){
            $.ajax({
                url: 'index.php?module=projekt&action=dashboard&cmd=getvk&id=[ID]',
                type: 'POST',
                dataType: 'text',
                data: { artikel: $('#artikel_geplant').val()},
                success: function(data) {
                    $('#vkausstammdaten').html(data);

                },
                beforeSend: function() {

                }
            });
            $.ajax({
                url: 'index.php?module=projekt&action=dashboard&cmd=getlink&id=[ID]',
                type: 'POST',
                dataType: 'text',
                data: { artikel: $('#artikel_geplant').val()},
                success: function(data) {
                    $('#artikellink').html(data);

                },
                beforeSend: function() {

                }
            });
        });

        $('#zeiterfassungalle').on('change',function(){
            $('#abrechnungszeitprojektdashboard').find('input[type="checkbox"]').prop('checked',$(this).prop('checked'));
        });


        $('.terminkalenderh2').on('click',function(){
            setTimeout(function(){$('#calendar').trigger('resize');},500);
        });

        var teilprojektsel = null;
        var teilprojektselid = null;
        var teilprojektselname = null;
        $('#teilprojekttable tr').on('mousedown',function(){
            teilprojektsel = this;
            var teilprojektselcb = null;
            $(this).find('input[type="checkbox"]').first().each(function(){teilprojektselcb = this;});
            if(typeof teilprojektselcb != 'undefined' && typeof teilprojektselcb.id != 'undefined')
            {
                var eins = 1;
                var teilprojektselida = teilprojektselcb.id.split('_');
                teilprojektselid = teilprojektselida[eins];
                $(this).find('td').first().next().next().each(function(){
                    var nu = 0;
                    var na = $(this).html().split('<br');
                    teilprojektselname = na[nu];
                });
                $(this).find('td').css('border-bottom','1px solid #333');
                $(this).find('td').css('border-top','1px solid #333');

            }
        });
        $('#teilprojekttable tr').on('mouseover',function(){
            if(teilprojektselid != null)
            {
                var found = false;
                $(this).find('#cb_0').each(function(){found = true});
                $(this).find('#cb_'+teilprojektselid).each(function(){found = true});
                if(!found)
                {
                    $(this).find('td').css('border-bottom','2px solid green');
                    $(this).find('th').css('border-bottom','2px solid green');
                }
            }
        });
        $('#teilprojekttable tr').on('mouseout',function(){
            if(teilprojektselid != null)
            {
                var found = false;
                $(this).find('#cb_0').each(function(){found = true});
                $(this).find('#cb_'+teilprojektselid).each(function(){found = true});
                if(!found)
                {
                    $(this).find('td').css('border-bottom','');
                    $(this).find('th').css('border-bottom','');
                }
            }
        });



        $('#teilprojekttable tr').on('mouseup',function(){

            if(teilprojektselid != null)
            {
                var idae2 = null;
                $(this).find('input[type="checkbox"]').first().each(function(){idae2 = this;});
                var nu = 0;
                var eins = 1;
                if( idae2 != null && typeof (idae2.id) != 'undefined')
                {
                    var idae2 = idae2.id.split('_');
                    if(idae2[eins] != teilprojektselid)
                    {
                        var name2 = null;
                        $(this).find('td').first().next().next().each(function(){
                            var nu = 0;
                            var na = $(this).html().split('<br');
                            name2 = na[nu];
                        });
                        $('#tabnachid').val(idae2[eins]);
                        $('#tabvonid').val(teilprojektselid);
                        $('#modalspanvonkind').html('<strong>"'+teilprojektselname +'"</strong> als Unterprojekt von <strong>"' + name2+'"</strong>');
                        $('#modalspanvon').html('<strong>"' + teilprojektselname +'"</strong> in gleiche Ebene wie <strong>"' + name2+'"</strong>');
                        $('#modalradionach').prop('checked','checked');
                        $('#modalradiokind').prop('checked','');
                        $('#modaltabledrag .kind').css('display','');
                        $('#modaltabledrag').dialog('open');
                    }
                }else{
                    $(this).find('th').first().each(function(){
                        $('#tabnachid').val('');
                        $('#tabvonid').val(teilprojektselid);
                        $('#modalspanvonkind').html('');
                        $('#modalspanvon').html('<strong>"' + teilprojektselname + '"</strong> nach oben verschieben');
                        $('#modalradionach').prop('checked','checked');
                        $('#modalradiokind').prop('checked','');
                        $('#modaltabledrag .kind').css('display','none');

                        $('#modaltabledrag').dialog('open');
                    });
                }

            }
            teilprojektselid = null;
            teilprojektsel = null;
            $('#teilprojekttable tr').find('td').css('border-bottom','');
            $('#teilprojekttable tr').find('td').css('border-top','');
            $('#teilprojekttable tr').find('th').css('border-bottom','');
            $('#teilprojekttable tr').find('th').css('border-top','');
            teilprojektselname = null;
            $('#teilprojekttable tr').css('cursor','');
        });


        $('#cb_0').on('change',function(){
            var checked = $(this).prop('checked');
            $('#teilprojekttable input[type="checkbox"]').prop('checked',checked);
        });

        $('#modaltabledrag').dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 940,
                title:'Teilprojekt verschieben',
                buttons: {
                    VERSCHIEBEN: function()
                    {
                        var von = $('#tabvonid').val();
                        var nach = $('#tabnachid').val();
                        var modus = '';
                        if($('#modalradiokind').prop('checked'))
                        {
                            modus = 'kind';
                        }else{
                            modus = 'nachbar';
                        }
                        if(von != '')
                        {
                            $.ajax({
                                url: 'index.php?module=projekt&action=dashboard&cmd=moveteilprojekt&id=[ID]',
                                type: 'POST',
                                dataType: 'text',
                                data: { vonid: von, nachid: nach,typ: modus},
                                success: function(data) {
                                    window.location.href=window.location.href;
                                },
                                beforeSend: function() {

                                }
                            });
                        }else{
                            $(this).dialog('close');
                        }
                    },
                    ABBRECHEN: function() {
                        $(this).dialog('close');
                    }
                },
                close: function(event, ui){

                }
            });

        $('#changeartikelliste').dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 940,
                title:'Projekt Artikel',
                buttons: {
                    SPEICHERN: function()
                    {
                        $.ajax({
                            url: 'index.php?module=projekt&action=dashboard&cmd=saveprojektartikelliste&id=[ID]',
                            type: 'POST',
                            dataType: 'json',
                            data: { pa: $('#paid').val(), menge_geplant: $('#pa_menge_geplant').val(), ek_geplant: $('#pa_ek_geplant').val(), vk_geplant: $('#pa_vk_geplant').val(),kalkulationbasis:($('#pa_prostueck').prop('checked')?'prostueck':'gesamt'),kommentar:$('#pa_kommentar').val(),showinmonitoring:($('#showinmonitoring').prop('checked')?1:0) },
                            success: function(data) {
                                if(data.status == 1)
                                {
                                    $('#changeartikelliste').dialog('close');
                                }else{
                                    $('#changeartikelliste').dialog('close');
                                }

                                if($('#showinmonitoring').prop('checked')) window.location.href = window.location.href.split("#")[0];
                            },
                            beforeSend: function() {

                            }
                        });
                    },
                    ABBRECHEN: function() {
                        $(this).dialog('close');
                    }
                },
                close: function(event, ui){
                    var oTable = $('#projektdashboardartikelliste').DataTable( );
                    oTable.ajax.reload();
                }
            });

        $('#addteilprojektpopup').dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 940,
                title:'Teilprojekt / Arbeitspaket anlegen',
                buttons: {
                    SPEICHERN: function()
                    {
                        $.ajax({
                            url: 'index.php?module=projekt&action=dashboard&cmd=checkteilprojekt&id=[ID]',
                            type: 'POST',
                            dataType: 'json',
                            data: { uid: $('#uid').val(), vorgaenger: $('#vorgaenger').val(), kind:$('#postypkind').prop('checked')?1:0, modus:$('#cmd').val() },
                            success: function(data) {
                                if(typeof data.status != 'undefined' && data.status == 1)
                                {
                                    $('#frmteilprojekt').submit();
                                }else{
                                    if(typeof data.error != 'undefined')alert(data.error);
                                }
                            }
                        });
                    },
                    ABBRECHEN: function() {
                        $(this).dialog('close');
                    }
                },
                close: function(event, ui){
                    window.location.href=window.location.href;
                }
            });
        $('#projektgeplantezeiten').dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 940,
                minHeight: 600,
                title:'Teilprojekt geplante Zeiten',
                buttons: {
                    'WERTE ÜBERNEHMEN': function()
                    {
                        $('#frmgeplantezeiten').submit();
                    },
                    ABBRECHEN: function() {
                        $(this).dialog('close');
                    }
                },
                close: function(event, ui){
                    window.location.href=window.location.href;
                }
            });




        $('#popupdiv').dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 940,
                width: '90%',
                minHeight:300,
                buttons: {
                    "SPEICHERN + SCHLIESSEN": function()
                    {
                        $('#frmpopupdiv').submit();
                        var rel = $('#reloaddiv').val();
                        var urla = window.location.href.split('#');
                        if(rel)window.location.href=urla[ 0 ];
                        $(this).dialog('close');
                    },
                    ABBRECHEN: function() {
                        $(this).dialog('close');
                        var rel = $('#reloaddiv').val();
                        var urla = window.location.href.split('#');
                        if(rel)window.location.href=urla[ 0 ];
                    }
                },
                close: function(event, ui){
                    var rel = $('#reloaddiv').val();
                    var urla = window.location.href.split('#');
                    if(rel)
                    {
                        $('#artikel_zu_beleg_select').val('');
                        $('#teilprojekt_zu_beleg_select').val('');
                        window.location.href=urla[ 0 ];
                    }else{
                        $('#artikel_zu_beleg_select').val('');
                        $('#teilprojekt_zu_beleg_select').val('');
                    }
                }
            });
        $('#artikel_zu_beleg_select').on('change',function(){
            var nurueberdasprojekt = $('#auchprojektartikel').prop('checked');
            if($('#artikel_zu_beleg_select').val() != '')
            {
                var anz = 0;
                var items = '';
                $('#projektdashboardartikelliste td input:checked').each(function(){
                    anz++;
                    var ida = this.id.split('_');
                    var eins = 1;
                    if(typeof ida[eins] != 'undefined')
                    {
                        if(items != '')items = items + ',';
                        items = items + ''+ida[eins];
                    }
                });

                if(anz > 0)
                {
                    if($('#artikel_zu_beleg_select').val() == 'loeschen')
                    {
                        if(confirm('Wollen Sie die Artikel wirklich löschen?'))
                        {
                            $.ajax({
                                url: 'index.php?module=projekt&action=dashboard&cmd=deleteartikel&id=[ID]',
                                type: 'POST',
                                dataType: 'json',
                                data: { artikelliste: items},
                                success: function(data) {
                                    var oTable = $('#projektdashboardartikelliste').DataTable( );
                                    oTable.ajax.reload();
                                },
                                beforeSend: function() {

                                }
                            });
                        }
                    }else{
                        $.ajax({
                            url: 'index.php?module=projekt&action=dashboard&cmd=open&id=[ID]',
                            type: 'POST',
                            dataType: 'text',
                            data: {nurprojekt:nurueberdasprojekt?1:0, artikelliste: items, typ: 'artikel_zu_beleg',belegtyp: $('#artikel_zu_beleg_select').val()},
                            success: function(data) {
                                $('#popupinhalt').html(data);
                                $('#popupdiv').dialog('open');
                            },
                            beforeSend: function() {

                            }
                        });
                    }
                }else{
                    $('#artikel_zu_beleg_select').val('');
                    alert('Keine Artikel ausgewählt');
                }
            }
        });

        $('#teilprojekt_zu_beleg_select').on('change',function(){
            if($('#teilprojekt_zu_beleg_select').val() != '')
            {
                var anz = 0;
                var items = '';
                $('#teilprojekttable td input:checked').each(function(){
                    anz++;
                    var ida = this.id.split('_');
                    var eins = 1;
                    if(typeof ida[eins] != 'undefined')
                    {
                        if(items != '')items = items + ',';
                        items = items + ''+ida[eins];
                    }
                });

                if(anz > 0)
                {

                    if($('#teilprojekt_zu_beleg_select').val() !== 'offen' && $('#teilprojekt_zu_beleg_select').val() !== 'abgeschlossen' && $('#teilprojekt_zu_beleg_select').val() !== 'gestartet')
                    {
                        $.ajax({
                            url: 'index.php?module=projekt&action=dashboard&cmd=open&id=[ID]',
                            type: 'POST',
                            dataType: 'text',
                            data: { artikelliste: items, typ: 'teilprojekt_zu_beleg',belegtyp: $('#teilprojekt_zu_beleg_select').val()},
                            success: function(data) {
                                $('#popupdiv').dialog('open');
                                $('#popupinhalt').html(data);

                            },
                            beforeSend: function() {

                            }
                        });
                    }else{
                        $.ajax({
                            url: 'index.php?module=projekt&action=dashboard&cmd=teilprojektstatusaendern&id=[ID]',
                            type: 'POST',
                            dataType: 'json',
                            data: { artikelliste: items, status: $('#teilprojekt_zu_beleg_select').val()},
                            success: function(data) {
                                window.location.href=window.location.href;
                            },
                            beforeSend: function() {

                            }
                        });
                    }
                }else{
                    $('#artikel_zu_beleg_select').val('');
                    alert('Keine Artikel ausgewählt');
                }
            }
        });

        $('#einzelartikel_hinzufuegen').on('click',function(){
            $.ajax({
                url: 'index.php?module=projekt&action=dashboard&cmd=open&id=[ID]',
                type: 'POST',
                dataType: 'text',
                data: { typ: 'einzelartikel_hinzufuegen'},
                success: function(data) {
                    $('#popupinhalt').html(data);
                    $('#popupdiv').dialog('open');
                    $('#popuptabs-1').click();
                    $.ajax({
                        url: 'index.php?module=projekt&action=dashboard&id=[ID]',
                        type: 'POST',
                        dataType: 'json',
                        data: { setaccordion: 2},
                        success: function(data) {
                        }
                    });
                }
            });
        });
        $('#stueckliste_hinzufuegen').on('click',function(){
            $.ajax({
                url: 'index.php?module=projekt&action=dashboard&cmd=open&id=[ID]',
                type: 'POST',
                dataType: 'text',
                data: { typ: 'stueckliste_hinzufuegen'},
                success: function(data) {
                    $('#popupinhalt').html(data);
                    $('#popupdiv').dialog('open');
                    $('#popuptabs-2').click();
                }
            });
        });
        $('#artikelliste_hinzufuegen').on('click',function(){
            $.ajax({
                url: 'index.php?module=projekt&action=dashboard&cmd=open&id=[ID]',
                type: 'POST',
                dataType: 'text',
                data: { typ: 'artikelliste_hinzufuegen'},
                success: function(data) {
                    $('#popupinhalt').html(data);
                    $('#popupdiv').dialog('open');
                    $('#popuptabs-3').click();
                }
            });
        });

        /*
				$('#zeiterfassung_zu_beleg_select').on('change',function(){
					if($('#zeiterfassung_zu_beleg_select').val() != '')
					{
						var anz = 1;
						var items = '';

						if(anz > 0)
						{
							$.ajax({
									url: 'index.php?module=projekt&action=dashboard&cmd=open&id=[ID]',
									type: 'POST',
									dataType: 'text',
									data: { artikelliste: items, typ: 'zeiterfassung_zu_beleg',belegtyp: $('#zeiterfassung_zu_beleg_select').val()},
									success: function(data) {
										$('#popupinhalt').html(data);
										$('#popupdiv').dialog('open');
									},
									beforeSend: function() {

									}
							});
						}else{
							$('#artikel_zu_beleg_select').val('');
							alert('Keine Artikel ausgewählt');
						}
					}
				});*/

        /*
				$('#teilprojekttable > tbody').sortable({
						items: "> tr:not(:first)",
						appendTo: "parent",
						helper: "clone"
				}).disableSelection();

				$("#teilprojekttable  > tbody > tr").droppable({
						hoverClass: "drophover",
						tolerance: "pointer",
						drop: function(e, ui) {
								var tabdiv = $(this).attr("href");
								$(tabdiv + " table tr:last").after("<tr>" + ui.draggable.html() + "</tr>");
								ui.draggable.remove();
						}
				});*/

        /*
				$('#accordion h2').on('click',function(){
				var id = this.id;
				if(id == 'ui-accordion-accordion-header-0')
				{
					$('#leftcontainer').toggleClass('col-md-10', false);
					$('#rightcontainer').toggleClass('col-md-2', false);
					$('#leftcontainer').toggleClass('col-md-12', true);
					$('#rightcontainer').css('display','none');

				}else{
					$('#leftcontainer').toggleClass('col-md-12', false);
					$('#leftcontainer').toggleClass('col-md-10', true);
					$('#rightcontainer').toggleClass('col-md-2', true);
					$('#rightcontainer').css('display','');
				}


				});
				 */

        $('#ui-accordion-accordion-header-0').trigger('click');

        $('table#teilprojekttable td.editierbar').on('click',function(){
            var schoneditierbar = false;
            $(this).children('.editierbartext').first().each(function(){schoneditierbar = true;});
            if(schoneditierbar)return;
            var wert = $(this).html();
            var aktel = this;
            var neuesel = null;
            $(this).html('<input type="text" value="'+wert+'" class="editierbartext" size="6" style="margin-left:0;margin-right:0;" />');
            $(this).children('.editierbartext').first().each(function(){neusel = this;});
            $(neusel).focus();
            $(neusel).on('focusout',function(){
                $(aktel).html(wert);
            });
            $(neusel).on('keyup',function(e){
                var key = parseInt(e.which);
                if(key == 13)
                {
                    var neuerwert = $(this).val();
                    var tpid = 0;
                    $(this).parent().parent().find('input:checkbox').first().each(function(){
                        var ida = this.id.split('_');
                        $.ajax({
                            url: 'index.php?module=projekt&action=dashboard&cmd=changezeitgeplant&id=[ID]',
                            type: 'POST',
                            dataType: 'json',
                            data: { paid: ida[ 1 ], zeit_geplant:neuerwert},
                            success: function(data) {
                                if(data.status == 1)
                                {
                                    $('#cb_'+data.id).parent().parent().find('.zeit_geplant').first().each(function(){$(this).html(data.zeit_geplant)});
                                    $('#cb_'+data.id).parent().parent().find('.zeitoffen').first().each(function(){$(this).html(data.zeitoffen)});
                                }
                            }
                        });
                    });
                }
            });
        });

    });

    function clickdashboardbelegeedit(el)
    {
        var span = $(el).parent().find('span').first();
        var ida = $(span).html().split('-');
        var eins = 1;
        var nu = 0;
        var beleg = ida[nu];
        var belegid = parseInt(ida[eins]);
        switch(beleg)
        {
            case '1':
                beleg = 'auftrag';
                break;
            case '2':
                beleg = 'rechnung';
                break;
            case '3':
                beleg = 'gutschrift';
                break;
            case '4':
                beleg = 'angebot';
                break;
            case '5':
                beleg = 'bestellung';
                break;
            case '6':
                beleg = 'produktion';
                break;
            case '7':
                beleg = 'lieferschein';
                break;
            case '8':
                beleg = 'preisanfrage';
                break;
            case '9':
                beleg = 'proformarechnung';
                break;
            case '10':
                beleg = 'reisekosten';
                break;
            default:
                beleg = '';
                break;
        }
        if(belegid > 0 && beleg != '')
        {
            window.open('index.php?module='+beleg+'&action=edit&id='+belegid,'_blank');
        }
    }

    function gotovorgang(artnummer){
        var vorgang = (artnummer.parentElement.firstChild.innerHTML).split("-");

        $.ajax({
            url: 'index.php?module=projekt&action=projektlogbuch&cmd=goto&art='+vorgang[0]+'&id='+vorgang[1],
            type: 'POST',
            dataType: 'json',
            data: {},
            success: function(data) {
                if(data.success == 1){
                    window.open(data.data);
                }else{
                    alert(data.data);
                }
            },
            beforeSend: function() {
            }
        });
    }

</script>
<div id="popupdiv"><div id="popupinhalt">[POPUPDIV]</div></div>
<div id="modaltabledrag" style="display:none;">
	<table>
		<tr><td><input type="hidden" id="tabvonid" value="" /><input type="hidden" id="tabnachid" value="" /><span id="modalspanvon"></span></td><td><input type="radio" name="modalradio" id="modalradionach" /></td></tr>
		<tr class="kind"><td><span id="modalspanvonkind"></span></td><td><input type="radio" name="modalradio" id="modalradiokind" /></td></tr>
	</table>
</div>

<div id="changeartikelliste">
	<fieldset class="planzahl" id="fieldsetplanzahl"><legend>Preise f&uuml;r Projektkalkulation</legend><input type="hidden" id="paid" value="" />
		<table class="mkTableFormular">
			<tr><td>Auswahl:</td><td><input type="radio" name="pa_kalkulationbasis" value="prostueck" id="pa_prostueck" checked="checked" />&nbsp;pro St&uuml;ck<br><input type="radio" name="pa_kalkulationbasis" value="gesamt" id="pa_gesamt" />&nbsp;Gesamt
				</td></tr>

			<tr valign="top"><td>Menge geplant:</td><td><input type="text" name="menge_geplant" id="pa_menge_geplant" size="20"></td></tr>
			<tr valign="top"><td>EK geplant:</td><td><input type="text" name="ek_geplant" id="pa_ek_geplant" size="20"> in EUR <span id="pa_ekausstammdaten"></span></td></tr>
			<tr valign="top"><td>VK geplant:</td><td><input type="text" name="vk_geplant" id="pa_vk_geplant" size="20"> in EUR <span id="pa_vkausstammdaten"></span></td></tr>
			<tr valign="top"><td>Kommentar:</td><td><textarea id="pa_kommentar" name="kommentar"></textarea></td></tr>
			<tr valign="top"><td>In Kostenmonitoring anzeigen</td><td><input type="checkbox" value="1" id="showinmonitoring" name="showinmonitoring" onchange="chshowinmonitoring();" /></td></tr>
		</table>
	</fieldset>
</div>

[MESSAGE]

<div style="padding:10px;">

	<div class="filter-box filter-usersave">
		<div class="filter-block filter-inline">
			<div class="filter-title">{|Filter|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="auchprojektartikel" class="switch">
						<input type="checkbox" value="1" id="auchprojektartikel" name="auchprojektartikel"/>
						<span class="slider round"></span>
					</label>
					<label for="alle">{|nur &uuml;ber das Projekt geplante Artikel|}</label>
				</li>
			</ul>
		</div>
	</div>

	[ARTICLE_TABLE]

	<span style="position:relative;top:-50px;">{|Alle|}: <input type="checkbox" onclick="alleartikel(this);"  /></span>
	<script>
      function alleartikel(el)
      {
          var wert = $(el).prop('checked');
          $('#projektdashboardartikelliste input[type="checkbox"]').prop('checked',wert);
      }
	</script>
	<table width="100%"><tr>
			<td width="50%">
				<fieldset style="height:40px"><legend>{|mit ausgew&auml;hlten Artikeln|}</legend>
					<select style="margin-left:3px" id="artikel_zu_beleg_select" [SELECTDISABLED]>
						<option value="">{|bitte w&auml;hlen ...|}</option>
						<option value="angebot">{|Angebot anlegen|}</option>
						<option value="auftrag">{|Auftrag anlegen|}</option>
						<option value="rechnung">{|Rechnung anlegen|}</option>
						<option value="lieferschein">{|Lieferschein anlegen|}</option>
						<option value="gutschrift">{|Gutschrift anlegen|}</option>
						<option value="bestellung">{|Bestellung anlegen|}</option>
						<option value="produktion">{|Produktion anlegen|}</option>
						<option value="preisanfrage">{|Preisanfrage anlegen|}</option>
						<option value="proformarechnung">{|Proformarechnung anlegen|}</option>
						<option value="loeschen">{|Artikel l&ouml;schen|}</option>
					</select>
				</fieldset>
			</td><td>
				<fieldset style="height:40px"><legend>{|Aktionen|}</legend>
					<input type="button" value="Artikel zu Teilprojekt hinzufügen" id="einzelartikel_hinzufuegen" [SELECTDISABLED] />
				</fieldset>
			</td></tr>
	</table>

</div>
