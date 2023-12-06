import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage, injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectApartmentDetail from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "../messages";
import Page from "../../../components/Page/Page";
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
  Spin,
} from "antd";

import config from "../../../utils/config";
import { TwitterPicker } from "react-color";

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
export class ModalCreateCategory extends React.PureComponent {
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
        this.props.handlerUpdateCategory &&
          this.props.handlerUpdateCategory({
            ...values,
            id: currentEdit.id,
            order: values.order,
          });
      } else {
        this.props.handlerAddCategory && this.props.handlerAddCategory(values);
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
            ? formatMessage(messages.editCategory)
            : formatMessage(messages.createCategory)
        }
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (creating) return;
          setState({
            visibleModalCategory: false,
          });
        }}
        okText={
          currentEdit
            ? formatMessage(messages.update)
            : formatMessage(messages.add)
        }
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: creating }}
        cancelButtonProps={{ disabled: creating }}
        maskClosable={false}
        width="40%"
      >
        <Form
          {...formItemLayout}
          className="notificationCategoryPage"
          labelAlign="left"
        >
          <Form.Item label={formatMessage(messages.nameCategory)} colon={false}>
            {getFieldDecorator("name", {
              initialValue: currentEdit && currentEdit.name,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.emptyNameCategory),
                  whitespace: true,
                },
              ],
            })(<Input maxLength={255} />)}
          </Form.Item>
          <Form.Item
            label={`${formatMessage(messages.nameCategory)} (EN)`}
            colon={false}
          >
            {getFieldDecorator("name_en", {
              initialValue: currentEdit && currentEdit.name_en,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.emptyNameEnCategory),
                  whitespace: true,
                },
              ],
            })(<Input maxLength={255} />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.order)} colon={false}>
            {getFieldDecorator("order", {
              initialValue: currentEdit && currentEdit.order,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.emptyOrder),
                },
              ],
            })(<InputNumber maxLength={5} min={1} />)}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalCreateCategory);
