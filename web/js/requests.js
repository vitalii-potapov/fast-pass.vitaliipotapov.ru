$(document).ready(function(){

  let secretForm = $('#secret-form').clone();
  let emptyList = $('.empty-list').clone();

  if (checkSessionStorage()) {
    $('#secret-form').remove();
    $('.empty-list').remove();
    let i = 1;
    for (key in sessionStorage) {
      if (typeof sessionStorage[key] === 'string' && sessionStorage[key].includes('encrypt_login')) {
        let obj = JSON.parse(sessionStorage[key]);
        let row = i,
            id = obj.id,
            site = obj.site,
            hideLogin = obj.hideLogin,
            login = obj.login,
            encrypt_login = obj.encrypt_login,
            pass = obj.pass,
            attr = obj.attribute,
            flogin = obj.field_login,
            fpass = obj.field_pass;

        $('#tbody').append('<tr id="site-' + id + '"><td scope="row">' + row + '</td><td><img src="/web/image/icons/' + getHostname(site) + '.png" alt="">' + getHostname(site) + '</td><td>' + hideLogin + '</td><td><a class="btn btn-success" href="' + site + '?name=' + encrypt_login + '&pass=' + pass + '&attr=' + attr + ',' + flogin + ',' + fpass + '" target="_blank">L</a><a data-id="' + id + '" class="btn btn-danger">R</a><a data-id="' + id + '" data-site="' + site + '" data-login="' + login + '" data-flogin="' + flogin + '" data-fpass="' + fpass + '" data-attr="' + attr + '" class="btn btn-primary btn-update" data-toggle="modal" data-target="#myModal">U</a></td></tr>');
        i++;
      }
    };
    clearUpdateRemoveBtn();
    callUpdateRemoveBtn();
  } else {
    $('.empty-list').show();
    $('#secret-form').show();
  };

  let query = 1;
  let notificationWrongSecretKey;
  let notificationSuccessSecretKey;
  $('#get-sites').on('click', function(){
    if (query && !checkSessionStorage()) {
      query = 0;
      $.ajax({
        url: window.location.pathname + 'site/index-json?key=' + $('#secret-key').val(),
        type: 'GET',
        dataType: 'json',
        success: function(res){
          if(res.length) {
            $('#secret-form').remove();
            $('.empty-list').remove();
            removeAlert();
            createAlert(1, 'Success secret key!');
            notificationSuccessSecretKey = setTimeout(function(){removeAlert()}, 5000);
          } else {
            if ($('.alert').length) {
              clearTimeout(notificationWrongSecretKey);
              notificationWrongSecretKey = setTimeout(function(){removeAlert()}, 5000);
            } else {
              createAlert(0, 'Wrong secret key or You have not added any records yet.');
              notificationWrongSecretKey = setTimeout(function(){removeAlert()}, 5000);
            }
          };
          $.each(res, function(i, val){
            if(!val.hasOwnProperty('id')) return true;
            let row = i + 1,
                id = val['id'],
                site = val['site'],
                hideLogin = val['hideLogin'],
                login = val['login'],
                encrypt_login = val['encrypt_login'],
                pass = val['pass'],
                attr = val['attribute'],
                flogin = val['field_login'],
                fpass = val['field_pass'];

            $('#tbody').append('<tr id="site-' + id + '"><td scope="row">' + row + '</td><td><img src="/web/image/icons/' + getHostname(site) + '.png" alt="">' + getHostname(site) + '</td><td>' + hideLogin + '</td><td><a class="btn btn-success" href="' + site + '?name=' + encrypt_login + '&pass=' + pass + '&attr=' + attr + ',' + flogin + ',' + fpass + '" target="_blank">L</a><a data-id="' + id + '" class="btn btn-danger">R</a><a data-id="' + id + '" data-site="' + site + '" data-login="' + login + '" data-flogin="' + flogin + '" data-fpass="' + fpass + '" data-attr="' + attr + '" class="btn btn-primary btn-update" data-toggle="modal" data-target="#myModal">U</a></td></tr>');
            sessionStorage.setItem('site-' + id, '{"id":"' + id + '","site":"' + site + '","hideLogin":"' + hideLogin + '","login":"' + login + '","encrypt_login":"' + encrypt_login + '","pass":"' + pass + '","attribute":"' + attr + '","field_login":"' + flogin + '","field_pass":"' + fpass + '"}');
          });
        },
        complete: function(){
          query = 1;
          $('#secret-key').val('');
          clearUpdateRemoveBtn();
          callUpdateRemoveBtn();
        },
        error: function(){
          console.log('Error!', window.location.pathname + 'site/index-json?key=' + $('#secret-key').val());
        }
      });
      return false;
    } else {
      return false;
    }
  });

  let send = 1;
  let updateMod;
  function callUpdateRemoveBtn() {
    let notificationUpdateRecord;
    $('.btn-update').on('click', function(){
      updateMod = 1;
      $('#service-update [type="submit"]').text('Update');
      $('#service-update').attr('data-id', $(this).attr('data-id'));
      $('#myModalLabel').text('Update record - ' + getHostname($(this).attr('data-site')) + '(' + $(this).attr('data-login') + ')');
      $('#services-site').val($(this).attr('data-site'));
      $('#services-login').val($(this).attr('data-login'));
      $('#services-field_login').val($(this).attr('data-flogin'));
      $('#services-field_pass').val($(this).attr('data-fpass'));
      $('#service-update [name *= attribute][value="0"]')[0].checked = false;
      $('#service-update [name *= attribute][value="1"]')[0].checked = false;
      $('#service-update [name *= attribute][value="' + $(this).attr('data-attr') + '"]')[0].checked = true;
      $('#service-update').on('beforeSubmit', function(){
        if (send && updateMod) {
          send = 0;
          let id = $(this).attr('data-id');
          let data = $(this).serialize();
          $.ajax({
            url: window.location.pathname + 'site/update?id=' + id,
            type: 'POST',
            data: data,
            success: function(res){
              let obj = JSON.parse(res);
              let id = obj.id,
                  site = obj.site,
                  hideLogin = obj.hideLogin,
                  login = obj.login,
                  encrypt_login = obj.encrypt_login,
                  pass = obj.pass,
                  attr = obj.attribute,
                  flogin = obj.field_login,
                  fpass = obj.field_pass;

              if ($('.alert').length) {
                clearTimeout(notificationUpdateRecord);
                notificationUpdateRecord = setTimeout(function(){removeAlert(); $('#site-' + id).removeClass('info');}, 5000);
              } else {
                createAlert(3, 'Record updated successfully!');
                $('#site-' + id).addClass('info');
                notificationUpdateRecord = setTimeout(function(){removeAlert(); $('#site-' + id).removeClass('info');}, 5000);
              }
              $('#site-' + id).children('td:nth-child(2)').html('<img src="/web/image/icons/' + getHostname(site) + '.png" alt="">' + getHostname(site));
              $('#site-' + id).children('td:nth-child(3)').text(hideLogin);
              $('#site-' + id).children('td:nth-child(4)').html('<td><a class="btn btn-success" href="' + site + '?name=' + encrypt_login + '&pass=' + pass + '&attr=' + attr + ',' + flogin + ',' + fpass + '" target="_blank">L</a><a data-id="' + id + '" class="btn btn-danger">R</a><a data-id="' + id + '" data-site="' + site + '" data-login="' + login + '" data-flogin="' + flogin + '" data-fpass="' + fpass + '" data-attr="' + attr + '" class="btn btn-primary btn-update" data-toggle="modal" data-target="#myModal">U</a></td>');
              sessionStorage['site-' + id] = res;
              $('#myModal').modal('hide');
              clearUpdateRemoveBtn();
              callUpdateRemoveBtn();
            },
            complete: function() {
              send = 1;
            },
            error: function() {
              alert('Error!');
            }
          });
        }
      return false;
      });
    });

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
              sessionStorage.removeItem('site-' + id);
              if (!checkSessionStorage()) {
                secretForm.css('display', 'block').prependTo(".container-main");
                emptyList.css('display', 'block').appendTo(".table-responsive");
              }
              if ($(".alert-success.alert").length){
                clearTimeout(notificationRemoveRecord);
                notificationRemoveRecord = setTimeout(function(){removeAlert()}, 5000);
              } else {
                createAlert(0, 'Record deleted!');
                notificationRemoveRecord = setTimeout(function(){removeAlert()}, 5000);
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
  }

  let createNewRecord = 1;
  let notificationCreateRecord;
  $('#create-new-record').on('click', function() {
    updateMod = 0;
    $('#service-update [type="submit"]').text('Create');
    $('#myModalLabel').text('Create new record');
    $('#service-update').on('beforeSubmit', function(){
      if (createNewRecord && !updateMod) {
        createNewRecord = 0;
        let data = $(this).serialize();
        $.ajax({
          url: window.location.pathname + 'site/services',
          type: 'POST',
          data: data,
          success: function(res){
            let obj = JSON.parse(res);
            let id = obj.id,
                site = obj.site,
                hideLogin = obj.hideLogin,
                login = obj.login,
                encrypt_login = obj.encrypt_login,
                pass = obj.pass,
                attr = obj.attribute,
                flogin = obj.field_login,
                fpass = obj.field_pass,
                row = + $('#tbody tr:last-child td:first-child').text() + 1;
                row = row > 0 ? row : 1;

            $('#tbody').append('<tr id="site-' + id + '"><td scope="row">' + row + '</td><td><img src="/web/image/icons/' + getHostname(site) + '.png" alt="">' + getHostname(site) + '</td><td>' + hideLogin + '</td><td><a class="btn btn-success" href="' + site + '?name=' + encrypt_login + '&pass=' + pass + '&attr=' + attr + ',' + flogin + ',' + fpass + '" target="_blank">L</a><a data-id="' + id + '" class="btn btn-danger">R</a><a data-id="' + id + '" data-site="' + site + '" data-login="' + login + '" data-flogin="' + flogin + '" data-fpass="' + fpass + '" data-attr="' + attr + '" class="btn btn-primary btn-update" data-toggle="modal" data-target="#myModal">U</a></td></tr>');
            if ($('.alert').length) {
              clearTimeout(notificationCreateRecord);
              notificationCreateRecord = setTimeout(function(){removeAlert(); $('#site-' + id).removeClass('success');}, 5000);
            } else {
              createAlert(1, 'Record successfully created!');
              $('#site-' + id).addClass('success');
              notificationCreateRecord = setTimeout(function(){removeAlert(); $('#site-' + id).removeClass('success');}, 5000);
            }
            sessionStorage['site-' + id] = res;
            $('#myModal').modal('hide');
            clearUpdateRemoveBtn();
            callUpdateRemoveBtn();
            $('#secret-form').remove();
            $('.empty-list').remove();
          },
          complete: function() {
            createNewRecord = 1;
          },
          error: function() {
            alert('Error!');
          }
        });
      }
    return false;
    });
  });

  $('#clear-storage').on('click', function() {
    sessionStorage.clear();
    window.location.href = '/';
  })

  $('#myModal').on('hidden.bs.modal', function (e) {
    $('#service-update')[0].reset();
  })

  function clearUpdateRemoveBtn() {
    $('.btn.btn-danger').off();
    $('.btn-update').off();
  }

  function getHostname(link) {
    let a = document.createElement('a');
    a.href = link;
    return a.hostname;
  }
  function checkSessionStorage() {
    return sessionStorage.length;
  }
  function createAlert(status, message) {
    let arrStatus = ['alert-danger', 'alert-success', 'alert-warning', 'alert-info'];
    $(".container-main").prepend( '<div class="' + arrStatus[status] + ' alert fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' + message + '</div>' );
  }
  function removeAlert() {
    $('.alert').remove();
  }
});
