<?php

namespace common\models;

use common\modules\geo\models\GeoName;
use Yii;

/**
 * Домены проекта
 * This is the model class for table "domain".
 *
 * @property integer $id
 * @property string $name
 * @property integer $city_id
 * @property integer $country_id
 * @property integer $default
 * @property string $description
 * @property integer $default_language
 * @property integer $enabled
 *
 * @property GeoName $city
 * @property GeoCountry $country
 * @property Languages $defaultLanguage
 * @property Languages[] $languages
 */
class Domain extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'domain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'country_id', 'default_language'], 'required'],
            [['city_id', 'country_id', 'default', 'default_language', 'enabled'], 'integer'],
            [['name', 'description'], 'string', 'max' => 250],
            [['name'], 'unique'],
            [['city_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoName::className(), 'targetAttribute' => ['city_id' => 'id']],
            [['country_id'], 'exist', 'skipOnError' => true, 'targetClass' => GeoCountry::className(), 'targetAttribute' => ['country_id' => 'id']],
            [['default_language'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['default_language' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Домен',
            'city_id' => 'Город',
            'country_id' => 'Страна',
            'default' => 'Основной домен',
            'description' => 'Описание',
            'default_language' => 'Язык по-умолчанию',
            'enabled' => 'Разрешен',
        ];
    }

    /**
     * Город
     * @return \yii\db\ActiveQuery
     */
    public function getCity()
    {
        return $this->hasOne(GeoName::className(), ['id' => 'city_id']);
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
     * Язык по-умолчанию
     * @return \yii\db\ActiveQuery
     */
    public function getDefaultLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'default_language']);
    }

    /**
     * Допустимые языки
     * @return \yii\db\ActiveQuery
     */
    public function getLanguages()
    {
        return $this->hasMany(Languages::className(), ['id' => 'language_id'])->viaTable('domain2language', ['domain_id' => 'id']);
    }
}
