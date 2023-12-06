/* eslint-disable no-undef */
/**
 *
 * DashboardReception
 *
 */

import {
  Button,
  Checkbox,
  Col,
  DatePicker,
  Icon,
  Input,
  Popover,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import moment from "moment";
import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import {
  DocTienBangChu,
  config,
  convertNumberToWords,
  formatPrice,
  notificationBar,
  parseBillToView,
} from "../../../utils";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectDashboardReception from "./selectors";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import _ from "lodash";
import { injectIntl } from "react-intl";
import JsxParser from "react-jsx-parser";
import { hotkeys } from "react-keyboard-shortcuts";
import { Link } from "react-router-dom";
import _JSXStyle from "styled-jsx/style";
import InputNumberFormat from "../../../components/InputNumberFormat";
import Loader from "../../../components/Loader/Loader";
import {
  selectBuildingCluster,
  selectUserDetail,
} from "../../../redux/selectors";
import { GLOBAL_COLOR } from "../../../utils/constants";
import DashboardBills from "../DashboardBills";
import messages from "../messages";
import {
  clearForm,
  createOrder,
  defaultAction,
  fetchApartment,
  fetchFeeOfApartment,
  fetchMemberAction,
} from "./actions";
import("./index.less");
const colWrapper = {
  md: 12,
  lg: 24,
  xl: 12,
};

const colTitle = {
  md: 10,
  lg: 8,
};

const colContent = {
  md: 14,
  lg: 16,
};

/* eslint-disable react/prefer-stateless-function */
export class DashboardReception extends React.PureComponent {
  constructor(props) {
    super(props);
    // let { ids, payment_gen_code } = props.location.state || {};
    this.state = {
      ids: [],
      apartment_id: undefined,
      payer_name: (this.props.location.state || {}).resident_user_name || "",
      fees: [],
      total_count: {
        total_money_collected: 0,
        total_more_money_collecte: 0,
        total_price: 0,
      },
      type_payment: 0,
      moneyReality: 0,
      payment_date: moment(),
      execution_date: moment(),
      number: "XXXX",
      payment_gen_code: null,
    };
    this._onSearchApartment = _.debounce(this.onSearch, 500);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this._onSearchApartment("");
    let { apartment_id } = this.props.location.state || {};
    this.props.dispatch(fetchFeeOfApartment(apartment_id));
    if (apartment_id) {
      this.props.dispatch(fetchMemberAction({ apartment_id: apartment_id }));
    }
  }

  hot_keys = {
    "alt+n": {
      // combo from mousetrap
      priority: 1,
      handler: (event) => {
        console.log("alt+n");
      },
    },
    f1: {
      // combo from mousetrap
      priority: 1,
      handler: (e) => {
        e.preventDefault();
        e.stopPropagation();
        this._createAndPrint();
      },
    },
    f2: {
      // combo from mousetrap
      priority: 1,
      handler: (e) => {
        e.preventDefault();
        e.stopPropagation();
        this._createBill();
      },
    },
    f3: {
      // combo from mousetrap
      priority: 1,
      handler: (e) => {
        e.preventDefault();
        e.stopPropagation();
        this._printBill();
      },
    },
    esc: {
      // combo from mousetrap
      priority: 1,
      handler: (e) => {
        e.preventDefault();
        e.stopPropagation();
        this._clearForm();
      },
    },
  };

  _printBill = () => {
    const { dashboardReception, buildingCluster } = this.props;

    let { service_bill_template } = buildingCluster.data || {};
    let service_bill_templateJSON = null;
    try {
      service_bill_templateJSON = service_bill_template;
    } catch (error) {
      console.log("error", error);
    }

    const { fees } = this.state;
    if (fees.length == 0 || !dashboardReception.createData) {
      return;
    }

    const printWindow = document.createElement("iframe");
    printWindow.style.position = "absolute";
    printWindow.style.top = "-5000px";
    printWindow.style.left = "-1000px";
    document.body.appendChild(printWindow);
    printWindow.onload = () => {
      console.log("onLoad");
    };

    const domDoc =
      printWindow.contentDocument || printWindow.contentWindow.document;
    domDoc.open();
    domDoc.write(`
    <!DOCTYPE html>
    <html lang="vi-VN">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
        <body>
            <style type="text/css">
    ${service_bill_templateJSON ? service_bill_templateJSON.style_string : ""}
    </style>
    <div style="margin-left: 16px">
                  <img src="${
                    config.logoPath4
                  }" alt="" width="120" height="60"></div>
    <div class="jsx-parser" >
    ${service_bill_templateJSON ? $("#lien1")[0].innerHTML : ""}
    <div style="page-break-before:always" />
    <div style="margin-left: 16px">
                  <img src="${
                    config.logoPath4
                  }" alt="" width="120" height="60"></div>
    ${service_bill_templateJSON ? $("#lien2")[0].innerHTML : ""}
    <div style="page-break-before:always" />
    <div style="margin-left: 16px">
                  <img src="${
                    config.logoPath4
                  }" alt="" width="120" height="60"></div>
    ${service_bill_templateJSON ? $("#lien3")[0].innerHTML : ""}
    </div>
    </body>
    </html>`);
    domDoc.close();
    setTimeout(() => {
      printWindow.contentWindow.focus();
      printWindow.contentWindow.print();
      setTimeout(() => {
        printWindow.parentNode.removeChild(printWindow);
      }, 500);
    }, 500);
  };

  componentWillReceiveProps(nextProps) {
    let { apartment_id, payment_gen_code, ids, limit_payment } =
      nextProps.location.state || {};
    if (
      ((!!payment_gen_code &&
        payment_gen_code !== this.state.payment_gen_code) ||
        limit_payment) &&
      !!ids &&
      !!ids.length &&
      ids.length !== this.state.ids.length
    ) {
      this.setState(
        {
          payment_gen_code,
          ids: ids,
        },
        () => {
          this.props.history.replace({
            state: {
              apartment_id,
              ids: [],
              payment_gen_code: undefined,
              limit_payment: false,
            },
          });
        }
      );
    }
    if (
      this.props.dashboardReception.fee.loading !=
        nextProps.dashboardReception.fee.loading &&
      !nextProps.dashboardReception.fee.loading
    ) {
      let { ids } = this.state;
      let fees = [];
      if (!!ids && !!ids.length) {
        fees = nextProps.dashboardReception.fee.items.map((ff) => {
          if (!!ids && !!ids.length) {
            if (ids.some((rr) => rr == ff.id)) {
              return {
                ...ff,
                new_money_collected: ff.more_money_collecte,
                new_more_money_collecte: 0,
              };
            }
            return {
              ...ff,
              new_money_collected: 0,
              new_more_money_collecte: ff.more_money_collecte,
            };
          }
        });
      } else {
        fees = nextProps.dashboardReception.fee.items.map((ff) => {
          return {
            ...ff,
            new_money_collected: ff.more_money_collecte,
            new_more_money_collecte: 0,
          };
        });
      }

      const fee_checked = fees.filter((e) => e.new_money_collected > 0);
      const oldFees = fees.filter((e) => e.new_money_collected <= 0);
      const newFees = [...fee_checked, ...oldFees];
      let sumMoneyReality = _.sumBy(fees, (ff) => ff.new_money_collected);
      this.setState({
        fees: newFees,
        total_count: { ...nextProps.dashboardReception.fee.total_count },
        moneyReality: sumMoneyReality > 0 ? sumMoneyReality : 0,
      });
    }

    if (
      this.props.dashboardReception.apartment.loading !=
        nextProps.dashboardReception.apartment.loading &&
      !nextProps.dashboardReception.apartment.loading &&
      !!apartment_id &&
      // this.state.payer_name == "" &&
      !this._theFirstTime
    ) {
      this._theFirstTime = true;
      let apartment = nextProps.dashboardReception.apartment.items.find(
        (dd) => dd.id == apartment_id
      );
      if (apartment) {
        this.setState({
          apartment_id,
          // payer_name: apartment.resident_user_name || "",
        });
      } else {
        this.setState({
          apartment_id: undefined,
          // payer_name: "",
        });
      }
    }
  }
  _createAndPrint = () => {
    const { dashboardReception, userDetail } = this.props;
    const {
      apartment_id,
      payment_date,
      execution_date,
      fees,
      moneyReality,
      payer_name,
      type_payment,
      payment_gen_code,
    } = this.state;
    let total_new_money_collected = _.sumBy(
      fees,
      (ss) => ss.new_money_collected
    );

    if (
      fees.length == 0 ||
      !!dashboardReception.createData ||
      total_new_money_collected < 0
    ) {
      return;
    }
    if (total_new_money_collected > moneyReality) {
      notificationBar(
        this.props.intl.formatMessage(messages.errorContent2),
        "warning"
      );
      return;
    }

    this.props.dispatch(
      createOrder({
        apartment_id,
        management_user_name: userDetail.first_name,
        payer_name,
        type_payment,
        description: "string",
        payment_gen_code,
        service_payment_fees: fees
          .filter((rr) => rr.new_money_collected != 0)
          .map((rr) => {
            return {
              service_payment_fee_id: rr.id,
              price: rr.new_money_collected,
            };
          }),
        payment_date: payment_date.unix(),
        execution_date: execution_date.unix(),
        callback: () => {
          this._printBill();
          this.props.history.push(
            `/main/finance/reception?page=1&refresh=${Date.now()}`
          );
        },
      })
    );
  };
  _createBill = () => {
    const { dashboardReception, userDetail } = this.props;
    const {
      apartment_id,
      payment_date,
      execution_date,
      fees,
      moneyReality,
      payer_name,
      type_payment,
      payment_gen_code,
    } = this.state;
    let total_new_money_collected = _.sumBy(
      fees,
      (ss) => ss.new_money_collected
    );

    if (
      fees.length == 0 ||
      !!dashboardReception.createData ||
      total_new_money_collected < 0
    ) {
      return;
    }

    if (total_new_money_collected > moneyReality) {
      notificationBar(
        this.props.intl.formatMessage(messages.errorContent2),
        "warning"
      );
      return;
    }

    this.props.dispatch(
      createOrder({
        apartment_id,
        management_user_name: userDetail.first_name,
        payer_name,
        type_payment,
        description: "string",
        payment_gen_code,
        service_payment_fees: fees
          .filter((rr) => rr.new_money_collected != 0)
          .map((rr) => {
            return {
              service_payment_fee_id: rr.id,
              price: rr.new_money_collected,
            };
          }),
        payment_date: payment_date.unix(),
        execution_date: execution_date.unix(),
        callback: (id) => {
          // this.props.history.push(
          //   `/main/finance/reception?page=1&refresh=${Date.now()}`
          // );
          this.props.history.push(`/main/finance/reception/bill/${id}`);
        },
      })
    );
  };

  _clearForm = () => {
    this.props.dispatch(clearForm());
    this.setState({
      apartment_id: undefined,
      payer_name: "",
      fees: [],
      total_count: {
        total_money_collected: 0,
        total_more_money_collecte: 0,
        total_price: 0,
      },
      type_payment: 0,
      moneyReality: 0,
      payment_date: moment(),
      execution_date: moment(),
      number: "XXXX",
    });
    this.props.history.push("/main/finance/reception");
  };

  _renderInfo = () => {
    const { dashboardReception, userDetail } = this.props;
    const { fees, total_count, moneyReality, type_payment } = this.state;
    let total_new_money_collected = _.sumBy(
      fees,
      (ss) => ss.new_money_collected
    );
    const columns = [
      {
        title: <span />,
        // fixed: "left",
        dataIndex: "info",
        key: "info",
        // width: 50,
        render: (_text, record) => {
          return (
            <Popover
              content={
                <span style={{ whiteSpace: "pre-wrap" }}>
                  {this.props.language === "en"
                    ? record.description_en
                    : record.description}
                </span>
              }
              title={this.props.intl.formatMessage(messages.detail)}
            >
              <Icon type="info-circle" />
            </Popover>
          );
        },
      },
      {
        title: <span>#</span>,
        // fixed: "left",
        dataIndex: "id",
        key: "id",
        // width: 50,
        render: (_text, record) => {
          return (
            <Checkbox
              checked={record.new_money_collected !== 0}
              onChange={() => {
                let fees = this.state.fees.map((fff) => {
                  if (fff.id == record.id) {
                    if (record.new_money_collected !== 0) {
                      return {
                        ...fff,
                        new_money_collected: 0,
                        new_more_money_collecte: fff.more_money_collecte,
                      };
                    } else {
                      return {
                        ...fff,
                        new_money_collected: fff.more_money_collecte,
                        new_more_money_collecte: 0,
                      };
                    }
                  }
                  return fff;
                });
                this.setState({
                  fees,
                });
              }}
            />
          );
        },
      },
      {
        // width: 80,
        // fixed: "left",
        title: <span>{this.props.intl.formatMessage(messages.month)}</span>,
        dataIndex: "fee_month",
        key: "fee_month",
        render: (_text, record) => (
          <span>{moment.unix(record.fee_of_month).format("MM/YYYY")}</span>
        ),
      },
      {
        title: (
          <span>{this.props.intl.formatMessage(messages.serviceType)}</span>
        ),
        dataIndex:
          this.props.language === "en"
            ? "service_map_management_service_name_en"
            : "service_map_management_service_name",
        key:
          this.props.language === "en"
            ? "service_map_management_service_name_en"
            : "service_map_management_service_name",
        render: (text) => <span>{text}</span>,
      },
      {
        // width: 90,
        align: "right",
        title: (
          <span>{this.props.intl.formatMessage(messages.receivables)}</span>
        ),
        dataIndex: "price",
        key: "price",
        render: (text) => <strong>{formatPrice(text)}</strong>,
      },
      {
        // width: 90,
        align: "right",
        title: <span>{this.props.intl.formatMessage(messages.received)}</span>,
        dataIndex: "money_collected",
        key: "money_collected",
        render: (text, record) => {
          return (
            <span
              style={{
                display: "flex",
                alignItems: "center",
              }}
            >
              <span>
                {formatPrice(text)}
                &ensp;
              </span>
              {!!record.service_bills && record.service_bills.length > 0 && (
                <Tooltip
                  title={
                    <div>
                      <span>
                        {this.props.intl.formatMessage(messages.enterBill)}:
                      </span>
                      <br />
                      {record.service_bills.map((ll) => {
                        return (
                          <>
                            <Link
                              to={`/main/finance/reception/bill/${ll.id}`}
                              key={ll}
                            >
                              {ll.number}
                            </Link>
                            <br key={`ll-${ll}`} />
                          </>
                        );
                      })}
                    </div>
                  }
                >
                  <Icon
                    type="info-circle"
                    style={{
                      color: GLOBAL_COLOR,
                    }}
                  />
                </Tooltip>
              )}
            </span>
          );
        },
      },
      {
        // width: 90,
        align: "right",
        title: <span>{this.props.intl.formatMessage(messages.stillOwe)}</span>,
        dataIndex: "new_more_money_collecte",
        key: "new_more_money_collecte",
        render: (text) => <span>{formatPrice(text)}</span>,
      },
      {
        // width: 90,
        align: "right",
        // fixed: 'right',
        title: (
          <span>{this.props.intl.formatMessage(messages.realReceiving)}</span>
        ),
        dataIndex: "new_money_collected",
        key: "new_money_collected",
        render: (text) => <span>{formatPrice(text)}</span>,
      },
      {
        width: 70,
        align: "center",
        fixed: "right",
        title: (
          <span>{this.props.intl.formatMessage(messages.debtRemove)}</span>
        ),
        dataIndex: "type_5",
        key: "type_5",
        render: (text, record) => {
          let _moneyReality = moneyReality || 0;
          let total = _.sumBy(fees, (ss) => ss.new_money_collected);
          _moneyReality = total - _moneyReality;
          if (
            _moneyReality <= 0 &&
            moneyReality > total_new_money_collected &&
            record.new_money_collected < 0 &&
            _.sumBy(fees, (ss) => ss.new_more_money_collecte) <= 0
          ) {
            if (_moneyReality < record.new_money_collected) {
              return (
                <Button
                  type="danger"
                  onClick={() => {
                    let fees = this.state.fees.map((fff) => {
                      if (fff.id == record.id) {
                        if (record.new_more_money_collecte < 0) {
                          return;
                        } else {
                          return {
                            ...fff,
                            new_more_money_collecte: record.new_money_collected,
                            new_money_collected: 0,
                          };
                        }
                      }
                      return fff;
                    });
                    this.setState({
                      fees,
                    });
                  }}
                >
                  {this.props.intl.formatMessage(messages.debtRemove)}
                </Button>
              );
            } else {
              return (
                <Button
                  type="danger"
                  onClick={() => {
                    let fees = this.state.fees.map((fff) => {
                      if (fff.id == record.id) {
                        if (record.new_more_money_collecte < 0) {
                          return {
                            ...fff,
                            new_more_money_collecte:
                              record.new_more_money_collecte +
                              Math.min(0, _moneyReality),
                            new_money_collected:
                              record.new_money_collected - _moneyReality,
                          };
                        } else {
                          return {
                            ...fff,
                            new_more_money_collecte: Math.min(0, _moneyReality),
                            new_money_collected:
                              record.more_money_collecte - _moneyReality,
                          };
                        }
                      }
                      return fff;
                    });
                    this.setState({
                      fees,
                    });
                  }}
                >
                  {this.props.intl.formatMessage(messages.debtRemove)}
                </Button>
              );
            }
          }
          if (
            (_moneyReality <= 0 && record.new_more_money_collecte > 0) ||
            moneyReality >= total_new_money_collected
          ) {
            return;
          }
          if (_moneyReality < record.new_money_collected) {
            return (
              <Button
                type="danger"
                onClick={() => {
                  let fees = this.state.fees.map((fff) => {
                    if (fff.id == record.id) {
                      if (record.new_more_money_collecte > 0) {
                        return {
                          ...fff,
                          new_money_collected:
                            record.new_money_collected - _moneyReality,
                          new_more_money_collecte:
                            record.new_more_money_collecte + _moneyReality,
                        };
                      } else {
                        return {
                          ...fff,
                          new_money_collected:
                            record.more_money_collecte - _moneyReality,
                          new_more_money_collecte: Math.max(0, _moneyReality),
                        };
                      }
                    }
                    return fff;
                  });
                  this.setState({
                    fees,
                  });
                }}
              >
                {this.props.intl.formatMessage(messages.debtRemove)}
              </Button>
            );
          } else if (
            _moneyReality >= record.new_money_collected &&
            record.new_money_collected > 0 &&
            _.sumBy(fees, (ss) => ss.new_more_money_collecte) < 0
          ) {
            return (
              <Button
                type="danger"
                onClick={() => {
                  let fees = this.state.fees.map((fff) => {
                    if (fff.id == record.id) {
                      return {
                        ...fff,
                        new_money_collected: 0,
                        new_more_money_collecte:
                          _moneyReality - record.new_more_money_collecte,
                      };
                    }
                    return fff;
                  });
                  this.setState({
                    fees,
                  });
                }}
              >
                {this.props.intl.formatMessage(messages.debtRemove)}
              </Button>
            );
          } else if (
            _moneyReality >= record.new_money_collected &&
            record.new_money_collected > 0 &&
            _.sumBy(fees, (ss) => ss.new_more_money_collecte) >= 0
          ) {
            return (
              <Button
                type="danger"
                onClick={() => {
                  let fees = this.state.fees.map((fff) => {
                    if (fff.id == record.id) {
                      return {
                        ...fff,
                        new_money_collected: 0,
                        new_more_money_collecte: Math.max(
                          0,
                          record.more_money_collecte
                        ),
                      };
                    }
                    return fff;
                  });
                  this.setState({
                    fees,
                  });
                }}
              >
                {this.props.intl.formatMessage(messages.debtRemove)}
              </Button>
            );
          }
        },
      },
    ];

    return (
      <Col md={24} lg={12} className="congNo">
        <Row gutter={[16, 10]}>
          <Col {...colWrapper}>
            <Row type="flex" align="middle">
              <Col
                {...colTitle}
                style={{
                  textAlign: "left",
                }}
              >
                {this.props.intl.formatMessage(messages.property)}:&ensp;
              </Col>
              <Col {...colContent}>
                <Select
                  style={{ width: "100%" }}
                  loading={dashboardReception.apartment.loading}
                  showSearch
                  placeholder={this.props.intl.formatMessage(
                    messages.choseProperty
                  )}
                  optionFilterProp="children"
                  notFoundContent={
                    dashboardReception.apartment.loading ? (
                      <Spin size="small" />
                    ) : null
                  }
                  onSearch={this._onSearchApartment}
                  value={
                    this.state.apartment_id
                      ? String(this.state.apartment_id)
                      : undefined
                  }
                  allowClear
                  onChange={(value, opt) => {
                    this.props.dispatch(clearForm());
                    this.setState(
                      {
                        apartment_id: value,
                        payer_name: (
                          dashboardReception.apartment.items.find(
                            (ii) => ii.id == value
                          ) || { resident_user_name: "" }
                        ).resident_user_name,
                        fees: [],
                        ids: [],
                        payment_gen_code: undefined,
                        total_count: {
                          total_money_collected: 0,
                          total_more_money_collecte: 0,
                          total_price: 0,
                        },
                        type_payment: 0,
                        moneyReality: 0,
                        payment_date: moment(),
                        execution_date: moment(),
                        number: "XXXX",
                      },
                      () => {
                        if (value) {
                          this.props.dispatch(fetchFeeOfApartment(value));
                          this.props.dispatch(
                            fetchMemberAction({ apartment_id: value })
                          );
                        }
                      }
                    );
                    if (!opt) {
                      this._onSearchApartment("");
                    }
                  }}
                >
                  {dashboardReception.apartment.items.map((gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${gr.name} (${gr.parent_path})${
                        gr.status == 0
                          ? ` - ${this.props.intl.formatMessage(
                              messages.nothing
                            )}`
                          : ""
                      }`}</Select.Option>
                    );
                  })}
                </Select>
              </Col>
            </Row>
          </Col>
          <Col {...colWrapper}>
            <Row type="flex" align="middle">
              <Col
                {...colTitle}
                style={{
                  textAlign: "left",
                }}
              >
                {this.props.intl.formatMessage(messages.owner)}:&ensp;
              </Col>
              <Col {...colContent}>
                <Input
                  style={{
                    width: "100%",
                    backgroundColor: "white",
                    color: "black",
                    fontWeight: "bold",
                    border: "0px",
                    padding: 0,
                  }}
                  disabled
                  value={
                    (
                      dashboardReception.apartment.items.find(
                        (ii) => ii.id == this.state.apartment_id
                      ) || { resident_user_name: "" }
                    ).resident_user_name
                  }
                />
              </Col>
            </Row>
          </Col>
          <Col {...colWrapper}>
            <Row type="flex" align="middle">
              <Col
                {...colTitle}
                style={{
                  textAlign: "left",
                }}
              >
                {this.props.intl.formatMessage(messages.collector)}:&ensp;
              </Col>
              <Col {...colContent}>
                {/* <Input style={{ width: '100%' }} disabled defaultValue={userDetail.first_name || userDetail.email} /> */}
                <span style={{ fontWeight: "bold", color: "black" }}>
                  {userDetail.first_name || userDetail.email}
                </span>
              </Col>
            </Row>
          </Col>
          <Col {...colWrapper}>
            <Row type="flex" align="middle">
              <Col
                {...colTitle}
                style={{
                  textAlign: "left",
                }}
              >
                {this.props.intl.formatMessage(messages.submitter)}:&ensp;
              </Col>
              <Col {...colContent}>
                <Select
                  style={{ width: "100%" }}
                  loading={
                    this.state.apartment_id &&
                    dashboardReception.members.loading
                  }
                  placeholder={this.props.intl.formatMessage(
                    messages.submitter
                  )}
                  optionFilterProp="children"
                  notFoundContent={
                    dashboardReception.members.loading ? (
                      <Spin size="small" />
                    ) : null
                  }
                  value={this.state.payer_name}
                  onChange={(value) => {
                    let name = dashboardReception.members.lst.filter(
                      (member) => member.phone === value
                    );
                    this.setState({
                      payer_name: name[0].first_name,
                    });
                  }}
                  disabled={!this.state.apartment_id}
                >
                  {dashboardReception.members.lst
                    .filter((mem) => mem.deleted_at === null)
                    .map((gr) => {
                      return (
                        <Select.Option
                          key={`group-${gr.apartment_map_resident_user_id}`}
                          value={`${gr.phone}`}
                        >{`${gr.first_name}`}</Select.Option>
                      );
                    })}
                </Select>
              </Col>
            </Row>
          </Col>
          <Col {...colWrapper}>
            <Row type="flex" align="middle">
              <Col
                {...colTitle}
                style={{
                  textAlign: "left",
                }}
              >
                {this.props.intl.formatMessage(messages.fillDate)}:&ensp;
              </Col>
              <Col {...colContent}>
                <DatePicker
                  allowClear={false}
                  placeholder={this.props.intl.formatMessage(
                    messages.choseDate
                  )}
                  style={{ width: "100%" }}
                  value={this.state.payment_date}
                  onChange={(payment_date) => {
                    if (payment_date)
                      this.setState({
                        payment_date,
                      });
                  }}
                  format="DD/MM/YYYY"
                  disabledDate={(current) => {
                    return current && current > moment().endOf("day");
                  }}
                />
              </Col>
            </Row>
          </Col>
          <Col {...colWrapper}>
            <Row type="flex" align="middle">
              <Col
                {...colTitle}
                style={{
                  textAlign: "left",
                }}
              >
                {this.props.intl.formatMessage(messages.implementDate)}:&ensp;
              </Col>
              <Col {...colContent}>
                <DatePicker
                  allowClear={false}
                  placeholder={this.props.intl.formatMessage(
                    messages.choseDate
                  )}
                  style={{ width: "100%" }}
                  value={this.state.execution_date}
                  onChange={(execution_date) => {
                    if (execution_date)
                      this.setState({
                        execution_date,
                      });
                  }}
                  format="DD/MM/YYYY"
                  disabledDate={(current) => {
                    return current && current > moment().endOf("day");
                  }}
                />
              </Col>
            </Row>
          </Col>
          <Col {...colWrapper}>
            <Row type="flex" align="middle">
              <Col
                {...colTitle}
                style={{
                  textAlign: "left",
                }}
              >
                {this.props.intl.formatMessage(messages.submissForm)}:&ensp;
              </Col>
              <Col {...colContent}>
                <Select
                  value={type_payment}
                  style={{ width: "100%" }}
                  onChange={(e) => {
                    this.setState({
                      type_payment: e,
                    });
                  }}
                >
                  <Select.Option value={0}>
                    {this.props.intl.formatMessage(messages.cash)}
                  </Select.Option>
                  <Select.Option value={1}>
                    {this.props.intl.formatMessage(messages.transfer)}
                  </Select.Option>
                </Select>
              </Col>
            </Row>
          </Col>
          <Col {...colWrapper}>
            <Row type="flex" align="middle">
              <Col
                {...colTitle}
                style={{
                  textAlign: "left",
                }}
              >
                {this.props.intl.formatMessage(messages.realReceiving)}:&ensp;
              </Col>
              <Col {...colContent}>
                <InputNumberFormat
                  style={{ width: "100%" }}
                  maxLength={13}
                  value={moneyReality}
                  onChange={(e) => {
                    let { ids } = this.state;
                    this.setState({
                      moneyReality: e,
                      fees: this.state.fees.map((ff) => {
                        if (!ids || ids.some((rr) => rr == ff.id))
                          return {
                            ...ff,
                            new_money_collected: ff.more_money_collecte,
                            new_more_money_collecte: 0,
                          };
                        return {
                          ...ff,
                          new_money_collected: 0,
                          new_more_money_collecte: ff.more_money_collecte,
                        };
                      }),
                      ids: undefined,
                    });
                  }}
                />
              </Col>
            </Row>
          </Col>
        </Row>
        <Row type="flex" justify="center" style={{ marginTop: 16 }}>
          <Col span={24}>
            <span style={{ fontSize: 18, fontWeight: "bold", color: "black" }}>
              {this.props.intl.formatMessage(messages.debts)}
            </span>
          </Col>
        </Row>

        <Row style={{ marginTop: 8 }}>
          <Col span={8}>
            <Row style={{ textAlign: "center" }}>
              <span style={{ fontSize: 14, color: "#909090" }}>
                {this.props.intl.formatMessage(messages.receivables)}
              </span>
              <br />
              {!dashboardReception.fee.loading && (
                <span
                  style={{ fontSize: 18, fontWeight: "bold" }}
                >{`${formatPrice(
                  total_count.total_more_money_collecte
                )} Đ`}</span>
              )}
              {dashboardReception.fee.loading && (
                <Spin style={{ marginTop: 8 }} />
              )}
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
              <span style={{ fontSize: 14, color: "#909090" }}>
                {this.props.intl.formatMessage(messages.realReceiving)}
              </span>
              <br />
              {!dashboardReception.fee.loading && (
                <span
                  style={{ fontSize: 18, fontWeight: "bold", color: "#3EA671" }}
                >{`${formatPrice(total_new_money_collected)} Đ`}</span>
              )}
              {dashboardReception.fee.loading && (
                <Spin style={{ marginTop: 8 }} />
              )}
            </Row>
          </Col>
          <Col span={8}>
            <Row style={{ textAlign: "center" }}>
              <span style={{ fontSize: 14, color: "#909090" }}>
                {" "}
                {this.props.intl.formatMessage(messages.stillOwe)}
              </span>
              <br />
              {!dashboardReception.fee.loading && (
                <span
                  style={{ fontSize: 18, fontWeight: "bold", color: "#D85357" }}
                >{`${formatPrice(
                  _.sumBy(fees, (ss) => ss.new_more_money_collecte)
                )} Đ`}</span>
              )}
              {dashboardReception.fee.loading && (
                <Spin style={{ marginTop: 8 }} />
              )}
            </Row>
          </Col>
        </Row>
        <Row style={{ marginTop: 16 }}>
          <Table
            dataSource={fees}
            columns={columns}
            bordered
            rowKey="id"
            scroll={{ x: 768 }}
            loading={dashboardReception.fee.loading}
            // expandedRowRender={(record) => <span style={{ whiteSpace: 'pre-wrap' }} >{record.description}</span>}
          />
        </Row>
      </Col>
    );
  };

  _renderPhieuThu = () => {
    const { dashboardReception, buildingCluster } = this.props;

    let { service_bill_template } = buildingCluster.data || {};
    let service_bill_templateJSON = null;
    try {
      service_bill_templateJSON = service_bill_template;
    } catch (error) {
      console.log("error", error);
    }

    const {
      apartment_id,
      payment_date,
      execution_date,
      fees,
      payer_name,
      type_payment,
    } = this.state;
    let currentApartment = dashboardReception.apartment.items.find(
      (ii) => ii.id == apartment_id
    );

    let total_new_money_collected = _.sumBy(
      fees,
      (ss) => ss.new_money_collected
    );

    let groupService = {};
    fees.forEach((fee) => {
      if (fee.new_money_collected > 0) {
        groupService[fee.service_map_management_service_name] =
          (groupService[fee.service_map_management_service_name] || 0) +
          fee.new_money_collected;
      }
    });

    let groupServiceString = Object.keys(groupService)
      .map((key) => {
        return `${key} (${formatPrice(groupService[key])}đ)`;
      })
      .join(", ");
    let obj = {
      fees: parseBillToView(
        fees.filter((rr) => rr.new_money_collected != 0),
        "vi"
      ).map((rr, index) => {
        if (
          !!service_bill_templateJSON &&
          !!service_bill_templateJSON.jsx_row
        ) {
          return (
            <p className="c0" key={`row-${index}`}>
              <span className="c1">
                Thu {rr.service_map_management_service_name} {rr.fee_of_month}:{" "}
                {rr.new_money_collected} &#273;&#7891;ng
              </span>
            </p>
          );
        }
        return null;
      }),
      fees_en: parseBillToView(
        fees.filter((rr) => rr.new_money_collected != 0),
        "en"
      ).map((rr, index) => {
        if (
          !!service_bill_templateJSON &&
          !!service_bill_templateJSON.jsx_row
        ) {
          return (
            <p className="c0" key={`row-${index}`}>
              <span
                className="c1"
                style={{
                  color: "#a8a3a3",
                  fontWeight: 400,
                  textDecoration: "none",
                  verticalAlign: "baseline",
                  fontSize: "10pt",
                  fontFamily: "Arial",
                  fontStyle: "italic",
                }}
              >
                Collect {rr.service_map_management_service_name}{" "}
                {rr.fee_of_month}: {rr.new_money_collected} dong
              </span>
            </p>
          );
        }
        return null;
      }),
      number: dashboardReception.createData
        ? dashboardReception.createData.number
        : "XXXX",
      payer_name: this.state.payer_name.toUpperCase(),
      apartment_name: currentApartment
        ? `${currentApartment.name} (${currentApartment.parent_path})`
        : "",
      payment_date: payment_date ? payment_date.format("DD/MM/YYYY") : "",
      execution_date: ` .....${execution_date.format("DD")}..... `,
      execution_month: ` .....${execution_date.format("MM")}..... `,
      execution_year: ` .....${execution_date.format("YYYY")}.....`,
      execution_date_short: execution_date
        ? execution_date.format("DD/MM/YYYY HH:mm")
        : "",
      total_more_money_collecte: formatPrice(
        _.sumBy(fees, (rr) => rr.more_money_collecte || 0)
      ),
      total_new_money_collected:
        total_new_money_collected >= 0
          ? formatPrice(total_new_money_collected)
          : "",
      total_new_money_collected_string: `${
        total_new_money_collected >= 0
          ? DocTienBangChu(total_new_money_collected)
          : ""
      } đồng`,
      total_new_money_collected_string_en: `${
        total_new_money_collected >= 0
          ? convertNumberToWords(total_new_money_collected)
          : ""
      } dong`,
      groupServiceString,
      type_payment: config.TYPE_PAYMENT.find((item) => item.id === type_payment)
        .name,
      type_payment_en: config.TYPE_PAYMENT.find(
        (item) => item.id === type_payment
      ).name_en,
    };

    return (
      <Col md={24} lg={12} className="phieuThu">
        <span style={{ fontSize: 18, fontWeight: "bold", color: "black" }}>
          {this.props.intl.formatMessage(messages.receipts)}
        </span>
        <Row style={{ marginBottom: 16, marginTop: 22 }}>
          <Button
            style={{ marginRight: 10 }}
            onClick={this._createAndPrint}
            disabled={
              fees.length == 0 ||
              !!dashboardReception.createData ||
              total_new_money_collected < 0 ||
              !payer_name.trim().length
            }
          >
            {this.props.intl.formatMessage(messages.paymentContent)} (
            <span>F1</span>)
          </Button>
          <Button
            style={{ marginRight: 10 }}
            onClick={this._createBill}
            disabled={
              fees.length == 0 ||
              !!dashboardReception.createData ||
              total_new_money_collected < 0 ||
              !payer_name.trim().length
            }
          >
            {this.props.intl.formatMessage(messages.createPV)} (F2)
          </Button>
          <Button
            style={{ marginRight: 10 }}
            onClick={() => {
              this._printBill();
            }}
            disabled={fees.length == 0 || !dashboardReception.createData}
          >
            {this.props.intl.formatMessage(messages.printPV)} (F3)
          </Button>
          <Button
            style={{
              marginRight: 10,
              marginTop: window.innerWidth > 1440 ? null : 10,
            }}
            onClick={this._clearForm}
          >
            {this.props.intl.formatMessage(messages.completed)} (ESC)
          </Button>
        </Row>
        <Row style={{ marginBottom: 16, marginTop: 22, marginLeft: 16 }}>
          <img
            style={{ width: 160 }}
            alt="logo_company"
            src={config.logoPath4}
          />
        </Row>
        {!service_bill_templateJSON && (
          <Row>
            <Col
              style={{
                textAlign: "center",
                fontWeight: "bold",
                fontSize: 18,
                marginTop: 24,
              }}
            >
              <span>{this.props.intl.formatMessage(messages.contentPV)}</span>
            </Col>
          </Row>
        )}
        {!!service_bill_templateJSON &&
          this._renderBill(1, obj, service_bill_templateJSON)}
        {!!service_bill_templateJSON &&
          this._renderBill(2, obj, service_bill_templateJSON, "none")}
        {!!service_bill_templateJSON &&
          this._renderBill(3, obj, service_bill_templateJSON, "none")}
        {!!service_bill_templateJSON && (
          <_JSXStyle id="123">{`
              ${service_bill_templateJSON.style_string}
            `}</_JSXStyle>
        )}
      </Col>
    );
  };

  _renderBill = (
    index,
    obj,
    service_bill_templateJSON,
    display = "visible"
  ) => {
    return (
      <Row id={`lien${index}`} style={{ display }}>
        {!!service_bill_templateJSON && (
          <JsxParser
            bindings={{
              ...obj,
              bill_name: `${index}`,
            }}
            jsx={service_bill_templateJSON.jsx}
          />
        )}
      </Row>
    );
  };

  render() {
    const { dashboardReception } = this.props;
    let { formatMessage } = this.props.intl;

    return (
      <>
        <Page inner style={{ minHeight: 0 }}>
          <Row className="DashboardReception">
            {this._renderInfo()}
            {this._renderPhieuThu()}
            {dashboardReception.creating && (
              <Loader backgroundColor={"rgba(255, 255, 255, 0.6)"} />
            )}
          </Row>
        </Page>
        <Row style={{ height: 24 }} />
        <DashboardBills location={this.props.location} />
      </>
    );
  }
}

DashboardReception.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  dashboardReception: makeSelectDashboardReception(),
  userDetail: selectUserDetail(),
  buildingCluster: selectBuildingCluster(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "dashboardReception", reducer });
const withSaga = injectSaga({ key: "dashboardReception", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(hotkeys(DashboardReception)));
