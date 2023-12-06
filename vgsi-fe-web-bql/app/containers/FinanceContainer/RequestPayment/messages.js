/*
 * RequestPayment Messages
 *
 * This contains all the text for the RequestPayment container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.RequestPayment";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the RequestPayment container!",
  },
  requestDate: {
    id: `${scope}.requestDate`,
    defaultMessage: "Ngày yêu cầu",
  },
  requestCode: {
    id: `${scope}.requestCode`,
    defaultMessage: "Mã yêu cầu",
  },
  source: {
    id: `${scope}.source`,
    defaultMessage: "Nguồn tạo",
  },
  system: {
    id: `${scope}.system`,
    defaultMessage: "Hệ thống",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  customerName: {
    id: `${scope}.customerName`,
    defaultMessage: "Chủ hộ",
  },
  amountMoney: {
    id: `${scope}.amountMoney`,
    defaultMessage: "Số tiền",
  },
  creator: {
    id: `${scope}.creator`,
    defaultMessage: "Người tạo",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  createVote: {
    id: `${scope}.createVote`,
    defaultMessage: "Tạo phiếu",
  },
  cancelRequest: {
    id: `${scope}.cancelRequest`,
    defaultMessage: "Hủy yêu cầu",
  },
  receiptVoucher: {
    id: `${scope}.receiptVoucher`,
    defaultMessage: "Phiếu thu",
  },
  voidVoucher: {
    id: `${scope}.voidVoucher`,
    defaultMessage: "Phiếu hủy",
  },
  confirmPayment: {
    id: `${scope}.confirmPayment`,
    defaultMessage: "Xác nhận thanh toán",
  },
  selectProperty: {
    id: `${scope}.selectProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  week: {
    id: `${scope}.week`,
    defaultMessage: "Tuần",
  },
  year: {
    id: `${scope}.year`,
    defaultMessage: "Năm",
  },
  fromDate: {
    id: `${scope}.fromDate`,
    defaultMessage: "Từ ngày",
  },
  toDate: {
    id: `${scope}.toDate`,
    defaultMessage: "Đến ngày",
  },
  totalPage: {
    id: `${scope}.totalPage`,
    defaultMessage: "Tổng số {total}",
  },
  month: {
    id: `${scope}.month`,
    defaultMessage: "Tháng",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  unpaid: {
    id: `${scope}.unpaid`,
    defaultMessage: "Chưa thanh toán",
  },
  paid: {
    id: `${scope}.paid`,
    defaultMessage: "Đã thanh toán",
  },
  denied: {
    id: `${scope}.denied`,
    defaultMessage: "Bị từ chối",
  },
  residentCancel: {
    id: `${scope}.residentCancel`,
    defaultMessage: "Cư dân hủy",
  },
  confirmDeleteRequestPayment: {
    id: `${scope}.confirmDeleteRequestPayment`,
    defaultMessage:
      "Bạn có chắc chắn muốn từ chối yêu cầu thanh toán này không?",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  enterReason: {
    id: `${scope}.enterReason`,
    defaultMessage: "Nhập lý do",
  },
  emptyReason: {
    id: `${scope}.emptyReason`,
    defaultMessage: "Lý do không được để trống.",
  },
  cancelled: {
    id: `${scope}.cancelled`,
    defaultMessage: "Đã hủy",
  },
  wasDenied: {
    id: `${scope}.wasDenied`,
    defaultMessage: "Bị từ chối",
  },
  waitForConfirmation: {
    id: `${scope}.waitForConfirmation`,
    defaultMessage: "Chờ xác nhận",
  },
  reject: {
    id: `${scope}.reject`,
    defaultMessage: "Từ chối",
  },
  reasonDenied: {
    id: `${scope}.reasonDenied`,
    defaultMessage: "Lý do từ chối",
  },
  done: {
    id: `${scope}.done`,
    defaultMessage: "Hoàn thành",
  },
});
