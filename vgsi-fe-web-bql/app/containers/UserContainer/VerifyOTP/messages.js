/*
 * VerifyOTP Messages
 *
 * This contains all the text for the VerifyOTP container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.VerifyOTP";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the VerifyOTP container!",
  },
  next: {
    id: `${scope}.next`,
    defaultMessage: "Tiếp theo",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: "Quay lại",
  },
  resendOTP: {
    id: `${scope}.resendOTP`,
    defaultMessage: "Gửi lại mã xác thực",
  },
  emptyOtp: {
    id: `${scope}.emptyOtp`,
    defaultMessage: "Mã xác thực không được để trống.",
  },
  subTitle: {
    id: `${scope}.subTitle`,
    defaultMessage: "Vui lòng nhập mã xác thực được gửi qua email",
  },
  confirmOTP: {
    id: `${scope}.confirmOTP`,
    defaultMessage: "Xác thực OTP",
  },
});
