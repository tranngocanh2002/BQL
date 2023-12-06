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
import { config, formatPrice } from "../../utils";
import { GLOBAL_COLOR } from "../../utils/constants";
import { MultiColorProgressBar } from "./MultiColorProgressBar";
import messages from "./messages";
import("./card.less");
export default class extends React.PureComponent {
  render() {
    const { service_utility_form, intl, auth_group, ...rest } = this.props;

    let form_agree = service_utility_form
      ? service_utility_form.status_agree
      : 0;
    let form_other = service_utility_form
      ? service_utility_form.status_other
      : 0;
    let total_booking = service_utility_form
      ? service_utility_form.status_agree + service_utility_form.status_other
      : 0;

    if (total_booking != 0) {
      form_agree = (form_agree * 100.0) / total_booking;
      if (form_agree - parseInt(form_agree) == 0) {
        form_agree = parseInt(form_agree);
      } else {
        form_agree = parseFloat(form_agree).toFixed(1);
      }

      form_other = (form_other * 100.0) / total_booking;
      if (form_other - parseInt(form_other) == 0) {
        form_other = parseInt(form_other);
      } else {
        form_other = parseFloat(form_other).toFixed(1);
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
        value: Number.isInteger(form_agree)
          ? form_agree
          : parseFloat(form_agree).toFixed(1),
        color: GLOBAL_COLOR,
      },
      {
        name: otherText,
        value: Number.isInteger(form_agree)
          ? 100 - form_agree
          : parseFloat(100 - form_agree).toFixed(1),
        color: "gray",
      },
    ];
    return (
      <Col className="customCard" {...rest}>
        <ChartCard
          bordered={false}
          bodyStyle={{ padding: 24, paddingLeft: 18, paddingRight: 18 }}
          style={{ cursor: "pointer" }}
          onClick={() => {
            if (
              auth_group.checkRole([
                config.ALL_ROLE_NAME
                  .MANAGE_FORM_REGISTRATION_SERVICE_UTILITY_FORM_LIST,
              ])
            ) {
              this.props.history.push("/main/service-utility-form/list");
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
                  <FormattedMessage {...messages.formRegister} />
                </span>
                <span
                  style={{
                    fontSize: "1.4375rem",
                    marginLeft: 8,
                  }}
                >
                  {`${service_utility_form ? formatPrice(total_booking) : 0}`}
                </span>
              </div>
              <div style={{ marginLeft: 0 }}>
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
                    marginLeft: 10,
                  }}
                >
                  {service_utility_form
                    ? formatPrice(service_utility_form.total_in_day)
                    : 0}
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
                  {service_utility_form
                    ? service_utility_form.status_agree < 10000
                      ? formatPrice(service_utility_form.status_agree)
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
                  {service_utility_form
                    ? service_utility_form.status_other < 10000
                      ? formatPrice(service_utility_form.status_other)
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
