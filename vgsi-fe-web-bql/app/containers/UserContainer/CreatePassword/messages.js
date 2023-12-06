/*
 * CreatePassword Messages
 *
 * This contains all the text for the CreatePassword container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.CreatePassword";

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
    defaultMessage: "Mật khẩu",
  },
  placeholderPassword2: {
    id: `${scope}.placeholderPassword2`,
    defaultMessage: "Nhập mật khẩu",
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
    defaultMessage: "Xác nhận mật khẩu mới không trùng khớp.",
  },
  errorPasswordLength: {
    id: `${scope}.errorPasswordLength`,
    defaultMessage: "Mật khẩu dài tối thiểu 6 ký tự.",
  },
  success: {
    id: `${scope}.success`,
    defaultMessage: "Thành công",
  },
  redirectLogin: {
    id: `${scope}.redirectLogin`,
    defaultMessage: "Đang chuyển hướng về đăng nhập...",
  },
});
