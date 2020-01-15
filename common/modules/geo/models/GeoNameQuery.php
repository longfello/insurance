<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 29.02.16
 * Time: 12:05
 */
namespace common\modules\geo\models;

/**
 * Class GeoNameQuery Запрос с гео-функциями
 * @package common\modules\geo\models
 */
class GeoNameQuery extends \yii\db\ActiveQuery {
  use \omgdef\multilingual\MultilingualTrait;

  /** @return $this с площадью, сортировка по населению */
  public function withArea() {

    $this->joinWith('sparital');
    $this->select([
      '*',
      'Y(EndPoint(area)) end_lat',
      'X(EndPoint(area)) end_lng',
      'Y(StartPoint(area)) start_lat',
      'X(StartPoint(area)) start_lng'
    ]);
    $this->orderBy('population DESC');

    return $this;
  }

    /**
     * @return $this  с площадью
     */
    public function withArea2() {

    $this->joinWith('sparital');
    $this->select([
      '*',
      'Y(EndPoint(area)) end_lat',
      'X(EndPoint(area)) end_lng',
      'Y(StartPoint(area)) start_lat',
      'X(StartPoint(area)) start_lng'
    ]);

    return $this;
  }

}