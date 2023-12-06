/*
 * FormDetail Messages
 *
 * This contains all the text for the FormDetail container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.FormDetail";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the FormDetail container!",
  },
  cancelFormContent: {
    id: `${scope}.cancelFormContent`,
    defaultMessage: "Bạn có chắc muốn từ chối đăng ký biểu mẫu này không?",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  reasonRequest: {
    id: `${scope}.reasonRequest`,
    defaultMessage: "Vui lòng nhập lý do từ chối!",
  },
  reason: {
    id: `${scope}.reason`,
    defaultMessage: "Lý do từ chối",
  },
  approveRequest: {
    id: `${scope}.approveRequest`,
    defaultMessage: "Bạn muốn phê duyệt biểu mẫu này?",
  },
  noDataApart: {
    id: `${scope}.noDataApart`,
    defaultMessage: "Không tìm thấy chi tiết bất động sản.",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: " Quay lại",
  },
  stt: {
    id: `${scope}.stt`,
    defaultMessage: "STT",
  },
  name: {
    id: `${scope}.name`,
    defaultMessage: "Họ và tên",
  },
  idCard: {
    id: `${scope}.idCard`,
    defaultMessage: "Số CCCD",
  },
  relationshipWithOwner: {
    id: `${scope}.relationshipWithOwner`,
    defaultMessage: "Quan hệ với chủ hộ",
  },
  transport: {
    id: `${scope}.transport`,
    defaultMessage: "Hàng hóa vận chuyển",
  },
  size: {
    id: `${scope}.size`,
    defaultMessage: "Kích thước/Khối lượng",
  },
  quantity: {
    id: `${scope}.quantity`,
    defaultMessage: "Số lượng",
  },
  registrationInformation: {
    id: `${scope}.registrationInformation`,
    defaultMessage: "Thông tin đăng ký",
  },
  customerName: {
    id: `${scope}.customerName`,
    defaultMessage: "Tên khách hàng",
  },
  propertyName: {
    id: `${scope}.propertyName`,
    defaultMessage: "Tên bất động sản",
  },
  phoneNumber: {
    id: `${scope}.phoneNumber`,
    defaultMessage: "Số điện thoại",
  },
  transferPlace: {
    id: `${scope}.transferPlace`,
    defaultMessage: "Nơi vận chuyển",
  },
  transferType: {
    id: `${scope}.transferType`,
    defaultMessage: "Loại chuyển đồ",
  },
  useTime: {
    id: `${scope}.useTime`,
    defaultMessage: "Thời gian sử dụng",
  },
  useElevator: {
    id: `${scope}.useElevator`,
    defaultMessage: "Sử dụng thang hàng",
  },
  emptyData: {
    id: `${scope}.emptyData`,
    defaultMessage: "Không có dữ liệu",
  },
  reject: {
    id: `${scope}.reject`,
    defaultMessage: "Từ chối",
  },
  approve: {
    id: `${scope}.approve`,
    defaultMessage: "Phê duyệt",
  },
  waiting: {
    id: `${scope}.waiting`,
    defaultMessage: "Chờ duyệt",
  },
  itemType: {
    id: `${scope}.itemType`,
    defaultMessage: "Loại đồ vận chuyển",
  },
  itemList: {
    id: `${scope}.itemList`,
    defaultMessage: "Danh sách vận chuyển",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái phê duyệt",
  },
  verifyUser: {
    id: `${scope}.verifyUser`,
    defaultMessage: "Người phê duyệt",
  },
  timeVerify: {
    id: `${scope}.timeVerify`,
    defaultMessage: "Thời gian phê duyệt",
  },
  registerList: {
    id: `${scope}.registerList`,
    defaultMessage: "Danh sách đăng ký",
  },
  createAt: {
    id: `${scope}.createAt`,
    defaultMessage: "Thời gian gửi",
  },
  cancelled: {
    id: `${scope}.cancelled`,
    defaultMessage: "Đã hủy",
  },
  birthday: {
    id: `${scope}.birthday`,
    defaultMessage: "Ngày sinh",
  },
  gender: {
    id: `${scope}.gender`,
    defaultMessage: "Giới tính",
  },
  issuedByIdCard: {
    id: `${scope}.issuedByIdCard`,
    defaultMessage: "Nơi cấp",
  },
  dateOfIssued: {
    id: `${scope}.dateOfIssued`,
    defaultMessage: "Ngày cấp",
  },
  relationship: {
    id: `${scope}.relationship`,
    defaultMessage: "Mối quan hệ",
  },
  paperType: {
    id: `${scope}.paperType`,
    defaultMessage: "Loại giấy tờ",
  },
  attachImage: {
    id: `${scope}.attachImage`,
    defaultMessage: "Ảnh đính kèm",
  },
  residentImage: {
    id: `${scope}.residentImage`,
    defaultMessage: "Ảnh cư dân",
  },
  idImage: {
    id: `${scope}.idImage`,
    defaultMessage: "Ảnh 2 mặt CMND/ Hộ chiếu / Giấy khai sinh",
  },
  livingImage: {
    id: `${scope}.livingImage`,
    defaultMessage: "Ảnh tạm trú/ thường trú",
  },
  bienSo: {
    id: `${scope}.bienSo`,
    defaultMessage: "Biển số",
  },
  phuongTien: {
    id: `${scope}.phuongTien`,
    defaultMessage: "Phương tiện",
  },
  nhanHieu: {
    id: `${scope}.nhanHieu`,
    defaultMessage: "Nhãn hiệu",
  },
  mauXe: {
    id: `${scope}.mauXe`,
    defaultMessage: "Màu",
  },
  oTo: {
    id: `${scope}.oTo`,
    defaultMessage: "O tô",
  },
  moto: {
    id: `${scope}.moto`,
    defaultMessage: "Xe máy/ Xe máy điện",
  },
  xeDap: {
    id: `${scope}.xeDap`,
    defaultMessage: "Xe đạp/ Xe đạp điện",
  },
  moveOut: {
    id: `${scope}.moveOut`,
    defaultMessage: "Chuyển đồ ra",
  },
  moveIn: {
    id: `${scope}.moveIn`,
    defaultMessage: "Chuyển đồ vào",
  },
  lobby: {
    id: `${scope}.lobby`,
    defaultMessage: "Qua cổng chính",
  },
  tunnel: {
    id: `${scope}.tunnel`,
    defaultMessage: "Qua cổng phụ",
  },
  yes: {
    id: `${scope}.yes`,
    defaultMessage: "Có",
  },
  no: {
    id: `${scope}.no`,
    defaultMessage: "Không",
  },
  registrantInformation: {
    id: `${scope}.registrantInformation`,
    defaultMessage: "Thông tin người đăng ký",
  },
  male: {
    id: `${scope}.male`,
    defaultMessage: "Nam",
  },
  female: {
    id: `${scope}.female`,
    defaultMessage: "Nữ",
  },
  cmnd: {
    id: `${scope}.cmnd`,
    defaultMessage: "CMND",
  },
  cccd: {
    id: `${scope}.cccd`,
    defaultMessage: "CCCD",
  },
  passport: {
    id: `${scope}.passport`,
    defaultMessage: "Hộ chiếu",
  },
  birthCertificate: {
    id: `${scope}.birthCertificate`,
    defaultMessage: "Giấy khai sinh",
  },
  registerTransfer: {
    id: `${scope}.registerTransfer`,
    defaultMessage: "Đăng ký chuyển tài sản",
  },
  registerAccessCard: {
    id: `${scope}.registerAccessCard`,
    defaultMessage: "Đăng ký thi công",
  },
  registerResidentCard: {
    id: `${scope}.registerResidentCard`,
    defaultMessage: "Đăng ký thẻ Meyhomes",
  },
  registerCarCard: {
    id: `${scope}.registerCarCard`,
    defaultMessage: "Đăng ký thẻ xe",
  },
  transportInformation: {
    id: `${scope}.transportInformation`,
    defaultMessage: "Thông tin chuyển đồ",
  },
  vehicleInformation: {
    id: `${scope}.vehicleInformation`,
    defaultMessage: "Thông tin phương tiện",
  },
  infoRegister: {
    id: `${scope}.infoRegister`,
    defaultMessage: "Thông tin người được đăng ký",
  },
  reasonPlaceholder: {
    id: `${scope}.reasonPlaceholder`,
    defaultMessage: "Nhập lý do",
  },
  characteristic: {
    id: `${scope}.characteristic`,
    defaultMessage: "Đặc tính",
  },
  beneficiaryInfo: {
    id: `${scope}.beneficiaryInfo`,
    defaultMessage: "Thông tin người thụ hưởng",
  },
  beneficiary: {
    id: `${scope}.beneficiary`,
    defaultMessage: "Người thụ hưởng",
  },
  constructionName: {
    id: `${scope}.constructionName`,
    defaultMessage: "Tên nhà thầu",
  },
  contact: {
    id: `${scope}.contact`,
    defaultMessage: "Số điện thoại liên hệ",
  },
  constructionContact: {
    id: `${scope}.constructionContact`,
    defaultMessage: "Người liên hệ/giám sát",
  },
  timeConstruct: {
    id: `${scope}.timeConstruct`,
    defaultMessage: "Thời gian thi công",
  },
  workItem: {
    id: `${scope}.workItem`,
    defaultMessage: "Hạng mục thực hiện",
  },
  workersList: {
    id: `${scope}.workersList`,
    defaultMessage: "Danh sách công nhân",
  },
  worker: {
    id: `${scope}.worker`,
    defaultMessage: "Công nhân",
  },
  idNumber: {
    id: `${scope}.idNumber`,
    defaultMessage: "Số CMND/CCCD",
  },
});
