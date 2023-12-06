/**
 *
 * AccountChangePassword
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage, injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectAccountChangePassword from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";
import { Row, Col, Form, Input, Button, Modal, Result } from "antd";
import { defaultAction, changePassword } from "./actions";
import { Redirect } from "react-router";
import { logout } from "redux/actions/config";
import("./index.less");
const formItemLayout = {
  labelCol: {
    xl: { span: 10 },
    lg: { span: 12 },
    xxl: { span: 8 },
  },
  wrapperCol: {
    xl: { span: 14 },
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class AccountChangePassword extends React.PureComponent {
  state = {
    confirmDirty: false,
  };

  handleConfirmBlur = (e) => {
    const value = e.target.value;
    this.setState({ confirmDirty: this.state.confirmDirty || !!value });
  };

  _onChange = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      dispatch(changePassword(values));
    });
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  render() {
    const { changing, success } = this.props.accountChangePassword;
    const { intl, dispatch } = this.props;
    const confirmPasswordNotMatchText = intl.formatMessage({
      ...messages.reNewPasswordNotMatch,
    });
    const oldPasswordPlaceholderText = intl.formatMessage({
      ...messages.oldPasswordPlaceholder,
    });
    const newPasswordPlaceholderText = intl.formatMessage({
      ...messages.newPasswordPlaceholder,
    });
    const confirmPasswordPlaceholderText = intl.formatMessage({
      ...messages.reNewPasswordPlaceholder,
    });

    // if (success) {
    //   return <Redirect to="/main/account/settings/security" />;
    // }

    const { getFieldDecorator } = this.props.form;
    return (
      <Row className="changePassPage">
        <Col style={{ marginBottom: 32 }}>
          <span style={{ fontWeight: "bold", fontSize: 18 }}>
            <FormattedMessage {...messages.changePassword} />
          </span>
        </Col>
        <Col span={16} offset={4}>
          <Form {...formItemLayout} labelAlign="left">
            <Form.Item label={<FormattedMessage {...messages.oldPassword} />}>
              {getFieldDecorator("old_password", {
                validateFirst: true,
                rules: [
                  {
                    required: true,
                    message: (
                      <FormattedMessage {...messages.oldPasswordRequired} />
                    ),
                    whitespace: true,
                  },
                  {
                    min: 8,
                    message: (
                      <FormattedMessage {...messages.oldPasswordMinLength} />
                    ),
                    whitespace: true,
                  },
                ],
              })(
                <Input.Password
                  style={{ width: "100%" }}
                  placeholder={oldPasswordPlaceholderText}
                  maxLength={20}
                />
              )}
            </Form.Item>
            <Form.Item label={<FormattedMessage {...messages.newPassword} />}>
              {getFieldDecorator("password", {
                validateFirst: true,
                rules: [
                  {
                    required: true,
                    message: (
                      <FormattedMessage {...messages.newPasswordRequired} />
                    ),
                    whitespace: true,
                  },
                  {
                    min: 8,
                    message: (
                      <FormattedMessage {...messages.newPasswordMinLength} />
                    ),
                    whitespace: true,
                  },
                  {
                    validator: (rule, value, callback) => {
                      const form = this.props.form;
                      if (value && this.state.confirmDirty) {
                        form.validateFields(["confirm_password"], {
                          force: true,
                        });
                      }
                      if (
                        value &&
                        value === form.getFieldValue("old_password")
                      ) {
                        callback(
                          this.props.intl.formatMessage(
                            messages.passwordNotMatch
                          )
                        );
                      }
                      callback();
                    },
                  },
                ],
              })(
                <Input.Password
                  style={{ width: "100%" }}
                  placeholder={newPasswordPlaceholderText}
                  onBlur={this.handleConfirmBlur}
                  maxLength={20}
                />
              )}
            </Form.Item>
            <Form.Item label={<FormattedMessage {...messages.reNewPassword} />}>
              {getFieldDecorator("confirm_password", {
                validateFirst: true,
                rules: [
                  {
                    required: true,
                    message: (
                      <FormattedMessage {...messages.reNewPasswordRequired} />
                    ),
                    whitespace: true,
                  },
                  {
                    min: 8,
                    message: (
                      <FormattedMessage {...messages.reNewPasswordMinLength} />
                    ),
                    whitespace: true,
                  },
                  {
                    validator: (rule, value, callback) => {
                      const form = this.props.form;
                      if (value && value !== form.getFieldValue("password")) {
                        callback(confirmPasswordNotMatchText);
                      } else {
                        callback();
                      }
                    },
                  },
                ],
              })(
                <Input.Password
                  style={{ width: "100%" }}
                  placeholder={confirmPasswordPlaceholderText}
                  maxLength={20}
                />
              )}
            </Form.Item>
            <Form.Item label={" "} colon={false}>
              <Button
                // ghost
                type="primary"
                loading={changing}
                onClick={this._onChange}
              >
                <FormattedMessage {...messages.update} />
              </Button>
              {/* <Button
                ghost
                type="danger"
                style={{ marginRight: 10 }}
                onClick={() =>
                  this.props.history.push("/main/account/settings/security")
                }
                disabled={changing}
              >
                <FormattedMessage {...messages.back} />
              </Button> */}
            </Form.Item>
          </Form>

          <Modal visible={success} closable={false} footer={null}>
            <Result
              status="success"
              subTitle={
                <span style={{ fontSize: 16, color: "#000" }}>
                  {this.props.intl.formatMessage(
                    messages.changePasswordSuccess
                  )}
                </span>
              }
              extra={
                <Button type="primary" onClick={() => dispatch(logout())}>
                  {this.props.intl.formatMessage(messages.close)}
                </Button>
              }
            />
          </Modal>
        </Col>
      </Row>
    );
  }
}

AccountChangePassword.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  accountChangePassword: makeSelectAccountChangePassword(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "accountChangePassword", reducer });
const withSaga = injectSaga({ key: "accountChangePassword", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(AccountChangePassword));
