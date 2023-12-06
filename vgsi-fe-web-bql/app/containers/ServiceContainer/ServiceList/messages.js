/*
 * ServiceList Messages
 *
 * This contains all the text for the ServiceList container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ServiceList";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ServiceList container!",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Hiện tại chưa có dịch vụ cung cấp nên chưa phát sinh phí.",
  },
  add: {
    id: `${scope}.add`,
    defaultMessage: "Thêm dịch vụ",
  },
  feeList: {
    id: `${scope}.feeList`,
    defaultMessage: "Danh sách phí",
  },
});
