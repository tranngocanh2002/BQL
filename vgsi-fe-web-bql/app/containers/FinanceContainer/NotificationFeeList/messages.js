/*
 * NotificationFeeList Messages
 *
 * This contains all the text for the NotificationFeeList container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.NotificationFeeList";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the NotificationFeeList container!",
  },
  confirmDeleteNotice: {
    id: `${scope}.confirmDeleteNotice`,
    defaultMessage: "Bạn chắc chắn muốn xóa thông báo này",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  title: {
    id: `${scope}.title`,
    defaultMessage: "Tiêu đề",
  },
  typeNotice: {
    id: `${scope}.typeNotice`,
    defaultMessage: "Loại thông báo",
  },
  public: {
    id: `${scope}.public`,
    defaultMessage: "Công khai",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  searchViaTitle: {
    id: `${scope}.searchViaTitle`,
    defaultMessage: "Tìm kiếm thông báo theo tiêu đề",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  refresh: {
    id: `${scope}.refresh`,
    defaultMessage: "Làm mới trang",
  },
  add: {
    id: `${scope}.add`,
    defaultMessage: "Thêm mới",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  totalNotice: {
    id: `${scope}.totalNotice`,
    defaultMessage:
      "Tổng số {total, plural, one {# thông báo} other {# thông báo}}",
  },
});
