<?php

namespace common\models;

use common\components\Calculator\forms\AccidentForm;
use common\components\Calculator\forms\BorrowerForm;
use common\components\Calculator\forms\EndowmentForm;
use common\components\Calculator\forms\InvestmentForm;
use common\components\Calculator\forms\MortgageForm;
use common\components\Calculator\forms\PropertyForm;
use common\components\Calculator\forms\TravelForm;
use common\components\MLModel;
use Yii;

/**
 * Типы страховок
 * This is the model class for table "insurance_type".
 *
 * @property integer $id
 * @property string $slug
 * @property string $name
 * @property string $description
 * @property integer $calc_page_id
 * @property integer $result_page_id
 * @property integer $program_page_id
 * @property integer $about_page_id
 * @property integer $landing_page_id
 * @property integer $sort_order
 * @property integer $enabled
 * @property integer $active
 *
 * @property Page $calcPage
 * @property Page $resultPage
 * @property Page $programPage
 * @property Page $aboutPage
 * @property Page $landingPage
 */
class InsuranceType extends MLModel
{
    /**
     * @inheritdoc
     */
    public $MLattributes = ['name', 'description'];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'insurance_type';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['slug', 'name', 'description'], 'required'],
            [['calc_page_id', 'result_page_id', 'program_page_id', 'about_page_id', 'landing_page_id', 'sort_order', 'enabled', 'active'], 'integer'],
            [['slug'], 'string', 'max' => 64],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1024],
            [['slug'], 'unique'],
            [['calc_page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::className(), 'targetAttribute' => ['calc_page_id' => 'id']],
            [['result_page_id'], 'exist', 'skipOnError' => true, 'targetClass' => Page::className(), 'targetAttribute' => ['result_page_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'slug' => 'Псевдоним',
            'name' => 'Название',
            'description' => 'Описание',
            'calc_page_id' => 'Связанная страница (главная форма)',
            'result_page_id' => 'Связанная страница (результаты, фильтр)',
            'program_page_id' => 'Связанная страница (о программе)',
            'about_page_id' => 'Связанная страница (как это работает)',
            'landing_page_id' => 'Связанная страница (лендинг)',
            'sort_order' => 'Порядок сортировки',
            'enabled' => 'Разрешен',
            'active' => 'Активен',
        ];
    }

    /**
     * Страница калькулятора
     * @return \yii\db\ActiveQuery
     */
    public function getCalcPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'calc_page_id']);
    }

    /**
     * Страница результатов
     * @return \yii\db\ActiveQuery
     */
    public function getResultPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'result_page_id']);
    }

    /**
     * Страница программы
     * @return \yii\db\ActiveQuery
     */
    public function getProgramPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'program_page_id']);
    }

    /**
     * Страница О типе страхования
     * @return \yii\db\ActiveQuery
     */
    public function getAboutPage()
    {
        return $this->hasOne(Page::className(), ['id' => 'about_page_id']);
    }

    /**
     * Страница Лендинг
     * @return \yii\db\ActiveQuery
     */
    public function getLandingPage()
    {
        return $this->hasOne(Landing::className(), ['id' => 'landing_page_id']);
    }

    /**
     * Возвращает html-код калькулятора
     * @return bool|mixed|string
     */
    public function getCalculator(){
	    $widget = false;

	    $className = '\common\components\Calculator\widgets\\'.$this->slug.'\CalculatorWidget';
	    if (class_exists($className)){
	    	$widget = call_user_func([$className, 'widget']);
	    }
	    if (!$widget){
	    	if ($this->active){
			    $widget = "<div class='alert alert-danger' style='margin-top:20px;'><h2>Undefined calculator for insurance type: {$this->name}</h2></div>";
		    }
	    }
	    return $widget;
    }
}
