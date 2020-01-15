$(document).ready(function () {
  $('.insurance-company__list').owlCarousel({
    items: 7,
    loop: true,
    dots: true,
    autoplay: true,
    autoplayTimeout: 5000,
    autoplaySpeed: 1200,
    responsive : {
      0: {
        items: 1
      },
      500: {
        items: 4
      },
      991: {
        items: 5
      },
      1280: {
        items: 7
      }
    }
  });
  var owl = $('.companies-slider__wrapper');
  owl.owlCarousel({
    loop: true,
    margin: 0,
    autoplay: true,
    autoplaySpeed: 1000,
    nav: true,
    navSpeed: 1000,
    navText: [],
    autoplayHoverPause: true,
    onInitialized: function () {
      if($(window).width() > 680) {
        var act = $('.companies-slider .owl-stage').find( ".active" );
        companiesSliderGradient(act);
      }
    },
    responsive: {
      0: {
        items: 1,
        center: true
      },
      425: {
        items: 3,
        center: true
      },
      681: {
        items: 5,
        center: true
      }
    }

  });

  if($(window).width() < 580) {
    $('.company-files').owlCarousel({
      autoWidth: true,
      nav: true,
      navText: []
    });
  }

  owl.on('change.owl.carousel', function(event) {
    $( ".companies-slider .owl-item" ).removeClass('first last');
    setTimeout(function() {
      if($(window).width() > 680) {
        var act = $('.companies-slider .owl-stage').find( ".active" );
        companiesSliderGradient(act);
      }
    }, 50);
  });

  function companiesSliderGradient(act) {
    act.first().addClass('first');
    act.last().addClass('last');
  }

  function slideSizes(owl) {
    var stageWidth = $('.companies-slider__wrapper').width();
    if($(window).width() > 680) {
      var slideWidth = stageWidth/5;
    } else if($(window).width() <= 680 && $(window).width() > 425) {
      var slideWidth = stageWidth/3;
    } else if ($(window).width() < 425) {
      var slideWidth = stageWidth;
    } else {
      return false
    }
    setTimeout(function () {
      $('.companies-slider .owl-item').width(slideWidth);
      $('.companies-slider .owl-item').height((slideWidth * 184)/176);
    }, 100);

    //owl.trigger('refresh.owl.carousel');
    return false;
  }

  slideSizes(owl);

  $(window).resize(function () {
    slideSizes();

    $('.days-counter').height(inpH);
    $('.date-input, .ppl-input').height(inpH);
  });

  $(".js-select").select2({
    minimumResultsForSearch: Infinity
  });

  $(".filter").find(".js-datepicker").datepicker({
    autoClose: true,
    minDate: new Date(new Date().getTime() + 24 * 60 * 60 * 1000),
    showOtherYears: true,
    onSelect: function (formattedDate, date, inst) {
        var date_from_str = $("#travelform-datefrom").val();
        var date_to_str = $("#travelform-dateto").val();
        if (date_from_str.length && date_to_str.length) {
          var date_from = moment(date_from_str, "DD.MM.YYYY");
          var date_to = moment(date_to_str, "DD.MM.YYYY");
          var daysRange  = date_to.diff(date_from, 'days')+1;
          if (daysRange>0) {
            $("#travelform_days_wrapper .days-counter__quantity").html(daysRange).show();
            $("#travelform_days_wrapper .days-counter__days").show();
          } else {
            $("#travelform_days_wrapper .days-counter__quantity").html('0').hide();
            $("#travelform_days_wrapper .days-counter__days").hide();
          }
        }
    }
  });

  var inpH = $('.days-counter').outerWidth();

  $('.days-counter').height(inpH);
  $('.date-input, .ppl-input').height(inpH);

  $('.change_travellersCount').on('click', function(e) {
    e.preventDefault();
    var change = parseInt($(this).data('kol'));
    var cur = parseInt($('#travelform-travellerscount').val());
    var new_var = cur + change;
    if (new_var<1) new_var = 1;
    if (new_var>100) new_var = 100;
    $('#travelform-travellerscount').val(new_var);
  });

  $('.js-select').on('select2:opening', function (e) {
      setTimeout(function () {
          $('.filter-popular').addClass('filter-popular_white');
          $('.select2-dropdown').width($('.field-travelform-countries').width() + 23);
      }, 20);
  });

  $('.js-select').on('select2:closing', function (e) {
    setTimeout(function () {
        $('.filter-popular').removeClass('filter-popular_white');
    }, 20);
  });

  $('.burger-menu').on('click', function () {
      $('.menu').addClass('active');
  });

  $('.menu__close').on('click', function (e) {
      e.preventDefault();
      $('.menu').removeClass('active');
  });
});