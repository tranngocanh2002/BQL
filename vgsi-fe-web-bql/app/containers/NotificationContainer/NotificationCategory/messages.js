/*
 * NotificationCategory Messages
 *
 * This contains all the text for the NotificationCategory container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.NotificationCategory";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the NotificationCategory container!",
  },
  deleteModalTitle: {
    id: `${scope}.deleteModalTitle`,
    defaultMessage: "Bạn chắc chắn muốn xoá loại danh mục thông báo này?",
  },
  okText: {
    id: `${scope}.okText`,
    defaultMessage: "Đồng ý",
  },
  cancelText: {
    id: `${scope}.cancelText`,
    defaultMessage: "Hủy",
  },
  color: {
    id: `${scope}.color`,
    defaultMessage: "Màu",
  },
  categoryName: {
    id: `${scope}.categoryName`,
    defaultMessage: "Tên danh mục",
  },
  categoryType: {
    id: `${scope}.categoryType`,
    defaultMessage: "Loại danh mục",
  },
  regularAnnouncement: {
    id: `${scope}.regularAnnouncement`,
    defaultMessage: "Thông báo thường",
  },
  feeAnnouncement: {
    id: `${scope}.feeAnnouncement`,
    defaultMessage: "Thông báo phí",
  },
  countAnnouncement: {
    id: `${scope}.countAnnouncement`,
    defaultMessage: "Tổng tin",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xoá",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  reloadPage: {
    id: `${scope}.reloadPage`,
    defaultMessage: "Tải lại trang",
  },
  addNew: {
    id: `${scope}.addNew`,
    defaultMessage: "Thêm mới",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage:
      "Tổng số {total, plural, one {# danh mục} other {# danh mục}}",
  },
  cantUpdate: {
    id: `${scope}.cantUpdate`,
    defaultMessage: "Bạn không thể cập nhật thành danh mục thông báo phí",
  },
  warning: {
    id: `${scope}.warning`,
    defaultMessage: "Bạn chỉ có thể tạo 1 danh mục thông báo phí",
  },
  editCategory: {
    id: `${scope}.editCategory`,
    defaultMessage: "Chỉnh sửa danh mục",
  },
  createCategory: {
    id: `${scope}.createCategory`,
    defaultMessage: "Tạo danh mục mới",
  },
  categoryNameRequired: {
    id: `${scope}.categoryNameRequired`,
    defaultMessage: "Tên danh mục không được để trống",
  },
  categoryNameEn: {
    id: `${scope}.categoryNameEn`,
    defaultMessage: "Tên danh mục (EN)",
  },
  categoryNameEnRequired: {
    id: `${scope}.categoryNameEnRequired`,
    defaultMessage: "Tên danh mục tiếng Anh không được để trống",
  },
  categoryTypeRequired: {
    id: `${scope}.categoryTypeRequired`,
    defaultMessage: "Loại danh mục không được để trống",
  },
  chooseColor: {
    id: `${scope}.chooseColor`,
    defaultMessage: "Chọn màu",
  },
  create: {
    id: `${scope}.create`,
    defaultMessage: "Tạo mới",
  },
  surveyAnnouncement: {
    id: `${scope}.surveyAnnouncement`,
    defaultMessage: "Thông báo khảo sát",
  },
});
