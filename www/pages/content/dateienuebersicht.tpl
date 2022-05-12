<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|&Uuml;bersicht|}</a></li>
        <li><a href="#tabs-2">{|neue Datei anlegen|}</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[PRETAB1]
[TAB1]
<script>
function chauswahl()
{
  var auswahl = '';
  $('#datei_list_referer').find('input:checked').each(function(){
    var aid = this.id.split('_');
    if(auswahl != '')auswahl = auswahl + ',';
    auswahl = auswahl + aid[ 1 ];
  });
  $('#auswahl').val(auswahl);
}
</script>
</div>

<div id="tabs-2">
[TAB2]
</div>

<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->


[SCRIPT]