/*
 * StaffList Messages
 *
 * This contains all the text for the StaffList container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.StaffList";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the StaffList container!",
  },
  deleteStaffConfirm: {
    id: `${scope}.deleteStaffConfirm`,
    defaultMessage: "Bạn có chắc chắn muốn xóa nhân sự này?",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Xác nhận",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  fullName: {
    id: `${scope}.fullName`,
    defaultMessage: "Họ và tên",
  },
  authGroup: {
    id: `${scope}.authGroup`,
    defaultMessage: "Nhóm quyền",
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
  findName: {
    id: `${scope}.findName`,
    defaultMessage: "Tìm kiếm tên",
  },
  findByName: {
    id: `${scope}.findByName`,
    defaultMessage: "Tìm kiếm nhân sự theo tên",
  },
  findEmail: {
    id: `${scope}.findEmail`,
    defaultMessage: "Tìm kiếm email",
  },
  findByEmail: {
    id: `${scope}.findByEmail`,
    defaultMessage: "Tìm kiếm nhân sự theo email",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  reloadPage: {
    id: `${scope}.reloadPage`,
    defaultMessage: "Tải lại trang",
  },
  addNewStaff: {
    id: `${scope}.addNewStaff`,
    defaultMessage: "Thêm nhân sự mới",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage:
      "Tổng số {total, plural, one {# nhân sự} other {# nhân sự}}",
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
  view: {
    id: `${scope}.view`,
    defaultMessage: "Xem chi tiết",
  },
  confirmActive: {
    id: `${scope}.confirmActive`,
    defaultMessage:
      "Bạn có chắc chắn muốn kích hoạt tài khoản nhân sự này không?",
  },
  confirmInactive: {
    id: `${scope}.confirmInactive`,
    defaultMessage:
      "Bạn có chắc chắn muốn dừng kích hoạt tài khoản nhân sự này không?",
  },
  import: {
    id: `${scope}.import`,
    defaultMessage: "Import dữ liệu",
  },
  export: {
    id: `${scope}.export`,
    defaultMessage: "Tải file import mẫu",
  },
  activate: {
    id: `${scope}.activate`,
    defaultMessage: "Kích hoạt",
  },
  stopActivation: {
    id: `${scope}.stopActivation`,
    defaultMessage: "Dừng kích hoạt",
  },
  confirmImport: {
    id: `${scope}.confirmImport`,
    defaultMessage: "Bạn có chắc chắn muốn tải lên danh sách nhân sự?",
  },
  exportStaff: {
    id: `${scope}.exportStaff`,
    defaultMessage: "Export nhân sự",
  },
  employeeCode: {
    id: `${scope}.employeeCode`,
    defaultMessage: "Mã nhân viên",
  },
  findByCode: {
    id: `${scope}.findByCode`,
    defaultMessage: "Tìm kiếm nhân sự theo mã nhân viên",
  },
});
