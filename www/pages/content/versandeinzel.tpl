<style>
div.empfaenger
{
width:70%;
min-width:300px;
float:left;
}



div.empfaenger div{
width:49%;
min-width:200px;
float:left;
}

div.informationen
{
width:29%;
min-width:300px;
float:left;
}
@media screen and (min-width: 768px){
  div.informationen fieldset
  {
  height:120px;
  }
  div.empfaenger fieldset
  {

  height:120px;width:100%;
  }
}
@media screen and (max-width: 767px){
input {max-width:137px;}
select{max-width:140px;}
div.empfaenger,div.informationen
{
font: Arial 10px;
}

table.mkTable tr td:first-child{
max-width:50px;
overflow:hidden;

}

}
</style>

<div class="empfaenger">
  <fieldset style=""><legend>Empf&auml;nger</legend>
    <div>
    <b>Rechnungsempf&auml;nger:</b>
    <br><br>
    [ADRESSE]
    </div>
    <div><b>Lieferungsempf&auml;nger:</b>
    <br><br>
    [LIEFERUNG]
    </div>
  </fieldset>
</div>
<div class="informationen">
<fieldset  style=""><legend>{|Informationen|}</legend>
[INFORMATION]
</fieldset>
</div>
<div style="clear:both;"></div>
