<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;

/**
 * Программы страхования АПИ
 * This is the model class for table "insurance_programm".
 *
 * @property integer $insuranceProgrammID
 * @property string $insuranceProgrammName
 * @property string $insuranceProgrammPrintName
 * @property string $insuranceProgrammUID
 * @property string $rule_base_url
 * @property string $rule_path
 * @property string $police_base_url
 * @property string $police_path
 * @property integer $pregnant_week
 */
class InsuranceProgramm extends \yii\db\ActiveRecord
{
	/**
	 * @var array
	 */
	public $rule;
	/**
	 * @var array
	 */
	public $police;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_insurance_programm';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['insuranceProgrammID', 'insuranceProgrammName', 'insuranceProgrammPrintName', 'insuranceProgrammUID'], 'required'],
            [['insuranceProgrammID', 'pregnant_week'], 'integer'],
            [['insuranceProgrammName', 'insuranceProgrammPrintName'], 'string', 'max' => 255],
            [['insuranceProgrammUID'], 'string', 'max' => 36],
	        [['rule', 'police'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'insuranceProgrammID' => Yii::t('backend', 'ID'),
            'insuranceProgrammName' => Yii::t('backend', 'Название'),
            'insuranceProgrammPrintName' => Yii::t('backend', 'Печатное название'),
            'insuranceProgrammUID' => Yii::t('backend', 'GUID'),
            'rule' => Yii::t('backend', 'Правила страхования'),
            'police' => Yii::t('backend', 'Образец полиса'),
			'pregnant_week' => Yii::t('backend', 'Максимальная неделя беременности при осложнениях')
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
     * Стоимости программы
	 * @return \yii\db\ActiveQuery
	 */
	public function getPrices()
	{
		return $this->hasMany(Price::className(), ['program_id' => 'insuranceProgrammID']);
	}

    /**
     * Возвращает настроки риска беременности
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
