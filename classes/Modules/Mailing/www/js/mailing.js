$(document).ready(function () {
    var $form = $('#mailing-form');
    var $submitButton = $form.find('[type="submit"]');
    if ($submitButton.val() === 'Senden') {
        $form.submit(function () {
            $('body').loadingOverlay();
        });
    }
});
