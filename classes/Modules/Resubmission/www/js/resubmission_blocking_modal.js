/**
 * Modal zur Anzeige von blockierenden Aufgabe/Freifeldern
 *
 * Modal wird angezeigt wenn beim Verschieben von Wiedervorlagen (auf eine andere Stage)
 * die zugeodneten Aufgaben oder Freifelder nicht die Anforderungen erfüllen.
 */
var ResubmissionBlockingItemsModal = (function ($) {
    "use strict";

    var me = {

        storage: {
            $modal: null,
            data: null,
            displayEditButton: true
        },

        /**
         * @param {Object}  data
         * @param {Boolean} displayEditButton "Wiedervorlage bearbeiten"-Button in Modal anzeigen?
         */
        show: function (data, displayEditButton) {
            if (typeof displayEditButton === 'boolean') {
                me.storage.displayEditButton = displayEditButton;
            }

            me.storage.data = data;
            me.storage.$modal = me.createModal();
            me.storage.$modal.dialog('open');
        },

        /**
         */
        hide: function () {
            if (me.storage.$modal === null) {
                return;
            }

            me.storage.$modal.dialog('close');
        },

        /**
         * @return {jQuery}
         */
        createModal: function () {
            var $prevModal = $('#resubmissiontask-blocking-items-modal');
            if ($prevModal.length > 0) {
                $prevModal.remove();
            }

            var data = me.storage.data;
            var $modal = $('<div>').attr('id', 'resubmissiontask-blocking-items-modal').appendTo('body').hide();
            var content = '';

            if (data.blocking.type === 'change-stage') {
                content = '<p>Die Wiedervorlage &quot;' + data.resubmission.title + '&quot; kann nicht ';
                content += 'in die Stage &quot;' + data.stage.title + '&quot; verschoben werden, weil ';
                content += 'folgende Element blockieren:</p>';
            }
            if (data.blocking.type === 'create-resubmission') {
                content = '<p>Die Wiedervorlage kann nicht in der Stage &quot;' + data.stage.title + '&quot; ';
                content += 'angelegt werden, weil folgende Element blockieren:</p>';
            }
            if (data.blocking.type === 'update-resubmission') {
                content = '<p>Die Wiedervorlage kann nicht in der Stage &quot;' + data.stage.title + '&quot; ';
                content += 'gespeichert werden, weil folgende Element blockieren:</p>';
            }

            content += '<ul>';
            $.each(data.blocking.tasks, function (index, task) {
                content += '<li><p>Aufgabe &quot;' + task.title + '&quot;<br><strong>nicht abgeschlossen</strong></p></li>';
            });
            $.each(data.blocking.textfields, function (index, textfield) {
                content += '<li><p>Freitextfeld &quot;' + textfield.label + '&quot;<br><strong>ist leer</strong></p></li>';
            });
            content += '</ul>';
            $modal.html(content);

            var modalTitle = data.blocking.hasOwnProperty('title') ? data.blocking.title : 'Speichern nicht möglich';
            var modalButtons = [{
                text: 'OK',
                click: function () {
                    $modal.dialog('close');
                }
            }];
            if (
                me.storage.displayEditButton === true &&
                me.storage.data.resubmission.id > 0 // Neue Wiedervorlage
            ) {
                modalButtons.unshift({
                    text: 'Wiedervorlage bearbeiten',
                    click: function () {
                        EditWiedervorlage(me.storage.data.resubmission.id);
                        $modal.dialog('close');
                    }
                });
            }

            $modal.dialog({
                modal: true,
                bgiframe: true,
                minWidth: 420,
                autoOpen: false,
                closeOnEscape: false,
                title: modalTitle,
                buttons: modalButtons
            });

            return $modal;
        }
    };

    return {
        show: me.show,
        hide: me.hide
    };

})(jQuery);
