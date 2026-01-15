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
            if($(window).scrollTop() + $(window).height() + 0.5 >= $(document).height()){ //scrolled to bottom of the page
                contents(el, settings); //load content chunk
            }
        });
        $(document).ready(function(){
          $( "#search" ).keyup(function() {
              $.post( settings.data_url, {'search': $("#search").val(),'category': $("#catid").val()}, function(data){ //jQuery Ajax post
                  $("tbody").html(data);  //append content
              })
          });
        });
    };
    //Ajax load function
    function contents(el, settings){
      var div_loader = "<tr id='loader_img'><td colspan='4' align='center'><img src='{{URL::asset('img/loading.gif')}}'></td></tr>";

        if(loading == false && end_record == false){
            loading = true; //set loading flag on
            el.append(div_loader); //append loading image
            $.post( settings.data_url, {'page': settings.start_page,'search': $("#search").val(),'category': $("#catid").val()}, function(data){ //jQuery Ajax post
                if(data.trim().length == 0){ //no more records
                    $('#loader_img').remove();
                    end_record = true; //set end record flag on
                    return; //exit
                }
                loading = false;  //set loading flag off
                $('#loader_img').remove();
                $("tbody").append(data);  //append content
                settings.start_page ++; //page increment
            })
        }
    }
})(jQuery);
$("tbody").loaddata(); //load the results into element
