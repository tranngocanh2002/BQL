import {
  Checkbox,
  Col,
  DatePicker,
  Form,
  Input,
  Modal,
  Radio,
  Row,
  Select,
} from "antd";
import moment from "moment";
import React from "react";
import { injectIntl } from "react-intl";
import {
  regexOnlyTextAndNumber,
  regexPhoneNumberVN,
  regexVNCharacter,
} from "utils/constants";
import PhoneNumberInput from "../../../components/PhoneNumberInput";
import { validateEmail } from "../../../utils";
import messages from "../messages";
import "./index.less";
const formItemLayout = {
  labelCol: {
    span: 9,
  },
  wrapperCol: {
    span: 12,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalChangePhone extends React.PureComponent {
  handlerUpdate = () => {
    const { form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      const newValues = {
        ...values,
        type_auth: this.props.value,
      };
      this.props.handlerUpdate({
        ...newValues,
        ngay_cap_cmtnd: values.ngay_cap_cmtnd
          ? values.ngay_cap_cmtnd.unix()
          : undefined,
      });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
    }
  }

  render() {
    const { updating, visible, setState } = this.props;
    const { getFieldDecorator } = this.props.form;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Modal
        title={formatMessage(messages.phoneTitle)}
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (updating) return;
          setState({
            visible2: false,
          });
        }}
        okText={formatMessage(messages.agree)}
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: updating }}
        cancelButtonProps={{ disabled: updating }}
        maskClosable={false}
        width={"40%"}
      >
        <Row>
          <Col>
            <span style={{ textAlign: "center" }}>
              {formatMessage(messages.phoneContent)}
            </span>
            <Form {...formItemLayout} style={{ paddingTop: 48 }}>
              <Form.Item
                //labelAlign="left"
                label={formatMessage(messages.phone)}
              >
                {getFieldDecorator("resident_phone", {
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptyPhone),
                    },
                    {
                      pattern: regexPhoneNumberVN,
                      message: `${formatMessage(
                        messages.phone
                      )} ${formatMessage(messages.invalid)}`,
                      whitespace: false,
                    },
                  ],
                })(<PhoneNumberInput defaultOnChange={false} maxLength={10} />)}
              </Form.Item>
              <Form.Item
                label={`${formatMessage(messages.authenticationMethod)}:`}
                style={{ marginBottom: 0, textAlign: "left" }}
              >
                <Radio.Group
                  onChange={(e) => {
                    setState({
                      value: e.target.value,
                    });
                  }}
                  value={this.props.value}
                >
                  <Radio value={0} style={{ marginTop: 8, minWidth: 250 }}>
                    <span style={{ color: "rgba(0, 0, 0, 0.65)" }}>
                      {formatMessage(messages.otpPhone)}
                    </span>
                  </Radio>
                  <br />
                  <Radio value={1} style={{ marginTop: 8, minWidth: 250 }}>
                    <span style={{ color: "rgba(0, 0, 0, 0.65)" }}>
                      {formatMessage(messages.defaultPhone)}
                    </span>
                  </Radio>
                </Radio.Group>
              </Form.Item>
            </Form>
          </Col>
        </Row>
      </Modal>
    );
  }
}

export default injectIntl(ModalChangePhone);
