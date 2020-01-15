<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use common\models\GeoCountry;
use Yii;

/**
 * Соответствие стране во внутреннем справочнике
 * This is the model class for table "api_alpha_country2dict".
 *
 * @property integer $internal_id
 * @property integer $api_id
 *
 * @property GeoCountry $geoNameModel
 * @property Country $apiModel
 */
class Country2dict extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_country2dict';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['internal_id', 'api_id'], 'required'],
            [['internal_id', 'api_id'], 'integer'],
            [['internal_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['internal_id' => 'id']],
            [['api_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['api_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'internal_id' => 'Internal ID',
            'api_id' => 'Api ID',
        ];
    }

    /**
     * Страна во внутреннем справочнике
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
        return $this->hasOne(Country::className(), ['id' => 'api_id']);
    }
}
