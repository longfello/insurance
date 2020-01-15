<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\components\riskHandlers;

/**
 * Class racedelay Задержка рейса
 * @package common\modules\ApiVtb\components\riskHandlers
 */
class racedelay extends prototype {
    /**
     * @inheritdoc
     */
    protected $api_order = 5;

    /**
     * @inheritdoc
     * @return int
     */
    public function getAdditionalPrice(){
        return 1*$this->form->travellersCount;
    }

    /**
     * @inheritdoc
     * @param $risk_arr
     *
     * @return bool
     */
    public function applyApiSearch(&$risk_arr){
        $risk_arr[$this->api_order]='<RaceDelay>
                                        <InsSum>500</InsSum>
                                    </RaceDelay>';
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
        $risk_arr[$this->api_order] = '<RaceDelay>
				   <InsSum>500</InsSum>
				   <InsPrem>'.$calc['Coverage']['RaceDelay']['InsPrem'].'</InsPrem>
			</RaceDelay>';
        return true;
    }
}