<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "api_log".
 *
 * @property string $id
 * @property string $fired_at
 * @property integer $user_id
 * @property string $method
 * @property string $uri
 * @property string $data
 * @property string $response
 * @property string $ip
 */
class ApiLog extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_log';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fired_at'], 'safe'],
            [['user_id'], 'integer'],
            [['method', 'uri'], 'required'],
            [['data', 'response'], 'string'],
            [['method'], 'string', 'max' => 10],
            [['uri'], 'string', 'max' => 128],
            [['ip'], 'string', 'max' => 15],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'fired_at' => 'Fired At',
            'user_id' => 'User ID',
            'method' => 'Method',
            'uri' => 'Uri',
            'data' => 'Data',
            'response' => 'Response',
            'ip' => 'Ip',
        ];
    }

    public function init(){
        if ($this->isNewRecord){
            $data = Yii::$app->request->post();
            if($data)
                $this->data = json_encode($data);
            $this->method = Yii::$app->request->method;
            $this->uri    = Yii::$app->request->url;
            $this->ip     = Yii::$app->request->getUserIP();
        }
    }
}
