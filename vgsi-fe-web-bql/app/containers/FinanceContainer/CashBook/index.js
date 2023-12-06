/**
 *
 * CashBook
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import("./index.less");

import {
  Button,
  Col,
  DatePicker,
  Input,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import _ from "lodash";
import moment from "moment";
import queryString from "query-string";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page";
import { config, formatPrice } from "../../../utils";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectCashBook from "./selectors";
const { Option } = Select;

import WithRole from "components/WithRole";
import { injectIntl } from "react-intl";
import { getFullLinkImage } from "../../../connection";
import { selectAuthGroup } from "../../../redux/selectors";
import { GLOBAL_COLOR } from "../../../utils/constants";
import messages from "../messages";
import {
  defaultAction,
  fetchAllBill,
  fetchApartmentAction,
  fetchBuildingAreaAction,
} from "./actions";
const col6 = {
  md: 6,
  lg: 4,
  xl: 4,
  xxl: 3,
};

/* eslint-disable react/prefer-stateless-function */
export class CashBook extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      current: 1,
      filter: {},
      selected: [],
      exporting: false,
    };
    this._onSearchApartment = _.debounce(this.onSearchApartment, 300);
    this._onSearchBuilding = _.debounce(this.onSearchBuilding, 300);
  }

  onSearchApartment = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
  };
  onSearchBuilding = (keyword) => {
    this.props.dispatch(fetchBuildingAreaAction({ name: keyword }));
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this._onSearchApartment("");
    this._onSearchBuilding("");
    this.reload(this.props.location.search, true);
    this.props.history.push(
      `/main/finance/cashbook?${queryString.stringify({
        type: 0,
        page: 1,
      })}`
    );
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
  }

  reload = (search, init = false) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }

    if (!params.start_time && init) {
      params.start_time = moment().startOf("month").unix();
    }

    if (!params.end_time && init) {
      params.end_time = moment().endOf("month").unix();
    }

    this.setState(
      {
        current: params.page,
        keyword: params.keyword,
        selected: [],
        filter: params,
      },
      () => {
        this.props.dispatch(fetchAllBill(params));
      }
    );
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
          `/main/finance/cashbook?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  render() {
    const { CashBook, auth_group, location } = this.props;
    const { loading, items, apartments, buildingArea, total_count } = CashBook;
    const { current, filter, exporting } = this.state;
    let { formatMessage } = this.props.intl;
    const { search } = location;
    let params = queryString.parse(search);

    const columns = [
      {
        width: 50,
        fixed: "left",
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
        width: 130,
        fixed: "left",
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
        width: 130,
        fixed: "left",
        title: (
          <span className="nameTable">
            {formatMessage(messages.numberOfVouchers)}
          </span>
        ),
        dataIndex: "number",
        key: "number",
        render: (text) => <span>{text}</span>,
      },
      {
        // width: 100,
        title: (
          <span className="nameTable">{formatMessage(messages.payments)}</span>
        ),
        dataIndex: "type_payment",
        key: "type_payment",
        render: (text) => (
          <span>
            {text === 0
              ? formatMessage(messages.cash)
              : text === 1
              ? formatMessage(messages.transfer)
              : formatMessage(messages.vnpay)}
          </span>
        ),
      },
      {
        // width: 120,
        title: (
          <span className="nameTable">{formatMessage(messages.loaiPhieu)}</span>
        ),
        dataIndex: "type",
        key: "type",
        render: (text) => (
          <span>
            {text === 0
              ? formatMessage(messages.receipts)
              : formatMessage(messages.phieuChi)}
          </span>
        ),
      },
      {
        // width: 100,
        title: (
          <span className="nameTable">{formatMessage(messages.property)}</span>
        ),
        dataIndex: "apartment_name",
        key: "apartment_name",
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
          <span className="nameTable">
            {this.state.filter.type && this.state.filter.type == 0
              ? formatMessage(messages.payer2)
              : formatMessage(messages.receiver)}
          </span>
        ),
        dataIndex: "payer_name",
        key: "payer_name",
      },
      {
        title: (
          <span className="nameTable">
            {this.state.filter.type && this.state.filter.type == 0
              ? formatMessage(messages.collector)
              : formatMessage(messages.payer2)}
          </span>
        ),
        dataIndex: "management_user_name",
        key: "management_user_name",
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
      //   // width: 80,
      //   fixed: "right",
      //   title: <span>Ngày thực hiện</span>,
      //   dataIndex: "execution_date",
      //   key: "execution_date",
      //   render: (text) => moment.unix(text).format("DD/MM/YYYY"),
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
                    _.sumBy(
                      total_count.filter(
                        (rr) => rr.type_payment == 1 || rr.type_payment == 4
                      ),
                      "total_price"
                    )
                  )} Đ`}</span>
                )}
                {(loading || !total_count) && <Spin style={{ marginTop: 8 }} />}
              </Row>
            </Col>
          </Row>
        </Page>
        <Page className="CashBookPage" inner>
          <div>
            <Row gutter={[8, 8]} style={{ paddingBottom: 16, marginTop: 24 }}>
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
                        ["apartment_id"]: value,
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
                  showSearch
                  style={{ width: "100%" }}
                  placeholder={formatMessage(messages.payments)}
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
                        ["type_payment"]: value,
                      },
                    });
                  }}
                  allowClear
                  value={filter["type_payment"]}
                >
                  <Option value="0">{formatMessage(messages.cash)}</Option>
                  <Option value="1">{formatMessage(messages.transfer)}</Option>
                  {/* {filter["type"] === "0" ? (
                    <Option value="4">{formatMessage(messages.vnpay)}</Option>
                  ) : null} */}
                </Select>
              </Col>
              <Col {...col6}>
                <Select
                  showSearch
                  style={{ width: "100%" }}
                  placeholder={formatMessage(messages.loaiPhieu)}
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
                        ["type"]: value,
                      },
                    });
                  }}
                  allowClear
                  value={filter["type"]}
                >
                  <Option value="0">{formatMessage(messages.receipts)}</Option>
                  <Option value="1">{formatMessage(messages.phieuChi)}</Option>
                </Select>
              </Col>
              <Col {...col6}>
                <Input
                  allowClear
                  value={filter["management_user_name"] || ""}
                  placeholder={
                    this.state.filter.type && this.state.filter.type == 0
                      ? formatMessage(messages.collector)
                      : formatMessage(messages.payer2)
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
              <Col md={4} xxl={2}>
                <Button
                  type="primary"
                  onClick={() => {
                    this.props.history.push(
                      `/main/finance/cashbook?${queryString.stringify({
                        ...this.state.filter,
                        page: 1,
                      })}`
                    );
                  }}
                >
                  {formatMessage(messages.search)}
                </Button>
              </Col>
              <Col md={24} xxl={7}>
                <Row type="flex" align="middle" gutter={[8, 8]}>
                  <Col
                    style={{
                      display: "flex",
                      alignItems: "center",
                      justifyContent: "space-between",
                    }}
                    xxl={8}
                    md={6}
                    lg={4}
                    xl={4}
                  >
                    <span
                      style={{
                        cursor: "pointer",
                        color: GLOBAL_COLOR,
                      }}
                      onClick={() => {
                        this.setState(
                          {
                            filter: {
                              ...filter,
                              start_time: moment().startOf("week").unix(),
                              end_time: moment().endOf("week").unix(),
                            },
                          },
                          () => {
                            this.props.history.push(
                              `/main/finance/cashbook?${queryString.stringify({
                                ...this.state.filter,
                                page: 1,
                              })}`
                            );
                          }
                        );
                      }}
                    >
                      {formatMessage(messages.week)}
                    </span>
                    <span
                      style={{
                        cursor: "pointer",
                        color: GLOBAL_COLOR,
                      }}
                      onClick={() => {
                        this.setState(
                          {
                            filter: {
                              ...filter,
                              start_time: moment().startOf("month").unix(),
                              end_time: moment().endOf("month").unix(),
                            },
                          },
                          () => {
                            this.props.history.push(
                              `/main/finance/cashbook?${queryString.stringify({
                                ...this.state.filter,
                                page: 1,
                              })}`
                            );
                          }
                        );
                      }}
                    >
                      {formatMessage(messages.month)}
                    </span>
                    <span
                      style={{ cursor: "pointer", color: GLOBAL_COLOR }}
                      onClick={() => {
                        this.setState(
                          {
                            filter: {
                              ...filter,
                              start_time: moment().startOf("year").unix(),
                              end_time: moment().endOf("year").unix(),
                            },
                          },
                          () => {
                            this.props.history.push(
                              `/main/finance/cashbook?${queryString.stringify({
                                ...this.state.filter,
                                page: 1,
                              })}`
                            );
                          }
                        );
                      }}
                    >
                      {formatMessage(messages.year)}
                    </span>
                  </Col>
                  <Col
                    md={{
                      span: 5,
                      offset: 1,
                    }}
                    xxl={{
                      span: 8,
                      offset: 0,
                    }}
                  >
                    <DatePicker
                      placeholder={formatMessage(messages.fromDate)}
                      style={{ width: "100%" }}
                      format="DD/MM/YYYY"
                      value={
                        filter.start_time
                          ? moment.unix(filter.start_time)
                          : undefined
                      }
                      onChange={(start_time) =>
                        this.setState(
                          {
                            filter: {
                              ...filter,
                              start_time: start_time
                                ? start_time.startOf("day").unix()
                                : undefined,
                            },
                          },
                          () => {
                            this.props.history.push(
                              `/main/finance/cashbook?${queryString.stringify({
                                ...this.state.filter,
                                page: 1,
                              })}`
                            );
                          }
                        )
                      }
                    />
                  </Col>
                  <Col
                    md={{
                      span: 5,
                      offset: 1,
                    }}
                    xxl={{
                      span: 8,
                      offset: 0,
                    }}
                  >
                    <DatePicker
                      placeholder={formatMessage(messages.toDate)}
                      style={{ width: "100%" }}
                      format="DD/MM/YYYY"
                      value={
                        filter.end_time
                          ? moment.unix(filter.end_time)
                          : undefined
                      }
                      onChange={(end_time) =>
                        this.setState(
                          {
                            filter: {
                              ...filter,
                              end_time: end_time
                                ? end_time.endOf("day").unix()
                                : undefined,
                            },
                          },
                          () => {
                            this.props.history.push(
                              `/main/finance/cashbook?${queryString.stringify({
                                ...this.state.filter,
                                page: 1,
                              })}`
                            );
                          }
                        )
                      }
                    />
                  </Col>
                </Row>
              </Col>
            </Row>
            <Row
              style={{
                paddingBottom: 16,
                marginTop: 8,
                display: "flex",
                alignItems: "center",
              }}
            >
              <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_CASH_BOOK]}>
                <Tooltip title={formatMessage(messages.exportVData4)}>
                  <Button
                    style={{ position: "absolute", right: 0 }}
                    onClick={() => {
                      this.setState(
                        {
                          exporting: true,
                        },
                        () => {
                          window.connection
                            .exportFinanceBillData({
                              ...params,
                              status: 10,
                              type: params.type ? params.type : 0,
                            })
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
              </WithRole>
            </Row>
            <Row gutter={24} style={{ marginTop: 16 }}>
              <Col>
                <Table
                  rowKey="id"
                  loading={loading}
                  columns={columns}
                  dataSource={items}
                  locale={{ emptyText: formatMessage(messages.emptyData) }}
                  bordered
                  pagination={{
                    pageSize: 20,
                    total: CashBook.totalPage,
                    current: this.state.current,
                    showTotal: (total) =>
                      `${formatMessage(
                        messages.total
                      )} ${total} ${formatMessage(
                        messages.bill
                      ).toLowerCase()}`,
                  }}
                  scroll={{ x: 1366 }}
                  onChange={this.handleTableChange}
                  onRow={(record) => {
                    return {
                      onClick: () => {
                        if (
                          record.type == 0 &&
                          (auth_group.checkRole([
                            config.ALL_ROLE_NAME.FINANCE_CREATE_BILL,
                          ]) ||
                            auth_group.checkRole([
                              config.ALL_ROLE_NAME.FINANCE_VIEW_BILL,
                            ]) ||
                            auth_group.checkRole([
                              config.ALL_ROLE_NAME.FINANCE_MANAGERMENT_BILL,
                            ]))
                        ) {
                          this.props.history.push(
                            `/main/finance/bills/detail/${record.id}`,
                            {
                              record,
                            }
                          );
                        } else if (
                          auth_group.checkRole([
                            config.ALL_ROLE_NAME.FINANCE_INVOICE_BILL,
                          ]) ||
                          auth_group.checkRole([
                            config.ALL_ROLE_NAME.MANAGE_INVOICE_BILL,
                          ])
                        ) {
                          this.props.history.push(
                            `/main/finance/invoice-bills/detail/${record.id}`,
                            {
                              record,
                            }
                          );
                        }
                      },
                    };
                  }}
                />
              </Col>
            </Row>
          </div>
        </Page>
      </>
    );
  }
}

CashBook.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  CashBook: makeSelectCashBook(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "CashBook", reducer });
const withSaga = injectSaga({ key: "CashBook", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(CashBook));
