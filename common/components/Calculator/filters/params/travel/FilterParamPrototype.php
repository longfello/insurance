<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 10:11
 */

namespace common\components\Calculator\filters\params\travel;


use common\models\CostInterval;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\forms\TravelForm;
use trntv\aceeditor\AceEditor;
use yii\base\Component;
use yii\bootstrap\Html;
use yii\widgets\ActiveForm;

/**
 * Class FilterParamPrototype
 * @package common\modules\filter\components
 *
 * @property $isVariable bool
 */
class FilterParamPrototype extends Component {
    /**
     * Процесс обычной обработки параметра
     */
    const SLUG_NORMAL = 'normal';
    /**
     * Обработка параметра беременности
     */
    const SLUG_PREGNANCY = 'pregnancy';
    /**
     * Обработка параметра суммы
     */
    const SLUG_SUM = 'sum';
    /**
     * Обработка параметра отмены поездки
     */
    const SLUG_CANCEL = 'cancel';
    /**
     * Обработка параметра франшизы
     */
    const SLUG_NOTFRANCHISE = 'not_franchise';
    /**
     * Обработка параметра репатриации
     */
    const SLUG_REPATRIATION = 'repatriation';
    /**
     * Обработка параметра Поисково-спасательные работы
     */
    const SLUG_SEARCH = 'search';
    /**
     * Обработка параметра Спорт
     */
    const SLUG_SPORT = 'sport';
    /**
     * Обработка параметра несчастного случая
     */
    const SLUG_ACCIDENT = 'accident';
    /**
     * Обработка параметра страхования багажа
     */
    const SLUG_LUGGAGE = 'luggage';
    /**
     * Обработка параметра Гражданская ответственность
     */
    const SLUG_CIVIL = 'civil';
    /**
     * Идентификатор простого параметра
     */
    const PARAM_SLUG_SIMPLE = 'variant-data';
    /**
     * @var string Тип обрабатываемого параметра
     */
    public $slug = self::SLUG_NORMAL;

	/**
	 * @var FilterParam Текущие параметры фильтра
	 */
	public $param;

	/**
	 * @var bool Выбран ли текущий параметр
	 */
	public $checked = false;

	/** @var int|string|CostInterval|null  Вариант параметра */
	public $variant;

    /**
     * @var string[] Описание доступных переменных вариантов риска
     */
    public $availableVariantVariables = [];

	/**
	 * @return string Возвращает виджет редактора вариантов текущего параметра
	 */
	public function getVariantsEditor(ActiveForm $form){
		return $form->field($this->param, 'variants')->widget(
			AceEditor::className(),
			[
				'mode' => 'json'
			]
		);
	}

    /**
     * Рендер параметра формы фильтра
     * @param $form  \kartik\form\ActiveForm Форма html
     * @param $model TravelForm Модель формы
     *
     * @return string
     */public function render($form, $model){
		/** @var $form \kartik\form\ActiveForm */
		/** @var $model TravelForm */
		return "
          <div class=\"checkbox-list__item\">
            <label class=\"checkbox\">
              ". Html::checkbox('param-'.$this->param->id, $this->checked, ['class' => 'checkbox__input']) ."
              <span class=\"checkbox__icon\"></span><span class=\"checkbox__label\">". $this->param->name ."</span>
              <div class=\"filter-param-helper\">
		          
              </div>
            </label>
            <div class=\"helper__answer\"><div class=\"helper__icon\">?</div></div>
		        <div class=\"helper__text\">". $this->param->getDescription() ."</div>
          </div>
";
	}

    /**
     * Допускает ли параметр выбор вариантов
     * @return bool
     */
    public function getIsVariable(){
		return (bool)$this->param->variants;
	}

    /**
     * Загрузка состояния (обычно из параметров запроса)
     * @param array $params
     */
    public function load($params = []){
		if (isset($params[$this->param->id]) || \Yii::$app->request->post('param-'.$this->param->id, null)){
			$this->checked = true;
			$this->setVariant($this->loadVariant($params));
		}
	}

    /**
     * Загрузка состояния варианта
     * @param array $params
     *
     * @return array|mixed
     */
    public function loadVariant($params = []){
		return (isset($params[$this->param->id]))?$params[$this->param->id]:\Yii::$app->request->post('param-'.$this->param->id.'-variant', null);
	}

    /**
     * Геттер варианта
     * @return CostInterval|int|string
     */
    public function getVariantValue(){
		return $this->variant;
	}

    /**
     * Сеттер варианта
     * @param $value
     *
     * @return bool
     */
    public function setVariant($value){
		if ($this->isVariable){
			$this->variant = $value;
			return true;
		}
		return false;
	}

	protected function encode($data){
        return base64_encode(serialize($data));
    }

	protected function decode($data){
        return unserialize(base64_decode($data));
    }
}