/**
 *
 * NotificationFeeList
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Col,
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Table,
  Tooltip,
  Typography,
} from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectNotificationFeeList from "./selectors";
const { Text } = Typography;

import moment from "moment";
import config from "../../../utils/config";
import { defaultAction, fetchNotificationFeeAction } from "./actions";
import styles from "./index.less";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import queryString from "query-string";
import { injectIntl } from "react-intl";
import WithRole from "../../../components/WithRole";
import messages from "./messages";
import { selectAuthGroup } from "redux/selectors";

/* eslint-disable react/prefer-stateless-function */
export class NotificationFeeList extends React.PureComponent {
  state = {
    current: 1,
    filter: {},
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.reload(this.props.location.search);
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

    params.keyword = params.keyword || "";

    this.setState(
      {
        current: params.page,
        keyword: params.keyword,
        filter: reset ? {} : params,
      },
      () => {
        this.props.dispatch(fetchNotificationFeeAction(reset ? {} : params));
        reset &&
          this.props.history.push(
            `${this.props.location.pathname}?${queryString.stringify({
              page: 1,
            })}`
          );
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/finance/notification-fee/list?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmDeleteNotice),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        // this.props.dispatch(deleteResidentAction({
        //   apartment_id: record.apartment_id,
        //   resident_id: record.id,
        //   callback: () => {
        //     this.reload(this.props.location.search)
        //   }
        // }))
      },
      onCancel() {},
    });
  };

  render() {
    const { notificationFeeList, auth_group, language } = this.props;
    const { loading } = notificationFeeList;
    const { current } = this.state;
    const columns = [
      {
        fixed: "left",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        width: 50,
        render: (text, record, index) => (
          <span>
            {Math.max(0, loading ? current - 2 : current - 1) * 20 + index + 1}
          </span>
        ),
      },
      {
        fixed: "left",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.title)}
          </span>
        ),
        dataIndex: this.props.language === "en" ? "title_en" : "title",
        key: "title",
        width: 300,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.typeNotice)}
          </span>
        ),
        dataIndex: "type",
        key: "type",
        render: (text) => {
          const type = config.NOTIFICATION_FEE_TYPE.find(
            (type) => type.id == text
          ) || { label: "Thông báo phí" };
          return (
            <span>
              {this.props.language === "en" ? type.label_en : type.label}
            </span>
          );
        },
      },
      {
        align: "right",
        title: <span className={styles.nameTable}>APP</span>,
        dataIndex: "total_APP_open",
        key: "total_APP_open",
        render: (text, record) => (
          <span>{`${record.total_app_success}/${record.total_app_send}`}</span>
        ),
      },
      {
        align: "right",
        title: <span className={styles.nameTable}>Email</span>,
        dataIndex: "total_Email_open",
        key: "total_Email_open",
        render: (text, record) => (
          <span>{`${record.total_email_send_success}/${record.total_email_send}`}</span>
        ),
      },
      // {
      //   align: "right",
      //   title: <span className={styles.nameTable}>SMS</span>,
      //   dataIndex: "total_SMS_open",
      //   key: "total_SMS_open",
      //   render: (text, record) => (
      //     <span>{`${record.total_sms_send_success}/${record.total_sms_send}`}</span>
      //   ),
      // },
      {
        align: "right",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.public)}
          </span>
        ),
        dataIndex: "send_at",
        key: "send_at",
        render: (text, record, index) =>
          text ? (
            <span>{moment.unix(text).format("DD/MM/YYYY - HH:mm")}</span>
          ) : (
            ""
          ),
      },
      {
        align: "right",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.update)}
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text, record, index) => (
          <span>
            {moment.unix(record.updated_at).format("DD/MM/YYYY - HH:mm")}
          </span>
        ),
      },
    ];
    const { filter } = this.state;

    return (
      <Page inner>
        <div className={styles.notificationFeeListPage}>
          <Row style={{ paddingBottom: 16 }}>
            <Col span={5} style={{ paddingRight: 8 }}>
              <Input.Search
                value={filter["title"] || ""}
                placeholder={this.props.intl.formatMessage(messages.title)}
                prefix={
                  <Tooltip
                    title={this.props.intl.formatMessage(
                      messages.searchViaTitle
                    )}
                  >
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["title"]: e.target.value,
                    },
                  });
                }}
              />
            </Col>
            <Col span={5} style={{ paddingRight: 8 }}>
              <Select
                showSearch
                style={{ width: "100%" }}
                placeholder={this.props.intl.formatMessage(messages.typeNotice)}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["type_in"]: value,
                    },
                  });
                }}
                allowClear
                value={filter["type_in"]}
              >
                {config.NOTIFICATION_FEE_TYPE.map((type) => {
                  return (
                    <Select.Option key={type.id} value={`${type.id}`}>
                      {language === "en" ? type.label_en : type.label}
                    </Select.Option>
                  );
                })}
              </Select>
            </Col>
            <Button
              type="primary"
              onClick={() => {
                this.props.history.push(
                  `/main/finance/notification-fee/list?${queryString.stringify({
                    ...this.state.filter,
                    page: 1,
                  })}`
                );
              }}
            >
              {this.props.intl.formatMessage(messages.search)}
            </Button>
          </Row>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={this.props.intl.formatMessage(messages.refresh)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={(e) => {
                  this.reload(this.props.location.search, true);
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            <WithRole
              roles={[config.ALL_ROLE_NAME.NOTIFICATION_FORM_MANAGEMENT_CREATE]}
            >
              <Tooltip title={this.props.intl.formatMessage(messages.add)}>
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => {
                    this.props.history.push(
                      "/main/finance/notification-fee/add"
                    );
                  }}
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </WithRole>
          </Row>
          <Row>
            <Col>
              <Table
                rowKey="id"
                loading={
                  notificationFeeList.loading || notificationFeeList.deleting
                }
                columns={columns}
                dataSource={notificationFeeList.data}
                locale={{
                  emptyText: this.props.intl.formatMessage(messages.noData),
                }}
                bordered
                pagination={{
                  pageSize: 20,
                  total: notificationFeeList.totalPage,
                  // current: this.state.current
                  showTotal: (total) =>
                    this.props.intl.formatMessage(messages.totalNotice, {
                      total,
                    }),
                }}
                scroll={{ x: 1366 }}
                onChange={this.handleTableChange}
                onRow={(record) => {
                  return {
                    onClick: () => {
                      auth_group.checkRole([
                        config.ALL_ROLE_NAME
                          .NOTIFICATION_FORM_MANAGEMENT_DETAIL,
                      ]) &&
                        this.props.history.push(
                          `/main/finance/notification-fee/detail/${record.id}`,
                          {
                            record,
                          }
                        );
                    },
                  };
                }}
              />
            </Col>
          </Row>
        </div>
      </Page>
    );
  }
}

NotificationFeeList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notificationFeeList: makeSelectNotificationFeeList(),
  language: makeSelectLocale(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "notificationFeeList", reducer });
const withSaga = injectSaga({ key: "notificationFeeList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(NotificationFeeList));
