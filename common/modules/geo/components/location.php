<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 29.02.16
 * Time: 9:48
 */

namespace common\modules\geo\components;

use common\modules\geo\models\GeoCountry;
use common\modules\geo\models\GeoName;
use common\modules\geo\models\GeoNameAlter;
use common\modules\geo\models\GeoNameSparital;
use common\modules\geo\models\GeoZone;
use yii\base\Component;
use Yii;
use yii\helpers\Url;
use yii\web\View;

/**
 * Class location локация
 * @package common\components\geo
 * @property \common\modules\geo\models\GeoName $city
 * @property \common\modules\geo\components\choosed $choosed
 *
 */
class location extends Component
{

    /**
     * Источник - кука
     */
    const COOKIE = 1;
    /**
     * Источник - сессия
     */
    const SESSION = 2;
    /**
     * Источник - БД
     */
    const DATABASE = 3;
    /**
     * Источник - GEOIP
     */
    const GEOIP = 10;

    /**
     * слюг параметра локации
     */
    const LOCATION_SLUG = 'UserLocation';
    /**
     * слюг параметра координат
     */
    const LOCATION_SLUG_COORD = 'UserLocationCoord';

    /**
     * дефолтные координаты
     */
    const defaultLatitude = 50.45466;
    /**
     * дефолтные координаты
     */
    const defaultlongitude = 30.5238;

    /**
     * @var string название параметра куки
     */
    private $cookie = self::LOCATION_SLUG;
    /**
     * @var string название параметра сессии
     */
    private $session = self::LOCATION_SLUG;
    /**
     * @var string префикс ресурсов
     */
    private $asset = '';

    /** @var $city GeoName город */
    public $city;
    /**
     * @var int режим обнаружения
     */
    public $mode = self::SESSION;

    /**
     * @var string IP адрес
     */
    public $ip;

    /**
     * @var int локация по-умолчанию
     */
    public $defaultLocation; // = 703448;
    /**
     * @var bool Не найдено (флаг)
     */
    public $notFounded = true;

    /**
     * @var choosed $choosed выбранная локация
     */
    public $choosed;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (is_a(Yii::$app, 'yii\console\Application')) {
            return;
        }
        Yii::setAlias('@frontendUrl', Url::base());

        $this->ip = self::GetIP();
        // грязный хак
        $this->choosed = new choosed();
        $location      = \Yii::$app->session->get($this->session, false);
        if ( ! $location) {
            $this->mode = self::COOKIE;
            $location   = \Yii::$app->request->cookies->get($this->cookie);
            if ( ! $location) {
                if (\Yii::$app->user->isGuest) {
                    $this->city = $this->getCityIDByIp();
                    $location   = $this->city ? $this->city->id : false;
                    $this->mode = self::GEOIP;
                } else {
//          $location = $location = \Yii::$app->user->identity->location_id;
//          $this->mode = self::DATABASE;
//          if (!$location) {
                    $this->city = $this->getCityIDByIp();
                    $location   = $this->city ? $this->city->id : false;
                    $this->mode = self::GEOIP;
//          }
                }
            }
        }
        if ( ! $this->notFounded) {
            $this->save($location);
        }

        if ( ! ($this->city instanceof GeoName)) {
            $this->city = GeoName::find()->localized()->andWhere(['id' => $location])->one();
        }
        if ( ! $this->city) {
            $this->city       = GeoName::find()->localized()->andWhere(['id' => $this->defaultLocation])->one();
            $this->notFounded = true;
        }

        if ($this->notFounded) {
            \Yii::$app->view->registerJsFile(
                $this->asset . '/js/locator.js',
                ['depends' => [\yii\web\JqueryAsset::className()]],
                View::POS_END
            );
        }

        parent::init();
    }

    /**
     * Определение по IP
     * @param bool $markAsNotFounded
     *
     * @return GeoNameSparital
     */
    public function getCityIDByIp($markAsNotFounded = true)
    {
        $city = false;
        // IP-адрес, который нужно проверить
        $this->ip = (substr($this->ip, 0, 7) == '192.168') ? "195.24.147.106" : $this->ip;
        // Преобразуем IP в число
        $int = sprintf("%u", ip2long($this->ip));

        // Ищем город в глобальной базе
        $sql    = "select * from (select * from geo_ip where begin_ip<=$int order by begin_ip desc limit 1) as t where end_ip>=$int";
        $result = \Yii::$app->db->createCommand($sql)->queryOne();
        if ($result) {
            $city = GeoName::find()->localized()->andWhere(['id' => $result['geonameid']])->one();
        }

        if ( ! $city) {
            $city = GeoName::find()->localized()->andWhere(['id' => $this->defaultLocation])->one();
            if ($markAsNotFounded) {
                $this->notFounded = true;
            }
        }

        return $city;
    }

    /**
     * Установка локации
     * @param $id_or_GeoName
     *
     * @return bool
     */
    function set($id_or_GeoName)
    {
        $id   = ($id_or_GeoName instanceof GeoName) ? $id_or_GeoName->id : $id_or_GeoName;
        $city = GeoName::find()->andWhere(['id' => $id])->one();
        if ($city) {
            $this->city = $city;
            $this->save($id);

            return true;
        }

        return false;
    }


    /**
     * Очистка кеша
     */
    function clearCache()
    {
        \Yii::$app->session->remove($this->session);
        \yii::$app->response->cookies->remove($this->cookie);
    }

    /**
     * Сохранение локации
     * @param $id
     */
    public function save($id)
    {
        \Yii::$app->session[$this->session] = $id;
        \Yii::$app->response->cookies->add(new \yii\web\Cookie([
            'name'  => $this->cookie,
            'value' => $id
        ]));
        if ( ! \Yii::$app->user->isGuest) {
            \Yii::$app->user->identity->location_id = $id;
            \Yii::$app->user->identity->save();
        }
    }


    /**
     * Получение IP пользователя
     * @return string|null
     */
    public static function GetIP()
    {
        $ip = null;
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")) {
            $ip = getenv("HTTP_CLIENT_IP");
        } else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")) {
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        } else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")) {
            $ip = getenv("REMOTE_ADDR");
        } else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'],
                "unknown")) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        return $ip;
    } // GetIP

    /**
     * Определение локации по координатам
     * @param $lat
     * @param $lng
     * @param bool $id
     *
     * @return false|null|string
     */
    public static function getCityIdByCoord($lat, $lng, $id = false)
    {
        $lng = floatval($lng);
        $lat = floatval($lat);

        $add_order = $id ? " name_id <> $id, " : "";

        $query = "SELECT name_id FROM geo_name_sparital WHERE MBRContains (area, POINT($lng, $lat)) ORDER BY $add_order population ASC";

        return \Yii::$app->db->createCommand($query)->queryScalar();
    }

    /**
     * Полное название локации
     * @param $id
     *
     * @return string
     */
    public static function getFullName($id)
    {
        $city = GeoName::find()->localized()->andWhere(['id' => $id])->one();
        if ($city) {
            $zone    = GeoZone::find()->localized()->andWhere(['id' => $city->zone_id])->one();
            $country = GeoCountry::find()->localized()->andWhere(['id' => $city->country_id])->one();

            $zone    = $zone ? ', ' . $zone->name : '';
            $country = $country ? ', ' . $country->name : '';

            return $city->name . $zone . $country;
        }

        return '';
    }

    /**
     * Получение локации по имени
     * @param $name
     *
     * @return array|GeoName|null|\yii\db\ActiveRecord
     */
    static function getCityByName($name)
    {
        $name = \Yii::$app->db->quoteValue($name);

        return GeoName::find()->joinWith('altername')->localized()->where(['altername' => $name])->one();
    }

    /**
     * Координаты в виде строки (SQL)
     * @return string
     */
    public static function getCoordsAsString()
    {
        $coords = self::getCoords();

        return "POINT({$coords['longitude']},{$coords['latitude']})";
    }

    /**
     * Лоадер координат
     * @return array|mixed
     */
    public static function getCoords()
    {
        if ($coords = Yii::$app->session->get(self::LOCATION_SLUG_COORD, false)) {
//      var_dump($coords); die();
            Yii::trace('Location obtained by browser - ' . $coords['latitude'] . ',' . $coords['longitude'],
                'Location');
        } else {
            if (Yii::$app->location->city && Yii::$app->location->city->sparital) {
                $data   = Yii::$app->location->city->sparital;
                $coords = array(
                    'latitude'  => $data->latitude,
                    'longitude' => $data->longitude
                );
            } else {
                $coords = array(
                    'latitude'  => self::defaultLatitude,
                    'longitude' => self::defaultlongitude
                );
            }
            Yii::trace('Location obtained by PHP - ' . $coords['latitude'] . ',' . $coords['longitude'], 'Location');
        }

        return $coords;
    }

}