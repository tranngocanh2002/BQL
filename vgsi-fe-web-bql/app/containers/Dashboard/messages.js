/*
 * BookingDetail Messages
 *
 * This contains all the text for the BookingDetail container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.Dashboard";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the BookingDetail container!",
  },
  bookingSuccess: {
    id: `${scope}.bookingSuccess`,
    defaultMessage: "Xác nhận đặt chỗ thành công.",
  },
  bookingCancelSuccess: {
    id: `${scope}.bookingCancelSuccess`,
    defaultMessage: "Huỷ đặt chỗ thành công.",
  },
  noDataBooking: {
    id: `${scope}.noDataBooking`,
    defaultMessage: "Không tìm thấy chi tiết đặt chỗ.",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: " Quay lại",
  },
  noImage: {
    id: `${scope}.noImage`,
    defaultMessage: "Chưa có ảnh tiện ích",
  },
  utility: {
    id: `${scope}.utility`,
    defaultMessage: "Tiện ích",
  },
  feeType: {
    id: `${scope}.feeType`,
    defaultMessage: "Loại phí",
  },
  free: {
    id: `${scope}.free`,
    defaultMessage: "Miễn phí",
  },
  notFree: {
    id: `${scope}.notFree`,
    defaultMessage: "Thu phí",
  },
  placeName: {
    id: `${scope}.placeName`,
    defaultMessage: "Tên chỗ",
  },
  bookingCode: {
    id: `${scope}.bookingCode`,
    defaultMessage: "Mã đặt tiện ích",
  },
  createReceipts: {
    id: `${scope}.createReceipts`,
    defaultMessage: "Tạo phiếu thu",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  time: {
    id: `${scope}.time`,
    defaultMessage: "Thời gian",
  },
  peopleNum: {
    id: `${scope}.peopleNum`,
    defaultMessage: "Số người:",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  createAt: {
    id: `${scope}.createAt`,
    defaultMessage: "Thời gian tạo",
  },
  updateAt: {
    id: `${scope}.updateAt`,
    defaultMessage: "Cập nhật lúc",
  },
  payment: {
    id: `${scope}.payment`,
    defaultMessage: "Thanh toán",
  },
  price: {
    id: `${scope}.price`,
    defaultMessage: "Giá tiền",
  },
  deposit: {
    id: `${scope}.deposit`,
    defaultMessage: "Đặt cọc",
  },
  costsIncurred: {
    id: `${scope}.costsIncurred`,
    defaultMessage: "Phí sử dụng tiện ích",
  },
  note: {
    id: `${scope}.note`,
    defaultMessage: "Ghi chú",
  },
  exit: {
    id: `${scope}.exit`,
    defaultMessage: "Thoát",
  },
  actionDo: {
    id: `${scope}.actionDo`,
    defaultMessage: "Duyệt hoặc Hủy",
  },
  actionTitle: {
    id: `${scope}.actionTitle`,
    defaultMessage: "Duyệt hoặc hủy đặt chỗ",
  },
  okT: {
    id: `${scope}.okT`,
    defaultMessage: "Duyệt đặt chỗ",
  },
  cancelT: {
    id: `${scope}.cancelT`,
    defaultMessage: "Hủy đặt chỗ",
  },
  actionContent: {
    id: `${scope}.actionContent`,
    defaultMessage:
      "Vui lòng kiểm tra tình trạng thanh toán trước khi xác nhận đặt chỗ. Tiếp tục ?",
  },
  unpaid: {
    id: `${scope}.unpaid`,
    defaultMessage: "Chưa thanh toán",
  },
  paid: {
    id: `${scope}.paid`,
    defaultMessage: "Đã thanh toán",
  },
  month: {
    id: `${scope}.month`,
    defaultMessage: "Tháng",
  },
  serviceType: {
    id: `${scope}.serviceType`,
    defaultMessage: "Loại dịch vụ",
  },
  receivables: {
    id: `${scope}.receivables`,
    defaultMessage: "Phải thu",
  },
  received: {
    id: `${scope}.received`,
    defaultMessage: "Đã thu",
  },
  owed: {
    id: `${scope}.owed`,
    defaultMessage: "Còn thu",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  realReceiving: {
    id: `${scope}.realReceiving`,
    defaultMessage: "Thực thu",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  errorContent: {
    id: `${scope}.errorContent`,
    defaultMessage: "Cần chọn tối thiểu 1 phí để cập nhật phiếu thanh toán.",
  },
  errorContent2: {
    id: `${scope}.errorContent2`,
    defaultMessage: "Còn phí cần gạt nợ.",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  modalContent: {
    id: `${scope}.modalContent`,
    defaultMessage:
      "Bạn chắc chắn chốt sổ phiếu thu này? Phiếu thu sẽ không được sửa đổi thông tin!",
  },
  continue: {
    id: `${scope}.continue`,
    defaultMessage: "Tiếp tục",
  },
  closingEntry: {
    id: `${scope}.closingEntry`,
    defaultMessage: "Chốt sổ",
  },
  unlock: {
    id: `${scope}.unlock`,
    defaultMessage: "Mở khóa",
  },
  cancelVote: {
    id: `${scope}.cancelVote`,
    defaultMessage: "Hủy phiếu",
  },
  cancelReceipt: {
    id: `${scope}.cancelReceipt`,
    defaultMessage: "Hủy phiếu",
  },
  voteNum: {
    id: `${scope}.voteNum`,
    defaultMessage: "Số phiếu",
  },
  receiptNo: {
    id: `${scope}.receiptNo`,
    defaultMessage: "Số phiếu",
  },
  collector: {
    id: `${scope}.collector`,
    defaultMessage: "Người thu",
  },
  submitter: {
    id: `${scope}.submitter`,
    defaultMessage: "Người nộp",
  },
  fillDate: {
    id: `${scope}.fillDate`,
    defaultMessage: "Ngày nộp",
  },
  implementDate: {
    id: `${scope}.implementDate`,
    defaultMessage: "Ngày thực hiện",
  },
  submissForm: {
    id: `${scope}.submissForm`,
    defaultMessage: "Hình thức nộp",
  },
  cash: {
    id: `${scope}.cash`,
    defaultMessage: "Tiền mặt",
  },
  transfer: {
    id: `${scope}.transfer`,
    defaultMessage: "Chuyển khoản",
  },
  day: {
    id: `${scope}.day`,
    defaultMessage: "Ngày",
  },
  year: {
    id: `${scope}.year`,
    defaultMessage: "Năm",
  },
  vnd: {
    id: `${scope}.vnd`,
    defaultMessage: "đ",
  },
  receipts: {
    id: `${scope}.receipts`,
    defaultMessage: "Phiếu thu",
  },
  print: {
    id: `${scope}.print`,
    defaultMessage: "In phiếu",
  },
  notFound: {
    id: `${scope}.notFound`,
    defaultMessage: "Không tìm thấy trang.",
  },
  rejectReceipt: {
    id: `${scope}.rejectReceipt`,
    defaultMessage: "Bạn chắc chắn muốn huỷ phiếu thu này?",
  },
  unlockReceipt: {
    id: `${scope}.unlockReceipt`,
    defaultMessage: "Bạn chắc chắn muốn mở khoá phiếu thu này?",
  },
  skip: {
    id: `${scope}.skip`,
    defaultMessage: "Bỏ qua",
  },
  reason: {
    id: `${scope}.reason`,
    defaultMessage: "Lý do",
  },
  dayVouchers: {
    id: `${scope}.dayVouchers`,
    defaultMessage: "Ngày chứng từ",
  },
  numberOfVouchers: {
    id: `${scope}.numberOfVouchers`,
    defaultMessage: "Số chứng từ",
  },
  payments: {
    id: `${scope}.payments`,
    defaultMessage: "Hình thức TT",
  },
  customerName: {
    id: `${scope}.customerName`,
    defaultMessage: "Chủ hộ",
  },
  amountOfMoney: {
    id: `${scope}.amountOfMoney`,
    defaultMessage: "Số tiền",
  },
  totalAmount: {
    id: `${scope}.totalAmount`,
    defaultMessage: "Tổng tiền",
  },
  choseProperty: {
    id: `${scope}.choseProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  choseAddress: {
    id: `${scope}.choseAddress`,
    defaultMessage: "Chọn địa chỉ",
  },
  fromDate: {
    id: `${scope}.fromDate`,
    defaultMessage: "Từ ngày",
  },
  toDate: {
    id: `${scope}.toDate`,
    defaultMessage: "Đến ngày",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  deleteSearch: {
    id: `${scope}.deleteSearch`,
    defaultMessage: "Xoá tìm kiếm",
  },
  emptyData: {
    id: `${scope}.emptyData`,
    defaultMessage: "Không có dữ liệu",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage: "Tổng số",
  },
  successMessage: {
    id: `${scope}.successMessage`,
    defaultMessage: "Tạo phí phát sinh thành công",
  },
  arise: {
    id: `${scope}.arise`,
    defaultMessage: "Phát sinh",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  approved: {
    id: `${scope}.approved`,
    defaultMessage: "Đã duyệt",
  },
  cancelled: {
    id: `${scope}.cancelled`,
    defaultMessage: "Đã hủy",
  },
  detail: {
    id: `${scope}.detail`,
    defaultMessage: "Chi tiết",
  },
  genFee: {
    id: `${scope}.genFee`,
    defaultMessage: "Tạo phí phát sinh",
  },
  createVoucher: {
    id: `${scope}.createVoucher`,
    defaultMessage: "Tạo phiếu chi",
  },
  choseUtility: {
    id: `${scope}.choseUtility`,
    defaultMessage: "Chọn tiện ích",
  },
  pending: {
    id: `${scope}.pending`,
    defaultMessage: "Chờ duyệt",
  },
  successPlace: {
    id: `${scope}.successPlace`,
    defaultMessage: "Đặt thành công",
  },
  cancelResident: {
    id: `${scope}.cancelResident`,
    defaultMessage: "Cư dân hủy",
  },
  cancelMan: {
    id: `${scope}.cancelMan`,
    defaultMessage: "BQL hủy",
  },
  cancelSystem: {
    id: `${scope}.cancelSystem`,
    defaultMessage: "Hệ thống hủy",
  },
  end: {
    id: `${scope}.end`,
    defaultMessage: "Kết thúc",
  },
  start: {
    id: `${scope}.start`,
    defaultMessage: "Bắt đầu",
  },
  refresh: {
    id: `${scope}.refresh`,
    defaultMessage: "Làm mới trang",
  },
  addNew: {
    id: `${scope}.addNew`,
    defaultMessage: "Thêm mới đặt tiện ích",
  },
  permission: {
    id: `${scope}.permission`,
    defaultMessage: "Bạn cần cấp thêm quyền Thu phí để thực hiện chức năng này",
  },
  fee: {
    id: `${scope}.fee`,
    defaultMessage: "Thu phí",
  },
  bookNum: {
    id: `${scope}.bookNum`,
    defaultMessage: "lượt book",
  },
  addFee: {
    id: `${scope}.addFee`,
    defaultMessage: "Thêm phí",
  },
  moneyError: {
    id: `${scope}.moneyError`,
    defaultMessage: "Giá tiền không được để trống và lớn hơn 0",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  descriptionError: {
    id: `${scope}.descriptionError`,
    defaultMessage: "Mô tả phí không được để trống.",
  },
  descriptionEnError: {
    id: `${scope}.descriptionEnError`,
    defaultMessage: "Mô tả phí tiếng anh không được để trống.",
  },
  nothing: {
    id: `${scope}.nothing`,
    defaultMessage: "Trống.",
  },
  owner: {
    id: `${scope}.owner`,
    defaultMessage: "Chủ hộ",
  },
  payer: {
    id: `${scope}.payer`,
    defaultMessage: "Người chi",
  },
  receiver: {
    id: `${scope}.receiver`,
    defaultMessage: "Người nhận",
  },
  dateSpending: {
    id: `${scope}.dateSpending`,
    defaultMessage: "Ngày chi",
  },
  choseDate: {
    id: `${scope}.choseDate`,
    defaultMessage: "Chọn ngày",
  },
  paymentForm: {
    id: `${scope}.paymentForm`,
    defaultMessage: "Hình thức chi",
  },
  accountHolder: {
    id: `${scope}.accountHolder`,
    defaultMessage: "Chủ tài khoản",
  },
  bank: {
    id: `${scope}.bank`,
    defaultMessage: "Ngân hàng",
  },
  accountNumber: {
    id: `${scope}.accountNumber`,
    defaultMessage: "Số tài khoản",
  },
  feeList: {
    id: `${scope}.feeList`,
    defaultMessage: "Danh sách phí",
  },
  paymentVoucher: {
    id: `${scope}.paymentVoucher`,
    defaultMessage: "Phiếu chi",
  },
  paymentContent: {
    id: `${scope}.paymentContent`,
    defaultMessage: "Lập phiếu và In",
  },
  createPV: {
    id: `${scope}.createPV`,
    defaultMessage: "Lập phiếu",
  },
  printPV: {
    id: `${scope}.printPV`,
    defaultMessage: "In phiếu",
  },
  completed: {
    id: `${scope}.completed`,
    defaultMessage: "Hoàn tất",
  },
  contentPV: {
    id: `${scope}.contentPV`,
    defaultMessage:
      "Chưa cấu hình mẫu phiếu chi. Vui lòng liên hệ với {COMPANY_NAME} để được hỗ trợ.",
  },
  interBill: {
    id: `${scope}.interBill`,
    defaultMessage: "Liên",
  },
  cancelPVContent: {
    id: `${scope}.cancelPVContent`,
    defaultMessage: "Bạn chắc chắn muốn huỷ phiếu chi này?",
  },
  unlockPVContent: {
    id: `${scope}.unlockPVContent`,
    defaultMessage: "Bạn chắc chắn muốn mở khoá phiếu chi này?",
  },
  spent: {
    id: `${scope}.spent`,
    defaultMessage: "Đã chi",
  },
  closingEntryContent: {
    id: `${scope}.closingEntryContent`,
    defaultMessage:
      "Bạn chắc chắn chốt sổ phiếu chi này? Phiếu chi sẽ không được sửa đổi thông tin!",
  },
  enterBill: {
    id: `${scope}.enterBill`,
    defaultMessage: "Đã vào đơn",
  },
  stillOwe: {
    id: `${scope}.stillOwe`,
    defaultMessage: "Còn nợ",
  },
  debtRemove: {
    id: `${scope}.debtRemove`,
    defaultMessage: "Gạt nợ",
  },
  debts: {
    id: `${scope}.debts`,
    defaultMessage: "Công nợ",
  },
  confirmed: {
    id: `${scope}.confirmed`,
    defaultMessage: "Đã xác nhận",
  },
  denied: {
    id: `${scope}.denied`,
    defaultMessage: "Bị từ chối",
  },
  systemCancel: {
    id: `${scope}.systemCancel`,
    defaultMessage: "Đã hủy bởi hệ thống",
  },
  residentCancel: {
    id: `${scope}.residentCancel`,
    defaultMessage: "Đã hủy bởi cư dân",
  },
  evaluate: {
    id: `${scope}.evaluate`,
    defaultMessage: "Đánh giá",
  },
  reasonReject: {
    id: `${scope}.reasonReject`,
    defaultMessage: "Lý do bị từ chối",
  },
  reasonPlaceholder: {
    id: `${scope}.reasonPlaceholder`,
    defaultMessage: "Nhập lý do",
  },
  reasonRequest: {
    id: `${scope}.reasonRequest`,
    defaultMessage: "Vui lòng nhập lý do từ chối!",
  },
  feedbackCode: {
    id: `${scope}.feedbackCode`,
    defaultMessage: "Mã phản ánh",
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
    defaultMessage: "Chọn tiện ích",
  },
  date: {
    id: `${scope}.date`,
    defaultMessage: "Ngày sử dụng",
  },
  emptyDateError: {
    id: `${scope}.emptyDateError`,
    defaultMessage: "Ngày sử dụng không được để trống.",
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
  explain: {
    id: `${scope}.explain`,
    defaultMessage: "Diễn giải",
  },
  totalMoney: {
    id: `${scope}.totalMoney`,
    defaultMessage: "Tổng giá tiền cần thanh toán",
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
  cancelTitle: {
    id: `${scope}.cancelTitle`,
    defaultMessage: "Bạn có chắc chắn muốn từ chối đăng ký tiện ích này không?",
  },
  paymentStatus: {
    id: `${scope}.paymentStatus`,
    defaultMessage: "Tình trạng thanh toán",
  },
  price1: {
    id: `${scope}.price1`,
    defaultMessage: "Giá",
  },
  reject: {
    id: `${scope}.reject`,
    defaultMessage: "Từ chối",
  },
  approve: {
    id: `${scope}.approve`,
    defaultMessage: "Phê duyệt",
  },
  vnpay: {
    id: `${scope}.vnpay`,
    defaultMessage: "Ví VNPay",
  },
});
