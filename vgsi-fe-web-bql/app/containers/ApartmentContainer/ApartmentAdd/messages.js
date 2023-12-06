/*
 * ApartmentAdd Messages
 *
 * This contains all the text for the ApartmentAdd container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ApartmentAdd";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ApartmentAdd container!",
  },
  cancelAddTitle: {
    id: `${scope}.cancelAddTitle`,
    defaultMessage: "Bạn có chắc chắn muốn hủy thêm bất động sản này không?",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  cancelEditTitle: {
    id: `${scope}.cancelEditTitle`,
    defaultMessage:
      "Bạn có chắc chắn muốn hủy chỉnh sửa bất động sản này không?",
  },
  info: {
    id: `${scope}.info`,
    defaultMessage: "Thông tin bất động sản",
  },
  propertyInfo: {
    id: `${scope}.propertyInfo`,
    defaultMessage: "Thông tin bất động sản",
  },
  propertyName: {
    id: `${scope}.propertyName`,
    defaultMessage: "Tên bất động sản",
  },
  propertyNameRequired: {
    id: `${scope}.propertyNameRequired`,
    defaultMessage: "Tên bất động sản không được để trống",
  },
  address: {
    id: `${scope}.address`,
    defaultMessage: "Địa chỉ",
  },
  addressRequired: {
    id: `${scope}.addressRequired`,
    defaultMessage: "Địa chỉ không được để trống",
  },
  chooseAddress: {
    id: `${scope}.chooseAddress`,
    defaultMessage: "Chọn địa chỉ",
  },
  propertyType: {
    id: `${scope}.propertyType`,
    defaultMessage: "Loại bất động sản",
  },
  propertyTypeRequired: {
    id: `${scope}.propertyTypeRequired`,
    defaultMessage: "Loại bất động sản không được để trống",
  },
  choosePropertyType: {
    id: `${scope}.choosePropertyType`,
    defaultMessage: "Chọn loại bất động sản",
  },
  propertyArea: {
    id: `${scope}.propertyArea`,
    defaultMessage: "Diện tích",
  },
  propertyAreaRequired: {
    id: `${scope}.propertyAreaRequired`,
    defaultMessage: "Diện tích không được để trống và phải lớn hơn 0",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Tình trạng sử dụng",
  },
  statusRequired: {
    id: `${scope}.statusRequired`,
    defaultMessage: "Tình trạng sử dụng không được để trống",
  },
  chooseStatus: {
    id: `${scope}.chooseStatus`,
    defaultMessage: "Chọn tình trạng sử dụng",
  },
  ownerInfo: {
    id: `${scope}.ownerInfo`,
    defaultMessage: "Thông tin chủ hộ (nếu có)",
  },
  phone: {
    id: `${scope}.phone`,
    defaultMessage: "Số điện thoại",
  },
  phoneRequired: {
    id: `${scope}.phoneRequired`,
    defaultMessage: "Số điện thoại không được để trống",
  },
  name: {
    id: `${scope}.name`,
    defaultMessage: "Tên chủ hộ",
  },
  nameRequired: {
    id: `${scope}.nameRequired`,
    defaultMessage: "Tên chủ hộ không được để trống",
  },
  totalMember: {
    id: `${scope}.totalMember`,
    defaultMessage: "Số thành viên",
  },
  totalMemberRequired: {
    id: `${scope}.totalMemberRequired`,
    defaultMessage: "Số thành viên không được để trống và phải lớn hơn 0",
  },
  dateReceive: {
    id: `${scope}.dateReceive`,
    defaultMessage: "Ngày nhận nhà",
  },
  createProperty: {
    id: `${scope}.createProperty`,
    defaultMessage: "Tạo bất động sản",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  handoverStatus: {
    id: `${scope}.handoverStatus`,
    defaultMessage: "Tình trạng bàn giao:",
  },
  handoverStatusRequired: {
    id: `${scope}.handoverStatusRequired`,
    defaultMessage: "Tình trạng bàn giao không được để trống",
  },
  chooseHandoverStatus: {
    id: `${scope}.chooseHandoverStatus`,
    defaultMessage: "Chọn tình trạng bàn giao",
  },
  owner: {
    id: `${scope}.owner`,
    defaultMessage: "Chủ hộ",
  },
  dayHandover: {
    id: `${scope}.dayHandover`,
    defaultMessage: "Ngày bàn giao",
  },
  note: {
    id: `${scope}.note`,
    defaultMessage: "Ghi chú",
  },
  propertyError: {
    id: `${scope}.propertyError`,
    defaultMessage: "Tên bất động sản không đúng định dạng",
  },
  nameError: {
    id: `${scope}.nameError`,
    defaultMessage: "Tên chủ hộ không đúng định dạng",
  },
  invalidName: {
    id: `${scope}.invalidName`,
    defaultMessage: "Họ và tên không hợp lệ",
  },
  invalidPhone: {
    id: `${scope}.invalidPhone`,
    defaultMessage: "Số điện thoại không hợp lệ",
  },
  propertyAreaError: {
    id: `${scope}.propertyAreaError`,
    defaultMessage: "Diện tích không đúng định dạng",
  },
  dayHandoverError: {
    id: `${scope}.dayHandoverError`,
    defaultMessage: "Ngày bàn giao không được để trống",
  },
});
