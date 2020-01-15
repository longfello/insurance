<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;

/**
 * Соотношение региона АПИ продукту АПИ
 * This is the model class for table "api_tinkoff_area2product".
 *
 * @property integer $area_id
 * @property integer $product_id
 */
class Area2Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_area2product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['area_id', 'product_id'], 'required'],
            [['area_id', 'product_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'area_id' => 'Id региона',
            'product_id' => 'Id продукта',
        ];
    }
}
