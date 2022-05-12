function CounterComponent(self) {
    'use strict';
    var me = {
        storage: {
            maximum: null
        },
        elem: {
            $self: null,
            $sub: null,
            $plus: null,
            $counter: null
        },
        init: function (self) {
            me.elem.$self = self;
            me.elem.$sub = self.find('.sub-button');
            me.elem.$plus = self.find('.plus-button');
            me.elem.$counter = self.find('input');
            me.storage.maximum = me.elem.$self.data('max');

            if (me.elem.$sub.length === 0 ||
                me.elem.$plus.length === 0 ||
                me.elem.$counter.length === 0) {
                return;
            }

            me.registerEvents();
        },

        registerEvents: function () {
            me.elem.$sub.on('click touch', function () {
                if(me.elem.$counter[0].value > 1){
                    me.elem.$counter[0].value--;

                    me.elem.$counter.trigger('change');
                }
            });

            me.elem.$plus.on('click touch', function () {
                if(me.storage.maximum !== undefined && me.storage.maximum !== null){
                    if(me.elem.$counter[0].value < me.storage.maximum){
                        me.elem.$counter[0].value++;
                    }
                } else {
                    me.elem.$counter[0].value++;
                }
                
                me.elem.$counter.trigger('change');
            });
        }

    }

    me.init(self)
}

$(document).ready(function () {
    $('.counter-component').each(function () {
        new CounterComponent($(this));
    })
});