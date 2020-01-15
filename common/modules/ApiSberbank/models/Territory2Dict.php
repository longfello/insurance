<?php
/**
 * Copyright (c) kvk-group 2018.
 */

namespace common\modules\ApiSberbank\models;

use common\models\GeoCountry;
use Yii;

/**
 * Соответствие территорий внутреннему справочнику
 * This is the model class for table "api_sberbank_territory2dict".
 *
 * @property int $territory_id
 * @property int $internal_id
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
        return 'api_sberbank_territory2dict';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['territory_id', 'internal_id'], 'required'],
            [['territory_id', 'internal_id'], 'integer'],
            [['territory_id', 'internal_id'], 'unique', 'targetAttribute' => ['territory_id', 'internal_id']],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['internal_id' => 'id']],
            [['territory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Territory::className(), 'targetAttribute' => ['territory_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'territory_id' => 'Ид территории',
            'internal_id' => 'Id из внутреннего справочника',
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
    public function getApiModel()
    {
        return $this->hasOne(Territory::className(), ['id' => 'territory_id']);
    }
}
