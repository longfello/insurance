<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 10.04.17
 * Time: 11:22
 */

namespace common\modules\ApiInGosStrah\components;


use common\components\Calculator\forms\TravelForm;
use yii\base\Component;

/**
 * Прототип дополнительных условий
 * Class AdditionalConditionPrototype
 * @package common\modules\ApiInGosStrah\components
 */
class AdditionalConditionPrototype extends Component {
	/** @var TravelForm модель формы параметров поиска */
	public $form;
	/** @var array параметры */
	public $params = [];
	/** @var float Базовая стоимость */
	public $baseAmount;

    /**
     * @inheritdoc
     */
    public function init() {
		parent::init();
		$this->params = $this->params?json_decode($this->params):[];
	}

    /**
     * Коэфициент изменения стоимости
     * @return float
     */
    public function getKoef(){
		return 1;
	}
}