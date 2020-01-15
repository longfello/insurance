<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use Yii;

/**
 * Персоны
 * This is the model class for table "person".
 *
 * @property integer $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birthday
 * @property string $gender
 * @property string $phone
 * @property string $email
 * @property string $passport_seria
 * @property string $passport_no
 */
class Person extends \yii\db\ActiveRecord
{
    /**
     * Плательщик
     */
    const SCENARIO_PAYER     = 'payer';
    /**
     * Путешественник
     */
    const SCENARIO_TRAVELLER = 'traveller';

    /**
     * Плательщик при выборе api ВТБ
     */
    const SCENARIO_PAYER_VTB = 'payer_vtb';

    /**
     * Плательщик при выборе api Сбербанка
     */
    const SCENARIO_PAYER_SBERBANK = 'payer_sberbank';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'person';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'birthday', 'gender'], 'required'],
            [['birthday'], 'safe'],
	        [['email'], 'email'],
            [['first_name', 'last_name', 'email'], 'string', 'max' => 200],
            [['phone'], 'validatePhone'],
            [['passport_seria', 'passport_no'], 'string', 'max' => 15],
	        [['first_name', 'last_name'], 'match',
		        'pattern' => '/^[a-zA-Z\-\s]+$/',
		        'message' => 'Допускаются только символы латинницы',
                'on' => [self::SCENARIO_PAYER, self::SCENARIO_TRAVELLER, self::SCENARIO_DEFAULT]
            ],
            [['first_name', 'last_name'], 'match',
                'pattern' => '/^[а-яА-Я\-\s]+$/u',
                'message' => 'Допускаются только символы киррилицы',
                'on' => [self::SCENARIO_PAYER_SBERBANK]
            ],
            [['gender'], 'in', 'range'=>['male', 'female']],
	        [['email', 'phone', 'passport_no'], 'required', 'on' => self::SCENARIO_PAYER],
        ];
    }

    /**
     * Валидатор телефона
     * @param $attribute
     * @param $params
     */
    public function validatePhone($attribute, $params)
    {
        $phone = str_replace('_','',$this->phone);
        if (strlen($phone)<16) {
            $this->addError($attribute, 'Необходимо заполнить "Телефон"');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'             => 'ID',
            'first_name'     => 'Имя',
            'last_name'      => 'Фамилия',
            'birthday'       => 'Дата рождения',
            'gender'         => 'Пол',
            'phone'          => 'Телефон',
            'email'          => 'Email',
            'passport_seria' => 'Серия паспорта',
            'passport_no'    => 'Номер паспорта',
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
		$scenarios          = parent::scenarios();
		$scenarios[self::SCENARIO_TRAVELLER] = [ 'first_name', 'last_name', 'birthday', 'gender'];
		$scenarios[self::SCENARIO_PAYER]     = [ 'first_name', 'last_name', 'birthday', 'gender', 'email', 'phone', 'passport_no'];
		$scenarios[self::SCENARIO_PAYER_VTB] = [ 'first_name', 'last_name', 'birthday', 'gender', 'email', 'phone', 'passport_no'];
		$scenarios[self::SCENARIO_PAYER_SBERBANK] = [ 'first_name', 'last_name', 'birthday', 'gender', 'email', 'phone', 'passport_no'];
		return $scenarios;
	}

    /**
     * Возвращает дату рождения в заданом формате
     * @param string $format
     *
     * @return string
     */
    public function birthdayAsDate($format = 'd.m.Y'){
    	$ts = \DateTime::createFromFormat('Y-m-d', $this->birthday);
    	if ($ts){
    		return $ts->format($format);
	    } else return '';
	}
}
