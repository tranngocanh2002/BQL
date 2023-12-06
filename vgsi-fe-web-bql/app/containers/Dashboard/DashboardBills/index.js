/**
 *
 * DashboardBills
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Col, DatePicker, Row, Select, Spin, Table } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import {
  defaultAction,
  fetchAllBills,
  fetchApartment,
  fetchBuildingAreaAction,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectDashboardBills from "./selectors";

import _ from "lodash";
import moment from "moment";
import queryString from "query-string";
import { withRouter } from "react-router-dom";
import { formatPrice } from "../../../utils";
import { injectIntl } from "react-intl";
import messages from "../messages";
import("./index.less");

const col6 = {
  md: 6,
  lg: 5,
  xl: 4,
  xxl: 3,
};

/* eslint-disable react/prefer-stateless-function */
export class DashboardBills extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      current: 1,
      filter: {},
    };
    this._onSearchApartment = _.debounce(this.onSearchApartment, 300);
    this._onSearchBuilding = _.debounce(this.onSearchBuilding, 300);
  }

  onSearchApartment = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };
  onSearchBuilding = (keyword) => {
    this.props.dispatch(fetchBuildingAreaAction({ name: keyword }));
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this._onSearchApartment("");
    this._onSearchBuilding("");
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

    if (!params.start_time) {
      params.start_time = moment().startOf("day").unix();
    }
    if (!params.end_time) {
      params.end_time = moment().endOf("day").unix();
    }

    this.setState({ current: params.page, filter: params }, () => {
      this.props.dispatch(fetchAllBills(params));
    });
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/finance/reception?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  render() {
    const { dashboardBills } = this.props;
    const { loading, items, apartments, buildingArea, total_count } =
      dashboardBills;
    const { filter, current } = this.state;
    let { formatMessage } = this.props.intl;

    let columns = [
      {
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
        title: (
          <span className="nameTable">
            {formatMessage(messages.dayVouchers)}
          </span>
        ),
        dataIndex: "payment_date",
        key: "payment_date",
        render: (text) => moment.unix(text).format("DD/MM/YYYY"),
      },
      {
        title: (
          <span className="nameTable">
            {formatMessage(messages.numberOfVouchers)}
          </span>
        ),
        dataIndex: "number",
        key: "number",
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.payments)}</span>
        ),
        dataIndex: "type_payment_name",
        key: "type_payment_name",
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.property)}</span>
        ),
        dataIndex: "apartment_name",
        key: "apartment_name",
        render: (text, record) => `${text} (${record.apartment_parent_path})`,
      },
      {
        title: (
          <span className="nameTable">
            {formatMessage(messages.customerName)}
          </span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        title: (
          <span className="nameTable">
            {formatMessage(messages.amountOfMoney)}
          </span>
        ),
        dataIndex: "total_price",
        key: "total_price",
        align: "right",
        render: (text) => <span>{`${formatPrice(text)} đ`}</span>,
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.submitter)}</span>
        ),
        dataIndex: "payer_name",
        key: "payer_name",
      },
      {
        title: (
          <span className="nameTable">
            {formatMessage(messages.implementDate)}
          </span>
        ),
        dataIndex: "execution_date",
        key: "execution_date",
        render: (text) => moment.unix(text).format("DD/MM/YYYY"),
      },
      // {
      //   title: <span >Thao tác</span>,
      //   dataIndex: "iddd8", key: "iddd8",
      //   align: 'center',
      //   render: (text, record, index) => <Row>
      //     <Tooltip title='Chi tiết' ><Link to={`/main/finance/reception/bill/${record.id}`} ><Icon type="container" /></Link></Tooltip>&ensp;|&ensp;<Tooltip title='Xoá' ><Icon type="delete" style={{ color: 'red', cursor: 'pointer' }} /></Tooltip>
      //   </Row>
      // },
    ];
    return (
      <>
        <Page inner style={{ minHeight: 10, marginBottom: 24 }}>
          <Row>
            <Col span={8}>
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16, color: "#909090" }}>
                  {formatMessage(messages.totalAmount).toUpperCase()}
                </span>
                <br />
                {!(loading || !total_count) && (
                  <span
                    style={{ fontSize: 24, fontWeight: "bold" }}
                  >{`${formatPrice(
                    _.sumBy(total_count, (rr) => rr.total_price)
                  )} Đ`}</span>
                )}
                {(loading || !total_count) && <Spin style={{ marginTop: 8 }} />}
              </Row>
            </Col>
            <Col
              span={8}
              style={{
                borderLeft: "1px solid rgba(210, 210, 210, 0.5)",
                borderRight: "1px solid rgba(210, 210, 210, 0.5)",
              }}
            >
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16, color: "#909090" }}>
                  {formatMessage(messages.cash).toUpperCase()}
                </span>
                <br />
                {!(loading || !total_count) && (
                  <span
                    style={{ fontSize: 24, fontWeight: "bold" }}
                  >{`${formatPrice(
                    (
                      total_count.find((rr) => rr.type_payment == 0) || {
                        total_price: 0,
                      }
                    ).total_price
                  )} Đ`}</span>
                )}
                {(loading || !total_count) && <Spin style={{ marginTop: 8 }} />}
              </Row>
            </Col>
            <Col span={8}>
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16, color: "#909090" }}>
                  {formatMessage(messages.transfer).toUpperCase()}
                </span>
                <br />
                {!(loading || !total_count) && (
                  <span
                    style={{ fontSize: 24, fontWeight: "bold" }}
                  >{`${formatPrice(
                    (
                      total_count.find((rr) => rr.type_payment == 1) || {
                        total_price: 0,
                      }
                    ).total_price
                  )} Đ`}</span>
                )}
                {(loading || !total_count) && <Spin style={{ marginTop: 8 }} />}
              </Row>
            </Col>
          </Row>
        </Page>
        <Page inner className="DashboardBills">
          <Row>
            <Col span={24} style={{ marginBottom: 24, zIndex: 99 }}>
              <Row gutter={[24, 16]}>
                <Col {...col6}>
                  <Select
                    style={{ width: "100%" }}
                    loading={apartments.loading}
                    showSearch
                    placeholder={formatMessage(messages.choseProperty)}
                    optionFilterProp="children"
                    notFoundContent={
                      apartments.loading ? <Spin size="small" /> : null
                    }
                    onSearch={this._onSearchApartment}
                    onChange={(value, opt) => {
                      this.setState({
                        filter: {
                          ...filter,
                          apartment_id: value,
                        },
                      });
                      if (!opt) {
                        this._onSearchApartment("");
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
                <Col {...col6}>
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
                          building_area_id: value,
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
                          key={`group-building-${gr.id}`}
                          value={`${gr.id}`}
                        >{`${gr.parent_path} / ${gr.name}`}</Select.Option>
                      );
                    })}
                  </Select>
                </Col>
                <Col {...col6}>
                  <Select
                    style={{ width: "100%" }}
                    placeholder={formatMessage(messages.payments)}
                    allowClear
                    value={filter.type_payment}
                    onChange={(e) => {
                      this.setState({
                        filter: {
                          ...filter,
                          type_payment: e,
                        },
                      });
                    }}
                  >
                    <Select.Option value={"0"}>
                      {formatMessage(messages.cash)}
                    </Select.Option>
                    <Select.Option value={"1"}>
                      {formatMessage(messages.transfer)}
                    </Select.Option>
                  </Select>
                </Col>
                <Col {...col6}>
                  <DatePicker
                    placeholder={formatMessage(messages.fromDate)}
                    format="DD/MM/YYYY"
                    style={{ width: "100%" }}
                    value={moment.unix(filter.start_time)}
                    onChange={(start_time) => {
                      if (start_time) {
                        this.setState({
                          filter: {
                            ...filter,
                            start_time: start_time.startOf("day").unix(),
                          },
                        });
                      }
                    }}
                  />
                </Col>
                <Col {...col6}>
                  <DatePicker
                    placeholder={formatMessage(messages.toDate)}
                    format="DD/MM/YYYY"
                    style={{ width: "100%" }}
                    value={moment.unix(filter.end_time)}
                    onChange={(end_time) => {
                      if (end_time) {
                        this.setState({
                          filter: {
                            ...filter,
                            end_time: end_time.endOf("day").unix(),
                          },
                        });
                      }
                    }}
                  />
                </Col>
                <Col {...col6}>
                  <Button
                    type="primary"
                    style={{
                      width: "85%",
                    }}
                    onClick={() => {
                      this.props.history.push(
                        `/main/finance/reception?${queryString.stringify({
                          ...this.state.filter,
                          page: 1,
                        })}`
                      );
                    }}
                  >
                    {formatMessage(messages.search)}
                  </Button>
                </Col>
                <Col {...col6}>
                  <Button
                    style={{
                      width: "85%",
                    }}
                    type="danger"
                    onClick={() => {
                      this.props.history.push(
                        `/main/finance/reception?${queryString.stringify({
                          page: 1,
                        })}`
                      );
                    }}
                  >
                    {formatMessage(messages.deleteSearch)}
                  </Button>
                </Col>
              </Row>
            </Col>
            <Col span={24}>
              <Table
                rowKey="id"
                loading={loading}
                dataSource={items}
                columns={columns}
                locale={{ emptyText: formatMessage(messages.emptyData) }}
                bordered
                scroll={{ x: 1000 }}
                onChange={this.handleTableChange}
                pagination={{
                  pageSize: 20,
                  total: dashboardBills.totalPage,
                  current: this.state.current,
                  showTotal: (total) =>
                    `${formatMessage(messages.total)} ${total}`,
                }}
                onRow={(record) => {
                  return {
                    onClick: () => {
                      this.props.history.push(
                        `/main/finance/reception/bill/${record.id}`
                      );
                    }, // click row
                  };
                }}
              />
            </Col>
          </Row>
        </Page>
      </>
    );
  }
}

DashboardBills.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  dashboardBills: makeSelectDashboardBills(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "dashboardBills", reducer });
const withSaga = injectSaga({ key: "dashboardBills", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(withRouter(DashboardBills)));
