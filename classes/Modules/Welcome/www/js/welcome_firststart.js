$.ajax({
    url: 'index.php?module=welcome&action=settings&cmd=startclickbyclick',
    type: 'POST',
    dataType: 'json',
    data: {

    }
}).done( function(data) {
    new Vue({
        el: '#welcome-firststart',
        data: {
            showAssistant: true,
            pagination: true,
            allowClose: false,
            pages: data.pages
        }
    });
});
