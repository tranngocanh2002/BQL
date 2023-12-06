<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "wards".
 *
 * @property int $id
 * @property string $name
 * @property int $city_id
 * @property int $district_id
 */
class Wards extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'wards';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'city_id', 'district_id'], 'required'],
            [['city_id', 'district_id'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'city_id' => 'City ID',
            'district_id' => 'District ID',
        ];
    }
}
