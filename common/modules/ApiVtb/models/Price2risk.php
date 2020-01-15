<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use Yii;

/**
 * Соответствие цены рискам
 * This is the model class for table "api_vtb_price2risk".
 *
 * @property integer $price_id
 * @property integer $risk_id
 * @property integer $amount
 *
 * @property Price $price
 * @property Risk $risk
 */
class Price2risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_price2risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price_id', 'risk_id', 'amount'], 'required'],
            [['price_id', 'risk_id', 'amount'], 'integer'],
            [['price_id'], 'exist', 'skipOnError' => true, 'targetClass' => Price::className(), 'targetAttribute' => ['price_id' => 'id']],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'id']],
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

    /**
     * Цена
     * @return \yii\db\ActiveQuery
     */
    public function getPrice()
    {
        return $this->hasOne(Price::className(), ['id' => 'price_id']);
    }

    /**
     * Риск
     * @return \yii\db\ActiveQuery
     */
    public function getRisk()
    {
        return $this->hasOne(Risk::className(), ['id' => 'risk_id']);
    }
}
