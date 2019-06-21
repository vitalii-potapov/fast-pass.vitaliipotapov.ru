$(document).ready(function(){

  let notificationRemoveRecord;
  $('.btn.btn-danger').on('click', function(){
    let answer=confirm('Do you really want to delete the entry?','');
    if(answer) {
      let id = $(this).attr('data-id');

      $.ajax({
        url: window.location.pathname + 'site/remove?id=' + id,
        type: 'POST',
        success: function(res){
          if(+res) {
            $('[data-id="'+id+'"]').closest('tr').remove();
            if ($(".alert-success.alert").length){
              clearTimeout(notificationRemoveRecord);
              notificationRemoveRecord = setTimeout(function(){removeAlert('.alert-success.alert')}, 5000);
            } else {
              $( ".container-main" ).prepend( '<div class="alert-success alert fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Record deleted!</div>' );
              notificationRemoveRecord = setTimeout(function(){removeAlert('.alert-success.alert')}, 5000);
            }
          } else {
            console.log('bad request - ' + window.location.pathname + 'site/remove?id=' + id)
          }
        },
        error: function(){
          console.log('Error!');
        }
      });
      return false;
    };
  });

  $('.btn-update').on('click', function(){
    $('#myModalLabel').text($(this).closest('tr').children('.field-site').text())
    $('#service-update').attr('action', 'site/update?id='+$(this).attr('data-id'));
    $('#services-site').val($(this).attr('data-site'));
    $('#services-login').val($(this).attr('data-login'));
    $('#services-field_login').val($(this).attr('data-flogin'));
    $('#services-field_pass').val($(this).attr('data-fpass'));
    $('#services-attribute [value="' + $(this).attr('data-attr') + '"]').attr('checked', 'checked');
  });

  function removeAlert(alert) {
    $(alert).remove();
  }

});