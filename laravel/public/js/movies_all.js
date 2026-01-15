(function($){
    $.fn.loaddata = function(options) {// Settings
        var settings = $.extend({
            data_url        : urlsearch, //url to PHP page
            start_page      : 1 //initial page
        }, options);

        var el = this;
        loading  = false;
        end_record = false;
        contents(el, settings); //initial data load

        $(window).scroll(function() { //detact scroll
            if($(window).scrollTop() + $(window).height() >= $(document).height()){ //scrolled to bottom of the page
                contents(el, settings); //load content chunk
            }
        });
        $(document).ready(function(){
          $( "#search" ).keyup(function() {
              $.post( settings.data_url, {'search': $("#search").val(),'support': $("#supportid").val(),'_token': csrfToken}, function(data){ //jQuery Ajax post
                  $("#movies").html(data);  //append content
              })
          });
        });
    };
    //Ajax load function
    function contents(el, settings){
      var div_loader = "<div id='loader_img'><img src='"+loadingImg+"'></div>";

        if(loading == false && end_record == false){
            loading = true; //set loading flag on
            el.append(div_loader); //append loading image
            $.post( settings.data_url, {'page': settings.start_page,'search': $("#search").val(),'support': $("#supportid").val(),'_token': csrfToken}, function(data){ //jQuery Ajax post
                if(data.trim().length == 0){ //no more records
                    $('#loader_img').remove();
                    end_record = true; //set end record flag on
                    return; //exit
                }
                loading = false;  //set loading flag off
                $('#loader_img').remove();
                $("#movies").append(data);  //append content
                settings.start_page ++; //page increment
            })
        }
    }
})(jQuery);
$("#movies").loaddata(); //load the results into element
