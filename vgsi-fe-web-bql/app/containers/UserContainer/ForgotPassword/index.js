/**
 *
 * Login
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
import { defaultAction, getCaptchaAction, forgotPassAction } from "./actions";
import makeSelectRegister from "./selectors";
import { validateEmail } from "../../../utils";
import { Result } from "ant-design-pro";
import Loader from "../../../components/Loader";

const FormItem = Form.Item;

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ForgotPassword extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      loading: false,
    };
  }
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    // this.props.dispatch(getCaptchaAction());
  }

  forgotPassword = async (values) => {
    try {
      let res = await window.connection.forgotPassword({
        email: values.email,
        captcha_code: "",
      });
      if (res.success) {
        this.props.history.push("/user/verifyOtp", { email: values.email });
        this.setState({ loading: false });
      }
    } catch (e) {}
  };

  handleOk = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.forgotPassword(values);

      this.setState({ loading: true });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (
      this.props.register.success != nextProps.register.success &&
      nextProps.register.success
    ) {
      setTimeout(() => {
        this.props.history.push("/user/login");
      }, 3000);
    }
  }

  render() {
    let { formatMessage } = this.props.intl;
    const { form, register } = this.props;
    const { getFieldDecorator } = form;
    const { captcha, loading, success } = register;
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
              {formatMessage(messages.title)}
            </span>
          </Col>
          <Col offset={2} span={20}>
            {success && (
              <Result
                type="success"
                title={formatMessage(messages.subtitle)}
                description={
                  <div>
                    <span style={{ whiteSpace: "pre-wrap" }}>
                      {`${formatMessage(messages.subtitle2)}`}
                    </span>
                    <br />
                    <br />
                    <span
                      style={{
                        fontStyle: "italic",
                        fontWeight: "bold",
                        fontSize: 12,
                        color: "#262626",
                      }}
                    >
                      {formatMessage(messages.redirectLogin)}
                    </span>
                  </div>
                }
              />
              // <>
              //   <Row>
              //     <span className={styles.titleBtnResend} onClick={e => {
              //       e.preventDefault()
              //       this.props.dispatch(defaultAction())
              //       this.props.dispatch(getCaptchaAction())
              //     }} >{formatMessage(messages.titleResend)}</span>
              //   </Row>
              //   <Row style={{ marginTop: 24 }} >
              //     <Button
              //       type="primary"
              //       onClick={() => this.props.history.push('/user/login')}
              //     >
              //       <span style={{ fontWeight: 'bold' }} >{formatMessage(messages.titleLogin)}</span>
              //     </Button>
              //   </Row>
              // </>
            )}
            {!success && (
              <form>
                <FormItem>
                  {getFieldDecorator("email", {
                    rules: [
                      {
                        required: true,
                        whitespace: true,
                        message: `${formatMessage(
                          messages.placeholderUserName
                        )} ${formatMessage(messages.subErroEmpty)}`,
                      },
                      {
                        validator: (rule, value, callback) => {
                          if (
                            value &&
                            value.trim() != "" &&
                            !validateEmail(value)
                          ) {
                            callback(formatMessage(messages.errorEmailInvalid));
                          } else {
                            callback();
                          }
                        },
                      },
                    ],
                  })(
                    <Input
                      prefix={
                        <i
                          className="far fa-user-circle"
                          style={{ color: "rgba(0,0,0,.25)", fontSize: 18 }}
                        ></i>
                      }
                      onPressEnter={this.handleOk}
                      maxLength={50}
                      placeholder={formatMessage(messages.placeholderUserName)}
                    />
                  )}
                </FormItem>
                {/* <Row>
                  <Col xxl={14} sm={11}>
                    <FormItem>
                      {getFieldDecorator("captcha_code", {
                        rules: [
                          {
                            required: true,
                            whitespace: true,
                            message: `${formatMessage(
                              messages.placeholderCaptcha
                            )} ${formatMessage(messages.subErroEmpty)}`,
                          },
                        ],
                      })(
                        <Input
                          onPressEnter={this.handleOk}
                          placeholder={formatMessage(
                            messages.placeholderCaptcha
                          )}
                        />
                      )}
                    </FormItem>
                  </Col>
                  <Col
                    xxl={7}
                    sm={8}
                    className="ant-form-item-control"
                    style={{ textAlign: "center" }}
                  >
                    {!!captcha.data && (
                      <img
                        style={{ height: 50 }}
                        src={captcha.data}
                        alt="Red dot"
                      />
                    )}
                    {!captcha.data && <Icon type="loading" />}
                  </Col>
                  <Col
                    style={{ marginLeft: window.innerWidth > 1280 ? 0 : 12 }}
                    xxl={3}
                    sm={3}
                    className="ant-form-item-control"
                  >
                    <Tooltip title={formatMessage(messages.changeCaptcha)}>
                      <img
                        onClick={() => {
                          this.props.dispatch(getCaptchaAction());
                        }}
                        style={{ width: 35, height: 35, cursor: "pointer" }}
                        src={require("../../../images/recaptcha.png")}
                        alt="recaptcha"
                      />
                    </Tooltip>
                  </Col>
                </Row> */}
                <Row type="flex" align="middle" justify="space-between">
                  <Col span={14}>
                    <a
                      href="/user/login"
                      className={styles.titleBtnQuenMK}
                      onClick={(e) => {
                        e.preventDefault();
                        this.props.history.push("/user/login");
                      }}
                    >
                      {formatMessage(messages.titleForgot)}
                    </a>
                  </Col>
                  <Col span={8}>
                    <Button
                      type="primary"
                      onClick={this.handleOk}
                      loading={this.state.loading}
                      block
                    >
                      <span style={{ fontWeight: "bold" }}>
                        {formatMessage(messages.continue)}
                      </span>
                    </Button>
                  </Col>
                </Row>
              </form>
            )}
          </Col>
        </Row>
      </Row>
    );
  }
}

ForgotPassword.propTypes = {};

const mapStateToProps = createStructuredSelector({
  register: makeSelectRegister(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "register", reducer });
const withSaga = injectSaga({ key: "register", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ForgotPassword));
