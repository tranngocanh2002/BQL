/**
 *
 * ServiceCloud
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Card, Col, Row, Typography } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { fetchAllServiceCloud } from "./actions";
import messages from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectServiceCloud from "./selectors";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { getFullLinkImage } from "../../../connection";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";
import { GLOBAL_COLOR } from "../../../utils/constants";

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
                    <div style={{ padding: "0 24px" }} key={`item-${item.id}`}>
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
                          {<FormattedMessage {...messages.activeService} />}
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
                              <FormattedMessage {...messages.activedService} />
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
                        {this.props.language === "vi"
                          ? item.name
                          : item.name_en}
                      </span>
                    }
                    description={
                      <Row>
                        {this.props.language === "vi" ? (
                          <p
                            dangerouslySetInnerHTML={{
                              __html: item.description.replace(
                                /(&nbsp;|<([^>]+)>)/gi,
                                ""
                              ),
                            }}
                            style={{
                              minHeight: 85,
                              //overflow: "hidden",
                              whiteSpace: "pre-wrap",
                            }}
                          />
                        ) : (
                          <p
                            dangerouslySetInnerHTML={{
                              __html: item.description_en.replace(
                                /(&nbsp;|<([^>]+)>)/gi,
                                ""
                              ),
                            }}
                            style={{
                              minHeight: 85,
                              //overflow: "hidden",
                              whiteSpace: "pre-wrap",
                            }}
                          />
                        )}
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
  language: makeSelectLocale(),
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
