<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
</script>
[MESSAGE]
<div>
  <fieldset class="white">
  [TAB1]
  [TAB1NEXT]
  </fieldset>
</div>
</div>

<!-- tab view schließen -->
</div>

<script>
  function gotovorgang(artnummer){
    var vorgang = (artnummer.parentElement.firstChild.innerHTML).split("-");

      $.ajax({
        url: 'index.php?module=projektlogbuch&action=projektlogbuch&cmd=goto&art='+vorgang[0]+'&id='+vorgang[1],
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          if(data.success == 1){
            window.open(data.data);
          }else{
            alert(data.data);
          }
        },
        beforeSend: function() {

        }
      });

  }

</script>
