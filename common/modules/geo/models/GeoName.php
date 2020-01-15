<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\models;

use common\components\MLModel;
use Yii;

/**
 * Локации
 * This is the model class for table "geo_name".
 *
 * @property integer $id
 * @property string $name
 * @property integer $population
 * @property string $timezone
 * @property integer $zone_id
 * @property integer $country_id
 * @property string $google_id
 * @property string $slug
 * @property string $big_banner_url
 * @property string $small_banner_url
 * @property string $domain
 *
 * @property GeoZone $zoneModel
 * @property GeoCountry $countryModel
 * @property GeoNameSparital $sparital
 * @property GeoNameAlter $altername
 */
class GeoName extends MLModel
{
    /**
     * @inheritdoc
     */
    public $MLattributes = ['name'];
    /**
     * @inheritdoc
     */
    public $MLfk = 'name_id';

    /**
     * @var float завершающая широта
     */
    public $end_lat;
    /**
     * @var float завершающая долгота
     */
    public $end_lng;
    /**
     * @var float начальная широта
     */
    public $start_lat;
    /**
     * @var float начальная долгота
     */
    public $start_lng;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo_name';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'slug'], 'required'],
            [['id', 'population', 'zone_id', 'country_id'], 'integer'],
            [['name'], 'string', 'max' => 200],
            [['slug', 'small_banner_url', 'big_banner_url', 'domain', 'google_id'], 'string', 'max' => 255],
            [['timezone'], 'string', 'max' => 40],
            [['slug'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название (en)',
            'name_ru' => 'Название (ru)',
            'name_kz' => 'Название (kz)',
            'population' => 'Население',
            'timezone' => 'Времянная зона',
            'zone_id' => 'Область/регион',
            'country_id' => 'Страна',
            'slug' => 'Псевдоним',
            'google_id' => 'Идентификатор Google Maps',
            'domain' => 'Домен',
            'small_banner_url' => 'Маленький баннер',
            'big_banner_url' => 'Большой баннер',
        ];
    }

    /**
     * Область
     * @return \yii\db\ActiveQuery
     */
    public function getZoneModel()
    {
        return $this->hasOne(GeoZone::className(), ['id' => 'zone_id']);
    }

    /**
     * Страна
     * @return \yii\db\ActiveQuery
     */
    public function getCountryModel()
    {
        return $this->hasOne(GeoCountry::className(), ['id' => 'country_id']);
    }

    /**
     * Гео-данные
     * @return \yii\db\ActiveQuery
     */
    public function getSparital()
    {
        return $this->hasOne(GeoNameSparital::className(), ['name_id' => 'id']);
    }

    /**
     * Альтернативные названия
     * @return \yii\db\ActiveQuery
     */
    public function getAltername()
    {
        return $this->hasOne(GeoNameAlter::className(), ['name_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return GeoNameQuery
     */
    public static function find() {
        return new GeoNameQuery(get_called_class());
    }
}
