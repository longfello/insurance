<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;
use common\components\SerializeBehavior;

/**
 * Риски
 * This is the model class for table "api_tinkoff_risk".
 *
 * @property integer $id
 * @property string $Name
 * @property string $Code
 * @property string $Type
 * @property string $TypeValues
 * @property integer $parent_id
 * @property integer $enabled
 *
 * @property Risk2Internal[] $Risk2Internals
 * @property Risk[] $internalRisks
 */
class Risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name', 'Code'], 'required'],
            [['parent_id', 'enabled'], 'integer'],
            [['Name', 'Type'], 'string', 'max' => 255],
            [['Code'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'Name' => 'Название',
            'Code' => 'Код',
            'Type' => 'Тип',
            'TypeValues' => 'Значение',
            'parent_id' => 'Родительский риск',

        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'serializedAttributes' => [
                'class' => SerializeBehavior::className(),
                'attributes' => ['TypeValues'],
                'encode' => true,
            ],
        ];
    }

    /**
     * Соотношение рисков АПИ рискам внутренним
     * @return \yii\db\ActiveQuery
     */
    public function getRisk2Internals()
    {
        return $this->hasMany(Risk2Internal::className(), ['risk_id' => 'id']);
    }

    /**
     * Внутренние риски
     * @return \yii\db\ActiveQuery
     */
    public function getInternalRisks()
    {
        return $this->hasMany(\common\models\Risk::className(), ['id' => 'internal_id'])->viaTable('api_tinkoff_risk2internal', ['risk_id' => 'id']);
    }
}
