<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use common\models\GeoCountry;
use Yii;

/**
 * Соотношение регионов странам внутреннего справочника
 * This is the model class for table "api_tinkoff_area2dict".
 *
 * @property integer $internal_id
 * @property integer $area_id
 *
 * @property GeoCountry $geoNameModel
 * @property Area $apiModel
 */
class Area2Dict extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_area2dict';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['internal_id', 'area_id'], 'required'],
            [['internal_id', 'area_id'], 'integer'],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['internal_id' => 'id']],
            [['area_id'], 'exist', 'skipOnError' => true, 'targetClass' => Area::className(), 'targetAttribute' => ['area_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'internal_id' => 'Id из внутреннего справочника',
            'area_id' => 'Id региона',
        ];
    }

    /**
     * страна
     * @return \yii\db\ActiveQuery
     */
    public function getGeoNameModel()
    {
        return $this->hasOne(GeoCountry::className(), ['id' => 'internal_id']);
    }

    /**
     * регион АПИ
     * @return \yii\db\ActiveQuery
     */
    public function getApiModel(){
        return $this->hasOne(Area::className(), ['id' => 'area_id']);
    }
}
