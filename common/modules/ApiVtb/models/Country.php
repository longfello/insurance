<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use common\models\GeoCountry;
use Yii;

/**
 * Страны
 * This is the model class for table "api_vtb_country".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $minInsuranceSum
 * @property integer $shengen
 * @property integer $war
 * @property integer $enabled
 * @property string $currencies
 *
 * @property GeoCountry[] $countries
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['minInsuranceSum', 'shengen', 'war', 'enabled'], 'integer'],
            [['name', 'code'], 'string', 'max' => 255],
            [['currencies'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'minInsuranceSum' => 'Минимальная страховая сумма',
            'shengen' => 'Шанген',
            'war' => 'Военные действия',
            'currencies' => 'Валюты',
            'enabled' => 'Разрешена',
            'code' => 'Код',
        ];
    }

	/**
     * Соответствие стране во внутреннем справочнике
	 * @return \yii\db\ActiveQuery
	 */
	public function getCountries()
	{
		return $this->hasMany(GeoCountry::className(), ['id' => 'internal_id'])->viaTable('api_vtb_country2dict', ['api_id' => 'id']);
	}

}
