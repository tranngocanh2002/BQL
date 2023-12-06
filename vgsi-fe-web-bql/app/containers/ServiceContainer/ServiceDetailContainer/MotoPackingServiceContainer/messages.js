/*
 * MotoPackingServiceContainer Messages
 *
 * This contains all the text for the MotoPackingServiceContainer container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.MotoPackingServiceContainer";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the MotoPackingServiceContainer container!",
  },
  manageVehicle: {
    id: `${scope}.manageVehicle`,
    defaultMessage: "Quản lý xe",
  },
  approvedFeeVehicle: {
    id: `${scope}.approvedFeeVehicle`,
    defaultMessage: "Phí xe đã duyệt",
  },
  waitApproveFeeVehicle: {
    id: `${scope}.waitApproveFeeVehicle`,
    defaultMessage: "Phí xe chờ duyệt",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  bienSo: {
    id: `${scope}.bienSo`,
    defaultMessage: "Biển số xe",
  },
  typeFee: {
    id: `${scope}.typeFee`,
    defaultMessage: "Loại phí",
  },
  month: {
    id: `${scope}.month`,
    defaultMessage: "Tháng",
  },
  amountMoney: {
    id: `${scope}.amountMoney`,
    defaultMessage: "Số tiền",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  paid: {
    id: `${scope}.paid`,
    defaultMessage: "Đã thanh toán",
  },
  unpaid: {
    id: `${scope}.unpaid`,
    defaultMessage: "Chưa thanh toán",
  },
  approveDate: {
    id: `${scope}.approveDate`,
    defaultMessage: "Ngày duyệt",
  },
  approveBy: {
    id: `${scope}.approveBy`,
    defaultMessage: "Người duyệt",
  },
  selectProperty: {
    id: `${scope}.selectProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  selectMonth: {
    id: `${scope}.selectMonth`,
    defaultMessage: "Chọn tháng",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  refreshPage: {
    id: `${scope}.refreshPage`,
    defaultMessage: "Làm mới trang",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  totalFee: {
    id: `${scope}.totalFee`,
    defaultMessage: "Tổng số {total, plural, one {# phí} other {# phí}}",
  },
  confirmDelete: {
    id: `${scope}.confirmDelete`,
    defaultMessage: "Bạn chắc chắn muốn xóa thông tin này?",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  deleteFee: {
    id: `${scope}.deleteFee`,
    defaultMessage: "Xóa phí",
  },
  addFee: {
    id: `${scope}.addFee`,
    defaultMessage: "Thêm mới phí",
  },
  approve: {
    id: `${scope}.approve`,
    defaultMessage: "Duyệt",
  },
  approveAll: {
    id: `${scope}.approveAll`,
    defaultMessage: "Duyệt tất cả",
  },
  confirmApproveAll: {
    id: `${scope}.confirmApproveAll`,
    defaultMessage: "Bạn có chắc chắn muốn duyệt tất cả phí này không?",
  },
  confirmDeleteAll: {
    id: `${scope}.confirmDeleteAll`,
    defaultMessage: "Bạn có chắc chắn muốn xóa các phí này không?",
  },
  confirmDeleteOne: {
    id: `${scope}.confirmDeleteOne`,
    defaultMessage: "Bạn có chắc chắn muốn xóa  phí này không?",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage: "Tổng số {total}",
  },
  import: {
    id: `${scope}.import`,
    defaultMessage: "Import dữ liệu",
  },
  export: {
    id: `${scope}.export`,
    defaultMessage: "Tải file import mẫu",
  },
  confirmDeletePayment: {
    id: `${scope}.confirmDeletePayment`,
    defaultMessage: "Bạn chắc chắn muốn xóa thông tin thanh toán này?",
  },
  condition: {
    id: `${scope}.condition`,
    defaultMessage: "Tình trạng",
  },
  enteredBill: {
    id: `${scope}.enteredBill`,
    defaultMessage: "Đã vào đơn",
  },
  confirmDeleteVehicle: {
    id: `${scope}.confirmDeleteVehicle`,
    defaultMessage: "Bạn chắc chắn muốn xóa xe bất động sản này?",
  },
  levelFee: {
    id: `${scope}.levelFee`,
    defaultMessage: "Mức phí (đ/tháng)",
  },
  startDate: {
    id: `${scope}.startDate`,
    defaultMessage: "Ngày bắt đầu",
  },
  endDate: {
    id: `${scope}.endDate`,
    defaultMessage: "Đóng đến ngày",
  },
  dateCancelVehicle: {
    id: `${scope}.dateCancelVehicle`,
    defaultMessage: "Ngày hủy xe",
  },
  notHave: {
    id: `${scope}.notHave`,
    defaultMessage: "Chưa có",
  },
  create: {
    id: `${scope}.create`,
    defaultMessage: "Tạo mới",
  },
  activated: {
    id: `${scope}.activated`,
    defaultMessage: "Đã kích hoạt",
  },
  canceled: {
    id: `${scope}.canceled`,
    defaultMessage: "Đã hủy",
  },
  cancelVehicle: {
    id: `${scope}.cancelVehicle`,
    defaultMessage: "Hủy xe",
  },
  activateVehicle: {
    id: `${scope}.activateVehicle`,
    defaultMessage: "Kích hoạt xe",
  },
  enterLicensePlate: {
    id: `${scope}.enterLicensePlate`,
    defaultMessage: "Nhập biển số xe",
  },
  addVehicle: {
    id: `${scope}.addVehicle`,
    defaultMessage: "Thêm mới xe",
  },
  createFee: {
    id: `${scope}.createFee`,
    defaultMessage: "Tạo phí thanh toán",
  },
  draft: {
    id: `${scope}.draft`,
    defaultMessage: "Lưu nháp",
  },
  saveAndApprove: {
    id: `${scope}.saveAndApprove`,
    defaultMessage: "Lưu và duyệt",
  },
  errorEmptyProperty: {
    id: `${scope}.errorEmptyProperty`,
    defaultMessage: "Bất động sản không được để trống.",
  },
  errorEmptyCurrentProperty: {
    id: `${scope}.errorEmptyCurrentProperty`,
    defaultMessage: "Bất động sản được chọn chưa có chủ hộ.",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Trống",
  },
  errorEmptyLicensePlate: {
    id: `${scope}.errorEmptyLicensePlate`,
    defaultMessage: "Biển số xe không được để trống.",
  },
  selectLicensePlate: {
    id: `${scope}.selectLicensePlate`,
    defaultMessage: "Chọn biển số xe",
  },
  numberMonth: {
    id: `${scope}.numberMonth`,
    defaultMessage: "Số tháng",
  },
  thang: {
    id: `${scope}.thang`,
    defaultMessage: "tháng",
  },
  typePaymentFee: {
    id: `${scope}.typePaymentFee`,
    defaultMessage: "Loại hình tính phí",
  },
  explain: {
    id: `${scope}.explain`,
    defaultMessage: "Diễn giải",
  },
  amount: {
    id: `${scope}.amount`,
    defaultMessage: "Thành tiền",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  selectDate: {
    id: `${scope}.selectDate`,
    defaultMessage: "Chọn ngày",
  },
  dateSend: {
    id: `${scope}.dateSend`,
    defaultMessage: "Ngày gửi",
  },
  errorDateSend: {
    id: `${scope}.errorDateSend`,
    defaultMessage: "Ngày gửi không được để trống.",
  },
  selectTypePaymentFee: {
    id: `${scope}.selectTypePaymentFee`,
    defaultMessage: "Chọn loại hình tính phí",
  },
  errorTypePaymentFee: {
    id: `${scope}.errorTypePaymentFee`,
    defaultMessage: "Loại hình tính phí không được để trống.",
  },
  them: {
    id: `${scope}.them`,
    defaultMessage: "Thêm",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  editVehicle: {
    id: `${scope}.editVehicle`,
    defaultMessage: "Chỉnh sửa xe",
  },
  themXe: {
    id: `${scope}.themXe`,
    defaultMessage: "Thêm xe",
  },
  titleImportVehicle: {
    id: `${scope}.titleImportVehicle`,
    defaultMessage: "Bạn chắc chắn muốn tải lên danh sách gửi xe không?",
  },
  confirmApprove: {
    id: `${scope}.confirmApprove`,
    defaultMessage: "Bạn chắc chắn muốn duyệt các phí này không?",
  },
  confirmApproveOne: {
    id: `${scope}.confirmApproveOne`,
    defaultMessage: "Bạn chắc chắn muốn duyệt phí này không?",
  },
});
