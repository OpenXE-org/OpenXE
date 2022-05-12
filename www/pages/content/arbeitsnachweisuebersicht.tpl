<script type="text/javascript">
$(document).ready(function() {
    $('#selecctall').click(function(event) {  //on click
        if(this.checked) { // check select status
            $('.checkall').each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"              
            });
        }else{
            $('.checkall').each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"                      
            });        
        }
    });
   
});
</script>


<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Arbeitsnachweise|}</a></li>
        <li><a href="#tabs-2">{|nicht versendete Arbeitsnachweise|}</a></li>
        <li><a href="#tabs-3">{|in Bearbeitung|}</a></li>
    </ul>
<div id="tabs-1">

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="arbeitsnachweisoffen" class="switch">
            <input type="checkbox" id="arbeitsnachweisoffen">
            <span class="slider round"></span>
          </label>
          <label for="arbeitsnachweisoffen">{|Alle nicht versendeten Arbeitsnachweise|}</label>
        </li>
        <li class="filter-item">
          <label for="arbeitsnachweisheute" class="switch">
            <input type="checkbox" id="arbeitsnachweisheute">
            <span class="slider round"></span>
          </label>
          <label for="arbeitsnachweisheute">{|Alle Arbeitsnachweise von heute|}</label>
        </li>
    </div>
  </div>

<form action="index.php?module=arbeitsnachweis&action=abrechnung&back=[BACK]&id=[ID]" method="post">
[MESSAGE]
[TAB1]
</form>
</div>

<div id="tabs-2">
[TAB2]
</div>

<div id="tabs-3">
[TAB3]
</div>



</div>


