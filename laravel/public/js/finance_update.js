$(document).ready(function(){
  $('button').click(function() {
    $('#category_id').val($(this).val());
    $('#TransactionUpdateForm').submit();
  });
});
