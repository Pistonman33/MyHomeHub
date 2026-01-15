$(document).ready(function(){
  $('.info_selection').click(function() {
     $("input[name='infomovie_id']").val($(this).attr("infomovie_id"));
     $("input[name='movie_id']").val($(this).attr("movie_id"));
     $("form[id='movieUpdateForm']").submit();
  });
});
