<?php
/**
 * Copyright (c) kvk-group 2017.
 */

/**
 * Created by PhpStorm.
 * User: miloslawsky
 * Date: 13.03.17
 * Time: 11:52
 */

namespace frontend\components;


/**
 * Class SearchHighlighter Подсветка вхождений
 * @package frontend\components
 */
class SearchHighlighter
{
    /**
     * Возвращает подсвеченную подстроку вхождения
     * @param $text
     * @param $word
     *
     * @return mixed|string
     */public static function getFragment($text, $word){
		if ($word)
		{
			$pos = max(mb_stripos($text, $word, null, 'UTF-8') - 100, 0);
			$fragment = mb_substr($text, $pos, 200, 'UTF-8');
			$highlighted = str_ireplace($word, '<mark>' . $word . '</mark>', $fragment);
		} else {
			$highlighted = mb_substr($text, 0, 200, 'UTF-8');
		}
		return $highlighted;
	}
}