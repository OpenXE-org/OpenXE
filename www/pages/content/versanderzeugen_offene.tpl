<div id="tabs">
  <ul>
    <li><a href="#tabs-1">{|offene Lieferungen|}</a></li>
    <li><a href="#tabs-2">{|versendete Lieferungen|}</a></li>
    <li><a href="#tabs-3">{|Kommissionierl&auml;ufe|}</a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
  <div id="tabs-1" data-focus="[ELEMENTFOCUS]" data-settab="[SETURL]">

    <div class="filter-box filter-usersave">
      <div class="filter-block filter-inline">
        <div class="filter-title">{|Filter|}</div>
        <ul class="filter-list">
          <li class="filter-item">
            <label for="versandoffene_fastlane" class="switch">
              <input type="checkbox" id="versandoffene_fastlane" />
              <span class="slider round"></span>
            </label>
            <label for="versandoffene_fastlane">{|nur Fast-Lane|}<span id="count_fastlane"></span></label>
          </li>
          <li class="filter-item">
            <label for="ohneklaerfaelle" class="switch">
              <input type="checkbox" id="ohneklaerfaelle" />
              <span class="slider round"></span>
            </label>
            <label for="ohneklaerfaelle">ohne Kl&auml;rf&auml;lle<span id="count_noneproblemcases"></span></label>
          </li>
          <li class="filter-item">
            <label for="klaerfall" class="switch">
              <input type="checkbox" id="klaerfall" />
              <span class="slider round"></span>
            </label>
            <label for="klaerfall">nur Kl&auml;rf&auml;lle<span id="count_problemcases" class="red"></span></label>
          </li>
        </ul>
      </div>
    </div>

    <br />
    <br />
    <center>
      <label for="lieferschein">{|Lieferschein / Kiste scannen|}:</label>
      <form action="" method="post">
        <input type="text" id="lieferschein" name="lieferschein" size="20">
      </form>&nbsp;
      <label for="artikel">{|Artikel scannen|}:</label>&nbsp;
      <form action="" method="post">
        <input type="text" id="artikel" name="artikel" value="[ARTIKEL]" size="20">
      </form>
      [VERSANDERZEUGEN_OFFENE_HOOK_SCAN]
    </center>
    <br>
    [MESSAGE]
    <table class="hidenot480">
      <tr>
        <td><label for="kundeoffene">{|Kunde anzeigen|}</label></td>
        <td><input type="checkbox" id="kundeoffene" /></td>
      </tr>
    </table>
    [TAB1]
    [MESSAGEENDE]
  </div>

  <div id="tabs-2">
    <table class="hidenot480">
      <tr>
        <td><label for="kundefertig">{|Kunde anzeigen|}</label></td>
        <td><input type="checkbox" id="kundefertig" /></td>
      </tr>
    </table>
    [TAB2]
  </div>

  <div id="tabs-3">
    [MESSAGE]
    [TAB3]
    <label for="kommdrucker">{|Drucker|}:</label> <select name="kommdrucker" id="kommdrucker">[SELKOMMDRUCKER]</select>
  </div>
<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->


<script type="text/javascript">
  var getInfoBoxesTimeout = null;

  function getInfoBoxes()
  {
    if(getInfoBoxesTimeout)
    {
      clearTimeout(getInfoBoxesTimeout);
    }
    getInfoBoxesTimeout = setTimeout(function(){getInfoBoxes()},60000);
    $.ajax({
      url: 'index.php?module=versanderzeugen&action=offene&cmd=getinfoboxes',
      type: 'POST',
      dataType: 'json',
      data: { },
      success: function(data) {
        if(data.count_fastlane > 0) {
          $('#count_fastlane').html(' (' + data.count_fastlane + ')');
        }else{
          $('#count_fastlane').html('');
        }
        if(data.count_problemcases > 0) {
          $('#count_problemcases').html(' (' + data.count_problemcases + ')');
        }else{
          $('#count_problemcases').html('');
        }
        $('#count_noneproblemcases').html(' ('+data.count_noneproblemcases+')');
      }
    });

  }

  $(document).ready(function() {

    $('#'+$('#tabs-1').data('focus')).focus();
    if($('#tabs-1').data('settab')) {
      $('a[href="#'+$('#tabs-1').data('settab')+'"]').trigger('click');
    }
    $('#kundeoffene').on('change',function(){
      var wert = $(this).prop('checked');
      $('#versandoffene > thead > tr > th:nth-child(4)').toggleClass('showtd', wert);
      $('#versandoffene > tfoot > tr > th:nth-child(4)').toggleClass('showtd', wert);
      $('#versandoffene > tbody > tr > td:nth-child(4)').toggleClass('showtd', wert);
    });

    $('#kundefertig').on('change',function(){
      var wert = $(this).prop('checked');
      $('#versandfertig > thead > tr > th:nth-child(4)').toggleClass('showtd', wert);
      $('#versandfertig > tfoot > tr > th:nth-child(4)').toggleClass('showtd', wert);
      $('#versandfertig > tbody > tr > td:nth-child(4)').toggleClass('showtd', wert);
    });


    $('#versandoffene').on('afterreload',function(){
      getInfoBoxes();
    });
  });

  function kommissionierungdrucken(kommissionierung, dummy)
  {
    if($('#kommdrucker').val() > 0)
    {
      $.ajax({
          url: 'index.php?module=versanderzeugen&action=offene&cmd=kommissionierungdrucken',
          type: 'POST',
          dataType: 'json',
          data: {id:kommissionierung, drucker:$('#kommdrucker').val() },
          success: function(data) {
            if(typeof data.status != 'undefined' && data.status == 1)
            {
              window.location.href='index.php?module=versanderzeugen&action=offene'
            }
          },
          beforeSend: function() {

          }
      });
    }else {
      alert('{|Bitte einen Drucker wählen|}');
    }
  }
</script>

<style>
  div.filter-item span{
    font-weight: bold;
  }
  div.filter-item span.red{
    color:red;
  }
@media screen and (max-width: 767px){
  table#versandoffene  > tbody > tr > td,
  table#versandoffene th
  {
  max-width:25px!important;
  overflow:hidden;
  }
  table#versandoffene > tbody > tr > td:first-child >  + td +td +td,
  table#versandoffene th:first-child+ th+th+th
  {
  max-width:3px!important;
  overflow:hidden;
  }
  
  table#versandoffene > tbody > tr > td:last-child,
  table#versandoffene th:last-child
  {
  max-width:35px!important;
  }
  table#versandoffene > tbody > tr > td:first-child +td +td ,
  table#versandoffene th:first-child + th + th
  {
    display:none;
  }
  table#versandoffene > tbody > tr > td:first-child +td +td +td  +td+td,
  table#versandoffene th:first-child + th + th + th +th+th
  {  
  display:none;
  }
  table#versandfertig > tbody > tr> td:first-child +td +td +td  ,
  table#versandfertig th:first-child + th + th + th
  {  
  display:none;
  }
  table#versandfertig > tbody > tr> td:first-child ,
  table#versandfertig th:first-child
  {  
  display:none;
  }
  table#versandoffene > tbody > tr> td:first-child + td,
  table#versandoffene th:first-child  +th
  {  
  display:none;
  }
}

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

  table#versandoffene > tbody > tr > td:first-child +td +td +td +td +td,
  table#versandoffene th:first-child + th + th + th +th+th
  {  
  display:none;
  }
  table#versandoffene > tbody > tr > td:first-child +td +td +td +td +td+td,
  table#versandoffene th:first-child + th + th + th +th+th+th
  {
    display:none;
  }

  table#versandfertig > tbody > tr >  td:first-child +td +td +td +td ,
  table#versandfertig th:first-child + th + th + th +th
  {  
  display:none;
  }
  table#versandfertig > tbody > tr >  td:first-child +td +td+td  ,
  table#versandfertig th:first-child + th + th +th
  {  
  display:none;
  }

  table#versandfertig > tbody > tr >  td:first-child +td +td+td+td+td  ,
  table#versandfertig th:first-child + th + th +th+th+th
  {  
  display:none;
  }
  
  table#versandoffene > tbody > tr > td:first-child + td + td + td+ td + td +td,
  table#versandoffene th:first-child +th+th+th +th+th+th
  {  
  display:none;
  }

  table#versandoffene > tbody > tr > td:first-child + td + td ,
  table#versandoffene th:first-child +th+th
  {
    display:none;
  }

  table#versandoffene td  img {
    width:25px !important;
  }
  #versandoffene > tfoot > tr > th:nth-child(4)
  {
    min-width:35px;
  }
  #versandfertig > tfoot > tr > th:nth-child(3)
  {
    min-width:35px;
  }  
  table#versandfertig td  img {
  width:25px !important;
  }
}

  @media screen and (max-width: 320px){
    table#versandoffene > tbody > tr > td:first-child + td + td +td,
    table#versandoffene th:first-child +th+th+th
    {
      display:none;
    }
  }
</style>
