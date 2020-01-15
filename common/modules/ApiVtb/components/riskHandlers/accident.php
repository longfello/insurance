<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\components\riskHandlers;

/**
 * Class accident несчастный случай
 * @package common\modules\ApiVtb\components\riskHandlers
 */
class accident extends prototype {
    /**
     * @inheritdoc
     */
    protected $api_order = 3;

    /**
     * @inheritdoc
     */
    public function getAdditionalPrice(){
        if ($this->form->dayCount<=30) {
            $price_for_day = 0.55;
        } else {
            $price_for_day = 0.5;
        }

        return $price_for_day*$this->form->dayCount*$this->form->travellersCount;
    }

    /**
     * @inheritdoc
     * @param $risk_arr
     *
     * @return bool
     */
    public function applyApiSearch(&$risk_arr){
        $risk_arr[$this->api_order]='<Accident>
                                        <Type>2</Type>
                                    </Accident>';
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
        $risk_arr[$this->api_order] = '<Accident>
				   <Type>2</Type>
				   <InsSum>'.$calc['Coverage']['Accident']['InsSum'].'</InsSum>
				   <InsPrem>'.$calc['Coverage']['Accident']['InsPrem'].'</InsPrem>
			</Accident>';
        return true;
    }
}