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
      apartment,
      dispatch,
      auth_group,
      screenwidth,
      intl,
      ...rest
    } = this.props;
    let total = apartment ? apartment.status_live + apartment.status_empty : 0;
    if (total != 0) {
      total = (apartment.status_live * 100.0) / total;
      if (total - parseInt(total) == 0) {
        total = parseInt(total);
      } else {
        total = parseFloat(total).toFixed(1);
      }
    }
    const inhabitedText = intl.formatMessage({
      ...messages.inhabited,
    });
    const emptyText = intl.formatMessage({
      ...messages.empty,
    });
    let apartment_percent = [
      {
        name: inhabitedText,
        value: Number.isInteger(total) ? total : parseFloat(total).toFixed(1),
        color: GLOBAL_COLOR,
      },
      {
        name: emptyText,
        value: Number.isInteger(total)
          ? 100 - total
          : parseFloat(100 - total).toFixed(1),
        color: "gray",
      },
    ];
    return (
      <Col className="customCard" {...rest}>
        <ChartCard
          style={{ cursor: "pointer" }}
          onClick={(e) => {
            if (
              auth_group.checkRole([
                config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_LIST,
              ])
            ) {
              this.props.history.push("/main/apartment/list");
            }
          }}
          bodyStyle={{ padding: 24, paddingLeft: 18, paddingRight: 18 }}
          bordered={false}
          title={
            <>
              <span>
                <FormattedMessage {...messages.totalApart} />
              </span>{" "}
              <span
                style={{
                  fontSize: "1.4375rem",
                  marginLeft: 8,
                }}
              >
                {`${
                  apartment
                    ? formatPrice(
                        apartment.status_live + apartment.status_empty
                      )
                    : 0
                }`}
              </span>
            </>
          }
          footer={
            <Row type={"flex"} align="middle" justify="space-between">
              <Trend>
                <span
                  style={{
                    fontSize: "1.125rem",
                  }}
                >
                  {inhabitedText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {apartment ? formatPrice(apartment.status_live) : 0}
                </span>
              </Trend>
              <Trend>
                <span
                  style={{
                    fontSize: "1.125rem",
                  }}
                >
                  {emptyText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {apartment ? formatPrice(apartment.status_empty) : 0}
                </span>
              </Trend>
            </Row>
          }
          contentHeight={150}
        >
          <div>
            <MultiColorProgressBar
              readings={apartment_percent}
              screenwidth={screenwidth}
            />
          </div>
        </ChartCard>
      </Col>
    );
  }
}
