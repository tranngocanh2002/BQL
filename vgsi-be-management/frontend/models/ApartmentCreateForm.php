<?php

namespace frontend\models;

use common\helpers\CUtils;
use common\helpers\ErrorCode;
use common\models\Apartment;
use common\models\ApartmentMapResidentUser;
use common\models\BuildingArea;
use common\models\ResidentUser;
use Yii;
use yii\base\Model;
use yii\db\Exception;

/**
 * @SWG\Definition(
 *   type="object",
 *   @SWG\Xml(name="ApartmentCreateForm")
 * )
 */
class ApartmentCreateForm extends Model
{
    /**
     * @SWG\Property(description="Id - Bắt buộc khi update", default=1, type="integer")
     * @var integer
     */
    public $id;

    /**
     * @SWG\Property(description="Name")
     * @var string
     */
    public $name;

    /**
     * @SWG\Property(description="Description")
     * @var string
     */
    public $description;

    /**
     * @SWG\Property(property="medias", type="object",
     *     @SWG\Property(property="key1", type="integer"),
     *     @SWG\Property(property="key2", type="string"),
     * ),
     * @var array
     */
    public $medias;

    /**
     * @SWG\Property(description="Capacity", default=1)
     * @var float
     */
    public $capacity;

    /**
     * @SWG\Property(description="set_water_level: trạng thái khai báo định mức nước sử dụng - 0 là chưa khai báo, 1 là đã khai báo", default=1, type="integer")
     * @var integer
     */
    public $set_water_level;

    /**
     * @SWG\Property(description="Building Area Id", default=1, type="integer")
     * @var integer
     */
    public $building_area_id;

    /**
     * @SWG\Property(description="trang thai can ho (0: Empty, 1: live)", default=0, type="integer")
     * @var integer
     */
    public $status;

    /**
     * @SWG\Property(description="Resident name - chủ hộ -> dùng khi tạo mới, không có khi update")
     * @var string
     */
    public $resident_name;

    /**
     * @SWG\Property(description="Resident phone")
     * @var string
     */
    public $resident_phone;

    /**
     * @SWG\Property(description="handover -> người bàn giao nhà")
     * @var string
     */
    public $handover;

    /**
     * @SWG\Property(description="date_received -> ngày nhận nhà", default=0, type="integer")
     * @var integer
     */
    public $date_received;

    /**
     * @SWG\Property(description="date_delivery -> ngày bàn giao", default=0, type="integer")
     * @var integer
     */
    public $date_delivery;

    /**
     * @SWG\Property(description="total_members-> số thành viên trong căn hộ", default=1, type="integer")
     * @var integer
     */
    public $total_members;

    /**
     * @SWG\Property(description="0: Villa đơn lập, 1: Villa song lập, 2: Nhà phố, 3: Nhà phố thương mại, 4: Căn hộ Studio, 5: Căn hộ, 6: Căn hộ Duplex thông tầng, 7: Căn hộ penthouse, 8: Officetel, 9:  Khách sạn và căn hộ dịch vụ", default=1, type="integer")
     * @var integer
     */
    public $form_type;

    public $type;

    public $type_relationship;

    public $cmtnd;

    public $ngay_cap_cmtnd;

    public $noi_cap_cmtnd;

    public $nationality;

    public $work;

    public $so_thi_thuc;

    public $ngay_het_han_thi_thuc;

    public $ngay_dang_ky_tam_chu;

    public $ngay_dang_ky_nhap_khau;

    public $birthday;

    public $gender;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'building_area_id', 'capacity'], 'required'],
            [['name', 'description', 'handover', 'cmtnd', 'noi_cap_cmtnd', 'nationality', 'work', 'so_thi_thuc'], 'string'],
            [['form_type', 'set_water_level', 'gender', 'birthday', 'building_area_id', 'status', 'type', 'type_relationship', 'date_received', 'date_delivery', 'ngay_cap_cmtnd', 'ngay_dang_ky_nhap_khau', 'ngay_dang_ky_tam_chu', 'ngay_het_han_thi_thuc'], 'integer'],
            [['building_area_id'], 'exist', 'skipOnError' => true, 'targetClass' => BuildingArea::className(), 'targetAttribute' => ['building_area_id' => 'id']],
            [['medias', 'resident_name', 'resident_phone', 'capacity'], 'safe'],
            [['id'], 'required', "on" => ['update']],
            [['id'], 'integer', "on" => ['update']],
            [['resident_name', 'resident_phone'], 'string', "on" => ['create']],
            [['resident_phone'], 'validateMobile', "on" => ['create']],
            [['capacity'], 'validateCapacity'],
        ];
    }

    public function validateMobile($attribute, $params, $validator)
    {
        $this->$attribute = CUtils::validateMsisdn($this->$attribute);
        if (empty($this->$attribute)) {
            $this->addError($attribute, Yii::t('frontend', 'invalid phone number'));
        }
    }

    public function validateCapacity($attribute, $params, $validator)
    {
        if ($this->$attribute <= 0) {
            $this->addError($attribute, Yii::t('frontend', 'invalid capacity'));
        }
    }

    public function create()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $buildingCluster = Yii::$app->building->BuildingCluster;            
            $this->total_members = Yii::$app->request->post('total_members') ?? 1;
            $item = new Apartment();
            $item->load(CUtils::arrLoad($this->attributes), '');
            $item->name = trim($item->name);
            $item->short_name = trim($item->short_name);
            $item->generateCode();
            if (isset($this->medias) && is_array($this->medias)) {
                $item->medias = !empty($this->medias) ? json_encode($this->medias) : null;
            }
            $item->building_cluster_id = $buildingCluster->id;
            $checkName = Apartment::findOne(['name' => $item->name, 'building_cluster_id' => $buildingCluster->id, 'is_deleted' => Apartment::NOT_DELETED]);
            if(!empty($checkName)){
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Tên bất động sản đã tồn tại trong hệ thống"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            if(1 == $this->status && empty($this->total_members))
            {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Tổng thành viên không được để trống"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                ];
            }
            $item->setParentPath();
            if (!$item->save()) {
                $transaction->rollBack();
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    'errors' => $item->getErrors()
                ];
            } else {
                if (!empty($this->resident_phone)) {
                    $this->type = ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD;
                    $this->type_relationship = ApartmentMapResidentUser::TYPE_RELATIONSHIP_HEAD_OF_HOUSEHOLD;
                    //kiểm tra resident user nếu chưa tồn tại thì tạo mới theo số điện thoại
//                    $residentUser = ResidentUser::getOrCreate($this);
//                    if (!$residentUser['success']) {
//                        $transaction->rollBack();
//                        return $residentUser;
//                    }else{
//                        $residentUser = $residentUser['residentUser'];
//                    }

                    //map resident user vào căn hộ vừa tạo
                    $apartmentMapResidentUser = ApartmentMapResidentUser::addGetOrCreate($item, null, $this);
                    if(!$apartmentMapResidentUser['success']){
                        $transaction->rollBack();
                        return $apartmentMapResidentUser;
                    }
                    if(!$apartmentMapResidentUser['is_new']){
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => Yii::t('frontend', "Residents were in the apartment"),
                        ];
                    }
                }
            }
            $transaction->commit();
            return [
                'success' => true,
                'message' => Yii::t('frontend', "Create success"),
            ];
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }

    public function update()
    {
        $transaction   = Yii::$app->db->beginTransaction();
        $total_members = Yii::$app->request->post('total_members') ?? 1;
        try {
            $item = ApartmentResponse::findOne(['id' => (int)$this->id]);
            if ($item) {
                $building_area_id_old = $item->building_area_id;
                $item->load(CUtils::arrLoad($this->attributes), '');
                $item->name = trim($item->name);
                $item->short_name = trim($item->short_name);
                $checkName = Apartment::find()->where(['name' => $item->name, 'building_cluster_id' => $item->building_cluster_id, 'is_deleted' => Apartment::NOT_DELETED])
                    ->andWhere(['<>', 'id', $item->id])->one();
                if(!empty($checkName)){
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Tên bất động sản đã tồn tại trong hệ thống"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                    ];
                }

                if (isset($this->medias) && is_array($this->medias)) {
                    $item->medias = !empty($this->medias) ? json_encode($this->medias) : null;
                }
                $building_area_id_new = $item->building_area_id;
                if($building_area_id_old !== $building_area_id_new && !empty($building_area_id_new)){
                    $item->setParentPath();
                }
                $item->total_members = $total_members;
                if (!empty($this->resident_phone)) {
                    $this->type = ApartmentMapResidentUser::TYPE_HEAD_OF_HOUSEHOLD;
                    $this->type_relationship = ApartmentMapResidentUser::TYPE_RELATIONSHIP_HEAD_OF_HOUSEHOLD;
                    //kiểm tra resident user nếu chưa tồn tại thì tạo mới theo số điện thoại
                    //                    $residentUser = ResidentUser::getOrCreate($this);
                    //                    if (!$residentUser['success']) {
                    //                        $transaction->rollBack();
                    //                        return $residentUser;
                    //                    }else{
                    //                        $residentUser = $residentUser['residentUser'];
                    //                    }
                    //map resident user vào căn hộ vừa tạo
                    $apartmentMapResidentUser = ApartmentMapResidentUser::getOrUpdate($item, null, $this);
                    if(!$apartmentMapResidentUser['success']){
                        $transaction->rollBack();
                        return $apartmentMapResidentUser;
                    }
                    // kiểm tra cư dân đã tồn tại trong bds
                    // if(!$apartmentMapResidentUser['is_new']){
                    //     $transaction->rollBack();
                    //     return [
                    //         'success' => false,
                    //         'message' => Yii::t('frontend', "Residents were in the apartment"),
                    //     ];
                    // }
                }
                if (!$item->save()) {
                    $transaction->rollBack();
                    return [
                        'success' => false,
                        'message' => Yii::t('frontend', "Invalid data"),
                        'statusCode' => ErrorCode::ERROR_INVALID_PARAM,
                        'errors' => $item->getErrors()
                    ];
                } else {
                    $transaction->commit();
                    return $item;
                }
            } else {
                return [
                    'success' => false,
                    'message' => Yii::t('frontend', "Invalid data"),
                    'statusCode' => ErrorCode::ERROR_INVALID_PARAM
                ];
            }
        } catch (\Exception $ex) {
            Yii::error($ex->getMessage());
            $transaction->rollBack();
            return [
                'success' => false,
                'message' => CUtils::convertMessageError($ex->getMessage()),
                'statusCode' => ErrorCode::ERROR_INVALID_PARAM
            ];
        }
    }
}
