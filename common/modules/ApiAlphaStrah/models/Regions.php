<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use common\models\GeoCountry;
use Yii;

/**
 * Регионы - АПИ
 * This is the model class for table "api_alpha_regions".
 *
 * @property integer $id
 * @property string $short_name
 * @property string $name
 * @property string $code
 *
 * @property Price[] $apiAlphaPrices
 * @property Country[] $countries
 */
class Regions extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_regions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['short_name', 'name', 'code'], 'required'],
            [['short_name'], 'string', 'max' => 10],
            [['name'], 'string', 'max' => 255],
            [['code'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'short_name' => 'Краткое название',
            'name' => 'Название',
            'code' => 'Код',
        ];
    }

    /**
     * Стоимости
     * @return \yii\db\ActiveQuery
     */
    public function getApiAlphaPrices()
    {
        return $this->hasMany(Price::className(), ['region_id' => 'id']);
    }

    /**
     * Страны
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(Country::className(), ['id' => 'region_id']);
    }
}
