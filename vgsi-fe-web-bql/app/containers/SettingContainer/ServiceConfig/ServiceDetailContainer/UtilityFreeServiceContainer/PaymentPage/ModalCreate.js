import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage, injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectApartmentDetail from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "../../../messages";
import {
  Row,
  Col,
  Table,
  Tooltip,
  Icon,
  Modal,
  Button,
  TreeSelect,
  InputNumber,
  Input,
  Form,
  Select,
  Spin,
  DatePicker,
} from "antd";
import { config } from "../../../../../../utils";
import { fetchApartment } from "./actions";
import moment from "moment";
import NumericInput from "../../../../../../components/NumericInput";
import _ from "lodash";
const monthFormat = "MM/YYYY";
const { MonthPicker, RangePicker } = DatePicker;
import("./index.less");
const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 12,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalCreate extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      showPickerColor: false,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };
  componentDidMount() {
    this._onSearch("");
  }

  handlerUpdate = () => {
    const { currentEdit, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      let apartment_id = values.apartment_id.split(":");
      if (currentEdit) {
        this.props.updatePayment &&
          this.props.updatePayment({
            ...values,
            status: 0,
            price: parseInt(values.price),
            fee_of_month: values.fee_of_month.unix(),
            day_expired: values.day_expired.unix(),
            apartment_id: apartment_id[0],
          });
      } else {
        this.props.addPayment &&
          this.props.addPayment({
            ...values,
            status: 0,
            price: parseInt(values.price),
            fee_of_month: values.fee_of_month.unix(),
            day_expired: values.day_expired.unix(),
            apartment_id: apartment_id[0],
          });
      }
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
      if (nextProps.visible) {
        this._onSearch(
          nextProps.currentEdit ? nextProps.currentEdit.apartment_name : ""
        );
      }
    }
  }

  render() {
    const { showPickerColor } = this.state;
    const { visible, setState, paymentPage, currentEdit } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    return (
      <Modal
        title={
          currentEdit
            ? this.props.intl.formatMessage(messages.editPaymentFee)
            : this.props.intl.formatMessage(messages.createPaymentFee)
        }
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (paymentPage.creating) return;
          setState({
            visible: false,
          });
        }}
        okText={
          currentEdit
            ? this.props.intl.formatMessage(messages.update)
            : this.props.intl.formatMessage(messages.add)
        }
        cancelText="Huỷ"
        okButtonProps={{ loading: paymentPage.creating }}
        cancelButtonProps={{ disabled: paymentPage.creating }}
        maskClosable={false}
      >
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item label={this.props.intl.formatMessage(messages.property)}>
            {getFieldDecorator("apartment_id", {
              initialValue: currentEdit
                ? `${currentEdit.apartment_id}:1`
                : undefined,
              rules: [
                {
                  required: true,
                  message: this.props.intl.formatMessage(
                    messages.emptyProperty
                  ),
                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    const form = this.props.form;
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback(
                          this.props.intl.formatMessage(
                            messages.emptyCurrentProperty
                          )
                        );
                      } else {
                        callback();
                      }
                    } else {
                      callback();
                    }
                  },
                },
              ],
            })(
              <Select
                loading={paymentPage.apartment.loading}
                showSearch
                placeholder={this.props.intl.formatMessage(
                  messages.selectProperty
                )}
                optionFilterProp="children"
                notFoundContent={
                  paymentPage.apartment.loading ? <Spin size="small" /> : null
                }
                onSearch={this._onSearch}
              >
                {paymentPage.apartment.items.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}:${gr.status}`}
                    >{`${gr.name} (${gr.parent_path})${
                      gr.status == 0
                        ? ` - ${this.props.intl.formatMessage(messages.empty)}`
                        : ""
                    }`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item
            label={this.props.intl.formatMessage(messages.description)}
          >
            {getFieldDecorator("description", {
              initialValue: currentEdit ? currentEdit.description : undefined,
              rules: [],
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
          <Form.Item label={"Số tiền"}>
            {getFieldDecorator("price", {
              initialValue: currentEdit ? `${currentEdit.price}` : undefined,
              rules: [
                {
                  required: true,
                  message: this.props.intl.formatMessage(
                    messages.emptyAmountMoney
                  ),
                  whitespace: true,
                },
              ],
            })(<NumericInput maxLength={10} />)}
          </Form.Item>
          <Form.Item label={this.props.intl.formatMessage(messages.month)}>
            {getFieldDecorator("fee_of_month", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.fee_of_month)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: this.props.intl.formatMessage(messages.emptyMonth),
                },
              ],
            })(
              <MonthPicker
                style={{ width: "100%" }}
                placeholder={this.props.intl.formatMessage(
                  messages.selectMonth
                )}
                format={monthFormat}
              />
            )}
          </Form.Item>
          <Form.Item label={this.props.intl.formatMessage(messages.dayExpire)}>
            {getFieldDecorator("day_expired", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.day_expired)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: this.props.intl.formatMessage(messages.emptyMonth),
                },
              ],
            })(
              <DatePicker
                style={{ width: "100%" }}
                placeholder={this.props.intl.formatMessage(messages.selectDay)}
                format={"DD/MM/YYYY"}
              />
            )}
          </Form.Item>
          {/* <Form.Item
                    label={'Trạng thái'}
                >
                    {getFieldDecorator('status', {
                        initialValue: !!currentEdit ? currentEdit.status : 0,
                        rules: [{ required: true, message: 'Trạng thái không được để trống.', whitespace: true, type: 'number' }],
                    })(
                        <Select
                            showSearch
                            placeholder="Chọn trạng thái"
                            optionFilterProp="children"
                            // onChange={onChange}
                            filterOption={(input, option) =>
                                option.props.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                            }
                        >
                            {
                                config.STATUS_SERVICE_PAYMENT.map(gr => {
                                    return <Select.Option key={`group-${gr.id}`} value={gr.id}>{gr.name}</Select.Option>
                                })
                            }
                        </Select>
                    )}
                </Form.Item> */}
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalCreate);
