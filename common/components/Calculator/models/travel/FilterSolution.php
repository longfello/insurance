<?php

namespace common\components\Calculator\models\travel;

use trntv\filekit\behaviors\UploadBehavior;
use common\models\GeoCountry;
use common\models\Api;
use Yii;

/**
 * This is the model class for table "filter_solution".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $thumbnail_base_url
 * @property string $thumbnail_path
 * @property integer $is_front
 * @property integer $is_api
 *
 * @property FilterSolution2country[] $filterSolution2countries
 * @property GeoCountry[] $countries
 * @property FilterSolution2param[] $filterSolution2params
 * @property FilterSolution2api[] $filterSolution2api
 * @property FilterParam[] $params
 * @property Api[] $api
 */
class FilterSolution extends \yii\db\ActiveRecord
{
    /**
     * Картинка
     * @var array
     */
    public $thumbnail;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'filter_solution';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['is_front', 'is_api'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['thumbnail', 'description'], 'safe']
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
            'thumbnail' => 'Картинка',
            'description' => 'Описание',
            'is_front' => 'Доступно на сайте',
            'is_api' => 'Доступно из api',
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
            ],
        ];
    }
    /**
     * Связь готового решения и стран
     * @return \yii\db\ActiveQuery
     */
    public function getFilterSolution2countries()
    {
        return $this->hasMany(FilterSolution2country::className(), ['filter_solution_id' => 'id']);
    }

    /**
     * Страны готового решения
     * @return \yii\db\ActiveQuery
     */
    public function getCountries()
    {
        return $this->hasMany(GeoCountry::className(), ['id' => 'country_id'])->viaTable('filter_solution2country', ['filter_solution_id' => 'id']);
    }

    /**
     * Связь готового решения и параметров фильтра
     * @return \yii\db\ActiveQuery
     */
    public function getFilterSolution2params()
    {
        return $this->hasMany(FilterSolution2param::className(), ['filter_solution_id' => 'id']);
    }

    /**
     * Связь готового решения и доступных api
     * @return \yii\db\ActiveQuery
     */
    public function getFilterSolution2api()
    {
        return $this->hasMany(FilterSolution2api::className(), ['filter_solution_id' => 'id']);
    }

    /**
     * Параметры фильтра готового решения
     * @return \yii\db\ActiveQuery
     */
    public function getParams()
    {
        return $this->hasMany(FilterParam::className(), ['id' => 'param_id'])->viaTable('filter_solution2param', ['filter_solution_id' => 'id']);
    }

    /**
     * Доступный api в готовом решении
     * @return \yii\db\ActiveQuery
     */
    public function getApi()
    {
        return $this->hasMany(Api::className(), ['id' => 'api_id'])->viaTable('filter_solution2api', ['filter_solution_id' => 'id']);
    }
}
