<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * PartnerForm is the model behind the partner form.
 */
class PartnerForm extends Model
{
    /**
     * @var string Тип партнера
     */
    public $type;
    /**
     * @var string Наименование Юр. Лица.
     */
    public $jur;
    /**
     * @var string Фамилия
     */
    public $surname;
    /**
     * @var string Имя
     */
    public $name;
    /**
     * @var string Отчество
     */
    public $thirdname;
    /**
     * @var string Сайт
     */
    public $site;
    /**
     * @var string Электронная почта
     */
    public $email;
    /**
     * @var string Телефон
     */
    public $phone;
    /**
     * @var string Комментарий
     */
    public $comment;

    /**
     * @inheritdoc
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['type', 'surname', 'name', 'email', 'phone'], 'required'],
            // We need to sanitize them
            [['jur', 'surname', 'name', 'thirdname', 'site', 'phone', 'comment'], 'filter', 'filter' => 'strip_tags'],
            // email has to be a valid email address
            ['email', 'email'],
        ];
    }

    /**
     * @inheritdoc
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('frontend', 'Тип партнера'),
            'jur' => Yii::t('frontend', 'Наименование Юр. Лица.'),
            'surname' => Yii::t('frontend', 'Фамилия'),
            'name' => Yii::t('frontend', 'Имя'),
            'thirdname' => Yii::t('frontend', 'Отчество'),
            'site' => Yii::t('frontend', 'Адреса сайта/соц сети'),
            'email' => Yii::t('frontend', 'E-mail'),
            'phone' => Yii::t('frontend', 'Телефон'),
            'comment' => Yii::t('frontend', 'Комментарий')
        ];
    }

    /**
     * Отправка почты
     * Sends an email to the specified email address using the information collected by this model.
     * @param  string  $email the target email address
     * @return boolean whether the model passes validation
     */
    public function send($email)
    {
        if ($this->validate()) {
            $text = '';
            $text .= 'Тип пользователя: '.$this->type.PHP_EOL;
            if ($this->type=='Юридическое лицо' && $this->jur!='') $text .= 'Наименование Юр. Лица.: '.$this->jur.PHP_EOL;
            $text .= 'Фамилия: '.$this->surname.PHP_EOL;
            $text .= 'Имя: '.$this->name.PHP_EOL;
            if ($this->thirdname!='') $text .= 'Отчество: '.$this->thirdname.PHP_EOL;
            if ($this->site!='') $text .= 'Адреса сайта/соц сети: '.$this->site.PHP_EOL;
            $text .= 'E-mail: '.$this->email.PHP_EOL;
            $text .= 'Телефон: '.$this->phone.PHP_EOL;
            if ($this->comment!='') $text .= 'Комментарий: '.$this->comment.PHP_EOL;

            return Yii::$app->mailer->compose()
                ->setTo($email)
                ->setFrom(Yii::$app->params['robotEmail'])
                ->setReplyTo([$this->email => $this->name])
                ->setSubject("Форма 'Как стать партнером'")
                ->setTextBody($text)
                ->send();
        } else {
            return false;
        }
    }
}
