<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "resident_user_count_by_age".
 *
 * @property int $id
 * @property int|null $building_cluster_id
 * @property int|null $start_age
 * @property int|null $end_age
 * @property int|null $total_foreigner Tổng người nước ngoài
 * @property int|null $total_vietnam Tổng người việt nam
 * @property int|null $total
 */
class ResidentUserCountByAge extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'resident_user_count_by_age';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['building_cluster_id', 'start_age', 'end_age', 'total_foreigner', 'total_vietnam', 'total'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'start_age' => Yii::t('common', 'Start Age'),
            'end_age' => Yii::t('common', 'End Age'),
            'total_foreigner' => Yii::t('common', 'Total Foreigner'),
            'total_vietnam' => Yii::t('common', 'Total Vietnam'),
            'total' => Yii::t('common', 'Total'),
        ];
    }
}
