<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 03.06.16
 * Time: 13:30
 */

namespace common\modules\geo\components;

use common\modules\geo\models\GeoCountry;
use common\modules\geo\models\GeoName;
use common\modules\geo\models\GeoUrl;
use common\modules\geo\models\GeoZone;
use frontend\components\city;

/**
 * Class choosed выбранная локация
 * @package common\modules\geo\components
 */
class choosed {
  /**
   * @var bool Выбрано глобальное отображение (локация не выбран)
   */
  public $isGlobal = true;
  /**
   * @var bool|integer идентификатор страны
   */
  public $country_id = false;
  /**
   * @var bool|integer идентификатор региона
   */
  public $zone_id    = false;
  /**
   * @var bool|integer идентификатор города
   */
  public $city_id    = false;

  /**
   * choosed constructor.
   */
  public function __construct(){
    $this->reset();
//    $this->setLocation(city::getChoosed());
  }

    /**
     * Установка локации
     * @param $location
     *
     * @return bool
     */
  public function setLocation($location){
    if ($location) {
      if ($location instanceof GeoCountry) {
        $this->isGlobal = false;
        if ($this->country_id && ($this->country_id == $location->id) && (!$this->zone_id) && (!$this->city_id)) return true;
        $this->country_id = $location->id;
        $this->zone_id    = false;
        $this->city_id    = false;
        return true;
      }
      if ($location instanceof GeoZone) {
        $this->isGlobal = false;
        if ($this->country_id && $this->zone_id && (!$this->city_id) && ($this->zone_id == $location->id)) return true;

        $city = GeoName::findOne(['zone_id' => $location->id]);
        if ($city) {
          $this->country_id = $city->country_id;
        } else $this->country_id = false;
        $this->zone_id    = $location->id;
        $this->city_id    = false;
        return true;
      }
      if ($location instanceof GeoName) {
        $this->isGlobal = false;
        if ($this->country_id && $this->zone_id && $this->city_id && ($this->city_id == $location->id)) return true;

        $this->country_id = $location->country_id;
        $this->zone_id    = $location->zone_id;
        $this->city_id    = $location->id;

        return true;
      }
      if (is_string($location)) {
        $model = GeoUrl::findOne(['slug' => $location]);
        if ($model) {
          $this->country_id  = $model->country_id;
          $this->zone_id     = $model->zone_id;
          $this->city_id     = $model->city_id;
          $this->isGlobal = false;
          return true;
        }
      }
      if (is_numeric($location)) {
        $this->isGlobal = false;
        if ($this->country_id && $this->zone_id && $this->city_id && ($this->city_id == $location)) return true;

        $city = GeoName::findOne(['id' => $location]);
        if ($city) {
          $this->country_id = $city->country_id;
          $this->zone_id    = $city->zone_id;
          $this->city_id    = $city->id;
        }

        return true;
      }
    }
    return false;
  }

    /**
     * Часть URL локации
     * @return string
     */
    public function getUrlPart(){
    if (!$this->isGlobal){
      $condition = [];
      if ($this->country_id) $condition['country_id'] = $this->country_id;
      if ($this->zone_id)    $condition['zone_id']    = $this->zone_id;
      if ($this->city_id)    $condition['city_id']    = $this->city_id;


      $db = \Yii::$app->db;// or Category::getDb()
      $model = $db->cache(function ($db) use ($condition) {
        return GeoUrl::findOne($condition);
      }, 3660);

      if ($model) {
        return $model->slug.'/';
      }
    }
    return '';
  }

    /**
     * Сброс
     */
    public function reset(){
    $this->isGlobal = true;
    $this->city_id     = false;
    $this->zone_id     = false;
    $this->country_id  = false;
  }

    /**
     * Парсинг строки
     * @param $string
     *
     * @return bool
     */
    public function parseUrlToken($string){
        return $this->setLocation($string);
    }
}