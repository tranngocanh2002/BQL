<?php

namespace common\models;

use common\helpers\ErrorCode;
use Yii;

/**
 * This is the model class for table "history_resident_map_apartment".
 *
 * @property int $id
 * @property int $apartment_id
 * @property string $apartment_name
 * @property string $apartment_parent_path
 * @property int $resident_user_id
 * @property string $resident_user_phone
 * @property int $building_cluster_id
 * @property int $type 0 - thành viên, 1 - chủ hộ, 2 - khách
 * @property int $time_in
 * @property int $time_out
 * 
 */
class HistoryResidentMapApartment extends \yii\db\ActiveRecord
{
    const IS_REMOVE_APARTMENT = 0;
    const IS_ADD_APARTMENT = 1;

    const TYPE_MEMBER = 0;
    const TYPE_HEAD_OF_HOUSEHOLD = 1;
    const TYPE_GUEST = 2;

    public static $type_list = [
        self::TYPE_MEMBER => 'Thành viên',
        self::TYPE_HEAD_OF_HOUSEHOLD => 'Chủ hộ'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'history_resident_map_apartment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'apartment_id',
                'resident_user_id',
                'building_cluster_id',
                'type',
                'time_in',
                'time_out'
            ], 'integer'],
            [[
                'apartment_name',
                'apartment_parent_path',
                'resident_user_phone',
            ], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'apartment_id' => Yii::t('common','Apartment ID'),
            'apartment_name' => Yii::t('common','Apartment Name'),
            'apartment_parent_path' => Yii::t('common','Apartment Parent Path'),
            'resident_user_id' => Yii::t('common','Resident User ID'),
            'building_cluster_id' => Yii::t('common','Building Cluster ID'),
            'resident_user_phone' => Yii::t('common','Resident User Phone'),
            'type' => Yii::t('common','Type'),
            'time_in' => Yii::t('common','Time In'),
            'time_out' => Yii::t('common','Time Out'),
        ];
    }
}
