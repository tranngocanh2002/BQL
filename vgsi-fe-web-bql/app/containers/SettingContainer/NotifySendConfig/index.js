/**
 *
 * Notify Send Config
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";
import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectNotifySendConfig from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page/Page";
import { Col, Button, Row, Checkbox } from "antd";

import styles from "./index.less";
import {
  defaultAction,
  fetchNotifySendConfig,
  updateNotifySendConfig,
  updateAllNotifySendConfig,
} from "./actions";
import Loader from "../../../components/Loader/Loader";
import _ from "lodash";
import { withRouter } from "react-router";
import { config } from "../../../utils";
export class NotifySendConfig extends React.PureComponent {
  constructor(props) {
    super(props);
  }
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchNotifySendConfig());
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.notifySendConfig.loading !==
        nextProps.notifySendConfig.loading ||
      this.props.notifySendConfig.update_all !==
        nextProps.notifySendConfig.update_all
    ) {
      this.props.dispatch(fetchNotifySendConfig());
    }
  }

  render() {
    const { notifySendConfig } = this.props;
    const { sends } = notifySendConfig;

    return (
      <div className={styles.sendsPage} style={{ paddingBottom: 70 }}>
        <Page inner loading={sends.loading}>
          {sends.loading && (
            <Row>
              <Loader inner hideText />
            </Row>
          )}
          <Row className="rowItem" key="all">
            <Col offset={0} style={{ paddingLeft: 0 }}>
              <Button
                danger
                type="primary"
                style={{ marginLeft: 0 }}
                onClick={(e) => {
                  this.props.dispatch(
                    updateAllNotifySendConfig({
                      check_all: 1,
                    })
                  );
                }}
              >
                Gửi tất cả
              </Button>
              <Button
                danger
                type="danger"
                style={{ marginLeft: 20 }}
                onClick={(e) => {
                  this.props.dispatch(
                    updateAllNotifySendConfig({
                      check_all: 0,
                    })
                  );
                }}
              >
                Bỏ gửi tất cả
              </Button>
            </Col>
          </Row>
          <Row className="rowItem" key="tr-table">
            <Col className={styles.separator} />
            <Col className={styles.tr_table}>
              <Row>
                <Col style={{ marginTop: 20, fontWeight: 600 }} lg={6} md={8} offset={1}>
                  Loại thông báo
                </Col>
                <Col className={styles.tr_item} lg={4} md={6} offset={1}>
                  Email
                </Col>
                <Col className={styles.tr_item} lg={4} md={6} offset={1}>
                  SMS
                </Col>
                <Col className={styles.tr_item} lg={4} md={6} offset={1}>
                  APP
                </Col>
              </Row>
            </Col>
          </Row>

          {!sends.loading && (
            <Row className={styles.border_table} key="th-table">
              {sends.data.map((send, index) => {
                return (
                  <Col key={`send-${index}`}>
                    <Row>
                      <Col style={{ marginTop: 48 }} lg={6} md={8} offset={1}>
                        <Checkbox
                          checked={
                            send.send_email +
                              send.send_sms +
                              send.send_notify_app ===
                            3
                          }
                          disabled={false}
                          onChange={(e) => {
                            if (e.target.checked) {
                              this.props.dispatch(
                                updateNotifySendConfig({
                                  id: send.id,
                                  type: send.type,
                                  send_email: 1,
                                  send_sms: 1,
                                  send_notify_app: 1,
                                })
                              );
                            } else {
                              this.props.dispatch(
                                updateNotifySendConfig({
                                  id: send.id,
                                  type: send.type,
                                  send_email: 0,
                                  send_sms: 0,
                                  send_notify_app: 0,
                                })
                              );
                            }
                          }}
                        >
                          <span className={styles.channel}>
                            {send.type_name}
                          </span>
                        </Checkbox>
                      </Col>
                      <Col className={styles.th_item} lg={4} md={6} offset={1}>
                        <Checkbox
                          checked={send.send_email === 1}
                          disabled={false}
                          onChange={(e) => {
                            this.props.dispatch(
                              updateNotifySendConfig({
                                id: send.id,
                                type: send.type,
                                send_email: e.target.checked ? 1 : 0,
                                send_sms: send.send_sms,
                                send_notify_app: send.send_notify_app,
                              })
                            );
                          }}
                        />
                      </Col>
                      <Col className={styles.th_item} lg={4} md={6} offset={1}>
                        <Checkbox
                          checked={send.send_sms === 1}
                          disabled={false}
                          onChange={(e) => {
                            this.props.dispatch(
                              updateNotifySendConfig({
                                id: send.id,
                                type: send.type,
                                send_email: send.send_email,
                                send_sms: e.target.checked ? 1 : 0,
                                send_notify_app: send.send_notify_app,
                              })
                            );
                          }}
                        />
                      </Col>
                      <Col className={styles.th_item} lg={4} md={6} offset={1}>
                        <Checkbox
                          checked={send.send_notify_app === 1}
                          disabled={false}
                          onChange={(e) => {
                            this.props.dispatch(
                              updateNotifySendConfig({
                                id: send.id,
                                type: send.type,
                                send_email: send.send_email,
                                send_sms: send.send_sms,
                                send_notify_app: e.target.checked ? 1 : 0,
                              })
                            );
                          }}
                        />
                      </Col>
                    </Row>
                  </Col>
                );
              })}
              <Col className={styles.separator} />
            </Row>
          )}
        </Page>
      </div>
    );
  }
}

NotifySendConfig.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notifySendConfig: makeSelectNotifySendConfig(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(
  mapStateToProps,
  mapDispatchToProps
);

const withReducer = injectReducer({ key: "notifySendConfig", reducer });
const withSaga = injectSaga({ key: "notifySendConfig", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(NotifySendConfig));
