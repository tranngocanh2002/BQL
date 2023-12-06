/*
 * FeeList Messages
 *
 * This contains all the text for the FeeList container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.FeeList";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the FeeList container!",
  },
  modalDeleteInfo: {
    id: `${scope}.modalDeleteInfo`,
    defaultMessage:
      "Phí này đang ở trạng thái thanh toán nên không thể xóa được!",
  },
  modalDeleteConfirm: {
    id: `${scope}.modalDeleteConfirm`,
    defaultMessage: "Bạn có chắc chắn muốn xóa phí dịch vụ này không?",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  modalEditInfo: {
    id: `${scope}.modalEditInfo`,
    defaultMessage:
      "Phí này đang ở trạng thái thanh toán nên không thể chỉnh sửa được!",
  },
  service: {
    id: `${scope}.service`,
    defaultMessage: "Dịch vụ",
  },
  price: {
    id: `${scope}.price`,
    defaultMessage: "Số tiền",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Tình trạng",
  },
  daVaoDon: {
    id: `${scope}.daVaoDon`,
    defaultMessage: "Đã vào đơn:",
  },
  month: {
    id: `${scope}.month`,
    defaultMessage: "Tháng",
  },
  dayExpired: {
    id: `${scope}.dayExpired`,
    defaultMessage: "Hạn thanh toán",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  totalFee: {
    id: `${scope}.totalFee`,
    defaultMessage: "Tổng số {total} phí",
  },
  feeDescription: {
    id: `${scope}.feeDescription`,
    defaultMessage: "Mô tả phí",
  },
  feeDescriptionRequired: {
    id: `${scope}.feeDescriptionRequired`,
    defaultMessage: "Mô tả phí không được để trống.",
  },
  feeDescriptionEN: {
    id: `${scope}.feeDescriptionEN`,
    defaultMessage: "Mô tả phí (EN)",
  },
  feeDescriptionRequiredEN: {
    id: `${scope}.feeDescriptionRequiredEN`,
    defaultMessage: "Mô tả phí tiếng Anh không được để trống.",
  },
  editFeePaid: {
    id: `${scope}.editFeePaid`,
    defaultMessage: "Chỉnh sửa phí thanh toán",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  priceRequired: {
    id: `${scope}.priceRequired`,
    defaultMessage: "Số tiền không được để trống.",
  },
  monthRequired: {
    id: `${scope}.monthRequired`,
    defaultMessage: "Tháng thanh toán không được để trống.",
  },
  monthPlaceholder: {
    id: `${scope}.monthPlaceholder`,
    defaultMessage: "Chọn tháng",
  },
  dayExpiredRequired: {
    id: `${scope}.dayExpiredRequired`,
    defaultMessage: "Ngày hết hạn không được để trống.",
  },
  chooseDayPlaceholder: {
    id: `${scope}.chooseDayPlaceholder`,
    defaultMessage: "Chọn ngày",
  },
  statusRequired: {
    id: `${scope}.statusRequired`,
    defaultMessage: "Trạng thái không được để trống.",
  },
  chooseStatusPlaceholder: {
    id: `${scope}.chooseStatusPlaceholder`,
    defaultMessage: "Chọn trạng thái",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xóa",
  },
});
