<?php
namespace app\models;
use Yii;
use yii\base\Model;
use yii\helpers\Html;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|NULL $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $authKey;
    public $rememberMe = true;
    private $_user = false;
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username, password, authKey are both required
            [['username', 'password', 'authKey'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['authKey', 'validateAuthKey'],
        ];
    }
    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !password_verify($this->password, $user->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Validates the authKey.
     * This method serves as the inline validation for authKey.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validateAuthKey($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if ($user->authKey !== NULL) {
                $secretKey = 'A' . Html::encode($this->password) . Html::encode($this->username);
                $secretAuth = Services::decryptWord($user->authKey, $secretKey);
                if ($secretAuth) {
                    $ga = new \PHPGangsta_GoogleAuthenticator();
                    $oneCode = $ga->getCode($secretAuth);
                    if (!$user || $this->authKey !== $oneCode) {
                        $this->addError($attribute, 'Incorrect username or password.');
                    }
                } else {
                    $this->addError($attribute, 'Incorrect username or password.');
                }
            }
        }
    }
    /**
     * Logs in a user using the provided username and password.
     * @return bool whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
        }
        return false;
    }
    /**
     * Finds user by [[username]]
     *
     * @return User|NULL
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }
        return $this->_user;
    }

    public static function decryptWord($word, $secret_key)
    {
        return Yii::$app->getSecurity()->decryptByPassword(base64_decode($word), $secret_key);
    }
}