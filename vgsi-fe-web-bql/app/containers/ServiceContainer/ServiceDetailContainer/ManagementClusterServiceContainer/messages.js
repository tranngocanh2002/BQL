/*
 * ManagementClusterServiceContainer Messages
 *
 * This contains all the text for the ManagementClusterServiceContainer container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ManagementClusterServiceContainer";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ManagementClusterServiceContainer container!",
  },
  usage: {
    id: `${scope}.usage`,
    defaultMessage: "Sử dụng",
  },
  approvedFee: {
    id: `${scope}.approvedFee`,
    defaultMessage: "Phí đã duyệt",
  },
  waitApproveFee: {
    id: `${scope}.waitApproveFee`,
    defaultMessage: "Phí chờ duyệt",
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
  area: {
    id: `${scope}.area`,
    defaultMessage: "Diện tích",
  },
  apartmentDateReceive: {
    id: `${scope}.apartmentDateReceive`,
    defaultMessage: "Ngày nhận nhà",
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
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
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
  confirmApprove: {
    id: `${scope}.confirmApprove`,
    defaultMessage:
      "Bạn có chắc chắn muốn duyệt tất cả {total, plural, one {# phí thanh toán} other {# phí thanh toán}}?",
  },
  confirmDeleteAllFee: {
    id: `${scope}.confirmDeleteAllFee`,
    defaultMessage: "Bạn có chắc chắn muốn xóa các phí này không?",
  },
  confirmDeleteOneFee: {
    id: `${scope}.confirmDeleteOneFee`,
    defaultMessage: "Bạn có chắc chắn muốn xóa phí này không?",
  },
  deleteAllFee: {
    id: `${scope}.deleteAllFee`,
    defaultMessage: "Xóa tất cả ",
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
  draft: {
    id: `${scope}.draft`,
    defaultMessage: "Lưu nháp",
  },
  saveAndApprove: {
    id: `${scope}.saveAndApprove`,
    defaultMessage: "Lưu và duyệt",
  },
  numberMonth: {
    id: `${scope}.numberMonth`,
    defaultMessage: "Số tháng",
  },
  thang: {
    id: `${scope}.thang`,
    defaultMessage: "tháng",
  },
  explain: {
    id: `${scope}.explain`,
    defaultMessage: "Diễn giải",
  },
  amount: {
    id: `${scope}.amount`,
    defaultMessage: "Thành tiền",
  },
  confirmApproveOneFee: {
    id: `${scope}.confirmApproveOneFee`,
    defaultMessage: "Bạn có chắc chắn muốn duyệt phí thanh toán này?",
  },
  confirmApproveFee: {
    id: `${scope}.confirmApproveFee`,
    defaultMessage: "Bạn có chắc chắn muốn duyệt các phí thanh toán này?",
  },
  titleImportUsage: {
    id: `${scope}.titleImportUsage`,
    defaultMessage:
      "Bạn chắc chắn muốn tải lên danh sách bất động sản sử dụng dịch vụ quản lý không?",
  },
  notHave: {
    id: `${scope}.notHave`,
    defaultMessage: "Chưa có",
  },
});
