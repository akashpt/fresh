(function ($) {
    'use strict';

    if (! $ || ! $.event) {
        return;
    }

    var passiveOptions = false;

    try {
        var opts = Object.defineProperty({}, 'passive', {
            get: function () {
                passiveOptions = { passive: true };
                return true;
            }
        });

        window.addEventListener('freshPassiveTest', null, opts);
        window.removeEventListener('freshPassiveTest', null, opts);
    } catch (error) {
        passiveOptions = false;
    }

    if (! passiveOptions) {
        return;
    }

    $.event.special.touchstart = {
        setup: function (data, namespaces, eventHandle) {
            this.addEventListener('touchstart', eventHandle, passiveOptions);
        }
    };

    $.event.special.touchmove = {
        setup: function (data, namespaces, eventHandle) {
            this.addEventListener('touchmove', eventHandle, passiveOptions);
        }
    };
}(jQuery));
