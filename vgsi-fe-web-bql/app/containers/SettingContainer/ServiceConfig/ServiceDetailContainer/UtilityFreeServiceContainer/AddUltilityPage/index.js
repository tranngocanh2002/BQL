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

import {
  Button,
  Col,
  Form,
  Input,
  InputNumber,
  Modal,
  Row,
  Select,
  TimePicker,
} from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Avatar from "../../../../../../components/Avatar";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectAddUltilityPage from "./selectors";

import { getFullLinkImage } from "../../../../../../connection";

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
import PhoneNumberInput from "../../../../../../components/PhoneNumberInput";
import makeSelectUtilityFreeServiceContainer from "../selectors";

import htmlToDraft from "html-to-draftjs";
import _ from "lodash";
import moment from "moment";
import { injectIntl } from "react-intl";
import InputNumberFormat from "../../../../../../components/InputNumberFormat";
import Page from "../../../../../../components/Page/Page";
import { config, validateText } from "../../../../../../utils";
import { CUSTOM_TOOLBAR } from "../../../../../../utils/config";
import messages from "../../../messages";
import { regexOnlyText, regexPhoneNumberVN } from "utils/constants";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

const formItemLayout = {
  labelCol: {
    xxl: { span: 6 },
    xl: { span: 24 },
    sm: { span: 24 },
    style: {
      lineHeight: 3,
    },
  },
  wrapperCol: {
    xxl: { span: 16 },
    xl: { span: 24 },
    sm: { span: 24 },
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
              errors: [new Error("Nội dung không được dài quá 2000 ký tự.")],
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
            status: values.status,
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
            status: values.status,
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
      return <Redirect to="/main/setting/service/detail/utility-free/list" />;
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
              <Form.Item
                label={this.props.intl.formatMessage(messages.nameUtility)}
              >
                {getFieldDecorator("name", {
                  initialValue: record ? record.name : "",
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyUtilityName
                      ),
                      whitespace: true,
                    },
                  ],
                })(<Input style={{ width: "100%" }} maxLength={50} />)}
              </Form.Item>
              <Form.Item
                label={`${this.props.intl.formatMessage(
                  messages.nameUtility
                )} (EN)`}
              >
                {getFieldDecorator("name_en", {
                  initialValue: record ? record.name_en : "",
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyUtilityNameEn
                      ),
                      whitespace: true,
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (
                          value &&
                          value.trim() != "" &&
                          validateText(value)
                        ) {
                          callback(
                            this.props.intl.formatMessage(
                              messages.emptyUtilityNameEn
                            )
                          );
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(<Input style={{ width: "100%" }} maxLength={50} />)}
              </Form.Item>
              <Form.Item label={this.props.intl.formatMessage(messages.status)}>
                {getFieldDecorator("status", {
                  initialValue: record ? record.status.toString() : "1",
                  rules: [
                    {
                      required: true,
                      message: "Không được để trống",
                      whitespace: true,
                    },
                  ],
                })(
                  <Select
                    placeholder={this.props.intl.formatMessage(
                      messages.selectType
                    )}
                  >
                    {config.STATUS_TYPE.map((type) => {
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
              <Form.Item
                label={this.props.intl.formatMessage(messages.description)}
              >
                {getFieldDecorator("content", {
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyDescription
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
                        if (this.props.maxLength) {
                          if (
                            draftToHtml(
                              convertToRaw(
                                this.state.editorState.getCurrentContent()
                              )
                            ).length >= this.props.maxLength
                          ) {
                            return "handled";
                          }
                        }
                        if (
                          _.sum(
                            convertToRaw(
                              this.state.editorState.getCurrentContent()
                            ).blocks.map((bl) => bl.text.length)
                          ) >= 1000
                        ) {
                          return "handled";
                        }
                      }}
                      toolbar={CUSTOM_TOOLBAR}
                    />
                  </div>
                )}
              </Form.Item>
              <Form.Item
                label={this.props.intl.formatMessage(messages.regulation)}
              >
                {getFieldDecorator("regulation", {
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyRegulation
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
                      handlePastedText={() => false}
                      editorState={editorStateregulation}
                      wrapperClassName="demo-wrapper"
                      editorClassName="rdw-storybook-editor"
                      onEditorStateChange={(editorState) => {
                        this.setState({
                          editorStateregulation: editorState,
                        });
                      }}
                      handleBeforeInput={(input) => {
                        if (this.props.maxLength) {
                          if (
                            draftToHtml(
                              convertToRaw(
                                this.state.editorState.getCurrentContent()
                              )
                            ).length >= this.props.maxLength
                          ) {
                            return "handled";
                          }
                        }
                        if (
                          _.sum(
                            convertToRaw(
                              this.state.editorStateregulation.getCurrentContent()
                            ).blocks.map((bl) => bl.text.length)
                          ) >= 1000
                        ) {
                          return "handled";
                        }
                      }}
                      toolbar={CUSTOM_TOOLBAR}
                    />
                  </div>
                )}
              </Form.Item>
              <Form.Item label={this.props.intl.formatMessage(messages.type)}>
                {getFieldDecorator("booking_type", {
                  initialValue: record ? String(record.booking_type) : "0",
                })(
                  <Select
                    placeholder={this.props.intl.formatMessage(
                      messages.selectType
                    )}
                    // disabled={!!record}
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
              <Form.Item
                label={this.props.intl.formatMessage(messages.timeOpen)}
              >
                {getFieldDecorator("hours_open", {
                  initialValue: moment(
                    record ? record.hours_open : "08:00",
                    "HH:mm"
                  ),
                  rules: [
                    {
                      type: "object",
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyTimeOpen
                      ),
                    },
                  ],
                })(<TimePicker style={{ width: "100%" }} format="HH:mm" />)}
              </Form.Item>
              <Form.Item
                label={this.props.intl.formatMessage(messages.timeClose)}
              >
                {getFieldDecorator("hours_close", {
                  initialValue: moment(
                    record ? record.hours_close : "20:00",
                    "HH:mm"
                  ),
                  rules: [
                    {
                      type: "object",
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyTimeClose
                      ),
                    },
                  ],
                })(<TimePicker style={{ width: "100%" }} format="HH:mm" />)}
              </Form.Item>
              <Form.Item
                label={this.props.intl.formatMessage(messages.timeWaitApprove)}
              >
                {getFieldDecorator("timeout_pay_request", {
                  initialValue: record ? record.timeout_pay_request : 30,
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.limitTimeToApprove
                      ),
                      type: "number",
                      whitespace: true,
                      min: 1,
                    },
                  ],
                })(<InputNumber style={{ width: "100%" }} maxLength={6} />)}
              </Form.Item>
              <Form.Item
                label={this.props.intl.formatMessage(messages.timeToCancel)}
              >
                {getFieldDecorator("timeout_cancel_book", {
                  initialValue: record ? record.timeout_cancel_book : 30,
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.limitTimeToCancel
                      ),
                      type: "number",
                      whitespace: true,
                      min: 1,
                    },
                  ],
                })(<InputNumber style={{ width: "100%" }} maxLength={6} />)}
              </Form.Item>
              <Form.Item label={this.props.intl.formatMessage(messages.books)}>
                {getFieldDecorator("limit_book_apartment", {
                  initialValue: record ? record.limit_book_apartment : 50,
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.limitBooks
                      ),
                      type: "number",
                      whitespace: true,
                      min: 1,
                    },
                  ],
                })(<InputNumber style={{ width: "100%" }} maxLength={6} />)}
              </Form.Item>
              <Form.Item
                label={this.props.intl.formatMessage(messages.deposit)}
              >
                {getFieldDecorator("deposit_money", {
                  initialValue: record ? record.deposit_money : 0,
                  rules: [
                    {
                      type: "number",
                      min: 0,
                    },
                  ],
                })(
                  <InputNumberFormat maxLength={19} style={{ width: "100%" }} />
                )}
              </Form.Item>
              <Form.Item
                label={this.props.intl.formatMessage(messages.numberContact)}
              >
                {getFieldDecorator("hotline", {
                  initialValue: record ? record.hotline : "",
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyNumberContact
                      ),
                    },
                    {
                      // this is different with regexPhoneNumberVN
                      pattern:
                        /((^(\+84|84|0|0084){1})(2|3|5|7|8|9))+([0-9]{8,9})$/,
                      message: this.props.intl.formatMessage(
                        messages.hotlineInvalid
                      ),
                    },
                  ],
                })(<PhoneNumberInput maxLength={11} />)}
              </Form.Item>
            </Form>
          </Col>
          <Col lg={6} md={24} style={{ marginBottom: 330 }}>
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
                      title: this.props.intl.formatMessage(
                        messages.confirmCancel
                      ),
                      okText: this.props.intl.formatMessage(messages.agree),
                      okType: "danger",
                      cancelText: this.props.intl.formatMessage(
                        messages.cancel
                      ),
                      onOk: () => {
                        this.props.history.push(
                          "/main/setting/service/detail/utility-free/list"
                        );
                      },
                      onCancel() {},
                    });
                  }}
                >
                  {this.props.intl.formatMessage(messages.cancel)}
                </Button>
                <Button
                  ghost
                  loading={loading}
                  type="primary"
                  style={{ width: 150, marginLeft: 10 }}
                  onClick={this._onSave}
                >
                  {record
                    ? this.props.intl.formatMessage(messages.update)
                    : this.props.intl.formatMessage(messages.add)}
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
)(withRouter(injectIntl(AddUltilityPage)));
