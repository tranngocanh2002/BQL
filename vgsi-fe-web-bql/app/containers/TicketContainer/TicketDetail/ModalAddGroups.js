import React from "react";
import { injectIntl } from "react-intl";

import messages from "./messages";
import { Modal, Form, Select } from "antd";
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
class ModalAddGroups extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      visibleAddGroups: false,
    };
  }

  handlerUpdate = () => {
    const { form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.props.handlerAddGroups &&
        this.props.handlerAddGroups({
          ...values,
          auth_group_ids: [values.auth_group_id],
        });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visibleAddGroups != nextProps.visibleAddGroups) {
      this.props.form.resetFields();
    }
  }

  render() {
    const {
      creating,
      visibleAddGroups,
      setState,
      authGroup,
      ignoreGroup,
      language,
    } = this.props;
    const formatMessage = this.props.intl.formatMessage;
    const { getFieldDecorator } = this.props.form;
    return (
      <Modal
        title={formatMessage(messages.addGroupProcess)}
        visible={visibleAddGroups}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (creating) return;
          setState({
            visibleAddGroups: false,
          });
        }}
        okText={formatMessage(messages.addNew)}
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: creating }}
        cancelButtonProps={{ disabled: creating }}
        maskClosable={false}
      >
        <Form {...formItemLayout} className="managerGroups">
          <Form.Item label={formatMessage(messages.groupProcess)} colon={false}>
            {getFieldDecorator("auth_group_id", {
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.groupProcessRequired),
                  whitespace: true,
                },
              ],
            })(
              <Select
                loading={authGroup.loading}
                showSearch
                placeholder={formatMessage(messages.chooseProcess)}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
              >
                {authGroup &&
                  authGroup.lst &&
                  authGroup.lst.map((gr) => {
                    if (ignoreGroup.includes(gr.id)) {
                      return;
                    }
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${
                        language === "vi" ? gr.name : gr.name_en
                      }`}</Select.Option>
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

export default injectIntl(ModalAddGroups);
