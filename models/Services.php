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
            [['user_id', 'site', 'login', 'pass', 'field_login', 'field_pass', 'attribute', 'date_create', 'secretKey'], 'required'],
            [['id', 'user_id', 'attribute', 'status'], 'integer'],
            [['date_create'], 'date', 'format' => 'php:Y-m-d H:i:s'],
            [['site', 'login', 'pass', 'field_login', 'field_pass', 'secretKey'], 'string'],
            [['attribute'], 'default', 'value' => '0'],
            [['status'], 'default', 'value' => '1'],
            [['site', 'login', 'pass', 'field_login', 'field_pass'], 'string', 'max' => 255],
        ];
    }

    public static function getServices($user_id)
    {
        return static::find()->where(['user_id' => $user_id, 'status' => self::STATUS_ACTIVE])->all();
    }
    public static function getServicesOne($user_id)
    {
        return static::find()->where(['user_id' => $user_id, 'status' => self::STATUS_ACTIVE])->one();
    }
}
