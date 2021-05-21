<link rel="stylesheet" type="text/css" href="css/jquery.timeline.css?v=3"/>
<script src="js/jquery.timeline.js?v=3"></script>


<script>
    var ReminderStageSelection = (function ($) {
        
        var me = {

            storage: {
                currentStageId: null,
                reminderId: null,
                stages: null
            },

            /**
             *
             * @param {number} reminderId
             * @param {number} currentStageId
             * @param {array} stages
             */
            init: function (reminderId, currentStageId, stages) {
                me.storage.reminderId = reminderId;
                me.storage.currentStageId = currentStageId;
                me.storage.stages = stages;
                if (me.storage.stages.length === 0) {
                    return;
                }

                // Button-Status setzen
                me.markActiveStages();
            },

            /**
             * Markiert Stage-Buttons als aktiv/inaktiv
             */
            markActiveStages: function () {
                me.storage.stages.forEach(function (stage) {
                    var $button = $('#stage-selection-button' + stage.id);
                    $button.toggleClass('active', stage.active);
                    $button.off('click').on('click', function (e) {
                        e.preventDefault();
                        var stageId = $button.data('id');
                        me.onStageSelect(stageId);
                    });
                });
            },

            /**
             * @param {number} stageId
             */
            onStageSelect: function (stageId) {
                $.ajax({
                    url: 'index.php?module=wiedervorlage&action=edit&cmd=stageselected',
                    data: {
                        selectedStageId: stageId,
                        reminderId: me.storage.reminderId
                    },
                    method: 'post',
                    dataType: 'json',
                    success: function (data) {

                        // Bei Klick auf die aktuell gewählte Stage soll die Wiedervorlage
                        // in die Stage davor verschoben werden.
                        stageId = data.current_stage_id;

                        // Stage-Eingabefeld füllen, falls Formular gespeichert wird
                        data.stage_selection.forEach(function (stage) {
                            if (stage.id === stageId) {
                                $('#editstages').val(stage.name);

                                // Beim Wechsel der Stage > Chance aus Stage übernehmen
                                if (typeof stage.chance !== 'undefined' && stage.chance !== null) {
                                    $('#editchance').val(stage.chance);
                                }
                            }
                        });

                        // Timeline füllen
                        if (data.timeline != null) {
                            $('#element').html('');
                            $('#element').timeline('destroy', [{ key: 'value'} ]);
                            $('#element').timeline({
                                data: dateTimeUmschreiben(data.timeline)
                            });
                        }

                        // Button-Status setzen
                        me.init(me.storage.reminderId, stageId, data.stage_selection);
                    },
                    error: function (jqXhr) {

                        // Unbekannter Fehler; ErrorHandler wird vermutlich angezeigt
                        if (!jqXhr.hasOwnProperty('responseJSON')) {
                            alert('Fehler beim Verschieben der Wiedervorlage: Unbekannter Server-Fehler.');
                            return;
                        }

                        // Modal anzeigen; welche Items (Aufgaben/Freifelder) haben das Verschieben blockiert
                        if (jqXhr.responseJSON.hasOwnProperty('data')) {
                            ResubmissionBlockingItemsModal.show(jqXhr.responseJSON.data);
                            return;
                        }

                        // Bekannter Fehler; Error-Property anzeigen
                        if (jqXhr.responseJSON.hasOwnProperty('error')) {
                            var alertMsg = '';
                            alertMsg += 'Fehler beim Verschieben der Wiedervorlage ID' + me.storage.reminderId + ': ';
                            alertMsg += jqXhr.responseJSON.error;
                            alert(alertMsg);
                        }
                    }
                });
            }
        };
        
        return {
            init: me.init
        };
        
    })(jQuery);

	$(document).ready(function () {

		$("#editdatum_abschluss").datepicker({
        dateFormat: 'dd.mm.yy',
        dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'],
        firstDay: 1,
        showWeek: true,
        monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    });
		$("#editdatum_erinnerung").datepicker({
        dateFormat: 'dd.mm.yy',
        dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'],
        firstDay: 1,
        showWeek: true,
        monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
    });
		$("#editzeit_erinnerung").timepicker();


		$("#editWiedervorlage").dialog({
			modal: true,
			bgiframe: true,
			closeOnEscape: false,
			minWidth: 1280,
			minHeight: 750,
			autoOpen: false,
			buttons: {
				[DATEIBUTTON]
				ABBRECHEN
	:

		function () {
			WiedervorlageReset();
			$(this).dialog('close');
		}

	,
		SPEICHERN: function () {
			WiedervorlageSave();
		}
	}
	})
		;

		$("#editWiedervorlage").dialog({
			close: function (event, ui) {
				WiedervorlageReset();
			}
		});


	});


	function addEditor(selector) {

		var ckdata_textvorlagetext = $(selector).val();
		if (typeof ckdata_textvorlagetext != 'undefined' && ckdata_textvorlagetext.indexOf("<") < 0) {
			var ckdataanz_textvorlagetext = 0;
			while (ckdataanz_textvorlagetext < 100 && ckdata_textvorlagetext.indexOf("\r\n") > -1) {
				ckdataanz_textvorlagetext++;
				ckdata_textvorlagetext = ckdata_textvorlagetext.replace(/\r\n/g, "<br />");
			}
			ckdataanz_textvorlagetext = 0;
			while (ckdataanz_textvorlagetext < 100 && ckdata_textvorlagetext.indexOf("\n") > -1) {
				ckdataanz_textvorlagetext++;
				ckdata_textvorlagetext = ckdata_textvorlagetext.replace(/\n/g, "<br />");
			}
			$(selector).val(ckdata_textvorlagetext);
		}
		$(selector).ckeditor({
			toolbar:
				[
					['Bold', 'Italic', 'Underline', '-', 'Undo', 'Redo'], ['NumberedList', 'BulletedList'],
					['Font', 'FontSize', 'TextColor'], ['Source']
				]
		}).editor.updateElement();

	}


	function CreateTimelineItem(id) {
		$.ajax({
			url: 'index.php?module=wiedervorlage&action=edit&cmd=timelinecreate',
			data: {
				wiedervorlageid: id
			},
			method: 'post',
			dataType: 'json',
			beforeSend: function () {
				App.loading.open();
			},
			success: function (data) {
				if (data.timeline != null) {
					// Timeline füllen
					$("#element").html('');
					$("#element").timeline("destroy", [
						{
							key: 'value'
						}
					]);
					$("#element").timeline({
						data: dateTimeUmschreiben(data.timeline)
					});

				}
				$('.SaveTimeLineBtn').hide();
				$('.EditTimeLineBtn').show();
				$('.DeleteTimeLineBtn').show();
        $('.timeline-message').show();
        $('.timeline-textarea').hide();

        var $timelineItem = $('#TimeLineItem_' + data.timeline[0].id);
        var $timelineTextarea = $timelineItem.find('textarea').show();
				$timelineTextarea.focus();
				$timelineTextarea.select();

        $timelineItem.find('.timeline-message').hide();
				$timelineItem.find('a.EditTimeLineBtn').hide();
				$timelineItem.find('a.DeleteTimeLineBtn').hide();
				$timelineItem.find('a.SaveTimeLineBtn').show();
			}
		});

	}


	function SaveTimelineItem(item) {
		$.ajax({
			url: 'index.php?module=wiedervorlage&action=edit&cmd=timelinesave',
			data: {
				wiedervorlagetimelineid: item,
				wiedervorlageid: $('#editWiedervorlage').find('#editid').val(),
				content: $('#TimeLineItem_' + item).find('textarea').val()
			},
			method: 'post',
			dataType: 'json',
			success: function (data) {
				if ($("#element ul.timeline").length) {
					$("#element").html('');
					$("#element").timeline("destroy", [
						{
							key: 'value'
						}
					]);
				}
				if (data.timeline != null) {
					// Timeline füllen
					$("#element").timeline({
						data: dateTimeUmschreiben(data.timeline)
					});
				}
			}
		});
		// Speichervorgang wenn OK, dann Rest ausführen
		$('.editTimeline').prop('disabled', true);
		$('.SaveTimeLineBtn').hide();
		$('.EditTimeLineBtn').show();
		$('.DeleteTimeLineBtn').show();

	}


	function EditTimelineItem(item) {
		$('.SaveTimeLineBtn').hide();
		$('.EditTimeLineBtn').show();
		$('.DeleteTimeLineBtn').show();
		$('.timeline-message').show();
		$('.timeline-textarea').hide();

		var $timelineItem = $('#TimeLineItem_' + item);

		var $timelineMessage = $timelineItem.find('.timeline-message').hide();
		var message = $timelineMessage.html();
		var linebreakCount = (message.match(/<br>/g)||[]).length;

    var $timelineTextarea = $timelineItem.find('textarea');
		$timelineTextarea.addClass('TimeLineEdit');
		$timelineTextarea.prop('rows', linebreakCount+1);
    $timelineTextarea.show();

		$timelineItem.find('a.EditTimeLineBtn').hide();
		$timelineItem.find('a.DeleteTimeLineBtn').hide();
		$timelineItem.find('a.SaveTimeLineBtn').show();
	}

	function DeleteTimelineItem(item) {
		$.ajax({
			url: 'index.php?module=wiedervorlage&action=edit&cmd=timelinedelete',
			data: {
				wiedervorlagetimelineid: item,
				wiedervorlageid: $('#editWiedervorlage').find('#editid').val()
			},
			method: 'post',
			dataType: 'json',
			success: function (data) {
				if ($("#element ul.timeline").length) {
					$("#element").html('');
					$("#element").timeline("destroy", [
						{
							key: 'value'
						}
					]);
				}
				if (data.timeline != null) {
					// Timeline füllen
					$("#element").timeline({
						data: dateTimeUmschreiben(data.timeline)
					});
				}
			}
		});
	}

	function EditWiedervorlage(id,refresh=false) {
		$.ajax({
			url: 'index.php?module=wiedervorlage&action=edit&cmd=get',
			data: {
				id: id
			},
			method: 'post',
			dataType: 'json',
			beforeSend: function () {
				App.loading.open();
			},
			success: function (data) {

        if (typeof data.stage_selection !== 'undefined') {
          ReminderStageSelection.init(data.id, data.stage_id, data.stage_selection);
        }

        // Tabs initialisieren (Verlauf/Aufgaben/Freifelder)
 			  var $tabs = $('#resubmission-tabs').tabs({
            disabled: [1, 2, 3] // Aufgaben- und Freifeld-Tabs deaktivieren
 			  });

        // Modul zur Bedienung der Aufgaben initialisieren
        if (data.hasOwnProperty('id') && data.id !== 0) {
          ResubmissionTasksUi.init(data.id);
          $tabs.tabs('enable', '#resubmission-tasks-tab'); // Aufgaben-Tab aktivieren
        }

        // // Zusatzfelder-Adresstabelle-Tab aktivieren
        if (data.hasOwnProperty('id') && data.id !== 0) {
          $tabs.tabs('enable', '#resubmission-addressfields-tab');
        }

        // Modul zur Bedienung der Freifelder initialisieren
        if (data.hasOwnProperty('textfields')) {
            ResubmissionTextFieldUi.init(data.textfields);
            if (data.hasOwnProperty('id') && data.id !== 0) {
                $tabs.tabs('enable', '#resubmission-textfields-tab');
            }
        }

        $('#editWiedervorlage').find('#e_id').val(data.id);
				if(refresh = true) {
				  $('#editWiedervorlage').find('#e_refresh').val(1);
				}else{
				  $('#editWiedervorlage').find('#e_refresh').val(0);
				}
				$('#editWiedervorlage').find('#editid').val(data.id);
				$('#editWiedervorlage').find('#editbezeichnung').val(data.bezeichnung);
				$('#editWiedervorlage').find('#editbearbeiter').val(data.bearbeiter);
				$('#editWiedervorlage').find('#editadresse').val(data.adresse);
				$('#editWiedervorlage').find('#editansprechpartner').val(data.ansprechpartner);
				$('#editWiedervorlage').find('#editprojekt').val(data.projekt);
				$('#editWiedervorlage').find('#editsubproject').val(data.subproject);
				$('#editWiedervorlage').find('#editbetrag').val(data.betrag);
				$('#editWiedervorlage').find('#editbeschreibung').val(data.beschreibung);
				$('#editWiedervorlage').find('#editstages').val(data.stages);
				$('#editWiedervorlage').find('#editcolor').val(data.color);
				$('#editWiedervorlage').find('#editcolor').change();
				$('#editWiedervorlage').find('#editadresse_mitarbeiter').val(data.adresse_mitarbeiter);
				$('#editWiedervorlage').find('#editzeit_erinnerung').val(data.zeit_erinnerung);
				$('#editWiedervorlage').find('#editzeit_angelegt').val(data.zeit_angelegt);
				$('#editWiedervorlage').find('#editdatum_angelegt').val(data.datum_angelegt);
        $('#editWiedervorlage').find('#editdatum_abschluss').val(data.datum_abschluss);
				$('#editWiedervorlage').find('#editdatum_erinnerung').val(data.datum_erinnerung);
				$('#editWiedervorlage').find('#editdatum_erinnerung_permail').prop("checked", data.erinnerung_per_mail == 1 ? true : false);

				$('#editWiedervorlage').find('#editprio').prop("checked", data.prio == 1 ? true : false);
				$('#editWiedervorlage').find('#editabgeschlossen').prop("checked", data.abgeschlossen == 1 ? true : false);
				$('#editWiedervorlage').find('#TimeLIneNewBtn').attr('data-wiedervorlage-id', data.id);

        $("input#editansprechpartner").autocomplete({
            source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+data.adresse_id,
        });

                if (data.kontaktdaten !== undefined && data.kontaktdaten !== null) {
                    var kontaktdaten = '';
                    $(data.kontaktdaten).each(function (index, zeile) {
                        if (zeile.value === '') {
                            return;
                        }
                        kontaktdaten += '<div>';
                        kontaktdaten += '<span class="label">' + zeile.label + ':</span>';
                        kontaktdaten += '<span class="value">';
                        if (data.placetel_aktiv === '1' && zeile.placetel) {
                            kontaktdaten += '<a href="#" onclick="call(\''+zeile.placetel+'\');return false;">' + zeile.value + '</a>';
                        } else if (zeile.link) {
                            kontaktdaten += '<a href="' + zeile.link + '" target="_blank">' + zeile.value + '</a>';
                        } else {
                            kontaktdaten += zeile.value;
                        }
                        kontaktdaten += '</span>';
                        kontaktdaten += '</div>';
                    });
                  kontaktdaten += '<div><a class="button" target="_blank" href="./index.php?module=adresse&action=edit&id='+data.adresse_id+'" style="margin:5px 0;">Zur Adresse</a></div>';
                  $('#kontaktdaten-container').show();
                  $('#kontaktdaten-content').html(kontaktdaten);
                } else {
                    $('#kontaktdaten-container').hide();
                    $('#kontaktdaten-content').html('');
                }

          // Freifelder-Tabelle zusammenbauen
          if (data.freifelder_adresse !== undefined && data.freifelder_adresse !== null) {
              var freifeldHtml = '<table id="" class="mkTableFormular" width="100%" border="0">';
              var freifelderCount = 0;
              $(data.freifelder_adresse).each(function (index, freifeld) {
                  var freifeldValue = typeof freifeld.value === 'string' && freifeld.value !== 'null' ? freifeld.value : '';
                  freifeldHtml += '<tr>';
                  freifeldHtml += '<td width="160" class="label">';
                  freifeldHtml += '<label>' + freifeld.label + '</label>';
                  freifeldHtml += '</td>';
                  freifeldHtml += '<td class="value">';
                  freifeldHtml += '<input type="text" id="' + freifeld.id + '" data-key="' + freifeld.key + '" name="freifeld[' + freifeld.key + ']" value="' + freifeldValue + '">';
                  freifeldHtml += '</td>';
                  freifeldHtml += '</tr>';
                  freifelderCount++;
              });
              freifeldHtml += '</table>';
              $('#freifelder-adresse-content').html(freifeldHtml);
              $('#freifelder-adresse-container').show();
              $tabs.tabs('enable', '#resubmission-addressfields-tab'); // Adressen-Zusatzfelder-Tab aktivieren

          } else {
              var freifeldInfo = '';
              if (typeof data.freifelder_adresse_konfiguriert !== 'undefined' && data.freifelder_adresse_konfiguriert === false) {
                  freifeldInfo = '<div class="info">Es sind keine Zusatzfelder-Adresstabelle konfiguriert. ';
                  freifeldInfo += '<a href="index.php?module=wiedervorlage&action=settings#tabs-1">Zu den Einstellungen</a>';
                  freifeldInfo += '</div>';
              } else {
                  freifeldInfo = '<div class="info">Zusatzfelder-Adresstabelle können nicht angezeigt werden. ';
                  freifeldInfo += 'Die Wiedervorlage ist keiner Adresse zugewiesen.';
                  freifeldInfo += '</div>';
              }
              $('#freifelder-adresse-content').html(freifeldInfo);
          }

				if ($("#element ul.timeline").length) {
					$("#element").html('');
					$("#element").timeline("destroy", [
						{
							key: 'value'
						}
					]);
				}

				if (data.timeline != null) {
					// Timeline füllen
					$("#element").timeline({
						data: dateTimeUmschreiben(data.timeline)
					});
				}

				if (data.chance == "" || data.chance <= 0)
					$('#editWiedervorlage').find('#editchance').val('0');
				else
					$('#editWiedervorlage').find('#editchance').val(data.chance);


        // Wiedervorlage bearbeiten > Labels laden
        data.id = parseInt(data.id);
        if (!isNaN(data.id) || data.id > 0) {
          $('#editWiedervorlage').find('#editlabelrow').show();
          $('#editlabelcontainer').labels({
            referenceTable: 'wiedervorlage',
            referenceId: data.id
          });
        }
        // Wiedervorlage anlegen > Labels ausblenden
        if (isNaN(data.id) || data.id === 0) {
          $('#editWiedervorlage').find('#editlabelrow').hide();
        }

				App.loading.close();

				[AFTERPOPUPOPEN]

				$("#editWiedervorlage").dialog('open');
			}
		});
	}

	function dateTimeUmschreiben(value) {
		value.forEach(function (item, index) {
			value[index].time = new Date(value[index].time.replace(' ', 'T'));
		});
		return value;
	}

	function WiedervorlageReset() {
    ResubmissionTasksUi.destroy();

		$('#editWiedervorlage').find('#editid').val('');
		$('#editWiedervorlage').find('#e_refresh').val('');
		$('#editWiedervorlage').find('#editchance').val(0);
		$('#editWiedervorlage').find('#editprio').prop("checked", false);
		$('#editWiedervorlage').find('#editabgeschlossen').prop("checked", false);
		$('#editWiedervorlage').find('#editbezeichnung').val('');
		$('#editWiedervorlage').find('#editbeschreibung').val('');
		$('#editWiedervorlage').find('#editbetrag').val('');
		$('#editWiedervorlage').find('#editcolor').val('#a2d624');
		$('#editWiedervorlage').find('#editcolor').change();
		$('#editWiedervorlage').find('#editdatum_abschluss').val('');
		$('#editWiedervorlage').find('#editdatum_erinnerung').val('');
		$('#editWiedervorlage').find('#editzeit_erinnerung').val('');
		$('#editWiedervorlage').find('#editdatum_erinnerung_permail').val('');
		$('#editWiedervorlage').find('#editbearbeiter').val('');
		$('#editWiedervorlage').find('#editadresse').val('');
		$('#editWiedervorlage').find('#editansprechpartner').val('');
		$('#editWiedervorlage').find('#editprojekt').val('');
		$('#editWiedervorlage').find('#editsubproject').val('');
		$('#editWiedervorlage').find('#editstages').val('');
		$('#editWiedervorlage').find('#editadresse_mitarbeiter').val('');
		$('#editWiedervorlage').find('#editzeit_angelegt').val('');
		$('#editWiedervorlage').find('#editdatum_angelegt').val('');
		$('#editWiedervorlage').find('#TimeLIneNewBtn').attr('data-wiedervorlage-id', '0');

		// Labels zurücksetzen
		$('#editWiedervorlage').find('#editlabelrow').show();
    var $labelContainer = $('#editWiedervorlage').find('#editlabelcontainer').html('');
    var labelsApi = $labelContainer.data('labelsApi');
    if (typeof labelsApi !== 'undefined') {
        labelsApi.hideOverlay();
    }

    $("input#editansprechpartner").autocomplete({
        source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+0,
    });

	}


	function WiedervorlageSave() {
	  // Freifelder-Adresse fürs Speichern aufbereiten
	  var freifeldAdresseData = new Object();
	  var $freifelderAdresse = $('#freifelder-adresse-container');
    $freifelderAdresse.find('input').each(function (index, element) {
        var $elem = $(element);
        var key = $elem.data('key');
        freifeldAdresseData[key] = $elem.val();
    });

    // Freifelder-Wiedervorlage fürs Speichern aufbereiten
    var textfieldsResubmissionData = new Object();
    var $textfieldsResubmission = $('input.resubmission-textfield-content');
    $textfieldsResubmission.each(function (index, element) {
        var $elem = $(element);
        var configId = $elem.data('resubmissionTextfieldConfigId');
        textfieldsResubmissionData[configId] = $elem.val();
    });

    $.ajax({
			url: 'index.php?module=wiedervorlage&action=edit&cmd=save',
			data: {
				//Alle Felder die fürs editieren vorhanden sind
				id: $('#editid').val(),
				chance: $('#editchance').val(),
				prio: $('#editprio').prop("checked") ? 1 : 0,
				abgeschlossen: $('#editabgeschlossen').prop("checked") ? 1 : 0,
				bezeichnung: $('#editbezeichnung').val(),
				beschreibung: $('#editbeschreibung').val(),
				betrag: $('#editbetrag').val(),
				color: $('#editcolor').val(),
				datum_abschluss: $('#editdatum_abschluss').val(),
				datum_erinnerung: $('#editdatum_erinnerung').val(),
				zeit_erinnerung: $('#editzeit_erinnerung').val(),
				editdatum_erinnerung_permail: $('#editdatum_erinnerung_permail').prop("checked") ? 1 : 0,
				bearbeiter: $('#editbearbeiter').val(),
				adresse: $('#editadresse').val(),
        ansprechpartner: $('#editansprechpartner').val(),
				projekt: $('#editprojekt').val(),
				subproject: $('#editsubproject').val(),
				stages: $('#editstages').val(),
				adresse_mitarbeiter: $('#editadresse_mitarbeiter').val(),
				zeit_angelegt: $('#editzeit_angelegt').val(),
				datum_angelegt: $('#editdatum_angelegt').val(),
        freifelder_adresse: freifeldAdresseData,
        textfields_resubmission: textfieldsResubmissionData
			},
			method: 'post',
			dataType: 'json',
			beforeSend: function () {
				App.loading.open();
			},
			success: function (data) {
        App.loading.close();
        if (data.status == 1) {
          if ($('#editWiedervorlage').find('#e_refresh').val() == '1') {
            WiedervorlageReset();
            $("#editWiedervorlage").dialog('close');
            updatePipeItem(data.values);
          } else {
            $("#editWiedervorlage").dialog('close');
          }
          updateLiveTable();
        } else {
          alert(data.statusText);
        }
      },
      error: function (jqXhr) {
        // Unbekannter Fehler; ErrorHandler wird vermutlich angezeigt
        if (!jqXhr.hasOwnProperty('responseJSON')) {
          alert('Fehler beim Speichern der Wiedervorlage: Unbekannter Server-Fehler.');
          return;
        }

          // Modal anzeigen; welche Items (Aufgaben/Freifelder) haben das Verschieben blockiert
        if (jqXhr.responseJSON.hasOwnProperty('data') && jqXhr.responseJSON.data.hasOwnProperty('blocking')) {
          ResubmissionBlockingItemsModal.show(jqXhr.responseJSON.data, false);
          return;
        }

        // Pflicht-Freitextfelder sind leer
        if (
            jqXhr.responseJSON.hasOwnProperty('data') &&
            jqXhr.responseJSON.data.hasOwnProperty('type') &&
            jqXhr.responseJSON.data.hasOwnProperty('errors')
        ) {
            // Freitext-Tab aktivieren
            var $tabs = $('#resubmission-tabs');
            $tabs.tabs('enable', '#resubmission-textfields-tab');

            // Auf Freitext-Tab wechseln
            $tabs.find('a[href="#resubmission-textfields-tab"]').trigger('click');

            // Fehler-Meldungen anzeigen
            ResubmissionTextFieldUi.renderErrorMessages(jqXhr.responseJSON.data.errors);
            return;
        }

        // Bekannter Fehler; Error-Property anzeigen
        if (jqXhr.responseJSON.hasOwnProperty('error')) {
          alert('Fehler beim Speichern der Wiedervorlage: ' + jqXhr.responseJSON.error);
        }
      }
		});
	}

	// Wiedervorlage in Sales-Funnel-Ansicht aktualisieren
	function updatePipeItem(values) {
	  var $pipeItem = $('#wiedervorlageitem-' + values.id);
	  if ($pipeItem.length > 0) {

	      // Prüfen ob hinterlegte View-ID noch mit dem angezeigtem View übereinstimmt
        var currentViedId = $('#resubmission-view-id').val();
        if (typeof currentViedId !== 'undefined' && values.view_id !== null) {
            currentViedId = parseInt(currentViedId, 10);
            var valuesViewId = parseInt(values.view_id, 10);
            if (currentViedId !== valuesViewId) {
                // Wiedervorlage wurde in anderes Board/View verschoben > Element aus DOM entfernen
                $pipeItem.remove();
                return;
            }
        }

        // Inhalte ändern
        $pipeItem.find('.row-name').html(values.name).toggle(values.name !== null);
        $pipeItem.find('.row-bezeichnung').html(values.bezeichnung);
        $pipeItem.find('.row-beschreibung').html(values.beschreibung);
        $pipeItem.find('.row-erinnerung').html(values.erinnerung);
        $pipeItem.find('.row-betrag').html(values.betrag_formatiert);
        $pipeItem.find('.row-mitarbeiter').html('MA: ' + values.mitarbeiter).toggle(values.mitarbeiter !== null);
        $pipeItem.find('.toolbar-adresse').attr('href', 'index.php?module=adresse&action=edit&id=' + values.adresse).toggle(values.adresse !== null);
        $pipeItem.find('.item-left').css('border-left-color', values.color);

        // Profilbild aktualisieren
        if (values.mitarbeiter_id !== null) {
            var $profileImage = $pipeItem.find('img.profile-image');
            $profileImage.attr('src', 'index.php?module=ajax&action=profilbild&id=' + values.mitarbeiter_id);
            $profileImage.attr('alt', values.mitarbeiter);
            $profileImage.attr('title', values.mitarbeiter);
        }

        $pipeItem.toggleClass('is-prio', values.prio === '1');
        $pipeItem.toggleClass('is-overdue', values.ueberfaellig === '1');
        $pipeItem.toggleClass('is-finished', values.abgeschlossen === '1');

        // Wiedervorlage in andere Stage verschieben
        var currentPipeId = $pipeItem.parent('ul').data('id');
        if (currentPipeId != values.stage) {
          $pipeItem.appendTo('#wiedervorlage-pipe-' + values.stage);
          if (values.stage === '0') {
            // Spalte "Ohne Stage" einblenden
            $('#sortable_column_0').show();
          }
        }

        // Geänderte Wiedervorlage kurz hervorheben
        $pipeItem.effect('highlight', 'slow');
    } else {
        // Neue Wiedervorlage wurde angelegt > Seite neu laden
        window.location.reload();
    }
  }

	function updateLiveTable(i) {
		var oTableL = $('#wiedervorlage').dataTable();
		if (oTableL) {
			oTableL.fnFilter('%');
			oTableL.fnFilter('');
		}
	}

    function call(id, dummy){
        $.ajax({
            url: 'index.php?module=placetel&action=call&id='+id,
            type: 'POST',
            dataType: 'json',
            data: { },
            success: function(data) {
                if(data)
                {

                }
            }
        });
    }
</script>
<style>
    .wiedervorlage_timeline_hidden {
        display: none !important;
    }

    .wiedervorlage_timeline_text {
        border: none;
    }

    .SaveTimeLineBtn {
        display: none;
    }
    #kontaktdaten-content > div {
        float: left;
        width: 50%;
        padding: 5px 0;
    }
    #kontaktdaten-content .label {
        display: inline-block;
        width: 35%;
    }
    #kontaktdaten-content .value {
        display: inline-block;
        width: 65%;
    }
    #stage-selection {
        width: 100%;
        table-layout: fixed;
        border-collapse: separate;
        border-spacing: 5px 0;
    }
    #stage-selection td {
        padding: 0;
        margin: 0;
    }
    #stage-selection a {
        display: block;
        width: 100%;
        padding: 10px 0;
        text-align: center;
        color: var(--gray);
        background-color: var(--fieldset);
        border: 1px solid var(--fieldset-dark);
        border-radius: 7px;
    }
    #stage-selection a.active {
        color: #FFF;
        border-color: var(--green);
        background-color: var(--green);
    }
</style>
[DATEIENPOPUP]
<div id="editWiedervorlage" style="display:none;" title="Bearbeiten">
    <input type="hidden" id="e_id">
    <input type="hidden" id="e_refresh">
    <div class="row">
        <div class="row-height">
            <div class="col-xs-12 col-sm-7 col-sm-height">
                <div class="inside_white inside-full-height">
                    [POPUPSTAGESELECTION]
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="row-height">
            <div class="col-xs-12 col-sm-7 col-sm-height">
                <div class="inside inside-full-height">
                    <input type="hidden" id="editid">
                    <fieldset>
                        <legend>{|Wiedervorlage|}</legend>
                        <table class="mkTableFormular">
                            <tr>
                                <td width="160">{|Bezeichnung|}:</td>
                                <td colspan="3"><input type="text" name="editbezeichnung" size="80"
                                                       id="editbezeichnung"></td>
                            </tr>
                            <tr>
                                <td width="">{|f&uuml;r Kunde|}:</td>
                                <td colspan="3"><input type="text" name="editadresse" id="editadresse" size="80">[LINKADRESSE]
                                </td>
                            </tr>
                            <tr>
                                <td width="">{|Ansprechpartner|}:</td>
                                <td colspan="3"><input type="text" name="editansprechpartner" id="editansprechpartner" size="80">[LINKANSPRECHPARTNER]</td>
                            </tr>
                            <tr id="kontaktdaten-container">
                                <td width="">{|Kontaktdaten|}:</td>
                                <td colspan="3" id="kontaktdaten-content"></td>
                            </tr>
                            <tr>
                                <td width="">{|Projekt|}:</td>
                                <td colspan="3"><input type="text" name="editprojekt" id="editprojekt" size="80">[LINKPROJEKT]
                                </td>
                            </tr>
                            <tr>
                                <td width="">{|Teilprojekt|}:</td>
                                <td colspan="3"><input type="text" name="editsubproject" id="editsubproject" size="80">[LINKSUBPROJECT]
                                </td>
                            </tr>
                            <tr>
                                <td>{|Beschreibung|}:</td>
                                <td colspan="3"><textarea name="editbeschreibung" id="editbeschreibung" rows="5"
                                                          cols="60"></textarea></td>
                            </tr>
                            <tr>
                                <td width="">{|Farbe|}:</td>
                                <td><input type="text" name="editcolor" id="editcolor" size="80"></td>
                                <td width="">{|Abschlussdatum|}:</td>
                                <td><input type="text" name="editdatum_abschluss" id="editdatum_abschluss" size="10"> </td>
                            </tr>
                            <tr>
                                <td width="">{|Volumen|}:</td>
                                <td><input type="text" name="editbetrag" id="editbetrag" size="20">&nbsp;{|in EUR|}</td>
                                <td>{|Chance|}:</td>
                                <td><select name="editchance" id="editchance">
                                        <option value="0">0 %</option>
                                        <option value="10">10 %</option>
                                        <option value="20">20 %</option>
                                        <option value="30">30 %</option>
                                        <option value="40">40 %</option>
                                        <option value="50">50 %</option>
                                        <option value="60">60 %</option>
                                        <option value="70">70 %</option>
                                        <option value="80">80 %</option>
                                        <option value="90">90 %</option>
                                        <option value="100">100 %</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td width="">{|Stage|}:</td>
                                <td colspan="3"><input type="text" name="editstages" id="editstages" size="80"></td>
                            </tr>
                            <tr>
                                <td width="">{|Erinnerung-Datum|}:</td>
                                <td><input type="text" name="editdatum_erinnerung" id="editdatum_erinnerung" size="10">
                                    <input type="hidden" name="editdatum_angelegt" id="editdatum_angelegt">
                                    <input type="hidden" name="editzeit_angelegt" id="editzeit_angelegt">
                                    <input type="checkbox" name="editdatum_erinnerung_permail"
                                           id="editdatum_erinnerung_permail" value="1">&nbsp;{|per E-Mail|}
                                </td>
                                <td width="">{|Uhrzeit|}:</td>
                                <td><input type="text" name="editzeit_erinnerung" id="editzeit_erinnerung" size="10">
                                </td>
                            </tr>
                            <tr>
                                <td width="">{|Verantwortlicher|}:</td>
                                <td colspan="3"><input type="text" name="editbearbeiter" id="editbearbeiter" value=""
                                                       size="80"></td>
                            </tr>
                            <tr>
                                <td width="">{|Bearbeiter|}:</td>
                                <td colspan="3"><input type="text" name="editadresse_mitarbeiter"
                                                       id="editadresse_mitarbeiter" size="80"></td>
                            </tr>
                            <tr>
                                <td width="">{|Prio|}:</td>
                                <td><input type="checkbox" name="editprio" id="editprio" value="1"></td>
                                <td width="">{|abgeschlossen|}:</td>
                                <td><input type="checkbox" name="editabgeschlossen" id="editabgeschlossen" value="1">
                                </td>
                            </tr>
                            <tr id="editlabelrow">
                                <td width="">{|Labels|}:</td>
                                <td colspan="3">
                                    <span id="editlabelcontainer" data-label-trigger="#editlabeltrigger"></span>
                                    <a href="#" id="editlabeltrigger">{|Labels zuweisen|}</a>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
            <div class="col-xs-12 col-sm-5 col-sm-height">
                <div id="resubmission-tabs">
                    <ul>
                        <li data-type="history"><a href="#resubmission-history-tab">{|Verlauf|}</a></li>
                        <li data-type="tasks"><a href="#resubmission-tasks-tab">{|Aufgaben|}</a></li>
                        <li data-type="textfields"><a href="#resubmission-textfields-tab">{|Freifelder|}</a></li>
                        <li data-type="addressfields"><a href="#resubmission-addressfields-tab">{|Zusatzfelder Adresstabelle|}</a></li>
                    </ul>
                    <div id="resubmission-history-tab">
                        <fieldset>
                            <legend>{|Verlauf|}</legend>
                            <div style="text-align: center">
                                <input id="TimeLIneNewBtn" data-wiedervorlage-id="0"
                                       onclick="CreateTimelineItem($('#TimeLIneNewBtn').attr('data-wiedervorlage-id'))"
                                       type="button" class="btnGreen" value="Neuen Eintrag">
                            </div>
                            <div id="scroll" style="max-height:558px; overflow: auto;padding-right:10px;">
                                <div id="element"></div>
                            </div>
                        </fieldset>
                    </div>
                    <div id="resubmission-tasks-tab">
                        <fieldset>
                            <legend>{|Aufgaben|}</legend>
                            <div id="resubmission-tasks-datatable"></div>
                            <div><input id="resubmissiontask-create" type="button" class="btnGreen pull-right" value="Neue Aufgabe"></div>
                        </fieldset>
                    </div>
                    <div id="resubmission-textfields-tab">
                        <fieldset>
                            <legend>{|Freifelder|}</legend>
                            <div id="resubmission-textfields-errors"></div>
                            <div id="resubmission-textfields-content"></div>
                        </fieldset>
                    </div>
                    <div id="resubmission-addressfields-tab">
                        <fieldset id="freifelder-adresse-container">
                            <legend>{|Zusatzfelder Adresstabelle|}</legend>
                            <div id="freifelder-adresse-content"></div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<div id="editResubmissionTask" class="hide" title="Aufgabe bearbeiten">
    <div class="row">
        <div class="row-height">
            <div class="col-xs-12 col-md-12 col-sm-height">
                <div class="inside inside-full-height">
                    <input type="hidden" id="resubmissiontask-id">
                    <fieldset>
                        <legend>{|Aufgabe|}</legend>
                        <table class="mkTableFormular">
                            <tr>
                                <td width="160"><label for="resubmissiontask-title">{|Bezeichnung|}</label></td>
                                <td colspan="2"><input type="text" name="title" size="40" id="resubmissiontask-title"></td>
                            </tr>
                            <tr>
                                <td><label for="resubmissiontask-employee">{|Bearbeiter|}</label></td>
                                <td colspan="2"><input type="text" name="employee" size="40" id="resubmissiontask-employee"></td>
                            </tr>
                            <tr>
                                <td><label for="resubmissiontask-customer">{|f&uuml;r Kunde|}</label></td>
                                <td colspan="2"><input type="text" name="customer" size="40" id="resubmissiontask-customer"></td>
                            </tr>
                            <tr>
                                <td><label for="resubmissiontask-submissiondate">{|Abgabe bis|}</label></td>
                                <td>
                                    <input type="text" name="submissiondate" size="15" id="resubmissiontask-submissiondate">
                                    <input type="text" name="submissiontime" size="15" id="resubmissiontask-submissiontime">
                                </td>
                            </tr>
                            <tr>
                                <td><label for="resubmissiontask-project">{|Projekt|}</label></td>
                                <td colspan="2"><input type="text" name="project" size="40" id="resubmissiontask-project"></td>
                            </tr>
                            <tr>
                                <td><label for="resubmissiontask-subproject">{|Teilprojekt|}</label></td>
                                <td colspan="2"><input type="text" name="subproject" size="40" id="resubmissiontask-subproject"></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
            <div class="col-xs-12 col-md-6 col-sm-height">
                <div class="inside inside-full-height">
                    <input type="hidden" id="editid">
                    <fieldset>
                        <legend>{|Einstellungen|}</legend>
                        <table class="mkTableFormular" width="100%">
                            <tr>
                                <td width="50%">
                                    <label for="resubmissiontask-requiredcompletionstage">{|Muss abgeschlossen sein ab Stage|}</label>
                                </td>
                                <td width="50%">
                                    <select name="requiredcompletionstage" id="resubmissiontask-requiredcompletionstage"></select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="resubmissiontask-state">{|Status|}</label></td>
                                <td>
                                    <select name="state" id="resubmissiontask-state">
                                        <option value="open">{|Offen|}</option>
                                        <option value="processing">{|In Bearbeitung|}</option>
                                        <option value="completed">{|Abgeschlossen|}</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td><label for="resubmissiontask-priority">{|Priorität|}</label></td>
                                <td>
                                    <select name="priority" id="resubmissiontask-priority">
                                        <option value="low">Niedrig</option>
                                        <option value="medium">Mittel</option>
                                        <option value="high">Hoch</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="row-height">
            <div class="col-md-12 col-sm-height">
                <div class="inside inside-full-height">
                    <fieldset>
                        <legend>{|Beschreibung|}</legend>
                        <input type="hidden" id="resubmissiontask-description">
                        <textarea name="description" id="resubmissiontaskdescription" rows="5" cols="60"></textarea>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

<script type='text/javascript'>
	$("input#editprojekt").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=projektname",
	});

  $("input#editansprechpartner").autocomplete({
      source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+0,
  });

  $("input#editadresse").autocomplete({
      source: "index.php?module=ajax&action=filter&filtername=adresse",
      select: function( event, ui ) {
          if(ui.item){
              $("input#editansprechpartner").autocomplete({
                  source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+ui.item.value,
              });
          }
      }
  });


	$("input#editbearbeiter").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=[AUTOCOMPLETEMITARBEITER]",
	});


	$("input#editadresse_mitarbeiter").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=[AUTOCOMPLETEMITARBEITER]",
	});


	$("input#editstages").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=wiedervorlage_stages",
	});


</script>

