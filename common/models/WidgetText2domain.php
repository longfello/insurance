<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\models;

use common\components\MLModel;
use Yii;

/**
 * Текстовый виджет для домена
 * This is the model class for table "widget_text2domain".
 *
 * @property integer $id
 * @property integer $domain_id
 * @property integer $widget_id
 * @property string $body
 *
 * @property Domain $domain
 * @property WidgetText $widget
 */
class WidgetText2domain extends MLModel
{
    /**
     * @inheritdoc
     */
    public $MLattributes = ['body'];
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'widget_text2domain';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['domain_id', 'widget_id', 'body'], 'required'],
            [['domain_id', 'widget_id'], 'integer'],
            [['body'], 'string'],
            [['domain_id'], 'exist', 'skipOnError' => true, 'targetClass' => Domain::className(), 'targetAttribute' => ['domain_id' => 'id']],
            [['widget_id'], 'exist', 'skipOnError' => true, 'targetClass' => WidgetText::className(), 'targetAttribute' => ['widget_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'domain_id' => 'Домен',
            'widget_id' => 'Виджет',
            'body' => 'Текст',
        ];
    }

    /**
     * Домен
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    /**
     * Виджет
     * @return \yii\db\ActiveQuery
     */
    public function getWidget()
    {
        return $this->hasOne(WidgetText::className(), ['id' => 'widget_id']);
    }
}
