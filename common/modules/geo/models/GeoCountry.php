<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\models;

use common\components\MLBehavior;
use common\components\MLModel;
use Yii;

/**
 * Страна
 * This is the model class for table "geocountry".
 *
 * @property integer $id
 * @property string $iso_alpha2
 * @property string $iso_alpha3
 * @property integer $iso_numeric
 * @property string $fips_code
 * @property string $name
 * @property string $capital
 * @property double $areainsqkm
 * @property integer $population
 * @property string $continent
 * @property string $tld
 * @property string $currency
 * @property string $currencyName
 * @property string $Phone
 * @property string $postalCodeFormat
 * @property string $postalCodeRegex
 * @property integer $geonameId
 * @property string $languages
 * @property string $neighbours
 * @property string $slug
 *
 * @property GeoName[] $cities
 */
class GeoCountry extends MLModel
{
    /**
     * @inheritdoc
     */
    public $MLattributes = ['name'];
    /**
     * @inheritdoc
     */
    public $MLfk = 'country_id';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo_country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iso_numeric', 'population', 'geonameId'], 'integer'],
            [['areainsqkm'], 'number'],
            [['iso_alpha2', 'continent'], 'string', 'max' => 2],
            [['iso_alpha3', 'fips_code', 'tld', 'currency'], 'string', 'max' => 3],
            [['name', 'capital', 'languages'], 'string', 'max' => 200],
            [['currencyName'], 'string', 'max' => 20],
            [['Phone'], 'string', 'max' => 10],
            [['postalCodeFormat', 'neighbours'], 'string', 'max' => 100],
            [['postalCodeRegex'], 'string', 'max' => 255],
            [['slug'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'iso_alpha2' => Yii::t('app', 'Iso Alpha2'),
            'iso_alpha3' => Yii::t('app', 'Iso Alpha3'),
            'iso_numeric' => Yii::t('app', 'Iso Numeric'),
            'fips_code' => Yii::t('app', 'Fips Code'),
            'name' => Yii::t('app', 'Name'),
            'capital' => Yii::t('app', 'Capital'),
            'areainsqkm' => Yii::t('app', 'Areainsqkm'),
            'population' => Yii::t('app', 'Population'),
            'continent' => Yii::t('app', 'Continent'),
            'tld' => Yii::t('app', 'Tld'),
            'currency' => Yii::t('app', 'Currency'),
            'currencyName' => Yii::t('app', 'Currency Name'),
            'Phone' => Yii::t('app', 'Phone'),
            'postalCodeFormat' => Yii::t('app', 'Postal Code Format'),
            'postalCodeRegex' => Yii::t('app', 'Postal Code Regex'),
            'geonameId' => Yii::t('app', 'Geoname ID'),
            'languages' => Yii::t('app', 'Languages'),
            'neighbours' => Yii::t('app', 'Neighbours'),
            'slug' => Yii::t('app', 'Slug'),
        ];
    }

  /**
   * Города
   * @return \yii\db\ActiveQuery
   */
  public function getCities()
  {
    return $this->hasMany(GeoName::className(), ['country_id' => 'id'])->orderBy(['name' => SORT_ASC]);
  }

}
