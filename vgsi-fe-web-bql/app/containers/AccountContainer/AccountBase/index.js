/**
 *
 * AccountBase
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
import makeSelectAccountBase from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";
import { Row, Col, Form, Input, DatePicker, Select, Button } from "antd";
import Avatar from "../../../components/Avatar";
import { getFullLinkImage } from "../../../connection";
import { selectUserDetail } from "../../../redux/selectors";
import { defaultAction, fetchDetail, updateInfo } from "./actions";
import Page from "../../../components/Page/Page";
import moment from "moment";
import PhoneNumberInput from "../../../components/PhoneNumberInput";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { isVnPhone } from "utils";
import { regexPhoneNumberVN } from "utils/constants";
import("./index.less");

const formItemLayout = {
  labelCol: {
    xl: { span: 8 },
  },
  wrapperCol: {
    xl: { span: 14 },
  },
};
const colProps = {
  xs: 24,
  sm: 24,
  md: 24,
  lg: 12,
  xl: 12,
  xxl: 12,
};

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class AccountBase extends React.PureComponent {
  state = {
    updateInfo: true,
  };

  _updateInfo = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      dispatch(
        updateInfo({
          id: this.props.userDetail.id,
          ...values,
          birthday: values.birthday ? values.birthday.unix() : undefined,
          avatar: this.state.imageUrl,
        })
      );
      this.setState({ updateInfo: false });
    });
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchDetail({ id: this.props.userDetail.id }));
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.accountBase.detail.loading !=
        nextProps.accountBase.detail.loading &&
      !nextProps.accountBase.detail.loading &&
      nextProps.accountBase.detail.data
    ) {
      this.setState({
        imageUrl: nextProps.accountBase.detail.data.avatar,
      });
    }
    if (
      this.props.accountBase.updateSuccess !=
        nextProps.accountBase.updateSuccess &&
      nextProps.accountBase.updateSuccess &&
      nextProps.accountBase.detail.data
    ) {
      this.setState({
        imageUrl: nextProps.accountBase.detail.data.avatar,
      });
      this.props.form.resetFields();
    }
  }

  render() {
    const { imageUrl, updateInfo } = this.state;
    const { accountBase, intl, language } = this.props;
    const chooseDateText = intl.formatMessage({ ...messages.chooseDate });
    const { getFieldDecorator } = this.props.form;
    const { loading, data } = accountBase.detail;
    console.log(imageUrl);
    return (
      <Page inner noPadding loading={loading}>
        <Row>
          <Col>
            <Row className="basicPage">
              <Col {...colProps}>
                <Form {...formItemLayout} labelAlign="left">
                  <span style={{ fontWeight: "bold", fontSize: 18 }}>
                    <FormattedMessage {...messages.information} />
                  </span>

                  <Form.Item
                    label={<FormattedMessage {...messages.name} />}
                    style={{ marginTop: 24 }}
                  >
                    {getFieldDecorator("first_name", {
                      initialValue: data ? data.first_name : "",
                      rules: [
                        {
                          required: true,
                          message: (
                            <FormattedMessage {...messages.nameRequired} />
                          ),
                          whitespace: true,
                        },
                      ],
                    })(
                      <Input
                        disabled
                        style={{ width: "100%" }}
                        maxLength={50}
                      />
                    )}
                  </Form.Item>
                  <Form.Item label={"Email"}>
                    {getFieldDecorator("email", {
                      initialValue: data ? data.email : "",
                      rules: [
                        {
                          required: true,
                        },
                      ],
                      // rules: [
                      //   { required: true, message: 'Email không được để trống.', whitespace: true },
                      //   {
                      //     validator: (rule, value, callback) => {
                      //       if (value && value.trim() != '' && !validateEmail(value)) {
                      //         callback('Email không đúng định dạng.');
                      //       } else {
                      //         callback();
                      //       }
                      //     }
                      //   }
                      // ],
                    })(<Input disabled style={{ width: "100%" }} />)}
                  </Form.Item>
                  <Form.Item label={<FormattedMessage {...messages.phone} />}>
                    {getFieldDecorator("phone", {
                      initialValue: data ? `0${data.phone.slice(-9)}` : "",
                      rules: [
                        {
                          required: true,
                          message: this.props.intl.formatMessage(
                            messages.phoneRequired
                          ),
                        },
                        {
                          pattern: regexPhoneNumberVN,
                          message: this.props.intl.formatMessage(
                            messages.phoneInvalid
                          ),
                        },
                      ],
                    })(
                      <PhoneNumberInput
                        maxLength={10}
                        style={{ width: "100%" }}
                        placeholder={this.props.intl.formatMessage(
                          messages.phonePlaceholder
                        )}
                        onChange={(e) => {
                          this.setState({
                            updateInfo:
                              e.target.value !== data.phone ? false : true,
                          });
                        }}
                      />
                    )}
                  </Form.Item>
                  <Form.Item
                    label={<FormattedMessage {...messages.birthday} />}
                  >
                    {getFieldDecorator("birthday", {
                      initialValue: data
                        ? moment.unix(data.birthday)
                        : undefined,
                      rules: [
                        {
                          message: (
                            <FormattedMessage {...messages.birthdayRequired} />
                          ),
                          type: "object",
                        },
                      ],
                    })(
                      <DatePicker
                        format="DD/MM/YYYY"
                        style={{ width: "100%" }}
                        placeholder={chooseDateText}
                        disabledDate={(current) => {
                          // Can not select days before today and today
                          return current && current > moment().startOf("day");
                        }}
                        disabled
                      />
                    )}
                  </Form.Item>
                  <Form.Item label={<FormattedMessage {...messages.gender} />}>
                    {getFieldDecorator("gender", {
                      initialValue: data ? data.gender : undefined,
                    })(
                      <Select
                        placeholder={
                          <FormattedMessage
                            {...messages.chooseGenderPlaceholder}
                          />
                        }
                        disabled
                      >
                        <Select.Option value={1}>
                          <FormattedMessage {...messages.male} />
                        </Select.Option>
                        <Select.Option value={2}>
                          <FormattedMessage {...messages.female} />
                        </Select.Option>
                        <Select.Option value={0}>
                          <FormattedMessage {...messages.female} />
                        </Select.Option>
                      </Select>
                    )}
                  </Form.Item>
                  <Form.Item
                    label={<FormattedMessage {...messages.authGroup} />}
                  >
                    {getFieldDecorator("auth_group", {
                      initialValue: data
                        ? language === "vi"
                          ? data.auth_group.name
                          : data.auth_group.name_en
                        : "",
                      rules: [
                        {
                          required: true,
                        },
                      ],
                    })(<Input disabled style={{ width: "100%" }} />)}
                  </Form.Item>
                </Form>
              </Col>
              <Col
                {...{
                  xs: 24,
                  sm: 24,
                  md: 24,
                  lg: { span: 10, offset: 1 },
                  xl: { span: 10, offset: 1 },
                  xxl: { span: 10, offset: 1 },
                }}
                style={{
                  marginBottom: 24,
                  width: window.innerWidth >= 1440 ? "30%" : null,
                }}
              >
                <Avatar
                  imageUrl={getFullLinkImage(imageUrl)}
                  onUploaded={(url) =>
                    this.setState({
                      imageUrl: url || imageUrl,
                      updateInfo: false,
                    })
                  }
                />
              </Col>
            </Row>
          </Col>
          <Col {...colProps}>
            <Form {...formItemLayout}>
              <Form.Item label={" "} style={{}} colon={false}>
                <Button
                  type="primary"
                  loading={accountBase.updating}
                  onClick={this._updateInfo}
                  disabled={updateInfo}
                >
                  <FormattedMessage {...messages.updateInfo} />
                </Button>
              </Form.Item>
            </Form>
          </Col>
        </Row>
      </Page>
    );
  }
}

AccountBase.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  accountBase: makeSelectAccountBase(),
  userDetail: selectUserDetail(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "accountBase", reducer });
const withSaga = injectSaga({ key: "accountBase", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(AccountBase));
