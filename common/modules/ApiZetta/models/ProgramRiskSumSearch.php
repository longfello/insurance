<?php

namespace common\modules\ApiZetta\models;

use yii\base\Model;
use yii\db\Query;
use yii\db\Expression;
use yii\data\ActiveDataProvider;

class ProgramRiskSumSearch extends Model {

    public $program;
    public $risk;
    public $sum;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['program', 'risk', 'sum'], 'string']
        ];
    }

    public function attributeLabels() {
        return array(
            'program' => 'Программа',
            'risk' => 'Риск',
            'sum' => 'Сумма страхования'
        );
    }

    public function search($params, $where = null) {
        $query = (new Query())
                ->select([
                    'azp.title program',
                    'azr.title risk',
                    new Expression('CONCAT(azs.sum, " ", azc.title) sum'),
                    'azpr.*',
                    'azps.sum_id'
                ])
                ->from('api_zetta_program azp')
                ->innerJoin('api_zetta_program_risk azpr', 'azp.id=azpr.program_id')
                ->innerJoin('api_zetta_risk azr', 'azpr.risk_id=azr.id')
                ->innerJoin('api_zetta_program_sum azps', 'azp.id=azps.program_id')
                ->innerJoin('api_zetta_sum azs', 'azps.sum_id=azs.id')
                ->innerJoin('api_zetta_currency azc', 'azs.currency_id=azc.id')
                ->orderBy([
                    'azpr.program_id' => SORT_ASC,
                    'azs.currency_id' => SORT_ASC,
                    'azps.sum_id' => SORT_ASC,
                    'azpr.risk_id' => SORT_ASC
                ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'azp.id' => $this->program,
            'azr.id' => $this->risk,
            'azs.id' => $this->sum
        ]);

        return $dataProvider;
    }

}
