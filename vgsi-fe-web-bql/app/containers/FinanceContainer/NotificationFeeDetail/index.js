/**
 *
 * NotificationFeeDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Checkbox,
  Col,
  Form,
  Input,
  Radio,
  Row,
  Select,
  Table,
  Tooltip,
} from "antd";
import "react-draft-wysiwyg/dist/react-draft-wysiwyg.css";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { formatPrice } from "../../../utils";
import {
  defaultAction,
  fetchApartmentFeeReminder,
  fetchApartmentSent,
  fetchNotificationDetail,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";

import { EditorState } from "draft-js";

import Exception from "ant-design-pro/lib/Exception";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import { injectIntl } from "react-intl";
import { GLOBAL_COLOR, removeAccents } from "../../../utils/constants";
import messages from "../../NotificationContainer/messages";
import makeSelectNotificationFeeDetail from "./selectors";

import("./index.less");
const formItemLayout = {
  labelCol: {
    xl: { span: 8 },
  },
  wrapperCol: {
    xl: { span: 16 },
  },
};
const colLayout = {
  md: 5,
  lg: 5,
  xl: 4,
};

const ReactHighstock = require("react-highcharts");
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class NotificationFeeDetail extends React.PureComponent {
  constructor(props) {
    super(props);
    const { record } = props.location.state || {};

    this.state = {
      record,
      editorState: EditorState.createEmpty(),
      type: 1,
      current: 1,
      // contentSMS: "",
      prevType: 1,
      pushType: [],
      dataSource: [],
      apartmentSearch: "",
      residentSearch: "",
      statusSearch: undefined,
    };
  }

  onEditorStateChange = (editorState) => {
    this.setState({
      editorState,
    });
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    setTimeout(() => {
      window.scrollTo(0, 0);
    }, 200);
    this.props.dispatch(fetchNotificationDetail(this.props.match.params.id));
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.notificationDetail.apartmentReminder.loading !=
        nextProps.notificationDetail.apartmentReminder.loading &&
      !nextProps.notificationDetail.apartmentReminder.loading &&
      !!nextProps.notificationDetail.detail &&
      !!nextProps.notificationDetail.apartmentReminder.data.length
    ) {
      this.setState({
        dataSource: nextProps.notificationDetail.apartmentReminder.data,
      });
    }
  }

  render() {
    const { notificationDetail, intl, language } = this.props;
    const formatMessage = intl.formatMessage;
    const { loading, detail, apartmentReminder } = notificationDetail;
    if (loading) {
      return <Page inner loading />;
    }

    if (!detail) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={formatMessage(messages.notFindNotificationDetail)}
            actions={
              <Button
                type="primary"
                onClick={() =>
                  this.props.history.push("/main/finance/notification-fee/list")
                }
              >
                {formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }
    let columns = [
      {
        title: <span>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              apartmentReminder.loading
                ? this.state.current - 2
                : this.state.current - 1
            ) *
              10 +
              index +
              1}
          </span>
        ),
      },
      {
        title: <span>{formatMessage(messages.property)}</span>,
        dataIndex: "apartment_name",
        key: "apartment_name",
      },
      {
        title: <span>{formatMessage(messages.resident)}</span>,
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        title: <span>{formatMessage(messages.address)}</span>,
        dataIndex: "apartment_parent_path",
        key: "apartment_parent_path",
      },
      {
        title: <span>{formatMessage(messages.endDept)}</span>,
        dataIndex: "end_debt",
        key: "end_debt",
        render: (text) => {
          return <span>{formatPrice(text)} đ</span>;
        },
      },
      {
        title: <span>Email</span>,
        dataIndex: "email",
        key: "email",
        render: (text, record) => {
          if (!record.email) {
            return (
              <Tooltip title={formatMessage(messages.notConfig)}>
                <i
                  className="material-icons"
                  style={{ color: "#E4E4E4", cursor: "pointer" }}
                >
                  unsubscribe
                </i>
              </Tooltip>
            );
          }
          let title = formatMessage(messages.sent);
          let color = GLOBAL_COLOR;
          if (record.read_email_at) {
            title = formatMessage(messages.read);
            color = "#F5A362";
          } else if (record.status_email == 1) {
            title = formatMessage(messages.sent);
            color = GLOBAL_COLOR;
          } else if (record.status_email == 2) {
            title = formatMessage(messages.sendError);
            color = "red";
          }
          return (
            <Tooltip title={title}>
              <span style={{ color }}>
                {!!text && text.length > 30
                  ? text.substring(0, 30) + "..."
                  : text}
              </span>
            </Tooltip>
          );
        },
      },
      {
        title: <span>App</span>,
        dataIndex: "app",
        key: "app",
        render: (text, record) => {
          if (!record.app) {
            return (
              <Tooltip title={formatMessage(messages.appNotInstall)}>
                <i
                  className="material-icons"
                  style={{ color: "#E4E4E4", cursor: "pointer" }}
                >
                  mobile_off
                </i>
              </Tooltip>
            );
          }
          let title = formatMessage(messages.sent);
          let color = GLOBAL_COLOR;
          if (record.read_at) {
            title = formatMessage(messages.read);
            color = "#F5A362";
          } else if (record.status_notify == 1) {
            title = formatMessage(messages.sent);
            color = GLOBAL_COLOR;
          } else if (record.status_notify == 2) {
            title = formatMessage(messages.sendError);
            color = "red";
          }
          return (
            <Tooltip title={title}>
              <i
                className="material-icons"
                style={{ color: color, cursor: "pointer" }}
              >
                mobile_friendly
              </i>
            </Tooltip>
          );
        },
      },
      // {
      //   title: <span>SMS</span>,
      //   dataIndex: "phone",
      //   key: "phone",
      //   render: (text, record) => {
      //     if (!record.phone) {
      //       return (
      //         <Tooltip title={formatMessage(messages.appNotInstall)}>
      //           <i
      //             className="material-icons"
      //             style={{ color: "#E4E4E4", cursor: "pointer" }}
      //           >
      //             phone_disabled
      //           </i>
      //         </Tooltip>
      //       );
      //     }
      //     let title = formatMessage(messages.read);
      //     let color = GLOBAL_COLOR;
      //     if (record.status_sms == 2) {
      //       title = formatMessage(messages.sendError);
      //       color = "red";
      //     }
      //     return (
      //       <Tooltip title={title}>
      //         <span style={{ color }}>{`0${text.slice(-9)}`}</span>
      //       </Tooltip>
      //     );
      //   },
      // },
    ];

    const {
      prevType,
      apartmentSearch,
      residentSearch,
      statusSearch,
      dataSource,
    } = this.state;
    return (
      <Page inner>
        <Row className="NotificationDetail">
          <Col span={24}>
            <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
              <Col
                span={12}
                style={{ borderRight: "1px solid #D9D9D9", paddingRight: 24 }}
              >
                <Row type="flex" align="middle" justify="space-between">
                  <span
                    style={{ fontWeight: "bold", fontSize: 18, color: "black" }}
                  >
                    {formatMessage(messages.contentSend)}
                  </span>
                </Row>
                <br />
                <Form labelAlign="left" {...formItemLayout}>
                  <Form.Item
                    label={formatMessage(messages.title)}
                    style={{ marginBottom: 0 }}
                  >
                    <strong style={{ color: "black" }}>
                      {language === "en" ? detail.title_en : detail.title}
                    </strong>
                  </Form.Item>
                  <Form.Item
                    label={formatMessage(messages.notificationType)}
                    style={{ marginBottom: 0 }}
                  >
                    <span>{formatMessage(messages.feeNotification)}</span>
                  </Form.Item>
                  <Form.Item
                    label={formatMessage(messages.status)}
                    style={{ marginBottom: 0 }}
                  >
                    <span className={"luci-status-success"}>
                      {formatMessage(messages.public)}
                    </span>
                  </Form.Item>
                  <Form.Item
                    label={formatMessage(messages.sendMethod)}
                    style={{ marginBottom: 0 }}
                  >
                    <Checkbox disabled checked={detail.is_send_push == 1}>
                      <span style={{ color: "rgba(0, 0, 0, 0.65)" }}>
                        {formatMessage(messages.sendApp)}
                      </span>
                    </Checkbox>
                    <br />
                    <Checkbox checked={detail.is_send_email == 1}>
                      {formatMessage(messages.sendEmail)}
                    </Checkbox>
                    <br />
                    {/* <Checkbox checked={detail.is_send_sms == 1}>
                      {formatMessage(messages.sendSMS)}
                    </Checkbox>
                    <br /> */}
                  </Form.Item>
                  <Form.Item
                    label={formatMessage(messages.timeSent)}
                    style={{ marginBottom: 0 }}
                  >
                    {moment.unix(detail.send_at).format("HH:mm DD/MM/YYYY")}
                  </Form.Item>
                  <Form.Item
                    label={formatMessage(messages.sendTarget)}
                    style={{ marginBottom: 0 }}
                  >
                    <Checkbox.Group
                      // disabled
                      value={detail.targets ? detail.targets : [1]}
                    >
                      <Checkbox
                        disabled={true}
                        value={1}
                        style={{ marginTop: 8 }}
                      >
                        <span style={{ color: "rgba(0, 0, 0, 0.65)" }}>
                          {formatMessage(messages.ownerDefault)}
                        </span>
                      </Checkbox>
                      <br />
                      <Checkbox value={0} style={{ marginTop: 8 }}>
                        {formatMessage(messages.ownerFamily)}
                      </Checkbox>
                      <br />
                      <Checkbox value={2} style={{ marginTop: 8 }}>
                        {formatMessage(messages.guest)}
                      </Checkbox>
                      <br />
                      <Checkbox value={3} style={{ marginTop: 8 }}>
                        {formatMessage(messages.guestFamily)}
                      </Checkbox>
                      <br />
                    </Checkbox.Group>
                  </Form.Item>
                </Form>
                <Row style={{ marginTop: 56 }}>
                  <Col
                    md={{
                      span: 22,
                      offset: 1,
                    }}
                    lg={24}
                  >
                    <ReactHighstock
                      config={{
                        chart: {
                          type: "bar",
                        },
                        title: {
                          text: formatMessage(messages.statistic),
                        },
                        xAxis: {
                          categories: ["Email", "App"],
                          title: {
                            text: null,
                          },
                        },
                        yAxis: {
                          min: 0,
                          title: {
                            text: formatMessage(messages.sendCount),
                            align: "high",
                          },
                          labels: {
                            overflow: "justify",
                          },
                        },
                        credits: {
                          enabled: false,
                        },
                        plotOptions: {
                          bar: {
                            dataLabels: {
                              enabled: true,
                            },
                          },
                        },
                        series: [
                          {
                            name: formatMessage(messages.needSend),
                            data: [
                              detail.total_email_send || 0,
                              detail.total_app_send || 0,
                              // detail.total_sms_send || 0,
                            ].map((cc) => {
                              return {
                                y: cc,
                              };
                            }),
                            color: "#93EB82",
                          },
                          {
                            name: formatMessage(messages.sent),
                            data: [
                              detail.total_email_send_success || 0,
                              detail.total_app_success || 0,
                              // detail.total_sms_send_success || 0,
                            ].map((cc) => {
                              return {
                                y: cc,
                              };
                            }),
                            color: GLOBAL_COLOR,
                          },
                          {
                            name: formatMessage(messages.read),
                            data: [
                              detail.total_email_open || 0,
                              detail.total_app_open || 0,
                              // detail.total_sms_send_success || 0,
                            ].map((cc) => {
                              return {
                                y: cc,
                              };
                            }),
                            color: "#F5A362",
                          },
                        ],
                      }}
                    />
                  </Col>
                </Row>
              </Col>
              <Col md={24} lg={12}>
                <Row>
                  <Col
                    md={{
                      span: 20,
                      offset: 2,
                    }}
                    lg={{
                      span: 22,
                      offset: 1,
                    }}
                  >
                    {prevType == 0 && (
                      <div className={"webPreview"}>
                        <div style={{ height: "100%", overflowY: "scroll" }}>
                          <div
                            className="dangerouslySetInnerHTMLWeb"
                            dangerouslySetInnerHTML={{
                              __html: (detail.content || "")
                                .replace(
                                  /{{RESIDENT_NAME}}/g,
                                  "<strong>Nguyễn Văn A</strong>"
                                )
                                .replace(
                                  /{{APARTMENT_NAME}}/g,
                                  "<strong>TSQ.T1007</strong>"
                                )
                                .replace(
                                  /{{TOTAL_FEE}}/g,
                                  "<strong>2.000.000 VNĐ</strong>"
                                )
                                .replace(
                                  /{{PAYMENT_CODE}}/g,
                                  "<strong>9ATV0GV4</strong>"
                                ),
                            }}
                          />
                        </div>
                      </div>
                    )}
                    {prevType == 1 && (
                      <div className={"mobilePreview"}>
                        <div
                          style={{
                            height: 540,
                            overflowY: "scroll",
                            backgroundColor: "white",
                            paddingLeft: 16,
                            paddingRight: 0,
                          }}
                        >
                          <br />
                          <strong style={{ color: "black", fontSize: 20 }}>
                            {detail.title}
                          </strong>
                          <Row
                            style={{
                              fontSize: 12,
                              color: "gray",
                              marginTop: 4,
                              marginBottom: 4,
                            }}
                            type="flex"
                            align="middle"
                            justify="space-between"
                          >
                            <span>
                              {moment
                                .unix(detail.updated_at)
                                .format("HH:mm DD/MM/YYYY")}
                            </span>
                            <Row type="flex" align="middle">
                              <div
                                style={{
                                  width: 0,
                                  height: 0,
                                  borderTop: `12px solid ${detail.announcement_category_color}`,
                                  borderBottom: `12px solid ${detail.announcement_category_color}`,
                                  borderLeft: "12px solid transparent",
                                }}
                              />
                              <Row
                                style={{
                                  height: 24,
                                  backgroundColor:
                                    detail.announcement_category_color,
                                  paddingLeft: 8,
                                  paddingRight: 8,
                                  color: "white",
                                }}
                                type="flex"
                                align="middle"
                                justify="center"
                              >
                                {detail.announcement_category_name}
                              </Row>
                            </Row>
                          </Row>
                          <div
                            style={{ marginRight: 16 }}
                            className="dangerouslySetInnerHTMLApp"
                            dangerouslySetInnerHTML={{
                              __html: (detail.content || "")
                                .replace(
                                  /{{RESIDENT_NAME}}/g,
                                  "<strong>Nguyễn Văn A</strong>"
                                )
                                .replace(
                                  /{{APARTMENT_NAME}}/g,
                                  "<strong>TSQ.T1007</strong>"
                                )
                                .replace(
                                  /{{TOTAL_FEE}}/g,
                                  "<strong>2.000.000 VNĐ</strong>"
                                )
                                .replace(
                                  /{{PAYMENT_CODE}}/g,
                                  "<strong>9ATV0GV4</strong>"
                                ),
                            }}
                          />
                        </div>
                      </div>
                    )}
                    {/* {prevType == 2 && (
                      <div className={"mobilePreview1"}>
                        <div className="smsPreviewContainer">
                          <Row
                            type="flex"
                            align="middle"
                            style={{ width: "100%" }}
                            justify="space-between"
                          >
                            <Row type="flex" align="middle">
                              <Row
                                style={{
                                  width: 32,
                                  height: 32,
                                  borderRadius: 10,
                                  backgroundColor: "#53E86D",
                                  color: "white",
                                  marginRight: 12,
                                }}
                                type="flex"
                                align="middle"
                                justify="center"
                              >
                                <i
                                  className="material-icons"
                                  style={{ fontSize: 22 }}
                                >
                                  message
                                </i>
                              </Row>
                              <span style={{ color: "black", fontSize: 12 }}>
                                {formatMessage(messages.sms)}
                              </span>
                            </Row>
                            <span
                              style={{ color: "#C4C4C4", textAlign: "right" }}
                            >
                              {formatMessage(messages.justNow)}
                            </span>
                          </Row>
                          <Row
                            style={{
                              marginTop: 4,
                              fontWeight: "bold",
                              color: "black",
                            }}
                          >
                            {COMPANY_NAME}
                          </Row>
                          <Row
                            style={{
                              marginTop: 4,
                              color: "black",
                            }}
                          >
                            {(detail.content_sms || "")
                              .replace(/{{RESIDENT_NAME}}/g, "Nguyễn Văn A")
                              .replace(/{{APARTMENT_NAME}}/g, "TSQ.T1007")
                              .replace(/{{TOTAL_FEE}}/g, "2.000.000 VNĐ")
                              .replace(/{{PAYMENT_CODE}}/g, "9ATV0GV4")}
                          </Row>
                        </div>
                      </div>
                    )} */}
                  </Col>
                </Row>
                <Row
                  type="flex"
                  justify="center"
                  style={{ marginTop: 24, height: "10%" }}
                >
                  <Radio.Group
                    value={prevType}
                    onChange={(e) => {
                      this.setState({
                        prevType: e.target.value,
                      });
                    }}
                    style={{ zIndex: 99 }}
                    buttonStyle="solid"
                  >
                    {/* {detail.is_send_push == 1 && ( */}
                    <Radio.Button value={1}>App</Radio.Button>
                    {/* )} */}
                    {/* {detail.is_send_email == 1 && ( */}
                    <Radio.Button value={0}>Email</Radio.Button>
                    {/* )} */}
                    {/* {detail.is_send_sms == 1 && (
                    <Radio.Button value={2}>SMS</Radio.Button>
                     )}  */}
                  </Radio.Group>
                </Row>
              </Col>
            </Row>
          </Col>
          <Col span={24} style={{ paddingTop: 24 }}>
            <span style={{ fontWeight: "bold", fontSize: 18, color: "black" }}>
              {formatMessage(messages.sendList)}
            </span>
            <br />
            <Row
              style={{ marginTop: 16, marginLeft: "20%", marginRight: "20%" }}
              type="flex"
              align="middle"
              justify="space-around"
            >
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>
                  {formatMessage(messages.totalResident)}
                </span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationDetail.apartmentReminder.totalPage || 0}
                </span>
              </Col>
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>Email</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {apartmentReminder.total_count.total_email || 0}
                </span>
              </Col>
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>App</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {apartmentReminder.total_count.total_app || 0}
                </span>
              </Col>
              {/* <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>SMS</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {apartmentReminder.total_count.total_sms || 0}
                </span>
              </Col> */}
            </Row>
            <br />
            <Row gutter={[16, 16]}>
              <Col {...colLayout}>
                <Input.Search
                  value={apartmentSearch}
                  placeholder={formatMessage(messages.property)}
                  onChange={(e) => {
                    this.setState({
                      apartmentSearch: e.target.value,
                    });
                  }}
                  maxLength={255}
                />
              </Col>
              <Col {...colLayout}>
                <Input.Search
                  value={residentSearch}
                  placeholder={formatMessage(messages.resident)}
                  onChange={(e) => {
                    this.setState({
                      residentSearch: e.target.value,
                    });
                  }}
                  maxLength={255}
                />
              </Col>
              <Col {...colLayout}>
                <Select
                  style={{ width: "100%" }}
                  placeholder={formatMessage(messages.status)}
                  onChange={(value) => {
                    this.setState({
                      statusSearch: value,
                    });
                  }}
                  allowClear
                >
                  <Select.Option value={1}>
                    {formatMessage(messages.read)}
                  </Select.Option>
                  <Select.Option value={0}>
                    {formatMessage(messages.notRead)}
                  </Select.Option>
                </Select>
              </Col>
              <Col {...colLayout}>
                <Button
                  type="primary"
                  onClick={() => {
                    this.setState({
                      dataSource:
                        notificationDetail.apartmentReminder.data.filter(
                          (item) =>
                            removeAccents(item.apartment_name || "").includes(
                              removeAccents(apartmentSearch)
                            ) &&
                            removeAccents(
                              item.resident_user_name || ""
                            ).includes(removeAccents(residentSearch)) &&
                            (statusSearch === undefined
                              ? true
                              : statusSearch
                              ? !!item.read_at === true
                              : !!item.read_at === false)
                        ),
                    });
                  }}
                >
                  {formatMessage(messages.search)}
                </Button>
              </Col>
            </Row>
            <br />
            <Table
              rowKey={"id"}
              columns={columns}
              bordered
              scroll={{ x: 900 }}
              dataSource={dataSource}
              loading={apartmentReminder.loading}
              pagination={{
                pageSize: 10,
                total: apartmentReminder.totalPage,
                current: this.state.current,
                showTotal: (total) =>
                  formatMessage(messages.totals, {
                    total,
                  }),
              }}
              onChange={this.handleTableChange}
            />
          </Col>
        </Row>
      </Page>
    );
  }
  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      if (this.props.notificationDetail.detail.status == 0) {
        this.props.dispatch(
          fetchApartmentSent({
            page: pagination.current,
            pageSize: 10,
            announcement_campaign_id: this.props.match.params.id,
            building_area_ids:
              this.props.notificationDetail.detail.building_area_ids,
          })
        );
      } else {
        this.props.dispatch(
          fetchApartmentFeeReminder({
            type: this.state.type,
            page: pagination.current,
            pageSize: 10,
            announcement_campaign_id: this.props.match.params.id,
          })
        );
      }
    });
  };
}

NotificationFeeDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notificationDetail: makeSelectNotificationFeeDetail(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "notificationFeeDetail", reducer });
const withSaga = injectSaga({ key: "notificationFeeDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(NotificationFeeDetail));
