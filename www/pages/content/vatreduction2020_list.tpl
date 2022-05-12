<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
    <div class="info">
        <input type="checkbox" value="1" id="vatreduction2020_active" name="vatreduction2020_active" [ISACTIVE] />
        <label for="vatreduction2020_active">
            {|Aufträge im Autoversand nicht automatisch freigeben.|}
        </label>
        <label for="vatreduction2020_date">{|mit dem Datum vor|}:</label>
        <input type="text" value="[VATREDUCTION2020_DATE]" id="vatreduction2020_date" name="vatreduction2020_date" />

    </div>
    [MESSAGE]
    [TAB1]
    [TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>
<script type="application/javascript">
    $('#frmsubmit').on('submit', function(event){
        if(!confirm('Sind Sie sich sicher?')) {
            event.preventDefault();
            return false;
        }

        return true;
    });
</script>
