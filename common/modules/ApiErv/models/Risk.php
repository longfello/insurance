<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiErv\models;

use Yii;

/**
 * Риски
 * This is the model class for table "api_erv_risk".
 *
 * @property integer $id
 * @property integer $parent_id
 * @property string $description
 *
 * @property \common\models\Risk $parent
 */
class Risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_erv_risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'required'],
            [['parent_id'], 'integer'],
            [['description'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'parent_id' => Yii::t('backend', 'Эквивалент во внутреннем справочнике'),
            'description' => Yii::t('backend', 'Описание'),
        ];
    }

	/**
     * Соответствие риску во внутреннем справочнике рисков
	 * @return \yii\db\ActiveQuery
	 */
	public function getParent()
	{
		return $this->hasOne(\common\models\Risk::className(), ['id' => 'parent_id']);
	}

}
