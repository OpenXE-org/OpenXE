<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<div id="tabs-1">
[MESSAGE]
<form method="post" action="#">
[TAB1]

<center>
<input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;alle markieren&nbsp;
<input type="submit" value="Download als ZIP" name="download">

[SAVELOCALFOLDER]
</center>


</form>
</div>


</div>

<script>
function alleauswaehlen()
{
  var wert = $('#auswahlalle').prop('checked');
  $('#transus_rechnungen').find(':checkbox').prop('checked',wert);
}
</script>
