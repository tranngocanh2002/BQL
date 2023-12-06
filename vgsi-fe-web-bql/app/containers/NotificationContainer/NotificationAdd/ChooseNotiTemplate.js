/**
 *
 * ChooseNotiTemplate
 *
 */

import React from "react";
import Page from "../../../components/Page/Page";
import { Row, Col, Card, Icon, Tooltip } from "antd";
import { getFullLinkImage } from "../../../connection";
import {
  fetchAnnouncementFeeTemplate,
  showChooseTemplateList,
} from "./actions";
import { FormattedMessage } from "react-intl";
import messages from "../messages";
const { Meta } = Card;

/* eslint-disable react/prefer-stateless-function */
export class ChooseNotiTemplate extends React.PureComponent {
  componentDidMount() {}

  render() {
    const { templateList, dispatch } = this.props;
    const { loading, data } = templateList;
    return (
      <Page inner={loading} loading={loading}>
        <Row gutter={24} style={{ padding: "20px" }}>
          {data &&
            data.map((item, index) => {
              return (
                <Col
                  key={`item-${index}`}
                  xxl={8}
                  xl={8}
                  lg={12}
                  md={12}
                  sm={12}
                  xs={24}
                  offset={2}
                  style={{ marginBottom: "32px" }}
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
                        key={"check"}
                        placement="top"
                        title={
                          <FormattedMessage {...messages.chooseTemplate} />
                        }
                      >
                        <Icon
                          type={"check"}
                          onClick={() => {
                            dispatch(showChooseTemplateList(false));
                            dispatch(
                              fetchAnnouncementFeeTemplate({ id: item.id })
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
                          values={{ total: index + 1 }}
                        />
                      }
                      style={{ textAlign: "center" }}
                    />
                  </Card>
                </Col>
              );
            })}
        </Row>
      </Page>
    );
  }
}
