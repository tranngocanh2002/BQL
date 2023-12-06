/**
 *
 * ChooseNotiTemplate
 *
 */

import React from "react";
import Page from "../../../components/Page/Page";
import { Row, Col, Card, Tooltip, Icon } from "antd";
import { getFullLinkImage } from "../../../connection";
import {
  fetchAnnouncementFeeTemplate,
  showChooseTemplateList,
  chooseCreateTemplate,
} from "./actions";
import { FormattedMessage } from "react-intl";
import messages from "../messages";
const { Meta } = Card;

/* eslint-disable react/prefer-stateless-function */
export class ChooseNotiTemplate extends React.PureComponent {
  render() {
    const { templateList, dispatch, handleDeleteTemplate, language } =
      this.props;
    const { loading, data } = templateList;
    return (
      <Page inner={loading} loading={loading}>
        <Row gutter={24}>
          {data &&
            data.reverse().map((item, index) => {
              const i = index + 1;
              return (
                <Col
                  key={`item-${index}`}
                  xxl={window.innerWidth > 1700 ? 4 : 5}
                  xl={6}
                  lg={10}
                  md={8}
                  sm={12}
                  xs={24}
                  style={{ marginRight: 48, marginBottom: 48 }}
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
                    actions={
                      item.building_cluster_id
                        ? [
                            <Tooltip
                              key={`item-${index}-edit`}
                              placement="top"
                              title={
                                <FormattedMessage
                                  {...messages.updateTemplate}
                                />
                              }
                            >
                              <Icon
                                type={"edit"}
                                onClick={() => {
                                  if (item.building_cluster_id) {
                                    dispatch(showChooseTemplateList(false));
                                    dispatch(
                                      fetchAnnouncementFeeTemplate({
                                        id: item.id,
                                      })
                                    );
                                  }
                                }}
                              />
                            </Tooltip>,
                            <Tooltip
                              key={`item-${index}-delete`}
                              placement="top"
                              title={
                                <FormattedMessage
                                  {...messages.deleteTemplate}
                                />
                              }
                            >
                              <Icon
                                type={"delete"}
                                onClick={() => {
                                  if (item.building_cluster_id) {
                                    handleDeleteTemplate(item.id);
                                  }
                                }}
                              />
                            </Tooltip>,
                          ]
                        : [
                            <Tooltip
                              key={`item-${index}-deny`}
                              placement="top"
                              title={
                                <FormattedMessage
                                  {...messages.denyEditTemplate}
                                />
                              }
                            >
                              <span style={{ color: "red" }}>
                                <FormattedMessage {...messages.default} />
                              </span>
                            </Tooltip>,
                          ]
                    }
                  >
                    <Tooltip
                      placement="top"
                      title={
                        item.name || item.name_en ? (
                          language == "en" ? (
                            item.name_en
                          ) : (
                            item.name
                          )
                        ) : (
                          <FormattedMessage {...messages.notSetNameTemplate} />
                        )
                      }
                    >
                      <Meta
                        title={
                          item.name || item.name_en ? (
                            language == "en" ? (
                              item.name_en
                            ) : (
                              item.name
                            )
                          ) : (
                            <FormattedMessage
                              {...messages.notSetNameTemplate}
                            />
                          )
                        }
                        description={
                          <FormattedMessage
                            {...messages.template}
                            values={{ i }}
                          />
                        }
                        style={{ textAlign: "center" }}
                      />
                    </Tooltip>
                  </Card>
                </Col>
              );
            })}
          <Col
            key={"item-add1"}
            xxl={window.innerWidth > 1700 ? 4 : 5}
            xl={6}
            lg={10}
            md={8}
            sm={12}
            xs={24}
            style={{ marginRight: 48, marginBottom: 48 }}
          >
            <Card
              hoverable
              style={{
                borderRadius: 4,
                overflow: "hidden",
                width: "270px",
              }}
              onClick={() => {
                dispatch(showChooseTemplateList(false));
                dispatch(chooseCreateTemplate(true));
              }}
              bordered={true}
              cover={
                <Tooltip
                  placement="right"
                  title={
                    <FormattedMessage
                      {...messages.addNewTemplateNotification}
                    />
                  }
                >
                  <img
                    alt="example"
                    style={{ height: 257, objectFit: "cover" }}
                    src={require("../../../images/imagePlus.png")}
                  />
                </Tooltip>
              }
              actions={[
                <Tooltip
                  key={"item-add2"}
                  placement="top"
                  title={
                    <FormattedMessage
                      {...messages.addNewTemplateNotification}
                    />
                  }
                >
                  <span>
                    <FormattedMessage {...messages.addTemplate} />
                  </span>
                </Tooltip>,
              ]}
            />
          </Col>
        </Row>
      </Page>
    );
  }
}
