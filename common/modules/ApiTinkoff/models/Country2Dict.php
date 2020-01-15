<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use common\models\GeoCountry;
use Yii;

/**
 * Соотношение страны АПИ стране внутреннего справочника
 * This is the model class for table "api_tinkoff_country2dict".
 *
 * @property integer $internal_id
 * @property integer $country_id
 *
 * @property GeoCountry $geoNameModel
 * @property Country $apiModel
 */
class Country2Dict extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_country2dict';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['internal_id', 'country_id'], 'required'],
            [['internal_id', 'country_id'], 'integer'],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['internal_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'internal_id' => 'Id из внутреннего справочника',
            'country_id' => 'Id страны',
        ];
    }

    /**
     * Страна внутреннего справочника
     * @return \yii\db\ActiveQuery
     */
    public function getGeoNameModel()
    {
        return $this->hasOne(GeoCountry::className(), ['id' => 'internal_id']);
    }

    /**
     * Страна АПИ
     * @return \yii\db\ActiveQuery
     */
    public function getApiModel(){
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }
}
