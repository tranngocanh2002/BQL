import React from "react";
import { Modal, Form, Select, Spin, DatePicker } from "antd";
import { fetchApartment } from "./actions";
import moment from "moment";
import InputNumberFormat from "../../../../../components/InputNumberFormat";
import _ from "lodash";
import { injectIntl } from "react-intl";
import messages from "../messages";

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

  handlerUpdate = () => {
    const { currentEdit, form } = this.props;
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
    const { visible, setState, infoUsage, currentEdit } = this.props;
    const { getFieldDecorator } = this.props.form;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Modal
        title={
          currentEdit
            ? formatMessage(messages.edit)
            : formatMessage(messages.add)
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
          currentEdit
            ? formatMessage(messages.update)
            : formatMessage(messages.add)
        }
        cancelText={formatMessage(messages.cancel)}
      >
        <Form {...formItemLayout}>
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
                loading={infoUsage.apartment.loading}
                showSearch
                placeholder={formatMessage(messages.selectProperty)}
                optionFilterProp="children"
                notFoundContent={
                  infoUsage.apartment.loading ? <Spin size="small" /> : null
                }
                onSearch={this._onSearch}
                allowClear
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
                        ? ` - ${formatMessage(messages.empty)}`
                        : ""
                    }`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.endIndex)}>
            {getFieldDecorator("end_index", {
              initialValue: currentEdit ? currentEdit.end_index : 0,
              rules: [],
            })(<InputNumberFormat maxLength={6} style={{ width: "100%" }} />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.endDate)}>
            {getFieldDecorator("end_date", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.end_date)
                : undefined,
              rules: [
                {
                  required: true,
                  message: `${formatMessage(messages.endDate)} ${formatMessage(
                    messages.required
                  ).toLowerCase()}`,
                  type: "object",
                },
              ],
            })(
              <DatePicker
                format="DD/MM/YYYY"
                placeholder={formatMessage(messages.chooseDate)}
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
