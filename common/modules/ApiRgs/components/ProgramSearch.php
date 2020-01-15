<?php

namespace common\modules\ApiRgs\components;

use yii\base\Component;
use yii\base\Exception;
use yii\db\Query;

use common\components\ApiModule;
use common\components\Calculator\forms\TravelForm;
use common\components\Calculator\models\travel\FilterParam;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;

use common\models\ProgramResult;
use common\models\Risk;

use common\modules\ApiRgs\models\Sum;
use common\modules\ApiRgs\models\Sum2dict;
use common\modules\ApiRgs\models\Program;
use common\modules\ApiRgs\models\ProgramRisk;
use common\modules\ApiRgs\models\RiskType;
use common\modules\ApiRgs\models\AdditionalConditionTypeRisk;

class ProgramSearch extends Component {

    /** @var TravelForm Модель формы параметров */
    public $form;

    /** @var Module Модуль Апи */
    public $module;

    /** @var ActiveQuery Поисковый запрос */
    public $query;

    /** @var ActiveQuery Подзапрос страховой суммы */
    public $subquery = null;

    /** @var CostInterval Выбранная страховая сумма */
    public $cost_interval = null;

    /** @var array Массив медицинских рисков */
    public $risks = [];

    /** @var array Массив не медицинских рисков (Багаж, Отмена поездки, ГО, НС) */
    public $additionalRisks = [];

    /** @var array Массив дополнительных условий (Спорт, Хронические болезни, Беременность, Алкоголь) */
    public $additionalConditions = [];

    /**
     * Поиск программы страхования по заданным критериям
     *
     * @return ProgramResult|null
     */
    public function findAll() {
        try {
            $this->prepareQuery();
            $this->processMinSum();

            $all_risks = $this->getAllRisks();
            $skip_risks = $this->getSkipRisks();
            $additional_conditions = $this->getAdditionalConditions();

            foreach ($this->form->params as $param) {
                if (!$param->handler->checked) {
                    continue;
                }

                $this->processParam($param, $all_risks, $skip_risks, $additional_conditions);
            }

            $this->processRisks();

            $main = $this->query->one();
//echo $this->query->createCommand()->getRawSql(), PHP_EOL, PHP_EOL;
//exit;
            if (!empty($main)) {
                $programs = [
                    'main' => $main,
                    'additionalRisks' => [],
                    'additionalConditions' => []
                ];

                // remove min_sum condition in subquery because it will be used in next queries
                foreach ($this->subquery->where as $k => $v) {
                    if (is_array($v) && isset($v[1]) && $v[1] === 'ars.sum') {
                        unset($this->subquery->where[$k]);
                        break;
                    }
                }

                $this->processAdditionalRisks();
                if (!empty($this->additionalRisks)) {
                    foreach ($this->additionalRisks as $additionalRisk) {
                        $programs['additionalRisks'][] = $additionalRisk;
                    }
                }

                $this->additionalConditions = $this->processAdditionalConditions();
                if (!empty($this->additionalConditions)) {
                    $programs['additionalConditions'] = $this->additionalConditions;
                }

                if (in_array($this->form->scenario, [TravelForm::SCENARIO_PREPAY, TravelForm::SCENARIO_PAYER, TravelForm::SCENARIO_PAY])) {
                    return $this->adapt($programs, ApiModule::CALC_API);
                }

                return $this->adapt($programs);
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Поиск программы страхования
     * 
     * @return void
     */
    private function prepareQuery() {
        $this->subquery = Sum::find()->alias('ars')->select([
            'ars.id',
            'ars.ext_id',
            'ars.title',
            'MIN(ars.sum) sum',
            'ars.program_id',
            'ars.enabled',
            'ars.manual'
        ])->leftJoin([
            'ars2d' => Sum2dict::tableName()
        ], 'ars.id = ars2d.sum_id')->where([
            'ars.enabled' => 1
        ])->groupBy([
            'ars.program_id'
        ]);

        $this->query = Sum::find()->from([
            'ars' => $this->subquery
        ])->leftJoin([
            'arp' => Program::tableName()
        ], 'ars.program_id = arp.id')->leftJoin([
            'arrt' => RiskType::tableName()
        ], 'arp.risk_type_id = arrt.id')->where([
            'arp.enabled' => 1,
            'arrt.main' => 1
        ])->groupBy([
            'ars.program_id'
        ])->orderBy([
            'ars.program_id' => SORT_ASC,
            'ars.sum' => SORT_ASC
        ]);
    }

    /**
     * Корректировка минимальной суммы страхования
     * 
     * @return void
     */
    private function processMinSum() {
        $min_sum = $this->module->getCountries($this->form, true);

        $this->subquery->andWhere(['>=', 'ars.sum', $min_sum]);
    }

    /**
     * Получение возможных рисков (кроме дополнительных условий)
     * 
     * @return Array
     * @throws Exception
     */
    private function getAllRisks() {
        $all_risks = [
            'main' => [],
            'additional' => []
        ];

        $risks = (new Query())->select([
            'r.id',
            'arp.risk_type_id',
            'arrt.main'
        ])->from([
            'r' => Risk::tableName()
        ])->rightJoin([
            'arpr' => ProgramRisk::tableName()
        ], 'r.id = arpr.risk_id')->leftJoin([
            'arp' => Program::tableName()
        ], 'arpr.program_id = arp.id')->leftJoin([
            'arrt' => RiskType::tableName()
        ], 'arp.risk_type_id = arrt.id')->groupBy([
            'r.id'
        ])->all();

        if (empty($risks)) {
            throw new Exception('Risks not found');
        }

        foreach ($risks as $risk) {
            if ($risk['main']) {
                $all_risks['main'][] = $risk['id'];
            } else {
                if (!isset($all_risks['additional'][$risk['risk_type_id']])) {
                    $all_risks['additional'][$risk['risk_type_id']] = [];
                }

                $all_risks['additional'][$risk['risk_type_id']][] = $risk['id'];
            }
        }

        return $all_risks;
    }

    /**
     * Получение недоступных рисков
     * 
     * @return Array
     */
    private function getSkipRisks() {
        $risks = Risk::find()->alias('r')->select('r.id')->where([
            'not in',
            'r.id',
            ProgramRisk::find()->alias('arpr')->select(['arpr.risk_id'])->groupBy(['arpr.risk_id'])
        ])->andWhere([
            'not in',
            'r.id',
            AdditionalConditionTypeRisk::find()->alias('aractr')->select(['aractr.risk_id'])->groupBy(['aractr.risk_id'])
        ])->column();

        return $risks;
    }

    /**
     * Получение возможных дополнительных условий
     * 
     * @return Array
     */
    private function getAdditionalConditions() {
        return AdditionalConditionTypeRisk::find()->select('risk_id')->groupBy(['risk_id'])->column();
    }

    /**
     * Обработка параметров фильтра
     * 
     * @param FilterParam $param
     * @param Array $all_risks
     * @param Array $skip_risks
     * @param Array $additional_conditions
     * 
     * @return void
     * @throws Exception
     */
    public function processParam(FilterParam $param, $all_risks = [], $skip_risks = [], $additional_conditions = []) {
        if (in_array($param->risk_id, $skip_risks)) {
            throw new Exception('Risk doesn\'t supported');
        } else if (in_array($param->risk_id, $all_risks['main'])) {
            switch ($param->handler->slug) {
                case FilterParamPrototype::SLUG_SUM:
                    $this->processParamSum($param);
                break;
                default:
                    $this->risks[] = $param->risk_id;
                break;
            }
        } else if (in_array($param->risk_id, $additional_conditions)) {
            $this->additionalConditions[] = $param->risk_id;
        } else {
            $this->processAdditionalParam($param, $all_risks['additional']);
        }
    }

    /**
     * Обработка параметра суммы страхования
     * 
     * @param FilterParam $param
     * 
     * @return void
     */
    public function processParamSum(FilterParam $param) {
        if ($param && $param->handler && $param->handler->variant) {
            $this->cost_interval = $param->handler->variant;

            $this->subquery->andWhere(['ars2d.internal_id' => $this->cost_interval->id]);
        }
    }

    /**
     * Обработка параметров не медицинских рисков
     * 
     * @param FilterParam $param Параметр фильтра
     * @param Array $additional_risks Массив не медицинских рисков
     * 
     * @return void
     * @throws Exception
     */
    public function processAdditionalParam(FilterParam $param, $additional_risks = []) {
        switch ($param->handler->slug) {
            case FilterParamPrototype::SLUG_CANCEL:
                $this->processParamCancel($param);
            break;
            default:
                foreach ($additional_risks as $risk_type_id => $risks) {
                    if (in_array($param->risk_id, $risks)) {
                        if (!isset($this->additionalRisks[$risk_type_id])) {
                            $this->additionalRisks[$risk_type_id] = [];
                        }
                        $this->additionalRisks[$risk_type_id][] = $param->risk_id;
                        break;
                    }
                }
            break;
        }
    }

    /**
     * Обработка параметров отмены поездки
     * 
     * @todo Доработать дополнительные поля требующиеся для данного параметра
     * (Дата заключения / оплаты договора, кол-во багажных мест, паспортные данные)
     * 
     * @param FilterParam $param
     * 
     * @throws Exception
     */
    public function processParamCancel(FilterParam $param) {
        throw new Exception('Trip cancellation not implemented yet', 501);
    }

    /**
     * Обработка медицинских рисков
     * 
     * @return void
     */
    public function processRisks() {
        if (empty($this->risks)) {
            return;
        }

        $this->query->innerJoin(['arpr' => ProgramRisk::tableName()], 'ars.program_id = arpr.program_id')->andWhere(['arpr.risk_id' => $this->risks]);
        if (count($this->risks) > 1) {
            $this->query->having(['COUNT(DISTINCT arpr.risk_id)' => count($this->risks)]);
        }
    }

    /**
     * Обработка не медицинских рисков
     * 
     * @return void
     * @throws Exception
     */
    private function processAdditionalRisks() {
        if (empty($this->additionalRisks)) {
            return;
        }

        foreach ($this->additionalRisks as $riskTypeId => $risks) {
            $subquery = ProgramRisk::find()->alias('arpr')->leftJoin([
                'arp' => Program::tableName()
            ], 'arpr.program_id = arp.id')->where([
                'arp.enabled' => 1
            ])->groupBy([
                'arpr.program_id'
            ])->having([
                'arpr.risk_id' => $risks,
                'COUNT(arpr.program_id)' => count($risks)
            ]);

            $query = Sum::find()->from([
                'ars' => $this->subquery
            ])->rightJoin([
                'arpr' => $subquery
            ], 'ars.program_id = arpr.program_id')->groupBy([
                'ars.program_id'
            ])->orderBy([
                'ars.program_id' => SORT_ASC,
                'ars.sum' => SORT_ASC
            ]);
//echo $query->createCommand()->getRawSql();
//exit;
            $additionalProgram = $query->one();
            if (empty($additionalProgram)) {
                throw new Exception('Risks not found');
            }

            $this->additionalRisks[$riskTypeId] = $additionalProgram;
        }
    }

    /**
     * Обработка дополнительных условий
     * 
     * @return void|Array
     */
    private function processAdditionalConditions() {
        if (empty($this->additionalConditions)) {
            return;
        }

        return (new Query())->select([
            'TypeID' => 'aract.ext_id',
            'ID' => 'arac.ext_id'
        ])->from([
            'aract' => \common\modules\ApiRgs\models\AdditionalConditionType::tableName()
        ])->rightJoin([
            'arac' => \common\modules\ApiRgs\models\AdditionalCondition::tableName()
        ], 'aract.id = arac.additional_condition_type_id')->rightJoin([
            'aractr' => AdditionalConditionTypeRisk::tableName()
        ], 'aract.id = aractr.additional_condition_type_id')->where([
            'arac.default' => 1,
            'aractr.risk_id' => $this->additionalConditions
        ])->groupBy([
            'aract.id'
        ])->all();
    }

    /**
     * Адаптация результата поиска в стандартное представление
     * 
     * @param array $programs
     * @param string $calcType
     *
     * @return ProgramResult
     */
    public function adapt($programs, $calcType = ApiModule::CALC_LOCAL) {
        $product = $this->module->product;

        $model = new ProgramResult();
        $model->api_id = $this->module->model->id;
        $model->program_id = $programs;
        $model->rate_expert = $this->module->model->rate_expert;
        $model->rate_asn = $this->module->model->rate_asn;
        $model->thumbnail_url = $this->module->model->thumbnail_base_url . '/' . $this->module->model->thumbnail_path;
        $model->rule_url = $product->rule_base_url . '/' . $product->rule_path;
        $model->police_url = $product->police_base_url . '/' . $product->police_path;
        $model->risks = $programs['main']->getRisksAsArray($this->form);
        $model->actions = $this->module->model->actions;
        $model->cost = ($this->form->forceRemoteCalc) ? $this->module->calcPrice($programs, $this->form, $calcType) : 0;
        $model->phones = $this->module->model->getPhonesAsArray();
        $model->calc = $this->form;

        return $model;
    }

}
