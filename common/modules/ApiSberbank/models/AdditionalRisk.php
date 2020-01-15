<?php

namespace common\modules\ApiSberbank\models;

use Yii;

/**
 * This is the model class for table "api_sberbank_risk".
 *
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $paragraph
 *
 * @property \common\models\Risk[] $internalRisks
 * @property Territory[] $territories
 */
class AdditionalRisk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_sberbank_additional_risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slug', 'name'], 'required'],
            [['slug'], 'string', 'max' => 50],
            [['name'], 'string', 'max' => 100],
            [['paragraph'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Параметр API Sberbank',
            'name' => 'Название риска',
            'paragraph' => 'Пункты'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInternalRisks()
    {
        return $this->hasMany(\common\models\Risk::className(), ['id' => 'internal_id'])->viaTable('api_sberbank_additional_risk2internal', ['risk_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTerritories()
    {
        return $this->hasMany(Territory::className(), ['id' => 'territory_id'])->viaTable('api_sberbank_additional_risk2territory', ['risk_id' => 'id']);
    }
}
