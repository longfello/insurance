<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use common\models\GeoCountry;
use Yii;

/**
 * Соответствие регионов странам
 * This is the model class for table "api_erv_region2country".
 *
 * @property integer $country_id
 * @property integer $region_id
 *
 * @property GeoCountry $country
 * @property Regions $region
 */
class Region2Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_region2country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['country_id', 'region_id'], 'required'],
            [['country_id', 'region_id'], 'integer'],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'country_id' => Yii::t('backend', 'Country ID'),
            'region_id' => Yii::t('backend', 'Region ID'),
        ];
    }

    /**
     * Страна
     * @return \yii\db\ActiveQuery
     */
    public function getCountry()
    {
        return $this->hasOne(GeoCountry::className(), ['id' => 'country_id']);
    }

    /**
     * Регион
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
}
