<?php

namespace app\controllers;

use Yii;
use yii\helpers\Html;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Services;
use app\components\AesCrypt;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'services', 'remove', 'update', 'index-json'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        if(!Yii::$app->params['REST_API_MOD']){
            if($_POST) {
                $userKey = $_POST['secretKey'];
                $data = Services::getServices(Yii::$app->user->identity->id);

                return $this->render('index', [
                    'data' => $data,
                    'userKey' => $userKey,
                ]);
            }
        }
        $count_records = Services::find()->where(['user_id' => Yii::$app->user->identity->id])->count();

        return $this->render('index', [
            'count_records' => $count_records,
        ]);
    }

    /**
     * JsonIndex action.
     *
     * @return Response|string
     */
    public function actionIndexJson($key)
    {
        $encrypt_data = Services::getServices(Yii::$app->user->identity->id);
        $decrypt_data = [];
        foreach ($encrypt_data as $v => $val) {
            $secret_key = 'A' . $key . strtotime(date($val['date_update']));

            $site = Services::decryptWord($val['site'], $secret_key);
            if(!$site) break;
            $login = Services::decryptWord($val['login'], $secret_key);
            $pass = Services::decryptWord($val['pass'], $secret_key);

            $decrypt_data[] = [
                'id' => $val['id'],
                'site' => $site,
                'hideLogin' => Services::hideLogin($login),
                'login' => $login,
                'pass' => $pass,
                'attribute' => $val['attribute'],
                'field_login' => $val['field_login'],
                'field_pass' => $val['field_pass'],
                'public_key' => $val['public_key'],
            ];
        }
        return json_encode($decrypt_data);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Services action.
     *
     * @return Response|string
     */
    public function actionServices()
    {
        if (Yii::$app->params['REST_API_MOD']) {
            if(\Yii::$app->request->isAjax){
                $model = new Services();
                $model->load(Yii::$app->request->post());

                $model->attribute = Html::encode($model->attribute);
                $model->field_login = Html::encode($model->field_login);
                $model->field_pass = Html::encode($model->field_pass);

                $model->date_create = date('Y-m-d H:i:s');
                $model->date_update = $model->date_create;

                $secretKey = $model->secretKey;
                $model->secretKey = 'A' . Html::encode($model->secretKey) . strtotime($model->date_update);
                $model->user_id = Yii::$app->user->identity->id;

                $site = $model->site;
                $model->site = Services::encryptBase64($model->site, $model->secretKey);

                $login = $model->login;
                $model->login = Services::encryptBase64($model->login, $model->secretKey);

                $model->public_key = AesCrypt::getPublicKey(32);
                $private_key = substr($secretKey . $model->public_key, 0, 32);

                $pass = AesCrypt::AES_Encrypt($model->pass, $private_key);
                $model->pass = Services::encryptBase64(json_encode($pass), $model->secretKey);

                if ($model->save()) {
                    $data = [
                        'id' => $model->id,
                        'site' => $site,
                        'hideLogin' => Services::hideLogin($login),
                        'login' => $login,
                        'pass' => $pass,
                        'attribute' => $model->attribute,
                        'field_login' => $model->field_login,
                        'field_pass' => $model->field_pass,
                        'public_key' => $model->public_key,
                    ];
                    return json_encode($data);
                } else {
                    return '0';
                }
            }
        } else {
            $model = new Services();

            if($_POST){
                $model->load(Yii::$app->request->post());
                $model->field_login = Html::encode($model->field_login);
                $model->field_pass = Html::encode($model->field_pass);
                $model->attribute = Html::encode($model->attribute);
                $model->secretKey = 'A' . Html::encode($model->secretKey) . strtotime(date('Y-m-d H:i:s'));
                $model->user_id = Yii::$app->user->identity->id;
                $model->date_update = date('Y-m-d H:i:s');
                $model->site = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->site, $model->secretKey));
                $model->login = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->login, $model->secretKey));
                $model->pass = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->pass, $model->secretKey));
                if ($model->save()) {
                    return $this->goHome();
                }
            }

            return $this->render('services', [
                'model' => $model,
            ]);
        }

        return $this->goHome();
    }

    /**
     * Update action.
     *
     * @return Response
     */
    public function actionUpdate($id)
    {
        if(Yii::$app->params['REST_API_MOD']){
            if(\Yii::$app->request->isAjax){
                $model = new Services();
                $model = $model->findOne(['id' => $id, 'user_id' => Yii::$app->user->identity->id]);
                $model->load(Yii::$app->request->post());


                $model->attribute = Html::encode($model->attribute);
                $model->field_login = Html::encode($model->field_login);
                $model->field_pass = Html::encode($model->field_pass);

                $model->date_update = date('Y-m-d H:i:s');

                $secretKey = $model->secretKey;
                $model->secretKey = 'A' . Html::encode($model->secretKey) . strtotime($model->date_update);
                $model->user_id = Yii::$app->user->identity->id;

                $site = $model->site;
                $model->site = Services::encryptBase64($model->site, $model->secretKey);
                $login = $model->login;
                $model->login = Services::encryptBase64($model->login, $model->secretKey);

                $model->public_key = AesCrypt::getPublicKey(32);
                $private_key = substr($secretKey . $model->public_key, 0, 32);

                $pass = AesCrypt::AES_Encrypt($model->pass, $private_key);
                $model->pass = Services::encryptBase64(json_encode($pass), $model->secretKey);

                $data = [
                    'id' => $model->id,
                    'site' => $site,
                    'hideLogin' => Services::hideLogin($login),
                    'login' => $login,
                    'pass' => $pass,
                    'attribute' => $model->attribute,
                    'field_login' => $model->field_login,
                    'field_pass' => $model->field_pass,
                    'public_key' => $model->public_key,
                ];
                if ($model->update()) {
                    return json_encode($data);
                } else {
                    return '0';
                }
            }
        } else {
            $model = new Services();
            $model = $model->findOne(['id' => $id, 'user_id' => Yii::$app->user->identity->id]);
            $model->load(Yii::$app->request->post());

            $model->field_login = Html::encode($model->field_login);
            $model->field_pass = Html::encode($model->field_pass);
            $model->attribute = Html::encode($model->attribute);

            $model->secretKey = 'A' . Html::encode($model->secretKey) . strtotime(date('Y-m-d H:i:s'));
            $model->user_id = Yii::$app->user->identity->id;
            $model->date_update = date('Y-m-d H:i:s');

            $model->site = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->site, $model->secretKey));
            $model->login = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->login, $model->secretKey));
            $model->pass = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->pass, $model->secretKey));

            if ($model->update()) {
                return $this->goHome();
            }
        }
    }

    /**
     * Remove action.
     *
     * @return Response
     */
    public function actionRemove($id)
    {
        if(\Yii::$app->request->isAjax){
            $model = new Services();
            $model = $model->findOne(['id' => $id, 'user_id' => Yii::$app->user->identity->id]);
            if ($model !== NULL && $model->delete()) {
                return 1;
            } else {
                return 0;
            }
        }
    }

}
