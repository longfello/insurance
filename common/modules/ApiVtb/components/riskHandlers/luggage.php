<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\components\riskHandlers;
use common\modules\ApiVtb\models\Program;

/**
 * Class luggage Багаж
 * @package common\modules\ApiVtb\components\riskHandlers
 */
class luggage extends prototype {
    /**
     * @inheritdoc
     */
    protected $api_order=1;

    /**
     * @inheritdoc
     * @return float|int
     */
    public function getAdditionalPrice(){
        $program = Program::findOne(['id' => $this->price->program_id]);
        $return = 0;
        if($program->baggage_sum>0) {
            $return = 3*($program->baggage_sum/500);
        }
        return $return;
    }

    /**
     * @inheritdoc
     * @param $risk_arr
     *
     * @return bool
     */
    public function applyApiSearch(&$risk_arr){
        $program = Program::findOne(['id' => $this->price->program_id]);
        $risk_arr[$this->api_order]='<Luggage>
                                        <InsSum>'.$program->baggage_sum.'</InsSum>
                                    </Luggage>';
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
        $program = Program::findOne(['id' => $this->price->program_id]);
        $risk_arr[$this->api_order] = '<Luggage>
				   <InsSum>'.$program->baggage_sum.'</InsSum>
				   <InsPrem>'.$calc['Coverage']['Luggage']['InsPrem'].'</InsPrem>
			</Luggage>';
        return true;
    }
}