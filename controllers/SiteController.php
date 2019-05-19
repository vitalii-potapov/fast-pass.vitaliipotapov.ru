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
                        'actions' => ['logout', 'index', 'services', 'remove', 'update'],
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
        if($_POST) {
            $userKey = $_POST['secretKey'];
            $data = Services::getServices(Yii::$app->user->identity->id);

            return $this->render('index', [
                'data' => $data,
                'userKey' => $userKey,
            ]);
        }

        return $this->render('index');
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
        $model = new Services();
        if($_POST){
            $model->load(Yii::$app->request->post());
            $model->field_login = Html::encode($model->field_login);
            $model->field_pass = Html::encode($model->field_pass);
            $model->attribute = Html::encode($model->attribute);
            $model->secretKey = 'A' . $model->secretKey . strtotime(date('Y-m-d H:i:s'));
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
        $model = new Services();
        $model = $model->findOne($id);
        $model->load(Yii::$app->request->post());
        $model->field_login = Html::encode($model->field_login);
        $model->field_pass = Html::encode($model->field_pass);
        $model->attribute = Html::encode($model->attribute);
        $userKey = $model->secretKey;
        $model->secretKey = 'A' . $model->secretKey . strtotime(date('Y-m-d H:i:s'));
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
