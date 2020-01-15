<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\components\riskHandlers;

use common\components\Calculator\models\travel\FilterParam;
use common\modules\ApiVtb\models\Price;
use common\components\Calculator\forms\TravelForm;
use yii\base\Component;

/**
 * Class prototype Прототип обработчиков рисков
 * @package common\modules\ApiVtb\components\riskHandlers
 */
class prototype extends Component
{
    /**
     * @var int Порядок сортировки
     */
    protected $api_order = 0;

    /** @var TravelForm модель формы параметров */
    public $form;

    /** @var Price стоимость */
    public $price;

    /** @var FilterParam параметр фильтра */
    public $param;

    /**
     * Возвращает добавленную стоимость
     * @return int
     */
    public function getAdditionalPrice(){
        return 0;
    }

    /**
     * Добавляет параметр в параметры запроса к АПИ при поиске
     * @param $risk_arr
     *
     * @return bool
     */
    public function applyApiSearch(&$risk_arr) {
        $risk_arr[$this->api_order]='';
        return true;
    }

    /**
     * Добавляет параметр в параметры запроса к АПИ при оформлении
     * @param $risk_arr
     * @param $calc
     *
     * @return bool
     */
    public function applyApiSave(&$risk_arr, $calc) {
        $risk_arr[$this->api_order]='';
        return true;
    }
}