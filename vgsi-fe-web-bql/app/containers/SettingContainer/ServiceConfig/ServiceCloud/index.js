/**
 *
 * ServiceCloud
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage, injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectServiceCloud from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "../messages";
import Page from "../../../../components/Page/Page";
import { Row, Col, Card, Button, Typography, Icon } from "antd";
import { fetchAllServiceCloud } from "./actions";

import { getFullLinkImage } from "../../../../connection";
import { selectAuthGroup } from "../../../../redux/selectors";
import { config } from "../../../../utils";
import { GLOBAL_COLOR } from "../../../../utils/constants";

const { Meta } = Card;
const { Paragraph } = Typography;

/* eslint-disable react/prefer-stateless-function */
export class ServiceCloud extends React.PureComponent {
  componentDidMount() {
    this.props.dispatch(fetchAllServiceCloud());
  }

  render() {
    const { serviceCloud, auth_group } = this.props;
    const { loading, items } = serviceCloud;
    const canAdd = auth_group.checkRole([
      config.ALL_ROLE_NAME.SERVICE_CLOUD_ADD,
    ]);
    const formatMessage = this.props.intl.formatMessage;
    console.log("====================================");
    console.log(serviceCloud);
    console.log("====================================");
    return (
      <Page inner={loading} loading={loading}>
        <Row gutter={24}>
          {items.map((item, index) => {
            const { is_map_management, logo } = item;
            return (
              <Col
                key={`item-${item.id}`}
                xxl={6}
                xl={8}
                lg={12}
                md={12}
                sm={12}
                xs={24}
                style={{ paddingRight: 4, marginBottom: 16, height: 450 }}
              >
                <Card
                  style={{
                    borderRadius: 4,
                    overflow: "hidden",
                  }}
                  bordered={false}
                  cover={
                    <img
                      alt="example"
                      style={{ height: 210, objectFit: "cover" }}
                      src={getFullLinkImage(logo)}
                    />
                  }
                  actions={[
                    <div style={{ padding: "0 24px" }}>
                      {canAdd && !is_map_management && (
                        <Button
                          type="primary"
                          ghost={true}
                          block
                          onClick={() => {
                            this.props.history.push(
                              `/main/service/add/${item.id}`
                            );
                          }}
                        >
                          {formatMessage(messages.activateService)}
                        </Button>
                      )}
                      {!!is_map_management && (
                        <Row
                          style={{
                            color: GLOBAL_COLOR,
                          }}
                        >
                          <div
                            style={{
                              display: "flex",
                            }}
                          >
                            <i
                              className="material-icons"
                              style={{
                                marginRight: 6,
                                color: GLOBAL_COLOR,
                                width: 24,
                              }}
                            >
                              check
                            </i>
                            <span>
                              {formatMessage(messages.activatedService)}
                            </span>
                          </div>
                        </Row>
                      )}
                    </div>,
                  ]}
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
                        {item.name}
                      </span>
                    }
                    description={
                      <Row>
                        <p
                          dangerouslySetInnerHTML={{
                            __html:
                              item.description.replace(
                                /(&nbsp;|<([^>]+)>)/gi,
                                ""
                              ).length > 149
                                ? item.description
                                    .replace(/(&nbsp;|<([^>]+)>)/gi, "")
                                    .substring(0, 150) + "..."
                                : item.description.replace(
                                    /(&nbsp;|<([^>]+)>)/gi,
                                    ""
                                  ),
                          }}
                          style={{
                            height: 85,
                            overflow: "hidden",
                            whiteSpace: "pre-wrap",
                          }}
                        ></p>
                      </Row>
                    }
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

ServiceCloud.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  serviceCloud: makeSelectServiceCloud(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "serviceCloud", reducer });
const withSaga = injectSaga({ key: "serviceCloud", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ServiceCloud));
