$(document).ready(function(){

  let secretForm = $('#secret-form').clone();
  let emptyList = $('.empty-list').clone();
  let countRecords = $('#count-records');

  if (checkSessionStorage()) {
    $('#secret-form').remove();
    $('.empty-list').remove();
    let i = 1;
    for (key in sessionStorage) {
      if (typeof sessionStorage[key] === 'string' && sessionStorage[key].includes('site')) {
        let obj = JSON.parse(sessionStorage[key]);
        let row = i,
            id = obj.id,
            site = obj.site,
            hideLogin = obj.hideLogin,
            login = obj.login,
            pass = typeof obj.pass === 'string' ? JSON.parse(obj.pass) : obj.pass,
            length = pass[1],
            pkey = obj.public_key,
            attr = obj.attribute,
            flogin = obj.field_login,
            fpass = obj.field_pass;

        $('#tbody')
          .append('<tr id="site-' + id + '">'
            + '<td scope="row">' + row + '</td>'
            + '<td><img src="/web/image/icons/' + getHostname(site) + '.png" alt=""><a target="_blank" href="https://'
              + getHostname(site) + '">' + getHostname(site) + '</a></td>'
            + '<td>' + hideLogin + '</td>'
            + '<td>'
              + '<a class="btn btn-success" href="' + site + '?name=' + login + '&pass=' + pass[0] + '&pkey=' + pkey
                + '&l=' + length + '&attr=' + attr + ',' + flogin + ',' + fpass + '" target="_blank">L</a>'
              + '<a data-pass="' + JSON.stringify(pass[0]) + '" data-length="' + JSON.stringify(pass[1])
                + '" data-pkey="' + pkey + '" class="btn btn-warning btn-copy-pass">С</a>'
              + '<a data-id="' + id + '" class="btn btn-danger">R</a>'
              + '<a data-id="' + id + '" data-site="' + site + '" data-login="' + login + '" data-flogin="' + flogin
                + '" data-fpass="' + fpass + '" data-attr="' + attr + '" class="btn btn-primary btn-update" '
                + ' data-toggle="modal" data-target="#myModal">U</a>'
            + '</td></tr>');
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
      let data = $('#secret-form form').serialize();
      $.ajax({
        url: window.location.pathname + 'site/index-json',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(res){
          if(Array.isArray(res) && res.length) {
            $('#secret-form').remove();
            $('.empty-list').remove();

            $.each(res, function(i, val){
              if(!val.hasOwnProperty('id')) return true;
              let row = i + 1,
                  id = val['id'],
                  site = val['site'],
                  hideLogin = val['hideLogin'],
                  login = val['login'],
                  pass = JSON.parse(val['pass']),
                  length = pass[1],
                  pkey = val['public_key'],
                  attr = val['attribute'],
                  flogin = val['field_login'],
                  fpass = val['field_pass'];
              let obj = {
                  id: val['id'],
                  site: val['site'],
                  hideLogin: val['hideLogin'],
                  login: val['login'],
                  pass: JSON.parse(val['pass']),
                  length: pass[1],
                  public_key: val['public_key'],
                  attribute: val['attribute'],
                  field_login: val['field_login'],
                  field_pass: val['field_pass'],
                }
              $('#tbody')
                .append('<tr id="site-' + id + '">'
                  + '<td scope="row">' + row + '</td>'
                  + '<td><img src="/web/image/icons/' + getHostname(site) + '.png" alt=""><a target="_blank" '
                    + ' href="https://' + getHostname(site) + '">' + getHostname(site) + '</a></td>'
                  + '<td>' + hideLogin + '</td>'
                  + '<td>'
                    + '<a class="btn btn-success" href="' + site + '?name=' + login + '&pass=' + pass[0] + '&pkey='
                      + pkey + '&l=' + length + '&attr=' + attr + ',' + flogin + ',' + fpass + '" target="_blank">L</a>'
                    + '<a data-pass="' + JSON.stringify(pass[0]) + '" data-length="' + length
                      + '" data-pkey="' + pkey + '" class="btn btn-warning btn-copy-pass">С</a>'
                    + '<a data-id="' + id + '" class="btn btn-danger">R</a>'
                    + '<a data-id="' + id + '" data-site="' + site + '" data-login="' + login + '" data-flogin="'
                      + flogin + '" data-fpass="' + fpass + '" data-attr="' + attr + '" '
                      + 'class="btn btn-primary btn-update" data-toggle="modal" data-target="#myModal">U</a>'
                  + '</td></tr>');
              sessionStorage.setItem('site-' + id, JSON.stringify(obj));
            });

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
            };
          };
        },
        complete: function(){
          query = 1;
          $('#secret-key').val('');
          clearUpdateRemoveBtn();
          callUpdateRemoveBtn();
        },
        error: function(){
          console.log('Server error!');
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
      $('#myModalLabel')
        .text('Update record - ' + getHostname($(this).attr('data-site')) + '(' + $(this)
        .attr('data-login') + ')');
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
                  pass = obj.pass,
                  length = pass[1],
                  pkey = obj.public_key,
                  attr = obj.attribute,
                  flogin = obj.field_login,
                  fpass = obj.field_pass;

              $('#site-' + id).children('td:nth-child(2)')
                .html('<img src="/web/image/icons/' + getHostname(site)
                  + '.png" alt=""><a target="_blank" href="https://' + getHostname(site) + '">'
                  + getHostname(site) + '</a>');
              $('#site-' + id).children('td:nth-child(3)').text(hideLogin);
              $('#site-' + id)
                .children('td:nth-child(4)')
                .html('<a class="btn btn-success" href="' + site + '?name=' + login + '&pass=' + pass[0] + '&pkey='
                    + pkey + '&l=' + length + '&attr=' + attr + ',' + flogin + ',' + fpass + '" target="_blank">L</a>'
                  + '<a data-pass="' + JSON.stringify(pass[0]) + '" data-length="' + length
                    + '" data-pkey="' + pkey + '" class="btn btn-warning btn-copy-pass">С</a>'
                  + '<a data-id="' + id + '" class="btn btn-danger">R</a>'
                  + '<a data-id="' + id + '" data-site="' + site + '" data-login="' + login + '" data-flogin="'
                    + flogin + '" data-fpass="' + fpass + '" data-attr="' + attr + '" class="btn btn-primary '
                    + ' btn-update" data-toggle="modal" data-target="#myModal">U</a>');
              sessionStorage['site-' + id] = res;

              if ($('.alert').length) {
                clearTimeout(notificationUpdateRecord);
                notificationUpdateRecord = setTimeout(function() {
                  removeAlert(); $('#site-' + id).removeClass('info');
                }, 5000);
              } else {
                createAlert(3, 'Record updated successfully!');
                $('#site-' + id).addClass('info');
                notificationUpdateRecord = setTimeout(function() {
                  removeAlert(); $('#site-' + id).removeClass('info');
                }, 5000);
              }
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
              if ($(".alert-danger.alert").length){
                clearTimeout(notificationRemoveRecord);
                notificationRemoveRecord = setTimeout(function(){removeAlert()}, 5000);
              } else {
                createAlert(0, 'Record deleted!');
                notificationRemoveRecord = setTimeout(function(){removeAlert()}, 5000);
              }
            }
            countRecords.text(+ $('#count-records').text() - 1);
          },
          error: function(){
            console.log('Error!');
          }
        });
        return false;
      };
    });

    $('.btn-copy-pass').on('click', function() {
      const self = this;
      const myElement = document.createElement('div');
      myElement.style.cssText = 'position:fixed;top:0;right:0;left:0;padding:15px 0;background:black;text-align:center;'
        + 'z-index:1000000;box-sizing:border-box;';
      myElement.setAttribute('id', 'secret-key-bar');
      const input = document.createElement('input');
      input.setAttribute('type', 'password');
      input.style.cssText = 'display:inline-block;width:210px;height:34px;padding:6px 12px;font-size:14px;line-height:1;'
        + 'color:rgb(85,85,85);background-color:rgb(255,255,255);background-image:none;border:1px solid rgb(204,204,204);'
        + 'border-radius:4px;margin-left:50px;box-sizing:border-box;font-family:sans-serif!important;';
      input.setAttribute('type', 'password');
      input.setAttribute('placeholder', 'Please enter your secret key');
      input.addEventListener('change', function change() {
        const key = this.value;
        const pass = $(self).attr('data-pass').slice(1,-1).split(',');
        const publicKey = $(self).attr('data-pkey');
        const l = $(self).attr('data-length');
        const privateKey = (key + publicKey).substr(0, 32).split('');
        const privateKeyCharCode = [];
        for (let i = 0; i < privateKey.length; i += 1) {
          privateKeyCharCode.push(privateKey[i].charCodeAt());
        }
        AES_ExpandKey(privateKeyCharCode);
        AES_Decrypt(pass, privateKeyCharCode);
        function bin2String(array) {
          return String.fromCharCode(...array);
        }
        const fieldCopyText = document.createElement('input');
        fieldCopyText.setAttribute('value', bin2String(pass.slice(0, l)));
        document.querySelector('body').appendChild(fieldCopyText);
        fieldCopyText.select();
        document.execCommand('copy');
        fieldCopyText.remove();
        document.querySelector('#secret-key-bar').remove();
        createAlert(2, 'Success copy password to buffer!');
        setTimeout(removeAlert, 5000);
      });
      const close = document.createElement('span');
      close.style.cssText = 'display:inline-block;padding:7px 9.7px;margin-left:10px;border:2px solid white;'
        + 'border-radius:3px;color:white;font-weight:700;cursor:pointer;box-sizing:border-box;line-height:1;'
        + 'font-family:sans-serif!important;font-size:16px;';
      close.innerText = 'X';
      close.addEventListener('click', () => {
        document.querySelector('#secret-key-bar').remove();
      });
      myElement.appendChild(input);
      myElement.appendChild(close);
      document.querySelector('body').appendChild(myElement);
      input.focus();
    });

    // mobile script start
    let lastActiveId;
    $('#tbody tr').on('click', function() {
      let currentActiveId = $(this).attr('id');
      $('#tbody tr').removeClass('active');
      if (lastActiveId !== currentActiveId) {
        $(this).addClass('active');
        lastActiveId = currentActiveId;
      } else {
        lastActiveId = 0;
      };
    });
    // mobile script end
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
                pass = obj.pass,
                length = pass[1],
                attr = obj.attribute,
                flogin = obj.field_login,
                fpass = obj.field_pass,
                pkey = obj.public_key,
                row = + $('#tbody tr:last-child td:first-child').text() + 1;

            row = row > 0 ? row : 1;

            $('#tbody')
              .append('<tr id="site-' + id + '">'
                + '<td scope="row">' + row + '</td>'
                + '<td><img src="/web/image/icons/' + getHostname(site) + '.png" alt="">'
                  + '<a target="_blank" href="https://' + getHostname(site) + '">' + getHostname(site) + '</a></td>'
                + '<td>' + hideLogin + '</td>'
                + '<td>'
                  + '<a class="btn btn-success" href="' + site + '?name=' + login + '&pass=' + pass[0] + '&pkey='
                    + pkey + '&l=' + length + '&attr=' + attr + ',' + flogin + ',' + fpass + '" target="_blank">L</a>'
                  + '<a data-pass="' + JSON.stringify(pass[0]) + '" data-length="' + length
                    + '" data-pkey="' + pkey + '" class="btn btn-warning btn-copy-pass">С</a>'
                  + '<a data-id="' + id + '" class="btn btn-danger">R</a>'
                  + '<a data-id="' + id + '" data-site="' + site + '" data-login="' + login + '" data-flogin="'
                    + flogin + '" data-fpass="' + fpass + '" data-attr="' + attr + '" '
                    + 'class="btn btn-primary btn-update" data-toggle="modal" data-target="#myModal">U</a>'
                + '</td></tr>');
            if ($('.alert').length) {
              clearTimeout(notificationCreateRecord);
              notificationCreateRecord = setTimeout(function() {
                removeAlert(); $('#site-' + id).removeClass('success');
              }, 5000);
            } else {
              createAlert(1, 'Record successfully created!');
              $('#site-' + id).addClass('success');
              notificationCreateRecord = setTimeout(function() {
                removeAlert(); $('#site-' + id).removeClass('success');
              }, 5000);
            }
            sessionStorage['site-' + id] = res;
            $('#myModal').modal('hide');
            clearUpdateRemoveBtn();
            callUpdateRemoveBtn();
            $('#secret-form').remove();
            $('.empty-list').remove();
            countRecords.text(+ $('#count-records').text() + 1);
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

  let createNewGoogleAuth = 1;
  let notificationGoogleAuth;
  let notificationGoogleAuthError;
  $('#google-auth').on('beforeSubmit', function(){
    if (createNewGoogleAuth) {
      createNewGoogleAuth = 0;
      let data = $(this).serialize();
      $.ajax({
        url: window.location.pathname + 'site/google-authenticator',
        type: 'POST',
        data: data,
        success: function(res){
          $('#google-auth-modal').modal('hide');
          if(+res) {
            $('body').removeClass('modal-open');
            $('.modal-backdrop.fade.in').remove();
            $('#google-auth-modal').remove();
            $('#add-google-auth').remove();
            if ($('.alert.alert-success').length) {
              clearTimeout(notificationGoogleAuth);
              notificationGoogleAuth = setTimeout(function(){ removeAlert(); }, 5000);
            } else {
              createAlert(1, 'Successfully changed google authenticator!');
              notificationGoogleAuth = setTimeout(function(){ removeAlert(); }, 5000);
            }
          } else {
            if ($('.alert.alert-danger').length) {
              clearTimeout(notificationGoogleAuthError);
              notificationGoogleAuthError = setTimeout(function(){ removeAlert(); }, 5000);
            } else {
              createAlert(0, 'Error record, google authenticator no changed!');
              notificationGoogleAuthError = setTimeout(function(){ removeAlert(); }, 5000);
            }
          }
        },
        complete: function() {
          createNewGoogleAuth = 1;
        },
        error: function() {
          alert('Error!');
        }
      });
    }
    return false;
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
    $('.btn-copy-pass').off();
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
    $(".container-main").prepend( '<div class="' + arrStatus[status] + ' alert fade in"><button type="button" '
      + 'class="close" data-dismiss="alert" aria-hidden="true">×</button>' + message + '</div>' );
  }
  function removeAlert() {
    $('.alert').remove();
  }

  $('.toolbar .fas').on('click', function() {
    $('.toolbar-modal').toggleClass('active');
    $(this).parent().toggleClass('active');
  });

  // copy-pass start
  const AES_Sbox = [99, 124, 119, 123, 242, 107, 111, 197, 48, 1, 103, 43, 254, 215, 171,
    118, 202, 130, 201, 125, 250, 89, 71, 240, 173, 212, 162, 175, 156, 164, 114, 192, 183, 253,
    147, 38, 54, 63, 247, 204, 52, 165, 229, 241, 113, 216, 49, 21, 4, 199, 35, 195, 24, 150, 5, 154,
    7, 18, 128, 226, 235, 39, 178, 117, 9, 131, 44, 26, 27, 110, 90, 160, 82, 59, 214, 179, 41, 227,
    47, 132, 83, 209, 0, 237, 32, 252, 177, 91, 106, 203, 190, 57, 74, 76, 88, 207, 208, 239, 170,
    251, 67, 77, 51, 133, 69, 249, 2, 127, 80, 60, 159, 168, 81, 163, 64, 143, 146, 157, 56, 245,
    188, 182, 218, 33, 16, 255, 243, 210, 205, 12, 19, 236, 95, 151, 68, 23, 196, 167, 126, 61,
    100, 93, 25, 115, 96, 129, 79, 220, 34, 42, 144, 136, 70, 238, 184, 20, 222, 94, 11, 219, 224,
    50, 58, 10, 73, 6, 36, 92, 194, 211, 172, 98, 145, 149, 228, 121, 231, 200, 55, 109, 141, 213,
    78, 169, 108, 86, 244, 234, 101, 122, 174, 8, 186, 120, 37, 46, 28, 166, 180, 198, 232, 221,
    116, 31, 75, 189, 139, 138, 112, 62, 181, 102, 72, 3, 246, 14, 97, 53, 87, 185, 134, 193, 29,
    158, 225, 248, 152, 17, 105, 217, 142, 148, 155, 30, 135, 233, 206, 85, 40, 223, 140, 161,
    137, 13, 191, 230, 66, 104, 65, 153, 45, 15, 176, 84, 187, 22];
  const AES_ShiftRowTab = [0, 5, 10, 15, 4, 9, 14, 3, 8, 13, 2, 7, 12, 1, 6, 11];

  const AES_Sbox_Inv = new Array(256);
  for (let i = 0; i < 256; i += 1) { AES_Sbox_Inv[AES_Sbox[i]] = i; }

  const AES_ShiftRowTab_Inv = new Array(16);
  for (let i = 0; i < 16; i += 1) { AES_ShiftRowTab_Inv[AES_ShiftRowTab[i]] = i; }

  const AES_xtime = new Array(256);
  for (let i = 0; i < 128; i += 1) {
    AES_xtime[i] = i << 1;
    AES_xtime[128 + i] = (i << 1) ^ 0x1b;
  }

  function AES_ExpandKey(key) {
    const kl = key.length;
    let Rcon = 1;
    let ks;
    switch (kl) {
      case 16: ks = 16 * (10 + 1); break;
      case 24: ks = 16 * (12 + 1); break;
      case 32: ks = 16 * (14 + 1); break;
      default:
        console.log('AES_ExpandKey: Only key lengths of 16, 24 or 32 bytes allowed!');
    }
    for (let i = kl; i < ks; i += 4) {
      let temp = key.slice(i - 4, i);
      const k = key;
      if (i % kl === 0) {
        temp = [AES_Sbox[temp[1]] ^ Rcon, AES_Sbox[temp[2]], AES_Sbox[temp[3]], AES_Sbox[temp[0]]];
        Rcon <<= 1;
        if (Rcon >= 256) { Rcon ^= 0x11b; }
      } else if ((kl > 24) && (i % kl === 16)) {
        temp = [AES_Sbox[temp[0]], AES_Sbox[temp[1]], AES_Sbox[temp[2]], AES_Sbox[temp[3]]];
      }
      for (let j = 0; j < 4; j += 1) { k[i + j] = key[i + j - kl] ^ temp[j]; }
    }
  }
  function AES_AddRoundKey(state, rkey) {
    const s = state;
    for (let i = 0; i < 16; i += 1) { s[i] ^= rkey[i]; }
  }
  function AES_ShiftRows(state, shifttab) {
    const s = state;
    const h = [].concat(state);
    for (let i = 0; i < 16; i += 1) { s[i] = h[shifttab[i]]; }
  }
  function AES_SubBytes(state, sbox) {
    const s = state;
    for (let i = 0; i < 16; i += 1) { s[i] = sbox[s[i]]; }
  }
  function AES_MixColumns_Inv(state) {
    const s = state;
    for (let i = 0; i < 16; i += 4) {
      const s0 = s[i + 0];
      const s1 = s[i + 1];
      const s2 = s[i + 2];
      const s3 = s[i + 3];
      const h = s0 ^ s1 ^ s2 ^ s3;
      const xh = AES_xtime[h];
      const h1 = AES_xtime[AES_xtime[xh ^ s0 ^ s2]] ^ h;
      const h2 = AES_xtime[AES_xtime[xh ^ s1 ^ s3]] ^ h;
      s[i + 0] ^= h1 ^ AES_xtime[s0 ^ s1];
      s[i + 1] ^= h2 ^ AES_xtime[s1 ^ s2];
      s[i + 2] ^= h1 ^ AES_xtime[s2 ^ s3];
      s[i + 3] ^= h2 ^ AES_xtime[s3 ^ s0];
    }
  }
  function AES_Decrypt(block, key) {
    const l = key.length;
    AES_AddRoundKey(block, key.slice(l - 16, l));
    AES_ShiftRows(block, AES_ShiftRowTab_Inv);
    AES_SubBytes(block, AES_Sbox_Inv);
    for (let i = l - 32; i >= 16; i -= 16) {
      AES_AddRoundKey(block, key.slice(i, i + 16));
      AES_MixColumns_Inv(block);
      AES_ShiftRows(block, AES_ShiftRowTab_Inv);
      AES_SubBytes(block, AES_Sbox_Inv);
    }
    AES_AddRoundKey(block, key.slice(0, 16));
  }
  // copy-pass end
});