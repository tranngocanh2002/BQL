/* eslint-disable no-undef */
/**
 *
 * DashboardInvoiceBill
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
  DatePicker,
  Icon,
  Input,
  Popover,
  Radio,
  Row,
  Select,
  Spin,
  Table,
} from "antd";
import _ from "lodash";
import moment from "moment";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import {
  DocTienBangChu,
  config,
  convertNumberToWords,
  formatPrice,
  parseInvoiceBillToView,
} from "../../../utils";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectDashboardInvoiceBill from "./selectors";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { injectIntl } from "react-intl";
import JsxParser from "react-jsx-parser";
import { hotkeys } from "react-keyboard-shortcuts";
import _JSXStyle from "styled-jsx/style";
import Loader from "../../../components/Loader/Loader";
import {
  selectBuildingCluster,
  selectUserDetail,
} from "../../../redux/selectors";
import { COMPANY_NAME } from "../../../utils/constants";
import DashboardInvoiceList from "../DashboardInvoiceList";
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
export class DashboardInvoiceBill extends React.PureComponent {
  constructor(props) {
    super(props);
    let { ids } = props.location.state || {};
    this.state = {
      ids,
      apartment_id: undefined,
      payer_name: "",
      bank_name: "",
      bank_holders: "",
      bank_account: "",
      fees: [],
      total_count: {
        total_money_collected: 0,
        total_price: 0,
      },
      type_payment: 0,
      payment_date: moment(),
      execution_date: moment(),
      number: "XXXX",
      feeChecked: [],
    };
    this._onSearch = _.debounce(this.onSearch, 500);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this._onSearch("");
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

  checkEmptyAccount = () => {
    const { type_payment, bank_name, bank_holders, bank_account } = this.state;
    let empty = false;
    if (
      type_payment == 1 &&
      (!bank_name.trim().length ||
        !bank_holders.trim().length ||
        !bank_account.trim().length)
    ) {
      empty = true;
    }
    return empty;
  };

  onRowKeysChange = (e) => {
    this.setState({
      ids: [e.target.value],
      feeChecked: this.state.fees.filter((ff) => ff.id == e.target.value),
    });
  };

  _printBill = () => {
    const { dashboardInvoiceBill, buildingCluster } = this.props;

    let { service_bill_invoice_template } = buildingCluster.data || {};
    let service_bill_invoice_templateJSON = null;
    try {
      service_bill_invoice_templateJSON = service_bill_invoice_template;
    } catch (error) {
      console.log("error", error);
    }

    const { fees } = this.state;
    if (fees.length == 0 || !dashboardInvoiceBill.createData) {
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
    ${
      service_bill_invoice_templateJSON
        ? service_bill_invoice_templateJSON.style_string
        : ""
    }
    </style>
    <div style="margin-left: 16px">
                  <img src="${
                    config.logoPath4
                  }" alt="" width="120" height="60"> </div>
    <div class="jsx-parser" >
    ${service_bill_invoice_templateJSON ? $("#lien1")[0].innerHTML : ""}
    <div style="page-break-before:always" />
    <div style="margin-left: 16px">
                  <img src="${
                    config.logoPath4
                  }" alt="" width="120" height="60"> </div>
    ${service_bill_invoice_templateJSON ? $("#lien2")[0].innerHTML : ""}
    <div style="page-break-before:always" />
    <div style="margin-left: 16px">
                  <img src="${
                    config.logoPath4
                  }" alt="" width="120" height="60"> </div>
    ${service_bill_invoice_templateJSON ? $("#lien3")[0].innerHTML : ""}
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
    if (
      this.props.dashboardInvoiceBill.fee.loading !=
        nextProps.dashboardInvoiceBill.fee.loading &&
      !nextProps.dashboardInvoiceBill.fee.loading
    ) {
      let { ids } = this.state;
      let fees = nextProps.dashboardInvoiceBill.fee.items.map((ff) => {
        return {
          ...ff,
          new_money_collected: ff.money_collected,
        };
      });
      let newFees = fees;
      if (!!ids && !!ids.length) {
        const fee_checked = fees.filter((e) => e.id == ids[0]);
        const oldFees = fees.filter((e) => e.id != ids[0]);
        newFees = [...fee_checked, ...oldFees];
        this.setState({
          feeChecked: fee_checked,
        });
      }
      this.setState({
        fees: newFees,
        total_count: { ...nextProps.dashboardInvoiceBill.fee.total_count },
      });
    }

    let { apartment_id } = nextProps.location.state || {};
    if (
      this.props.dashboardInvoiceBill.apartment.loading !=
        nextProps.dashboardInvoiceBill.apartment.loading &&
      !nextProps.dashboardInvoiceBill.apartment.loading &&
      !!apartment_id &&
      // this.state.payer_name == "" &&
      !this._theFirstTime
    ) {
      this._theFirstTime = true;
      let apartment = nextProps.dashboardInvoiceBill.apartment.items.find(
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
    const { dashboardInvoiceBill, userDetail } = this.props;
    const {
      apartment_id,
      payment_date,
      execution_date,
      feeChecked,
      payer_name,
      bank_name,
      bank_holders,
      bank_account,
      type_payment,
    } = this.state;
    let total_new_money_collected = _.sumBy(
      feeChecked,
      (ss) => ss.new_money_collected
    );

    if (
      feeChecked.length == 0 ||
      !!dashboardInvoiceBill.createData ||
      total_new_money_collected <= 0 ||
      this.checkEmptyAccount()
    ) {
      return;
    }
    this.props.dispatch(
      createOrder({
        apartment_id,
        management_user_name: userDetail.first_name,
        payer_name,
        bank_name,
        bank_holders,
        bank_account,
        type_payment,
        description: "string",
        service_payment_fee_id: feeChecked[0].id,
        payment_date: payment_date.unix(),
        execution_date: execution_date.unix(),
        callback: () => {
          this._printBill();
          this.props.history.push(
            `/main/finance/invoice-bill?page=1&refresh=${Date.now()}`
          );
        },
      })
    );
  };
  _createBill = () => {
    const { dashboardInvoiceBill, userDetail } = this.props;
    const {
      apartment_id,
      payment_date,
      execution_date,
      feeChecked,
      payer_name,
      type_payment,
      bank_name,
      bank_holders,
      bank_account,
    } = this.state;
    let total_new_money_collected = _.sumBy(
      feeChecked,
      (ss) => ss.new_money_collected
    );

    if (
      feeChecked.length == 0 ||
      !!dashboardInvoiceBill.createData ||
      total_new_money_collected <= 0
    ) {
      return;
    }

    this.props.dispatch(
      createOrder({
        apartment_id,
        management_user_name: userDetail.first_name,
        payer_name,
        type_payment,
        bank_name,
        bank_holders,
        bank_account,
        description: "string",
        service_payment_fee_id: feeChecked[0].id,
        payment_date: payment_date.unix(),
        execution_date: execution_date.unix(),
        callback: (id) => {
          // this.props.history.push(
          //   `/main/finance/invoice-bill?page=1&refresh=${Date.now()}`
          // );
          this.props.history.push(`/main/finance/invoice-bill/bill/${id}`);
        },
      })
    );
  };

  _clearForm = () => {
    this.props.dispatch(clearForm());
    this.setState(
      {
        apartment_id: undefined,
        payer_name: "",
        bank_name: "",
        bank_holders: "",
        bank_account: "",
        fees: [],
        feeChecked: [],
        total_count: {
          total_money_collected: 0,
          total_price: 0,
        },
        type_payment: 0,
        payment_date: moment(),
        execution_date: moment(),
        number: "XXXX",
      },
      () => {
        this.props.history.push("/main/finance/invoice-bill");
        this._onSearch("");
      }
    );
  };

  _renderInfo = () => {
    const { dashboardInvoiceBill, userDetail } = this.props;
    const { fees, type_payment, bank_name, bank_holders, bank_account } =
      this.state;

    const columns = [
      {
        title: <span />,
        dataIndex: "info",
        key: "info",
        render: (text, record) => {
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
        title: <span className="nameTable">#</span>,
        dataIndex: "id",
        key: "id",
        render: (id) => (
          <Radio
            checked={
              !!this.state.ids &&
              !!this.state.ids.length &&
              id == this.state.ids[0]
            }
            value={id}
            key={`group-${id}`}
            onChange={this.onRowKeysChange}
          />
        ),
      },
      {
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.month)}
          </span>
        ),
        dataIndex: "fee_month",
        key: "fee_month",
        render: (text, record) => (
          <span>{moment.unix(record.fee_of_month).format("MM/YYYY")}</span>
        ),
      },
      {
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.serviceType)}
          </span>
        ),
        dataIndex: "service_map_management_service_name",
        key: "service_map_management_service_name",
      },
      {
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.received)}
          </span>
        ),
        dataIndex: "money_collected",
        key: "money_collected",
        render: (text) => {
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
            </span>
          );
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
                  loading={dashboardInvoiceBill.apartment.loading}
                  showSearch
                  placeholder={this.props.intl.formatMessage(
                    messages.choseProperty
                  )}
                  optionFilterProp="children"
                  notFoundContent={
                    dashboardInvoiceBill.apartment.loading ? (
                      <Spin size="small" />
                    ) : null
                  }
                  onSearch={this._onSearch}
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
                          dashboardInvoiceBill.apartment.items.find(
                            (ii) => ii.id == value
                          ) || { resident_user_name: "" }
                        ).resident_user_name,
                        fees: [],
                        total_count: {
                          total_money_collected: 0,
                          total_price: 0,
                        },
                        type_payment: 0,
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
                      this._onSearch("");
                    }
                  }}
                >
                  {dashboardInvoiceBill.apartment.items.map((gr) => {
                    return (
                      <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>
                        {`${gr.name} (${gr.parent_path})${
                          gr.status == 0
                            ? ` - ${this.props.intl.formatMessage(
                                messages.nothing
                              )}`
                            : ""
                        }`}
                      </Select.Option>
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
                    window.innerWidth > 1280
                      ? (
                          dashboardInvoiceBill.apartment.items.find(
                            (ii) => ii.id == this.state.apartment_id
                          ) || { resident_user_name: "" }
                        ).resident_user_name
                      : (
                          dashboardInvoiceBill.apartment.items.find(
                            (ii) => ii.id == this.state.apartment_id
                          ) || { resident_user_name: "" }
                        ).resident_user_name.substring(0, 15)
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
                {this.props.intl.formatMessage(messages.payer)}:&ensp;
              </Col>
              <Col {...colContent}>
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
                {this.props.intl.formatMessage(messages.receiver)}:&ensp;
              </Col>
              <Col {...colContent}>
                <Select
                  style={{ width: "100%" }}
                  loading={
                    this.state.apartment_id &&
                    dashboardInvoiceBill.members.loading
                  }
                  placeholder={this.props.intl.formatMessage(messages.receiver)}
                  optionFilterProp="children"
                  notFoundContent={
                    dashboardInvoiceBill.members.loading ? (
                      <Spin size="small" />
                    ) : null
                  }
                  value={this.state.payer_name}
                  onChange={(value) => {
                    let name = dashboardInvoiceBill.members.lst.filter(
                      (member) => member.phone === value
                    );
                    this.setState({
                      payer_name: name[0].first_name,
                    });
                  }}
                  disabled={!this.state.apartment_id}
                >
                  {dashboardInvoiceBill.members.lst
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
                {this.props.intl.formatMessage(messages.dateSpending)}:&ensp;
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
                    return (
                      current &&
                      current < moment().subtract(1, "day").endOf("day")
                    );
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
                {this.props.intl.formatMessage(messages.paymentForm)}:&ensp;
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
                  <Select.Option value={0} key="0">
                    {this.props.intl.formatMessage(messages.cash)}
                  </Select.Option>
                  <Select.Option value={1} key="1">
                    {this.props.intl.formatMessage(messages.transfer)}
                  </Select.Option>
                </Select>
              </Col>
            </Row>
          </Col>
          {type_payment == 1 && (
            <Col {...colWrapper}>
              <Row type="flex" align="middle">
                <Col
                  md={10}
                  lg={8}
                  style={{
                    textAlign: "left",
                    color: bank_holders.trim().length ? null : "red",
                  }}
                >
                  {this.props.intl.formatMessage(messages.accountHolder)}:&ensp;
                </Col>
                <Col {...colContent}>
                  <Input
                    style={{ width: "100%" }}
                    value={bank_holders}
                    onChange={(e) => {
                      this.setState({
                        bank_holders: e.target.value,
                      });
                    }}
                  />
                </Col>
              </Row>
            </Col>
          )}
          {type_payment == 1 && (
            <Col {...colWrapper}>
              <Row type="flex" align="middle">
                <Col
                  md={10}
                  lg={8}
                  style={{
                    textAlign: "left",
                    color: bank_name.trim().length ? null : "red",
                  }}
                >
                  {this.props.intl.formatMessage(messages.bank)}:&ensp;
                </Col>
                <Col {...colContent}>
                  <Input
                    style={{ width: "100%" }}
                    value={bank_name}
                    onChange={(e) => {
                      this.setState({
                        bank_name: e.target.value,
                      });
                    }}
                  />
                </Col>
              </Row>
            </Col>
          )}
          {type_payment == 1 && (
            <Col {...colWrapper}>
              <Row type="flex" align="middle">
                <Col
                  md={10}
                  lg={8}
                  style={{
                    textAlign: "left",
                    color: bank_account.trim().length ? null : "red",
                  }}
                >
                  {this.props.intl.formatMessage(messages.accountNumber)}:&ensp;
                </Col>
                <Col {...colContent}>
                  <Input
                    style={{ width: "100%" }}
                    value={bank_account}
                    onChange={(e) => {
                      this.setState({
                        bank_account: e.target.value,
                      });
                    }}
                  />
                </Col>
              </Row>
            </Col>
          )}
        </Row>
        <Row type="flex" justify="center" style={{ marginTop: 16 }}>
          <Col span={24}>
            <span
              style={{
                fontSize: 18,
                fontWeight: "bold",
                color: "black",
              }}
            >
              {this.props.intl.formatMessage(messages.feeList)}
            </span>
            <Table
              style={{ marginTop: 16 }}
              dataSource={fees}
              columns={columns}
              locale={{
                emptyText: this.props.intl.formatMessage(messages.emptyData),
              }}
              bordered
              rowKey="id"
              scroll={{ x: 750 }}
              loading={dashboardInvoiceBill.fee.loading}
            />
          </Col>
        </Row>
      </Col>
    );
  };

  _renderPhieuChi = () => {
    const { dashboardInvoiceBill, buildingCluster } = this.props;

    let { service_bill_invoice_template } = buildingCluster.data || {};
    let service_bill_invoice_templateJSON = null;
    try {
      service_bill_invoice_templateJSON = service_bill_invoice_template;
    } catch (error) {
      console.log("error", error);
    }

    const {
      apartment_id,
      payment_date,
      execution_date,
      fees,
      feeChecked,
      type_payment,
    } = this.state;
    let currentApartment = dashboardInvoiceBill.apartment.items.find(
      (ii) => ii.id == apartment_id
    );

    let total_new_money_collected = _.sumBy(
      feeChecked,
      (ss) => ss.new_money_collected
    );

    let groupService = {};
    feeChecked.forEach((fee) => {
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
      fees: parseInvoiceBillToView(feeChecked).map((rr, index) => {
        if (
          !!service_bill_invoice_templateJSON &&
          !!service_bill_invoice_templateJSON.jsx_row
        ) {
          rr.new_money_collected = rr.new_money_collected.replace(/,/g, ".");
          return (
            <JsxParser
              key={`row-${index}`}
              renderInWrapper={false}
              bindings={{
                rr,
              }}
              jsx={`
                <p class="c0"><span class="c1">Chi {rr.service_map_management_service_name} {rr.fee_of_month}: {rr.new_money_collected} &#273;&#7891;ng</span></p>
              `}
            />
          );
        }
        return null;
      }),
      fees_en: parseInvoiceBillToView(feeChecked).map((rr, index) => {
        if (
          !!service_bill_invoice_templateJSON &&
          !!service_bill_invoice_templateJSON.jsx_row
        ) {
          rr.new_money_collected = rr.new_money_collected.replace(/,/g, ".");
          return (
            <JsxParser
              key={`row-${index}`}
              renderInWrapper={false}
              bindings={{
                rr,
              }}
              jsx={`
                <p class="c0"><span class="c1" style="
                  color: #a8a3a3;
                  font-weight: 400;
                  text-decoration: none;
                  vertical-align: baseline;
                  font-size: 9pt;
                  font-family: 'Arial';
                  font-style: italic;
                ">Pay {rr.service_map_management_service_name_en} {rr.fee_of_month}: {rr.new_money_collected} dong</span></p>
              `}
            />
          );
        }
        return null;
      }),
      number: dashboardInvoiceBill.createData
        ? dashboardInvoiceBill.createData.number
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
      total_new_money_collected:
        total_new_money_collected > 0
          ? formatPrice(total_new_money_collected)
          : "",
      total_new_money_collected_string: `${
        total_new_money_collected > 0
          ? DocTienBangChu(total_new_money_collected)
          : ""
      } đồng`,
      total_new_money_collected_string_en: `${
        total_new_money_collected > 0
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
      <Col Col md={24} lg={12} className="phieuChi">
        <span style={{ fontSize: 18, fontWeight: "bold", color: "black" }}>
          {this.props.intl.formatMessage(messages.paymentVoucher)}
        </span>
        <Row style={{ marginBottom: 16, marginTop: 22 }}>
          <Button
            style={{ marginRight: 10 }}
            onClick={this._createAndPrint}
            disabled={
              feeChecked.length == 0 ||
              !!dashboardInvoiceBill.createData ||
              total_new_money_collected <= 0 ||
              this.checkEmptyAccount()
            }
          >
            {this.props.intl.formatMessage(messages.paymentContent)} (
            <span>F1</span>)
          </Button>
          <Button
            style={{ marginRight: 10 }}
            onClick={this._createBill}
            disabled={
              feeChecked.length == 0 ||
              !!dashboardInvoiceBill.createData ||
              total_new_money_collected <= 0 ||
              this.checkEmptyAccount()
            }
          >
            {this.props.intl.formatMessage(messages.createPV)} (F2)
          </Button>
          <Button
            style={{ marginRight: 10 }}
            onClick={() => {
              this._printBill();
            }}
            disabled={fees.length == 0 || !dashboardInvoiceBill.createData}
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
        {!service_bill_invoice_templateJSON && (
          <Row>
            {" "}
            <Col
              style={{
                textAlign: "center",
                fontWeight: "bold",
                fontSize: 18,
                marginTop: 24,
              }}
            >
              <span>
                {this.props.intl.formatMessage(messages.contentPV, {
                  COMPANY_NAME: COMPANY_NAME,
                })}
              </span>
            </Col>
          </Row>
        )}
        {!!service_bill_invoice_templateJSON &&
          this._renderBill(1, obj, service_bill_invoice_templateJSON)}
        {!!service_bill_invoice_templateJSON &&
          this._renderBill(2, obj, service_bill_invoice_templateJSON, "none")}
        {!!service_bill_invoice_templateJSON &&
          this._renderBill(3, obj, service_bill_invoice_templateJSON, "none")}
        {!!service_bill_invoice_templateJSON && (
          <_JSXStyle id="123">{`
              ${service_bill_invoice_templateJSON.style_string}
            `}</_JSXStyle>
        )}
      </Col>
    );
  };

  _renderBill = (
    index,
    obj,
    service_bill_invoice_templateJSON,
    display = "visible"
  ) => {
    return (
      <Row id={`lien${index}`} style={{ display }}>
        {!!service_bill_invoice_templateJSON && (
          <JsxParser
            bindings={{
              ...obj,
              bill_name: `${index}`,
            }}
            jsx={service_bill_invoice_templateJSON.jsx}
          />
        )}
      </Row>
    );
  };

  render() {
    const { dashboardInvoiceBill } = this.props;
    let { formatMessage } = this.props.intl;

    return (
      <>
        <Page inner style={{ minHeight: 0 }}>
          <Row className="DashboardInvoiceBill">
            {this._renderInfo()}
            {this._renderPhieuChi()}
            {dashboardInvoiceBill.creating && (
              <Loader backgroundColor={"rgba(255, 255, 255, 0.6)"} />
            )}
          </Row>
        </Page>
        <Row style={{ height: 24 }} />
        <DashboardInvoiceList location={this.props.location} />
      </>
    );
  }
}

DashboardInvoiceBill.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  dashboardInvoiceBill: makeSelectDashboardInvoiceBill(),
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

const withReducer = injectReducer({ key: "dashboardInvoiceBill", reducer });
const withSaga = injectSaga({ key: "dashboardInvoiceBill", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(hotkeys(DashboardInvoiceBill)));
