<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\ApiLiberty\models;

use Yii;
use baibaratsky\yii\behaviors\model\SerializedAttributes;
use common\components\SerializeBehavior;

/**
 * Программы
 * This is the model class for table "api_liberty_program".
 *
 * @property string $id
 * @property integer $productId
 * @property string $productName
 * @property integer $riskId
 * @property string $riskName
 * @property integer $summ_id
 * @property integer $amount
 * @property array $countries
 * @property array $risks
 * @property integer $medical_option
 * @property string $created_at
 * @property integer $in_order
 *
 * @property Product $product
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_liberty_program';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productId', 'riskId', 'riskName', 'amount', 'countries', 'summ_id'], 'required'],
            [['productId', 'riskId', 'amount', 'summ_id', 'medical_option', 'in_order'], 'integer'],
            [['created_at'], 'safe'],
            [['productName', 'riskName'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id программы',
            'productId' => 'Id продукта',
            'productName' => 'Название продукта',
            'riskId' => 'Id основного риска',
            'riskName' => 'Название основного риска',
            'summ_id' => 'Id страховой суммы основного риска',
            'amount' => 'Страховая сумма основного риска',
            'countries' => 'Страны',
            'risks' => 'Риски',
            'medical_option' => 'Ид опции по медицине',
            'created_at' => 'Дата создания',
            'in_order' => 'Используется в заказах',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'serializedAttributes' => [
                'class' => SerializeBehavior::className(),
                'attributes' => ['countries', 'risks'],
                'encode' => true,
            ],
        ];
    }

    /**
     * Продукт
     * @return \yii\db\ActiveQuery
     */
    public function getProduct()
    {
        return $this->hasOne(Product::className(), ['productId' => 'productId']);
    }

    /**
     * Риски в виде массива
     * @return array
     */
    public function getRisksAsArray() {
        $res = [];
        $res['Медицинские расходы'] = $this->amount; //Строка с программой медицинских рисков

        $medical_query = Summ2Cost::find()->where(['summ_id'=>$this->summ_id]);
        foreach ($medical_query->all() as $medical_row) {
            $res[$medical_row['name']] = $medical_row['amount'];
        }

        foreach ($this->risks as $one_risk) {
            $description = (isset($one_risk['description']) && $one_risk['description']!='')?$one_risk['description']:$one_risk['name'];
            $res[$description] = $one_risk['amount'];
        }
        //arsort($res);

        return $res;
    }


    /**
     * Предпросмотр
     * @return string
     */
    public function preview() {
        return $this->productName;
    }
}
