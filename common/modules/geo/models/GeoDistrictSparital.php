<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\models;

use Yii;

/**
 * Геобаза районов
 * This is the model class for table "geo_district_sparital".
 *
 * @property integer $district_id
 * @property string $latitude
 * @property string $longitude
 * @property string $coord
 */
class GeoDistrictSparital extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo_district_sparital';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['district_id'], 'required'],
            [['district_id'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['coord'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'district_id' => 'District ID',
            'latitude' => 'Latitude',
            'longitude' => 'Longitude',
            'coord' => 'Coord',
        ];
    }
}
