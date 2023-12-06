/*
 * ServiceCloud Messages
 *
 * This contains all the text for the ServiceCloud container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ServiceFeeCreate";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ServiceFeeCreate container!",
  },
  emptyService: {
    id: `${scope}.emptyService`,
    defaultMessage: "Hiện tại chưa có dịch vụ cung cấp.",
  },
  addService: {
    id: `${scope}.addService`,
    defaultMessage: "Thêm dịch vụ",
  },
  managementFee: {
    id: `${scope}.managementFee`,
    defaultMessage: "Phí quản lý",
  },
  electricService: {
    id: `${scope}.electricService`,
    defaultMessage: "Dịch vụ điện",
  },
  waterService: {
    id: `${scope}.waterService`,
    defaultMessage: "Nước sinh hoạt",
  },
  parkingService: {
    id: `${scope}.parkingService`,
    defaultMessage: "Gửi xe",
  },
});
