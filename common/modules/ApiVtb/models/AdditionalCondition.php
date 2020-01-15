<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use Yii;

/**
 * Дополнительные условия
 * This is the model class for table "api_vtb_additional_conditions".
 *
 * @property integer $id
 * @property string $name
 * @property string $params_description
 * @property string $params
 * @property string $class
 */
class AdditionalCondition extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_additional_conditions';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'params_description', 'class'], 'string', 'max' => 255],
            [['params'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'params_description' => 'Описание параметров',
            'params' => 'Параметры',
            'class' => 'Класс обработчика',
        ];
    }
}
