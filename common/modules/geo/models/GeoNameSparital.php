<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\models;

use Yii;

/**
 * Гео-данніе локаций
 * This is the model class for table "geo_name_sparital".
 *
 * @property integer $name_id
 * @property string $latitude
 * @property string $longitude
 * @property integer $dia
 * @property string $coord
 * @property string $area
 */
class GeoNameSparital extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo_name_sparital';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_id', 'coord', 'area'], 'required'],
            [['name_id', 'dia'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['coord', 'area'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name_id' => Yii::t('app', 'ID'),
            'latitude' => Yii::t('app', 'Latitude'),
            'longitude' => Yii::t('app', 'Longitude'),
            'dia' => Yii::t('app', 'Dia'),
            'coord' => Yii::t('app', 'Coord'),
            'area' => Yii::t('app', 'Area'),
        ];
    }
}
