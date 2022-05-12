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

/**
 * For bulk actions, check or uncheck all addresses.
 *
 * @type {HTMLElement}
 */
let bulkSelect = document.getElementById('bulkSelect');
if (bulkSelect) {
    bulkSelect.addEventListener("change", function( e ) {
        let checked = $('#bulkSelect').prop('checked');
        $('#snapaddy_address').find(':checkbox').prop('checked', checked);
    }, false);
}

/**
 * On click to the import button,
 * call some ajax functions.
 */
$('#snapImport').click(function () {
    let url = 'index.php?module=snapaddy&action=import';
    let importButton = $(this);
    importButton.attr('value', 'Lädt ...');
    $.ajax({
        dataType: "json",
        url: url,
    }).done(function(data) {

        location.reload();
    }).fail(function(xhr, status, error) {
        console.warn(xhr);
        console.log(status);
        console.error(error);

        let value = 'Ooops.';
        let response = JSON.parse(xhr.responseText);
        if (response.error) {
            value = response.error;
        }
        console.warn(response);

        importButton.attr('value', value);
    });
});

/**
 * Let JS detect the single endpoint url.
 * @type {HTMLElement}
 */
let field = document.getElementById('endpoint');
if (field) {
    let url = window.location.href;
    url = url.replace('=edit', '=single');
    field.value = url;
}

$('#api-test').click(function (e) {
    e.preventDefault();
    console.log('api test');
    let key = $('#api-key').val();
    let resultField = $('#api-test-result');
    if (!key) {
        resultField.html('<span class="api_fail">API Key ist leer.</span>');
        return;
    }
    let url = 'index.php?module=snapaddy&action=apicheck';
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

$('#browser-reset').click(function (e) {
    e.preventDefault();
    if(!confirm("Nach der Erneuerung des Authorization-Headers muss dieser noch einmal im snapADDY-Grabber Browser-Plugin eingetragen werden.\n\nSind Sie sicher, dass der Authorization-Header trotzdem erneuert werden soll?")) {
        return false;
    }
    console.log('reset auth header');
    let inputField = $('#browser-key');
    let oldKey = inputField.val();
    let url = 'index.php?module=snapaddy&action=newheader';
    $.post(url, {})
        .done(function(msg, status, xhr){
            if (oldKey === msg.key) {
                inputField.html('reset auth header failed: same header');
                console.error('reset auth header failed: same header');
            }
            inputField.val(msg.key);
        })
        .fail(function(xhr, status, error) {
            inputField.html('Reset schlug fehl.');
            console.error(status, error, xhr);
        });
});

