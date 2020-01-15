<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 10.04.17
 * Time: 11:12
 */

namespace common\modules\ApiVtb\components;


use common\components\Calculator\filters\params\travel\FilterParamPrototype;

/**
 * Class SportAdditionalCondition Дополнительный параметр Занятия спортом
 * @package common\modules\ApiVtb\components
 */
class SportAdditionalCondition extends AdditionalConditionPrototype {

    /**
     * @inheritdoc
     * @return int
     */
    public function getKoef(){
        $koeficient = 1;
        foreach($this->form->params as $param){
            if ($param->handler->slug==FilterParamPrototype::SLUG_SPORT && $param->handler->checked) {
                $koeficient = (int)$this->params;
            }
        }
        return $koeficient;
    }
}