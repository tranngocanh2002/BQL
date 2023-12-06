/*
 * ResetPassword Messages
 *
 * This contains all the text for the ResetPassword container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ResetPassword";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the Login container!",
  },
  title: {
    id: `${scope}.title`,
    defaultMessage: "Đặt lại mật khẩu",
  },
  subtitle: {
    id: `${scope}.subtitle`,
    defaultMessage:
      "Sử dụng mật khẩu với chữ cái viết hoa và chữ số để tăng khả năng bảo mật",
  },
  placeholderPassword: {
    id: `${scope}.placeholderPassword`,
    defaultMessage: "Mật khẩu mới",
  },
  placeholderPassword2: {
    id: `${scope}.placeholderPassword2`,
    defaultMessage: "Nhập lại mật khẩu mới",
  },
  subErroEmpty: {
    id: `${scope}.subErroEmpty`,
    defaultMessage: "không được để trống.",
  },
  captchaInValid: {
    id: `${scope}.captchaInValid`,
    defaultMessage: "Mã captcha không đúng.",
  },
  titleForgot: {
    id: `${scope}.titleForgot`,
    defaultMessage: "Quay lại",
  },
  titleBtnKhoiPhuc: {
    id: `${scope}.titleBtnKhoiPhuc`,
    defaultMessage: "Tạo",
  },
  errorPassworMatch: {
    id: `${scope}.errorPassworMatch`,
    defaultMessage: "Mật khẩu không trùng khớp.",
  },
  errorPasswordLength: {
    id: `${scope}.errorPasswordLength`,
    defaultMessage: "Mật khẩu dài tối thiểu 8 ký tự.",
  },
  success: {
    id: `${scope}.success`,
    defaultMessage: "Thành công",
  },
  redirectLogin: {
    id: `${scope}.redirectLogin`,
    defaultMessage: "Đang chuyển hướng về đăng nhập...",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Xác nhận",
  },
});
