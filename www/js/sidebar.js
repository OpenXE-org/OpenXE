var Sidebar = function ($) {
    "use strict";

    var me = {

        settings: {
            sidebar: {
                toggleClass: 'collapsed'
            },
            listItem: {
                toggleClass: 'active'
            },
            subMenu: {
                position: {
                    my: "left top",
                    at: "right top",
                    collision: 'fit'
                }
            }
        },

        selector: {
            sidebar: '#sidebar',
            sidebarTrigger: '.sidebar-toggle',
            listItems: '.list-item',
            subMenu: '.sidebar-submenu div'
        },

        storage: {
            isTouch: 'ontouchstart' in window,
            wizardMinimized: false,
            collapseState: null
        },

        elem: {
            $listItems: null
        },

        init: function () {
            me.elem.$listItems = $(me.selector.listItems);

            me.attachEvents();
        },

        attachEvents: function () {

            // opens/ closes sidebar
            $(me.selector.sidebarTrigger).on('click touch', function () {
                me.toggleSidebar()
            });

            if (me.storage.isTouch) {
                registerClick();
            } else {
                // mouseenter on non touch device
                me.elem.$listItems.on('mouseenter', function () {
                    var self = $(this);

                    if(self.hasClass("wizard-highlight")){
                        registerClick();
                        return;
                    }

                    me.highlightListItem(self);
                });

                // mouseleave on non touch device
                me.elem.$listItems.on('mouseleave', function () {
                    var self = $(this);

                    if(self.hasClass("wizard-highlight")){
                        return;
                    }
                    me.resetHighlightListItem();
                });
            }

            function registerClick() {
                // click/ touch event on touch device
                me.elem.$listItems.on('click touch', function () {
                    var self = $(this);

                    me.highlightListItem(self);
                });
            }
        },

        toggleSidebar: function () {
            $(me.selector.sidebar).toggleClass(me.settings.sidebar.toggleClass);

            $(me.selector.sidebar).hasClass(me.settings.sidebar.toggleClass)
                ? me.storage.collapseState = 'true'
                : me.storage.collapseState = 'false'

            me.postSidebarState();
        },

        highlightListItem: function (item) {
            var submenu = item.find(me.selector.subMenu);

            me.resetHighlightListItem();

            item.toggleClass(me.settings.listItem.toggleClass);

            if (submenu.length > 0) {
                submenu.position({
                    my: me.settings.subMenu.position.my,
                    at: me.settings.subMenu.position.at,
                    of: item,
                    collision: me.settings.subMenu.position.collision
                });

                // case submenu items would be out of screen because of screen height
                if (submenu.outerHeight() > window.innerHeight) {
                    submenu.css({
                        'height': window.innerHeight,
                        'overflow': 'scroll'
                    });
                }
            }
        },

        resetHighlightListItem: function () {
            me.elem.$listItems.removeClass(me.settings.listItem.toggleClass);
        },

        postSidebarState: function () {
            if(me.storage.collapseState === null){
                return
            }

            $.ajax({
                url: 'index.php?module=ajax&action=sidebar&cmd=set_collapsed&value=' + me.storage.collapseState,
                method: 'post',
                dataType: 'json',
                success: function (data) {

                }
            });
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    Sidebar.init();
});