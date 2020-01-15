<?php

namespace common\modules\ApiZetta\models;

use Yii;

/**
 * Суммы страхования
 * This is the model class for table "api_zetta_sum".
 *
 * @property int $currency_id
 * @property int $sum
 * @property int $enabled
 */
class Sum extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_zetta_sum';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['currency_id', 'sum'], 'required'],
            [['currency_id', 'sum', 'enabled'], 'integer'],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']],
            [['enabled'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'currency_id' => Yii::t('backend', 'Внутренний ID валюты'),
            'sum' => Yii::t('backend', 'Сумма страхования'),
            'enabled' => Yii::t('backend', 'Разрешена')
        ];
    }

    /**
     * Валюта
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getCurrencyModel() {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

}
