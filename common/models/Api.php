<?php

namespace common\models;

use common\components\ApiModule;
use common\components\Calculator\forms\TravelForm;
use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\base\Module;
use yii\helpers\Html;
use yii\httpclient\Exception;

/**
 * API страховых компаний
 * This is the model class for table "api".
 *
 * @property integer $id
 * @property integer $local_calc
 * @property string $name
 * @property string $class
 * @property string $rate_expert
 * @property string $rate_asn
 * @property integer $enabled
 * @property string $thumbnail_base_url
 * @property string $thumbnail_path
 * @property string $actions
 * @property string $description
 * @property string $service_center_url
 *
 * @property ApiPhone[] $phones
 * @property ApiFiles[] $files
 */
class Api extends \yii\db\ActiveRecord
{
	/**
     * Логотип
	 * @var array
	 */
	public $thumbnail;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'class', 'rate_expert', 'rate_asn'], 'required'],
            [['id', 'enabled', 'local_calc'], 'integer'],
            [['name', 'class', 'service_center_url'], 'string', 'max' => 255],
            [['rate_expert', 'rate_asn'], 'string', 'max' => 50],
	        [['thumbnail_base_url', 'thumbnail_path'], 'string', 'max' => 1024],
	        [['actions', 'description'], 'string'],
	        [['thumbnail'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'local_calc' => 'Локальный расчет стоимости полиса 0/1',
            'name' => 'Название',
            'class' => 'Класс модуля',
            'rate_expert' => 'Рейтинг эксперта',
            'rate_asn' => 'Рейтинг АСН',
            'enabled' => 'Разрешено',
            'thumbnail' => 'Логотип',
            'actions' => 'Действия при страховом случае',
            'description' => 'Описание',
            'service_center_url' => 'Код ссылки на сервисный центр',
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
				'attribute' => 'thumbnail',
				'pathAttribute' => 'thumbnail_path',
				'baseUrlAttribute' => 'thumbnail_base_url'
			]
		];
	}

    /**
     * Возвращает массив для генерации административного меню
     * @return array
     */
    public static function getAdminMenu(){
		$menu = [
			'label'=>Yii::t('backend', 'Внешние API'),
			'icon'=>'<i class="fa fa-address-book"></i>',
			'url' => '#',
			'items'=>[]
		];
		$models = Api::find()->where(['enabled' => 1])->orderBy(['name' => SORT_ASC])->all();
		foreach ($models as $model){
			$className = $model->class;
			if (class_exists($className) && method_exists($className, 'getAdminMenu')){
				$menu['items'][] = call_user_func(array($className, 'getAdminMenu'));
			}
		}
		return $menu;
	}

	/**
     * Телефоны
	 * @return \yii\db\ActiveQuery
	 */
	public function getPhones()
	{
		return $this->hasMany(ApiPhone::className(), ['api_id' => 'id']);
	}

	/**
     * Файлы
	 * @return \yii\db\ActiveQuery
	 */
	public function getFiles()
	{
		return $this->hasMany(ApiFiles::className(), ['api_id' => 'id']);
	}

	/**
     * Возвращет класс модуля API
	 * @return ApiModule
	 * @throws Exception
	 */
	public function getModule(){
	    $className = $this->class;
	    if (class_exists($className)){
		    return new $className($className);
	    }
		throw new Exception("Api module {$this->name} not implemented", 501);
    }

    /**
     * Поиск программ по заданным критериям
     * @param TravelForm $form Критерии поиска
     *
     * @return ProgramResult|null
     * @throws Exception
     */
    public function search(TravelForm $form, $order_information = null){
    	$module = $this->getModule();
    	if ($module){
    	    $result = $module->search($form);
    	    if ($result && $order_information){
    	        /** @var $result ProgramResult */
                $order = new Orders();
                $order->api_id = $result->api_id;
                $order->price = $result->cost;
                $order->currency_id = Currency::findOne(['char_code' => Currency::RUR])->id;
                $order->status = Orders::STATUS_CALC;
                $order->holder_id = null;
                $order->info = $order_information;
                $order->calc_form = $form;
                $order->program = $result;

                if ($order_information && isset($order_information['user_id'])){
                    $order->user_id = $order_information['user_id'];
                }

                if (!$order->save()) {
                    throw new Exception(strip_tags(Html::errorSummary($order)), 500);
                }
                $result->order_id = $order->id;
            }
		    return $result;
	    }
	    throw new Exception("Method search not implemented in api module {$this->name}", 501);
    }

    /**
     * Возвращает номера телефонов в виде массива, где ключ - название, значение - телефон
     * @return string[]
     */
    public function getPhonesAsArray(){
    	$res = [];

    	foreach($this->phones as $phone){
    		$res[$phone->name] = $phone->phone;
	    }

	    return $res;
    }
}
