<?php

namespace common\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\I18nLanguages;

/**
 * Локали
 * I18NLanguagesSearch represents the model behind the search form about `common\models\I18nLanguages`.
 */
class I18NLanguagesSearch extends I18nLanguages
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'fallback', 'sort_order'], 'integer'],
            [['iso', 'name', 'domain'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = I18nLanguages::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'fallback' => $this->fallback,
            'sort_order' => $this->sort_order,
        ]);

        $query->andFilterWhere(['like', 'iso', $this->iso])
            ->andFilterWhere(['like', 'domain', $this->iso])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }
}
