/**
 *
 * BuildingClusterInfomation
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";

import {
  Button,
  Col,
  Empty,
  Form,
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Typography,
} from "antd";
import { injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import Avatar from "../../../components/Avatar";
import Page from "../../../components/Page/Page";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import { editBuildingCluster, fetchCity } from "../../../redux/actions/config";
import { selectBuildingCluster, selectCity } from "../../../redux/selectors";
import { addressValidate, config, validateEmail } from "../../../utils";
import messages from "../messages";
import ModalCreate from "./ModalCreate";
import "./index.less";
const confirm = Modal.confirm;
const formItemLayout = {
  labelCol: {
    lg: { span: 5 },
    sm: { span: 6 },
  },
  wrapperCol: {
    lg: { span: 18, offset: 1 },
    sm: { span: 16, offset: 1 },
  },
};

const { Paragraph } = Typography;

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class BuildingClusterInfomation extends React.PureComponent {
  state = {
    isEditting: false,
    hotline: [],
    visible: false,
    bank_account: "",
    bank_holders: "",
    bank_name: "",
    cash_instruction: "",
    merchant_id: "",
    merchant_pass: "",
    receiver_account: "",

    email_account_push: "",
    email_password_push: "",
    sms_account_push: "",
    sms_password_push: "",
    sms_brandname_push: "",
    message_request_default: "",
    alias: "",
  };

  componentDidMount() {
    if (this.props.cities.lst.length == 0) {
      this.props.dispatch(fetchCity());
    }
  }

  _onSave = (type) => {
    const { dispatch, form } = this.props;
    const { data } = this.props.buildingCluster;
    const { validateFieldsAndScroll } = form;
    const {
      bank_account,
      bank_holders,
      bank_name,
      cash_instruction,
      merchant_id,
      merchant_pass,
      receiver_account,
      isEdittingPayment,

      email_account_push,
      email_password_push,
      sms_account_push,
      sms_brandname_push,
      sms_password_push,
      message_request_default,
      alias,
    } = this.state;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      let payment = {
        bank_account,
        bank_holders,
        bank_name,
        cash_instruction,
        payment_config: {
          merchant_id,
          merchant_pass,
          receiver_account,
        },
      };
      try {
        if (type == 0) {
          dispatch(
            editBuildingCluster({
              ...data,
              ...values,
              city_id: parseInt(values.city_id),
              medias: {
                ...(data.medias || {}),
                imageUrl: this.state.imageUrl || data.medias.imageUrl,
              },
              hotline: JSON.stringify(this.state.hotline),
              setting_group_receives_notices_financial: (
                (data || []).setting_group_receives_notices_financial || []
              ).map((dd) => dd.id),
              ...(isEdittingPayment ? payment : {}),
            })
          );
          return;
        }
        if (type == 1) {
          dispatch(
            editBuildingCluster({
              ...data,
              setting_group_receives_notices_financial: (
                (data || []).setting_group_receives_notices_financial || []
              ).map((dd) => dd.id),
              email_account_push,
              email_password_push,
              sms_account_push,
              sms_brandname_push,
              sms_password_push,
            })
          );
          return;
        }
        if (type == 2) {
          dispatch(
            editBuildingCluster({
              ...data,
              setting_group_receives_notices_financial: (
                (data || []).setting_group_receives_notices_financial || []
              ).map((dd) => dd.id),
              ...payment,
            })
          );
          return;
        }
        if (type == 3) {
          let text_message_request_default = message_request_default.trim();
          let text_message_request_default_2 = alias.trim();

          dispatch(
            editBuildingCluster({
              ...data,
              setting_group_receives_notices_financial: (
                (data || []).setting_group_receives_notices_financial || []
              ).map((dd) => dd.id),
              message_request_default: text_message_request_default,
              alias: text_message_request_default_2,
              medias: {
                ...(data.medias || {}),
                avatarUrl: this.state.avatarUrl || data.medias.avatarUrl,
              },
            })
          );
          return;
        }
      } catch (error) {
        console.log("error", error);
      }
    });
  };

  componentWillReceiveProps(nextProps) {
    if (
      this.props.buildingCluster.editting !=
        nextProps.buildingCluster.editting &&
      !nextProps.buildingCluster.editting
    ) {
      this.setState({
        isEditting: false,
        isEdittingPayment: false,
        isEdittingNotification: false,
        isEdittingFeedbackAuto: false,
      });
    }
  }

  renderEdit = (data) => {
    const { editting } = this.props.buildingCluster;
    const { getFieldDecorator } = this.props.form;
    const { cities } = this.props;
    const { imageUrl, hotline } = this.state;
    const formatMessage = this.props.intl.formatMessage;

    return (
      <Page
        inner
        className="buildingClusterInfomationPage"
        key={"block-info-edit"}
      >
        <div>
          <Row type="flex" justify="space-between">
            <strong style={{ fontSize: 18 }}>
              {formatMessage(messages.generalInformation)}
            </strong>
            <Col offset={8}>
              <Button
                ghost
                type="danger"
                style={{ width: 100 }}
                disabled={editting}
                onClick={() => {
                  confirm({
                    title: formatMessage(messages.confirmDelete),
                    okText: formatMessage(messages.agree),
                    okType: "danger",
                    cancelText: formatMessage(messages.cancel),
                    onOk: () => {
                      this.setState({
                        isEditting: false,
                      });
                    },
                    onCancel() {},
                  });
                }}
              >
                {formatMessage(messages.cancel)}
              </Button>
              <Button
                loading={editting}
                ghost
                type="primary"
                style={{ width: 100, marginLeft: 10 }}
                onClick={() => {
                  this._onSave(0);
                }}
              >
                {formatMessage(messages.update)}
              </Button>
            </Col>
          </Row>
          <Row gutter={24} style={{ marginTop: 40 }}>
            <Col xl={8} lg={7} md={24}>
              <Row style={{ marginBottom: 40 }}>
                <Col md={5} lg={24}>
                  <Avatar
                    imageUrl={getFullLinkImage(
                      imageUrl || data.medias.imageUrl
                    )}
                    onUploaded={(url) => this.setState({ imageUrl: url })}
                  />
                </Col>
              </Row>
            </Col>
            <Col xl={16} lg={17} md={24}>
              <Form {...formItemLayout} onSubmit={this.handleSubmit}>
                <Form.Item
                  label={formatMessage(messages.managementClusterName)}
                  labelAlign="left"
                >
                  {getFieldDecorator("name", {
                    initialValue: data.name,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(
                          messages.emptyManagementClusterName
                        ),
                        whitespace: true,
                      },
                    ],
                  })(<Input style={{ width: "100%" }} maxLength={50} />)}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.domain)}
                  labelAlign="left"
                >
                  {getFieldDecorator("domain", {
                    initialValue: data.domain,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.domainEmpty),
                        whitespace: true,
                      },
                    ],
                  })(<Input disabled />)}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.city)}
                  labelAlign="left"
                >
                  {getFieldDecorator("city_id", {
                    initialValue: data.city_id ? `${data.city_id}` : "",
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.emptyCity),
                        whitespace: true,
                      },
                    ],
                  })(
                    <Select
                      loading={cities.loading}
                      showSearch
                      placeholder={formatMessage(messages.selectCity)}
                      optionFilterProp="children"
                      filterOption={(input, option) =>
                        option.props.children
                          .toLowerCase()
                          .indexOf(input.toLowerCase()) >= 0
                      }
                    >
                      {cities.lst.map((gr) => {
                        return (
                          <Select.Option
                            key={`group-${gr.id}`}
                            value={`${gr.id}`}
                          >
                            {gr.name}
                          </Select.Option>
                        );
                      })}
                    </Select>
                  )}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.address)}
                  labelAlign="left"
                >
                  {getFieldDecorator("address", {
                    initialValue: data.address.trim(),
                    rules: [
                      {
                        required: true,
                        whitespace: true,
                        message: formatMessage(messages.addressEmpty),
                      },
                      {
                        validator: (rule, value, callback) => {
                          if (
                            value &&
                            value.trim() != "" &&
                            !addressValidate(value)
                          ) {
                            callback(formatMessage(messages.addressInvalid));
                          } else {
                            callback();
                          }
                        },
                      },
                    ],
                  })(<Input maxLength={244} />)}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.introduce)}
                  labelAlign="left"
                >
                  {getFieldDecorator("description", {
                    initialValue: data.description,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.limitIntroduce),
                      },
                    ],
                  })(<Input.TextArea rows={4} maxLength={1000} />)}
                </Form.Item>
                <Form.Item label={"Email"} labelAlign="left">
                  {getFieldDecorator("email", {
                    initialValue: data.email,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.emptyEmail),
                        whitespace: true,
                      },
                      {
                        validator: (rule, value, callback) => {
                          if (
                            value &&
                            value.trim() != "" &&
                            !validateEmail(value)
                          ) {
                            callback(formatMessage(messages.formatEmail));
                          } else {
                            callback();
                          }
                        },
                      },
                    ],
                  })(<Input maxLength={50} />)}
                </Form.Item>
                {/* <Form.Item
                    label={'API lấy thời tiết khu vực'}
                    labelAlign='left'
                  >
                    {getFieldDecorator('link_whether', {
                      initialValue: data.link_whether,
                      rules: [
                        { required: true, message: 'API lấy thời tiết khu vực không được để trống.', whitespace: true },
                      ],
                    })(<Input />)}
                  </Form.Item> */}
                <Form.Item label={"Hotline"} labelAlign="left">
                  {/* {getFieldDecorator('hotline', {
                    initialValue: data.hotline,
                    rules: [],
                  })(<Input />)} */}
                  {hotline.map((hl, index) => {
                    return (
                      <Row key={`hotline-index-${index}`}>
                        <Col span={14}>
                          <Paragraph
                            // editable={{
                            //   onChange: title => {
                            //     this.setState({
                            //       hotline: hotline.map((hhh, iii) => {
                            //         if (iii == index) {
                            //           return { ...hhh, title }
                            //         }
                            //         return hhh
                            //       })
                            //     })
                            //   }
                            // }}

                            mark
                            ellipsis={{
                              rows: 1,
                            }}
                          >
                            {hl.title}
                            {hl.title_en ? ` (${hl.title_en})` : ""}
                          </Paragraph>
                        </Col>
                        <Col span={1}>
                          <span style={{ marginLeft: 10, marginRight: 10 }}>
                            {" : "}
                          </span>
                        </Col>
                        <Col span={8}>
                          <Paragraph
                            // editable={{
                            //   onChange: phone => {
                            //     this.setState({
                            //       hotline: hotline.map((hhh, iii) => {
                            //         if (iii == index) {
                            //           return { ...hhh, phone }
                            //         }
                            //         return hhh
                            //       })
                            //     })
                            //   }
                            // }}
                            ellipsis={{
                              rows: 1,
                            }}
                            code
                          >
                            {hl.phone}
                          </Paragraph>
                        </Col>
                        <Col span={1} style={{ textAlign: "right" }}>
                          <Icon
                            className="dynamic-delete-button"
                            type="minus-circle-o"
                            style={{ cursor: "pointer" }}
                            onClick={() => {
                              let newHotline = [...hotline];
                              newHotline.splice(index, 1);
                              this.setState({
                                hotline: newHotline,
                              });
                            }}
                          />
                        </Col>
                      </Row>
                    );
                  })}
                  <Button
                    type="dashed"
                    onClick={() => {
                      this.setState({
                        visible: true,
                      });
                    }}
                    block
                  >
                    <Icon type="plus" /> {formatMessage(messages.add)} hotline
                  </Button>
                </Form.Item>
              </Form>
            </Col>
            <ModalCreate
              visible={this.state.visible}
              formatMessage={formatMessage}
              setState={this.setState.bind(this)}
              onAdd={(values) => {
                this.setState({
                  hotline: this.state.hotline.concat([values]),
                });
              }}
            />
          </Row>
        </div>
      </Page>
    );
  };
  renderInfo = (data) => {
    const formatMessage = this.props.intl.formatMessage;
    let hotline = [];
    try {
      hotline = JSON.parse(data.hotline);
    } catch (error) {
      hotline = [{ title: "CSKH", phone: data.hotline }];
    }
    return (
      <Page
        inner
        className="buildingClusterInfomationPage"
        key={"block-info"}
        noMinHeight
      >
        <div>
          <Row type="flex" justify="space-between">
            <strong style={{ fontSize: 18 }}>
              {formatMessage(messages.generalInformation)}
            </strong>
            {!this.props.disableEditable && (
              <WithRole
                roles={[config.ALL_ROLE_NAME.SETTING_BUILDING_INFOMATION]}
              >
                <Button
                  ghost
                  type="primary"
                  onClick={() => {
                    this.setState({
                      isEditting: true,
                      imageUrl: data.medias ? data.medias.imageUrl : undefined,
                      hotline,
                    });
                  }}
                >
                  {formatMessage(messages.edit)}
                </Button>
              </WithRole>
            )}
          </Row>
          <Row gutter={24} style={{ marginTop: 16 }}>
            <Col xl={8} lg={7} md={24} style={{ paddingRight: 24 }}>
              {!!data.medias && !!data.medias.imageUrl ? (
                <img
                  style={{ width: "100%", height: "100%" }}
                  src={getFullLinkImage(
                    !!data.medias && !!data.medias.imageUrl
                      ? data.medias.imageUrl
                      : undefined
                  )}
                />
              ) : (
                <Empty
                  style={{
                    alignSelf: "center",
                    width: "100%",
                  }}
                  description={formatMessage(messages.emptyAvatar)}
                  image="https://gw.alipayobjects.com/zos/antfincdn/ZHrcdLPrvN/empty.svg"
                />
              )}
            </Col>
            <Col xl={16} lg={17} md={24}>
              <Row className="rowItem">
                <Col span={4}>
                  {formatMessage(messages.managementClusterName)}:
                </Col>
                <Col offset={1} span={19} style={{ fontWeight: "bold" }}>
                  {data.name}
                </Col>
              </Row>
              <Row className="rowItem">
                <Col span={4}>{formatMessage(messages.domain)}:</Col>
                <Col offset={1} span={19} style={{ fontWeight: "bold" }}>
                  {data.domain}
                </Col>
              </Row>
              <Row className="rowItem">
                <Col span={4}>{formatMessage(messages.city)}:</Col>
                <Col offset={1} span={19} style={{ fontWeight: "bold" }}>
                  {data.city_name}
                </Col>
              </Row>
              <Row className="rowItem">
                <Col span={4}>{formatMessage(messages.address)}:</Col>
                <Col offset={1} span={19} style={{ fontWeight: "bold" }}>
                  {data.address.trim()}
                </Col>
              </Row>
              <Row className="rowItem">
                <Col span={4}>{formatMessage(messages.introduce)}:</Col>
                <Col offset={1} span={19} style={{ whiteSpace: "pre-wrap" }}>
                  {data.description}
                </Col>
              </Row>
              <Row className="rowItem">
                <Col span={4}>Email:</Col>
                <Col offset={1} span={19} style={{ fontWeight: "bold" }}>
                  {data.email}
                </Col>
              </Row>
              <Row className="rowItem">
                <Col span={4}>Hotline:</Col>
                <Col span={19} offset={1} style={{ fontWeight: "bold" }}>
                  {hotline.map((hl, index) => {
                    return (
                      <Row
                        key={`hotline-index-${index}`}
                        style={{ marginBottom: 4 }}
                      >
                        <Col span={14}>
                          <Paragraph mark ellipsis={{ rows: 1 }}>
                            {hl.title}
                            {hl.title_en ? ` (${hl.title_en})` : ""}
                          </Paragraph>
                        </Col>
                        <Col span={1}>
                          <span style={{ marginLeft: 10, marginRight: 10 }}>
                            {" : "}
                          </span>
                        </Col>
                        <Col span={8}>
                          <Paragraph ellipsis={{ rows: 1 }} code>
                            {hl.phone}
                          </Paragraph>
                        </Col>
                      </Row>
                    );
                  })}
                </Col>
              </Row>
            </Col>
          </Row>
        </div>
      </Page>
    );
  };

  renderPushnotificationInfo = (data) => {
    const { isSettingPlane, disableEditable } = this.props;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Page
        inner
        className="buildingClusterInfomationPage"
        key={"block-renderPushnotificationInfo"}
        style={{ marginTop: 16 }}
        noMinHeight
      >
        <div>
          <Row type="flex" justify="space-between">
            <strong style={{ fontSize: 18 }}>
              {formatMessage(messages.notiConfiguration)}
            </strong>
            {!disableEditable && (
              <WithRole
                roles={[config.ALL_ROLE_NAME.SETTING_BUILDING_INFOMATION]}
              >
                <Button
                  ghost
                  type="primary"
                  onClick={() => {
                    this.setState({
                      isEdittingNotification: true,

                      email_account_push: data.email_account_push || "",
                      email_password_push: data.email_password_push || "",
                      sms_account_push: data.sms_account_push || "",
                      sms_password_push: data.sms_password_push || "",
                      sms_brandname_push: data.sms_brandname_push || "",
                    });
                  }}
                >
                  {formatMessage(messages.edit)}
                </Button>
              </WithRole>
            )}
          </Row>
          <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
            <Col
              span={isSettingPlane && window.innerWidth <= 1366 ? 24 : 12}
              style={{ padding: 8 }}
            >
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>Email</strong>
                </Col>
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.account)}:
                </span>
                <strong style={{ wordWrap: "break-word" }}>
                  {data.email_account_push}
                </strong>
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.password)}:
                </span>
                <strong>********</strong>
              </Row>
            </Col>
            <Col
              span={isSettingPlane && window.innerWidth <= 1366 ? 24 : 12}
              style={{ padding: 8 }}
            >
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>SMS</strong>
                </Col>
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.account)}:
                </span>
                <strong style={{ wordWrap: "break-word" }}>
                  {data.sms_account_push}
                </strong>
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.password)}:
                </span>
                <strong>********</strong>
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  Brandname:
                </span>
                <strong>{data.sms_brandname_push}</strong>
              </Row>
            </Col>
          </Row>
        </div>
      </Page>
    );
  };

  renderNotificationEdit = () => {
    const { editting } = this.props.buildingCluster;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Page
        inner
        className="buildingClusterInfomationPage"
        key={"block-renderNotificationEdit"}
        style={{ marginTop: 16 }}
        noMinHeight
      >
        <div>
          <Row type="flex" justify="space-between">
            <strong style={{ fontSize: 18 }}>
              {formatMessage(messages.notiConfiguration)}
            </strong>
            <Col offset={8}>
              <Button
                ghost
                type="danger"
                style={{ width: 100 }}
                disabled={editting}
                onClick={() => {
                  confirm({
                    title: formatMessage(messages.confirmDelete),
                    okText: formatMessage(messages.agree),
                    okType: "danger",
                    cancelText: formatMessage(messages.cancel),
                    onOk: () => {
                      this.setState({
                        isEdittingNotification: false,
                      });
                    },
                    onCancel() {},
                  });
                }}
              >
                {formatMessage(messages.cancel)}
              </Button>
              <Button
                loading={editting}
                ghost
                type="primary"
                style={{ width: 100, marginLeft: 10 }}
                onClick={() => {
                  this._onSave(1);
                }}
              >
                {formatMessage(messages.update)}
              </Button>
            </Col>
          </Row>
          <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
            <Col span={12} style={{ padding: 8 }}>
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>Email</strong>
                </Col>
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.account)}:
                </span>
                <Input
                  style={{ marginBottom: 8 }}
                  value={this.state.email_account_push}
                  onChange={(e) => {
                    this.setState({
                      email_account_push: e.target.value,
                    });
                  }}
                />
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.password)}:
                </span>
                <Input
                  value={this.state.email_password_push}
                  onChange={(e) => {
                    this.setState({
                      email_password_push: e.target.value,
                    });
                  }}
                />
              </Row>
            </Col>
            <Col span={12} style={{ padding: 8 }}>
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>SMS</strong>
                </Col>
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.account)}:
                </span>
                <Input
                  style={{ marginBottom: 8 }}
                  value={this.state.sms_account_push}
                  onChange={(e) => {
                    this.setState({
                      sms_account_push: e.target.value,
                    });
                  }}
                />
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.password)}:
                </span>
                <Input
                  style={{ marginBottom: 8 }}
                  value={this.state.sms_password_push}
                  onChange={(e) => {
                    this.setState({
                      sms_password_push: e.target.value,
                    });
                  }}
                />
                <br />
                <span style={{ fontStyle: "italic", marginRight: 8 }}>
                  Brandname:
                </span>
                <Input
                  value={this.state.sms_brandname_push}
                  onChange={(e) => {
                    this.setState({
                      sms_brandname_push: e.target.value,
                    });
                  }}
                />
              </Row>
            </Col>
          </Row>
        </div>
      </Page>
    );
  };

  renderPaymentInfo = (data) => {
    const { isSettingPlane, disableEditable } = this.props;
    const formatMessage = this.props.intl.formatMessage;

    return (
      <Page
        inner
        className="buildingClusterInfomationPage"
        key={"block-payment"}
        style={{ marginTop: 16 }}
        noMinHeight
      >
        <div>
          <Row type="flex" justify="space-between">
            <strong style={{ fontSize: 18 }}>
              {formatMessage(messages.paymentInformation)}
            </strong>
            {!disableEditable && (
              <WithRole
                roles={[config.ALL_ROLE_NAME.SETTING_BUILDING_INFOMATION]}
              >
                <Button
                  ghost
                  type="primary"
                  onClick={() => {
                    this.setState({
                      isEdittingPayment: true,
                      bank_account: data.bank_account || "",
                      bank_holders: data.bank_holders || "",
                      bank_name: data.bank_name || "",
                      cash_instruction: data.cash_instruction || "",
                      merchant_id:
                        (data.payment_config
                          ? data.payment_config.merchant_id
                          : "") || "",
                      merchant_pass:
                        (data.payment_config
                          ? data.payment_config.merchant_pass
                          : "") || "",
                      receiver_account:
                        (data.payment_config
                          ? data.payment_config.receiver_account
                          : "") || "",
                    });
                  }}
                >
                  {formatMessage(messages.edit)}
                </Button>
              </WithRole>
            )}
          </Row>
          <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
            <Col
              span={isSettingPlane && window.innerWidth <= 1366 ? 24 : 12}
              style={{ padding: 8 }}
            >
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.cash)}</strong>
                </Col>
                <br />
                <span style={{ fontStyle: "italic", fontWeight: "bold" }}>
                  {formatMessage(messages.guide)}:
                </span>
                <br />
                {data.cash_instruction}
              </Row>
            </Col>
            <Col
              span={isSettingPlane && window.innerWidth <= 1366 ? 24 : 12}
              style={{ padding: 8 }}
            >
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.transfer)}</strong>
                </Col>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.bankName)}:
                </strong>
                <span>{data.bank_name}</span>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.accountNumber)}:
                </strong>
                <span>{data.bank_account}</span>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.ownerAccount)}:
                </strong>
                <span>{data.bank_holders}</span>
              </Row>
            </Col>
            {/* <Col
              span={isSettingPlane && window.innerWidth <= 1366 ? 24 : 8}
              style={{ padding: 8 }}
            >
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.money)}</strong>
                </Col>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.moneyAccount)}:
                </strong>
                <span>
                  {!!data.payment_config &&
                    data.payment_config.receiver_account}
                </span>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.merchantID)}:
                </strong>
                <span>
                  {!!data.payment_config && data.payment_config.merchant_id}
                </span>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.merchantPassword)}:
                </strong>
                <span>********</span>
              </Row>
            </Col> */}
          </Row>
        </div>
      </Page>
    );
  };

  renderPaymentEdit = () => {
    const { editting } = this.props.buildingCluster;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Page
        inner
        className="buildingClusterInfomationPage"
        key={"block-payment"}
        style={{ marginTop: 16 }}
        noMinHeight
      >
        <div>
          <Row type="flex" justify="space-between">
            <strong style={{ fontSize: 18 }}>
              {formatMessage(messages.paymentInformation)}
            </strong>
            <Col offset={8}>
              <Button
                ghost
                type="danger"
                style={{ width: 100 }}
                disabled={editting}
                onClick={() => {
                  confirm({
                    title: formatMessage(messages.confirmDelete),
                    okText: formatMessage(messages.agree),
                    okType: "danger",
                    cancelText: formatMessage(messages.cancel),
                    onOk: () => {
                      this.setState({
                        isEdittingPayment: false,
                      });
                    },
                    onCancel() {},
                  });
                }}
              >
                {formatMessage(messages.cancel)}
              </Button>
              <Button
                loading={editting}
                ghost
                type="primary"
                style={{ width: 100, marginLeft: 10 }}
                onClick={() => {
                  this._onSave(2);
                }}
              >
                {formatMessage(messages.update)}
              </Button>
            </Col>
          </Row>
          <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
            <Col span={12} style={{ padding: 8 }}>
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.cash)}</strong>
                </Col>
                <br />
                <strong style={{ fontStyle: "italic", fontWeight: "bold" }}>
                  {formatMessage(messages.guide)}:
                </strong>
                <br />
                <Input.TextArea
                  rows={12}
                  value={this.state.cash_instruction}
                  onChange={(e) => {
                    this.setState({
                      cash_instruction: e.target.value,
                    });
                  }}
                />
              </Row>
            </Col>
            <Col span={12} style={{ padding: 8 }}>
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.transfer)}</strong>
                </Col>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.bankName)}:
                </strong>
                <Input
                  style={{ marginBottom: 8 }}
                  value={this.state.bank_name}
                  onChange={(e) => {
                    this.setState({
                      bank_name: e.target.value,
                    });
                  }}
                />
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.accountNumber)}:
                </strong>
                <Input
                  style={{ marginBottom: 8 }}
                  value={this.state.bank_account}
                  onChange={(e) => {
                    this.setState({
                      bank_account: e.target.value,
                    });
                  }}
                />
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.ownerAccount)}:
                </strong>
                <Input
                  value={this.state.bank_holders}
                  onChange={(e) => {
                    this.setState({
                      bank_holders: e.target.value,
                    });
                  }}
                />
              </Row>
            </Col>
            {/* <Col span={8} style={{ padding: 8 }}>
              <Row
                style={{ border: "1px solid gray", height: "100%", padding: 8 }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.money)}</strong>
                </Col>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.moneyAccount)}:
                </strong>
                <Input
                  style={{ marginBottom: 8 }}
                  value={this.state.receiver_account}
                  onChange={(e) => {
                    this.setState({
                      receiver_account: e.target.value,
                    });
                  }}
                />
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.merchantID)}:
                </strong>
                <Input
                  style={{ marginBottom: 8 }}
                  value={this.state.merchant_id}
                  onChange={(e) => {
                    this.setState({
                      merchant_id: e.target.value,
                    });
                  }}
                />
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.merchantPassword)}:
                </strong>
                <Input
                  value={this.state.merchant_pass}
                  onChange={(e) => {
                    this.setState({
                      merchant_pass: e.target.value,
                    });
                  }}
                />
              </Row>
            </Col> */}
          </Row>
        </div>
      </Page>
    );
  };

  renderFeedbackAutoInfo = (data) => {
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Page
        inner
        className="buildingClusterInfomationPage"
        key={"block-renderFeedbackAutoInfo"}
        style={{ marginTop: 16 }}
        noMinHeight
      >
        <div>
          <Row type="flex" justify="space-between">
            <strong style={{ fontSize: 18 }}>
              {formatMessage(messages.configurationContentAutoAnswerResponse)}
            </strong>
            {!this.props.disableEditable && (
              <WithRole
                roles={[config.ALL_ROLE_NAME.SETTING_BUILDING_INFOMATION]}
              >
                <Button
                  ghost
                  type="primary"
                  onClick={() => {
                    this.setState({
                      isEdittingFeedbackAuto: true,
                      message_request_default:
                        data.message_request_default || "",
                      alias: data.alias || "",
                    });
                  }}
                >
                  {formatMessage(messages.edit)}
                </Button>
              </WithRole>
            )}
          </Row>
          <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
            <Col span={12} style={{ paddingTop: 8, paddingRight: 12 }}>
              <Row
                style={{
                  border: "1px solid gray",
                  minHeight: "210px",
                  padding: 8,
                }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.contentAutoFeedback)}</strong>
                </Col>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.content)}:
                </strong>
                <span>{data.message_request_default}</span>
              </Row>
            </Col>
            <Col span={12} style={{ paddingTop: 8, paddingLeft: 12 }}>
              <Row
                style={{
                  border: "1px solid gray",
                  minHeight: "210px",
                  padding: 8,
                }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.aliasTitle)}</strong>
                </Col>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.alias)}:
                </strong>
                <span>{data.alias}</span>
                <Col md={24} style={{ paddingTop: 8 }}>
                  <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                    {formatMessage(messages.avatar)}
                  </strong>
                  {!!data.medias && !!data.medias.avatarUrl ? (
                    <img
                      style={{ width: 111, height: 111 }}
                      src={getFullLinkImage(
                        !!data.medias && !!data.medias.avatarUrl
                          ? data.medias.avatarUrl
                          : undefined
                      )}
                    />
                  ) : (
                    <Empty
                      style={{
                        alignSelf: "center",
                        width: 100,
                      }}
                      description={formatMessage(messages.noAvatar)}
                      image="https://gw.alipayobjects.com/zos/antfincdn/ZHrcdLPrvN/empty.svg"
                    />
                  )}
                </Col>
              </Row>
            </Col>
          </Row>
        </div>
      </Page>
    );
  };

  renderFeedbackAutoEdit = (data) => {
    const { editting } = this.props.buildingCluster;
    const formatMessage = this.props.intl.formatMessage;
    const { avatarUrl } = this.state;

    return (
      <Page
        inner
        className="buildingClusterInfomationPage"
        key={"edit-feedback-auto"}
        style={{ marginTop: 16 }}
        noMinHeight
      >
        <div>
          <Row type="flex" justify="space-between">
            <strong style={{ fontSize: 18 }}>
              {formatMessage(messages.configurationContentAutoAnswerResponse)}
            </strong>
            <Col offset={8}>
              <Button
                ghost
                type="danger"
                style={{ width: 100 }}
                disabled={editting}
                onClick={() => {
                  confirm({
                    title: formatMessage(messages.confirmDelete),
                    okText: formatMessage(messages.agree),
                    okType: "danger",
                    cancelText: formatMessage(messages.cancel),
                    onOk: () => {
                      this.setState({
                        isEdittingFeedbackAuto: false,
                      });
                    },
                    onCancel() {},
                  });
                }}
              >
                {formatMessage(messages.cancel)}
              </Button>
              <Button
                loading={editting}
                ghost
                type="primary"
                style={{ width: 100, marginLeft: 10 }}
                onClick={() => {
                  this._onSave(3);
                }}
              >
                {formatMessage(messages.update)}
              </Button>
            </Col>
          </Row>
          <Row span={24} style={{ alignItems: "stretch", marginTop: 16 }}>
            <Col span={12} style={{ paddingTop: 8, paddingRight: 12 }}>
              <Row
                style={{
                  border: "1px solid gray",
                  height: "100%",
                  padding: 8,
                  minHeight: "400px",
                }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.contentAutoFeedback)}</strong>
                </Col>
                <Row span={24}>
                  <strong>{formatMessage(messages.content)}:</strong>
                  <Input.TextArea
                    rows={6}
                    style={{ marginTop: 8 }}
                    maxLength={200}
                    value={this.state.message_request_default}
                    onChange={(e) => {
                      this.setState({
                        message_request_default: e.target.value,
                      });
                    }}
                  />
                </Row>
              </Row>
            </Col>

            <Col
              span={12}
              style={{
                paddingTop: 8,
                paddingLeft: 12,
              }}
            >
              <Row
                style={{
                  border: "1px solid gray",
                  height: "100%",
                  padding: 8,
                  minHeight: "400px",
                }}
              >
                <Col style={{ textAlign: "center" }}>
                  <strong>{formatMessage(messages.aliasTitle)}</strong>
                </Col>
                <br />
                <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                  {formatMessage(messages.alias)}:
                </strong>

                <br />

                <Input
                  style={{ marginBottom: 8 }}
                  maxLength={50}
                  value={this.state.alias}
                  onChange={(e) => {
                    this.setState({
                      alias: e.target.value,
                    });
                  }}
                />
                <Col span={8}>
                  <strong style={{ fontStyle: "italic", marginRight: 8 }}>
                    {formatMessage(messages.avatar)}
                  </strong>
                </Col>
                <Col
                  span={12}
                  style={{
                    width: "180px",
                  }}
                >
                  <br />
                  <br />
                  <Avatar
                    imageUrl={getFullLinkImage(
                      avatarUrl || data.medias.avatarUrl
                    )}
                    onUploaded={(url) => this.setState({ avatarUrl: url })}
                  />
                </Col>
              </Row>
            </Col>
          </Row>
        </div>
      </Page>
    );
  };

  render() {
    const { isEditting, isEdittingPayment, isEdittingFeedbackAuto } =
      this.state;
    const { data } = this.props.buildingCluster;
    if (!data) {
      return <div />;
    }

    return [
      !isEditting ? this.renderInfo(data) : this.renderEdit(data),
      // !isEdittingNotification
      //   ? this.renderPushnotificationInfo(data)
      //   : this.renderNotificationEdit(data),
      !isEdittingPayment
        ? this.renderPaymentInfo(data)
        : this.renderPaymentEdit(data),
      !isEdittingFeedbackAuto
        ? this.renderFeedbackAutoInfo(data)
        : this.renderFeedbackAutoEdit(data),
    ];
  }
}

BuildingClusterInfomation.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  buildingCluster: selectBuildingCluster(),
  cities: selectCity(),
});
function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

export default compose(withConnect)(injectIntl(BuildingClusterInfomation));
