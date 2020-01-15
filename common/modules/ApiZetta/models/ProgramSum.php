<?php

namespace common\modules\ApiZetta\models;

use Yii;

use yii\db\Query;
use yii\helpers\ArrayHelper;
use common\components\Calculator\forms\TravelForm;
use common\modules\ApiZetta\models\ProgramRiskSum;

/**
 * Соответствие сумм страхования программам
 * This is the model class for table "api_zetta_program_sum".
 *
 * @property int $program_id
 * @property int $sum_id
 */
class ProgramSum extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'api_zetta_program_sum';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['program_id', 'sum_id'], 'required'],
            [['program_id', 'sum_id'], 'integer'],
            [['program_id'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['program_id' => 'id']],
            [['sum_id'], 'exist', 'skipOnError' => true, 'targetClass' => Sum::className(), 'targetAttribute' => ['sum_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'program_id' => Yii::t('backend', 'ID программы страхования'),
            'sum_id' => Yii::t('backend', 'ID страховой суммы')
        ];
    }

    /**
     * Программа страхования
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getProgramModel() {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }

    /**
     * Сумма страхования
     * 
     * @return \yii\db\ActiveQuery
     */
    public function getSumModel() {
        return $this->hasOne(Sum::className(), ['id' => 'sum_id']);
    }

    /**
     * Риски в виде массива
     * @param TravelForm $form
     *
     * @return array
     */
    public function getRisksAsArray(TravelForm $form) {
        $res = ArrayHelper::merge([
            'Медицинские расходы' => $this->sumModel->sum
        ], ArrayHelper::map((new Query())->select([
            'r.name',
            'azprs.sum'
        ])->from([
            'azprs' => 'api_zetta_program_risk_sum'
        ])
        ->innerJoin('api_zetta_program azp', 'azprs.program_id = azp.id')
        ->innerJoin('api_zetta_sum azs', 'azprs.sum_id = azs.id')
        ->innerJoin('api_zetta_risk azr', 'azprs.risk_id = azr.id')
        ->innerJoin('api_zetta_risk2dict azr2d', 'azr.id = azr2d.risk_id')
        ->innerjoin('risk r', 'azr2d.internal_id = r.id')
        ->where([
            'azprs.program_id' => $this->program_id,
            'azprs.sum_id' => $this->sum_id
        ])
        ->orderBy([
            'r.sort_order' => SORT_ASC
        ])
        ->all(), 'name', 'sum'));

        return $res;
    }

}
