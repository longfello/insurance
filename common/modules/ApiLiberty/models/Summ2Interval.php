<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Соответствие сумм страховым интервалам
 * This is the model class for table "api_liberty_summ2interval".
 *
 * @property integer $summ_id
 * @property integer $cost_id
 */
class Summ2Interval extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_summ2interval';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['summ_id', 'cost_id'], 'required'],
            [['summ_id', 'cost_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'summ_id' => 'Id страховой суммы',
            'cost_id' => 'Id страхового интервала',
        ];
    }
}
