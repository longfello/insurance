<?php

namespace common\models;

use Yii;

/**
 * Телефон API
 * This is the model class for table "api_phone".
 *
 * @property integer $id
 * @property integer $api_id
 * @property string $name
 * @property string $phone
 *
 * @property Api $api
 */
class ApiPhone extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_phone';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['api_id', 'name', 'phone'], 'required'],
            [['api_id', 'id'], 'integer'],
            [['name', 'phone'], 'string', 'max' => 255],
            [['api_id'], 'exist', 'skipOnError' => true, 'targetClass' => Api::className(), 'targetAttribute' => ['api_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'api_id' => 'Api',
            'name' => 'Название',
            'phone' => 'Номер телефона',
        ];
    }

    /**
     * API
     * @return \yii\db\ActiveQuery
     */
    public function getApi()
    {
        return $this->hasOne(Api::className(), ['id' => 'api_id']);
    }
}
