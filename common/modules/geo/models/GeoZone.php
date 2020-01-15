<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\models;

use common\components\MLBehavior;
use common\components\MLModel;
use Yii;

/**
 * Области
 * This is the model class for table "geozone".
 *
 * @property integer $id
 * @property string $code
 * @property string $name
 * @property string $slug
 */
class GeoZone extends MLModel
{
    /**
     * @inheritdoc
     */
    public $MLattributes = ['name'];
    /**
     * @inheritdoc
     */
    public $MLfk = 'zone_id';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo_zone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string'],
            [['code'], 'string', 'max' => 15],
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
            'code' => Yii::t('app', 'Code'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Name'),
        ];
    }
}
