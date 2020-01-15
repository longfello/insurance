<?php

namespace common\modules\ApiZetta\models;

use Yii;

/**
 * Соответствие валют странам
 * This is the model class for table "api_zetta_country_currency".
 *
 * @property int $country_id
 * @property int $currency_id
 */
class CountryCurrency extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_zetta_country_currency';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['country_id', 'currency_id'], 'required'],
            [['country_id', 'currency_id'], 'integer'],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'program_id' => Yii::t('backend', 'Внутренний ID страны'),
            'currency_id' => Yii::t('backend', 'Внутренний ID валюты')
        ];
    }

    /**
     * Страна
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getCountryModel() {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
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
