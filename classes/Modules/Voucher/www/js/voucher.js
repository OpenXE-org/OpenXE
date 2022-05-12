var vouchercode = '';
var vouchervalue = 0;
var vouchertax = 0;
var coucherArticleId = 0;
var voucherarticle = null;
var $voucherbutton = null;
var $voucherdialog = null;
var $posaddvoucher = null;
var $voucherposdialog = null;
var $voucherposfinsavesplitdialog = null;
var $voucherposfinsavedialog = null;
$(document).ready(function() {
    $voucherbutton = $('#voucherbutton');
    $voucherdialog = $('#voucherdialog');
    $posaddvoucher = $('#posaddvoucher');
    $voucherposdialog = $('#voucherposdialog');
    $voucherposfinsavesplitdialog = $('#voucherposfinsavesplitdialog');
    $voucherposfinsavedialog = $('#voucherposfinsavedialog');
    if($voucherbutton.length > 0 && $voucherdialog.length > 0) {
        $voucherbutton.on('click', function () {
            $voucherdialog.dialog('open');
        });
    }
    if($voucherposdialog.length > 0 && $posaddvoucher.length > 0)
    {
        $('#posaddvoucher + label').on('click', function () {
            $('#posvoucher').val('');
            $voucherposdialog.dialog('open');
        });
    }

    if($('#posvoucher').length > 0)
    {
        $('#posvoucher').on('keypress',function(event){

            if (event.which == 13) {
                $voucherdialog.dialog('close');
                posdialogsubmit();
            }
        });
    }
    if($voucherposdialog.length > 0)
    {
        $voucherposdialog.dialog(
            {
                modal: true,
                autoOpen: false,
                minWidth: 750,
                title:'Gutschein einlösen',
                buttons: {
                    'EINLÖSEN': function()
                    {
                        posdialogsubmit();
                        $voucherposdialog.dialog('close');

                    },
                    'ABBRECHEN': function() {
                        $voucherposdialog.dialog('close');
                    }
                },
                close: function(event, ui){

                }
            });
    }
    if($voucherdialog.length > 0)
    {
        $voucherdialog.dialog(
        {
            modal: true,
            autoOpen: false,
            minWidth: 1080,
            title:'Gutschein',
            buttons: {
                'ABBRECHEN': function() {
                    $voucherdialog.dialog('close');
                }
            },
            close: function(event, ui){

            }
        });
    }
    if($voucherposfinsavesplitdialog.length > 0)
    {
        $voucherposfinsavesplitdialog.dialog(
        {
            modal: true,
            autoOpen: false,
            minWidth: 750,
            title:'Gutschein einlösen',
            buttons: {
                'HINZUFÜGEN': function()
                {
                    addarticle(voucherarticle);
                    $voucherposfinsavesplitdialog.dialog('close');
                    finsale_click();
                    vouchercode = '';
                    voucherarticle = null;
                },
                'OHNE GUTSCHEIN FORTSETZEN': function() {
                    $voucherposfinsavesplitdialog.dialog('close');
                    finsale_click();
                    vouchercode = '';
                    voucherarticle = null;
                }
            },
            close: function(event, ui){

            }
        });
    }
    if($voucherposfinsavedialog.length > 0)
    {
        $voucherposfinsavedialog.dialog(
        {
            modal: true,
            autoOpen: false,
            minWidth: 750,
            title:'Gutschein einlösen',
            buttons: {
                'HINZUFÜGEN': function()
                {
                    addarticle(voucherarticle);
                    finsale_click();
                    vouchercode = '';
                    voucherarticle = null;
                    $voucherposfinsavedialog.dialog('close');
                },
                'OHNE GUTSCHEIN FORTSETZEN': function() {
                    finsale_click();
                    vouchercode = '';
                    voucherarticle = null;
                    $voucherposfinsavedialog.dialog('close');
                }
            },
            close: function(event, ui){

            }
        });
    }

});

function hasAddedVoucherToPos()
{
    var positions = getPositions();
    if(positions.length === 0)
    {
        return false;
    }
    $.each(positions,function(posIndex, position){
        if(position['id'] == coucherArticleId)
        {
            return true;
        }
    });
    return false;
}

function openAskPosFinSaledialog(checkVoucherCodeData)
{
    voucherarticle = checkVoucherCodeData.add;
    if(checkVoucherCodeData.alluseable == 1)
    {
        $('#voucherposfinsavesPossible').html(checkVoucherCodeData.useable+' '+checkVoucherCodeData.currency);
        $voucherposfinsavedialog.dialog('open');
    }else{
        $('#voucherposfinsavesplitPossible').html(checkVoucherCodeData.useable+' '+checkVoucherCodeData.currency);
        $('#voucherposfinsavesplitOrig').html(checkVoucherCodeData.voucher_residual_value+' '+checkVoucherCodeData.currency);
        $voucherposfinsavesplitdialog.dialog('open');
    }
}

function voucherfinsale_click()
{
    if(vouchercode != '' && !hasAddedVoucherToPos())
    {
        $.ajax({
            url: 'index.php?module=voucher&action=pos&cmd=checkvouchercode',
            type: 'POST',
            dataType: 'json',
            data: {voucher_code:vouchercode, positions: JSON.stringify(getPositions()), gross:brutto},
            success: function(data) {
                if(typeof data.status != 'undefined') {
                    if(data.status == 1)
                    {
                        if(typeof data.add != 'undefined' && typeof data.add.id != 'undefined')
                        {
                            openAskPosFinSaledialog(data);
                            return;
                        }
                    }
                }
                finsale_click();
                vouchercode = '';
                voucherarticle = null;
            }
        });
    }else {
        finsale_click();
        vouchercode = '';
        voucherarticle = null;
    }
}

function posdialogsubmit()
{
    var voucher = $('#posvoucher').val();
    if(voucher+'' !== '')
    {
        vouchercode = '';
        $.ajax({
            url: 'index.php?module=voucher&action=pos&cmd=checkvouchercode',
            type: 'POST',
            dataType: 'json',
            data: {voucher_code:voucher, positions: JSON.stringify(getPositions()), gross:brutto},
            success: function(data) {
                if(typeof data.status != 'undefined') {
                    if(data.status == 1)
                    {
                        vouchercode = data.voucher_code;
                        coucherArticleId = data.article_id;
                        var $finsalebutton = $('#finsale');
                        $finsalebutton.off('click');
                        $finsalebutton.click(function() {
                            voucherfinsale_click();
                        });
                        vouchertax = data.tax_name;
                        vouchervalue = data.voucher_residual_value;
                        if(typeof data.alluseable != 'undefined' && data.alluseable == 1 &&
                            typeof data.add != 'undefined' )
                        {
                            if(typeof data.add.id != 'undefined') {
                                addarticle(data.add);
                            }
                            else {
                                alert('Der Gutschein hat keine Artikelzuordnung');
                            }
                        }
                    }else {
                        if (typeof data.error != 'undefined') {
                            alert(data.error);
                        } else {
                            window.console.log(data);
                            alert('Es ist ein Fehler aufgetreten');
                        }
                    }
                }else{
                    alert('Es ist ein Fehler aufgetreten');
                }
            }
        });
    }
}

function dialogsubmit()
{
    var voucher = $('#voucher').val();
    var doctypeid = $('#voucher_doctypeid').val();
    var doctype = $('#voucher_doctype').val();
    $('#voucherpopupcontent').html('');
    if(voucher+'' !== '')
    {
        vouchercode = '';
        $.ajax({
            url: 'index.php?module=voucher&action=order&cmd=checkvouchercode',
            type: 'POST',
            dataType: 'json',
            data: {voucher_code:voucher, orderId: doctypeid},
            success: function(data) {
                if(typeof data.status != 'undefined') {
                    if(data.status == 1)
                    {
                        if(typeof data.reload != 'undefined' && typeof data.reload == 1)
                        {
                            window.location.url = 'index.php?module='+doctype+'&action=positionen&id='+doctypeid;
                            return;
                        }else{
                            $('#voucherpopupcontent').html(data.html);
                            $('#voucher_code').val(voucher);
                        }
                    }else if(typeof data.error != 'undefined'){
                        alert(data.error);
                    }else{
                        alert('Es ist ein Fehler aufgetreten');
                    }
                }else{
                    alert('Es ist ein Fehler aufgetreten');
                }
            }
        });
    }
}
