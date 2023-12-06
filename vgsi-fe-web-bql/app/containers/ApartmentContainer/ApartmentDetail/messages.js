/*
 * ApartmentDetail Messages
 *
 * This contains all the text for the ApartmentDetail container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ApartmentDetail";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ApartmentDetail container!",
  },
  deleteMemberTitle: {
    id: `${scope}.deleteMemberTitle`,
    defaultMessage:
      "Bạn có chắc chắn muốn xóa thành viên ra khỏi bất động sản này không?",
  },
  deleteMemberOkText: {
    id: `${scope}.deleteMemberOkText`,
    defaultMessage: "Đồng ý",
  },
  deleteMemberCancelText: {
    id: `${scope}.deleteMemberCancelText`,
    defaultMessage: "Hủy",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không tìm thấy chi tiết bất động sản",
  },
  btnBack: {
    id: `${scope}.btnBack`,
    defaultMessage: "Quay lại",
  },
  memberName: {
    id: `${scope}.memberName`,
    defaultMessage: "Tên thành viên",
  },
  memberPhone: {
    id: `${scope}.memberPhone`,
    defaultMessage: "Số điện thoại",
  },
  memberRole: {
    id: `${scope}.memberRole`,
    defaultMessage: "Vai trò",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  deleteMember: {
    id: `${scope}.deleteMember`,
    defaultMessage: "Xóa thành viên",
  },
  addMember: {
    id: `${scope}.addMember`,
    defaultMessage: "Thêm thành viên",
  },
  apartmentDetail: {
    id: `${scope}.apartmentDetail`,
    defaultMessage: "Thông tin bất động sản",
  },
  apartmentName: {
    id: `${scope}.apartmentName`,
    defaultMessage: "Tên bất động sản",
  },
  apartmentArea: {
    id: `${scope}.apartmentArea`,
    defaultMessage: "Diện tích",
  },
  block: {
    id: `${scope}.block`,
    defaultMessage: "Địa chỉ",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Tình trạng ở",
  },
  receiveDay: {
    id: `${scope}.receiveDay`,
    defaultMessage: "Ngày nhận nhà",
  },
  apartmentCode: {
    id: `${scope}.apartmentCode`,
    defaultMessage: "Mã bất động sản",
  },
  totalMember: {
    id: `${scope}.totalMember`,
    defaultMessage:
      "Danh sách ({total, plural, one {# thành viên} other {# thành viên}})",
  },
  emptyData: {
    id: `${scope}.emptyData`,
    defaultMessage: "Không có dữ liệu",
  },
  editInfo: {
    id: `${scope}.editInfo`,
    defaultMessage: "Chỉnh sửa thông tin",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  apartmentNameRequired: {
    id: `${scope}.apartmentNameRequired`,
    defaultMessage: "Tên bất động sản không được để trống",
  },
  apartmentAddressRequired: {
    id: `${scope}.apartmentAddressRequired`,
    defaultMessage: "Địa chỉ không được để trống",
  },
  chooseAddress: {
    id: `${scope}.chooseAddress`,
    defaultMessage: "Chọn địa chỉ",
  },
  apartmentType: {
    id: `${scope}.apartmentType`,
    defaultMessage: "Loại bất động sản",
  },
  apartmentTypeRequired: {
    id: `${scope}.apartmentTypeRequired`,
    defaultMessage: "Loại bất động sản không được để trống",
  },
  apartmentTypePlaceholder: {
    id: `${scope}.apartmentTypePlaceholder`,
    defaultMessage: "Vui lòng chọn loại bất động sản",
  },
  apartmentAreaRequired: {
    id: `${scope}.apartmentAreaRequired`,
    defaultMessage: "Diện tích không được để trống",
  },
  statusRequired: {
    id: `${scope}.statusRequired`,
    defaultMessage: "Tình trạng không được để trống",
  },
  statusPlaceholder: {
    id: `${scope}.statusPlaceholder`,
    defaultMessage: "Chọn tình trạng",
  },
  totalMembers: {
    id: `${scope}.totalMembers`,
    defaultMessage: "Số thành viên",
  },
  totalMembersRequired: {
    id: `${scope}.totalMembersRequired`,
    defaultMessage: "Số thành viên không được để trống và lớn hơn 0.",
  },
  trangThaiKhaiBao: {
    id: `${scope}.trangThaiKhaiBao`,
    defaultMessage: "Trạng thái khai báo",
  },
  dinhMucNuoc: {
    id: `${scope}.dinhMucNuoc`,
    defaultMessage: "định mức nước",
  },
  modalAddMemberTitle: {
    id: `${scope}.modalAddMemberTitle`,
    defaultMessage: "Thêm thành viên - Bất động sản {apartmentName}",
  },
  addNew: {
    id: `${scope}.addNew`,
    defaultMessage: "Thêm mới",
  },
  phoneRequired: {
    id: `${scope}.phoneRequired`,
    defaultMessage: "Số điện thoại không được để trống.",
  },
  memberNameRequired: {
    id: `${scope}.memberNameRequired`,
    defaultMessage: "Tên thành viên không được để trống.",
  },
  memberRoleRequired: {
    id: `${scope}.memberRoleRequired`,
    defaultMessage: "Vai trò không được để trống.",
  },
  memberRelationship: {
    id: `${scope}.memberRelationship`,
    defaultMessage: "Mối quan hệ",
  },
  memberRelationshipRequired: {
    id: `${scope}.memberRelationshipRequired`,
    defaultMessage: "Mối quan hệ không được để trống.",
  },
  memberRelationshipPlaceholder: {
    id: `${scope}.memberRelationshipPlaceholder`,
    defaultMessage: "Chọn quan hệ với chủ hộ",
  },
  modalEditMemberTitle: {
    id: `${scope}.modalEditMemberTitle`,
    defaultMessage: "Sửa thông tin thành viên - Bất động sản {apartmentName}",
  },
  dayHandover: {
    id: `${scope}.dayHandover`,
    defaultMessage: "Ngày bàn giao",
  },
  handoverStatus: {
    id: `${scope}.handoverStatus`,
    defaultMessage: "Tình trạng bàn giao",
  },
  handoverStatusCompleted: {
    id: `${scope}.handoverStatusCompleted`,
    defaultMessage: "Đã bàn giao",
  },
  handoverStatusNotCompleted: {
    id: `${scope}.handoverStatusNotCompleted`,
    defaultMessage: "Chưa bàn giao",
  },
  relationship: {
    id: `${scope}.relationship`,
    defaultMessage: "Mối quan hệ",
  },
  dayIn: {
    id: `${scope}.dayIn`,
    defaultMessage: "Ngày vào",
  },
  dayOut: {
    id: `${scope}.dayOut`,
    defaultMessage: "Ngày ra",
  },
  handoverStatusRequired: {
    id: `${scope}.handoverStatusRequired`,
    defaultMessage: "Tình trạng bàn giao không được để trống.",
  },
  handoverStatusPlaceholder: {
    id: `${scope}.handoverStatusPlaceholder`,
    defaultMessage: "Chọn tình trạng bàn giao",
  },
  owner: {
    id: `${scope}.owner`,
    defaultMessage: "Chủ hộ",
  },
  ownerInfo: {
    id: `${scope}.ownerInfo`,
    defaultMessage: "Thông tin chủ hộ",
  },
  phone: {
    id: `${scope}.phone`,
    defaultMessage: "Số điện thoại",
  },
  name: {
    id: `${scope}.name`,
    defaultMessage: "Tên chủ hộ",
  },
  nameRequired: {
    id: `${scope}.nameRequired`,
    defaultMessage: "Tên chủ hộ không được để trống",
  },
  note: {
    id: `${scope}.note`,
    defaultMessage: "Ghi chú",
  },
  homeReceived: {
    id: `${scope}.homeReceived`,
    defaultMessage: "Đã bàn giao",
  },
  homeNotReceived: {
    id: `${scope}.homeNotReceived`,
    defaultMessage: "Chưa bàn giao",
  },
  invalidName: {
    id: `${scope}.invalidName`,
    defaultMessage: "Họ và tên không đúng định dạng",
  },
  invalidPhone: {
    id: `${scope}.invalidPhone`,
    defaultMessage: "Số điện thoại không đúng định dạng",
  },
  propertyAreaError: {
    id: `${scope}.propertyAreaError`,
    defaultMessage: "Diện tích không đúng định dạng",
  },
  nameError: {
    id: `${scope}.nameError`,
    defaultMessage: "Tên chủ hộ không đúng định dạng",
  },
  propertyError: {
    id: `${scope}.propertyError`,
    defaultMessage: "Tên bất động sản không đúng định dạng",
  },
  addResident: {
    id: `${scope}.addResident`,
    defaultMessage: "Thêm cư dân",
  },
  phoneNotExisted: {
    id: `${scope}.phoneNotExisted`,
    defaultMessage: "Số điện thoại không tồn tại trong hệ thống",
  },
});
