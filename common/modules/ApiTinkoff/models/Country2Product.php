<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;

/**
 * Соотношение страны АПИ продукту АПИ
 * This is the model class for table "api_tinkoff_country2product".
 *
 * @property integer $country_id
 * @property integer $product_id
 */
class Country2Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_country2product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'product_id'], 'required'],
            [['country_id', 'product_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'country_id' => 'Id страны',
            'product_id' => 'Id продукта',
        ];
    }
}
