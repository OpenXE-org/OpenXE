<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tab1">Import</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->

<div id="tabs1">
[IMPORT]
    <form method="POST"><input type="submit" name="deletedouble" value="{|bereits importierte Auftrage l&ouml;schen|}" /></form>
</div>

<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->
<script type="application/javascript">
    $(document).on('ready',function(){
       $('#submit').on('click',function(){
          $('#tabs').loadingOverlay('show');
       });
    });
</script>