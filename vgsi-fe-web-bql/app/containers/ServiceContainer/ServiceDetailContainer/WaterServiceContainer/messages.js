/*
 * WaterServiceContainer Messages
 *
 * This contains all the text for the WaterServiceContainer container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.WaterServiceContainer";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the WaterServiceContainer container!",
  },
  usage: {
    id: `${scope}.usage`,
    defaultMessage: "Sử dụng",
  },
  approvedWater: {
    id: `${scope}.approvedWater`,
    defaultMessage: "Số nước đã duyệt",
  },
  waitingApproveWater: {
    id: `${scope}.waitingApproveWater`,
    defaultMessage: "Số nước chờ duyệt",
  },
  confirmDeleteData: {
    id: `${scope}.confirmDeleteData`,
    defaultMessage: "Bạn chắc chắn muốn xóa dữ liệu này?",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  typeFee: {
    id: `${scope}.typeFee`,
    defaultMessage: "Loại tính phí",
  },
  startDate: {
    id: `${scope}.startDate`,
    defaultMessage: "Ngày bắt đầu",
  },
  endDate: {
    id: `${scope}.endDate`,
    defaultMessage: "Đóng đến ngày",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xóa",
  },
  selectProperty: {
    id: `${scope}.selectProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  refresh: {
    id: `${scope}.refresh`,
    defaultMessage: "Làm mới trang",
  },
  add: {
    id: `${scope}.add`,
    defaultMessage: "Thêm mới",
  },
  import: {
    id: `${scope}.import`,
    defaultMessage: "Import dữ liệu",
  },
  export: {
    id: `${scope}.export`,
    defaultMessage: "Tải file import mẫu",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  totalApartment: {
    id: `${scope}.totalApartment`,
    defaultMessage:
      "Tổng số {total, plural, one {# bất động sản} other {# bất động sản}}",
  },
  confirmDeletePaymentInformation: {
    id: `${scope}.confirmDeletePaymentInformation`,
    defaultMessage: "Bạn chắc chắn muốn xóa thông tin thanh toán này?",
  },
  firstIndex: {
    id: `${scope}.firstIndex`,
    defaultMessage: "Chỉ số đầu",
  },
  lastIndex: {
    id: `${scope}.lastIndex`,
    defaultMessage: "Chỉ số cuối",
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
  dateApprove: {
    id: `${scope}.dateApprove`,
    defaultMessage: "Ngày duyệt",
  },
  approveBy: {
    id: `${scope}.approveBy`,
    defaultMessage: "Người duyệt",
  },
  selectMonth: {
    id: `${scope}.selectMonth`,
    defaultMessage: "Chọn tháng",
  },
  totalFee: {
    id: `${scope}.totalFee`,
    defaultMessage: "Tổng số {total, plural, one {# phí} other {# phí}}",
  },
  deleteFee: {
    id: `${scope}.deleteFee`,
    defaultMessage: "Xóa phí",
  },
  approveAll: {
    id: `${scope}.approveAll`,
    defaultMessage: "Duyệt tất cả",
  },
  approve: {
    id: `${scope}.approve`,
    defaultMessage: "Duyệt",
  },
  addNewFee: {
    id: `${scope}.addNewFee`,
    defaultMessage: "Thêm mới phí",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  confirmApproveTotalPage: {
    id: `${scope}.confirmApproveTotalPage`,
    defaultMessage: "Bạn có chắc chắn muốn duyệt tất cả phí này không?",
  },
  confirmDeleteAllData: {
    id: `${scope}.confirmDeleteAllData`,
    defaultMessage: "Bạn có chắc chắn muốn xóa các phí này không?",
  },
  confirmDeleteOneData: {
    id: `${scope}.confirmDeleteOneData`,
    defaultMessage: "Bạn có chắc chắn muốn xóa phí này không?",
  },
  propertyRequired: {
    id: `${scope}.propertyRequired`,
    defaultMessage: "Bất động sản không được để trống.",
  },
  propertyEmpty: {
    id: `${scope}.propertyEmpty`,
    defaultMessage: "Bất động sản được chọn chưa có chủ hộ.",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Trống",
  },
  endIndex: {
    id: `${scope}.endIndex`,
    defaultMessage: "Số chốt cuối",
  },
  required: {
    id: `${scope}.required`,
    defaultMessage: "Không được để trống.",
  },
  chooseDate: {
    id: `${scope}.chooseDate`,
    defaultMessage: "Chọn ngày",
  },
  editPayment: {
    id: `${scope}.editPayment`,
    defaultMessage: "Chỉnh sửa phí thanh toán",
  },
  addPayment: {
    id: `${scope}.addPayment`,
    defaultMessage: "Tạo phí thanh toán",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  amountMoneyRequired: {
    id: `${scope}.amountMoneyRequired`,
    defaultMessage: "Số tiền không được để trống.",
  },
  monthRequired: {
    id: `${scope}.monthRequired`,
    defaultMessage: "Tháng thanh toán không được để trống.",
  },
  duePayment: {
    id: `${scope}.duePayment`,
    defaultMessage: "Hạn thanh toán",
  },
  duePaymentRequired: {
    id: `${scope}.duePaymentRequired`,
    defaultMessage: "Hạn thanh toán không được để trống.",
  },
  totalWater: {
    id: `${scope}.totalWater`,
    defaultMessage: "Tổng : {total} (m3)",
  },
  totalPrice: {
    id: `${scope}.totalPrice`,
    defaultMessage: "Thành tiền : {total} (VNĐ)",
  },
  saveDraft: {
    id: `${scope}.saveDraft`,
    defaultMessage: "Lưu nháp",
  },
  save: {
    id: `${scope}.save`,
    defaultMessage: "Lưu và duyệt",
  },
  lockDate: {
    id: `${scope}.lockDate`,
    defaultMessage: "Ngày chốt",
  },
  lockDateRequired: {
    id: `${scope}.lockDateRequired`,
    defaultMessage: "Ngày chốt không được để trống.",
  },
  chooseLockDate: {
    id: `${scope}.chooseLockDate`,
    defaultMessage: "Chọn ngày chốt",
  },
  feeOfMonth: {
    id: `${scope}.feeOfMonth`,
    defaultMessage: "Phí của tháng",
  },
  duDauKy: {
    id: `${scope}.duDauKy`,
    defaultMessage: "Số dư đầu kỳ",
  },
  duCuoiKy: {
    id: `${scope}.duCuoiKy`,
    defaultMessage: "Số dư cuối kỳ",
  },
  soDuRequired: {
    id: `${scope}.soDuRequired`,
    defaultMessage: "Số dư cuối kỳ không được để trống.",
  },
  soDuMax: {
    id: `${scope}.soDuMax`,
    defaultMessage: "Số dư không được lớn hơn 10.000.000",
  },
  totalWaterMonth: {
    id: `${scope}.totalWaterMonth`,
    defaultMessage: "Số nước tháng:",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage: "Tổng",
  },
  totalPriceRaw: {
    id: `${scope}.totalPriceRaw`,
    defaultMessage: "Thành tiền",
  },
  titleImportUsage: {
    id: `${scope}.titleImportUsage`,
    defaultMessage:
      "Bạn chắc chắn muốn tải lên danh sách bất động sản sử dụng nước không?",
  },
  titleImportWaterTemplate: {
    id: `${scope}.titleImportWaterTemplate`,
    defaultMessage: "Bạn chắc chắn muốn tải lên danh sách chốt số nước không?",
  },
  confirmApproveOne: {
    id: `${scope}.confirmApproveOne`,
    defaultMessage: "Bạn chắc chắn muốn duyệt phí này không?",
  },
  confirmApprove: {
    id: `${scope}.confirmApprove`,
    defaultMessage: "Bạn chắc chắn muốn duyệt các phí này không?",
  },
});
