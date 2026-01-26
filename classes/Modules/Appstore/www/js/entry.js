import '../css/appstore.css';
$(document).ready(function () {
    $('body').addClass('module-appstore');

    var appstore = {
        storage: {}
    };
    appstore.search = $('#appstore-search');
    appstore.categoryPage = $('.category-page');
    appstore.categoryBlock = $('.appstore-categories');
    appstore.popularBlock = $('.popular');
    appstore.categoryBack = $('.category-page-back');
    appstore.tabs = $('span.appstore-tab');
    appstore.activePhrase = '';
    appstore.activeCategory = '';
    if($('#modulesJson').length > 0) {
        appstore.storage.apps = JSON.parse($('#modulesJson').html());
    }

    appstore.tabs.on("click touch", function () {
        var self = $(this);

        appstore.tabs.removeClass('appstore-tab-active');
        self.addClass('appstore-tab-active');

        appTypeVisibility(self.data("filter"));
    });

    appstore.categoryBack.on("click touch", function () {
        window.history.back();
    });

    if(appstore.categoryPage.length > 0){
        $('#appstore .popular').hide();
    }

    /**
     *
     * @param {String} filter
     */
    function appTypeVisibility(filter){
        var overview = $('#appstore .overview'),
            popularApps = overview.find('.popular'),
            availableApps = overview.find('.available-apps')

        if(filter === 'installed'){
            popularApps.hide();
            availableApps.hide();
        }

        if(filter === 'all'){
            popularApps.show();

            if(availableApps.children().length > 0){
                availableApps.show();
            }
        }

        if(appstore.categoryPage.length > 0){
           popularApps.hide();
        }
    }

    // Suchen beim Tippen im Suchfeld
    appstore.search.on('keyup', function () {
        appstore.activePhrase = $(this).val()
        searchAppsByPhrase(appstore.activePhrase, appstore.activeCategory, appstore.categoryBlock, appstore.popularBlock);
    });

    /**
     * Dropdown-Button
     */
    $('.dropdown').each(function () {
        var $container = $(this);
        var $link = $container.children('a.dropdown-link');
        var $sublinks = $container.find('a.dropdown-sublink');
        var $caret = $link.children('.caret');

        // Dropdown öffen/schließen
        $caret.on('click', function (e) {
            e.preventDefault();
            $container.toggleClass('open');
        });

        // Dropdown schließen, wenn Fokus verloren geht
        $link.on('focusout', function () {
            setTimeout(function () {
                $container.removeClass('open');
            }, 300);
        });

        // Bei Button-Click: Dropdown öffnen, wenn Linkziel '#' ist; ansonsten Link folgen
        $link.on('click', function (e) {
            var linkTarget = $(this).attr('href');
            if (linkTarget === '#') {
                $container.addClass('open');
                e.preventDefault();
            }
        });

        // Dropdown-Links öffnen für iOs-Geräte
        $sublinks.on('touchend', function (e) {
            e.preventDefault();

            var linkTarget = $(this).attr('href');
            if (linkTarget !== '#') {
                window.open(linkTarget);
            }

            setTimeout(function () {
                $container.removeClass('open');
            }, 300);
        });
    });

    $('a.activate').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'index.php?module=appstore&action=list&cmd=' +
                ($(this).hasClass('deactivate') ? 'deactivate' : 'activate'),
            type: 'POST',
            dataType: 'json',
            data: {
                module: $(this).data('module')
            }
        }).done(function (data) {
            if (typeof data.status != 'undefined' && data.status == 1 && typeof data.module != 'undefined') {
                var $moda = $('*').find('[data-module=\'' + data.module + '\']');
                if ($moda.length) {
                    $($moda).toggleClass('activate');
                    $($moda).toggleClass('deactivate');
                    if ($($moda).hasClass('deactivate')) {
                        $($moda).html('Deaktivieren');
                        $($moda).parents('div.dropdown').first().find('a.dropdown-link').first().toggleClass(
                            'deactivated', false);
                        $($moda).parents('div.dropdown').first().find('a.dropdown-link').first().toggleClass(
                            'activated', true);
                    } else {
                        $($moda).html('Aktivieren');
                        $($moda).parents('div.dropdown').first().find('a.dropdown-link').first().toggleClass(
                            'deactivated', true);
                        $($moda).parents('div.dropdown').first().find('a.dropdown-link').first().toggleClass(
                            'activated', false);
                    }
                }
            } else if (typeof data.error != 'undefined') {
                alert($data.error);
            }
        });
    });
    $('a.deactivate').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: 'index.php?module=appstore&action=list&cmd=' +
                ($(this).hasClass('deactivate') ? 'deactivate' : 'activate'),
            type: 'POST',
            dataType: 'json',
            data: {
                module: $(this).data('module')
            }
        }).done(function (data) {
            if (typeof data.status != 'undefined' && data.status == 1 && typeof data.module != 'undefined') {
                var $moda = $('*').find('[data-module=\'' + data.module + '\']');
                if ($moda.length) {
                    $($moda).toggleClass('activate');
                    $($moda).toggleClass('deactivate');
                    if ($($moda).hasClass('deactivate')) {
                        $($moda).html('Deaktivieren');
                        $($moda).parents('div.dropdown').first().find('a.dropdown-link').first().toggleClass(
                            'deactivated', false);
                        $($moda).parents('div.dropdown').first().find('a.dropdown-link').first().toggleClass(
                            'activated', true);
                    } else {
                        $($moda).html('Aktivieren');
                        $($moda).parents('div.dropdown').first().find('a.dropdown-link').first().toggleClass(
                            'deactivated', true);
                        $($moda).parents('div.dropdown').first().find('a.dropdown-link').first().toggleClass(
                            'activated', false);
                    }
                }
            } else if (typeof data.error != 'undefined') {
                alert($data.error);
            }
        });
    });
});

/**
 *
 * @param {String} phrase
 * @param {String} category
 * @param {jQuery} categoryBlock
 * @param {jQuery} popularBlock
 */
function searchAppsByPhrase(phrase, category, categoryBlock, popularBlock) {
    $.ajax({
        url: 'index.php?module=appstore&action=list&cmd=suche',
        type: 'POST',
        dataType: 'json',
        data: {
            val: phrase,
            category: category
        }
    })
     .done(function (data) {
         if (typeof data === 'undefined' || data === null) {
             return;
         }

         if (typeof data.ausblenden !== 'undefined' && data.ausblenden !== null) {
             $.each(data.ausblenden, function (k, v) {
                 if (k != '') {
                     $('#' + k).hide();
                 }
             });
         }
         if (typeof data.anzeigen !== 'undefined' && data.anzeigen !== null) {
             $.each(data.anzeigen, function (k, v) {
                 if (k != '') {
                     $('#' + k).show();
                 }
             });
         }
         if (typeof data.katausblenden !== 'undefined' && data.katausblenden !== null) {
             $.each(data.katausblenden, function (k, v) {
                 if (k != '') {
                     $('#' + k).hide();
                 }
             });
         }
         if (typeof data.kateinblenden !== 'undefined' && data.kateinblenden !== null) {
             $.each(data.kateinblenden, function (k, v) {
                 if (k != '') {
                     $('#' + k).show();
                 }
             });
         }
         // Meldung anzeigen wenn keine Module gefunden wurden
         if (typeof data.installiertgefunden !== 'undefined' && data.installiertgefunden !== null) {
             if (parseInt(data.installiertgefunden) === 0) {
                 $('.purchases').hide();
             } else {
                 $('.purchases').show();
             }
         }
         // "Käufe" ein-/ausblenden
         if (typeof data.kaufbargefunden !== 'undefined' && data.kaufbargefunden !== null) {
             if (parseInt(data.kaufbargefunden) === 0) {
                 $('#no-apps-found').show();
             } else {
                 $('#no-apps-found').hide();
             }
         }


         if(phrase.length > 0){
             categoryBlock.hide();
             categoryBlock.prev('h2').hide();

             popularBlock.hide();
         } else {
             categoryBlock.show();
             categoryBlock.prev('h2').show();

             popularBlock.show();
         }
     });
}
