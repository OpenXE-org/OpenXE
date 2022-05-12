<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">{|Jahresansicht|}</a></li>
		<li><a href="#tabs-2">{|Urlaubs&uuml;bersicht|}</a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]
		<table width="100%">
			<tr>
				<td width="75%"></td>
				<td width="25%">
					<fieldset style="height:30px">
						<legend>{|F&uuml;r Jahr|}</legend>
						<center>
							<form id="frmjahr" method="post" action="#tabs-1">{|Jahr|}: <select
										onchange="$('#frmjahr').submit()" name="jahr">[JAHROPTIONEN]</select></form>
						</center>
					</fieldset>
				</td>
		</table>
		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-md-10 col-md-height">
					<div class="inside_white inside-full-height">
						[TAB1]
					</div>
				</div>
			</div>
		</div>
		[TAB1NEXT]
	</div>

	<div id="tabs-2">
		[MESSAGE]
		<table width="100%">
			<tr>
				<td width="75%"></td>
				<td width="25%">
					<fieldset style="height:30px">
						<legend>{|F&uuml;r Jahr|}</legend>
						<center>
							<form id="uebersichtjahr" method="post" action="#tabs-2">{|Jahr|}: <select
										onchange="$('#uebersichtjahr').submit()" name="tabellejahr">[UEBERSICHTJAHROPTIONEN]</select>
							</form>
						</center>
					</fieldset>
				</td>
		</table>

		[TAB2]
		[TAB2NEXT]
	</div>
	<!-- tab view schließen -->
</div>
<div id="urlauballedetaildiv" style="display:none;"></div>
<script type="text/javascript">
    function getUrlaubdetails(datum) {
        $.ajax({
            url: "index.php?module=mitarbeiterzeiterfassung&action=geturlauballe&datum=" + datum,
            type: 'POST',
            dataType: 'json',
            data: {}
        }).done(function (data) {
            if (typeof data == 'undefined' || data == null || typeof data.status == 'undefined' || data.status == 0) {

            } else {
                jQuery('#urlauballedetaildiv').dialog({
                    title: 'Urlaub- / Krankheitsinformationen',
                    width: 600
                });
                jQuery('#urlauballedetaildiv').html(data.html);
            }
        }).fail(function (jqXHR, textStatus) {

        });
    }
</script>