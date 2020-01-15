function reInit(first) {
    var windowWidth = $(window).outerWidth();

    $('form').each(function () {
        if ($(this).data('yiiActiveForm')) {
            $(this).data('yiiActiveForm').submitting = true;
            $(this).yiiActiveForm('validate', true);
        }
    });
    $('#selectCountry').click(function (e) {

        $('.select-country').show();
    });
    $('.select-country').mouseleave(function (e) {
        $('.select-country').hide();
    });

    $(".filter").find(".js-datepicker").datepicker({
        autoClose: true,
        minDate: new Date(new Date().getTime() + 24 * 60 * 60 * 1000),
        showOtherYears: true
    });

    $('.video-banner').height($('.page__left_main').width() * 0.6313);

    if (windowWidth < 481) {

        $(".js-datepicker").attr('readonly', 'readonly');

        var windowHeight = $(window).outerHeight(),
            obj = {
                width: windowWidth,
                height: windowHeight + 100
            };

        $(".datepicker").css(obj).append('<div class="datepicker--close"><svg viewBox="0 0 12 12" aria-hidden="true" focusable="false" style="display: block; fill: rgb(11, 175, 29); height: 15px; width: 15px;"><path fill-rule="evenodd" d="M11.5 10.5c.3.3.3.8 0 1.1-.3.3-.8.3-1.1 0L6 7.1l-4.5 4.5c-.3.3-.8.3-1.1 0-.3-.3-.3-.8 0-1.1L4.9 6 .5 1.5C.2 1.2.2.7.5.4c.3-.3.8-.3 1.1 0L6 4.9 10.5.4c.3-.3.8-.3 1.1 0 .3.3.3.8 0 1.1L7.1 6l4.4 4.5z"></path></svg></div>');

        var dateFromPicker = $("#travelform-datefrom").datepicker().data('datepicker'),
            dateToPicker = $("#travelform-dateto").datepicker().data('datepicker'),
            i = 0;


        $("#travelform-datefrom").datepicker({
            onShow: function (inst, animationCompleted) {
                dateToPicker.hide();
                $('body').css({'overflow': 'hidden'});
                $(this).css({'cursor': 'none'});
                if (i == 0) {
                    $('.datepicker').prepend('<div class="datepicker--title"><span class="datepicker--title-from">Туда</span>  &#8594;  <span class="datepicker--title-to">Обратно</span></div>');
                    i++;
                }
                $('.datepicker--title-to').removeClass('active');
                $('.datepicker--title-from').addClass('active');
            },
            onSelect: function (inst, animationCompleted) {
                console.log(inst);
                $('.datepicker--title-from').html(inst).removeClass('active');
                $('.datepicker--title-to').addClass('active');
                dateToPicker.show();
            }
        });


        $("#travelform-dateto").datepicker({
            onShow: function () {
                $('.datepicker--title-to').addClass('active');
                $('.datepicker--title-from').removeClass('active');
                $(this).css({'cursor': 'none'});
            },
            onHide: function (inst, animationCompleted) {
                $('body').css({'overflow': 'auto'});
                $(this).css({'cursor': 'auto'});
            },
            onSelect: function (inst, animationCompleted) {
                $('.datepicker--title-to').html(inst);
            }
        });

        $(".datepicker--close").on('click', function () {
            dateFromPicker.hide();
            dateToPicker.hide();
        })

    } else {

        var dateToPicker = $("#travelform-dateto").datepicker().data('datepicker');
        $("#travelform-datefrom").datepicker({
            onShow: function (inst, animationCompleted) {
                dateToPicker.hide();
            },
            onSelect: function (inst, animationCompleted) {
                dateToPicker.show();
            }
        });

    }

    if (windowWidth > 1020) {

        resultsFixed();

    }

    $(".options__leaving input.js-datepicker").each(function () {
        var dateIntervalPicker = $(this);
        if (!dateIntervalPicker.hasClass('adip')) {
            var dates = $(dateIntervalPicker).data('dates');
            for (var i in dates) dates[i] = new Date(dates[i]);

            $(dateIntervalPicker)
                .addClass('adip')
                .datepicker({
                    autoClose: true,
                    range: true,
                    toggleSelected: false,
                    minDate: new Date(new Date().getTime() + 24 * 60 * 60 * 1000),
                    onSelect: function (formattedDate, date, inst) {
                        if (date.length > 1) {
                            var day1 = date[0],
                                day2 = date[1],
                                daysRange = Math.floor(((day2.getTime() - day1.getTime()) / (1000 * 60 * 60 * 24)) + 1);
                            $('.days_count').html(" (" + daysRange + daysLiteral(daysRange) + ")");
                            $("body").trigger('change-filter');
                        } else {
                            $('.days_count').html(" ");
                        }
                    }
                })
                .data('datepicker').selectDate(dates);
        }
    });

    if ($(".js-progress").length) {

        var variants = $(".pregnant-progress .js-progress").data('variants');
        variants = variants ? variants.split(',') : [];

        $(".pregnant-progress .js-progress").each(function () {
            $(this).ionRangeSlider({
                type: "single",
                from: variants.indexOf($(this).val()),
                keyboard: true,
                values: variants,
                grid: true,
                prettify: function (num) {
                    return "до " + num;
                }
            });

        });

        var variants = $(".period__progress .js-progress").data('variants');
        variants = variants ? variants.split(',') : [];

        $(".period__progress .js-progress").each(function () {
            $(this).ionRangeSlider({
                type: "single",
                from: variants.indexOf($(this).val()),
                keyboard: true,
                values: variants,
                grid: true,
                prettify: function (num) {
                    return num;
                }
            });

        });

    }

    $('.checkbox__input_check_all').change(function () {
        $('body').off('change-filter');
        var options = $('.medical-options').find('input[type="checkbox"]');

        if ($(this).is(':checked')) {
            options.prop('checked', true);
            $('.pregnant-progress').slideDown();
        } else {
            options.prop('checked', false);
            $('.pregnant-progress').slideUp();
        }
        $('body').off('change-filter').on('change-filter', function (e) {
            $('form.insurance-find__body').submit();
        });
        $('body').trigger('change-filter');
    });

    variants = $(".additional-options__progress_sum .js-progress").data('variants');
    variants = variants ? variants.split(',') : [];
    $(".additional-options__progress_sum").find(".js-progress").each(function () {
        $(this).ionRangeSlider({
            type: "single",
            //    min: 0,
            //    max: 5,
            //    step: 1,
            from: variants.indexOf($(this).val()),
            keyboard: true,
            values: variants,
            grid: true
        });
    });

}

$(document).ready(function () {

    var banner = $("#calc-tour"),
        windowWidth = $(window).outerWidth();

    if (windowWidth < 1021) {

        $(".page__left").prepend(banner);
        $('.insurance-find__type.active').clone().prependTo('.page__left');

    }

    if (windowWidth < 501) {

        $(".header").addClass("header-small");

    }

    $(".page-about").closest(".page__inner").css({"height": "auto"});

    $(document).on('click', '.js-local-hrefs', function (e) {
        e.preventDefault();
        var id = $(this).parents('.insurance-find__type').attr('id');
        $(".js-insurance-forms-linker a[href='#" + id + "']").click();
        $('html, body').animate({scrollTop: 0}, 500);
    });
    $(document).on('click', '.js-insurance-forms-linker a', function (e) {
        e.preventDefault();
        if ($(this).hasClass('insurance-types__item_disabled')) return;
        $('.js-insurance-forms-linker a').removeClass('insurance-types__item_current');
        $(this).addClass('insurance-types__item_current');
        $('.insurance-find .insurance-find__type').removeClass('active');
        var div = $($(this).attr('href'));
        $(div).addClass('active').prependTo('.insurance-find');
    });

    $('.video-background, .icon_play-video').click(function () {
        $('.video-banner').YTPPlay();
        setTimeout(function () {
            $('.video-background').fadeOut(300);
            $('.icon_play-video').fadeOut(300);
        }, 200);
    });

    $(".video-banner").YTPlayer();

    $(".video-banner").on('YTPEnd', function () {
        $('.video-background').fadeIn(300);
        $('.icon_play-video').fadeIn(300);
    });

    $(document).on('change.common', '.checkbox-list__item_pregnant input[type="checkbox"]', function () {
        $('.pregnant-progress').slideToggle();
    });

    $(document).on('change.common', '.checkbox-list__item_escape-travel>.checkbox  input[type="checkbox"]', function () {
        $('.escape-travel').slideToggle();
    });


    $(".js-select").select2({
        minimumResultsForSearch: Infinity
    });

    $(document).on("click", ".js-rf-item", function () {
        if (!$(this).hasClass("_active")) {
            $(".js-rf-item").removeClass("_active");
            $(".js-rf-item").find("input[type=radio]").prop("checked");
            $(this).addClass("_active");
            $(this).find("input[type=radio]").prop("checked", true).trigger('change');
        }
    });
    $(document).on("click", ".js-insurance-ppl-add", function () {
        var list = $(this).parents(".js-insurance-ppl");
        var source = $("#insurancedPpl").html();
        var template = Handlebars.compile(source);
        var index = list.children().length;
        list.append(template({
            name: index
        }));
        reInit();
    });
    $(document).on("click", ".js-tabs-item", function () {
        var parent = $(this).parents(".tabs");
        var tabData = $(this).attr("data-tab");
        parent.find(".js-tabs-item").removeClass("_active");
        $(this).addClass("_active");
        parent.find(".js-content-item").removeClass("_active");
        parent.find(".js-content-item[data-content=" + tabData + "]").addClass("_active");
        if (windowWidth > 1020)
            resultsFixed();
    });
    $(".js-ao-close").on("click", function () {
        $(this).parents(".additional-options").toggleClass("_closed");
    });
    $(document).on("click", ".js-back-to-top", function () {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
    });
    $(document).on("click", ".js-add-new-traveler", function () {
        var max_travellers = $(this).data('max');
        var cur_travellers = $(".js-travelers-list .add-card").length;
        if (cur_travellers < max_travellers) {
            addTraveller(true);
            if (windowWidth > 1020) resultsFixed();
        }

        if ((cur_travellers + 1) >= max_travellers) {
            $(this).hide();
        }
    });
    $(document).on("click", ".js-add-card-close", function () {
        $(this).parent().remove();
        $(".add-card").each(function (index, item) {
            $(item).find(".add-card__counter").html(index + 1);
        });
        $(".js-add-new-traveler").show();
        if ($(".js-travelers-list .add-card").length == 0) {
            addTraveller();
        }
        reloadPrice();
    });
    $(document).on("click", ".dropdown__header", function () {

        var _this = $(this),
            dropdown = _this.parent(),
            d_text = _this.find($('.dropdown__text')),
            d_icon = _this.find($('.dropdown__icon'));

        dropdown.toggleClass("_open");

        if (dropdown.hasClass('_open')) {
            d_text.html('Скрыть');
            d_icon.removeClass('dropdown__icon_open');
            var h = $('.page__right').height(),
                dropOpen = $('.dropdown').height(),
                point = h - dropOpen;
            $('html, body').animate({scrollTop: point}, 1200);
        } else {
            d_text.html('Показать');
            d_icon.addClass('dropdown__icon_open');
            h = $('.page__right').height();
            dropOpen = $('.dropdown').height();
            $('html, body').animate({scrollTop: h - dropOpen}, 1000);
        }
    });

    var columnRight = $(".js-scroll-column:nth-of-type(2)").outerHeight();
    var columnLeft = $(".js-scroll-column:nth-of-type(1)").outerHeight();
    var differ = columnLeft - columnRight;
    var cr = 0;
    var cl = 0;


    if (differ > 0) {
        cr = differ;
        cl = 0;
    } else {
        cr = 0;
        cl = -differ;
    }

    $(window).scrollTop(0);
    var scrollCounter = 0;
    var fixed = false;

    if (windowWidth > 1020) {

        $(".page__inner_index .js-scroll-column:nth-of-type(2)").scrollToFixed({
            marginTop: function () {
                var marginTop = $(window).height() - $(".js-scroll-column:nth-of-type(2)").outerHeight(true) + 0;
                if (marginTop >= 0) {
                    return marginTop;
                } else {
                    return marginTop - cl;
                }
            },
            zIndex: 1,
            postFixed: function () {
            },
            preFixed: function () {
            }


        });
        $(".page__inner_index .js-scroll-column:nth-of-type(1)").scrollToFixed({
            marginTop: function () {
                var marginTop = $(window).height() - $(".js-scroll-column:nth-of-type(1)").outerHeight(true) + 0;

                console.log('1отступ' + marginTop);
                if (marginTop >= 0) {
                    return marginTop + 1;
                } else {
                    return marginTop - cr;
                }
            },
            zIndex: 1,
            postFixed: function () {
            },
            preFixed: function () {
            }
        });
    }

    $('.medical-options').find('input[type="checkbox"]').change(function () {
        if ($(this).is(':checked') == false) {
            $('.checkbox__input_check_all').prop('checked', false);
        }
    });

    if ($(window).width() > 1020) {

        $(document).on('mouseover', '.checkbox-list__item', function () {
            var helper = $(this).find('.helper__text');

            /*if(!helper.hasClass('visible')) {*/
            helper.addClass('visible');
            /*}*/
        });

        $(document).on('mouseout', '.checkbox-list__item', function () {
            var helper = $(this).find('.helper__text');

            /*if(helper.hasClass('visible')) {*/
            helper.removeClass('visible');
            /*}*/
        });
    }

    var i = 0;

    if (windowWidth > 500) {

        $(window).scroll(function () {

            if ($(document).scrollTop() > 0) {
                $('.header').addClass('header-small');
                $('.content-wrapper').css('padding-top', '55px');
            } else {
                $('.header').removeClass('header-small');
                $('.content-wrapper').css('padding-top', '113px');
            }

        });

    }

    $("#partner_form input[name='PartnerForm[type]']").on('change', function () {
        if ($(this).val() == 'Юридическое лицо') {
            $("#partner_form input[name='PartnerForm[jur]']").parents('.partners-form__row').show();
        } else {
            $("#partner_form input[name='PartnerForm[jur]']").parents('.partners-form__row').hide();
        }
    });

    $("#partner_form").validate({
        rules: {
            'PartnerForm[jur]': "required",
            'PartnerForm[surname]': "required",
            'PartnerForm[name]': "required",
            'PartnerForm[thirdname]': "required",
            'PartnerForm[email]': {
                required: true,
                email: true
            },
            'PartnerForm[phone]': "required"
        },
        messages: {
            'PartnerForm[jur]': "Это обязательное поле",
            'PartnerForm[surname]': "Это обязательное поле",
            'PartnerForm[name]': "Это обязательное поле",
            'PartnerForm[thirdname]': "Это обязательное поле",
            'PartnerForm[email]': {
                required: "Это обязательное поле",
                email: "Введите корректный email"
            },
            'PartnerForm[phone]': "Это обязатльное поле"
        },
        submitHandler: function () {

            var action = $('#partner_form').attr('action');
            var fields = $('#partner_form').serializeArray();
            $.ajax({
                method: "POST",
                url: action,
                data: fields,
                success: function (result) {
                    console.log(result);
                    $('.partners-form__form').append('<span class="partners-form__success"><div class="success__centering"><span class="success__text">Ваш запрос успешно отправлен</span></div></span>');
                    setTimeout(successSendPopup(), 400);
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(thrownError);
                    $('.partners-form__form').append('<span class="partners-form__success"><div class="success__centering"><span class="success__text">Ошибка отправки:' + thrownError + '</span></div></span>');
                }
            });

        }
    });

    if ($('.page__right_landing').length) {

        landingRightHeight();

        $(window).scroll(function () {
            landingRightHeight();
        });
    }

    setTimeout(function () {
        $('.slider').owlCarousel({
            items: 1,
            autoplay: true,
            loop: true,
            nav: true,
            dots: true
        });
    }, 150);

    function successSendPopup() {
        $('.partners-form__success').fadeIn(600, function () {
            setTimeout(function () {
                $('.partners-form__success').fadeOut(600);
            }, 3000);
        });
    };

    function landingRightHeight() {
        var landingRight = $('.page__right_landing'),
            landingSlide = $('.slider__item');
        if ($(window).scrollTop() > 0) {
            landingRight.height($(window).height() - 55);
            landingSlide.height($(window).height() - 55);
        } else {
            landingRight.height($(window).height() - 113);
            landingSlide.height($(window).height() - 113);
        }
    }

    reInit();
});

function addTraveller(recalcPrice) {
    var list = $(".js-travelers-list");
    if (list.length) {
        var source = $("#addCard").html();
        var template = Handlebars.compile(source);
        var index = $(".js-travelers-list .add-card").length + 1;
        list.append(template({
            counter: index
        }));
        reInit();
        var cur_date = new Date();
        cur_date.setFullYear(cur_date.getFullYear() - 90),
            $(".js-datepicker", list).datepicker({
                startDate: new Date('1970'),
                autoClose: true,
                minDate: cur_date,
                onSelect: function (formattedDate, date, inst) {
                    reloadPrice();
                }
            }).on('change', function () {
                var data = $(this).val();
                var parts = data.split('.');

                if (parts.length == 3) {
                    $(this).datepicker().data('datepicker').selectDate(new Date(parts[2], parts[1] - 1, parts[0]));
                }

                reloadPrice()
            });

        if (recalcPrice) {
            reloadPrice();
        }
    }
}

function reloadPrice() {
    var data = $('form#calc-prepay-form').blur().serializeArray();
    if (last_query !== undefined && last_query) {
        last_query.abort();
    }

    last_query = $.post('/api/travel/calc-fix-cost.html', data, function (response) {
        if (response.price) {
            $('.company__price .price-value, .traveler-buyer__pay-price-number div, .travelers-data__pay-price-number div').html(response.price);
        }
    }, 'json');
}

function daysLiteral(d) {
    var days = d.toString(),
        l = days.length;

    if (l > 1) {
        if ((days[l - 1] > 1 && days[l - 1] < 5) && days[l - 2] == 1) {
            return " дней";
        } else if ((days[l - 1] > 1 && days[l - 1] < 5) && days[l - 2] > 1) {
            return " дня";
        } else if (days[l - 1] == 1 && days[l - 2] > 1) {
            return " день";
        } else {
            return " дней";
        }
    } else {
        if (days == 1) {
            return " день";
        } else if (days > 1 && days < 5) {
            return " дня";
        } else {
            return " дней";
        }
    }
}

function filtersLiteral(n) {
    var numb = (n + 3).toString(),
        l = numb.length;

    if (l > 1) {
        if ((numb[l - 1] > 1 && numb[l - 1] < 5) && numb[l - 2] == 1) {
            return "Применено " + numb + " фильтров";
        } else if ((numb[l - 1] > 1 && numb[l - 1] < 5) && numb[l - 2] > 1) {
            return "Применено " + numb + " фильтра";
        } else if (numb[l - 1] == 1 && numb[l - 2] > 1) {
            return "Применён " + numb + " фильтр";
        } else {
            return "Применено " + numb + " фильтров";
        }
    } else {
        if (numb == 1) {
            return "Применён " + numb + " фильтр";
        } else if (numb > 1 && numb < 5) {
            return "Применено " + numb + " фильтра";
        } else {
            return "Применено " + numb + " фильтров";
        }
    }
}

var workService = window.location.hash;

if (workService === "#work-service") {
    $("#work-service").addClass("active");
    $(".how-work-ins-travel").show();
    $(".how-work-ins-travel .top").addClass("active");
} else if (workService === "#travel-insurance") {
    $("#travel-insurance").addClass("active");
    $(".how-work-ins-travel").show();
    $(".how-work-ins-travel .top").addClass("active");
} else if (workService === "#insurance-policy") {
    $("#insurance-policy").addClass("active");
    $(".how-work-ins-travel").show();
    $(".how-work-ins-travel .top").addClass("active");
}

$(".burger-menu").on("click", function () {
    if (!$(".burger-menu").hasClass("active")) {
        $(this).addClass("active");
        $(".menu_header").addClass("active");
    } else {
        $(this).removeClass("active");
        $(".menu_header").removeClass("active");
    }
});

function closeMenu() {
    if ($(".how-work-ins-travel").hasClass('active')) {
        $(".how-work-ins-travel").removeClass('active');
        $('.content-how-work').removeClass('active');
    } else {
        $(".how-work-ins-travel").addClass('active');
    }

}

$('.wrap-fa').on('click', function () {
    closeMenu()
});

/*function calcScrolling(flag) {
    console.log(flag);
    if(flag == 1) {
        if($(document).scrollTop() > 0) {
            $(".page__left").addClass('calc-scroll').removeClass('calc-sticky');
        } else {
            $(".page__left").removeClass('calc-scroll').addClass('calc-sticky');
        }
    }
}*/

if ($(".page__inner_company").length > 0) {

    if ($('.card-wrapper').height() < $(window).height() - $('.header').outerHeight()) {
        $(window).scroll(function () {
            if ($(document).scrollTop() > 0) {
                $('.page__right').addClass('page__right_scrolling').removeClass('page__right_sticky');
            } else {
                $('.page__right').addClass('page__right_sticky').removeClass('page__right_scrolling');
            }
        });
    } else {
        if ($(".page__inner_company").length > 0) {
            $(".page__right").removeClass('page__right_overflow page__right_sticky');
        }
        ;
    }
}

function resultsFixed() {
    $(window).scroll();

    $('.company-list').css({'height': 'auto'});
    $(".page__left").css({'position': 'absolute'});

    $(".calc .js-scroll-column:nth-of-type(2)").trigger('detach.ScrollToFixed').scrollToFixed({
        marginTop: function () {
            var columnLeft = $(".js-scroll-column:nth-of-type(1)").outerHeight(); //высота левой колонки
            var columnRight = $(".js-scroll-column:nth-of-type(2)").outerHeight(); //высота правой колонки
            var differ = columnLeft - columnRight; //разница между высотами колонок
            var cl = 0;

            if (differ > 0) { //если разница положительная - левая колонка выше
                cl = 0;
            } else { //если разница отрицательная - правая колонка выше
                cl = -differ;
            }

            var marginTop = $(window).height() - $(".js-scroll-column:nth-of-type(2)").outerHeight(true) + 0;

            if (marginTop > 0) {
                return marginTop;
            } else {
                return marginTop - cl;
            }
        },
        zIndex: 1,
        postFixed: function () {
        },
        preFixed: function () {
        }


    });

    $(".calc .js-scroll-column:nth-of-type(1)").trigger('detach.ScrollToFixed').scrollToFixed({
        marginTop: function () {
            var columnLeft = $(".js-scroll-column:nth-of-type(1)").outerHeight(); //высота левой колонки
            var columnRight = $(".js-scroll-column:nth-of-type(2)").outerHeight(); //высота правой колонки
            var differ = columnLeft - columnRight; //разница между высотами колонок
            var cr = 0;

            if (differ > 0) { //если разница положительная - левая колонка выше
                cr = differ;
            } else { //если разница отрицательная - правая колонка выше
                cr = 0;
            }

            var marginTop = $(window).height() - $(".js-scroll-column:nth-of-type(1)").outerHeight(true);
            if (marginTop > 0) {
                return marginTop;
            } else {
                return marginTop - cr;
            }
        },
        zIndex: 1,
        postFixed: function () {
        },
        preFixed: function () {
        }
    });

    $(window).scroll();
}





