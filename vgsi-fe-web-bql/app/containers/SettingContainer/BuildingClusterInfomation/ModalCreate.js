/* eslint-disable react/display-name */
import React from "react";

import { Modal, Input, Form } from "antd";
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

import PhoneNumberInput from "../../../components/PhoneNumberInput";
import { regexPhoneNumberVN } from "utils/constants";

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export default class extends React.PureComponent {
  constructor(props) {
    super(props);
  }

  handlerUpdate = () => {
    const { setState, form, onAdd } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }

      !!onAdd && onAdd(values);
      setState({
        visible: false,
      });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
    }
  }

  render() {
    const { visible, setState, formatMessage } = this.props;
    const { getFieldDecorator } = this.props.form;
    return (
      <Modal
        title={formatMessage(messages.addHotline)}
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          setState({
            visible: false,
          });
        }}
        okText={formatMessage(messages.add)}
        cancelText={formatMessage(messages.cancel)}
        maskClosable={false}
        width="38%"
      >
        <Form {...formItemLayout}>
          <Form.Item
            label={formatMessage(messages.contactName)}
            colon={false}
            labelAlign={"left"}
          >
            {getFieldDecorator("title", {
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.contactError),
                  whitespace: true,
                },
              ],
            })(<Input maxLength={50} />)}
          </Form.Item>
          <Form.Item
            label={formatMessage(messages.contactNameEn)}
            colon={false}
            labelAlign={"left"}
          >
            {getFieldDecorator("title_en", {
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.contactErrorEn),
                  whitespace: true,
                },
              ],
            })(<Input maxLength={50} />)}
          </Form.Item>
          <Form.Item
            label={formatMessage(messages.contactNumber)}
            colon={false}
            labelAlign={"left"}
          >
            {getFieldDecorator("phone", {
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.contactNumberError),
                  whitespace: true,
                },
                {
                  pattern: regexPhoneNumberVN,
                  message: formatMessage(messages.phoneInvalid),
                },
              ],
            })(<PhoneNumberInput maxLength={10} />)}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}
