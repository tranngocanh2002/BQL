/**
 *
 * CreatePassword
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
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";

import { Button, Row, Form, Icon, Input, Col, Tooltip } from "antd";
import { Result } from "ant-design-pro";

import styles from "./index.less";
import {
  defaultAction,
  checkTokenAction,
  createPasswordAction,
} from "./actions";
import makeSelectCreatePassword from "./selectors";
import Loader from "../../../components/Loader";
import queryString from "query-string";

const FormItem = Form.Item;

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class CreatePassword extends React.PureComponent {
  state = {};

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
      let params = queryString.parse(this.props.location.search);

      dispatch(createPasswordAction({ ...values, token: params.token }));
    });
  };

  componentDidMount() {
    let params = queryString.parse(this.props.location.search);
    this.props.dispatch(checkTokenAction(params));
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.createPassword.successCreate !=
        nextProps.createPassword.successCreate &&
      nextProps.createPassword.successCreate
    ) {
      setTimeout(() => {
        this.props.history.push("/user/login");
      }, 3000);
    }
  }

  render() {
    let { formatMessage } = this.props.intl;
    const { form, createPassword } = this.props;
    const { getFieldDecorator } = form;
    const { firstLoading, loading, successCreate } = createPassword;

    return (
      <Row
        type="flex"
        align="middle"
        className={styles.createPasswordPage}
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
            <span
              style={{
                display: "flex",
                justifyContent: "center",
                alignItems: "center",
              }}
            >
              <span className={styles.titleLogin}>{`${formatMessage(
                messages.title
              )} `}</span>
              <Tooltip title={formatMessage(messages.subtitle)}>
                <Icon type="info-circle" />
              </Tooltip>
            </span>
          </Col>
          {successCreate && (
            <Result
              type="success"
              title={formatMessage(messages.success)}
              description={
                <div>
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
          )}
          {/* <Col offset={2} span={20} style={{ marginBottom: 20, textAlign: 'center' }} >
            <span className={styles.subTitleLogin} >{formatMessage(messages.subtitle)}</span>
          </Col> */}
          {!successCreate && (
            <Col offset={2} span={20}>
              <form style={{ paddingBottom: 10 }}>
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
                        message: `${formatMessage(
                          messages.errorPasswordLength
                        )}`,
                      },
                      {
                        validator: (rule, value, callback) => {
                          const form = this.props.form;
                          if (value && this.state.confirmDirty) {
                            form.validateFields(["confirm_password"], {
                              force: true,
                            });
                          }
                          callback();
                        },
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
                    />
                  )}
                </FormItem>
                <FormItem>
                  {getFieldDecorator("confirm_password", {
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
                        message: `${formatMessage(
                          messages.errorPasswordLength
                        )}`,
                      },
                      {
                        validator: (rule, value, callback) => {
                          const form = this.props.form;
                          if (
                            value &&
                            value !== form.getFieldValue("password")
                          ) {
                            callback(formatMessage(messages.errorPassworMatch));
                          } else {
                            callback();
                          }
                        },
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
                      placeholder={formatMessage(messages.placeholderPassword2)}
                      onBlur={(e) => {
                        const value = e.target.value;
                        this.setState({
                          confirmDirty: this.state.confirmDirty || !!value,
                        });
                      }}
                    />
                  )}
                </FormItem>
                <Row type="flex" align="middle">
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
                      loading={loading}
                      disabled={firstLoading}
                      block
                    >
                      <span style={{ fontWeight: "bold" }}>
                        {formatMessage(messages.titleBtnKhoiPhuc)}
                      </span>
                    </Button>
                  </Col>
                </Row>
              </form>
              {firstLoading && <Loader />}
            </Col>
          )}
        </Row>
      </Row>
    );
  }
}

CreatePassword.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  createPassword: makeSelectCreatePassword(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "createPassword", reducer });
const withSaga = injectSaga({ key: "createPassword", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(CreatePassword));
