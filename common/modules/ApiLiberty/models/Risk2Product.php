<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Соответствие рисков продукту
 * This is the model class for table "api_liberty_risk2product".
 *
 * @property integer $riskId
 * @property integer $productId
 * @property integer $required
 */
class Risk2Product extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_risk2product';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['riskId', 'productId'], 'required'],
            [['riskId', 'productId', 'required'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'riskId' => 'Ид риска',
            'productId' => 'Ид продукта',
            'required' => 'Тарифная группа',
        ];
    }
}
