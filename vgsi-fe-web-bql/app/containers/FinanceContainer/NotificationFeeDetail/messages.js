/*
 * NotificationFeeDetail Messages
 *
 * This contains all the text for the NotificationFeeDetail container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.NotificationFeeDetail";

export default defineMessages({
  pageNotFound: {
    id: `${scope}.pageNotFound`,
    defaultMessage: "Không tìm thấy chi tiết thông báo.",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: "Quay lại",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  householder: {
    id: `${scope}.householder`,
    defaultMessage: "Chủ hộ",
  },
  address: {
    id: `${scope}.address`,
    defaultMessage: "Địa chỉ",
  },
  endDept: {
    id: `${scope}.endDept`,
    defaultMessage: "Nợ cuối kỳ",
  },
  notConfigure: {
    id: `${scope}.notConfigure`,
    defaultMessage: "Chưa cấu hình",
  },
  sent: {
    id: `${scope}.sent`,
    defaultMessage: "Đã gửi",
  },
  read: {
    id: `${scope}.read`,
    defaultMessage: "Đã đọc",
  },
  sendFail: {
    id: `${scope}.sendFail`,
    defaultMessage: "Gửi lỗi",
  },
  notInstallApp: {
    id: `${scope}.notInstallApp`,
    defaultMessage: "Chưa cài app",
  },
  contentSend: {
    id: `${scope}.contentSend`,
    defaultMessage: "Nội dung gửi",
  },
  title: {
    id: `${scope}.title`,
    defaultMessage: "Tiêu đề",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Tình trạng",
  },
  public: {
    id: `${scope}.public`,
    defaultMessage: "Công khai",
  },
  typeSend: {
    id: `${scope}.typeSend`,
    defaultMessage: "Hình thức gửi",
  },
  sendViaEmail: {
    id: `${scope}.sendViaEmail`,
    defaultMessage: "Gửi qua mail",
  },
  sendViaApp: {
    id: `${scope}.sendViaApp`,
    defaultMessage: "Gửi qua app (mặc định)",
  },
  sendViaSMS: {
    id: `${scope}.sendViaSMS`,
    defaultMessage: "Gửi qua SMS",
  },
  statistic: {
    id: `${scope}.statistic`,
    defaultMessage: "Thống kê",
  },
  turnSend: {
    id: `${scope}.turnSend`,
    defaultMessage: "Lượt gửi",
  },
  needSend: {
    id: `${scope}.needSend`,
    defaultMessage: "Cần gửi",
  },
  message: {
    id: `${scope}.message`,
    defaultMessage: "TIN NHẮN",
  },
  justFn: {
    id: `${scope}.justFn`,
    defaultMessage: "vừa xong",
  },
  listSend: {
    id: `${scope}.listSend`,
    defaultMessage: "Danh sách gửi",
  },
  totalPropertyCount: {
    id: `${scope}.totalPropertyCount`,
    defaultMessage: "Tổng bất động sản",
  },
  totalProperty: {
    id: `${scope}.totalProperty`,
    defaultMessage:
      "Tổng số {total, plural, one {# bất động sản} other {# bất động sản}}",
  },
});
