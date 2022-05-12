
<input type="checkbox" id ="chckHead2"/>

<script type="text/javascript">
    $('.chcktbl2').click(function () {
        var length = $('.chcktbl2:checked').length;      
        if (length > 3) {
            alert(length);
            $('.chcktbl2:not(:checked)').attr('disabled', true);
        }
        else {
            $('.chcktbl2:not(:checked)').attr('disabled', false);
        }
    });
</script>
<script type="text/javascript">
    $('#chckHead2').click(function () {
        if (this.checked == false) {
            $('.chcktbl2:checked').attr('checked', false);
        }
        else {
            $('.chcktbl2:not(:checked)').attr('checked', true);
        }
    });
    $('#chckHead2').click(function () {
    });
</script>
