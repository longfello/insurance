<?php
namespace frontend\modules\agency\models;

use yii\base\Model;
use Yii;
use yii\web\JsExpression;

/**
 * Account form
 *
 */
class AccountForm extends Model
{
    /**
     * @var string   Логин
     */
    public $username;
    /**
     * @var string   Email
     */
    public $email;
    /**
     * @var string Пароль
     */
    public $password;
    /**
     * @var string  Повтор пароля
     */
    public $password_confirm;
    /**
     * @var User  Класс
     */
    private $user;

    /**
     * @return bool
     */
    public function setUser($user)
    {
        $this->user = $user;
        $this->email = $user->email;
        $this->username = $user->username;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('frontend', 'This username has already been taken.'),
                'filter' => function ($query) {
                    $query->andWhere(['not', ['id' => Yii::$app->user->getId()]]);
                }
            ],
            ['username', 'string', 'min' => 1, 'max' => 255],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass' => '\common\models\User',
                'message' => Yii::t('frontend', 'This email has already been taken.'),
                'filter' => function ($query) {
                    $query->andWhere(['not', ['id' => Yii::$app->user->getId()]]);
                }
            ],
            ['password', 'string'],
            [
                'password_confirm',
                'required',
                'when' => function($model) {
                    return !empty($model->password);
                },
                'whenClient' => new JsExpression("function (attribute, value) {
                    return $('#caccountform-password').val().length > 0;
                }")
            ],
            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false],

        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('frontend', 'Username'),
            'email' => Yii::t('frontend', 'Email'),
            'password' => Yii::t('frontend', 'Password'),
            'password_confirm' => Yii::t('frontend', 'Confirm Password')
        ];
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $this->user->username = $this->username;
        $this->user->email = $this->email;
        if ($this->password) {
            $this->user->setPassword($this->password);
        }
        return $this->user->save();
    }
}
