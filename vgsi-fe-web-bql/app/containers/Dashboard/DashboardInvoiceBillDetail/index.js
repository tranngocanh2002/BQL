/**
 *
 * DashboardInvoiceBillDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Exception } from "ant-design-pro";
import {
  Button,
  Col,
  DatePicker,
  Input,
  Modal,
  Row,
  Select,
  Table,
} from "antd";
import $ from "jquery";
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
  notificationBar,
  parseInvoiceBillToView,
} from "../../../utils";
import {
  blockBill,
  cancelBill,
  changeStatusBill,
  defaultAction,
  fetchDetailBill,
  updateBill,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectDashboardInvoiceBillDetail from "./selectors";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { injectIntl } from "react-intl";
import JsxParser from "react-jsx-parser";
import { hotkeys } from "react-keyboard-shortcuts";
import _JSXStyle from "styled-jsx/style";
import WithRole from "../../../components/WithRole";
import { selectBuildingCluster } from "../../../redux/selectors";
import messages from "../messages";
import("./index.less");
const styleString = `
.jsx-parser ol {
  margin: 0;
  padding: 0
}

.jsx-parser table td,
table th {
  padding: 0
}

.jsx-parser .c7c20{
  min-width: 30%;
  color: #000000;
  font-weight: 700;
  text-decoration: none;
  vertical-align: baseline;
  font-size: 10pt;
  font-family: "Arial";
  font-style: normal;
  text-align: right;
}
.jsx-parser .c7 {
  border-right-style: solid;
  padding-top: 6pt;
  border-top-width: 0pt;
  border-bottom-color: null;
  border-right-width: 0pt;
  padding-left: 0pt;
  border-left-color: null;
  padding-bottom: 6pt;
  line-height: 1.0;
  border-right-color: null;
  border-left-width: 0pt;
  border-top-style: solid;
  background-color: #ffffff;
  border-left-style: solid;
  border-bottom-width: 0pt;
  border-top-color: null;
  border-bottom-style: solid;
  orphans: 2;
  widows: 2;
  padding-right: 0pt;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.jsx-parser .c9 {
  border-right-style: solid;
  padding-top: 6pt;
  border-top-width: 0pt;
  border-bottom-color: null;
  border-right-width: 0pt;
  padding-left: 0pt;
  border-left-color: null;
  padding-bottom: 2pt;
  line-height: 1.0;
  border-right-color: null;
  border-left-width: 0pt;
  border-top-style: solid;
  border-left-style: solid;
  border-bottom-width: 0pt;
  border-top-color: null;
  border-bottom-style: solid;
  orphans: 2;
  widows: 2;
  text-align: center;
  padding-right: 0pt
}
.jsx-parser .c9999 {
  border-right-style: solid;
  padding-top: 1pt;
  border-top-width: 0pt;
  border-bottom-color: null;
  border-right-width: 0pt;
  padding-left: 0pt;
  border-left-color: null;
  padding-bottom: 1pt;
  line-height: 1.0;
  border-right-color: null;
  border-left-width: 0pt;
  border-top-style: solid;
  border-left-style: solid;
  border-bottom-width: 0pt;
  border-top-color: null;
  border-bottom-style: solid;
  orphans: 2;
  widows: 2;
  text-align: center;
  padding-right: 0pt
}

.jsx-parser .c3 {
  border-right-style: solid;
  padding: 2pt 2pt 2pt 2pt;
  border-bottom-color: #dddddd;
  border-top-width: 0pt;
  border-right-width: 0pt;
  border-left-color: #dddddd;
  vertical-align: top;
  border-right-color: #dddddd;
  border-left-width: 0pt;
  border-top-style: solid;
  background-color: #ffffff;
  border-left-style: solid;
  border-bottom-width: 0pt;
  width: 114.8pt;
  border-top-color: #dddddd;
  border-bottom-style: solid
}

.jsx-parser .c26 {
  border-right-style: solid;
  padding: 2pt 2pt 2pt 2pt;
  border-bottom-color: #dddddd;
  border-top-width: 0pt;
  border-right-width: 0pt;
  border-left-color: #dddddd;
  vertical-align: top;
  border-right-color: #dddddd;
  border-left-width: 0pt;
  border-top-style: solid;
  background-color: #ffffff;
  border-left-style: solid;
  border-bottom-width: 0pt;
  width: 86.2pt;
  border-top-color: #dddddd;
  border-bottom-style: solid
}

.jsx-parser .c27 {
  border-right-style: solid;
  padding: 2pt 2pt 2pt 2pt;
  border-bottom-color: #dddddd;
  border-top-width: 0pt;
  border-right-width: 0pt;
  border-left-color: #dddddd;
  vertical-align: top;
  border-right-color: #dddddd;
  border-left-width: 0pt;
  border-top-style: solid;
  background-color: #ffffff;
  border-left-style: solid;
  border-bottom-width: 0pt;
  width: 91.5pt;
  border-top-color: #dddddd;
  border-bottom-style: solid
}

.jsx-parser .c15 {
  border-right-style: solid;
  padding: 2pt 2pt 2pt 2pt;
  border-bottom-color: #dddddd;
  border-top-width: 0pt;
  border-right-width: 0pt;
  border-left-color: #dddddd;
  vertical-align: top;
  border-right-color: #dddddd;
  border-left-width: 0pt;
  border-top-style: solid;
  background-color: #ffffff;
  border-left-style: solid;
  border-bottom-width: 0pt;
  width: 68.2pt;
  border-top-color: #dddddd;
  border-bottom-style: solid
}

.jsx-parser .c6 {
  border-right-style: solid;
  padding: 5pt 5pt 5pt 5pt;
  border-bottom-color: #666666;
  border-top-width: 1pt;
  border-right-width: 1pt;
  border-left-color: #666666;
  vertical-align: top;
  border-right-color: #666666;
  border-left-width: 1pt;
  border-top-style: solid;
  border-left-style: solid;
  border-bottom-width: 1pt;
  width: 330pt;
  border-top-color: #666666;
  border-bottom-style: solid
}

.jsx-parser .c29 {
  border-right-style: solid;
  padding: 5pt 5pt 5pt 5pt;
  border-bottom-color: #000000;
  border-top-width: 0pt;
  border-right-width: 0pt;
  border-left-color: #000000;
  vertical-align: top;
  border-right-color: #000000;
  border-left-width: 0pt;
  border-top-style: solid;
  border-left-style: solid;
  border-bottom-width: 0pt;
  width: 243pt;
  border-top-color: #000000;
  border-bottom-style: solid
}

.jsx-parser .c30 {
  border-right-style: solid;
  padding: 2pt 2pt 2pt 2pt;
  border-bottom-color: #dddddd;
  border-top-width: 0pt;
  border-right-width: 0pt;
  border-left-color: #dddddd;
  vertical-align: top;
  border-right-color: #dddddd;
  border-left-width: 0pt;
  border-top-style: solid;
  border-left-style: solid;
  border-bottom-width: 0pt;
  width: 99pt;
  border-top-color: #dddddd;
  border-bottom-style: solid
}

.jsx-parser .c19 {
  border-right-style: solid;
  padding: 5pt 5pt 5pt 5pt;
  border-bottom-color: #000000;
  border-top-width: 0pt;
  border-right-width: 0pt;
  border-left-color: #000000;
  vertical-align: top;
  border-right-color: #000000;
  border-left-width: 0pt;
  border-top-style: solid;
  border-left-style: solid;
  border-bottom-width: 0pt;
  width: 255pt;
  border-top-color: #000000;
  border-bottom-style: solid
}

.jsx-parser .c14 {
  border-right-style: solid;
  padding: 5pt 5pt 5pt 5pt;
  border-bottom-color: #666666;
  border-top-width: 1pt;
  border-right-width: 1pt;
  border-left-color: #666666;
  vertical-align: top;
  border-right-color: #666666;
  border-left-width: 1pt;
  border-top-style: solid;
  border-left-style: solid;
  border-bottom-width: 1pt;
  width: 150pt;
  border-top-color: #666666;
  border-bottom-style: solid
}

.jsx-parser .c1 {
  color: #000000;
  font-weight: 400;
  text-decoration: none;
  vertical-align: baseline;
  font-size: 10pt;
  font-family: "Arial";
  font-style: normal
}

.jsx-parser .c25 {
  color: #000000;
  font-weight: 400;
  text-decoration: none;
  vertical-align: baseline;
  font-size: 9pt;
  font-family: "Arial";
  font-style: normal
}

.jsx-parser .c20 {
  color: #000000;
  font-weight: 700;
  text-decoration: none;
  vertical-align: baseline;
  font-size: 10pt;
  font-family: "Arial";
  font-style: normal
}

.jsx-parser .c2 {
  color: #000000;
  font-weight: 700;
  text-decoration: none;
  vertical-align: baseline;
  font-size: 10.5pt;
  font-family: "Arial";
  font-style: normal
}

.jsx-parser .c16 {
  color: #000000;
  font-weight: 400;
  text-decoration: none;
  vertical-align: baseline;
  font-size: 10.5pt;
  font-family: "Arial";
  font-style: italic
}

.jsx-parser .c22 {
  color: #000000;
  font-weight: 700;
  text-decoration: none;
  vertical-align: baseline;
  font-size: 9pt;
  font-family: "Arial";
  font-style: normal
}

.jsx-parser .c10 {
  color: #000000;
  font-weight: 400;
  text-decoration: none;
  vertical-align: baseline;
  font-size: 11pt;
  font-family: "Arial";
  font-style: normal
}

.jsx-parser .c17 {
  padding-top: 0pt;
  padding-bottom: 0pt;
  line-height: 1.15;
  orphans: 2;
  widows: 2;
  text-align: left
}

.jsx-parser .c5 {
  padding-top: 0pt;
  padding-bottom: 0pt;
  line-height: 1.0;
  text-align: center
}

.jsx-parser .c0 {
  padding-top: 0pt;
  padding-bottom: 0pt;
  line-height: 1.0;
  text-align: left
}

.jsx-parser .c24 {
  width:100%;
  border-spacing: 0;
  border-collapse: collapse;
}

.jsx-parser .c18 {
  width:100%;
  border-spacing: 0;
  border-collapse: collapse;
}

.jsx-parser .c23 {
  width:100%;
  border-spacing: 0;
  border-collapse: collapse;
}

.jsx-parser .c13 {
  font-size: 16pt;
  font-family: "Arial";
  font-weight: 700;
}

.jsx-parser .c8 {
  background-color: #ffffff;
  padding: 0pt 8pt 0pt 8pt
}

.jsx-parser .c11 {
  background-color: #ffffff;
  height: 11pt
}

.jsx-parser .c21 {
  height: 11pt
}

.jsx-parser .c4 {
  height: 24pt
}

.jsx-parser .c28 {
  background-color: #ffffff
}

.jsx-parser .c12 {
  height: 0pt
}

.jsx-parser .title {
  padding-top: 0pt;
  color: #000000;
  font-size: 26pt;
  padding-bottom: 3pt;
  font-family: "Arial";
  line-height: 1.15;
  page-break-after: avoid;
  orphans: 2;
  widows: 2;
  text-align: left
}

.jsx-parser .subtitle {
  padding-top: 0pt;
  color: #666666;
  font-size: 15pt;
  padding-bottom: 16pt;
  font-family: "Arial";
  line-height: 1.15;
  page-break-after: avoid;
  orphans: 2;
  widows: 2;
  text-align: left
}

.jsx-parser li {
  color: #000000;
  font-size: 11pt;
  font-family: "Arial"
}

.jsx-parser p {
  margin: 0;
  color: #000000;
  font-size: 11pt;
  font-family: "Arial"
}

.jsx-parser h1 {
  padding-top: 20pt;
  color: #000000;
  font-size: 20pt;
  padding-bottom: 6pt;
  font-family: "Arial";
  line-height: 1.15;
  page-break-after: avoid;
  orphans: 2;
  widows: 2;
  text-align: left
}

.jsx-parser h2 {
  padding-top: 18pt;
  color: #000000;
  font-size: 16pt;
  padding-bottom: 6pt;
  font-family: "Arial";
  line-height: 1.15;
  page-break-after: avoid;
  orphans: 2;
  widows: 2;
  text-align: left
}

.jsx-parser h3 {
  padding-top: 16pt;
  color: #434343;
  font-size: 14pt;
  padding-bottom: 4pt;
  font-family: "Arial";
  line-height: 1.15;
  page-break-after: avoid;
  orphans: 2;
  widows: 2;
  text-align: left
}

.jsx-parser h4 {
  padding-top: 14pt;
  color: #666666;
  font-size: 12pt;
  padding-bottom: 4pt;
  font-family: "Arial";
  line-height: 1.15;
  page-break-after: avoid;
  orphans: 2;
  widows: 2;
  text-align: left
}

.jsx-parser h5 {
  padding-top: 12pt;
  color: #666666;
  font-size: 11pt;
  padding-bottom: 4pt;
  font-family: "Arial";
  line-height: 1.15;
  page-break-after: avoid;
  orphans: 2;
  widows: 2;
  text-align: left
}

.jsx-parser h6 {
  padding-top: 12pt;
  color: #666666;
  font-size: 11pt;
  padding-bottom: 4pt;
  font-family: "Arial";
  line-height: 1.15;
  page-break-after: avoid;
  font-style: italic;
  orphans: 2;
  widows: 2;
  text-align: left
}
}`;

/* eslint-disable react/prefer-stateless-function */
export class DashboardInvoiceBillDetail extends React.PureComponent {
  state = {
    visibleConfirm: false,
    note: "",
    noteFor: "cancel",
    isEditting: false,
    payer_name: "",
    bank_name: "",
    bank_holders: "",
    bank_account: "",
    payment_date: moment(),
    execution_date: moment(),
    type_payment: 0,
    fees: [],
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchDetailBill({ id: this.props.match.params.id }));
  }

  componentWillReceiveProps(nextProps) {
    if (
      (this.props.dashboardInvoiceBillDetail.loading !=
        nextProps.dashboardInvoiceBillDetail.loading &&
        !nextProps.dashboardInvoiceBillDetail.loading &&
        !!nextProps.dashboardInvoiceBillDetail.data) ||
      (this.props.dashboardInvoiceBillDetail.updating !=
        nextProps.dashboardInvoiceBillDetail.updating &&
        !nextProps.dashboardInvoiceBillDetail.updating &&
        !!nextProps.dashboardInvoiceBillDetail.data)
    ) {
      const {
        payer_name,
        payment_date,
        execution_date,
        type_payment,
        service_payment_fees,
        bank_name,
        bank_holders,
        bank_account,
      } = nextProps.dashboardInvoiceBillDetail.data;
      this.setState({
        isEditting: false,
        payer_name,
        bank_name,
        bank_holders,
        bank_account,
        payment_date: moment.unix(payment_date),
        execution_date: moment.unix(execution_date),
        type_payment,
        fees: service_payment_fees.map((rr) => {
          return {
            ...rr,
            new_money_collected: rr.service_bill_item,
          };
        }),
      });
    }
  }

  hot_keys = {
    f3: {
      // combo from mousetrap
      priority: 1,
      handler: (e) => {
        e.preventDefault();
        e.stopPropagation();
        this._printBill();
      },
    },
  };

  checkEmptyAccount = () => {
    const { type_payment, payer_name, bank_name, bank_holders, bank_account } =
      this.state;
    let empty = false;
    if (
      (type_payment == 1 &&
        (!bank_name.trim().length ||
          !bank_holders.trim().length ||
          !bank_account.trim().length)) ||
      !payer_name.trim().length
    ) {
      empty = true;
    }
    return empty;
  };
  _renderInfo = () => {
    const { dashboardInvoiceBillDetail } = this.props;
    const { canceling, changingStatus, data, updating } =
      dashboardInvoiceBillDetail;

    const {
      isEditting,
      fees,
      payment_date,
      execution_date,
      payer_name,
      type_payment,
      bank_name,
      bank_holders,
      bank_account,
    } = this.state;
    const columns = [
      {
        title: <span>#</span>,
        dataIndex: "id",
        key: "id",
        width: 50,
        render: (text, record) => <span>{1}</span>,
      },
      {
        title: <span>{this.props.intl.formatMessage(messages.month)}</span>,
        dataIndex: "fee_month",
        key: "fee_month",
        render: (text, record) => (
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
      },
      {
        align: "right",
        title: <span>{this.props.intl.formatMessage(messages.spent)}</span>,
        dataIndex: "price",
        key: "price",
        render: (text, record) => {
          return (
            <span>
              {formatPrice(text)}
              &ensp;
            </span>
          );
        },
      },
    ];

    return (
      <Col span={12} style={{ paddingRight: 8 }}>
        <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_INVOICE_BILL]}>
          <>
            {data.status != 2 && (
              <Row style={{ marginBottom: 16 }}>
                {data.status != 10 && !isEditting && (
                  <Button
                    style={{ marginRight: 10 }}
                    disabled={canceling || changingStatus}
                    onClick={() => {
                      this.setState({
                        isEditting: true,
                      });
                    }}
                  >
                    {this.props.intl.formatMessage(messages.edit)}
                  </Button>
                )}
                {isEditting && (
                  <Button
                    style={{ marginRight: 10 }}
                    onClick={() => {
                      const {
                        payer_name,
                        payment_date,
                        bank_name,
                        execution_date,
                        bank_holders,
                        type_payment,
                        bank_account,
                        service_payment_fees,
                      } = this.props.dashboardInvoiceBillDetail.data;
                      this.setState({
                        isEditting: false,
                        payer_name,
                        bank_name,
                        bank_holders,
                        bank_account,
                        payment_date: moment.unix(payment_date),
                        execution_date: moment.unix(execution_date),
                        type_payment,
                      });
                    }}
                    type="danger"
                    disabled={updating}
                  >
                    {this.props.intl.formatMessage(messages.cancel)}
                  </Button>
                )}
                {isEditting && (
                  <Button
                    style={{ marginRight: 10 }}
                    onClick={() => {
                      let total_new_money_collected = _.sumBy(
                        fees,
                        (ss) => ss.new_money_collected
                      );

                      if (total_new_money_collected == 0) {
                        notificationBar(
                          this.props.intl.formatMessage(messages.errorContent),
                          "warning"
                        );
                        return;
                      }
                      this.props.dispatch(
                        updateBill({
                          id: data.id,
                          payer_name: payer_name,
                          type_payment: type_payment,
                          bank_name: bank_name,
                          bank_holders: bank_holders,
                          bank_account: bank_account,
                          description: "string",
                          service_payment_fee_id: fees[0].id,
                          payment_date: payment_date.unix(),
                          execution_date: execution_date.unix(),
                        })
                      );
                    }}
                    disabled={this.checkEmptyAccount()}
                    type="primary"
                    loading={updating}
                  >
                    {this.props.intl.formatMessage(messages.update)}
                  </Button>
                )}
                {data.status == 1 && !isEditting && (
                  <WithRole
                    roles={[config.ALL_ROLE_NAME.FINANCE_SPECIAL_INVOICE_BILL]}
                  >
                    <Button
                      style={{ marginRight: 10 }}
                      disabled={canceling}
                      loading={changingStatus}
                      onClick={() => {
                        Modal.confirm({
                          autoFocusButton: null,
                          title: this.props.intl.formatMessage(
                            messages.closingEntryContent
                          ),
                          okText: this.props.intl.formatMessage(
                            messages.okText
                          ),
                          cancelText: this.props.intl.formatMessage(
                            messages.cancel
                          ),
                          onOk: () => {
                            this.props.dispatch(
                              blockBill({
                                ids: [data.id],
                                callback: () => {
                                  this.props.dispatch(
                                    fetchDetailBill({ id: data.id })
                                  );
                                },
                              })
                            );
                          },
                        });
                      }}
                    >
                      {this.props.intl.formatMessage(messages.closingEntry)}
                    </Button>
                  </WithRole>
                )}
                {data.status == 10 && !isEditting && (
                  <WithRole
                    roles={[config.ALL_ROLE_NAME.FINANCE_SPECIAL_INVOICE_BILL]}
                  >
                    <Button
                      style={{ marginRight: 10 }}
                      disabled={canceling}
                      loading={changingStatus}
                      onClick={() => {
                        this.setState({
                          visibleConfirm: true,
                          noteFor: "unlock",
                          note: "",
                        });
                      }}
                    >
                      {this.props.intl.formatMessage(messages.unlock)}
                    </Button>
                  </WithRole>
                )}
                {data.status != 10 && !isEditting && (
                  <WithRole
                    roles={[config.ALL_ROLE_NAME.FINANCE_SPECIAL_INVOICE_BILL]}
                  >
                    <Button
                      style={{ float: "right" }}
                      type="danger"
                      loading={canceling}
                      disabled={changingStatus}
                      onClick={() => {
                        this.setState({
                          visibleConfirm: true,
                          noteFor: "cancel",
                          note: "",
                        });
                      }}
                    >
                      {this.props.intl.formatMessage(messages.cancelReceipt)}
                    </Button>
                  </WithRole>
                )}
              </Row>
            )}
          </>
        </WithRole>
        <Row gutter={24}>
          <Col
            span={24}
            style={{ paddingLeft: 10, paddingRight: 32, marginBottom: 16 }}
          >
            <Row type="flex" align="middle">
              <Col span={5} style={{ textAlign: "left" }}>
                {this.props.intl.formatMessage(messages.receiptNo)}: &ensp;
              </Col>
              <Col
                span={7}
                style={{ color: "black", fontWeight: "bold", fontSize: 18 }}
              >
                {data.number}
              </Col>
            </Row>
          </Col>
          <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
            <Row type="flex" align="middle">
              <Col span={10} style={{ textAlign: "left" }}>
                {this.props.intl.formatMessage(messages.property)}:&ensp;
              </Col>
              <Col span={14}>
                <Input
                  defaultValue={`${data.apartment_name} (${data.apartment_parent_path})`}
                  disabled
                  style={{ color: "black" }}
                />
              </Col>
            </Row>
          </Col>
          <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
            <Row type="flex" align="middle">
              <Col span={10} style={{ textAlign: "left" }}>
                {this.props.intl.formatMessage(messages.status)}:&ensp;
              </Col>
              <Col span={14}>
                <Input
                  value={
                    data.status === 10
                      ? this.props.intl.formatMessage(messages.closingEntry)
                      : data.status === 2
                      ? this.props.intl.formatMessage(messages.cancelled)
                      : data.status === 1
                      ? this.props.intl.formatMessage(messages.paid)
                      : this.props.intl.formatMessage(messages.unpaid)
                  }
                  onChange={(e) => {}}
                  disabled
                  style={{
                    fontWeight: "bold",
                    color: data.status != 2 ? "black" : "#F1494E",
                  }}
                />
              </Col>
            </Row>
          </Col>
          <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
            <Row type="flex" align="middle">
              <Col span={10} style={{ textAlign: "left" }}>
                {this.props.intl.formatMessage(messages.payer)}:&ensp;
              </Col>
              <Col span={14}>
                <Input
                  defaultValue={data.management_user_name}
                  style={{ color: "black" }}
                  disabled
                />
              </Col>
            </Row>
          </Col>
          <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
            <Row type="flex" align="middle">
              <Col span={10} style={{ textAlign: "left" }}>
                {this.props.intl.formatMessage(messages.receiver)}:&ensp;
              </Col>
              <Col span={14}>
                <Input
                  value={this.state.payer_name}
                  onChange={(e) =>
                    this.setState({ payer_name: e.target.value })
                  }
                  disabled={!isEditting}
                  style={{ color: "black" }}
                />
              </Col>
            </Row>
          </Col>
          <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
            <Row type="flex" align="middle">
              <Col span={10} style={{ textAlign: "left" }}>
                {this.props.intl.formatMessage(messages.dateSpending)}:&ensp;
              </Col>
              <Col span={14}>
                <DatePicker
                  allowClear={isEditting ? false : true}
                  value={this.state.payment_date}
                  onChange={(payment_date) => this.setState({ payment_date })}
                  disabled={!isEditting}
                  style={{ color: "black", width: "100%" }}
                  format="DD/MM/YYYY"
                  disabledDate={(current) => {
                    return current && current > moment().endOf("day");
                  }}
                />
              </Col>
            </Row>
          </Col>
          <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
            <Row type="flex" align="middle">
              <Col
                span={10}
                style={{
                  textAlign: "left",
                  paddingRight: window.innerWidth <= 1366 ? 5 : null,
                }}
              >
                {this.props.intl.formatMessage(messages.implementDate)}:&ensp;
              </Col>
              <Col span={14}>
                <DatePicker
                  allowClear={isEditting ? false : true}
                  value={this.state.execution_date}
                  onChange={(execution_date) =>
                    this.setState({ execution_date })
                  }
                  disabled={!isEditting}
                  style={{ color: "black", width: "100%" }}
                  format="DD/MM/YYYY"
                  disabledDate={(current) => {
                    return current && current > moment().endOf("day");
                  }}
                />
              </Col>
            </Row>
          </Col>
          <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
            <Row type="flex" align="middle">
              <Col span={10} style={{ textAlign: "left" }}>
                {this.props.intl.formatMessage(messages.paymentForm)}:&ensp;
              </Col>
              <Col span={14}>
                <Select
                  value={`${this.state.type_payment}`}
                  style={{ width: "100%" }}
                  disabled={!isEditting}
                  onChange={(e) => {
                    this.setState({
                      type_payment: e,
                    });
                  }}
                >
                  <Select.Option value={"0"}>
                    {this.props.intl.formatMessage(messages.cash)}
                  </Select.Option>
                  <Select.Option value={"1"}>
                    {this.props.intl.formatMessage(messages.transfer)}
                  </Select.Option>
                </Select>
              </Col>
            </Row>
          </Col>
          {type_payment == 1 && (
            <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
              <Row type="flex" align="middle">
                <Col
                  span={10}
                  style={{
                    textAlign: "left",
                    paddingRight: window.innerWidth <= 1366 ? 5 : null,
                    color: bank_holders.trim().length ? null : "red",
                  }}
                >
                  {this.props.intl.formatMessage(messages.accountHolder)}:&ensp;
                </Col>
                <Col span={14}>
                  <Input
                    style={{ width: "100%" }}
                    value={bank_holders}
                    disabled={!isEditting}
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
            <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
              <Row type="flex" align="middle">
                <Col
                  span={10}
                  style={{
                    textAlign: "left",
                    color: bank_name.trim().length ? null : "red",
                  }}
                >
                  {this.props.intl.formatMessage(messages.bank)}:&ensp;
                </Col>
                <Col span={14}>
                  <Input
                    style={{ width: "100%" }}
                    value={bank_name}
                    disabled={!isEditting}
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
            <Col span={12} style={{ marginBottom: 16, paddingLeft: 10 }}>
              <Row type="flex" align="middle">
                <Col
                  span={10}
                  style={{
                    textAlign: "left",
                    color: bank_account.trim().length ? null : "red",
                  }}
                >
                  {this.props.intl.formatMessage(messages.accountNumber)}:&ensp;
                </Col>
                <Col span={14}>
                  <Input
                    style={{ width: "100%" }}
                    disabled={!isEditting}
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
        <Row style={{ marginTop: 16 }}>
          <Table
            dataSource={this.state.fees}
            columns={columns}
            bordered
            rowKey="id"
            pagination={false}
            // scroll={{ x: 750 }}
          />
        </Row>
      </Col>
    );
  };
  _renderPhieuChi = () => {
    const { dashboardInvoiceBillDetail, buildingCluster } = this.props;
    const { data } = dashboardInvoiceBillDetail;
    let { service_bill_invoice_template } = buildingCluster.data || {};
    let service_bill_templateJSON = null;
    try {
      service_bill_templateJSON = service_bill_invoice_template;
    } catch (error) {
      console.log("error", error);
    }

    const { payer_name, payment_date, execution_date, fees } = this.state;
    let obj = {
      fees: parseInvoiceBillToView(fees).map((rr, index) => {
        if (
          !!service_bill_templateJSON &&
          !!service_bill_templateJSON.jsx_row
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
      fees_en: parseInvoiceBillToView(fees).map((rr, index) => {
        if (
          !!service_bill_templateJSON &&
          !!service_bill_templateJSON.jsx_row
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
      number: data.number,
      payer_name: payer_name.toUpperCase(),
      apartment_name: `${data.apartment_name} (${data.apartment_parent_path})`,
      payment_date: moment.unix(payment_date).format("DD/MM/YYYY"),
      execution_date: ` .....${execution_date.format("DD")}..... `,
      execution_month: ` .....${execution_date.format("MM")}..... `,
      execution_year: ` .....${execution_date.format("YYYY")}.....`,
      total_new_money_collected: formatPrice(
        _.sumBy(fees, (rr) => rr.new_money_collected || 0)
      ),
      total_new_money_collected_string: `${DocTienBangChu(
        _.sumBy(fees, (rr) => rr.new_money_collected || 0)
      )} đồng`,
      total_new_money_collected_string_en: `${convertNumberToWords(
        _.sumBy(fees, (rr) => rr.new_money_collected || 0)
      )} dong`,
      type_payment: config.TYPE_PAYMENT.find(
        (item) => item.id === data.type_payment
      ).name,
      type_payment_en: config.TYPE_PAYMENT.find(
        (item) => item.id === data.type_payment
      ).name_en,
    };

    return (
      <Col
        span={12}
        style={{
          paddingLeft: 8,
          borderLeft: "1px solid #d9d9d9",
        }}
      >
        <Row
          style={{
            marginBottom: 16,
            paddingLeft: 16,
          }}
          type="flex"
          justify="space-between"
        >
          <span style={{ fontSize: 18, fontWeight: "bold", color: "black" }}>
            {this.props.intl.formatMessage(messages.paymentVoucher)}
          </span>
          {data.status != 2 && (
            <Button
              style={{ marginRight: 10 }}
              onClick={() => {
                this._printBill();
              }}
            >
              {this.props.intl.formatMessage(messages.print)} (F3)
            </Button>
          )}
        </Row>
        <Row style={{ marginBottom: 16, marginTop: 22, marginLeft: 16 }}>
          <img
            style={{ width: 160 }}
            alt="logo_company"
            src={config.logoPath4}
          />
        </Row>
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

  _printBill = () => {
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
                    ${styleString}
                  </style>
                  <div style="margin-left: 16px">
                  <img src="${
                    config.logoPath4
                  }" alt="" width="180" height="60"></div>
                  <div class="jsx-parser" >
                    ${$("#lien1")[0].innerHTML}
                    <div style="page-break-before:always" />
                    <div style="margin-left: 16px">
                  <img src="${
                    config.logoPath4
                  }" alt="" width="180" height="60"> </div>
                    ${$("#lien2")[0].innerHTML}
                    <div style="page-break-before:always" />
                    <div style="margin-left: 16px">
                  <img src="${
                    config.logoPath4
                  }" alt="" width="180" height="60"> </div>
                    ${$("#lien3")[0].innerHTML}
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
    const { dashboardInvoiceBillDetail } = this.props;
    const { loading, data, canceling, cancelSuccess } =
      dashboardInvoiceBillDetail;
    const { noteFor } = this.state;
    let { formatMessage } = this.props.intl;

    if (loading) {
      return <Page inner loading />;
    }

    if (!data) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={formatMessage(messages.notFound)}
            actions={
              <Button
                type="primary"
                onClick={() =>
                  this.props.history.push("/main/finance/invoice-bills")
                }
              >
                {formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }

    return (
      <Page inner>
        <Row className="dashboardInvoiceBillDetail">
          <Col span={24} style={{ marginBottom: 16 }}>
            <Row type="flex" align="middle">
              <a
                href="#"
                onClick={(e) => {
                  e.preventDefault();
                  this.props.history.goBack();
                }}
                style={{ display: "flex", alignItems: "center" }}
              >
                <i className="material-icons">keyboard_backspace</i>
                &ensp;{formatMessage(messages.back)}
              </a>
            </Row>
          </Col>
          <Col span={24}>
            <Row
              style={{ height: "100%", display: "flex", alignItems: "stretch" }}
            >
              {this._renderInfo()}
              {this._renderPhieuChi()}
            </Row>
          </Col>
          <Modal
            visible={this.state.visibleConfirm}
            onCancel={() => this.setState({ visibleConfirm: false, note: "" })}
            title={
              noteFor == "cancel"
                ? formatMessage(messages.cancelPVContent)
                : noteFor == "unlock"
                ? formatMessage(messages.unlockPVContent)
                : ""
            }
            okText={formatMessage(messages.continue)}
            cancelText={formatMessage(messages.skip)}
            onOk={() => {
              this.setState(
                {
                  visibleConfirm: false,
                },
                () => {
                  if (noteFor == "cancel")
                    this.props.dispatch(
                      cancelBill({
                        id: data.id,
                        note: this.state.note,
                      })
                    );
                  if (noteFor == "unlock")
                    this.props.dispatch(
                      changeStatusBill({
                        id: data.id,
                        status: 1,
                        status_name: formatMessage(messages.paid),
                      })
                    );
                }
              );
            }}
            okButtonProps={{ disabled: !this.state.note.trim().length }}
          >
            <Row>
              <Col span={4} style={{ textAlign: "right" }}>
                <span>{formatMessage(messages.reason)}:&ensp;</span>
              </Col>
              <Col span={20}>
                <Input.TextArea
                  rows={5}
                  value={this.state.note}
                  onChange={(e) => this.setState({ note: e.target.value })}
                />
              </Col>
            </Row>
          </Modal>
        </Row>
      </Page>
    );
  }
}

DashboardInvoiceBillDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  dashboardInvoiceBillDetail: makeSelectDashboardInvoiceBillDetail(),
  buildingCluster: selectBuildingCluster(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "dashboardInvoiceBillDetail",
  reducer,
});
const withSaga = injectSaga({ key: "dashboardInvoiceBillDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(hotkeys(DashboardInvoiceBillDetail)));
