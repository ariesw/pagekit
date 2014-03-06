define(['jquery', 'tmpl!link.modal,link.replace', 'uikit', 'link'], function($, tmpl, UI, Link) {

    var modal  = $(tmpl.get('link.modal')).appendTo('body'),
        picker = new UI.modal.Modal(modal),
        url    = modal.find('.js-link-url'),
        title  = modal.find('.js-title'),
        link   = new Link(),
        handler;

    modal.on('click', '.js-update', function () {
        handler();
    });

    return function(markdownarea) {

        markdownarea.addPlugin('urls', /(?:\[([^\n\]]*)\])(?:\(([^\n\]]*)\))?/gim, function (marker) {

            if(marker.found[4] && marker.found[4].indexOf("!"+marker.found[0])!=-1) {
                return marker.found[0];
            }

            marker.area.preview.on('click', '#' + marker.uid, function (e) {

                e.preventDefault();

                handler = function () {
                    picker.hide();
                    marker.replace('[' + title.val() + '](' + url.val() + ')');
                };

                url.val(marker.found[2]);
                title.val(marker.found[1]);
                picker.show();
                setTimeout(function() { title.focus(); }, 10);
                link.init();
            });

            return tmpl.render('link.replace', { marker: marker, link: marker.found[2].trim() ? marker.found[2] : null, txt:  marker.found[1].trim() ? marker.found[1] : null}).replace(/(\r\n|\n|\r)/gm, '');
        });

    };
});