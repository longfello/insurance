<?php
namespace frontend\modules\agency\models;

use cheatsheet\Time;
use common\commands\SendEmailCommand;
use common\models\User;
use common\models\UserProfile;

use common\models\UserToken;

use frontend\modules\agency\Module;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\Url;


/**
 * Signup form
 * 
 */
class NewManagerForm extends Model
{

    /**
     *  сценарий для создания пользователя
     */
    const SCENARIO_CREATE = 'create';
    /**
     *  сценарий для релактирования пользователя
     */
    const SCENARIO_UPDATE = 'update';

    /**
     * @var string  Логин
     */
    public $username;
    /**
     * @var string  Email
     */
    public $email;
    /**
     * @var string Пароль
     */
    public $password;
    /**
     * @var string  Повтор пароля
     */
    public $password_repeat;
    /**
     * @var string  Имя или ФИО пользователя
     */
    public $fio;
    /**
     * @var string  Город
     */
    public $city;
    /**
     * @var string  Телефон
     */
    public $phone;
    /**
     * @var  string  Роли в агентстве
     */
    public $role;
    /**
     * @var string  Статус
     */
    public $status;


    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['username'], 'filter', 'filter' => 'trim'],
            [['username','fio','city','phone','role','status'], 'required'],

            ['username', 'unique',
                'targetClass'=>'\common\models\User',
                'message' => Yii::t('frontend', 'This username has already been taken.'),
                'on' => self::SCENARIO_CREATE
            ],
            [['username', 'fio', 'city','phone'], 'string', 'min' => 2, 'max' => 255],
            ['status', 'integer'],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass'=> '\common\models\User',
                'message' => Yii::t('frontend', 'This email address has already been taken.'),
                'on' => self::SCENARIO_CREATE
            ],

            ['password', 'required', 'on' => self::SCENARIO_CREATE],
            ['password', 'string', 'min' => 6,  'on' => self::SCENARIO_CREATE],
            ['password_repeat', 'required',  'on' => self::SCENARIO_CREATE],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match" , 'on' => self::SCENARIO_CREATE],

            ['password', 'required','skipOnEmpty' => true],
            ['password', 'string', 'min' => 6,'skipOnEmpty' => true],
            ['password_repeat', 'required','when' => function($model){
                return $model->password != null;
            }],
            ['password_repeat', 'compare', 'compareAttribute'=>'password', 'message'=>"Passwords don't match", ],
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CREATE] = ['username','password','password_repeat','email','status','fio','city','phone','role'];
        $scenarios[self::SCENARIO_UPDATE] = ['username','password','password_repeat','email','status','fio','city','phone','role'];

        return $scenarios;
    }


    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username'=>Yii::t('frontend', 'Логин'),
            'email'=>Yii::t('frontend', 'E-mail'),
            'password'=>Yii::t('frontend', 'Password'),
            'password_repeat'=>Yii::t('frontend', 'Повторите пароль'),
            'fio'=>'ФИО',
            'city'=>'Город',
            'phone'=>'Телефон',
            'role'=>'Роль',
            'status'=>'Статус'
        ];
    }

    /**
     * Signs user up.
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {

            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = $this->status;
            $user->setPassword($this->password);

            if(!$user->save()) {
                throw new Exception("User couldn't be  saved");
            };
            $user->afterSignupManagerAgency();

            $profile = UserProfile::findOne(['user_id' => $user->id]);
            $profile->firstname = $this->fio;
            $profile->city = $this->city;
            $profile->phone = $this->phone;
            if($profile->update()) {
                $auth = Yii::$app->authManager;
                $auth->assign($auth->getRole($this->role), $user->id);
                return $user;
            } else {
                throw new Exception("User couldn't be correct saved");
            }

        }

        return null;
    }

    /**
     * Updats user .
     * @param $user_id
     * @return User|null the update model or null if updating fails
     */
    public function update($user_id)
    {

            $user = User::findOne(['id'=>$user_id]);
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = $this->status;
            if(!empty($this->password)) {
                $user->setPassword($this->password);
            }
            $user->updateRole($user_id,$this->role);

            if(!$user->update()) {
                throw new Exception("User couldn't be  saved");
            };
            $profile = UserProfile::findOne(['user_id' => $user->id]);
            $profile->locale = 'ru';
            $profile->firstname = $this->fio;
            $profile->city = $this->city;
            $profile->phone = $this->phone;
            if($profile->save()) {
                return $user;
            } else {
                throw new Exception("User couldn't be correct saved");
            }


        return null;
    }

}
