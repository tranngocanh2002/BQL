import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "../messages";
import { Modal, TreeSelect, Input, Form } from "antd";

import("./index.less");
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
@Form.create()
export class ModalEditCard extends React.PureComponent {
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
        this.props.handlerUpdate &&
          this.props.handlerUpdate({
            ...values,
            id: currentEdit.id,
          });
      } else {
        this.props.handlerAdd && this.props.handlerAdd(values);
      }
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
    }
  }

  render() {
    const { creating, visible, setState, currentEdit } = this.props;
    const { getFieldDecorator } = this.props.form;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Modal
        title={
          currentEdit
            ? formatMessage(messages.editCard)
            : formatMessage(messages.addCombineCard)
        }
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (creating) return;
          setState({
            visibleAddCard: false,
          });
        }}
        okText={
          currentEdit
            ? formatMessage(messages.update)
            : formatMessage(messages.addNew)
        }
        cancelText={formatMessage(messages.cancelText)}
        okButtonProps={{ loading: creating }}
        cancelButtonProps={{ disabled: creating }}
        maskClosable={false}
        width="40%"
      >
        <Form
          {...formItemLayout}
          className="notificationPage"
          labelAlign="left"
        >
          <Form.Item label={formatMessage(messages.cardId)} colon={false}>
            {getFieldDecorator("code", {
              initialValue: currentEdit && currentEdit.code,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.errorEmpty, {
                    field: formatMessage(messages.cardId),
                  }),
                  whitespace: true,
                },
              ],
            })(<Input maxLength={255} />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.cardNumber)} colon={false}>
            {getFieldDecorator("number", {
              initialValue: currentEdit && currentEdit.number,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.errorEmpty, {
                    field: formatMessage(messages.cardNumber),
                  }),
                  whitespace: true,
                },
              ],
            })(<Input maxLength={255} />)}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalEditCard);
