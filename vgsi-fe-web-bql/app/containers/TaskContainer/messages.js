import { defineMessages } from "react-intl";

export const scope = "app.containers.Task";

export default defineMessages({
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
  search: {
    id: `${scope}.search`,
    defaultMessage: "Tìm kiếm",
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
  action: {
    id: `${scope}.action`,
    defaultMessage: "Thao tác",
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
  addNew: {
    id: `${scope}.addNew`,
    defaultMessage: "Thêm mới",
  },
  task: {
    id: `${scope}.task`,
    defaultMessage: "công việc",
  },
  taskType: {
    id: `${scope}.taskType`,
    defaultMessage: "Công việc",
  },
  tasks: {
    id: `${scope}.tasks`,
    defaultMessage: "công việc",
  },
  assignees: {
    id: `${scope}.assignees`,
    defaultMessage: "Người thực hiện",
  },
  followers: {
    id: `${scope}.followers`,
    defaultMessage: "Người theo dõi",
  },
  attachments: {
    id: `${scope}.attachments`,
    defaultMessage: "Tài liệu đính kèm",
  },
  priority: {
    id: `${scope}.priority`,
    defaultMessage: "Ưu tiên",
  },
  description: {
    id: `${scope}.description`,
    defaultMessage: "Mô tả",
  },
  comment: {
    id: `${scope}.comment`,
    defaultMessage: "Bình luận",
  },
  noComment: {
    id: `${scope}.noComment`,
    defaultMessage: "Không có bình luận nào",
  },
  noActivity: {
    id: `${scope}.noActivity`,
    defaultMessage: "Không có hoạt động nào",
  },
  creator: {
    id: `${scope}.creator`,
    defaultMessage: "Người tạo việc",
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
  activity: {
    id: `${scope}.activity`,
    defaultMessage: "Hoạt động",
  },
  maxSizeImg: {
    id: `${scope}.maxSizeImg`,
    defaultMessage: "Dung lượng mỗi ảnh tối đa 10MB",
  },
  maxSizeDoc: {
    id: `${scope}.maxSizeDoc`,
    defaultMessage: "Dung lượng tài liệu tối đa 20MB",
  },
  fileIncorrect: {
    id: `${scope}.fileIncorrect`,
    defaultMessage: "Tệp tải lên không đúng định dạng",
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
  upload: {
    id: `${scope}.upload`,
    defaultMessage: "Tải lên",
  },
  conditionDate: {
    id: `${scope}.conditionDate`,
    defaultMessage: "Tải lên",
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
  cancelContent: {
    id: `${scope}.cancelContent`,
    defaultMessage: "Bạn có chắc chắn muốn huỷ thêm mới công việc này không?",
  },
  okText: {
    id: `${scope}.okText`,
    defaultMessage: "Đồng ý",
  },
  cancelText: {
    id: `${scope}.cancelText`,
    defaultMessage: "Hủy",
  },
  createSuccess: {
    id: `${scope}.createSuccess`,
    defaultMessage: "Thêm mới công việc thành công",
  },
  updateSuccess: {
    id: `${scope}.updateSuccess`,
    defaultMessage: "Chỉnh sửa thành công",
  },
  sortedBy: {
    id: `${scope}.sortedBy`,
    defaultMessage: "Sắp xếp theo",
  },
  noPriority: {
    id: `${scope}.noPriority`,
    defaultMessage: "Không ưu tiên",
  },
  updatedAt: {
    id: `${scope}.updatedAt`,
    defaultMessage: "Thời gian cập nhật",
  },
  createdAt: {
    id: `${scope}.createdAt`,
    defaultMessage: "Thời gian tạo",
  },
  assigning: {
    id: `${scope}.assigning`,
    defaultMessage: "Bạn đã giao",
  },
  performing: {
    id: `${scope}.performing`,
    defaultMessage: "Bạn thực hiện",
  },
  following: {
    id: `${scope}.following`,
    defaultMessage: "Bạn theo dõi",
  },
  deleteTask: {
    id: `${scope}.deleteTask`,
    defaultMessage: "Xoá công việc",
  },
  deleteTaskQuestion: {
    id: `${scope}.deleteTaskQuestion`,
    defaultMessage: "Bạn có chắc chắn muốn xoá công việc này không?",
  },
  deleteTaskSuccess: {
    id: `${scope}.deleteTaskSuccess`,
    defaultMessage: "Xoá công việc thành công",
  },
  agree: {
    id: `${scope}.agree`,
    defaultMessage: "Đồng ý",
  },
  plhComment: {
    id: `${scope}.plhComment`,
    defaultMessage: "Bình luận và ấn enter để gửi",
  },
  choseType: {
    id: `${scope}.choseType`,
    defaultMessage: "Chọn",
  },
  left: {
    id: `${scope}.left`,
    defaultMessage: "Còn {left} ngày",
  },
  meetDeadline: {
    id: `${scope}.meetDeadline`,
    defaultMessage: "Hôm nay đến hạn",
  },
  over: {
    id: `${scope}.over`,
    defaultMessage: "Chậm {over} ngày",
  },
  startTimeValidate: {
    id: `${scope}.startTimeValidate`,
    defaultMessage: "Thời gian bắt đầu phải lớn hơn thời gian hiện tại",
  },
});
