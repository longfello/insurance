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

namespace common\modules\ApiAlphaStrah\components;


use common\components\Calculator\forms\TravelForm;
use yii\base\Component;

/**
 * Class AdditionalConditionPrototype Прототип дополнительных параметров
 * @package common\modules\ApiAlphaStrah\components
 */
class AdditionalConditionPrototype extends Component {
	/** @var TravelForm Модель формы */
	public $form;
	/** @var array Парамтры  */
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
     * @return float Коеффициент изменения стоимости
     */
    public function getKoef(){
		return 1;
	}
}