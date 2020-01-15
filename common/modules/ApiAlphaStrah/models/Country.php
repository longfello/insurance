<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;

/**
 * Страны - справочник АПИ
 * This is the model class for table "api_alpha_country".
 *
 * @property integer $countryID
 * @property string $countryUID
 * @property string $countryName
 * @property string $name
 * @property string $terName
 * @property double $countryKV
 * @property integer $assistanteID
 * @property string $assistanteUID
 * @property string $assistanceCode
 * @property string $assistanceName
 * @property string $assistancePhones
 * @property bool $enabled
 * @property bool $visa
 * @property integer $region_id
 *
 * @property Regions $region
 */
class Country extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['countryID', 'countryUID', 'countryName'], 'required'],
            [['countryID', 'assistanteID', 'enabled', 'visa'], 'integer'],
            [['countryKV'], 'number'],
            [['countryUID', 'assistanteUID'], 'string', 'max' => 36],
            [['countryName', 'name', 'terName', 'assistanceCode', 'assistanceName'], 'string', 'max' => 255],
            [['assistancePhones'], 'string', 'max' => 1024],
            //[['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            ['region_id', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'countryID' => 'ID',
            'countryUID' => 'GUID',
            'countryName' => 'Название (api)',
            'name' => 'Название (сайт)',
            'terName' => 'Наименование территории',
            'countryKV' => 'Тарифный коэффициент надбавки для страны',
            'assistanteID' => 'ID ассистента',
            'assistanteUID' => 'GUID ассистента',
            'assistanceCode' => 'Код ассистента',
            'assistanceName' => 'Наименование ассистента',
            'assistancePhones' => 'Телефон ассистента',
            'enabled' => 'Разрешена',
            'visa' => 'Визовая страна',
            'region_id' => 'Id региона'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }
}
