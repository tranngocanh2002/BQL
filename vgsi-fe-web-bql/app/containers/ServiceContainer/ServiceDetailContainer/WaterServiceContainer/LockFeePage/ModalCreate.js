import React from "react";
import { injectIntl } from "react-intl";

import { Modal, Input, Form, Select, Spin, DatePicker } from "antd";
import { fetchApartment } from "./actions";
import _ from "lodash";
import moment from "moment";
import NumericInput from "../../../../../components/NumericInput";
import messages from "../messages";

const monthFormat = "MM/YYYY";
const { MonthPicker } = DatePicker;
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
class ModalCreate extends React.PureComponent {
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
            apartment_id: apartment_id[0],
            price: parseInt(values.price),
            fee_of_month: values.fee_of_month.unix(),
            day_expired: values.day_expired.unix(),
          });
      } else {
        this.props.addPayment &&
          this.props.addPayment({
            ...values,
            status: 0,
            apartment_id: apartment_id[0],
            price: parseInt(values.price),
            fee_of_month: values.fee_of_month.unix(),
            day_expired: values.day_expired.unix(),
          });
      }
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
      if (nextProps.visible && !!nextProps.currentEdit) {
        this._onSearch(
          nextProps.currentEdit ? nextProps.currentEdit.apartment_name : ""
        );
      }
    }
  }

  render() {
    const { visible, setState, lockFeePagePage, currentEdit } = this.props;
    const { getFieldDecorator } = this.props.form;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Modal
        title={
          currentEdit
            ? formatMessage(messages.editPayment)
            : formatMessage(messages.addPayment)
        }
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (lockFeePagePage.creating) return;
          setState({
            visible: false,
          });
        }}
        okText={
          currentEdit
            ? formatMessage(messages.update)
            : formatMessage(messages.add)
        }
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: lockFeePagePage.creating }}
        cancelButtonProps={{ disabled: lockFeePagePage.creating }}
        maskClosable={false}
      >
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item label={formatMessage(messages.property)}>
            {getFieldDecorator("apartment_id", {
              initialValue: currentEdit
                ? `${currentEdit.apartment_id}:1`
                : undefined,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.propertyRequired),
                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback(formatMessage(messages.propertyEmpty));
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
                loading={lockFeePagePage.apartment.loading}
                showSearch
                placeholder={formatMessage(messages.selectProperty)}
                optionFilterProp="children"
                notFoundContent={
                  lockFeePagePage.apartment.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                onSearch={this._onSearch}
              >
                {lockFeePagePage.apartment.items.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}:${gr.status}`}
                    >{`${gr.name} (${gr.parent_path})${
                      gr.status == 0
                        ? ` - ${formatMessage(messages.empty)}`
                        : ""
                    }`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.description)}>
            {getFieldDecorator("description", {
              initialValue: currentEdit ? currentEdit.description : undefined,
              rules: [],
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.amountMoney)}>
            {getFieldDecorator("price", {
              initialValue: currentEdit ? `${currentEdit.price}` : undefined,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.amountMoneyRequired),
                  whitespace: true,
                },
              ],
            })(<NumericInput maxLength={10} />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.month)}>
            {getFieldDecorator("fee_of_month", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.fee_of_month)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: formatMessage(messages.monthRequired),
                },
              ],
            })(
              <MonthPicker
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.selectMonth)}
                format={monthFormat}
              />
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.duePayment)}>
            {getFieldDecorator("day_expired", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.day_expired)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: formatMessage(messages.duePaymentRequired),
                },
              ],
            })(
              <DatePicker
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.chooseDate)}
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
