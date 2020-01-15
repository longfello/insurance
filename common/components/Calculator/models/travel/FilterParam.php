<?php

namespace common\components\Calculator\models\travel;

use common\models\Risk;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use Yii;

/**
 * This is the model class for table "filter_param".
 *
 * @property integer $id
 * @property string $name
 * @property string $type
 * @property integer $risk_id
 * @property string $variants
 * @property string $class
 * @property integer $sort_order
 * @property string $position
 * @property integer $change_desc
 *
 * @property FilterParamPrototype $handler
 * @property Risk $risk
 * @property FilterSolution2param[] $filterSolution2params
 * @property FilterSolution[] $filterSolutions
 */
class FilterParam extends \yii\db\ActiveRecord
{
    /**
     * Позиция вывода - блок дополнительных параметров
     */
    const POSITION_ADDITIONAL = 'additional';
    /**
     * Позиция вывода - блок расширенных параметров
     */
    const POSITION_EXTENDED   = 'extended';
    /**
     * Позиция вывода - блок основных параметров
     */
    const POSITION_MEDICAL    = 'medical';

    /**
     * @var FilterParamPrototype обработчик параметра фильтра
     */
    private $__handler;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'filter_param';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type', 'position'], 'string'],
            [['risk_id', 'sort_order', 'change_desc'], 'integer'],
            [['name', 'variants', 'class'], 'string', 'max' => 255],
            [['risk_id'], 'exist', 'skipOnError' => true, 'targetClass' => Risk::className(), 'targetAttribute' => ['risk_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'type' => 'Тип',
            'risk_id' => 'Риск',
            'variants' => 'Варианты',
            'sort_order' => 'Порядок сортировки',
            'class' => 'Класс обработчика',
            'position' => 'Позиция отображения параметра',
            'change_desc' => 'Влияет на информацию о рисках'
        ];
    }

    /**
     * Связанный страховой рисок
     * @return \yii\db\ActiveQuery|Risk
     */
    public function getRisk()
    {
        return $this->hasOne(Risk::className(), ['id' => 'risk_id']);
    }

    /**
     * Параметры, связанные с готовыми решениями
     * @return \yii\db\ActiveQuery|FilterSolution2param[]
     */
    public function getFilterSolution2params()
    {
        return $this->hasMany(FilterSolution2param::className(), ['param_id' => 'id']);
    }

	/**
     * Готовые решения
	 * @return \yii\db\ActiveQuery|FilterSolution[]
	 */
    public function getFilterSolutions()
    {
        return $this->hasMany(FilterSolution::className(), ['id' => 'filter_solution_id'])->viaTable('filter_solution2param', ['param_id' => 'id']);
    }

	/**
     * Обработчик параметра фильтра
	 * @return FilterParamPrototype|false
	 */
    public function getHandler(){
    	if (!$this->__handler){
		    if ($this->class && class_exists($this->class) && is_subclass_of($this->class, FilterParamPrototype::class)){
			    $className = $this->class;
			    $this->__handler = new $className(['param' => $this]);
		    } else {
			    $this->__handler = new FilterParamPrototype(['param' => $this]);
		    }
	    }
	    return $this->__handler;
    }

    /**
     * Комплексное получение описания параметра фильтра, связанного с ним риска
     * @return bool|string
     */
    public function getDescription(){
    	if ($this->risk && $this->risk->description) {
    		return $this->risk->description;
	    }
	    return false;
    }
}
