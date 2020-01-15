<?php

namespace common\modules\ApiZetta\components;

use yii\db\Query;
use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\base\Exception;

use common\models\Risk;
use common\models\ProgramResult;

use common\components\ApiModule;
use common\components\Calculator\forms\TravelForm;
use common\components\Calculator\filters\params\travel\FilterParamPrototype;
use common\components\Calculator\models\travel\FilterParam;

use common\modules\ApiZetta\models\Country;
use common\modules\ApiZetta\models\Currency;
use common\modules\ApiZetta\models\Program;
use common\modules\ApiZetta\models\Sum;
use common\modules\ApiZetta\models\Sum2dict;
use common\modules\ApiZetta\models\ProgramSum;
use common\modules\ApiZetta\models\ProgramRisk;
use common\modules\ApiZetta\models\Risk2dict;

class ProgramSearch extends Component {

    /** @var TravelForm Модель формы параметров */
    public $form;

    /** @var Module Модуль Апи */
    public $module;

    /** @var ActiveQuery Поисковый запрос */
    public $query;

    /** @var array $countries Страны */
    public $countries = [];

    /** @var Country|null Территория */
    public $territory = null;

    /** @var Currency Валюта */
    public $currency = null;

    /** @var Array Программа страхования */
    public $program = null;

    /** @var Array Риски */
    public $risks = [];

    /**
     * Поиск программы страхования по заданным критериям
     *
     * @return ProgramResult|null
     */
    public function findAll() {
        try {
            list($this->countries, $this->territory) = $this->module->getCountries($this->form);
            $this->processCurrency();
            $this->processProgram();
            foreach ($this->form->params as $param) {
                if ($param->handler->checked && !in_array($param->handler->slug, [/*FilterParamPrototype::SLUG_CANCEL, */FilterParamPrototype::SLUG_ACCIDENT, FilterParamPrototype::SLUG_SPORT])) {
                    $this->processParam($param);
                }
            }
            $this->processRisks();

            $this->program = $this->query->one();
            if (!empty($this->program)) {
                if (in_array($this->form->scenario, [TravelForm::SCENARIO_PREPAY, TravelForm::SCENARIO_PAYER, TravelForm::SCENARIO_PAY])) {
                    return $this->adapt($this->program, ApiModule::CALC_API);
                }

                return $this->adapt($this->program);
            }

            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Обработка валюты
     *
     * @return void
     * @throws Exception
     */
    private function processCurrency() {
        $countries = ArrayHelper::getColumn($this->countries, 'id');
        if (!empty($this->territory)) {
            $countries[] = $this->territory->id;
        }

        $this->currency = Currency::find()
                ->alias('azcr')
                ->innerJoin('api_zetta_country_currency azcc', 'azcr.id = azcc.currency_id')
                ->innerJoin('api_zetta_country azcn', 'azcn.id = azcc.country_id')
                ->where([
                    'azcn.id' => $countries,
                    'azcr.enabled' => 1
                ])
                ->groupBy(['azcr.id'])
                ->orderBy(['azcr.id' => SORT_ASC])
                ->limit(1)
                ->one();

        if (empty($this->currency)) {
            throw new Exception('Не найдено подходящей валюты');
        }
    }

    /**
     * Поиск программы страхования
     * 
     * @return void
     */
    private function processProgram() {
        $this->query = (new Query())
                ->from(['azp' => Program::tableName()])
                ->where([
                    'azp.enabled' => 1
                ])
                ->groupBy(['azp.id'])
                ->orderBy([
                    'azp.priority' => SORT_ASC,
                    'azp.id' => SORT_ASC
                ]);
    }

//    /**
//     * Поиск доступных программ через Api
//     * 
//     * @return void
//     */
//    private function processProgramWithApi() {
//        $data = $this->data;
//        $data['Durability'] = $data['Duration'];
//        unset($data['Duration']);
//
//        $result = $this->module->apiRequest('TravelV3.GetProgramList', $data);
//        if ($result['success'] && !empty($result['data'])) {
//            $this->query = (new \yii\db\Query())
//                    ->from(['azp' => Program::tableName()])
//                    ->where([
//                        'azp.ext_id' => ArrayHelper::getColumn($result['data'], 'ID'),
//                        'azp.enabled' => 1
//                    ])
//                    ->groupBy(['azp.id'])
//                    ->orderBy([
//                        'azp.priority' => SORT_ASC,
//                        'azp.id' => SORT_ASC
//                    ]);
//        }
//    }

    /**
     * Обработка параметров фильтра
     * 
     * @param FilterParam $param
     * 
     * @return void
     */
    private function processParam(FilterParam $param) {
        switch ($param->handler->slug) {
            case FilterParamPrototype::SLUG_SUM:
                $this->processParamSum($param);
            break;
            case FilterParamPrototype::SLUG_CANCEL:
                $this->processParamCancel($param);
            break;
            default:
                $this->processParamNormal($param);
            break;
        }
    }

    /**
     * Обработка параметра суммы
     * 
     * @param FilterParam $param
     * 
     * @return void
     */
    private function processParamSum(FilterParam $param) {
        if ($param && $param->handler && $param->handler->variant) {
            $this->query->innerJoin(['azps' => ProgramSum::tableName()], 'azp.id = azps.program_id')
                    ->innerJoin(['azs' => Sum::tableName()], 'azps.sum_id = azs.id')
                    ->innerJoin(['azs2d' => Sum2dict::tableName()], 'azs.id = azs2d.sum_id')
                    ->andWhere([
                        'azs.currency_id' => $this->currency->id,
                        'azs.enabled' => 1,
                        'azs2d.internal_id' => $param->handler->variant->id
                    ])
                    ->addOrderBy(['azs.sum' => SORT_ASC]);
        }
    }

    /**
     * Обработка параметров отмены поездки
     * @todo Get min and max sums from api
     * 
     * @param FilterParam $param
     * 
     * @return void
     */
    public function processParamCancel(FilterParam $param) {
        $min = 1;
        $max = 3000;
        $amount = $param->handler->variant['amount'];
        $sicklist = (bool) $param->handler->variant['sick-list'];

        if (!$amount || $amount < $min || $amount > $max || $sicklist) {
            $this->query->andWhere('0 = 1');
        } else {
            $this->processParamNormal($param);
        }
    }

    /**
     * Обработка ординарных параметров соответствия рискам
     * 
     * @param FilterParam $param
     * 
     * @return void
     */
    public function processParamNormal(FilterParam $param) {
        $this->risks[$param->risk_id] = $param->name;
    }

    /**
     * Обработка рисков
     * 
     * @return void
     */
    private function processRisks() {
        if ($this->query && !empty($this->risks)) {
            $this->query->innerJoin(['azpr' => ProgramRisk::tableName()], 'azp.id = azpr.program_id')
                    ->innerJoin(['azr2d' => Risk2dict::tableName()], 'azpr.risk_id = azr2d.risk_id')
                    ->innerJoin(['r' => Risk::tableName()], 'azr2d.internal_id = r.id')
                    ->andWhere(['r.id' => array_keys($this->risks)]);

            if (count($this->risks) > 1) {
                $this->query->having(['COUNT(DISTINCT r.id)' => count($this->risks)]);
            }
        }
    }

    /**
     * Адаптация результата поиска в стандартное представление
     * 
     * @param array $program
     * @param string $calcType
     *
     * @return ProgramResult
     */
    public function adapt($program, $calcType = ApiModule::CALC_LOCAL) {
        $model = new ProgramResult();
        $product = $this->module->product;
        $program_sum = $this->module->getProgram([
            'program_id' => $this->program['program_id'],
            'sum_id' => $this->program['sum_id']
        ]);
        $model->api_id = $this->module->model->id;
        $model->program_id = [
            'program_id' => $this->program['program_id'],
            'sum_id' => $this->program['sum_id']
        ];
        $model->rate_expert = $this->module->model->rate_expert;
        $model->rate_asn = $this->module->model->rate_asn;
        $model->thumbnail_url = $this->module->model->thumbnail_base_url . '/' . $this->module->model->thumbnail_path;
        $model->rule_url = $product->rule_base_url . '/' . $product->rule_path;
        $model->police_url = $product->police_base_url . '/' . $product->police_path;
        $model->risks = $program_sum->getRisksAsArray($this->form);
        $model->actions = $this->module->model->actions;
        $model->cost = ($this->form->forceRemoteCalc) ? $this->module->calcPrice($program, $this->form, $calcType) : 0;
        $model->phones = $this->module->model->getPhonesAsArray();
        $model->calc = $this->form;
        return $model;
    }

}
