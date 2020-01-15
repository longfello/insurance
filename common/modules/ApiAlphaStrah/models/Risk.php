<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;

/**
 * Риски
 * This is the model class for table "risk".
 *
 * @property integer $riskID
 * @property string $risk
 * @property string $riskPrintName
 * @property string $riskUID
 * @property string $parent_id
 * @property string $description
 * @property bool $enabled
 * @property string $class
 *
 * @property \common\models\Risk[] $internalRisks
 */
class Risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['riskID', 'risk', 'riskPrintName', 'riskUID'], 'required'],
            [['riskID', 'enabled'], 'integer'],
            [['risk', 'riskPrintName', 'parent_id', 'class'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1024],
            [['riskUID'], 'string', 'max' => 36],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'riskID' => Yii::t('backend', 'ID'),
            'risk' => Yii::t('backend', 'Наименование'),
            'riskPrintName' => Yii::t('backend', 'Наименование для печати'),
            'riskUID' => Yii::t('backend', 'GUID'),
            'parent_id' => 'Соответствует риску во внутреннем справочнике',
            'enabled' => 'Разрешен',
            'description' => 'Описание',
            'class' => 'Класс обработчика',
        ];
    }

    /**
     * Соответствие внутренним рискам (общему справочнику)
     * @return \common\models\Risk[]
     */
    public function getInternalRisks(){
    	return \common\models\Risk::findAll(['id' => explode(',', $this->parent_id)]);
    }
}
