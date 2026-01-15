$(document).ready(function(){
  $('#upload input').on('change', function () {
    $('#upload p').text(this.files.length + " file(s) selected");
    $(".ajaxloader").show();
    $('#upload').submit();
  });
});
