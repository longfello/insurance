<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Страховые суммы
 * This is the model class for table "api_liberty_summ".
 *
 * @property integer $id
 * @property integer $riskId
 * @property integer $productId
 * @property integer $countryId
 * @property integer $amount
 *
 *
 * @property Product $product
 */
class Summ extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_summ';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['riskId', 'productId', 'countryId', 'amount'], 'required'],
            [['riskId', 'productId', 'countryId', 'amount'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'riskId' => 'Ид риска',
            'productId' => 'Ид продукта',
            'countryId' => 'Ид страны',
            'amount' => 'Страховая сумма'
        ];
    }

    /**
     * Продукт
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productId' => 'productId']);
    }
}
