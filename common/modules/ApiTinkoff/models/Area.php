<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;

/**
 * Регионы
 * This is the model class for table "api_tinkoff_area".
 *
 * @property integer $id
 * @property string $Value
 * @property string $Display
 * @property integer $enabled
 */
class Area extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_area';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Value', 'Display'], 'required'],
            [['enabled'], 'integer'],
            [['Value', 'Display'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Value' => 'Код региона',
            'Display' => 'Название',
            'enabled' => 'Разрешен',
        ];
    }
}
