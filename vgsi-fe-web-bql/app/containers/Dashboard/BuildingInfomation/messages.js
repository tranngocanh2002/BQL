/*
 * BuildingInfomation Messages
 *
 * This contains all the text for the BuildingInfomation container.
 */

import { defineMessages } from "react-intl";

export const scope = "app.containers.BuildingInfomation";

export default defineMessages({
  header: {
    id: `${scope}.header`,
    defaultMessage: "This is the BuildingInfomation container!",
  },
  noImage: {
    id: `${scope}.noImage`,
    defaultMessage: "Chưa có ảnh đại diện toà nhà",
  },
  cskh: {
    id: `${scope}.cskh`,
    defaultMessage: "CSKH",
  },
  setting: {
    id: `${scope}.setting`,
    defaultMessage: "Cấu hình",
  },
  generalInformation: {
    id: `${scope}.generalInformation`,
    defaultMessage: "Cấu hình",
  },
  managementName: {
    id: `${scope}.managementName`,
    defaultMessage: "Tên dự án:",
  },
  domain: {
    id: `${scope}.domain`,
    defaultMessage: "Tên miền:",
  },
  city: {
    id: `${scope}.city`,
    defaultMessage: "Thành phố:",
  },
  address: {
    id: `${scope}.address`,
    defaultMessage: "Địa chỉ:",
  },
  introduce: {
    id: `${scope}.introduce`,
    defaultMessage: "Giới thiệu",
  },
  email: {
    id: `${scope}.email`,
    defaultMessage: "Email:",
  },
  hotline: {
    id: `${scope}.hotline`,
    defaultMessage: "Hotline:",
  },
  quota: {
    id: `${scope}.quota`,
    defaultMessage: "Định mức",
  },
  from: {
    id: `${scope}.from`,
    defaultMessage: "Từ",
  },
  above: {
    id: `${scope}.above`,
    defaultMessage: "Trở lên",
  },
  vnd: {
    id: `${scope}.vnd`,
    defaultMessage: "đ",
  },
  noSetting: {
    id: `${scope}.noSetting`,
    defaultMessage: "Chưa cấu hình thông tin dịch vụ",
  },
  serviceConfiguration: {
    id: `${scope}.serviceConfiguration`,
    defaultMessage: "Cấu hình dịch vụ",
  },
  month: {
    id: `${scope}.month`,
    defaultMessage: "Tháng",
  },
  monthly: {
    id: `${scope}.monthly`,
    defaultMessage: "Hàng tháng",
  },
  serviceFee: {
    id: `${scope}.serviceFee`,
    defaultMessage: "Phí dịch vụ:",
  },
  otherFee: {
    id: `${scope}.otherFee`,
    defaultMessage: "Phí khác:",
  },
  autoCreate: {
    id: `${scope}.autoCreate`,
    defaultMessage: "Tự động tạo phí:",
  },
  on: {
    id: `${scope}.on`,
    defaultMessage: "Bật",
  },
  off: {
    id: `${scope}.off`,
    defaultMessage: "Tắt",
  },
  time: {
    id: `${scope}.time`,
    defaultMessage: "Thời gian tạo phí:",
  },
  day: {
    id: `${scope}.day`,
    defaultMessage: "Ngày",
  },
  deadline: {
    id: `${scope}.deadline`,
    defaultMessage: "Hạn thanh toán:",
  },
});
