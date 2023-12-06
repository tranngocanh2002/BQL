<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "maintenance_device".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string|null $position Vị trí thiết bị
 * @property string|null $description
 * @property string|null $attach
 * @property int $building_cluster_id
 * @property int $status
 * @property int|null $guarantee_time_start thời gian bắt đầu bảo hành
 * @property int|null $guarantee_time_end thời gian kết thúc bảo hành
 * @property int $maintenance_time_start thời gian bắt đầu bảo trì
 * @property int|null $maintenance_time_last thời gian bảo trì gần nhất
 * @property int|null $maintenance_time_next thời gian bảo trì sắp tới
 * @property int $type
 * @property int $cycle Chu kỳ bảo chỉ theo tháng
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $is_deleted
 */
class MaintenanceDevice extends \yii\db\ActiveRecord
{
    const NOT_DELETED = 0;
    const DELETED = 1;

    const STATUS_OFF = 0;
    const STATUS_ON = 1;
    public static $status_list = [
        self::STATUS_OFF => "Dừng hoạt động",
        self::STATUS_ON => "Đang hoạt động"
    ];

    const TYPE_0 = 0;
    const TYPE_1 = 1;
    const TYPE_2 = 2;
    const TYPE_3 = 3;
    const TYPE_4 = 4;
    public static $type_list = [
        self::TYPE_0 => "Máy tính",
        self::TYPE_1 => "Quạt",
        self::TYPE_2 => "Camera",
        self::TYPE_3 => "Đèn",
        self::TYPE_4 => "Thang máy",
    ];
    public static $type_list_number = [
        "Máy tính" => self::TYPE_0,
        "Quạt" => self::TYPE_1,
        "Camera" => self::TYPE_2,
        "Đèn" => self::TYPE_3,
        "Thang máy" => self::TYPE_4,
    ];

    const CYCLE_0 = 0;
    const CYCLE_1 = 1;
    const CYCLE_2 = 2;
    const CYCLE_3 = 3;
    const CYCLE_6 = 4;
    const CYCLE_12 = 5;
    const CYCLE_24 = 6;
    public static $cycle_list = [
        self::CYCLE_0 => "Không lặp lại",
        self::CYCLE_1 => "1 tháng",
        self::CYCLE_2 => "2 tháng",
        self::CYCLE_3 => "3 tháng",
        self::CYCLE_6 => "6 tháng",
        self::CYCLE_12 => "12 tháng",
        self::CYCLE_24 => "24 tháng",
    ];
    public static $cycle_list_number = [
        "Không lặp lại" => self::CYCLE_0,
        "1 tháng" => self::CYCLE_1,
        "2 tháng" => self::CYCLE_2,
        "3 tháng" =>self::CYCLE_3,
        "6 tháng" =>self::CYCLE_6,
        "12 tháng" =>self::CYCLE_12,
        "24 tháng" =>self::CYCLE_24,
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'maintenance_device';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'code', 'building_cluster_id', 'maintenance_time_start'], 'required'],
            [['attach'], 'string'],
            [['building_cluster_id', 'status', 'guarantee_time_start', 'guarantee_time_end', 'maintenance_time_start', 'maintenance_time_last', 'maintenance_time_next', 'type', 'cycle', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['name', 'code', 'position'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 1000],
//            [['code', 'building_cluster_id'], 'unique', 'targetAttribute' => ['code'], 'message' => Yii::t('common', "Code has exist!")],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('common', 'ID'),
            'name' => Yii::t('common', 'Name'),
            'code' => Yii::t('common', 'Code'),
            'position' => Yii::t('common', 'Position'),
            'description' => Yii::t('common', 'Description'),
            'attach' => Yii::t('common', 'Attach'),
            'building_cluster_id' => Yii::t('common', 'Building Cluster ID'),
            'status' => Yii::t('common', 'Status'),
            'guarantee_time_start' => Yii::t('common', 'Guarantee Time Start'),
            'guarantee_time_end' => Yii::t('common', 'Guarantee Time End'),
            'maintenance_time_start' => Yii::t('common', 'Maintenance Time Start'),
            'maintenance_time_last' => Yii::t('common', 'Maintenance Time Last'),
            'maintenance_time_next' => Yii::t('common', 'Maintenance Time Next'),
            'type' => Yii::t('common', 'Type'),
            'cycle' => Yii::t('common', 'Cycle'),
            'created_at' => Yii::t('common', 'Created At'),
            'updated_at' => Yii::t('common', 'Updated At'),
            'created_by' => Yii::t('common', 'Created By'),
            'updated_by' => Yii::t('common', 'Updated By'),
            'is_deleted' => Yii::t('common', 'Is Deleted'),
        ];
    }

    /**
     * @inheritdoc
     */
    function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'time',
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => ['updated_at'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ]
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by', 'updated_by'],
                    self::EVENT_BEFORE_UPDATE => ['updated_by'],
                    self::EVENT_BEFORE_DELETE => ['updated_at'],
                ],
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'is_deleted' => true
                ],
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }
        if($this->cycle > self::CYCLE_0){
            $next_time = $this->maintenance_time_last;
            if(empty($next_time)){
                $next_time = $this->maintenance_time_start;
            }
            $dateTime = new \DateTime("@$next_time");

            $formattedDate = $dateTime->format('Y-m-d');

            $ngay = new \DateTime($formattedDate);

            $cycles = (INT)$this->cycle;
            switch ($cycles) {
                case 0:
                    $cycle = 0;
                    break;
                case 1:
                    $cycle = 1;
                    break;
                case 2:
                    $cycle = 2;
                    break;
                case 3:
                    $cycle = 3;
                    break;
                case 4:
                    $cycle = 6;
                    break;
                case 5:
                    $cycle = 12;
                    break;
                case 6:
                    $cycle = 24;
                    break;
                default:
                    break;
            }
            if ((in_array($ngay->format('j'), ['29', '30', '31']) && $ngay->format('n') === '1') || $ngay->format('j') === $ngay->format('t')) {
                $ngay->modify("last day of +$cycle month");
            } else {
                $ngay->modify("+$cycle month");
            }
            $newDate = $ngay->format('Y-m-d');
            $timestamp = strtotime($newDate);
            $this->maintenance_time_next = $timestamp;
        }else{
            $this->maintenance_time_next = null;
        }
        return true;
    }
}
