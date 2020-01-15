<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 17.10.17
 * Time: 16:55
 */

namespace api\components\Rest\Get;

use api\components\Rest\RestMethod;
use common\models\GeoCountry;

/**
 * Class DictCountry
 *
 * ### Страны
 *
 * Тип запроса | URI | Комментарий
 * --- | --- | ---
 * GET | {%api_url}dict/country | Перечень стран и их аттрибутов
 *
 * Ответ — массив элементов со следующими атрибутами
 *
 * Ключ | Значение
 * --- | ---
 * iso_alpha2 | ISO 3166-1 alpha-2 код страны
 * iso_alpha3 |	ISO 3166-1 alpha-3 код страны
 * iso_numeric |	ISO 3166-1 numeric код страны
 * fips_code |	FIPS код страны
 * name |	Название страны
 * currency |	Валюта страны
 * slug |	псевдоним
 * slug |	псевдоним
 * type |	Тип записи - страна или территория ('country','territory')
 * shengen |	Входит в Шанген (0/1)
 *
 * @package api\components\Rest\Get
 */
class DictCountry extends RestMethod
{
    /** @inheritdoc */
    public $sort_order = 10;
    /**
     * @inheritdoc
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /** @inheritdoc */
    public function save(){
        return $this->filterFields(GeoCountry::find()->select("*")->all(),
            ['iso_alpha2', 'iso_alpha3', 'iso_numeric', 'fips_code', 'name', 'currency', 'slug', 'type', 'shengen']);
    }

}