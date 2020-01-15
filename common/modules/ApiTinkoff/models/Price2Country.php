<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;

/**
 * Соотношение цен странам
 * This is the model class for table "api_tinkoff_price2country".
 *
 * @property integer $price_id
 * @property integer $country_id
 */
class Price2Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_price2country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_id', 'country_id'], 'required'],
            [['price_id', 'country_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'price_id' => 'Price ID',
            'country_id' => 'Country ID',
        ];
    }
}
