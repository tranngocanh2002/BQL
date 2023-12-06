import React from "react";
import { Modal, Input, Form, Select, Spin, DatePicker } from "antd";
import moment from "moment";
import { fetchApartmentAction } from "./actions";
import { config } from "../../../utils";
import InputNumberNagative from "../../../components/InputNumberNagative";
import InputNumberFormat from "../../../components/InputNumberFormat";
import messages from "../messages";
import _ from "lodash";

const monthFormat = "MM/YYYY";
const { MonthPicker } = DatePicker;
import("./index.less");
const formItemLayout = {
  labelCol: {
    span: 6,
  },
  wrapperCol: {
    span: 16,
  },
};

class NumericInput extends React.Component {
  onChange = (e) => {
    const { value } = e.target;
    const reg = /^-?(0|[1-9][0-9]*)(\.[0-9]*)?$/;
    if (
      (!Number.isNaN(value) && reg.test(value) && Number(value) > 0) ||
      value === ""
    ) {
      this.props.onChange(value);
    }
  };

  // '.' at the end or only '-' in the input box.
  onBlur = () => {
    const { value, onBlur, onChange } = this.props;
    if (value.charAt(value.length - 1) === ".") {
      onChange(value.slice(0, -1));
    }
    if (onBlur) {
      onBlur();
    }
  };

  render() {
    const { value } = this.props;
    return (
      <Input {...this.props} onChange={this.onChange} onBlur={this.onBlur} />
    );
  }
}
/* eslint-disable react/prefer-stateless-function */
// eslint-disable-next-line react/display-name
@Form.create()
export default class extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      showPickerColor: false,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
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
            apartment_id: apartment_id[0],
            price: parseInt(values.price),
            fee_of_month: values.fee_of_month.unix(),
            day_expired: values.day_expired.unix(),
          });
      }
    });
  };

  UNSAFE_componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
      if (nextProps.visible && !!nextProps.currentEdit) {
        this._onSearch(nextProps.currentEdit.apartment_name);
      }
      if (!nextProps.visible) {
        this._onSearch("");
      }
    }
  }

  disabledDate = (current) => {
    const { currentEdit } = this.props;
    return (
      // Can not select days before currentEdit
      current &&
      current <
        moment.unix(currentEdit ? currentEdit.day_expired : 0).startOf("day")
    );
  };

  render() {
    const { showPickerColor } = this.state;
    const {
      visible,
      setState,
      currentEdit,
      updating,
      apartments,
      formatMessage,
      language,
    } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    const ruleOfDescription =
      currentEdit && currentEdit.for_type === 2
        ? [
            {
              required: true,
              message: formatMessage(messages.feeError),
              whitespace: true,
            },
          ]
        : [];
    const ruleOfDescription_en =
      currentEdit && currentEdit.for_type === 2
        ? [
            {
              required: true,
              message: formatMessage(messages.feeErrorEn),
              whitespace: true,
            },
          ]
        : [];
    return (
      <Modal
        title={formatMessage(messages.editPaymentFee)}
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          setState({
            visible: false,
          });
        }}
        okText={formatMessage(messages.update)}
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: updating }}
        cancelButtonProps={{ disabled: updating }}
        maskClosable={false}
        width={600}
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
                  message: formatMessage(messages.emptyApartmentError),
                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    const form = this.props.form;
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback(formatMessage(messages.apartmentError));
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
                loading={apartments.loading}
                showSearch
                placeholder={formatMessage(messages.choseProperty)}
                optionFilterProp="children"
                notFoundContent={
                  apartments.loading ? <Spin size="small" /> : null
                }
                onSearch={this._onSearch}
                disabled={true}
              >
                {apartments.lst.map((gr) => {
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
              rules: ruleOfDescription,
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.descriptionEn)}>
            {getFieldDecorator("description_en", {
              initialValue: currentEdit
                ? currentEdit.description_en
                : undefined,
              rules: ruleOfDescription_en,
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.amountOfMoney)}>
            {getFieldDecorator("price", {
              initialValue: currentEdit ? currentEdit.price : undefined,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.amountOfMoneyError),
                  whitespace: true,
                  type: "number",
                },
              ],
            })(
              !!currentEdit && currentEdit.type == 5 ? (
                <InputNumberNagative maxLength={14} style={{ width: "100%" }} />
              ) : (
                <InputNumberFormat maxLength={14} style={{ width: "100%" }} />
              )
            )}
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
                  message: formatMessage(messages.monthError),
                },
              ],
            })(
              <MonthPicker
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.choseMonth)}
                disabled={true}
                format={monthFormat}
              />
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.paymentDueDate)}>
            {getFieldDecorator("day_expired", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.day_expired)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: formatMessage(messages.monthError),
                },
              ],
            })(
              <DatePicker
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.choseDate)}
                disabledDate={this.disabledDate}
                format={"DD/MM/YYYY"}
              />
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.status)}>
            {getFieldDecorator("status", {
              initialValue: currentEdit ? currentEdit.status : 0,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.statusError),
                  whitespace: true,
                  type: "number",
                },
              ],
            })(
              <Select
                showSearch
                placeholder={formatMessage(messages.choseStatus)}
                optionFilterProp="children"
                // onChange={onChange}
                disabled={true}
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
              >
                {config.STATUS_SERVICE_PAYMENT.map((gr) => {
                  return (
                    <Select.Option key={`group-${gr.id}`} value={gr.id}>
                      {language === "en" ? gr.name_en : gr.name}
                    </Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}
