<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace frontend\models;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\models\Person;

/**
 * ContactForm is the model behind the contact form.
 *
 */
class PersonInfo extends Person
{
/*
    public $first_name;
    public $last_name;
    public $birthday;
	public $phone;
	public $email;
	public $passport_seria;
	public $passport_no;
*/
    /**
     * @inheritdoc
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
	        [['first_name', 'last_name', 'birthday', 'gender'], 'required'],
	        [['birthday'], 'validateDate'],
//	        [['birthday'], 'date', 'format' => 'd.m.Y'],
	        [['email'], 'email'],
	        [['phone'], 'validatePhone'],
	        [['passport_no'], 'string', 'max' => 15],
	        [['first_name', 'last_name'], 'match', 'pattern' => '/^[a-zA-Z\-\s]+$/', 'message' => 'Допускаются только символы латинницы', 'on' => [self::SCENARIO_PAYER, self::SCENARIO_TRAVELLER, self::SCENARIO_DEFAULT]],
	        [['first_name', 'last_name'], 'string', 'min' => '1'],
	        [['first_name', 'last_name'], 'string', 'max' => '50'],
			[['gender'], 'in', 'range'=>['male', 'female']],
			['birthday', 'validate90Age'],
	        [['email', 'phone', 'passport_no'], 'required', 'on' => self::SCENARIO_PAYER],
			['birthday', 'validate18Age', 'on' => self::SCENARIO_PAYER]
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
     * Валидатор 18 плюс
     * @param $attribute
     * @param $params
     */
    public function validate18Age($attribute, $params)
	{
		$cdate = new \DateTime();

		$bdate = \DateTime::createFromFormat('d.m.Y', trim($this->birthday));
		if ($bdate) {
			$interval = $bdate->diff($cdate);
			if ($interval->y<18) {
				$this->addError($attribute, 'Страхователь должен быть старше 18 лет');
			}
		}
		$bdate = \DateTime::createFromFormat('Y-m-d', trim($this->birthday));
		if ($bdate){
			$interval = $bdate->diff($cdate);
			if ($interval->y<18) {
				$this->addError($attribute, 'Страхователь должен быть старше 18 лет');
			}
		}
	}

    /**
     * Валидатор 90 минус
     * @param $attribute
     * @param $params
     */
    public function validate90Age($attribute, $params)
	{
		$cdate = new \DateTime();

		$bdate = \DateTime::createFromFormat('d.m.Y', trim($this->birthday));
		if ($bdate) {
			$interval = $bdate->diff($cdate);
			if ($interval->y>90) {
				$this->addError($attribute, 'Возраст не должен быть больше 90 лет');
			}
		}
		$bdate = \DateTime::createFromFormat('Y-m-d', trim($this->birthday));
		if ($bdate){
			$interval = $bdate->diff($cdate);
			if ($interval->y>90) {
				$this->addError($attribute, 'Возраст не должен быть больше 90 лет');
			}
		}
	}

    /**
     * Валидация даты
     * @param $attribute
     * @param $params
     */
    public function validateDate($attribute, $params){
	    $date = \DateTime::createFromFormat('d.m.Y', trim($this->birthday));
	    if ($date){
		    $errors = \DateTime::getLastErrors();
		    if (empty($errors['warning_count'])) {
			    return;
		    }
	    }
	    $date = \DateTime::createFromFormat('Y-m-d', trim($this->birthday));
	    if ($date){
		    $errors = \DateTime::getLastErrors();
		    if (empty($errors['warning_count'])) {
			    return;
		    }
	    }
	}

    /**
     * @inheritdoc
     * @param array $data
     * @param null $formName
     *
     * @return bool
     */public function load($data, $formName = null){

    	$result = parent::load($data, $formName);

		$date = \DateTime::createFromFormat('d.m.Y', trim($this->birthday));
		$this->birthday = null;
		if ($date){
			$errors = \DateTime::getLastErrors();
			if (empty($errors['warning_count'])) {
				$this->birthday = $date->format('Y-m-d');
			}
		}

		return $result;
	}

}
