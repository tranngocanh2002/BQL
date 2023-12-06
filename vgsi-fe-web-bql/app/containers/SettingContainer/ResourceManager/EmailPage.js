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
import { FormattedMessage, injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import makeSelectResourceManager from "./selectors";
import { fetchLogEmail } from "./actions";
import messages from "../messages";
import("./index.less");
class EmailPage extends React.PureComponent {
  state = {
    text_search: "",
    visible: false,
    month: moment(),
  };

  componentDidMount() {
    this.props.dispatch(fetchLogEmail({}));
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
          fetchLogEmail({
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
    const { email } = this.props.resourceManager;
    const formatMessage = this.props.intl.formatMessage;
    const columns = [
      {
        width: 50,
        align: "center",
        title: <span className="nameTable">#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>{(email.pagination.currentPage - 1) * 20 + index + 1}</span>
        ),
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.timeSend)}</span>
        ),
        dataIndex: "created_at",
        key: "created_at",
        render: (text) => moment.unix(text).format("DD/MM/YYYY - HH:mm"),
      },
      {
        title: <span className="nameTable">Email</span>,
        dataIndex: "email",
        key: "email",
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.status)}</span>
        ),
        dataIndex: "status_sms",
        key: "status_sms",
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
        dataIndex: "action_name",
        key: "action_name",
        render: (text, record) => {
          return (
            <a
              href="#"
              onClick={(e) => {
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
            >{`${email.total_count.total_send} / ${email.total_count.total_limit}`}</span>
            <Col span={8} style={{ marginLeft: 16 }}>
              <Progress
                percent={
                  email.total_count.total_send == 0 ||
                  email.total_count.total_limit == 0
                    ? 0
                    : email.total_count.total_send /
                      email.total_count.total_limit
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
                    placeholder={formatMessage(messages.enterEmail)}
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
                    onClick={(e) => {
                      this.props.dispatch(
                        fetchLogEmail({
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
                          fetchLogEmail({
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
                        fetchLogEmail({
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
            loading={email.loading}
            columns={columns}
            dataSource={email.items}
            bordered
            pagination={{
              pageSize: 20,
              total: email.pagination.totalCount,
              current: email.pagination.currentPage,
              showTotal: (total, range) =>
                formatMessage(messages.total, { total }),
            }}
            onChange={this.handleTableChange}
          />
        </Col>
        <Modal
          title={formatMessage(messages.detailEmail)}
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

export default withConnect(injectIntl(EmailPage));
