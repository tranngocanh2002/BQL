/**
 *
 * TaskList
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Col,
  Icon,
  Input,
  Modal,
  Radio,
  Row,
  Select,
  Table,
  Tooltip,
  Typography,
  notification,
} from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import queryString from "query-string";
import { selectAuthGroup, selectUserDetail } from "redux/selectors";
import { toLowerCaseNonAccentVietnamese } from "utils";
import config from "utils/config";
import { JobStatuses, getTaskStatusName } from "utils/constants";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { GLOBAL_COLOR } from "../../../utils/constants";
import messages from "../messages";
import {
  defaultAction,
  fetchAllStaffAction,
  fetchAllTaskAction,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectTaskList from "./selectors";
const { Text } = Typography;

const topCol3 = {
  sm: 8,
  md: 4,
  lg: 5,
  xl: 6,
  xxl: 3,
};

/* eslint-disable react/prefer-stateless-function */
export class TaskList extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      visible: false,
      current: 1,
      filter: {
        sort: "-created_at",
      },
      currentEdit: undefined,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(
      fetchAllStaffAction({
        status: "1",
      })
    );
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
      if (params.page) {
        params.page = parseInt(params.page);
      } else {
        params.page = 1;
      }
      if (!params.sort) {
        params.sort = "-created_at";
      }
    } catch (error) {
      params.page = 1;
    }
    this.setState(
      {
        current: params.page,
        filter: reset
          ? {}
          : {
              ...params,
              performer: params.performer
                ? Array.isArray(params.performer)
                  ? params.performer.map((item) => Number(item))
                  : [Number(params.performer)]
                : undefined,
              people_involved: params.people_involved
                ? Array.isArray(params.people_involved)
                  ? params.people_involved.map((item) => Number(item))
                  : [Number(params.people_involved)]
                : undefined,
              created_by: params.created_by
                ? Array.isArray(params.created_by)
                  ? params.created_by.map((item) => Number(item))
                  : [Number(params.created_by)]
                : undefined,
            },
        // {
        // ...params,
        // performer: params.performer
        //   ? Array.isArray(params.performer)
        //     ? params.performer.map((item) => Number(item))
        //     : [Number(params.performer)]
        //   : undefined,
        // people_involved: params.people_involved
        //   ? Array.isArray(params.people_involved)
        //     ? params.people_involved.map((item) => Number(item))
        //     : [Number(params.people_involved)]
        //   : undefined,
        // created_by: params.created_by
        //   ? Array.isArray(params.created_by)
        //     ? params.created_by.map((item) => Number(item))
        //     : [Number(params.created_by)]
        //   : undefined,
        // },
      },
      () => {
        this.props.dispatch(
          fetchAllTaskAction(reset ? { page: 1, sort: "-created_at" } : params)
        );
        reset &&
          this.props.history.push(
            `${this.props.location.pathname}?${queryString.stringify({
              sort: "-created_at",
              page: 1,
            })}`
          );
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    this.setState({ current: pagination.current }, () => {
      this.props.history.push(
        `/main/task/list?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  onChangeStatus = (e) => {
    const { filter } = this.state;
    this.setState(
      {
        filter: {
          ...filter,
          ["status"]: e.target.value == -3 ? undefined : e.target.value,
        },
      },
      () => {
        this.props.history.push(
          `/main/task/list?${queryString.stringify({
            ...this.state.filter,
            page: 1,
          })}`
        );
      }
    );
  };

  _onAdd = () => {
    this.props.history.push("/main/task/add");
  };

  _onEdit = (record) => {
    this.props.history.push(`/main/task/edit/${record.id}`, { record });
  };

  onChangeFilter = (key, value) => {
    const filter = { ...this.state.filter };
    this.setState({
      filter: {
        ...filter,
        [key]: value,
      },
    });
  };

  _deleteTask = (record) => {
    Modal.confirm({
      title: this.props.intl.formatMessage(messages.deleteTaskQuestion),
      //content: this.props.intl.formatMessage(messages.deleteTaskQuestion),
      cancelText: this.props.intl.formatMessage(messages.cancel),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      onOk: () => {
        window.connection.deleteTask({ id: record.id }).then((res) => {
          if (res.success) {
            notification.success({
              message: this.props.intl.formatMessage(
                messages.deleteTaskSuccess
              ),
            });
            let params = queryString.parse(this.props.location.search);
            if (
              params.page &&
              params.page > 1 &&
              this.props.taskList.pageCount === params.page
            ) {
              this.props.history.push(
                `/main/task/list?${queryString.stringify({
                  ...this.state.filter,
                  page: params.page - 1,
                })}`
              );
            } else {
              this.reload(this.props.location.search);
            }
          }
        });
      },
    });
  };

  render() {
    const formatMessage = this.props.intl.formatMessage;
    const { current, filter } = this.state;
    const { userDetail, auth_group } = this.props;
    const { loading, loadingStaff, data, totalCount, staffs, pageCount } =
      this.props.taskList;

    const columns = [
      {
        fixed: "left",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "prioritize",
        key: "prioritize",
        width: 50,
        render: (text) => (
          <Icon
            type="flag"
            theme={text === 1 ? "filled" : "outlined"}
            style={{ color: text === 1 ? "#FF821E" : "" }}
          />
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.title)}
          </span>
        ),
        dataIndex: "title",
        key: "title",
        align: "left",
        fixed: "left",
        width: 300,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.assignee)}
          </span>
        ),
        width: 300,
        dataIndex: "performers",
        key: "performers",
        render: (text) => text.map((e) => e.first_name).join(", "),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.startTime)}
          </span>
        ),
        dataIndex: "time_start",
        key: "time_start",
        width: 180,
        render: (text) =>
          text ? moment.unix(text).format("HH:mm, DD/MM/YYYY") : "---",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.endTime)}
          </span>
        ),
        dataIndex: "time_end",
        key: "time_end",
        width: 180,
        render: (text) =>
          text ? moment.unix(text).format("HH:mm, DD/MM/YYYY") : "---",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.status)}
          </span>
        ),
        dataIndex: "status",
        key: "status",
        width: 150,
        render: (text) => (
          <Text className={`status-${getTaskStatusName(text)}`}>
            {formatMessage(messages[getTaskStatusName(text)])}
          </Text>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.term)}
          </span>
        ),
        dataIndex: "time_end",
        key: "term",
        render: (text, record) => {
          // if (record.count_expire < 0) {
          //   return (
          //     <span style={{ color: "#fc0303" }}>
          //       {formatMessage(messages.over, {
          //         over: Math.abs(record.count_expire),
          //       })}
          //     </span>
          //   );
          // }
          // if (record.count_expire > 0) {
          //   return (
          //     <span style={{ color: "#07fc03" }}>
          //       {formatMessage(messages.left, {
          //         left: record.count_expire,
          //       })}
          //     </span>
          //   );
          // }
          // if (record.count_expire === 0) {
          //   return (
          //     <span style={{ color: "#fcb103" }}>
          //       {formatMessage(messages.meetDeadline)}
          //     </span>
          //   );
          // }
          if (text === "" || text === undefined || text === null) {
            return "";
          }
          let hours = moment.unix(text).format("HH:mm:ss");
          let day = moment.unix(text).format("YYYY-MM-DD");
          let current = moment().format("YYYY-MM-DD");
          if (record.status === 0 || record.status === 1) {
            if (hours > "07:00:00") {
              if (
                day > current &&
                moment(day).diff(moment(current), "days") < 7
              ) {
                return (
                  <span style={{ color: "#07fc03" }}>
                    {formatMessage(messages.left, {
                      left: moment(day).diff(moment(current), "days"),
                    })}
                  </span>
                );
              }
              if (day < current) {
                return (
                  <span style={{ color: "#fc0303" }}>
                    {formatMessage(messages.over, {
                      over: moment(current).diff(moment(day), "days"),
                    })}
                  </span>
                );
              }
              if (day === current) {
                return (
                  <span style={{ color: "#fcb103" }}>
                    {formatMessage(messages.meetDeadline)}
                  </span>
                );
              }
              return "";
            }
          } else return "";
        },
      },
      {
        fixed: "right",
        align: "center",
        width: 150,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "status",
        key: "x",
        render: (_text, record) => {
          // if (
          // userDetail &&
          // record.assignor.id === userDetail.id &&
          // record.status !== JobStatuses.done &&
          // record.status !== JobStatuses.cancel
          // ) {
          const showDelete = record.status === JobStatuses.new;
          const showEdit =
            record.status === JobStatuses.new ||
            record.status === JobStatuses.doing;
          const assign =
            userDetail &&
            record &&
            record.assignor &&
            record.assignor.id === userDetail.id &&
            record.status !== JobStatuses.done &&
            record.status !== JobStatuses.cancel;

          return (
            <Row type="flex" align="middle" justify="center">
              {/* <Tooltip title={this.props.intl.formatMessage(messages.detail)}>
                <Row
                  type="flex"
                  align="middle"
                  style={{
                    color: GLOBAL_COLOR,
                    marginRight: 10,
                    cursor: "pointer",
                  }}
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this._onView(record);
                  }}
                >
                  <i className="fa fa-eye" style={{ fontSize: 18 }} />
                </Row>
              </Tooltip>
              | */}

              <Tooltip title={this.props.intl.formatMessage(messages.edit)}>
                <Row
                  type="flex"
                  align="middle"
                  style={{
                    color: !showEdit || !assign ? "grey" : GLOBAL_COLOR,
                    marginLeft: 10,
                    marginRight: 8,
                    cursor: !showEdit || !assign ? "not-allowed" : "pointer",
                    padding: 4,
                  }}
                  onClick={
                    !showEdit || !assign
                      ? null
                      : (e) => {
                          e.preventDefault();
                          e.stopPropagation();
                          this._onEdit(record);
                        }
                  }
                >
                  <i className="fa fa-edit" style={{ fontSize: 18 }} />
                </Row>
              </Tooltip>

              <Tooltip title={this.props.intl.formatMessage(messages.delete)}>
                <Row
                  type="flex"
                  align="middle"
                  style={{
                    color: !showDelete || !assign ? "grey" : "#F15A29",
                    marginLeft: 8,
                    cursor: !showDelete || !assign ? "not-allowed" : "pointer",

                    padding: 4,
                  }}
                  onClick={
                    !showDelete || !assign
                      ? null
                      : (e) => {
                          e.preventDefault();
                          e.stopPropagation();
                          this._deleteTask(record);
                        }
                  }
                >
                  <i className="fa fa-trash" style={{ fontSize: 18 }} />
                </Row>
              </Tooltip>
            </Row>
          );
        },
      },
    ];
    if (
      !auth_group.checkRole([config.ALL_ROLE_NAME.WORKFLOW_MANAGENMENT_GROUP])
    ) {
      columns.splice(columns.length - 1, 1);
    }
    return (
      <Page inner>
        <div className={styles.taskListPage}>
          <Row style={{ paddingBottom: 16 }} gutter={[12, 12]} span={24}>
            <Col {...topCol3} style={{ paddingRight: 8, marginTop: 2 }}>
              <Input.Search
                placeholder={formatMessage(messages.title)}
                prefix={
                  <Tooltip title={formatMessage(messages.title)}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => this.onChangeFilter("title", e.target.value)}
                value={filter.title}
              />
            </Col>
            <Col {...topCol3} style={{ paddingRight: 8, marginTop: 2 }}>
              <Select
                mode="multiple"
                showArrow
                loading={loadingStaff}
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.assignee)}
                maxTagCount={1}
                onChange={(value) => {
                  this.onChangeFilter(
                    "performer",
                    value.length > 0 ? value : undefined
                  );
                }}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  toLowerCaseNonAccentVietnamese(option.props.children).indexOf(
                    toLowerCaseNonAccentVietnamese(input)
                  ) >= 0
                }
                value={filter.performer}
              >
                {staffs.map((staff) => (
                  <Select.Option value={staff.id} key={staff.id}>
                    {staff.first_name}
                  </Select.Option>
                ))}
              </Select>
            </Col>
            <Col {...topCol3} style={{ paddingRight: 8, marginTop: 2 }}>
              <Select
                mode="multiple"
                showArrow
                loading={loadingStaff}
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.followers)}
                maxTagCount={1}
                onChange={(value) => {
                  this.onChangeFilter(
                    "people_involved",
                    value.length > 0 ? value : undefined
                  );
                }}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  toLowerCaseNonAccentVietnamese(option.props.children).indexOf(
                    toLowerCaseNonAccentVietnamese(input)
                  ) >= 0
                }
                value={filter.people_involved}
              >
                {staffs.map((staff) => (
                  <Select.Option value={staff.id} key={staff.id}>
                    {staff.first_name}
                  </Select.Option>
                ))}
              </Select>
            </Col>
            <Col {...topCol3} style={{ paddingRight: 8, marginTop: 2 }}>
              <Select
                mode="multiple"
                showArrow
                loading={loadingStaff}
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.creator)}
                maxTagCount={1}
                onChange={(value) => {
                  this.onChangeFilter(
                    "created_by",
                    value.length > 0 ? value : undefined
                  );
                }}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  toLowerCaseNonAccentVietnamese(option.props.children).indexOf(
                    toLowerCaseNonAccentVietnamese(input)
                  ) >= 0
                }
                value={filter.created_by}
              >
                {staffs.map((staff) => (
                  <Select.Option value={staff.id} key={staff.id}>
                    {staff.first_name}
                  </Select.Option>
                ))}
              </Select>
            </Col>

            <Col {...topCol3} style={{ paddingRight: 8, marginTop: 2 }}>
              <Select
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.taskType)}
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["category"]:
                        value === undefined ? undefined : value.toString(),
                    },
                  });
                }}
                allowClear
                value={
                  filter["category"] === undefined
                    ? undefined
                    : filter["category"] === "0"
                    ? formatMessage(messages.assigning)
                    : filter["category"] === "1"
                    ? formatMessage(messages.performing)
                    : formatMessage(messages.following)
                }
              >
                <Select.Option value={0}>
                  {formatMessage(messages.assigning)}
                </Select.Option>
                <Select.Option value={1}>
                  {formatMessage(messages.performing)}
                </Select.Option>
                <Select.Option value={2}>
                  {formatMessage(messages.following)}
                </Select.Option>
              </Select>
            </Col>

            <Col {...topCol3} style={{ paddingRight: 8, marginTop: 2 }}>
              <Select
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.sortedBy)}
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      sort: value,
                    },
                  });
                }}
                value={filter.sort}
                allowClear
              >
                <Select.Option value={"-updated_at"}>
                  {formatMessage(messages.updatedAt)}
                </Select.Option>
                <Select.Option value={"-created_at"}>
                  {formatMessage(messages.createdAt)}
                </Select.Option>
                <Select.Option value={"time_end"}>
                  {formatMessage(messages.deadline)}
                </Select.Option>
              </Select>
            </Col>

            <Col {...topCol3} style={{ paddingRight: 8, marginTop: 2 }}>
              <Select
                // defaultValue={this.state.priority}
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.priority)}
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["prioritize"]:
                        value === undefined ? undefined : value.toString(),
                    },
                  });
                }}
                value={
                  filter["prioritize"] === undefined
                    ? undefined
                    : filter["prioritize"] === "0"
                    ? formatMessage(messages.no)
                    : formatMessage(messages.yes)
                }
                allowClear
              >
                <Select.Option value={1}>
                  {formatMessage(messages.yes)}
                </Select.Option>
                <Select.Option value={0}>
                  {formatMessage(messages.no)}
                </Select.Option>
              </Select>
            </Col>
            <Col {...topCol3}>
              <Button
                type="primary"
                style={{ marginTop: 2 }}
                onClick={() => {
                  this.props.history.push(
                    `/main/task/list?${queryString.stringify({
                      ...filter,
                      page: 1,
                    })}`
                  );
                }}
              >
                <FormattedMessage {...messages.search} />
              </Button>
            </Col>
          </Row>

          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={formatMessage(messages.reload)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                icon="reload"
                size="large"
                onClick={() => {
                  this.reload(this.props.location.search, true);
                }}
              />
            </Tooltip>
            {auth_group.checkRole([
              config.ALL_ROLE_NAME.WORKFLOW_MANAGENMENT_GROUP,
            ]) && (
              <Tooltip title={formatMessage(messages.addNew)}>
                <Button
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this._onAdd();
                  }}
                  shape="circle-outline"
                  style={{ padding: 0, marginRight: 10 }}
                  icon="plus"
                  size="large"
                ></Button>
              </Tooltip>
            )}
          </Row>
          <Radio.Group
            style={{ paddingBottom: 16 }}
            value={filter["status"] != undefined ? filter["status"] : "-3"}
            buttonStyle="solid"
            // size='large'
            onChange={this.onChangeStatus}
          >
            <Radio.Button value="-3">
              {formatMessage(messages.all)}
            </Radio.Button>
            <Radio.Button value="0">{formatMessage(messages.new)}</Radio.Button>
            <Radio.Button value="1">
              {formatMessage(messages.doing)}
            </Radio.Button>
            <Radio.Button value="2">
              {formatMessage(messages.done)}
            </Radio.Button>
            <Radio.Button value="-1">
              {formatMessage(messages.cancel)}
            </Radio.Button>
            <Radio.Button value="3">
              {formatMessage(messages.overdude)}
            </Radio.Button>
          </Radio.Group>
          <Row>
            <Table
              rowKey="id"
              loading={loading}
              // showHeader={false}
              columns={columns}
              dataSource={data}
              locale={{ emptyText: formatMessage(messages.noData) }}
              bordered
              pagination={{
                pageSize: 20,
                total: totalCount,
                current,
                showTotal: (total) =>
                  formatMessage(messages.total, { total }) +
                  " " +
                  (total > 1
                    ? formatMessage(messages.tasks)
                    : formatMessage(messages.task)),
              }}
              onChange={this.handleTableChange}
              scroll={{ x: 1366 }}
              onRow={(record) => {
                return {
                  onClick: (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.WORKFLOW_MANAGENMENT_DETAIL,
                    ]) &&
                      this.props.history.push(
                        `/main/task/detail/${record.id}`,
                        {
                          detail: record,
                        }
                      );
                  },
                };
              }}
            />
          </Row>
        </div>
      </Page>
    );
  }
}

TaskList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  taskList: makeSelectTaskList(),
  language: makeSelectLocale(),
  userDetail: selectUserDetail(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "taskList", reducer });
const withSaga = injectSaga({ key: "taskList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(TaskList));
