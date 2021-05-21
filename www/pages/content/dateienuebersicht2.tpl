<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-md-8 col-md-height">
      <div class="inside inside-full-height">
        <fieldset>
          <legend>{|Dateien|}</legend>
[PRETAB1]
[TAB1]
<script>
function chauswahl()
{
  var auswahl = '';
  $('#dateien_popup_[TYP]').find('input:checked').each(function(){
    var aid = this.id.split('_');
    if(auswahl != '')auswahl = auswahl + ',';
    auswahl = auswahl + aid[ 1 ];
  });
  $('#auswahl').val(auswahl);
}
</script>

[SCRIPT]

        </fieldset>
      </div>
    </div>
    <div class="col-xs-12 col-md-4 col-md-height">
      <div class="inside inside-full-height">
        <fieldset>
          <legend>{|Hochladen|}</legend>
[TAB2]
        </fieldset>
      </div>
    </div>
  </div>
</div>
<style>
@media only screen and (min-width: 651px){
  .table-responsive {
    max-width:100%;
    /*position: absolute;*/
  }
}
  @media only screen and (min-width: 992px){
  .table-responsive {
    /*06.02.19 rausgenommen, da fieldset fuer stapelverarbeitung ueber livetabelle lag (LG)*/
    /*position: absolute;*/
    }
</style>