<?php

namespace common\components;
use common\components\Calculator\forms\prototype;
use common\models\Api;
use common\models\Orders;
use common\models\Person;
use common\components\Calculator\forms\TravelForm;
use common\modules\ApiLiberty\components\ProgramSearch;
use frontend\models\PersonInfo;
use linslin\yii2\curl\Curl;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\httpclient\Exception;
use yii\web\HttpException;

/**
 * alpha module definition class
 * @property $soap \SoapClient
 */
abstract class ApiModule extends \yii\base\Module
{
    /**
     * Расчет стоимости локальный
     */
    const CALC_LOCAL = 'local';
    /**
     * Расчет стоимости через API
     */
    const CALC_API   = 'api';

    /**
     * Окружение тестовое
     */
    const ENV_TEST   = "test";
    /**
     * Окружение боевое
     */
    const ENV_PROD   = "prod";

	/** @var Api Модель API */
	public $model;
    /**
     * @var string Название API
     */
    public $name;
    /**
     * @var bool Признак возможности расчета стоимости локально
     */
    public $has_local = true;

	/** @var \SoapClient SOAP клиент */
	protected $_soap;

    /**
     * @var string External API URI
     */
    public $uri;

	/**
	 * @var int Max traveller count
	 */
	public $maxTravellersCount = 100;

    /**
     * @inheritdoc
     */
    public function init()
    {

        parent::init();

        $this->model = Api::findOne(['class' => get_called_class()]);
        if ($this->model){
        	$this->name = $this->model->name;
        	$this->has_local = (bool)$this->model->local_calc;
        }
        // custom initialization code goes here
    }

	/**
     * Поиск программ по заданным критериям
	 * @param TravelForm $form Критерии поиска
	 *
	 * @throws Exception
	 *
	 * @return null|\common\models\ProgramResult
	 */
	public function search( TravelForm $form ){
		throw new Exception("Search method not implemented for api ".$this->name, 501);
    }

	/**
     * Создает и возвращает заказ по заданым критериям
     * @throws Exception
     * @return false|\common\models\Orders
	 */
	public function getOrder(TravelForm $form, $program_id){
		throw new Exception("getPolice method not implemented for api ".$this->name, 501);
    }

	/**
     * Оформляет полис указанного заказа на стороне удаленного API
     * @throws Exception
     * @return null
	 */
	public function buyOrder(Orders $order){
		$order->status = Orders::STATUS_PAYED;
		$order->save();

		if ($this->confirmApiPayment($order)) {
			$this->downloadOrder($order, null);
			$this->sendOrderMail($order);
		}
    }


	/**
	 * Отправляет информацию об оплате полиса в api
	 * @throws Exception
	 * @return bool
	 */
	public function confirmApiPayment( Orders $order ) {
		throw new Exception("confirmApiPayment method not implemented for api ".$this->name, 501);
	}

	/**
	 * Отправляет информацию о полиса на почту (для некоторых api не нужно)
	 */
	public function sendOrderMail($order) {

	}

	/**
     * Скачивает полис указанного заказа с API
     * @throws Exception
     * @return string[] Лог операции. Ключ - timestamp, значение - строка
	 */
	public function downloadOrder(Orders $order, $additionalInfo = null){
		throw new Exception("downloadOrder method not implemented for api ".$this->name, 501);
    }

	/**
     * Возвращает массив для формирования меню \yii\widgets\Menu
     * @return array[]|false
	 */
	public static function getAdminMenu(){
		return false;
	}

    /**
     * Запрос к API
     * @param string $method
     * @param array $params
     *
     * @return mixed
     */
    private function request( $method, $params = [] ) {
		$parameters = array_merge( [
			'agentUid' => $this->agentUid
		], $params );
		$result     = $this->soap->$method( [ 'parameters' => $parameters ] );
/*
		$result = (array) $result;
		$result = array_pop( $result );
		$result = (array) $result;
		$result = array_pop( $result );
*/
		return $result;
	}

	/**
     * Получение модели персоны по параметрам информации о персоне
	 * @param $info PersonInfo
	 * @return Person
     * @throws \Exception
	 */
	public function getHolder(PersonInfo $info){
		$holder = Person::findOne([
			'first_name' => $info->first_name,
			'last_name' => $info->last_name,
			'birthday' => $info->birthday,
		]);
		if (!$holder){
			$holder = new Person();
			$holder->load(ArrayHelper::toArray($info), '');
			if (!$holder->save()){
			    throw new \Exception(strip_tags(Html::errorSummary($holder)));
            }
		}
		return $holder;
	}

    /**
     * Возвращает ссылку на скачивание полиса
     * @param Orders $order
     *
     * @return bool|string
     */
	public function getPoliceLink(Orders $order){
		$filename = 'police.pdf';
		$folder = $this->getOrderFolder($order);
		$file   = $folder.'/'.$filename;
		if (file_exists($file)){
			return Url::to(\Yii::getAlias('@frontendUrl').$this->getOrderRelativeFolder($order).'/'.$filename, true);
		}
		return false;
 	}

    /**
     * Загружает полис заказа с указанного URL
     * @param Orders $order
     * @param $url
     */
    public function wgetPolice(Orders $order, $url){
		$folder = $this->getOrderFolder($order);
		if( $curl = curl_init() ) {
			curl_setopt($curl, CURLOPT_URL, $url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($curl, CURLOPT_HEADER,0);
			curl_setopt($curl, CURLOPT_TIMEOUT, '150');
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, '150');
			curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
			if( ! $result = curl_exec($curl))
			{
				trigger_error(curl_error($curl));
			}

			file_put_contents($folder.'police.pdf', $result);
			curl_close($curl);
		}

/*
		$curl = new Curl();
		$folder = $this->getOrderFolder($order);
		$responce = $curl->get($url);

		if ($curl->response) {
			file_put_contents($folder.'police.pdf', $curl->response);
		} else {
			// List of curl error codes here https://curl.haxx.se/libcurl/c/libcurl-errors.html
			throw new HttpException(500, print_r($curl, true));
		}
*/
	}

    /**
     * Возвращает папку для файла полиса заказа
     * @param Orders $order
     *
     * @return string
     */
    public function getOrderFolder(Orders $order){
		$path  = \Yii::getAlias('@frontend/web');
		$path .= $this->getOrderRelativeFolder($order);
		if (!is_dir($path)){
			mkdir($path, 0777, true);
		}

		return $path.'/';
	}

    /**
     * Возвращает относительную папку для файла полиса заказа
     * @param Orders $order
     *
     * @return string
     */
    public function getOrderRelativeFolder(Orders $order){
		$path = '/orders/';

		$hash = sha1($order->id . $order->holder->email);

		$path .= substr($hash, 0, 1).'/';
		$path .= substr($hash, 0, 3).'/';
		$path .= $hash;
		return $path;
	}

	/**
     * Возвращает объект искателя программ
     *
     * @param TravelForm|prototype $calcForm
     * @return ProgramSearch|mixed
	 */
	public function getProgramSearch($calcForm){
        $class = new \ReflectionClass($this);
        $searcherClass = $class->getNamespaceName().'\components\ProgramSearch';
        if (class_exists($searcherClass)){
            return new $searcherClass([
                'form'   => $calcForm,
                'module' => $this
            ]);
        } else return null;
    }

    /**
     * Расчитывает стоимость указанного полиса. Возможно два варианта расчета: self::CALC_LOCAL - локальный, self::CALC_API - расчет на стороне API
     * @param $program
     * @param $form
     * @param string $calc_type
     *
     * @return float|int
     */
    abstract function calcPrice($program, $form, $calc_type = self::CALC_LOCAL);

    /**
     * Возвращает программу по её id
     * @param $program_id
     *
     * @return mixed
     */
    abstract function getProgram($program_id);
    /**
     * Возвращает идентификатор программы по переданоому ей объекту программы
     * @param mixed|object $program
     *
     * @return mixed
     */
     abstract function getProgramId($program);
}
