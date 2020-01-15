<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\models;

use Yii;

/**
 * geoip
 * This is the model class for table "geoip".
 *
 * @property integer $geonameid
 * @property integer $begin_ip
 * @property integer $end_ip
 */
class GeoIp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo_ip';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['geonameid', 'begin_ip', 'end_ip'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'geonameid' => Yii::t('app', 'Geonameid'),
            'begin_ip' => Yii::t('app', 'Begin Ip'),
            'end_ip' => Yii::t('app', 'End Ip'),
        ];
    }
}
