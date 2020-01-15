<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Территории
 * This is the model class for table "api_liberty_territory".
 *
 * @property integer $id_area
 * @property string $name
 * @property integer $territoryGroupId
 * @property integer $enabled
 */
class Territory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_territory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_area', 'name', 'territoryGroupId'], 'required'],
            [['id_area', 'territoryGroupId', 'enabled'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_area' => 'Ид территории',
            'name' => 'Название территории (api)',
            'territoryGroupId' => 'Тарифная группа',
            'enabled' => 'Разрешена',
        ];
    }
}
