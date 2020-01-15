<?php

namespace common\modules\ApiSberbank\models;

use Yii;

/**
 * This is the model class for table "api_sberbank_risk".
 *
 * @property int $id
 * @property string $name
 * @property string $paragraph
 *
 * @property ApiSberbankRisk2internal[] $apiSberbankRisk2internals
 * @property Risk[] $internals
 */
class Risk extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_sberbank_risk';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'paragraph'], 'required'],
            [['name', 'paragraph'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'paragraph' => 'Paragraph',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApiSberbankRisk2internals()
    {
        return $this->hasMany(ApiSberbankRisk2internal::className(), ['risk_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInternals()
    {
        return $this->hasMany(Risk::className(), ['id' => 'internal_id'])->viaTable('api_sberbank_risk2internal', ['risk_id' => 'id']);
    }
}
