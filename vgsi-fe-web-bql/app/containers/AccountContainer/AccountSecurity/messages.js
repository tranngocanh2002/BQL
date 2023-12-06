/*
 * AccountSecurity Messages
 *
 * This contains all the text for the AccountSecurity container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.AccountSecurity";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the AccountSecurity container!",
  },
  configSecurity: {
    id: `${scope}.configSecurity`,
    defaultMessage: "Cài đặt bảo mật",
  },
  password: {
    id: `${scope}.password`,
    defaultMessage: "Mật khẩu",
  },
  passwordStrength: {
    id: `${scope}.passwordStrength`,
    defaultMessage: "Độ mạnh mật khẩu hiện tại：",
  },
  strong: {
    id: `${scope}.strong`,
    defaultMessage: "Mạnh",
  },
  changePassword: {
    id: `${scope}.changePassword`,
    defaultMessage: "Đổi mật khẩu",
  },
});
