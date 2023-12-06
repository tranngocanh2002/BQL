/*
 * CombineCard Messages
 *
 * This contains all the text for the CombineCard container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.CombineCardContainer";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the CombineCardContainer container!",
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
    defaultMessage: "Bạn có chắc muốn xóa thẻ này?",
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
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  cardDetail: {
    id: `${scope}.cardDetail`,
    defaultMessage: "Chi tiết thẻ",
  },
  editCard: {
    id: `${scope}.editCard`,
    defaultMessage: "Chỉnh sửa thẻ",
  },
  deleteCard: {
    id: `${scope}.deleteCard`,
    defaultMessage: "Xóa thẻ",
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
  addCombineCard: {
    id: `${scope}.addCombineCard`,
    defaultMessage: "Thêm mới thẻ",
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
  totalCombineCard: {
    id: `${scope}.totalCombineCard`,
    defaultMessage: "Tổng số {total, plural, one {# thẻ} other {# thẻ}}",
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
    defaultMessage: "Bạn chắc chắn muốn tải lên danh sách thẻ không?",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  cardNumber: {
    id: `${scope}.cardNumber`,
    defaultMessage: "Số thẻ",
  },
  cardId: {
    id: `${scope}.cardId`,
    defaultMessage: "Mã thẻ",
  },
  cardOwner: {
    id: `${scope}.cardOwner`,
    defaultMessage: "Chủ thẻ",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  createAt: {
    id: `${scope}.createAt`,
    defaultMessage: "Ngày tạo",
  },
  addNew: {
    id: `${scope}.addNew`,
    defaultMessage: "Thêm mới",
  },
  errorEmpty: {
    id: `${scope}.errorEmpty`,
    defaultMessage: "Không được để trống.",
  },
  all: {
    id: `${scope}.all`,
    defaultMessage: "Tất cả",
  },
  new: {
    id: `${scope}.new`,
    defaultMessage: "Mới",
  },
  doing: {
    id: `${scope}.doing`,
    defaultMessage: "Đang làm",
  },
  done: {
    id: `${scope}.done`,
    defaultMessage: "Làm xong",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Huỷ",
  },
  overdude: {
    id: `${scope}.overdude`,
    defaultMessage: "Quá hạn",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Đồng ý",
  },
  title: {
    id: `${scope}.title`,
    defaultMessage: "Tiêu đề",
  },
  assignee: {
    id: `${scope}.assignee`,
    defaultMessage: "Người thực hiện",
  },

  startTime: {
    id: `${scope}.startTime`,
    defaultMessage: "Ngày bắt đầu",
  },
  endTime: {
    id: `${scope}.endTime`,
    defaultMessage: "Ngày kết thúc",
  },
  term: {
    id: `${scope}.term`,
    defaultMessage: "Hạn",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  deadline: {
    id: `${scope}.deadline`,
    defaultMessage: "Thời hạn",
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
    defaultMessage: "Tổng số {total}",
  },
  priority: {
    id: `${scope}.priority`,
    defaultMessage: "Ưu tiên",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  noActivity: {
    id: `${scope}.noActivity`,
    defaultMessage: "Không có hoạt động nào",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: "Quay lại",
  },
  time: {
    id: `${scope}.time`,
    defaultMessage: "Thời gian",
  },
  createTime: {
    id: `${scope}.createTime`,
    defaultMessage: "Ngày tạo",
  },
  updateTime: {
    id: `${scope}.updateTime`,
    defaultMessage: "Ngày cập nhật",
  },
  yes: {
    id: `${scope}.yes`,
    defaultMessage: "Có",
  },
  no: {
    id: `${scope}.no`,
    defaultMessage: "Không",
  },

  taskInfo: {
    id: `${scope}.taskInfo`,
    defaultMessage: "Thông tin công việc",
  },
  itemCantBlankType: {
    id: `${scope}.itemCantBlankType`,
    defaultMessage: "{item} không được để trống",
  },
  createTask: {
    id: `${scope}.createTask`,
    defaultMessage: "Tạo công việc",
  },
  updateTask: {
    id: `${scope}.updateTask`,
    defaultMessage: "Cập nhật",
  },

  detail: {
    id: `${scope}.detail`,
    defaultMessage: "Chi tiết",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xóa",
  },
  createSuccess: {
    id: `${scope}.createSuccess`,
    defaultMessage: "Thêm mới công việc thành công",
  },
  updateSuccess: {
    id: `${scope}.updateSuccess`,
    defaultMessage: "Chỉnh sửa thành công",
  },

  deleteTaskSuccess: {
    id: `${scope}.deleteTaskSuccess`,
    defaultMessage: "Xoá công việc thành công",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  history: {
    id: `${scope}.history`,
    defaultMessage: "Lịch sử thẻ hợp nhất",
  },
  searchProperty: {
    id: `${scope}.searchProperty`,
    defaultMessage: "Tìm kiếm bất động sản",
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
  cancelContent: {
    id: `${scope}.cancelContent`,
    defaultMessage: "Bạn chắc chắn muốn huỷ",
  },
  createNew: {
    id: `${scope}.createNew`,
    defaultMessage: "Tạo mới",
  },
  notFindPage: {
    id: `${scope}.notFindPage`,
    defaultMessage: "Không tìm thấy trang.",
  },
  cardInfo: {
    id: `${scope}.cardInfo`,
    defaultMessage: "Thông tin thẻ",
  },
});
