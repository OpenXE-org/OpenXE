<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">[TAB1TEXT]</a></li>
    <li><a href="#tabs-2">[TAB2TEXT]</a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
  <div id="tabs-1">
    [TAB1START]
    [MESSAGE1]
    [TAB1]
    [TAB1ENDE]
  </div>

  <div id="tabs-2">
    [TAB2START]
    [MESSAGE2]
    [TAB2]
    [TAB2ENDE]
  </div>

</div>

<script type="text/javascript">
  $(document).ready(function() {
    $( "#tabs" ).tabs( "option", "active", [TABINDEX]);
  });
</script>