/**
 * Kodekit - http://timble.net/kodekit
 *
 * @copyright   Copyright (C) 2007 - 2016 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license     MPL v2.0 <https://www.mozilla.org/en-US/MPL/2.0>
 * @link        https://github.com/timble/kodekit for the canonical source repository
 */
(function($){
/**
 * Overlay class
 */
Kodekit.Overlay = Kodekit.Class.extend({
    element : null,
    /**
     * @returns {object}
     */
    getOptions: function() {
        return $.extend(true, this.supr(), {
            selector: 'body',
            ajaxify: true,
            method: 'get',
            cache: false,
            dataType: 'text',
            evalScripts: false,
            evalStyles: false,
            transport: $.ajax
        });
    },
    initialize: function(element, options) {
        var self = this;

        this.supr();

        this.element = $(element);

        this.setOptions(options).setOptions(this.element.data());

        this.options.complete = function(jqXHR) {
            var element = $('<div>'+jqXHR.responseText+'</div>'),
                scripts = element.find('script').detach(),
                styles = element.find('link[type=text\\/css],style').detach(),
                body = element.find(self.options.selector).length ? element.find(self.options.selector) : element;

            self.element.empty().append(body);

            if (self.options.evalScripts) {
                scripts.appendTo('head');
            }

            if (self.options.evalStyles) {
                styles.appendTo('head');
            }

            if (self.options.ajaxify) {
                self.element.find('a[href]').each(function(i, el){
                    var link = $(el);

                    //Avoid links with data-noasync attributes
                    if(link.data('noasync') != null) {
                        return;
                    }

                    link.on('click', function(event){
                        event.preventDefault();
                        event.stopPropagation();
                        self.send({url: this.href, data: {tmpl:''}});
                    });
                });

                self.element.find('.submittable').on('click', function(event){
                    event.preventDefault();

                    new Kodekit.Form($(event.target).data('config')).submit();
                });

                self.element.find('.-koowa-grid').each(function(i, el){
                    var grid = $(el);

                    new Kodekit.Controller.Grid({
                        form: grid,
                        ajaxify: true,
                        transport: function(url, data, method){
                            data += '&tmpl=';
                            self.send({url: url, data: data, method: method});
                        }
                    });
                });

                self.element.find('.-koowa-form').each(function(i, el){
                    var form = $(el);
                    new Kodekit.Controller.Form({
                        form: form,
                        ajaxify: true,
                        transport: function(url, data, method) {
                            data += '&tmpl=';
                            self.send({url: url, data: data, method: method});
                        }
                    });
                });
            }
        };

        this.options.transport(this.options);
    },
    send: function(options){
        options = $.extend(true, {}, this.options, options);

        this.options.transport(options);
    }
});

})(window.kQuery);