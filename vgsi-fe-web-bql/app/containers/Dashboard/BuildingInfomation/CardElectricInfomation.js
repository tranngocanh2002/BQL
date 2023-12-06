/**
 *
 * CardElectricInfomation
 *
 */

import React from "react";

import { Row, Col, Card, Button } from "antd";
import { Link } from "react-router-dom";
import { getFullLinkImage } from "../../../connection";
import { formatPrice } from "../../../utils";
import config from "../../../utils/config";
import messages from "./messages";

const { Meta } = Card;

/* eslint-disable react/prefer-stateless-function */
export class CardElectricInfomation extends React.PureComponent {
  render() {
    const { electric, auth_group, formatMessage, language, ...rest } =
      this.props;
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
                !!electric.medias && !!electric.medias.logo
                  ? electric.medias.logo
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
                  ? electric.service_name_en
                  : electric.service_name}{" "}
                (kWh)
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
                  {electric.service_level.length
                    ? electric.service_level.map((item, index) => {
                        return (
                          <Row
                            key={index}
                            className="rowItem"
                            type="flex"
                            justify="space-between"
                          >
                            <Col>{`${formatMessage(messages.quota)} ${
                              index + 1
                            }: ${formatMessage(messages.from)} ${
                              item.from_level
                            } - ${
                              electric.service_level.length === index + 1
                                ? formatMessage(messages.above)
                                : item.to_level
                            }`}</Col>
                            <Col style={{ fontWeight: "bold" }}>
                              <>
                                <span style={{ fontWeight: "bold" }}>
                                  {formatPrice(item.price)} (
                                  {formatMessage(messages.vnd)}/kWh)
                                </span>
                              </>
                            </Col>
                          </Row>
                        );
                      })
                    : formatMessage(messages.noSetting)}
                </Col>
              </Row>
            }
          />
          {electric.service_level.length == 0 &&
          auth_group.checkRole([
            config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
          ]) ? (
            <Button
              style={{ position: "absolute", bottom: "5%", marginLeft: "21%" }}
            >
              <Link to="/main/setting/service/detail/electric/setup-fee">
                {formatMessage(messages.serviceConfiguration)}
              </Link>
            </Button>
          ) : null}
        </Card>
      </Col>
    );
  }
}
