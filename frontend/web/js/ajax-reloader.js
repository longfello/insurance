(function ($) {
    var __reloader = function () {
        this.requests = [];
        this.validatetm = false;
        this.init = function () {
            self.initEvents();
        };
        this.initEvents = function () {
            //console.log('validate');
            $(document).on('submit', 'form.ajax-reloader', self.handlers.form.submit);
        };
        this.sendForm = function (form) {
            var target = $(form).data('target');
            target = target ? target : '.content-wrapper';
            var source = $(form).data('source');
            source = source ? source : '.content-wrapper';
            //self.showLoader(target);
            if (self.requests[form.attr('id')] !== undefined && self.requests[form.attr('id')]) {
                self.requests[form.attr('id')].abort();
            }
            if (typeof price_request !== 'undefined' && price_request.length) {
                $.each(price_request, function (api_id, xhr) {
                    if (xhr !== undefined) xhr.abort();
                });
            }

            self.requests[form.attr('id')] = $.ajax({
                url: form.attr('action'),
                type: 'post',
                data: form.serialize(),
                success: function (data) {
                    delete self.requests[form.attr('id')];

                    //          $(target).empty().append($(data).filter(source).html());
                    $(target).empty().append(data);
                    self.hideLoader();

                    reInit();

                    $(".form-group").find(".js-select").select2({
                        minimumResultsForSearch: Infinity
                    });

                    if ($('.current-filters__num').length) {
                        $('.current-filters__num').html(filtersLiteral($('.checkbox__input:checked').length));
                    }

                    /*------------- fixed results ---------------*/
                    var windowWidth = $(window).width();
                    console.log(windowWidth);


                    if (windowWidth > 1020) {

                        if ($('.company-list_previews').length > 0) {
                            var resultsHeight = $('.company-list__item_wrapper').height();

                            /*if(resultsHeight < $(window).height()) {
                                $(".page__left").css({'position': 'fixed', 'top' : '113px'});
                                console.log('very small');
                            } else {
                                $(".page__left").css({'position': 'absolute', 'top' : '113px'});
                                console.log('very big');
                            }*/
                        }

                        if ($('.company-detail').length > 0) {
                            var resultsHeight = $('.company-list__item_wrapper').outerHeight();

                            /*if(resultsHeight < $(window).height()) {
                                $(".page__left").css({'position': 'fixed'});
                            } else {
                                $(".page__left").css({'position': 'absolute'});
                            }*/
                        }

                    } else if (windowWidth < 501) {

                        $(".header").addClass("header-small");
                        /*$(".page__left").css({"top" : "55px"});*/

                    }

                },
                error: function (jqXHR) {
                    delete self.requests[form.attr('id')];
                    if (jqXHR.responseText) alert(jqXHR.responseText);
                }
            });
        };
        this.hideLoader = function () {
            $('body').removeClass("no-overflow");
            $('.loader-wrapper').remove();
        };
        this.showLoader = function (target) {
            if ($(target).find('.loader-wrapper').length == 0) {
                $(target).css({
                    'position': 'relative',
                    'min-height': '100vh'
                }).append('<div class="loader-wrapper"><img src="/img/bullo-loading.gif"></div>');
                if ($('.js-scroll-column.page__left .company-list__item').length == 0) {
                    $(".js-scroll-column.page__left").css({'top': '0px'});
                }
                $('body').addClass("no-overflow");
            }
        };

        this.handlers = {
            form: {
                submit: function (event, jqXHR, settings) {
                    event.preventDefault();
                    var form = $(this);

                    var target = $(form).data('target');
                    target = target ? target : '.content-wrapper';
                    self.showLoader(target);

                    $('.form-group', form).removeClass('has-error');
                    $('.help-block', form).empty();
                    if ($('.form_type', form).length) {
                        if (self.validatetm) clearTimeout(self.validatetm);
                        self.validatetm = setTimeout(function () {
                            var form_type = $('.form_type', form).val();
                            $.ajax({
                                url: '/validate-form.html',
                                type: 'post',
                                dataType: 'json',
                                data: form.serialize(),
                                success: function (data) {
                                    if (data.result) {
                                        self.sendForm(form);
                                    } else {
                                        self.hideLoader();
                                        for (var i in data.errors) {
                                            $('#' + form_type + 'form-' + i.toLowerCase()).siblings('.help-block').html(data.errors[i]).parents('.form-group').addClass('has-error');
                                        }
                                    }
                                },
                                error: function (jqXHR) {
                                    self.hideLoader();
                                    if (jqXHR.responseText) alert(jqXHR.responseText);
                                }
                            });
                        }, 2000);
                    } else self.sendForm(form);
                    return false;
                }
            }
        };

        var self = this;
        this.init();
    };

    window.reloader = new __reloader();

})(jQuery);