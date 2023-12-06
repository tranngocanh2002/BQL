/**
 *
 * StaffAdd
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import Exception from "ant-design-pro/lib/Exception";
import { Button, Col, DatePicker, Form, Input, Modal, Row, Select } from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import { withRouter } from "react-router";
import { regexPhoneNumberVN } from "utils/constants";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Avatar from "../../../components/Avatar";
import Page from "../../../components/Page/Page";
import PhoneNumberInput from "../../../components/PhoneNumberInput";
import { getFullLinkImage } from "../../../connection";
import { selectUserDetail } from "../../../redux/selectors";
import { validateEmail, validateName } from "../../../utils";
import {
  createStaffAction,
  defaultAction,
  fetchDetailAction,
  fetchGroupAuthAction,
  updateStaffAction,
  updateStaffAndDetail,
} from "./actions";
import messages from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectStaffAdd from "./selectors";

const confirm = Modal.confirm;

const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 14,
  },
  labelAlign: "left",
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class StaffAdd extends React.PureComponent {
  constructor(props) {
    super(props);
    let record = (props.location.state || {}).record;
    let link = (props.location.state || {}).link;
    this.state = {
      record,
      link,
      imageUrl: record ? record.avatar : undefined,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchGroupAuthAction());

    const { id } = this.props.match.params;
    if (id != undefined && !this.state.record) {
      this.props.dispatch(fetchDetailAction({ id }));
    }
  }

  componentWillReceiveProps(nextProps) {
    if (this.props.staffAdd.detail.data != nextProps.staffAdd.detail.data) {
      this.setState({
        record: nextProps.staffAdd.detail.data,
        imageUrl: nextProps.staffAdd.detail.data.avatar,
      });
    }
  }

  handerCancel = () => {
    // console.log("ádadas", this.state.link);
    // if (this.state.link) {
    //   this.props.history.goback;
    // }
    this.props.history.push("/main/setting/building/staff/list");
  };

  handlerUpdate = () => {
    const { dispatch, form, userDetail } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }

      if (this.state.record.id == userDetail.id) {
        dispatch(
          updateStaffAndDetail({
            ...values,
            auth_group_id: parseInt(values.auth_group_id),
            id: this.state.record.id,
            avatar: this.state.imageUrl,
            birthday: values.birthday ? values.birthday.unix() : undefined,
          })
        );
      } else {
        dispatch(
          updateStaffAction({
            ...values,
            auth_group_id: parseInt(values.auth_group_id),
            id: this.state.record.id,
            avatar: this.state.imageUrl,
            birthday: values.birthday ? values.birthday.unix() : undefined,
          })
        );
      }
    });
  };
  handleOk = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;

    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }

      dispatch(
        createStaffAction({
          ...values,
          auth_group_id: parseInt(values.auth_group_id),
          avatar: this.state.imageUrl,
          birthday: values.birthday ? values.birthday.unix() : undefined,
        })
      );
    });
  };
  render() {
    const { staffAdd } = this.props;
    const { formatMessage } = this.props.intl;
    const { getFieldDecorator } = this.props.form;
    const { creating, success, updating, updateSuccess, detail } = staffAdd;
    const { loading, lst } = staffAdd.authGroup;
    const { record } = this.state;
    if (success || updateSuccess) {
      this.props.history.goBack();
    }
    if (detail.data == -1) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={formatMessage(messages.notFindPage)}
            actions={
              <Button
                type="primary"
                onClick={() =>
                  this.props.history.push("/main/setting/building/staff/list")
                }
              >
                {formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }
    return (
      <Page inner loading={detail.loading}>
        <Row gutter={24} style={{ marginTop: 40 }}>
          <Row gutter={24} type="flex" justify="space-between" align="middle">
            <Col
              span={12}
              style={{
                fontWeight: "bold",
                fontSize: 18,
                textAlign: "left",
                marginBottom: 32,
                marginLeft: 32,
              }}
            >
              {this.props.intl.formatMessage(messages.information)}
            </Col>
          </Row>
          <Col span={14} style={{ marginLeft: 32 }}>
            <Form {...formItemLayout} onSubmit={this.handleSubmit}>
              <Form.Item label={formatMessage(messages.employeeCode)}>
                {getFieldDecorator("code_management_user", {
                  initialValue: record && record.code_management_user,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.employeeCodeRequired),
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (
                          value &&
                          value.trim() != "" &&
                          !/^[a-zA-Z0-9]+$/.test(value)
                        ) {
                          callback(formatMessage(messages.employeeCodeInvalid));
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(<Input style={{ width: "100%" }} maxLength={10} />)}
              </Form.Item>
              <Form.Item label={formatMessage(messages.fullName)}>
                {getFieldDecorator("first_name", {
                  initialValue: record && record.first_name,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.fullNameRequired),
                      whitespace: true,
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (
                          value &&
                          value.trim() != "" &&
                          !validateName(value)
                        ) {
                          callback(formatMessage(messages.nameInvalid));
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(<Input style={{ width: "100%" }} maxLength={50} />)}
              </Form.Item>
              <Form.Item label={"Email"}>
                {getFieldDecorator("email", {
                  initialValue: record && record.email,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emailRequired),
                      whitespace: true,
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (
                          value &&
                          value.trim() != "" &&
                          !validateEmail(value)
                        ) {
                          callback(formatMessage(messages.emailInvalid));
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(<Input maxLength={50} disabled={record ? true : false} />)}
              </Form.Item>
              <Form.Item label={formatMessage(messages.phone)}>
                {getFieldDecorator("phone", {
                  initialValue: record && `0${record.phone.slice(-9)}`,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.phoneRequired),
                      whitespace: true,
                    },
                    {
                      pattern: regexPhoneNumberVN,
                      message: formatMessage(messages.phoneInvalid),
                    },
                  ],
                })(<PhoneNumberInput maxLength={10} />)}
              </Form.Item>
              <Form.Item label={formatMessage(messages.birthday)}>
                {getFieldDecorator("birthday", {
                  initialValue:
                    record && record.birthday
                      ? moment.unix(record.birthday)
                      : undefined,
                  rules: [
                    {
                      type: "object",
                    },
                  ],
                })(
                  <DatePicker
                    style={{ width: "100%" }}
                    placeholder={formatMessage(messages.selectDate)}
                    format="DD/MM/YYYY"
                    disabledDate={(current) => {
                      return current && current > moment().endOf("day");
                    }}
                  />
                )}
              </Form.Item>
              <Form.Item label={formatMessage(messages.gender)}>
                {getFieldDecorator("gender", {
                  initialValue:
                    record && record.gender ? record.gender : undefined,
                  rules: [],
                })(
                  <Select
                    loading={loading}
                    showSearch
                    placeholder={formatMessage(messages.selectGender)}
                    optionFilterProp="children"
                    filterOption={(input, option) =>
                      option.props.children
                        .toLowerCase()
                        .indexOf(input.toLowerCase()) >= 0
                    }
                  >
                    <Select.Option key={1} value={1}>
                      {formatMessage(messages.male)}
                    </Select.Option>
                    <Select.Option key={2} value={2}>
                      {formatMessage(messages.female)}
                    </Select.Option>
                  </Select>
                )}
              </Form.Item>
              <Form.Item label={formatMessage(messages.authGroup)}>
                {getFieldDecorator("auth_group_id", {
                  initialValue:
                    record && record.auth_group
                      ? this.props.language === "vi"
                        ? record.auth_group.name
                        : record.auth_group.name_en
                      : undefined,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.authGroupRequired),
                      whitespace: true,
                    },
                  ],
                })(
                  <Select
                    loading={loading}
                    showSearch
                    placeholder={formatMessage(messages.chooseAuthGroup)}
                    optionFilterProp="children"
                    // onChange={onChange}
                    filterOption={(input, option) =>
                      option.props.children
                        .toLowerCase()
                        .indexOf(input.toLowerCase()) >= 0
                    }
                  >
                    {lst.map((gr) => {
                      return (
                        <Select.Option
                          key={`group-${gr.id}`}
                          value={`${gr.id}`}
                        >
                          {this.props.language == "vi" ? gr.name : gr.name_en}
                        </Select.Option>
                      );
                    })}
                  </Select>
                )}
              </Form.Item>
            </Form>
            <Col offset={8} style={{ paddingLeft: 0 }}>
              <Button
                disabled={updating}
                style={{}}
                ghost
                type="danger"
                onClick={
                  this.state.link
                    ? (e) => {
                        e.preventDefault();
                        this.props.history.goBack();
                      }
                    : this.handerCancel
                }
              >
                {formatMessage(messages.cancel)}
              </Button>
              {!record && (
                <Button
                  ghost
                  type="primary"
                  style={{ marginLeft: 10 }}
                  onClick={this.handleOk}
                  loading={creating}
                >
                  {formatMessage(messages.addStaff)}
                </Button>
              )}
              {!!record && (
                <>
                  <Button
                    loading={updating}
                    style={{ marginLeft: 10 }}
                    ghost
                    type="primary"
                    onClick={this.handlerUpdate}
                  >
                    {formatMessage(messages.update)}
                  </Button>
                </>
              )}
            </Col>
          </Col>
          <Col
            span={10}
            style={{
              width: window.innerWidth <= 1440 ? "30%" : "20%",
              height: "100%",
            }}
          >
            <Avatar
              imageUrl={getFullLinkImage(this.state.imageUrl)}
              onUploaded={(url) => this.setState({ imageUrl: url })}
            />
          </Col>
        </Row>
      </Page>
    );
  }
}

StaffAdd.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  staffAdd: makeSelectStaffAdd(),
  userDetail: selectUserDetail(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "staffAdd", reducer });
const withSaga = injectSaga({ key: "staffAdd", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(StaffAdd)));
