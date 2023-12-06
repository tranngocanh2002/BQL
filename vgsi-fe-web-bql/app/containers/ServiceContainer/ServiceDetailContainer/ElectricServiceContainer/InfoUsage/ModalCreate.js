import { DatePicker, Form, Modal, Select, Spin } from "antd";
import _ from "lodash";
import moment from "moment";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import InputNumberFormat from "../../../../../components/InputNumberFormat";
import messages from "../messages";
import { fetchApartment } from "./actions";

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

  handlerUpdate = (need_approve) => {
    const { currentEdit, form, infoUsage } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      let apartment_id = values.apartment_id.split(":");
      if (currentEdit) {
        this.props.updateInfo &&
          this.props.updateInfo({
            ...values,
            end_date: values.end_date.unix(),
            id: currentEdit.id,
            apartment_id: apartment_id[0],
          });
      } else {
        this.props.addInfo &&
          this.props.addInfo({
            ...values,
            end_date: values.end_date.unix(),
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
      } else {
        this._onSearch("");
      }
    }
  }

  render() {
    const { visible, setState, infoUsage, currentEdit, intl } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    const { descriptionFee } = infoUsage;
    return (
      <Modal
        title={
          currentEdit ? (
            <FormattedMessage {...messages.edit} />
          ) : (
            <FormattedMessage {...messages.create} />
          )
        }
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (infoUsage.creating) return;
          setState({
            visible: false,
          });
        }}
        maskClosable={false}
        okText={
          currentEdit ? (
            <FormattedMessage {...messages.update} />
          ) : (
            <FormattedMessage {...messages.them} />
          )
        }
        cancelText={<FormattedMessage {...messages.cancelText} />}
      >
        <Form {...formItemLayout}>
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
                loading={infoUsage.apartment.loading}
                showSearch
                placeholder={<FormattedMessage {...messages.plhProperty} />}
                optionFilterProp="children"
                notFoundContent={
                  infoUsage.apartment.loading ? <Spin size="small" /> : null
                }
                allowClear
                onSearch={this._onSearch}
                onChange={(value, opt) => {
                  if (!opt) {
                    this._onSearch("");
                  }
                }}
              >
                {infoUsage.apartment.items.map((gr) => {
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
          <Form.Item label={this.props.intl.formatMessage(messages.soChotCuoi)}>
            {/* {getFieldDecorator("end_index", {
              initialValue: !!currentEdit ? currentEdit.end_index : undefined,
              rules: [
                {
                  required: true,
                  message: "Không được để trống.",
                  type: "number",
                },
              ],
            })(<InputNumber style={{ width: "100%" }} />)} */}

            {getFieldDecorator("end_index", {
              initialValue: currentEdit ? currentEdit.end_index : 0,
              rules: [],
            })(<InputNumberFormat maxLength={6} style={{ width: "100%" }} />)}
          </Form.Item>
          <Form.Item label={<FormattedMessage {...messages.endDate} />}>
            {getFieldDecorator("end_date", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.end_date)
                : undefined,
              rules: [
                {
                  required: true,
                  message: `${this.props.intl.formatMessage(
                    messages.endDate
                  )} ${this.props.intl
                    .formatMessage(messages.errorEmpty)
                    .toLowerCase()}`,
                  type: "object",
                },
              ],
            })(
              <DatePicker
                format="DD/MM/YYYY"
                placeholder={intl.formatMessage({ ...messages.selectDate })}
                style={{ width: "100%" }}
              />
            )}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalCreate);
