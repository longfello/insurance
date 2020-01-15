<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use Yii;

/**
 * Период страхования
 * This is the model class for table "api_vtb_period".
 *
 * @property integer $id
 * @property integer $from
 * @property integer $to
 *
 * @property string $asText
 */
class Period extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_period';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['from', 'to'], 'required'],
            [['from', 'to'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from' => 'От',
            'to' => 'До',
        ];
    }

    /**
     * Текстовое представление
     * @return string
     */
    public function getAsText(){
    	$to = ($this->to > 9999)?" дней и более":' до '.$this->to.' дней';
    	return 'от '.$this->from.$to;
    }
}
