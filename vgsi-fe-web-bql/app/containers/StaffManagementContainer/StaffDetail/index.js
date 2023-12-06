/**
 *
 * StaffDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import Exception from "ant-design-pro/lib/Exception";
import {
  Button,
  Checkbox,
  Col,
  Dropdown,
  Empty,
  Form,
  Icon,
  Input,
  Menu,
  Modal,
  Row,
} from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import dateFormat from "dateformat";
import queryString from "query-string";
import { withRouter } from "react-router-dom";
import { selectAuthGroup } from "redux/selectors";
import config from "utils/config";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Avatar from "../../../components/Avatar";
import Page from "../../../components/Page/Page";
import { getFullLinkImage } from "../../../connection";
import {
  changeStatusStaffAction,
  defaultAction,
  deleteStaffAction,
  fetchDetailAction,
  resetPasswordAction,
} from "./actions";
import messages from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectStaffDetail from "./selectors";

const confirm = Modal.confirm;

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class StaffDetail extends React.PureComponent {
  constructor(props) {
    super(props);
    let record = (props.location.state || {}).record;
    this.state = {
      record,
      imageUrl: record ? record.avatar : undefined,
      visibleModalResetPassword: false,
      isSendEmail: false,
      link: `/main/setting/building/staff/detail/${record.id}`,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    const { id } = this.props.match.params;
    this.reload(this.props.location.search);
    if (id != undefined && !this.state.record) {
      this.props.dispatch(fetchDetailAction({ id }));
    }
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
    if (
      this.props.staffDetail.detail.data != nextProps.staffDetail.detail.data
    ) {
      this.setState({
        record: nextProps.staffDetail.detail.data,
        imageUrl: nextProps.staffDetail.detail.data.avatar,
      });
    }
  }

  reload = (search) => {
    let params = queryString.parse(search);
    const { id } = this.props.match.params;
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }
    this.setState({ isSendEmail: false }, () => {
      this.props.dispatch(fetchDetailAction({ id }));
    });
  };

  _onEdit = (record) => {
    this.props.history.push(`/main/setting/building/staff/edit/${record.id}`, {
      record,
      link: this.state.link,
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
        resetPasswordAction({
          password: values.password,
          management_user_id: this.state.record.id,
          is_send_email: this.state.isSendEmail ? 1 : 0,
        })
      );
      this.setState({
        visibleModalResetPassword: false,
      });
    });
  };

  handleCancel = () => {
    this.setState({
      visibleModalResetPassword: false,
    });
  };

  _onDelete = (record) => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.deleteStaffConfirm),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          deleteStaffAction({
            id: record.id,
            callback: () => {
              this.props.history.push("/main/setting/building/staff/list");
            },
          })
        );
      },
      onCancel() {},
    });
  };

  _onActive = (record) => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmActive),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          changeStatusStaffAction({
            management_user_id: record.id,
            status: 1,
            callback: () => {
              this.reload(this.props.location.search);
            },
          })
        );
      },
      onCancel() {},
    });
  };

  _onInactive = (record) => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmInactive),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          changeStatusStaffAction({
            management_user_id: record.id,
            status: 0,
            callback: () => {
              this.reload(this.props.location.search);
            },
          })
        );
      },
      onCancel() {},
    });
  };

  _handleMenuClick(e, record) {
    switch (e.key) {
      case "1":
        return this._onEdit(record);
      case "2":
        return record.status === 0
          ? this._onActive(record)
          : this._onInactive(record);
      case "3":
        return this.setState({
          visibleModalResetPassword: true,
        });
      case "4":
        return this._onDelete(record);
      default:
        break;
    }
  }

  render() {
    const { staffDetail, auth_group } = this.props;
    const { getFieldDecorator } = this.props.form;
    const { record, isSendEmail } = this.state;
    const menu = (
      <Menu onClick={(e) => this._handleMenuClick(e, record)}>
        <Menu.Item
          disabled={
            !auth_group.checkRole([
              config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_UPDATE,
            ])
          }
          key="1"
        >
          {this.props.intl.formatMessage(messages.edit)}
        </Menu.Item>
        <Menu.Item
          disabled={
            !auth_group.checkRole([
              config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_CHANGE_STATUS,
            ])
          }
          key="2"
        >
          {record.status === 1
            ? this.props.intl.formatMessage(messages.stopActivation)
            : this.props.intl.formatMessage(messages.activate)}
        </Menu.Item>
        <Menu.Item
          disabled={
            !auth_group.checkRole([
              config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_SET_PASSWORD,
            ])
          }
          key="3"
        >
          {this.props.intl.formatMessage(messages.resetPassword)}
        </Menu.Item>
        <Menu.Item
          disabled={
            !auth_group.checkRole([
              config.ALL_ROLE_NAME.BUILDING_PERSONNEL_MANAGEMENT_DELETE,
            ])
          }
          key="4"
        >
          {this.props.intl.formatMessage(messages.deleteStaff)}
        </Menu.Item>
      </Menu>
    );
    if (staffDetail.data == -1) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={this.props.intl.formatMessage(messages.notFindPage)}
            actions={
              <Button
                type="primary"
                onClick={() =>
                  this.props.history.push("/main/setting/building/staff/list")
                }
              >
                {this.props.intl.formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }
    return (
      <Page inner loading={staffDetail.detail.loading}>
        <div>
          <Row gutter={24} style={{ marginTop: 20 }}>
            <Col span={12}>
              <Row gutter={24} type="flex">
                <Col
                  span={8}
                  style={{
                    fontSize: 18,
                    fontWeight: "bold",
                    textAlign: "left",
                    marginLeft: 32,
                  }}
                >
                  {this.props.intl.formatMessage(messages.information)}
                </Col>
              </Row>
              <Row
                gutter={24}
                type="flex"
                style={{ marginTop: 24, marginLeft: 32 }}
              >
                <Col span={8} style={{ textAlign: "left", color: "#A4A4AA" }}>
                  {this.props.intl.formatMessage(messages.employeeCode)}:
                </Col>
                <Col span={16} style={{ color: "#1B1B27", fontWeight: "bold" }}>
                  {record && record.code_management_user
                    ? record.code_management_user
                    : ""}
                </Col>
              </Row>
              <Row
                gutter={24}
                type="flex"
                style={{ marginTop: 24, marginLeft: 32 }}
              >
                <Col span={8} style={{ textAlign: "left", color: "#A4A4AA" }}>
                  {this.props.intl.formatMessage(messages.fullName)}:
                </Col>
                <Col span={16} style={{ color: "#1B1B27", fontWeight: "bold" }}>
                  {record && record.first_name}
                </Col>
              </Row>
              <Row
                gutter={24}
                type="flex"
                style={{ marginTop: 24, marginLeft: 32 }}
              >
                <Col span={8} style={{ textAlign: "left", color: "#A4A4AA" }}>
                  {this.props.intl.formatMessage(messages.phone)}:
                </Col>
                <Col span={16} style={{ color: "#1B1B27", fontWeight: "bold" }}>
                  {record && `0${record.phone.slice(-9)}`}
                </Col>
              </Row>
              <Row
                gutter={24}
                type="flex"
                style={{ marginTop: 24, marginLeft: 32 }}
              >
                <Col span={8} style={{ textAlign: "left", color: "#A4A4AA" }}>
                  Email:
                </Col>
                <Col
                  span={16}
                  style={{
                    color: "#1B1B27",
                    fontWeight: "bold",
                    wordWrap: "break-word",
                  }}
                >
                  {record && record.email}
                </Col>
              </Row>
              <Row
                gutter={24}
                type="flex"
                style={{ marginTop: 24, marginLeft: 32 }}
              >
                <Col span={8} style={{ textAlign: "left", color: "#A4A4AA" }}>
                  {this.props.intl.formatMessage(messages.birthday)}:
                </Col>
                <Col span={16} style={{ color: "#1B1B27", fontWeight: "bold" }}>
                  {record &&
                    record.birthday &&
                    dateFormat(record.birthday * 1000, "dd/mm/yyyy")}
                </Col>
              </Row>
              <Row
                gutter={24}
                type="flex"
                style={{ marginTop: 24, marginLeft: 32 }}
              >
                <Col span={8} style={{ textAlign: "left", color: "#A4A4AA" }}>
                  {this.props.intl.formatMessage(messages.gender)}:
                </Col>
                <Col span={16} style={{ color: "#1B1B27", fontWeight: "bold" }}>
                  {record && (
                    <span>
                      {record.gender == 1
                        ? this.props.intl.formatMessage(messages.male)
                        : this.props.intl.formatMessage(messages.female)}
                    </span>
                  )}
                </Col>
              </Row>
              <Row
                gutter={24}
                type="flex"
                style={{ marginTop: 24, marginLeft: 32 }}
              >
                <Col span={8} style={{ textAlign: "left", color: "#A4A4AA" }}>
                  {this.props.intl.formatMessage(messages.authGroup)}:
                </Col>
                <Col span={16} style={{ color: "#1B1B27", fontWeight: "bold" }}>
                  {record && (
                    <span>
                      {this.props.language === "en"
                        ? record.auth_group.name_en
                        : record.auth_group.name}
                    </span>
                  )}
                </Col>
              </Row>
              <Row
                gutter={24}
                type="flex"
                style={{ marginTop: 24, marginLeft: 32 }}
              >
                <Col span={8} style={{ textAlign: "left", color: "#A4A4AA" }}>
                  {this.props.intl.formatMessage(messages.status)}:
                </Col>
                <Col span={16} style={{ color: "#1B1B27", fontWeight: "bold" }}>
                  {record && (
                    <span>
                      {record.status === 0
                        ? this.props.intl.formatMessage(messages.inactive)
                        : this.props.intl.formatMessage(messages.active)}
                    </span>
                  )}
                </Col>
              </Row>
            </Col>
            <Col
              span={8}
              style={{
                width: window.innerWidth <= 1440 ? "30%" : "20%",
                height: "100%",
                marginTop: 44,
              }}
            >
              {record.avatar ? (
                <Avatar
                  imageUrl={getFullLinkImage(this.state.imageUrl)}
                  disabled={true}
                />
              ) : (
                <Empty
                  style={{
                    alignSelf: "center",
                    width: "100%",
                    paddingBottom: 10,
                  }}
                  description={this.props.intl.formatMessage(messages.noAvatar)}
                  image="https://gw.alipayobjects.com/zos/antfincdn/ZHrcdLPrvN/empty.svg"
                />
              )}
            </Col>
          </Row>
          <Col
            style={{
              position: "absolute",
              right: 64,
              top: 40,
            }}
          >
            <Dropdown overlay={menu}>
              <Button>
                {this.props.intl.formatMessage(messages.action)}{" "}
                <Icon type="down" />
              </Button>
            </Dropdown>
          </Col>
          <Modal
            title={this.props.intl.formatMessage(messages.resetPassword)}
            visible={this.state.visibleModalResetPassword}
            onOk={this.handleOk}
            onCancel={this.handleCancel}
            okText={this.props.intl.formatMessage(messages.reset)}
            cancelText={this.props.intl.formatMessage(messages.cancel)}
          >
            <Form layout="vertical" className="resetPasswordStaff">
              <Form.Item
                label={this.props.intl.formatMessage(messages.newPassword)}
                colon={false}
              >
                {getFieldDecorator("password", {
                  validateFirst: true,
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyNewPassword
                      ),
                      whitespace: false,
                    },
                    {
                      min: 8,
                      message: this.props.intl.formatMessage(
                        messages.leastNewPassword
                      ),
                      whitespace: false,
                    },
                  ],
                })(
                  <Input.Password
                    type="password"
                    onPressEnter={this.handleOk}
                    placeholder={this.props.intl.formatMessage(
                      messages.enterNewPassword
                    )}
                    maxLength={20}
                  />
                )}
              </Form.Item>
              <Form.Item
                label={this.props.intl.formatMessage(
                  messages.reEnterNewPassword
                )}
                colon={false}
              >
                {getFieldDecorator("confirm_password", {
                  validateFirst: true,
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyReNewPassword
                      ),
                      whitespace: false,
                    },
                    {
                      min: 8,
                      message: this.props.intl.formatMessage(
                        messages.leastReNewPassword
                      ),
                      whitespace: false,
                    },
                    {
                      validator: (rule, value, callback) => {
                        const form = this.props.form;
                        if (value && value !== form.getFieldValue("password")) {
                          callback(
                            this.props.intl.formatMessage(
                              messages.passwordNotMatch
                            )
                          );
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(
                  <Input.Password
                    type="password"
                    onPressEnter={this.handleOk}
                    placeholder={this.props.intl.formatMessage(
                      messages.reEnterNewPassword
                    )}
                    maxLength={20}
                  />
                )}
              </Form.Item>
              <Row type="flex">
                <span style={{ color: "red" }}>*</span>
                <span
                  style={{
                    fontStyle: "italic",
                    marginLeft: 8,
                    marginBottom: 16,
                  }}
                >
                  {this.props.intl.formatMessage(messages.rangePassword)}
                </span>
              </Row>
              <Row type="flex">
                <Checkbox
                  checked={isSendEmail}
                  onClick={() => {
                    this.setState({ isSendEmail: !isSendEmail });
                  }}
                />
                <span style={{ marginLeft: 8 }}>
                  {this.props.intl.formatMessage(messages.sendEmail)}
                </span>
              </Row>
            </Form>
          </Modal>
        </div>
      </Page>
    );
  }
}

StaffDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  staffDetail: makeSelectStaffDetail(),
  language: makeSelectLocale(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "staffDetail", reducer });
const withSaga = injectSaga({ key: "staffDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(StaffDetail)));
