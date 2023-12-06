/*
 * TicketDetail Messages
 *
 * This contains all the text for the TicketDetail container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.TicketDetail";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the TicketDetail container!",
  },
  modalDelete: {
    id: `${scope}.modalDelete`,
    defaultMessage: "Bạn chắc chắn muốn bỏ nhóm này không?",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Huỷ",
  },
  unauthorized: {
    id: `${scope}.unauthorized`,
    defaultMessage: "Bạn không có quyền truy cập chức năng này.",
  },
  newFeedback: {
    id: `${scope}.newFeedback`,
    defaultMessage: "Mới",
  },
  pending: {
    id: `${scope}.pending`,
    defaultMessage: "Chờ xử lý",
  },
  processing: {
    id: `${scope}.processing`,
    defaultMessage: "Đang xử lý",
  },
  processingAgain: {
    id: `${scope}.processingAgain`,
    defaultMessage: "Đang xử lý lại",
  },
  processed: {
    id: `${scope}.processed`,
    defaultMessage: "Đã xử lý",
  },
  closed: {
    id: `${scope}.closed`,
    defaultMessage: "Đã đóng",
  },
  cancelFeedback: {
    id: `${scope}.cancelFeedback`,
    defaultMessage: "Đã hủy",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  sender: {
    id: `${scope}.sender`,
    defaultMessage: "Người gửi",
  },
  category: {
    id: `${scope}.category`,
    defaultMessage: "Danh mục",
  },
  dayCreate: {
    id: `${scope}.dayCreate`,
    defaultMessage: "Ngày tạo",
  },
  evaluate: {
    id: `${scope}.evaluate`,
    defaultMessage: "Đánh giá",
  },
  receive: {
    id: `${scope}.receive`,
    defaultMessage: "Tiếp nhận",
  },
  process: {
    id: `${scope}.process`,
    defaultMessage: "Xử lý",
  },
  complete: {
    id: `${scope}.complete`,
    defaultMessage: "Hoàn thành",
  },
  content: {
    id: `${scope}.content`,
    defaultMessage: "Nội dung",
  },
  ticketCode: {
    id: `${scope}.ticketCode`,
    defaultMessage: "Mã phiếu",
  },
  answerFeedback: {
    id: `${scope}.answerFeedback`,
    defaultMessage: "Trả lời phản ánh",
  },
  internalChat: {
    id: `${scope}.internalChat`,
    defaultMessage: "Trao đổi nội bộ",
  },
  groupJoin: {
    id: `${scope}.groupJoin`,
    defaultMessage: "Nhóm tham gia",
  },
  addGroup: {
    id: `${scope}.addGroup`,
    defaultMessage: "Thêm nhóm",
  },
  addGroupProcess: {
    id: `${scope}.addGroupProcess`,
    defaultMessage: "Thêm nhóm vào xử lý",
  },
  addNew: {
    id: `${scope}.addNew`,
    defaultMessage: "Thêm mới",
  },
  groupProcess: {
    id: `${scope}.groupProcess`,
    defaultMessage: "Nhóm xử lý",
  },
  groupProcessRequired: {
    id: `${scope}.groupProcessRequired`,
    defaultMessage: "Nhóm xử lý không được để trống.",
  },
  chooseProcess: {
    id: `${scope}.chooseProcess`,
    defaultMessage: "Chọn xử lý",
  },
  resident: {
    id: `${scope}.resident`,
    defaultMessage: "Cư dân",
  },
  enterContent: {
    id: `${scope}.enterContent`,
    defaultMessage: "Nhập nội dung",
  },
  send: {
    id: `${scope}.send`,
    defaultMessage: "Gửi",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  closeFeedbackContent: {
    id: `${scope}.closeFeedbackContent`,
    defaultMessage: "Bạn chắc chắn muốn đóng phản ánh này?",
  },
  feedbackCode: {
    id: `${scope}.feedbackCode`,
    defaultMessage: "Mã phản ánh",
  },
});
