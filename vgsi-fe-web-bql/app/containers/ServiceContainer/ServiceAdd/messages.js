/*
 * ServiceAdd Messages
 *
 * This contains all the text for the ServiceAdd container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ServiceAdd";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ServiceAdd container!",
  },
  serviceName: {
    id: `${scope}.serviceName`,
    defaultMessage: "Tên dịch vụ",
  },
  errorEmptyServiceName: {
    id: `${scope}.errorEmptyServiceName`,
    defaultMessage: "Tên dịch vụ không được để trống.",
  },
  contractor: {
    id: `${scope}.contractor`,
    defaultMessage: "Nhà cung cấp",
  },
  errorEmptySupplier: {
    id: `${scope}.errorEmptySupplier`,
    defaultMessage: "Nhà cung cấp không được để trống.",
  },
  selectSupplier: {
    id: `${scope}.selectSupplier`,
    defaultMessage: "Chọn nhà cung cấp",
  },
  introduce: {
    id: `${scope}.introduce`,
    defaultMessage: "Giới thiệu",
  },
  backBtn: {
    id: `${scope}.backBtn`,
    defaultMessage: "Quay lại",
  },
  addBtn: {
    id: `${scope}.addBtn`,
    defaultMessage: "Thêm",
  },
});
