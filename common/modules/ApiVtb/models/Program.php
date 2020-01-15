<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;

/**
 * Программа
 * This is the model class for table "api_vtb_program".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property string $rule_base_url
 * @property string $rule_path
 * @property string $police_base_url
 * @property string $police_path
 * @property integer $pregnant_week
 * @property integer $baggage_sum
 *
 * @property Price[] $Prices
 */
class Program extends \yii\db\ActiveRecord
{
	/**
	 * @var array файл правил
	 */
	public $rule;
	/**
	 * @var array файл примера полиса
	 */
	public $police;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_program';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'code'], 'required'],
            [['name', 'code'], 'string', 'max' => 255],
	        [['rule', 'police'], 'safe'],
            [['pregnant_week', 'baggage_sum'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'code' => 'Код',
            'rule' => Yii::t('backend', 'Правила страхования'),
            'police' => Yii::t('backend', 'Образец полиса'),
            'pregnant_week' => Yii::t('backend', 'Максимальная неделя беременности при осложнениях'),
            'baggage_sum' => Yii::t('backend', 'Страховая сумма для риска Багаж')
        ];
    }

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			[
				'class' => UploadBehavior::className(),
				'attribute' => 'rule',
				'pathAttribute' => 'rule_path',
				'baseUrlAttribute' => 'rule_base_url'
			],
			[
				'class' => UploadBehavior::className(),
				'attribute' => 'police',
				'pathAttribute' => 'police_path',
				'baseUrlAttribute' => 'police_base_url'
			]
		];
	}

    /**
     * Цены
     * @return \yii\db\ActiveQuery
     */
    public function getPrices()
    {
        return $this->hasMany(Price::className(), ['program_id' => 'id']);
    }

    /**
     * Настройки риска беременности
     * @return array
     */
    public function getPregnantVariants() {
        $res = ['id'=>0,'name'=>0];
        $pr = \common\models\Risk::findOne(['id'=>6]);
        $params = json_decode($pr->params, true);
        if (isset($params['Количество недель'])) {
            foreach ($params['Количество недель'] as $cw) {
                $res[] = ['id'=>$cw,'name'=>$cw];
            }
        }
        return $res;
    }
}
