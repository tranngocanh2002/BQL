/*
 * TicketCategory Messages
 *
 * This contains all the text for the TicketCategory container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.TicketCategory";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the TicketCategory container!",
  },
  modalDelete: {
    id: `${scope}.modalDelete`,
    defaultMessage: "Bạn chắc chắn muốn xoá danh mục này ?",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Huỷ",
  },
  color: {
    id: `${scope}.color`,
    defaultMessage: "Màu",
  },
  nameType: {
    id: `${scope}.nameType`,
    defaultMessage: "Tên loại",
  },
  groupProcess: {
    id: `${scope}.groupProcess`,
    defaultMessage: "Nhóm xử lý",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xoá",
  },
  reload: {
    id: `${scope}.reload`,
    defaultMessage: "Làm mới trang",
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
  createCategory: {
    id: `${scope}.createCategory`,
    defaultMessage: "Tạo danh mục mới",
  },
  editCategory: {
    id: `${scope}.editCategory`,
    defaultMessage: "Chỉnh sửa danh mục",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  categoryName: {
    id: `${scope}.categoryName`,
    defaultMessage: "Tên danh mục",
  },
  categoryNameRequired: {
    id: `${scope}.categoryNameRequired`,
    defaultMessage: "Tên danh mục không được để trống.",
  },
  categoryNameEn: {
    id: `${scope}.categoryNameEn`,
    defaultMessage: "Tên danh mục (EN)",
  },
  categoryNameEnRequired: {
    id: `${scope}.categoryNameEnRequired`,
    defaultMessage: "Tên danh mục tiếng Anh không được để trống.",
  },
  chooseColor: {
    id: `${scope}.chooseColor`,
    defaultMessage: "Chọn màu",
  },
  chooseColorRequired: {
    id: `${scope}.chooseColorRequired`,
    defaultMessage: "Màu không được để trống.",
  },
  groupProcessRequired: {
    id: `${scope}.groupProcessRequired`,
    defaultMessage: "Nhóm xử lý không được để trống.",
  },
  groupProcessPlaceholder: {
    id: `${scope}.groupProcessPlaceholder`,
    defaultMessage: "Chọn nhóm xử lý",
  },
});
