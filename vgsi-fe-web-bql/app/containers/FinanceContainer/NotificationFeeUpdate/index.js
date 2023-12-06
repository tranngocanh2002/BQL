/**
 *
 * NotificationFeeUpdate
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
  DatePicker,
  Form,
  Icon,
  Input,
  Modal,
  Radio,
  Row,
  Select,
  Steps,
  Table,
  Tooltip,
  TreeSelect,
} from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import reducer from "./reducer";
import saga from "./saga";

import { parseTree } from "../../../utils";
import {
  defaultAction,
  fetchApartmentSent,
  fetchBuildingAreaAction,
  fetchCategory,
  fetchDetailAnnouncement,
  updateNotificationAction,
} from "./actions";

import Exception from "ant-design-pro/lib/Exception";

import { ContentState, EditorState, convertToRaw } from "draft-js";
import draftToHtml from "draftjs-to-html";
import moment from "moment";
import { Redirect } from "react-router";

import htmlToDraft from "html-to-draftjs";
import _ from "lodash";
import DraftEditor from "components/Editor/Editor";
import { injectIntl } from "react-intl";
import Upload from "../../../components/Uploader";
import { selectBuildingCluster } from "../../../redux/selectors";
import { CUSTOM_TOOLBAR } from "../../../utils/config";
import { COMPANY_NAME, GLOBAL_COLOR } from "../../../utils/constants";
import messages from "./messages";
import makeSelectNotificationFeeUpdate from "./selectors";
import("./index.less");

const formItemLayout = {
  labelCol: {
    xl: { span: 6 },
  },
  wrapperCol: {
    xl: { span: 18 },
  },
};

class RowTreeSelect extends React.PureComponent {
  render() {
    const tProps = {
      treeData: this.props.treeData,
      treeCheckable: true,
      showCheckedStrategy: TreeSelect.SHOW_PARENT,
      treeDefaultExpandAll: true,
      searchPlaceholder: this.props.intl.formatMessage(
        messages.selectListToSend
      ),
      loading: true,
    };
    return (
      <TreeSelect
        {...tProps}
        disabled={this.props.disabled}
        value={this.props.value}
        onChange={(value) => {
          let ids = [];
          if (value.length == 1 && value[0] == -1) {
            ids = this.props.buildingArea.lst
              .filter((ddd) => !ddd.parent_id)
              .map((ddd) => {
                return this.props.buildingArea.lst
                  .filter((dd) => dd.parent_id == ddd.id)
                  .map((aaa) => aaa.id);
              });
            ids = _.flatten(ids);
          } else {
            ids = value.map((ddd) => {
              let lll = this.props.buildingArea.lst.filter(
                (dd) => dd.parent_id == ddd
              );
              if (lll.length == 0) {
                return [ddd];
              }
              return lll.map((aaa) => aaa.id);
            });
            ids = _.flatten(ids);
          }
          this.props.selectBuildingArea(ids);
          if (ids.length > 0) {
            this.props.dispatch(
              fetchApartmentSent({
                building_area_ids: ids.toString(),
                page: 1,
                pageSize: 10,
              })
            );
          }
          this.props.onChange(value);
        }}
      />
    );
  }
}

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class NotificationFeeUpdate extends React.PureComponent {
  constructor(props) {
    super(props);
    const { record } = props.location.state || {};

    this.state = {
      record,
      editorState: EditorState.createEmpty(),
      treeData: parseTree(
        props.buildingCluster.data,
        props.notificationUpdate.buildingArea.lst.map((node) => ({
          key: `${node.id}`,
          title: node.name,
          value: `${node.id}`,
          ...node,
          children: [],
        }))
      ),
      type: 1,
      current: 1,
      prevType: 0,
      pushType: [],
      fileImageList: [],
      fileList: [],
      is_event: false,
      is_send_at: 0,
      currentTimeSent: moment(),
      buildingAreaList: [],
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
    this.props.dispatch(fetchDetailAnnouncement(this.props.match.params.id));
    this.props.dispatch(fetchCategory());
    this.props.dispatch(fetchBuildingAreaAction());
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.notificationUpdate.buildingArea.loading !=
        nextProps.notificationUpdate.buildingArea.loading &&
      !nextProps.notificationUpdate.buildingArea.loading
    ) {
      this.setState({
        treeData: parseTree(
          this.props.buildingCluster.data,
          nextProps.notificationUpdate.buildingArea.lst.map((node) => ({
            key: `${node.id}`,
            title: node.name,
            value: `${node.id}`,
            ...node,
            children: [],
          }))
        ),
      });
    }
    if (
      this.props.notificationUpdate.loading !=
        nextProps.notificationUpdate.loading &&
      !nextProps.notificationUpdate.loading &&
      !!nextProps.notificationUpdate.detail
    ) {
      const { setFields } = this.props.form;
      let blockArray = htmlToDraft(nextProps.notificationUpdate.detail.content);
      setFields({
        content_sms: {
          value: nextProps.notificationUpdate.detail.content_sms,
        },
      });
      this.setState({
        editorState:
          !!blockArray && !!blockArray.contentBlocks
            ? EditorState.createWithContent(
                ContentState.createFromBlockArray(blockArray.contentBlocks)
              )
            : EditorState.createEmpty(),
        fileImageList:
          !!nextProps.notificationUpdate.detail.attach &&
          !!nextProps.notificationUpdate.detail.attach.fileImageList
            ? nextProps.notificationUpdate.detail.attach.fileImageList
            : [],
        fileList:
          !!nextProps.notificationUpdate.detail.attach &&
          !!nextProps.notificationUpdate.detail.attach.fileList
            ? nextProps.notificationUpdate.detail.attach.fileList
            : [],
        is_event: nextProps.notificationUpdate.detail.is_event == 1,
        is_send_at: nextProps.notificationUpdate.detail.send_at ? 1 : 0,
        currentTimeSent: nextProps.notificationUpdate.detail.send_at
          ? moment.unix(nextProps.notificationUpdate.detail.send_at)
          : moment(),
      });
    }
  }

  handleOk = (status, message) => {
    const { dispatch, form, notificationUpdate } = this.props;
    const { validateFieldsAndScroll, setFields } = form;

    let contentRaw = convertToRaw(this.state.editorState.getCurrentContent());
    let isErrorContent = false;
    if (
      !contentRaw ||
      !contentRaw.blocks ||
      !contentRaw.blocks.some(
        (block) => block.text.replace(/ /g, "").length != 0
      )
    ) {
      setFields({
        content: {
          value: "",
        },
      });
      isErrorContent = true;
    } else {
      setFields({
        content: {
          value: "111",
          errors: [],
        },
      });
      isErrorContent = false;
    }

    validateFieldsAndScroll((errors, values) => {
      // if (!isErrorContent) {
      //   if (_.sum(contentRaw.blocks.map(bl => bl.text.length)) > 2000) {
      //     setFields({
      //       content: {
      //         value: '111',
      //         errors: [new Error('Nội dung không được dài quá 2000 ký tự.')]
      //       }
      //     })
      //     isErrorContent = true
      //   }
      // }
      if (errors || isErrorContent) {
        return;
      }
      if (status == 1) {
        Modal.confirm({
          autoFocusButton: null,
          title: this.props.intl.formatMessage(messages.confirm),
          content: this.props.intl.formatMessage(messages.contentNotice),
          okText: this.props.intl.formatMessage(messages.continue),
          cancelText: this.props.intl.formatMessage(messages.cancel),
          centered: true,
          onOk: () => {
            let pushType = {};
            values.pushType.forEach((key) => {
              pushType[key] = 1;
            });
            dispatch(
              updateNotificationAction({
                ...values,
                type: 0,
                id: this.props.notificationUpdate.detail.id,
                content: draftToHtml(contentRaw),
                status,
                attach: {
                  fileImageList: this.state.fileImageList,
                  fileList: this.state.fileList,
                },
                is_event: 0,
                send_at: values.send_at ? values.send_at.unix() : undefined,
                send_event_at: values.send_event_at
                  ? values.send_event_at.unix()
                  : undefined,
                content_sms: values.content_sms,
                ...pushType,
                pushType: undefined,
                building_area_ids: this.state.buildingAreaList,
                message,
              })
            );
          },
        });
      } else {
        let pushType = {};
        values.pushType.forEach((key) => {
          pushType[key] = 1;
        });
        this.props.dispatch(
          updateNotificationAction({
            ...values,
            type: 0,
            id: this.props.notificationUpdate.detail.id,
            content: draftToHtml(contentRaw),
            status: Math.min(status, 1),
            attach: {
              fileImageList: this.state.fileImageList,
              fileList: this.state.fileList,
            },
            is_event: 0,
            send_at: values.send_at ? values.send_at.unix() : undefined,
            send_event_at: values.send_event_at
              ? values.send_event_at.unix()
              : undefined,
            content_sms: values.content_sms,
            ...pushType,
            pushType: undefined,
            building_area_ids: this.state.buildingAreaList,
            message,
          })
        );
      }
    });
  };

  render() {
    const { notificationUpdate } = this.props;

    const { category, detail, loading } = notificationUpdate;
    if (notificationUpdate.createSuccess) {
      return <Redirect to="/main/finance/notification-fee/list" />;
    }

    if (loading) {
      return <Page inner loading />;
    }
    if (!detail || detail.type != 0) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={this.props.intl.formatMessage(messages.notFoundPage)}
            actions={[]}
          />
        </Page>
      );
    }

    const { getFieldDecorator, getFieldsError, getFieldValue, setFields } =
      this.props.form;
    const columns = [
      {
        title: <span>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              notificationUpdate.apartmentToSend.loading
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
        title: <span>{this.props.intl.formatMessage(messages.property)}</span>,
        dataIndex: "apartment_name",
        key: "apartment_name",
      },
      {
        title: (
          <span>{this.props.intl.formatMessage(messages.householder)}</span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        align: "right",
        title: <span>{this.props.intl.formatMessage(messages.address)}</span>,
        dataIndex: "apartment_parent_path",
        key: "apartment_parent_path",
      },
      {
        align: "right",
        title: <span>Email</span>,
        dataIndex: "email",
        key: "email",
        render: (text, record) => {
          if (!record.email) {
            return (
              <Tooltip
                title={this.props.intl.formatMessage(messages.notConfiguration)}
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
          return text;
        },
      },
      {
        align: "right",
        title: <span>App</span>,
        dataIndex: "app",
        key: "app",
        render: (text, record) => {
          if (!record.app) {
            return (
              <Tooltip
                title={this.props.intl.formatMessage(messages.notInstallApp)}
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
          return (
            <Tooltip
              title={this.props.intl.formatMessage(messages.installedApp)}
            >
              <i
                className="material-icons"
                style={{ color: GLOBAL_COLOR, cursor: "pointer" }}
              >
                mobile_friendly
              </i>
            </Tooltip>
          );
        },
      },
      {
        align: "right",
        title: <span>SMS</span>,
        dataIndex: "phone",
        key: "phone",
        render: (text, record) => {
          if (!record.phone) {
            return (
              <Tooltip
                title={this.props.intl.formatMessage(messages.notConfiguration)}
              >
                <i
                  className="material-icons"
                  style={{ color: "#E4E4E4", cursor: "pointer" }}
                >
                  phone_disabled
                </i>
              </Tooltip>
            );
          }
          return `0${text.slice(-9)}`;
        },
      },
    ];

    const { editorState, treeData, prevType } = this.state;
    const errorCurrent = getFieldsError(["content"]);

    let announcement_category = category.data.find((cc) => cc.type == 1);
    let content_sms = getFieldValue("content_sms") || "";
    return (
      <Page inner>
        <Row className="NotificationUpdate">
          <Col span={24}>
            <Row
              type="flex"
              justify="center"
              style={{ marginTop: 24, marginBottom: 24 }}
            >
              <Col span={12}>
                <Steps size="default" current={detail.status + 1}>
                  <Steps.Step
                    title={this.props.intl.formatMessage(messages.create)}
                  />
                  <Steps.Step
                    title={this.props.intl.formatMessage(messages.draft)}
                  />
                  <Steps.Step
                    title={this.props.intl.formatMessage(messages.public)}
                  />
                </Steps>
              </Col>
            </Row>
            <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
              <Col span={12} style={{ paddingRight: 16 }}>
                <span
                  style={{ fontWeight: "bold", fontSize: 18, color: "black" }}
                >
                  {this.props.intl.formatMessage(messages.contentSend)}
                </span>
                <br />
                <br />
                <Form {...formItemLayout}>
                  <Form.Item
                    label={this.props.intl.formatMessage(messages.title)}
                  >
                    {getFieldDecorator("title", {
                      initialValue: detail.title,
                      rules: [
                        {
                          required: true,
                          message: this.props.intl.formatMessage(
                            messages.emptyTitle
                          ),
                          whitespace: true,
                        },
                      ],
                    })(<Input style={{ width: "100%" }} maxLength={150} />)}
                  </Form.Item>
                  <Form.Item
                    label={this.props.intl.formatMessage(messages.content)}
                  >
                    {getFieldDecorator("content", {
                      initialValue: "",
                      rules: [
                        {
                          required: true,
                          message: this.props.intl.formatMessage(
                            messages.emptyContent
                          ),
                        },
                      ],
                    })(
                      <div
                        style={{
                          border: errorCurrent.content ? "1px solid red" : "",
                        }}
                      >
                        <DraftEditor
                          editorState={this.state.editorState}
                          wrapperClassName="demo-wrapper"
                          editorClassName="rdw-storybook-editor"
                          onEditorStateChange={this.onEditorStateChange}
                          toolbar={CUSTOM_TOOLBAR}
                        />
                      </div>
                    )}
                  </Form.Item>
                  <Form.Item
                    label={this.props.intl.formatMessage(messages.category)}
                  >
                    {getFieldDecorator("announcement_category_id", {
                      initialValue: String(detail.announcement_category_id),
                      rules: [
                        {
                          required: true,
                          message: this.props.intl.formatMessage(
                            messages.emptyCategory
                          ),
                          whitespace: true,
                        },
                      ],
                    })(
                      <Select
                        loading={category.loading}
                        showSearch
                        placeholder={this.props.intl.formatMessage(
                          messages.selectNoticeCategory
                        )}
                        optionFilterProp="children"
                        // onChange={onChange}
                        filterOption={(input, option) =>
                          option.props.children
                            .toLowerCase()
                            .indexOf(input.toLowerCase()) >= 0
                        }
                      >
                        {category.data.map((gr) => {
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
                    label={this.props.intl.formatMessage(messages.public)}
                  >
                    <Row type="flex" align="middle">
                      <Select
                        disabled={detail.status == 1}
                        value={this.state.is_send_at}
                        style={{
                          width: "50%",
                          marginRight: "1%",
                          marginBottom: window.innerWidth > 1440 ? null : 8,
                        }}
                        onChange={(e) => {
                          this.setState({
                            is_send_at: e,
                          });
                        }}
                      >
                        {[
                          {
                            id: 0,
                            title: this.props.intl.formatMessage(
                              messages.publicNow
                            ),
                          },
                          {
                            id: 1,
                            title: this.props.intl.formatMessage(
                              messages.publicAt
                            ),
                          },
                        ].map((gr) => {
                          return (
                            <Select.Option key={`group-${gr.id}`} value={gr.id}>
                              {gr.title}
                            </Select.Option>
                          );
                        })}
                      </Select>
                      {this.state.is_send_at == 1 &&
                        getFieldDecorator("send_at", {
                          initialValue: this.state.currentTimeSent,
                          rules: [
                            {
                              required: true,
                              message: this.props.intl.formatMessage(
                                messages.ruleTimePublic
                              ),
                              type: "object",
                            },
                          ],
                        })(
                          <DatePicker
                            showTime
                            format="HH:mm - DD/MM/YYYY"
                            placeholder={this.props.intl.formatMessage(
                              messages.selectDate
                            )}
                            disabled={detail.status == 1}
                            style={{ width: "49%" }}
                            disabledDate={(current) => {
                              // Can not select days before today and today
                              return (
                                current && current < moment().startOf("day")
                              );
                            }}
                            disabledTime={(current) => {
                              if (!current) return {};
                              let now = moment();
                              if (current > moment().endOf("day")) {
                                return {};
                              }
                              return {
                                disabledHours: () => _.range(0, now.hour()),
                                disabledMinutes:
                                  current.hour() == now.hour()
                                    ? () => _.range(0, now.minute())
                                    : () => [],
                                disabledSeconds:
                                  current.hour() == now.hour() &&
                                  current.minute() == now.minute()
                                    ? () => _.range(0, now.second())
                                    : () => [],
                              };
                            }}
                          />
                        )}
                    </Row>
                  </Form.Item>
                  <Form.Item
                    label={
                      <span>
                        {this.props.intl.formatMessage(messages.event)}
                        <Tooltip
                          title={this.props.intl.formatMessage(
                            messages.tooltipEvent
                          )}
                        >
                          <Icon type="info-circle-o" />
                        </Tooltip>
                      </span>
                    }
                  >
                    <Checkbox
                      checked={this.state.is_event}
                      disabled={detail.status == 1}
                      onChange={(value) => {
                        this.setState({
                          is_event: value.target.checked,
                        });
                      }}
                      style={{ marginRight: 10 }}
                    />
                    {this.state.is_event &&
                      getFieldDecorator("send_event_at", {
                        // initialValue:  !!record.send_event_at ? moment.unix(record.send_event_at) : moment(),
                        rules: [
                          {
                            required: true,
                            message: this.props.intl.formatMessage(
                              messages.emptyTimeEvent
                            ),
                            type: "object",
                          },
                        ],
                      })(
                        <DatePicker
                          showTime
                          format="HH:mm - DD/MM/YYYY"
                          disabled={detail.status == 1}
                          disabledDate={(current) => {
                            // Can not select days before today and today
                            return current && current < moment().startOf("day");
                          }}
                          disabledTime={(current) => {
                            if (!current) return {};
                            let now = moment();
                            if (current > moment().endOf("day")) {
                              return {};
                            }
                            return {
                              disabledHours: () => _.range(0, now.hour()),
                              disabledMinutes:
                                current.hour() == now.hour()
                                  ? () => _.range(0, now.minute())
                                  : () => [],
                              disabledSeconds:
                                current.hour() == now.hour() &&
                                current.minute() == now.minute()
                                  ? () => _.range(0, now.second())
                                  : () => [],
                            };
                          }}
                        />
                      )}
                  </Form.Item>
                  <Form.Item
                    label={this.props.intl.formatMessage(messages.imageAttach)}
                  >
                    <Upload
                      disabled={detail.status == 1}
                      listType="picture-card"
                      showUploadList={true}
                      fileList={this.state.fileImageList}
                      acceptList={["image/"]}
                      accept={"image/*"}
                      multiple
                      onRemove={(file) => {
                        this.setState({
                          fileImageList: this.state.fileImageList.filter(
                            (ff) => ff.uid != file.uid
                          ),
                        });
                      }}
                      onUploaded={(url, file) => {
                        this.setState({
                          fileImageList: this.state.fileImageList.concat([
                            {
                              uid: file.uid,
                              name: file.name,
                              status: "done",
                              url,
                            },
                          ]),
                        });
                      }}
                    >
                      <Icon type="plus" />
                    </Upload>
                  </Form.Item>
                  <Form.Item
                    label={
                      <span>
                        {this.props.intl.formatMessage(messages.fileAttach)}
                      </span>
                    }
                  >
                    <Upload
                      disabled={detail.status == 1}
                      showUploadList={true}
                      fileList={this.state.fileList}
                      acceptList={[
                        "doc",
                        "docx",
                        "pdf",
                        "application/pdf",
                        "xls",
                        "xlsx",
                        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                        "application/vnd.ms-excel",
                      ]}
                      multiple
                      onRemove={(file) => {
                        this.setState({
                          fileList: this.state.fileList.filter(
                            (ff) => ff.uid != file.uid
                          ),
                        });
                      }}
                      onUploaded={(url, file) => {
                        this.setState({
                          fileList: this.state.fileList.concat([
                            {
                              uid: file.uid,
                              name: file.name,
                              status: "done",
                              url,
                            },
                          ]),
                        });
                      }}
                    >
                      <Button>
                        <Icon type="upload" />{" "}
                        {this.props.intl.formatMessage(messages.downloadFile)}
                      </Button>
                      <span style={{ marginLeft: 8 }}>
                        ({this.props.intl.formatMessage(messages.ruleFile)})
                      </span>
                    </Upload>
                  </Form.Item>
                  <Form.Item
                    label={this.props.intl.formatMessage(messages.sendTo)}
                  >
                    {getFieldDecorator("building_area_ids", {
                      initialValue: detail.building_area_ids,
                      rules: [
                        {
                          required: true,
                          message: this.props.intl.formatMessage(
                            messages.emptySendTo
                          ),
                          type: "array",
                        },
                      ],
                    })(
                      <RowTreeSelect
                        disabled={detail.status == 1}
                        treeData={treeData}
                        dispatch={this.props.dispatch}
                        buildingArea={
                          this.props.notificationUpdate.buildingArea
                        }
                        selectBuildingArea={(ids) => {
                          this.setState({
                            buildingAreaList: ids,
                          });
                        }}
                      />
                    )}
                  </Form.Item>
                </Form>
              </Col>
              <Col span={12}>
                <Row
                  style={{
                    height: "70%",
                    paddingLeft: 36,
                    paddingRight: 36,
                  }}
                  type="flex"
                  align="middle"
                  justify="center"
                >
                  {prevType == 0 && (
                    <div className={"webPreview"}>
                      <div style={{ height: "100%", overflowY: "scroll" }}>
                        <div
                          dangerouslySetInnerHTML={{
                            __html: draftToHtml(
                              convertToRaw(
                                this.state.editorState.getCurrentContent()
                              )
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
                          height: "100%",
                          overflowY: "scroll",
                          backgroundColor: "white",
                          paddingLeft: 16,
                          paddingRight: 0,
                        }}
                      >
                        <br />
                        <strong style={{ color: "black", fontSize: 20 }}>
                          {getFieldValue("title")}
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
                          <span>{moment().format("HH:mm DD/MM/YYYY")}</span>
                          {!!announcement_category && (
                            <Row type="flex" align="middle">
                              <div
                                style={{
                                  width: 0,
                                  height: 0,
                                  borderTop: `12px solid ${announcement_category.label_color}`,
                                  borderBottom: `12px solid ${announcement_category.label_color}`,
                                  borderLeft: "12px solid transparent",
                                }}
                              />
                              <Row
                                style={{
                                  height: 24,
                                  backgroundColor:
                                    announcement_category.label_color,
                                  paddingLeft: 8,
                                  paddingRight: 8,
                                  color: "white",
                                }}
                                type="flex"
                                align="middle"
                                justify="center"
                              >
                                {announcement_category.name}
                              </Row>
                            </Row>
                          )}
                        </Row>
                        <div
                          style={{ marginRight: 16 }}
                          dangerouslySetInnerHTML={{
                            __html: draftToHtml(
                              convertToRaw(
                                this.state.editorState.getCurrentContent()
                              )
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
                  {prevType == 2 && (
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
                              {this.props.intl.formatMessage(messages.message)}
                            </span>
                          </Row>
                          <span
                            style={{ color: "#C4C4C4", textAlign: "right" }}
                          >
                            {this.props.intl.formatMessage(messages.justFn)}
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
                          {(getFieldValue("content_sms") || "")
                            .replace(/{{RESIDENT_NAME}}/g, "Nguyễn Văn A")
                            .replace(/{{APARTMENT_NAME}}/g, "TSQ.T1007")
                            .replace(/{{TOTAL_FEE}}/g, "2.000.000 VNĐ")
                            .replace(/{{PAYMENT_CODE}}/g, "9ATV0GV4")}
                        </Row>
                      </div>
                    </div>
                  )}
                </Row>
                <Row type="flex" justify="center" style={{ marginTop: 8 }}>
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
                    <Radio.Button value={0}>Email</Radio.Button>
                    <Radio.Button value={1}>App</Radio.Button>
                    <Radio.Button value={2}>SMS</Radio.Button>
                  </Radio.Group>
                </Row>
                <Row style={{ marginTop: 24, height: "30%" }}>
                  <Col>
                    <Form {...formItemLayout}>
                      <Form.Item
                        label={this.props.intl.formatMessage(messages.typeSend)}
                      >
                        {getFieldDecorator("pushType", {
                          initialValue: ["is_send_email", "is_send_push"],
                          rules: [
                            {
                              required: true,
                              message: this.props.intl.formatMessage(
                                messages.ruleType
                              ),
                            },
                          ],
                        })(
                          <Checkbox.Group>
                            <Checkbox
                              value={"is_send_email"}
                              style={{ marginTop: 8 }}
                              disabled={detail.status == 1}
                            >
                              {this.props.intl.formatMessage(
                                messages.sendViaEmail
                              )}
                            </Checkbox>
                            <br />
                            <Checkbox
                              value="is_send_push"
                              style={{ marginTop: 8, marginBottom: 8 }}
                              disabled={true}
                            >
                              <span style={{ color: "rgba(0, 0, 0, 0.65)" }}>
                                {this.props.intl.formatMessage(
                                  messages.sendViaApp
                                )}
                              </span>
                            </Checkbox>
                            <br />
                            <Checkbox
                              value="is_send_sms"
                              disabled={detail.status == 1}
                            >
                              {this.props.intl.formatMessage(
                                messages.sendViaSMS
                              )}
                            </Checkbox>
                            <br />
                          </Checkbox.Group>
                        )}
                      </Form.Item>
                      <Form.Item
                        label={this.props.intl.formatMessage(
                          messages.contentSMS
                        )}
                        colon={false}
                      >
                        {getFieldDecorator("content_sms", {
                          initialValue: "",
                          rules: [
                            {
                              required: (getFieldValue("pushType") || []).some(
                                (ss) => ss == "is_send_sms"
                              ),
                              message: this.props.intl.formatMessage(
                                messages.emptySMS
                              ),
                            },
                          ],
                        })(
                          <Input.TextArea
                            rows={5}
                            style={{ marginTop: 8, width: "82%" }}
                            disabled={detail.status == 1}
                          />
                        )}
                      </Form.Item>
                    </Form>
                  </Col>
                </Row>
              </Col>
            </Row>
          </Col>
          <Col span={24} style={{ paddingTop: 24 }}>
            <span style={{ fontWeight: "bold", fontSize: 18, color: "black" }}>
              {this.props.intl.formatMessage(messages.listSend)}
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
                  {this.props.intl.formatMessage(messages.totalPropertyCount)}
                </span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationUpdate.apartmentToSend.total_count
                    .total_apartment || 0}
                </span>
              </Col>
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>Email</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationUpdate.apartmentToSend.total_count.total_email ||
                    0}
                </span>
              </Col>
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>App</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationUpdate.apartmentToSend.total_count.total_app ||
                    0}
                </span>
              </Col>
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>SMS</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationUpdate.apartmentToSend.total_count.total_sms ||
                    0}
                </span>
              </Col>
            </Row>
            <br />
            <Table
              rowKey={"id"}
              columns={columns}
              dataSource={notificationUpdate.apartmentToSend.data}
              loading={notificationUpdate.apartmentToSend.loading}
              pagination={{
                pageSize: 10,
                total: notificationUpdate.apartmentToSend.totalPage,
                current: this.state.current,
                showTotal: (total, range) =>
                  this.props.intl.formatMessage(messages.totalProperty, {
                    total,
                  }),
              }}
              onChange={this.handleTableChange}
              bordered
            />
          </Col>
          {detail.status == 0 && (
            <Col span={24} style={{ marginTop: 24 }}>
              <Row type="flex" align="middle" justify="center">
                <Button
                  size="large"
                  style={{ marginRight: 8 }}
                  disabled={notificationUpdate.apartmentToSend.totalPage == 0}
                  onClick={(e) => {
                    this.handleOk(
                      0,
                      this.props.intl.formatMessage(messages.saveNoticeSuccess)
                    );
                  }}
                  loading={notificationUpdate.creating}
                >
                  {this.props.intl.formatMessage(messages.draft)}
                </Button>
                <Button
                  type="primary"
                  size="large"
                  style={{ marginLeft: 8 }}
                  disabled={notificationUpdate.apartmentToSend.totalPage == 0}
                  onClick={(e) => {
                    this.handleOk(
                      1,
                      this.props.intl.formatMessage(
                        messages.publicNoticeSuccess
                      )
                    );
                  }}
                  loading={notificationUpdate.creating}
                >
                  {this.props.intl.formatMessage(messages.public)}
                </Button>
              </Row>
            </Col>
          )}
          {detail.status == 1 && (
            <Col span={24} style={{ marginTop: 24 }}>
              <Row type="flex" align="middle" justify="center">
                <Button
                  type="primary"
                  size="large"
                  style={{ marginLeft: 8 }}
                  onClick={(e) => {
                    this.handleOk(
                      2,
                      this.props.intl.formatMessage(messages.saveNoticeSuccess)
                    );
                  }}
                  loading={notificationUpdate.creating}
                >
                  {this.props.intl.formatMessage(messages.save)}
                </Button>
              </Row>
            </Col>
          )}
        </Row>
      </Page>
    );
  }
  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.dispatch(
        fetchApartmentSent({
          type: this.state.type,
          page: pagination.current,
          pageSize: 10,
          building_area_ids:
            this.props.notificationUpdate.detail.building_area_ids,
        })
      );
    });
  };
}

NotificationFeeUpdate.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notificationUpdate: makeSelectNotificationFeeUpdate(),
  buildingCluster: selectBuildingCluster(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "notificationFeeUpdate", reducer });
const withSaga = injectSaga({ key: "notificationFeeUpdate", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(NotificationFeeUpdate));
