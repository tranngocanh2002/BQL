/*
 * OldDebitServiceContainer Messages
 *
 * This contains all the text for the OldDebitServiceContainer container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.OldDebitServiceContainer";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the OldDebitServiceContainer container!",
  },
  oldDeptApproved: {
    id: `${scope}.oldDeptApproved`,
    defaultMessage: "Nợ cũ đã duyệt",
  },
  oldDeptNotApproved: {
    id: `${scope}.oldDeptNotApproved`,
    defaultMessage: "Nợ cũ chưa duyệt",
  },
  confirmDelete: {
    id: `${scope}.confirmDelete`,
    defaultMessage: "Bạn chắc chắn muốn xóa phí này không?",
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
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  deleteFee: {
    id: `${scope}.deleteFee`,
    defaultMessage: "Xóa phí",
  },
  selectProperty: {
    id: `${scope}.selectProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  refreshPage: {
    id: `${scope}.refreshPage`,
    defaultMessage: "Làm mới trang",
  },
  addFee: {
    id: `${scope}.addFee`,
    defaultMessage: "Thêm mới phí",
  },
  import: {
    id: `${scope}.import`,
    defaultMessage: "Import dữ liệu",
  },
  export: {
    id: `${scope}.export`,
    defaultMessage: "Tải file import mẫu",
  },
  approve: {
    id: `${scope}.approve`,
    defaultMessage: "Duyệt",
  },
  approveAll: {
    id: `${scope}.approveAll`,
    defaultMessage: "Duyệt tất cả",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  totalFee: {
    id: `${scope}.totalFee`,
    defaultMessage: "Tổng số {total, plural, one {# phí} other {# phí}}",
  },
  confirmDeleteData: {
    id: `${scope}.confirmDeleteData`,
    defaultMessage: "Bạn có chắc chắn muốn xóa các phí này không?",
  },
  confirmDeleteOneData: {
    id: `${scope}.confirmDeleteOneData`,
    defaultMessage: "Bạn có chắc chắn muốn xóa phí này không?",
  },
  confirmApproveAll: {
    id: `${scope}.confirmApproveAll`,
    defaultMessage: "Bạn có chắc chắn muốn duyệt tất cả phí này không?",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  emptyMoney: {
    id: `${scope}.emptyMoney`,
    defaultMessage: "Số tiền không được để trống.",
  },
  money: {
    id: `${scope}.money`,
    defaultMessage: "Số tiền",
  },
  selectMonth: {
    id: `${scope}.selectMonth`,
    defaultMessage: "Chọn tháng",
  },
  errorFeeOfMonth: {
    id: `${scope}.errorFeeOfMonth`,
    defaultMessage: "Tháng thanh toán không được để trống.",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Trống",
  },
  errorEmptyProperty: {
    id: `${scope}.errorEmptyProperty`,
    defaultMessage: "Bất động sản không được để trống.",
  },
  errorEmptyCurrentProperty: {
    id: `${scope}.errorEmptyCurrentProperty`,
    defaultMessage: "Bất động sản được chọn chưa có chủ hộ.",
  },
  saveAndApprove: {
    id: `${scope}.saveAndApprove`,
    defaultMessage: "Lưu và duyệt",
  },
  draft: {
    id: `${scope}.draft`,
    defaultMessage: "Lưu nháp",
  },
  editFee: {
    id: `${scope}.editFee`,
    defaultMessage: "Chỉnh sửa phí thanh toán",
  },
  createFee: {
    id: `${scope}.createFee`,
    defaultMessage: "Tạo phí thanh toán",
  },
  approveDate: {
    id: `${scope}.approveDate`,
    defaultMessage: "Ngày duyệt",
  },
  titleImportOldFeeTemplate: {
    id: `${scope}.titleImportOldFeeTemplate`,
    defaultMessage: "Bạn chắc chắn muốn tải lên danh sách nợ cũ không?",
  },
  confirmApproveOne: {
    id: `${scope}.confirmApproveOne`,
    defaultMessage: "Bạn có chắc chắn muốn duyệt phí này không?",
  },
  confirmApprove: {
    id: `${scope}.confirmApprove`,
    defaultMessage: "Bạn có chắc chắn muốn duyệt các phí này không?",
  },
});
