/*
 * SettingPlane Messages
 *
 * This contains all the text for the SettingPlane container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.SettingPlane";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the SettingPlane container!",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  confirmCancelEdit: {
    id: `${scope}.confirmCancelEdit`,
    defaultMessage: "Bạn chắc chắn muốn hủy sửa {cap} này?",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  close: {
    id: `${scope}.close`,
    defaultMessage: "Đóng",
  },
  confirmCancelCreate: {
    id: `${scope}.confirmCancelCreate`,
    defaultMessage: "Bạn chắc chắn muốn hủy tạo {cap} mới?",
  },
  createSuccess: {
    id: `${scope}.createSuccess`,
    defaultMessage: "Tạo {cap} thành công.",
  },
  editSuccess: {
    id: `${scope}.editSuccess`,
    defaultMessage: "Cập nhật {cap} thành công.",
  },
  editLevel: {
    id: `${scope}.editLevel`,
    defaultMessage: "Sửa thông tin {cap}",
  },
  addLevel: {
    id: `${scope}.addLevel`,
    defaultMessage: "Thêm cấp mới",
  },
  nameLevel: {
    id: `${scope}.nameLevel`,
    defaultMessage: "Tên cấp {cap}",
  },
  emptyNameLevel: {
    id: `${scope}.emptyNameLevel`,
    defaultMessage: "Tên {cap} không được để trống.",
  },
  shortName: {
    id: `${scope}.shortName`,
    defaultMessage: "Tên rút gọn",
  },
  emptyShortName: {
    id: `${scope}.emptyShortName`,
    defaultMessage: "Tên rút gọn không được để trống.",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  save: {
    id: `${scope}.save`,
    defaultMessage: "Lưu",
  },
  confirmDeleteLevel: {
    id: `${scope}.confirmDeleteLevel`,
    defaultMessage: "Bạn chắc chắn muốn xóa {title} này?",
  },
  deleteLevelSuccess: {
    id: `${scope}.deleteLevelSuccess`,
    defaultMessage: "Xóa {title} thành công.",
  },
  informationLevel: {
    id: `${scope}.informationLevel`,
    defaultMessage: "Thông tin {cap}",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xóa",
  },
  addNewLevel: {
    id: `${scope}.addNewLevel`,
    defaultMessage: "Thêm cấp {level}",
  },
  emptyAvatar: {
    id: `${scope}.emptyAvatar`,
    defaultMessage: "Ảnh đại diện không được để trống",
  },
  emptyDescription: {
    id: `${scope}.emptyDescription`,
    defaultMessage: "Mô tả không được để trống.",
  },
  info: {
    id: `${scope}.info`,
    defaultMessage: "thông tin",
  },
});
