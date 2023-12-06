/**
 *
 * NotificationList
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import messages from "../messages";

import {
  Button,
  Col,
  Dropdown,
  Icon,
  Input,
  Menu,
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
import makeSelectNotificationList from "./selectors";
const { Text } = Typography;

import config from "../../../utils/config";
import {
  defaultAction,
  deleteNotificationAction,
  fetchCategoryNotificationAction,
  fetchNotificationAction,
} from "./actions";
import styles from "./index.less";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import queryString from "query-string";
import { selectAuthGroup } from "redux/selectors";
import { getIconStyle, getRowStyle, timeFromNow } from "utils/constants";
import WithRole from "../../../components/WithRole";

const notificationType1 = [
  {
    id: "0",
    label: "Thông báo thường",
    label_en: "Normal announcement",
  },
  {
    id: "5",
    label: "Thông báo phí",
    label_en: "Fee announcement",
  },
  {
    id: "6",
    label: "Thông báo khảo sát",
    label_en: "Survey announcement",
  },
];

const notificationType2 = [
  {
    id: "0",
    label: "Thông báo thường",
    label_en: "Normal announcement",
  },
  {
    id: "6",
    label: "Thông báo khảo sát",
    label_en: "Survey announcement",
  },
];
const colLayout = {
  md: 5,
  xxl: 4,
};
/* eslint-disable react/prefer-stateless-function */
export class NotificationList extends React.PureComponent {
  state = {
    current: 1,
    filter: {},
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchCategoryNotificationAction({ page: 1 }));
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
        this.props.dispatch(
          fetchNotificationAction(reset ? { page: 1 } : params)
        );

        reset && this.props.history.push("/main/notification/list");
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/notification/list?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage({
        ...messages.deleteNotificationModalTitle,
      }),
      okText: this.props.intl.formatMessage({ ...messages.confirm }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({ ...messages.cancel }),
      onOk: () => {
        console.log(record);
        this.props.dispatch(
          deleteNotificationAction({
            ...record,
            callback: () => {
              this.props.dispatch(fetchNotificationAction(this.state.filter));
            },
            message: `${this.props.intl.formatMessage(
              messages.deleteNotification
            )} ${this.props.intl.formatMessage(messages.success)}`,
          })
        );
      },
      onCancel() {},
    });
  };

  render() {
    const { notificationList, intl, language, auth_group } = this.props;
    const titleText = intl.formatMessage({ ...messages.title });
    const statusText = intl.formatMessage({ ...messages.status });
    const authorText = intl.formatMessage({ ...messages.author });
    const { category, loading } = notificationList;
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
            <FormattedMessage {...messages.title} />
          </span>
        ),
        dataIndex: this.props.language == "en" ? "title_en" : "title",
        key: "title",
        width: 300,
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.category} />
          </span>
        ),
        dataIndex: "announcement_category_name",
        key: "announcement_category_name",
        width: 250,
        render: (text, record) => {
          const categoryData = category.lst.find(
            (item) => item.id === record.announcement_category_id
          );
          return (
            <span
              className={"luci-status-success"}
              style={{ backgroundColor: record.announcement_category_color }}
            >
              {language === "en"
                ? categoryData
                  ? categoryData.name_en
                  : text
                : text}
            </span>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.status} />
          </span>
        ),
        width: 140,
        dataIndex: "status",
        key: "status",
        align: "left",
        render: (text, record) => {
          const statusData = config.STATUS_NOTIFICATION.find(
            (item) => item.id === record.status
          );
          return (
            <Text className={statusData.className}>
              {language === "en"
                ? statusData && statusData.name_en
                : statusData && statusData.name}
            </Text>
          );
        },
      },
      {
        align: "left",
        title: <span className={styles.nameTable}>APP</span>,
        dataIndex: "total_APP_open",
        key: "total_APP_open",
        render: (text, record) =>
          `${record.total_app_success}/${record.total_app_send}`,
      },
      {
        align: "left",
        title: <span className={styles.nameTable}>Email</span>,
        dataIndex: "total_Email_open",
        key: "total_Email_open",
        render: (text, record) =>
          `${record.total_email_send_success}/${record.total_email_send}`,
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.author} />
          </span>
        ),
        align: "left",
        dataIndex: "management_user.first_name",
        key: "author",
      },
      {
        align: "left",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.public} />
          </span>
        ),
        dataIndex: "send_at",
        key: "send_at",
        render: (text) => (text ? <span>{timeFromNow(text)}</span> : ""),
      },
      {
        align: "left",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.update} />
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text) => <span>{timeFromNow(text)}</span>,
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {<FormattedMessage {...messages.action} />}
          </span>
        ),
        dataIndex: "",
        key: "x",
        width: 200,
        fixed: "right",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip
              title={<FormattedMessage {...messages.notificationDetail} />}
            >
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.NOTIFICATION_FORM_MANAGEMENT_DETAIL,
                  ])
                    ? { cursor: "pointer" }
                    : { cursor: "not-allowed" }
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();

                  this.props.history.push(
                    `/main/notification/detail/${record.id}`,
                    {
                      record,
                      category,
                    }
                  );
                }}
              >
                <i
                  className="fa fa-eye"
                  style={getIconStyle(
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.NOTIFICATION_FORM_MANAGEMENT_DETAIL,
                    ])
                  )}
                />
              </Row>
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip
              title={<FormattedMessage {...messages.editNotification} />}
            >
              <Row
                type="flex"
                align="middle"
                style={getRowStyle(
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.NOTIFICATION_FORM_MANAGEMENT_UPDATE,
                  ]) && record.type === 0
                )}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  record.type === 0 &&
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.NOTIFICATION_FORM_MANAGEMENT_UPDATE,
                    ]) &&
                    this.props.history.push(
                      `/main/notification/edit/${record.id}?notiType=${
                        record.is_survey === 1 ? 6 : 0
                      }`
                    );
                }}
              >
                <i
                  className="fa fa-edit"
                  style={getIconStyle(
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.NOTIFICATION_FORM_MANAGEMENT_UPDATE,
                    ]) && record.type === 0
                  )}
                />
              </Row>
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip
              title={<FormattedMessage {...messages.deleteNotification} />}
            >
              <Row
                type="flex"
                align="middle"
                style={getRowStyle(
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.NOTIFICATION_FORM_MANAGEMENT_DELETE,
                  ]) && record.status !== 1,
                  true
                )}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.NOTIFICATION_FORM_MANAGEMENT_DELETE,
                  ]) &&
                    record.status !== 1 &&
                    this._onDelete(record);
                }}
              >
                <i
                  className="fa fa-trash"
                  style={{
                    fontSize: 18,
                  }}
                />
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];
    const { filter } = this.state;
    const { title, management_user_name, ...rest } = filter;
    const newFilter = {
      ...rest,
      management_user_name: management_user_name
        ? management_user_name.trim()
        : management_user_name,
      title: title ? title.trim() : title,
    };

    return (
      <Page inner>
        <div className={styles.notificationListPage}>
          <Row style={{ paddingBottom: 16 }} gutter={[8, 8]}>
            <Col {...colLayout}>
              <Input.Search
                value={filter["title"] || ""}
                placeholder={titleText}
                maxLength={255}
                prefix={
                  <Tooltip
                    title={
                      <FormattedMessage {...messages.searchNotificationTitle} />
                    }
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
            <Col {...colLayout}>
              <Select
                // showSearch
                style={{ width: "100%" }}
                placeholder={<FormattedMessage {...messages.category} />}
                // optionFilterProp="children"
                // filterOption={(input, option) =>
                //   option.props.children
                //     .toLowerCase()
                //     .indexOf(input.toLowerCase()) >= 0
                // }
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["announcement_category_id"]: value,
                    },
                  });
                }}
                allowClear
                value={filter["announcement_category_id"]}
              >
                {category.lst.map((lll) => {
                  return (
                    <Select.Option key={lll.id} value={`${lll.id}`}>
                      {this.props.language === "vi" ? lll.name : lll.name_en}
                    </Select.Option>
                  );
                })}
              </Select>
            </Col>
            <Col {...colLayout}>
              <Select
                // showSearch
                style={{ width: "100%" }}
                placeholder={statusText}
                // optionFilterProp="children"
                // filterOption={(input, option) =>
                //   option.props.children
                //     .toLowerCase()
                //     .indexOf(input.toLowerCase()) >= 0
                // }
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["status"]: value,
                    },
                  });
                }}
                allowClear
                value={filter["status"]}
              >
                {config.STATUS_NOTIFICATION.map((lll) => (
                  <Select.Option key={lll.id} value={`${lll.id}`}>
                    {language === "en" ? lll.name_en : lll.name}
                  </Select.Option>
                ))}
              </Select>
            </Col>
            <Col {...colLayout}>
              <Input.Search
                value={filter["management_user_name"] || ""}
                placeholder={authorText}
                maxLength={255}
                prefix={
                  <Tooltip
                    title={
                      <FormattedMessage
                        {...messages.searchNotificationAuthor}
                      />
                    }
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
                      ["management_user_name"]: e.target.value,
                    },
                  });
                }}
              />
            </Col>

            <Col md={4} lg={3} xl={2}>
              <Button
                type="primary"
                onClick={() => {
                  this.props.history.push(
                    `/main/notification/list?${queryString.stringify({
                      ...newFilter,
                      page: 1,
                    })}`
                  );
                }}
              >
                <FormattedMessage {...messages.search} />
              </Button>
            </Col>
          </Row>
          <Row
            type="flex"
            justify="space-between"
            style={{ paddingBottom: 16 }}
          >
            <Tooltip title={<FormattedMessage {...messages.reload} />}>
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
            <WithRole
              roles={[config.ALL_ROLE_NAME.NOTIFICATION_FORM_MANAGEMENT_CREATE]}
            >
              <Dropdown
                overlay={
                  <Menu
                    onClick={(item) => {
                      if (item.key === "5")
                        this.props.history.push(
                          "/main/finance/notification-fee/add"
                        );
                      else {
                        this.props.history.push(
                          `/main/notification/add/${item.key}`
                        );
                      }
                    }}
                  >
                    {auth_group.checkRole([
                      config.ALL_ROLE_NAME.FINANCE_NOTIFICATION_FEE_MANAGER,
                    ])
                      ? notificationType1.map((item) => {
                          return (
                            <Menu.Item key={item.id}>
                              {this.props.language === "en"
                                ? item.label_en
                                : item.label}
                            </Menu.Item>
                          );
                        })
                      : notificationType2.map((item) => {
                          return (
                            <Menu.Item key={item.id}>
                              {this.props.language === "en"
                                ? item.label_en
                                : item.label}
                            </Menu.Item>
                          );
                        })}
                  </Menu>
                }
              >
                <Button>
                  {this.props.intl.formatMessage(messages.addNew)}{" "}
                  <Icon type="down" />
                </Button>
              </Dropdown>
            </WithRole>
          </Row>
          <Row>
            <Col>
              <Table
                rowKey="id"
                loading={notificationList.loading || notificationList.deleting}
                columns={columns}
                dataSource={notificationList.data}
                bordered
                locale={{
                  emptyText: <FormattedMessage {...messages.noData} />,
                }}
                pagination={{
                  pageSize: 20,
                  total: notificationList.totalPage,
                  current: this.state.current,
                  showTotal: (total) => (
                    <FormattedMessage
                      {...messages.totalNotification}
                      values={{
                        total,
                      }}
                    />
                  ),
                }}
                scroll={{ x: 1366 }}
                onChange={this.handleTableChange}
                onRow={(record) => {
                  return {
                    onClick: () => {
                      this.props.history.push(
                        `/main/notification/detail/${record.id}`,
                        {
                          record,

                          category,
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

NotificationList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notificationList: makeSelectNotificationList(),
  language: makeSelectLocale(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "notificationList", reducer });
const withSaga = injectSaga({ key: "notificationList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(NotificationList));
