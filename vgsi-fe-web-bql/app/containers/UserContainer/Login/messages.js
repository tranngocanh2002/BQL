/*
 * Login Messages
 *
 * This contains all the text for the Login container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.Login";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the Login container!",
  },
  title: {
    id: `${scope}.title`,
    defaultMessage: "Đăng nhập",
  },
  subtitle: {
    id: `${scope}.subtitle`,
    defaultMessage: "Tiếp tục với",
  },
  placeholderUserName: {
    id: `${scope}.placeholderUserName`,
    defaultMessage: "Email đăng nhập",
  },
  placeholderCaptcha: {
    id: `${scope}.placeholderCaptcha`,
    defaultMessage: "Mã captcha",
  },
  placeholderPassword: {
    id: `${scope}.placeholderPassword`,
    defaultMessage: "Mật khẩu",
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
    defaultMessage: "Quên mật khẩu?",
  },

  errorEmailInvalid: {
    id: `${scope}.errorEmailInvalid`,
    defaultMessage: "Email không hợp lệ.",
  },
  errorPasswordLength: {
    id: `${scope}.errorPasswordLength`,
    defaultMessage: "Mật khẩu dài tối thiểu 6 ký tự.",
  },
  changeCaptcha: {
    id: `${scope}.changeCaptcha`,
    defaultMessage: "Thay đổi mã captcha",
  },
  confirmLogin: {
    id: `${scope}.confirmLogin`,
    defaultMessage: "Xác nhận đăng nhập",
  },
  contentConfirm: {
    id: `${scope}.contentConfirm`,
    defaultMessage:
      "Tài khoản hiện được đăng nhập vào một thiết bị khác. Bạn có muốn tiếp tục đăng nhập vào thiết bị này không?",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
});
