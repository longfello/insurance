<?php
namespace frontend\modules\agency\models;

use cheatsheet\Time;
use common\commands\SendEmailCommand;
use common\models\User;
use common\models\UserToken;
use common\models\Agency;
use common\models\UserToAgency;

use frontend\modules\agency\Module;
use yii\base\Exception;
use yii\base\Model;
use Yii;
use yii\helpers\Url;

/**
 * Signup form
 *
 */
class SignupForm extends Model
{
    /**
     *  сценарий регистрации OOO/ОАО
     */
    const SCENARIO_REGISTER_OOO = 'reg_ooo';
    /**
     *  сценарий регистрации ИП
     */
    const SCENARIO_REGISTER_IP = 'reg_ip';

    /**
     * @var  string  Логин
     */
    public $username;
    /**
     * @var string  Email
     */
    public $email;
    /**
     * @var string  Пароль
     */
    public $password;
    /**
     * @var int  Варианты сценария
     */
    public $value_scenario;
    /**
     * @var string  ФИО Руководителя
     */
    public $chief_name;
    /**
     * @var string  Должность Руководителя
     */
    public $chief_position;
    /**
     * @var string  Организационная форма компании
     */
    public $company_type;
    /**
     * @var string  Форма налогооблажения
     */
    public $company_tax_type;
    /**
     * @var string  Форма Сотрудничества
     */
    public $cooperation_form;
    /**
     * @var string  Наименование компании (без орг.формы)
     */
    public $name;
    /**
     * @var string  Юридический адрес - регион
     */
    public $legal_region;
    /**
     * @var string  Юридический адрес - город
     */
    public $legal_city;
    /**
     * @var string  Юридический адрес
     */
    public $legal_address;
    /**
     * @var  string  Юридический адрес - индекс
     */
    public $legal_index;
    /**
     * @var string  Фактический адрес - регион
     */
    public $actual_region;
    /**
     * @var string  Фактический адрес - город
     */
    public $actual_city;
    /**
     * @var string $actual_address Фактический адрес
     */
    public $actual_address;
    /**
     * @var string  Фактический адрес - индекс
     */
    public $actual_index;
    /**
     * @var string  Телефон
     */
    public $phone;
    /**
     * @var string  ИНН
     */
    public $inn;
    /**
     * @var string  КПП
     */
    public $kpp;
    /**
     * @var string  ОГРН/ОГРНИП
     */
    public $ogrn;
    /**
     * @var string  ОКВЭД
     */
    public $okved;
    /**
     * @var string  ОКПО
     */
    public $okpo;
    /**
     * @var string  ОКАТО
     */
    public $okato;
    /**
     * @var string  Расчетный счет
     */
    public $checking_account;
    /**
     * @var string  Банк
     */
    public $bank;
    /**
     * @var string  Кор. счет
     */
    public $correspondent_account;
    /**
     * @var string  БИК
     */
    public $bik;
    /**
     * @var string  Адрес сайта
     */
    public $href;
    /**
     * @var string  Комментарии
     */
    public $comment;
    /**
     * @var int  Сеть туристических агентств
     */
    public $travel_network_id;

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [
            [['username'], 'filter', 'filter' => 'trim'],
            [['username','travel_network_id', 'company_type', 'company_tax_type', 'cooperation_form', 'chief_name', 'chief_position', 'name', 'legal_city', 'legal_address', 'legal_index', 'actual_city', 'actual_address', 'actual_index', 'phone', 'inn', 'ogrn', 'checking_account', 'bank', 'correspondent_account', 'bik'], 'required'],
            [['okpo'], 'string'],
            [['legal_region','actual_region','href','okved'],'string', 'max' => 255],
            [['comment'],'string', 'max' => '500'],
            [['inn'], 'string', 'max' => 12, 'on' => self::SCENARIO_REGISTER_IP],
            [['inn'], 'string', 'max' => 10, 'on' => self::SCENARIO_REGISTER_OOO],
            [['kpp', 'bik'], 'string', 'max' => 9],
            [['ogrn'], 'string', 'max' => 16, 'on' => self::SCENARIO_REGISTER_IP],
            [['ogrn'], 'string', 'max' => 13, 'on' => self::SCENARIO_REGISTER_OOO],
            [['okpo'], 'string', 'max' => 10, 'on' => self::SCENARIO_REGISTER_IP],
            [['okpo'], 'string', 'max' => 8, 'on' => self::SCENARIO_REGISTER_OOO],
            [['okato'], 'string', 'max' => 11],
            [['travel_network_id','value_scenario'], 'integer'],
            [['checking_account', 'correspondent_account'], 'string', 'max' => 20],
            ['username', 'unique',
                'targetClass'=>'\common\models\User',
                'message' => Yii::t('frontend', 'This username has already been taken.')
            ],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique',
                'targetClass'=> '\common\models\User',
                'message' => Yii::t('frontend', 'This email address has already been taken.')
            ],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REGISTER_OOO] = ['username','password','value_scenario','travel_network_id', 'company_type', 'company_tax_type', 'cooperation_form','comment', 'chief_name', 'chief_position', 'name', 'legal_city','legal_region', 'legal_address', 'legal_index', 'actual_city','actual_region', 'actual_address', 'actual_index', 'phone', 'email','href', 'inn', 'ogrn','okpo','okato','okved', 'checking_account', 'bank', 'correspondent_account','kpp', 'bik'];
        $scenarios[self::SCENARIO_REGISTER_IP] = ['username','password','value_scenario','travel_network_id', 'company_type', 'company_tax_type', 'cooperation_form','comment', 'chief_name', 'chief_position', 'name', 'legal_city','legal_region', 'legal_address', 'legal_index', 'actual_city','actual_region', 'actual_address', 'actual_index', 'phone', 'email','href', 'inn', 'ogrn','okpo','okato','okved', 'checking_account', 'bank', 'correspondent_account', 'bik'];

        return $scenarios;
    }

    /**     *
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'username'=>Yii::t('frontend', 'Логин'),
            'email'=>Yii::t('frontend', 'E-mail'),
            'password'=>Yii::t('frontend', 'Password'),
            'travel_network_id' => 'Сеть туристических агентств',
            'company_type' => 'Организационная форма компании',
            'company_tax_type' => 'Форма налогооблажения',
            'cooperation_form' => 'Форма Сотрудничества',
            'chief_name' => 'ФИО Руководителя',
            'chief_position' => 'Должность Руководителя',
            'name' => 'Наименование компании (без орг.формы)',
            'legal_region' => 'Юридический адрес - регион',
            'legal_city' => 'Юридический адрес - город',
            'legal_address' => 'Юридический адрес',
            'legal_index' => 'Юридический адрес - индекс',
            'actual_region' => 'Фактический адрес - регион',
            'actual_city' => 'Фактический адрес - город',
            'actual_address' => 'Фактический адрес',
            'actual_index' => 'Фактический адрес - индекс',
            'phone' => 'Телефон',
            'inn' => 'ИНН',
            'kpp' => 'КПП',
            'ogrn' => 'ОГРН/ОГРНИП',
            'okved' => 'ОКВЭД',
            'okpo' => 'ОКПО',
            'okato' => 'ОКАТО',
            'checking_account' => 'Расчетный счет',
            'bank' => 'Банк',
            'correspondent_account' => 'Кор. счет',
            'bik' => 'БИК',
            'href' => 'Адрес сайта',
            'comment' => 'Комментарии',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $shouldBeActivated = $this->shouldBeActivated();
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
            $user->status = $shouldBeActivated ? User::STATUS_NOT_ACTIVE : User::STATUS_ACTIVE;
            $user->setPassword($this->password);
            if(!$user->save()) {
                throw new Exception("User couldn't be  saved");
            };


            $agency = new Agency();
            if($this->value_scenario == 1) {
                $agency->scenario = Agency::SCENARIO_REGISTER_OOO;
            } elseif($this->value_scenario == 2) {
                $agency->scenario = Agency::SCENARIO_REGISTER_IP;
            }
            $agency->email = $this->email;
            $agency->chief_name = $this->chief_name;
            $agency->chief_position = $this->chief_position;
            $agency->name = $this->name;
            $agency->travel_network_id = $this->travel_network_id;
            $agency->company_type = $this->company_type;
            $agency->company_tax_type = $this->company_tax_type;
            $agency->cooperation_form = $this->cooperation_form;
            $agency->legal_region = $this->legal_region;
            $agency->legal_city = $this->legal_city;
            $agency->legal_address = $this->legal_address;
            $agency->legal_index = $this->legal_index;
            $agency->actual_region = $this->actual_region;
            $agency->actual_city = $this->actual_city;
            $agency->actual_address = $this->actual_address;
            $agency->actual_index = $this->actual_index;
            $agency->phone = $this->phone;
            $agency->inn = $this->inn;
            $agency->kpp = $this->kpp;
            $agency->ogrn = $this->ogrn;
            $agency->okved = $this->okved;
            $agency->okpo = $this->okpo;
            $agency->okato = $this->okato;
            $agency->checking_account = $this->checking_account;
            $agency->bank = $this->bank;
            $agency->correspondent_account = $this->correspondent_account;
            $agency->bik = $this->bik;
            $agency->href = $this->href;
            $agency->comment = $this->comment;

            if(!$agency->save()) {
                throw new Exception("Agency couldn't be  saved");
            };
            $user->afterSignupAdminAgency();
            if ($shouldBeActivated) {
                $token = UserToken::create(
                    $user->id,
                    UserToken::TYPE_ACTIVATION,
                    Time::SECONDS_IN_A_DAY
                );
                Yii::$app->commandBus->handle(new SendEmailCommand([
                    'subject' => Yii::t('frontend', 'Activation email'),
                    'view' => 'activation',
                    'to' => $this->email,
                    'params' => [
                        'url' => Url::to(['/agency/sign-in/activation', 'token' => $token->token], true)
                    ]
                ]));
            }

            $user_to_agency = new UserToAgency();
            $user_to_agency->user_id = $user->id;
            $user_to_agency->agency_id = $agency->id;
            if(!$user_to_agency->save()) {
                throw new Exception("model UserToAgency couldn't be  saved");
            };

            return $user;
        }

        return null;
    }

    /**
     * @return bool
     */
    public function shouldBeActivated()
    {
        /** @var Module $userModule */
        $userModule = Yii::$app->getModule('user');
        if (!$userModule) {
            return false;
        } elseif ($userModule->shouldBeActivated) {
            return true;
        } else {
            return false;
        }
    }
}
