$(document).ready(function(){
  $('input:radio[name="byperiod"]').change(function(){
     period = $(this).val();
     year_filter = $("#year_filter").val();
     window.location.href = urlstats + '?year='+year_filter+'&byperiod='+period;
   });
});
