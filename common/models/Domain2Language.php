<?php

namespace common\models;

use Yii;

/**
 * Связь доменов с локализациями
 * This is the model class for table "domain2language".
 *
 * @property integer $domain_id
 * @property integer $language_id
 *
 * @property Domain $domain
 * @property Languages $language
 */
class Domain2Language extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'domain2language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['domain_id', 'language_id'], 'required'],
            [['domain_id', 'language_id'], 'integer'],
            [['domain_id'], 'exist', 'skipOnError' => true, 'targetClass' => Domain::className(), 'targetAttribute' => ['domain_id' => 'id']],
            [['language_id'], 'exist', 'skipOnError' => true, 'targetClass' => Languages::className(), 'targetAttribute' => ['language_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'domain_id' => 'Domain ID',
            'language_id' => 'Language ID',
        ];
    }

    /**
     * Домен
     * @return \yii\db\ActiveQuery
     */
    public function getDomain()
    {
        return $this->hasOne(Domain::className(), ['id' => 'domain_id']);
    }

    /**
     * Язык
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Languages::className(), ['id' => 'language_id']);
    }
}
