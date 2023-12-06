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
    const {
      loading,
      request,
      dispatch,
      auth_group,
      screenwidth,
      intl,
      ...rest
    } = this.props;
    let total = request
      ? request.status_processing + request.status_complete
      : 0;
    let processing_percent = request ? request.status_processing : 0;
    if (total != 0) {
      processing_percent = (processing_percent * 100.0) / total;
      if (processing_percent - parseInt(processing_percent) == 0) {
        processing_percent = parseInt(processing_percent);
      } else {
        processing_percent = parseFloat(processing_percent).toFixed(1);
      }
    }
    const processingText = intl.formatMessage({
      ...messages.processing,
    });
    const resolvedText = intl.formatMessage({
      ...messages.resolved,
    });
    let request_percent = [
      {
        name: processingText,
        value: Number.isInteger(processing_percent)
          ? processing_percent
          : parseFloat(processing_percent).toFixed(1),
        color: GLOBAL_COLOR,
      },
      {
        name: resolvedText,
        value: Number.isInteger(processing_percent)
          ? 100 - processing_percent
          : parseFloat(100 - processing_percent).toFixed(1),
        color: "gray",
      },
    ];
    return (
      <Col className="customCard" {...rest}>
        <ChartCard
          bodyStyle={{ padding: 24, paddingLeft: 18, paddingRight: 18 }}
          bordered={false}
          style={{ cursor: "pointer", position: "relative" }}
          onClick={(e) => {
            if (auth_group.checkRole([config.ALL_ROLE_NAME.REQUEST_LIST])) {
              this.props.history.push("/main/ticket/list");
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
                  <FormattedMessage {...messages.feedback} />
                </span>
                <span
                  style={{
                    fontSize: "1.4375rem",
                    marginLeft: 8,
                  }}
                >
                  {`${
                    request
                      ? formatPrice(
                          request.status_processing + request.status_complete
                        )
                      : 0
                  }`}
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
                  {request ? formatPrice(request.total_in_day) : 0}
                </span>
              </div>
            </div>
          }
          contentHeight={150}
          footer={
            <Row type={"flex"} align="middle" justify="space-between">
              <Trend>
                <span
                  style={{
                    fontSize: "1.125rem",
                  }}
                >
                  {processingText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {request
                    ? request.status_processing < 1000
                      ? formatPrice(request.status_processing)
                      : "999+"
                    : 0}
                </span>
              </Trend>
              <Trend>
                <span
                  style={{
                    fontSize: "1.125rem",
                  }}
                >
                  {resolvedText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {request
                    ? request.status_complete < 10000
                      ? formatPrice(request.status_complete)
                      : "9999+"
                    : 0}
                </span>
              </Trend>
            </Row>
          }
        >
          <div>
            <MultiColorProgressBar readings={request_percent} />
          </div>
        </ChartCard>
      </Col>
    );
  }
}
