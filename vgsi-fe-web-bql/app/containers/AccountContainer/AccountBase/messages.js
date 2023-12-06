/*
 * AccountBase Messages
 *
 * This contains all the text for the AccountBase container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.AccountBase";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the AccountBase container!",
  },
  information: {
    id: `${scope}.information`,
    defaultMessage: "Thông tin",
  },
  name: {
    id: `${scope}.name`,
    defaultMessage: "Họ và tên",
  },
  nameRequired: {
    id: `${scope}.nameRequired`,
    defaultMessage: "Họ và tên không được để trống.",
  },
  phone: {
    id: `${scope}.phone`,
    defaultMessage: "Số điện thoại",
  },
  phonePlaceholder: {
    id: `${scope}.phonePlaceholder`,
    defaultMessage: "Nhập số điện thoại",
  },
  birthday: {
    id: `${scope}.birthday`,
    defaultMessage: "Ngày sinh",
  },
  birthdayRequired: {
    id: `${scope}.birthdayRequired`,
    defaultMessage: "Ngày sinh không được để trống.",
  },
  chooseDate: {
    id: `${scope}.chooseDate`,
    defaultMessage: "Chọn ngày",
  },
  gender: {
    id: `${scope}.gender`,
    defaultMessage: "Giới tính",
  },
  chooseGenderPlaceholder: {
    id: `${scope}.chooseGenderPlaceholder`,
    defaultMessage: "Vui lòng chọn giới tính",
  },
  male: {
    id: `${scope}.male`,
    defaultMessage: "Nam",
  },
  female: {
    id: `${scope}.female`,
    defaultMessage: "Nữ",
  },
  other: {
    id: `${scope}.other`,
    defaultMessage: "Khác",
  },
  updateInfo: {
    id: `${scope}.updateInfo`,
    defaultMessage: "Cập nhật",
  },
  avatar: {
    id: `${scope}.avatar`,
    defaultMessage: "Ảnh đại diện",
  },
  onlyUploadImage: {
    id: `${scope}.onlyUploadImage`,
    defaultMessage: "Bạn chỉ có thể tải lên ảnh!",
  },
  imageTooLarge: {
    id: `${scope}.imageTooLarge`,
    defaultMessage: "Ảnh tải lên vượt quá 10MB",
  },
  notFormatImage: {
    id: `${scope}.notFormatImage`,
    defaultMessage: "Ảnh không đúng định dạng.",
  },
  changeAvatar: {
    id: `${scope}.changeAvatar`,
    defaultMessage: "Thay ảnh đại diện",
  },
  authGroup: {
    id: `${scope}.authGroup`,
    defaultMessage: "Nhóm quyền",
  },
  phoneInvalid: {
    id: `${scope}.phoneInvalid`,
    defaultMessage: "Số điện thoại không đúng định dạng.",
  },
  phoneRequired: {
    id: `${scope}.phoneRequired`,
    defaultMessage: "Số điện thoại không được để trống.",
  },
});
