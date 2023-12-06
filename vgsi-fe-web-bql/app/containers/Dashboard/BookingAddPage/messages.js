/*
 * BookingAdd Messages
 *
 * This contains all the text for the BookingAdd container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.BookingAdd";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the BookingAdd container!",
  },
  bookingValid: {
    id: `${scope}.bookingValid`,
    defaultMessage: "Vui lòng cấu hình tiện ích này trước khi tạo booking",
  },
  apartment: {
    id: `${scope}.Apartment`,
    defaultMessage: "Bất động sản",
  },
  emptyApartmentError: {
    id: `${scope}.emptyApartmentError`,
    defaultMessage: "Bất động sản không được để trống.",
  },
  noOwnerApartment: {
    id: `${scope}.noOwnerApartment`,
    defaultMessage: "Bất động sản được chọn chưa có chủ sở hữu.",
  },
  choseApartment: {
    id: `${scope}.choseApartment`,
    defaultMessage: "Chọn Bất động sản",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Trống",
  },
  utilities: {
    id: `${scope}.utilities`,
    defaultMessage: "Tiện ích",
  },
  emptyUtilitiesError: {
    id: `${scope}.emptyUtilitiesError`,
    defaultMessage: "Dịch vụ không được để trống",
  },
  choseUtilities: {
    id: `${scope}.choseUtilities`,
    defaultMessage: "Chọn dịch vụ",
  },
  date: {
    id: `${scope}.date`,
    defaultMessage: "Ngày sử dụng",
  },
  emptyDateError: {
    id: `${scope}.emptyDateError`,
    defaultMessage: "Ngày sử dụng không được để trống.",
  },
  choseDate: {
    id: `${scope}.choseDate`,
    defaultMessage: "Chọn ngày",
  },
  area: {
    id: `${scope}.area`,
    defaultMessage: "Khu vực",
  },
  emptyAreaError: {
    id: `${scope}.emptyAreaError`,
    defaultMessage: "Khu vực không được để trống.",
  },
  choseArea: {
    id: `${scope}.choseArea`,
    defaultMessage: "Chọn khu vực",
  },
  usedTime: {
    id: `${scope}.usedTime`,
    defaultMessage: "Thời gian sử dụng",
  },
  usedTimeError: {
    id: `${scope}.usedTimeError`,
    defaultMessage: "Thời gian sử dụng phải chọn tối thiểu 1 khoảng.",
  },
  noTime: {
    id: `${scope}.noTime`,
    defaultMessage: "Chưa có khung giờ để đặt dịch vụ",
  },
  numberPeople: {
    id: `${scope}.numberPeople`,
    defaultMessage: "Số người",
  },
  numberPeopleError: {
    id: `${scope}.numberPeopleError`,
    defaultMessage: "Số người không được để trống và lớn hơn 0.",
  },
  note: {
    id: `${scope}.note`,
    defaultMessage: "Ghi chú",
  },
  explain: {
    id: `${scope}.explain`,
    defaultMessage: "Diễn giải",
  },
  totalMoney: {
    id: `${scope}.totalMoney`,
    defaultMessage: "Tổng giá tiền cần thanh toán",
  },
  deposit: {
    id: `${scope}.deposit`,
    defaultMessage: "Tiền đặt cọc",
  },
  cancelContent: {
    id: `${scope}.cancelContent`,
    defaultMessage: "Bạn chắc chắn muốn huỷ",
  },
  okText: {
    id: `${scope}.okText`,
    defaultMessage: "Đồng ý",
  },
  cancelText: {
    id: `${scope}.cancelText`,
    defaultMessage: "Hủy",
  },
  createNew: {
    id: `${scope}.createNew`,
    defaultMessage: "Tạo mới",
  },
});
