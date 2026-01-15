(function($){
    $.fn.loaddata = function(options) {// Settings
        var settings = $.extend({
            data_url        : urlsearch, //url to PHP page
            start_page      : 1, //initial page
            update          : false
        }, options);

        var el = this;
        contents(el, settings); //initial data load

        $(document).ready(function(){
          $( "#search" ).keyup(function() {
            settings.update = false;
            settings.start_page = 1;
            contents(el, settings);
          });
          $(".dropdown_genre").click(function(){
            $("#genre_select").val($(this).attr("choice"));
            $("#dropdownMenuLinkGenre").text($(this).text());            
            settings.update = false;
            settings.start_page = 1;
            contents(el, settings);
          });
          $(".dropdown_year").click(function(){
            $("#year_select").val($(this).attr("choice"));
            $("#dropdownMenuLinkYear").text($(this).text());            
            settings.update = false;
            settings.start_page = 1;
            contents(el, settings);
          });
          $(".dropdown_support").click(function(){
            $("#support_select").val($(this).attr("choice"));
            $("#dropdownMenuLinkSupport").text($(this).text());            
            settings.update = false;
            settings.start_page = 1;
            contents(el, settings);
          });
          $(".more_data").click(function(){
            settings.update = true;
            contents(el, settings);
          });
        });
    };
    //Ajax load function
    function contents(el, settings){
        $.post( settings.data_url, {'page': settings.start_page,'search': $("#search").val(),'year': $("#year_select").val(),'genre': $("#genre_select").val(),'support': $("#support_select").val(),'_token': csrfToken}, function(data){ //jQuery Ajax post
            if(settings.update)
                $("#movies-list-2cols").append(data.content);  //append content
            else
                $("#movies-list-2cols").html(data.content);  //append content
            $("#nb_movies").text(data.total);
            if(data.nb < 10)
                $(".more_data").hide();
            else
                $(".more_data").show();
            settings.start_page ++; //page increment
        })
    }
})(jQuery);
$("#movies-list-2cols").loaddata(); //load the results into element
