alert(1);

(function ($){
  $(window).ready(function(){
    if (Modernizr.geolocation) {
      navigator.geolocation.getCurrentPosition(setUserLocation, askUserLocation);
    } else {
      // Нет встроенной поддержки
    }

    function setUserLocation(position){

      $.post('/geo', {location:position.coords}, function(data){
        if (data.cmd) {
          if (data.cmd == 'reload') {
            window.location.href = window.location.protocol +'//'+ window.location.host + window.location.pathname;
            // document.location.href = document.location.href;
          }
          if (data.cmd == 'ask') {
            askUserLocation();
          }
        }
      }, 'json');
    }

    function askUserLocation(){
      // alert('При получении текущего местоположения произошла непредвиденная ситуация. Сервис геолокации не задействован.');
    }

  })
})(jQuery);