<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;

/**
 * Соотношение риска АПИ продуктам АПИ
 * This is the model class for table "api_tinkoff_risk2product".
 *
 * @property integer $risk_id
 * @property integer $product_id
 */
class Risk2Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_risk2product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['risk_id', 'product_id'], 'required'],
            [['risk_id', 'product_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'risk_id' => 'Id риска',
            'product_id' => 'Id продукта',
        ];
    }
}
