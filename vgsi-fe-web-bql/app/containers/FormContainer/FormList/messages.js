/*
 * FormList Messages
 *
 * This contains all the text for the FormList container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.FormList";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the FormList container!",
  },
  cancelFormContent: {
    id: `${scope}.cancelFormContent`,
    defaultMessage: "Bạn có chắc muốn từ chối đăng ký biểu mẫu này không?",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  reasonRequest: {
    id: `${scope}.reasonRequest`,
    defaultMessage: "Vui lòng nhập lý do từ chối!",
  },
  reasonPlaceholder: {
    id: `${scope}.reasonPlaceholder`,
    defaultMessage: "Nhập lý do",
  },
  approveRequest: {
    id: `${scope}.approveRequest`,
    defaultMessage:
      "Bạn có chắc chắn muốn phê duyệt đăng ký biểu mẫu này không?",
  },
  stt: {
    id: `${scope}.stt`,
    defaultMessage: "STT",
  },
  propertyCode: {
    id: `${scope}.propertyCode`,
    defaultMessage: "Tên bất động sản",
  },
  creator: {
    id: `${scope}.creator`,
    defaultMessage: "Người tạo",
  },
  form: {
    id: `${scope}.form`,
    defaultMessage: "Biểu mẫu",
  },
  createAt: {
    id: `${scope}.createAt`,
    defaultMessage: "Ngày tạo",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  waitingApprove: {
    id: `${scope}.waitingApprove`,
    defaultMessage: "Chờ duyệt",
  },
  approve: {
    id: `${scope}.approve`,
    defaultMessage: "Đã xác nhận",
  },
  deny: {
    id: `${scope}.deny`,
    defaultMessage: "Từ chối",
  },
  formDetail: {
    id: `${scope}.formDetail`,
    defaultMessage: "Chi tiết biểu mẫu",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  findApartment: {
    id: `${scope}.findApartment`,
    defaultMessage: "Tìm kiếm theo tên bất động sản",
  },
  findUserName: {
    id: `${scope}.findUserName`,
    defaultMessage: "Tìm kiếm theo người tạo",
  },
  carRegister: {
    id: `${scope}.carRegister`,
    defaultMessage: "Đăng ký thẻ xe",
  },
  residentRegister: {
    id: `${scope}.residentRegister`,
    defaultMessage: "Đăng ký thẻ cư dân",
  },
  transferRegister: {
    id: `${scope}.transferRegister`,
    defaultMessage: "Đăng ký chuyển tài sản",
  },
  accessRegister: {
    id: `${scope}.accessRegister`,
    defaultMessage: "Đăng ký thẻ ra vào",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Không có dữ liệu",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage: "Tổng số",
  },
  reload: {
    id: `${scope}.reload`,
    defaultMessage: "Làm mới trang",
  },
  cancelled: {
    id: `${scope}.cancelled`,
    defaultMessage: "Đã hủy",
  },
  registerTransfer: {
    id: `${scope}.registerTransfer`,
    defaultMessage: "Đăng ký chuyển tài sản",
  },
  registerAccessCard: {
    id: `${scope}.registerAccessCard`,
    defaultMessage: "Đăng ký thẻ thi công",
  },
  registerResidentCard: {
    id: `${scope}.registerResidentCard`,
    defaultMessage: "Đăng ký thẻ Meyhomes",
  },
  registerCarCard: {
    id: `${scope}.registerCarCard`,
    defaultMessage: "Đăng ký thẻ xe",
  },
  approve2: {
    id: `${scope}.approve2`,
    defaultMessage: "Phê duyệt",
  },
});
