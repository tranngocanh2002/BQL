/*
 * Roles Messages
 *
 * This contains all the text for the Roles container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.Roles";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the Roles container!",
  },
  errorEmpty: {
    id: `${scope}.errorEmpty`,
    defaultMessage: "{messageError} không được để trống.",
  },
  nameGroup: {
    id: `${scope}.nameGroup`,
    defaultMessage: "Tên nhóm",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  save: {
    id: `${scope}.save`,
    defaultMessage: "Lưu",
  },
  confirmCancel: {
    id: `${scope}.confirmCancel`,
    defaultMessage: "Bạn có chắc muốn hủy?",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  confirmDeleteGroup: {
    id: `${scope}.confirmDeleteGroup`,
    defaultMessage: "Bạn có chắc muốn xóa nhóm này?",
  },
  deleteGroupSuccess: {
    id: `${scope}.deleteGroupSuccess`,
    defaultMessage: "Xóa nhóm thành công.",
  },
  editGroupSuccess: {
    id: `${scope}.editGroupSuccess`,
    defaultMessage: "Chỉnh sửa thành công.",
  },
  addGroupSuccess: {
    id: `${scope}.addGroupSuccess`,
    defaultMessage: "Thêm nhóm thành công.",
  },
  addGroup: {
    id: `${scope}.addGroup`,
    defaultMessage: "Thêm nhóm",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
});
