<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiTinkoff\models;

use Yii;
use common\components\Calculator\forms\TravelForm;
/**
 * Цены
 * This is the model class for table "api_tinkoff_price".
 *
 * @property integer $id
 * @property integer $product_id
 * @property string $name
 * @property string $AssistanceLevel
 * @property string $Currency
 * @property integer $TravelMedicineLimit
 * @property integer $DeductibleAmount
 *
 * @property Product $product
 * @property Price2Risk[] $price2Risks
 * @property Price2Area[] $price2Areas
 * @property Price2Country[] $price2Countries
 */
class Price extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_tinkoff_price';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['product_id'], 'required'],
            [['product_id', 'TravelMedicineLimit', 'DeductibleAmount'], 'integer'],
            [['name', 'AssistanceLevel'], 'string', 'max' => 255],
            [['Currency'], 'string', 'max' => 3],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'product_id' => 'Product ID',
            'name' => 'Название',
            'AssistanceLevel' => 'Уровень поддержки',
            'Currency' => 'Валюта',
            'TravelMedicineLimit' => 'Страховой лимит',
            'DeductibleAmount' => 'Размер франшизы (в валюте страхования)',
        ];
    }

    /**
     * Продукт
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['id' => 'product_id']);
    }

    /**
     * Риски
     * @return \yii\db\ActiveQuery
     */
    public function getPrice2Risks()
    {
        return $this->hasMany(Price2Risk::className(), ['price_id' => 'id']);
    }


    /**
     * Справочник риски-территории
     * @return \yii\db\ActiveQuery
     */
    public function getPrice2Areas()
    {
        return $this->hasMany(Price2Area::className(), ['price_id' => 'id']);
    }

    /**
     * Страны
     * @return \yii\db\ActiveQuery
     */
    public function getPrice2Countries()
    {
        return $this->hasMany(Price2Country::className(), ['price_id' => 'id']);
    }

    /**
     * Риски в виде массива
     * @return []
     */
    public function getRisksAsArray(TravelForm $form) {
        $res = [];
        //$res['Медицинские расходы'] = $this->TravelMedicineLimit;
        $medical_risk = Risk::findOne(['Code'=>'TravelMedicine']);
        /** @var $medical_risk Risk */
        if ($medical_risk) {
            foreach($medical_risk->internalRisks as $internalRisk){
                $res[$internalRisk->name] = $this->TravelMedicineLimit;
            }
        } else $res['Медицинские расходы'] = $this->TravelMedicineLimit;

        $res['Размер франшизы'] = $this->DeductibleAmount;




        $filter_risks = [];
        foreach($form->params as $param) {
            if ($param->change_desc==1) $filter_risks[$param->risk_id] = $param->handler->checked;
        }

        foreach ($this->price2Risks as $p2r){
            /** @var $p2r Price2risk */
            if ($p2r) {
                $risk = Risk::findOne($p2r['risk_id']);
                if ($risk && $risk['parent_id']==0 && $risk['Code']!='TravelMedicine') {
                    /** @var $risk Risk */
                    $show_risk = false;
                    foreach($risk->internalRisks as $internalRisk){
                        if (isset($filter_risks[$internalRisk->id])) {
                            $show_risk = $show_risk || $filter_risks[$internalRisk->id];
                        }
                    }

                    $amount = $p2r->amount;
                    if ($amount==0) {
                        $subrisks_query = Risk::find()
                            ->alias('r')
                            ->select("r.*")
                            ->innerJoin("api_tinkoff_price2risk p2r", 'p2r.risk_id = r.id')
                            ->where(['r.parent_id' => $risk->id]);

                        $subrisks = $subrisks_query->all();
                        foreach ($subrisks as $subrisk) {
                            if ($subrisk->Type == 'DECIMAL') {
                                $p2r = Price2Risk::find()->where(['risk_id'=>$subrisk->id, 'price_id'=>$this->id])->one();
                                if ($p2r->amount && ($p2r->amount<$amount || $amount==0)) $amount = $p2r->amount;
                            }
                        }
                    }

                    if ($show_risk) $res[$risk->Name] = $amount;
                }
            }
        }

        arsort($res);
        return $res;
    }
}
