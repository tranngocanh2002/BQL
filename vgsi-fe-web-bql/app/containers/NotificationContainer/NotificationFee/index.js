/**
 *
 * NotificationFee
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
  Modal,
  Radio,
  Row,
  Select,
  Table,
  Tooltip,
  TreeSelect,
} from "antd";
import DraftEditor from "components/Editor/Editor";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { ContentState, EditorState, Modifier, convertToRaw } from "draft-js";
import draftToHtml from "draftjs-to-html";
import htmlToDraft from "html-to-draftjs";
import moment from "moment";
import queryString from "query-string";
import { FormattedMessage, injectIntl } from "react-intl";
import { Redirect } from "react-router";
import { selectBuildingCluster } from "redux/selectors";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { formatPrice, notificationBar, parseTree } from "../../../utils";
import { CUSTOM_TOOLBAR } from "../../../utils/config";
import { GLOBAL_COLOR, removeAccents } from "../../../utils/constants";
import messages, { scope } from "../messages";
import {
  createNotificationFeeReminder,
  defaultAction,
  fetchAllAnnouncementFeeTemplate,
  fetchAnnouncementFeeTemplate,
  fetchApartmentFeeReminder,
  fetchBuildingAreaAction,
  fetchCategory,
  fetchNotificationToPrint,
  showChooseTemplateList,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectNotificationFee from "./selectors";
import("./index.less");

const switchListReminder = (type) => {
  switch (type) {
    case 5:
      return 1;
    case 1:
      return 2;
    case 2:
      return 3;
    case 3:
      return 4;
    case 4:
      return 5;
    default:
      return 1;
  }
};

const formItemLayout = {
  labelCol: {
    xl: { span: 8 },
  },
  wrapperCol: {
    xl: { span: 16 },
  },
};

const colLayout = {
  md: 7,
  lg: 6,
  xl: 5,
};

class RowTree extends React.PureComponent {
  render() {
    const tProps = {
      treeData: this.props.treeData,
      treeCheckable: true,
      showCheckedStrategy: TreeSelect.SHOW_ALL,
      treeDefaultExpandAll: true,
      searchPlaceholder: this.props.intl.formatMessage({
        ...messages.rowTreeSelect,
      }),
      loading: true,
    };
    const { type } = this.props;
    return (
      <TreeSelect
        {...tProps}
        value={this.props.value}
        onChange={(value) => {
          this.props.selectBuildingArea(value);
          this.props.dispatch(
            fetchApartmentFeeReminder({
              type: switchListReminder(type),
              building_area_ids: value.toString(),
            })
          );

          this.props.onChange(value);
        }}
      />
    );
  }
}

const RowTreeSelect = injectIntl(RowTree);

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class NotificationFee extends React.PureComponent {
  constructor(props) {
    super(props);
    const { record } = props.location.state || {};
    let params = queryString.parse(this.props.location.search);
    let type = 5;
    if (params.type) {
      type = parseInt(params.type);
    }
    this.state = {
      record,
      title: "",
      editorState: EditorState.createEmpty(),
      treeData: parseTree(
        props.buildingCluster.data,
        props.notificationFee.buildingArea.lst.map((node) => ({
          key: `${node.id}`,
          title: node.name,
          value: `${node.id}`,
          ...node,
          children: [],
        }))
      ),
      type,
      current: 1,
      prevType: 1,
      buildingAreaList: [],
      apartment_ids: [],
      selectedRow: [],
      apartment_not_send_ids: [],
      showChooseTemplate: false,
      dataSource: [],
      apartmentSearch: "",
      residentSearch: "",
      resident_user_phones: [],
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
    const { type } = this.state;
    this.props.dispatch(fetchBuildingAreaAction());
    this.props.dispatch(fetchAllAnnouncementFeeTemplate({ type: type }));
    this.props.dispatch(fetchCategory());
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.notificationFee.template.loading !=
        nextProps.notificationFee.template.loading &&
      !nextProps.notificationFee.template.loading
    ) {
      const { setFields } = this.props.form;
      if (nextProps.notificationFee.template.data) {
        let blockArray = htmlToDraft(
          nextProps.notificationFee.template.data.content_email
        );
        setFields({
          title: {
            value: nextProps.notificationFee.template.data.name,
          },
          title_en: {
            value: nextProps.notificationFee.template.data.name_en,
          },
          // content_sms: {
          //   value: nextProps.notificationFee.template.data.content_sms,
          // },
        });
        this.setState({
          editorState:
            !!blockArray && !!blockArray.contentBlocks
              ? EditorState.createWithContent(
                  ContentState.createFromBlockArray(blockArray.contentBlocks)
                )
              : EditorState.createEmpty(),
        });
      } else {
        setFields({
          title: {
            value: "",
          },
          title_en: {
            value: "",
          },
          // content_sms: {
          //   value: "",
          // },
        });
        this.setState({
          editorState: EditorState.createEmpty(),
        });
      }
    }

    if (
      this.props.notificationFee.buildingArea.loading !=
        nextProps.notificationFee.buildingArea.loading &&
      !nextProps.notificationFee.buildingArea.loading
    ) {
      this.setState({
        treeData: parseTree(
          this.props.buildingCluster.data,
          nextProps.notificationFee.buildingArea.lst.map((node) => ({
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
      this.props.notificationFee.apartmentReminder.loading !=
        nextProps.notificationFee.apartmentReminder.loading &&
      !nextProps.notificationFee.apartmentReminder.loading
    ) {
      if (
        nextProps.notificationFee.apartmentReminder.data.length ||
        nextProps.notificationFee.apartmentReminder.totalPage === 0
      ) {
        this.setState({
          apartment_ids: nextProps.notificationFee.apartmentReminder.data.map(
            (row) => {
              return row.apartment_id;
            }
          ),
          selectedRow: nextProps.notificationFee.apartmentReminder.data.map(
            (row) => {
              return row.id;
            }
          ),
          dataSource: nextProps.notificationFee.apartmentReminder.data,
        });
      }
    }
  }
  getApartmentIds = () => {
    // const { notificationFee } = this.props;
    const { apartment_ids, apartment_not_send_ids } = this.state;
    // if (
    //   apartment_ids.length === notificationFee.apartmentReminder.data.length
    // ) {
    //   return [];
    // }
    if (apartment_not_send_ids.length >= apartment_ids.length) {
      return apartment_ids;
    }
    return [];
  };

  getApartmentNotSendIds = () => {
    // const { notificationFee } = this.props;

    const { apartment_ids, apartment_not_send_ids } = this.state;
    // if (
    //   apartment_ids.length === notificationFee.apartmentReminder.data.length
    // ) {
    //   return [];
    // }
    if (apartment_ids.length >= apartment_not_send_ids.length) {
      return apartment_not_send_ids;
    }
    return [];
  };
  handleOk = (status, message) => {
    const { dispatch, form, notificationFee } = this.props;
    const { validateFieldsAndScroll, setFields } = form;

    let category = notificationFee.category.data.find((dd) => dd.type == 1);
    if (!category) {
      notificationBar(
        this.props.intl.formatMessage({ ...messages.noFeeAnnouncementDefault }),
        "warn"
      );
      return;
    }

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
          errors: [
            new Error(
              this.props.intl.formatMessage({ ...messages.contentRequired })
            ),
          ],
        },
      });
      isErrorContent = true;
    } else {
      setFields({
        content: {
          value: "111",
        },
      });
      isErrorContent = false;
    }

    validateFieldsAndScroll((errors, values) => {
      if (errors || isErrorContent) {
        return;
      }
      let pushType = {};
      values.pushType.forEach((key) => {
        pushType[key] = 1;
      });
      const formData = {
        // targets: [1],
        ...values,
        type: switchListReminder(this.state.type),
        announcement_category_id: category.id,
        content: draftToHtml(contentRaw),
        status: 1,
        attach: {
          fileImageList: [],
          fileList: [],
        },
        is_event: 0,
        // add_email_send: [],
        // content_sms: values.content_sms,
        apartment_ids: this.getApartmentIds(),
        apartment_not_send_ids: this.getApartmentNotSendIds(),
        ...pushType,
        apartment_send_ids: this.state.apartment_ids,
        resident_user_phones: this.state.resident_user_phones,
      };

      Modal.confirm({
        autoFocusButton: null,
        title: this.props.intl.formatMessage({ ...messages.confirm }),
        content: this.props.intl.formatMessage({
          ...messages.announcementFeeModalContent,
        }),
        okText: this.props.intl.formatMessage({ ...messages.continue }),
        cancelText: this.props.intl.formatMessage({ ...messages.cancel }),
        centered: true,
        onOk: () => {
          dispatch(createNotificationFeeReminder(formData));
        },
      });
    });
  };

  _previewNoti = (record) => {
    this.props.dispatch(
      fetchNotificationToPrint({
        apartment_id: record.apartment_id,
        campaign_type: this.state.type,
      })
    );
  };

  onSelectChange = (selectedRowKeys, selectedRows) => {
    const { notificationFee } = this.props;
    this.setState({
      apartment_ids: selectedRows.map((row) => {
        return row.apartment_id;
      }),
      apartment_not_send_ids: notificationFee.apartmentReminder.data
        .filter((row) => !selectedRowKeys.includes(row.id))
        .map((opt) => opt.apartment_id),
      resident_user_phones: notificationFee.apartmentReminder.data
        .filter((row) => !selectedRowKeys.includes(row.id))
        .map((opt) => opt.phone),
      selectedRow: selectedRows.map((row) => {
        return row.id;
      }),
    });
  };

  render() {
    const { notificationFee, dispatch, intl } = this.props;
    const { template, template_list } = notificationFee;
    const formatMessage = this.props.intl.formatMessage;
    const apartmentText = intl.formatMessage({
      ...messages.property,
    });
    const residentText = intl.formatMessage({
      ...messages.resident,
    });
    const exampleText = intl.formatMessage({ id: `${scope}.example` });
    const sendEmailText = intl.formatMessage({ id: `${scope}.sendEmail` });
    const sendAppText = intl.formatMessage({ id: `${scope}.sendApp` });
    // const sendSMSText = intl.formatMessage({ id: `${scope}.sendSMS` });
    let announcement_category = notificationFee.category.data.find(
      (dd) => dd.type == 1
    );
    if (notificationFee.sentSuccess) {
      return <Redirect to="/main/finance/notification-fee/list" />;
    }
    const { getFieldDecorator, getFieldsError, getFieldValue, setFieldsValue } =
      this.props.form;
    const apartmentSelection = {
      selectedRowKeys: this.state.selectedRow,
      onChange: this.onSelectChange,
    };
    const columns = [
      {
        title: <span>#</span>,
        width: 50,
        fixed: "left",
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              notificationFee.apartmentReminder.loading
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
        width: 200,
        fixed: "left",
        dataIndex: "apartment_name",
        key: "apartment_name",
      },
      {
        title: (
          <span style={{ marginLeft: 10 }}>
            <FormattedMessage {...messages.resident} />
          </span>
        ),
        width: 200,
        render: (text) => <span style={{ marginLeft: 10 }}>{text}</span>,
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        align: "left",
        width: 200,
        title: (
          <span>
            <FormattedMessage {...messages.address} />
          </span>
        ),
        dataIndex: "apartment_parent_path",
        key: "apartment_parent_path",
      },
      {
        align: "left",
        width: 160,
        title: (
          <span>
            <FormattedMessage {...messages.endTermDebt} />
          </span>
        ),
        dataIndex: "end_debt",
        key: "end_debt",
        render: (text) => {
          return <span>{text ? formatPrice(text) : 0} đ</span>;
        },
      },
      {
        align: "left",
        title: <span>Email</span>,
        dataIndex: "email",
        key: "email",
        render: (text) => {
          if (!text) {
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
          return (
            <span style={{ color: GLOBAL_COLOR }}>
              {!!text && text.length > 30
                ? text.substring(0, 30) + "..."
                : text}
            </span>
          );
        },
      },
      {
        align: "left",
        title: <span>App</span>,
        dataIndex: "app",
        key: "app",
        render: (text) => {
          if (!text) {
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
          return (
            <Tooltip title={<FormattedMessage {...messages.appInstalled} />}>
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
      // {
      //   align: "left",
      //   title: <span>SMS</span>,
      //   dataIndex: "resident_user_phone",
      //   key: "resident_user_phone",
      //   render: (text, record) => {
      //     if (!record.resident_user_phone) {
      //       return (
      //         <Tooltip title={<FormattedMessage {...messages.notConfig} />}>
      //           <i
      //             className="material-icons"
      //             style={{ color: "#E4E4E4", cursor: "pointer" }}
      //           >
      //             phone_disabled
      //           </i>
      //         </Tooltip>
      //       );
      //     }
      //     return `0${text.slice(-9)}`;
      //   },
      // },
      {
        title: (
          <span>
            <FormattedMessage {...messages.status} />
          </span>
        ),
        width: 180,
        align: "center",
        dataIndex: "status",
        key: "status",
        render: (text, record) => {
          return (
            <span
              className={"luci-status-success"}
              style={{ backgroundColor: record.status_color }}
            >
              {text === 0
                ? formatMessage(messages.noDebt)
                : text === -1
                ? formatMessage(messages.prepay)
                : text === 1
                ? formatMessage(messages.stillOwe)
                : text === 2
                ? formatMessage(messages.feeNotification)
                : text === 3
                ? formatMessage(messages.debtRemind1)
                : text === 4
                ? formatMessage(messages.debtRemind2)
                : formatMessage(messages.debtRemind3)}
            </span>
          );
        },
      },
    ];
    const {
      type,
      prevType,
      residentSearch,
      apartmentSearch,
      dataSource,
      treeData,
    } = this.state;
    const errorCurrent = getFieldsError(["content"]);
    return (
      <Page inner>
        <Row className="NotificationFee">
          <Col span={24}>
            <Row
              type="flex"
              justify="center"
              style={{ marginTop: 0, marginBottom: 48 }}
            >
              <Radio.Group
                value={type}
                defaultValue={type}
                onChange={(e) => {
                  this.setState({ type: e.target.value, current: 1 }, () => {
                    // this.props.dispatch(
                    //   fetchApartmentFeeReminder({
                    //     type: switchListReminder(e.target.value),
                    //   })
                    // );
                    this.props.history.push(
                      `/main/finance/notification-fee/add?${queryString.stringify(
                        {
                          type: e.target.value,
                        }
                      )}`
                    );
                    this.props.dispatch(
                      fetchAllAnnouncementFeeTemplate({ type: e.target.value })
                    );
                    this.props.dispatch(showChooseTemplateList(false));
                    setFieldsValue({
                      building_area_ids: [],
                    });
                    this.setState({
                      apartment_ids: [],
                      apartment_not_send_ids: [],
                      selectedRow: [],
                      dataSource: [],
                    });
                  });
                }}
                size="large"
                buttonStyle="solid"
                disabled={notificationFee.sending}
              >
                <Radio.Button value={5}>
                  <FormattedMessage {...messages.feeAnnouncement} />
                </Radio.Button>
                <Radio.Button value={1}>
                  <FormattedMessage {...messages.debtRemind1} />
                </Radio.Button>
                <Radio.Button value={2}>
                  <FormattedMessage {...messages.debtRemind2} />
                </Radio.Button>
                <Radio.Button value={3}>
                  <FormattedMessage {...messages.debtRemind3} />
                </Radio.Button>
                <Radio.Button value={4}>
                  <FormattedMessage {...messages.pauseService} />
                </Radio.Button>
              </Radio.Group>
            </Row>
            <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
              <Col
                md={24}
                lg={12}
                style={{ borderRight: "1px solid #D9D9D9", paddingRight: 16 }}
              >
                <Row type="flex" justify="space-between">
                  <span
                    style={{ fontWeight: "bold", fontSize: 18, color: "black" }}
                  >
                    <FormattedMessage {...messages.contentSend} />
                  </span>
                  <Select
                    loading={template_list.loading || template.loading}
                    value={template.data ? template.data.id : undefined}
                    style={{
                      minWidth: 250,
                    }}
                  >
                    {template_list.data.map((item) => (
                      <Select.Option
                        key={item.id}
                        value={item.id}
                        onClick={() => {
                          dispatch(
                            fetchAnnouncementFeeTemplate({ id: item.id })
                          );
                        }}
                      >
                        {this.props.language === "en"
                          ? item.name_en
                          : item.name}
                      </Select.Option>
                    ))}
                  </Select>
                </Row>
                <br />
                <br />
                <Form labelAlign="left" {...formItemLayout}>
                  <Form.Item label={<FormattedMessage {...messages.title} />}>
                    {getFieldDecorator("title", {
                      initialValue: (
                        <FormattedMessage {...messages.feeAnnouncement} />
                      ),
                      rules: [
                        {
                          required: true,
                          message: (
                            <FormattedMessage {...messages.titleRequired} />
                          ),
                          whitespace: true,
                        },
                      ],
                    })(<Input style={{ width: "100%" }} maxLength={255} />)}
                  </Form.Item>
                  <Form.Item label={<FormattedMessage {...messages.titleEn} />}>
                    {getFieldDecorator("title_en", {
                      initialValue: "",
                      rules: [
                        {
                          required: true,
                          message: (
                            <FormattedMessage {...messages.titleEnRequired} />
                          ),
                          whitespace: true,
                        },
                      ],
                    })(<Input style={{ width: "100%" }} maxLength={255} />)}
                  </Form.Item>
                  <Form.Item label={<FormattedMessage {...messages.content} />}>
                    {getFieldDecorator("content", {
                      initialValue: "",
                      rules: [
                        {
                          required: true,
                          message: (
                            <FormattedMessage {...messages.contentRequired} />
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
                    <Tooltip title={`${exampleText}: Nguyễn Văn A`}>
                      <Button
                        onClick={() => {
                          const { editorState } = this.state;
                          const contentState = Modifier.replaceText(
                            editorState.getCurrentContent(),
                            editorState.getSelection(),
                            "{{RESIDENT_NAME}}",
                            editorState.getCurrentInlineStyle()
                          );
                          this.setState({
                            editorState: EditorState.push(
                              editorState,
                              contentState,
                              "insert-characters"
                            ),
                          });
                        }}
                      >
                        <FormattedMessage {...messages.ownerName} />
                      </Button>
                    </Tooltip>
                    <Tooltip title={`${exampleText}: TSQ.T1007`}>
                      <Button
                        style={{ marginLeft: 10, marginRight: 10 }}
                        onClick={() => {
                          const { editorState } = this.state;
                          const contentState = Modifier.replaceText(
                            editorState.getCurrentContent(),
                            editorState.getSelection(),
                            "{{APARTMENT_NAME}}",
                            editorState.getCurrentInlineStyle()
                          );
                          this.setState({
                            editorState: EditorState.push(
                              editorState,
                              contentState,
                              "insert-characters"
                            ),
                          });
                        }}
                      >
                        <FormattedMessage {...messages.property} />
                      </Button>
                    </Tooltip>
                    <Tooltip title={`${exampleText}: 2.000.000 VNĐ`}>
                      <Button
                        style={{ marginRight: 10 }}
                        onClick={() => {
                          const { editorState } = this.state;
                          const contentState = Modifier.replaceText(
                            editorState.getCurrentContent(),
                            editorState.getSelection(),
                            "{{TOTAL_FEE}}",
                            editorState.getCurrentInlineStyle()
                          );
                          this.setState({
                            editorState: EditorState.push(
                              editorState,
                              contentState,
                              "insert-characters"
                            ),
                          });
                        }}
                      >
                        <FormattedMessage {...messages.totalFee} />
                      </Button>
                    </Tooltip>
                  </Form.Item>
                  <Row>
                    <Col md={12} xl={24}>
                      <Form.Item
                        label={<FormattedMessage {...messages.sendMethod} />}
                      >
                        {getFieldDecorator("pushType", {
                          initialValue: ["is_send_push"],
                          rules: [
                            {
                              required: true,
                              message: (
                                <FormattedMessage
                                  {...messages.sendMethodRequired}
                                />
                              ),
                            },
                          ],
                        })(
                          <Checkbox.Group>
                            <Checkbox
                              value={"is_send_email"}
                              style={{ marginTop: 8 }}
                            >
                              {sendEmailText}
                            </Checkbox>
                            <br />
                            <Checkbox
                              value="is_send_push"
                              disabled={true}
                              style={{ marginTop: 8 }}
                            >
                              <span style={{ color: "rgba(0, 0, 0, 0.65)" }}>
                                {sendAppText}
                              </span>
                            </Checkbox>
                            <br />
                            {/* <Checkbox
                              value="is_send_sms"
                              style={{ marginTop: 8 }}
                            >
                              {sendSMSText}
                            </Checkbox>
                            <br /> */}
                          </Checkbox.Group>
                        )}
                      </Form.Item>
                    </Col>
                    <Col md={12} xl={24}>
                      <Form.Item
                        label={<FormattedMessage {...messages.sendTarget} />}
                      >
                        <Row>
                          <Col span={12}>
                            {getFieldDecorator("targets", {
                              initialValue: [1],
                              rules: [
                                {
                                  required: true,
                                  message: (
                                    <FormattedMessage
                                      {...messages.sendTargetRequired}
                                    />
                                  ),
                                },
                              ],
                            })(
                              <Checkbox.Group
                                onChange={(e) => {
                                  if (
                                    getFieldValue("building_area_ids").length
                                  ) {
                                    this.props.dispatch(
                                      fetchApartmentFeeReminder({
                                        type: switchListReminder(type),
                                        building_area_ids:
                                          this.state.buildingAreaList.toString() ||
                                          getFieldValue(
                                            "building_area_ids"
                                          ).toString(),
                                        targets: e.toString(),
                                        page: 1,
                                        pageSize: 200000,
                                      })
                                    );
                                  }
                                }}
                              >
                                <Checkbox
                                  disabled={true}
                                  value={1}
                                  style={{ marginTop: 8 }}
                                >
                                  <span
                                    style={{ color: "rgba(0, 0, 0, 0.65)" }}
                                  >
                                    <FormattedMessage
                                      {...messages.ownerDefault}
                                    />
                                  </span>
                                </Checkbox>
                                <br />
                                <Checkbox value={2} style={{ marginTop: 8 }}>
                                  <FormattedMessage {...messages.guest} />
                                </Checkbox>
                                <br />
                                <Checkbox value={0} style={{ marginTop: 8 }}>
                                  <FormattedMessage {...messages.ownerFamily} />
                                </Checkbox>
                                <br />
                                <Checkbox value={3} style={{ marginTop: 8 }}>
                                  <FormattedMessage {...messages.guestFamily} />
                                </Checkbox>
                                <br />
                              </Checkbox.Group>
                            )}
                          </Col>
                          <Col span={12}>
                            <Checkbox
                              checked={
                                getFieldValue("targets").length === 4 &&
                                getFieldValue("targets").includes(1)
                              }
                              onChange={(value) => {
                                if (value.target.checked) {
                                  setFieldsValue({ targets: [0, 1, 2, 3] });
                                } else {
                                  setFieldsValue({ targets: [1] });
                                }
                                if (getFieldValue("building_area_ids").length) {
                                  this.props.dispatch(
                                    fetchApartmentFeeReminder({
                                      building_area_ids:
                                        this.state.buildingAreaList.toString() ||
                                        getFieldValue(
                                          "building_area_ids"
                                        ).toString(),
                                      targets:
                                        getFieldValue("targets").toString(),
                                      type: switchListReminder(this.state.type),
                                      page: 1,
                                      pageSize: 200000,
                                    })
                                  );
                                }
                              }}
                            >
                              <FormattedMessage {...messages.all} />
                            </Checkbox>
                          </Col>
                        </Row>
                      </Form.Item>
                    </Col>
                  </Row>
                  <Form.Item label={<FormattedMessage {...messages.sendTo} />}>
                    {getFieldDecorator("building_area_ids", {
                      initialValue: [],
                      rules: [
                        {
                          required: true,
                          message: (
                            <FormattedMessage {...messages.sendToRequired} />
                          ),
                          type: "array",
                        },
                      ],
                    })(
                      <RowTreeSelect
                        treeData={treeData}
                        type={type}
                        dispatch={this.props.dispatch}
                        buildingArea={this.props.notificationFee.buildingArea}
                        selectBuildingArea={(ids) => {
                          this.setState({
                            buildingAreaList: ids,
                          });
                        }}
                      />
                    )}
                  </Form.Item>
                  {/* <Form.Item
                    label={<FormattedMessage {...messages.smsContent} />}
                    colon={false}
                  >
                    {getFieldDecorator("content_sms", {
                      initialValue: "",
                      rules: [
                        {
                          required: (getFieldValue("pushType") || []).some(
                            (ss) => ss == "is_send_sms"
                          ),
                          message: (
                            <FormattedMessage
                              {...messages.smsContentRequired}
                            />
                          ),
                        },
                      ],
                    })(<Input.TextArea rows={5} style={{ marginTop: 8 }} />)}
                  </Form.Item> */}
                </Form>
              </Col>
              <Col md={24} lg={12}>
                <Row>
                  <Col
                    md={{
                      span: 24,
                      offset: 0,
                    }}
                    lg={{
                      span: 22,
                      offset: 1,
                    }}
                  >
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
                            className="dangerouslySetInnerHTMLApp"
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
                    {prevType == 0 && (
                      <div className={"webPreview"}>
                        <div style={{ height: "100%", overflowY: "scroll" }}>
                          <div
                            className="dangerouslySetInnerHTMLWeb"
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
                            {(getFieldValue("content_sms") || "")
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
                <Row type="flex" justify="center" style={{ marginTop: 24 }}>
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
                    <Radio.Button value={1}>App</Radio.Button>
                    <Radio.Button value={0}>Email</Radio.Button>
                    {/* <Radio.Button value={2}>SMS</Radio.Button> */}
                  </Radio.Group>
                </Row>
              </Col>
            </Row>
          </Col>
          <Col span={24} style={{ paddingTop: 24 }}>
            <span style={{ fontWeight: "bold", fontSize: 18, color: "black" }}>
              <FormattedMessage {...messages.sendList} />
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
                  <FormattedMessage {...messages.totalResident} />
                </span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationFee.apartmentReminder.data.length || 0}
                </span>
              </Col>
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>Email</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {
                    // count the number of apartment has email
                    notificationFee.apartmentReminder.data.filter(
                      (item) => item.email
                    ).length || 0
                  }
                </span>
              </Col>
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>App</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationFee.apartmentReminder.data.filter(
                    (item) => item.app
                  ).length || 0}
                </span>
              </Col>
              {/* <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>SMS</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationFee.apartmentReminder.data.filter(
                    (item) => item.phone
                  ).length || 0}
                </span>
              </Col> */}
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
                <Button
                  type="primary"
                  onClick={() => {
                    this.setState({
                      dataSource: notificationFee.apartmentReminder.data.filter(
                        (item) =>
                          removeAccents(item.apartment_name || "").includes(
                            removeAccents(apartmentSearch)
                          ) &&
                          removeAccents(item.resident_user_name || "").includes(
                            removeAccents(residentSearch)
                          )
                      ),
                    });
                  }}
                >
                  <FormattedMessage {...messages.search} />
                </Button>
              </Col>
            </Row>
            <Table
              rowKey={"id"}
              bordered
              columns={columns}
              dataSource={dataSource}
              loading={notificationFee.apartmentReminder.loading}
              scroll={{ x: 1200 }}
              pagination={{
                pageSize: 10,
                total: notificationFee.apartmentReminder.data.length,
                current: this.state.current,
                showTotal: (total) => (
                  <FormattedMessage {...messages.total} values={{ total }} />
                ),
              }}
              onChange={this.handleTableChange}
              rowSelection={apartmentSelection}
            />
          </Col>
          <Col span={24} style={{ marginTop: 24 }}>
            <Row type="flex" align="middle" justify="center">
              <Button
                type="primary"
                size="large"
                disabled={
                  notificationFee.apartmentReminder.totalPage == 0 ||
                  !this.state.apartment_ids.length
                }
                onClick={() => {
                  this.handleOk();
                }}
                loading={notificationFee.sending}
              >
                <FormattedMessage {...messages.createFeeAnnouncement} />
              </Button>
            </Row>
          </Col>
        </Row>
      </Page>
    );
  }
  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current });
  };
}

NotificationFee.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notificationFee: makeSelectNotificationFee(),
  language: makeSelectLocale(),
  buildingCluster: selectBuildingCluster(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "notificationFee", reducer });
const withSaga = injectSaga({ key: "notificationFee", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(NotificationFee));
