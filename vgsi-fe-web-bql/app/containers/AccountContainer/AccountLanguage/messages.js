/*
 * AccountLanguage Messages
 *
 * This contains all the text for the AccountLanguage container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.AccountLanguage";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the AccountLanguage container!",
  },
  language: {
    id: `${scope}.language`,
    defaultMessage: "Ngôn ngữ",
  },
  chooseLanguagePlaceholder: {
    id: `${scope}.chooseLanguagePlaceholder`,
    defaultMessage: "Chọn ngôn ngữ hiển thị cho hệ thống",
  },
  chooseLanguageTooltip: {
    id: `${scope}.chooseLanguageTooltip`,
    defaultMessage:
      "Sau khi nhấn cập nhật, hệ thống sẽ tự động tải lại trang và hiển thị bằng ngôn ngữ đã chọn.",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
});
