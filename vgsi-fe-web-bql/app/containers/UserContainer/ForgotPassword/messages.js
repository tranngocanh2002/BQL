/*
 * Register Messages
 *
 * This contains all the text for the Register container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.Register";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the Login container!",
  },
  title: {
    id: `${scope}.title`,
    defaultMessage: "Quên mật khẩu",
  },
  subtitle: {
    id: `${scope}.subtitle`,
    defaultMessage: "Nhập email để khôi phục lại mật khẩu",
  },
  subtitle2: {
    id: `${scope}.subtitle2`,
    defaultMessage:
      "Hệ thống đã gửi thông tin khôi phục mật khẩu tới e-mail của bạn.",
  },
  placeholderUserName: {
    id: `${scope}.placeholderUserName`,
    defaultMessage: "Nhập email để khôi phục lại mật khẩu",
  },
  placeholderCaptcha: {
    id: `${scope}.placeholderCaptcha`,
    defaultMessage: "Mã captcha",
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
    defaultMessage: "Khôi phục",
  },
  titleResend: {
    id: `${scope}.titleResend`,
    defaultMessage: "Tôi chưa nhận được e-mail, gửi lại!",
  },
  titleLogin: {
    id: `${scope}.titleLogin`,
    defaultMessage: "Đăng nhập",
  },
  errorEmailInvalid: {
    id: `${scope}.errorEmailInvalid`,
    defaultMessage: "Email không hợp lệ",
  },
  success: {
    id: `${scope}.success`,
    defaultMessage: "Thành công",
  },
  redirectLogin: {
    id: `${scope}.redirectLogin`,
    defaultMessage: "Đang chuyển hướng về đăng nhập...",
  },
  changeCaptcha: {
    id: `${scope}.changeCaptcha`,
    defaultMessage: "Thay đổi mã captcha",
  },
  address: {
    id: `${scope}.address`,
    defaultMessage: "Địa chỉ:",
  },
  continue: {
    id: `${scope}.continue`,
    defaultMessage: "Tiếp theo",
  },
});
