<?php

namespace common\modules\ApiSberbank\models;

use Yii;

/**
 * Территории
 * This is the model class for table "api_sberbank_territory".
 *
 * @property int $id
 * @property string $insTerritory Территория
 * @property string $name Название территории
 * @property int $enabled Разрешена
 */
class Territory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_sberbank_territory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['insTerritory', 'name'], 'required'],
            [['enabled'], 'integer'],
            [['insTerritory', 'name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'insTerritory' => 'Территория',
            'name' => 'Название территории',
            'enabled' => 'Разрешена',
        ];
    }
}
