<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\models;

use common\behaviors\CacheInvalidateBehavior;
use common\components\MLModel;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Текстовый виджет
 * This is the model class for table "text_block".
 *
 * @property integer $id
 * @property string $key
 * @property string $title
 * @property string $body
 * @property integer $status
 */
class WidgetText extends MLModel
{
    /**
     * @inheritdoc
     */
    public $MLattributes = ['body'];

    /**
     * Статус - активно
     */
    const STATUS_ACTIVE = 1;
    /**
     * Статус = черновик
     */
    const STATUS_DRAFT = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%widget_text}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors[] = TimestampBehavior::className();
        $behaviors['cacheInvalidate'] = [
            'class' => CacheInvalidateBehavior::className(),
            'cacheComponent' => 'frontendCache',
            'keys' => [
                function ($model) {
                    return [
                        self::className(),
                        $model->key
                    ];
                }
            ]
        ];
        return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['key', 'title', 'body'], 'required'],
            [['key'], 'unique'],
            [['body'], 'string'],
            [['status'], 'integer'],
            [['title', 'key'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'key' => Yii::t('common', 'Key'),
            'title' => Yii::t('common', 'Title'),
            'body' => Yii::t('common', 'Body'),
            'status' => Yii::t('common', 'Active'),
        ];
    }

    /**
     * Модель для домена
     * @param $domain_id
     *
     * @return WidgetText2domain|static
     */
    public function getModel4Domain($domain_id){
        $model = WidgetText2domain::findOne([
            'domain_id' => $domain_id,
            'widget_id' => $this->id
        ]);
        if (!$model){
            $model = new WidgetText2domain([
                'domain_id' => $domain_id,
                'widget_id' => $this->id,
                'body'      => $this->body
            ]);


            foreach (Yii::$app->params['availableLocales'] as $iso => $name){
                $iso = substr($iso, 0, 2);
                $suffix   = ($iso == substr(Yii::$app->sourceLanguage,0,2))?"":"_".$iso;
                $field    = 'body'.$suffix;

                $model->$field = $this->$field;
            }

            $model->save();
        }
        return $model;
    }
}
