<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\controllers;

use common\modules\geo\components\location;
use yii\web\Controller;

/**
 * Class DefaultController
 * @package common\modules\geo\controllers
 */
class DefaultController extends Controller
{
    /**
     * Определение локации, запрос на браузерный запрос координат
     */
    public function actionIndex(){

    $location = \Yii::$app->request->post('location', array());
    $lng = isset($location['longitude'])?$location['longitude']:false;
    $lat = isset($location['latitude'])?$location['latitude']:false;

    if ($lng && $lat) {
      $city = \Yii::$app->location->getCityIdByCoord($lat, $lng);
      if ($city) {
        \Yii::$app->location->set($city[0]['id']);
        echo(json_encode(array('cmd' => 'reload')));
      } else {
        echo(json_encode(array('cmd' => 'ask')));
      }
    } else {
      echo(json_encode(array('cmd' => 'ask')));
    }
  }

    /**
     * Геттер координат
     */
    public function actionLocation(){

    $location = \Yii::$app->request->post('location', array());
    $lng = isset($location['longitude'])?$location['longitude']:false;
    $lat = isset($location['latitude'])?$location['latitude']:false;

    if ($lng && $lat) {
      \Yii::$app->session->set(location::LOCATION_SLUG_COORD, [
        'latitude' => $lat,
        'longitude' => $lng
      ]);
    }
  }
}
