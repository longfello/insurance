<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiErv\models;

use common\models\Risk;
use common\components\Calculator\forms\TravelForm;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;

/**
 * Программы страхования
 * This is the model class for table "api_erv_program".
 *
 * @property integer $id
 * @property string $name
 * @property string $product_code
 * @property string $tariff_code
 * @property string $tariff_code_sport
 * @property string $tariff_code_cancel
 * @property string $tariff_code_cancel_p
 * @property string $price
 * @property string $price_type
 * @property string $summa
 * @property integer $region_id
 * @property string $rule_base_url
 * @property string $rule_path
 * @property string $police_base_url
 * @property string $police_path
 * @property integer $pregnant_week
 *
 * @property Regions $region
 * @property Program2Risk[] $apiErvProgram2risks
 * @property Risk[] $risks
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * стоимость за день
     */
    const PRICE_PER_DAY = 'day';
    /**
     * стоимость за год
     */
    const PRICE_PER_YEAR = 'year';
	/**
	 * @var array файл правил
	 */
	public $rule;
	/**
	 * @var array файл примера полиса
	 */
	public $police;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_erv_program';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'product_code', 'tariff_code', 'price', 'summa', 'region_id'], 'required'],
            [['id', 'region_id', 'pregnant_week'], 'integer'],
            [['price', 'summa'], 'number'],
            [['price_type'], 'string'],
            [['price_type'], 'in', 'range' => [self::PRICE_PER_DAY, self::PRICE_PER_YEAR]],
            [['name'], 'string', 'max' => 255],
            [['product_code', 'tariff_code', 'tariff_code_sport', 'tariff_code_cancel', 'tariff_code_cancel_p'], 'string', 'max' => 50],
            [['region_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regions::className(), 'targetAttribute' => ['region_id' => 'id']],
	        [['rule', 'police'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('backend', 'ID'),
            'name' => Yii::t('backend', 'Название'),
            'product_code' => Yii::t('backend', 'Код продукта'),
            'tariff_code' => Yii::t('backend', 'Код тарифа'),
            'tariff_code_sport' => Yii::t('backend', 'Код доп.тарифа "Спорт"'),
            'tariff_code_cancel' => Yii::t('backend', 'Код доп.тарифа "Отмена поездки"'),
            'tariff_code_cancel_p' => Yii::t('backend', 'Код доп.тарифа "Отмена поездки Плюс"'),
            'price' => Yii::t('backend', 'Стоимость, евро'),
            'summa' => Yii::t('backend', 'Страховая сумма, евро'),
            'region_id' => Yii::t('backend', 'Регион'),
            'rule' => Yii::t('backend', 'Правила страхования'),
            'police' => Yii::t('backend', 'Образец полиса'),
            'price_type' => Yii::t('backend', 'Тип цены'),
            'pregnant_week' => Yii::t('backend', 'Максимальная неделя беременности при осложнениях')
        ];
    }

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			[
				'class' => UploadBehavior::className(),
				'attribute' => 'rule',
				'pathAttribute' => 'rule_path',
				'baseUrlAttribute' => 'rule_base_url'
			],
			[
				'class' => UploadBehavior::className(),
				'attribute' => 'police',
				'pathAttribute' => 'police_path',
				'baseUrlAttribute' => 'police_base_url'
			]
		];
	}



	/**
     * регион
     * @return \yii\db\ActiveQuery
     */
    public function getRegion(){
        return $this->hasOne(Regions::className(), ['id' => 'region_id']);
    }

    /**
     * таблица соответствия рисков
     * @return \yii\db\ActiveQuery
     */
    public function getApiErvProgram2risks()
    {
        return $this->hasMany(Program2Risk::className(), ['program_id' => 'id']);
    }

    /**
     * риски
     * @return \yii\db\ActiveQuery
     */
    public function getRisks()
    {
        return $this->hasMany(Risk::className(), ['id' => 'risk_id'])->viaTable('api_erv_program2risk', ['program_id' => 'id']);
    }

    /**
     * риски в виде массива
     * @param TravelForm $form
     *
     * @return array
     */
    public function getRisksAsArray(TravelForm $form){
    	$res = [];
        $res['Медицинские расходы'] = intval($this->summa);
        $filter_risks = [];
        foreach($form->params as $param) {
            $filter_risks[$param->risk_id] = $param->handler->checked;
        }

    	foreach ($this->apiErvProgram2risks as $one){
    		/** @var $risk Risk */
		    $description = $one->risk->description;
		    $ervRisk = \common\modules\ApiErv\models\Risk::findOne(['parent_id' => $one->risk->id]);
		    $description = $ervRisk?$ervRisk->description:$description;
            if($one->is_optional==0 || (isset($filter_risks[$one->risk->id]) && $filter_risks[$one->risk->id])) $res[$description] = intval($one->summa);
	    }
	    return $res;
    }

    /**
     * конфигурация риска беременности
     * @return array
     */
    public function getPregnantVariants() {
        $res = ['id'=>0,'name'=>0];
        $pr = Risk::findOne(['id'=>6]);
        $params = json_decode($pr->params, true);
        if (isset($params['Количество недель'])) {
            foreach ($params['Количество недель'] as $cw) {
                $res[] = ['id'=>$cw,'name'=>$cw];
            }
        }
        return $res;
    }
}
