<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "chat".
 *
 * @property int $id
 * @property int $id_agency
 * @property int $from_user_id
 * @property string $message
 * @property string $file
 * @property string $create_at
 */
class Chat extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_agency', 'from_user_id'], 'required'],
            [['id_agency', 'from_user_id'], 'integer'],
            [['message'], 'string'],
            [['create_at'], 'safe'],
            [['file'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_agency' => 'Id Agency',
            'from_user_id' => 'From User ID',
            'message' => 'Message',
            'file' => 'File',
            'create_at' => 'Create At',
        ];
    }
}
