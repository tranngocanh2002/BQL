/*
 * ServiceProvider Messages
 *
 * This contains all the text for the ServiceProvider container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ServiceProvider";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ServiceProvider container!",
  },
  supplierName: {
    id: `${scope}.supplierName`,
    defaultMessage: "Tên nhà cung cấp",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Tình trạng",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  deleteSupplier: {
    id: `${scope}.deleteSupplier`,
    defaultMessage: "Xóa nhà cung cấp",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xóa",
  },
  add: {
    id: `${scope}.add`,
    defaultMessage: "Thêm mới",
  },
  contractor: {
    id: `${scope}.contractor`,
    defaultMessage: "Nhà cung cấp",
  },
  searchSupplierViaName: {
    id: `${scope}.searchSupplierViaName`,
    defaultMessage: "Tìm kiếm nhà cung cấp theo tên",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  totalSuppliers: {
    id: `${scope}.totalSuppliers`,
    defaultMessage:
      "Tổng số {total,plural, one {# nhà cung cấp} other {# nhà cung cấp}}",
  },
});
