import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";

import messages from "./messages";
import { Modal, Input, Form, Select } from "antd";

import config from "../../../utils/config";
import { TwitterPicker } from "react-color";
import { notificationBar } from "../../../utils";
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
  }

  handlerUpdate = () => {
    const { currentEdit, form, isExitNotificationFee } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      if (currentEdit) {
        if (currentEdit.type == 0 && values.type == 1) {
          notificationBar(
            this.props.intl.formatMessage({ ...messages.cantUpdate }),
            "warning"
          );
        } else {
          this.props.handlerUpdateMember &&
            this.props.handlerUpdateMember({ ...values, id: currentEdit.id });
        }
      } else {
        if (isExitNotificationFee && values.type == 1) {
          notificationBar(
            this.props.intl.formatMessage({ ...messages.warning }),
            "warning"
          );
        } else {
          this.props.handlerAddMember && this.props.handlerAddMember(values);
        }
      }
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
    }
  }

  render() {
    const { showPickerColor } = this.state;
    const { creating, visible, setState, currentEdit } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    return (
      <Modal
        title={
          currentEdit ? (
            <FormattedMessage {...messages.editCategory} />
          ) : (
            <FormattedMessage {...messages.createCategory} />
          )
        }
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (creating) return;
          setState({
            visible: false,
          });
        }}
        okText={
          currentEdit ? (
            <FormattedMessage {...messages.update} />
          ) : (
            <FormattedMessage {...messages.create} />
          )
        }
        cancelText={<FormattedMessage {...messages.cancelText} />}
        okButtonProps={{ loading: creating }}
        cancelButtonProps={{ disabled: creating }}
        maskClosable={false}
        width="40%"
      >
        <Form {...formItemLayout} className="notificationCategoryPage">
          <Form.Item
            label={<FormattedMessage {...messages.categoryName} />}
            colon={false}
          >
            {getFieldDecorator("name", {
              initialValue: currentEdit && currentEdit.name,
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.categoryNameRequired} />
                  ),
                  whitespace: true,
                },
              ],
            })(
              <Input
                maxLength={50}
                onFocus={() => {
                  this.setState({ showPickerColor: false });
                }}
              />
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.categoryNameEn} />}
            colon={false}
          >
            {getFieldDecorator("name_en", {
              initialValue: currentEdit && currentEdit.name_en,
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.categoryNameEnRequired} />
                  ),
                  whitespace: true,
                },
              ],
            })(
              <Input
                maxLength={50}
                onFocus={() => {
                  this.setState({ showPickerColor: false });
                }}
              />
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.categoryType} />}
            colon={false}
          >
            {getFieldDecorator("type", {
              initialValue: currentEdit && currentEdit.type,
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.categoryTypeRequired} />
                  ),
                  whitespace: true,
                  type: "number",
                },
              ],
            })(
              <Select allowClear={false}>
                <Select.Option value={0}>
                  <FormattedMessage {...messages.regularAnnouncement} />
                </Select.Option>
                <Select.Option value={1}>
                  <FormattedMessage {...messages.feeAnnouncement} />
                </Select.Option>
                <Select.Option value={2}>
                  <FormattedMessage {...messages.surveyAnnouncement} />
                </Select.Option>
              </Select>
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.chooseColor} />}
            colon={false}
            style={{ marginBottom: 0 }}
          >
            {getFieldDecorator("label_color", {
              initialValue: currentEdit
                ? currentEdit.label_color
                : config.COLOR_LIST[0],
            })(
              <div>
                <div
                  style={{
                    width: "100%",
                    height: 30,
                    background: `${getFieldValue("label_color")}`,
                    borderRadius: 4,
                    marginTop: 4,
                  }}
                  onClick={(e) => {
                    e.preventDefault();
                    this.setState({
                      showPickerColor: !this.state.showPickerColor,
                    });
                  }}
                />
                {showPickerColor && (
                  <div style={{ position: "absolute", zIndex: 1000 }}>
                    <TwitterPicker
                      color={getFieldValue("label_color")}
                      onChange={({ hex }) => {
                        setFieldsValue({ label_color: hex });
                        this.setState({ showPickerColor: false });
                      }}
                    />
                  </div>
                )}
              </div>
            )}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalCreate);
