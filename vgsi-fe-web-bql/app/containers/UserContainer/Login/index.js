/**
 *
 * Login
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectLogin from "./selectors";
import { selectBuildingCluster } from "../../../redux/selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";

import { Button, Row, Form, Input, Col, Tooltip, Modal, Select } from "antd";

import styles from "./index.less";
import { defaultAction, loginAction, getCaptchaAction } from "./actions";
import { Redirect } from "react-router";
import { notificationBar, validateEmail } from "../../../utils";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
const FormItem = Form.Item;

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class Login extends React.PureComponent {
  state = {
    loginLoading: false,
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  handleOk = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.setState({ loginLoading: true });
      window.connection
        .login({
          email: values.username,
          password: values.password,
        })
        .then((res) => {
          this.setState({ loginLoading: false });
          if (!res.success) {
            if (res.statusCode === 598) {
              Modal.confirm({
                autoFocusButton: null,
                title: this.props.intl.formatMessage(messages.confirmLogin),
                content: this.props.intl.formatMessage(messages.contentConfirm),
                cancelText: this.props.intl.formatMessage(messages.cancel),
                okText: this.props.intl.formatMessage(messages.agree),
                okType: "danger",
                onOk: () => {
                  dispatch(loginAction({ ...values, form, confirmLogin: 1 }));
                },
              });
            } else {
              notificationBar(res.message, "error");
            }
            // else {
            //   if (this.props.language === "vi") {
            //     notificationBar(res.message, "error");
            //   } else {
            //     notificationBar("The email or password is incorrect.", "error");
            //   }
            // }
          } else {
            dispatch(loginAction({ ...values, form, confirmLogin: 1 }));
          }
        });
    });
  };

  render() {
    let { formatMessage } = this.props.intl;
    const { login, form, i18n, buildingCluster, dispatch } = this.props;
    const { getFieldDecorator } = form;

    const { isLogining, success, countCallFall, captchaImage, captcha } = login;

    if (success) {
      return <Redirect to="/main/home" />;
    }

    return (
      <Row type="flex" align="middle" className={styles.loginPage}>
        <Row>
          <Col span={24} className={styles.logo}>
            <img
              style={{ width: 360, height: 120 }}
              src={require("../../../images/logo_waterpoint_lg.jpg")}
            />
          </Col>
          {/* <Col span={24} style={{ textAlign: "center", marginBottom: 20 }}>
            <span className={styles.titleLogin}>{`${formatMessage(
              messages.subtitle
            )} ${!!buildingCluster.data && buildingCluster.data.name}`}</span>
          </Col> */}
          <Col offset={2} span={20}>
            <Form wrapperCol={{ span: 24 }}>
              <FormItem>
                {getFieldDecorator("username", {
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
                      />
                    }
                    onPressEnter={this.handleOk}
                    placeholder={formatMessage(messages.placeholderUserName)}
                    maxLength={50}
                  />
                )}
              </FormItem>
              <FormItem>
                {getFieldDecorator("password", {
                  validateFirst: true,
                  rules: [
                    {
                      required: true,
                      whitespace: true,
                      message: `${formatMessage(
                        messages.placeholderPassword
                      )} ${formatMessage(messages.subErroEmpty)}`,
                    },
                    {
                      min: 8,
                      message: `${formatMessage(messages.errorPasswordLength)}`,
                    },
                  ],
                })(
                  <Input.Password
                    prefix={
                      <i
                        className="material-icons"
                        style={{ color: "rgba(0,0,0,.25)", fontSize: 18 }}
                      >
                        lock
                      </i>
                    }
                    type="password"
                    onPressEnter={this.handleOk}
                    placeholder={formatMessage(messages.placeholderPassword)}
                    maxLength={20}
                  />
                )}
              </FormItem>
              {/* {countCallFall >= 3 && (
                <Row>
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
                          className={styles.inputConfirmCode}
                        />
                      )}
                    </FormItem>
                  </Col>
                  <Col xxl={7} sm={8} className="ant-form-item-control">
                    <img
                      style={{ height: 50 }}
                      src={captcha.data ? captcha.data : captchaImage}
                      alt="Red dot"
                    />
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
                </Row>
              )} */}
              <Row
                type="flex"
                align="middle"
                justify="space-between"
                style={{ marginTop: 30 }}
              >
                <Col span={window.innerWidth <= 1280 ? 13 : 14}>
                  <a
                    href="/user/forgotpassword"
                    className={styles.titleBtnQuenMK}
                    onClick={(e) => {
                      e.preventDefault();
                      this.props.history.push("/user/forgotpassword");
                    }}
                  >
                    {formatMessage(messages.titleForgot)}
                  </a>
                </Col>
                <Col span={7}>
                  <Button
                    type="primary"
                    onClick={this.handleOk}
                    loading={isLogining || this.state.loginLoading}
                    block
                  >
                    <span style={{ fontWeight: "bold" }}>
                      {formatMessage(messages.title)}
                    </span>
                  </Button>
                </Col>
              </Row>
            </Form>
          </Col>
        </Row>
      </Row>
    );
  }
}

Login.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  login: makeSelectLogin(),
  buildingCluster: selectBuildingCluster(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "login", reducer });
const withSaga = injectSaga({ key: "login", saga });

export default compose(withReducer, withSaga, withConnect)(injectIntl(Login));
