<?php

namespace common\modules\ApiZetta\models;

use Yii;

/**
 * Соответствие стран территориям
 * This is the model class for table "api_zetta_country_territory".
 *
 * @property int $country_id
 * @property int $territory_id
 */
class CountryTerritory extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_zetta_country_territory';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['country_id', 'territory_id'], 'required'],
            [['country_id', 'territory_id'], 'integer'],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['territory_id'], 'exist', 'skipOnError' => true, 'targetClass' => Country::className(), 'targetAttribute' => ['territory_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'country_id' => Yii::t('backend', 'Внутренний ID страны'),
            'territory_id' => Yii::t('backend', 'Внутренний ID территории')
        ];
    }

    /**
     * Страна
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getCountryModel() {
        return $this->hasOne(Country::className(), ['id' => 'country_id']);
    }

    /**
     * Территория
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getTerritoryModel() {
        return $this->hasOne(Country::className(), ['id' => 'territory_id']);
    }

}
