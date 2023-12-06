/*
 * TicketList Messages
 *
 * This contains all the text for the TicketList container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.TicketList";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the TicketList container!",
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
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  title: {
    id: `${scope}.title`,
    defaultMessage: "Nội dung",
  },
  category: {
    id: `${scope}.category`,
    defaultMessage: "Danh mục",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  sender: {
    id: `${scope}.sender`,
    defaultMessage: "Người gửi",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  dayCreate: {
    id: `${scope}.dayCreate`,
    defaultMessage: "Ngày tạo",
  },
  rate: {
    id: `${scope}.rate`,
    defaultMessage: "Đánh giá",
  },
  all: {
    id: `${scope}.all`,
    defaultMessage: "Tất cả",
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
  searchTitle: {
    id: `${scope}.searchTitle`,
    defaultMessage: "Tìm kiếm tiêu đề",
  },
  searchTitleTooltip: {
    id: `${scope}.searchTitleTooltip`,
    defaultMessage: "Tìm kiếm theo tiêu đề",
  },
  chooseProperty: {
    id: `${scope}.chooseProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  reload: {
    id: `${scope}.reload`,
    defaultMessage: "Làm mới trang",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage:
      "Tổng số {total, plural, one {# phản ánh} other {# phản ánh}}",
  },
  feedbackCode: {
    id: `${scope}.feedbackCode`,
    defaultMessage: "Mã phản ánh",
  },
});
