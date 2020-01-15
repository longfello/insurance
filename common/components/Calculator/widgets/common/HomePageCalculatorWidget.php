<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 20.02.17
 * Time: 17:03
 */
namespace common\components\Calculator\widgets\common;

use common\components\Calculator\forms\TravelForm;
use common\components\Calculator\widgets\travel\CalculatorWidget;
use common\models\InsuranceType;
use common\models\Page;
use yii\bootstrap\Widget;

/**
 * Class HomePageCalculatorWidget Виджет калькулятора главной страниці
 * @package common\components\Calculator\widgets\common
 */
class HomePageCalculatorWidget extends Widget {
    /**
     * Лайаут левого столбца
     */
    const LAYOUT_LEFTSIDE  = 'left';
    /**
     * Лайаут правого столбца
     */
    const LAYOUT_RIGHTSIDE = 'right';
	/**
	 * Лайаут лендинга
	 */
	const LAYOUT_LANDING = 'landing';
	/**
	 * Лайаут на всю ширину
	 */
	const LAYOUT_FULLWIDTH = 'full';

	/** @var null|Page страница отображения */
	public $page = null;

    /**
     * @var string Лайаут для отображения
     */
	public $layout;

	/** @var null|InsuranceType Тип страхования */
	public $type;

	/** @var InsuranceType[] доступные типы страхования */
	public $availableTypes = [];

    /**
     * Инициализация свойств
     */
    public function init(){
		parent::init();

		foreach (InsuranceType::find()->orderBy(['sort_order' => SORT_ASC])->where(['enabled' => 1])->all() as $one){
			/** @var $one InsuranceType */
			$this->availableTypes[$one->slug] = $one;
			if ($this->page){
				if ($one->calc_page_id == $this->page->id || $one->landing_page_id == $this->page->id)
				{
					$this->type = $one;
				}
			}
		}
	}

    /**
     * @inheritdoc
     */
	public function run(){
		$widget = false;

		if ($this->type){
			if ($this->layout == self::LAYOUT_RIGHTSIDE) {
				$widget = $this->type->getCalculator();
			}
		}

		return $this->render('home-page-calculator/'.$this->layout, [
			'page'   => $this->page,
			'type'   => $this->type,
			'widget' => $widget,
			'availableTypes' => $this->availableTypes,
		]);
	}
}