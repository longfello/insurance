<?php

namespace common\modules\ApiRgs\models;

use Yii;
use yii\db\Query;
use common\models\GeoCountry;
use common\modules\ApiRgs\models\TerritoryType;

/**
 * Страна
 * This is the model class for table "api_rgs_country".
 *
 * @property int $id
 * @property srting $ext_id
 * @property string $title
 * @property int $territory_type_id
 * @property int $min_sum
 * @property int $enabled
 */
class Country extends Classifier {

    /**
     * @inheritdoc
     */
    public static $table_postfix = 'country';

    /**
     * @inheritdoc
     */
    public $title_length = 125;

    /**
     * @inheritdoc
     */
    public function rules() {
        $rules = parent::rules();

        $rules[] = [['territory_type_id', 'min_sum', 'enabled'], 'integer'];
        $rules[] = [['territory_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => TerritoryType::className(), 'targetAttribute' => ['territory_type_id' => 'id']];
        $rules[] = [['min_sum'], 'safe'];

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
            'territory_type_id' => Yii::t('backend', 'Территория'),
            'min_sum' => Yii::t('backend', 'Минимальная страховая сумма'),
            'enabled' => Yii::t('backend', 'Разрешена')
        ];
    }

    /**
     * Территория
     * 
     * @return TerritoryType
     */
    public function getTerritory() {
        return $this->hasOne(TerritoryType::className(), ['id' => 'territory_type_id']);
    }

    /**
     * Территория весь мир
     * 
     * @param boolean $min_sum
     * 
     * @return Country
     */
    public static function getAllWorld($min_sum = false) {
        $select = !$min_sum ? [
            'Countries' => 'arc.ext_id',
            'CountriesText' => 'arc.title',
            'TerritoryType' => 'artt.ext_id'
        ] : ['arc.min_sum'];

        $query = (new Query())->select($select)->from([
            'arc' => self::tableName()
        ])->leftJoin([
            'artt' => TerritoryType::tableName()
        ], 'arc.territory_type_id = artt.id')->leftJoin([
            'arc2d' => Country2dict::tableName()
        ], 'arc.id = arc2d.country_id')->leftJoin([
            'gc' => GeoCountry::tableName()
        ], 'arc2d.internal_id = gc.id')->where([
            'arc.enabled' => 1,
            'gc.type' => GeoCountry::TYPE_TERRITORY,
            'gc.slug' => 'all-of-the-world'
        ]);

        return !$min_sum ? $query->one() : $query->max('arc.min_sum');
    }

}
