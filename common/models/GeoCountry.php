<?php

namespace common\models;

use Yii;

/**
 * Страны / территории
 * This is the model class for table "geo_country".
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
 * @property string $languages
 * @property string $neighbours
 * @property string $slug
 * @property string $type
 * @property integer $shengen
 * @property integer $is_popular
 * @property GeoCountry[] $subCountries
 */
class GeoCountry extends \yii\db\ActiveRecord
{
    /**
     * Страна
     */
    const TYPE_COUNTRY   = 'country';
    /**
     * Территория
     */
    const TYPE_TERRITORY = 'territory';

    /**
     * Допустимые типы записей
     * @var array
     */
    public $types = [
		self::TYPE_COUNTRY => 'Страна',
		self::TYPE_TERRITORY => 'Территория'
	];

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
			[['iso_numeric', 'population', 'shengen', 'is_popular'], 'integer'],
			[['areainsqkm'], 'number'],
			[['iso_alpha2', 'continent'], 'string', 'max' => 2],
			[['iso_alpha3', 'fips_code', 'tld', 'currency'], 'string', 'max' => 3],
			[['name', 'capital', 'languages'], 'string', 'max' => 200],
			[['currencyName'], 'string', 'max' => 20],
			[['Phone'], 'string', 'max' => 10],
			[['postalCodeFormat', 'neighbours'], 'string', 'max' => 100],
			[['postalCodeRegex'], 'string', 'max' => 255],
			[['slug'], 'string', 'max' => 100],
			[['type'], 'string', 'max' => 10],
			[['type'], 'in' , 'range' => [self::TYPE_COUNTRY, self::TYPE_TERRITORY]]
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => Yii::t('app', 'ID'),
			'iso_alpha2' => Yii::t('app', 'ISO Alpha2'),
			'iso_alpha3' => Yii::t('app', 'ISO Alpha3'),
			'iso_numeric' => Yii::t('app', 'ISO Numeric'),
			'fips_code' => Yii::t('app', 'FIPS Code'),
			'name' => Yii::t('app', 'Название'),
			'capital' => Yii::t('app', 'Столица'),
			'areainsqkm' => Yii::t('app', 'Площадь, км.кв.'),
			'population' => Yii::t('app', 'Население'),
			'continent' => Yii::t('app', 'Континент'),
			'tld' => Yii::t('app', 'Домен'),
			'currency' => Yii::t('app', 'Валюта'),
			'currencyName' => Yii::t('app', 'Название валюты'),
			'Phone' => Yii::t('app', 'Формат телефона'),
			'postalCodeFormat' => Yii::t('app', 'Формат почтового индекса'),
			'postalCodeRegex' => Yii::t('app', 'Регулярное выражение проверки почтового индекса'),
			'languages' => Yii::t('app', 'Языки'),
			'neighbours' => Yii::t('app', 'Соседи'),
			'slug' => Yii::t('app', 'Псевдоним'),
			'type' => Yii::t('app', 'Тип записи'),
			'shengen' => Yii::t('app', 'Шенген'),
			'is_popular' => Yii::t('app', 'Популярная')
		];
	}

    /**
     * Возвращает перечень стран, включеных в территории
     * @return \yii\db\ActiveQuery
     */
    public function getSubCountries()
    {
        return $this->hasMany(GeoCountry::className(), ['id' => 'geo_territory_id'])->viaTable('geo_territory2country', ['geo_country_id' => 'id']);
    }
}
