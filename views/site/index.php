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

?>

<div class="site-index">
  <div class="row">
    <div class="aside-content col-xs-2">
      <a href="javascript:(function(){let key=prompt('Ключ?',''),arrKey=key.split(''),summKey=0,arrlog=[],arrpass=[];for(let i=0;i<arrKey.length;i++){summKey+=arrKey[i].charCodeAt()} let s=window.location.search,login=s.substring(5,s.indexOf('&pass')).slice(1).split(','),pass=s.substring(s.indexOf('&pass'),s.indexOf('&attr')).slice(6).split(',');for(let i=0;i<login.length;i++){arrlog.push(String.fromCharCode(login[i]-summKey))} for(let i=0;i<pass.length;i++){arrpass.push(String.fromCharCode(pass[i]-summKey))} let attr=s.substring(s.indexOf('&attr')).slice(6).split(','),field_login=attr[1],field_pass=attr[2];if(attr[0]==0){document.querySelector(`[name='${field_login}']`).value=arrlog.join('');document.querySelector(`[name='${field_pass}']`).value=arrpass.join('');document.querySelector(`[name='${field_pass}']`).form.submit()}else{document.querySelector('#'+field_login).value=arrlog.join('');document.querySelector(''+field_pass).value=arrpass.join('');document.querySelector(''+field_pass).form.submit()}}())">
        Fast-pass
      </a>
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
                <td><?=$site?></td>
                <td><?=$login?></td>
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
                  <a data-id=<?=$s->id?> class="btn btn-danger">R</a>
                  <a data-id=<?=$s->id?> class="btn btn-primary btn-update" data-toggle="modal" data-target="#myModal">U</a>
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
    $('#service-update').attr('action', 'site/update?id='+$(this).attr('data-id'));
  });



JS;

$this->registerJs($js);
?>