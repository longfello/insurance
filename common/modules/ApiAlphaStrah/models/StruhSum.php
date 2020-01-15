<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;

/**
 * Страховые суммы
 * This is the model class for table "struh_sum".
 *
 * @property integer $riskID
 * @property string $risk
 * @property string $riskUID
 * @property integer $strahSummFrom
 * @property integer $strahSummTo
 * @property string $valutaCode
 * @property string $variant
 * @property string $variantUid
 * @property string $program_id
 * @property string $hash
 * @property float $premia
 */
class StruhSum extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_struh_sum';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hash', 'riskID', 'risk', 'riskUID', 'strahSummFrom', 'strahSummTo', 'valutaCode'], 'required'],
            [['riskID', 'strahSummFrom', 'strahSummTo', 'program_id'], 'integer'],
            [['premia'], 'number'],
            [['risk'], 'string', 'max' => 255],
            [['hash'], 'string', 'max' => 72],
            [['riskUID', 'variantUid'], 'string', 'max' => 36],
            [['valutaCode'], 'string', 'max' => 3],
            [['variant'], 'string', 'max' => 50],
	        [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => InsuranceProgramm::className(), 'targetAttribute' => ['program_id' => 'insuranceProgrammID']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'riskID' => Yii::t('backend', 'ID риска'),
            'risk' => Yii::t('backend', 'Название риска'),
            'riskUID' => Yii::t('backend', 'GUID риска'),
            'strahSummFrom' => Yii::t('backend', 'Сумма от'),
            'strahSummTo' => Yii::t('backend', 'Сумма до'),
            'valutaCode' => Yii::t('backend', 'ISO-код валюты'),
            'variant' => Yii::t('backend', 'Вариант'),
            'variantUid' => Yii::t('backend', 'GUID варианта'),
	        'program_id' => Yii::t('backend', 'Программа страхования'),
	        'premia' => Yii::t('backend', 'Страховая премия'),
        ];
    }

    /**
     * Хеш страховой суммы - уникальный идентификатор
     * @param $data
     *
     * @return string
     */
    public static function getHash($data){
	    ksort($data);
	    $serial = serialize($data);
	    return sha1($serial).md5($serial);
    }

    /**
     * Геттер печатной версии страховой суммы
     * @return int|string
     */
    public function getAmountPrint(){
    	if ($this->strahSummFrom == $this->strahSummTo){
		    return $this->strahSummFrom;
	    } else {
		    return $this->strahSummFrom.'-'.$this->strahSummTo;
	    }
    }
}
