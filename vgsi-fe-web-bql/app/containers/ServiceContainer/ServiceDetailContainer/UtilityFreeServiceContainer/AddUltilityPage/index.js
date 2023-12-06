/**
 *
 * AddUltilityPage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Col, Form, Input, Modal, Row, Select, TimePicker } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Avatar from "../../../../../components/Avatar";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectAddUltilityPage from "./selectors";

import { getFullLinkImage } from "../../../../../connection";

import { ContentState, EditorState, convertToRaw } from "draft-js";
import draftToHtml from "draftjs-to-html";
import DraftEditor from "components/Editor/Editor";

import {
  addUltilityItem,
  defaultAction,
  fetchDetailUltilityItem,
  updateUltilityItem,
} from "./actions";
import styles from "./index.less";

import { Redirect, withRouter } from "react-router";
import PhoneNumberInput from "../../../../../components/PhoneNumberInput";
import makeSelectUtilityFreeServiceContainer from "../selectors";

import htmlToDraft from "html-to-draftjs";
import _ from "lodash";
import moment from "moment";
import { FormattedMessage } from "react-intl";
import NumericInput from "../../../../../components/NumericInput";
import Page from "../../../../../components/Page/Page";
import { config } from "../../../../../utils";
import { CUSTOM_TOOLBAR } from "../../../../../utils/config";
import messages from "../messages";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

const formItemLayout = {
  labelCol: {
    xxl: { span: 6 },
    xl: { span: 9 },
  },
  wrapperCol: {
    xxl: { span: 18 },
    xl: { span: 15 },
  },
};

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class AddUltilityPage extends React.PureComponent {
  constructor(props) {
    super(props);
    const { record } = props.location.state || {};

    let blockArray = record ? htmlToDraft(record.description) : undefined;
    let blockArrayregulation = record
      ? htmlToDraft(record.regulation || "")
      : undefined;

    this.state = {
      record,
      imageUrl: !!record && !!record.medias ? record.medias.logo : undefined,
      editorState:
        !!blockArray && !!blockArray.contentBlocks
          ? EditorState.createWithContent(
              ContentState.createFromBlockArray(blockArray.contentBlocks)
            )
          : EditorState.createEmpty(),
      editorStateregulation:
        !!blockArrayregulation && !!blockArrayregulation.contentBlocks
          ? EditorState.createWithContent(
              ContentState.createFromBlockArray(
                blockArrayregulation.contentBlocks
              )
            )
          : EditorState.createEmpty(),
    };
  }

  componentDidMount() {
    const { id } = this.props.match.params;
    if (id != undefined && !this.state.record) {
      this.props.dispatch(fetchDetailUltilityItem({ id }));
    }
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.addUltilityPage.detail.loading !=
        nextProps.addUltilityPage.detail.loading &&
      !nextProps.addUltilityPage.detail.loading
    ) {
      const record = nextProps.addUltilityPage.detail.data;
      let blockArray = record ? htmlToDraft(record.description) : undefined;
      let blockArrayregulation = record
        ? htmlToDraft(record.regulation || "")
        : undefined;

      this.setState({
        record,
        imageUrl: !!record && !!record.medias ? record.medias.logo : undefined,
        editorState:
          !!blockArray && !!blockArray.contentBlocks
            ? EditorState.createWithContent(
                ContentState.createFromBlockArray(blockArray.contentBlocks)
              )
            : EditorState.createEmpty(),
        editorStateregulation:
          !!blockArrayregulation && !!blockArrayregulation.contentBlocks
            ? EditorState.createWithContent(
                ContentState.createFromBlockArray(
                  blockArrayregulation.contentBlocks
                )
              )
            : EditorState.createEmpty(),
      });
    }
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  _onSave = (status, message) => {
    const { form } = this.props;
    const { validateFieldsAndScroll, setFields } = form;

    let contentRaw = convertToRaw(this.state.editorState.getCurrentContent());
    let contentRawregulation = convertToRaw(
      this.state.editorStateregulation.getCurrentContent()
    );
    let isErrorContent = false;
    let isErrorregulation = false;
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
    if (
      !contentRawregulation ||
      !contentRawregulation.blocks ||
      !contentRawregulation.blocks.some(
        (block) => block.text.replace(/ /g, "").length != 0
      )
    ) {
      setFields({
        regulation: {
          value: "",
        },
      });
      isErrorregulation = true;
    } else {
      setFields({
        regulation: {
          value: "111",
          errors: [],
        },
      });
      isErrorregulation = false;
    }

    validateFieldsAndScroll((errors, values) => {
      if (!isErrorContent) {
        if (_.sum(contentRaw.blocks.map((bl) => bl.text.length)) > 2000) {
          setFields({
            content: {
              value: "111",
              errors: [
                new Error(
                  this.props.intl.formatMessage({ ...messages.validateContent })
                ),
              ],
            },
          });
          isErrorContent = true;
        }
      }
      if (errors || isErrorContent || isErrorregulation) {
        return;
      }
      const { record } = this.state;

      if (!record) {
        this.props.dispatch(
          addUltilityItem({
            ...values,
            description: draftToHtml(contentRaw),
            regulation: draftToHtml(contentRawregulation),
            medias: {
              logo: this.state.imageUrl,
            },
            hours_close: values.hours_close.format("HH:mm"),
            hours_open: values.hours_open.format("HH:mm"),
            service_map_management_id:
              this.props.utilityFreeServiceContainer.data.id,
            timeout_pay_request: values.timeout_pay_request,
            timeout_cancel_book: values.timeout_cancel_book,
            limit_book_apartment: values.limit_book_apartment,
          })
        );
      } else {
        this.props.dispatch(
          updateUltilityItem({
            ...values,
            id: record.id,
            description: draftToHtml(contentRaw),
            regulation: draftToHtml(contentRawregulation),
            medias: {
              logo: this.state.imageUrl,
            },
            hours_close: values.hours_close.format("HH:mm"),
            hours_open: values.hours_open.format("HH:mm"),
            service_map_management_id:
              this.props.utilityFreeServiceContainer.data.id,
            timeout_pay_request: values.timeout_pay_request,
            timeout_cancel_book: values.timeout_cancel_book,
            limit_book_apartment: values.limit_book_apartment,
          })
        );
      }
    });
  };

  render() {
    const { editorState, record, editorStateregulation } = this.state;
    const { addUltilityPage } = this.props;
    const { getFieldDecorator, getFieldsError, getFieldValue } =
      this.props.form;
    const errorCurrent = getFieldsError(["content"]);
    const { loading, createSuccess, detail } = addUltilityPage;
    if (createSuccess) {
      return <Redirect to="/main/service/detail/utility-free/list" />;
    }

    return (
      <Page noPadding loading={detail.loading}>
        <Row
          gutter={24}
          style={{ marginTop: 24 }}
          className={styles.addUltilityPage}
        >
          <Col lg={18} md={24}>
            <Form {...formItemLayout} onSubmit={this.handleSubmit}>
              <Form.Item label={<FormattedMessage {...messages.nameUtility} />}>
                {getFieldDecorator("name", {
                  initialValue: record ? record.name : "",
                  rules: [
                    {
                      required: true,
                      message: (
                        <FormattedMessage {...messages.ruleNameUtility} />
                      ),
                      whitespace: true,
                    },
                  ],
                })(<Input style={{ width: "100%" }} maxLength={50} />)}
              </Form.Item>
              <Form.Item label={<FormattedMessage {...messages.description} />}>
                {getFieldDecorator("content", {
                  rules: [
                    {
                      required: true,
                      message: (
                        <FormattedMessage {...messages.ruleDescription} />
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
                      editorState={editorState}
                      wrapperClassName="demo-wrapper"
                      editorClassName="rdw-storybook-editor"
                      onEditorStateChange={(editorState) => {
                        this.setState({
                          editorState,
                        });
                      }}
                      handleBeforeInput={(input) => {
                        if (
                          _.sum(
                            convertToRaw(
                              this.state.editorState.getCurrentContent()
                            ).blocks.map((bl) => bl.text.length)
                          ) >= 2000
                        ) {
                          return "handled";
                        }
                      }}
                      toolbar={CUSTOM_TOOLBAR}
                    />
                  </div>
                )}
              </Form.Item>
              <Form.Item label={<FormattedMessage {...messages.regulation} />}>
                {getFieldDecorator("regulation", {
                  rules: [
                    {
                      required: true,
                      message: (
                        <FormattedMessage {...messages.ruleRegulation} />
                      ),
                    },
                  ],
                })(
                  <div
                    style={{
                      border: errorCurrent.regulation ? "1px solid red" : "",
                    }}
                  >
                    <DraftEditor
                      editorState={editorStateregulation}
                      wrapperClassName="demo-wrapper"
                      editorClassName="rdw-storybook-editor"
                      onEditorStateChange={(editorState) => {
                        this.setState({
                          editorStateregulation: editorState,
                        });
                      }}
                      handleBeforeInput={(input) => {
                        if (
                          _.sum(
                            convertToRaw(
                              this.state.editorStateregulation.getCurrentContent()
                            ).blocks.map((bl) => bl.text.length)
                          ) >= 2000
                        ) {
                          return "handled";
                        }
                      }}
                      toolbar={CUSTOM_TOOLBAR}
                    />
                  </div>
                )}
              </Form.Item>
              <Form.Item label={<FormattedMessage {...messages.type} />}>
                {getFieldDecorator("booking_type", {
                  initialValue: record ? String(record.booking_type) : "0",
                })(
                  <Select
                    placeholder={<FormattedMessage {...messages.ruleType} />}
                    disabled={!!record}
                  >
                    {config.BOOKING_TYPE.map((type) => {
                      return (
                        <Select.Option key={type.id} value={type.id}>
                          {this.props.language === "en"
                            ? type.label_en
                            : type.label}
                        </Select.Option>
                      );
                    })}
                  </Select>
                )}
              </Form.Item>
              <Form.Item label={<FormattedMessage {...messages.hourOpen} />}>
                {getFieldDecorator("hours_open", {
                  initialValue: moment(
                    record ? record.hours_open : "08:00",
                    "HH:mm"
                  ),
                  rules: [
                    {
                      type: "object",
                      required: true,
                      message: <FormattedMessage {...messages.ruleHourOpen} />,
                    },
                  ],
                })(<TimePicker style={{ width: "100%" }} format="HH:mm" />)}
              </Form.Item>
              <Form.Item label={<FormattedMessage {...messages.hourClose} />}>
                {getFieldDecorator("hours_close", {
                  initialValue: moment(
                    record ? record.hours_close : "20:00",
                    "HH:mm"
                  ),
                  rules: [
                    {
                      type: "object",
                      required: true,
                      message: <FormattedMessage {...messages.ruleHourClose} />,
                    },
                  ],
                })(<TimePicker style={{ width: "100%" }} format="HH:mm" />)}
              </Form.Item>
              <Form.Item
                label={<FormattedMessage {...messages.waitingTimeApprove} />}
              >
                {getFieldDecorator("timeout_pay_request", {
                  initialValue: record ? record.timeout_pay_request : "",
                  rules: [
                    {
                      required: true,
                      message: (
                        <FormattedMessage
                          {...messages.ruleWaitingTimeApprove}
                        />
                      ),
                    },
                  ],
                })(<NumericInput maxLength={9} suffix="phút" />)}
              </Form.Item>
              <Form.Item label="Thời gian được hủy (trước giờ đặt)">
                {getFieldDecorator("timeout_cancel_book", {
                  initialValue: record ? record.timeout_cancel_book : "",
                  rules: [
                    {
                      required: true,
                      message: "Thời gian được hủy không được để trống.",
                    },
                  ],
                })(<NumericInput maxLength={9} suffix="phút" />)}
              </Form.Item>
              <Form.Item label="Số lượt đặt/căn hộ/tháng">
                {getFieldDecorator("limit_book_apartment", {
                  initialValue: record ? record.limit_book_apartment : "",
                  rules: [
                    {
                      required: true,
                      message: "Số lượt book không được để trống.",
                    },
                  ],
                })(<NumericInput maxLength={9} suffix="lượt" />)}
              </Form.Item>
              <Form.Item label="Số liên hệ">
                {getFieldDecorator("hotline", {
                  initialValue: record ? record.hotline : "",
                  rules: [
                    {
                      required: true,
                      message: "Số liên hệ không được để trống.",
                    },
                  ],
                })(<PhoneNumberInput maxLength={11} />)}
              </Form.Item>
            </Form>
          </Col>
          <Col lg={6} md={24} style={{ marginBottom: 32 }}>
            <Row>
              <Col
                lg={{
                  col: 16,
                  offset: 0,
                }}
                md={{
                  col: 16,
                  offset: 0,
                }}
              >
                <Avatar
                  imageUrl={getFullLinkImage(this.state.imageUrl)}
                  onUploaded={(url) => this.setState({ imageUrl: url })}
                />
              </Col>
            </Row>
          </Col>
          <Col lg={24} md={24}>
            <Row>
              <Col
                xxl={{
                  col: 16,
                  offset: 8,
                }}
                xl={{
                  col: 16,
                  offset: 7,
                }}
                lg={{
                  col: 24,
                  offset: 3,
                }}
                md={{
                  col: 24,
                  offset: 0,
                }}
                sm={{
                  col: 24,
                  offset: 0,
                }}
                xs={{
                  col: 24,
                  offset: 0,
                }}
              >
                <Button
                  type="danger"
                  style={{ width: 150 }}
                  disabled={loading}
                  onClick={(e) => {
                    Modal.confirm({
                      autoFocusButton: null,
                      title: "Bạn chắc chắn muốn huỷ yêu cầu abc?",
                      okText: "Đồng ý",
                      okType: "danger",
                      cancelText: "Huỷ",
                      onOk: () => {
                        this.props.history.push(
                          "/main/service/detail/utility-free/list"
                        );
                      },
                      onCancel() {},
                    });
                  }}
                >
                  Huỷ
                </Button>
                <Button
                  ghost
                  loading={loading}
                  type="primary"
                  style={{ width: 150, marginLeft: 10 }}
                  onClick={this._onSave}
                >
                  {record ? "Cập nhật" : "Thêm mới"}
                </Button>
              </Col>
            </Row>
          </Col>
        </Row>
      </Page>
    );
  }
}

AddUltilityPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  addUltilityPage: makeSelectAddUltilityPage(),
  utilityFreeServiceContainer: makeSelectUtilityFreeServiceContainer(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "addUltilityPage", reducer });
const withSaga = injectSaga({ key: "addUltilityPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(AddUltilityPage));
