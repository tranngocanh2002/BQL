/*
 * StaffDetail Messages
 *
 * This contains all the text for the StaffDetail container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.StaffDetail";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the StaffDetail container!",
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
    defaultMessage: "Chi tiết",
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
  notFindPage: {
    id: `${scope}.notFindPage`,
    defaultMessage: "Not found page",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: "Quay lại",
  },
  phone: {
    id: `${scope}.phone`,
    defaultMessage: "Số điện thoại",
  },
  birthday: {
    id: `${scope}.birthday`,
    defaultMessage: "Ngày sinh",
  },
  gender: {
    id: `${scope}.gender`,
    defaultMessage: "Giới tính",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  activate: {
    id: `${scope}.activate`,
    defaultMessage: "Kích hoạt",
  },
  resetPassword: {
    id: `${scope}.resetPassword`,
    defaultMessage: "Đặt lại mật khẩu",
  },
  deleteStaff: {
    id: `${scope}.deleteStaff`,
    defaultMessage: "Xóa nhân sự",
  },
  information: {
    id: `${scope}.information`,
    defaultMessage: "Thông tin",
  },
  noAvatar: {
    id: `${scope}.noAvatar`,
    defaultMessage: "Chưa có ảnh đại diện",
  },
  reset: {
    id: `${scope}.reset`,
    defaultMessage: "Đặt lại",
  },
  newPassword: {
    id: `${scope}.newPassword`,
    defaultMessage: "Mật khẩu mới",
  },
  emptyNewPassword: {
    id: `${scope}.emptyNewPassword`,
    defaultMessage: "Mật khẩu mới không được để trống.",
  },
  leastNewPassword: {
    id: `${scope}.leastNewPassword`,
    defaultMessage: "Mật khẩu mới ít nhất 8 ký tự.",
  },
  enterNewPassword: {
    id: `${scope}.enterNewPassword`,
    defaultMessage: "Nhập mật khẩu mới",
  },
  reEnterNewPassword: {
    id: `${scope}.reEnterNewPassword`,
    defaultMessage: "Nhập lại mật khẩu mới",
  },
  emptyReNewPassword: {
    id: `${scope}.emptyReNewPassword`,
    defaultMessage: "Nhập lại mật khẩu mới không được để trống.",
  },
  leastReNewPassword: {
    id: `${scope}.leastReNewPassword`,
    defaultMessage: "Nhập lại mật khẩu mới ít nhất 8 ký tự.",
  },
  passwordNotMatch: {
    id: `${scope}.passwordNotMatch`,
    defaultMessage: "Mật khẩu không trùng khớp.",
  },
  rangePassword: {
    id: `${scope}.rangePassword`,
    defaultMessage: "Mật khẩu từ 8 - 20 ký tự",
  },
  sendEmail: {
    id: `${scope}.sendEmail`,
    defaultMessage: "Gửi email thông báo cho nhân sự",
  },
  stopActivation: {
    id: `${scope}.stopActivation`,
    defaultMessage: "Dừng kích hoạt",
  },
  male: {
    id: `${scope}.male`,
    defaultMessage: "Nam",
  },
  female: {
    id: `${scope}.female`,
    defaultMessage: "Nữ",
  },
  employeeCode: {
    id: `${scope}.employeeCode`,
    defaultMessage: "Mã nhân viên",
  },
});
