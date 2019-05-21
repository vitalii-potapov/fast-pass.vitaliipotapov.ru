<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
        NavBar::begin([
            'brandLabel' => Yii::$app->name,
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-inverse navbar-fixed-top',
            ],
        ]);
        if (Yii::$app->params['REST_API_MOD']) {
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'login', 'url' => ['/site/login']];
            } else {
                $menuItems = [
                    ['label' => 'Home', 'url' => ['/site/index']],
                ];
                $menuItems[] = '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        'logout (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>';
            }
        } else {
            if (Yii::$app->user->isGuest) {
                $menuItems[] = ['label' => 'login', 'url' => ['/site/login']];
            } else {
                $menuItems = [
                    ['label' => 'Home', 'url' => ['/site/index']],
                    ['label' => 'Services', 'url' => ['/site/services']],
                ];
                $menuItems[] = '<li>'
                    . Html::beginForm(['/site/logout'], 'post')
                    . Html::submitButton(
                        'logout (' . Yii::$app->user->identity->username . ')',
                        ['class' => 'btn btn-link logout']
                    )
                    . Html::endForm()
                    . '</li>';
            }
        }
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => $menuItems,
        ]);
        NavBar::end();
    ?>

    <?php if (Yii::$app->params['REST_API_MOD'] && !Yii::$app->user->isGuest) : ?>
        <div class="toolbar">
            <i class="fas fa-cog"></i>
            <div class="container width-sm-100">
                <a id="create-new-record" data-toggle="modal" data-target="#myModal">Create New Record</a>
                <a title='Move the "Fast-pass" to your panel bookmarks' href="javascript:(function(){const a=document.createElement('div');a.style.cssText='position:fixed;top:0;right:0;left:0;padding:15px 0;background:black;text-align:center;z-index:1000000;box-sizing:border-box;',a.setAttribute('id','secret-key-bar');const b=document.createElement('input');b.setAttribute('type','password'),b.style.cssText='display:inline-block;width:210px;height:34px;padding:6px 12px;font-size:14px;line-height:1;color:rgb(85,85,85);background-color:rgb(255,255,255);background-image:none;border:1px solid rgb(204,204,204);border-radius:4px;margin-left:50px;box-sizing:border-box;font-family:sans-serif!important;',b.setAttribute('type','password'),b.setAttribute('placeholder','Please enter your secret key'),b.addEventListener('change',function(){let a=this.value,b=a.split(''),c=0,d=[],e=[];for(let a=0;a<b.length;a++)c+=b[a].charCodeAt();const f=window.location.search,g=f.substring(5,f.indexOf('&pass')).slice(1).split(','),h=f.substring(f.indexOf('&pass'),f.indexOf('&attr')).slice(6).split(',');for(let a=0;a<g.length;a++)d.push(String.fromCharCode(g[a]-c));for(let a=0;a<h.length;a++)e.push(String.fromCharCode(h[a]-c));const i=f.substring(f.indexOf('&attr')).slice(6).split(','),j=i[1],k=i[2];0==i[0]?(console.log(document.querySelector(`[name='${j}']`)),console.log(document.querySelector(`[name='${k}']`)),document.querySelector(`[name='${j}']`).value=d.join(''),document.querySelector(`[name='${k}']`).value=e.join(''),document.querySelector(`[name='${k}']`).form.submit()):(document.querySelector('#'+j).value=d.join(''),document.querySelector('#'+k).value=e.join(''),document.querySelector('#'+k).form.submit())});const c=document.createElement('span');c.style.cssText='display:inline-block;padding:7px 9.7px;margin-left:10px;border:2px solid white;border-radius:3px;color:white;font-weight:700;cursor:pointer;box-sizing:border-box;line-height:1;font-family:sans-serif!important;font-size:16px;',c.innerText='X',c.addEventListener('click',function(){document.querySelector('#secret-key-bar').remove()}),a.appendChild(b),a.appendChild(c),document.querySelector('body').appendChild(a),b.focus()})();">Script Fast Password(Submit)</a>
                <a title='Move the "Fast-pass" to your panel bookmarks' href="javascript:(function(){const a=document.createElement('div');a.style.cssText='position:fixed;top:0;right:0;left:0;padding:15px 0;background:black;text-align:center;z-index:1000000;box-sizing:border-box;',a.setAttribute('id','secret-key-bar');const b=document.createElement('input');b.setAttribute('type','password'),b.style.cssText='display:inline-block;width:210px;height:34px;padding:6px 12px;font-size:14px;line-height:1;color:rgb(85,85,85);background-color:rgb(255,255,255);background-image:none;border:1px solid rgb(204,204,204);border-radius:4px;margin-left:50px;box-sizing:border-box;font-family:sans-serif!important;',b.setAttribute('type','password'),b.setAttribute('placeholder','Please enter your secret key'),b.addEventListener('change',function(){let a=this.value,b=a.split(''),c=0,d=[],e=[];for(let a=0;a<b.length;a++)c+=b[a].charCodeAt();const f=window.location.search,g=f.substring(5,f.indexOf('&pass')).slice(1).split(','),h=f.substring(f.indexOf('&pass'),f.indexOf('&attr')).slice(6).split(',');for(let a=0;a<g.length;a++)d.push(String.fromCharCode(g[a]-c));for(let a=0;a<h.length;a++)e.push(String.fromCharCode(h[a]-c));const i=f.substring(f.indexOf('&attr')).slice(6).split(','),j=i[1],k=i[2];0==i[0]?(console.log(document.querySelector(`[name='${j}']`)),console.log(document.querySelector(`[name='${k}']`)),document.querySelector(`[name='${j}']`).value=d.join(''),document.querySelector(`[name='${k}']`).value=e.join('')):(document.querySelector('#'+j).value=d.join(''),document.querySelector('#'+k).value=e.join(''))});const c=document.createElement('span');c.style.cssText='display:inline-block;padding:7px 9.7px;margin-left:10px;border:2px solid white;border-radius:3px;color:white;font-weight:700;cursor:pointer;box-sizing:border-box;line-height:1;font-family:sans-serif!important;font-size:16px;',c.innerText='X',c.addEventListener('click',function(){document.querySelector('#secret-key-bar').remove()}),a.appendChild(b),a.appendChild(c),document.querySelector('body').appendChild(a),b.focus()})();">Script Fast Password</a>
                <a title='sessionStorage.clear()' id="clear-storage">Clear Session Storage</a>
            </div>
        </div>
    <?php endif ?>

    <div class="container container-main width-sm-100">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <a href="https://vitaliipotapov.ru/" target="_blank">VitaliiPotapov.ru</a> <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
