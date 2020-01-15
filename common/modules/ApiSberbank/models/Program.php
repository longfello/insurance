<?php

namespace common\modules\ApiSberbank\models;

use common\models\Risk;
use common\models\CostInterval;
use common\components\Calculator\forms\TravelForm;
use Yii;
use trntv\filekit\behaviors\UploadBehavior;

/**
 * Страховые программы
 * This is the model class for table "api_sberbank_program".
 *
 * @property int $id Id 
 * @property string $insProgram
 * @property string $name
 * @property string $rule_base_url
 * @property string $rule_path
 * @property string $police_base_url
 * @property string $police_path
 * @property int $cost_interval_id
 *
 * @property Program2Risk[] $Program2risks
 * @property Risk[] $risks
 * @property CostInterval $costinterval
 */
class Program extends \yii\db\ActiveRecord
{
    /**
     * @var array файл правил
     */
    public $rule;
    /**
     * @var array файл примера полиса
     */
    public $police;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_sberbank_program';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['insProgram', 'name'], 'required'],
            [['insProgram', 'name'], 'string', 'max' => 255],
            [['cost_interval_id'], 'exist', 'skipOnError' => true, 'targetClass' => CostInterval::className(), 'targetAttribute' => ['cost_interval_id' => 'id']],
            [['rule', 'police'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'insProgram' => 'Программа',
            'name' => 'Название',
            'rule' => Yii::t('backend', 'Правила страхования'),
            'police' => Yii::t('backend', 'Образец полиса'),
            'cost_interval_id' => 'Страховая сумма'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProgram2risks()
    {
        return $this->hasMany(Program2Risk::className(), ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRisks()
    {
        return $this->hasMany(Risk::className(), ['id' => 'risk_id'])->viaTable('api_sberbank_program2risk', ['program_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCostInterval()
    {
        return $this->hasOne(CostInterval::className(), ['id' => 'cost_interval_id']);
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'rule',
                'pathAttribute' => 'rule_path',
                'baseUrlAttribute' => 'rule_base_url'
            ],
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'police',
                'pathAttribute' => 'police_path',
                'baseUrlAttribute' => 'police_base_url'
            ]
        ];
    }

    /**
     * риски в виде массива
     * @param TravelForm $form
     *
     * @return array
     */
    public function getRisksAsArray(TravelForm $form) {
        $res = [];
        $filter_risks = [];
        foreach($form->params as $param) {
            $filter_risks[$param->risk_id] = $param->handler->checked;
        }

        foreach ($this->program2risks as $one){
            /** @var $risk Risk */
            $description = ($one->name!='')?$one->name:$one->risk->name;
            if($one->is_optional==0 || (isset($filter_risks[$one->risk->id]) && $filter_risks[$one->risk->id])) $res[$description] = intval($one->summa);
        }
        return $res;
    }
}
