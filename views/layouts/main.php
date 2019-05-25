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
<!-- Copyright (c) Vitalii Potapov | https://quick-login.vitaliipotapov.ru/ -->
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
        <div class="toolbar-modal" tabindex="-1">
            <div class="toolbar">
                <i class="fas fa-cog"></i>
                <div class="container width-sm-100">
                    <a id="create-new-record" data-toggle="modal" data-target="#myModal">Create New Record</a>
                    <a id="add-google-auth" data-toggle="modal" data-target="#google-auth-modal">Add Google Authenticator</a>
                    <!-- You can view the source code "quick login" buttons the path to the file web/js/quick-login -->
                    <a title='Move the "Quick login" to your panel bookmarks' href="javascript:(function(){function a(a){const b=a.length;let c,d=1;16===b?c=176:24===b?c=208:32===b?c=240:console.log('AES_ExpandKey: Only key lengths of 16, 24 or 32 bytes allowed!');for(let e,f=b;f<c;f+=4){e=a.slice(f-4,f);0==f%b?(e=[g[e[1]]^d,g[e[2]],g[e[3]],g[e[0]]],d<<=1,256<=d&&(d^=283)):24<b&&16==f%b&&(e=[g[e[0]],g[e[1]],g[e[2]],g[e[3]]]);for(let c=0;4>c;c+=1)a[f+c]=a[f+c-b]^e[c]}}function b(a,b){for(let c=0;16>c;c+=1)a[c]^=b[c]}function c(a,b){const c=[].concat(a);for(let d=0;16>d;d+=1)a[d]=c[b[d]]}function d(a,b){const c=a;for(let d=0;16>d;d+=1)c[d]=b[c[d]]}function e(a){const b=a;for(let c=0;16>c;c+=4){const a=b[c+0],d=b[c+1],e=b[c+2],f=b[c+3],g=a^d^e^f,h=l[g],i=l[l[h^a^e]]^g,j=l[l[h^d^f]]^g;b[c+0]^=i^l[a^d],b[c+1]^=j^l[d^e],b[c+2]^=i^l[e^f],b[c+3]^=j^l[f^a]}}function f(a,f){const g=f.length;b(a,f.slice(g-16,g)),c(a,k),d(a,j);for(let h=g-32;16<=h;h-=16)b(a,f.slice(h,h+16)),e(a),c(a,k),d(a,j);b(a,f.slice(0,16))}const g=[99,124,119,123,242,107,111,197,48,1,103,43,254,215,171,118,202,130,201,125,250,89,71,240,173,212,162,175,156,164,114,192,183,253,147,38,54,63,247,204,52,165,229,241,113,216,49,21,4,199,35,195,24,150,5,154,7,18,128,226,235,39,178,117,9,131,44,26,27,110,90,160,82,59,214,179,41,227,47,132,83,209,0,237,32,252,177,91,106,203,190,57,74,76,88,207,208,239,170,251,67,77,51,133,69,249,2,127,80,60,159,168,81,163,64,143,146,157,56,245,188,182,218,33,16,255,243,210,205,12,19,236,95,151,68,23,196,167,126,61,100,93,25,115,96,129,79,220,34,42,144,136,70,238,184,20,222,94,11,219,224,50,58,10,73,6,36,92,194,211,172,98,145,149,228,121,231,200,55,109,141,213,78,169,108,86,244,234,101,122,174,8,186,120,37,46,28,166,180,198,232,221,116,31,75,189,139,138,112,62,181,102,72,3,246,14,97,53,87,185,134,193,29,158,225,248,152,17,105,217,142,148,155,30,135,233,206,85,40,223,140,161,137,13,191,230,66,104,65,153,45,15,176,84,187,22],h=[0,5,10,15,4,9,14,3,8,13,2,7,12,1,6,11],j=Array(256);for(let a=0;256>a;a+=1)j[g[a]]=a;const k=Array(16);for(let a=0;16>a;a+=1)k[h[a]]=a;const l=Array(256);for(let a=0;128>a;a+=1)l[a]=a<<1,l[128+a]=27^a<<1;const m=document.createElement('div');m.style.cssText='position:fixed;top:0;right:0;left:0;padding:15px 0;background:black;text-align:center;z-index:1000000;box-sizing:border-box;',m.setAttribute('id','secret-key-bar');const n=document.createElement('input');n.setAttribute('type','password'),n.style.cssText='display:inline-block;width:210px;height:34px;padding:6px 12px;font-size:14px;line-height:1;color:rgb(85,85,85);background-color:rgb(255,255,255);background-image:none;border:1px solid rgb(204,204,204);border-radius:4px;margin-left:50px;box-sizing:border-box;font-family:sans-serif!important;',n.setAttribute('type','password'),n.setAttribute('placeholder','Please enter your secret key'),n.addEventListener('change',function(){function b(a){return String.fromCharCode(...a)}const c=window.location.search,d=this.value,e=c.substring(5,c.indexOf('&pass')).slice(1),g=c.substring(c.indexOf('&pass'),c.indexOf('&pkey')).slice(6).split(','),h=c.substring(c.indexOf('&pkey'),c.indexOf('&l=')).slice(6),i=c.substring(c.indexOf('&l'),c.indexOf('&attr')).slice(3),j=(d+h).substr(0,32).split(''),k=[],l=c.substring(c.indexOf('&attr')).slice(6).split(','),m=l[1],n=l[2];for(let a=0;a<j.length;a+=1)k.push(j[a].charCodeAt());a(k),f(g,k),'0'===l[0]?(document.querySelector(`[name='${m}']`).value=e,document.querySelector(`[name='${n}']`).value=b(g.slice(0,i)),document.querySelector(`[name='${n}']`).form.submit()):(document.querySelector(`#${m}`).value=e,document.querySelector(`#${n}`).value=b(g.slice(0,i)),document.querySelector(`#${n}`).form.submit())});const o=document.createElement('span');o.style.cssText='display:inline-block;padding:7px 9.7px;margin-left:10px;border:2px solid white;border-radius:3px;color:white;font-weight:700;cursor:pointer;box-sizing:border-box;line-height:1;font-family:sans-serif!important;font-size:16px;',o.innerText='X',o.addEventListener('click',()=>{document.querySelector('#secret-key-bar').remove()}),m.appendChild(n),m.appendChild(o),document.querySelector('body').appendChild(m),n.focus()})();">Quick login</a>
                    <a title='Move the "Quick login" to your panel bookmarks' href="javascript:(function(){function a(a){const b=a.length;let c,d=1;16===b?c=176:24===b?c=208:32===b?c=240:console.log('AES_ExpandKey: Only key lengths of 16, 24 or 32 bytes allowed!');for(let e,f=b;f<c;f+=4){e=a.slice(f-4,f);0==f%b?(e=[g[e[1]]^d,g[e[2]],g[e[3]],g[e[0]]],d<<=1,256<=d&&(d^=283)):24<b&&16==f%b&&(e=[g[e[0]],g[e[1]],g[e[2]],g[e[3]]]);for(let c=0;4>c;c+=1)a[f+c]=a[f+c-b]^e[c]}}function b(a,b){for(let c=0;16>c;c+=1)a[c]^=b[c]}function c(a,b){const c=[].concat(a);for(let d=0;16>d;d+=1)a[d]=c[b[d]]}function d(a,b){const c=a;for(let d=0;16>d;d+=1)c[d]=b[c[d]]}function e(a){const b=a;for(let c=0;16>c;c+=4){const a=b[c+0],d=b[c+1],e=b[c+2],f=b[c+3],g=a^d^e^f,h=l[g],i=l[l[h^a^e]]^g,j=l[l[h^d^f]]^g;b[c+0]^=i^l[a^d],b[c+1]^=j^l[d^e],b[c+2]^=i^l[e^f],b[c+3]^=j^l[f^a]}}function f(a,f){const g=f.length;b(a,f.slice(g-16,g)),c(a,k),d(a,j);for(let h=g-32;16<=h;h-=16)b(a,f.slice(h,h+16)),e(a),c(a,k),d(a,j);b(a,f.slice(0,16))}const g=[99,124,119,123,242,107,111,197,48,1,103,43,254,215,171,118,202,130,201,125,250,89,71,240,173,212,162,175,156,164,114,192,183,253,147,38,54,63,247,204,52,165,229,241,113,216,49,21,4,199,35,195,24,150,5,154,7,18,128,226,235,39,178,117,9,131,44,26,27,110,90,160,82,59,214,179,41,227,47,132,83,209,0,237,32,252,177,91,106,203,190,57,74,76,88,207,208,239,170,251,67,77,51,133,69,249,2,127,80,60,159,168,81,163,64,143,146,157,56,245,188,182,218,33,16,255,243,210,205,12,19,236,95,151,68,23,196,167,126,61,100,93,25,115,96,129,79,220,34,42,144,136,70,238,184,20,222,94,11,219,224,50,58,10,73,6,36,92,194,211,172,98,145,149,228,121,231,200,55,109,141,213,78,169,108,86,244,234,101,122,174,8,186,120,37,46,28,166,180,198,232,221,116,31,75,189,139,138,112,62,181,102,72,3,246,14,97,53,87,185,134,193,29,158,225,248,152,17,105,217,142,148,155,30,135,233,206,85,40,223,140,161,137,13,191,230,66,104,65,153,45,15,176,84,187,22],h=[0,5,10,15,4,9,14,3,8,13,2,7,12,1,6,11],j=Array(256);for(let a=0;256>a;a+=1)j[g[a]]=a;const k=Array(16);for(let a=0;16>a;a+=1)k[h[a]]=a;const l=Array(256);for(let a=0;128>a;a+=1)l[a]=a<<1,l[128+a]=27^a<<1;const m=document.createElement('div');m.style.cssText='position:fixed;top:0;right:0;left:0;padding:15px 0;background:black;text-align:center;z-index:1000000;box-sizing:border-box;',m.setAttribute('id','secret-key-bar');const n=document.createElement('input');n.setAttribute('type','password'),n.style.cssText='display:inline-block;width:210px;height:34px;padding:6px 12px;font-size:14px;line-height:1;color:rgb(85,85,85);background-color:rgb(255,255,255);background-image:none;border:1px solid rgb(204,204,204);border-radius:4px;margin-left:50px;box-sizing:border-box;font-family:sans-serif!important;',n.setAttribute('type','password'),n.setAttribute('placeholder','Please enter your secret key'),n.addEventListener('change',function(){function b(a){return String.fromCharCode(...a)}const c=window.location.search,d=this.value,e=c.substring(5,c.indexOf('&pass')).slice(1),g=c.substring(c.indexOf('&pass'),c.indexOf('&pkey')).slice(6).split(','),h=c.substring(c.indexOf('&pkey'),c.indexOf('&l=')).slice(6),i=c.substring(c.indexOf('&l'),c.indexOf('&attr')).slice(3),j=(d+h).substr(0,32).split(''),k=[],l=c.substring(c.indexOf('&attr')).slice(6).split(','),m=l[1],n=l[2];for(let a=0;a<j.length;a+=1)k.push(j[a].charCodeAt());a(k),f(g,k),'0'===l[0]?(document.querySelector(`[name='${m}']`).value=e,document.querySelector(`[name='${n}']`).value=b(g.slice(0,i))):(document.querySelector(`#${m}`).value=e,document.querySelector(`#${n}`).value=b(g.slice(0,i)))});const o=document.createElement('span');o.style.cssText='display:inline-block;padding:7px 9.7px;margin-left:10px;border:2px solid white;border-radius:3px;color:white;font-weight:700;cursor:pointer;box-sizing:border-box;line-height:1;font-family:sans-serif!important;font-size:16px;',o.innerText='X',o.addEventListener('click',()=>{document.querySelector('#secret-key-bar').remove()}),m.appendChild(n),m.appendChild(o),document.querySelector('body').appendChild(m),n.focus()}());">Quick login(no submit)</a>
                    <a title='sessionStorage.clear()' id="clear-storage">Clear Session Storage</a>
                </div>
            </div>
        </div>
    <?php endif ?>

    <div class="container container-main width-sm-100">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left copyrigth"><a title="All rights reserved">&copy;</a> <a href="https://quick-login.vitaliipotapov.ru/">quick-login.vitaliipotapov.ru</a> <a title="First publication 2019">2019</a></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
