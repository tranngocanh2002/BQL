/*
 * SupplierContainer Messages
 *
 * This contains all the text for the SupplierContainer container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.SupplierContainer";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the SupplierContainer container!",
  },
  supplierName: {
    id: `${scope}.supplierName`,
    defaultMessage: "Tên nhà thầu",
  },
  emptySupplierName: {
    id: `${scope}.emptySupplierName`,
    defaultMessage: "Tên nhà thầu không được để trống.",
  },
  address: {
    id: `${scope}.address`,
    defaultMessage: "Địa chỉ",
  },
  emptyAddress: {
    id: `${scope}.emptyAddress`,
    defaultMessage: "Địa chỉ không được để trống.",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  emptyDescription: {
    id: `${scope}.emptyDescription`,
    defaultMessage: "Địa chỉ không được để trống.",
  },
  attachFile: {
    id: `${scope}.attachFile`,
    defaultMessage: "Tệp đính kèm",
  },
  exceedFile: {
    id: `${scope}.exceedFile`,
    defaultMessage: "Tệp đính kèm vượt quá 10MB",
  },
  upload: {
    id: `${scope}.upload`,
    defaultMessage: "Tải tệp lên",
  },
  ruleFile: {
    id: `${scope}.ruleFile`,
    defaultMessage:
      "Định dạng .doc, .docx, .pdf, .xls, .xlsx không vượt quá 10MB",
  },
  fullName: {
    id: `${scope}.fullName`,
    defaultMessage: "Họ và tên",
  },
  informationContact: {
    id: `${scope}.informationContact`,
    defaultMessage: "Thông tin người liên hệ",
  },
  emptyFullName: {
    id: `${scope}.emptyFullName`,
    defaultMessage: "Họ và tên không được để trống.",
  },
  phone: {
    id: `${scope}.phone`,
    defaultMessage: "Số điện thoại",
  },
  emptyPhone: {
    id: `${scope}.emptyPhone`,
    defaultMessage: "Số điện thoại không được để trống.",
  },
  formatPhone: {
    id: `${scope}.formatPhone`,
    defaultMessage: "Số điện thoại không đúng định dạng.",
  },
  emptyEmail: {
    id: `${scope}.emptyEmail`,
    defaultMessage: "Email không được để trống.",
  },
  formatEmail: {
    id: `${scope}.formatEmail`,
    defaultMessage: "Email không đúng định dạng.",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  add: {
    id: `${scope}.add`,
    defaultMessage: "Thêm mới",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  informationSupplier: {
    id: `${scope}.informationSupplier`,
    defaultMessage: "THÔNG TIN NHÀ THẦU",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  active: {
    id: `${scope}.active`,
    defaultMessage: "Đang hoạt động",
  },
  inactive: {
    id: `${scope}.inactive`,
    defaultMessage: "Dừng hoạt động",
  },
  nameContact: {
    id: `${scope}.nameContact`,
    defaultMessage: "Họ và tên người liên hệ",
  },
  createdAt: {
    id: `${scope}.createdAt`,
    defaultMessage: "Ngày tạo",
  },
  option: {
    id: `${scope}.option`,
    defaultMessage: "Tùy chọn",
  },
  detail: {
    id: `${scope}.detail`,
    defaultMessage: "Chi tiết",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xóa",
  },
  contractor: {
    id: `${scope}.contractor`,
    defaultMessage: "Nhà thầu",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  refresh: {
    id: `${scope}.refresh`,
    defaultMessage: "Làm mới trang",
  },
  addSupplier: {
    id: `${scope}.addSupplier`,
    defaultMessage: "Thêm nhà thầu",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  totalSupplier: {
    id: `${scope}.totalSupplier`,
    defaultMessage:
      "Tổng số {total, plural, one {# nhà thầu} other {# nhà thầu}}",
  },
  confirmDelete: {
    id: `${scope}.confirmDelete`,
    defaultMessage: "Bạn có chắc chắn muốn xóa nhà thầu này không?",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  phoneContact: {
    id: `${scope}.phoneContact`,
    defaultMessage: "Số điện thoại người liên hệ",
  },
  emailContact: {
    id: `${scope}.emailContact`,
    defaultMessage: "Email",
  },
  btnBack: {
    id: `${scope}.btnBack`,
    defaultMessage: "Quay lại",
  },
  invalid: {
    id: `${scope}.invalid`,
    defaultMessage: "không hợp lệ",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
});
