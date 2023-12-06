/*
 * MaintainList Messages
 *
 * This contains all the text for the MaintainList container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.MaintainList";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the Maintain container!",
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
    defaultMessage: "Bạn có chắc chắn muốn xóa thiết bị này không?",
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
    defaultMessage: "Trạng thái",
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
    defaultMessage: "Tìm kiếm theo chủ sở hữu",
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
  totalDevice: {
    id: `${scope}.totalDevice`,
    defaultMessage:
      "Tổng số {total, plural, one {# thiết bị} other {# thiết bị}}",
  },
  device: {
    id: `${scope}.device`,
    defaultMessage: "Loại thiết bị",
  },
  deviceList: {
    id: `${scope}.deviceList`,
    defaultMessage: "Danh sách thiết bị",
  },
  deviceCode: {
    id: `${scope}.deviceCode`,
    defaultMessage: "Mã thiết bị",
  },
  deviceName: {
    id: `${scope}.deviceName`,
    defaultMessage: "Tên thiết bị",
  },
  deviceType: {
    id: `${scope}.deviceType`,
    defaultMessage: "Loại thiết bị",
  },
  maintenanceStartDate: {
    id: `${scope}.maintenanceStartDate`,
    defaultMessage: "Ngày bắt đầu bảo trì",
  },
  option: {
    id: `${scope}.option`,
    defaultMessage: "Thao tác",
  },
  stt: {
    id: `${scope}.stt`,
    defaultMessage: "STT",
  },
  lastMaintenanceDate: {
    id: `${scope}.lastMaintenanceDate`,
    defaultMessage: "Ngày bảo trì gần nhất",
  },
  upcomingMaintenanceDay: {
    id: `${scope}.upcomingMaintenanceDay`,
    defaultMessage: "Ngày bảo trì sắp tới",
  },
  maintenanceSchedule: {
    id: `${scope}.maintenanceSchedule`,
    defaultMessage: "Lịch bảo trì",
  },
  deviceDetails: {
    id: `${scope}.deviceDetails`,
    defaultMessage: "Chi tiết thiết bị",
  },
  deviceEditing: {
    id: `${scope}.deviceEditing`,
    defaultMessage: "Chỉnh sửa thiết bị",
  },
  addDevice: {
    id: `${scope}.addDevice`,
    defaultMessage: "Thêm thiết bị",
  },
  deleteDevice: {
    id: `${scope}.deleteDevice`,
    defaultMessage: "Xóa thiết bị",
  },
  computer: {
    id: `${scope}.computer`,
    defaultMessage: "Máy tính",
  },
  fan: {
    id: `${scope}.fan`,
    defaultMessage: "Quạt",
  },
  camera: {
    id: `${scope}.camera`,
    defaultMessage: "Camera",
  },
  lamp: {
    id: `${scope}.lamp`,
    defaultMessage: "Đèn",
  },
  elevator: {
    id: `${scope}.elevator`,
    defaultMessage: "Thang máy",
  },
  active: {
    id: `${scope}.active`,
    defaultMessage: "Đang hoạt động",
  },
  inActive: {
    id: `${scope}.inActive`,
    defaultMessage: "Dừng hoạt động",
  },
  startDate: {
    id: `${scope}.start`,
    defaultMessage: "Bắt đầu",
  },
  endDate: {
    id: `${scope}.endDate`,
    defaultMessage: "Kết thúc",
  },
  errorEmpty: {
    id: `${scope}.errorEmpty`,
    defaultMessage: "{field} không được để trống",
  },
  location: {
    id: `${scope}.location`,
    defaultMessage: "Vị trí",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả ",
  },
  warrantyPeriod: {
    id: `${scope}.warrantyPeriod`,
    defaultMessage: "Thời gian bảo hành ",
  },
  fromDate: {
    id: `${scope}.fromDate`,
    defaultMessage: "Từ ngày",
  },
  toDate: {
    id: `${scope}.toDate`,
    defaultMessage: "Đến ngày",
  },
  timeMaintenance: {
    id: `${scope}.timeMaintenance`,
    defaultMessage: "Thời gian bắt đầu bảo trì",
  },
  repeatedMaintenance: {
    id: `${scope}.repeatedMaintenance`,
    defaultMessage: "Bảo trì lặp lại",
  },
  createDevice: {
    id: `${scope}.createDevice`,
    defaultMessage: "Tạo thiết bị",
  },
  updateInfo: {
    id: `${scope}.updateInfo`,
    defaultMessage: "Cập nhật thông tin",
  },
  upload: {
    id: `${scope}.upload`,
    defaultMessage: "Tải ảnh lên",
  },
  ruleFile: {
    id: `${scope}.ruleFile`,
    defaultMessage: "Định dạng png, jfif, jpg, jpeg không vượt quá 10MB",
  },
  attachFile: {
    id: `${scope}.attachFile`,
    defaultMessage: "Ảnh đính kèm",
  },
  exceedFile: {
    id: `${scope}.exceedFile`,
    defaultMessage: "Ảnh đính kèm vượt quá 10MB",
  },
  createAt: {
    id: `${scope}.createAt`,
    defaultMessage: "Ngày tạo",
  },
  lastMaintenanceTime: {
    id: `${scope}.lastMaintenanceTime`,
    defaultMessage: "Thời gian bảo trì gần nhất",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: " Quay lại",
  },
  fileAttach: {
    id: `${scope}.fileAttach`,
    defaultMessage: "Ảnh đính kèm",
  },
  qrCode: {
    id: `${scope}.qrCode`,
    defaultMessage: "Mã QR",
  },
  maintenanceConfirmation: {
    id: `${scope}.maintenanceConfirmation`,
    defaultMessage: "Xác nhận bảo trì",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Xác nhận",
  },
  chooseDayPlaceholder: {
    id: `${scope}.chooseDayPlaceholder`,
    defaultMessage: "Chọn ngày",
  },
  cancelAddTitle: {
    id: `${scope}.cancelAddTitle`,
    defaultMessage: "Bạn có chắc chắn muốn hủy thêm mới thiết bị này không?",
  },
  cancelUpdateTitle: {
    id: `${scope}.cancelUpdateTitle`,
    defaultMessage: "Bạn có chắc chắn muốn hủy cập nhật thiết bị này không?",
  },
  importMess: {
    id: `${scope}.importMess`,
    defaultMessage: "Bạn chắc chắn muốn tải lên danh sách thiết bị không?",
  },
});
