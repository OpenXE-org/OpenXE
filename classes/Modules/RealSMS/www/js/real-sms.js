/**
 * On mouseover change the password input to text and change back
 *
 * @type {HTMLElement}
 */
let realSMSForm = document.getElementById('tabs');
if (realSMSForm) {
    /*
     * Find all input fields in 'tabs' div.
     */
    let inputs = realSMSForm.getElementsByTagName('input');
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

/**
 * Check the api user
 */
$('#api-test').click(function (e) {
    e.preventDefault();
    console.log('api test');
    let key = $('#api-key').val();
    let user = $('#api-username').val();
    let resultField = $('#api-test-result');

    if (!user) {
        resultField
            .attr('class', 'api_fail')
            .html('Der API-Username ist leer.');
        return;
    }
    if (!key) {
        resultField
            .attr('class', 'api_fail')
            .html('Der API-Key ist leer.');
        return;
    }

    let url = 'index.php?module=realsms&action=apicheck';
    resultField
        .attr('class', '')
        .html('Lädt ...');

    $.post(url, {user: user, key: key})
        .done(function(msg, status, xhr){
            console.log(msg, status, xhr);
            console.warn(msg.message);
            console.warn(msg.class);
            if (msg.message && msg.class) {
                resultField
                    .attr('class', msg.class)
                    .html(msg.message);
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
