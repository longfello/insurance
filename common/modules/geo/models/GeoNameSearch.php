<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\modules\geo\models\GeoName;

/**
 * GeoNameSearch represents the model behind the search form about `common\modules\geo\models\GeoName`.
 */
class GeoNameSearch extends GeoName
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'population', 'zone_id', 'country_id'], 'integer'],
            [['name', 'timezone', 'slug', 'domain', 'synonyms', 'google_id', 'big_banner_url', 'small_banner_url'], 'safe'],
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
        $query = GeoName::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'population' => $this->population,
            'zone_id' => $this->zone_id,
            'country_id' => $this->country_id,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'timezone', $this->timezone])
            ->andFilterWhere(['like', 'slug', $this->slug])
            ->andFilterWhere(['like', 'domain', $this->domain])
            ->andFilterWhere(['like', 'synonyms', $this->synonyms])
            ->andFilterWhere(['like', 'google_id', $this->google_id])
            ->andFilterWhere(['like', 'big_banner_url', $this->big_banner_url])
            ->andFilterWhere(['like', 'small_banner_url', $this->small_banner_url]);

        return $dataProvider;
    }
}
