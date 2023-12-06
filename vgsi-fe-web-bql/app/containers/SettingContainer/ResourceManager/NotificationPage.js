/*
 * Created by duydatpham@gmail.com on 22/10/2019
 * Copyright (c) 2019 duydatpham@gmail.com
 */
import React from "react";
import {
  Row,
  Col,
  Progress,
  Input,
  Button,
  DatePicker,
  Table,
  Modal,
} from "antd";
import moment from "moment";
import { connect } from "react-redux";
import { injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import makeSelectResourceManager from "./selectors";
import { fetchLogNotification } from "./actions";
import messages from "../messages";
import("./index.less");

class NotificationPage extends React.PureComponent {
  state = {
    text_search: "",
    visible: false,
    month: moment(),
  };

  componentDidMount() {
    this.props.dispatch(fetchLogNotification({}));
  }

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.dispatch(
          fetchLogNotification({
            page: pagination.current,
            text_search: this.state.text_search,
            ...(this.state.month
              ? {
                  start_time: moment(this.state.month).startOf("month").unix(),
                  end_time: moment(this.state.month).endOf("month").unix(),
                }
              : {}),
          })
        );
      }
    );
  };
  render() {
    const { notification } = this.props.resourceManager;
    const formatMessage = this.props.intl.formatMessage;
    const columns = [
      {
        width: 50,
        align: "center",
        title: <span className="nameTable">#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {(notification.pagination.currentPage - 1) * 20 + index + 1}
          </span>
        ),
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.timeSend)}</span>
        ),
        width: 180,
        dataIndex: "created_at",
        key: "created_at",
        render: (text) => moment.unix(text).format("DD/MM/YYYY - HH:mm"),
      },
      {
        title: <span className="nameTable">Device Token</span>,
        dataIndex: "device_token",
        key: "device_token",
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.status)}</span>
        ),
        dataIndex: "status_notify",
        width: 170,
        key: "status_notify",
        render: (text) => {
          if (text == 0) {
            return (
              <span className="luci-status-primary">
                {formatMessage(messages.sent)}
              </span>
            );
          }
          if (text == 1) {
            return (
              <span className="luci-status-success">
                {formatMessage(messages.sendSuccess)}
              </span>
            );
          }
          if (text == 2) {
            return (
              <span className="luci-status-warning">
                {formatMessage(messages.sendFail)}
              </span>
            );
          }
        },
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.action)}</span>
        ),
        width: 150,
        dataIndex: "action_name",
        key: "action_name",
        render: (text, record) => {
          return (
            <a
              href="#"
              onClick={() => {
                this.setState({
                  visible: true,
                  currentRecord: record,
                });
              }}
            >
              {formatMessage(messages.viewContent)}
            </a>
          );
        },
      },
    ];
    return (
      <Row className="page" style={{ marginTop: 48 }}>
        <Col style={{ marginBottom: 48 }}>
          <Row type="flex" justify="center" align="middle">
            <span
              style={{ fontSize: 18 }}
            >{`${notification.total_count.total_send} / ${notification.total_count.total_limit}`}</span>
            <Col span={8} style={{ marginLeft: 16 }}>
              <Progress
                percent={
                  notification.total_count.total_send == 0 ||
                  notification.total_count.total_limit == 0
                    ? 0
                    : notification.total_count.total_send /
                      notification.total_count.total_limit
                }
                showInfo={false}
              />
            </Col>
          </Row>
        </Col>
        <Col>
          <Row>
            <Col span={12}>
              <Row>
                <Col span={10}>
                  <Input
                    placeholder={formatMessage(messages.enterDeviceToken)}
                    value={this.state.text_search}
                    onChange={(e) => {
                      this.setState({
                        text_search: e.target.value,
                      });
                    }}
                  />
                </Col>
                <Col span={10} offset={1}>
                  <Button
                    type="primary"
                    onClick={() => {
                      this.props.dispatch(
                        fetchLogNotification({
                          text_search: this.state.text_search,
                          ...(this.state.month
                            ? {
                                start_time: moment(this.state.month)
                                  .startOf("month")
                                  .unix(),
                                end_time: moment(this.state.month)
                                  .endOf("month")
                                  .unix(),
                              }
                            : {}),
                        })
                      );
                    }}
                  >
                    {formatMessage(messages.search)}
                  </Button>
                </Col>
              </Row>
            </Col>
            <Col span={12}>
              <Row type="flex" align="middle" justify="end">
                <a
                  href="#"
                  onClick={(e) => {
                    e.preventDefault();
                    this.setState(
                      {
                        month: moment(),
                      },
                      () => {
                        this.props.dispatch(
                          fetchLogNotification({
                            text_search: this.state.text_search,
                            ...(this.state.month
                              ? {
                                  start_time: moment(this.state.month)
                                    .startOf("month")
                                    .unix(),
                                  end_time: moment(this.state.month)
                                    .endOf("month")
                                    .unix(),
                                }
                              : {}),
                          })
                        );
                      }
                    );
                  }}
                >
                  {formatMessage(messages.month)}
                </a>
                <DatePicker.MonthPicker
                  format="MM/YYYY"
                  style={{ marginLeft: 24 }}
                  value={this.state.month}
                  allowClear={false}
                  onChange={(month) =>
                    this.setState({ month }, () => {
                      this.props.dispatch(
                        fetchLogNotification({
                          text_search: this.state.text_search,
                          ...(this.state.month
                            ? {
                                start_time: moment(this.state.month)
                                  .startOf("month")
                                  .unix(),
                                end_time: moment(this.state.month)
                                  .endOf("month")
                                  .unix(),
                              }
                            : {}),
                        })
                      );
                    })
                  }
                />
              </Row>
            </Col>
          </Row>
        </Col>
        <Col style={{ paddingTop: 16 }}>
          <Table
            key="id"
            loading={notification.loading}
            columns={columns}
            dataSource={notification.items}
            bordered
            scroll={{ x: 1300 }}
            pagination={{
              pageSize: 20,
              total: notification.pagination.totalCount,
              current: notification.pagination.currentPage,
              showTotal: (total) => {
                formatMessage(messages.total, { total });
              },
            }}
            onChange={this.handleTableChange}
          />
        </Col>
        <Modal
          title={formatMessage(messages.detailNotification)}
          visible={this.state.visible}
          onOk={() => {
            this.setState({
              visible: false,
            });
          }}
          onCancel={() => {
            this.setState({
              visible: false,
            });
          }}
          footer={null}
        >
          {this.state.currentRecord && (
            <div style={{ height: "100%", overflowY: "scroll" }}>
              <div
                className="dangerouslySetInnerHTML"
                dangerouslySetInnerHTML={{
                  __html: this.state.currentRecord.content,
                }}
              />
            </div>
          )}
        </Modal>
      </Row>
    );
  }
}

const mapStateToProps = createStructuredSelector({
  resourceManager: makeSelectResourceManager(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

export default withConnect(injectIntl(NotificationPage));
