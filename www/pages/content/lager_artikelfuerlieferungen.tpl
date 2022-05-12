<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]

		[TAB1]

		<form method="post">
			<div class="clear"></div>
			<fieldset>
				<legend>{|Etiketten|}</legend>
				<input type="checkbox" name="etiketten_drucken" id="etiketten_drucken" value="1" [ETIKETT_DRUCKEN]> {|Etiketten drucken|}
				<input type="checkbox" name="etiketten_eins_pro_zeile" id="etiketten_eins_pro_zeile" value="1" [ETIKETT_EINS_PRO_ZEILE]> {|Ein Etikett pro Artikelzeile|}
				&nbsp;&nbsp;{|Etikett|}: <select name="etiketten" id="etiketten">
								[ETIKETTEN]
							</select>
				&nbsp;&nbsp;{|Drucker|}: <select name="etikettendrucker" id="etikettendrucker">
								[ETIKETTENDRUCKER]
							</select>
		</form>
		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>