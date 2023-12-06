/*
 * AccountContainer Messages
 *
 * This contains all the text for the AccountContainer container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.AccountContainer";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the AccountContainer container!",
  },
  basic: {
    id: `${scope}.basic`,
    defaultMessage: "Cơ bản",
  },
  information: {
    id: `${scope}.information`,
    defaultMessage: "Thông tin",
  },
  security: {
    id: `${scope}.security`,
    defaultMessage: "Bảo mật",
  },
  language: {
    id: `${scope}.language`,
    defaultMessage: "Ngôn ngữ",
  },
  password: {
    id: `${scope}.password`,
    defaultMessage: "Mật khẩu",
  },
});
