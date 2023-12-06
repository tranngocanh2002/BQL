/**
 *
 * SupplierList
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
} from "antd";
import queryString from "query-string";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import {
  defaultAction,
  deleteSupplierAction,
  fetchAllSupplierAction,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectResidentList from "./selectors";

const confirm = Modal.confirm;

import WithRole from "components/WithRole";
import moment from "moment";
import { injectIntl } from "react-intl";
import { selectAuthGroup } from "redux/selectors";
import { config } from "utils";
import { globalStyles } from "../../../utils/constants";
import messages from "../messages";

const { Option } = Select;

const topCol3 = {
  md: 8,
  lg: 6,
  xl: 5,
  xxl: 4,
};

/* eslint-disable react/prefer-stateless-function */
export class SupplierList extends React.PureComponent {
  state = {
    current: 1,
    currentEdit: undefined,
    visible: false,
    filter: {},
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
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
    this.setState({ current: params.page, filter: reset ? {} : params }, () => {
      this.props.dispatch(fetchAllSupplierAction(reset ? { page: 1 } : params));
      reset && this.props.history.push(`${this.props.location.pathname}`);
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
          `/main/contractor/list?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  _onView = (record) => {
    this.props.history.push(`/main/contractor/detail/${record.id}`, { record });
  };

  _onAdd = () => {
    this.props.history.push("/main/contractor/add");
  };

  _onEdit = (record) => {
    this.props.history.push(`/main/contractor/edit/${record.id}`, { record });
  };

  _onDelete = (record) => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmDelete),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          deleteSupplierAction({
            id: record.id,
            callback: () => {
              this.reload(this.props.location.search);
            },
          })
        );
      },
      onCancel: () => {},
    });
  };

  render() {
    const { supplierList, auth_group } = this.props;

    const { loading, data, totalPage, deleting } = supplierList;

    const { current, filter } = this.state;

    const columns = [
      {
        width: 50,
        align: "center",
        fixed: "left",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => <span>{index + 1}</span>,
      },
      {
        width: 280,
        // fixed: "left",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.supplierName)}
          </span>
        ),
        dataIndex: "name",
        key: "name",
      },
      {
        width: 220,
        // fixed: "left",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.nameContact)}
          </span>
        ),
        dataIndex: "contact_name",
        key: "contact_name",
      },
      {
        width: 150,
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.phoneContact)}
          </span>
        ),
        dataIndex: "contact_phone",
        key: "contact_phone",
        render: (text) => (text ? `0${text.slice(-9)}` : ""),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.emailContact)}
          </span>
        ),
        dataIndex: "contact_email",
        key: "contact_email",
      },
      {
        width: 150,
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.createdAt)}
          </span>
        ),
        dataIndex: "created_at",
        key: "created_at",
        render: (text) => (text ? moment.unix(text).format("DD/MM/YYYY") : ""),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.status)}
          </span>
        ),
        dataIndex: "status",
        key: "status",
        width: 180,
        render: (text) => (
          <span>
            {text === 0
              ? this.props.intl.formatMessage(messages.inactive)
              : this.props.intl.formatMessage(messages.active)}
          </span>
        ),
      },
      {
        width: 200,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.option)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        fixed: "right",
        render: (text, record) => {
          return (
            <Row type="flex" align="middle" justify="center">
              <Tooltip title={this.props.intl.formatMessage(messages.detail)}>
                <Row
                  type="flex"
                  align="middle"
                  style={
                    !auth_group.checkRole([
                      config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_DETAIL,
                    ])
                      ? globalStyles.rowDisabled
                      : globalStyles.row
                  }
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_DETAIL,
                    ]) && this._onView(record);
                  }}
                >
                  <i
                    className="fa fa-eye"
                    style={
                      !auth_group.checkRole([
                        config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_DETAIL,
                      ])
                        ? globalStyles.iconDisabled
                        : globalStyles.icon
                    }
                  />
                </Row>
              </Tooltip>
              &ensp;&ensp; | &ensp;&ensp;
              <Tooltip title={this.props.intl.formatMessage(messages.edit)}>
                <Row
                  type="flex"
                  align="middle"
                  style={
                    !auth_group.checkRole([
                      config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_UPDATE,
                    ])
                      ? globalStyles.rowDisabled
                      : globalStyles.row
                  }
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_UPDATE,
                    ]) && this._onEdit(record);
                  }}
                >
                  <i
                    className="fa fa-edit"
                    style={
                      !auth_group.checkRole([
                        config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_UPDATE,
                      ])
                        ? globalStyles.iconDisabled
                        : globalStyles.icon
                    }
                  />
                </Row>
              </Tooltip>
              &ensp;&ensp; | &ensp;&ensp;
              <Tooltip title={this.props.intl.formatMessage(messages.delete)}>
                <Row
                  type="flex"
                  align="middle"
                  style={
                    !auth_group.checkRole([
                      config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_DELETE,
                    ])
                      ? globalStyles.rowDisabled
                      : globalStyles.row
                  }
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_DELETE,
                    ]) && this._onDelete(record);
                  }}
                >
                  <i
                    className="fa fa-trash"
                    style={
                      !auth_group.checkRole([
                        config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_DELETE,
                      ])
                        ? globalStyles.iconDisabled
                        : globalStyles.iconDelete
                    }
                  />
                </Row>
              </Tooltip>
            </Row>
          );
        },
      },
    ];

    const { name, contact_phone, ...rest } = filter;
    const newFilter = {
      ...rest,
      name: name ? name.trim() : name,
      contact_phone: contact_phone ? contact_phone.trim() : contact_phone,
    };

    return (
      <Page inner className={styles.contractorListPage}>
        <div>
          <Row style={{ paddingBottom: 16 }} gutter={16}>
            <Col {...topCol3}>
              <Input.Search
                value={filter["name"] || ""}
                placeholder={this.props.intl.formatMessage(messages.contractor)}
                prefix={
                  <Tooltip
                    title={this.props.intl.formatMessage(messages.supplierName)}
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
                      ["name"]: e.target.value,
                    },
                  });
                }}
                maxLength={255}
              ></Input.Search>
            </Col>
            <Col {...topCol3}>
              <Input.Search
                value={filter["contact_phone"] || ""}
                placeholder={this.props.intl.formatMessage(messages.phone)}
                prefix={
                  <Tooltip
                    title={this.props.intl.formatMessage(messages.phone)}
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
                      ["contact_phone"]: e.target.value,
                    },
                  });
                }}
                maxLength={10}
              ></Input.Search>
            </Col>
            <Col {...topCol3}>
              <Select
                style={{ width: "100%" }}
                placeholder={this.props.intl.formatMessage(messages.status)}
                onChange={(value) =>
                  this.setState({ filter: { ...filter, ["status"]: value } })
                }
                value={
                  filter["status"] === undefined
                    ? undefined
                    : filter["status"] === "0"
                    ? this.props.intl.formatMessage(messages.inactive)
                    : this.props.intl.formatMessage(messages.active)
                }
                allowClear
              >
                <Option value={"0"}>
                  {this.props.intl.formatMessage(messages.inactive)}
                </Option>
                <Option value={"1"}>
                  {this.props.intl.formatMessage(messages.active)}
                </Option>
              </Select>
            </Col>
            <Col {...topCol3}>
              <Button
                type="primary"
                onClick={() => {
                  this.props.history.push(
                    `/main/contractor/list?${queryString.stringify({
                      ...newFilter,
                      page: 1,
                    })}`
                  );
                }}
              >
                {this.props.intl.formatMessage(messages.search)}
              </Button>
            </Col>
          </Row>
          <Row style={{ marginBottom: 16 }}>
            <Tooltip title={this.props.intl.formatMessage(messages.refresh)}>
              <Button
                icon="reload"
                size="large"
                shape="circle"
                onClick={() => this.reload(this.props.location.search, true)}
              />
            </Tooltip>
            <WithRole
              roles={[config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_CREATE]}
            >
              <Tooltip
                title={this.props.intl.formatMessage(messages.addSupplier)}
              >
                <Button
                  style={{ marginLeft: 10 }}
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this._onAdd();
                  }}
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </WithRole>
          </Row>
          <Table
            rowKey="id"
            loading={loading || deleting}
            columns={columns}
            dataSource={data}
            bordered
            locale={{
              emptyText: this.props.intl.formatMessage(messages.noData),
            }}
            pagination={{
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total) =>
                this.props.intl.formatMessage(messages.totalSupplier, {
                  total,
                }),
            }}
            onRow={(record) => {
              return {
                onClick: (event) => {
                  event.preventDefault();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/contractor/detail/${record.id}`,
                      { record }
                    );
                },
              };
            }}
            onChange={this.handleTableChange}
            expandRowByClick
            scroll={{ x: 800 }}
          />
        </div>
      </Page>
    );
  }
}

SupplierList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  supplierList: makeSelectResidentList(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "supplierList", reducer });
const withSaga = injectSaga({ key: "supplierList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(SupplierList));
