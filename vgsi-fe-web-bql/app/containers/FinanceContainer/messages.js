/*
 * Finance Messages
 *
 * This contains all the text for the Finance container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.Finance";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the Finance container!",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  descriptionEn: {
    id: `${scope}.descriptionEn`,
    defaultMessage: "Mô tả(EN)",
  },
  service: {
    id: `${scope}.service`,
    defaultMessage: "Dịch vụ",
  },
  enterBill: {
    id: `${scope}.enterBill`,
    defaultMessage: "Đã vào đơn",
  },
  month: {
    id: `${scope}.month`,
    defaultMessage: "Tháng",
  },
  amountOfMoney: {
    id: `${scope}.amountOfMoney`,
    defaultMessage: "Số tiền",
  },
  amountOfMoneyError: {
    id: `${scope}.amountOfMoneyError`,
    defaultMessage: "Số tiền không được để trống.",
  },
  receipts: {
    id: `${scope}.receipts`,
    defaultMessage: "Phiếu thu",
  },
  errorProperty: {
    id: `${scope}.errorProperty`,
    defaultMessage: "Bất động sản không được để trống.",
  },
  choseProperty: {
    id: `${scope}.choseProperty`,
    defaultMessage: "Chọn bất động sản",
  },
  payer: {
    id: `${scope}.payer`,
    defaultMessage: "Người nộp tiền",
  },
  payer2: {
    id: `${scope}.payer2`,
    defaultMessage: "Người chi",
  },
  receiver: {
    id: `${scope}.receiver`,
    defaultMessage: "Người nhận",
  },
  owner: {
    id: `${scope}.owner`,
    defaultMessage: "Chủ hộ",
  },
  payerError: {
    id: `${scope}.payerError`,
    defaultMessage: "Xin vui lòng điền tên người thanh toán!",
  },
  payments: {
    id: `${scope}.payments`,
    defaultMessage: "Hình thức thanh toán",
  },
  phieuChi: {
    id: `${scope}.phieuChi`,
    defaultMessage: "Phiếu chi",
  },
  paymentsError: {
    id: `${scope}.paymentsError`,
    defaultMessage: "Xin vui lòng chọn phương thức thanh toán!",
  },
  chosePayment: {
    id: `${scope}.chosePayment`,
    defaultMessage: "Chọn phương thức thanh toán",
  },
  cash: {
    id: `${scope}.cash`,
    defaultMessage: "Tiền mặt",
  },
  transfer: {
    id: `${scope}.transfer`,
    defaultMessage: "Chuyển khoản",
  },
  emptyData: {
    id: `${scope}.emptyData`,
    defaultMessage: "Không có dữ liệu",
  },
  createVoucher: {
    id: `${scope}.createVoucher`,
    defaultMessage: "Tạo phiếu chi",
  },
  createVote: {
    id: `${scope}.createVote`,
    defaultMessage: "Tạo phiếu ",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  print: {
    id: `${scope}.print`,
    defaultMessage: "In",
  },
  paymentConfirmation: {
    id: `${scope}.paymentConfirmation`,
    defaultMessage: "Xác nhận thanh toán",
  },
  year: {
    id: `${scope}.year`,
    defaultMessage: "Năm",
  },
  closingEntry: {
    id: `${scope}.closingEntry`,
    defaultMessage: "Chốt sổ",
  },
  paid: {
    id: `${scope}.paid`,
    defaultMessage: "Đã thanh toán",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  formCode: {
    id: `${scope}.formCode`,
    defaultMessage: "Mã phiếu",
  },
  voterReview: {
    id: `${scope}.voterReview`,
    defaultMessage: "Người duyệt phiếu",
  },
  feePayer: {
    id: `${scope}.feePayer`,
    defaultMessage: "Người đóng phí",
  },
  closingTime: {
    id: `${scope}.closingTime`,
    defaultMessage: "Thời gian đóng",
  },
  form: {
    id: `${scope}.form`,
    defaultMessage: "Hình thức",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  totalAmount: {
    id: `${scope}.totalAmount`,
    defaultMessage: "Tổng tiền",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage: "Tổng số",
  },
  content1: {
    id: `${scope}.content1`,
    defaultMessage:
      "Bạn có chắc chắn xác nhận phiếu thu này đã được thanh toán?",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  continue: {
    id: `${scope}.continue`,
    defaultMessage: "Đồng ý",
  },
  content2: {
    id: `${scope}.content2`,
    defaultMessage:
      "Bạn chắc chắn chốt sổ phiếu thu này ? Phiếu thu sẽ không được sửa đổi thông tin!",
  },
  content3: {
    id: `${scope}.content3`,
    defaultMessage: "Bạn chắc chắn muốn phiếu thu này?",
  },
  dayVouchers: {
    id: `${scope}.dayVouchers`,
    defaultMessage: "Ngày chứng từ",
  },
  numberOfVouchers: {
    id: `${scope}.numberOfVouchers`,
    defaultMessage: "Số chứng từ",
  },
  customerName: {
    id: `${scope}.customerName`,
    defaultMessage: "Chủ hộ",
  },
  customer: {
    id: `${scope}.customer`,
    defaultMessage: "Chủ hộ",
  },
  vnd: {
    id: `${scope}.vnd`,
    defaultMessage: "đ",
  },
  vnd2: {
    id: `${scope}.vnd2`,
    defaultMessage: "Đ",
  },
  collector: {
    id: `${scope}.collector`,
    defaultMessage: "Người thu",
  },
  submitter: {
    id: `${scope}.submitter`,
    defaultMessage: "Người nộp",
  },
  fromDate: {
    id: `${scope}.fromDate`,
    defaultMessage: "Từ ngày",
  },
  toDate: {
    id: `${scope}.toDate`,
    defaultMessage: "Đến ngày",
  },
  fillDate: {
    id: `${scope}.fillDate`,
    defaultMessage: "Ngày nộp",
  },
  implementDate: {
    id: `${scope}.implementDate`,
    defaultMessage: "Ngày thực hiện",
  },
  status2: {
    id: `${scope}.status2`,
    defaultMessage: "Tình trạng",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  detail: {
    id: `${scope}.detail`,
    defaultMessage: "Chi tiết",
  },
  cancellationSlip: {
    id: `${scope}.cancellationSlip`,
    defaultMessage: "Phiếu hủy",
  },
  choseAddress: {
    id: `${scope}.choseAddress`,
    defaultMessage: "Chọn địa chỉ",
  },
  address: {
    id: `${scope}.address`,
    defaultMessage: "Địa chỉ:",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  week: {
    id: `${scope}.week`,
    defaultMessage: "Tuần",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  waitForPay: {
    id: `${scope}.waitForPay`,
    defaultMessage: "Chờ thanh toán",
  },
  selected: {
    id: `${scope}.selected`,
    defaultMessage: "Đã chọn",
  },
  bill: {
    id: `${scope}.bill`,
    defaultMessage: "Phiếu",
  },
  exportVData: {
    id: `${scope}.exportVData`,
    defaultMessage: "Export dữ liệu phiếu thu",
  },
  exportVData5: {
    id: `${scope}.exportVData5`,
    defaultMessage: "Export dữ liệu phiếu chi",
  },
  login: {
    id: `${scope}.login`,
    defaultMessage: "login",
  },
  canceler: {
    id: `${scope}.canceler`,
    defaultMessage: "Người huỷ",
  },
  cancellationTime: {
    id: `${scope}.cancellationTime`,
    defaultMessage: "Ngày huỷ",
  },
  exportVData2: {
    id: `${scope}.exportVData2`,
    defaultMessage: "Export dữ liệu phiếu thu huỷ",
  },
  exportVData3: {
    id: `${scope}.exportVData3`,
    defaultMessage: "Export dữ liệu phiếu chi huỷ",
  },
  loaiPhieu: {
    id: `${scope}.loaiPhieu`,
    defaultMessage: "Loại phiếu",
  },
  exportVData4: {
    id: `${scope}.exportVData4`,
    defaultMessage: "Export dữ liệu sổ quỹ",
  },
  unpaidDebt: {
    id: `${scope}.unpaidDebt`,
    defaultMessage: "Công nợ chưa trả",
  },
  openingDebit: {
    id: `${scope}.openingDebit`,
    defaultMessage: "Nợ đầu kỳ",
  },
  arise: {
    id: `${scope}.arise`,
    defaultMessage: "Phát sinh",
  },
  receivables: {
    id: `${scope}.receivables`,
    defaultMessage: "Phải thu",
  },
  collected: {
    id: `${scope}.collected`,
    defaultMessage: "Đã thu",
  },
  closingDebit: {
    id: `${scope}.closingDebit`,
    defaultMessage: "Nợ cuối kỳ",
  },
  stillOwe: {
    id: `${scope}.stillOwe`,
    defaultMessage: "Còn nợ",
  },
  feeNotice: {
    id: `${scope}.feeNotice`,
    defaultMessage: "Thông báo phí",
  },
  choseMonth: {
    id: `${scope}.choseMonth`,
    defaultMessage: "Chọn tháng",
  },
  paidDebt: {
    id: `${scope}.paidDebt`,
    defaultMessage: "Đã trả công nợ",
  },
  remainingDebt: {
    id: `${scope}.remainingDebt`,
    defaultMessage: "Còn công nợ",
  },
  debtReminder1: {
    id: `${scope}.debtReminder1`,
    defaultMessage: "Nhắc nợ lần 1",
  },
  debtReminder2: {
    id: `${scope}.debtReminder2`,
    defaultMessage: "Nhắc nợ lần 2",
  },
  debtReminder3: {
    id: `${scope}.debtReminder3`,
    defaultMessage: "Nhắc nợ lần 3",
  },
  pay: {
    id: `${scope}.pay`,
    defaultMessage: "Tạo phiếu thu",
  },
  prepay: {
    id: `${scope}.prepay`,
    defaultMessage: "Trả trước",
  },
  noDebt: {
    id: `${scope}.noDebt`,
    defaultMessage: "Không nợ",
  },
  serviceType: {
    id: `${scope}.serviceType`,
    defaultMessage: "Loại dịch vụ",
  },
  choseService: {
    id: `${scope}.choseService`,
    defaultMessage: "Chọn dịch vụ",
  },
  paymentDueDate: {
    id: `${scope}.paymentDueDate`,
    defaultMessage: "Hạn thanh toán",
  },
  haveToPay: {
    id: `${scope}.haveToPay`,
    defaultMessage: "Còn phải thu",
  },
  createAt: {
    id: `${scope}.createAt`,
    defaultMessage: "Ngày tạo",
  },
  approvedBy: {
    id: `${scope}.approvedBy`,
    defaultMessage: "Người duyệt",
  },
  unpaid: {
    id: `${scope}.unpaid`,
    defaultMessage: "Chưa thanh toán",
  },
  delete: {
    id: `${scope}.delete`,
    defaultMessage: "Xóa",
  },
  permission: {
    id: `${scope}.permission`,
    defaultMessage: "Bạn cần cấp thêm quyền Thu phí để thực hiện chức năng này",
  },
  fee: {
    id: `${scope}.fee`,
    defaultMessage: "Thu phí",
  },
  totalFeePayable: {
    id: `${scope}.totalFeePayable`,
    defaultMessage: "Tổng phí phải thu",
  },
  totalFeeCollected: {
    id: `${scope}.totalFeeCollected`,
    defaultMessage: "Tổng phí đã thu",
  },
  refresh: {
    id: `${scope}.refresh`,
    defaultMessage: "Làm mới trang",
  },
  totalFeeLeft: {
    id: `${scope}.totalFeeLeft`,
    defaultMessage: "Tổng phí còn phải thu",
  },
  export: {
    id: `${scope}.export`,
    defaultMessage: "Export dữ liệu",
  },
  deleteContent: {
    id: `${scope}.deleteContent`,
    defaultMessage: "Bạn chắc chắn muốn xoá phí dịch vụ này không ?",
  },
  deleteModalContent: {
    id: `${scope}.deleteModalContent`,
    defaultMessage:
      "Phí này đang ở trạng thái thanh toán nên không thể xóa được!",
  },
  deleteModalContent2: {
    id: `${scope}.deleteModalContent2`,
    defaultMessage:
      "Phí này đang ở trạng thái thanh toán nên không thể chỉnh sửa được!",
  },
  editInformation: {
    id: `${scope}.editInformation`,
    defaultMessage: "Chỉnh sửa thông tin",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  payerError2: {
    id: `${scope}.payerError2`,
    defaultMessage: "Tên người đóng phí không được để trống.",
  },
  paymentsError2: {
    id: `${scope}.paymentsError2`,
    defaultMessage: "Hình thức thanh toán không được để trống.",
  },
  emptyApartmentError: {
    id: `${scope}.emptyApartmentError`,
    defaultMessage: "Bất động sản không được để trống.",
  },
  apartmentError: {
    id: `${scope}.apartmentError`,
    defaultMessage: "Bất động sản được chọn chưa có chủ hộ.",
  },
  chosePayment2: {
    id: `${scope}.chosePayment2`,
    defaultMessage: "Chọn hình thức thanh toán",
  },
  editPaymentFee: {
    id: `${scope}.editPaymentFee`,
    defaultMessage: "Chỉnh sửa phí thanh toán",
  },
  feeError: {
    id: `${scope}.feeError`,
    defaultMessage: "Mô tả phí không được để trống.",
  },
  feeErrorEn: {
    id: `${scope}.feeErrorEn`,
    defaultMessage: "Mô tả phí tiếng anh không được để trống.",
  },
  empty: {
    id: `${scope}.empty`,
    defaultMessage: "Trống",
  },
  monthError: {
    id: `${scope}.monthError`,
    defaultMessage: "Tháng thanh toán không được để trống.",
  },
  choseDate: {
    id: `${scope}.choseDate`,
    defaultMessage: "Chọn ngày",
  },
  statusError: {
    id: `${scope}.statusError`,
    defaultMessage: "Trạng thái không được để trống.",
  },
  choseStatus: {
    id: `${scope}.choseStatus`,
    defaultMessage: "Chọn trạng thái",
  },
  canceled: {
    id: `${scope}.canceled`,
    defaultMessage: "Đã hủy",
  },
  vnpay: {
    id: `${scope}.vnpay`,
    defaultMessage: "Ví VNPay",
  },
  voucher: {
    id: `${scope}.voucher`,
    defaultMessage: "phiếu",
  },
  voidedVoucher: {
    id: `${scope}.voidedVoucher`,
    defaultMessage: "Phiếu hủy",
  },
});
