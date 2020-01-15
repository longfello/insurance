<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\models;

use common\components\Sphinx;
use Yii;

/**
 * Альтернативные названия локаций
 * This is the model class for table "geo_name_altername".
 *
 * @property integer $name_id
 * @property string $altername
 *
 * @property GeoName $name
 */
class GeoNameAlter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo_name_altername';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name_id'], 'required'],
            [['name_id'], 'integer'],
            [['altername'], 'string', 'max' => 4000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name_id' => Yii::t('app', 'Name ID'),
            'altername' => Yii::t('app', 'Altername'),
        ];
    }

    /**
     * Локация
     * @return \yii\db\ActiveQuery
     */
    public function getName()
    {
        return $this->hasOne(GeoName::className(), ['id' => 'name_id']);
    }

}
