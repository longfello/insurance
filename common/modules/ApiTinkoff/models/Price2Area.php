<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;

/**
 * Соотношение цен территориям
 * This is the model class for table "api_tinkoff_price2area".
 *
 * @property integer $price_id
 * @property integer $area_id
 */
class Price2Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_price2area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_id', 'area_id'], 'required'],
            [['price_id', 'area_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'price_id' => 'Price ID',
            'area_id' => 'Area ID',
        ];
    }
}
