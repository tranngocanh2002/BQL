import React from "react";
import { Modal, Input, Form, Select, DatePicker } from "antd";
import moment from "moment";
import { FormattedMessage, injectIntl } from "react-intl";
import messages, { scope } from "./messages";
import { config } from "../../../../utils";
import InputNumberFormat from "../../../../components/InputNumberFormat";
import InputNumberNagative from "../../../../components/InputNumberNagative";

const monthFormat = "MM/YYYY";
const { MonthPicker } = DatePicker;
import("./index.less");
const formItemLayout = {
  labelCol: {
    span: 7,
  },
  wrapperCol: {
    span: 16,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalFee extends React.PureComponent {
  constructor(props) {
    super(props);
  }
  handlerUpdate = () => {
    const { currentEdit, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      if (currentEdit) {
        this.props.updatePayment &&
          this.props.updatePayment({
            ...values,
            apartment_id: currentEdit.apartment_id,
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
    }
  }

  disabledDate = (current) => {
    const { currentEdit } = this.props;
    return (
      current && current < moment.unix(currentEdit.fee_of_month).endOf("month")
    );
  };

  render() {
    const { visible, setState, currentEdit, updating, intl } = this.props;
    const { getFieldDecorator } = this.props.form;
    const datePlaceholder = intl.formatMessage({
      id: `${scope}.chooseDayPlaceholder`,
    });

    const ruleOfDescription =
      currentEdit && currentEdit.for_type === 2
        ? [
            {
              required: true,
              message: intl.formatMessage({
                id: `${scope}.feeDescriptionRequired`,
              }),
              whitespace: true,
            },
          ]
        : [];
    const ruleOfDescription_en =
      currentEdit && currentEdit.for_type === 2
        ? [
            {
              required: true,
              message: intl.formatMessage({
                id: `${scope}.feeDescriptionRequiredEN`,
              }),
              whitespace: true,
            },
          ]
        : [];
    return (
      <Modal
        title={<FormattedMessage {...messages.editFeePaid} />}
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          setState({
            visible: false,
          });
        }}
        okText={<FormattedMessage {...messages.update} />}
        cancelText={<FormattedMessage {...messages.cancel} />}
        okButtonProps={{ loading: updating }}
        cancelButtonProps={{ disabled: updating }}
        maskClosable={false}
      >
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item
            label={<FormattedMessage {...messages.property} />}
            colon={false}
          >
            {getFieldDecorator("apartment_id", {
              initialValue: currentEdit
                ? `${currentEdit.apartment_name} (${currentEdit.apartment_parent_path})`
                : undefined,
            })(<Input disabled={true} />)}
          </Form.Item>
          <Form.Item label={<FormattedMessage {...messages.feeDescription} />}>
            {getFieldDecorator("description", {
              initialValue: currentEdit ? currentEdit.description : undefined,
              rules: ruleOfDescription,
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.feeDescriptionEN} />}
          >
            {getFieldDecorator("description_en", {
              initialValue: currentEdit
                ? currentEdit.description_en
                : undefined,
              rules: ruleOfDescription_en,
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
          <Form.Item label={<FormattedMessage {...messages.price} />}>
            {getFieldDecorator("price", {
              initialValue: currentEdit ? currentEdit.price : undefined,
              rules: [
                {
                  required: true,
                  message: <FormattedMessage {...messages.priceRequired} />,
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
          <Form.Item label={<FormattedMessage {...messages.month} />}>
            {getFieldDecorator("fee_of_month", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.fee_of_month)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: <FormattedMessage {...messages.monthRequired} />,
                },
              ],
            })(
              <MonthPicker
                style={{ width: "100%" }}
                placeholder={
                  <FormattedMessage {...messages.monthPlaceholder} />
                }
                format={monthFormat}
                disabled={true}
              />
            )}
          </Form.Item>
          <Form.Item label={<FormattedMessage {...messages.dayExpired} />}>
            {getFieldDecorator("day_expired", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.day_expired)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: (
                    <FormattedMessage {...messages.dayExpiredRequired} />
                  ),
                },
              ],
            })(
              <DatePicker
                style={{ width: "100%" }}
                placeholder={datePlaceholder}
                format={"DD/MM/YYYY"}
                disabledDate={this.disabledDate}
              />
            )}
          </Form.Item>
          <Form.Item label={<FormattedMessage {...messages.status} />}>
            {getFieldDecorator("status", {
              initialValue: currentEdit ? currentEdit.status : 0,
              rules: [
                {
                  required: true,
                  message: <FormattedMessage {...messages.statusRequired} />,
                  whitespace: true,
                  type: "number",
                },
              ],
            })(
              <Select
                showSearch
                placeholder={
                  <FormattedMessage {...messages.chooseStatusPlaceholder} />
                }
                optionFilterProp="children"
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
                      {gr.name}
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

export default injectIntl(ModalFee);
