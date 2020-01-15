<?php

namespace common\models;

use Yii;

/**
 * Связь территорий и включенных в них стран
 * This is the model class for table "geo_territory2country".
 *
 * @property integer $geo_territory_id
 * @property integer $geo_country_id
 *
 * @property GeoCountry $geoTerritory
 * @property GeoCountry $geoCountry
 */
class GeoTerritory2country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo_territory2country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['geo_territory_id', 'geo_country_id'], 'required'],
            [['geo_territory_id', 'geo_country_id'], 'integer'],
            [['geo_territory_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['geo_territory_id' => 'id']],
            [['geo_country_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['geo_country_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'geo_territory_id' => 'Geo Territory ID',
            'geo_country_id' => 'Geo Country ID',
        ];
    }

    /**
     * Территория
     * @return \yii\db\ActiveQuery
     */
    public function getGeoTerritory()
    {
        return $this->hasOne(GeoCountry::className(), ['id' => 'geo_territory_id']);
    }

    /**
     * Страна
     * @return \yii\db\ActiveQuery
     */
    public function getGeoCountry()
    {
        return $this->hasOne(GeoCountry::className(), ['id' => 'geo_country_id']);
    }
}
