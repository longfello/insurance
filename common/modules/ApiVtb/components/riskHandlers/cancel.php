<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\components\riskHandlers;
use common\modules\ApiVtb\models\AdditionalCondition;
use common\models\Currency;

/**
 * Class cancel Отмена поездки
 * @package common\modules\ApiVtb\components\riskHandlers
 */
class cancel extends prototype {
    /**
     * @inheritdoc
     */
    protected $api_order = 2;

    /**
     * @inheritdoc
     * @return float|int
     */
    public function getAdditionalPrice(){
        $return = 0;
        $cancel_amount = $this->param->handler->variant['amount'];
        $model = AdditionalCondition::findOne(['name' => 'Отмена поездки']);
        if ($model) $return = ($cancel_amount*$model->params)/100;

        return $return;
    }

    /**
     * @inheritdoc
     * @param $risk_arr
     *
     * @return bool
     */
    public function applyApiSearch(&$risk_arr){
        $cancel_amount = Currency::convert($this->param->handler->variant['amount']);
        $risk_arr[$this->api_order] = '<Cancel>
				    <Type>1</Type>
					<TourCost>' . $cancel_amount . '</TourCost>
				</Cancel>';
        return true;
    }

    /**
     * @inheritdoc
     * @param $risk_arr
     * @param $calc
     *
     * @return bool
     */
    public function applyApiSave(&$risk_arr, $calc) {
        $cancel_amount = Currency::convert($this->param->handler->variant['amount']);
        $risk_arr[$this->api_order] =  '<Cancel>
				    <Type>1</Type>
					<InsSum>'.$calc['Coverage']['Cancel']['InsSum'].'</InsSum>
					<TourCost>' . $cancel_amount . '</TourCost>
					<InsPrem>'.$calc['Coverage']['Cancel']['InsPrem'].'</InsPrem>
				</Cancel>';
        return true;
    }
}