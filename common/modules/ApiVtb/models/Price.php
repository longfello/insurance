<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiVtb\models;

use Yii;
use yii\helpers\Html;
use common\components\Calculator\forms\TravelForm;

/**
 * Стоимость
 * This is the model class for table "api_vtb_price".
 *
 * @property integer $id
 * @property integer $program_id
 * @property integer $amount_id
 * @property integer $period_id
 * @property integer $region_id
 * @property string $price
 *
 * @property Program $program
 * @property Amount $amount
 * @property Period $period
 * @property Regions $region
 * @property Price2risk[] $apiVtbPrice2risks
 * @property Risk[] $risks
 */
class Price extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_vtb_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['program_id', 'amount_id', 'period_id', 'region_id', 'price'], 'required'],
            [['program_id', 'amount_id', 'period_id', 'region_id'], 'integer'],
            [['price'], 'number'],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['amount_id'], 'exist', 'skipOnError' => true, 'targetClass' => Amount::className(), 'targetAttribute' => ['amount_id' => 'id']],
            [['period_id'], 'exist', 'skipOnError' => true, 'targetClass' => Period::className(), 'targetAttribute' => ['period_id' => 'id']],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'program_id' => 'Програма страхования',
            'amount_id' => 'Страховая сумма',
            'period_id' => 'Период страховки',
            'region_id' => 'Регион',
            'price' => 'Страховая премия',
        ];
    }

    /**
     * Программа
     * @return \yii\db\ActiveQuery
     */
    public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    /**
     * Страховая сумма
     * @return \yii\db\ActiveQuery
     */
    public function getAmount()
    {
        return $this->hasOne(Amount::className(), ['id' => 'amount_id']);
    }

    /**
     * Период
     * @return \yii\db\ActiveQuery
     */
    public function getPeriod()
    {
        return $this->hasOne(Period::className(), ['id' => 'period_id']);
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
     * Соответствие цена/риск
     * @return \yii\db\ActiveQuery
     */
    public function getApiVtbPrice2risks()
    {
        return $this->hasMany(Price2risk::className(), ['price_id' => 'id']);
    }

    /**
     * Риски
     * @return \yii\db\ActiveQuery
     */
    public function getRisks()
    {
        return $this->hasMany(Risk::className(), ['id' => 'risk_id'])->viaTable('api_vtb_price2risk', ['price_id' => 'id']);
    }


    /**
     * @param TravelForm $form
     *
     * @return array
     */public function getRisksAsArray(TravelForm $form){
		$res = [];

        $res['Медицинские расходы'] = $this->amount->amount;
        $filter_risks = [];
        foreach($form->params as $param) {
            if ($param->change_desc==1) $filter_risks[$param->risk_id] = $param->handler->checked;
        }

		foreach ($this->apiVtbPrice2risks as $p2r){
			/** @var $p2r Price2risk */
			if ($p2r && $p2r->risk) {
				$risk = $p2r->risk;
				/** @var $risk Risk */
				$description = [];

                $col_risks = 0;
                $show_risks = false;
				foreach($risk->internalRisks as $internalRisk){
                    if (isset($filter_risks[$internalRisk->id])) {
                        $col_risks++;
                        $show_risks = $show_risks || $filter_risks[$internalRisk->id];
                    }

					/** @var $internalRisk \common\models\Risk */
					$description[] = $internalRisk->description;
				}

                if ($col_risks==0 || $show_risks) {
                    $description = $risk->description ? $risk->description : implode('. ', $description);
                    $res[$description] = $p2r->amount;
                }
			}
		}
		return $res;
	}

    /**
     * @return string
     */
    public function preview(){
		$name = 'Програма страхования: '.$this->program->name.' ('.$this->program->code.')<br>';
		$name .= 'Страховая сумма: '.$this->amount->amount.' EUR<br>';
		$name .= 'Период: от '.$this->period->from.' до '.$this->period->to.' дней<br>';
		$name .= 'Регион: '.$this->region->name.'<br>';
		return $name;
	}

}
