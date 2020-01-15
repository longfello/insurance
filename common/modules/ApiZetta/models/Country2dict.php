<?php

namespace common\modules\ApiZetta\models;

use Yii;
use common\models\GeoCountry;

/**
 * Соответствие стране во внутреннем справочнике
 * This is the model class for table "api_zetta_country2dict".
 *
 * @property int $internal_id
 * @property int $country_id
 */
class Country2dict extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_zetta_country2dict';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['internal_id', 'country_id'], 'required'],
            [['internal_id', 'country_id'], 'integer'],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['internal_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'internal_id' => Yii::t('backend', 'ID во внутреннем справочнике'),
            'country_id' => Yii::t('backend', 'ID страны по АПИ')
        ];
    }

    /**
     * Страна во внутреннем справочнике
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getGeoNameModel() {
        return $this->hasOne(GeoCountry::className(), ['id' => 'internal_id']);
    }

    /**
     * Страна АПИ
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getApiModel() {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

}
