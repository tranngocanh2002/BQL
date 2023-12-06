/*
 * AccountContainer Messages
 *
 * This contains all the text for the AccountContainer container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.NotificationContainer";

export default defineMessages({
  rowTreeSelect: {
    id: `${scope}.rowTreeSelect`,
    defaultMessage: "Chọn danh sách để gửi",
  },
  totalResident: {
    id: `${scope}.totalResident`,
    defaultMessage: "Tổng cư dân",
  },
  confirm: {
    id: `${scope}.confirm`,
    defaultMessage: "Xác nhận",
  },
  modalConfirm1: {
    id: `${scope}.modalConfirm1`,
    defaultMessage:
      "Thông báo sẽ được gửi đến toàn bộ bất động sản đã chọn. Bạn có chắc chắn muốn tiếp tục?",
  },
  continue: {
    id: `${scope}.continue`,
    defaultMessage: "Tiếp tục",
  },
  cancel: {
    id: `${scope}.cancel`,
    defaultMessage: "Hủy",
  },
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
  },
  reload: {
    id: `${scope}.reload`,
    defaultMessage: "Làm mới",
  },
  property: {
    id: `${scope}.property`,
    defaultMessage: "Bất động sản",
  },
  owner: {
    id: `${scope}.owner`,
    defaultMessage: "Chủ hộ",
  },
  address: {
    id: `${scope}.address`,
    defaultMessage: "Địa chỉ",
  },
  notConfig: {
    id: `${scope}.notConfig`,
    defaultMessage: "Chưa cấu hình",
  },
  appNotInstall: {
    id: `${scope}.appNotInstall`,
    defaultMessage: "Chưa cài ứng dụng",
  },
  appInstalled: {
    id: `${scope}.appInstalled`,
    defaultMessage: "Đã cài ứng dụng",
  },
  createNew: {
    id: `${scope}.createNew`,
    defaultMessage: "Tạo mới",
  },
  saveDraft: {
    id: `${scope}.saveDraft`,
    defaultMessage: "Lưu nháp",
  },
  public: {
    id: `${scope}.public`,
    defaultMessage: "Công khai",
  },
  contentSend: {
    id: `${scope}.contentSend`,
    defaultMessage: "Nội dung gửi",
  },
  title: {
    id: `${scope}.title`,
    defaultMessage: "Tiêu đề",
  },
  titleRequired: {
    id: `${scope}.titleRequired`,
    defaultMessage: "Tiêu đề không được để trống.",
  },
  titleEn: {
    id: `${scope}.titleEn`,
    defaultMessage: "Tiêu đề (EN)",
  },
  titleEnRequired: {
    id: `${scope}.titleEnRequired`,
    defaultMessage: "Tiêu đề tiếng Anh không được để trống.",
  },
  content: {
    id: `${scope}.content`,
    defaultMessage: "Nội dung",
  },
  contentRequired: {
    id: `${scope}.contentRequired`,
    defaultMessage: "Nội dung không được để trống.",
  },
  category: {
    id: `${scope}.category`,
    defaultMessage: "Danh mục",
  },
  categoryRequired: {
    id: `${scope}.categoryRequired`,
    defaultMessage: "Danh mục không được để trống.",
  },
  chooseCategoryPlaceholder: {
    id: `${scope}.chooseCategoryPlaceholder`,
    defaultMessage: "Chọn danh mục thông báo",
  },
  surveyResident: {
    id: `${scope}.surveyResident`,
    defaultMessage: "Khảo sát cư dân",
  },
  surveyResidentTooltip: {
    id: `${scope}.surveyResidentTooltip`,
    defaultMessage:
      "Thời điểm diễn ra khảo sát, hệ thống sẽ tự động gửi khảo sát cho cư dân",
  },
  surveyDeadlineRequired: {
    id: `${scope}.surveyDeadlineRequired`,
    defaultMessage: "Thời hạn khảo sát không được để trống.",
  },
  surveyDeadlinePlaceholder: {
    id: `${scope}.surveyDeadlinePlaceholder`,
    defaultMessage: "Chọn thời hạn khảo sát",
  },
  event: {
    id: `${scope}.event`,
    defaultMessage: "Sự kiện",
  },
  eventTooltip: {
    id: `${scope}.eventTooltip`,
    defaultMessage:
      "Hệ thống sẽ gửi bản tin sự kiện tới App CD trước 24h diễn ra sự kiện nếu thực hiện công khai bản tin",
  },
  eventDateRequired: {
    id: `${scope}.eventDateRequired`,
    defaultMessage: "Thời gian diễn ra sự kiện không được để trống.",
  },
  publicNow: {
    id: `${scope}.publicNow`,
    defaultMessage: "Công khai ngay",
  },
  publicAt: {
    id: `${scope}.publicAt`,
    defaultMessage: "Công khai vào lúc",
  },
  publicAtRequired: {
    id: `${scope}.publicAtRequired`,
    defaultMessage: "Thời gian công khai không được để trống.",
  },
  imageTooLarge: {
    id: `${scope}.imageTooLarge`,
    defaultMessage: "Ảnh đính kèm vượt quá dung lượng",
  },
  attachImage: {
    id: `${scope}.attachImage`,
    defaultMessage: "Ảnh đính kèm",
  },
  fileTooLarge: {
    id: `${scope}.fileTooLarge`,
    defaultMessage: "Tệp tải lên vượt quá 25MB",
  },
  attachFile: {
    id: `${scope}.attachFile`,
    defaultMessage: "Tệp đính kèm",
  },
  fileUpload: {
    id: `${scope}.fileUpload`,
    defaultMessage: "Tải tệp lên",
  },
  fileUploadTooltip: {
    id: `${scope}.fileUploadTooltip`,
    defaultMessage:
      "(Định dạng .doc, .docx, .pdf, .xls, .xlsx không vượt quá 10MB)",
  },
  sendTo: {
    id: `${scope}.sendTo`,
    defaultMessage: "Gửi tới",
  },
  sendToRequired: {
    id: `${scope}.sendToRequired`,
    defaultMessage: "Danh sách gửi không được để trống.",
  },
  sendMethod: {
    id: `${scope}.sendMethod`,
    defaultMessage: "Hình thức gửi",
  },
  sendMethodRequired: {
    id: `${scope}.sendMethodRequired`,
    defaultMessage: "Cần chọn tối thiểu 1 hình thức gửi.",
  },
  sendEmail: {
    id: `${scope}.sendEmail`,
    defaultMessage: "Gửi qua Email",
  },
  sendApp: {
    id: `${scope}.sendApp`,
    defaultMessage: "Gửi qua App (mặc định)",
  },
  sendSMS: {
    id: `${scope}.sendSMS`,
    defaultMessage: "Gửi qua SMS",
  },
  sendTarget: {
    id: `${scope}.sendTarget`,
    defaultMessage: "Đối tượng nhận bản tin",
  },
  sendTargetRequired: {
    id: `${scope}.sendTargetRequired`,
    defaultMessage: "Cần chọn tối thiểu 1 đối tượng nhận thông báo.",
  },
  ownerDefault: {
    id: `${scope}.ownerDefault`,
    defaultMessage: "Chủ hộ (Mặc định)",
  },
  member: {
    id: `${scope}.member`,
    defaultMessage: "Thành viên",
  },
  guest: {
    id: `${scope}.guest`,
    defaultMessage: "Khách thuê",
  },
  smsContent: {
    id: `${scope}.smsContent`,
    defaultMessage: "Nội dung SMS",
  },
  smsContentRequired: {
    id: `${scope}.smsContentRequired`,
    defaultMessage: "Nội dung SMS không được để trống.",
  },
  phoneSend: {
    id: `${scope}.phoneSend`,
    defaultMessage: "Số điện thoại gửi thêm",
  },
  addPhone: {
    id: `${scope}.addPhone`,
    defaultMessage: "Thêm số",
  },
  emailWrongFormat: {
    id: `${scope}.emailWrongFormat`,
    defaultMessage: "Email không đúng định dạng",
  },
  emailSend: {
    id: `${scope}.emailSend`,
    defaultMessage: "Email gửi thêm",
  },
  addEmail: {
    id: `${scope}.addEmail`,
    defaultMessage: "Thêm email",
  },
  sms: {
    id: `${scope}.sms`,
    defaultMessage: "TIN NHẮN",
  },
  justNow: {
    id: `${scope}.justNow`,
    defaultMessage: "vừa xong",
  },
  sendList: {
    id: `${scope}.sendList`,
    defaultMessage: "Danh sách gửi",
  },
  totalProperty: {
    id: `${scope}.totalProperty`,
    defaultMessage: "Tổng bất động sản",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage: "Tổng số {total, plural, one {# cư dân} other {# cư dân}}",
  },
  saveDraftSuccess: {
    id: `${scope}.saveDraftSuccess`,
    defaultMessage: "Lưu nháp thành công.",
  },
  createNotificationSuccess: {
    id: `${scope}.createNotificationSuccess`,
    defaultMessage: "Tạo thông báo thành công.",
  },
  createNotification: {
    id: `${scope}.createNotification`,
    defaultMessage: "Tạo thông báo",
  },
  chooseTimePlaceholder: {
    id: `${scope}.chooseTimePlaceholder`,
    defaultMessage: "Chọn thời điểm",
  },
  notFindNotificationDetail: {
    id: `${scope}.notFindNotificationDetail`,
    defaultMessage: "Không tìm thấy chi tiết thông báo.",
  },
  back: {
    id: `${scope}.back`,
    defaultMessage: "Quay lại",
  },
  answer: {
    id: `${scope}.answer`,
    defaultMessage: "Trả lời",
  },
  notAnswer: {
    id: `${scope}.notAnswer`,
    defaultMessage: "Chưa trả lời",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  disagree: {
    id: `${scope}.disagree`,
    defaultMessage: "Không đồng ý",
  },
  answerTime: {
    id: `${scope}.answerTime`,
    defaultMessage: "Thời gian",
  },
  surveyDeadline: {
    id: `${scope}.surveyDeadline`,
    defaultMessage: "Thời hạn khảo sát",
  },
  lastEditDate: {
    id: `${scope}.lastEditDate`,
    defaultMessage: "Lần chỉnh sửa cuối",
  },
  createDate: {
    id: `${scope}.createDate`,
    defaultMessage: "Ngày tạo",
  },
  sent: {
    id: `${scope}.sent`,
    defaultMessage: "Đã gửi",
  },
  read: {
    id: `${scope}.read`,
    defaultMessage: "Đã đọc",
  },
  sendError: {
    id: `${scope}.sendError`,
    defaultMessage: "Gửi lỗi",
  },
  timeSent: {
    id: `${scope}.timeSent`,
    defaultMessage: "Thời gian gửi",
  },
  totalResidentAnswer: {
    id: `${scope}.totalResidentAnswer`,
    defaultMessage: "Tổng số cư dân đã trả lời",
  },
  edit: {
    id: `${scope}.edit`,
    defaultMessage: "Chỉnh sửa",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  draft: {
    id: `${scope}.draft`,
    defaultMessage: "Nháp",
  },
  nearestEditDate: {
    id: `${scope}.nearestEditDate`,
    defaultMessage: "Ngày chỉnh sửa gần nhất",
  },
  statistic: {
    id: `${scope}.statistic`,
    defaultMessage: "Thống kê",
  },
  sendCount: {
    id: `${scope}.sendCount`,
    defaultMessage: "Lượt gửi",
  },
  needSend: {
    id: `${scope}.needSend`,
    defaultMessage: "Cần gửi",
  },
  residentAnswerList: {
    id: `${scope}.residentAnswerList`,
    defaultMessage: "Danh sách cư dân trả lời khảo sát",
  },
  surveyAnswer: {
    id: `${scope}.surveyAnswer`,
    defaultMessage: "Kết quả khảo sát",
  },
  surveyAnswerList: {
    id: `${scope}.surveyAnswerList`,
    defaultMessage: "Danh sách trả lời khảo sát",
  },
  residentAnswerStatistic: {
    id: `${scope}.residentAnswerStatistic`,
    defaultMessage: "Thống kê cư dân tham gia khảo sát",
  },
  noAnswerYet: {
    id: `${scope}.noAnswerYet`,
    defaultMessage: "Chưa có câu trả lời nào",
  },
  answerNumber: {
    id: `${scope}.answerNumber`,
    defaultMessage: "Số câu trả lời",
  },
  answerChart: {
    id: `${scope}.answerChart`,
    defaultMessage: "Biểu đồ phản hồi",
  },
  totals: {
    id: `${scope}.totals`,
    defaultMessage: "Tổng số {total}",
  },
  noFeeAnnouncementDefault: {
    id: `${scope}.noFeeAnnouncementDefault`,
    defaultMessage:
      "Hiện tại chưa có danh mục mặc định cho thông báo phí. Vui lòng tạo danh mục trước!",
  },
  announcementFeeModalContent: {
    id: `${scope}.announcementFeeModalContent`,
    defaultMessage:
      "Thông báo phí sẽ được gửi đến toàn bộ bất động sản đã chọn. Bạn có chắc chắn muốn tiếp tục tạo phí?",
  },
  findProperty: {
    id: `${scope}.findProperty`,
    defaultMessage: "Tìm bất động sản",
  },
  endTermDebt: {
    id: `${scope}.endTermDebt`,
    defaultMessage: "Nợ cuối kỳ",
  },
  feeAnnouncement: {
    id: `${scope}.feeAnnouncement`,
    defaultMessage: "Thông báo phí",
  },
  debtRemind1: {
    id: `${scope}.debtRemind1`,
    defaultMessage: "Nhắc nợ lần 1",
  },
  debtRemind2: {
    id: `${scope}.debtRemind2`,
    defaultMessage: "Nhắc nợ lần 2",
  },
  debtRemind3: {
    id: `${scope}.debtRemind3`,
    defaultMessage: "Nhắc nợ lần 3",
  },
  pauseService: {
    id: `${scope}.pauseService`,
    defaultMessage: "Tạm ngưng dịch vụ",
  },
  chooseNotificationTemplate: {
    id: `${scope}.chooseNotificationTemplate`,
    defaultMessage: "Chọn mẫu thông báo",
  },
  example: {
    id: `${scope}.example`,
    defaultMessage: "Ví dụ",
  },
  ownerName: {
    id: `${scope}.ownerName`,
    defaultMessage: "Tên chủ hộ",
  },
  totalFee: {
    id: `${scope}.totalFee`,
    defaultMessage: "Tổng phí",
  },
  paymentCode: {
    id: `${scope}.paymentCode`,
    defaultMessage: "Mã thanh toán",
  },
  createFeeAnnouncement: {
    id: `${scope}.createFeeAnnouncement`,
    defaultMessage: "Tạo thông báo phí",
  },
  done: {
    id: `${scope}.done`,
    defaultMessage: "Hoàn thành",
  },
  updateTemplate: {
    id: `${scope}.updateTemplate`,
    defaultMessage: "Cập nhật mẫu",
  },
  notificationTemplateDefault: {
    id: `${scope}.notificationTemplateDefault`,
    defaultMessage: "Mẫu thông báo mặc định",
  },
  default: {
    id: `${scope}.default`,
    defaultMessage: "Mặc định",
  },
  chooseTemplate: {
    id: `${scope}.chooseTemplate`,
    defaultMessage: "Chọn mẫu",
  },
  noNameTemplate: {
    id: `${scope}.noNameTemplate`,
    defaultMessage: "Chưa đặt tên mẫu",
  },
  template: {
    id: `${scope}.template`,
    defaultMessage: "Mẫu {total}",
  },
  deleteNotificationModalTitle: {
    id: `${scope}.deleteNotificationModalTitle`,
    defaultMessage: "Bạn chắc chắn muốn xoá thông báo này?",
  },
  searchNotificationTitle: {
    id: `${scope}.searchNotificationTitle`,
    defaultMessage: "Tìm kiếm thông báo theo tiêu đề",
  },
  totalNotification: {
    id: `${scope}.totalNotification`,
    defaultMessage:
      "Tổng số {total, plural, one {# thông báo} other {# thông báo}}",
  },
  update: {
    id: `${scope}.update`,
    defaultMessage: "Cập nhật",
  },
  addNew: {
    id: `${scope}.addNew`,
    defaultMessage: "Thêm mới",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
  save: {
    id: `${scope}.save`,
    defaultMessage: "Lưu",
  },
  saveNotificationSuccess: {
    id: `${scope}.saveNotificationSuccess`,
    defaultMessage: "Lưu thông báo thành công",
  },
  publicNotificationSuccess: {
    id: `${scope}.publicNotificationSuccess`,
    defaultMessage: "Công khai thông báo thành công",
  },
  normalAnnouncement: {
    id: `${scope}.normalAnnouncement`,
    defaultMessage: "Thông báo thường",
  },
  surveyAnnouncement: {
    id: `${scope}.surveyAnnouncement`,
    defaultMessage: "Thông báo khảo sát",
  },
  ownerFamily: {
    id: `${scope}.ownerFamily`,
    defaultMessage: "Gia đình chủ hộ",
  },
  guestFamily: {
    id: `${scope}.guestFamily`,
    defaultMessage: "Gia đình khách thuê",
  },
  all: {
    id: `${scope}.all`,
    defaultMessage: "Tất cả",
  },
  formula: {
    id: `${scope}.formula`,
    defaultMessage: "Công thức tính",
  },
  formulaTooltip: {
    id: `${scope}.formulaTooltip`,
    defaultMessage: "Công thức tính kết quả khảo sát",
  },
  formulaRequired: {
    id: `${scope}.formulaRequired`,
    defaultMessage: "Công thức tính không được để trống.",
  },
  formula1: {
    id: `${scope}.formula1`,
    defaultMessage: "Theo diện tích",
  },
  formula2: {
    id: `${scope}.formula2`,
    defaultMessage: "Theo đầu người",
  },
  formulaTooltip1: {
    id: `${scope}.formulaTooltip1`,
    defaultMessage:
      "Tính theo diện tích. % đồng ý = Tổng diện tích BĐS đồng ý / Tổng diện tích BĐS tham gia khảo sát * 100",
  },
  formulaTooltip2: {
    id: `${scope}.formulaTooltip2`,
    defaultMessage:
      "Tính theo đầu người. % đồng ý = Tổng số người đồng ý / Tổng số người được tham gia khảo sát * 100",
  },
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
  },
  notificationDetail: {
    id: `${scope}.notificationDetail`,
    defaultMessage: "Chi tiết bảng tin",
  },
  editNotification: {
    id: `${scope}.editNotification`,
    defaultMessage: "Chỉnh sửa bảng tin",
  },
  deleteNotification: {
    id: `${scope}.deleteNotification`,
    defaultMessage: "Xoá bảng tin",
  },
  author: {
    id: `${scope}.author`,
    defaultMessage: "Người tạo",
  },
  resident: {
    id: `${scope}.resident`,
    defaultMessage: "Cư dân",
  },
  searchNotificationAuthor: {
    id: `${scope}.searchNotificationAuthor`,
    defaultMessage: "Tìm kiếm thông báo theo người tạo",
  },
  newsTemplate: {
    id: `${scope}.newsTemplate`,
    defaultMessage: "Mẫu bản tin",
  },
  surveyAnswerStatistic: {
    id: `${scope}.surveyAnswerStatistic`,
    defaultMessage: "Thống kê kết quả khảo sát",
  },
  updateNotificationSuccess: {
    id: `${scope}.updateNotificationSuccess`,
    defaultMessage: "Cập nhật thành công",
  },
  notRead: {
    id: `${scope}.notRead`,
    defaultMessage: "Chưa đọc",
  },
  result: {
    id: `${scope}.result`,
    defaultMessage: "Kết quả",
  },
  readStatus: {
    id: `${scope}.readStatus`,
    defaultMessage: "Trạng thái đọc bản tin",
  },
  totalLabel: {
    id: `${scope}.totalLabel`,
    defaultMessage: "Tổng",
  },
  notificationType: {
    id: `${scope}.notificationType`,
    defaultMessage: "Loại thông báo",
  },
  regularNotification: {
    id: `${scope}.regularNotification`,
    defaultMessage: "Thông báo thường",
  },
  surveyNotification: {
    id: `${scope}.surveyNotification`,
    defaultMessage: "Thông báo khảo sát",
  },
  feeNotification: {
    id: `${scope}.feeNotification`,
    defaultMessage: "Thông báo phí",
  },
  endDept: {
    id: `${scope}.endDept`,
    defaultMessage: "Nợ cuối kỳ",
  },
  success: {
    id: `${scope}.success`,
    defaultMessage: "thành công",
  },
  prepay: {
    id: `${scope}.prepay`,
    defaultMessage: "Trả trước",
  },
  noDebt: {
    id: `${scope}.noDebt`,
    defaultMessage: "Không nợ",
  },
  stillOwe: {
    id: `${scope}.stillOwe`,
    defaultMessage: "Còn nợ",
  },
  timer: {
    id: `${scope}.timer`,
    defaultMessage: "Hẹn giờ",
  },
  isAfterSendAt: {
    id: `${scope}.isAfterSendAt`,
    defaultMessage: "Thời gian công khai phải lớn hơn thời gian hiện tại",
  },
  isAfterSendEventAt: {
    id: `${scope}.isAfterSendEventAt`,
    defaultMessage: "Thời gian diễn ra sự kiện phải lớn hơn thời gian hiện tại",
  },
  isAfterSurveyDeadline: {
    id: `${scope}.isAfterSurveyDeadline`,
    defaultMessage:
      "Thời hạn khảo sát phải lớn hơn 1 ngày so với thời gian hiện tại",
  },
});
