<?php
/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 17.10.17
 * Time: 16:46
 */

namespace api\components\Rest;


use api\components\ErrorCode;
use api\components\Response;
use common\models\ApiLog;
use common\models\Orders;
use yii\base\Model;
use \Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\web\HttpException;

/**
 * Class RestMethod - prototype for rest methods
 *
 * #API BulloSafe <small>версия {%api_version}</small>
 *
 * История изменений:
 *
 * Версия | Дата изменения | Комментарий
 * --- | --- | ---
 * 0.1 | 27.11.2017 | Начальная версия
 * {%api_version} | {%api_date} | Текущая версия
 *
 * ## Общее
 * Актуальная документация доступна по адресу {%api_url}
 *
 * Аутентификация пользователя производится согласно HTTP BASIC AUTH. С каждым запросом должны быть переданы имя пользователя и пароль.
 *
 * Возможные форматы ответа регламентируются GET-параметром запроса format. Доступны следующие форматы:
 *
 * Формат | Параметр запроса | Пример URL |Комментарий
 * --- | --- | ---
 *  JSON |  format=json | {%api_url}get/currency?format=json <br> {%api_url}get/currency | Формат по-умолчанию
 *  XML | format=xml | {%api_url}get/currency?format=xml | &nbsp;
 *
 * Базовый URL запросов: {%api_url}
 *
 * ## Методы API
 *
 * @package api\components\Rest
 *
 */

class RestMethod extends Model
{
    /**
     * Base api request component
     * @var \api\components\Rest\RestComponent
     */
    public $rest;

    /**
     * @var ApiLog Log component
     */
    public $log;

    /**
     * Decoded request POST data
     * @var mixed
     */
    public $data;

    /**
     * Порядок сортировки при извлечении документации
     */
    public $sort_order = 0;

    /**
     * Доступные для метода типы аутентификации
     */
    public $accessEnabledBy = [ RestComponent::AUTH_BASIC, RestComponent::AUTH_CALC_TOKEN ];

    /**
     * Публиковать ли документацию
     */
    public $shareDocumentation = true;

    /**
     * Model initialization - setup base rest api component, create log record
     * @throws \Exception
     */
    public function init(){
        $this->rest = Yii::$app->rest;
        $this->log  = new ApiLog();
        $this->log->user_id = $this->rest->user?$this->rest->user->id:null;
        $this->log->save();

        parent::init();
    }

    /**
     * Run api method
     * @return null|mixed Method response or null if error occurred
     * @throws \yii\db\Exception
     * @throws \yii\web\HttpException
     */
    public function run(){
        if (!in_array(Yii::$app->rest->authedBy, $this->accessEnabledBy)){
            Yii::$app->response->throwError(ErrorCode::AUTH_DENIED, 'Доступ к данному методу ограничен при данном виде аутентификации.');
        }

        $response = null;
        try{
            $this->initData();
        } catch (\Throwable $e){
            Yii::$app->response->throwError(ErrorCode::PARSE_DATA, $this->complieErrorMessage($e, ErrorCode::getMessage(ErrorCode::PARSE_DATA)));
        }

        if ($this->validate()) {
            $transaction = \Yii::$app->db->beginTransaction();
            try{
                $response = $this->save();
                if (!$response && !is_array($response)) {
                    Yii::$app->response->throwError(ErrorCode::UNDEFINED_ERROR);
                }
            } catch (\Throwable $e){
                $transaction->rollBack();
                Yii::$app->response->throwError(ErrorCode::SAVE_DATA, $this->complieErrorMessage($e, ErrorCode::getMessage(ErrorCode::SAVE_DATA)));
            }
            if ($this->hasErrors()){
                $transaction->rollBack();

                $errors = strip_tags(Html::errorSummary($this));
                Yii::$app->response->throwError(ErrorCode::SAVE_DATA, "Ошибки: $errors");
            } else {
                $transaction->commit();
                $this->log->save();
            }
        } else {
            $errors = strip_tags(Html::errorSummary($this));
            Yii::$app->response->throwError(ErrorCode::VALIDATE_DATA, "Ошибки: $errors");
        }
        return $response;
    }

    /**
     * Validate attribute as ActiveRecord model
     * @param string $attribute Model attribute
     * @param array $params Attribute rules parameters
     */
    public function validateModel($attribute, $params = []){
        $model = $this->$attribute;
        if (is_array($model)){
            foreach ($model as $one){
                /** @var $model ActiveRecord */
                if (!$one->validate()) {
                    $this->addErrors($one->getErrors());
                }
            }
        } else {
            /** @var $model ActiveRecord */
            if (!$model->validate()) {
                $this->addErrors($model->getErrors());
            }
        }
    }

    /**
     * Validate attribute as ActiveRecord model
     * @param string $attribute Model attribute
     * @param array $params Attribute rules parameters
     */
    public function validateOrderOwner($attribute, $params = []){
        $model = $this->$attribute;
        /** @var $model Orders */
        if (!$model->info || !isset($model->info['user_id']) || ($model->info['user_id'] !== $this->rest->user->id))  {
            Yii::$app->response->throwError(ErrorCode::AUTH_DENIED, "Запрошенный заказ не принадлежит текущему пользователю.");
        }
    }

    /**
     * Validate required fields
     * @param string $attribute Model attribute
     * @param array $params Attribute rules parameters
     *
     * @throws \yii\web\HttpException Fired on not exists required attributes
     */
    public function validateRequired($attribute, $params){
        $code    = $this->getParam($params, 'code', ErrorCode::VALIDATE_DATA);
        $message = $this->getParam($params, 'message', "Отсутствует обязательный параметр: ".$attribute);
        if(!$this->$attribute) Yii::$app->response->throwError($code, $message);
    }


    /**
     * Service function to get rule parameter
     * @param array $from Params array
     * @param string $slug Name of parameter
     * @param mixed $default default value if not exists
     *
     * @return mixed Parameter value or defaut value if not exists
     */
    protected function getParam($from, $slug, $default){
        return isset($from[$slug])?$from[$slug]:$default;
    }

    /**
     * Base api method function - must be redefined on child classes
     * @throws HttpException
     */
    protected function save(){
        Yii::$app->response->throwError(500, "Метод еще не готов");
    }

    /**
     * Read and decode request data into data attribute
     * @throws \yii\web\HttpException
     */
    protected function initData()
    {
        $data = Yii::$app->request->post("data", false);
        if ($data){
            switch (Yii::$app->response->format){
                case Response::FORMAT_JSON:
                    $this->data = json_decode($data, true);
                    break;
/*                case Response::FORMAT_SERIALIZED:
                    $this->data = unserialize($data);
                    break;*/
                case Response::FORMAT_XML:
                    libxml_disable_entity_loader(true);
                    libxml_use_internal_errors(true);
                    $xml = str_replace("\n", "", $data);
                    $xml = SimpleXML_Load_String($xml);

                    if ( ! $xml) {
                        $xmlstr = explode("\n", $xml);

                        $errors = libxml_get_errors();

                        $message = '';
                        foreach ($errors as $xml_error) {
                            $message .= $this->display_xml_error($xml_error, $xmlstr);
                        }
                        libxml_clear_errors();
                        Yii::$app->response->throwError(ErrorCode::PARSE_DATA, $message);
                    }

                    $this->data = $xml;
                    break;
                default:
                    $this->data = json_decode($data);
                    break;
            }
        }
        if (is_array($this->data)){
            $this->load($this->data, '');
        }
    }

    /**
     * Format XML error messages
     * @param \LibXMLError $error Error object
     * @param string $xml XML text
     *
     * @return string Formatted error message
     */
    protected function display_xml_error($error, $xml)
    {
        $return  = $xml[$error->line - 1] . "\n";
        $return .= str_repeat('-', $error->column) . "^\n";

        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
                break;
        }

        $return .= trim($error->message) .
                   "\n  Line: $error->line" .
                   "\n  Column: $error->column";

        if ($error->file) {
            $return .= "\n  File: $error->file";
        }

        return "$return\n\n----------------\n\n";
    }

    /**
     * Format common error message
     * @param \Exception $e Error object
     *
     * @return string formatted error message
     */
    protected function complieErrorMessage($e, $defaultMessage = null){
        /** @var $e \Exception*/
        return $this->rest->debug ? $e->getMessage(). " in ".$e->getFile()." (".$e->getLine().") ".$e->getTraceAsString()
                                  : ($defaultMessage?$defaultMessage:$e->getMessage());
    }

    /**
     * Filter ActiveRecord[] data for response
     * @param ActiveRecord[] $models Source information array
     * @param mixed[] $fields Field rules description
     *
     * @return mixed[] Targed data for response
     */
    protected function filterFields($models, $fields){
        $return = [];
        foreach ($models as $model) {
            /** @var $model ActiveRecord */
            $value = [];
            foreach ($fields as $key => $field){
                if (is_numeric($key)){
                    $value[$field] = $model->getAttribute($field);
                } elseif (is_callable($field)) {
                    $value[$key] = $field($model);
                } else {
                    $value[$field] = $model->getAttribute($key);
                }
            }
            $return[] = $value;
        }
        return $return;
    }

    protected function render($view, $params = []){

        $class = get_called_class();
        $path  = explode('\\', $class);
        array_shift($path);
        array_shift($path);
        $path = implode('/', $path);
        $path = '@app/views/api/'.strtolower($path).'/';
        return \Yii::$app->view->renderFile($path.$view.'.php', $params);
    }
}