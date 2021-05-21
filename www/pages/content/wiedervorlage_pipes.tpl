<style>
    .ui-sortable-helper #wiedervorlage-minidetail-container {
        display: none;
    }
    #wiedervorlage-minidetail-container {
        z-index: 99999;
        display: block;
        position: absolute;
        top: 0;
        left: -1px;
        min-width: 320px;
        max-width: 400px;
        padding: 10px;
        background-color: #FFF;
        box-shadow: rgba(0, 0, 0, .25) 0 0 10px;
        border: 2px solid rgb(166, 201, 226);
    }
    #wiedervorlage-minidetail-container .detail {
        margin-bottom: 10px;
    }
    #wiedervorlage-minidetail-container .detail > div {
        margin: 5px;
    }
    #wiedervorlage-minidetail-container .title {
        font-size: 110%;
        font-weight: bold;
    }
    #wiedervorlage-minidetail-container .description {
        line-height: 1.3em;
        max-height: 100px;
        overflow-y: auto;
    }
    #wiedervorlage-minidetail-container .overdue {
        font-weight: bold;
        color: orangered;
    }
    #wiedervorlage-minidetail-container .timeline {
        font-size: 85%;
    }
    #wiedervorlage-minidetail-container .timeline ul {
        margin: 0;
        padding: 0;
    }
    #wiedervorlage-minidetail-container .timeline .tl-wrap {
        padding-top: 10px;
        padding-bottom: 10px;
    }
    #wiedervorlage-minidetail-container .tl-wrap:before {
        top: 25px;
    }
    #wiedervorlage-minidetail-container .tl-date {
        top: 0;
        text-align: left;
    }
    #wiedervorlage-minidetail-container .tl-content {
        top: 3px;
    }
    #wiedervorlage-minidetail-container .arrow.pull-up {
        display: none;
    }

		#datacontent {
			width: auto;
			margin: 15px 0 0 0;
		}

    #scroll-wrapper {
			  width: 100%;
        overflow-x: auto;
        overflow-y: visible;
    }
    #scroll-area {
			  display: flex;
        min-height: 100vh;
				flex-wrap: nowrap;
				justify-content: flex-start;
				align-items: stretch;
        -ms-user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
        border-left: 1px solid var(--fieldset-dark);
        margin-bottom: 60px;
    }

    ul[class^="draggable"] {
        list-style-type: none;
        margin: 0;
        padding: 0;
    }
    ul[class^="draggable"] > li {
        position: relative;
        margin: 0;
        padding: 5px 10px;
        min-height: 52px;
    }
    ul[class^="draggable"] > li:hover {
    }
    .ui-sortable-helper {
        z-index: 1001;
        box-shadow: rgba(0, 0, 0, .5) 1px 1px 3px;
    }

    .same_height {
        height: [COLUMNHEIGHT];
    }
    .sortable_column {
  			flex-grow: 1;
			  flex-direction: row;
				flex-basis: 200px;
        min-width: 150px;
        max-width: 350px;
        box-sizing: border-box;
        margin: 0;
        border-right: 1px solid var(--fieldset-dark);
        border-top: 1px solid var(--fieldset-dark);
        border-bottom: 1px solid var(--fieldset-dark);
        background: var(--fieldset);
    }
    .sortable_heading {
        border-bottom: 1px solid var(--fieldset-dark);
    }
    .sortable_heading h3 {
        color: var(--grey);
        white-space: nowrap;
        text-overflow: ellipsis;
        font-weight: bold;
        margin: 0;
        padding: 5px 10px;
    }

    .sortable_placeholder {
        background-color: #FFC;
        border-bottom: 1px solid var(--fieldset-dark);
    }

    .wiedervorlageitem {
        cursor: pointer;
        border: none;
        background-color: #FFF !important;
        border-bottom: 1px solid var(--fieldset-dark);
    }
    .wiedervorlageitem.is-finished,
    .wiedervorlageitem.is-finished .item-center {
        text-decoration: line-through;
    }

    .item-center {
        width: 100%;
        padding-right: 22px;
        padding-left: 40px;
    }
    .item-center .row {
        width: 100%;
        padding: 0;
        margin: 2px 0;
    }
    .item-center .row-name {
        font-weight: normal;
    }
    .item-center .row-bezeichnung {
        word-wrap: break-word;
        font-weight: bold;
    }
    .item-center .additional-field-value {
        color: #848484;
    }
    .item-left {
        position: absolute;
        top: 0;
        left: 0;
        width: 50px;
        height: 100%;
        border-left-style: solid;
        border-left-width: 10px;
        border-left-color: #eee;
    }
    .item-left img {
        width: 30px;
        height: auto;
        margin-top: 7px;
        margin-left: 5px;
    }
    .item-right {
        clear: right;
        position: absolute;
        top: 5px;
        right: 10px;
        width: 20px;
    }
    .item-right .icon-today-due,
    .item-right .icon-over-due,
    .item-right .icon-under-due,
    .item-right .icon-prio {
        display: inline-block;
        width: 20px;
        height: 20px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: auto;
    }
    .item-right .icon-today-due {
        background-image: url("./themes/new/images/sales_gruen.png");
    }
    .item-right .icon-over-due {
        background-image: url("./themes/new/images/sales_rot.png");
    }
    .item-right .icon-under-due {
        background-image: url("./themes/new/images/sales_grau.png");
    }
    .item-right .icon-prio {
        background-image: url("./themes/new/images/sales_achtung_gelb.png");
    }

    #error {
        color: red;
    }

    #deal {
        position: fixed;
        z-index: 920;
        left: 0;
        right: 0;
        bottom: 0;
        padding: 15px;
        filter: alpha(opacity=0);
        -moz-opacity: 0;
        opacity: 0;
        text-align: center;
        transition: all ease-in-out .3s;
        background-color: dimgrey;
        -ms-user-select: none;
        -webkit-user-select: none;
        -moz-user-select: none;
        user-select: none;
    }
    #deal.open {
        opacity: 1;
        transition: opacity ease-in-out .3s;
    }
    #deal-inner {
        max-width: 900px;
        min-width: 300px;
        margin: 0 auto;
    }
    .droppable-container {
        float: left;
        width: 33%;
    }
    .droppable {
        overflow: hidden;
        box-sizing: border-box;
        max-width: 300px;
        min-height: 35px;
        padding: 5px;
        margin: 5px;
        border-radius: 15px;
        color: white;
        font-size: 30px;
        text-align: center;
    }
    .droppable.gewinner {
        background: #4EC560;
    }
    .droppable.verlierer {
        background: #E14449;
    }
    .droppable.papierkorb {
        background: #bcbcbc;
    }
    .droppable.highlight {
        background: orange !important;
    }

    #page_container > div > div.ui-tabs-panel {
        margin-bottom: 70px !important;
    }

    @media only screen and (max-width: 650px){
        .droppable {
            font-size: 21px;
        }
    }

    .filter-item {
        float: left;
        padding: 0 10px 10px 0;
    }
</style>
<script type="text/javascript">

    var WiedervorlagenMiniDetail = function($) {

        var me = {

            settings: {
                // Zeit die gewartet wird; vom Hovern bis AJAX abgesetzt wird (in Millisekunden)
                bufferTime: 500
            },

            selector: {
                minidetail: '#wiedervorlage-minidetail-container',

                // Selektor für alle Wiedervorlagen
                wiedervorlageItems: '.wiedervorlageitem'
            },

            storage: {
                // Wird Minidetail gerade angezeigt?
                currentOpen: false,

                // ID der aktuell geöffneten Wiedervorlage
                currentId: null,

                // ID der Wiedervorlage die aktuell überfahren wird
                currentHoverId: null,

                // StageID der aktuell geöffneten Wiedervorlage
                currentStageId: null,

                // Puffer-Variable
                bufferTimeout: null
            },

            /**
             * MiniDetail-Feature initialisieren
             */
            init: function () {
                me.attachEvents();
            },

            /**
             * Registiert alle benötigten Events
             */
            attachEvents: function () {
                $(me.selector.wiedervorlageItems)
                    .on('mouseenter mouseover', function () {
                        var $current = $(this);

                        // Merken welche Wiedervorlage gerade überfahren wird
                        me.storage.currentHoverId = parseInt($current.data('id'));

                        // Puffer zurücksetzen
                        clearTimeout(me.storage.bufferTimeout);

                        // Pufferzeit warten, dann Wiedervorlage-MiniDetail per AJAX laden
                        me.storage.bufferTimeout = setTimeout(
                            function () {
                                var wiedervorlageId = parseInt($current.data('id'));
                                var stageId = parseInt($current.parents('.sortable_column').data('id'));
                                me.loadMiniDetail(wiedervorlageId, stageId);
                            },
                            me.settings.bufferTime
                        );
                    })
                    .on('mouseleave', function () {
                        // Merken wenn sich Maus gerade über keiner Wiedervorlage befindet
                        me.storage.currentHoverId = null;
                        // Timer zurücksetzen, wenn innerhalb der Pufferzeit das Element wieder verlassen wird
                        clearTimeout(me.storage.bufferTimeout);
                        // Alle MiniDetails löschen
                        me.closeAll();
                        me.storage.deactivateItem = null;
                    })
                    .on('click', function () {
                        var wiedervorlageId = $(this).data('id');
                        EditWiedervorlage(wiedervorlageId);
                    });
            },

            /**
             * MiniDetail-Daten per AJAX laden und rendern
             *
             * @param {number} wiedervorlageId
             * @param {number} stageId
             */
            loadMiniDetail: function (wiedervorlageId, stageId) {
                if (typeof wiedervorlageId === 'undefined') {
                    return;
                }

                // MiniDetail ist für die aktuelle ID und Stage schon geöffnet
                if (me.storage.currentId === wiedervorlageId &&
                    me.storage.currentOpen === true
                ) {
                    return;
                }

                $.ajax({
                    url: 'index.php?module=wiedervorlage&action=pipes&cmd=minidetail',
                    data: {
                        id: wiedervorlageId
                    },
                    method: 'post',
                    dataType: 'json',
                    success: function (data, textStatus) {
                        if (typeof data !== 'object') {
                            me.closeAll();
                            return;
                        }

                        // AJAX ist da, Maus befindet sich zwischenzeitlich über keiner Wiedervorlage
                        if (me.storage.currentHoverId === null) {
                            return;
                        }

                        // AJAX ist da, Maus befindet sich zwischenzeitlich über anderer Wiedervorlage
                        if (me.storage.currentHoverId !== parseInt(data.id)) {
                            return;
                        }

                        me.closeAll();
                        $('<div id="wiedervorlage-minidetail-container"></div>').appendTo('#wiedervorlageitem-' + data.id);
                        $(me.selector.minidetail).html(me.renderMiniDetail(data));

                        me.storage.currentOpen = true;
                        me.storage.currentId = parseInt(data.id);
                        me.storage.currentStageId = parseInt(data.stage);
                    },
                    fail: function () {
                        me.closeAll();
                    },
                    error: function () {
                        alert('Hoppla, Wiedervorlage konnte nicht geladen werden');
                        me.destroy();
                    }
                });
            },

            /**
             * Generiert HTML für MiniDetail-Container
             *
             * @param {array} data
             *
             * @return {string} HTML-Struktur
             */
            renderMiniDetail: function (data) {
                var result =
                    '<div class="detail">'+
                    '  <div class="title"><strong>{{title}}</strong></div>'+
                    '  <div class="duedate {{overDue}}">Fällig am: {{duedate}}</div>'+
                    '  <div class="description">{{description}}</div>'+
                    '</div>'+
                    '<div class="timeline">{{timeline}}</div>';

                if (data.abgeschlossen === '1') {
                    data.bezeichnung += ' (Abgeschlossen)';
                }
                result = result.replace('{{title}}', data.bezeichnung);
                result = result.replace('{{overDue}}', (data.ueberfaellig === '1') ? 'overdue' : '');
                result = result.replace('{{duedate}}', me.formatDateTime(data.erinnerung));
                result = result.replace('{{description}}', data.beschreibung);
                result = result.replace('{{timeline}}', me.generateTimelineHtml(data.timeline));

                return result;
            },

            /**
             * Generiert HTML für die Timeline
             *
             * @param {array} data
             *
             * @return {string} HTML-Struktur
             */
            generateTimelineHtml: function (data) {
                var result = '<ul>';
                var tpl =
                    '<li class="tl-item">'+
                    '  <div class="tl-wrap">'+
                    '    <div class="tl-date">{{time}} - {{username}}</div>'+
                    '    <div class="tl-content panel padder b-a">'+
                    '      <span class="arrow left pull-up"></span>'+
                    '      <div class="content">{{content}}</div>'+
                    '    </div>'+
                    '  </div>'+
                    '</li>';

                for (var i = 0; i < data.length; i++) {
                    var eventHtml = tpl;
                    eventHtml = eventHtml.replace('{{username}}', data[i].username);
                    eventHtml = eventHtml.replace('{{content}}', data[i].content);
                    eventHtml = eventHtml.replace('{{time}}', me.formatDateTime(data[i].time));

                    result += eventHtml;
                }
                result += '</ul>';

                return result;
            },

            /**
             * Alle MiniDetails schließen
             */
            closeAll: function () {
                $(me.selector.minidetail).remove();
                me.storage.currentOpen = false;
                me.storage.currentId = null;
                me.storage.currentStageId = null;
            },

            /**
             * Wandelt DATETIME-Wert aus Datenbank in ein schönes Format
             *
             * @example "2018-06-21 13:37:01" > "Donnerstag, 21. Juni 2018, 13:37 Uhr"
             *
             * @param {string} value
             *
             * @return {string} Datum in "schön"
             */
            formatDateTime: function (value) {
                var date = new Date(value.replace(' ', 'T'));
                var options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: 'numeric', minute: 'numeric' };

                return date.toLocaleDateString('de-DE', options) + ' Uhr';
            },

            /**
             * Minidetail-Feature kurz deaktivieren
             */
            deactivateForCurrentItem: function () {
                var oldCurrentId = me.storage.currentId;
                var oldCurrentHoverId = me.storage.currentHoverId;

                // Minidetail schließen und vorgaukeln dass es gerade geöffent ist
                me.closeAll();
                me.storage.currentOpen = true;
                me.storage.currentId = oldCurrentId;
                me.storage.currentHoverId = oldCurrentHoverId;
            },

            /**
             * Deaktiviert das MiniDetail-Feature
             */
            destroy: function () {
                me.closeAll();
                $(me.selector.wiedervorlageItems).off('mouseenter mouseleave');
            }
        };

        return {
            init: me.init,
            closeAll: me.closeAll,
            destroy: me.destroy,
            deactivateForCurrentItem: me.deactivateForCurrentItem
        };

    }(jQuery);

    $(document).ready(function () {

        WiedervorlagenMiniDetail.init();

        setColumnsToSameHeight();

        $('ul.draggable').sortable({
            cursor: 'move',
            connectWith: 'ul',
            placeholder: 'sortable_placeholder',
            forcePlaceholderSize: true,
            start: function (event, ui) {
                $('#deal').addClass('open');
                ui.item.off('click'); // Klick-Event entfernen
            },
            stop: function (event, ui) {
                $('#deal').removeClass('open');
                setTimeout(function () {
                    // Klick-Event nach kurzer Wartezeit wieder attachen; ansonsten führt Drag-n-Drop zu Klick
                    ui.item.on('click', function () {
                        var wiedervorlageId = $(this).data('id');
                        EditWiedervorlage(wiedervorlageId);
                    });
                }, 250);
            },
            receive: function (event, ui) {
                if ($(ui.item).hasClass('cancel-sorting')) {
                    return; // Sorting abbrechen: Item wurde auf "Gewinner" oder "Verlierer" abgelegt
                }
                setColumnsToSameHeight();
                hideFirstColumnWhenEmpty();
                WiedervorlagenMiniDetail.deactivateForCurrentItem();

                $.ajax({
                    url: 'index.php?module=wiedervorlage&action=edit&cmd=move',
                    data: {
                        //Alle Felder die fürs editieren vorhanden sind
                        id: ui.item.data('id'),
                        stage: $(event.target).data('id'),
                        position: ui.item.index()
                    },
                    method: 'post',
                    dataType: 'json',
                    error: function (jqXhr) {
                        // Sortierung abbrechen > Wiedervorlage an Ursprung verschieben
                        $(ui.sender).sortable('cancel');

                        // Unbekannter Fehler; ErrorHandler wird vermutlich angezeigt
                        if (!jqXhr.hasOwnProperty('responseJSON')) {
                            alert('Fehler beim Verschieben der Wiedervorlage: Unbekannter Server-Fehler.');
                            return;
                        }

                        // Modal anzeigen; welche Items (Aufgaben/Freifelder) haben das Verschieben blockiert
                        if (jqXhr.responseJSON.hasOwnProperty('data') &&
                            jqXhr.responseJSON.data.hasOwnProperty('blocking')
                        ) {
                            ResubmissionBlockingItemsModal.show(jqXhr.responseJSON.data);
                            return;
                        }

                        // Bekannter Fehler; Error-Property anzeigen
                        if (jqXhr.responseJSON.hasOwnProperty('error')) {
                            var alertMsg = '';
                            alertMsg += 'Fehler beim Verschieben der Wiedervorlage ID' + ui.item.data('id') + ': ';
                            alertMsg += jqXhr.responseJSON.error;
                            alert(alertMsg);
                        }
                    }
                });
            }
        }).disableSelection();

        $('.droppable').droppable({
            hoverClass: 'highlight',
            tolerance: 'pointer',
            drop: function (event, ui) {
                if (this.id == '99') {
                    // Wir öffnen den Gewinner Dialog
                    $('#editWinnerLoser #editwinnerloserwiedervorlagestageid').val(ui.draggable.data('id'));
                    $('#editWinnerLoser #editwinnerlosertype').val('gewonnen');
                    $('#editWinnerLoser').dialog('option', 'title', 'Gewinner');
                    $('#editWinnerLoser').dialog('option', 'width', '600');
                    $('#editWinnerLoser').dialog('open');
                    $(ui.draggable).addClass('cancel-sorting');

                } else if (this.id == '100') {
                    // Wir öffnen den Verlierer Dialog
                    $('#editWinnerLoser #editwinnerloserwiedervorlagestageid').val(ui.draggable.data('id'));
                    $('#editWinnerLoser #editwinnerlosertype').val('verloren');
                    $('#editWinnerLoser').dialog('option', 'title', 'Verlierer');
                    $('#editWinnerLoser').dialog('option', 'width', '600');
                    $('#editWinnerLoser').dialog('open');
                    $(ui.draggable).addClass('cancel-sorting');

                } else if (this.id == '98') {
                    // Papierkorb
                    $(ui.draggable).addClass('cancel-sorting');
                    $.ajax({
                        url: 'index.php?module=wiedervorlage&action=edit&cmd=delete',
                        data: {
                            id: ui.draggable.data('id'),
                        },
                        method: 'post',
                        dataType: 'json',
                    });
                    ui.draggable.remove();
                }
            }
        });

        $("#editWinnerLoser").dialog({
            modal: true,
            bgiframe: true,
            minWidth: 420,
            autoOpen: false,
            closeOnEscape: false,
            open: function (event, ui) {
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons: {
                ABBRECHEN: function () {
                    // wir laden neu, damit der Item wieder in die richtige Pipe kommt
                    window.location.reload();
                },
                SPEICHERN: function () {
                    if ($("#editWinnerLoser textarea").val() == '') {
                        $("#editWinnerLoser h4#error").html('Das Kommentarfeld darf nicht leer sein');
                    } else {
                        WiedervorlageLeadClose();
                    }
                }
            }
        });

        // Seite neuladen, wenn Filter verändert wird
        var $pipeFilter = $('.filter-item input');
        $pipeFilter.change(function () {
            window.setTimeout(function () {
                window.location.reload();
            }, 250);
        });
    });

    function WiedervorlageLeadClose() {
        $.ajax({
            url: 'index.php?module=wiedervorlage&action=abschliessen',
            data: {
                //Alle Felder die fürs editieren vorhanden sind
                id: $("#editWinnerLoser #editwinnerloserwiedervorlagestageid").val(),
                type: $("#editWinnerLoser #editwinnerlosertype").val(),
                timelinekommentar: $("#editWinnerLoser textarea").val(),
                jsonresponse: 'true'
            },
            method: 'post',
            dataType: 'json',
            success: function (data) {
                window.location.reload();
            }
        });
    }

    // Setzt alle Stages auf die gleiche Höhe
    function setColumnsToSameHeight() {
        var maxColumnHeight = 0;

        $('.same_height').each(function () {
            var columnHeight = 40; // Heading
            $(this).children().each(function () {
                columnHeight += $(this).outerHeight();
                columnHeight += 5; // Margin
            });
            columnHeight += 40; // Placeholder

            if (columnHeight > maxColumnHeight) {
                maxColumnHeight = columnHeight;
            }
        });

        $('.same_height').height(maxColumnHeight);
    }

    // Spalte "Ohne Stage" ausblenden, wenn keine Wiedervorlagen enthalten
    function hideFirstColumnWhenEmpty() {
        $firstColumn = $('#sortable_column_0');
        if ($firstColumn.find('li').length === 0) {
            $firstColumn.hide();
        }
    }

</script>

[WIEDERVORLAGEPOPUP]

<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
    <div id="tabs-1">

        <div class="rTabs">
            <ul>
                <li><a href="index.php?module=wiedervorlage&action=dashboard&mitarbeiter=[MITARBEITER]">{|Dashboard|}</a></li>
                <li class="aktiv"><a href="index.php?module=wiedervorlage&action=pipes&mitarbeiter=[MITARBEITER]">{|Pipelines|}</a></li>
                <li><a href="index.php?module=wiedervorlage&action=table&mitarbeiter=[MITARBEITER]">{|Liste|}</a></li>
                <li><a href="index.php?module=wiedervorlage&action=creationdate&mitarbeiter=[MITARBEITER]">{|Eingangsdatum|}</a></li>
                <li><a href="index.php?module=wiedervorlage&action=closingdate&mitarbeiter=[MITARBEITER]">{|Abschlussdatum|}</a></li>
                <li><a href="index.php?module=wiedervorlage&action=winsloses&mitarbeiter=[MITARBEITER]">{|Wins/Losses|}</a></li>
                <li><a href="index.php?module=wiedervorlage&action=calendar&mitarbeiter=[MITARBEITER]">{|Kalender|}</a></li>
            </ul>
            <div class="rTabSelect">[ANSICHTSELECT]&nbsp;[MITARBEITERSELECT]</div>
            <div class="clear"></div>
        </div>

        <fieldset>
            <legend>Filter</legend>
            <div class="clear"></div>
            <div class="filter-item">
                <label class="switch">
                    <input type="checkbox" id="prio" name="prio" value="1" [FILTERPRIOCHECKED]>
                    <span class="slider round"></span>
                    Prio
                </label>
            </div>
            <div class="filter-item">
                <label class="switch">
                    <input type="checkbox" id="faellige" name="faellige" value="1" [FILTERFAELLIGECHECKED]>
                    <span class="slider round"></span>
                    fällige
                </label>
            </div>
            [VORMEINE]
            <div class="filter-item">
                <label class="switch">
                    <input type="checkbox" id="meine" name="meine" value="1" [FILTERMEINECHECKED]>
                    <span class="slider round"></span>
                    meine
                </label>
            </div>
            <div class="filter-item">
                <label class="switch">
                    <input type="checkbox" id="nur_meine_vergebenen" name="nur_meine_vergebenen" value="1" [FILTERNURMEINEVERGEBENENCHECKED]>
                    <span class="slider round"></span>
                    nur meine vergebenen
                </label>
            </div>
            [NACHMEINE]
            <div class="filter-item">
                <label class="switch">
                    <input type="checkbox" id="abgeschlossen" name="abgeschlossen" value="1" [FILTERABGESCHLOSSENCHECKED]>
                    <span class="slider round"></span>
                    mit abgeschlossene
                </label>
            </div>
        </fieldset>

        <div id="datacontent" class="row">
						[TAB1]
						[TAB1NEXT]
						<div id="scroll-wrapper">
								<div id="scroll-area" class="clearfix">
										[PIPES]
								</div>
						</div>
        </div>
    </div>
<!-- tab view schließen -->

    <div id="editWinnerLoser" style="display:none;" title="Bearbeiten">
        <input type="hidden" id="editwinnerloserwiedervorlagestageid" value="">
        <input type="hidden" id="editwinnerlosertype" value="">
        <h4 id="error"></h4>
        <table>
            <tr>
                <td><textarea id="editWinnerLoserkommentar" name="kommentar" cols="50"></textarea></td>
            </tr>
        </table>
    </div>

</div>

<div id="deal">
    <div id="deal-inner">
        <div class="droppable-container">
            <div id="98" class="droppable papierkorb">Papierkorb</div>
        </div>
        <div class="droppable-container">
            <div id="99" class="droppable gewinner">Gewinner</div>
        </div>
        <div class="droppable-container">
            <div id="100" class="droppable verlierer">Verlierer</div>
        </div>
    </div>
</div>
