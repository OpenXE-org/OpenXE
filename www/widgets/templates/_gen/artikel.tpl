<script type="text/javascript">


   $(document).ready(function(){

 		document.getElementById('rabattstyle').style.display='none';

    if(document.getElementById('rabatt').checked) {
      document.getElementById('rabattstyle').style.display = '';
    }

    stuecklisteevent(1);

  if($('#steuersatz').val()=='')
  {
    $('#anderersteuersatz').prop('checked', false);
    $('.steuersatz').hide();
    $("#umsatzsteuer").prop('disabled', false);
  }
  else
  {
    $('.steuersatz').show();
    $('#anderersteuersatz').prop('checked', true);
    $("#umsatzsteuer").prop('disabled', 'disabled');
  }

  $('#anderersteuersatz').click(function() {
    if (!$(this).is(':checked')) {
      $('.steuersatz').hide();
      $('#steuersatz').val('');
      $("#umsatzsteuer").prop('disabled', false);
    } else {
      $('.steuersatz').show();
      $("#umsatzsteuer").prop('disabled', 'disabled');
    }
  });

  });


  function rabattevent()
  {
    document.getElementById('rabattstyle').style.display='none';
    if(document.getElementById('rabatt').checked) {
      document.getElementById('rabattstyle').style.display = '';
    }
  }

	function juststuecklisteevent(cmd)
  {
    if(document.getElementById('juststueckliste').checked)
    {
      document.getElementById("stueckliste").checked = true;
      $('.preproduced_partlist').show();
      document.getElementById("lagerartikel").checked = false;
    }
    else
    {
      document.getElementById("stueckliste").checked = false;
      $('.preproduced_partlist').hide();
      document.getElementById("has_preproduced_partlist").checked = false;
    }
					
   }
  function stuecklisteevent(cmd)
  {
    if(!document.getElementById('stueckliste').checked)
    {
      document.getElementById("juststueckliste").checked = false;
      document.getElementById("has_preproduced_partlist").checked = false;
      $('.preproduced_partlist').hide();
    }
  }

  function portoevent(cmd)
  {
    if(document.getElementById('porto').checked)
    {
      document.getElementById("lagerartikel").checked = false;
    }
  }

  function lagerartikelevent(cmd)
  {
    if(document.getElementById('lagerartikel').checked)
    {
      document.getElementById("porto").checked = false;

      document.getElementById("stueckliste").checked = false;
      document.getElementById("juststueckliste").checked = false;
      document.getElementById("has_preproduced_partlist").checked = false;
      $('.preproduced_partlist').hide();
    }
  }


      //-->
</script>

[SAVEPAGEREALLY]

<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

[LEERFELD][MSGLEERFELD]

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Artikel|}</a></li>
[DISABLEOPENTEXTE]<li><a href="#tabs-2">{|Texte und Beschreibungen|}</a></li>[DISABLECLOSETEXTE]
[DISABLEOPENPARAMETER]<li><a href="#tabs-3">{|Parameter und Freifelder|}</a></li>[DISABLECLOSEPARAMETER]
        [DISABLEOPENSHOP]<li><a href="#tabs-4">{|Online-Shop Optionen|}</a></li>[DISABLECLOSESHOP]
        <li><a href="#tabs-5">{|Finanzbuchhaltung|}</a></li>
    </ul>

<!-- ende gehort zu tabview -->

<div id="tabs-1">
[MESSAGE]



<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">


<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">{|Artikel|} <font color="blue">[ANZEIGENUMMER]</font></b>[ANZEIGENAMEDE]</td>
<td>[STATUSICONS]</td>
<td align="right">[ICONMENU]&nbsp; <input type="submit" name="speichern" class="button-sticky wizard-article-save"
    value="Speichern" onclick="this.form.action += '#tabs-1';"/> [ABBRECHEN]</td>
</tr>
</table>

</div>
</div>
</div>
</div>





<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-8 col-md-height">
<div class="inside inside-full-height">



<fieldset><legend>&nbsp;{|Name und Nummer des Artikels|}&nbsp;</legend>
<table class="mkTableFormular" border="0">
<tr><td width="200">{|Artikel (DE)|}:</td><td colspan="4">[NAME_DE][MSGNAME_DE]</td></tr>
<tr><td width="">{|Artikel Nr.|}:</td><td width="180">[NUMMER][MSGNUMMER]</td><td width="20">&nbsp;</td><td width="150">{|Projekt|}:</td><td width="">[PROJEKTSTART][PROJEKT][MSGPROJEKT][PROJEKTENDE]</td></tr>
<tr><td>{|Artikelkategorie|}:</td><td>[TYP][MSGTYP]
</td><td></td><td>{|Standardlieferant|}:</td><td>[ADRESSE][MSGADRESSE]</td></tr>

 <tr><td nowrap>{|Artikelbeschreibung (DE)|}:</td><td colspan="4">[ANABREGS_TEXT][MSGANABREGS_TEXT]</td></tr>
 <tr><td nowrap>{|Kurztext (DE)|}:</td><td colspan="4">[KURZTEXT_DE][MSGKURZTEXT_DE]</td></tr>

<tr><td>{|Interner Kommentar|}:</td><td colspan="4">[INTERNERKOMMENTAR][MSGINTERNERKOMMENTAR]</td><tr>

</table>
</fieldset>

</div>
</div>

<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Vorschau|}</legend>
[ARTIKELBILD]
</fieldset>
[INFOFUERAUFTRAGSERFASSUNG]
</div>
</div>
</div>
</div>
[BENUTZERDEFINIERTSTART]
    

<div class="row">
<div class="row-height">

<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>[BENUTZERDEFINIERT]</legend>
[FREIFELDSPALTE1]
</fieldset>
</div>
</div>

<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>&nbsp;</legend>
[FREIFELDSPALTE2]
</fieldset>
</div>
</div>

</div>
</div>

[BENUTZERDEFINIERTENDE]

[DISABLEOPENSTOCK]

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">



<fieldset><legend>{|Hersteller|}</legend>
<table class="mkTableFormular" border="0">
<tr><td width="200">{|Hersteller|}:</td><td nowrap>[HERSTELLERSTART][HERSTELLER][MSGHERSTELLER][HERSTELLERENDE]</td></tr>
<tr><td width="">{|Herstellerlink|}:</td><td>[HERSTELLERLINKSTART][HERSTELLERLINK][MSGHERSTELLERLINK][HERSTELLERLINKENDE]</td></tr>
<tr><td width="">{|Hersteller Nr.|}:</td><td>[HERSTELLERNUMMER][MSGHERSTELLERNUMMER]</td></tr>
<tr><td width="">{|EAN Nr. / Barcode|}:</td><td>[EAN][MSGEAN]</td><td width="">&nbsp;</td></tr>
<tr><td width="">{|Zolltarifnummer|}:</td><td>[ZOLLTARIFNUMMER][MSGZOLLTARIFNUMMER]</td><td width="">&nbsp;</td></tr>
<tr><td width="">{|Herkunftsland|}:</td><td>[HERKUNFTSLAND][MSGHERKUNFTSLAND]</td><td width="">&nbsp;</td></tr>
<tr><td width="">{|Ursprungsregion|}:</td><td>[URSPRUNGSREGION][MSGURSPRUNGSREGION]</td><td width="">&nbsp;</td></tr>
</table>
</fieldset>

</div>
</div>
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">




<fieldset><legend>{|Lager / Abmessungen|}</legend>
<table class="mkTableFormular" border="0">
<tr>
  <td width="120">{|Min. Lagermenge|}:</td>
  <td width="170">[MINDESTLAGER][MSGMINDESTLAGER]</td>
  <td width="130">{|Gewicht|} (in [GEWICHTBEZEICHNUNG]):</td>
  <td>[GEWICHT][MSGGEWICHT]</td>
</tr>
<tr>
  <td width="120">{|Min. Bestellmenge|}:</td>
  <td>[MINDESTBESTELLUNG][MSGMINDESTBESTELLUNG]</td>
  <td width="130">{|Nettogewicht|} (in [GEWICHTBEZEICHNUNG]):</td>
  <td>[NETTOGEWICHT][MSGNETTOGEWICHT]</td>
</tr>
<tr>
  <td width="120">{|Standardlager|}:</td>
  <td>[LAGER_PLATZ][MSGLAGER_PLATZ]</td>
  <td width="130">{|L&auml;nge|} (in cm):</td>
  <td>[LAENGE][MSGLAENGE]</td>
</tr>
<tr>
  <td width="120">{|Einheit|}:</td>
  <td>[EINHEIT][MSGEINHEIT]</td>
  <td width="130">{|Breite|} (in cm):</td>
  <td>[BREITE][MSGBREITE]</td>
</tr>
<tr>
  <td width="120">{|XVP|}:</td>
  <td>[XVP][MSGXVP]</td>
  <td width="130">{|H&ouml;he|} (in cm):</td>
  <td>[HOEHE][MSGHOEHE]</td>
</tr>
<tr>
  <td width="120"></td>
  <td></td>
  <td>{|Kategorie|}:</td>
  <td>[ABCKATEGORIE][MSGABCKATEGORIE]</td>
</tr>
</table>
</fieldset>


</div>
</div>
</div>
</div>


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>&nbsp;{|Artikel Optionen|}&nbsp;</legend>
<table class="mkTableFormular"  border="0"  width="100%">
<tr><td width="200"><font color="#961F1C">{|Lagerartikel|}:</font></td><td width="">[LAGERARTIKEL][MSGLAGERARTIKEL]</td></tr>
<tr><td width=""><font color="#961F1C">{|Artikel ist Porto|}:</font></td><td>[PORTO][MSGPORTO]</td></tr>
<tr><td width=""><font color="#961F1C">{|Artikel ist Rabatt|}:</font></td><td width="">[RABATT][MSGRABATT]&nbsp;<span id="rabattstyle">[RABATT_PROZENT][MSGRABATT_PROZENT] in %</span></td></tr>
</table>
</fieldset>

</div>
</div>
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">



<fieldset><legend>&nbsp;{|Varianten|}&nbsp;</legend>
<table border="0" class="mkTableFormular">
<tr><td>{|Variante|}:</td><td>[VARIANTE][MSGVARIANTE]</td><td nowrap>{|von Artikel|}:&nbsp;[ARTIKELSTART][VARIANTE_VON][MSGVARIANTE_VON][ARTIKELENDE]</td></tr>
[VORMATRIX]<tr><td>
{|Matrixprodukt|}:
</td><td>
[MATRIXPRODUKT][MSGMATRIXPRODUKT]
</td></tr>[NACHMATRIX]
[VORTAGESPREISE]<tr><td>
{|Tagespreise|}:
</td><td>
[TAGESPREISE][MSGTAGESPREISE]
</td></tr>[NACHTAGESPREISE]
</table>
</fieldset>

</div>
</div>
</div>
</div>



[DISABLECLOSESTOCK]

[ARTIKELKUNDENSPEZIFISCH]

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">



<fieldset>
<legend>&nbsp;{|Sonstige Einstellungen|}&nbsp;</legend>

<table class="mkTableFormular" border="0" style="height:260px">

[DISABLEOPENSTOCK]
<tr>
<td width="200">{|Umsatzsteuer|}:</td>
<td>
[UMSATZSTEUER][MSGUMSATZSTEUER]
[ANDERERSTEUERSATZ][MSGANDERERSTEUERSATZ]&nbsp;individuellen Steuersatz verwenden
</td>
</tr>

<tr style="display:none" class="steuersatz"><td>{|Individueller Steuersatz|}:</td><td>[STEUERSATZ][MSGSTEUERSATZ]&nbsp;in Prozent</td></tr>


<tr>
<td width="">{|Kein Rabatt erlaubt|}:</td><td width="">[KEINRABATTERLAUBT][MSGKEINRABATTERLAUBT]</td>
<td></td>
</tr>
<tr>
<td width="">{|Provisionssperre|}:</td><td width="">[PROVISIONSSPERRE][MSGPROVISIONSSPERRE]</td>
<td></td>
</tr>


[DISABLECLOSESTOCK]
<tr>
<td width="200">{|Chargenverwaltung|}:</td><td width="">[CHARGENVERWALTUNG][MSGCHARGENVERWALTUNG]</td>
</tr>


<tr><td width="">{|Seriennummern|}:</td><td> [SERIENNUMMERN][MSGSERIENNUMMERN] </td>
</tr>
<tr>
<td>{|Mindesthaltbarkeitsdatum|}:</td><td>[MINDESTHALTBARKEITSDATUM][MSGMINDESTHALTBARKEITSDATUM]</td>
</tr>


[DISABLEOPENSTOCK]
<tr>
<td>{|Einkauf bei allen Lieferanten|}:</td><td>[ALLELIEFERANTEN][MSGALLELIEFERANTEN]</td>
</tr>
<tr><td>{|Inventur Sperre|}:</td><td>[INVENTURSPERRE][MSGINVENTURSPERRE]</td>
</tr>
<tr>
<td>{|Inventur Wert|}:</td><td nowrap>[INVENTUREKAKTIV][MSGINVENTUREKAKTIV]&nbsp;[INVENTUREK][MSGINVENTUREK]</td>
</tr>
<tr>
<td>{|Kalkulierter EK-Preis|}:</td><td nowrap>[VERWENDEBERECHNETEREK][MSGVERWENDEBERECHNETEREK]&nbsp;[BERECHNETEREK][MSGBERECHNETEREK]&nbsp;{|W&auml;hrung|}:&nbsp;[BERECHNETEREKWAEHRUNG][MSGBERECHNETEREKWAEHRUNG]</td>

</tr>

[DISABLECLOSESTOCK]
<tr>
<td>{|Keine Verkaufspreis Meldung|}:</td><td>[VKMELDUNGUNTERDRUECKEN][MSGVKMELDUNGUNTERDRUECKEN]</td>
</tr>
<tr>
<td>{|Kein Skonto erlauben|}:</td><td>[KEINSKONTO][MSGKEINSKONTO]</td>
</tr>
<tr><td>{|Altersfreigabe|}:</td><td>
[ALTERSFREIGABE][MSGALTERSFREIGABE]&nbsp;<i>{|nur f&uuml;r DHL|}</i>
</td></tr>
</table>
</fieldset>

</div>
</div>
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">




<fieldset>

<legend>&nbsp;</legend>
<table class="mkTableFormular" border="0" style="height:260px">

[DISABLEOPENSTOCK]
<tr>
  <td width="200">{|St&uuml;ckliste|}:</td><td>[STUECKLISTE][MSGSTUECKLISTE]</td>
</tr>

<tr>
<td>{|Just-In-Time St&uuml;ckliste|}:</td><td>[JUSTSTUECKLISTE][MSGJUSTSTUECKLISTE]&nbsp;<i>({|auflösen / explodieren im Auftrag|})</i>&nbsp;[DISABLEOPENSTOCK][KEINEEINZELARTIKELANZEIGEN][MSGKEINEEINZELARTIKELANZEIGEN]&nbsp;Einzelpos. ausblenden[DISABLECLOSESTOCK]</td>
</tr>
<tr class="preproduced_partlist">
  <td>{|Vorproduzierte Stückliste|}:</td><td>[HAS_PREPRODUCED_PARTLIST][MSGHAS_PREPRODUCED_PARTLIST]&nbsp;[PREPRODUCED_PARTLIST][MSGPREPRODUCED_PARTLIST]</td>
</tr>
[DISABLECLOSESTOCK]
<tr>
  <td width="200">{|Produktionsartikel|}:</td><td>[PRODUKTION][MSGPRODUKTION]</td>
</tr>
<tr>
<td width="200">{|Externe Produktion|}:</td><td width="">[EXTERNEPRODUKTION][MSGEXTERNEPRODUKTION]</td>
</tr>


<tr>

<td>[DISABLEOPENSTOCK]{|Rohstoffliste|}:[DISABLECLOSESTOCK]</td><td>[DISABLEOPENSTOCK][ROHSTOFFE][MSGROHSTOFFE][DISABLECLOSESTOCK]</td>
</tr>
<tr>
<td>[DISABLEOPENSTOCK]{|Ger&auml;t|}:[DISABLECLOSESTOCK]</td><td>[DISABLEOPENSTOCK][GERAET][MSGGERAET][DISABLECLOSESTOCK]</td>
</tr>


[DISABLEOPENSTOCK]
<tr>
<td>{|Serviceartikel|}:</td><td>[SERVICEARTIKEL][MSGSERVICEARTIKEL]</td>
</tr>

<tr>
<td>{|Geb&uuml;hr|}:</td><td>[GEBUEHR][MSGGEBUEHR]</td>
</tr>


<tr>
<td>{|Dienstleistung|}:</td><td>[DIENSTLEISTUNG][MSGDIENSTLEISTUNG]</td>
</tr>

<tr><td nowrap>{|Individualartikel:|}</td><td>[UNIKAT][MSGUNIKAT]</td></tr>
<tr>
  <td>{|Ohne Preis im PDF|}:</td><td>[OHNEPREISIMPDF][MSGOHNEPREISIMPDF]</td>
</tr>

[VORFORMELN]
<tr valign="top"><td>{|Belege Formel Menge|}:</td><td>[FORMELMENGE][MSGFORMELMENGE]</td></tr>
<tr valign="top"><td>{|Belege Formel Preis|}:</td><td>[FORMELPREIS][MSGFORMELPREIS]</td></tr>
[NACHFORMELN]

[DISABLECLOSESTOCK]





</table>
</fieldset>
</div>
</div>
</div>
</div>

[DISABLEOPENSTOCK]
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>&nbsp;{|Sperre&nbsp;|}</legend>
<table class="mkTableFormular" border="0">
<tr valign="top"><td width="200">{|Meldung|}:</td><td colspan="4">[INTERN_GESPERRTGRUND][MSGINTERN_GESPERRTGRUND]</td><tr>
<tr><td>{|Sperre aktiv|}:</td><td colspan="4">[INTERN_GESPERRT][MSGINTERN_GESPERRT]</td><tr>
</table>
</fieldset>
</div>
</div>
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>&nbsp;{|Texte|}</legend>
<table class="mkTableFormular">
<tr valign="top"><td width="200">{|Hinweis-Text|}:</td><td colspan="4">[HINWEIS_EINFUEGEN][MSGHINWEIS_EINFUEGEN]</td><tr>
</table>
</fieldset>
</div>
</div>

</div>
</div>

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>&nbsp;{|Kundenfreigabe|}&nbsp;</legend>
<table class="mkTableFormular">
<tr><td width="200">{|Pr&uuml;fung notwendig|}:</td><td colspan="4">[FREIGABENOTWENDIG][MSGFREIGABENOTWENDIG]&nbsp;<i>z.B. Artikel der nur an Fachleute verkauft werden darf.</i></td><tr>
<tr><td>{|Freigabe Regel|}:</td><td colspan="4">[FREIGABEREGEL][MSGFREIGABEREGEL]</td><tr>
</table>
</fieldset>

</div>
</div>
</div>
</div>

[DISABLECLOSESTOCK]



 </div>
[DISABLEOPENSTOCK]
<div id="tabs-2">

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">

<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">{|Artikel|} <font color="blue">[ANZEIGENUMMER]</font></b>[ANZEIGENAMEDE]</td>
<td>[STATUSICONS]</td>
<td width="33%" align="right">[ICONMENU]&nbsp; <input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-1';"/> [ABBRECHEN]</td>
</tr>
</table>
</div>
</div>
</div>
</div>


<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>&nbsp;{|Beschreibung|}&nbsp;</legend>
<table class="mkTableFormular" border="0">

				<tr valign="top"><td width="500">{|Artikel (DE) (Bitte im ersten Tab bearbeiten)|}:<br>[ARTIKEL_DE_ANZEIGE]</td><td width="20"></td>
	      <td width="500">{|Artikel (EN)|}:<br>[NAME_EN][MSGNAME_EN]</td></tr>


				<tr><td>{|Kurztext (DE) (Bitte im ersten Tab bearbeiten)|}:<br>[KURZTEXT_DE_ANZEIGE]</td><td width="20"></td>
	      <td>{|Kurztext (EN)|}:<br>[KURZTEXT_EN][MSGKURZTEXT_EN]</td></tr>

 <tr><td>{|Artikelbeschreibung (DE) (Bitte im ersten Tab bearbeiten)|}:<br>[ARTIKELBESCHREIBUNG_DE_ANZEIGE]</td><td></td><td nowrap>Artikelbeschreibung (EN):<br>[ANABREGS_TEXT_EN][MSGANABREGS_TEXT_EN]</td></tr>

</table>
</fieldset>
</div>
</div>
</div>
</div>
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>&nbsp;{|Online-Shop Texte|}&nbsp;</legend>
<table class="mkTableFormular">
	    	<tr valign="top"><td width="500">{|Artikelbeschreibung (DE)|}:<br>[UEBERSICHT_DE][MSGUEBERSICHT_DE]</td><td width="20"></td>
	      <td width="500">{|Artikelbeschreibung (EN)|}:<br>[UEBERSICHT_EN][MSGUEBERSICHT_EN]</td></tr>
	      <!--<tr><td nowrap>Beschreibung (DE):<br>[BESCHREIBUNG_DE][MSGBESCHREIBUNG_DE]</td><td width="20"></td>
	      <td>Beschreibung (EN):<br>[BESCHREIBUNG_EN][MSGBESCHREIBUNG_EN]</td></tr>-->
	      <!--<tr><td>Links (DE):<br>[LINKS_DE][MSGLINKS_DE]</td><td width="20"></td>
	      <td>Links (EN):<br>[LINKS_EN][MSGLINKS_EN]</td></tr>
	      <tr><td>Startseite (DE):<br>[STARTSEITE_DE][MSGSTARTSEITE_DE]</td><td width="20"></td>
	      <td>Startseite (EN):<br>[STARTSEITE_EN][MSGSTARTSEITE_EN]</td></tr>-->
	      <tr><td>{|Meta-Title (DE)|}:<br>[METATITLE_DE][MSGMETATITLE_DE]</td><td width="20"></td>
	      <td>{|Meta-Title (EN)|}:<br>[METATITLE_EN][MSGMETATITLE_EN]</td></tr>
	      <tr><td>{|Meta-Description (DE)|}:<br>[METADESCRIPTION_DE][MSGMETADESCRIPTION_DE]</td><td width="20"></td>
	      <td>{|Meta-Description (EN)|}:<br>[METADESCRIPTION_EN][MSGMETADESCRIPTION_EN]</td></tr>
	      <tr><td>{|Meta-Keywords (DE)|}:<br>[METAKEYWORDS_DE][MSGMETAKEYWORDS_DE]</td><td width="20"></td>
	      <td>{|Meta-Keywords (EN)|}:<br>[METAKEYWORDS_EN][MSGMETAKEYWORDS_EN]</td></tr>
</table>
</fieldset>
</div>
</div>
</div>
</div>

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">



<fieldset><legend>&nbsp;{|Katalog|}&nbsp;</legend>

<table class="mkTableFormular" border="0">
	     <tr><td width="500" colspan="3">{|Katalogartikel|}:&nbsp;[KATALOG][MSGKATALOG]</td></tr>
	     <tr><td width="500">{|Bezeichnung (DE)|}:<br>[KATALOGBEZEICHNUNG_DE][MSGKATALOGBEZEICHNUNG_DE]</td><td width="20"></td>
	      <td>{|Bezeichnung (EN)|}:<br>[KATALOGBEZEICHNUNG_EN][MSGKATALOGBEZEICHNUNG_EN]</td></tr>
	     <tr><td>{|Katalogtext (DE)|}:<br>[KATALOGTEXT_DE][MSGKATALOGTEXT_DE]</td><td width="20"></td>
	     <td>{|Katalogtext (EN)|}:<br>[KATALOGTEXT_EN][MSGKATALOGTEXT_EN]</td></tr>
</table>
</fieldset>
</div>
</div>
</div>
</div>


 </div>
[DISABLECLOSESTOCK]

[DISABLEOPENPARAMETER]

<div id="tabs-3">
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">



<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">{|Artikel|} <font color="blue">[ANZEIGENUMMER]</font></b>[ANZEIGENAMEDE]</td>
<td>[STATUSICONS]</td>
<td align="right">[ICONMENU]&nbsp; <input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-3';"/> [ABBRECHEN]</td>
</tr>
</table>
</div>
</div>
</div>
</div>

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">


[DISABLECLOSEPARAMETER]
[DISABLEOPENPARAMETER2]
<fieldset><legend>&nbsp;{|Parameter und Freifelder|}</legend>

<table class="mkTableFormular" border="0">

[VORFREIFELD1][FREIFELD1BEZEICHNUNG]:</td><td width="200">[FREIFELD1][MSGFREIFELD1][NACHFREIFELD1]
[VORFREIFELD2][FREIFELD2BEZEICHNUNG]:</td><td width="200">[FREIFELD2][MSGFREIFELD2][NACHFREIFELD2]
[VORFREIFELD3][FREIFELD3BEZEICHNUNG]:</td><td width="200">[FREIFELD3][MSGFREIFELD3][NACHFREIFELD3]
[VORFREIFELD4][FREIFELD4BEZEICHNUNG]:</td><td width="200">[FREIFELD4][MSGFREIFELD4][NACHFREIFELD4]
[VORFREIFELD5][FREIFELD5BEZEICHNUNG]:</td><td width="200">[FREIFELD5][MSGFREIFELD5][NACHFREIFELD5]
[VORFREIFELD6][FREIFELD6BEZEICHNUNG]:</td><td width="200">[FREIFELD6][MSGFREIFELD6][NACHFREIFELD6]
[VORFREIFELD7][FREIFELD7BEZEICHNUNG]:</td><td width="200">[FREIFELD7][MSGFREIFELD7][NACHFREIFELD7]
[VORFREIFELD8][FREIFELD8BEZEICHNUNG]:</td><td width="200">[FREIFELD8][MSGFREIFELD8][NACHFREIFELD8]
[VORFREIFELD9][FREIFELD9BEZEICHNUNG]:</td><td width="200">[FREIFELD9][MSGFREIFELD9][NACHFREIFELD9]
[VORFREIFELD10][FREIFELD10BEZEICHNUNG]:</td><td width="200">[FREIFELD10][MSGFREIFELD10][NACHFREIFELD10]
[VORFREIFELD11][FREIFELD11BEZEICHNUNG]:</td><td width="200">[FREIFELD11][MSGFREIFELD11][NACHFREIFELD11]
[VORFREIFELD12][FREIFELD12BEZEICHNUNG]:</td><td width="200">[FREIFELD12][MSGFREIFELD12][NACHFREIFELD12]
[VORFREIFELD13][FREIFELD13BEZEICHNUNG]:</td><td width="200">[FREIFELD13][MSGFREIFELD13][NACHFREIFELD13]
[VORFREIFELD14][FREIFELD14BEZEICHNUNG]:</td><td width="200">[FREIFELD14][MSGFREIFELD14][NACHFREIFELD14]
[VORFREIFELD15][FREIFELD15BEZEICHNUNG]:</td><td width="200">[FREIFELD15][MSGFREIFELD15][NACHFREIFELD15]
[VORFREIFELD16][FREIFELD16BEZEICHNUNG]:</td><td width="200">[FREIFELD16][MSGFREIFELD16][NACHFREIFELD16]
[VORFREIFELD17][FREIFELD17BEZEICHNUNG]:</td><td width="200">[FREIFELD17][MSGFREIFELD17][NACHFREIFELD17]
[VORFREIFELD18][FREIFELD18BEZEICHNUNG]:</td><td width="200">[FREIFELD18][MSGFREIFELD18][NACHFREIFELD18]
[VORFREIFELD19][FREIFELD19BEZEICHNUNG]:</td><td width="200">[FREIFELD19][MSGFREIFELD19][NACHFREIFELD19]
[VORFREIFELD20][FREIFELD20BEZEICHNUNG]:</td><td width="200">[FREIFELD20][MSGFREIFELD20][NACHFREIFELD20]
[VORFREIFELD21][FREIFELD21BEZEICHNUNG]:</td><td width="200">[FREIFELD21][MSGFREIFELD21][NACHFREIFELD21]
[VORFREIFELD22][FREIFELD22BEZEICHNUNG]:</td><td width="200">[FREIFELD22][MSGFREIFELD22][NACHFREIFELD22]
[VORFREIFELD23][FREIFELD23BEZEICHNUNG]:</td><td width="200">[FREIFELD23][MSGFREIFELD23][NACHFREIFELD23]
[VORFREIFELD24][FREIFELD24BEZEICHNUNG]:</td><td width="200">[FREIFELD24][MSGFREIFELD24][NACHFREIFELD24]
[VORFREIFELD25][FREIFELD25BEZEICHNUNG]:</td><td width="200">[FREIFELD25][MSGFREIFELD25][NACHFREIFELD25]
[VORFREIFELD26][FREIFELD26BEZEICHNUNG]:</td><td width="200">[FREIFELD26][MSGFREIFELD26][NACHFREIFELD26]
[VORFREIFELD27][FREIFELD27BEZEICHNUNG]:</td><td width="200">[FREIFELD27][MSGFREIFELD27][NACHFREIFELD27]
[VORFREIFELD28][FREIFELD28BEZEICHNUNG]:</td><td width="200">[FREIFELD28][MSGFREIFELD28][NACHFREIFELD28]
[VORFREIFELD29][FREIFELD29BEZEICHNUNG]:</td><td width="200">[FREIFELD29][MSGFREIFELD29][NACHFREIFELD29]
[VORFREIFELD30][FREIFELD30BEZEICHNUNG]:</td><td width="200">[FREIFELD30][MSGFREIFELD30][NACHFREIFELD30]
[VORFREIFELD31][FREIFELD31BEZEICHNUNG]:</td><td width="200">[FREIFELD31][MSGFREIFELD31][NACHFREIFELD31]
[VORFREIFELD32][FREIFELD32BEZEICHNUNG]:</td><td width="200">[FREIFELD32][MSGFREIFELD32][NACHFREIFELD32]
[VORFREIFELD33][FREIFELD33BEZEICHNUNG]:</td><td width="200">[FREIFELD33][MSGFREIFELD33][NACHFREIFELD33]
[VORFREIFELD34][FREIFELD34BEZEICHNUNG]:</td><td width="200">[FREIFELD34][MSGFREIFELD34][NACHFREIFELD34]
[VORFREIFELD35][FREIFELD35BEZEICHNUNG]:</td><td width="200">[FREIFELD35][MSGFREIFELD35][NACHFREIFELD35]
[VORFREIFELD36][FREIFELD36BEZEICHNUNG]:</td><td width="200">[FREIFELD36][MSGFREIFELD36][NACHFREIFELD36]
[VORFREIFELD37][FREIFELD37BEZEICHNUNG]:</td><td width="200">[FREIFELD37][MSGFREIFELD37][NACHFREIFELD37]
[VORFREIFELD38][FREIFELD38BEZEICHNUNG]:</td><td width="200">[FREIFELD38][MSGFREIFELD38][NACHFREIFELD38]
[VORFREIFELD39][FREIFELD39BEZEICHNUNG]:</td><td width="200">[FREIFELD39][MSGFREIFELD39][NACHFREIFELD39]
[VORFREIFELD40][FREIFELD40BEZEICHNUNG]:</td><td width="200">[FREIFELD40][MSGFREIFELD40][NACHFREIFELD40]

</table>
</fieldset>
[DISABLECLOSEPARAMETER2]
[DISABLEOPENPARAMETER]
</div>
</div>
</div>
</div>




</div>
[DISABLECLOSEPARAMETER]

[DISABLEOPENSHOP]
<div id="tabs-4">

   [MESSAGE]
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height">

<table width="100%" align="center">
<tr>
<td>&nbsp;<b style="font-size: 14pt">Artikel <font color="blue">[ANZEIGENUMMER]</font></b>[ANZEIGENAMEDE]</td>
<td>[STATUSICONS]</td>
<td align="right">[ICONMENU]&nbsp; <input type="submit" name="speichern"
    value="Speichern" onclick="this.form.action += '#tabs-1';"/> [ABBRECHEN]</td>
</tr>
</table>
</div>
</div>
</div>
</div>



<!--<table class="mkTableFormular" height="120">
<tr><td width="200"><font color="#961F1C">Shop (1):</font></td><td width="">[SHOPSTART][SHOP][MSGSHOP][SHOPENDE]
</td><td width="20"></td><td width="">[SHOP1BUTTON][SHOP1BUTTONHOOK]</td><td></td></tr>
<tr><td width="200"><font color="#961F1C">Shop (2):</font></td><td width="">[SHOP2START][SHOP2][MSGSHOP2][SHOP2ENDE]
</td><td width="20"></td><td width="">[SHOP2BUTTON][SHOP2BUTTONHOOK]</td><td></td></tr>
<tr><td width="200"><font color="#961F1C">Shop (3):</font></td><td width="">[SHOP3START][SHOP3][MSGSHOP3][SHOP3ENDE]
</td><td width="20"></td><td width="">[SHOP3BUTTON][SHOP3BUTTONHOOK]</td><td></td></tr>

</table>-->

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside_white inside-full-height">


<fieldset class="white"><legend>{|Online-Shops|}</legend>
[SHOPTABELLE]
  <center>[NEUERONLINESHOPBUTTON]</center>
</fieldset>


</div>
</div>
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">




<fieldset><legend>&nbsp;{|Lagerbestand|}</legend>
<table class="mkTableFormular" height="270">
 <tr><td>{|Lagerzahlen Sync.|}:</td><td>[AUTOLAGERLAMPE][MSGAUTOLAGERLAMPE]&nbsp;</td></tr>
 <tr><td>{|Restmenge (Abverkauf)|}:</td><td>[RESTMENGE][MSGRESTMENGE]</td></tr>
 <tr><td>{|Pseudo Lagerzahl|}:</td><td>[PSEUDOLAGER][MSGPSEUDOLAGER]</td></tr>
 [HOOK_PSEUDO_STORAGE]
 <tr><td>{|Lieferzeittext manuell|}:</td><td>[LIEFERZEITMANUELL][MSGLIEFERZEITMANUELL]</td></tr>
 <tr><td>{|Bestand Alternativartikel|}:</td><td>[BESTANDALTERNATIVARTIKEL][MSGBESTANDALTERNATIVARTIKEL]</td></tr>
 <tr><td>{|Lagerkorrekturwert|}:</td><td>[LAGERKORREKTURWERT][MSGLAGERKORREKTURWERT]&nbsp;</td></tr>
 <tr><td></td><td></td></tr>
</table>
</fieldset>
</div>
</div>
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>&nbsp;{|Online-Shop Optionen|}</legend>
<table class="mkTableFormular" height="270">
<!--<tr><td width="200">{|Shop-Optionen|}:</td><td width="200">[OPTIONEN]</td></tr>
<tr><td width="200">{|Partnerprogramm Sperre|}:</td><td>[PARTNERPROGRAMM_SPERRE][MSGPARTNERPROGRAMM_SPERRE]</td></tr>
<tr><td>{|Neu|}:</td><td>[NEU][MSGNEU]</td></tr>
<tr><td>{|TopSeller|}:</td><td>[TOPSELLER][MSGTOPSELLER]</td></tr>
<tr><td>{|Startseite|}:</td><td>[STARTSEITE][MSGSTARTSEITE]</td></tr>
<tr><td>{|Downloadartikel|}:</td><td>[DOWNLOADARTIKEL][MSGDOWNLOADARTIKEL]</td></tr>-->
<tr><td>{|Artikel ausverkauft|}:</td><td width="180">[AUSVERKAUFT][MSGAUSVERKAUFT]&nbsp;</td></tr>
<tr><td>{|Artikel inaktiv|}:</td><td>[INAKTIV][MSGINAKTIV]&nbsp;</td></tr>
<tr><td>{|Pseudo Preis / UVP|}:</td><td>[PSEUDOPREIS][MSGPSEUDOPREIS]</td></tr>
<tr><td>{|Generiere Artikelnummer bei Optionsartikel|}:</td><td>[GENERIERENUMMERBEIOPTION][MSGGENERIERENUMMERBEIOPTION]</td></tr>
<tr><td>{|bei Kopie|}:</td><td>[VARIANTE_KOPIE][MSGVARIANTE_KOPIE]</td></tr>
<tr><td nowrap>{|Kundenspezifischer Artikel erstellen|}</td><td>[UNIKATBEIKOPIE][MSGUNIKATBEIKOPIE]</td></tr>
</table>
</fieldset>

</div>
</div>
</div>
</div>

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-12 col-md-height">
<div class="inside inside-full-height">


<fieldset><legend>&nbsp;{|Auftragsimport Einstellungen|}&nbsp;</legend>
<table class="mkTableFormular" width="100%">
 <tr><td width="200">{|Auto-Abgleich|}:</td><td colspan="4">[AUTOABGLEICHERLAUBT][MSGAUTOABGLEICHERLAUBT]&nbsp;<i>{|Preis und Artikelname bei Auftragsimport von Fremdshop verwenden und nicht aus WaWision verwenden z.B. bei Gutschein, Porto etc.|}</i></td></tr>
</table>

</fieldset>


</div>
</div>
</div>
</div>


</div>

[DISABLECLOSESHOP]

<div id="tabs-5">

</div>

<script>

    function hidefunctiononlinshopspopup_ausartikel(){
        if($('input[name="onlinshopspopup_ausartikel"]').prop('checked')){
            $('.onlineshopeditdis').css('display','none')
        }else{
            $('.onlineshopeditdis').css('display','')
        }
    }

$(document).ready(function() {
    $('input[name="onlinshopspopup_ausartikel"]').on('change',function(){ hidefunctiononlinshopspopup_ausartikel(); });

    $('#onlineshopeditpopup').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 940,
      title:'{|Onlineshops|}',
      buttons: {
        '{|SPEICHERN|}': function()
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
                url: 'index.php?module=artikel&action=edit&cmd=saveonlineshops&id=[ID]',
                type: 'POST',
                dataType: 'json',
                data: { 
                  "sid":$('#onlinshopspopup_sid').val(),
                  "shop":$('#onlinshopspopup_shop').val(),
                  "lagerkorrekturwert":$('#onlinshopspopup_lagerkorrekturwert').val(),
                  "pseudolager":$('#onlinshopspopup_pseudolager').val(),
                  "autolagerlampe":$('#onlinshopspopup_autolagerlampe').prop('checked')?1:0,
                  "aktiv":$('#onlinshopspopup_aktiv').prop('checked')?1:0,
                  "restmenge":$('#onlinshopspopup_restmenge').prop('checked')?1:0,
                  "lieferzeitmanuell":$('#onlinshopspopup_lieferzeitmanuell').val(),
                  "pseudopreis":$('#onlinshopspopup_pseudopreis').val(),
                  "generierenummerbeioption":$('#onlinshopspopup_generierenummerbeioption').prop('checked')?1:0,
                  "ausartikel":$('#onlinshopspopup_ausartikel').prop('checked')?1:0,
                  "variante_kopie":$('#onlinshopspopup_variante_kopie').prop('checked')?1:0,
                  "unikat":$('#onlinshopspopup_unikat').prop('checked')?1:0,
                  "unikatbeikopie":$('#onlinshopspopup_unikatbeikopie').prop('checked')?1:0,
                  "autoabgeleicherlaubt":$('#onlinshopspopup_autoabgeleicherlaubt').prop('checked')?1:0
                },
                success: function(data) {
                  if(typeof data.status != 'undefined' && data.status == 1)
                  {
                    $('#onlineshopeditpopup').dialog('close');
                    var oTable = $('#artikel_onlineshops').DataTable( );
                    oTable.ajax.reload();
                  }else{
                    if(typeof data.Error != 'undefined' && data.Error != '')
                    {
                      alert(data.Error);
                    }else{
                      alert('Fehler beim Speichern');
                    }
                  }
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
  
});
  
  
function deleteonlineshop(id)
{
  if(id)
  {
    if(confirm('{|Eintrag wirklich löschen?|}'))
    {
      $.ajax({
        url: 'index.php?module=artikel&action=edit&cmd=deleteonlineshop&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: { sid: id},
        success: function(data) {
          var oTable = $('#artikel_onlineshops').DataTable( );
          oTable.ajax.reload();
        }
      });
    }
  }
}
  
function editonlineshop(id)
{
    $.ajax({
        url: 'index.php?module=artikel&action=edit&cmd=getonlineshop&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: { sid: id},
        success: function(data) {
          $('#onlinshopspopup_sid').val(data.id);
          $('#onlinshopspopup_shop').val(data.shop);
          $('#onlinshopspopup_lagerkorrekturwert').val(data.lagerkorrekturwert);
          $('#onlinshopspopup_pseudolager').val(data.pseudolager);
          $('#onlinshopspopup_lieferzeitmanuell').val(data.lieferzeitmanuell);
          $('#onlinshopspopup_pseudopreis').val(data.pseudopreis);
          $('#onlinshopspopup_autoabgeleicherlaubt').prop('checked',data.autoabgeleicherlaubt==1?true:false);
          $('#onlinshopspopup_autolagerlampe').prop('checked',data.autolagerlampe==1?true:false);
          $('#onlinshopspopup_restmenge').prop('checked',data.restmenge==1?true:false);
          $('#onlinshopspopup_generierenummerbeioption').prop('checked',data.generierenummerbeioption==1?true:false);
          $('#onlinshopspopup_unikat').prop('checked',data.unikat==1?true:false);
          $('#onlinshopspopup_unikatbeikopie').prop('checked',data.unikatbeikopie==1?true:false);
          $('#onlinshopspopup_variante_kopie').prop('checked',data.variante_kopie==1?true:false);
          $('#onlinshopspopup_ausartikel').prop('checked',data.ausartikel==1?true:false);
          $('#onlinshopspopup_aktiv').prop('checked',data.aktiv==1?true:false);
          $('#onlineshopeditpopup').dialog('open');
          if(data.ausartikel == 1){
              $('.onlineshopeditdis').css('display','none')
          }else{
              $('.onlineshopeditdis').css('display','')
          }
        },
        beforeSend: function() {

        }
    });

}
  
</script>
  


<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->
</form>
<div id="onlineshopeditpopup" style="display:none">
<fieldset><legend>{|Verknüpfung|}</legend>
  <table>
    <tr><td width="220">{|Onlineshop|}:</td><td>[ONLINSHOPSPOPUP_SID][MSGONLINSHOPSPOPUP_SID][ONLINSHOPSPOPUP_SHOP][MSGONLINSHOPSPOPUP_SHOP]</td></tr>
    <tr><td>{|aktiv|}:</td><td>[ONLINSHOPSPOPUP_AKTIV][MSGONLINSHOPSPOPUP_AKTIV]</td></tr>
  </table>
</fieldset>
<fieldset><legend>{|Shop Einstellungen|}</legend>
  <table>
    <tr><td width="220">{|Einstellungen aus Artikel|}:</td><td>[ONLINSHOPSPOPUP_AUSARTIKEL][MSGONLINSHOPSPOPUP_AUSARTIKEL]</td></tr>
    <tr class="onlineshopeditdis"><td nowrap><legend>{|Individuelle Einstellungen|}</legend></td><td>&nbsp;</td></tr>
    <tr class="onlineshopeditdis"><td>{|Pseudolager|}:</td><td>[ONLINSHOPSPOPUP_PSEUDOLAGER][MSGONLINSHOPSPOPUP_PSEUDOLAGER]</td></tr>
    <tr class="onlineshopeditdis"><td>{|Lagersync|}:</td><td>[ONLINSHOPSPOPUP_AUTOLAGERLAMPE][MSGONLINSHOPSPOPUP_AUTOLAGERLAMPE]</td></tr>
    <tr class="onlineshopeditdis"><td>{|Restmenge (Abverkauf)|}:</td><td>[ONLINSHOPSPOPUP_RESTMENGE][MSGONLINSHOPSPOPUP_RESTMENGE]</td></tr>
    <tr class="onlineshopeditdis"><td>{|Lieferzeit manuell|}:</td><td>[ONLINSHOPSPOPUP_LIEFERZEITMANUELL][MSGONLINSHOPSPOPUP_LIEFERZEITMANUELL]</td></tr>
    <tr class="onlineshopeditdis"><td>{|Pseudopreis|}:</td><td>[ONLINSHOPSPOPUP_PSEUDOPREIS][MSGONLINSHOPSPOPUP_PSEUDOPREIS]</td></tr>
    <tr class="onlineshopeditdis"><td>{|Generiere Nummer bei Option|}:</td><td>[ONLINSHOPSPOPUP_GENERIERENUMMERBEIOPTION][MSGONLINSHOPSPOPUP_GENERIERENUMMERBEIOPTION]</td></tr>
    <tr class="onlineshopeditdis"><td>{|Variante bei Kopie|}:</td><td>[ONLINSHOPSPOPUP_VARIANTE_KOPIE][MSGONLINSHOPSPOPUP_VARIANTE_KOPIE]</td></tr>
    <tr class="onlineshopeditdis"><td>{|Kundenspezifischer Artikel|}:</td><td>[ONLINSHOPSPOPUP_UNIKAT][MSGONLINSHOPSPOPUP_UNIKAT]</td></tr>
    <tr class="onlineshopeditdis"><td width="220">{|Kundenspezifischen Artikel erstellen|}:</td><td>[ONLINSHOPSPOPUP_UNIKATBEIKOPIE][MSGONLINSHOPSPOPUP_UNIKATBEIKOPIE]</td></tr>
    <tr class="onlineshopeditdis"><td>{|Lagerkorrekturwert|}:</td><td>[ONLINSHOPSPOPUP_LAGERKORREKTURWERT][MSGONLINSHOPSPOPUP_LAGERKORREKTURWERT]</td></tr>
    <tr class="onlineshopeditdis"><td>{|Autoabgleich erlaubt|}:</td><td>[ONLINSHOPSPOPUP_AUTOABGELEICHERLAUBT][MSGONLINSHOPSPOPUP_AUTOABGELEICHERLAUBT]</td></tr>
  </table>
</fieldset>
</div>
