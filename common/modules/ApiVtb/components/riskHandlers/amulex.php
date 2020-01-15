<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\components\riskHandlers;

/**
 * Class amulex Обработка параметра Amulex
 * @package common\modules\ApiVtb\components\riskHandlers
 */
class amulex extends prototype {
    /**
     * @inheritdoc
     */
    protected $api_order = 4;

    /**
     * @inheritdoc
     * @return float|int
     */
    public function getAdditionalPrice(){
        if ($this->form->dayCount<=8) {
            $price_for_day_amul = 2;
        } elseif ($this->form->dayCount<=15) {
            $price_for_day_amul = 1.96;
        } elseif ($this->form->dayCount<=30) {
            $price_for_day_amul = 1.68;
        } elseif ($this->form->dayCount<=365) {
            $price_for_day_amul = 1.24;
        } else $price_for_day_amul = 1.22;

        if ($this->form->dayCount<=30) {
            $price_for_day = 0.5;
        } else $price_for_day = 0.45;

        return $this->form->dayCount*($price_for_day*$this->form->travellersCount + $price_for_day_amul);
    }

    /**
     * @inheritdoc
     * @param $risk_arr
     *
     * @return bool
     */
    public function applyApiSearch(&$risk_arr){
        $risk_arr[$this->api_order]='<Amulex>
                                        <InsSum>50000</InsSum>
                                    </Amulex>';
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
        $risk_arr[$this->api_order] = '<Amulex>
				   <InsSum>50000</InsSum>
				   <InsPrem>'.$calc['Coverage']['Amulex']['InsPrem'].'</InsPrem>
			</Amulex>';
        return true;
    }
}