<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;

/**
 * Риски
 * This is the model class for table "api_liberty_risk".
 *
 * @property integer $riskId
 * @property string $riskName
 * @property integer $main
 * @property string $description
 */
class Risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['riskId'], 'required'],
            [['riskId', 'main'], 'integer'],
            [['riskName'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1024],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'riskId' => 'Ид риска',
            'riskName' => 'Название риска',
            'main' => 'Основной риск (мед.)',
            'description' => 'Описание',
        ];
    }
}
