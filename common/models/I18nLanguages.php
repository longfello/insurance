<?php

namespace common\models;

use Yii;

/**
 * Языки / локализации
 * This is the model class for table "i18n_languages".
 *
 * @property integer $id
 * @property string $iso
 * @property string $google_iso
 * @property string $name
 * @property integer $fallback
 * @property integer $sort_order
 * @property string $domain
 */
class I18nLanguages extends \yii\db\ActiveRecord
{
    /**
     * @var string[] фалбэки - ISO коды локалей, где смотреть перевод, если отсутствует в текущем
     */
    static $fallbacks;
    /**
     * @var string[] перечень доступных локалей
     */
    static $lang;
    /**
     * @var string[] перечень доступных локалей в короткой нотации
     */
    static $langShort;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'i18n_languages';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iso', 'name', 'fallback'], 'required'],
            [['fallback', 'sort_order'], 'integer'],
            [['iso', 'google_iso'], 'string', 'max' => 6],
            [['name'], 'string', 'max' => 50],
            [['domain'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => Yii::t('app', 'ID'),
            'iso'        => Yii::t('app', 'Iso'),
            'name'       => Yii::t('app', 'Name'),
            'fallback'   => Yii::t('app', 'Fallback'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'domain'     => Yii::t('app', 'Domain'),
        ];
    }

    /**
     * Возвращает текущий язык
     * @return I18nLanguages|null
     */
    public static function getCurrentLanguage()
    {
        return self::findOne(['iso' => Yii::$app->language]);
    }

    /**
     * Возвращает перечень доступных локалей
     * @param bool $short
     *
     * @return array
     */
    public static function getAvailableLanguages($short = false)
    {
        if ($short) {
            if (self::$langShort) {
                return self::$langShort;
            }
        } else {
            if (self::$lang) {
                return self::$lang;
            }
        }

        $lang = [];

        $result = self::getDb()->cache(function ($db) {
            return self::find()->where(['domain' => Yii::getAlias('@frontendUrl')])->orderBy('sort_order, name')->all();
        });

        foreach ($result as $one) {
            $key        = $short ? substr($one->iso, 0, 2) : $one->iso;
            $lang[$key] = $one->name;
        }

        if ($short) {
            self::$langShort = $lang;
        } else {
            self::$lang = $lang;
        }

        return $lang;
    }

    /**
     * Возвращает перечень ISO-кодов fallback локалей (где смотреть перевод, если перевода для данной локали нет)
     * @return string[]
     */
    public static function getFallbacks()
    {
        if (self::$fallbacks) {
            return self::$fallbacks;
        }
        self::$fallbacks = [];

        $result = self::getDb()->cache(function ($db) {
            return self::find()->where(['domain' => Yii::getAlias('@frontendUrl')])->orderBy('sort_order, name')->all();
        });

        foreach ($result as $one) {
            $model = self::findOne(['id' => $one->fallback]);
            if ($model) {
                self::$fallbacks[substr($one->iso, 0, 2)] = $model->iso;
            }
        }

        return self::$fallbacks;
    }

    /**
     * Возвращает ISO-код fallback локали  (где смотреть перевод, если перевода для данной локали нет)
     * @param $code
     *
     * @return bool|string
     */
    public static function getFallback($code)
    {
        $fallbacks = self::getFallbacks();
        if (isset($fallbacks[$code])) {
            return substr($fallbacks[$code], 0, 2);
        }

        return false;
    }

    /**
     * Возвращает имя поля для хранения перевода на указанную локаль. Если локаль не задана будет использована текущая.
     * @param $field
     * @param bool $iso
     *
     * @return string
     */
    public static function localizedFieldName($field, $iso = false)
    {
        $current_iso = substr(\Yii::$app->language, 0, 2);
        $default_iso = substr(\Yii::$app->sourceLanguage, 0, 2);
        $iso         = $iso ? $iso : $current_iso;
        $iso         = substr($iso, 0, 2);

        $suffix = ($iso == $default_iso) ? "" : "_" . $iso;

        return $field . $suffix;
    }
}
