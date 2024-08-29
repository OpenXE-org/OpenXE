<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <!-- Example for multiple tabs
    <ul hidden">
        <li><a href="#tabs-1">First Tab</a></li>
        <li><a href="#tabs-2">Second Tab</a></li>
    </ul>
    -->
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Gruppe|}</legend><i>Gruppe f&uuml;r die Zuordnung zu Preislisten, Onlineshop-Preislisten oder Adressgruppen</i>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Aktiv|}:
                                        </td>
                                        <td>
                                            <input type="checkbox" name="aktiv" id="aktiv" value="1" [AKTIV] size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Name|}:
                                        </td>
                                        <td>
                                            <input type="text" name="name" id="name" value="[NAME]" required size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Kennziffer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="kennziffer" id="kennziffer" pattern="[a-zA-Z0-9_\-]+" value="[KENNZIFFER]" required size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Internebemerkung|}:
                                        </td>
                                        <td>
                                            <textarea name="internebemerkung" id="internebemerkung" rows="6" style="width:100%;"></textarea>    
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Art|}:
                                        </td>
                                        <td>                                           
                            				<select name="art" id="art">
                            				    [ARTSELECT]
                        					</select>                                                                                      
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Projekt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="projekt" id="projekt" value="[PROJEKT]" size="20">
                                        </td>
                                    </tr>
                                    <tr [PREISGRUPPEHIDDEN]>
                                        <td>
                                            {|Grundrabatt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="grundrabatt" id="grundrabatt" value="[GRUNDRABATT]" size="20">
                                        </td>
                                    </tr>
                                    <tr [PREISGRUPPEHIDDEN]>
                                        <td>
                                            {|Zahlungszieltage|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zahlungszieltage" id="zahlungszieltage" value="[ZAHLUNGSZIELTAGE]" size="20">
                                        </td>
                                    </tr>
                                    <tr [PREISGRUPPEHIDDEN]>
                                        <td>
                                            {|Zahlungszielskonto|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zahlungszielskonto" id="zahlungszielskonto" value="[ZAHLUNGSZIELSKONTO]" size="20">
                                        </td>
                                    </tr>
                                    <tr [PREISGRUPPEHIDDEN]>
                                        <td>
                                            {|Zahlungszieltageskonto|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zahlungszieltageskonto" id="zahlungszieltageskonto" value="[ZAHLUNGSZIELTAGESKONTO]" size="20">
                                        </td>
                                    </tr>
                                    <tr [PREISGRUPPEHIDDEN]>
                                        <td>
                                            {|Portofrei_aktiv|}:
                                        </td>
                                        <td>
                                            <input type="text" name="portofrei_aktiv" id="portofrei_aktiv" value="[PORTOFREI_AKTIV]" size="20">
                                        </td>
                                    </tr>
                                    <tr [PREISGRUPPEHIDDEN]>
                                        <td>
                                            {|Portofreiab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="portofreiab" id="portofreiab" value="[PORTOFREIAB]" size="20">
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <!-- Example for 2nd row            
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Another legend|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Name|}:
                                        </td>
                                        <td>
                                            <input type="text" name="name" id="name" value="[NAME]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Art|}:
                                        </td>
                                        <td>
                                            <input type="text" name="art" id="art" value="[ART]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Kennziffer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="kennziffer" id="kennziffer" value="[KENNZIFFER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Internebemerkung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="internebemerkung" id="internebemerkung" value="[INTERNEBEMERKUNG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Grundrabatt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="grundrabatt" id="grundrabatt" value="[GRUNDRABATT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rabatt1|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rabatt1" id="rabatt1" value="[RABATT1]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rabatt2|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rabatt2" id="rabatt2" value="[RABATT2]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rabatt3|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rabatt3" id="rabatt3" value="[RABATT3]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rabatt4|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rabatt4" id="rabatt4" value="[RABATT4]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rabatt5|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rabatt5" id="rabatt5" value="[RABATT5]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Sonderrabatt_skonto|}:
                                        </td>
                                        <td>
                                            <input type="text" name="sonderrabatt_skonto" id="sonderrabatt_skonto" value="[SONDERRABATT_SKONTO]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Provision|}:
                                        </td>
                                        <td>
                                            <input type="text" name="provision" id="provision" value="[PROVISION]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Kundennummer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="kundennummer" id="kundennummer" value="[KUNDENNUMMER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Partnerid|}:
                                        </td>
                                        <td>
                                            <input type="text" name="partnerid" id="partnerid" value="[PARTNERID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Dta_aktiv|}:
                                        </td>
                                        <td>
                                            <input type="text" name="dta_aktiv" id="dta_aktiv" value="[DTA_AKTIV]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Dta_periode|}:
                                        </td>
                                        <td>
                                            <input type="text" name="dta_periode" id="dta_periode" value="[DTA_PERIODE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Dta_dateiname|}:
                                        </td>
                                        <td>
                                            <input type="text" name="dta_dateiname" id="dta_dateiname" value="[DTA_DATEINAME]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Dta_mail|}:
                                        </td>
                                        <td>
                                            <input type="text" name="dta_mail" id="dta_mail" value="[DTA_MAIL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Dta_mail_betreff|}:
                                        </td>
                                        <td>
                                            <input type="text" name="dta_mail_betreff" id="dta_mail_betreff" value="[DTA_MAIL_BETREFF]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Dta_mail_text|}:
                                        </td>
                                        <td>
                                            <input type="text" name="dta_mail_text" id="dta_mail_text" value="[DTA_MAIL_TEXT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Dtavariablen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="dtavariablen" id="dtavariablen" value="[DTAVARIABLEN]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Dta_variante|}:
                                        </td>
                                        <td>
                                            <input type="text" name="dta_variante" id="dta_variante" value="[DTA_VARIANTE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus1|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus1" id="bonus1" value="[BONUS1]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus1_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus1_ab" id="bonus1_ab" value="[BONUS1_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus2|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus2" id="bonus2" value="[BONUS2]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus2_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus2_ab" id="bonus2_ab" value="[BONUS2_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus3|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus3" id="bonus3" value="[BONUS3]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus3_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus3_ab" id="bonus3_ab" value="[BONUS3_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus4|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus4" id="bonus4" value="[BONUS4]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus4_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus4_ab" id="bonus4_ab" value="[BONUS4_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus5|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus5" id="bonus5" value="[BONUS5]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus5_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus5_ab" id="bonus5_ab" value="[BONUS5_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus6|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus6" id="bonus6" value="[BONUS6]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus6_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus6_ab" id="bonus6_ab" value="[BONUS6_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus7|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus7" id="bonus7" value="[BONUS7]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus7_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus7_ab" id="bonus7_ab" value="[BONUS7_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus8|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus8" id="bonus8" value="[BONUS8]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus8_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus8_ab" id="bonus8_ab" value="[BONUS8_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus9|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus9" id="bonus9" value="[BONUS9]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus9_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus9_ab" id="bonus9_ab" value="[BONUS9_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus10|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus10" id="bonus10" value="[BONUS10]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Bonus10_ab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="bonus10_ab" id="bonus10_ab" value="[BONUS10_AB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Zahlungszieltage|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zahlungszieltage" id="zahlungszieltage" value="[ZAHLUNGSZIELTAGE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Zahlungszielskonto|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zahlungszielskonto" id="zahlungszielskonto" value="[ZAHLUNGSZIELSKONTO]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Zahlungszieltageskonto|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zahlungszieltageskonto" id="zahlungszieltageskonto" value="[ZAHLUNGSZIELTAGESKONTO]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Portoartikel|}:
                                        </td>
                                        <td>
                                            <input type="text" name="portoartikel" id="portoartikel" value="[PORTOARTIKEL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Portofreiab|}:
                                        </td>
                                        <td>
                                            <input type="text" name="portofreiab" id="portofreiab" value="[PORTOFREIAB]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Erweiterteoptionen|}:
                                        </td>
                                        <td>
                                            <input type="text" name="erweiterteoptionen" id="erweiterteoptionen" value="[ERWEITERTEOPTIONEN]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Zentralerechnung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zentralerechnung" id="zentralerechnung" value="[ZENTRALERECHNUNG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Zentralregulierung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="zentralregulierung" id="zentralregulierung" value="[ZENTRALREGULIERUNG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Gruppe|}:
                                        </td>
                                        <td>
                                            <input type="text" name="gruppe" id="gruppe" value="[GRUPPE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Preisgruppe|}:
                                        </td>
                                        <td>
                                            <input type="text" name="preisgruppe" id="preisgruppe" value="[PREISGRUPPE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Verbandsgruppe|}:
                                        </td>
                                        <td>
                                            <input type="text" name="verbandsgruppe" id="verbandsgruppe" value="[VERBANDSGRUPPE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_name|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_name" id="rechnung_name" value="[RECHNUNG_NAME]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_strasse|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_strasse" id="rechnung_strasse" value="[RECHNUNG_STRASSE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_ort|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_ort" id="rechnung_ort" value="[RECHNUNG_ORT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_plz|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_plz" id="rechnung_plz" value="[RECHNUNG_PLZ]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_abteilung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_abteilung" id="rechnung_abteilung" value="[RECHNUNG_ABTEILUNG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_land|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_land" id="rechnung_land" value="[RECHNUNG_LAND]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_email|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_email" id="rechnung_email" value="[RECHNUNG_EMAIL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_periode|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_periode" id="rechnung_periode" value="[RECHNUNG_PERIODE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_anzahlpapier|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_anzahlpapier" id="rechnung_anzahlpapier" value="[RECHNUNG_ANZAHLPAPIER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Rechnung_permail|}:
                                        </td>
                                        <td>
                                            <input type="text" name="rechnung_permail" id="rechnung_permail" value="[RECHNUNG_PERMAIL]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Webid|}:
                                        </td>
                                        <td>
                                            <input type="text" name="webid" id="webid" value="[WEBID]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Portofrei_aktiv|}:
                                        </td>
                                        <td>
                                            <input type="text" name="portofrei_aktiv" id="portofrei_aktiv" value="[PORTOFREI_AKTIV]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Projekt|}:
                                        </td>
                                        <td>
                                            <input type="text" name="projekt" id="projekt" value="[PROJEKT]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Objektname|}:
                                        </td>
                                        <td>
                                            <input type="text" name="objektname" id="objektname" value="[OBJEKTNAME]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Objekttyp|}:
                                        </td>
                                        <td>
                                            <input type="text" name="objekttyp" id="objekttyp" value="[OBJEKTTYP]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Parameter|}:
                                        </td>
                                        <td>
                                            <input type="text" name="parameter" id="parameter" value="[PARAMETER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Objektname2|}:
                                        </td>
                                        <td>
                                            <input type="text" name="objektname2" id="objektname2" value="[OBJEKTNAME2]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Objekttyp2|}:
                                        </td>
                                        <td>
                                            <input type="text" name="objekttyp2" id="objekttyp2" value="[OBJEKTTYP2]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Parameter2|}:
                                        </td>
                                        <td>
                                            <input type="text" name="parameter2" id="parameter2" value="[PARAMETER2]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Objektname3|}:
                                        </td>
                                        <td>
                                            <input type="text" name="objektname3" id="objektname3" value="[OBJEKTNAME3]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Objekttyp3|}:
                                        </td>
                                        <td>
                                            <input type="text" name="objekttyp3" id="objekttyp3" value="[OBJEKTTYP3]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Parameter3|}:
                                        </td>
                                        <td>
                                            <input type="text" name="parameter3" id="parameter3" value="[PARAMETER3]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Kategorie|}:
                                        </td>
                                        <td>
                                            <input type="text" name="kategorie" id="kategorie" value="[KATEGORIE]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Aktiv|}:
                                        </td>
                                        <td>
                                            <input type="text" name="aktiv" id="aktiv" value="[AKTIV]" size="20">
                                        </td>
                                    </tr>
                                    
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div> -->
            <input type="submit" name="submit" value="Speichern" style="float:right"/>
        </form>
    </div>    
    <!-- Example for 2nd tab
    <div id="tabs-2">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-12 col-md-12 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|...|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    ...
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <input type="submit" name="submit" value="Speichern" style="float:right"/>
        </form>
    </div>    
    -->
</div>

