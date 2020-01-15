<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;

/**
 * Соотношение цен рискам
 * This is the model class for table "api_tinkoff_price2risk".
 *
 * @property integer $price_id
 * @property integer $risk_id
 * @property integer $amount
 *
 *
 * @property Risk $risk
 */
class Price2Risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_price2risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_id', 'risk_id'], 'required'],
            [['price_id', 'risk_id', 'amount'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'price_id' => 'Price ID',
            'risk_id' => 'Risk ID',
            'amount' => 'Amount',
        ];
    }
}
