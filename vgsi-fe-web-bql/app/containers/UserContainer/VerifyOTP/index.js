/**
 *
 * VerifyOTP
 *
 */

import React from "react";
import { connect } from "react-redux";
import { injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";

import { Button, Row, Form, Icon, Input, Col, Tooltip } from "antd";

import styles from "./index.less";
import { defaultAction, verifyOTPAction } from "./actions";
// import { forgotPassAction } from "../ForgotPassword/actions";
import makeSelectVerifyOTP from "./selectors";
import { validateEmail } from "../../../utils";
import { Result } from "ant-design-pro";
import ReactCodeInput from "react-code-input";
const FormItem = Form.Item;

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class VerifyOTP extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      email: (props.location.state || {}).email,
      loading: false,
      countdown: 60,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    // this.props.dispatch(forgotPassAction());
    this.countDown();
  }

  countDown = () => {
    setInterval(() => {
      if (this.state.countdown > 0) {
        this.setState((prev) => ({
          countdown: prev.countdown - 1,
        }));
      }
      if (this.state.countdown === 0) {
        clearInterval(this.myTimeOut);
      }
    }, 1000);
  };

  resendOTP = async () => {
    this.setState({ countdown: 60 });
    try {
      let res = await window.connection.forgotPassword({
        email: this.state.email,
        captcha_code: "",
      });
    } catch (e) {}
  };

  verifyOTP = async (token) => {
    try {
      let res = await window.connection.verifyOTP({ token: token });
      if (res.success) {
        this.props.history.push("/user/resetpassword", { token });
        this.setState({ loading: false });
      } else {
        this.setState({ loading: false });
      }
    } catch (error) {}
  };

  handleOk = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.verifyOTP(values.otp);

      this.setState({ loading: true });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (
      this.props.verifyOTP.success != nextProps.verifyOTP.success &&
      nextProps.verifyOTP.success
    ) {
      setTimeout(() => {
        this.props.history.push("/user/login");
      }, 3000);
    }
  }

  render() {
    let { formatMessage } = this.props.intl;
    const { form, verifyOTP } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue, getFieldError } =
      form;
    const { loading, success } = verifyOTP;
    const { email, countdown } = this.state;

    return (
      <Row
        type="flex"
        align="middle"
        className={styles.forgotPasswordPage}
        style={{ marginLeft: 0, marginRight: 0 }}
      >
        <Row>
          <Col span={24} className={styles.logo}>
            <img
              style={{ width: 360, height: 120 }}
              src={require("../../../images/logo_waterpoint_lg.jpg")}
            />
          </Col>
          <Col span={24} style={{ textAlign: "center", marginBottom: 20 }}>
            <span className={styles.titleLogin}>
              {this.props.intl.formatMessage(messages.confirmOTP)}
            </span>
          </Col>
          <Col span={24} style={{ textAlign: "center", marginBottom: 20 }}>
            <span>
              {this.props.intl.formatMessage(messages.subTitle)}{" "}
              <strong>{email && email}</strong>
            </span>
          </Col>
          <Col span={24} style={{ textAlign: "center", marginBottom: 16 }}>
            <form>
              <FormItem>
                {getFieldDecorator("otp", {
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(messages.emptyOtp),
                    },
                  ],
                })(
                  <ReactCodeInput
                    inputStyle={{
                      width: 32,
                      borderRadius: 3,
                      fontSize: 14,
                      height: 32,
                      MozAppearance: "textfield",
                      border: getFieldError("otp")
                        ? "1px solid red"
                        : "1px solid black",
                      margin: "4px",
                      textAlign: "center",
                    }}
                    type="number"
                    fields={6}
                    onChange={(values) => {
                      setFieldsValue({ otp: values });
                    }}
                  />
                )}
              </FormItem>
            </form>
          </Col>
          <Col span={24} style={{ textAlign: "center", marginBottom: 20 }}>
            {countdown > 0 ? (
              <span>
                {this.props.intl.formatMessage(messages.resendOTP)} ({countdown}
                s)
              </span>
            ) : (
              <span
                style={{ cursor: "pointer", color: "#1278ED" }}
                onClick={this.resendOTP}
              >
                {this.props.intl.formatMessage(messages.resendOTP)}
              </span>
            )}
          </Col>
          <Col offset={0} span={24}>
            <Row type="flex" align="middle" justify="space-around">
              <Col offset={1} span={5}>
                <a
                  href="/user/forgotpassword"
                  className={styles.titleBtnQuenMK}
                  onClick={(e) => {
                    e.preventDefault();
                    this.props.history.push("/user/forgotpassword");
                  }}
                >
                  {this.props.intl.formatMessage(messages.back)}
                </a>
              </Col>
              <Col offset={1} span={5}>
                <Button
                  type="primary"
                  onClick={this.handleOk}
                  loading={this.state.loading}
                  block
                >
                  <span style={{ fontWeight: "bold" }}>
                    {this.props.intl.formatMessage(messages.next)}
                  </span>
                </Button>
              </Col>
            </Row>
          </Col>
        </Row>
      </Row>
    );
  }
}

VerifyOTP.propTypes = {};

const mapStateToProps = createStructuredSelector({
  verifyOTP: makeSelectVerifyOTP(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "verifyOTP", reducer });
const withSaga = injectSaga({ key: "verifyOTP", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(VerifyOTP));
