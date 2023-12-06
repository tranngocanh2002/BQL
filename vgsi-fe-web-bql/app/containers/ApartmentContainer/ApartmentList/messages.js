/*
 * ApartmentList Messages
 *
 * This contains all the text for the ApartmentList container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.ApartmentList";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the ApartmentDetail container!",
  },
  apartmentName: {
    id: `${scope}.apartmentName`,
    defaultMessage: "Tên bất động sản",
  },
  apartmentId: {
    id: `${scope}.apartmentId`,
    defaultMessage: "Mã bất động sản",
  },
  deleteModalTitle: {
    id: `${scope}.deleteModalTitle`,
    defaultMessage: "Bạn có chắc muốn xóa bất động sản này?",
  },
  okText: {
    id: `${scope}.okText`,
    defaultMessage: "Đồng ý",
  },
  cancelText: {
    id: `${scope}.cancelText`,
    defaultMessage: "Hủy",
  },
  block: {
    id: `${scope}.block`,
    defaultMessage: "Địa chỉ",
  },
  apartmentType: {
    id: `${scope}.apartmentType`,
    defaultMessage: "Loại bất động sản",
  },
  apartmentOwner: {
    id: `${scope}.apartmentOwner`,
    defaultMessage: "Chủ hộ",
  },
  apartmentArea: {
    id: `${scope}.apartmentArea`,
    defaultMessage: "Diện tích",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Tình trạng ở",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  apartmentDetail: {
    id: `${scope}.apartmentDetail`,
    defaultMessage: "Chi tiết bất động sản",
  },
  editApartment: {
    id: `${scope}.editApartment`,
    defaultMessage: "Chỉnh sửa thông tin bất động sản",
  },
  deleteApartment: {
    id: `${scope}.deleteApartment`,
    defaultMessage: "Xóa bất động sản",
  },
  searchName: {
    id: `${scope}.searchName`,
    defaultMessage: "Tìm kiếm theo tên",
  },
  searchId: {
    id: `${scope}.searchId`,
    defaultMessage: "Tìm kiếm theo mã",
  },
  searchBlock: {
    id: `${scope}.searchBlock`,
    defaultMessage: "Tìm kiếm theo lô / tầng",
  },
  searchOwner: {
    id: `${scope}.searchOwner`,
    defaultMessage: "Tìm kiếm theo chủ hộ",
  },
  statusEmpty: {
    id: `${scope}.statusEmpty`,
    defaultMessage: "Đang trống",
  },
  statusLiving: {
    id: `${scope}.statusLiving`,
    defaultMessage: "Đang ở",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  reloadPage: {
    id: `${scope}.reloadPage`,
    defaultMessage: "Làm mới trang",
  },
  addApartment: {
    id: `${scope}.addApartment`,
    defaultMessage: "Thêm bất động sản",
  },
  importData: {
    id: `${scope}.importData`,
    defaultMessage: "Import dữ liệu",
  },
  downloadTemplate: {
    id: `${scope}.downloadTemplate`,
    defaultMessage: "Tải file import mẫu",
  },
  exportData: {
    id: `${scope}.exportData`,
    defaultMessage: "Export dữ liệu",
  },
  emptyData: {
    id: `${scope}.emptyData`,
    defaultMessage: "Không có dữ liệu",
  },
  totalApartment: {
    id: `${scope}.totalApartment`,
    defaultMessage:
      "Tổng số {total, plural, one {# bất động sản} other {# bất động sản}}",
  },
  handOverStatus: {
    id: `${scope}.handOverStatus`,
    defaultMessage: "Tình trạng bàn giao",
  },
  handOverComplete: {
    id: `${scope}.handOverComplete`,
    defaultMessage: "Đã bàn giao",
  },
  handOverNotComplete: {
    id: `${scope}.handOverNotComplete`,
    defaultMessage: "Chưa bàn giao",
  },
  importMess: {
    id: `${scope}.importMess`,
    defaultMessage: "Bạn chắc chắn muốn tải lên danh sách bất động sản không?",
  },
  propertyAreaError: {
    id: `${scope}.propertyAreaError`,
    defaultMessage: "Diện tích không đúng định dạng",
  },
});
