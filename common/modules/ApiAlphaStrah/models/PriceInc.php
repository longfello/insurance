<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;

/**
 * Страховая сумма дополнительных рисков
 * This is the model class for table "api_alpha_price_inc".
 *
 * @property integer $price_id
 * @property string $name
 * @property integer $amount
 * @property integer $filter_id
 *
 * @property Price $price
 */
class PriceInc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_price_inc';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_id', 'name', 'amount'], 'required'],
            [['price_id', 'amount', 'filter_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Price::className(), 'targetAttribute' => ['price_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'price_id' => 'Price ID',
            'name' => 'Name',
            'amount' => 'Amount',
            'filter_id' => 'Filter ID'
        ];
    }

    /**
     * Основная сумма
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
    {
        return $this->hasOne(Price::className(), ['id' => 'price_id']);
    }
}
