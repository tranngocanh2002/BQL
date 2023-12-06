/*
 * NotificationFeeUpdate Messages
 *
 * This contains all the text for the RequestPayment container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.NotificationFeeUpdate";

export default defineMessages({
  save: {
    id: `${scope}.save`,
    defaultMessage: "Lưu",
  },
  saveNoticeSuccess: {
    id: `${scope}.saveNoticeSuccess`,
    defaultMessage: "Lưu thông báo thành công.",
  },
  public: {
    id: `${scope}.public`,
    defaultMessage: "Công khai",
  },
  publicNoticeSuccess: {
    id: `${scope}.publicNoticeSuccess`,
    defaultMessage: "Công khai thông báo thành công.",
  },
  draft: {
    id: `${scope}.draft`,
    defaultMessage: "Lưu nháp",
  },
  totalProperty: {
    id: `${scope}.totalProperty`,
    defaultMessage:
      "Tổng số {total, plural, one {# bất động sản} other {# bất động sản}}",
  },
  totalPropertyCount: {
    id: `${scope}.totalPropertyCount`,
    defaultMessage: "Tổng bất động sản",
  },
  listSend: {
    id: `${scope}.listSend`,
    defaultMessage: "Danh sách gửi",
  },
  emptySMS: {
    id: `${scope}.emptySMS`,
    defaultMessage: "Nội dung SMS không được để trống.",
  },
  contentSMS: {
    id: `${scope}.contentSMS`,
    defaultMessage: "Nội dung SMS",
  },
  sendViaSMS: {
    id: `${scope}.sendViaSMS`,
    defaultMessage: "Gửi qua SMS",
  },
  sendViaApp: {
    id: `${scope}.sendViaApp`,
    defaultMessage: "Gửi qua app (mặc định)",
  },
  sendViaEmail: {
    id: `${scope}.sendViaEmail`,
    defaultMessage: "Gửi qua email",
  },
  ruleType: {
    id: `${scope}.ruleType`,
    defaultMessage: "Cần chọn tối thiểu 1 hình thức gửi.",
  },
  typeSend: {
    id: `${scope}.typeSend`,
    defaultMessage: "Hình thức gửi",
  },
  justFn: {
    id: `${scope}.justFn`,
    defaultMessage: "vừa xong",
  },
  message: {
    id: `${scope}.message`,
    defaultMessage: "TIN NHẮN",
  },
  emptySendTo: {
    id: `${scope}.emptySendTo`,
    defaultMessage: "Gửi tới không được để trống.",
  },
  sendTo: {
    id: `${scope}.sendTo`,
    defaultMessage: "Gửi tới",
  },
  ruleFile: {
    id: `${scope}.ruleFile`,
    defaultMessage:
      "Định dạng .doc, .docx, .pdf, .xls, .xlsx không vượt quá 25MB",
  },
  downloadFile: {
    id: `${scope}.downloadFile`,
    defaultMessage: "Tải tệp",
  },
  fileAttach: {
    id: `${scope}.fileAttach`,
    defaultMessage: "Tệp đính kèm",
  },
  imageAttach: {
    id: `${scope}.imageAttach`,
    defaultMessage: "Ảnh đính kèm",
  },
  emptyTimeEvent: {
    id: `${scope}.emptyTimeEvent`,
    defaultMessage: "Thời gian sự kiện không được để trống.",
  },
  event: {
    id: `${scope}.event`,
    defaultMessage: "Sự kiện",
  },
  tooltipEvent: {
    id: `${scope}.tooltipEvent`,
    defaultMessage:
      "Thời điểm diễn ra sự kiện, hệ thống sẽ thông báo trước 1 ngày diễn ra sự kiện cho cư dân",
  },
  selectDate: {
    id: `${scope}.selectDate`,
    defaultMessage: "Chọn ngày",
  },
  ruleTimePublic: {
    id: `${scope}.ruleTimePublic`,
    defaultMessage: "Thời gian công khai không được để trống.",
  },
  publicAt: {
    id: `${scope}.publicAt`,
    defaultMessage: "Công khai vào lúc",
  },
  publicNow: {
    id: `${scope}.publicNow`,
    defaultMessage: "Công khai ngay",
  },
  selectNoticeCategory: {
    id: `${scope}.selectNoticeCategory`,
    defaultMessage: "Chọn danh mục thông báo",
  },
  emptyCategory: {
    id: `${scope}.emptyCategory`,
    defaultMessage: "Danh mục không được để trống.",
  },
  category: {
    id: `${scope}.category`,
    defaultMessage: "Danh mục",
  },
  emptyContent: {
    id: `${scope}.emptyContent`,
    defaultMessage: "Nội dung không được để trống.",
  },
  content: {
    id: `${scope}.content`,
    defaultMessage: "Nội dung",
  },
  emptyTitle: {
    id: `${scope}.emptyTitle`,
    defaultMessage: "Tiêu đề không được để trống.",
  },
  title: {
    id: `${scope}.title`,
    defaultMessage: "Tiêu đề",
  },
  contentSend: {
    id: `${scope}.contentSend`,
    defaultMessage: "Nội dung gửi",
  },
  create: {
    id: `${scope}.create`,
    defaultMessage: "Tạo mới",
  },
  notConfiguration: {
    id: `${scope}.notConfiguration`,
    defaultMessage: "Chưa cấu hình",
  },
  installedApp: {
    id: `${scope}.installedApp`,
    defaultMessage: "Đã cài app",
  },
  notInstalledApp: {
    id: `${scope}.notInstalledApp`,
    defaultMessage: "Chưa cài app",
  },
  address: {
    id: `${scope}.address`,
    defaultMessage: "Địa chỉ",
  },
  householder: {
    id: `${scope}.householder`,
    defaultMessage: "Chủ hộ",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  notFoundPage: {
    id: `${scope}.notFoundPage`,
    defaultMessage: "Không tìm thấy trang.",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  continue: {
    id: `${scope}.continue`,
    defaultMessage: "Tiếp tục",
  },
  contentNotice: {
    id: `${scope}.contentNotice`,
    defaultMessage:
      "Thông báo sẽ được gửi đến toàn bộ bất động sản đã chọn. Bạn có chắc chắn muốn tiếp tục?",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Xác nhận",
  },
  selectListToSend: {
    id: `${scope}.selectListToSend`,
    defaultMessage: "Chọn danh sách để gửi",
  },
});
