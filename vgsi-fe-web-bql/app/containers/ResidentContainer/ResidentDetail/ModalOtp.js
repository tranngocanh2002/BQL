/* eslint-disable react/prop-types */
import { Col, Form, Modal, Row } from "antd";
import React from "react";
import { injectIntl } from "react-intl";
import messages from "../messages";
import "./index.less";
import ReactCodeInput from "react-code-input";
import styles from "./index.less";
import CountDown from "components/CountDown";

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalOtp extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      loading: false,
      countdown: true,
    };
  }
  handlerUpdate = () => {
    const { form } = this.props;
    const { validateFieldsAndScroll, setFieldsValue } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.props.handlerUpdate({
        ...values,
        ngay_cap_cmtnd: values.ngay_cap_cmtnd
          ? values.ngay_cap_cmtnd.unix()
          : undefined,
      });
      setFieldsValue({ otp: "" });
    });
  };

  handlerCancel = () => {
    if (this.props.updating) return;
    this.props.setState({
      visible3: false,
    });
    this.props.form.setFieldsValue({ otp: "" });
    this.setState({ countdown: !this.state.countdown });
  };

  UNSAFE_componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.setState({
        countdown: !this.state.countdown,
      });
    }
  }
  componentWillUnmount() {
    console.log("componentWillUnmount", this.state);
  }

  componentDidMount() {
    console.log("componentDidMount", this.state);
    // this.props.dispatch(forgotPassAction());
    // this.countDown();
  }

  resendOTP = async () => {
    this.setState({ countdown: !this.state.countdown });
    this.props.handlerResend();
  };

  render() {
    const { updating, visible, number } = this.props;
    const {
      getFieldDecorator,
      getFieldError,
      setFieldsValue,
      resetFields,
      getFieldValue,
    } = this.props.form;
    const formatMessage = this.props.intl.formatMessage;
    const { countdown } = this.state;

    return (
      <Modal
        title={formatMessage(messages.verifyPhone)}
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={this.handlerCancel}
        okText={formatMessage(messages.authentication)}
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: updating }}
        cancelButtonProps={{ disabled: updating }}
        maskClosable={false}
        width={"40%"}
      >
        <Row className={styles.residentDetailPage}>
          <Col>
            <span style={{ textAlign: "center" }}>
              {formatMessage(messages.verifyPhoneContent, {
                field: ` *********${number.slice(-3)}`,
              })}
            </span>
            <Form style={{ paddingTop: 32 }}>
              <Form.Item style={{ alignItems: "center", textAlign: "center" }}>
                {getFieldDecorator("otp", {
                  initialValue: "",
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.otpRequired),
                    },
                    {
                      pattern: /^[0-9]{6}$/,
                      message: formatMessage(messages.otpInvalid),
                    },
                  ],
                })(
                  <ReactCodeInput
                    inputStyle={{
                      width: 32,
                      fontSize: 14,
                      height: 32,
                      MozAppearance: "textfield",
                      WebkitAppearance: "none",
                      borderBottom: getFieldError("otp")
                        ? "1px solid red"
                        : "1px solid black",
                      margin: "4px",
                      textAlign: "center",
                    }}
                    type="number"
                    fields={6}
                    onChange={(value) => {
                      setFieldsValue({ otp: value });
                    }}
                  />
                )}
              </Form.Item>
            </Form>
          </Col>
          <Col span={24} style={{ textAlign: "center", marginBottom: 20 }}>
            <CountDown
              time={60}
              reset={countdown}
              prefix={formatMessage(messages.resendOtp)}
            >
              <span
                style={{ cursor: "pointer", color: "#1278ED" }}
                onClick={this.resendOTP}
              >
                {formatMessage(messages.resendOtp)}
              </span>
            </CountDown>
          </Col>
        </Row>
      </Modal>
    );
  }
}

export default injectIntl(ModalOtp);
<style></style>;
