/*
 * Created by duydatpham@gmail.com on 04/02/2020
 * Copyright (c) 2020 duydatpham@gmail.com
 */
import React from "react";
import { Modal, Form, Input, Select, InputNumber } from "antd";
import InputNumberFormat from "../../../../../../components/InputNumberFormat";
import { injectIntl } from "react-intl";
import messages from "../../../messages";
@Form.create()
export class ModalAddSlot extends React.PureComponent {
  _onSave = (status, message) => {
    const { onSave, form } = this.props;
    const { validateFieldsAndScroll, setFields } = form;

    validateFieldsAndScroll((errors, values) => {
      if (errors) return;
      !!onSave && onSave(values);
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible && !nextProps.visible) {
      this.props.form.resetFields();
    }
  }

  render() {
    const { getFieldDecorator, getFieldsError, getFieldValue } =
      this.props.form;
    const { dataSlot } = this.props;

    return (
      <Modal {...this.props} onOk={this._onSave}>
        <Form>
          <Form.Item
            label={this.props.intl.formatMessage(messages.nameSlot)}
            style={{ marginTop: 0, marginBottom: 0 }}
          >
            {getFieldDecorator("name", {
              initialValue: dataSlot ? dataSlot.name : "",
              rules: [
                {
                  required: true,
                  message: this.props.intl.formatMessage(
                    messages.emptyNameSlot
                  ),
                  whitespace: true,
                },
              ],
            })(<Input style={{ width: "100%" }} maxLength={50} />)}
          </Form.Item>
          <Form.Item
            label={`${this.props.intl.formatMessage(messages.nameSlot)} (EN)`}
            style={{ marginTop: 0, marginBottom: 0 }}
          >
            {getFieldDecorator("name_en", {
              initialValue: dataSlot ? dataSlot.name_en : "",
              rules: [
                {
                  required: true,
                  message: this.props.intl.formatMessage(
                    messages.emptyNameSlotEn
                  ),
                  whitespace: true,
                },
              ],
            })(<Input style={{ width: "100%" }} maxLength={50} />)}
          </Form.Item>
          <Form.Item
            label={this.props.intl.formatMessage(messages.address)}
            style={{ marginTop: 0, marginBottom: 0 }}
          >
            {getFieldDecorator("address", {
              initialValue: dataSlot ? dataSlot.address : "",
              rules: [
                {
                  required: true,
                  message: this.props.intl.formatMessage(messages.emptyAddress),
                  whitespace: true,
                },
              ],
            })(<Input style={{ width: "100%" }} maxLength={255} />)}
          </Form.Item>
          <Form.Item
            label={`${this.props.intl.formatMessage(messages.address)} (EN)`}
            style={{ marginTop: 0, marginBottom: 0 }}
          >
            {getFieldDecorator("address_en", {
              initialValue: dataSlot ? dataSlot.address_en : "",
              rules: [
                {
                  required: true,
                  message: this.props.intl.formatMessage(
                    messages.emptyAddressEn
                  ),
                  whitespace: true,
                },
              ],
            })(<Input style={{ width: "100%" }} maxLength={255} />)}
          </Form.Item>
          <Form.Item
            label={this.props.intl.formatMessage(messages.Type)}
            style={{ marginTop: 0, marginBottom: 0 }}
          >
            {getFieldDecorator("type", {
              initialValue: dataSlot ? String(dataSlot.type) : "1",
              rules: [
                {
                  required: true,
                  message: "Loại phí không được để trống.",
                  whitespace: true,
                },
              ],
            })(
              <Select disabled={!!dataSlot}>
                <Select.Option value={"1"}>
                  {this.props.intl.formatMessage(messages.fee)}
                </Select.Option>
                <Select.Option value={"0"}>
                  {this.props.intl.formatMessage(messages.free)}
                </Select.Option>
              </Select>
            )}
          </Form.Item>
          <Form.Item
            label={this.props.intl.formatMessage(messages.slotFree)}
            style={{ marginTop: 0, marginBottom: 0 }}
          >
            {getFieldDecorator("total_slot", {
              initialValue: dataSlot ? dataSlot.total_slot : "",
              rules: [
                {
                  required: true,
                  message: this.props.intl.formatMessage(
                    messages.emptySlotFree
                  ),
                  whitespace: true,
                  type: "number",
                  min: 1,
                },
              ],
            })(<InputNumberFormat style={{ width: "100%" }} maxLength={6} />)}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalAddSlot);
