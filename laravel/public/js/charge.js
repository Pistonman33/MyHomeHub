$(document).ready(function(){
  $('#gaz').focusout(function() {
    if($(this).val() > 0){
      var eval = $(this).val() - $("#previous_gaz").val();
      $('#gaz_eval').text(eval.toFixed(3)+' m3');
    }
  });
  $('#elec').focusout(function() {
    if($(this).val() > 0){
      var eval = $(this).val() - $("#previous_elec").val();
      $('#elec_eval').text(eval.toFixed(1)+' kwh');
    }
  });
  $('#water').focusout(function() {
    if($(this).val() > 0){
      var eval = $(this).val() - $("#previous_water").val();
      $('#water_eval').text(eval.toFixed(4)+' m3');
    }
  });
});
