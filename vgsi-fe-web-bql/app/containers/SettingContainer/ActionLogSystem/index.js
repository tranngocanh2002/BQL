/**
 *
 * ActionLogSystem
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectActionLogSystem from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "../messages";
import Page from "../../../components/Page/Page";
import {
  Row,
  Col,
  DatePicker,
  Select,
  Button,
  Tooltip,
  Table,
  Input,
} from "antd";

import { defaultAction, fetchActionController, fetchLogs } from "./actions";

import queryString from "query-string";
import moment from "moment";
import("./index.less");
import { injectIntl } from "react-intl";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

/* eslint-disable react/prefer-stateless-function */
export class ActionLogSystem extends React.PureComponent {
  state = {
    filter: {},
    // start_date: undefined,
    // end_date: undefined,
    // management_user_name: "",
    // controller: undefined,
    // action: undefined,
    current: 1,
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.reload(this.props.location.search);
    this.props.dispatch(fetchActionController());
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
  }

  reload = (search, reset) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }
    this.setState({ current: params.page, filter: reset ? {} : params }, () => {
      this.props.dispatch(fetchLogs(reset ? { page: 1 } : params));
      reset &&
        this.props.history.push(
          `${this.props.location.pathname}?${queryString.stringify({
            page: 1,
          })}`
        );
    });
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/setting/logs?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  render() {
    const { controllers, logs } = this.props.actionLogSystem;
    let currentController = controllers.data[this.state.filter.controller];
    const { current, loading } = this.state;
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
            {Math.max(0, loading ? current - 2 : current - 1) * 20 + index + 1}
          </span>
        ),
      },
      {
        width: "25%",
        title: (
          <span className="nameTable">{formatMessage(messages.time)}</span>
        ),
        dataIndex: "created_at",
        key: "created_at",
        render: (text) => moment.unix(text).format("DD/MM/YYYY - HH:mm"),
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        width: "25%",
        title: (
          <span className="nameTable">
            {formatMessage(messages.accountLog)}
          </span>
        ),
        dataIndex: "management_user_name",
        key: "management_user_name",
      },
      {
        width: "25%",
        title: (
          <span className="nameTable">{formatMessage(messages.object)}</span>
        ),
        dataIndex:
          this.props.language === "vi" ? "controller_name" : "controller",
        key: "controller_name",
        render: (text) => {
          const replace = text
            .replaceAll("-", " ")
            .replace("apartment", "property")
            .replace("building", "project")
            .replace("Căn hộ", "bất động sản")
            .replace("tòa nhà", "dự án")
            .capitalize();
          return <span>{replace}</span>;
        },
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.action)}</span>
        ),
        dataIndex: this.props.language === "vi" ? "action_name" : "action",
        key: "action_name",
        render: (text) => <span>{text.replaceAll("-", " ").capitalize()}</span>,
      },
    ];
    return (
      <Page className="actionLogSystem" inner>
        <Row>
          <Col>
            <Row>
              <Col span={4} style={{ paddingRight: 10 }}>
                <DatePicker
                  placeholder={formatMessage(messages.start)}
                  style={{ width: "100%" }}
                  format="DD/MM/YYYY"
                  value={
                    this.state.filter.start_date
                      ? moment.unix(this.state.filter.start_date)
                      : undefined
                  }
                  disabledDate={(current) => {
                    if (this.state.filter.end_date) {
                      return (
                        current &&
                        current >
                          moment(
                            moment.unix(this.state.filter.end_date).endOf("day")
                          )
                      );
                    }
                    // can not select days after today
                    return current && current > moment().endOf("day");
                  }}
                  onChange={(start_date) => {
                    this.setState({
                      filter: {
                        ...this.state.filter,
                        start_date: start_date
                          ? start_date.startOf("day").unix()
                          : undefined,
                      },
                    });
                  }}
                />
              </Col>
              <Col span={4} style={{ paddingRight: 10 }}>
                <DatePicker
                  placeholder={formatMessage(messages.end)}
                  style={{ width: "100%" }}
                  format="DD/MM/YYYY"
                  value={
                    this.state.filter.end_date
                      ? moment.unix(this.state.filter.end_date)
                      : undefined
                  }
                  disabledDate={(current) => {
                    if (this.state.filter.start_date) {
                      return (
                        current &&
                        current <
                          moment(
                            moment
                              .unix(this.state.filter.start_date)
                              .startOf("day")
                          )
                      );
                    }
                    // can not select days after today
                    return current && current > moment().endOf("day");
                  }}
                  onChange={(end_date) => {
                    this.setState({
                      filter: {
                        ...this.state.filter,
                        end_date: end_date
                          ? end_date.endOf("day").unix()
                          : undefined,
                      },
                    });
                  }}
                />
              </Col>
              <Col span={4} style={{ paddingRight: 10 }}>
                <Input.Search
                  placeholder={formatMessage(messages.accountLog)}
                  value={this.state.filter.management_user_name}
                  onChange={(e) => {
                    this.setState({
                      filter: {
                        ...this.state.filter,
                        management_user_name: e.target.value,
                      },
                    });
                  }}
                  onSearch={(management_user_name) => {
                    this.setState(
                      {
                        filter: { ...this.state.filter, management_user_name },
                      },
                      () => {
                        this.props.history.push(
                          `/main/setting/logs?${queryString.stringify({
                            ...this.state.filter,
                            page: 1,
                          })}`
                        );
                      }
                    );
                  }}
                />
              </Col>
              <Col span={4} style={{ paddingRight: 10 }}>
                <Select
                  placeholder={formatMessage(messages.selectObject)}
                  style={{ width: "100%" }}
                  loading={controllers.loading}
                  value={this.state.filter.controller}
                  onChange={(e) => {
                    this.setState({
                      filter: {
                        ...this.state.filter,
                        controller: e,
                        action: undefined,
                      },
                    });
                  }}
                  allowClear
                >
                  {Object.keys(controllers.data).map((key, index) => {
                    return (
                      <Select.Option key={`${key}-${index}`} value={key}>
                        {this.props.language === "vi"
                          ? controllers.data[key].name
                          : key.replaceAll("-", " ").capitalize()}
                      </Select.Option>
                    );
                  })}
                </Select>
              </Col>
              <Col span={4} style={{ paddingRight: 10 }}>
                <Select
                  placeholder={formatMessage(messages.action)}
                  style={{ width: "100%" }}
                  loading={controllers.loading}
                  value={this.state.filter.action}
                  onChange={(e) => {
                    this.setState({
                      filter: { ...this.state.filter, action: e },
                    });
                  }}
                  allowClear
                >
                  {!!currentController &&
                    Object.keys(currentController.actions).map((key, index) => {
                      return (
                        <Select.Option key={`${key}-${index}`} value={key}>
                          {this.props.language === "vi"
                            ? currentController.actions[key]
                            : key.replaceAll("-", " ").capitalize()}
                        </Select.Option>
                      );
                    })}
                </Select>
              </Col>
              <Button
                type="primary"
                onClick={(e) => {
                  e.preventDefault();
                  this.props.history.push(
                    `/main/setting/logs?${queryString.stringify({
                      ...this.state.filter,
                      page: 1,
                    })}`
                  );
                }}
              >
                {formatMessage(messages.search)}
              </Button>
            </Row>
          </Col>
          <Col>
            <Row style={{ paddingBottom: 16, paddingTop: 16 }}>
              <Tooltip title={formatMessage(messages.refresh)}>
                <Button
                  shape="circle-outline"
                  style={{ padding: 0, marginRight: 10 }}
                  onClick={() => {
                    this.reload(this.props.location.search, true);
                  }}
                  icon="reload"
                  size="large"
                />
              </Tooltip>
            </Row>
          </Col>
          <Table
            rowKey="id"
            loading={logs.loading}
            bordered
            columns={columns}
            dataSource={logs.data}
            pagination={{
              pageSize: 20,
              total: logs.totalPage,
              current,
              showTotal: (total) =>
                formatMessage(messages.total, {
                  total,
                }),
            }}
            onChange={this.handleTableChange}
          />
        </Row>
      </Page>
    );
  }
}

ActionLogSystem.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  actionLogSystem: makeSelectActionLogSystem(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "actionLogSystem", reducer });
const withSaga = injectSaga({ key: "actionLogSystem", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ActionLogSystem));
