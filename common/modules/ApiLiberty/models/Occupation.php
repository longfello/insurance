<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Опции
 * This is the model class for table "api_liberty_occupation".
 *
 * @property integer $id
 * @property string $occupationName
 * @property integer $is_sport
 */
class Occupation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_occupation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'occupationName'], 'required'],
            [['id', 'is_sport'], 'integer'],
            [['occupationName'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Ид опции',
            'occupationName' => 'Название опции',
            'is_sport' => 'Занятия спортом (внутр.)',
        ];
    }
}
