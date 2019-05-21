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
        if(Yii::$app->params['REST_API_MOD']){

        } else {
            if($_POST) {
                $userKey = $_POST['secretKey'];
                $data = Services::getServices(Yii::$app->user->identity->id);

                return $this->render('index', [
                    'data' => $data,
                    'userKey' => $userKey,
                ]);
            }
        }
        return $this->render('index');
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
            $secret_key = 'A' . $key . strtotime(date($val['date_create']));
            $site = Services::decryptWord($val['site'], $secret_key);
            if(!$site) break;
            $login = Services::decryptWord($val['login'], $secret_key);
            $pass = Services::decryptWord($val['pass'], $secret_key);

            $encrypt_secret_key = Services::encryptWord($key);
            $encrypt_secret_key = array_sum($encrypt_secret_key);

            $encrypt_login = Services::encryptWord($login, $encrypt_secret_key);
            $encrypt_login = implode(',', $encrypt_login);

            $encrypt_pass = Services::encryptWord($pass, $encrypt_secret_key);
            $encrypt_pass = implode(',', $encrypt_pass);

            $decrypt_data[] = [
                'id' => $val['id'],
                'site' => $site,
                'hideLogin' => Services::hideLogin($login),
                'login' => $login,
                'encrypt_login' => $encrypt_login,
                'pass' => $encrypt_pass,
                'attribute' => $val['attribute'],
                'field_login' => $val['field_login'],
                'field_pass' => $val['field_pass'],
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

                $secretKey = $model->secretKey;
                $model->secretKey = 'A' . Html::encode($model->secretKey) . strtotime(date('Y-m-d H:i:s'));
                $model->user_id = Yii::$app->user->identity->id;
                $model->date_create = date('Y-m-d H:i:s');

                $site = $model->site;
                $model->site = Services::encryptBase64($model->site, $model->secretKey);
                $login = $model->login;
                $model->login = Services::encryptBase64($model->login, $model->secretKey);
                $pass = $model->pass;
                $model->pass = Services::encryptBase64($model->pass, $model->secretKey);

                $encrypt_secret_key = Services::encryptWord($secretKey);
                $encrypt_secret_key = array_sum($encrypt_secret_key);

                $encrypt_login = Services::encryptWord($login, $encrypt_secret_key);
                $encrypt_login = implode(',', $encrypt_login);
                $encrypt_pass = Services::encryptWord($pass, $encrypt_secret_key);
                $encrypt_pass = implode(',', $encrypt_pass);

                if ($model->save()) {
                    $data = [
                        'id' => $model->id,
                        'site' => $site,
                        'hideLogin' => Services::hideLogin($login),
                        'login' => $login,
                        'encrypt_login' => $encrypt_login,
                        'pass' => $encrypt_pass,
                        'attribute' => $model->attribute,
                        'field_login' => $model->field_login,
                        'field_pass' => $model->field_pass,
                    ];
                    return json_encode($data);
                } else {
                    return '0';
                }

                return 'test';
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
                $model->date_create = date('Y-m-d H:i:s');
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

                $secretKey = $model->secretKey;
                $model->secretKey = 'A' . Html::encode($model->secretKey) . strtotime(date('Y-m-d H:i:s'));
                $model->user_id = Yii::$app->user->identity->id;
                $model->date_create = date('Y-m-d H:i:s');

                $site = $model->site;
                $model->site = Services::encryptBase64($model->site, $model->secretKey);
                $login = $model->login;
                $model->login = Services::encryptBase64($model->login, $model->secretKey);
                $pass = $model->pass;
                $model->pass = Services::encryptBase64($model->pass, $model->secretKey);

                $encrypt_secret_key = Services::encryptWord($secretKey);
                $encrypt_secret_key = array_sum($encrypt_secret_key);

                $encrypt_login = Services::encryptWord($login, $encrypt_secret_key);
                $encrypt_login = implode(',', $encrypt_login);
                $encrypt_pass = Services::encryptWord($pass, $encrypt_secret_key);
                $encrypt_pass = implode(',', $encrypt_pass);

                $data = [
                    'id' => $model->id,
                    'site' => $site,
                    'hideLogin' => Services::hideLogin($login),
                    'login' => $login,
                    'encrypt_login' => $encrypt_login,
                    'pass' => $encrypt_pass,
                    'attribute' => $model->attribute,
                    'field_login' => $model->field_login,
                    'field_pass' => $model->field_pass,
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
            $model->date_create = date('Y-m-d H:i:s');

            $model->site = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->site, $model->secretKey));
            $model->login = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->login, $model->secretKey));
            $model->pass = base64_encode(Yii::$app->getSecurity()->encryptByPassword($model->pass, $model->secretKey));

            if ($model->update()) {
                return $this->goHome();
            }
        }
    }

}
