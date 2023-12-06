/**
 *
 * TicketDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Card,
  Col,
  Drawer,
  Form,
  Icon,
  List,
  Modal,
  Rate,
  Row,
  Select,
  Steps,
  Upload,
} from "antd";
import { Bind, Debounce } from "lodash-decorators";
import moment from "moment";
import { injectIntl } from "react-intl";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import {
  addManagerGroupsAction,
  defaultAction,
  fetchAuthGroupAction,
  fetchExternalMessagesAction,
  fetchInternalMessagesAction,
  fetchManagerGroupsAction,
  fetchTicketDetailAction,
  removeManagerGroupsAction,
  sendExternalMessageAction,
  sendInternalMessageAction,
  updateTicketStatusAction,
  updateTicketStatusCompleteAction,
} from "./actions";
import messages from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectTicketDetail from "./selectors";

import { getFullLinkImage } from "../../../connection";
import ChatBox from "./ChatBox";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import _ from "lodash";
import { Redirect } from "react-router";
import { ALL_ROLE_NAME } from "utils/config";
import WithRole from "../../../components/WithRole";
import { selectAuthGroup, selectUserDetail } from "../../../redux/selectors";
import { config, formatPrice, notificationBar } from "../../../utils";
import { GLOBAL_COLOR, timeFromNow } from "../../../utils/constants";
import styles from "../TicketList/index.less";
import ModalCreate from "./ModalAddGroups";
import { CHAT_BOX_TYPE_EXTERNAL, CHAT_BOX_TYPE_INTERNAL } from "./constants";
import "./index.less";

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class TicketDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      record: (props.location.state || {}).record,
      heightBlock: undefined,
      images: [],
      visibleManagerGroups: false,
      visibleAddGroups: false,
      groupAdding: false,
    };
  }

  @Bind()
  @Debounce(300)
  resize() {
    if (!this.root) {
      window.removeEventListener("resize", this.resize);
      return;
    } else {
      this.setState({
        heightBlock: this.root.clientHeight,
      });
    }
  }

  handleRoot = (n) => {
    this.root = n;
  };

  componentDidMount() {
    window.addEventListener(
      "resize",
      () => {
        this.requestRef = requestAnimationFrame(() => this.resize());
      },
      { passive: true }
    );
    if (this.root) {
      this.setState({
        heightBlock: this.root.clientHeight,
      });
    }
    let { params } = this.props.match;
    this.reload(params);
  }

  reload = (params) => {
    this.props.dispatch(fetchTicketDetailAction({ id: params.id }));
    this.props.dispatch(fetchExternalMessagesAction({ request_id: params.id }));
    this.props.dispatch(fetchInternalMessagesAction({ request_id: params.id }));
    this.props.dispatch(fetchManagerGroupsAction({ request_id: params.id }));
    this.props.dispatch(fetchAuthGroupAction({ request_id: params.id }));
  };

  componentWillReceiveProps(nextProps) {
    if (
      this.props.match.params.id != nextProps.match.params.id ||
      this.props.location.search != nextProps.location.search
    ) {
      this.reload(nextProps.match.params);
    }
    if (
      this.props.ticketDetail.detail.data != nextProps.ticketDetail.detail.data
    ) {
      this.setState({
        record: nextProps.ticketDetail.detail.data,
      });
      if (
        !!nextProps.ticketDetail.detail.data &&
        !!nextProps.ticketDetail.detail.data.attach &&
        !!nextProps.ticketDetail.detail.data.attach.attachImage
      ) {
        this.setState({
          images: nextProps.ticketDetail.detail.data.attach.attachImage.map(
            function (image, index) {
              return {
                uid: index,
                name: index,
                status: "done",
                url: getFullLinkImage(image, true),
              };
            }
          ),
        });
      }
    }
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    window.cancelAnimationFrame(this.requestRef);
    window.removeEventListener("resize", this.resize);
    this.resize.cancel();
  }

  updateStatus(status) {
    const { dispatch } = this.props;
    let { params } = this.props.match;
    const { record } = this.state;
    dispatch(
      updateTicketStatusAction({
        request_id: record.id,
        status: status,
        callback: () => {
          this.props.dispatch(updateTicketStatusCompleteAction());
          this.props.dispatch(fetchTicketDetailAction({ id: params.id }));
        },
      })
    );
  }

  _onRemoveGroup = (item) => {
    const { record } = this.state;
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.modalDelete),
      okText: this.props.intl.formatMessage(messages.confirm),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          removeManagerGroupsAction({
            auth_group_ids: [item.auth_group_id],
            request_id: record.id,
            callback: () => {
              this.reloadGroups();
            },
          })
        );
      },
      onCancel() {},
    });
  };

  reloadGroups = () => {
    const { record } = this.state;
    this.props.dispatch(fetchManagerGroupsAction({ request_id: record.id }));
  };

  onSwitchManagerGroups = () => {
    this.setState({
      visibleManagerGroups: !this.state.visibleManagerGroups,
    });
  };

  hideManagerGroups = () => {
    this.setState({ visibleManagerGroups: false });
  };

  _getStepToView = (currentStep) => {
    if (currentStep == 1 || currentStep == 3) return 2;
    if (currentStep == -1) return 1;
    if (currentStep == -2) return 5;
    if (currentStep == 2) return 3;
    return currentStep;
  };
  _convert = (text) => {
    const { formatMessage } = this.props.intl;
    if (text.toString() === "0") {
      return formatMessage(messages.newFeedback);
    }
    if (text.toString() === "-1") {
      return formatMessage(messages.pending);
    }
    if (text.toString() === "1") {
      return formatMessage(messages.processing);
    }
    if (text.toString() === "2") {
      return formatMessage(messages.processed);
    }
    if (text.toString() === "4") {
      return formatMessage(messages.closed);
    }
    return formatMessage(messages.cancelFeedback);
  };
  _onClose = () => {
    const { formatMessage } = this.props.intl;
    Modal.confirm({
      autoFocusButton: null,
      title: formatMessage(messages.closeFeedbackContent),
      okText: formatMessage(messages.agree),
      okType: "primary",
      cancelText: formatMessage(messages.cancel),
      onOk: () => {
        this.updateStatus(4);
      },
      onCancel() {},
    });
  };
  render() {
    const { heightBlock, record, images } = this.state;
    console.log(images);
    const formatMessage = this.props.intl.formatMessage;
    // const arrColor = [
    //   "#E5F7FF",
    //   "#15F7FF",
    //   "#3127FF",
    //   "#6F23F1",
    //   "#91271F",
    //   "#91271F",
    // ];
    if (!record) {
      return <Page inner loading />;
    } else if (record && !record.permission) {
      notificationBar(formatMessage(messages.unauthorized), "warning");
      return <Redirect to="/main/ticket/list" />;
    }
    const currentStep = record ? record.status : 0;
    const { external_messages, internal_messages, managerment_groups } =
      this.props.ticketDetail;
    const language = this.props.language;
    const { ticketDetail, userInfo, auth_group } = this.props;
    const { attach } = record;
    return (
      <Page inner>
        <div className="ticketDetailPage">
          <div ref={this.handleRoot}>
            <Row gutter={24} className="block">
              <Row>
                <Col
                  md={{
                    span: 24,
                    offset: 0,
                  }}
                  xl={{
                    span: 20,
                    offset: 2,
                  }}
                  style={{ marginTop: 10, marginBottom: 10 }}
                >
                  <Steps
                    labelPlacement="horizontal"
                    current={this._getStepToView(currentStep)}
                  >
                    <Steps.Step title={formatMessage(messages.newFeedback)} />
                    <Steps.Step title={formatMessage(messages.pending)} />
                    <Steps.Step
                      title={
                        currentStep != 3
                          ? formatMessage(messages.processing)
                          : formatMessage(messages.processingAgain)
                      }
                    />
                    <Steps.Step title={formatMessage(messages.processed)} />
                    <Steps.Step title={formatMessage(messages.closed)} />
                    <Steps.Step
                      title={formatMessage(messages.cancelFeedback)}
                    />
                  </Steps>
                </Col>
              </Row>
              <Row style={{ marginTop: 40 }}>
                <Col
                  lg={{
                    span: 6,
                    offset: 4,
                  }}
                  md={24}
                >
                  <Row>
                    <Col>
                      <Row type="flex" align="middle">
                        <Col style={{ width: 100, color: "#A4A4AA" }}>
                          {`${formatMessage(messages.feedbackCode)}: `}
                        </Col>
                        <Col style={{ fontWeight: "bold" }}>
                          {`${record.number}`}
                        </Col>
                      </Row>
                      <Row type="flex" align="middle" style={{ marginTop: 24 }}>
                        <Col style={{ width: 100, color: "#A4A4AA" }}>
                          {`${formatMessage(messages.property)}: `}
                        </Col>
                        <Col style={{ fontWeight: "bold" }}>
                          {`${record.apartment_name} (${record.building_area_name})`}{" "}
                        </Col>
                      </Row>
                      <Row type="flex" align="middle" style={{ marginTop: 24 }}>
                        <Col style={{ width: 100, color: "#A4A4AA" }}>
                          {`${formatMessage(messages.sender)}: `}
                        </Col>
                        <Col style={{ fontWeight: "bold" }}>
                          {record.resident_user_name}
                        </Col>
                      </Row>
                      <Row type="flex" align="middle" style={{ marginTop: 24 }}>
                        <Col style={{ width: 100, color: "#A4A4AA" }}>
                          {`${formatMessage(messages.category)}: `}
                        </Col>
                        <Col
                          className="luci-status-warning"
                          style={{
                            background: record.request_category_color,
                          }}
                        >
                          {this.props.language === "vi"
                            ? record.request_category_name
                            : record.request_category_name_en}
                        </Col>
                      </Row>
                      <Row type="flex" align="middle" style={{ marginTop: 24 }}>
                        <Col style={{ width: 100, color: "#A4A4AA" }}>
                          {`${formatMessage(messages.dayCreate)}: `}
                        </Col>
                        <Col style={{ fontWeight: "bold" }}>
                          {timeFromNow(record.created_at)}
                        </Col>
                      </Row>
                      {!!record.rate && (
                        <Row
                          type="flex"
                          align="middle"
                          style={{ marginTop: 24 }}
                        >
                          <Col style={{ width: 100, color: "#A4A4AA" }}>
                            {`${formatMessage(messages.evaluate)}: `}
                          </Col>
                          <Col style={{ fontWeight: "bold" }}>
                            <Rate
                              disabled
                              allowHalf
                              defaultValue={record.rate}
                            />
                          </Col>
                        </Row>
                      )}
                      <Row type="flex" align="middle" style={{ marginTop: 24 }}>
                        <Col style={{ width: 100, color: "#A4A4AA" }}> </Col>
                        <WithRole
                          roles={[
                            config.ALL_ROLE_NAME.REQUEST_CONTACT_RESIDENT,
                          ]}
                        >
                          <Col span={8}>
                            <Select
                              showSearch
                              style={{
                                width: "100%",
                              }}
                              size={"large"}
                              placeholder={formatMessage(messages.status)}
                              optionFilterProp="children"
                              filterOption={(input, option) =>
                                option.props.children
                                  .toLowerCase()
                                  .indexOf(input.toLowerCase()) >= 0
                              }
                              disabled={
                                currentStep !== 0 &&
                                currentStep !== -1 &&
                                currentStep !== 1 &&
                                currentStep !== 2
                                  ? true
                                  : false
                              }
                              onChange={(value) => {
                                if (value === "4") {
                                  this._onClose();
                                } else {
                                  this.updateStatus(value);
                                }
                              }}
                              // allowClear
                              value={this._convert(currentStep)}
                            >
                              <Select.Option value="0">
                                {formatMessage(messages.newFeedback)}
                              </Select.Option>
                              <Select.Option value="-1">
                                {formatMessage(messages.pending)}
                              </Select.Option>
                              <Select.Option value="1">
                                {formatMessage(messages.processing)}
                              </Select.Option>
                              <Select.Option value="2">
                                {formatMessage(messages.processed)}
                              </Select.Option>
                              <Select.Option value="4">
                                {formatMessage(messages.closed)}
                              </Select.Option>
                            </Select>
                          </Col>
                        </WithRole>
                      </Row>
                    </Col>
                  </Row>
                </Col>
                <Col lg={14} md={24}>
                  <span style={{ color: "#A4A4AA" }}>
                    {formatMessage(messages.content)}
                  </span>
                  <div
                    style={{
                      whiteSpace: "pre-wrap",
                      marginTop: 24,
                      color: "#131313",
                    }}
                  >
                    {record.content}
                  </div>
                  {!!attach &&
                    !!attach.comment &&
                    (attach.comment.type == "fee" ||
                      attach.comment.type == "order") && (
                      <Row
                        style={{
                          borderRadius: 4,
                          marginTop: 16,
                          backgroundColor: GLOBAL_COLOR,
                          paddingLeft: 4,
                          width: "30%",
                          minWidth: 400,
                          borderBottomRightRadius: 10,
                          borderTopRightRadius: 10,
                        }}
                        type="flex"
                      >
                        <Row
                          style={{
                            paddingTop: 6,
                            paddingBottom: 6,
                            paddingLeft: 10,
                            paddingRight: 10,
                            width: "100%",
                            backgroundColor: "#E8F3FF",
                            borderTopRightRadius: 4,
                            borderBottomRightRadius: 4,
                          }}
                        >
                          {attach.comment.type == "fee" && (
                            <Col style={{ cursor: "pointer" }}>
                              <Row
                                type="flex"
                                align="middle"
                                style={{ position: "relative" }}
                              >
                                <img
                                  style={{
                                    width: 64,
                                    height: 64,
                                    marginRight: 16,
                                  }}
                                  src={
                                    (
                                      config.ICON_SERVICE.find(
                                        (iii) =>
                                          attach.comment.data
                                            .service_map_management_service_icon_name ==
                                          iii.value
                                      ) || config.ICON_SERVICE[4]
                                    ).icon
                                  }
                                />
                                <Row
                                  type="flex"
                                  justify="space-between"
                                  style={{ width: "calc(100% - 84px)" }}
                                >
                                  <Col
                                    style={{
                                      color: "#131313",
                                      fontWeight: "bold",
                                      fontSize: 18,
                                      width: "60%",
                                    }}
                                  >
                                    {
                                      attach.comment.data
                                        .service_map_management_service_name
                                    }
                                  </Col>
                                  <Col
                                    style={{
                                      color: "#131313",
                                      fontWeight: "bold",
                                      fontSize: 18,
                                      width: "40%",
                                      textAlign: "right",
                                    }}
                                  >
                                    {formatPrice(attach.comment.data.price)}Đ
                                  </Col>
                                </Row>
                              </Row>
                              <Row style={{ marginBottom: 10 }}>
                                <span style={{ marginLeft: 10 }}>
                                  {moment(
                                    attach.comment.data.fee_of_month
                                  ).format("DD/MM/YYYY HH:mm")}
                                </span>
                              </Row>
                              {attach.comment.data.description && (
                                <Row
                                  style={{
                                    paddingTop: 10,
                                    paddingBottom: 10,
                                    borderTop: "1px solid #E8E8E8",
                                  }}
                                >
                                  <span
                                    style={{
                                      marginLeft: 10,
                                      whiteSpace: "pre-wrap",
                                    }}
                                  >
                                    {attach.comment.data.description}
                                  </span>
                                </Row>
                              )}
                            </Col>
                          )}
                          {attach.comment.type == "order" && (
                            <Col
                              style={{ cursor: "pointer", margin: 6 }}
                              onClick={() => {
                                this.props.history.push(
                                  `/main/finance/bills/detail/${attach.comment.data.id}`
                                );
                              }}
                            >
                              <Row
                                type="flex"
                                align="middle"
                                style={{ position: "relative" }}
                              >
                                <Row
                                  type="flex"
                                  justify="space-between"
                                  style={{ width: "100%" }}
                                >
                                  <Col
                                    style={{
                                      color: "#131313",
                                      fontWeight: "bold",
                                      fontSize: 18,
                                      width: "60%",
                                    }}
                                  >{`${formatMessage(messages.ticketCode)}: ${
                                    attach.comment.data.code
                                  }`}</Col>
                                  <Col
                                    style={{
                                      color: "#131313",
                                      fontWeight: "bold",
                                      fontSize: 18,
                                      width: "40%",
                                      textAlign: "right",
                                    }}
                                  >
                                    {formatPrice(
                                      _.sum(
                                        attach.comment.data.service_bill_items.map(
                                          (mm) => mm.price
                                        )
                                      )
                                    )}
                                    Đ
                                  </Col>
                                </Row>
                              </Row>
                              <Row>
                                <span>
                                  {moment
                                    .unix(attach.comment.data.created_at)
                                    .format("DD/MM/YYYY HH:mm")}
                                </span>
                              </Row>
                            </Col>
                          )}
                        </Row>
                      </Row>
                    )}
                  <Row style={{ marginTop: 24 }}>
                    <Upload
                      className="ant-upload-list"
                      listType="picture-card"
                      fileList={images}
                      onRemove={false}
                      onPreview={this.handlePreview}
                      onChange={this.handleChange}
                      showUploadList={{ showDownloadIcon: false }}
                    />
                  </Row>
                </Col>
              </Row>
            </Row>
          </div>
          <Row gutter={24} style={{ marginRight: 0 }}>
            <WithRole roles={[config.ALL_ROLE_NAME.REQUEST_CONTACT_RESIDENT]}>
              <Col
                lg={12}
                md={24}
                style={{ marginTop: 24, paddingLeft: 12, paddingRight: 0 }}
              >
                <Card
                  title={formatMessage(messages.answerFeedback)}
                  headStyle={{ minHeight: "65px" }}
                  bodyStyle={{ padding: "5px" }}
                >
                  <ChatBox
                    authGroup={auth_group}
                    limitFile={1}
                    language={this.props.language}
                    type={CHAT_BOX_TYPE_EXTERNAL}
                    disableFeedback={
                      record.status === 4 || record.status === -2
                    }
                    userInfo={userInfo}
                    roles={[config.ALL_ROLE_NAME.REQUEST_CONTACT_RESIDENT]}
                    heightBlock={heightBlock}
                    messages={external_messages.data}
                    handleSendMessage={(message) => {
                      const { dispatch } = this.props;
                      const { record } = this.state;
                      dispatch(
                        sendExternalMessageAction({
                          request_id: record.id,
                          content: message.content.trim(),
                          attach: message.attach,
                        })
                      );
                    }}
                  />
                </Card>
              </Col>
            </WithRole>
            <WithRole
              roles={[
                config.ALL_ROLE_NAME.REQUEST_MANAGER_GROUPS_PROCESS_ADMIN,
              ]}
            >
              <Col
                lg={12}
                md={24}
                style={{ marginTop: 24, paddingLeft: 12, paddingRight: 0 }}
              >
                <Card
                  title={formatMessage(messages.internalChat)}
                  bodyStyle={{ padding: "5px" }}
                  extra={
                    auth_group.checkRole([
                      ALL_ROLE_NAME.REQUEST_MANAGER_GROUPS_PROCESS_ADMIN,
                    ]) ? (
                      <Button
                        icon="menu"
                        onClick={this.onSwitchManagerGroups}
                      />
                    ) : null
                  }
                >
                  <div ref={this.saveContainer}>
                    <Drawer
                      title={formatMessage(messages.groupProcess)}
                      placement="right"
                      width={400}
                      closable={false}
                      onClose={this.hideManagerGroups}
                      visible={this.state.visibleManagerGroups}
                    >
                      <Row>
                        <List
                          split={false}
                          className="list-manager-groups"
                          loading={managerment_groups.loading}
                          itemLayout="horizontal"
                          dataSource={managerment_groups.data}
                          renderItem={(item) => {
                            return (
                              <List.Item
                                actions={[
                                  <WithRole
                                    key={item.id}
                                    roles={[
                                      config.ALL_ROLE_NAME
                                        .REQUEST_MANAGER_GROUPS_PROCESS_ADMIN,
                                    ]}
                                  >
                                    <Icon
                                      className={styles.iconAction}
                                      type="delete"
                                      onClick={(e) => {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        this._onRemoveGroup(item);
                                      }}
                                    />
                                  </WithRole>,
                                ]}
                              >
                                <List.Item.Meta
                                  // avatar={<Avatar icon="user" />}
                                  title={
                                    language === "vi"
                                      ? item.auth_group_name
                                      : item.auth_group_name_en
                                  }
                                />
                              </List.Item>
                            );
                          }}
                        />
                      </Row>
                      <WithRole
                        roles={[
                          config.ALL_ROLE_NAME
                            .REQUEST_MANAGER_GROUPS_PROCESS_ADMIN,
                        ]}
                      >
                        <Row type="flex" justify="center">
                          <Button
                            onClick={() => {
                              this.setState({
                                visibleAddGroups: true,
                              });
                            }}
                          >
                            {formatMessage(messages.addGroup)}
                          </Button>
                        </Row>
                        <ModalCreate
                          language={this.props.language}
                          visibleAddGroups={this.state.visibleAddGroups}
                          setState={this.setState.bind(this)}
                          authGroup={ticketDetail.authGroup}
                          ignoreGroup={managerment_groups.data.map((group) => {
                            return group.auth_group_id;
                          })}
                          creating={managerment_groups.adding}
                          handlerAddGroups={(values) => {
                            this.props.dispatch(
                              addManagerGroupsAction({
                                ...values,
                                request_id: record.id,
                                callback: () => {
                                  this.setState({ visibleAddGroups: false });
                                  this.reloadGroups();
                                },
                              })
                            );
                          }}
                        />
                      </WithRole>
                    </Drawer>
                  </div>
                  <ChatBox
                    language={this.props.language}
                    limitFile={1}
                    type={CHAT_BOX_TYPE_INTERNAL}
                    disableFeedback={
                      record.status === 4 || record.status === -2
                    }
                    userInfo={userInfo}
                    roles={[config.ALL_ROLE_NAME.REQUEST_PROCESS]}
                    heightBlock={heightBlock}
                    messages={internal_messages.data}
                    handleSendMessage={(message) => {
                      const { dispatch } = this.props;
                      const { record } = this.state;
                      dispatch(
                        sendInternalMessageAction({
                          request_id: record.id,
                          content: message.content.trim(),
                          attach: message.attach,
                        })
                      );
                    }}
                  />
                </Card>
              </Col>
            </WithRole>
          </Row>
        </div>
      </Page>
    );
  }
}

TicketDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  ticketDetail: makeSelectTicketDetail(),
  auth_group: selectAuthGroup(),
  userInfo: selectUserDetail(),
  userGroup: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "ticketDetail", reducer });
const withSaga = injectSaga({ key: "ticketDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(TicketDetail));
