<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
        <li><a href="#tabs-2">[TABTEXT2]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
[TAB1NEXT]
</div>


<!-- erstes tab -->
<div id="tabs-2">
[MESSAGE]
[TAB2]
[TAB2NEXT]
</div>

<!-- tab view schließen -->
</div>

<style type="text/css">
    @media only screen and (max-width: 599px) and (min-width: 350px) {
        table#multiorderpicking_list td,
        table#multiorderpicking_fertig td,
        div#multiorderpicking_list_filter {
            font-size :120%;
            font-weight: bold;
        }
        div#multiorderpicking_list_wrapper > div.dt-buttons {
            display: none;
        }
    }

    @media only screen and (max-width: 1000px) and (min-width: 600px) {
        table#multiorderpicking_list td,
        table#multiorderpicking_fertig td,
        div#multiorderpicking_list_filter {
            font-size :150%;
            font-weight: bold;
        }

        div#multiorderpicking_list_filter {
            margin:5px;
        }

        table#multiorderpicking_list td img,
        table#multiorderpicking_fertig img{
            width: 30px;
            height:30px;
        }
        div#multiorderpicking_list_wrapper > div.dt-buttons {
            display: none;
        }
    }
</style>