
<input type="checkbox" id ="chckHead" checked/>

<script type="text/javascript">

    $('#chckHead').attr('checked', false);

    $('.chcktbl').click(function () {
        var length = $('.chcktbl:checked').length;      
        if (length > 3) {
            $('.chcktbl:not(:checked)').attr('disabled', true);
        }
        else {
            $('.chcktbl:not(:checked)').attr('disabled', false);
        }
    });
</script>
<script type="text/javascript">
    $('#chckHead').click(function () {
        if (this.checked == false) {
            $( ".chcktbl").prop( "checked", false );
        }
        else {
            $( ".chcktbl").prop( "checked", true );
        }
    });
</script>
