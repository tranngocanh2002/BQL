/**
 *
 * NotificationDetail
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
  Checkbox,
  Col,
  Form,
  Input,
  Radio,
  Row,
  Select,
  Table,
  Tag,
  Tooltip,
} from "antd";
import Chart from "react-apexcharts";
import "react-draft-wysiwyg/dist/react-draft-wysiwyg.css";
import { FormattedMessage, injectIntl } from "react-intl";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { config, formatPrice } from "../../../utils";
import messages from "../messages";
import {
  defaultAction,
  fetchApartmentFeeReminder,
  fetchApartmentSent,
  fetchNotificationDetail,
  fetchReportChart,
  fetchSurveyAnswer,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectNotificationDetail from "./selectors";

import { EditorState } from "draft-js";

import Exception from "ant-design-pro/lib/Exception";
import { getFullLinkImage } from "connection";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import WithRole from "../../../components/WithRole";
import {
  COMPANY_NAME,
  GLOBAL_COLOR,
  removeAccents,
} from "../../../utils/constants";

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
export class NotificationDetail extends React.PureComponent {
  constructor(props) {
    super(props);
    let { record } = (props.location.state || {}).record;
    let category = (props.location.state || {}).category;

    this.state = {
      record,
      category,
      editorState: EditorState.createEmpty(),
      type: 1,
      current: 1,
      surveyCurrent: 1,
      // contentSMS: "",
      prevType: 1,
      pushType: [],
      selectedRow: [],
      resident_user_phones: [],
      dataSource: [],
      apartmentSearch: "",
      residentSearch: "",
      statusSearch: undefined,
      dataSourceSurvey: [],
      apartmentSearchSurvey: "",
      residentSearchSurvey: "",
      statusSearchSurvey: undefined,
      resultSearch: undefined,
      activeTab: "1",
      exporting: false,
    };
  }

  onEditorStateChange = (editorState) => {
    this.setState({
      editorState,
    });
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    setTimeout(() => {
      window.scrollTo(0, 0);
    }, 200);
    this.props.dispatch(fetchNotificationDetail(this.props.match.params.id));
    this.props.dispatch(fetchReportChart(this.props.match.params.id));
  }
  componentWillReceiveProps(nextProps) {
    if (
      this.props.notificationDetail.detail !=
        nextProps.notificationDetail.detail &&
      !!nextProps.notificationDetail.detail
    ) {
      this.setState({
        prevType:
          nextProps.notificationDetail.detail.is_send_push == 1
            ? 1
            : nextProps.notificationDetail.detail.is_send_email == 1
            ? 0
            : 2,
      });
      if (
        nextProps.notificationDetail.detail.resident_user_phones &&
        nextProps.notificationDetail.detail.resident_user_phones.length
      ) {
        this.setState({
          resident_user_phones:
            nextProps.notificationDetail.detail.resident_user_phones,
        });
      }
    }
    if (
      this.props.notificationDetail.apartmentReminder.loading !=
        nextProps.notificationDetail.apartmentReminder.loading &&
      !nextProps.notificationDetail.apartmentReminder.loading &&
      !!nextProps.notificationDetail.detail &&
      !!nextProps.notificationDetail.apartmentReminder.data.length
    ) {
      this.setState({
        dataSource: nextProps.notificationDetail.apartmentReminder.data,
        //   .filter(
        //   (row) =>
        //     !(
        //       nextProps.notificationDetail.detail.resident_user_phones ||
        //       this.state.resident_user_phones
        //     ).includes(row.phone)
        // )
      });
    }
    if (
      this.props.notificationDetail.surveyAnswer.loading !=
        nextProps.notificationDetail.surveyAnswer.loading &&
      !nextProps.notificationDetail.surveyAnswer.loading &&
      !!nextProps.notificationDetail.surveyAnswer &&
      !!nextProps.notificationDetail.surveyAnswer.data.length
    ) {
      this.setState({
        dataSourceSurvey: nextProps.notificationDetail.surveyAnswer.data,
      });
    }
    if (
      this.props.notificationDetail.apartmentReminder.loading !=
        nextProps.notificationDetail.apartmentReminder.loading &&
      !nextProps.notificationDetail.apartmentReminder.loading &&
      !!nextProps.notificationDetail.detail &&
      nextProps.notificationDetail.detail.status == 0 &&
      !!nextProps.notificationDetail.apartmentReminder.data.length
    ) {
      const phone_not_send = this.state.resident_user_phones || [];
      if (
        nextProps.notificationDetail.detail.apartment_ids &&
        nextProps.notificationDetail.detail.apartment_ids.length
      ) {
        this.setState({
          selectedRow: nextProps.notificationDetail.apartmentReminder.data
            .filter(
              (row) =>
                nextProps.notificationDetail.detail.apartment_ids.includes(
                  row.apartment_id
                ) && !phone_not_send.includes(row.phone)
            )
            .map((opt) => opt.id),
        });
      } else if (
        nextProps.notificationDetail.detail.apartment_not_send_ids &&
        nextProps.notificationDetail.detail.apartment_not_send_ids.length
      ) {
        this.setState({
          selectedRow: nextProps.notificationDetail.apartmentReminder.data
            .filter(
              (row) =>
                !nextProps.notificationDetail.detail.apartment_not_send_ids.includes(
                  row.apartment_id
                ) && !phone_not_send.includes(row.phone)
            )
            .map((opt) => opt.id),
        });
      } else {
        this.setState({
          selectedRow: nextProps.notificationDetail.apartmentReminder.data
            .filter((row) => !phone_not_send.includes(row.phone))
            .map((opt) => opt.id),
        });
      }
    }
  }

  render() {
    const { notificationDetail, intl, language } = this.props;
    const apartmentText = intl.formatMessage({
      ...messages.property,
    });
    const residentText = intl.formatMessage({
      ...messages.resident,
    });
    const statusText = intl.formatMessage({
      ...messages.status,
    });
    const readStatusText = intl.formatMessage({
      ...messages.readStatus,
    });
    const resultText = intl.formatMessage({
      ...messages.result,
    });
    const totalLabel = intl.formatMessage({
      ...messages.totalLabel,
    });
    const sentText = intl.formatMessage({ ...messages.sent });
    const readText = intl.formatMessage({ ...messages.read });
    const sendErrorText = intl.formatMessage({ ...messages.sendError });
    const totalResidentAnswerText = intl.formatMessage({
      ...messages.totalResidentAnswer,
    });
    const statisticText = intl.formatMessage({ ...messages.statistic });
    const sendCountText = intl.formatMessage({ ...messages.sendCount });
    const needSendText = intl.formatMessage({ ...messages.needSend });
    const noAnswerYetText = intl.formatMessage({ ...messages.noAnswerYet });
    const answerNumberText = intl.formatMessage({ ...messages.answerNumber });
    const agreeText = intl.formatMessage({ ...messages.agree });
    const disagreeText = intl.formatMessage({ ...messages.disagree });
    const { loading, detail, apartmentReminder, surveyAnswer, reportChart } =
      notificationDetail;
    if (loading) {
      return <Page inner loading />;
    }
    const now = moment().unix();
    const isTimer =
      (detail && detail.status === 2) ||
      (detail && detail.is_event === 1 && detail.send_event_at > now) ||
      (detail.is_event === 0 && detail.send_at > now);
    const isPublic = detail && !isTimer && detail.status == 1;
    const isDraft = detail && !isTimer && detail.status == 0;

    if (!detail) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={<FormattedMessage {...messages.notFindNotificationDetail} />}
            actions={
              <Button
                type="primary"
                onClick={() =>
                  this.props.history.push("/main/notification/list")
                }
              >
                <FormattedMessage {...messages.back} />
              </Button>
            }
          />
        </Page>
      );
    }
    let surveyColumns = [
      {
        title: <span>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              surveyAnswer.loading
                ? this.state.surveyCurrent - 2
                : this.state.surveyCurrent - 1
            ) *
              10 +
              index +
              1}
          </span>
        ),
      },
      {
        title: (
          <span>
            <FormattedMessage {...messages.property} />
          </span>
        ),
        dataIndex: "apartment.name",
        key: "apartment",
      },
      {
        title: (
          <span>
            <FormattedMessage {...messages.resident} />
          </span>
        ),
        render: (text, record) => (
          <span>
            {record.resident_user ? record.resident_user.first_name : ""}
          </span>
        ),
        key: "resident_user",
      },
      {
        title: (
          <span>
            <FormattedMessage {...messages.address} />
          </span>
        ),
        dataIndex: "apartment.parent_path",
        key: "apartment_parent_path",
      },
      {
        title: <span>Email</span>,
        dataIndex: "resident_user.email",
        key: "email",
        render: (text, record) => {
          if (!record.resident_user) {
            return (
              <Tooltip title={<FormattedMessage {...messages.notConfig} />}>
                <i
                  className="material-icons"
                  style={{ color: "#E4E4E4", cursor: "pointer" }}
                >
                  unsubscribe
                </i>
              </Tooltip>
            );
          }
          if (!record.resident_user.email) {
            return (
              <Tooltip title={<FormattedMessage {...messages.notConfig} />}>
                <i
                  className="material-icons"
                  style={{ color: "#E4E4E4", cursor: "pointer" }}
                >
                  unsubscribe
                </i>
              </Tooltip>
            );
          }
          let title = sentText;
          let color = GLOBAL_COLOR;
          if (record.read_at) {
            title = readText;
            color = "#F5A362";
          } else if (record.status_notify == 1) {
            title = sentText;
            color = GLOBAL_COLOR;
          } else if (record.status_notify == 2) {
            title = sendErrorText;
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
        dataIndex: "resident_user.active_app",
        key: "app",
        render: (text, record) => {
          if (!record.resident_user) {
            return (
              <Tooltip title={<FormattedMessage {...messages.appNotInstall} />}>
                <i
                  className="material-icons"
                  style={{ color: "#E4E4E4", cursor: "pointer" }}
                >
                  mobile_off
                </i>
              </Tooltip>
            );
          }
          if (!record.resident_user.active_app) {
            return (
              <Tooltip title={<FormattedMessage {...messages.appNotInstall} />}>
                <i
                  className="material-icons"
                  style={{ color: "#E4E4E4", cursor: "pointer" }}
                >
                  mobile_off
                </i>
              </Tooltip>
            );
          }
          let title = sentText;
          let color = GLOBAL_COLOR;
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
      {
        title: (
          <span>
            <FormattedMessage {...messages.result} />
          </span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text, record) => {
          if (record.status == 0) {
            return <span />;
            // (
            //   <Tag color="#F5A362">
            //     <FormattedMessage {...messages.notAnswer} />
            //   </Tag>
            // );
          } else if (record.status == 1) {
            return (
              <Tag color={GLOBAL_COLOR}>
                <FormattedMessage {...messages.agree} />
              </Tag>
            );
          } else {
            return (
              <Tag color="red-inverse">
                <FormattedMessage {...messages.disagree} />
              </Tag>
            );
          }
        },
      },
      {
        title: (
          <span>
            <FormattedMessage {...messages.answerTime} />
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text, record) =>
          record.status == 0 ? (
            <span />
          ) : (
            moment.unix(text).format("HH:mm DD/MM/YYYY")
          ),
      },
    ];

    let columns =
      detail.type === 0
        ? [
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
              title: (
                <span>
                  <FormattedMessage {...messages.property} />
                </span>
              ),
              dataIndex: "apartment_name",
              key: "apartment_name",
            },
            {
              title: (
                <span>
                  <FormattedMessage {...messages.resident} />
                </span>
              ),
              dataIndex: "resident_user_name",
              key: "resident_user_name",
            },
            {
              title: (
                <span>
                  <FormattedMessage {...messages.address} />
                </span>
              ),
              dataIndex: "apartment_parent_path",
              key: "apartment_parent_path",
            },
            {
              title: <span>Email</span>,
              dataIndex: "email",
              key: "email",
              render: (text, record) => {
                if (!record.email) {
                  return (
                    <Tooltip
                      title={<FormattedMessage {...messages.notConfig} />}
                    >
                      <i
                        className="material-icons"
                        style={{ color: "#E4E4E4", cursor: "pointer" }}
                      >
                        unsubscribe
                      </i>
                    </Tooltip>
                  );
                }
                let title = sentText;
                let color = GLOBAL_COLOR;
                if (record.read_email_at) {
                  title = readText;
                  color = "#F5A362";
                } else if (record.status_email == 1) {
                  title = sentText;
                  color = GLOBAL_COLOR;
                } else if (record.status_email == 2) {
                  title = sendErrorText;
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
                    <Tooltip
                      title={<FormattedMessage {...messages.appNotInstall} />}
                    >
                      <i
                        className="material-icons"
                        style={{ color: "#E4E4E4", cursor: "pointer" }}
                      >
                        mobile_off
                      </i>
                    </Tooltip>
                  );
                }
                let title = sentText;
                let color = GLOBAL_COLOR;
                if (record.read_at) {
                  title = readText;
                  color = "#F5A362";
                } else if (record.status_notify == 1) {
                  title = sentText;
                  color = GLOBAL_COLOR;
                } else if (record.status_notify == 2) {
                  title = sendErrorText;
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
          ]
        : [
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
              title: (
                <span>
                  <FormattedMessage {...messages.property} />
                </span>
              ),
              dataIndex: "apartment_name",
              key: "apartment_name",
            },
            {
              title: (
                <span>
                  <FormattedMessage {...messages.resident} />
                </span>
              ),
              dataIndex: "resident_user_name",
              key: "resident_user_name",
            },
            {
              title: (
                <span>
                  <FormattedMessage {...messages.address} />
                </span>
              ),
              dataIndex: "apartment_parent_path",
              key: "apartment_parent_path",
            },
            {
              title: <span>{<FormattedMessage {...messages.endDept} />}</span>,
              dataIndex: "end_debt",
              key: "end_debt",
              render: (text) => {
                return <span>{formatPrice(text ? text : 0)} đ</span>;
              },
            },
            {
              title: <span>Email</span>,
              dataIndex: "email",
              key: "email",
              render: (text, record) => {
                if (!record.email) {
                  return (
                    <Tooltip
                      title={<FormattedMessage {...messages.notConfig} />}
                    >
                      <i
                        className="material-icons"
                        style={{ color: "#E4E4E4", cursor: "pointer" }}
                      >
                        unsubscribe
                      </i>
                    </Tooltip>
                  );
                }
                let title = sentText;
                let color = GLOBAL_COLOR;
                if (record.read_email_at) {
                  title = readText;
                  color = "#F5A362";
                } else if (record.status_email == 1) {
                  title = sentText;
                  color = GLOBAL_COLOR;
                } else if (record.status_email == 2) {
                  title = sendErrorText;
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
                    <Tooltip
                      title={<FormattedMessage {...messages.appNotInstall} />}
                    >
                      <i
                        className="material-icons"
                        style={{ color: "#E4E4E4", cursor: "pointer" }}
                      >
                        mobile_off
                      </i>
                    </Tooltip>
                  );
                }
                let title = sentText;
                let color = GLOBAL_COLOR;
                if (record.read_at) {
                  title = readText;
                  color = "#F5A362";
                } else if (record.status_notify == 1) {
                  title = sentText;
                  color = GLOBAL_COLOR;
                } else if (record.status_notify == 2) {
                  title = sendErrorText;
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
            //         <Tooltip
            //           title={<FormattedMessage {...messages.appNotInstall} />}
            //         >
            //           <i
            //             className="material-icons"
            //             style={{ color: "#E4E4E4", cursor: "pointer" }}
            //           >
            //             phone_disabled
            //           </i>
            //         </Tooltip>
            //       );
            //     }
            //     let title = readText;
            //     let color = GLOBAL_COLOR;
            //     if (record.status_sms == 2) {
            //       title = sendErrorText;
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
      dataSourceSurvey,
      apartmentSearchSurvey,
      residentSearchSurvey,
      statusSearchSurvey,
      resultSearch,
      category,
    } = this.state;
    const apartmentSelection = {
      selectedRowKeys: this.state.selectedRow,
    };
    const { pie_chart, bar_chart } = reportChart.data;

    let series = [
      {
        status: 0,
        total: 0,
      },
      {
        status: 1,
        total: 0,
      },
      {
        status: 2,
        total: 0,
      },
    ];
    if (pie_chart) {
      pie_chart.forEach((item) => {
        series[item.status].total = Number(item.total_answer);
      });
    }

    // reverse series to be 1, 2, 0
    const seriesData = series
      .slice(1, 3)
      .concat(series.slice(0, 1))
      .map((item) => item.total);

    const seriesLine = [
      {
        name: totalResidentAnswerText,
        data: bar_chart
          ? bar_chart.map((item) => {
              return {
                x: item.report_day,
                y: item.total,
              };
            })
          : [],
      },
    ];

    const stockCategories = ["Email", "App"];
    const stockNeedSend = [
      detail.total_email_send || 0,
      detail.total_app_send || 0,
    ];
    const stockSent = [
      detail.total_email_send_success || 0,
      detail.total_app_success || 0,
    ];
    const stockRead = [undefined, detail.total_app_open || 0];

    return (
      <Page inner>
        <Row className="NotificationDetail">
          <Col span={24}>
            <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
              <Col
                md={24}
                lg={12}
                style={{ borderRight: "1px solid #D9D9D9", paddingRight: 24 }}
              >
                <Row type="flex" align="middle" justify="space-between">
                  <span
                    style={{ fontWeight: "bold", fontSize: 18, color: "black" }}
                  >
                    <FormattedMessage {...messages.contentSend} />
                  </span>
                  {detail.type == 0 && (
                    <WithRole
                      roles={[
                        config.ALL_ROLE_NAME
                          .NOTIFICATION_FORM_MANAGEMENT_UPDATE,
                      ]}
                    >
                      <Button
                        type="primary"
                        onClick={() => {
                          this.props.history.push(
                            `/main/notification/edit/${detail.id}?notiType=${
                              detail.is_survey === 1 ? 6 : 0
                            }`
                          );
                        }}
                      >
                        <FormattedMessage {...messages.edit} />
                      </Button>
                    </WithRole>
                  )}
                </Row>
                <br />
                <Form labelAlign="left" {...formItemLayout}>
                  <Form.Item
                    label={<FormattedMessage {...messages.title} />}
                    style={{ marginBottom: 0 }}
                  >
                    <strong style={{ color: "black" }}>
                      {language === "en" ? detail.title_en : detail.title}
                    </strong>
                  </Form.Item>
                  <Form.Item
                    label={<FormattedMessage {...messages.notificationType} />}
                    style={{ marginBottom: 0 }}
                  >
                    {detail.is_survey == 1 ? (
                      <span>
                        <FormattedMessage {...messages.surveyNotification} />
                      </span>
                    ) : (
                      <span>
                        {detail.type == 0 ? (
                          <FormattedMessage {...messages.regularNotification} />
                        ) : (
                          <FormattedMessage {...messages.feeNotification} />
                        )}
                      </span>
                    )}
                  </Form.Item>
                  <Form.Item
                    label={<FormattedMessage {...messages.status} />}
                    style={{ marginBottom: 0 }}
                  >
                    {!isTimer && isPublic && (
                      <span className={"luci-status-success"}>
                        <FormattedMessage {...messages.public} />
                      </span>
                    )}
                    {!isTimer && isDraft && (
                      <span className={"luci-status-warning"}>
                        <FormattedMessage {...messages.draft} />
                      </span>
                    )}
                    {isTimer && (
                      <span className={"luci-status-danger"}>
                        <FormattedMessage {...messages.timer} />
                      </span>
                    )}
                  </Form.Item>
                  {detail.type == 0 && (
                    <Form.Item
                      label={<FormattedMessage {...messages.category} />}
                      style={{ marginBottom: 0 }}
                    >
                      <span
                        className="announcementWrapper"
                        style={{
                          backgroundColor: detail.announcement_category_color,
                        }}
                      >
                        {this.props.language === "vi"
                          ? category.lst.find(
                              (item) =>
                                item.id === detail.announcement_category_id
                            ).name
                          : category.lst.find(
                              (item) =>
                                item.id === detail.announcement_category_id
                            ).name_en}
                      </span>
                    </Form.Item>
                  )}
                  <Form.Item
                    label={<FormattedMessage {...messages.sendMethod} />}
                    style={{ marginBottom: 0 }}
                  >
                    <Checkbox disabled checked={detail.is_send_push == 1}>
                      <span style={{ color: "rgba(0, 0, 0, 0.65)" }}>
                        <FormattedMessage {...messages.sendApp} />
                      </span>
                    </Checkbox>
                    <br />
                    <Checkbox disabled checked={detail.is_send_email == 1}>
                      <FormattedMessage {...messages.sendEmail} />
                    </Checkbox>
                    <br />
                    {/* {detail.type !== 0 && (
                      <>
                        <Checkbox checked={detail.is_send_sms == 1}>
                          <FormattedMessage {...messages.sendSMS} />
                        </Checkbox>
                        <br />
                      </>
                    )} */}
                  </Form.Item>
                  <Form.Item
                    label={<FormattedMessage {...messages.timeSent} />}
                    style={{ marginBottom: 0 }}
                  >
                    {detail.is_event === 1
                      ? moment
                          .unix(detail.send_event_at)
                          .subtract(1, "day")
                          .format("HH:mm DD/MM/YYYY")
                      : moment.unix(detail.send_at).format("HH:mm DD/MM/YYYY")}
                  </Form.Item>
                  {/* {detail.created_at !== detail.updated_at && (
                    <Form.Item
                      label={<FormattedMessage {...messages.nearestEditDate} />}
                      style={{ marginBottom: 0 }}
                    >
                      {moment
                        .unix(detail.updated_at)
                        .format("HH:mm DD/MM/YYYY")}
                    </Form.Item>
                  )} */}
                  {detail.is_survey === 1 && (
                    <Form.Item
                      label={<FormattedMessage {...messages.surveyDeadline} />}
                      style={{ marginBottom: 0 }}
                    >
                      {moment
                        .unix(detail.survey_deadline)
                        .format("HH:mm DD/MM/YYYY")}
                    </Form.Item>
                  )}
                  <Form.Item
                    label={<FormattedMessage {...messages.sendTarget} />}
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
                          <FormattedMessage {...messages.ownerDefault} />
                        </span>
                      </Checkbox>
                      <br />
                      <Checkbox value={0} style={{ marginTop: 8 }}>
                        <FormattedMessage {...messages.ownerFamily} />
                      </Checkbox>
                      <br />
                      <Checkbox value={2} style={{ marginTop: 8 }}>
                        <FormattedMessage {...messages.guest} />
                      </Checkbox>
                      <br />
                      <Checkbox value={3} style={{ marginTop: 8 }}>
                        <FormattedMessage {...messages.guestFamily} />
                      </Checkbox>
                      <br />
                    </Checkbox.Group>
                  </Form.Item>
                  {!!detail && !!detail.add_phone_send.length && (
                    <Form.Item
                      label={<FormattedMessage {...messages.phoneSend} />}
                    >
                      {detail.add_phone_send.map((item) => {
                        return <Tag key={`${item}`}>{item}</Tag>;
                      })}
                    </Form.Item>
                  )}
                  {!!detail && !!detail.add_email_send.length && (
                    <Form.Item
                      label={<FormattedMessage {...messages.emailSend} />}
                    >
                      {detail.add_email_send.map((item) => {
                        return <Tag key={`${item}`}>{item}</Tag>;
                      })}
                    </Form.Item>
                  )}
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
                          text: statisticText,
                        },
                        xAxis: {
                          categories: stockCategories,
                          title: {
                            text: null,
                          },
                        },
                        yAxis: {
                          min: 0,
                          title: {
                            text: sendCountText,
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
                            name: needSendText,
                            data: stockNeedSend.map((cc) => {
                              return {
                                y: cc,
                              };
                            }),
                            color: "#93EB82",
                          },
                          {
                            name: sentText,
                            data: stockSent.map((cc) => {
                              return {
                                y: cc,
                              };
                            }),
                            // color: "#93EB82",
                            color: GLOBAL_COLOR,
                          },
                          {
                            name: readText,
                            data: stockRead.map((cc) => {
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
                              __html: (
                                detail.content
                                  .replaceAll(
                                    "https://drive.google.com/file/d/",
                                    "https://drive.google.com/uc?export=view&id="
                                  )
                                  .replaceAll("/view?usp=sharing", "")
                                  .replaceAll("/view?usp=drive_link", "") || ""
                              )
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
                                {this.props.language === "vi"
                                  ? category.lst.find(
                                      (item) =>
                                        item.id ===
                                        detail.announcement_category_id
                                    ).name
                                  : category.lst.find(
                                      (item) =>
                                        item.id ===
                                        detail.announcement_category_id
                                    ).name_en}
                              </Row>
                            </Row>
                          </Row>
                          <div
                            style={{ marginRight: 16 }}
                            className="dangerouslySetInnerHTMLApp"
                            dangerouslySetInnerHTML={{
                              __html: (
                                detail.content
                                  .replaceAll(
                                    "https://drive.google.com/file/d/",
                                    "https://drive.google.com/uc?export=view&id="
                                  )
                                  .replaceAll("/view?usp=sharing", "")
                                  .replaceAll("/view?usp=drive_link", "") || ""
                              )
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
                                <FormattedMessage {...messages.sms} />
                              </span>
                            </Row>
                            <span
                              style={{ color: "#C4C4C4", textAlign: "right" }}
                            >
                              <FormattedMessage {...messages.justNow} />
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
                              .replace(/{{TOTAL_FEE}}/g, "2.000.000 VNĐ")}
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
                    {/* {detail.type !== 0 && (
                      <>
                        {detail.is_send_sms == 1 && (
                        <Radio.Button value={2}>SMS</Radio.Button>
                        )} 
                      </>
                    )} */}
                  </Radio.Group>
                </Row>
              </Col>
            </Row>
          </Col>
          {(detail.is_survey !== 1 || detail.status === 0) && (
            <Col span={24} style={{ paddingTop: 24 }}>
              <span
                style={{ fontWeight: "bold", fontSize: 18, color: "black" }}
              >
                <FormattedMessage {...messages.sendList} />
              </span>
              <br />
              <Row
                style={{
                  marginTop: 16,
                  marginLeft: "20%",
                  marginRight: "20%",
                }}
                type="flex"
                align="middle"
                justify="space-around"
              >
                <Col style={{ textAlign: "center" }}>
                  <span style={{ fontSize: 16 }}>
                    <FormattedMessage {...messages.totalResident} />
                  </span>
                  <br />
                  <span
                    style={{
                      fontWeight: "bold",
                      fontSize: 28,
                      lineHeight: 2,
                    }}
                  >
                    {apartmentReminder.data.length || 0}
                  </span>
                </Col>
                <Col style={{ textAlign: "center" }}>
                  <span style={{ fontSize: 16 }}>Email</span>
                  <br />
                  <span
                    style={{
                      fontWeight: "bold",
                      fontSize: 28,
                      lineHeight: 2,
                    }}
                  >
                    {apartmentReminder.data.filter(
                      (item) => item.email && item.email !== ""
                    ).length || 0}
                  </span>
                </Col>
                <Col style={{ textAlign: "center" }}>
                  <span style={{ fontSize: 16 }}>App</span>
                  <br />
                  <span
                    style={{
                      fontWeight: "bold",
                      fontSize: 28,
                      lineHeight: 2,
                    }}
                  >
                    {apartmentReminder.data.filter(
                      (item) => item.app && item.app === 1
                    ).length || 0}
                  </span>
                </Col>
                {/* {detail.type !== 0 && (
                  <Col style={{ textAlign: "center" }}>
                    <span style={{ fontSize: 16 }}>SMS</span>
                    <br />
                    <span
                      style={{
                        fontWeight: "bold",
                        fontSize: 28,
                        lineHeight: 2,
                      }}
                    >
                      {apartmentReminder.data.filter(
                        (item) => item.phone !== null
                      ).length || 0}
                    </span>
                  </Col>
                )} */}
              </Row>
              <br />
              <Row gutter={[16, 16]}>
                <Col {...colLayout}>
                  <Input.Search
                    value={apartmentSearch}
                    placeholder={apartmentText}
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
                    placeholder={residentText}
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
                    placeholder={statusText}
                    onChange={(value) => {
                      this.setState({
                        statusSearch: value,
                      });
                    }}
                    allowClear
                  >
                    <Select.Option value={1}>
                      <FormattedMessage {...messages.read} />
                    </Select.Option>
                    <Select.Option value={0}>
                      <FormattedMessage {...messages.notRead} />
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
                    <FormattedMessage {...messages.search} />
                  </Button>
                </Col>
                <Tooltip title={"Export"}>
                  <Button
                    style={{ position: "absolute", right: 10 }}
                    onClick={() => {
                      this.setState(
                        {
                          exporting: true,
                        },
                        () => {
                          window.connection
                            .exportNotificationDetail({
                              announcement_campaign_type:
                                detail.is_survey === 1 ? 2 : detail.type,
                              announcement_campaign_id: detail.id,
                            })
                            .then((res) => {
                              if (this._unmounted) return;
                              this.setState(
                                {
                                  exporting: false,
                                },
                                () => {
                                  if (res.success) {
                                    window.open(
                                      getFullLinkImage(res.data.file_path)
                                    );
                                  }
                                }
                              );
                            })
                            .catch(() => {
                              if (this._unmounted) return;
                              this.setState({
                                exporting: false,
                              });
                            });
                        }
                      );
                    }}
                    loading={this.state.exporting}
                    shape="circle"
                    size="large"
                  >
                    <i
                      className="material-icons"
                      style={{
                        fontSize: 14,
                        display: "flex",
                        justifyContent: "center",
                        fontWeight: "bold",
                      }}
                    >
                      login
                    </i>
                  </Button>
                </Tooltip>
              </Row>
              <br />

              <Table
                rowKey={"id"}
                locale={{
                  emptyText: <FormattedMessage {...messages.noData} />,
                }}
                bordered
                columns={columns}
                dataSource={dataSource}
                rowSelection={detail.status === 0 ? apartmentSelection : null}
                loading={apartmentReminder.loading}
                scroll={{ x: 900 }}
                pagination={{
                  pageSize: 10,
                  total: apartmentReminder.totalPage,
                  current: this.state.current,
                  showTotal: (total) => (
                    <FormattedMessage {...messages.totals} values={{ total }} />
                  ),
                }}
                onChange={this.handleTableChange}
              />
            </Col>
          )}

          {detail.is_survey == 1 &&
            this.props.notificationDetail.detail.status == 1 && (
              <>
                <span
                  style={{
                    fontWeight: "bold",
                    fontSize: 18,
                    color: "black",
                  }}
                >
                  <FormattedMessage {...messages.surveyAnswer} />
                </span>
                <br />
                <br />
                <Col
                  md={24}
                  lg={12}
                  style={{
                    paddingTop: 24,
                    // paddingLeft: 24,
                    paddingRight: 24,
                  }}
                >
                  <Card
                    // bordered={false}
                    bodyStyle={{ height: 350 }}
                    title={
                      <span style={{ fontSize: 20, fontWeight: "bold" }}>
                        <FormattedMessage
                          {...messages.residentAnswerStatistic}
                        />
                      </span>
                    }
                  >
                    <Row>
                      <Col span={24}>
                        <Chart
                          options={{
                            chart: {
                              type: "bar",
                              toolbar: {
                                show: true,
                                offsetX: 0,
                                offsetY: 0,
                                tools: {
                                  download: true,
                                  selection: false,
                                  zoom: false,
                                  zoomin: false,
                                  zoomout: false,
                                  pan: false,
                                  reset: false,
                                },
                              },
                              events: {
                                mouseMove: function (event) {
                                  event.target.style.cursor = "pointer";
                                },
                              },
                            },
                            plotOptions: {
                              bar: {
                                horizontal: false,
                                columnWidth: "15%",
                                // endingShape: "rounded",
                              },
                            },
                            dataLabels: {
                              enabled: true,
                            },
                            stroke: {
                              show: true,
                              width: 2,
                              colors: ["transparent"],
                            },
                            // stroke: {
                            //   curve: "smooth",
                            // },
                            noData: {
                              text: noAnswerYetText,
                              align: "center",
                              verticalAlign: "middle",
                              style: {
                                fontSize: "16px",
                              },
                            },
                            xaxis: {
                              type: "date",
                              tickPlacement: "between",
                              labels: {
                                show: true,
                                rotate: -45,
                                // rotateAlways: true,
                                formatter: (value) => {
                                  return new Date(value).toLocaleDateString(
                                    "en-AU"
                                  );
                                },
                              },
                            },
                            yaxis: {
                              categories: "numeric",
                              min: 0,

                              decimalsInFloat: 0,
                              title: {
                                text: answerNumberText,
                                style: {
                                  fontSize: 14,
                                  fontWeight: 600,
                                  fontFamily: "Arial",
                                },
                              },
                            },
                            fill: {
                              type: "solid",
                              opacity: 1,
                            },

                            legend: {
                              position: "bottom",
                              itemMargin: {
                                horizontal: 8,
                                vertical: 8,
                              },
                            },
                            colors: config.COLOR_CHART,
                            responsive: [
                              {
                                breakpoint: 480,
                                options: {
                                  legend: {
                                    position: "bottom",
                                    offsetX: -10,
                                    offsetY: 0,
                                  },
                                },
                              },
                            ],
                          }}
                          series={seriesLine}
                          type="bar"
                          width={"100%"}
                          height={300}
                        />
                      </Col>
                    </Row>
                  </Card>
                </Col>
                <Col md={24} lg={12} style={{ paddingTop: 24 }}>
                  <Card
                    bodyStyle={{ height: 350 }}
                    title={
                      <span style={{ fontSize: 20, fontWeight: "bold" }}>
                        <FormattedMessage {...messages.surveyAnswerStatistic} />
                      </span>
                    }
                  >
                    <Chart
                      options={{
                        chart: {
                          type: "donut",
                          toolbar: {
                            show: true,
                          },
                          events: {
                            mouseMove: function (event) {
                              event.target.style.cursor = "pointer";
                            },
                          },
                        },
                        responsive: [
                          {
                            breakpoint: 480,
                            options: {
                              chart: {
                                width: 200,
                              },
                              legend: {
                                position: "bottom",
                              },
                            },
                          },
                          {
                            breakpoint: 991,
                            options: {
                              chart: {
                                width: 500,
                                height: 500,
                              },
                              legend: {
                                position: "right",
                              },
                            },
                          },
                          {
                            breakpoint: 1300,
                            options: {
                              chart: {
                                width: 400,
                                height: 400,
                              },
                              legend: {
                                position: "right",
                              },
                            },
                          },
                        ],
                        plotOptions: {
                          pie: {
                            donut: {
                              labels: {
                                show: true,
                                name: {
                                  show: true,
                                  fontSize: "18px",
                                  fontWeight: 600,
                                },
                                value: {
                                  show: true,
                                  fontSize: "18px",
                                  fontWeight: 400,
                                },
                                total: {
                                  show: true,
                                  label: totalLabel,
                                  fontSize: "16px",
                                  fontWeight: 600,
                                  formatter: function (w) {
                                    if (
                                      w.globals.seriesTotals.length == 1 &&
                                      w.globals.seriesTotals[0] == 0.5
                                    ) {
                                      return 0;
                                    }
                                    return w.globals.seriesTotals.reduce(
                                      (a, b) => {
                                        return a + b;
                                      },
                                      0
                                    );
                                  },
                                },
                              },
                            },
                          },
                        },
                        tooltip: {
                          y: {
                            formatter: function (value) {
                              if (value == 0.5) {
                                return 0;
                              }
                              return value;
                            },
                          },
                        },
                        labels: [agreeText, disagreeText, noAnswerYetText],
                        legend: {
                          position: "right",
                          width: 150,
                          offsetY: 40,
                          itemMargin: {
                            horizontal: 0,
                            vertical: 8,
                          },
                        },
                        fill: {
                          opacity: 1,
                        },
                        colors: [GLOBAL_COLOR, "#F00000", "#F5A362"],
                      }}
                      series={seriesData}
                      type="donut"
                      width={"100%"}
                      height={250}
                    />
                  </Card>
                </Col>
                <Col span={24} style={{ marginTop: 24, minHeight: 400 }}>
                  <span
                    style={{
                      fontWeight: "bold",
                      fontSize: 18,
                      color: "black",
                    }}
                  >
                    {surveyAnswer.data.length}{" "}
                    <FormattedMessage {...messages.resident} />
                  </span>
                  <br />
                  <br />
                  <Row gutter={[16, 16]}>
                    <Col {...colLayout}>
                      <Input.Search
                        value={apartmentSearchSurvey}
                        placeholder={apartmentText}
                        onChange={(e) => {
                          this.setState({
                            apartmentSearchSurvey: e.target.value,
                          });
                        }}
                        maxLength={255}
                      />
                    </Col>
                    <Col {...colLayout}>
                      <Input.Search
                        value={residentSearchSurvey}
                        placeholder={residentText}
                        onChange={(e) => {
                          this.setState({
                            residentSearchSurvey: e.target.value,
                          });
                        }}
                        maxLength={255}
                      />
                    </Col>
                    <Col {...colLayout}>
                      <Select
                        style={{ width: "100%" }}
                        placeholder={readStatusText}
                        onChange={(value) => {
                          this.setState({
                            statusSearchSurvey: value,
                          });
                        }}
                        allowClear
                      >
                        <Select.Option value={1}>
                          <FormattedMessage {...messages.read} />
                        </Select.Option>
                        <Select.Option value={0}>
                          <FormattedMessage {...messages.notRead} />
                        </Select.Option>
                      </Select>
                    </Col>
                    <Col {...colLayout}>
                      <Select
                        style={{ width: "100%" }}
                        placeholder={resultText}
                        onChange={(value) => {
                          this.setState({
                            resultSearch: value,
                          });
                        }}
                        allowClear
                      >
                        <Select.Option value={1}>
                          <FormattedMessage {...messages.agree} />
                        </Select.Option>
                        <Select.Option value={2}>
                          <FormattedMessage {...messages.disagree} />
                        </Select.Option>
                        <Select.Option value={0}>
                          <FormattedMessage {...messages.notAnswer} />
                        </Select.Option>
                      </Select>
                    </Col>
                    <Col span={2}>
                      <Button
                        type="primary"
                        onClick={() => {
                          this.setState({
                            dataSourceSurvey:
                              notificationDetail.surveyAnswer.data.filter(
                                (item) =>
                                  removeAccents(
                                    item.apartment.name || ""
                                  ).includes(
                                    removeAccents(apartmentSearchSurvey)
                                  ) &&
                                  removeAccents(
                                    (item.resident_user || { first_name: "" })
                                      .first_name || ""
                                  ).includes(
                                    removeAccents(residentSearchSurvey)
                                  ) &&
                                  (resultSearch === undefined
                                    ? true
                                    : item.status === resultSearch) &&
                                  (statusSearchSurvey === undefined
                                    ? true
                                    : statusSearchSurvey
                                    ? item.status !== 0
                                    : item.status === 0)
                              ),
                          });
                        }}
                      >
                        <FormattedMessage {...messages.search} />
                      </Button>
                    </Col>
                    <Tooltip title={"Export"}>
                      <Button
                        style={{ position: "absolute", right: 10 }}
                        onClick={() => {
                          this.setState(
                            {
                              exporting: true,
                            },
                            () => {
                              window.connection
                                .exportNotificationDetail({
                                  announcement_campaign_type:
                                    detail.is_survey === 1 ? 2 : detail.type,
                                  announcement_campaign_id: detail.id,
                                })
                                .then((res) => {
                                  if (this._unmounted) return;
                                  this.setState(
                                    {
                                      exporting: false,
                                    },
                                    () => {
                                      if (res.success) {
                                        window.open(
                                          getFullLinkImage(res.data.file_path)
                                        );
                                      }
                                    }
                                  );
                                })
                                .catch(() => {
                                  if (this._unmounted) return;
                                  this.setState({
                                    exporting: false,
                                  });
                                });
                            }
                          );
                        }}
                        loading={this.state.exporting}
                        shape="circle"
                        size="large"
                      >
                        <i
                          className="material-icons"
                          style={{
                            fontSize: 14,
                            display: "flex",
                            justifyContent: "center",
                            fontWeight: "bold",
                          }}
                        >
                          login
                        </i>
                      </Button>
                    </Tooltip>
                  </Row>
                  <br />
                  <Table
                    rowKey={"id"}
                    bordered
                    locale={{
                      emptyText: <FormattedMessage {...messages.noData} />,
                    }}
                    columns={surveyColumns}
                    dataSource={dataSourceSurvey}
                    loading={surveyAnswer.loading}
                    scroll={{ x: 900 }}
                    pagination={{
                      pageSize: 10,
                      total: surveyAnswer.pagination.totalCount,
                      current: this.state.surveyCurrent,
                      showTotal: (total) => (
                        <FormattedMessage
                          {...messages.totals}
                          values={{ total }}
                        />
                      ),
                    }}
                    onChange={this.handleSurveyTableChange}
                  />
                </Col>
              </>
            )}
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

  handleSurveyTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, surveyCurrent: pagination.current }, () => {
      this.props.dispatch(
        fetchSurveyAnswer({
          page: pagination.current,
          pageSize: 10,
          announcement_campaign_id: this.props.match.params.id,
        })
      );
    });
  };
}

NotificationDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notificationDetail: makeSelectNotificationDetail(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "notificationDetail", reducer });
const withSaga = injectSaga({ key: "notificationDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(NotificationDetail));
