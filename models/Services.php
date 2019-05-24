<?php

namespace app\models;

use Yii;

class Services extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = -1;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'services';
    }

    public $secretKey;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'site', 'login', 'pass', 'field_login', 'field_pass', 'attribute', 'date_create', 'date_update', 'secretKey'], 'required'],
            [['id', 'user_id', 'attribute', 'status'], 'integer'],
            [['date_create', 'date_update'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['site', 'login', 'pass', 'field_login', 'field_pass', 'secretKey'], 'string'],
            [['attribute'], 'default', 'value' => '0'],
            [['status'], 'default', 'value' => '1'],
            [['site', 'login', 'field_login', 'field_pass'], 'string', 'max' => 255],
            [['public_key'], 'string', 'max' => 32],
            [['pass'], 'string', 'max' => 500],
        ];
    }

    public static function getServices($user_id)
    {
        if(Yii::$app->params['REST_API_MOD']){

            $data = static::find()
                            ->asArray()
                            ->select(['id','site', 'login', 'pass', 'public_key', 'field_login', 'field_pass', 'attribute', 'date_update'])
                            ->where(['user_id' => $user_id, 'status' => self::STATUS_ACTIVE])
                            ->all();

            return $data;
        } else {
            return static::find()->where(['user_id' => $user_id, 'status' => self::STATUS_ACTIVE])->all();
        }
    }

    public static function hideLogin($login)
    {
        if($login) {
            $login_start = strstr($login, '@', true) ? strstr($login, '@', true) : $login;
            $login_end = strstr($login, '@');
            $login = substr_replace($login_start, '*****', 3, -2) . $login_end;
            return $login;
        }
    }

    public static function encryptWord($word, $add = NULL)
    {
        $encrypt_word = str_split($word);
        $changeCharASCII = function(&$item, $key, $add) {
            $item = ord($item);
            $item += $add;
        };
        array_walk($encrypt_word, $changeCharASCII, $add);

        return $encrypt_word;
    }

    public static function decryptWord($word, $secret_key)
    {
        return Yii::$app->getSecurity()->decryptByPassword(base64_decode($word), $secret_key);
    }

    public static function encryptBase64($word, $secret_key)
    {
        return base64_encode(Yii::$app->getSecurity()->encryptByPassword($word, $secret_key));
    }
}
