/**
 *
 * Dashboard
 *
 */

import { Trend } from "ant-design-pro";
import { ChartCard } from "ant-design-pro/lib/Charts";
import { Col, Row } from "antd";
import moment from "moment";
import React from "react";
import { FormattedMessage } from "react-intl";
import { formatPrice } from "../../utils";
import config from "../../utils/config";
import { GLOBAL_COLOR } from "../../utils/constants";
import { MultiColorProgressBar } from "./MultiColorProgressBar";
import messages from "./messages";
const salesData = [];
for (let i = 0; i < 12; i += 1) {
  salesData.push({
    x: `${i + 1}`,
    y: 100 + Math.random() * 100,
  });
}

export default class extends React.PureComponent {
  render() {
    const {
      loading,
      announcement,
      dispatch,
      auth_group,
      screenwidth,
      intl,
      ...rest
    } = this.props;
    const data = announcement
      ? Object.keys(announcement.recent_days).map((key, index) => {
          return {
            x: `${moment.unix(key).format("DD/MM/YYYY")}`,
            y: announcement.recent_days[key],
          };
        })
      : [];

    let normal = announcement ? announcement.total_normal : 0;
    let total = announcement ? announcement.total : 0;
    if (total != 0) {
      normal = (normal * 100.0) / total;
      if (normal - parseInt(normal) == 0) {
        total = parseInt(normal);
      } else {
        normal = parseFloat(normal).toFixed(1);
      }
    }
    const normalText = intl.formatMessage({
      ...messages.normal,
    });
    const paidText = intl.formatMessage({
      ...messages.paid,
    });
    let announcement_percent = [
      {
        name: normalText,
        value: Number.isInteger(normal)
          ? normal
          : parseFloat(normal).toFixed(1),
        color: GLOBAL_COLOR,
      },
      {
        name: paidText,
        value: Number.isInteger(normal)
          ? 100 - normal
          : parseFloat(100 - normal).toFixed(1),
        color: "gray",
      },
    ];
    return (
      <Col {...rest}>
        <ChartCard
          bordered={false}
          bodyStyle={{ padding: 24, paddingLeft: 18, paddingRight: 18 }}
          style={{ cursor: "pointer" }}
          titleStyle={{ backgroundColor: "#ff0000" }}
          title={
            <div
              style={{
                display: "flex",
                width: "100%",
                justifyContent: "space-between",
                position: "absolute",
                top: 0,
              }}
            >
              <div>
                <span
                  style={{
                    fontWeight: 600,
                    fontSize: "1.125rem",
                    color: "rgba(0, 0, 0, 0.85)",
                  }}
                >
                  <FormattedMessage {...messages.notification} />
                </span>
                <span
                  style={{
                    fontWeight: 600,
                    fontSize: "1.4375rem",
                    color: "rgba(0, 0, 0, 0.85)",
                    marginLeft: 8,
                  }}
                >
                  {announcement ? formatPrice(announcement.total) : 0}
                </span>
              </div>
              <div>
                <span
                  style={{
                    fontWeight: 600,
                    fontSize: "1.125rem",
                    color: "rgba(0, 0, 0, 0.85)",
                  }}
                >
                  <FormattedMessage {...messages.today} />
                </span>
                <span
                  style={{
                    fontWeight: 600,
                    fontSize: "1.4375rem",
                    color: "rgba(0, 0, 0, 0.85)",
                    marginLeft: 8,
                  }}
                >
                  {announcement ? formatPrice(announcement.total_in_day) : 0}
                </span>
              </div>
            </div>
          }
          footer={
            <Row type={"flex"} align="middle" justify="space-between">
              <Trend>
                <span
                  onClick={(e) => {
                    if (
                      auth_group.checkRole([
                        config.ALL_ROLE_NAME.ANNOUNCE_CREATE_UPDATE,
                      ]) ||
                      auth_group.checkRole([config.ALL_ROLE_NAME.ANNOUNCE_LIST])
                    ) {
                      this.props.history.push("/main/notification/list");
                    }
                  }}
                  style={{
                    fontSize: "1.125rem",
                  }}
                >
                  {normalText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {`${
                    announcement
                      ? announcement.total_normal < 10000
                        ? formatPrice(announcement.total_normal)
                        : "9999+"
                      : 0
                  }`}
                </span>
              </Trend>
              <Trend>
                <span
                  onClick={(e) => {
                    if (
                      auth_group.checkRole([
                        config.ALL_ROLE_NAME.FINANCE_NOTIFICATION_FEE_LIST,
                      ]) ||
                      auth_group.checkRole([
                        config.ALL_ROLE_NAME.FINANCE_NOTIFICATION_FEE_MANAGER,
                      ])
                    ) {
                      this.props.history.push(
                        "/main/finance/notification-fee/list"
                      );
                    }
                  }}
                  style={{
                    fontSize: "1.125rem",
                  }}
                >
                  {paidText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {`${
                    announcement
                      ? announcement.total - announcement.total_normal < 10000
                        ? formatPrice(
                            announcement.total - announcement.total_normal
                          )
                        : "9999+"
                      : 0
                  }`}
                </span>
              </Trend>
            </Row>
          }
          contentHeight={150}
        >
          <div>
            <MultiColorProgressBar readings={announcement_percent} />
          </div>
        </ChartCard>
      </Col>
    );
  }
}
