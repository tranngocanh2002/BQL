/**
 *
 * NotificationFee
 *
 */
//TODO: Sua cau hinh template Notification
import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Col,
  Form,
  Icon,
  Input,
  Modal,
  Radio,
  Row,
  Select,
  Tooltip,
} from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import reducer from "./reducer";
import saga from "./saga";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { ContentState, EditorState, Modifier, convertToRaw } from "draft-js";
import draftToHtml from "draftjs-to-html";
import htmlToDraft from "html-to-draftjs";
import moment from "moment";
import queryString from "query-string";
import DraftEditor from "components/Editor/Editor";
import { injectIntl } from "react-intl";
import UploaderSimple from "../../../components/UploaderSimple";
import { getFullLinkImage } from "../../../connection";
import { config } from "../../../utils";
import { CUSTOM_TOOLBAR } from "../../../utils/config";
import messages from "../messages";
import { ChooseNotiTemplate } from "./ChooseNotiTemplate";
import {
  chooseCreateTemplate,
  createAnnouncementFeeTemplate,
  defaultAction,
  deleteAnnouncementFeeTemplate,
  fetchAllAnnouncementFeeTemplate,
  fetchAnnouncementFeeTemplate,
  showChooseTemplateList,
  updateAnnouncementFeeTemplate,
} from "./actions";
import makeSelectNotificationFeeManager from "./selectors";
import("./index.less");
const formItemLayout = {
  labelCol: {
    xl: { span: 6 },
  },
  wrapperCol: {
    xl: { span: 18 },
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class NotificationFeeManager extends React.PureComponent {
  constructor(props) {
    super(props);
    const record = props.location.state || {};
    this.state = {
      name: "",
      editorState: EditorState.createEmpty(),
      type: record.type ? String(record.type) : "5",
      prevType: 1,
      imageUrl: null,
      isCreateTemplate: false,
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
    let search = this.props.location.search;
    let params = queryString.parse(search);
    if (params.id) {
      this.props.dispatch(
        showChooseTemplateList(
          !this.props.notificationFeeManager.showChooseTemplate
        )
      );
      this.props.dispatch(fetchAnnouncementFeeTemplate({ id: params.id }));
    } else {
      this.props.dispatch(fetchAllAnnouncementFeeTemplate({ type: type }));
    }
  }
  componentWillReceiveProps(nextProps) {
    if (
      this.props.notificationFeeManager.template.loading !=
        nextProps.notificationFeeManager.template.loading &&
      !nextProps.notificationFeeManager.template.loading &&
      nextProps.notificationFeeManager.chooseCreateTemplate !== true
    ) {
      const { setFields } = this.props.form;
      if (nextProps.notificationFeeManager.template.data) {
        let blockArray = htmlToDraft(
          nextProps.notificationFeeManager.template.data.content_email
        );
        setFields({
          name: {
            value: nextProps.notificationFeeManager.template.data.name,
          },
          name_en: {
            value: nextProps.notificationFeeManager.template.data.name_en,
          },
          // content_sms: {
          //   value: nextProps.notificationFeeManager.template.data.content_sms,
          // },
        });
        this.setState({
          imageUrl: nextProps.notificationFeeManager.template.data.image,
          editorState:
            !!blockArray && !!blockArray.contentBlocks
              ? EditorState.createWithContent(
                  ContentState.createFromBlockArray(blockArray.contentBlocks)
                )
              : EditorState.createEmpty(),
        });
      } else {
        setFields({
          name: {
            value: "",
          },
          name_en: {
            value: "",
          },
          // content_sms: {
          //   value: "",
          // },
        });
        this.setState({
          editorState: EditorState.createEmpty(),
          imageUrl: null,
        });
      }
    }
    if (
      this.props.notificationFeeManager.showChooseTemplate !=
        nextProps.notificationFeeManager.showChooseTemplate &&
      nextProps.notificationFeeManager.showChooseTemplate == true
    ) {
      this.props.dispatch(
        fetchAllAnnouncementFeeTemplate({
          type: this.state.type,
        })
      );
    }
    if (
      this.props.notificationFeeManager.chooseCreateTemplate !=
        nextProps.notificationFeeManager.chooseCreateTemplate &&
      nextProps.notificationFeeManager.chooseCreateTemplate == true
    ) {
      this.createTemplate();
    }
  }

  handleUpdateTemplate = (status, message) => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll, setFields } = form;
    let contentRaw = convertToRaw(this.state.editorState.getCurrentContent());
    let isErrorContent = false;
    const formatMessage = this.props.intl.formatMessage;
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
          errors: [new Error(formatMessage({ ...messages.contentRequired }))],
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

      Modal.confirm({
        autoFocusButton: null,
        title: formatMessage(messages.edit),
        content: formatMessage(messages.confirmUpdateNotification),
        okText: formatMessage(messages.continue),
        cancelText: formatMessage(messages.cancel),
        centered: true,
        onOk: () => {
          dispatch(
            updateAnnouncementFeeTemplate({
              id: this.props.notificationFeeManager.template.data.id,
              type: this.state.type,
              name: values.name,
              name_en: values.name_en,
              image: this.state.imageUrl ? this.state.imageUrl : "",
              content_email: draftToHtml(contentRaw),
              // content_sms: values.content_sms,
            })
          );
          setTimeout(() => {
            dispatch(showChooseTemplateList(true));
          }, 1000);
        },
      });
    });
  };

  handleDeleteTemplate = (id) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmDeleteNotification),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          deleteAnnouncementFeeTemplate({
            id,
            callback: () => {
              this.props.dispatch(
                fetchAllAnnouncementFeeTemplate({ type: this.state.type })
              );
            },
          })
        );
      },
      onCancel() {},
    });
  };

  createTemplate = () => {
    const { notificationFeeManager, form } = this.props;
    const { setFields } = form;
    if (notificationFeeManager.showChooseTemplate) {
      this.props.dispatch(
        showChooseTemplateList(!notificationFeeManager.showChooseTemplate)
      );
    }
    setFields({
      name: {
        value: "",
      },
      // content_sms: {
      //   value: "",
      // },
    });
    this.setState({
      editorState: EditorState.createEmpty(),
      prevType: 1,
      imageUrl: null,
      isCreateTemplate: true,
    });
  };

  handleCreateTemplate = (status, message) => {
    const { dispatch, form } = this.props;
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

      Modal.confirm({
        autoFocusButton: null,
        title: this.props.intl.formatMessage(messages.confirm),
        content: this.props.intl.formatMessage(
          messages.confirmSaveNotification
        ),
        okText: this.props.intl.formatMessage(messages.continue),
        cancelText: this.props.intl.formatMessage(messages.cancel),
        centered: true,
        onOk: () => {
          dispatch(
            createAnnouncementFeeTemplate({
              type: this.state.type ? this.state.type : 1,
              name: values.name,
              name_en: values.name_en,
              image: this.state.imageUrl,
              content_email: draftToHtml(contentRaw),
              // content_sms: values.content_sms,
            })
          );
          this.setState({ isCreateTemplate: false });
          dispatch(showChooseTemplateList(true));
        },
      });
    });
  };
  render() {
    const { notificationFeeManager, dispatch, language } = this.props;
    const { template_list, template, updating, creating } =
      notificationFeeManager;
    const { getFieldDecorator, getFieldsError } = this.props.form;
    const { type, prevType, imageUrl, isCreateTemplate } = this.state;
    const errorCurrent = getFieldsError(["content"]);
    const formatMessage = this.props.intl.formatMessage;

    return (
      <Page inner>
        <Row style={{ paddingBottom: 16 }} key="filter">
          {notificationFeeManager.showChooseTemplate ? (
            <Row style={{ paddingBottom: 16 }} key="filter">
              <Col span={5} style={{ paddingRight: 8 }}>
                <Select
                  showSearch
                  style={{ width: "100%" }}
                  placeholder={formatMessage(messages.typeNotification)}
                  optionFilterProp="children"
                  filterOption={(input, option) =>
                    option.props.children
                      .toLowerCase()
                      .indexOf(input.toLowerCase()) >= 0
                  }
                  // allowClear
                  value={type}
                  onChange={(e) => {
                    this.setState({ type: e, isCreateTemplate: false }, () => {
                      this.props.dispatch(
                        fetchAllAnnouncementFeeTemplate({
                          type: e,
                        })
                      );
                    });
                  }}
                >
                  {config.NOTIFICATION_TYPE.map((type) => {
                    return (
                      <Select.Option key={type.id} value={`${type.id}`}>
                        {language === "en" ? type.label_en : type.label}
                      </Select.Option>
                    );
                  })}
                </Select>
              </Col>
              {/* <Col span={12} style={{ paddingLeft: 24 }}>
                <Row type="flex" key="action">
                  <Tooltip title="Thêm mới mẫu">
                    <Button
                      style={{ marginRight: 10 }}
                      onClick={() => this.createTemplate()}
                      disabled={notificationFeeManager.creating}
                      loading={notificationFeeManager.creating}
                      icon="plus"
                      shape="circle"
                      size="medium"
                      type="dashed"
                    />
                  </Tooltip>
                </Row>
              </Col> */}
            </Row>
          ) : (
            <Col span={5} style={{ paddingRight: 8 }}>
              <Button
                type="link"
                size="large"
                style={{
                  display: "flex",
                  alignItems: "center",
                  textDecoration: "underline",
                }}
                onClick={() => {
                  if (isCreateTemplate) {
                    this.setState({
                      isCreateTemplate: false,
                    });
                  }
                  dispatch(fetchAllAnnouncementFeeTemplate({ type: type }));
                  dispatch(
                    showChooseTemplateList(
                      !notificationFeeManager.showChooseTemplate
                    )
                  );
                  dispatch(chooseCreateTemplate(false));
                }}
              >
                <Icon type="left" />
                <span>{formatMessage(messages.listTemplateNotification)}</span>
              </Button>
            </Col>
          )}
        </Row>

        <Row className="NotificationFeeManager" key="content">
          <Col span={24}>
            <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
              <Col
                span={!notificationFeeManager.showChooseTemplate ? 12 : 24}
                style={{
                  borderRight: !notificationFeeManager.showChooseTemplate
                    ? "1px solid #D9D9D9"
                    : null,
                  paddingRight: 24,
                }}
              >
                {!notificationFeeManager.showChooseTemplate ? (
                  <Form {...formItemLayout}>
                    <Form.Item label={formatMessage(messages.title)}>
                      {getFieldDecorator("name", {
                        initialValue: "",
                        rules: [
                          {
                            required: true,
                            message: formatMessage(messages.emptyTitle),
                            whitespace: true,
                          },
                          {
                            validator: (rule, value, callback) => {
                              // check if value is in template list
                              if (
                                template_list.data &&
                                template_list.data.length > 0
                              ) {
                                if (!isCreateTemplate && template.data) {
                                  const isExist = template_list.data
                                    .filter((i) => i.id !== template.data.id)
                                    .find(
                                      (item) =>
                                        item.name.trim() === value.trim()
                                    );
                                  if (isExist) {
                                    callback(
                                      `${formatMessage(
                                        messages.title
                                      )} ${formatMessage(
                                        messages.alreadyExists
                                      )}`
                                    );
                                  }
                                }
                                if (isCreateTemplate) {
                                  const isExist = template_list.data.find(
                                    (item) => item.name.trim() === value.trim()
                                  );
                                  if (isExist) {
                                    callback(
                                      `${formatMessage(
                                        messages.title
                                      )} ${formatMessage(
                                        messages.alreadyExists
                                      )}`
                                    );
                                  }
                                }
                              }
                              callback();
                            },
                          },
                        ],
                      })(<Input style={{ width: "100%" }} maxLength={255} />)}
                    </Form.Item>
                    <Form.Item label={`${formatMessage(messages.title)} (EN)`}>
                      {getFieldDecorator("name_en", {
                        initialValue: "",
                        rules: [
                          {
                            required: true,
                            message: formatMessage(messages.emptyTitleEn),
                            whitespace: true,
                          },
                          {
                            validator: (rule, value, callback) => {
                              // check if value is in template list
                              if (
                                template_list.data &&
                                template_list.data.length > 0
                              ) {
                                if (!isCreateTemplate && template.data) {
                                  const isExist = template_list.data
                                    .filter((i) => i.id !== template.data.id)
                                    .find(
                                      (item) =>
                                        item.name_en.trim() === value.trim()
                                    );
                                  if (isExist) {
                                    callback(
                                      `${formatMessage(
                                        messages.title
                                      )} ${formatMessage(
                                        messages.alreadyExists
                                      )}`
                                    );
                                  }
                                }
                                if (isCreateTemplate) {
                                  const isExist = template_list.data.find(
                                    (item) =>
                                      item.name_en.trim() === value.trim()
                                  );
                                  if (isExist) {
                                    callback(
                                      `${formatMessage(
                                        messages.title
                                      )} (EN) ${formatMessage(
                                        messages.alreadyExists
                                      )}`
                                    );
                                  }
                                }
                              }
                              callback();
                            },
                          },
                        ],
                      })(<Input style={{ width: "100%" }} maxLength={255} />)}
                    </Form.Item>
                    <Form.Item label={formatMessage(messages.content)}>
                      {getFieldDecorator("content", {
                        initialValue: "",
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

                      {type === "5" && (
                        <>
                          <Tooltip
                            title={`${formatMessage(
                              messages.example
                            )}: Nguyễn Văn A`}
                          >
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
                              {formatMessage(messages.nameHouseholder)}
                            </Button>
                          </Tooltip>
                          <Tooltip
                            title={`${formatMessage(
                              messages.example
                            )}: TSQ.T1007`}
                          >
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
                              {formatMessage(messages.property)}
                            </Button>
                          </Tooltip>
                          <Tooltip
                            title={`${formatMessage(
                              messages.example
                            )}: 2.000.000 VNĐ`}
                          >
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
                              {formatMessage(messages.totalFee)}
                            </Button>
                          </Tooltip>
                          {/* <Tooltip
                            title={`${formatMessage(
                              messages.example
                            )}: 9ATV0GV4`}
                          >
                            <Button
                              onClick={() => {
                                const { editorState } = this.state;
                                const contentState = Modifier.replaceText(
                                  editorState.getCurrentContent(),
                                  editorState.getSelection(),
                                  "{{PAYMENT_CODE}}",
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
                              {formatMessage(messages.codePayment)}
                            </Button>
                          </Tooltip> */}
                        </>
                      )}
                    </Form.Item>
                    {/* <Form.Item
                      label={`${formatMessage(messages.content)} SMS:`}
                      colon={false}
                    >
                      {getFieldDecorator("content_sms", {
                        initialValue: "",
                        rules: [
                          {
                            required: (getFieldValue("pushType") || []).some(
                              (ss) => ss == "is_send_sms"
                            ),
                            message: formatMessage(messages.emptySMS),
                          },
                        ],
                      })(<Input.TextArea rows={5} style={{ marginTop: 8 }} />)}
                    </Form.Item> */}
                    <Form.Item label={formatMessage(messages.category)}>
                      <Select
                        disabled={!isCreateTemplate}
                        showSearch
                        style={{ width: "100%" }}
                        placeholder={formatMessage(messages.typeNotification)}
                        optionFilterProp="children"
                        filterOption={(input, option) =>
                          option.props.children
                            .toLowerCase()
                            .indexOf(input.toLowerCase()) >= 0
                        }
                        // allowClear
                        value={type}
                        onChange={(e) => {
                          this.setState({ type: e }, () => {
                            this.props.dispatch(
                              fetchAllAnnouncementFeeTemplate({
                                type: e,
                              })
                            );
                          });
                        }}
                      >
                        {config.NOTIFICATION_TYPE.map((type) => {
                          return (
                            <Select.Option key={type.id} value={`${type.id}`}>
                              {language === "en" ? type.label_en : type.label}
                            </Select.Option>
                          );
                        })}
                      </Select>
                    </Form.Item>
                    <Form.Item
                      label={formatMessage(messages.avatar)}
                      colon={false}
                    >
                      <UploaderSimple
                        // beforeUpload={beforeUpload}
                        allowClear
                        imageUrl={getFullLinkImage(imageUrl)}
                        onUploaded={(url) => this.setState({ imageUrl: url })}
                        maxSize={10}
                        disabled={false}
                      />
                    </Form.Item>

                    {!isCreateTemplate ? (
                      <Form.Item label={" "} colon={false}>
                        <Button
                          ghost
                          type="danger"
                          style={{ marginRight: 10 }}
                          onClick={() => {
                            if (isCreateTemplate) {
                              this.setState({
                                isCreateTemplate: false,
                              });
                            }
                            dispatch(
                              fetchAllAnnouncementFeeTemplate({ type: type })
                            );
                            dispatch(
                              showChooseTemplateList(
                                !notificationFeeManager.showChooseTemplate
                              )
                            );
                            dispatch(chooseCreateTemplate(false));
                          }}
                        >
                          {formatMessage(messages.cancel)}
                        </Button>
                        <Button
                          type="primary"
                          style={{ marginRight: 10 }}
                          onClick={() => {
                            this.handleUpdateTemplate();
                          }}
                          disabled={updating}
                        >
                          {formatMessage(messages.updateTemplate)}
                        </Button>
                      </Form.Item>
                    ) : (
                      <Form.Item label={" "} colon={false}>
                        <Button
                          ghost
                          type="danger"
                          style={{ marginRight: 10 }}
                          onClick={() => {
                            if (isCreateTemplate) {
                              this.setState({
                                isCreateTemplate: false,
                              });
                            }
                            dispatch(
                              fetchAllAnnouncementFeeTemplate({ type: type })
                            );
                            dispatch(
                              showChooseTemplateList(
                                !notificationFeeManager.showChooseTemplate
                              )
                            );
                            dispatch(chooseCreateTemplate(false));
                          }}
                        >
                          {formatMessage(messages.cancel)}
                        </Button>
                        <Button
                          disabled={creating}
                          type="primary"
                          onClick={() => {
                            this.handleCreateTemplate();
                            dispatch(chooseCreateTemplate(false));
                          }}
                          // disabled={!imageUrl}
                        >
                          {formatMessage(messages.addTemplate)}
                        </Button>
                      </Form.Item>
                    )}
                  </Form>
                ) : (
                  <ChooseNotiTemplate
                    language={language}
                    templateList={notificationFeeManager.template_list}
                    dispatch={dispatch}
                    handleDeleteTemplate={this.handleDeleteTemplate}
                  />
                )}
              </Col>
              {!notificationFeeManager.showChooseTemplate && (
                <Col span={12}>
                  <Row
                    style={{
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
                            {language === "en"
                              ? config.NOTIFICATION_TYPE.find(
                                  (item) => item.id == type
                                ).label_en
                              : config.NOTIFICATION_TYPE.find(
                                  (item) => item.id == type
                                ).label}
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
                                {formatMessage(messages.message)}
                              </span>
                            </Row>
                            <span
                              style={{ color: "#C4C4C4", textAlign: "right" }}
                            >
                              {formatMessage(messages.justFN)}
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
                      <Radio.Button value={1}>App</Radio.Button>
                      <Radio.Button value={0}>Email</Radio.Button>
                      {/* <Radio.Button value={2}>SMS</Radio.Button> */}
                    </Radio.Group>
                  </Row>
                </Col>
              )}
            </Row>
          </Col>
        </Row>
      </Page>
    );
  }
}

NotificationFeeManager.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notificationFeeManager: makeSelectNotificationFeeManager(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "notificationFeeManager", reducer });
const withSaga = injectSaga({ key: "notificationFeeManager", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(NotificationFeeManager));
