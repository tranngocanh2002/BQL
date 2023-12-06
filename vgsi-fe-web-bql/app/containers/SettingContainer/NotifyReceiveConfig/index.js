/**
 *
 * Notify Receive Config
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";
import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectNotifyReceiveConfig from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page/Page";
import { Col, Button, Row, Checkbox } from "antd";

import styles from "./index.less";
import {
  defaultAction,
  fetchNotifyReceiveConfig,
  updateNotifyReceiveConfig,
  updateAllNotifyReceiveConfig,
} from "./actions";
import Loader from "../../../components/Loader/Loader";
import _ from "lodash";
import { withRouter } from "react-router";
import { config } from "../../../utils";
export class NotifyReceiveConfig extends React.PureComponent {
  constructor(props) {
    super(props);
  }
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchNotifyReceiveConfig());
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.notifyReceiveConfig.loading !==
        nextProps.notifyReceiveConfig.loading ||
      this.props.notifyReceiveConfig.update_all !==
        nextProps.notifyReceiveConfig.update_all
    ) {
      this.props.dispatch(fetchNotifyReceiveConfig());
    }
  }

  render() {
    const { notifyReceiveConfig } = this.props;
    const { receives } = notifyReceiveConfig;

    return (
      <div className={styles.receivesPage} style={{ paddingBottom: 70 }}>
        <Page inner loading={receives.loading}>
          {receives.loading && (
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
                    updateAllNotifyReceiveConfig({
                      check_all: 1,
                    })
                  );
                }}
              >
                Nhận tất cả
              </Button>
              <Button
                danger
                type="danger"
                style={{ marginLeft: 20 }}
                onClick={(e) => {
                  this.props.dispatch(
                    updateAllNotifyReceiveConfig({
                      check_all: 0,
                    })
                  );
                }}
              >
                Bỏ nhận tất cả
              </Button>
            </Col>
          </Row>
          <Row className="rowItem" key="tr-table">
            <Col className={styles.separator} />
            <Col className={styles.tr_table}>
              <Row>
                <Col style={{ marginTop: 20, fontWeight: 600 }} lg={4} md={3} offset={1}>
                  Loại thông báo
                </Col>
                <Col className={styles.tr_item} lg={1} md={3} offset={1}>
                  Tạo
                </Col>
                <Col className={styles.tr_item} lg={2} md={3} offset={1}>
                  Cập nhật
                </Col>
                <Col className={styles.tr_item} lg={1} md={3} offset={1}>
                  Huỷ
                </Col>
                <Col className={styles.tr_item} lg={1} md={3} offset={1}>
                  Xoá
                </Col>
                <Col className={styles.tr_item} lg={2} md={3} offset={1}>
                  Duyệt
                </Col>
                <Col className={styles.tr_item} lg={2} md={3} offset={1}>
                  Bình luận
                </Col>
                <Col className={styles.tr_item} lg={2} md={3} offset={1}>
                  Đánh giá
                </Col>
              </Row>
            </Col>
          </Row>

          {!receives.loading &&
            config.NOTIFY_TYPE.map((type, idx) => {
              return (
                <Row key={`receive-${idx}`} className={styles.border_table}>
                  <Row
                    style={{ marginTop: 12, marginBottom: 8 }}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col
                      style={{ marginTop: 20 }}
                      key={`all-${type.type}-${idx}`}
                      lg={24}
                      md={24}
                      offset={1}
                    >
                      <span className={styles.title}>{type.type_name}</span>
                    </Col>
                  </Row>
                  {receives.data.map((receive, index) => {
                    if (type.type == receive.type) {
                      return (
                        <Col key={`receive-${index}`}>
                          <Row>
                            <Col
                              style={{ marginTop: 20 }}
                              lg={4}
                              md={3}
                              offset={1}
                            >
                              <Checkbox
                                checked={
                                  receive.action_create === 1 &&
                                  receive.action_update === 1 &&
                                  receive.action_cancel === 1 &&
                                  receive.action_approved === 1 &&
                                  receive.action_delete === 1 &&
                                  receive.action_comment === 1 &&
                                  receive.action_rate === 1
                                }
                                disabled={false}
                                onChange={(e) => {
                                  if (e.target.checked) {
                                    this.props.dispatch(
                                      updateNotifyReceiveConfig({
                                        id: receive.id,
                                        type: receive.type,
                                        channel: receive.channel,
                                        action_create: 1,
                                        action_update: 1,
                                        action_cancel: 1,
                                        action_approved: 1,
                                        action_delete: 1,
                                        action_comment: 1,
                                        action_rate: 1,
                                      })
                                    );
                                  } else {
                                    this.props.dispatch(
                                      updateNotifyReceiveConfig({
                                        id: receive.id,
                                        type: receive.type,
                                        channel: receive.channel,
                                        action_create: 0,
                                        action_update: 0,
                                        action_cancel: 0,
                                        action_approved: 0,
                                        action_delete: 0,
                                        action_comment: 0,
                                        action_rate: 0,
                                      })
                                    );
                                  }
                                }}
                              >
                                <span className={styles.channel}>
                                  {receive.channel_name}
                                </span>
                              </Checkbox>
                            </Col>
                            <Col
                              className={styles.tr_item}
                              lg={1}
                              md={3}
                              offset={1}
                            >
                              <Checkbox
                                checked={receive.action_create === 1}
                                disabled={false}
                                onChange={(e) => {
                                  this.props.dispatch(
                                    updateNotifyReceiveConfig({
                                      id: receive.id,
                                      type: receive.type,
                                      channel: receive.channel,
                                      action_create: e.target.checked ? 1 : 0,
                                      action_update: receive.action_update,
                                      action_cancel: receive.action_cancel,
                                      action_approved: receive.action_approved,
                                      action_delete: receive.action_delete,
                                      action_comment: receive.action_comment,
                                      action_rate: receive.action_rate,
                                    })
                                  );
                                }}
                              />
                            </Col>
                            <Col
                              className={styles.tr_item}
                              lg={2}
                              md={3}
                              offset={1}
                            >
                              <Checkbox
                                checked={receive.action_update === 1}
                                disabled={false}
                                onChange={(e) => {
                                  this.props.dispatch(
                                    updateNotifyReceiveConfig({
                                      id: receive.id,
                                      type: receive.type,
                                      channel: receive.channel,
                                      action_create: receive.action_create,
                                      action_update: e.target.checked ? 1 : 0,
                                      action_cancel: receive.action_cancel,
                                      action_approved: receive.action_approved,
                                      action_delete: receive.action_delete,
                                      action_comment: receive.action_comment,
                                      action_rate: receive.action_rate,
                                    })
                                  );
                                }}
                              />
                            </Col>
                            <Col
                              className={styles.tr_item}
                              lg={1}
                              md={3}
                              offset={1}
                            >
                              <Checkbox
                                checked={receive.action_cancel === 1}
                                disabled={false}
                                onChange={(e) => {
                                  this.props.dispatch(
                                    updateNotifyReceiveConfig({
                                      id: receive.id,
                                      type: receive.type,
                                      channel: receive.channel,
                                      action_create: receive.action_create,
                                      action_update: receive.action_update,
                                      action_cancel: e.target.checked ? 1 : 0,
                                      action_approved: receive.action_approved,
                                      action_delete: receive.action_delete,
                                      action_comment: receive.action_comment,
                                      action_rate: receive.action_rate,
                                    })
                                  );
                                }}
                              />
                            </Col>
                            <Col
                              className={styles.tr_item}
                              lg={1}
                              md={3}
                              offset={1}
                            >
                              <Checkbox
                                checked={receive.action_delete === 1}
                                disabled={false}
                                onChange={(e) => {
                                  this.props.dispatch(
                                    updateNotifyReceiveConfig({
                                      id: receive.id,
                                      type: receive.type,
                                      channel: receive.channel,
                                      action_create: receive.action_create,
                                      action_update: receive.action_update,
                                      action_cancel: receive.action_cancel,
                                      action_delete: e.target.checked ? 1 : 0,
                                      action_approved: receive.action_approved,
                                      action_comment: receive.action_comment,
                                      action_rate: receive.action_rate,
                                    })
                                  );
                                }}
                              />
                            </Col>
                            <Col
                              className={styles.tr_item}
                              lg={2}
                              md={3}
                              offset={1}
                            >
                              <Checkbox
                                checked={receive.action_approved === 1}
                                disabled={false}
                                onChange={(e) => {
                                  this.props.dispatch(
                                    updateNotifyReceiveConfig({
                                      id: receive.id,
                                      type: receive.type,
                                      channel: receive.channel,
                                      action_create: receive.action_create,
                                      action_update: receive.action_update,
                                      action_cancel: receive.action_cancel,
                                      action_delete: receive.action_delete,
                                      action_approved: e.target.checked ? 1 : 0,
                                      action_comment: receive.action_comment,
                                      action_rate: receive.action_rate,
                                    })
                                  );
                                }}
                              />
                            </Col>
                            <Col
                              className={styles.tr_item}
                              lg={2}
                              md={3}
                              offset={1}
                            >
                              <Checkbox
                                checked={receive.action_comment === 1}
                                disabled={false}
                                onChange={(e) => {
                                  this.props.dispatch(
                                    updateNotifyReceiveConfig({
                                      id: receive.id,
                                      type: receive.type,
                                      channel: receive.channel,
                                      action_create: receive.action_create,
                                      action_update: receive.action_update,
                                      action_cancel: receive.action_cancel,
                                      action_delete: receive.action_delete,
                                      action_approved: receive.taction_approved,
                                      action_comment: e.target.checked ? 1 : 0,
                                      action_rate: receive.action_rate,
                                    })
                                  );
                                }}
                              />
                            </Col>
                            <Col
                              className={styles.tr_item}
                              lg={2}
                              md={3}
                              offset={1}
                            >
                              <Checkbox
                                checked={receive.action_rate === 1}
                                disabled={false}
                                onChange={(e) => {
                                  this.props.dispatch(
                                    updateNotifyReceiveConfig({
                                      id: receive.id,
                                      type: receive.type,
                                      channel: receive.channel,
                                      action_create: receive.action_create,
                                      action_update: receive.action_update,
                                      action_cancel: receive.action_cancel,
                                      action_delete: receive.action_delete,
                                      action_approved: receive.taction_approved,
                                      action_comment: receive.action_comment,
                                      action_rate: e.target.checked ? 1 : 0,
                                    })
                                  );
                                }}
                              />
                            </Col>
                          </Row>
                        </Col>
                      );
                    }
                  })}
                  <Col className={styles.separator} />
                </Row>
              );
            })}
        </Page>
      </div>
    );
  }
}

NotifyReceiveConfig.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notifyReceiveConfig: makeSelectNotifyReceiveConfig(),
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

const withReducer = injectReducer({ key: "notifyReceiveConfig", reducer });
const withSaga = injectSaga({ key: "notifyReceiveConfig", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(NotifyReceiveConfig));
