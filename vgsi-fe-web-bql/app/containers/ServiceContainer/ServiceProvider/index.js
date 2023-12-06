/**
 *
 * ServiceProvider
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Avatar,
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
  deleteServiceProviderAction,
  fetchProvidersAction,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectServiceProvider from "./selectors";

import moment from "moment";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";
import messages from "./messages";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

/* eslint-disable react/prefer-stateless-function */
export class ServiceProvider extends React.PureComponent {
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

  reload = (search) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }

    params.keyword = params.keyword || "";

    this.setState(
      { current: params.page, keyword: params.keyword, filter: params },
      () => {
        this.props.dispatch(fetchProvidersAction(params));
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/service/providers?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onDelete = (record) => {
    console.log("_onDelete::record", record);
    Modal.confirm({
      autoFocusButton: null,
      title: "Bạn chắc chắn muốn xoá nhà cung cấp này?",
      okText: "Đồng ý",
      okType: "danger",
      cancelText: "Huỷ",
      onOk: () => {
        this.props.dispatch(
          deleteServiceProviderAction({
            id: record.id,
            callback: () => {
              this.reload(this.props.location.search);
            },
          })
        );
      },
      onCancel() {},
    });
  };

  render() {
    const { filter, current } = this.state;
    const { serviceProvider, auth_group, intl } = this.props;
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(0, serviceProvider.loading ? current - 2 : current - 1) *
              20 +
              index +
              1}
          </span>
        ),
      },
      {
        title: <span className={styles.nameTable}>Logo</span>,
        dataIndex: "medias_",
        key: "medias_",
        render: (text, record, index) => (
          <Avatar
            shape="square"
            size="large"
            icon="user"
            src={getFullLinkImage(
              record.medias ? record.medias.avatar : undefined
            )}
          />
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.supplierName} />
          </span>
        ),
        dataIndex: "name",
        key: "name",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.status} />
          </span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text, record) => (
          <span>
            {this.props.language === "vi"
              ? (
                  config.STATUS_SERVICE_PROVIDER.find(
                    (ss) => ss.id == record.status
                  ) || {}
                ).name
              : (
                  config.STATUS_SERVICE_PROVIDER.find(
                    (ss) => ss.id == record.status
                  ) || {}
                ).name_en}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.update} />
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
      {
        width: 120,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.action} />
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record, index) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title={<FormattedMessage {...messages.deleteSupplier} />}>
              <Row
                type="flex"
                align="middle"
                style={{ color: "#F15A29", cursor: "pointer" }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  this._onDelete(record);
                }}
              >
                <i
                  className="material-icons"
                  style={{ fontSize: 18, marginRight: 6 }}
                >
                  delete_outline
                </i>{" "}
                <FormattedMessage {...messages.delete} />
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    if (!auth_group.checkRole([config.ALL_ROLE_NAME.SERVICE_PROVIDER_DELETE])) {
      columns.splice(columns.length - 1, 1);
    }

    return (
      <Page inner>
        <div className={styles.serviceProviderPage}>
          <WithRole
            roles={[
              config.ALL_ROLE_NAME.SERVICE_PROVIDER_EDIT,
              config.ALL_ROLE_NAME.SERVICE_PROVIDER_DELETE,
            ]}
          >
            <Button
              type="primary"
              ghost
              onClick={() =>
                this.props.history.push("/main/service/providers/add")
              }
            >
              <FormattedMessage {...messages.add} />
            </Button>
          </WithRole>
          <Row style={{ marginTop: 16 }}>
            <Col span={8} style={{ paddingRight: 8 }}>
              <Input.Search
                value={filter["name"] || ""}
                placeholder={intl.formatMessage({ ...messages.contractor })}
                prefix={
                  <Tooltip
                    title={
                      <FormattedMessage {...messages.searchSupplierViaName} />
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
                      ["name"]: e.target.value,
                    },
                  });
                }}
                onSearch={(text) => {
                  this.props.history.push(
                    `/main/service/providers?${queryString.stringify({
                      ...this.state.filter,
                      page: 1,
                    })}`
                  );
                }}
              />
            </Col>
            <Col span={6} style={{ paddingRight: 8 }}>
              <Select
                showSearch
                style={{ width: "100%" }}
                placeholder={<FormattedMessage {...messages.status} />}
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
                      this.props.history.push(
                        `/main/service/providers?${queryString.stringify({
                          ...this.state.filter,
                          page: 1,
                        })}`
                      );
                    }
                  );
                }}
                allowClear
                value={filter["status"]}
              >
                {config.STATUS_SERVICE_PROVIDER.map((ss) => {
                  return (
                    <Select.Option key={`status-${ss.id}`} value={`${ss.id}`}>
                      {ss.name}
                    </Select.Option>
                  );
                })}
              </Select>
            </Col>
          </Row>

          <Row style={{ marginTop: 16 }}>
            <Table
              rowKey="id"
              loading={serviceProvider.loading || serviceProvider.deleting}
              columns={columns}
              dataSource={serviceProvider.data}
              locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
              pagination={{
                pageSize: 20,
                total: serviceProvider.totalPage,
                current: this.state.current,
                showTotal: (total, range) => (
                  <FormattedMessage
                    {...messages.totalSuppliers}
                    values={{ total }}
                  />
                ),
              }}
              onChange={this.handleTableChange}
              onRow={(record, rowIndex) => {
                return {
                  onClick: (event) => {
                    this.props.history.push(
                      `/main/service/providers/detail/${record.id}`,
                      {
                        record,
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

ServiceProvider.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  serviceProvider: makeSelectServiceProvider(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "serviceProvider", reducer });
const withSaga = injectSaga({ key: "serviceProvider", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ServiceProvider));
