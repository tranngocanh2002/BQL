/**
 *
 * ChooseNotiTemplate
 *
 */

import React from "react";
import { Row, Col, Card, Icon, Tooltip, Modal } from "antd";
import { FormattedMessage } from "react-intl";
import { getFullLinkImage } from "../../../connection";
import messages from "../messages";
import {
  fetchAnnouncementFeeTemplate,
  showChooseTemplateList,
} from "./actions";
const { Meta } = Card;

/* eslint-disable react/prefer-stateless-function */
export class ChooseNotiTemplate extends React.PureComponent {
  componentDidMount() {}

  render() {
    const { templateList, dispatch, showChooseTemplate } = this.props;
    const { loading, data } = templateList;
    return (
      <Modal
        visible={showChooseTemplate}
        width={window.innerWidth - 280}
        title={<FormattedMessage {...messages.chooseNotificationTemplate} />}
        style={{ left: 118, top: 60 }}
        okText={<FormattedMessage {...messages.done} />}
        cancelText={<FormattedMessage {...messages.cancel} />}
        onOk={() => {
          dispatch(showChooseTemplateList(false));
        }}
        onCancel={() => {
          dispatch(showChooseTemplateList(false));
        }}
      >
        <Row gutter={24} style={{ padding: "20px" }}>
          {data &&
            data.map((item, index) => {
              return (
                <Col
                  key={`item-${index}`}
                  xxl={6}
                  xl={8}
                  lg={8}
                  md={8}
                  sm={8}
                  xs={12}
                  style={{
                    marginBottom: "48px",
                    padding: 0,
                    display: "flex",
                    alignContent: "center",
                    justifyContent: "center",
                  }}
                >
                  <Card
                    hoverable
                    style={{
                      borderRadius: 4,
                      overflow: "hidden",
                      width: "270px",
                    }}
                    bordered={true}
                    cover={
                      <img
                        alt="example"
                        style={{ height: 210, objectFit: "cover" }}
                        src={
                          item.image
                            ? getFullLinkImage(item.image)
                            : require("../../../images/imageDefault.jpg")
                        }
                      />
                    }
                    actions={[
                      item.building_cluster_id ? (
                        <Tooltip
                          placement="top"
                          title={
                            <FormattedMessage {...messages.updateTemplate} />
                          }
                        >
                          <Icon
                            type={"edit"}
                            onClick={() => {
                              this.props.history.push(
                                `/main/setting/notification-fee-manager?id=${item.id}`,
                                { type: item.type }
                              );
                            }}
                          />
                        </Tooltip>
                      ) : (
                        <Tooltip
                          placement="top"
                          title={
                            <FormattedMessage
                              {...messages.notificationTemplateDefault}
                            />
                          }
                        >
                          <span style={{ color: "red" }}>
                            <FormattedMessage {...messages.default} />
                          </span>
                        </Tooltip>
                      ),
                      <Tooltip
                        key={"arr1"}
                        placement="top"
                        title={
                          <FormattedMessage {...messages.chooseTemplate} />
                        }
                      >
                        <Icon
                          type={"check"}
                          onClick={(e) => {
                            dispatch(showChooseTemplateList(false));
                            dispatch(
                              fetchAnnouncementFeeTemplate({
                                id: item.id,
                              })
                            );
                          }}
                        />
                      </Tooltip>,
                    ]}
                  >
                    <Meta
                      title={
                        item.name ? (
                          item.name
                        ) : (
                          <FormattedMessage {...messages.noNameTemplate} />
                        )
                      }
                      description={
                        <FormattedMessage
                          {...messages.template}
                          values={{
                            total: index + 1,
                          }}
                        />
                      }
                      style={{ textAlign: "center" }}
                    />
                  </Card>
                </Col>
              );
            })}
        </Row>
      </Modal>
    );
  }
}
