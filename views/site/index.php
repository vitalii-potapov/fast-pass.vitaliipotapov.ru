<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
use app\models\Services;
use yii\bootstrap\ActiveForm;

$this->title = 'My Yii Application';

function changeCharASCII(&$item, $key, $add = null) {
  $item = ord($item);
  $item += $add;
}
function hideLogin($login) {
  if($login) {
    $login_start = strstr($login, '@', true) ? strstr($login, '@', true) : $login;
    $login_end = strstr($login, '@');
    $login = substr_replace($login_start, '*****', 3, -2) . $login_end;
    return $login;
  }
}
$icons = [
  'github.com' => 'https://github.githubassets.com/favicon.ico',
  'auth.jino.ru' => 'https://www.jino.ru/static/icons/red/apple-touch-icon-76x76.png',
  'omsk.hh.ru' => 'https://i.hh.ru/apple/hh/touch-icon-ipad.png',
  'moikrug.ru' => 'https://moikrug.ru/favicon.ico',
  'vk.com' => 'https://vk.com/images/safari_76.png?1',
];
?>

<div class="site-index">
  <div class="row">
    <div class="aside-content col-xs-2">
      <a href="javascript:(function(){const a=document.createElement('div');a.style.cssText='position:fixed;top:0;right:0;left:0;padding:15px 0;background:black;text-align:center;z-index:1000000;box-sizing:border-box;',a.setAttribute('id','secret-key-bar');const b=document.createElement('input');b.setAttribute('type','password'),b.style.cssText='display:inline-block;width:210px;height:34px;padding:6px 12px;font-size:14px;line-height:1;color:rgb(85,85,85);background-color:rgb(255,255,255);background-image:none;border:1px solid rgb(204,204,204);border-radius:4px;margin-left:50px;box-sizing:border-box;font-family:sans-serif!important;',b.setAttribute('type','password'),b.setAttribute('placeholder','Please enter your secret key'),b.addEventListener('change',function(){let a=this.value,b=a.split(''),c=0,d=[],e=[];for(let a=0;a<b.length;a++)c+=b[a].charCodeAt();const f=window.location.search,g=f.substring(5,f.indexOf('&pass')).slice(1).split(','),h=f.substring(f.indexOf('&pass'),f.indexOf('&attr')).slice(6).split(',');for(let a=0;a<g.length;a++)d.push(String.fromCharCode(g[a]-c));for(let a=0;a<h.length;a++)e.push(String.fromCharCode(h[a]-c));const i=f.substring(f.indexOf('&attr')).slice(6).split(','),j=i[1],k=i[2];0==i[0]?(console.log(document.querySelector(`[name='${j}']`)),console.log(document.querySelector(`[name='${k}']`)),document.querySelector(`[name='${j}']`).value=d.join(''),document.querySelector(`[name='${k}']`).value=e.join(''),document.querySelector(`[name='${k}']`).form.submit()):(document.querySelector('#'+j).value=d.join(''),document.querySelector('#'+k).value=e.join(''),document.querySelector('#'+k).form.submit())});const c=document.createElement('span');c.style.cssText='display:inline-block;padding:7px 9.7px;margin-left:10px;border:2px solid white;border-radius:3px;color:white;font-weight:700;cursor:pointer;box-sizing:border-box;line-height:1;font-family:sans-serif!important;font-size:16px;',c.innerText='X',c.addEventListener('click',function(){document.querySelector('#secret-key-bar').remove()}),a.appendChild(b),a.appendChild(c),document.querySelector('body').appendChild(a),b.focus()})();">
        Fast-pass
      </a>
      <!-- ====== Исходный код javascirpta start ======
      javascript:(function(){
        const myElement = document.createElement('div');
        myElement.style.cssText = 'position:fixed;top:0;right:0;left:0;padding:15px 0;background:black;text-align:center;z-index:1000000;box-sizing:border-box;';
        myElement.setAttribute('id', 'secret-key-bar');
        const input = document.createElement('input');
        input.setAttribute('type', 'password');
        input.style.cssText = 'display:inline-block;width:210px;height:34px;padding:6px 12px;font-size:14px;line-height:1;color:rgb(85,85,85);background-color:rgb(255,255,255);background-image:none;border:1px solid rgb(204,204,204);border-radius:4px;margin-left:50px;box-sizing:border-box;font-family:sans-serif!important;';
        input.setAttribute('type', 'password');
        input.setAttribute('placeholder', 'Please enter your secret key');
        input.addEventListener('change', function(){
          let key = this.value,
              arrKey=key.split(''),
              summKey=0,
              arrlog=[],
              arrpass=[];
          for(let i=0;i<arrKey.length;i++){summKey+=arrKey[i].charCodeAt()};
          const s=window.location.search,
              login=s.substring(5,s.indexOf('&pass')).slice(1).split(','),
              pass=s.substring(s.indexOf('&pass'),s.indexOf('&attr')).slice(6).split(',');
          for(let i=0;i<login.length;i++){arrlog.push(String.fromCharCode(login[i]-summKey))};
          for(let i=0;i<pass.length;i++){arrpass.push(String.fromCharCode(pass[i]-summKey))};
          const attr=s.substring(s.indexOf('&attr')).slice(6).split(','),
              field_login=attr[1],
              field_pass=attr[2];
          if(attr[0]==0){
            console.log(document.querySelector(`[name='${field_login}']`));
            console.log(document.querySelector(`[name='${field_pass}']`));
            document.querySelector(`[name='${field_login}']`).value=arrlog.join('');
            document.querySelector(`[name='${field_pass}']`).value=arrpass.join('');
            document.querySelector(`[name='${field_pass}']`).form.submit()
          }else{
            document.querySelector('#'+field_login).value=arrlog.join('');
            document.querySelector('#'+field_pass).value=arrpass.join('');
            document.querySelector('#'+field_pass).form.submit()
          }
        });
        const close = document.createElement('span');
        close.style.cssText = 'display:inline-block;padding:7px 9.7px;margin-left:10px;border:2px solid white;border-radius:3px;color:white;font-weight:700;cursor:pointer;box-sizing:border-box;line-height:1;font-family:sans-serif!important;font-size:16px;';
        close.innerText = 'X';
        close.addEventListener('click', function(){
          document.querySelector('#secret-key-bar').remove();
        });
        myElement.appendChild(input);
        myElement.appendChild(close);
        document.querySelector('body').appendChild(myElement);
        input.focus();
      }());
      ====== Исходный код javascirpta end ====== -->
      <br>
      <small>Move the "Fast-pass" to your panel bookmarks</small>
    </div>
    <div class="body-content col-xs-8">

      <form method="post">
        <input type="hidden" name="_csrf" value="a9vqp0C_8lcOQSK3WcG77balpWnHFIrMTouGL_bQ5DEEgYXkJ9S3OlwZQd0BiZaf39DzG4Qi66cB6cVom-O2fg==">
        <input class="form-control" id="secretKey" name="secretKey" type="password" onchange="this.form.submit()" placeholder="Please enter your secret key">
      </form>

      <table class="table">
        <thead>
          <tr>
            <th width='30px' scope="col">#</th>
            <th scope="col">site</th>
            <th scope="col">login</th>
            <th width='1px' scope="col" style="text-align: center;white-space: nowrap;"><span>L</span>ink / Remove / Update</th>
          </tr>
        </thead>
        <tbody>
          <?php if(isset($data)) : ?>
            <?php foreach($data as $key => $s) :
              $key += 1;
              $secKey = 'A' . $userKey . strtotime(date($s->date_create));
              $login = Yii::$app->getSecurity()->decryptByPassword(base64_decode($s->login), $secKey);
              $pass = Yii::$app->getSecurity()->decryptByPassword(base64_decode($s->pass), $secKey);
              $auth = Yii::$app->getSecurity()->decryptByPassword(base64_decode($s->site), $secKey);

              $site = parse_url($auth, PHP_URL_HOST);
              $l_userKey = str_split($userKey);

              array_walk($l_userKey, 'changeCharASCII');
              $l_userKey = array_sum($l_userKey);

              $l_login = str_split($login);
              array_walk($l_login, 'changeCharASCII', $l_userKey);
              $l_login = implode(',', $l_login);

              $l_pass = str_split($pass);
              array_walk($l_pass, 'changeCharASCII', $l_userKey);
              $l_pass = implode(',', $l_pass);
              ?>
              <tr>
                <th scope="row"><?=$key?></th>
                <td class="field-site"><img src="<?=$icons[$site]?>" alt=""><?=$site?></td>
                <td class="field-login"><?=hideLogin($login)?></td>
                <td style="
                    display: flex;
                    justify-content: space-around;
                    <?=$key === 1 ? 'margin-top: -1px;' : null ?>
                  ">
                  <a
                    class="btn btn-success"
                    href="<?=$auth?>?name=<?=$l_login?>&pass=<?=$l_pass?>&attr=<?=$s->attribute?>,<?=$s->field_login?>,<?=$s->field_pass?>"
                    target="_blank">L
                  </a>
                  <a data-id="<?=$s->id?>" class="btn btn-danger">R</a>
                  <a
                    data-id="<?=$s->id?>"
                    data-site="<?=$auth?>"
                    data-login="<?=$login?>"
                    data-flogin="<?=$s->field_login?>"
                    data-fpass="<?=$s->field_pass?>"
                    data-attr="<?=$s->attribute?>"
                    class="btn btn-primary btn-update"
                    data-toggle="modal"
                    data-target="#myModal">U
                  </a>
                </td>
              </tr>
            <?php endforeach ?>
          <?php endif?>
        </tbody>
      </table>

    </div>
  </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel">Название модали</h4>
      </div>
      <?php
        $model = new Services;
        $form = ActiveForm::begin([
          'id' => 'service-update',
          'layout' => 'horizontal',
      ]); ?>
      <div class="modal-body">

          <?= $form->field($model, 'secretKey')->passwordInput(['autofocus' => true])->label('Secret Key') ?>
          <?= $form->field($model, 'site')->textInput() ?>
          <?= $form->field($model, 'login')->textInput() ?>
          <?= $form->field($model, 'pass')->passwordInput() ?>
          <?= $form->field($model, 'field_login')->textInput() ?>
          <?= $form->field($model, 'field_pass')->textInput() ?>
          <?= $form->field($model, 'attribute')->radioList([0 => 'name', 1 => 'id']) ?>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <?= Html::submitButton('Update', ['class' => 'btn btn-primary', 'name' => 'add-button']) ?>
      </div>
      <?php ActiveForm::end(); ?>
    </div>
  </div>
</div>

<?php

$js = <<<JS
  $('.btn.btn-danger').on('click', function(){
    let answer=confirm('Do you really want to delete the entry?','');

    if(answer) {
      let id = $(this).attr('data-id');

      $.ajax({
        url: window.location.pathname + 'site/remove?id=' + id,
        type: 'POST',
        success: function(res){
          $('[data-id="'+id+'"]').closest('tr').remove();
          $( ".container-main" ).prepend( '<div class="alert-success alert fade in"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Record deleted!</div>' );
          setTimeout(() => {
            $('.alert-success.alert').remove();
          }, 5000);
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



JS;

$this->registerJs($js);
?>