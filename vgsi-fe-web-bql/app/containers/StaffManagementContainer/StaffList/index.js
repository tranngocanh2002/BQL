/**
 *
 * StaffList
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
  Row,
  Select,
  Table,
  Tooltip,
} from "antd";
import { getFullLinkImage } from "connection";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import queryString from "query-string";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import WithRole from "../../../components/WithRole";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";
import { GLOBAL_COLOR, globalStyles } from "../../../utils/constants";
import {
  changeStatusStaffAction,
  defaultAction,
  deleteStaffAction,
  fetchAllStaffAction,
  fetchGroupAuthAction,
} from "./actions";
import styles from "./index.less";
import messages from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectStaffList from "./selectors";

const confirm = Modal.confirm;
const { Option } = Select;

const topCol3 = {
  md: 8,
  lg: 6,
  xl: 4,
  xxl: 4,
};

/* eslint-disable react/prefer-stateless-function */
export class StaffList extends React.PureComponent {
  state = {
    current: 1,
    filter: {},
    downloading: false,
    exporting: false,
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this.props.dispatch(fetchGroupAuthAction());
    this.reload(this.props.location.search);
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
    if (
      this.props.staffList.importing != nextProps.staffList.importing &&
      !nextProps.staffList.importing
    ) {
      this.props.history.push(
        `/main/setting/building/staff/list?${queryString.stringify({
          ...this.state.filter,
          page: 1,
        })}`
      );
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
      this.props.dispatch(fetchAllStaffAction(reset ? {} : params));
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
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/setting/building/staff/list?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onImport = () => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmImport),
      okText: this.props.intl.formatMessage(messages.confirm),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        window.modalImport.show(
          (url) => {
            return window.connection.importUser({ file_path: url });
          },
          () => {
            this.reload(this.props.location.search);
          }
        );
      },
      onCancel() {},
    });
  };

  _addStaff = () => {
    this.props.history.push("/main/setting/building/staff/add");
  };

  render() {
    const { auth_group, location } = this.props;
    const formatMessage = this.props.intl.formatMessage;
    const { loading, data, totalPage, deleting, authGroup } =
      this.props.staffList;
    const { current, filter, exporting } = this.state;
    const { search } = location;
    let params = queryString.parse(search);
    const status = [
      { id: 0, value: 1, name: formatMessage(messages.active) },
      { id: 1, value: 0, name: formatMessage(messages.inactive) },
    ];
    const columns = [
      {
        title: <span className={styles.nameTable}># </span>,
        dataIndex: "index",
        key: "index",
        render: (text, record, index) => (
          <span>
            {Math.max(0, loading ? current - 2 : current - 1) * 20 + index + 1}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.employeeCode)}
          </span>
        ),
        dataIndex: "code_management_user",
        key: "code_management_user",
        //render: () => <span>{Math.floor(Math.random() * 100000) + 1}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.fullName)}
          </span>
        ),
        dataIndex: "first_name",
        key: "first_name",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },

      {
        title: <span className={styles.nameTable}>Email</span>,
        dataIndex: "email",
        key: "email",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.status)}
          </span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text, record) => (
          <span>
            {record.status === 1
              ? formatMessage(messages.active)
              : formatMessage(messages.inactive)}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.authGroup)}
          </span>
        ),
        dataIndex: "auth_group_name",
        key: "auth_group_name",
        render: (text, record) => (
          <span>
            {record.auth_group && this.props.language === "en"
              ? record.auth_group.name_en
              : record.auth_group.name}
          </span>
        ),
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record, index) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title={formatMessage(messages.view)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_DETAIL,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.stopPropagation();
                  e.preventDefault();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/setting/building/staff/detail/${record.id}`,
                      {
                        record,
                      }
                    );
                }}
              >
                <i
                  className="fa fa-eye"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_DETAIL,
                    ])
                      ? globalStyles.icon
                      : globalStyles.iconDisabled
                  }
                />
              </Row>
            </Tooltip>
            &ensp;&ensp;| &ensp;&ensp;
            <Tooltip title={formatMessage(messages.edit)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_UPDATE,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.stopPropagation();
                  e.preventDefault();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_UPDATE,
                  ]) &&
                    this.props.history.push(
                      `/main/setting/building/staff/edit/${record.id}`,
                      {
                        record,
                      }
                    );
                }}
              >
                <i
                  className="fa fa-edit"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_UPDATE,
                    ])
                      ? globalStyles.icon
                      : globalStyles.iconDisabled
                  }
                />
              </Row>
            </Tooltip>
            &ensp;&ensp;| &ensp;&ensp;
            <Tooltip title={formatMessage(messages.delete)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_DELETE,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.stopPropagation();
                  e.preventDefault();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_DELETE,
                  ]) &&
                    confirm({
                      autoFocusButton: null,
                      title: this.props.intl.formatMessage(
                        messages.deleteStaffConfirm
                      ),
                      okText: this.props.intl.formatMessage(messages.confirm),
                      okType: "danger",
                      cancelText: this.props.intl.formatMessage(
                        messages.cancel
                      ),
                      onOk: () => {
                        this.props.dispatch(
                          deleteStaffAction({
                            id: record.id,
                            callback: () => {
                              this.reload(this.props.location.search);
                            },
                          })
                        );
                      },
                      onCancel() {},
                    });
                }}
              >
                <i
                  className="fa fa-trash"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_DELETE,
                    ])
                      ? globalStyles.iconDelete
                      : globalStyles.iconDisabled
                  }
                />
              </Row>
            </Tooltip>
            &ensp;&ensp;| &ensp;&ensp;
            <Tooltip
              title={
                record.status === 1
                  ? formatMessage(messages.stopActivation)
                  : formatMessage(messages.activate)
              }
            >
              <Row
                type="flex"
                align="middle"
                style={{
                  color:
                    record.status === 1 &&
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME
                        .BUILDING_PERSONNEL_MANAGEMENT_CHANGE_STATUS,
                    ])
                      ? "#f1292a"
                      : GLOBAL_COLOR,
                  cursor: auth_group.checkRole([
                    config.ALL_ROLE_NAME
                      .BUILDING_PERSONNEL_MANAGEMENT_CHANGE_STATUS,
                  ])
                    ? "pointer"
                    : "not-allowed",
                }}
                onClick={(e) => {
                  e.stopPropagation();
                  e.preventDefault();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME
                      .BUILDING_PERSONNEL_MANAGEMENT_CHANGE_STATUS,
                  ])
                    ? record.status === 1
                      ? confirm({
                          autoFocusButton: null,
                          title: this.props.intl.formatMessage(
                            messages.confirmInactive
                          ),
                          okText: this.props.intl.formatMessage(
                            messages.confirm
                          ),
                          okType: "danger",
                          cancelText: this.props.intl.formatMessage(
                            messages.cancel
                          ),
                          onOk: () => {
                            this.props.dispatch(
                              changeStatusStaffAction({
                                management_user_id: record.id,
                                status: 0,
                                callback: () => {
                                  this.reload(this.props.location.search);
                                },
                              })
                            );
                          },
                          onCancel() {},
                        })
                      : confirm({
                          autoFocusButton: null,
                          title: this.props.intl.formatMessage(
                            messages.confirmActive
                          ),
                          okText: this.props.intl.formatMessage(
                            messages.confirm
                          ),
                          okType: "danger",
                          cancelText: this.props.intl.formatMessage(
                            messages.cancel
                          ),
                          onOk: () => {
                            this.props.dispatch(
                              changeStatusStaffAction({
                                management_user_id: record.id,
                                status: 1,
                                callback: () => {
                                  this.reload(this.props.location.search);
                                },
                              })
                            );
                          },
                          onCancel() {},
                        })
                    : null;
                }}
              >
                <i
                  className={record.status === 0 ? "fa fa-check" : "fa fa-ban"}
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME
                        .BUILDING_PERSONNEL_MANAGEMENT_CHANGE_STATUS,
                    ])
                      ? globalStyles.icon
                      : globalStyles.iconDisabled
                  }
                />
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    return (
      <Page inner className={styles.staffListPafe}>
        <div>
          <Row style={{ paddingBottom: 16 }} gutter={[16, 16]}>
            <Col {...topCol3}>
              <Input.Search
                value={filter["code_management_user"] || ""}
                placeholder={formatMessage(messages.employeeCode)}
                prefix={
                  <Tooltip title={formatMessage(messages.findByCode)}>
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
                      ["code_management_user"]: e.target.value,
                    },
                  });
                }}
                onSearch={(text) => {
                  // this.props.history.push(`/main/setting/building/staff/list?${queryString.stringify({
                  //   ...this.state.filter,
                  //   page: 1,
                  // })}`)
                }}
              />
            </Col>
            <Col {...topCol3}>
              <Input.Search
                value={filter["name"] || ""}
                placeholder={formatMessage(messages.fullName)}
                prefix={
                  <Tooltip title={formatMessage(messages.findByName)}>
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
                      ["name"]: e.target.value,
                    },
                  });
                }}
                onSearch={(text) => {
                  // this.props.history.push(`/main/setting/building/staff/list?${queryString.stringify({
                  //   ...this.state.filter,
                  //   page: 1,
                  // })}`)
                }}
              />
            </Col>
            <Col {...topCol3}>
              <Input.Search
                value={filter["email"] || ""}
                placeholder={"Email"}
                prefix={
                  <Tooltip title={formatMessage(messages.findByEmail)}>
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
                      ["email"]: e.target.value,
                    },
                  });
                }}
                onSearch={(text) => {
                  // this.props.history.push(`/main/setting/building/staff/list?${queryString.stringify({
                  //   ...this.state.filter,
                  //   page: 1,
                  // })}`)
                }}
              />
            </Col>
            <Col {...topCol3}>
              <Select
                showSearch
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.authGroup)}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
                onChange={(value) => {
                  this.setState(
                    {
                      filter: {
                        ...filter,
                        ["auth_group_id"]: value,
                      },
                    },
                    () => {
                      // this.props.history.push(`/main/setting/building/staff/list?${queryString.stringify({
                      //   ...this.state.filter,
                      //   page: 1,
                      // })}`)
                    }
                  );
                }}
                allowClear
                value={filter["auth_group_id"]}
              >
                {authGroup.lst.map((lll) => {
                  return (
                    <Option key={lll.code} value={`${lll.id}`}>
                      {this.props.language === "vi" ? lll.name : lll.name_en}
                    </Option>
                  );
                })}
              </Select>
            </Col>
            <Col {...topCol3}>
              <Select
                showSearch
                allowClear
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.status)}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
                onChange={(value) => {
                  this.setState(
                    {
                      filter: {
                        ...filter,
                        ["status"]: value,
                      },
                    },
                    () => {
                      // this.props.history.push(`/main/setting/building/staff/list?${queryString.stringify({
                      //   ...this.state.filter,
                      //   page: 1,
                      // })}`)
                    }
                  );
                }}
                // allowClear
                value={filter["status"]}
              >
                {status.map((lll) => {
                  return (
                    <Option key={lll.id} value={`${lll.value}`}>
                      {lll.name}
                    </Option>
                  );
                })}
              </Select>
            </Col>
            <Col {...topCol3}>
              <Button
                type="primary"
                onClick={() => {
                  this.props.history.push(
                    `/main/setting/building/staff/list?${queryString.stringify({
                      ...this.state.filter,
                      page: 1,
                    })}`
                  );
                }}
              >
                {formatMessage(messages.search)}
              </Button>
            </Col>
          </Row>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={formatMessage(messages.reloadPage)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={() => {
                  this.reload(this.props.location.search, true);
                  //this.props.history.push("/main/setting/building/staff/list");
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            <WithRole
              roles={[
                config.ALL_ROLE_NAME["BUILDING_PERSONNEL_MANAGEMENT_CREATE"],
              ]}
            >
              <Tooltip title={formatMessage(messages.addNewStaff)}>
                <Button
                  style={{ marginRight: 10 }}
                  onClick={this._addStaff}
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </WithRole>
            <WithRole
              roles={[
                config.ALL_ROLE_NAME["BUILDING_PERSONNEL_MANAGEMENT_IMPORT"],
              ]}
            >
              <Tooltip title={formatMessage(messages.import)}>
                <Button
                  style={{ marginRight: 10 }}
                  shape="circle"
                  size="large"
                  onClick={this._onImport}
                >
                  <i className="material-icons" style={{ fontSize: 14 }}>
                    cloud_upload
                  </i>
                </Button>
              </Tooltip>
            </WithRole>
            <WithRole
              roles={[
                config.ALL_ROLE_NAME["BUILDING_PERSONNEL_MANAGEMENT_EXPORT"],
              ]}
            >
              <Tooltip title={formatMessage(messages.export)}>
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => {
                    this.setState(
                      {
                        downloading: true,
                      },
                      () => {
                        window.connection
                          .downloadFileSample({})
                          .then((res) => {
                            if (this._unmounted) return;
                            this.setState(
                              {
                                downloading: false,
                              },
                              () => {
                                if (res.success) {
                                  window.open(
                                    getFullLinkImage(res.data.file_path)
                                  );
                                }
                              }
                            );
                          })
                          .catch(() => {
                            if (this._unmounted) return;
                            this.setState({
                              downloading: false,
                            });
                          });
                      }
                    );
                  }}
                  loading={this.state.downloading}
                  shape="circle"
                  size="large"
                >
                  {!this.state.downloading && (
                    <i className="material-icons" style={{ fontSize: 14 }}>
                      cloud_download
                    </i>
                  )}
                </Button>
              </Tooltip>
            </WithRole>
            <WithRole
              roles={[
                config.ALL_ROLE_NAME["BUILDING_PERSONNEL_MANAGEMENT_EXPORT"],
              ]}
            >
              <Tooltip title={formatMessage(messages.exportStaff)}>
                <Button
                  style={{ position: "absolute", right: 0 }}
                  onClick={() => {
                    this.setState(
                      {
                        exporting: true,
                      },
                      () => {
                        window.connection
                          .exportUser({ ...params })
                          .then((res) => {
                            if (this._unmounted) return;
                            this.setState(
                              {
                                exporting: false,
                              },
                              () => {
                                if (res.success) {
                                  window.open(
                                    getFullLinkImage(res.data.file_path)
                                  );
                                }
                              }
                            );
                          })
                          .catch(() => {
                            if (this._unmounted) return;
                            this.setState({
                              exporting: false,
                            });
                          });
                      }
                    );
                  }}
                  loading={exporting}
                  shape="circle"
                  size="large"
                >
                  {!exporting && (
                    <i
                      className="material-icons"
                      style={{
                        fontSize: 14,
                        display: "flex",
                        justifyContent: "center",
                        fontWeight: "bold",
                      }}
                    >
                      login
                    </i>
                  )}
                </Button>
              </Tooltip>
            </WithRole>
          </Row>
          <Table
            rowKey="id"
            loading={loading || deleting}
            // showHeader={false}
            columns={columns}
            dataSource={data}
            locale={{ emptyText: formatMessage(messages.noData) }}
            bordered
            pagination={{
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total) => (
                <FormattedMessage {...messages.total} values={{ total }} />
              ),
            }}
            onChange={this.handleTableChange}
            onRow={(record) => {
              return {
                onClick: () => {
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/setting/building/staff/detail/${record.id}`,
                      {
                        record,
                      }
                    );
                },
              };
            }}
          />
        </div>
      </Page>
    );
  }
}

StaffList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  staffList: makeSelectStaffList(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "staffList", reducer });
const withSaga = injectSaga({ key: "staffList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(StaffList));
