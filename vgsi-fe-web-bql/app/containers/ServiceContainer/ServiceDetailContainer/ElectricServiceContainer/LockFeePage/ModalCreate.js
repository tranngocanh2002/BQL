import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";

import { DatePicker, Form, Input, Modal, Select, Spin } from "antd";
import _ from "lodash";
import moment from "moment";
import NumericInput from "../../../../../components/NumericInput";
import messages from "../messages";
import { fetchApartment } from "./actions";

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
    const { showPickerColor } = this.state;
    const { visible, setState, lockFeePagePage, currentEdit, intl } =
      this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    return (
      <Modal
        title={
          currentEdit ? (
            <FormattedMessage {...messages.editFee} />
          ) : (
            <FormattedMessage {...messages.createFee} />
          )
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
          currentEdit ? (
            <FormattedMessage {...messages.update} />
          ) : (
            <FormattedMessage {...messages.add} />
          )
        }
        cancelText={<FormattedMessage {...messages.cancelText} />}
        okButtonProps={{ loading: lockFeePagePage.creating }}
        cancelButtonProps={{ disabled: lockFeePagePage.creating }}
        maskClosable={false}
      >
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item label={<FormattedMessage {...messages.property} />}>
            {getFieldDecorator("apartment_id", {
              initialValue: currentEdit
                ? `${currentEdit.apartment_id}:1`
                : undefined,
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.errorEmptyProperty} />
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
                          <FormattedMessage
                            {...messages.errorEmptyCurrentProperty}
                          />
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
                loading={lockFeePagePage.apartment.loading}
                showSearch
                placeholder={<FormattedMessage {...messages.plhProperty} />}
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
                        ? ` - ${intl.formatMessage({ ...messages.empty })}`
                        : ""
                    }`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item label={<FormattedMessage {...messages.description} />}>
            {getFieldDecorator("description", {
              initialValue: currentEdit ? currentEdit.description : undefined,
              rules: [],
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
          <Form.Item label={<FormattedMessage {...messages.cash} />}>
            {getFieldDecorator("price", {
              initialValue: currentEdit ? `${currentEdit.price}` : undefined,
              rules: [
                {
                  required: true,
                  message: <FormattedMessage {...messages.errorEmptyCash} />,
                  whitespace: true,
                },
              ],
            })(<NumericInput maxLength={10} />)}
          </Form.Item>
          <Form.Item label={<FormattedMessage {...messages.month} />}>
            {getFieldDecorator("fee_of_month", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.fee_of_month)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: <FormattedMessage {...messages.errorMonthPayment} />,
                },
              ],
            })(
              <MonthPicker
                style={{ width: "100%" }}
                placeholder={intl.formatMessage({ ...messages.selectMonth })}
                format={monthFormat}
              />
            )}
          </Form.Item>
          <Form.Item label={<FormattedMessage {...messages.expirePayment} />}>
            {getFieldDecorator("day_expired", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.day_expired)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: (
                    <FormattedMessage {...messages.errorExpiredPayment} />
                  ),
                },
              ],
            })(
              <DatePicker
                style={{ width: "100%" }}
                placeholder={intl.formatMessage({ ...messages.selectDate })}
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
