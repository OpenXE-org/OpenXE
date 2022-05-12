/**
 * On mouseover change the password input to text and change back
 *
 * @type {HTMLElement}
 */
let snapForm = document.getElementById('tabs');
if (snapForm) {
    /*
     * Find all input fields in 'tabs' div.
     */
    let inputs = snapForm.getElementsByTagName('input');
    for (let i=0; i<inputs.length; i++) {
        if (inputs[i].type.toLowerCase() !== "password") {
            continue;
        }
        /*
         * On mouseenter, change the type from password to text
         */
        inputs[i].addEventListener("mouseenter", function( event ) {
            event.target.type = 'text';
        }, false);
        /*
         * On mouseout, change the type from text to password
         */
        inputs[i].addEventListener("mouseout", function( event ) {
            event.target.type = 'password';
        }, false);
    }
}

$('#sipgate_webhook').val(
    window.location.href
          .replace('=sipgate', '=callcenter&provider=sipgate')
          .replace('=edit', '=call')
);


$('#api-test').click(function (e) {
    e.preventDefault();
    console.log('api test');
    let key = $('#api-key').val();
    let resultField = $('#api-test-result');
    if (!key) {
        resultField.html('<span class="api_fail">API Key ist leer.</span>');
        return;
    }
    let url = 'index.php?module=sipgate&action=apicheck';
    resultField
        .attr('class', '')
        .html('Lädt ...');

    $.post(url, {key: key})
        .done(function(msg, status, xhr){
            console.log(msg, status, xhr);
            console.warn(msg.key);
            console.warn(msg.class);
            if (msg.key && msg.class) {
                resultField.append('<span class="' + + '">' + msg.key + '</span>');
                resultField
                    .attr('class', msg.class)
                    .html(msg.key);
            } else {
                resultField
                    .attr('class', 'api_fail')
                    .html('Server Fehler');
            }
        })
        .fail(function(xhr, status, error) {
            resultField
                .attr('class', 'api_fail')
                .html('Der Test schlug fehl.');
            console.error(status, error, xhr);
        });
});


function call(id, dummy)
{
    $.ajax({
        url: 'index.php?module=sipgate&action=call&id='+id,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
            if(data)
            {

            }
        }
    });
}
