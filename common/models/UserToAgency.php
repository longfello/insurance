<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_to_agency".
 *
 * @property int $user_id  Идентификатор пользователя модели User
 * @property int $agency_id Идентификатор агенства модели Agency
 *
 */
class UserToAgency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_to_agency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'agency_id'], 'required'],
            [['user_id', 'agency_id'], 'integer'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'agency_id' => 'Agency ID',
        ];
    }


}
