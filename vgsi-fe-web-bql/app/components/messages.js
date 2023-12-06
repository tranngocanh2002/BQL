import { defineMessages } from "react-intl";

export const scope = "app.containers.ModalImport";

export default defineMessages({
  line: {
    id: `${scope}.line`,
    defaultMessage: "Dòng",
  },
  contentError: {
    id: `${scope}.contentError`,
    defaultMessage: "Nội dung lỗi",
  },
  detailError: {
    id: `${scope}.detailError`,
    defaultMessage: "Chi tiết lỗi",
  },
  close: {
    id: `${scope}.close`,
    defaultMessage: "Đóng",
  },
  importData: {
    id: `${scope}.importData`,
    defaultMessage: "Import dữ liệu",
  },
  chooseFileImport: {
    id: `${scope}.chooseFileImport`,
    defaultMessage: "Chọn file tải lên",
  },
  uploadingFile: {
    id: `${scope}.uploadingFile`,
    defaultMessage: "Đang tải file lên ...",
  },
  importingFile: {
    id: `${scope}.importingFile`,
    defaultMessage: "Đang import dữ liệu ...",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage: "Tổng số",
  },
  success: {
    id: `${scope}.success`,
    defaultMessage: "Thành công",
  },
  error: {
    id: `${scope}.error`,
    defaultMessage: "Lỗi",
  },
  chooseImage: {
    id: `${scope}.chooseImage`,
    defaultMessage: "Chọn ảnh",
  },
  replaceImage: {
    id: `${scope}.replaceImage`,
    defaultMessage: "Thay ảnh",
  },
  chooseImageFromDevice: {
    id: `${scope}.chooseImageFromDevice`,
    defaultMessage: "Chọn ảnh từ thiết bị",
  },
  onlyImage: {
    id: `${scope}.onlyImage`,
    defaultMessage: "Bạn chỉ có thể tải lên ảnh!",
  },
  imageOverSize: {
    id: `${scope}.imageOverSize`,
    defaultMessage: "Ảnh tải lên vượt quá {size}MB",
  },
});
