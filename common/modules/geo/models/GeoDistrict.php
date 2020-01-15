<?php
/**
 * Copyright (c) kvk-group 2017.
 */

namespace common\modules\geo\models;

use Yii;

/**
 * Районы
 * This is the model class for table "geo_district".
 *
 * @property integer $id
 * @property string $name
 * @property string $altername
 * @property integer $population
 * @property string $country
 * @property string $state
 * @property string $city
 */
class GeoDistrict extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'geo_district';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id', 'population'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'altername' => 'Altername',
            'population' => 'Population',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
        ];
    }

    /**
     * Районы по городу
     * @param $cityID
     *
     * @return array
     */
    public static function getList($cityID){
      $cityID = (int)$cityID;
      $list = ['' => Yii::t('frontend', 'All')];
      $query = "
SELECT gd.id, gd.name 
FROM geo_name
LEFT JOIN geo_name_sparital gns ON gns.name_id = geo_name.id
LEFT JOIN geo_district_sparital gds ON contains(gns.area, gds.coord)
LEFT JOIN geo_district gd ON gd.id = gds.district_id
WHERE geo_name.id={$cityID}
ORDER BY gd.population, gd.name";
      $districts = Yii::$app->db->createCommand($query)->queryAll(\PDO::FETCH_OBJ);
      foreach ($districts as $district) {
        $list[$district->id] = $district->name;
      }
      return $list;
    }
}
