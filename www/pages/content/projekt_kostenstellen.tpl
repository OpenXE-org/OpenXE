

<script type="text/javascript">

	$(function() {

		var d1 = [];
		for (var i = 0; i < 14; i += 0.5) {
			d1.push([i, Math.sin(i)]);
		}

		var d2 = [[0, 3], [4, 8], [8, 5], [9, 13]];

		// A null signifies separate line segments

		var d3 = [[0, 12], [7, 12], null, [7, 2.5], [12, 2.5]];

		$.plot("#placeholder", [ d1, d2, d3 ]);

	});

	</script>
<style>
.demo-container {
	box-sizing: border-box;
	width: 620px;
	height: 350px;
}

.demo-placeholder {
	width: 100%;
	height: 100%;
	font-size: 14px;
	line-height: 1.2em;
}

.legend table {
	border-spacing: 5px;
}
</style>

<script language="javascript" type="text/javascript" src="./js/flot/jquery.flot.js"></script>
<script language="javascript" type="text/javascript" src="./js/flot/jquery.flot.stack.js"></script>
<script language="javascript" type="text/javascript" src="./js/flot/jquery.flot.pie.min.js"></script>
	<script language="javascript" type="text/javascript" src="./js/flot/jquery.flot.selection.js"></script>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Gesamt</a></li>
        <li><a href="#tabs-2">Zeitkonten</a></li>
        <li><a href="#tabs-3">Reisekosten</a></li>
        <li><a href="#tabs-4">Material</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

<table width="100%" cellpadding="3" border="0">
<tr><td width="60%"><h2 class="greyh2">Zeiterfassung</h2></td><td><h2 class="greyh2">Kostenstellen</h2></td></tr>
<tr valign="top">

<td>

		<div class="demo-container">
			<div id="placeholder" class="demo-placeholder"></div>
		</div>

</td>
<td>

<table border="0" cellpadding="3" id="tableone"
	cellspacing="1" width="100%" height="" style="font-size: 90%; "><tr style="background-color:#e0e0e0;display:;" id="0">
<td class="gentable" nowrap><b>Datum</b></td><td class="gentable" nowrap><b>Umsatz</b></td></tr><tr style="background-color:#fff;display:;" id="1">
<td class="gentable" nowrap>Personal</td><td class="gentable" nowrap>[SUMMEZEIT]</td></tr><tr style="background-color:#e0e0e0;display:;" id="2">
<td class="gentable" nowrap>Fremdleistung</td><td class="gentable" nowrap></td></tr><tr style="background-color:#fff;display:;" id="3">
<td class="gentable" nowrap>Verbrauchsmaterial</td><td class="gentable" nowrap>[SUMMEVK]</td></tr>
<tr style="background-color:#e0e0e0;display:;" id="4">
<td class="gentable" nowrap>Ger&auml;te/Inventur</td><td class="gentable" nowrap></td></tr>
<td class="gentable" nowrap>Entwicklungsbedarf</td><td class="gentable" nowrap></td></tr>
<tr style="background-color:#e0e0e0;display:;" id="4">
<td class="gentable" nowrap>Ger&auml;te/Inventur</td><td class="gentable" nowrap></td></tr>
<tr id="4">
<td class="gentable" nowrap>Porto</td><td class="gentable" nowrap></td></tr>
<tr style="background-color:#e0e0e0;display:;" id="4">
<td class="gentable" nowrap>Gesamtkosten</td><td class="gentable" nowrap>[KOSTEN]</td></tr>

</table>



</td></tr>
<tr><td width="60%"><h2 class="greyh2">Rechnungen</h2></td><td><h2 class="greyh2">Mitarbeiter</h2></td></tr>
<tr valign="top">

<td>
<table border="0" cellpadding="3" id="tableone"
	cellspacing="1" width="100%" height="" style="font-size: 90%; "><tr style="background-color:#e0e0e0;display:;" id="0">
<td class="gentable" nowrap><b>Monat</b></td><td class="gentable" nowrap><b>Stunden</b></td></tr><tr style="background-color:#fff;display:;" id="1">
<td class="gentable" nowrap>01-2013</td><td class="gentable" nowrap>2895,12</td></tr><tr style="background-color:#e0e0e0;display:;" id="2">
<td class="gentable" nowrap>02-2013</td><td class="gentable" nowrap>55,22</td></tr><tr style="background-color:#fff;display:;" id="3">
<td class="gentable" nowrap>03-2013</td><td class="gentable" nowrap>444,22</td></tr>

<tr style="background-color:#e0e0e0;display:;" id="4">
<td class="gentable" nowrap>04-2013</td><td class="gentable" nowrap>202,35</td></tr>

<tr style="background-color:#fff;display:;" id="5">
<td class="gentable" nowrap>05-2013</td><td class="gentable" nowrap>2508,20</td></tr><tr style="background-color:#e0e0e0;display:;" id="6">
</td></tr>

<tr style="background-color:#e0e0e0;display:;" id="4">
<td class="gentable" nowrap>04-2013</td><td class="gentable" nowrap>202,35</td></tr>


</table>


</td>
<td>
<table border="0" cellpadding="3" id="tableone"
	cellspacing="1" width="100%" height="" style="font-size: 90%; "><tr style="background-color:#e0e0e0;display:;" id="0">
<td class="gentable" nowrap><b>Name</b></td><td class="gentable" nowrap><b>Soll</b></td><td><b>Ist</b></td></tr><tr style="background-color:#fff;display:;" id="1">
<td class="gentable" nowrap>Peter Mustermann</td><td class="gentable" nowrap>28</td><td>8</td></tr><tr style="background-color:#e0e0e0;display:;" id="2">
<td class="gentable" nowrap>Petra Meier</td><td class="gentable" nowrap>55</td><td>56</td></tr><tr style="background-color:#fff;display:;" id="3">
<td class="gentable" nowrap>Lusisa Müller</td><td class="gentable" nowrap>444</td><td>67</td></tr><tr style="background-color:#e0e0e0;display:;" id="4">
<td class="gentable" nowrap>Max Knoll</td><td class="gentable" nowrap>22</td><td>3</td></tr><tr style="background-color:#e0e0e0;display:;" id="8">
</table>


</td></tr>

</table>

</div>

<div id="tabs-2">
</div>

<div id="tabs-3">
</div>


<div id="tabs-4">
[MATERIAL]
</div>


</div>
