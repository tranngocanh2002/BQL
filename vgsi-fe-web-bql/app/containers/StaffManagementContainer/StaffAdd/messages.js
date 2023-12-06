/*
 * StaffAdd Messages
 *
 * This contains all the text for the StaffAdd container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.StaffAdd";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the StaffAdd container!",
  },
  notFindPage: {
    id: `${scope}.notFindPage`,
    defaultMessage: "Không tìm thấy trang.",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: "Quay lại",
  },
  fullName: {
    id: `${scope}.fullName`,
    defaultMessage: "Họ và tên",
  },
  fullNameRequired: {
    id: `${scope}.fullNameRequired`,
    defaultMessage: "Họ và tên không được để trống.",
  },
  emailRequired: {
    id: `${scope}.emailRequired`,
    defaultMessage: "Email không được để trống.",
  },
  emailInvalid: {
    id: `${scope}.emailInvalid`,
    defaultMessage: "Email không đúng định dạng.",
  },
  phone: {
    id: `${scope}.phone`,
    defaultMessage: "Số điện thoại",
  },
  phoneRequired: {
    id: `${scope}.phoneRequired`,
    defaultMessage: "Số điện thoại không được để trống.",
  },
  phoneInvalid: {
    id: `${scope}.phoneInvalid`,
    defaultMessage: "Số điện thoại không đúng định dạng.",
  },
  authGroup: {
    id: `${scope}.authGroup`,
    defaultMessage: "Nhóm quyền",
  },
  authGroupRequired: {
    id: `${scope}.authGroupRequired`,
    defaultMessage: "Nhóm quyền không được để trống.",
  },
  chooseAuthGroup: {
    id: `${scope}.chooseAuthGroup`,
    defaultMessage: "Chọn nhóm quyền",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  addStaff: {
    id: `${scope}.addStaff`,
    defaultMessage: "Thêm nhân sự",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  birthday: {
    id: `${scope}.birthday`,
    defaultMessage: "Ngày sinh",
  },
  gender: {
    id: `${scope}.gender`,
    defaultMessage: "Giới tính",
  },
  male: {
    id: `${scope}.male`,
    defaultMessage: "Nam",
  },
  female: {
    id: `${scope}.female`,
    defaultMessage: "Nữ",
  },
  selectDate: {
    id: `${scope}.selectDate`,
    defaultMessage: "Chọn ngày",
  },
  information: {
    id: `${scope}.information`,
    defaultMessage: "Thông tin",
  },
  nameInvalid: {
    id: `${scope}.nameInvalid`,
    defaultMessage: "Họ và tên không đúng định dạng.",
  },
  employeeCode: {
    id: `${scope}.employeeCode`,
    defaultMessage: "Mã nhân viên",
  },
  employeeCodeInvalid: {
    id: `${scope}.employeeCodeInvalid`,
    defaultMessage: "Mã nhân viên không đúng định dạng.",
  },
  employeeCodeRequired: {
    id: `${scope}.employeeCodeRequired`,
    defaultMessage: "Mã nhân viên không được để trống.",
  },
  selectGender: {
    id: `${scope}.selectGender`,
    defaultMessage: "Chọn giới tính",
  },
});
