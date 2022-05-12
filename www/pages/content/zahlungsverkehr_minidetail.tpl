<script type="text/javascript">

function BaumAnsicht(adresse,abrechnung)
{

$.ajax({
  url: 'index.php',
  type: 'GET',
  data: 'module=zahlungsverkehr&action=baumansicht&id='+adresse+'&abrechnung='+abrechnung,
  success: function(data) {
        //called when successful

newwin=window.open('','printwin','width=700,height=500,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no')
newwin.document.write('<HTML>\n<HEAD>\n')
newwin.document.write('<TITLE>Print Page</TITLE>\n')
newwin.document.write('</HEAD>\n')
newwin.document.write('<BODY>\n')
newwin.document.write(data)
newwin.document.write('</BODY>\n')
newwin.document.write('</HTML>\n')
newwin.document.close()


  },
  error: function(e) {
        //called when there is an error
        //console.log(e.message);
  }
});
}

function DetailsZahlung(rechnung_position)
{

$.ajax({
  url: 'index.php',
  type: 'GET',
  data: 'module=zahlungsverkehr&action=detailsposition&id='+rechnung_position,
  success: function(data) {
	//called when successful

newwin=window.open('','printwin','width=1000,height=650,directories=no,titlebar=no,toolbar=no,location=no,status=no,menubar=no')
newwin.document.write('<HTML>\n<HEAD>\n')
newwin.document.write('<TITLE>Print Page</TITLE>\n')
newwin.document.write('</HEAD>\n')
newwin.document.write('<BODY>\n')
newwin.document.write(data)
newwin.document.write('</BODY>\n')
newwin.document.write('</HTML>\n')
newwin.document.close()


  },
  error: function(e) {
	//called when there is an error
	//console.log(e.message);
  }
});
}

<!--
function printContent(id){
str=document.getElementById(id).innerHTML
newwin=window.open('','printwin','left=100,top=100,width=400,height=400')
newwin.document.write('<HTML>\n<HEAD>\n')
newwin.document.write('<TITLE>Print Page</TITLE>\n')
newwin.document.write('<script>\n')
newwin.document.write('function chkstate(){\n')
newwin.document.write('if(document.readyState=="complete"){\n')
newwin.document.write('window.close()\n')
newwin.document.write('}\n')
newwin.document.write('else{\n')
newwin.document.write('setTimeout("chkstate()",2000)\n')
newwin.document.write('}\n')
newwin.document.write('}\n')
newwin.document.write('function print_win(){\n')
newwin.document.write('window.print();\n')
newwin.document.write('chkstate();\n')
newwin.document.write('}\n')
newwin.document.write('<\/script>\n')
newwin.document.write('</HEAD>\n')
newwin.document.write('<BODY onload="print_win()">\n')
newwin.document.write(str)
newwin.document.write('</BODY>\n')
newwin.document.write('</HTML>\n')
newwin.document.close()
}
//-->
</script><div id="zahlungsverkehr_minidetail_[ID]">
  
<script>
      $(function() {
    $( "#accordion[ID]" ).accordion({ autoHeight: true,fillSpace: true });
  });
  </script>
<style>

.auftrag_cell {
  color: #636363;border: 1px solid #ccc;padding: 0;

}
a:hover {cursor:pointer}
</style>


<table border="0">
<tr valign="top"><td>

</td><td width="100%">  
<div style="height:100%; background-color:white; padding:20px;">
  <div>
    <h2>{|Buchungen|}</h2><hr><p>[BUCHUNGEN]</p>
  </div>
   <!--<h3><a href="#">Zahlungseingang</a></h3>
  <div>[ZAHLUNGEN]</div>
    <h3><a href="#">Rechnungs-/Lieferadresse</a></h3>
  <div>[RECHNUNGLIEFERADRESSE]</div>
     <h3><a href="#">RMA Prozess</a></h3>
  <div>[RMA]</div>-->
<br><br>
<center><button onclick="printContent('zahlungsverkehr_minidetail_[ID]')">{|Drucken|}</button></center>
</div>

</td></tr>

</table>

</div>
