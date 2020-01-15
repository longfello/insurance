<?php

namespace common\modules\ApiZetta\models;

use Yii;
use common\models\Currency as CommonCurrency;

/**
 * Соответствие стране во внутреннем справочнике
 * This is the model class for table "api_zetta_currency2dict".
 *
 * @property int $internal_id
 * @property int $currency_id
 */
class Currency2dict extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_zetta_currency2dict';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['internal_id', 'currency_id'], 'required'],
            [['internal_id', 'currency_id'], 'integer'],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => CommonCurrency::className(), 'targetAttribute' => ['internal_id' => 'id']],
            [['currency_id'], 'exist', 'skipOnError' => true, 'targetClass' => Currency::className(), 'targetAttribute' => ['currency_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'internal_id' => Yii::t('backend', 'ID во внутреннем справочнике'),
            'currency_id' => Yii::t('backend', 'ID валюты по АПИ')
        ];
    }

    /**
     * Валюта во внутреннем справочнике
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getCurrencyModel() {
        return $this->hasOne(CommonCurrency::className(), ['id' => 'internal_id']);
    }

    /**
     * Валюта АПИ
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getApiModel() {
        return $this->hasOne(Currency::className(), ['id' => 'currency_id']);
    }

}
