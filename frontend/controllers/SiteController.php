<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace frontend\controllers;

use common\components\ApiModule;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\forms\prototype;
use common\models\Api;
use common\models\GeoCountry;
use common\models\Orders;
use common\models\ProgramResult;
use common\models\User;
use common\models\Page;
use common\components\Calculator\filters\Filter;
use common\components\Calculator\forms\TravelForm;
use common\modules\ApiAlphaStrah\models\Country;
use common\modules\ApiAlphaStrah\models\Price;
use common\modules\ApiAlphaStrah\models\StruhSum;
use common\modules\ApiAlphaStrah\Module;
use frontend\models\PartnerForm;
use frontend\models\PersonInfo;
use Yii;
use frontend\models\ContactForm;
use yii\base\Object;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\httpclient\Client;
use kartik\mpdf\Pdf;


/**
 * Site controller - сборная солянка действий которыне затруднительно или безсмысленно выносить в отдельные контроллеры
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null
            ],
            'set-locale'=>[
                'class'=>'common\actions\SetLocaleAction',
                'locales'=>array_keys(Yii::$app->params['availableLocales'])
            ]
        ];
    }

    /**
     * Тестовое действие
     */
    public function actionT(){
		/** @var $module Module */

		/*
		$soap = new \SoapClient('https://aisws.ingos.ru/sales-test/SalesService.svc?wsdl');

		$login_resp = $soap->Login([
			'User' => 'БУЛЛО СТРАХОВАНИЕ WS',
			'Password' => 'qk7p8cvj'
		]);
		if ($login_resp->ResponseStatus->ErrorCode==0 ){
			$session_token = $login_resp->ResponseData->SessionToken;


			$dicti = $soap->GetDicti([
				'SessionToken' => $session_token,
				'Product' => '13349216'
			]);

			//Yii::$app->response->format = Response::FORMAT_XML;
			//echo $dicti->ResponseData->any;

			VarDumper::dump( $dicti, 16, true);

		} else {
			var_dump($login_resp);
			die();
		}
		*/
		/*
		$url = "https://rgstest.virtusystems.ru/login.aspx";

		$post_data = json_encode(array("userName"=>"SarbeevAA", "password"=>"4rfv4RFV", "createPersistentCookie"=>false));

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		$data = curl_exec($ch);

	    VarDumper::dump($data, 16, true);
		*/

		$order = Orders::find()->where(['id'=>'498'])->one();

		$content = $this->renderPartial('_testpdf',[
			'order' => $order
		]);

		$pdf = new Pdf([
			'mode' => Pdf::MODE_UTF8,
			'format' => Pdf::FORMAT_A4,
			'orientation' => Pdf::ORIENT_PORTRAIT,
			'destination' => Pdf::DEST_BROWSER,
			'content' => $content,
			'cssFile' => '@webroot/styles/kv-mpdf-bootstrap.min.css',
			'options' => ['title' => 'Police for order #'.$order->id],
		]);
		Yii::$app->response->format = Response::FORMAT_RAW;

		return $pdf->render();
	}

    /**
     * Тестовое действие
     */
    public function actionT2()
	{
		$integrationID = Yii::$app->security->generateRandomString(20);

		$policy_request = array(
			'PolicyNumber' => '1561551538',
			'DocumentType' => 'policy'
		);

		$parameters = array_merge([
			'Header' => [
				'integrationID' => $integrationID,
				'user' => getenv('TINKOFF_USER'),
				'password' => getenv('TINKOFF_PASSWORD')
			]
		], $policy_request);

		$context = stream_context_create([
			'ssl' => [
				// set some SSL/TLS specific options
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			]
		]);

		try {
			$soap_lead = new \SoapClient('https://tstrp.tinkoffinsurance.ru:23001/toi/partners/quotesub/v1.0/assist?wsdl', [
				'exceptions'=>false,
				'trace'=>1,
				'stream_context' => $context
			]);

			$result = $soap_lead->getPolicyDocument($parameters);
			//echo "<pre>";
			echo htmlentities($soap_lead->__getLastRequest());
			echo "<hr>";
			echo htmlentities($soap_lead->__getLastResponse());
			//echo "</pre>";
			echo "<br/>";
			if (!is_soap_fault($result)) {
				$result = json_decode(json_encode((array)$result), true);
				VarDumper::dump($result, 16, true);
			}
		} catch (Exception $e) {
			echo "<h2>Exception Error!</h2>";
			echo $e->getMessage();
		}


		/*
		$soap = new \SoapClient('https://aisws.ingos.ru/sales-test/SalesService.svc?wsdl',['trace'=>true]);

		$login_resp = $soap->Login([
			'User' => 'БУЛЛО СТРАХОВАНИЕ WS',
			'Password' => 'qk7p8cvj'
		]);
		if ($login_resp->ResponseStatus->ErrorCode==0 ){
			$session_token = $login_resp->ResponseData->SessionToken;


			$TariffParameters = (object)[];
			$TariffParameters->Agreement = (object)[];
			$TariffParameters->Agreement->PRODUCT = '13349216';
			$TariffParameters->Agreement->CURCODE = 'USD';
			$TariffParameters->Agreement->DATEBEG = \DateTime::createFromFormat('d.m.Y','20.10.2017')->format('Y-m-d');
			$TariffParameters->Agreement->DATEEND = \DateTime::createFromFormat('d.m.Y','22.10.2017')->format('Y-m-d');
			$TariffParameters->Agreement->TERRITORY_LIST = (object)[];
			$TariffParameters->Agreement->TERRITORY_LIST->TERRITORY = '5222628903';
			$TariffParameters->Agreement->COVER = (object)[];
			$TariffParameters->Agreement->COVER->MEDICAL = (object)[];
			$TariffParameters->Agreement->COVER->MEDICAL->LimitSum = '15000';
			$TariffParameters->Agreement->INSURED_LIST = (object)[];
			$TariffParameters->Agreement->INSURED_LIST->INSURED = (object)[
				'NAME' => 'TEST',
				'BIRTHDATE' => \DateTime::createFromFormat('d.m.Y','01.01.1980')->format('Y-m-d')
			];

			$params = [
				'SessionToken' => $session_token,
				'TariffParameters' => $TariffParameters
			];

			$resp = $soap->GetTariff((object)$params);

			//echo $soap->__getLastRequest();
			//Yii::$app->response->format = Response::FORMAT_XML;
			var_dump($resp);
		} else {
			var_dump($login_resp);
			die();
		}
		*/
    }

    /**
     * Заглавная страница
     * @return string
     */
    public function actionIndex()
    {
		$model = Page::find()->where(['slug'=>'travel-insurance-form', 'status'=>Page::STATUS_PUBLISHED])->one();
		if (!$model) {
			throw new NotFoundHttpException(Yii::t('frontend', 'Page not found'));
		}

		$viewFile = $model->view ?: 'view';
		if ($viewFile=='calc_new') $this->layout = 'new';
		return $this->render('../page/'.$viewFile, ['model'=>$model]);
        //return $this->render('../page/calc');
    }

    /**
     * @param $id
     *
     * @return string
     * @throws HttpException
     */
    public function actionCompany($id){
		$this->layout = 'new';

    	$model = Api::findOne(['id' => $id]);
    	if ($model) {
    		return $this->render('company', ['model' => $model]);
	    } else {
    		throw new HttpException(404, 'Карточка компании не найдена');
	    }
    }

    /**
     * Действие Партнерам
     */
    public function actionSendPartner() {
		$send = false;
		try {
			$model = new PartnerForm();
			if ($model->load(Yii::$app->request->post())) {
				if ($model->validate() && $model->send('evgeniya.khityaeva@gmail.com')) {
					$send = true;
				}
			}
		} catch (Throwable $t) {

		} catch (Exception $e) {

		}

		if ($send) {
			echo json_encode(['res'=>1, 'msg'=>\Yii::t('frontend', 'Thank you for contacting us. We will respond to you as soon as possible.')]);
		} else {
			echo json_encode(['res'=>0, 'msg'=>\Yii::t('frontend', 'There was an error sending email.')]);
		}

	}
}
