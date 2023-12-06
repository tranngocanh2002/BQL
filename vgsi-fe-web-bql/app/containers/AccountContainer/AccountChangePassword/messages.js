/*
 * AccountChangePassword Messages
 *
 * This contains all the text for the AccountChangePassword container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.AccountChangePassword";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the AccountChangePassword container!",
  },
  oldPassword: {
    id: `${scope}.oldPassword`,
    defaultMessage: "Mật khẩu cũ",
  },
  oldPasswordRequired: {
    id: `${scope}.oldPasswordRequired`,
    defaultMessage: "Mật khẩu cũ không được để trống.",
  },
  oldPasswordMinLength: {
    id: `${scope}.oldPasswordMinLength`,
    defaultMessage: "Mật khẩu cũ phải có ít nhất 8 ký tự.",
  },
  oldPasswordPlaceholder: {
    id: `${scope}.oldPasswordPlaceholder`,
    defaultMessage: "Nhập mật khẩu cũ",
  },
  newPassword: {
    id: `${scope}.newPassword`,
    defaultMessage: "Mật khẩu mới",
  },
  newPasswordRequired: {
    id: `${scope}.newPasswordRequired`,
    defaultMessage: "Mật khẩu mới không được để trống.",
  },
  newPasswordMinLength: {
    id: `${scope}.newPasswordMinLength`,
    defaultMessage: "Mật khẩu mới phải có ít nhất 8 ký tự.",
  },
  newPasswordPlaceholder: {
    id: `${scope}.newPasswordPlaceholder`,
    defaultMessage: "Nhập mật khẩu mới",
  },
  confirmPassword: {
    id: `${scope}.confirmPassword`,
    defaultMessage: "Xác nhận mật khẩu",
  },
  confirmPasswordRequired: {
    id: `${scope}.confirmPasswordRequired`,
    defaultMessage: "Xác nhận mật khẩu không được để trống.",
  },
  confirmPasswordMinLength: {
    id: `${scope}.confirmPasswordMinLength`,
    defaultMessage: "Xác nhận mật khẩu phải có ít nhất 8 ký tự.",
  },
  confirmPasswordNotMatch: {
    id: `${scope}.confirmPasswordNotMatch`,
    defaultMessage: "Xác nhận mật khẩu không trùng khớp.",
  },
  confirmPasswordPlaceholder: {
    id: `${scope}.confirmPasswordPlaceholder`,
    defaultMessage: "Nhập xác nhận mật khẩu mới",
  },
  reNewPassword: {
    id: `${scope}.reNewPassword`,
    defaultMessage: "Nhập lại mật khẩu mới",
  },
  reNewPasswordRequired: {
    id: `${scope}.reNewPasswordRequired`,
    defaultMessage: "Nhập lại mật khẩu mới không được để trống.",
  },
  reNewPasswordMinLength: {
    id: `${scope}.reNewPasswordMinLength`,
    defaultMessage: "Nhập lại mật khẩu mới phải có ít nhất 8 ký tự.",
  },
  reNewPasswordNotMatch: {
    id: `${scope}.reNewPasswordNotMatch`,
    defaultMessage: "Mật khẩu không trùng khớp.",
  },
  reNewPasswordPlaceholder: {
    id: `${scope}.reNewPasswordPlaceholder`,
    defaultMessage: "Nhập lại mật khẩu mới",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: "Quay lại",
  },
  changePassword: {
    id: `${scope}.changePassword`,
    defaultMessage: "Đổi mật khẩu",
  },
  changePasswordSuccess: {
    id: `${scope}.changePasswordSuccess`,
    defaultMessage:
      "Mật khẩu của bạn đã được thay đổi thành công. Hệ thống sẽ yêu cầu bạn đăng nhập lại khi thông báo này được đóng.",
  },
  close: {
    id: `${scope}.close`,
    defaultMessage: "Đóng",
  },
  passwordNotMatch: {
    id: `${scope}.passwordNotMatch`,
    defaultMessage: "Mật khẩu mới không được trùng mật khẩu cũ",
  },
});
