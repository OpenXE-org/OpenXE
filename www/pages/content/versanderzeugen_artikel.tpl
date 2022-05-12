<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<table class="hidenot480"><tr><td>Kunde anzeigen</td><td><input type="checkbox" id="kundeoffene" /></td></tr></table>
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>

<script type="text/javascript">
$(document).ready(function() {

  $('#kundeoffene').on('change',function(){
    var wert = $(this).prop('checked');
    $('#versanderzeugen_artikeloffen > thead > tr > th:nth-child(4)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikeloffen > tfoot > tr > th:nth-child(4)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikeloffen > tbody > tr > td:nth-child(4)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikeloffen > thead > tr > th:nth-child(3)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikeloffen > tfoot > tr > th:nth-child(3)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikeloffen > tbody > tr > td:nth-child(3)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikel > thead > tr > th:nth-child(4)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikel > tfoot > tr > th:nth-child(4)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikel > tbody > tr > td:nth-child(4)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikel > thead > tr > th:nth-child(3)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikel > tfoot > tr > th:nth-child(3)').toggleClass('showtd', wert);
    $('#versanderzeugen_artikel > tbody > tr > td:nth-child(3)').toggleClass('showtd', wert);
  });
});
</script>
<style>

@media screen and (min-width: 481px)
{
  .hidenot480
  {
    display:none;
  }
}

.showtd
{
  display:table-cell !important;
  min-width:100px;
}

@media screen and (max-width: 480px){
  .hidenot480
  {
    max-height:20px;
    padding-top:0;
    padding-bottom:0;
    margin-top:0;
    margin-bottom:0;
  }
  #versanderzeugen_artikeloffen > thead > tr > th:nth-child(4)
  {
    display:none;
  }
  #versanderzeugen_artikeloffen > tfoot > tr > th:nth-child(4)
  {
    display:none;
  }
  #versanderzeugen_artikeloffen > tbody > tr > td:nth-child(4)
  {
    display:none;
  }
  #versanderzeugen_artikeloffen > thead > tr > th:nth-child(3)
  {
    display:none;
  }
  #versanderzeugen_artikeloffen > tfoot > tr > th:nth-child(3)
  {
    display:none;
  }
  #versanderzeugen_artikeloffen > tbody > tr > td:nth-child(3)
  {
    display:none;
  }
  
  #versanderzeugen_artikel > thead > tr > th:nth-child(4)
  {
    display:none;
  }
  #versanderzeugen_artikel > tfoot > tr > th:nth-child(4)
  {
    display:none;
  }
  #versanderzeugen_artikel > tbody > tr > td:nth-child(4)
  {
    display:none;
  }
  #versanderzeugen_artikel > thead > tr > th:nth-child(3)
  {
    display:none;
  }
  #versanderzeugen_artikel > tfoot > tr > th:nth-child(3)
  {
    display:none;
  }
  #versanderzeugen_artikel > tbody > tr > td:nth-child(3)
  {
    display:none;
  } 
}
</style>