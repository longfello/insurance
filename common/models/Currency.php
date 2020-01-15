<?php

namespace common\models;

use microinginer\CbRFRates\CBRFException;
use Yii;
use yii\base\Exception;

/**
 * Валюта
 * This is the model class for table "currency".
 *
 * @property integer $id
 * @property string $name
 * @property string $value
 * @property string $char_code
 * @property integer $num_code
 * @property integer $nominal
 */
class Currency extends \yii\db\ActiveRecord
{
    /**
     * Евро
     */
    const EUR = 'EUR';
    /**
     * Рубли
     */
    const RUR = 'RUR';
    /**
     * Доллары
     */
    const USD = 'USD';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'currency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'value', 'char_code', 'num_code', 'nominal'], 'required'],
            [['value'], 'number'],
            [['num_code', 'nominal'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['char_code'], 'string', 'max' => 3],
            [['char_code'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'value' => 'Курс',
            'char_code' => 'Код ISO',
            'num_code' => 'Код цифровой',
            'nominal' => 'Номинал',
        ];
    }

    /**
     * Конвертировать в другую валюту
     * @param $amount
     * @param string $fromCur
     * @param string $toCur
     * @param int $precision
     *
     * @return float
     * @throws Exception
     */public static function convert($amount, $fromCur = self::EUR, $toCur = self::RUR, $precision = 2)
	{
		$rates = [];
		$rates[$fromCur] = self::getRate($fromCur);
		$rates[$toCur] = self::getRate($toCur);

		if (!isset($rates[$toCur]) || !$rates[$toCur]) {
			throw new Exception("Currency ".$toCur." do not have rate");
		}

		$value = $amount * $rates[$fromCur] / $rates[$toCur];
		if ($precision < 0) $value = ceil($value);
		return round($value, $precision);
	}

    /**
     * Возвращает текущий курс
     * @param $iso
     *
     * @return int|string
     * @throws Exception
     */
    public static function getRate($iso){
    	if ($iso == self::RUR) return 1;
    	$model = Currency::findOne(['char_code' => $iso]);
    	if ($model){
    		return $model->value;
	    } else {
    		throw new Exception("Currency not founded: ".$iso, 404);
	    }

	}
}
