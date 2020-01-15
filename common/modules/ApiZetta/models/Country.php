<?php

namespace common\modules\ApiZetta\models;

use Yii;
use common\models\GeoCountry;

/**
 * Страна
 * This is the model class for table "api_zetta_country".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 * @property string $type
 * @property integer $enabled
 */
class Country extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'country';

    /**
     * @inheritdoc
     */
    public $title_length = 100;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['type'], 'string', 'max' => 10];
        $rules[] = [['type'], 'in' , 'range' => [GeoCountry::TYPE_COUNTRY, GeoCountry::TYPE_TERRITORY]];
        $rules[] = [['enabled'], 'integer'];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('backend', 'ИД страны'),
            'ext_id' => Yii::t('backend', 'ИД страны во внешней системе'),
            'title' => Yii::t('backend', 'Страна'),
            'type' => Yii::t('backend', 'Страна / Территория'),
            'enabled' => Yii::t('backend', 'Разрешена')
        ];
    }

    /**
     * Территория шенген
     * 
     * @return Country
     */
    public static function getShengen() {
        return self::find()
                ->alias('azc')
                ->innerJoin(Country2dict::tableName() . ' azc2d', 'azc.id = azc2d.country_id')
                ->innerJoin(GeoCountry::tableName() . ' gc', 'azc2d.internal_id = gc.id')
                ->where(['gc.type' => GeoCountry::TYPE_TERRITORY, 'gc.slug' => 'shengen'])->one();
    }

}
