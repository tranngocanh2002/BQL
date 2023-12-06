/*
 * Home Messages
 *
 * This contains all the text for the Home container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.Home";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the Home container!",
  },
  errorView: {
    id: `${scope}.errorView`,
    defaultMessage:
      "Không có quyền vào trang này. Vui lòng liên hệ với ban quản lý.",
  },
  vietnam: {
    id: `${scope}.vietnam`,
    defaultMessage: "Việt Nam",
  },
  foreigner: {
    id: `${scope}.foreigner`,
    defaultMessage: "Nước ngoài",
  },
  noResident: {
    id: `${scope}.noResident`,
    defaultMessage: "Chưa có cư dân",
  },
  young: {
    id: `${scope}.young`,
    defaultMessage: "Từ 0 - 14 tuổi",
  },
  adult: {
    id: `${scope}.adult`,
    defaultMessage: "Từ 15 - 54 tuổi",
  },
  old: {
    id: `${scope}.old`,
    defaultMessage: "Từ 55 tuổi trở lên",
  },
  people: {
    id: `${scope}.people`,
    defaultMessage: "Cư dân",
  },
  resident: {
    id: `${scope}.resident`,
    defaultMessage: "Cư dân",
  },
  feedback: {
    id: `${scope}.feedback`,
    defaultMessage: "Phản ánh",
  },
  month: {
    id: `${scope}.month`,
    defaultMessage: "Tháng",
  },
  choseMonth: {
    id: `${scope}.choseMonth`,
    defaultMessage: "Chọn tháng",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage: "Tổng",
  },
  noFeedback: {
    id: `${scope}.noFeedback`,
    defaultMessage: "Chưa có phản ánh",
  },
  totalReceived: {
    id: `${scope}.totalReceived`,
    defaultMessage: "Tổng đã thu",
  },
  totalRevenue: {
    id: `${scope}.totalRevenue`,
    defaultMessage: "Tổng doanh thu",
  },
  fromMonth: {
    id: `${scope}.fromMonth`,
    defaultMessage: "Từ tháng",
  },
  toMonth: {
    id: `${scope}.toMonth`,
    defaultMessage: "Đến tháng",
  },
  noRevenue: {
    id: `${scope}.noRevenue`,
    defaultMessage: "Chưa có doanh thu",
  },
  money: {
    id: `${scope}.money`,
    defaultMessage: "Số tiền",
  },
  noApartType: {
    id: `${scope}.noApartType`,
    defaultMessage: "Chưa có loại bất động sản",
  },
  noBuilding: {
    id: `${scope}.noBuilding`,
    defaultMessage: "Chưa có toà nhà",
  },
  house: {
    id: `${scope}.house`,
    defaultMessage: "Căn",
  },
  houseNumber: {
    id: `${scope}.houseNumber`,
    defaultMessage: "Số căn",
  },
  noHouse: {
    id: `${scope}.noHouse`,
    defaultMessage: "Chưa có bất động sản",
  },
  installApp: {
    id: `${scope}.installApp`,
    defaultMessage: "Cài app",
  },
  noInstall: {
    id: `${scope}.noInstall`,
    defaultMessage: "Chưa cài",
  },
  totalResident: {
    id: `${scope}.totalResident`,
    defaultMessage: "Tổng cư dân",
  },
  success: {
    id: `${scope}.success`,
    defaultMessage: "Đã xác nhận",
  },
  other: {
    id: `${scope}.other`,
    defaultMessage: "Khác",
  },
  reservations: {
    id: `${scope}.reservations`,
    defaultMessage: "Đặt chỗ",
  },
  today: {
    id: `${scope}.today`,
    defaultMessage: "Hôm nay",
  },
  processing: {
    id: `${scope}.processing`,
    defaultMessage: "Đang xử lý",
  },
  resolved: {
    id: `${scope}.resolved`,
    defaultMessage: "Đã xử lý",
  },
  inhabited: {
    id: `${scope}.inhabited`,
    defaultMessage: "Đang ở",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Trống",
  },
  totalApart: {
    id: `${scope}.totalApart`,
    defaultMessage: "Tổng bất động sản",
  },
  normal: {
    id: `${scope}.normal`,
    defaultMessage: "Loại thường",
  },
  paid: {
    id: `${scope}.paid`,
    defaultMessage: "Loại phí",
  },
  notification: {
    id: `${scope}.notification`,
    defaultMessage: "Thông báo",
  },
  apartment: {
    id: `${scope}.apartment`,
    defaultMessage: "Bất động sản",
  },
  noService: {
    id: `${scope}.noService`,
    defaultMessage: "Không có thông tin dịch vụ",
  },
  amount: {
    id: `${scope}.amount`,
    defaultMessage: "Số lượng",
  },
  fixed: {
    id: `${scope}.fixed`,
    defaultMessage: "Đã bảo trì",
  },
  notFixed: {
    id: `${scope}.notFixed`,
    defaultMessage: "Chưa bảo trì",
  },
  maintenance: {
    id: `${scope}.maintenance`,
    defaultMessage: "Bảo trì thiết bị",
  },
  noMaintenance: {
    id: `${scope}.noMaintenance`,
    defaultMessage: "Không có thông tin bảo trì",
  },
  device: {
    id: `${scope}.device`,
    defaultMessage: "{total} thiết bị",
  },
  from: {
    id: `${scope}.from`,
    defaultMessage: "Từ",
  },
  to: {
    id: `${scope}.to`,
    defaultMessage: "Đến",
  },
  formRegister: {
    id: `${scope}.formRegister`,
    defaultMessage: "Đăng ký biểu mẫu",
  },
  statusHandover: {
    id: `${scope}.statusHandover`,
    defaultMessage: "Tình trạng bàn giao",
  },
  handover: {
    id: `${scope}.handover`,
    defaultMessage: "Đã bàn giao",
  },
  notHandover: {
    id: `${scope}.notHandover`,
    defaultMessage: "Chưa bàn giao",
  },
});
