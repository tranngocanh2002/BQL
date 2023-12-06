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
      resident,
      dispatch,
      auth_group,
      screenwidth,
      intl,
      ...rest
    } = this.props;
    let total = resident ? resident.install_app + resident.not_install_app : 0;
    if (total != 0) {
      total = (resident.install_app * 100.0) / total;
      if (total - parseInt(total) == 0) {
        total = parseInt(total);
      } else {
        total = parseFloat(total).toFixed(1);
      }
    }
    const installAppText = intl.formatMessage({
      ...messages.installApp,
    });
    const noInstallText = intl.formatMessage({
      ...messages.noInstall,
    });
    let resident_percent = [
      {
        name: installAppText,
        value: Number.isInteger(total) ? total : parseFloat(total).toFixed(1),
        color: GLOBAL_COLOR,
      },
      {
        name: noInstallText,
        value: Number.isInteger(total)
          ? 100 - total
          : parseFloat(100 - total).toFixed(1),
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
                config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_LIST,
              ])
            ) {
              this.props.history.push("/main/resident/list");
            }
          }}
          title={
            <>
              <span>
                <FormattedMessage {...messages.totalResident} />
              </span>{" "}
              <span
                style={{
                  fontSize: "1.4375rem",
                  marginLeft: 8,
                }}
              >
                {`${
                  resident
                    ? formatPrice(
                        resident.install_app + resident.not_install_app
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
                  {installAppText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {resident
                    ? resident.install_app < 10000
                      ? formatPrice(resident.install_app)
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
                  {noInstallText}:
                </span>
                <span
                  style={{
                    marginLeft: 8,
                    color: "#262626",
                    fontSize: "1.125rem",
                  }}
                >
                  {resident
                    ? resident.not_install_app < 10000
                      ? formatPrice(resident.not_install_app)
                      : "9999+"
                    : 0}
                </span>
              </Trend>
            </Row>
          }
          contentHeight={150}
        >
          <div>
            <MultiColorProgressBar readings={resident_percent} />
          </div>
        </ChartCard>
      </Col>
    );
  }
}
