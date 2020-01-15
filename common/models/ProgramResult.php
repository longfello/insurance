<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 28.02.17
 * Time: 10:32
 */

namespace common\models;


use common\components\Calculator\forms\TravelForm;
use yii\base\Model;
use yii\helpers\VarDumper;

/**
 * Class ProgramResult Результат поиска
 * @package common\models
 */
class ProgramResult extends Model {

	/**
	 * @var integer идентификатор API
	 */
	public $api_id;
	/**
	 * @var integer идентификатор программы
	 */
	public $program_id;
	/**
	 * @var string рейтинг эксперта
	 */
	public $rate_expert;
	/**
	 * @var string рейтинг АСН
	 */
	public $rate_asn;
	/**
	 * @var string ссылка на превью
	 */
	public $thumbnail_url;
	/**
	 * @var string ссылка на правила
	 */
	public $rule_url;
	/**
	 * @var string ссылка на пример полиса
	 */
	public $police_url;
	/**
	 * @var string[] Включенные риски. ключ - название, значение - страховая сумма
	 */
	public $risks;
	/**
	 * @var string html текст возможных действий
	 */
	public $actions;
	/**
	 * @var float стоимость полиса
	 */
	public $cost;
	/**
	 * @var string[] Телефоны. Ключ - название, значение - ноиер телефона
	 */
	public $phones;
	/**
	 * @var TravelForm Модель формы
	 */
	public $calc;
	/**
	 * @var int|null
	 */
	public $order_id;


    /**
     * Загрузка из JSON
     *
     * @param string $data
     * @param string|bool $scenario
     */
    public function loadFromJson($data, $scenario = false){
		$data = unserialize(base64_decode($data));
		foreach ($data as $key => $value){
			if (property_exists($this, $key)){
				$this->$key = $value;
			}
		}
		if ($scenario) {
			$this->calc->scenario = $scenario;
		}
	}

    /**
     * Создает и возвращает заказ
     * @return bool|Orders|false
     */
    public function getOrder(){
		$api     = Api::findOne(['id' => $this->api_id]);
		if ($api) {
			$module  = $api->getModule();
			if ($module){
				return $module->getOrder($this->calc, $this->program_id);
			}
		}
		return false;
	}

	/**
	 * Api model
	 * return \common\models\Api
	 */
    public function getApi() {
        return Api::findOne(['id' => $this->api_id]);
    }
}