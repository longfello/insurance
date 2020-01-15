<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use common\models\GeoCountry;
use Yii;

/**
 * Соответствие территорий внутреннему справочнику
 * This is the model class for table "api_liberty_territory2dict".
 *
 * @property integer $internal_id
 * @property integer $id_area
 *
 * @property GeoCountry $geoNameModel
 * @property Territory $apiModel
 */
class Territory2Dict extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_territory2dict';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['internal_id', 'id_area'], 'integer'],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['internal_id' => 'id']],
            [['id_area'], 'exist', 'skipOnError' => true, 'targetClass' => Territory::className(), 'targetAttribute' => ['id_area' => 'id_area']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'internal_id' => 'Id из внутреннего справочника',
            'id_area' => 'Ид территории',
        ];
    }

    /**
     * Страна/территория внутреннего справочника
     * @return \yii\db\ActiveQuery
     */
    public function getGeoNameModel()
    {
        return $this->hasOne(GeoCountry::className(), ['id' => 'internal_id']);
    }

    /**
     * Страна/территория АПИ
     * @return \yii\db\ActiveQuery
     */
    public function getApiModel(){
        return $this->hasOne(Territory::className(), ['id_area' => 'id_area']);
    }
}
