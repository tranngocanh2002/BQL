import React from "react";
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
} from "antd";
import { defaultAction, updateDetailBillAction } from "./actions";
import messages from "../messages";

import config from "../../../utils/config";
import { Redirect } from "react-router";

import "./index.less";
const TreeNode = TreeSelect.TreeNode;
const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 12,
  },
};
/* eslint-disable react/prefer-stateless-function */
// eslint-disable-next-line react/display-name
@Form.create()
export default class extends React.PureComponent {
  handlerUpdate = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.props.handlerUpdate(values);
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visibleEdit != nextProps.visibleEdit) {
      this.props.form.resetFields();
    }
  }

  render() {
    const { updating, visibleEdit, setState, recordBill, formatMessage } =
      this.props;
    const { getFieldDecorator, getFieldValue } = this.props.form;
    return (
      <Modal
        title={formatMessage(messages.editInformation)}
        visible={visibleEdit}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (updating) return;
          setState({
            visibleEdit: false,
          });
        }}
        maskClosable={false}
        okText={formatMessage(messages.update)}
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: updating }}
        cancelButtonProps={{ disabled: updating }}
      >
        <Form {...formItemLayout}>
          <Form.Item label={formatMessage(messages.feePayer)} colon={false}>
            {getFieldDecorator("payer_name", {
              initialValue: recordBill && recordBill.payer_name,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.payerError2),
                  whitespace: true,
                },
              ],
            })(<Input />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.payments)} colon={false}>
            {getFieldDecorator("type_payment", {
              initialValue: recordBill && recordBill.type_payment,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.paymentsError2),
                  whitespace: true,
                  type: "number",
                },
              ],
            })(
              <Select
                showSearch
                placeholder={formatMessage(messages.chosePayment2)}
                optionFilterProp="children"
                // onChange={onChange}
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
              >
                {config.TYPE_PAYMENT.map((gr) => {
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
