<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\models;

use common\models\query\TimelineEventQuery;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Событие таймлайн
 * This is the model class for table "timeline_event".
 *
 * @property integer $id
 * @property string $application
 * @property string $category
 * @property string $event
 * @property string $data
 * @property string $created_at
 */
class TimelineEvent extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%timeline_event}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => null
            ]
        ];
    }

    /**
     * @inheritdoc
     * @return TimelineEventQuery
     */
    public static function find()
    {
        return new TimelineEventQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application', 'category', 'event'], 'required'],
            [['data'], 'safe'],
            [['application', 'category', 'event'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->data = @json_decode($this->data, true);
        parent::afterFind();
    }

    /**
     * Полное название события (включая категорию)
     * @return string
     */
    public function getFullEventName()
    {
        return sprintf('%s.%s', $this->category, $this->event);
    }
}
