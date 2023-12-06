import React from "react";
import { injectIntl } from "react-intl";
import messages from "./messages";

import { Modal, Input, Form, Select } from "antd";

import config from "../../../utils/config";
import { TwitterPicker } from "react-color";
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
  }

  handlerUpdate = () => {
    const { currentEdit, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      if (currentEdit) {
        this.props.handlerUpdateMember &&
          this.props.handlerUpdateMember({
            ...values,
            id: currentEdit.id,
            auth_group_ids: values.auth_group_ids.map((ii) => parseInt(ii)),
          });
      } else {
        this.props.handlerAddMember &&
          this.props.handlerAddMember({
            ...values,
            auth_group_ids: values.auth_group_ids.map((ii) => parseInt(ii)),
          });
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
    const formatMessage = this.props.intl.formatMessage;
    const { creating, visible, setState, authGroup, currentEdit } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
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
            visible: false,
          });
        }}
        okText={
          currentEdit
            ? formatMessage(messages.update)
            : formatMessage(messages.addNew)
        }
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: creating }}
        cancelButtonProps={{ disabled: creating }}
        maskClosable={false}
      >
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item label={formatMessage(messages.categoryName)} colon={false}>
            {getFieldDecorator("name", {
              initialValue: currentEdit && currentEdit.name,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.categoryNameRequired),
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
            label={formatMessage(messages.categoryNameEn)}
            colon={false}
          >
            {getFieldDecorator("name_en", {
              initialValue: currentEdit && currentEdit.name_en,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.categoryNameEnRequired),
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
          <Form.Item label={formatMessage(messages.chooseColor)} colon={false}>
            {getFieldDecorator("color", {
              initialValue: currentEdit
                ? currentEdit.color
                : config.COLOR_LIST[0],
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.chooseColorRequired),
                  whitespace: true,
                },
              ],
            })(
              <div>
                <div
                  style={{
                    width: "100%",
                    height: 30,
                    background: `${getFieldValue("color")}`,
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
                      color={this.state.color}
                      onChange={({ hex }) => {
                        setFieldsValue({ color: hex });
                        this.setState({ showPickerColor: false, color: hex });
                      }}
                    />
                  </div>
                )}
              </div>
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.groupProcess)} colon={false}>
            {getFieldDecorator("auth_group_ids", {
              initialValue: currentEdit
                ? currentEdit.auth_group_ids.map((rr) => `${rr.id}`)
                : [],
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.groupProcessRequired),
                  whitespace: true,
                  type: "array",
                },
              ],
            })(
              <Select
                mode="multiple"
                loading={authGroup.loading}
                showSearch
                placeholder={formatMessage(messages.groupProcessPlaceholder)}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
              >
                {authGroup.lst.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}`}
                    >{`${gr.name}`}</Select.Option>
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

export default injectIntl(ModalCreate);
