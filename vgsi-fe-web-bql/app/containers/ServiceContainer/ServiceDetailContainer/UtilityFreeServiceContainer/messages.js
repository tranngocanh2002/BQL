/*
 * UtilityFreeServiceContainer Messages
 *
 * This contains all the text for the UtilityFreeServiceContainer container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.UtilityFreeServiceContainer";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the UtilityFreeServiceContainer container!",
  },
  feeList: {
    id: `${scope}.feeList`,
    defaultMessage: "Danh sách phí",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: "Quay lại",
  },
  validateContent: {
    id: `${scope}.validateContent`,
    defaultMessage: "Nội dung không được dài quá 2000 ký tự.",
  },
  nameUtility: {
    id: `${scope}.nameUtility`,
    defaultMessage: "Tên tiện ích",
  },
  ruleNameUtility: {
    id: `${scope}.ruleNameUtility`,
    defaultMessage: "Tên tiện ích không được để trống.",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  ruleDescription: {
    id: `${scope}.ruleDescription`,
    defaultMessage: "Mô tả không được để trống.",
  },
  regulation: {
    id: `${scope}.rule`,
    defaultMessage: "Quy định",
  },
  ruleRegulation: {
    id: `${scope}.ruleRegulation`,
    defaultMessage: "Quy định không được để trống.",
  },
  type: {
    id: `${scope}.type`,
    defaultMessage: "Loại hình tính phí sử dụng tiện ích",
  },
  ruleType: {
    id: `${scope}.ruleType`,
    defaultMessage: "Vui lòng chọn loại hình dịch vụ",
  },
  hourOpen: {
    id: `${scope}.hourOpen`,
    defaultMessage: "Giờ mở cửa",
  },
  ruleHourOpen: {
    id: `${scope}.ruleHourOpen`,
    defaultMessage: "Giờ mở cửa không được để trống.",
  },
  hourClose: {
    id: `${scope}.hourClose`,
    defaultMessage: "Giờ đóng cửa",
  },
  ruleHourClose: {
    id: `${scope}.ruleHourClose`,
    defaultMessage: "Giờ đóng cửa không được để trống.",
  },
  waitingTimeApprove: {
    id: `${scope}.waitingTimeApprove`,
    defaultMessage: "Thời gian chờ duyệt",
  },
  ruleWaitingTimeApprove: {
    id: `${scope}.ruleWaitingTimeApprove`,
    defaultMessage: "Thời gian chờ duyệt không được để trống.",
  },
  utility: {
    id: `${scope}.utility`,
    defaultMessage: "Tiện ích",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  codeBooking: {
    id: `${scope}.codeBooking`,
    defaultMessage: "Mã đặt chỗ",
  },
  createReceipt: {
    id: `${scope}.createReceipt`,
    defaultMessage: "Tạo phiếu thu",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  paid: {
    id: `${scope}.paid`,
    defaultMessage: "Đã thanh toán",
  },
  unpaid: {
    id: `${scope}.unpaid`,
    defaultMessage: "Chưa thanh toán",
  },
  amountMoney: {
    id: `${scope}.amountMoney`,
    defaultMessage: "Số tiền",
  },
  deposit: {
    id: `${scope}.deposit`,
    defaultMessage: "Đặt cọc",
  },
  incurred: {
    id: `${scope}.incurred`,
    defaultMessage: "Phát sinh",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  selectProperty: {
    id: `${scope}.selectProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  selectUtility: {
    id: `${scope}.selectUtility`,
    defaultMessage: "Chọn tiện ích",
  },
  condition: {
    id: `${scope}.condition`,
    defaultMessage: "Tình trạng TT",
  },
  startDate: {
    id: `${scope}.start`,
    defaultMessage: "Bắt đầu",
  },
  endDate: {
    id: `${scope}.endDate`,
    defaultMessage: "Kết thúc",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  refresh: {
    id: `${scope}.refresh`,
    defaultMessage: "Làm mới trang",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  totalBook: {
    id: `${scope}.totalBook`,
    defaultMessage:
      "Tổng số {total, plural, one {# lượt book} other {# lượt book}}",
  },
  bookingCancel: {
    id: `${scope}.bookingCancel`,
    defaultMessage: "Đã hủy bởi cư dân",
  },
  canceled: {
    id: `${scope}.canceled`,
    defaultMessage: "Bị từ chối",
  },
  systemCancel: {
    id: `${scope}.systemCancel`,
    defaultMessage: "Đã hủy bởi hệ thống",
  },
  pending: {
    id: `${scope}.pending`,
    defaultMessage: "Chờ duyệt",
  },
  confirmed: {
    id: `${scope}.confirmed`,
    defaultMessage: "Đã xác nhận",
  },
  totalMoney: {
    id: `${scope}.totalMoney`,
    defaultMessage: "Tổng tiền",
  },
});
