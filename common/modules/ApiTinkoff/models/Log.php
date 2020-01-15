<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;

/**
 * Журнал событий
 * This is the model class for table "api_tinkoff_log".
 *
 * @property integer $id
 * @property integer $level
 * @property string $category
 * @property double $log_time
 * @property string $prefix
 * @property string $message
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['level'], 'integer'],
            [['log_time'], 'number'],
            [['prefix', 'message'], 'string'],
            [['category'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'level' => 'Уровень',
            'category' => 'integrationID',
            'log_time' => 'Время события',
            'prefix' => 'Запрос',
            'message' => 'Сообщение',
        ];
    }
}
