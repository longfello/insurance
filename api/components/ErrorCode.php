<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 29.11.17
 * Time: 12:22
 */

namespace api\components;

use yii\base\BaseObject;


/**
 * Class ErrorCode Store known error codes, http response codes and messages
 *
 * ## Коды ошибок
 *
 *  В случае ошибки все методы вощвращают HTTP код ошибки и контент в запрошенном формате струтуры вида:
 *
 * ```json
 * {
 *    "error": {
 *      "code": 404,
 *      "message": "Method Not Found: POST method"
 *   }
 * }
 * ```
 * Список кодов ошибок приведен ниже:
 *
 * @package api\components
 */
class ErrorCode extends BaseObject
{
    /**
     * Undefinded response format
     */
    const UNDEFINED_FORMAT = 1;
    /**
     * Undefined error
     */
    const UNDEFINED_ERROR = 2;
    /**
     * Parse data error
     */
    const PARSE_DATA = 4;
    /**
     * Save data error
     */
    const SAVE_DATA = 8;
    /**
     * Validate data error
     */
    const VALIDATE_DATA = 16;
    /**
     * Autentification error
     */
    const AUTH_DENIED = 32;
    /**
     * Not found error
     */
    const NOT_FOUND = 64;
    /**
     * Internal error
     */
    const INTERNAL_ERROR = 128;

    /**
     * Default error messages
     * @var array
     */
    public static $messages = [
        self::UNDEFINED_FORMAT => 'Неизвестный формат данных',
        self::UNDEFINED_ERROR  => 'Недокументированная ошибка',
        self::AUTH_DENIED      => 'Ошибка аутентификации',
        self::PARSE_DATA       => 'Ошибка разбора данных',
        self::SAVE_DATA        => 'Ошибка сохранения данных',
        self::VALIDATE_DATA    => 'Ошибка валидации данных',
        self::NOT_FOUND        => 'Не найдено',
        self::INTERNAL_ERROR   => 'Внутренняя ошибка',
    ];

    /**
     * Default http response codes
     * @var array
     */
    public static $httpCodes = [
        self::UNDEFINED_FORMAT => 405,
        self::UNDEFINED_ERROR  => 520,
        self::PARSE_DATA       => 500,
        self::SAVE_DATA        => 500,
        self::VALIDATE_DATA    => 500,
        self::AUTH_DENIED      => 403,
        self::NOT_FOUND        => 404,
        self::INTERNAL_ERROR   => 500,
    ];

    /**
     * Getter for default http response code
     *
     * @param int $errorCode Occurred error code
     *
     * @return int Http response code
     */
    static function getHttpCode($errorCode)
    {
        if (isset(self::$httpCodes[$errorCode])) {
            return self::$httpCodes[$errorCode];
        }

        return self::$httpCodes[self::UNDEFINED_ERROR];
    }

    /**
     * Getter for default error message
     * @param int $errorCode Error code
     *
     * @return string Error message
     */
    static function getMessage($errorCode)
    {
        if (isset(self::$messages[$errorCode])) {
            return self::$messages[$errorCode];
        }

        return self::$messages[self::UNDEFINED_ERROR];
    }
}