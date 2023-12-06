/**
 *
 * BillCreate
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectBillCreate from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page";
import {
  Card,
  Row,
  Spin,
  Form,
  Select,
  Input,
  Table,
  Tooltip,
  Icon,
  Button,
  Col,
  Statistic,
} from "antd";
import {
  createBill,
  fetchApartmentAction,
  fetchFilterFee,
  resetFilterFee,
} from "./actions";
import styles from "../FeeList/index.less";
import { config, formatPrice } from "../../../utils";
import moment from "moment";
import { initialState as feeList } from "../../NotificationContainer/NotificationCategory/reducer";
import { defaultAction } from "../FeeList/actions";
import { Redirect } from "react-router";
import { fetchDetailBillAction } from "../BillDetail/actions";
import queryString from "query-string";
import { injectIntl } from "react-intl";
import messages from "../messages";
const { Option } = Select;

const formItemLayout = {
  labelCol: {
    xl: { span: 4, offset: 4 },
    lg: { span: 2, offset: 4 },
  },
  wrapperCol: {
    xl: { span: 8 },
    lg: { span: 8 },
  },
};

const buttonFormItemLayout = {
  wrapperCol: {
    xs: {
      span: 8,
      offset: 0,
    },
    sm: {
      span: 8,
      offset: 4,
    },
  },
};

const tableFormItemLayout = {
  wrapperCol: {
    xs: {
      span: 24,
      offset: 0,
    },
    sm: {
      span: 16,
      offset: 4,
    },
  },
};

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class BillCreate extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      apartmentSelected: undefined,
      paymentFees: [],
      totalMoney: 0,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(resetFilterFee());
  }

  handerCancel = () => {
    this.props.history.goBack();
  };

  handleOk = () => {
    const { dispatch, form } = this.props;
    const { paymentFees } = this.state;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      dispatch(
        createBill({
          ...values,
          service_payment_fee_id: paymentFees.map((fee) => fee.id),
          callback: (record) => {
            this.props.history.push(`/main/finance/bills/detail/${record.id}`, {
              record,
            });
          },
        })
      );
    });
  };

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ keyword }));
  };

  handleChangeApartment = (value) => {
    const { apartments } = this.props.billCreate;
    let apartment = apartments.lst.find((apartment) => apartment.id == value);
    if (apartment) {
      this.setState({
        apartmentSelected: apartment,
      });

      this.props.dispatch(
        fetchFilterFee({
          apartment_id: apartment.id,
          status: 0, // Chua thanh toan
        })
      );
      this.props.form.setFields({
        payer_name: {
          value: apartment.resident_user_name,
        },
      });
    } else {
      this.props.dispatch(resetFilterFee());
    }
  };

  feeSelection = {
    onChange: (selectedRowKeys, selectedRows) => {
      console.log(
        `selectedRowKeys: ${selectedRowKeys}`,
        "selectedRows: ",
        selectedRows
      );
      this.setState({
        totalMoney: selectedRows.reduce((total, fee) => {
          return total + parseInt(fee.price);
        }, 0),
        paymentFees: selectedRows,
      });
    },
    getCheckboxProps: (record) => ({
      disabled: !!record.service_bill_code, // Column configuration not to be checked
      name: record.description,
    }),
  };

  render() {
    const { billCreate } = this.props;
    const { fees, creating, success, bill } = billCreate;
    const { loading, lst } = billCreate.apartments;
    const { getFieldDecorator } = this.props.form;
    let { formatMessage } = this.props.intl;

    if (success && !!bill) {
      return <Redirect to={`/main/finance/bills/detail/${bill.id}`} />;
    }

    const { paymentFees, totalMoney, apartmentSelected } = this.state;

    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => <span>{index + 1}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.description)}
          </span>
        ),
        dataIndex: "description",
        key: "description",
        render: (text, record) => (
          <div>
            <p style={{ whiteSpace: "pre-line" }}>{record.description}</p>{" "}
            {!!record.service_bill_code && (
              <p>
                {formatMessage(messages.enterBill)}:{" "}
                <span>{record.service_bill_code}</span>
              </p>
            )}
          </div>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.service)}
          </span>
        ),
        dataIndex: "service_map_management_service_name",
        key: "service_map_management_service_name",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.month)}
          </span>
        ),
        dataIndex: "fee_of_month",
        key: "fee_of_month",
        render: (text, record) => (
          <span>{moment.unix(record.fee_of_month).format("MM/YYYY")}</span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.amountOfMoney)}
          </span>
        ),
        dataIndex: "price",
        key: "price",
        render: (text, record) => <span>{formatPrice(record.price)}</span>,
        align: "right",
      },
    ];

    return (
      <Page>
        <Row className="billAddPage">
          <Card
            title={formatMessage(messages.receipts)}
            style={{
              borderRadius: 4,
              border: "0px solid transparent",
              marginBottom: 16,
            }}
          >
            <Form {...formItemLayout}>
              <Form.Item label={formatMessage(messages.property)}>
                {getFieldDecorator("apartment_id", {
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.errorProperty),
                      whitespace: true,
                    },
                  ],
                })(
                  <Select
                    loading={loading}
                    showSearch
                    placeholder={formatMessage(messages.choseProperty)}
                    optionFilterProp="children"
                    notFoundContent={loading ? <Spin size="small" /> : null}
                    onChange={this.handleChangeApartment}
                    onSearch={this._onSearch}
                  >
                    {lst.map((gr) => {
                      return (
                        <Select.Option
                          key={`group-${gr.id}`}
                          value={`${gr.id}`}
                        >{`${gr.name} (${gr.parent_path})`}</Select.Option>
                      );
                    })}
                  </Select>
                )}
              </Form.Item>
              <Form.Item
                {...formItemLayout}
                label={`${formatMessage(messages.owner)}:`}
              >
                <Input
                  disabled={true}
                  value={
                    this.state.apartmentSelected
                      ? this.state.apartmentSelected.resident_user_name
                      : ""
                  }
                />
              </Form.Item>
              <Form.Item label={formatMessage(messages.property)}>
                {getFieldDecorator("payer_name", {
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.payerError),
                    },
                  ],
                })(<Input />)}
              </Form.Item>
              <Form.Item label={formatMessage(messages.payments)}>
                {getFieldDecorator("type_payment", {
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.paymentsError),
                    },
                  ],
                })(
                  <Select
                    showSearch
                    style={{ width: "100%" }}
                    placeholder={formatMessage(messages.chosePayment)}
                    optionFilterProp="children"
                    filterOption={(input, option) =>
                      option.props.children
                        .toLowerCase()
                        .indexOf(input.toLowerCase()) >= 0
                    }
                  >
                    <Option value="0">{formatMessage(messages.cash)}</Option>
                    <Option value="1">
                      {formatMessage(messages.transfer)}
                    </Option>
                  </Select>
                )}
              </Form.Item>
              <Form.Item {...tableFormItemLayout}>
                <Table
                  rowKey="id"
                  loading={fees.loading}
                  columns={columns}
                  dataSource={fees.lst}
                  locale={{ emptyText: formatMessage(messages.emptyData) }}
                  pagination={false}
                  rowSelection={this.feeSelection}
                  bordered
                />
              </Form.Item>
              <Form.Item
                {...tableFormItemLayout}
                style={{ textAlign: "right" }}
              >
                <Statistic
                  title={formatMessage(messages.totalAmount)}
                  groupSeparator="."
                  value={totalMoney}
                  suffix=" đ"
                />
              </Form.Item>
              <Form.Item {...buttonFormItemLayout}>
                <Button
                  loading={creating}
                  style={{ width: 100 }}
                  ghost
                  type="primary"
                  onClick={this.handleOk}
                  disabled={paymentFees.length > 0 ? false : true}
                >
                  {formatMessage(messages.createVote)}
                </Button>

                <Button
                  disabled={creating}
                  style={{ width: 100, marginLeft: 8 }}
                  type="danger"
                  onClick={this.handerCancel}
                >
                  {formatMessage(messages.cancel)}
                </Button>
              </Form.Item>
            </Form>
          </Card>
        </Row>
      </Page>
    );
  }
}

BillCreate.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  billCreate: makeSelectBillCreate(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "billCreate", reducer });
const withSaga = injectSaga({ key: "billCreate", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(BillCreate));
