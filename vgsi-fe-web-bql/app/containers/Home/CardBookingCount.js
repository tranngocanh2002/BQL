/**
 *
 * Dashboard
 *
 */

import { Trend } from "ant-design-pro";
import { ChartCard } from "ant-design-pro/lib/Charts";
import { Col, Row } from "antd";
import React from "react";
import { FormattedMessage } from "react-intl";
import { formatPrice } from "../../utils";
import config from "../../utils/config";
import { GLOBAL_COLOR } from "../../utils/constants";
import { MultiColorProgressBar } from "./MultiColorProgressBar";
import messages from "./messages";
import("./card.less");

export default class extends React.PureComponent {
  render() {
    const { booking, auth_group, intl, ...rest } = this.props;

    let booking_success = booking ? booking.total_success : 0;
    let booking_pending = booking ? booking.total_pending : 0;
    let total_booking = booking ? booking.total_all : 0;

    if (total_booking != 0) {
      booking_success = (booking_success * 100.0) / total_booking;
      if (booking_success - parseInt(booking_success) == 0) {
        booking_success = parseInt(booking_success);
      } else {
        booking_success = parseFloat(booking_success).toFixed(1);
      }

      booking_pending = (booking_pending * 100.0) / total_booking;
      if (booking_pending - parseInt(booking_pending) == 0) {
        booking_pending = parseInt(booking_pending);
      } else {
        booking_pending = parseFloat(booking_pending).toFixed(1);
      }
    }
    const successText = intl.formatMessage({
      ...messages.success,
    });
    const otherText = intl.formatMessage({
      ...messages.other,
    });
    let booking_percent = [
      {
        name: successText,
        value: Number.isInteger(booking_success)
          ? booking_success
          : parseFloat(booking_success).toFixed(1),
        color: GLOBAL_COLOR,
      },
      {
        name: otherText,
        value: Number.isInteger(booking_success)
          ? 100 - booking_success
          : parseFloat(100 - booking_success).toFixed(1),
        color: "gray",
      },
    ];
    return (
      <Col className="customCard" {...rest}>
        <ChartCard
          bordered={false}
          bodyStyle={{ padding: 24, paddingLeft: 18, paddingRight: 18 }}
          style={{ cursor: "pointer" }}
          onClick={(e) => {
            if (
              auth_group.checkRole([
                config.ALL_ROLE_NAME.SET_WIDGET_SERVICE_UTILITY_BOOKING_LIST,
              ])
            ) {
              this.props.history.push("/main/bookinglist");
            }
          }}
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
                <span>
                  <FormattedMessage {...messages.reservations} />
                </span>
                <span
                  style={{
                    fontSize: "1.4375rem",
                    marginLeft: 8,
                  }}
                >
                  {`${booking ? formatPrice(booking.total_all) : 0}`}
                </span>
              </div>
              <div style={{ marginLeft: 0 }}>
                <span>
                  <FormattedMessage {...messages.today} />
                </span>
                <span
                  style={{
                    fontSize: "1.4375rem",
                    marginLeft: 10,
                  }}
                >
                  {booking ? formatPrice(booking.total_in_day) : 0}
                </span>
              </div>
            </div>
          }
          footer={
            <Row type={"flex"} align="middle" justify="space-between">
              <Trend>
                <span
                  style={{
                    fontSize: "1.125rem",
                  }}
                >
                  {successText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {booking
                    ? booking.total_success < 10000
                      ? formatPrice(booking.total_success)
                      : "9999+"
                    : 0}
                </span>
              </Trend>
              <Trend>
                <span
                  style={{
                    fontSize: "1.125rem",
                  }}
                >
                  {otherText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {booking
                    ? booking.total_all - booking.total_success < 10000
                      ? formatPrice(booking.total_all - booking.total_success)
                      : "9999+"
                    : 0}
                </span>
              </Trend>
            </Row>
          }
          contentHeight={150}
        >
          <div>
            <MultiColorProgressBar readings={booking_percent} />
          </div>
        </ChartCard>
      </Col>
    );
  }
}
