<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use Yii;

/**
 * Риски
 * This is the model class for table "api_vtb_risk".
 *
 * @property integer $id
 * @property string $name
 * @property string $class
 * @property string $description
 *
 * @property Risk2internal[] $apiVtbRisk2internals
 * @property Risk[] $internalRisks
 */
class Risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'class'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1024],
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
			'class' => 'Класс обработчика',
			'description' => 'Описание',
		];
	}

    /**
     * Таблица соответствия рисков АПИ рискам внутренним
     * @return \yii\db\ActiveQuery
     */
    public function getApiVtbRisk2internals()
    {
        return $this->hasMany(Risk2internal::className(), ['risk_id' => 'id']);
    }

    /**
     * Внутренние риски
     * @return \yii\db\ActiveQuery
     */
    public function getInternalRisks()
    {
        return $this->hasMany(\common\models\Risk::className(), ['id' => 'internal_id'])->viaTable('api_vtb_risk2internal', ['risk_id' => 'id']);
    }
}
