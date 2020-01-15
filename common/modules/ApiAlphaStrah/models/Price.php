<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiAlphaStrah\models;

use Yii;
use common\components\Calculator\forms\TravelForm;

/**
 * Стоимости программы страхования
 * This is the model class for table "api_alpha_price".
 *
 * @property integer $id
 * @property integer $program_id
 * @property integer $amount_id
 * @property integer $region_id
 * @property integer $struh_sum_id
 * @property integer $accident_sum_id
 * @property integer $luggage_sum_id
 * @property integer $civil_sum_id
 * @property string $price
 *
 * @property InsuranceProgramm $program
 * @property Amount $amount
 * @property Regions $region
 * @property StruhSum $struh_sum
 * @property StruhSum $accident_sum
 * @property StruhSum $luggage_sum
 * @property StruhSum $civil_sum
 * @property PriceInc[] $priceIncs
 * @property Risk[] $risks
 */
class Price extends \yii\db\ActiveRecord
{
    /**
     * Признак того, что сумма включена в программу
     */
    const SUM_INCLUDED = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_alpha_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'amount_id', 'region_id', 'price', 'struh_sum_id'], 'required'],
            [['program_id', 'amount_id', 'region_id', 'struh_sum_id', 'accident_sum_id', 'luggage_sum_id', 'civil_sum_id'], 'integer'],
            [['price'], 'number'],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => InsuranceProgramm::className(), 'targetAttribute' => ['program_id' => 'insuranceProgrammID']],
            [['amount_id'], 'exist', 'skipOnError' => true, 'targetClass' => Amount::className(), 'targetAttribute' => ['amount_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
            [['struh_sum_id'], 'exist', 'skipOnError' => true, 'targetClass' => StruhSum::className(), 'targetAttribute' => ['struh_sum_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_id' => 'Программа страхования',
            'amount_id' => 'Страховая сумма',
            'region_id' => 'Регион',
            'price' => 'Страховая премия',
            'struh_sum_id' => 'Соответствие главному страховому риску',
            'accident_sum_id' => 'Соответствие страховому риску несчастного случая',
            'luggage_sum_id' => 'Соответствие страховому риску страхования багажа',
            'civil_sum_id' => 'Соответствие страховому риску гражданской ответственности',
        ];
    }

    /**
     * Программа страхования
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(InsuranceProgramm::className(), ['insuranceProgrammID' => 'program_id']);
    }

    /**
     * Страховая сумма - апи
     * @return \yii\db\ActiveQuery
     */
    public function getAmount()
    {
        return $this->hasOne(Amount::className(), ['id' => 'amount_id']);
    }

    /**
     * Регион
     * @return \yii\db\ActiveQuery
     */
    public function getRegion()
    {
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    /**
     * Страховая сумма основного риска
     * @return \yii\db\ActiveQuery
     */
    public function getStruh_sum()
    {
        return $this->hasOne(StruhSum::className(), ['id' => 'struh_sum_id']);
    }
    /**
     * Страховая сумма риска несчастного случая
     * @return \yii\db\ActiveQuery
     */
    public function getAccident_sum()
    {
        return $this->hasOne(StruhSum::className(), ['id' => 'accident_sum_id']);
    }
    /**
     * Страховая сумма риска багажа
     * @return \yii\db\ActiveQuery
     */
    public function getLuggage_sum()
    {
        return $this->hasOne(StruhSum::className(), ['id' => 'luggage_sum_id']);
    }
    /**
     * Страховая сумма риска гражданской ответственности
     * @return \yii\db\ActiveQuery
     */
    public function getCivil_sum()
    {
        return $this->hasOne(StruhSum::className(), ['id' => 'civil_sum_id']);
    }

    /**
     * Страховые суммы дополнительных рисков
     * @return \yii\db\ActiveQuery
     */
    public function getPriceIncs()
    {
        return $this->hasMany(PriceInc::className(), ['price_id' => 'id']);
    }

    /**
     * Связанные риски
     * @return \yii\db\ActiveQuery
     */
    public function getRisks()
    {
        return $this->hasMany(Risk::className(), ['riskID' => 'risk_id'])->viaTable('api_alpha_price2risk', ['price_id' => 'id']);
    }

    /**
     * Связанные риски в виде массива
     * @param TravelForm $form
     *
     * @return array
     */public function getRisksAsArray(TravelForm $form){
		$res = [];
        $filter_risks = [];
        $res['Медицинские расходы'] = $this->amount->amount;
        foreach($form->params as $param) {
            if ($param->change_desc==1) $filter_risks[$param->id] = $param->handler->checked;
        }

		foreach ($this->priceIncs as $one){
            if (!isset($filter_risks[$one->filter_id]) || $filter_risks[$one->filter_id]) $res[$one->name] = $one->amount;
		}
		return $res;
	}

    /**
     * Предпросмотр суммы
     * @return string
     */
    public function preview(){
		$name = 'Програма страхования: '.$this->program->insuranceProgrammPrintName.'<br>';
		$name .= 'Страховая сумма: '.$this->amount->amount.' EUR<br>';
		$name .= 'Регион: '.$this->region->name.'<br>';
		return $name;
	}

}
