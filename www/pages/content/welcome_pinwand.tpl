<style type="text/css">
	.note {
		height: 125px;
		padding: 8px;
		width: 125px;
		position: absolute;
		overflow: hidden;
		cursor: move;
		word-wrap: break-word;
		-webkit-box-shadow: 3px 3px 10px rgba(0, 0, 0, .25);
		-moz-box-shadow: 3px 3px 10px rgba(0, 0, 0, .25);
		box-shadow: 3px 3px 10px rgba(0, 0, 0, .25);
		border: none;
		border-left-style: solid;
		border-left-width: 5px;
		/*border-left-color: #FFC;*/
		/**background-color: #FFC;**/
		border-left-color: #FEF388;
		background-color: #FFFFFF;
		border-radius: 6px;
	}

	#fancy_ajax .note {
		cursor: default;
	}

	/* Each note has a data span, which holds its ID */
	span.data {
		display: none;
	}

	/* The "Add a note" button: */
	#addButton {
		position: absolute;
		left: 810px;
	}

	/* Green button class: */
	a.green-button, a.green-button:visited {
		color: black;
		display: block;
		font-size: 10px;
		font-weight: bold;
		height: 15px;
		padding: 6px 5px 4px;
		text-align: center;
		text-shadow: 1px 1px 1px #DDDDDD;
	}

	a.green-button:hover {
		text-decoration: none;
		background-position: left bottom;
	}

	.author {
		position: absolute;
		right: 15px;
		bottom: 5px;
		padding-right: 3px;
		color: #666666;
		font-family: Arial, Verdana, sans-serif;
		font-size: 12px;
		background-color: transparent;
		text-align: right;
		width: 100%;
	}

	.note .author {
		display:none;
	}

	.note:hover .author,
	.note .author.visible
	{
		display: block;
	}

	.notemain {
		/* Contains all the notes and limits their movement: */
		margin: 0 auto;
		position: relative;
		width: 100%;
		min-height: 500px;
		height: calc(100vh - 270px);
		z-index: 10;
	}

	h3.popupTitle {
		border-bottom: 1px solid #DDDDDD;
		color: #666666;
		font-size: 24px;
		font-weight: normal;
		padding: 0 0 5px;
	}

	#noteData {
		/* The input form in the pop-up: */
		height: 200px;
		margin: 30px 0 0 200px;
		width: 350px;
	}

	.note-form label {
		display: block;
		font-size: 10px;
		font-weight: bold;
		letter-spacing: 1px;
		text-transform: uppercase;
		padding-bottom: 3px;
	}

	.note-form textarea, .note-form input[type=text] {
		background-color: #FCFCFC;
		border: 1px solid #AAAAAA;
		font-family: Arial, Verdana, sans-serif;
		font-size: 16px;
		height: 60px;
		padding: 5px;
		width: 300px;
		margin-bottom: 10px;
	}


	.note-form input[type=text] {
		height: auto;
	}

	.color {
		/* The color swatches in the form: */
		cursor: pointer;
		float: left;
		height: 10px;
		margin: 0 5px 0 0;
		width: 10px;
	}

	#note-submit {
		margin: 20px auto;
	}
</style>

<script type="text/javascript">

    $(document).ready(function () {
        $('a.popup').click(function (e) {
            e.preventDefault();
            var $this = $(this);
            var horizontalPadding = 30;
            var verticalPadding = 30;
            $('<iframe id="externalSite" class="externalSite" src="' + this.href + '" />').dialog({
                title: ($this.attr('title')) ? $this.attr('title') : 'External Site',
                autoOpen: true,
                width: [POPUPWIDTH],
                height: [POPUPHEIGHT],
                modal: true,
                resizable: true
            }).width([POPUPWIDTH] - horizontalPadding).height([POPUPHEIGHT] - verticalPadding);
        });


        $('#editPinwand').dialog({
            modal: true,
            bgiframe: true,
            closeOnEscape: false,
            minWidth: 450,
            maxHeight: 450,
            autoOpen: false,
            buttons: {
                ABBRECHEN: function () {
                    $(this).dialog('close');
                },
                SPEICHERN: function () {
                    //PinwandEdit();
                    var id = $('#editPinwand').find('#editid').val();
                    if(typeof CkEditor5Helper != 'undefined') {
                        CkEditor5Helper.update('editbeschreibung');
                    }
                    /*if(typeof editoreditbeschreibung != 'undefined') {
												editoreditbeschreibung.updateSourceElement();
										}*/

                    var beschreibung = $('#editPinwand').find('#editbeschreibung').val();
                    var note_color = $('#editPinwand').find('#editnote_color').val();
                    $.ajax({
                        url: 'index.php?module=welcome&action=pinwand&cmd=save',
                        data: {
                            id: id,
                            beschreibung: beschreibung,
                            note_color: note_color,
                            pinwand: $('#pinwand').val()
                        },
                        method: 'post',
                        dataType: 'json',
                        success: function (data) {
                            if (data.status == 1) {
                                if (document.getElementById('notehtml' + id)) {
                                    $('#notehtml' + id).html(data.beschreibung);
                                    $('#note' + id).css({'border-left-color':data.note_color});
                                    $('#editPinwand').dialog('close');
                                } else {
                                    window.location.href = 'index.php?module=welcome&action=pinwand&pinwand=' + $('#pinwand').val();
                                }
                            }
                        }
                    });
                }
            }
        });

        /* This code is executed after the DOM has been completely loaded */
        var tmp;

        $('.note').each(function () {
            /* Finding the biggest z-index value of the notes */
            tmp = $(this).css('z-index');
            if (tmp > zIndex) zIndex = tmp;
        });

        /* A helper function for converting a set of elements to draggables: */
        make_draggable($('.note'));

        /* Configuring the fancybox plugin for the "Add a note" button: */
        /*$("#addButton").fancybox({
					'zoomSpeedIn'		: 600,
					'zoomSpeedOut'		: 500,
					'easingIn'			: 'easeOutBack',
					'easingOut'			: 'easeInBack',
					'hideOnContentClick': false,
					'padding'			: 15
				});*/

        /* Listening for keyup events on fields of the "Add a note" form: */
        $(document).on('keyup', '.pr-body,.pr-author', function (e) {
            if (!this.preview)
                this.preview = $('#fancy_ajax .note');

            /* Setting the text of the preview to the contents of the input field, and stripping all the HTML tags: */
            this.preview.find($(this).attr('class').replace('pr-', '.')).html($(this).val().replace(/<[^>]+>/ig, ''));
        });

        /* Changing the color of the preview note: */
        $(document).on('click', '.color', function () {
            $('#fancy_ajax .note').removeClass('yellow green blue').addClass($(this).attr('class').replace('color', ''));
        });

        /* The submit button: */
        $(document).on('click', '#note-submit', function (e) {

            if ($('.pr-body').val().length < 4) {
                alert('The note text is too short!');
                return false;
            }

            if ($('.pr-author').val().length < 1) {
                alert('You haven\'t entered your name!');
                return false;
            }

            $(this).replaceWith('<img src="img/ajax_load.gif" style="margin:30px auto;display:block" />');

            var data = {
                'zindex': ++zIndex,
                'body': $('.pr-body').val(),
                'author': $('.pr-author').val(),
                'color': $.trim($('#fancy_ajax .note').attr('class').replace('note', ''))
            };


            /* Sending an AJAX POST request: */
            $.post('ajax/post.php', data, function (msg) {
                if (parseInt(msg)) {
                    /* msg contains the ID of the note, assigned by MySQL's auto increment: */

                    var tmp = $('#fancy_ajax .note').clone();

                    tmp.find('span.data').text(msg).end().css({'z-index':zIndex,top:0,left:0});
                    tmp.appendTo($('#notemain'));

                    make_draggable(tmp);
                }

                //$("#addButton").fancybox.close();
            });

            e.preventDefault();
        });

        $(document).on('submit', '.note-form', function (e){e.preventDefault();});
    });

    var zIndex = 0;

    function make_draggable(elements) {
        elements.draggable({
            containment: 'parent',
            start: function (e, ui) { ui.helper.css('z-index', ++zIndex); },
            stop: function (e, ui) {
                $.get('index.php?module=welcome&action=movenote', {
                    x: ui.position.left,
                    y: ui.position.top,
                    z: zIndex,
                    id: parseInt(ui.helper.find('span.data').html())
                });
            }
        });
        elements.resizable({
            containment: 'parent',
            start: function (e, ui) { ui.helper.css('z-index', ++zIndex); },
            stop: function (e, ui) {
                $.get('index.php?module=welcome&action=pinwand&cmd=resize', {
                    w: ui.size.width,
                    h: ui.size.height,
                    id: parseInt(ui.helper.find('span.data').html())
                });
            }
        });
				$(elements).on('touchstart', function(){
						$('.note .author').toggleClass('visible', false);
						$(this).find('.author').toggleClass('visible', true);
				});
    }

    $(function () {
        $('.button').button();
    });

    function PinwandEdit(pinwand, id) {

        if (id > 0) {
            $.ajax({
                url: 'index.php?module=welcome&action=pinwand&cmd=get',
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    $('#editPinwand').find('#editid').val(data.id);
                    $('#editPinwand').find('#editbeschreibung').val(data.beschreibung);
                    if(typeof CkEditor5Helper != 'undefined') {
                        CkEditor5Helper.setData('editbeschreibung', data.beschreibung);
                    }
                    /*if(typeof editoreditbeschreibung != 'undefined') {
												editoreditbeschreibung.setData(data.beschreibung);
										}*/
                    $('#editPinwand').find('#editnote_color').val(data.note_color);
                    $('#editPinwand').find('#editnote_color').trigger('change');
                    App.loading.close();
                    $('#editPinwand').dialog('open');
                    $('#editPinwand').find('#editbeschreibung').trigger('change');
                }
            });

        } else {
            $('#editPinwand').find('#editid').val('');
            $('#editPinwand').find('#editbeschreibung').val('');
            $('#editPinwand').find('#editnote_color').val('#FFCC00');
            $('#editPinwand').find('#editnote_color').trigger('change');
            $('#editPinwand').dialog('open');
        }
    }
</script>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">


		<div id="editPinwand" style="display:none;" title="Bearbeiten">
			<input type="hidden" id="editid">
			<table>
				<tr>
					<td>{|Bezeichnung|}:</td>
					<td><textarea name="editbeschreibung" id="editbeschreibung"></textarea></td>
				</tr>
				<tr>
					<td>{|Farbe|}:</td>
					<td><input type="text" name="editnote_color" id="editnote_color"></td>
				</tr>
				<tr>
			</table>
		</div>
		<table width="100%">
			<tr>
				<td>
					<input type="button" class="btnGreen" id="addnote" onclick="PinwandEdit($('#pinwand').val(),0)" value="{|Neue Notiz (Aufgabe) anlegen|}">
				</td>
				<td align="right">
					<select name="pinwand" id="pinwand" onchange="window.location.href='index.php?module=welcome&action=pinwand&pinwand='+$('#pinwand').val()">
						<option value="0">{|Eigene Pinnwand|}</option>
						[PINWAENDE]
					</select>
					<a id="" class="popup" href="index.php?module=welcome&action=addpinwand" title="{|Neue Pinnwand anlegen|}"><img src="./themes/new/images/add.png"></a>
					&nbsp; <a class="popup" href="index.php?module=pinwand&action=list" title="{|Pinnwand bearbeiten|}"><img src="./themes/new/images/edit.svg"></a>
				</td>
			</tr>
		</table>
		<div id="notemain" class="notemain">
			[NOTES]
		</div>
	</div>

	<!-- tab view schließen -->
</div>
[AUFGABENPOPUP]
