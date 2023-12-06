/**
 *
 * DashboardDebtAll
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectDashboardDebtAll from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import queryString from "query-string";
import {
  fetchApartmentAction,
  fetchBuildingAreaAction,
  defaultAction,
  fetchDebt,
} from "./actions";
import Page from "../../../components/Page/Page";
import {
  Row,
  Col,
  Select,
  Button,
  Spin,
  DatePicker,
  Table,
  Icon,
  Tooltip,
} from "antd";

import moment from "moment";
import { formatPrice, config } from "../../../utils";
import { Link } from "react-router-dom";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import { selectAuthGroup } from "../../../redux/selectors";
import { injectIntl } from "react-intl";
import messages from "../messages";
import("./index.less");
import _ from "lodash";

/* eslint-disable react/prefer-stateless-function */
export class DashboardDebtAll extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {},
      collapse: false,
      currentEdit: undefined,
      visible: false,
      exporting: false,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
    this._onSearchBuilding = _.debounce(this.onSearchBuilding, 300);
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this.reload(this.props.location.search);
    this._onSearch("");
    this._onSearchBuilding("");
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
  }

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/finance/debt-all?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  reload = (search) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }

    params.keyword = params.keyword || "";
    if (!params.month) {
      params.month = moment().startOf("month").unix();
    }

    this.setState(
      {
        current: params.page,
        keyword: params.keyword,
        filter: params,
      },
      () => {
        this.props.dispatch(fetchDebt(params));
      }
    );
  };

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
  };
  onSearchBuilding = (keyword) => {
    this.props.dispatch(fetchBuildingAreaAction({ name: keyword }));
  };
  render() {
    const { dashboardDebtAll, dispatch, auth_group, location } = this.props;
    const { loading, items, apartments, buildingArea, total_count } =
      dashboardDebtAll;
    const { filter, current, exporting } = this.state;
    let { formatMessage } = this.props.intl;
    const { search } = location;
    let params = queryString.parse(search);

    const columns = [
      {
        fixed: "left",
        title: <span className="nameTable">#</span>,
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
        width: 100,
        title: (
          <span className="nameTable">{formatMessage(messages.property)}</span>
        ),
        dataIndex: "apartment_name",
        key: "apartment_name",
      },
      {
        width: 200,
        fixed: "left",
        title: (
          <span className="nameTable">{formatMessage(messages.address)}</span>
        ),
        dataIndex: "apartment_parent_path",
        key: "apartment_parent_path",
      },
      {
        width: 150,
        fixed: "left",
        title: (
          <span className="nameTable">{formatMessage(messages.owner)}</span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        title: (
          <span className="nameTable" style={{ whiteSpace: "pre-wrap" }}>
            {formatMessage(messages.openingDebit)}
          </span>
        ),
        dataIndex: "early_debt",
        key: "early_debt",
        align: "left",
        render: (text) => (
          <span>
            {formatPrice(text)} {formatMessage(messages.vnd2)}
          </span>
        ),
      },
      {
        title: (
          <span className="nameTable" style={{ whiteSpace: "pre-wrap" }}>
            {`${formatMessage(messages.arise)}\n${formatMessage(
              messages.receivables
            ).toLowerCase()}`}
          </span>
        ),
        dataIndex: "receivables",
        key: "receivables",
        align: "left",
        render: (text) => (
          <span>
            {formatPrice(text)} {formatMessage(messages.vnd2)}
          </span>
        ),
      },
      {
        title: (
          <span className="nameTable" style={{ whiteSpace: "pre-wrap" }}>
            {`${formatMessage(messages.arise)}\n${formatMessage(
              messages.collected
            ).toLowerCase()}`}
          </span>
        ),
        dataIndex: "collected",
        key: "collected",
        align: "left",
        render: (text) => (
          <span>
            {formatPrice(text)} {formatMessage(messages.vnd2)}
          </span>
        ),
      },
      {
        title: (
          <span className="nameTable">
            {formatMessage(messages.closingDebit)}
          </span>
        ),
        dataIndex: "end_debt",
        key: "end_debt",
        align: "left",
        render: (text) => (
          <span>
            {formatPrice(text)} {formatMessage(messages.vnd2)}
          </span>
        ),
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.status2)}</span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text, record) => (
          <span style={{ color: record.status_color }}>
            {text === 0
              ? formatMessage(messages.noDebt)
              : text === -1
              ? formatMessage(messages.prepay)
              : text === 1
              ? formatMessage(messages.stillOwe)
              : text === 2
              ? formatMessage(messages.feeNotice)
              : text === 3
              ? formatMessage(messages.debtReminder1)
              : text === 4
              ? formatMessage(messages.debtReminder2)
              : formatMessage(messages.debtReminder3)}
          </span>
        ),
      },
      {
        fixed: "right",
        width: 100,
        align: "center",
        title: (
          <span className="nameTable">{formatMessage(messages.action)}</span>
        ),
        dataIndex: "action",
        key: "action",
        render: (text, record) => {
          return (
            <>
              {record.status !== 0 && record.status !== -1 && (
                <Tooltip title={formatMessage(messages.pay)}>
                  <Link
                    to={"/main/finance/reception"}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      this.props.history.push("/main/finance/reception", {
                        apartment_id: record.apartment_id,
                      });
                    }}
                  >
                    <Icon type="file-done" />
                  </Link>
                </Tooltip>
              )}
            </>
          );
        },
      },
    ];
    if (!auth_group.checkRole([config.ALL_ROLE_NAME.FINANCE_CREATE_BILL])) {
      columns.splice(columns.length - 1, 1);
    }

    return (
      <div className="dashboardDeptList">
        <Page inner style={{ minHeight: 10, marginBottom: 24 }}>
          <Row style={{ display: "flex", alignItems: "stretch" }}>
            <Col span={6}>
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 20, color: "#909090" }}>
                  {formatMessage(messages.openingDebit)}
                </span>
                <br />
                {!(loading || !total_count) && (
                  <span
                    style={{ fontSize: 24, fontWeight: "bold", color: "black" }}
                  >{`${formatPrice(total_count.early_debt)} Đ`}</span>
                )}
                {(loading || !total_count) && <Spin style={{ marginTop: 8 }} />}
              </Row>
            </Col>
            <Col
              span={12}
              style={{
                borderLeft: "1px solid rgba(210, 210, 210, 0.5)",
                borderRight: "1px solid rgba(210, 210, 210, 0.5)",
              }}
            >
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 20, color: "#909090" }}>
                  {formatMessage(messages.arise)}
                </span>
                <br />
                <Col>
                  <Row>
                    <Col span={12}>
                      {!(loading || !total_count) && (
                        <span
                          style={{
                            fontSize: 24,
                            fontWeight: "bold",
                            color: "#D85357",
                          }}
                        >{`${formatPrice(
                          total_count.receivables
                        )} ${formatMessage(messages.vnd2)}`}</span>
                      )}
                      {(loading || !total_count) && (
                        <Spin style={{ marginTop: 8 }} />
                      )}
                      <br />
                      <span style={{ fontSize: 14, color: "#909090" }}>
                        {formatMessage(messages.receivables)}
                      </span>
                    </Col>
                    <Col span={12}>
                      {!(loading || !total_count) && (
                        <span
                          style={{
                            fontSize: 24,
                            fontWeight: "bold",
                            color: "#3EA671",
                          }}
                        >{`${formatPrice(
                          total_count.collected
                        )} ${formatMessage(messages.vnd2)}`}</span>
                      )}
                      {(loading || !total_count) && (
                        <Spin style={{ marginTop: 8 }} />
                      )}
                      <br />
                      <span style={{ fontSize: 14, color: "#909090" }}>
                        {formatMessage(messages.collected)}
                      </span>
                    </Col>
                  </Row>
                </Col>
              </Row>
            </Col>
            <Col span={6}>
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 20, color: "#909090" }}>
                  {formatMessage(messages.closingDebit)}
                </span>
                <br />
                {!(loading || !total_count) && (
                  <span
                    style={{ fontSize: 24, fontWeight: "bold", color: "black" }}
                  >{`${formatPrice(total_count.end_debt)} Đ`}</span>
                )}
                {(loading || !total_count) && <Spin style={{ marginTop: 8 }} />}
              </Row>
            </Col>
          </Row>
        </Page>
        <Page className="feeListPage" inner>
          <div>
            <Row gutter={24} style={{ paddingBottom: 24 }}>
              <Col sm={4} style={{ paddingRight: 0 }}>
                <DatePicker.MonthPicker
                  allowClear={false}
                  placeholder={formatMessage(messages.choseMonth)}
                  style={{ width: "100%" }}
                  format="MM/YYYY"
                  value={moment.unix(filter["month"])}
                  onChange={(month) => {
                    if (month) {
                      this.setState({
                        filter: {
                          ...filter,
                          ["month"]: month.startOf("month").unix(),
                        },
                      });
                    }
                  }}
                />
              </Col>
              <Col xxl={4} sm={5} style={{ paddingRight: 0 }}>
                <Select
                  style={{ width: "100%" }}
                  loading={apartments.loading}
                  showSearch
                  placeholder={formatMessage(messages.choseProperty)}
                  optionFilterProp="children"
                  notFoundContent={
                    apartments.loading ? <Spin size="small" /> : null
                  }
                  onSearch={this._onSearch}
                  onChange={(value, opt) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["apartment_id"]: value,
                      },
                    });
                    if (!opt) {
                      this._onSearch("");
                    }
                  }}
                  allowClear
                  value={filter["apartment_id"]}
                >
                  {apartments.lst.map((gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${gr.name} (${gr.parent_path})`}</Select.Option>
                    );
                  })}
                </Select>
              </Col>
              <Col xxl={4} sm={5} style={{ paddingRight: 0 }}>
                <Select
                  style={{ width: "100%" }}
                  loading={buildingArea.loading}
                  showSearch
                  placeholder={formatMessage(messages.choseAddress)}
                  optionFilterProp="children"
                  notFoundContent={
                    buildingArea.loading ? <Spin size="small" /> : null
                  }
                  onSearch={this._onSearchBuilding}
                  onChange={(value, opt) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["building_area_id"]: value,
                      },
                    });
                    if (!opt) {
                      this._onSearchBuilding("");
                    }
                  }}
                  allowClear
                  value={filter["building_area_id"]}
                >
                  {buildingArea.lst.map((gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${gr.parent_path} / ${gr.name}`}</Select.Option>
                    );
                  })}
                </Select>
              </Col>
              <Col xxl={4} sm={5} style={{ paddingRight: 0 }}>
                <Select
                  showSearch
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
                        // this.props.history.push(`/main/finance/debt-all?${queryString.stringify({
                        //   ...this.state.filter,
                        //   page: 1,
                        // })}`)
                      }
                    );
                  }}
                  allowClear
                  value={filter["status"]}
                >
                  <Select.Option value="-1">
                    {formatMessage(messages.prepay)}
                  </Select.Option>
                  <Select.Option value="0">
                    {formatMessage(messages.noDebt)}
                  </Select.Option>
                  <Select.Option value="1">
                    {formatMessage(messages.stillOwe)}
                  </Select.Option>
                  <Select.Option value="2">
                    {formatMessage(messages.feeNotice)}
                  </Select.Option>
                  <Select.Option value="3">
                    {formatMessage(messages.debtReminder1)}
                  </Select.Option>
                  <Select.Option value="4">
                    {formatMessage(messages.debtReminder2)}
                  </Select.Option>
                  <Select.Option value="5">
                    {formatMessage(messages.debtReminder3)}
                  </Select.Option>
                </Select>
              </Col>
              <Col span={3}>
                <Button
                  type="primary"
                  onClick={(e) => {
                    this.props.history.push(
                      `/main/finance/debt-all?${queryString.stringify({
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
            <WithRole
              roles={[config.ALL_ROLE_NAME.FINANCE_NOTIFICATION_FEE_MANAGER]}
            >
              <Row style={{ marginBottom: 24 }}>
                <Button
                  style={{ marginRight: 8 }}
                  onClick={(e) => {
                    this.props.history.push(
                      "/main/finance/notification-fee/add?type=5"
                    );
                  }}
                >
                  {formatMessage(messages.feeNotice)}
                </Button>
                <Button
                  style={{ marginRight: 8 }}
                  onClick={(e) => {
                    this.props.history.push(
                      "/main/finance/notification-fee/add?type=1"
                    );
                  }}
                >
                  {formatMessage(messages.debtReminder1)}
                </Button>
                <Button
                  style={{ marginRight: 8 }}
                  onClick={(e) => {
                    this.props.history.push(
                      "/main/finance/notification-fee/add?type=2"
                    );
                  }}
                >
                  {formatMessage(messages.debtReminder2)}
                </Button>
                <Button
                  onClick={(e) => {
                    this.props.history.push(
                      "/main/finance/notification-fee/add?type=3"
                    );
                  }}
                >
                  {formatMessage(messages.debtReminder3)}
                </Button>
              </Row>
            </WithRole>
            <Row type="flex" align="middle" style={{ marginBottom: 24 }}>
              <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                <div
                  style={{
                    width: 20,
                    height: 20,
                    background: "#159C1F",
                    marginRight: 10,
                  }}
                />
                {formatMessage(messages.noDebt)}
              </Row>
              <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                <div
                  style={{
                    width: 20,
                    height: 20,
                    background: "#FF9900",
                    marginRight: 10,
                  }}
                />
                {formatMessage(messages.stillOwe)}
              </Row>
              <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                <div
                  style={{
                    width: 20,
                    height: 20,
                    background: "#050ED3",
                    marginRight: 10,
                  }}
                />
                {formatMessage(messages.prepay)}
              </Row>
              <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                <div
                  style={{
                    width: 20,
                    height: 20,
                    background: "#FF3333",
                    marginRight: 10,
                  }}
                />
                {formatMessage(messages.feeNotice)}
              </Row>
              <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                <div
                  style={{
                    width: 20,
                    height: 20,
                    background: "#BC0409",
                    marginRight: 10,
                  }}
                />
                {formatMessage(messages.debtReminder1)}
              </Row>
              <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                <div
                  style={{
                    width: 20,
                    height: 20,
                    background: "#97040B",
                    marginRight: 10,
                  }}
                />
                {formatMessage(messages.debtReminder2)}
              </Row>
              <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                <div
                  style={{
                    width: 20,
                    height: 20,
                    background: "#650205",
                    marginRight: 10,
                  }}
                />
                {formatMessage(messages.debtReminder3)}
              </Row>
              <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_DEBT]}>
                <Row
                  type="flex"
                  align="middle"
                  style={{ marginRight: 24, position: "absolute", right: 0 }}
                >
                  <Tooltip title="Export dữ liệu công nợ">
                    <Button
                      onClick={() => {
                        this.setState(
                          {
                            exporting: true,
                          },
                          () => {
                            window.connection
                              .exportFinanceDebtData({ ...params })
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
                              .catch((e) => {
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
                            fontSize: 18,
                            display: "flex",
                            justifyContent: "center",
                          }}
                        >
                          login
                        </i>
                      )}
                    </Button>
                  </Tooltip>
                </Row>
              </WithRole>
            </Row>
            <Row gutter={24}>
              <Col>
                <Table
                  rowKey="id"
                  loading={loading}
                  columns={columns}
                  dataSource={items}
                  locale={{ emptyText: formatMessage(messages.emptyData) }}
                  onChange={this.handleTableChange}
                  scroll={{ x: 1366 }}
                  bordered
                  pagination={{
                    pageSize: 20,
                    total: dashboardDebtAll.totalPage,
                    current: this.state.current,
                    showTotal: (total) =>
                      `${formatMessage(
                        messages.total
                      )} ${total} ${formatMessage(
                        messages.bill
                      ).toLowerCase()}`,
                  }}
                />
              </Col>
            </Row>
          </div>
        </Page>
        {/* <div style={{ position: "absolute", right: 0, top: 160, zIndex: 9999 }}>
          <Button
            icon="unordered-list"
            size="large"
            style={{
              width: 50,
              borderBottomLeftRadius: 0,
              borderBottomRightRadius: 0,
            }}
            type={
              this.props.location.pathname == "/main/finance/debt-all"
                ? "primary"
                : "default"
            }
            onClick={() => this.props.history.push("/main/finance/debt-all")}
          />
          <br />
          <Button
            icon="appstore"
            size="large"
            style={{
              width: 50,
              borderTopLeftRadius: 0,
              borderTopRightRadius: 0,
            }}
            type={
              this.props.location.pathname == "/main/finance/debt"
                ? "primary"
                : "default"
            }
            onClick={() => this.props.history.push("/main/finance/debt")}
          />
        </div> */}
      </div>
    );
  }
}

DashboardDebtAll.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  dashboardDebtAll: makeSelectDashboardDebtAll(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "dashboardDebtAll", reducer });
const withSaga = injectSaga({ key: "dashboardDebtAll", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(DashboardDebtAll));
