$(document).ready(function(){
    $('.info_selection').click(function() {
       $("input[name='new_img']").val($(this).attr("image"));
       $("form[id='movieUpdateForm']").submit();
    });
  });
  