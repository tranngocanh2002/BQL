/*
 * ElectricServiceContainer Messages
 *
 * This contains all the text for the ElectricServiceContainer container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ElectricServiceContainer";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ElectricServiceContainer container!",
  },
  usage: {
    id: `${scope}.usage`,
    defaultMessage: "Sử dụng",
  },
  approvedElectricity: {
    id: `${scope}.approvedElectricity`,
    defaultMessage: "Số điện đã duyệt",
  },
  notApprovedElectricity: {
    id: `${scope}.notApprovedElectricity`,
    defaultMessage: "Số điện chưa duyệt",
  },
  confirmDelete: {
    id: `${scope}.confirmDelete`,
    defaultMessage: "Bạn chắc chắn muốn xóa dữ liệu này?",
  },
  okText: {
    id: `${scope}.okText`,
    defaultMessage: "Đồng ý",
  },
  cancelText: {
    id: `${scope}.cancelText`,
    defaultMessage: "Hủy",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  loaiTinhPhi: {
    id: `${scope}.loaiTinhPhi`,
    defaultMessage: "Loại tính phí",
  },
  soChotCuoi: {
    id: `${scope}.soChotCuoi`,
    defaultMessage: "Số chốt cuối",
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
  plhProperty: {
    id: `${scope}.plhProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Trống",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  refreshPage: {
    id: `${scope}.refreshPage`,
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
  totalProperty: {
    id: `${scope}.totalProperty`,
    defaultMessage:
      "Tổng số {total, plural, one {# bất động sản} other {# bất động sản}}",
  },
  confirmDeletePayment: {
    id: `${scope}.confirmDeletePayment`,
    defaultMessage: "Bạn chắc chắn muốn xóa phí này?",
  },
  headIndex: {
    id: `${scope}.headIndex`,
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
  cash: {
    id: `${scope}.cash`,
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
  approvedBy: {
    id: `${scope}.approvedBy`,
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
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  deleteFee: {
    id: `${scope}.deleteFee`,
    defaultMessage: "Xóa phí",
  },
  addFee: {
    id: `${scope}.addFee`,
    defaultMessage: "Thêm mới phí",
  },
  create: {
    id: `${scope}.create`,
    defaultMessage: "Tạo mới",
  },
  them: {
    id: `${scope}.them`,
    defaultMessage: "Thêm",
  },
  errorEmptyProperty: {
    id: `${scope}.errorEmptyProperty`,
    defaultMessage: "Bất động sản không được để trống.",
  },
  errorEmptyCurrentProperty: {
    id: `${scope}.errorEmptyCurrentProperty`,
    defaultMessage: "Bất động sản được chọn chưa có chủ hộ.",
  },
  errorEmpty: {
    id: `${scope}.errorEmpty`,
    defaultMessage: "Không được để trống.",
  },
  selectDate: {
    id: `${scope}.selectDate`,
    defaultMessage: "Chọn ngày",
  },
  editFee: {
    id: `${scope}.editFee`,
    defaultMessage: "Chỉnh sửa phí thanh toán",
  },
  createFee: {
    id: `${scope}.createFee`,
    defaultMessage: "Tạo phí thanh toán",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  errorEmptyCash: {
    id: `${scope}.errorEmptyCash`,
    defaultMessage: "Số tiền không được để trống.",
  },
  errorMonthPayment: {
    id: `${scope}.errorMonthPayment`,
    defaultMessage: "Tháng thanh toán không được để trống.",
  },
  expirePayment: {
    id: `${scope}.expirePayment`,
    defaultMessage: "Hạn thanh toán",
  },
  errorExpiredPayment: {
    id: `${scope}.errorExpiredPayment`,
    defaultMessage: "Hạn thanh toán không được để trống.",
  },
  saveAndApprove: {
    id: `${scope}.saveAndApprove`,
    defaultMessage: "Lưu và Duyệt",
  },
  closeDate: {
    id: `${scope}.closeDate`,
    defaultMessage: "Ngày chốt",
  },
  errorEmptyCloseDate: {
    id: `${scope}.errorEmptyCloseDate`,
    defaultMessage: "Ngày chốt không được để trống.",
  },
  selectCloseDate: {
    id: `${scope}.selectCloseDate`,
    defaultMessage: "Chọn ngày chốt",
  },
  feeOfMonth: {
    id: `${scope}.feeOfMonth`,
    defaultMessage: "Phí của tháng",
  },
  sdDauKy: {
    id: `${scope}.sdDauKy`,
    defaultMessage: "Số dư đầu kỳ",
  },
  sdCuoiKy: {
    id: `${scope}.sdCuoiKy`,
    defaultMessage: "Số dư cuối kỳ",
  },
  errorSoDu: {
    id: `${scope}.errorSoDu`,
    defaultMessage: "Số dư cuối kỳ không được để trống.",
  },
  errorLimitSoDu: {
    id: `${scope}.errorLimitSoDu`,
    defaultMessage: "Số dư không được lớn hơn 10.000.000",
  },
  explain: {
    id: `${scope}.explain`,
    defaultMessage: "Diễn giải",
  },
  electricityMonth: {
    id: `${scope}.electricityMonth`,
    defaultMessage: "Số điện tháng",
  },
  consume: {
    id: `${scope}.consume`,
    defaultMessage: "Tiêu thụ",
  },
  amount: {
    id: `${scope}.amount`,
    defaultMessage: "Thành tiền",
  },
  titleImportUsage: {
    id: `${scope}.titleImportUsage`,
    defaultMessage:
      "Bạn chắc chắn muốn tải lên danh sách bất động sản sử dụng điện không?",
  },
  titleImportFeeTemplate: {
    id: `${scope}.titleImportFeeTemplate`,
    defaultMessage: "Bạn chắc chắn muốn tải lên danh sách chốt số điện không?",
  },
  saveDraft: {
    id: `${scope}.saveDraft`,
    defaultMessage: "Lưu nháp",
  },
  approve: {
    id: `${scope}.approve`,
    defaultMessage: "Duyệt",
  },
  confirmApproveOne: {
    id: `${scope}.confirmApproveOne`,
    defaultMessage: "Bạn chắc chắn muốn duyệt phí này không?",
  },
  confirmApprove: {
    id: `${scope}.confirmApprove`,
    defaultMessage: "Bạn chắc chắn muốn duyệt các phí này không?",
  },
  approveAll: {
    id: `${scope}.approveAll`,
    defaultMessage: "Duyệt tất cả",
  },
  confirmApproveAll: {
    id: `${scope}.confirmApproveAll`,
    defaultMessage: "Bạn chắc chắn muốn duyệt tất cả phí này không?",
  },
  confirmDeleteFee: {
    id: `${scope}.confirmDeleteFee`,
    defaultMessage: "Bạn chắc chắn muốn xóa phí này không?",
  },
  confirmDeleteAllFee: {
    id: `${scope}.confirmDeleteAllFee`,
    defaultMessage: "Bạn chắc chắn muốn xóa các phí này không?",
  },
});
