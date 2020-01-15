<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "agency".
 *
 * @property int $id
 * @property int $travel_network_id Сеть туристических агентств
 * @property string $company_type Организационная форма компании
 * @property string $company_tax_type Форма налогооблажения
 * @property string $cooperation_form Форма Сотрудничества
 * @property string $chief_name ФИО Руководителя
 * @property string $chief_position Должность Руководителя
 * @property string $name Наименование компании (без орг.формы
 * @property string $legal_region Юридический адрес - регион
 * @property string $legal_city Юридический адрес - город
 * @property string $legal_address Юридический адрес
 * @property string $legal_index Юридический адрес - индекс
 * @property string $actual_region Фактический адрес - регион
 * @property string $actual_city Фактический адрес - город
 * @property string $actual_address Фактический адрес
 * @property string $actual_index Фактический адрес - индекс
 * @property string $phone Телефон
 * @property string $email Email
 * @property string $inn ИНН
 * @property string $kpp КПП
 * @property string $ogrn ОГРН/ОГРНИП
 * @property string $okved ОКВЭД
 * @property string $okpo ОКПО
 * @property string $okato ОКАТО
 * @property string $checking_account Расчетный счет
 * @property string $bank Банк
 * @property string $correspondent_account Кор. счет
 * @property string $bik БИК
 * @property string $href Адрес сайта
 * @property string $comment Комментарии
 *
 * @property TravelNetworks $travelNetwork
 */
class Agency extends \yii\db\ActiveRecord
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
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'agency';
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [


            [['travel_network_id', 'company_type', 'company_tax_type', 'cooperation_form', 'chief_name', 'chief_position', 'name', 'legal_city', 'legal_address', 'legal_index', 'actual_city', 'actual_address', 'actual_index', 'phone', 'email', 'inn', 'ogrn', 'checking_account', 'bank', 'correspondent_account', 'bik'], 'required'],
            [['travel_network_id'], 'integer'],
            [['company_type', 'company_tax_type', 'cooperation_form', 'comment'], 'string'],
            [['chief_name', 'chief_position', 'name', 'legal_region', 'legal_city', 'legal_address', 'legal_index', 'actual_region', 'actual_city', 'actual_address', 'actual_index', 'phone', 'email', 'okved', 'bank', 'href'], 'string', 'max' => 255],
            [['inn'], 'string', 'max' => 12, 'on' => self::SCENARIO_REGISTER_IP],
            [['inn'], 'string', 'max' => 10, 'on' => self::SCENARIO_REGISTER_OOO],
            [['kpp', 'bik'], 'string', 'max' => 9],
            [['ogrn'], 'string', 'max' => 16, 'on' => self::SCENARIO_REGISTER_IP],
            [['ogrn'], 'string', 'max' => 13, 'on' => self::SCENARIO_REGISTER_OOO],
            [['okpo'], 'string', 'max' => 10, 'on' => self::SCENARIO_REGISTER_IP],
            [['okpo'], 'string', 'max' => 8, 'on' => self::SCENARIO_REGISTER_OOO],
            [['okato'], 'string', 'max' => 11],
            [['checking_account', 'correspondent_account'], 'string', 'max' => 20],
            [['travel_network_id'], 'exist', 'skipOnError' => true, 'targetClass' => TravelNetworks::className(), 'targetAttribute' => ['travel_network_id' => 'id']],

        ]; 
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REGISTER_OOO] = ['travel_network_id', 'company_type', 'company_tax_type', 'cooperation_form','comment', 'chief_name', 'chief_position', 'name', 'legal_city','legal_region', 'legal_address', 'legal_index', 'actual_city','actual_region', 'actual_address', 'actual_index', 'phone', 'email','href', 'inn', 'ogrn','okpo','okato','okved', 'checking_account', 'bank', 'correspondent_account','kpp', 'bik'];
        $scenarios[self::SCENARIO_REGISTER_IP] = ['travel_network_id', 'company_type', 'company_tax_type', 'cooperation_form','comment', 'chief_name', 'chief_position', 'name', 'legal_city','legal_region', 'legal_address', 'legal_index', 'actual_city','actual_region', 'actual_address', 'actual_index', 'phone', 'email','href', 'inn', 'ogrn','okpo','okato','okved', 'checking_account', 'bank', 'correspondent_account', 'bik'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'travel_network_id' => 'Сеть туристических агентств',
            'company_type' => 'Организационная форма компании',
            'company_tax_type' => 'Форма налогооблажения',
            'cooperation_form' => 'Форма Сотрудничества',
            'chief_name' => 'ФИО Руководителя',
            'chief_position' => 'Должность Руководителя',
            'name' => 'Наименование компании (без орг.формы',
            'legal_region' => 'Юридический адрес - регион',
            'legal_city' => 'Юридический адрес - город',
            'legal_address' => 'Юридический адрес',
            'legal_index' => 'Юридический адрес - индекс',
            'actual_region' => 'Фактический адрес - регион',
            'actual_city' => 'Фактический адрес - город',
            'actual_address' => 'Фактический адрес',
            'actual_index' => 'Фактический адрес - индекс',
            'phone' => 'Телефон',
            'email' => 'Email',
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
     * @return \yii\db\ActiveQuery
     */
    public function getTravelNetwork()
    {
        return $this->hasOne(TravelNetworks::className(), ['id' => 'travel_network_id']);
    }

    /**
     * Возвращает данные агенства по идентификатору admin_agency
     * @param  int $id  user by admin_agency
     * @return array
     */
    public function getDataAgency($id)
    {
        return  Yii::$app->db->createCommand("
            SELECT a.* FROM  agency a
            LEFT JOIN user_to_agency uta ON uta.agency_id = a.id  
            LEFT JOIN user u ON u.id = uta.user_id 
            WHERE  u.id = :id            
            ", [':id' => $id])->queryOne();
    }
}
