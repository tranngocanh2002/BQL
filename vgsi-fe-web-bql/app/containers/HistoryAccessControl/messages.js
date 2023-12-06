/*
 * historyAccessControl Messages
 *
 * This contains all the text for the historyAccessControl container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.historyAccessControl";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the historyAccessControl container!",
  },
  status: {
    id: `${scope}.status`,
    defaultMessage: "Trạng thái",
  },
  resident: {
    id: `${scope}.resident`,
    defaultMessage: "Cư dân",
  },
  guest: {
    id: `${scope}.guest`,
    defaultMessage: "Khách lạ",
  },
  name: {
    id: `${scope}.name`,
    defaultMessage: "Tên cư dân",
  },
  time: {
    id: `${scope}.time`,
    defaultMessage: "Thời điểm",
  },
  image: {
    id: `${scope}.image`,
    defaultMessage: "Ảnh",
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
});
