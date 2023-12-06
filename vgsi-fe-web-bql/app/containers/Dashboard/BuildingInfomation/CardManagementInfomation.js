/**
 *
 * CardManagementInfomation
 *
 */

import React from "react";

import { Row, Col, Card, Typography } from "antd";

import { getFullLinkImage } from "../../../connection";
import { formatPrice, config } from "../../../utils";
import messages from "./messages";

const { Meta } = Card;
/* eslint-disable react/prefer-stateless-function */
export class CardManagementInfomation extends React.PureComponent {
  render() {
    const { management, formatMessage, language, ...rest } = this.props;
    return (
      <Col
        style={{ paddingRight: 4, marginBottom: 16, minHeight: 454 }}
        {...rest}
      >
        <Card
          hoverable
          bodyStyle={{ padding: 16 }}
          style={{
            minHeight: 454,
            borderRadius: 4,
            overflow: "hidden",
          }}
          bordered={false}
          cover={
            <img
              alt="example"
              style={{ height: 210, objectFit: "cover" }}
              src={getFullLinkImage(
                !!management.medias && !!management.medias.logo
                  ? management.medias.logo
                  : undefined
              )}
            />
          }
          bodyStyle={{ overflow: "auto", maxHeight: "230px" }}
        >
          <Meta
            title={
              <span
                style={{
                  color: "#1B1B27",
                  fontWeight: "bold",
                  fontSize: 18,
                }}
              >
                {language === "en"
                  ? management.service_name_en
                  : management.service_name}{" "}
                (
                {(
                  config.MONTH_CYCLE_SERVICE_CONFIG.find(
                    (ii) =>
                      ii.value ==
                      (!!management.config ? management.config.month_cycle : 0)
                  ) || config.MONTH_CYCLE_SERVICE_CONFIG[0]
                ).title === "Hàng tháng"
                  ? formatMessage(messages.month)
                  : (
                      config.MONTH_CYCLE_SERVICE_CONFIG.find(
                        (ii) =>
                          ii.value ==
                          (!!management.config
                            ? management.config.month_cycle
                            : 0)
                      ) || config.MONTH_CYCLE_SERVICE_CONFIG[0]
                    ).title}
                )
              </span>
            }
            description={
              <Row>
                <Col
                  md={24}
                  style={{
                    alignContent: "center",
                    alignItems: "center",
                    justifyItems: "center",
                    justifyContent: "center",
                  }}
                >
                  <Row>
                    <Col
                      xl={24}
                      style={{
                        alignContent: "center",
                        alignItems: "center",
                        justifyItems: "center",
                        justifyContent: "center",
                      }}
                    >
                      <Row
                        className="rowItem"
                        type="flex"
                        justify="space-between"
                      >
                        <Col>{formatMessage(messages.serviceFee)}</Col>
                        <Col style={{ fontWeight: "bold" }}>
                          <>
                            <span style={{ fontWeight: "bold" }}>
                              {formatPrice(
                                !!management.config
                                  ? management.config.price
                                  : 0
                              )}{" "}
                              đ
                            </span>
                            {`/${
                              (
                                config.UNIT_SERVICE_CONFIG.find(
                                  (ii) =>
                                    ii.value ==
                                    (!!management.config
                                      ? management.config.unit
                                      : 0)
                                ) || config.UNIT_SERVICE_CONFIG[0]
                              ).title
                            }`}
                          </>
                        </Col>
                      </Row>
                      <Row
                        className="rowItem"
                        type="flex"
                        justify="space-between"
                      >
                        <Col>{formatMessage(messages.otherFee)}</Col>
                        <Col style={{ fontWeight: "bold" }}>
                          {!!management.config ? management.config.percent : 0}{" "}
                          <span> %</span>
                        </Col>
                      </Row>
                      <Row
                        className="rowItem"
                        type="flex"
                        justify="space-between"
                      >
                        <Col>{formatMessage(messages.autoCreate)}</Col>
                        <Col style={{ fontWeight: "bold" }}>
                          <>
                            <span style={{ fontWeight: "bold" }}>
                              {!!management.config &&
                              management.config.auto_create_fee == 1
                                ? formatMessage(messages.on)
                                : formatMessage(messages.off)}
                            </span>
                          </>
                        </Col>
                      </Row>
                      <Row
                        className="rowItem"
                        type="flex"
                        justify="space-between"
                      >
                        <Col>{formatMessage(messages.time)}</Col>
                        <Col style={{ fontWeight: "bold" }}>
                          <>
                            {formatMessage(messages.day)}
                            <span style={{ fontWeight: "bold" }}>
                              :{!!management.config ? management.config.day : 0}
                            </span>
                            {" - "}
                            <span style={{ fontWeight: "bold" }}>
                              {this.props.language === "vi"
                                ? (
                                    config.MONTH_CYCLE_SERVICE_CONFIG.find(
                                      (ii) =>
                                        ii.value ==
                                        (!!management.config
                                          ? management.config.month_cycle
                                          : 0)
                                    ) || config.MONTH_CYCLE_SERVICE_CONFIG[0]
                                  ).title
                                : (
                                    config.MONTH_CYCLE_SERVICE_CONFIG.find(
                                      (ii) =>
                                        ii.value ==
                                        (!!management.config
                                          ? management.config.month_cycle
                                          : 0)
                                    ) || config.MONTH_CYCLE_SERVICE_CONFIG[0]
                                  ).title_en}
                            </span>
                          </>
                        </Col>
                      </Row>
                      <Row
                        className="rowItem"
                        type="flex"
                        justify="space-between"
                      >
                        <Col>{formatMessage(messages.deadline)}</Col>
                        <Col
                          style={{ whiteSpace: "pre-wrap", fontWeight: "bold" }}
                        >
                          {!!management.config
                            ? management.config.offset_day
                            : 1}
                          <span> {formatMessage(messages.day)}</span>
                        </Col>
                      </Row>
                    </Col>
                  </Row>
                </Col>
              </Row>
            }
          />
        </Card>
      </Col>
    );
  }
}
