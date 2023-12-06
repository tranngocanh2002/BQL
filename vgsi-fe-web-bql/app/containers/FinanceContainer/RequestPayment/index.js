/**
 *
 * RequestPayment
 *
 */

import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Col,
  DatePicker,
  Input,
  Menu,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import _ from "lodash";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectRequestPayment from "./selectors";

import moment from "moment";
import queryString from "query-string";
import { injectIntl } from "react-intl";
import { selectAuthGroup } from "../../../redux/selectors";
import { formatPrice } from "../../../utils";
import config from "../../../utils/config";
import { GLOBAL_COLOR, globalStyles } from "../../../utils/constants";
import ModalDeny from "./ModalDeny";
import {
  defaultAction,
  deleteRequest,
  fetchApartmentAction,
  fetchPaymentRequest,
} from "./actions";
import messages from "./messages";
const { Option } = Select;

const col6 = {
  md: 6,
  lg: 4,
  xl: 4,
};

/* eslint-disable react/prefer-stateless-function */
export class RequestPayment extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      current: 1,
      filter: {},
      selected: [],
      showModalDeny: false,
      record: {},
    };
    this._onSearchApartment = _.debounce(this.onSearchApartment, 300);
  }

  onSearchApartment = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this._onSearchApartment("");
    this.reload(this.props.location.search);
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
    if (
      this.props.requestPayment.deleting != nextProps.requestPayment.deleting &&
      !nextProps.requestPayment.deleting
    ) {
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
        this.props.dispatch(fetchPaymentRequest(params));
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
          `/main/finance/payment-request?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  handleDeny = (record, values) => {
    const { dispatch } = this.props;
    dispatch(
      deleteRequest({
        apartment_id: record.apartment_id,
        code: record.code,
        reason: values.reason,
        callback: () => {
          this.setState({
            showModalDeny: false,
          });
        },
      })
    );
  };

  render() {
    const { requestPayment, auth_group, dispatch } = this.props;
    const { loading, items, deleting, apartments } = requestPayment;
    const { filter, showModalDeny } = this.state;

    const newTable = items.filter((item) => item.status !== 1);

    const status = [
      {
        id: 0,
        value: 0,
        name: this.props.intl.formatMessage(messages.waitForConfirmation),
      },
      {
        id: 1,
        value: -1,
        name: this.props.intl.formatMessage(messages.cancelled),
      },
      { id: 2, value: 2, name: this.props.intl.formatMessage(messages.denied) },
      // { id: 3, value: 1, name: this.props.intl.formatMessage(messages.done) },
    ];
    const columns = [
      {
        // width: 110,
        title: this.props.intl.formatMessage(messages.requestDate),
        dataIndex: "created_at",
        key: "created_at",
        width: 150,
        render: (text) => moment.unix(text).format("DD/MM/YYYY"),
      },
      {
        // width: 100,
        title: this.props.intl.formatMessage(messages.requestCode),
        dataIndex: "code",
        key: "code",
      },
      // {
      //   width: 100,
      //   title: <span>{this.props.intl.formatMessage(messages.source)}</span>,
      //   dataIndex: "is_auto",
      //   key: "is_auto",
      //   render: (text) => {
      //     if (text == 1) {
      //       return this.props.intl.formatMessage(messages.system);
      //     }
      //     return "App";
      //   },
      // },
      {
        // width: 80,
        title: this.props.intl.formatMessage(messages.property),
        dataIndex: "apartment_name",
        key: "apartment_name",
        render: (text, record) => {
          return `${text}(${record.apartment_parent_path})`;
        },
      },
      {
        title: (
          <span>{this.props.intl.formatMessage(messages.customerName)}</span>
        ),
        dataIndex: "head_household_name",
        key: "head_household_name",
      },
      {
        title: this.props.intl.formatMessage(messages.amountMoney),
        dataIndex: "total_price",
        key: "total_price",
        align: "left",
        width: 150,
        render: (text, record) => (
          <span>
            {formatPrice(
              _.sumBy(
                record.service_payment_fees || [],
                (iiii) => iiii.more_money_collecte
              )
            )}{" "}
            đ
          </span>
        ),
      },
      {
        title: this.props.intl.formatMessage(messages.creator),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        title: this.props.intl.formatMessage(messages.status),
        dataIndex: "status",
        key: "status",
        align: "center",
        width: 200,
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <span>
              {record.status === 0
                ? this.props.intl.formatMessage(messages.waitForConfirmation)
                : record.status === -1
                ? this.props.intl.formatMessage(messages.cancelled)
                : record.status === 2
                ? this.props.intl.formatMessage(messages.wasDenied)
                : this.props.intl.formatMessage(messages.done)}
            </span>
            {/* {record.status === 2 && (
              <Tooltip
                title={this.props.intl.formatMessage(messages.reasonDenied)}
              >
                <i
                  className="fa fa-info-circle"
                  style={{ fontSize: 18, marginLeft: 10 }}
                />
              </Tooltip>
            )} */}
          </Row>
        ),
      },
      {
        width: 100,
        fixed: "right",
        title: this.props.intl.formatMessage(messages.action),
        dataIndex: "#",
        key: "#",
        align: "center",
        render: (text, record) => {
          return (
            <Row type="flex" align="middle" justify="center">
              <>
                <Tooltip
                  title={this.props.intl.formatMessage(messages.createVote)}
                >
                  <Row
                    type="flex"
                    align="middle"
                    style={{
                      color:
                        record.status !== 0 ||
                        !auth_group.checkRole([
                          config.ALL_ROLE_NAME.FINANCE_CREATE_BILL,
                        ])
                          ? "#c5c5c5"
                          : GLOBAL_COLOR,
                      marginRight: 10,
                      cursor:
                        record.status !== 0 ||
                        !auth_group.checkRole([
                          config.ALL_ROLE_NAME.FINANCE_CREATE_BILL,
                        ])
                          ? "not-allowed"
                          : "pointer",
                    }}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      if (
                        record.status === 0 &&
                        auth_group.checkRole([
                          config.ALL_ROLE_NAME.FINANCE_CREATE_BILL,
                        ])
                      ) {
                        this.props.history.push("/main/finance/reception", {
                          payment_gen_code: record.code,
                          apartment_id: record.apartment_id,
                          ids: (record.service_payment_fees || []).map(
                            (iii) => iii.id
                          ),
                          resident_user_name: record.resident_user_name,
                        });
                      }
                    }}
                  >
                    <i
                      className="fa fa-check"
                      style={
                        auth_group.checkRole([
                          config.ALL_ROLE_NAME.FINANCE_CREATE_BILL,
                        ])
                          ? globalStyles.icon
                          : globalStyles.iconDisabled
                      }
                    />
                  </Row>
                </Tooltip>
                |
              </>

              <Tooltip title={this.props.intl.formatMessage(messages.reject)}>
                <Row
                  type="flex"
                  align="middle"
                  style={{
                    color:
                      record.status !== 0 ||
                      !auth_group.checkRole([
                        config.ALL_ROLE_NAME.FINANCE_MANAGERMENT_BILL,
                      ])
                        ? "#c5c5c5"
                        : "#F15A29",
                    marginLeft: 10,
                    cursor:
                      record.status !== 0 ||
                      !auth_group.checkRole([
                        config.ALL_ROLE_NAME.FINANCE_MANAGERMENT_BILL,
                      ])
                        ? "not-allowed"
                        : "pointer",
                  }}
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    if (
                      record.status === 0 &&
                      auth_group.checkRole([
                        config.ALL_ROLE_NAME.FINANCE_MANAGERMENT_BILL,
                      ])
                    ) {
                      this.setState({
                        showModalDeny: true,
                        record: record,
                      });
                    }
                  }}
                >
                  <i
                    className="fa fa-times"
                    style={
                      auth_group.checkRole([
                        config.ALL_ROLE_NAME.FINANCE_MANAGERMENT_BILL,
                      ])
                        ? globalStyles.iconDelete
                        : globalStyles.iconDisabled
                    }
                  />
                </Row>
              </Tooltip>
            </Row>
          );
        },
      },
    ];

    return (
      <Page className="CancelRequestPaymentPage" inner>
        <div>
          <Menu
            mode={"horizontal"}
            selectedKeys={[this.props.location.pathname]}
            onSelect={({ key }) => {
              this.props.history.push(key);
            }}
          >
            <Menu.Item key={"/main/finance/bills"}>
              {this.props.intl.formatMessage(messages.receiptVoucher)}
            </Menu.Item>
            <Menu.Item key={"/main/finance/bills-cancel"}>
              {this.props.intl.formatMessage(messages.voidVoucher)}
            </Menu.Item>
            <Menu.Item key={"/main/finance/payment-request"}>
              {this.props.intl.formatMessage(messages.confirmPayment)}
            </Menu.Item>
          </Menu>
          <Row gutter={[8, 8]} style={{ paddingBottom: 16, marginTop: 24 }}>
            <Col {...col6}>
              <Input
                allowClear
                value={filter["code"] || ""}
                placeholder={this.props.intl.formatMessage(
                  messages.requestCode
                )}
                onChange={(e) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["code"]: e.target.value,
                    },
                  });
                }}
              />
            </Col>
            <Col {...col6}>
              <Select
                style={{ width: "100%" }}
                loading={apartments.loading}
                showSearch
                placeholder={this.props.intl.formatMessage(
                  messages.selectProperty
                )}
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
                showSearch
                style={{ width: "100%" }}
                placeholder={this.props.intl.formatMessage(messages.status)}
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
                    }
                    // () => {
                    //   this.props.history.push(
                    //     `/main/finance/payment-request?${queryString.stringify({
                    //       ...this.state.filter,
                    //       page: 1,
                    //     })}`
                    //   );
                    // }
                  );
                }}
                allowClear
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
            <Col md={6} lg={3}>
              <Button
                type="primary"
                onClick={() => {
                  this.props.history.push(
                    `/main/finance/payment-request?${queryString.stringify({
                      ...this.state.filter,
                      page: 1,
                    })}`
                  );
                }}
              >
                {this.props.intl.formatMessage(messages.search)}
              </Button>
            </Col>
            <Col md={24} lg={13}>
              <Row type="flex" align="middle" gutter={[8, 8]}>
                <Col span={8}>
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
                            `/main/finance/payment-request?${queryString.stringify(
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
                    &ensp;{this.props.intl.formatMessage(messages.week)}&ensp;
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
                            `/main/finance/payment-request?${queryString.stringify(
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
                    &ensp;{this.props.intl.formatMessage(messages.month)}&ensp;
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
                            `/main/finance/payment-request?${queryString.stringify(
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
                    &ensp;{this.props.intl.formatMessage(messages.year)}&ensp;
                  </span>
                </Col>
                <Col span={8}>
                  <DatePicker
                    placeholder={this.props.intl.formatMessage(
                      messages.fromDate
                    )}
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
                            `/main/finance/payment-request?${queryString.stringify(
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
                <Col span={8}>
                  <DatePicker
                    placeholder={this.props.intl.formatMessage(messages.toDate)}
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
                            `/main/finance/payment-request?${queryString.stringify(
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
          <Table
            rowKey="id"
            loading={loading || deleting}
            columns={columns}
            dataSource={newTable}
            locale={{
              emptyText: this.props.intl.formatMessage(messages.noData),
            }}
            bordered
            scroll={{ x: 1200 }}
            pagination={{
              pageSize: 20,
              total: newTable.length,
              current: this.state.current,
              showTotal: (total) =>
                this.props.intl.formatMessage(messages.totalPage, { total }),
            }}
            onChange={this.handleTableChange}
            onRow={(record) => {
              return {
                onClick: () => {
                  this.props.history.push(
                    `/main/finance/payment-request/detail/${record.id}`,
                    {
                      record,
                    }
                  );
                },
              };
            }}
          />
          <ModalDeny
            setState={this.setState.bind(this)}
            showModalDeny={showModalDeny}
            handleDeny={this.handleDeny}
            dispatch={dispatch}
            record={this.state.record}
          />
        </div>
      </Page>
    );
  }
}

const mapStateToProps = createStructuredSelector({
  requestPayment: makeSelectRequestPayment(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "requestPayment", reducer });
const withSaga = injectSaga({ key: "requestPayment", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(RequestPayment));
