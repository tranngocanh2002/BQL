/**
 *
 * CancelInvoiceBillList
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import("./index.less");

import {
  Button,
  Col,
  Row,
  Table,
  Input,
  Select,
  Spin,
  DatePicker,
  Menu,
  Tooltip,
} from "antd";
const { Option } = Select;
import Page from "../../../components/Page";
import { config, formatPrice } from "../../../utils";
import moment from "moment";
import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectCancelInvoiceBillList from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import queryString from "query-string";
import _ from "lodash";

import {
  defaultAction,
  fetchAllBill,
  fetchApartmentAction,
  fetchBuildingAreaAction,
} from "./actions";
import { GLOBAL_COLOR } from "../../../utils/constants";
import { getFullLinkImage } from "../../../connection";
import WithRole from "../../../components/WithRole";
import { injectIntl } from "react-intl";
import messages from "../messages";
const col6 = {
  md: 6,
  lg: 4,
  xl: 4,
  xxl: 3,
};

/* eslint-disable react/prefer-stateless-function */
export class CancelInvoiceBillList extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      current: 1,
      filter: {},
      selected: [],
      downloading: false,
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
          `/main/finance/invoice-bills-cancel?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  render() {
    const { CancelInvoiceBillList, location } = this.props;
    const { loading, items, apartments, buildingArea } = CancelInvoiceBillList;
    const { current, filter, downloading } = this.state;
    let { formatMessage } = this.props.intl;
    const { search } = location;
    let params = queryString.parse(search);

    const columns = [
      {
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
        render: (text) => <span>{text}</span>,
      },
      {
        title: (
          <span className="nameTable"> {formatMessage(messages.payments)}</span>
        ),
        dataIndex: "type_payment_name",
        key: "type_payment_name",
        render: (text) => (
          <span>
            {text === 0
              ? formatMessage(messages.cash)
              : formatMessage(messages.transfer)}
          </span>
        ),
      },
      {
        width: 150,
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
        width: 120,
        title: (
          <span className="nameTable">
            {formatMessage(messages.amountOfMoney)}
          </span>
        ),
        dataIndex: "total_price",
        key: "total_price",
        align: "right",
        render: (text) => <span>{formatPrice(text)} đ</span>,
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.receiver)}</span>
        ),
        dataIndex: "payer_name",
        key: "payer_name",
      },
      {
        title: (
          <span className="nameTable">{formatMessage(messages.payer2)}</span>
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
      {
        title: (
          <span className="nameTable">{formatMessage(messages.canceler)}</span>
        ),
        dataIndex: "updated_name",
        key: "updated_name",
      },
      {
        title: (
          <span className="nameTable">
            {formatMessage(messages.cancellationTime)}
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text) => moment.unix(text).format("HH:mm DD/MM/YYYY"),
      },
    ];

    return (
      <Page className="CancelInvoiceBillListPage" inner>
        <div>
          <Menu
            mode={"horizontal"}
            selectedKeys={[this.props.location.pathname]}
            onSelect={({ key }) => {
              this.props.history.push(key);
            }}
          >
            <Menu.Item key={"/main/finance/invoice-bills"}>
              {formatMessage(messages.phieuChi)}
            </Menu.Item>
            <Menu.Item key={"/main/finance/invoice-bills-cancel"}>
              {formatMessage(messages.voidedVoucher)}
            </Menu.Item>
          </Menu>
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
              </Select>
            </Col>
            <Col {...col6}>
              <Input
                allowClear
                value={filter["management_user_name"] || ""}
                placeholder={formatMessage(messages.payer2)}
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
                    `/main/finance/invoice-bills-cancel?${queryString.stringify(
                      {
                        ...this.state.filter,
                        page: 1,
                      }
                    )}`
                  );
                }}
              >
                {formatMessage(messages.search)}
              </Button>
            </Col>

            <Col md={24} xxl={10}>
              <Row type="flex" align="middle" gutter={[8, 8]}>
                <Col xxl={8} md={5}>
                  <span
                    style={{ cursor: "pointer", color: GLOBAL_COLOR }}
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
                            `/main/finance/invoice-bills-cancel?${queryString.stringify(
                              {
                                ...this.state.filter,
                                page: 1,
                              }
                            )}`
                          );
                        }
                      );
                    }}
                  >
                    &ensp;{formatMessage(messages.week)}&ensp;
                  </span>
                  <span
                    style={{ cursor: "pointer", color: GLOBAL_COLOR }}
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
                            `/main/finance/invoice-bills-cancel?${queryString.stringify(
                              {
                                ...this.state.filter,
                                page: 1,
                              }
                            )}`
                          );
                        }
                      );
                    }}
                  >
                    &ensp;{formatMessage(messages.month)}&ensp;
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
                            `/main/finance/invoice-bills-cancel?${queryString.stringify(
                              {
                                ...this.state.filter,
                                page: 1,
                              }
                            )}`
                          );
                        }
                      );
                    }}
                  >
                    &ensp;{formatMessage(messages.year)}&ensp;
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
                            `/main/finance/invoice-bills-cancel?${queryString.stringify(
                              {
                                ...this.state.filter,
                                page: 1,
                              }
                            )}`
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
                      filter.end_time ? moment.unix(filter.end_time) : undefined
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
                            `/main/finance/invoice-bills-cancel?${queryString.stringify(
                              {
                                ...this.state.filter,
                                page: 1,
                              }
                            )}`
                          );
                        }
                      )
                    }
                  />
                </Col>
              </Row>
            </Col>
          </Row>

          <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_INVOICE_BILL]}>
            <Row
              type="flex"
              align="middle"
              style={{ paddingBottom: 24, marginTop: 24 }}
            >
              <Tooltip title={formatMessage(messages.exportVData3)}>
                <Button
                  style={{ marginRight: 24, position: "absolute", right: 0 }}
                  onClick={() => {
                    this.setState(
                      {
                        downloading: true,
                      },
                      () => {
                        window.connection
                          .exportFinanceBillData({
                            ...params,
                            type: 1,
                            status: 2,
                          })
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
                  loading={downloading}
                  shape="circle"
                  size="large"
                >
                  {!downloading && (
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
                  total: CancelInvoiceBillList.totalPage,
                  current: this.state.current,
                  showTotal: (total) =>
                    `${formatMessage(messages.total)} ${total} ${formatMessage(
                      messages.bill
                    ).toLowerCase()}`,
                }}
                onChange={this.handleTableChange}
                expandedRowRender={(record) => {
                  return record.note;
                }}
                scroll={{ x: 1366 }}
              />
            </Col>
          </Row>
        </div>
      </Page>
    );
  }
}

CancelInvoiceBillList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  CancelInvoiceBillList: makeSelectCancelInvoiceBillList(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "CancelInvoiceBillList", reducer });
const withSaga = injectSaga({ key: "CancelInvoiceBillList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(CancelInvoiceBillList));
