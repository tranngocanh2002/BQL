import React from "react";
import {
  Row,
  Modal,
  Button,
  Input,
  Form,
  Select,
  Spin,
  DatePicker,
} from "antd";
import { fetchApartment } from "./actions";
import moment from "moment";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "../messages";
import _ from "lodash";
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
        this.props.updateVehicle &&
          this.props.updateVehicle({
            id: currentEdit.id,
            ...values,
            apartment_id: apartment_id[0],
            start_date: values.start_date.unix(),
          });
      } else {
        this.props.addVehicle &&
          this.props.addVehicle({
            ...values,
            apartment_id: apartment_id[0],
            start_date: values.start_date.unix(),
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
    const { visible, setState, vihicleManagement, currentEdit } = this.props;
    const { getFieldDecorator } = this.props.form;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Modal
        title={
          currentEdit
            ? formatMessage(messages.editVehicle)
            : formatMessage(messages.themXe)
        }
        visible={visible}
        onCancel={() => {
          if (vihicleManagement.creating || vihicleManagement.updating) return;
          setState({
            visible: false,
          });
        }}
        maskClosable={false}
        onOk={this.handlerUpdate}
        okText={
          currentEdit
            ? formatMessage(messages.update)
            : formatMessage(messages.them)
        }
        cancelText={formatMessage(messages.cancel)}
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
                  message: formatMessage(messages.errorEmptyProperty),
                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback(
                          formatMessage(messages.errorEmptyCurrentProperty)
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
                loading={vihicleManagement.apartment.loading}
                showSearch
                disabled={!!currentEdit}
                placeholder={formatMessage(messages.selectProperty)}
                optionFilterProp="children"
                notFoundContent={
                  vihicleManagement.apartment.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                onSearch={this._onSearch}
                allowClear
                onChange={(value, opt) => {
                  if (!opt) {
                    this._onSearch("");
                  }
                }}
              >
                {vihicleManagement.apartment.items.map((gr) => {
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
          <Form.Item label={formatMessage(messages.bienSo)}>
            {getFieldDecorator("number", {
              initialValue: currentEdit ? `${currentEdit.number}` : undefined,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.errorEmptyLicensePlate),
                  whitespace: true,
                },
              ],
            })(<Input maxLength={20} />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.typePaymentFee)}>
            {getFieldDecorator("service_parking_level_id", {
              initialValue: currentEdit
                ? currentEdit.service_parking_level_id
                : undefined,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.errorTypePaymentFee),
                  whitespace: true,
                  type: "number",
                },
              ],
            })(
              <Select
                loading={vihicleManagement.feeLevel.loading}
                placeholder={formatMessage(messages.selectTypePaymentFee)}
                optionFilterProp="children"
                notFoundContent={
                  vihicleManagement.feeLevel.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                onSearch={this._onSearch}
              >
                {vihicleManagement.feeLevel.items.map((gr) => {
                  return (
                    <Select.Option key={`group2-${gr.id}`} value={gr.id}>
                      {gr.name}
                    </Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.dateSend)}>
            {getFieldDecorator("start_date", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.start_date)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: formatMessage(messages.errorDateSend),
                },
              ],
            })(
              <DatePicker
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.selectDate)}
                format={"DD/MM/YYYY"}
              />
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.description)}>
            {getFieldDecorator("description", {
              initialValue: currentEdit ? currentEdit.description : undefined,
              rules: [],
            })(<Input.TextArea rows={3} maxLength={500} />)}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalCreate);
