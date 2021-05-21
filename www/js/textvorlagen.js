/**
 * Textvorlagen initialisieren
 */
var initTextTemplates = function () {
    attachClickListener();
    attachFocusTextElementsListener();
    attachFocusCkEditorListener();

    initTextTemplateModal();

    // Nach dem Schließen des Textvorlagen-Modals bestimmte Focus-Events wieder attachen.
    // Notwendig, da jQuery-UI scheinbar alle Focus-Events entfernt.
    $('#textvorlagenModal').on('dialogclose', function () {
        attachFocusTextElementsListener();
        clearTextTemplateTargetMarker();
    });
};

var attachClickListener = function () {

    // Textvorlage-Modal öffnen
    $('input#edit').on('click', function (e) {
        e.preventDefault();
        setTextTemplateTargetMarker();
        openTextTemplateModal();
    });

    // "Textvorlage übernehmen"
    $(document).on('click', '.text-template-apply', function (e) {
        e.preventDefault();
        var textTemplateId = $(this).data('apply-id');
        applyTextTemplate(textTemplateId);
    });

    // "Textvorlage bearbeiten"
    $(document).on('click', '.text-template-edit', function (e) {
        e.preventDefault();
        var textTemplateId = $(this).data('edit-id');
        editTextTemplate(textTemplateId);

        // Scroll to edit form
        var fieldsetTop = $('#textvorlageneingabe').offset().top;
        var viewportHeight = $(window).innerHeight();
        var offsetTop = fieldsetTop - (viewportHeight / 2);
        $('html, body').animate({
            scrollTop: offsetTop
        },'slow');
    });

    // "Textvorlage löschen"
    $(document).on('click', '.text-template-delete', function (e) {
        e.preventDefault();
        var textTemplateId = $(this).data('delete-id');
        deleteTextTemplate(textTemplateId);
    });

    // "Textvorlage speichern"
    $('#textvorlagespeichern').click(function (e) {
        e.preventDefault();
        saveTextTemplate();
    });
};

/**
 * Focus-Event auf alle Input- und Textarea-Element attachen
 */
var attachFocusTextElementsListener = function () {
    $(document).on('focusin', 'input, textarea', function () {
        var focusedElement = $(this);
        var elementType = focusedElement.get(0).nodeName.toLowerCase();

        // Elemente im Textvorlagen-Modal ignorieren
        var isTextTemplateModalElement = (focusedElement.parents('#textvorlagenModal').length > 0);
        if (isTextTemplateModalElement) {
            return;
        }

        // Iframe im Iframe
        // z.B. "Position bearbeiten"-Popup in Auftragspositionen-Iframe
        if (typeof parent.parent !== 'undefined') {
            parent.parent.lastFocusedElement = focusedElement;
            parent.parent.lastFocusedType = elementType;
            return;
        }

        // Ein Iframe
        // z.B. Auftragspositionen-Iframe
        if (typeof parent !== 'undefined') {
            parent.lastFocusedElement = focusedElement;
            parent.lastFocusedType = elementType;
            return;
        }

        // Kein Iframe
        lastFocusedElement = focusedElement;
        lastFocusedType = elementType;
    });
};

/**
 * Focus-Event auf alle CKEditor-Elemente attachen
 */
var attachFocusCkEditorListener = function () {
    CKEDITOR.on('instanceReady', function (evt) {
        var editor = evt.editor;
        editor.on('focus', function () {

            // Elemente im Textvorlagen-Modal ignorieren
            if (editor.name.substr(0, 11) === 'textvorlage') {
                return;
            }

            // CKEditor im Iframe vom Iframe (z.b. Auftragspositionen-Bearbeiten-Popup)
            if (typeof parent.parent !== 'undefined') {
                parent.parent.lastFocusedElement = evt.editor;
                parent.parent.lastFocusedType = 'ckeditor';
                return;
            }

            // CKEditor im Iframe (z.B. Auftragspositionen)
            if (typeof parent !== 'undefined') {
                parent.lastFocusedElement = evt.editor;
                parent.lastFocusedType = 'ckeditor';
                return;
            }

            // CKEditor im "normalen" Content
            lastFocusedElement = evt.editor;
            lastFocusedType = 'ckeditor';
        });
    });
};

/**
 * Textvorlagen von HTML befreien
 *
 * Notwendig für Input- und Textarea-Elemente
 * Bei Input-Elementen zusätzlich Zeilenumbrüche entfernen
 *
 * @param {String} html
 * @param {String} elementType [input|textarea]
 *
 * @return {String}
 */
var cleanupTextTemplate = function (html, elementType) {
    html = html.replace('<br />', '<br>');
    var htmlLines = html.split('<br>');

    // Bei Textarea: BR's in Zeilenumbrüche wandeln
    // Bei Input: BR's in Leerzeichen wandeln
    var seperator = elementType === 'textarea' ? '\r\n' : ' ';

    var first = true;
    var result = '';
    var plaintext = '';
    $.each(htmlLines, function (k, v) {
        plaintext = $('<div>' + v + '</div>').text();
        if (first === true) {
            result += plaintext;
        } else {
            result += seperator + plaintext;
        }
        first = false;
    });

    return result;
};

/**
 * Dialog/Modal konfigurieren
 */
var initTextTemplateModal = function () {
    $("#textvorlagenModal").dialog({
        modal: true,
        bgiframe: true,
        closeOnEscape: false,
        autoOpen: false,
        minWidth: 940,
        buttons: {
            ABBRECHEN: function () {
                clearTextTemplateModal();
                closeTextTemplateModal();
            }
        }
    });
};

/**
 * Fokusiertes Eingabeelement hervorheben
 */
var setTextTemplateTargetMarker = function () {
    if (typeof lastFocusedElement !== 'undefined') {
        if (lastFocusedType === 'ckeditor') {
            lastFocusedElement.container.addClass('textvorlagen-target-marker');
        } else {
            lastFocusedElement.addClass('textvorlagen-target-marker');
        }
    }
};

/**
 * Hervorhebung von fokusiertem Eingabeelement entfernen
 */
var clearTextTemplateTargetMarker = function () {
    if (typeof lastFocusedElement !== 'undefined') {
        if (lastFocusedType === 'ckeditor') {
            lastFocusedElement.container.removeClass('textvorlagen-target-marker');
        } else {
            lastFocusedElement.removeClass('textvorlagen-target-marker');
        }
    }
    $('.textvorlagen-target-marker').removeClass('textvorlagen-target-marker');
};

/**
 * Modal öffnen
 */
var openTextTemplateModal = function () {
    $('#textvorlagenModal').dialog('open');
    if (!DataTableHelper.isInitialized('texttemplates')) {
        DataTableHelper.initDataTable('texttemplates');
    }
    clearTextTemplateModal();
};

/**
 * Modal schließen
 */
var closeTextTemplateModal = function () {
    $('#textvorlagenModal').dialog('close');
    clearTextTemplateTargetMarker();
    clearTextTemplateModal();
};

/**
 *  Modal-Eingabefelder leeren
 */
var clearTextTemplateModal = function () {
    $('#textvorlageid').val('');
    $('#textvorlagename').val('');
    $('#textvorlagetext').val('');
    $('#textvorlageprojekt').val('');
    $('#textvorlagestichwoerter').val('');
};

/**
 * Textvorlage übernehmen
 *
 * @param {Number} textTemplateId
 */
var applyTextTemplate = function (textTemplateId) {

    // Kein Eingabe-Element hatte bisher den Focus
    if (typeof lastFocusedElement === 'undefined') {
        closeTextTemplateModal();
        return;
    }

    /*
     * Textvorlagen-Inhalt aus DataTable selektieren
     */
    var textTemplateHtml = '';
    var rowIdAttr = '#texttemplates_row_' + textTemplateId;
    $('#textvorlagenModal').find(rowIdAttr).each(function () {
        textTemplateHtml = $(this).find('td').eq(1).html();
    });

    /*
     * Textvorlage in CKEditor-Instanz einfügen
     */

    if (typeof lastFocusedElement === 'object' && lastFocusedType === 'ckeditor') {
        // Timeout ist notwendig, weil iPad ansonsten nach oben scrollt.
        // Mit Timeout bleibt die Scroll-Position unverändert.
        window.setTimeout(function() {
            lastFocusedElement.insertHtml(textTemplateHtml);
        }, 10);

        closeTextTemplateModal();
        return;
    }

    /*
     * Textvorlage in Input- oder Textarea-Element einfügen
     */

    // Textvorlage von HTML befreien
    var textTemplateText = cleanupTextTemplate(textTemplateHtml, lastFocusedType);

    // Textmarkierung auslesen
    var selectionStart = lastFocusedElement.get(0).selectionStart;
    var selectionEnd = lastFocusedElement.get(0).selectionEnd;

    // Textmarkierung wurde nicht erkannt > Inhalt komplett ersetzen
    if (typeof selectionStart === 'undefined' || typeof selectionEnd === 'undefined') {
        lastFocusedElement.val(textTemplateText);
        closeTextTemplateModal();
        return;
    }

    // Nur markierten Text durch Textvorlage ersetzen
    var textBeforeSelectedText = lastFocusedElement.val().substring(0, selectionStart);
    var textAfterSelectedText = lastFocusedElement.val().substring(selectionEnd, lastFocusedElement.val().length);
    lastFocusedElement.val(textBeforeSelectedText + textTemplateText + textAfterSelectedText);
    closeTextTemplateModal();
};

/**
 * Textvorlage bearbeiten
 *
 * @param {Number} id
 */
var editTextTemplate = function (id) {
    var rowIdAttr = '#texttemplates_row_' + id;
    $('#textvorlagenModal').find(rowIdAttr).each(function () {
        var $cells = $(this).find('td');
        $('#textvorlagename').val($cells.eq(0).html());
        $('#textvorlageid').val(id);
        $('#textvorlagetext').val($cells.eq(1).html());
        $('#textvorlagestichwoerter').val($cells.eq(2).html());
        $('#textvorlageprojekt').val($cells.eq(3).html());
    });
};

/**
 * Textvorlage speichern
 */
var saveTextTemplate = function () {
    $.post("index.php?module=textvorlagen&action=save", {
        textvorlageid: $('#textvorlageid').val(),
        textvorlagename: $('#textvorlagename').val(),
        textvorlagetext: $('#textvorlagetext').val(),
        textvorlageprojekt: $('#textvorlageprojekt').val(),
        textvorlagestichworter: $('#textvorlagestichwoerter').val()
    })
    .done(function (data) {
        DataTableHelper.refreshDataTable('texttemplates');
    });
};

/**
 * Textvorlage löschen
 *
 * @param {Number} id
 */
var deleteTextTemplate = function (id) {
    var r = confirm("Textvorlage Löschen?");
    if (r === true) {
        $.post(
            "index.php?module=textvorlagen&action=save", {
                textvorlageid: id,
                deletetextvorlage: 1
            })
        .done(function (data) {
            DataTableHelper.refreshDataTable('texttemplates');
        });
    }
};

$(document).ready(function () {
    initTextTemplates();
});
