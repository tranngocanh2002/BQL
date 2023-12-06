/*
 * ServiceConfig Messages
 *
 * This contains all the text for the ServiceConfig container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ServiceConfig";

export default defineMessages({
  activateService: {
    id: `${scope}.activateService`,
    defaultMessage: "Kích hoạt dịch vụ",
  },
  activatedService: {
    id: `${scope}.activatedService`,
    defaultMessage: "Dịch vụ đã được kích hoạt",
  },
  addService: {
    id: `${scope}.addService`,
    defaultMessage: "Thêm dịch vụ",
  },
  notFoundService: {
    id: `${scope}.notFoundService`,
    defaultMessage: "Hiện tại chưa có dịch vụ cung cấp.",
  },
  information: {
    id: `${scope}.information`,
    defaultMessage: "Thông tin",
  },
  settingFee: {
    id: `${scope}.settingFee`,
    defaultMessage: "Cài đặt mức phí",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Xác nhận",
  },
  confirmChange: {
    id: `${scope}.confirmChange`,
    defaultMessage: "Bạn chắc chắn muốn thay đổi cấu hình thông tin?",
  },
  continue: {
    id: `${scope}.continue`,
    defaultMessage: "Tiếp tục",
  },
  skip: {
    id: `${scope}.skip`,
    defaultMessage: "Bỏ qua",
  },
  serviceName: {
    id: `${scope}.serviceName`,
    defaultMessage: "Tên dịch vụ",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  contractor: {
    id: `${scope}.contractor`,
    defaultMessage: "Nhà cung cấp",
  },
  typePayFee: {
    id: `${scope}.typePayFee`,
    defaultMessage: "Hình thức tính phí",
  },
  viaResident: {
    id: `${scope}.viaResident`,
    defaultMessage: "Theo cư dân",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  viaProperty: {
    id: `${scope}.viaProperty`,
    defaultMessage: "Theo bất động sản",
  },
  tax: {
    id: `${scope}.tax`,
    defaultMessage: "Thuế",
  },
  includedFee: {
    id: `${scope}.includedFee`,
    defaultMessage: "Đã bao gồm trong phí",
  },
  otherFee: {
    id: `${scope}.otherFee`,
    defaultMessage: "Phí khác",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  editInformationService: {
    id: `${scope}.editInformationService`,
    defaultMessage: "Chỉnh sửa thông tin dịch vụ",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  avatar: {
    id: `${scope}.avatar`,
    defaultMessage: "Ảnh đại diện",
  },
  emptyServiceName: {
    id: `${scope}.emptyServiceName`,
    defaultMessage: "Tên dịch vụ không được để trống.",
  },
  emptyServiceNameEn: {
    id: `${scope}.emptyServiceNameEn`,
    defaultMessage: "Tên dịch vụ tiếng anh không được để trống.",
  },
  selectStatus: {
    id: `${scope}.selectStatus`,
    defaultMessage: "Chọn trạng thái",
  },
  included: {
    id: `${scope}.included`,
    defaultMessage: "Đã bao gồm",
  },
  notIncluded: {
    id: `${scope}.notIncluded`,
    defaultMessage: "Chưa bao gồm",
  },
  emptySupplier: {
    id: `${scope}.emptySupplier`,
    defaultMessage: "Nhà cung cấp không được để trống.",
  },
  selectSupplier: {
    id: `${scope}.selectSupplier`,
    defaultMessage: "Chọn nhà cung cấp",
  },
  introduce: {
    id: `${scope}.introduce`,
    defaultMessage: "Giới thiệu",
  },
  more: {
    id: `${scope}.more`,
    defaultMessage: "Trở lên",
  },
  emptyLevel: {
    id: `${scope}.emptyLevel`,
    defaultMessage: "{title} không được để trống.",
  },
  priceError: {
    id: `${scope}.priceError`,
    defaultMessage: "Đơn giá phải lớn hơn 0.",
  },
  leastLevel: {
    id: `${scope}.leastLevel`,
    defaultMessage: "Bạn phải nhập trên {toLevel}",
  },
  level: {
    id: `${scope}.level`,
    defaultMessage: "Mức",
  },
  from: {
    id: `${scope}.from`,
    defaultMessage: "Từ",
  },
  to: {
    id: `${scope}.to`,
    defaultMessage: "Đến",
  },
  price: {
    id: `${scope}.price`,
    defaultMessage: "Giá",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  save: {
    id: `${scope}.save`,
    defaultMessage: "Lưu",
  },
  confirmCancel: {
    id: `${scope}.confirmCancel`,
    defaultMessage: "Bạn chắc chắn muốn hủy?",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  confirmDelete: {
    id: `${scope}.confirmDelete`,
    defaultMessage: "Bạn chắc chắn muốn xóa?",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xóa",
  },
  refresh: {
    id: `${scope}.refresh`,
    defaultMessage: "Làm mới trang",
  },
  addNewFee: {
    id: `${scope}.addNewFee`,
    defaultMessage: "Thêm mới phí",
  },
  contentEdit: {
    id: `${scope}.contentEdit`,
    defaultMessage:
      "Nếu chỉnh sửa cấu hình phí, có thể làm thay đổi đến tạo phí tự động của tháng hiện tại. Bạn chắc chắn muốn thay đổi?",
  },
  serviceFee: {
    id: `${scope}.serviceFee`,
    defaultMessage: "Phí dịch vụ",
  },
  autoCreateFee: {
    id: `${scope}.autoCreateFee`,
    defaultMessage: "Tự động tạo phí",
  },
  timeCreateFee: {
    id: `${scope}.timeCreateFee`,
    defaultMessage: "Thời gian tạo phí",
  },
  day: {
    id: `${scope}.day`,
    defaultMessage: "Ngày",
  },
  periodic: {
    id: `${scope}.periodic`,
    defaultMessage: "Chu kỳ",
  },
  payExpire: {
    id: `${scope}.payExpire`,
    defaultMessage: "Hạn thanh toán",
  },
  emptyStatus: {
    id: `${scope}.emptyStatus`,
    defaultMessage: "Trạng thái không được để trống.",
  },
  limitFee: {
    id: `${scope}.limitFee`,
    defaultMessage: "Phí dịch vụ phải lớn hơn 0.",
  },
  ruleTimeCreateFee: {
    id: `${scope}.ruleTimeCreateFee`,
    defaultMessage: "Thời gian tạo phí phải nhập từ mồng 1 đến 28.",
  },
  emptyPayExpire: {
    id: `${scope}.emptyPayExpire`,
    defaultMessage: "Hạn thanh toán không được để trống.",
  },
  code: {
    id: `${scope}.code`,
    defaultMessage: "Mã",
  },
  typeVehicle: {
    id: `${scope}.typeVehicle`,
    defaultMessage: "Loại xe",
  },
  unitPrice: {
    id: `${scope}.unitPrice`,
    defaultMessage: "Đơn giá (đ/tháng)",
  },
  note: {
    id: `${scope}.note`,
    defaultMessage: "Ghi chú",
  },
  addLevelFee: {
    id: `${scope}.addLevelFee`,
    defaultMessage: "Thêm mức phí",
  },
  on: {
    id: `${scope}.on`,
    defaultMessage: "Bật",
  },
  off: {
    id: `${scope}.off`,
    defaultMessage: "Tắt",
  },
  dinhMuc: {
    id: `${scope}.dinhMuc`,
    defaultMessage: "Định mức",
  },
  feeSaveEnvironment: {
    id: `${scope}.feeSaveEnvironment`,
    defaultMessage: "Phí bảo vệ môi trường",
  },
  list: {
    id: `${scope}.list`,
    defaultMessage: "Danh sách",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: "Quay lại",
  },
  configuration: {
    id: `${scope}.configuration`,
    defaultMessage: "Cấu hình",
  },
  add: {
    id: `${scope}.add`,
    defaultMessage: "Thêm mới",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  numberContact: {
    id: `${scope}.numberContact`,
    defaultMessage: "Số liên hệ",
  },
  emptyNumberContact: {
    id: `${scope}.emptyNumberContact`,
    defaultMessage: "Số liên hệ không được để trống.",
  },
  deposit: {
    id: `${scope}.deposit`,
    defaultMessage: "Tiền đặt cọc",
  },
  books: {
    id: `${scope}.books`,
    defaultMessage: "Số lượt đặt/bất động sản/tháng",
  },
  limitBooks: {
    id: `${scope}.limitBooks`,
    defaultMessage: "Số lượt book không được để trống và phải lớn hơn 0.",
  },
  timeToCancel: {
    id: `${scope}.timeToCancel`,
    defaultMessage: "Thời gian được hủy trước giờ sử dụng (phút)",
  },
  limitTimeToCancel: {
    id: `${scope}.limitTimeToCancel`,
    defaultMessage: "Thời gian được hủy không được để trống và lớn hơn 0.",
  },
  timeWaitApprove: {
    id: `${scope}.timeWaitApprove`,
    defaultMessage: "Thời gian chờ duyệt (phút)",
  },
  limitTimeToApprove: {
    id: `${scope}.limitTimeToApprove`,
    defaultMessage: "Thời gian chờ duyệt không được để trống và lớn hơn 0.",
  },
  timeClose: {
    id: `${scope}.timeClose`,
    defaultMessage: "Giờ đóng cửa",
  },
  emptyTimeClose: {
    id: `${scope}.emptyTimeClose`,
    defaultMessage: "Giờ đóng cửa không được để trống.",
  },
  timeOpen: {
    id: `${scope}.timeOpen`,
    defaultMessage: "Giờ mở cửa",
  },
  emptyTimeOpen: {
    id: `${scope}.emptyTimeOpen`,
    defaultMessage: "Giờ mở cửa không được để trống.",
  },
  selectType: {
    id: `${scope}.selectType`,
    defaultMessage: "Vui lòng chọn loại hình dịch vụ",
  },
  type: {
    id: `${scope}.type`,
    defaultMessage: "Loại hình tính phí sử dụng tiện ích",
  },
  emptyRegulation: {
    id: `${scope}.emptyRegulation`,
    defaultMessage: "Quy định không được để trống.",
  },
  regulation: {
    id: `${scope}.regulation`,
    defaultMessage: "Quy định",
  },
  emptyDescription: {
    id: `${scope}.emptyDescription`,
    defaultMessage: "Mô tả không được để trống.",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  time: {
    id: `${scope}.time`,
    defaultMessage: "Thời gian",
  },
  free: {
    id: `${scope}.free`,
    defaultMessage: "Miễn phí",
  },
  confirmDeleteTime: {
    id: `${scope}.confirmDeleteTime`,
    defaultMessage: "Bạn chắc chắn muốn xóa khung giờ này không?",
  },
  addNewSlot: {
    id: `${scope}.addNewSlot`,
    defaultMessage: "Thêm chỗ mới",
  },
  address: {
    id: `${scope}.address`,
    defaultMessage: "Địa chỉ",
  },
  fee: {
    id: `${scope}.fee`,
    defaultMessage: "Thu phí",
  },
  slotFree: {
    id: `${scope}.slotFree`,
    defaultMessage: "Số chỗ trống",
  },
  addTime: {
    id: `${scope}.addTime`,
    defaultMessage: "Thêm khung giờ",
  },
  updateSlot: {
    id: `${scope}.updateSlot`,
    defaultMessage: "Cập nhật chỗ",
  },
  confirmDeleteSlot: {
    id: `${scope}.confirmDeleteSlot`,
    defaultMessage: "Bạn chắc chắn muốn xóa chỗ này không?",
  },
  deleteSlot: {
    id: `${scope}.deleteSlot`,
    defaultMessage: "Xóa chỗ",
  },
  nameSlot: {
    id: `${scope}.nameSlot`,
    defaultMessage: "Tên chỗ",
  },
  emptyNameSlot: {
    id: `${scope}.emptyNameSlot`,
    defaultMessage: "Tên chỗ không được để trống.",
  },
  emptyNameSlotEn: {
    id: `${scope}.emptyNameSlotEn`,
    defaultMessage: "Tên chỗ (EN) không được để trống.",
  },
  emptyAddress: {
    id: `${scope}.emptyAddress`,
    defaultMessage: "Địa chỉ không được để trống.",
  },
  emptyAddressEn: {
    id: `${scope}.emptyAddressEn`,
    defaultMessage: "Địa chỉ tiếng anh không được để trống.",
  },
  Type: {
    id: `${scope}.Type`,
    defaultMessage: "Loại",
  },
  emptySlotFree: {
    id: `${scope}.emptySlotFree`,
    defaultMessage: "Số chỗ trống không được để trống và lớn hơn 0.",
  },
  emptyPrice: {
    id: `${scope}.emptyPrice`,
    defaultMessage: "Giá không được để trống và lớn hơn 0.",
  },
  timeStart: {
    id: `${scope}.timeStart`,
    defaultMessage: "Thời gian bắt đầu",
  },
  emptyTimeStart: {
    id: `${scope}.emptyTimeStart`,
    defaultMessage: "Thời gian bắt đầu không được để trống.",
  },
  timeEnd: {
    id: `${scope}.timeEnd`,
    defaultMessage: "Thời gian kết thúc",
  },
  emptyTimeEnd: {
    id: `${scope}.emptyTimeEnd`,
    defaultMessage: "Thời gian kết thúc không được để trống.",
  },
  contentToLong: {
    id: `${scope}.contentToLong`,
    defaultMessage: "Nội dung không được dài quá 2000 ký tự.",
  },
  timeWaitApprove2: {
    id: `${scope}.timeWaitApprove2`,
    defaultMessage: "Thời gian chờ duyệt",
  },
  timeToCancel2: {
    id: `${scope}.timeToCancel2`,
    defaultMessage: "Thời gian được hủy trước giờ sử dụng",
  },
  minute: {
    id: `${scope}.minute`,
    defaultMessage: "{total, plural, one {# phút} other {# phút}}",
  },
  emptyDeposit: {
    id: `${scope}.emptyDeposit`,
    defaultMessage: "Tiền đặt cọc không được để trống.",
  },
  confirmDeleteUtility: {
    id: `${scope}.confirmDeleteUtility`,
    defaultMessage: "Bạn chắc chắn muốn xóa tiện ích này?",
  },
  imageCover: {
    id: `${scope}.imageCover`,
    defaultMessage: "Ảnh cover",
  },
  nameUtility: {
    id: `${scope}.nameUtility`,
    defaultMessage: "Tên tiện ích",
  },
  timeActivation: {
    id: `${scope}.timeActivation`,
    defaultMessage: "Giờ hoạt động",
  },
  editUtility: {
    id: `${scope}.editUtility`,
    defaultMessage: "Chỉnh sửa tiện ích",
  },
  deleteUtility: {
    id: `${scope}.deleteUtility`,
    defaultMessage: "Xóa tiện ích",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  confirmDeletePaymentInfo: {
    id: `${scope}.confirmDeletePaymentInfo`,
    defaultMessage: "Bạn chắc chắn muốn xóa thông tin thanh toán này?",
  },
  amountMoney: {
    id: `${scope}.amountMoney`,
    defaultMessage: "Số tiền",
  },
  month: {
    id: `${scope}.month`,
    defaultMessage: "Tháng",
  },
  deleteFee: {
    id: `${scope}.deleteFee`,
    defaultMessage: "Xóa phí",
  },
  import: {
    id: `${scope}.import`,
    defaultMessage: "Import dữ liệu",
  },
  editPaymentFee: {
    id: `${scope}.editPaymentFee`,
    defaultMessage: "Chỉnh sửa phí thanh toán",
  },
  createPaymentFee: {
    id: `${scope}.createPaymentFee`,
    defaultMessage: "Tạo phí thanh toán",
  },
  emptyProperty: {
    id: `${scope}.emptyProperty`,
    defaultMessage: "Bất động sản không được để trống.",
  },
  emptyCurrentProperty: {
    id: `${scope}.emptyCurrentProperty`,
    defaultMessage: "Bất động sản được chọn chưa có chủ hộ.",
  },
  selectProperty: {
    id: `${scope}.selectProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Trống",
  },
  emptyAmountMoney: {
    id: `${scope}.emptyAmountMoney`,
    defaultMessage: "Số tiền không được để trống.",
  },
  emptyMonth: {
    id: `${scope}.emptyMonth`,
    defaultMessage: "Tháng thanh toán không được để trống.",
  },
  selectMonth: {
    id: `${scope}.selectMonth`,
    defaultMessage: "Chọn tháng",
  },
  dayExpire: {
    id: `${scope}.dayExpire`,
    defaultMessage: "Hạn thanh toán",
  },
  selectDay: {
    id: `${scope}.selectDay`,
    defaultMessage: "Chọn ngày",
  },
  stop: {
    id: `${scope}.stop`,
    defaultMessage: "Dừng hoạt động",
  },
  active: {
    id: `${scope}.active`,
    defaultMessage: "Đang hoạt động",
  },
  pause: {
    id: `${scope}.pause`,
    defaultMessage: "Tạm ngừng hoạt động",
  },
  emptyUtilityName: {
    id: `${scope}.emptyUtilityName`,
    defaultMessage: "Tên tiện ích không được để trống.",
  },
  emptyUtilityNameEn: {
    id: `${scope}.emptyUtilityNameEn`,
    defaultMessage: "Tên tiện ích (EN) không được để trống.",
  },
  totalPage: {
    id: `${scope}.totalPage`,
    defaultMessage:
      "Tổng số {total, plural, one {# tiện ích} other {# tiện ích}}",
  },
  hotlineInvalid: {
    id: `${scope}.hotlineInvalid`,
    defaultMessage: "Số liên hệ không đúng định dạng",
  },
  invalidServiceNameEn: {
    id: `${scope}.invalidServiceNameEn`,
    defaultMessage: "Tên dịch vụ (EN) không đúng định dạng",
  },
  turn: {
    id: `${scope}.turn`,
    defaultMessage: "{total, plural, one {# lượt} other {# lượt}}",
  },
  name: {
    id: `${scope}.name`,
    defaultMessage: "Tên",
  },
  feeDvtn: {
    id: `${scope}.feeDvtn`,
    defaultMessage: "Phí DVTN",
  },
});
