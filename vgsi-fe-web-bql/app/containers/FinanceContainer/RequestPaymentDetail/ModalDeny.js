import React from "react";
import { Modal, Input, Form, Select, AutoComplete } from "antd";
import messages from "./messages";
import config from "../../../utils/config";
import { injectIntl } from "react-intl";

const formItemLayout = {
  labelCol: {
    span: 24,
  },
  wrapperCol: {
    span: 24,
  },
};

@Form.create()
export class ModalDeny extends React.PureComponent {
  constructor(props) {
    super(props);
  }
  handleDeny = () => {
    const { form, record } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.props.handleDeny(record);
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.showModalDeny != nextProps.showModalDeny) {
      this.props.form.resetFields();
    }
  }

  componentDidMount() {}

  render() {
    const { setState, showModalDeny } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    return (
      <Modal
        title={
          <strong>
            {this.props.intl.formatMessage(
              messages.confirmDeleteRequestPayment
            )}
          </strong>
        }
        visible={showModalDeny}
        onOk={this.handleDeny}
        onCancel={() => {
          setState({
            showModalDeny: false,
          });
        }}
        okText={this.props.intl.formatMessage(messages.agree)}
        cancelText={this.props.intl.formatMessage(messages.cancel)}
        closable={false}
      >
        <Form {...formItemLayout}>
          <Form.Item>
            {getFieldDecorator("reason", {
              initialValue: "",
              rules: [
                {
                  required: true,
                  whitespace: true,
                  message: this.props.intl.formatMessage(messages.emptyReason),
                },
              ],
            })(
              <Input.TextArea
                placeholder={this.props.intl.formatMessage(
                  messages.enterReason
                )}
                rows={4}
                maxLength={200}
              />
            )}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalDeny);
