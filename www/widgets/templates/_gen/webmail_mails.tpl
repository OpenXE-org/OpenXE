<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

<!-- gehort zu tabview -->
<div class="yui-navset">
    <ul class="yui-nav">
        <li class="[AKTIV_TAB1]"><a href="#tab1"><em>Lesen</em></a></li>
        <li class="[AKTIV_TAB2]"><a href="index.php?module=webmail&action=list"><em>Eingang</em></a></li>
        <li class="[AKTIV_TAB3]"><a href="index.php?module=webmail&action=list"><em>Schreiben</em></a></li>
        <li class="[AKTIV_TAB4]"><a href="index.php?module=webmail&action=list"><em>Einstellungen</em></a></li>
    </ul>

    <div class="yui-content">
<!-- ende gehort zu tabview -->

<div>
 <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="3" bordercolor="" class="" align="" bgcolor="" height="" valign="">E-Mail von [MAILSENDER] am [MAILEMPFANG]<br></td>
      </tr>

      <tr valign="top" colspan="3">
        <td>

[MESSAGE]

<table border="0" width="100%">
<tr valign="top"><td width="65%">

<fieldset><legend>E-Mail</legend>
<table width="100%">
  <tr><td width="80px">{|Von|}:</td><td>[MAILSENDER]</td></tr>
[CC]
[BCC]
[REPLYTO]
  <tr><td>{|Betreff|}:</td><td>[SUBJECT]</td></tr>
  <tr><td colspan=2><div style="border:1px solid gray;background-color:white;min-height:250px; padding: 5px;">[MAILMESSAGE]</div>
  GPG-Verschl&uuml;sselt (0xA620923)</td></tr>
</table>
</fieldset>

<fieldset><legend>Anhänge</legend>
<table width="100%">
<tr><td>Anhang 1</td><td>Bild_von_artikel.png </td><td>23KB</td><td><a href="#">Download</a>&nbsp;|&nbsp;<a href="#">Vorschau</a></td></tr>
<tr><td>Anhang 2</td><td>sauter.vcf </td><td>1KB</td><td><a href="#">Download</a></td></tr>
</table>
</fieldset>

</td><td>
<fieldset><legend>{|Zuordnung|}</legend> 
<table>
<tr><td><b>Objekt</b></td><td><b>Bezeichnung</b></td><td align="center"><b>Aktion</b></td></tr>
[ZUORDNUNGEN]
</table>
<div align="center" style="margin-top:6px;"><input type="button" name="b" onclick="javascript:window.open('index.php?module=webmail&action=assocs&id=[MAILID]','popup','location=no,menubar=no,toolbar=no,status=no,resizable=yes,scrollbars=yes,width=300,height=250');" value="Einfügen" /></div>
</fieldset>

<fieldset><legend>{|Aktionen|}</legend> 
<b>Hier passt was garnicht!<br></b>

 Drucken<br />
[DO][MSGDO] Drucken<br />
[DO][MSGDO] Drucken<br />
[DO][MSGDO] SPAM<br />
[GELESENSTATUS]

<div align="center" style="margin-top:6px;"><input type="submit" value="Ausf&uuml;hren"></div>
</fieldset>

<fieldset><legend>{|Verlauf|}</legend>

<table>
<tr valign="top"><td>20.11.2009</td><td>Status von Bestellung</td></tr>
<tr valign="top"><td>21.11.2009</td><td>Re: Status von Bestellung</td></tr>
<tr valign="top"><td>21.11.2009</td><td>Re: Re: Status von Bestellung</td></tr>
</table>
</fieldset>


</td></tr>


</table>




        </td>
      </tr>
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Antworten" /> 
    <input type="button" value="Weiterleiten" />
    <input type="button" value="Abbrechen" />
</td>
    </tr>


    </tbody>
  </table>

</div>

 <!-- tab view schließen -->
</div></div>
<!-- ende tab view schließen -->
  
  </form>
