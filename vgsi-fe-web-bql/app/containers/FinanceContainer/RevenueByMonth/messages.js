/*
 * RevenueByMonth Messages
 *
 * This contains all the text for the RevenueByMonth container.
 */
import { defineMessages } from "react-intl";

export const scope = "app.containers.RevenueByMonth";

export default defineMessages({
  day: {
    id: `${scope}.day`,
    defaultMessage: "Ngày",
  },
  revenue: {
    id: `${scope}.revenue`,
    defaultMessage: "Đã thu",
  },
  notCollected: {
    id: `${scope}.notCollected`,
    defaultMessage: "Chưa thu",
  },
  total: {
    id: `${scope}.total`,
    defaultMessage: "Tổng",
  },
  month: {
    id: `${scope}.month`,
    defaultMessage: "Tháng",
  },
  selectMonth: {
    id: `${scope}.selectMonth`,
    defaultMessage: "Chọn tháng",
  },
  totalCollected: {
    id: `${scope}.totalCollected`,
    defaultMessage: "Tổng đã thu",
  },
  totalNotCollected: {
    id: `${scope}.totalNotCollected`,
    defaultMessage: "Tổng chưa thu",
  },
  noData: {
    id: `${scope}.noData`,
    defaultMessage: "Không có dữ liệu",
  },
});
