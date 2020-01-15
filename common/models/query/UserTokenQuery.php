<?php

namespace common\models\query;

use yii\db\ActiveQuery;

/**
 * Class UserTokenQuery Токены пользователей
 * @package common\models\query
 * @author Eugene Terentev <eugene@terentev.net>
 */
class UserTokenQuery extends ActiveQuery
{
    /**
     * Не просроченные
     * @return $this
     */
    public function notExpired()
    {
        $this->andWhere(['>', 'expire_at', time()]);
        return $this;
    }

    /**
     * По типу
     * @param $type
     * @return $this
     */
    public function byType($type)
    {
        $this->andWhere(['type' => $type]);
        return $this;
    }

    /**
     * По токену
     * @param $token
     * @return $this
     */
    public function byToken($token)
    {
        $this->andWhere(['token' => $token]);
        return $this;
    }
}