<?php
/**
 * Created by PhpStorm.
 * User: zein
 * Date: 8/5/14
 * Time: 10:46 AM
 */

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class TimelineEventQuery Таймлайн
 * @package common\models\query
 */
class TimelineEventQuery extends ActiveQuery
{
    /**
     * Сегодняшние события
     * @return $this
     */
    public function today()
    {
        $this->andWhere(['>=', 'created_at', strtotime('today midnight')]);
        return $this;
    }
}
