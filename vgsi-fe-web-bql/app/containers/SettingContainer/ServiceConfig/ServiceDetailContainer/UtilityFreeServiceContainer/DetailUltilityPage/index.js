/**
 *
 * DetailUltilityPage
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
  Divider,
  Empty,
  Form,
  Input,
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
import makeSelectDetailUltilityPage from "./selectors";

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

import { withRouter } from "react-router";
import PhoneNumberInput from "../../../../../../components/PhoneNumberInput";
import makeSelectUtilityFreeServiceContainer from "../selectors";

import htmlToDraft from "html-to-draftjs";
import _ from "lodash";
import moment from "moment";
import { injectIntl } from "react-intl";
import InputNumberFormat from "../../../../../../components/InputNumberFormat";
import Page from "../../../../../../components/Page/Page";
import WithRole from "../../../../../../components/WithRole";
import { formatPrice } from "../../../../../../utils";
import config, { CUSTOM_TOOLBAR } from "../../../../../../utils/config";
import messages from "../../../messages";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

const formItemLayout = {
  labelCol: {
    xxl: { span: 7 },
    xl: { span: 24 },
    lg: { span: 24 },
    style: {
      lineHeight: 3,
    },
  },
  wrapperCol: {
    xxl: { span: 17 },
    xl: { span: 24 },
    lg: { span: 24 },
  },
};

const formItem = {
  style: {
    marginBottom: 0,
    fontWeight: "bold",
    color: "black",
  },
};

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class DetailUltilityPage extends React.PureComponent {
  constructor(props) {
    super(props);
    const { record } = props.location.state || {};
    let blockArray = record ? htmlToDraft(record.description) : undefined;
    let blockArrayregulation = record
      ? htmlToDraft(record.regulation || "")
      : undefined;

    this.state = {
      isEditting: false,
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
      this.props.DetailUltilityPage.detail.loading !=
        nextProps.DetailUltilityPage.detail.loading &&
      !nextProps.DetailUltilityPage.detail.loading
    ) {
      const record = nextProps.DetailUltilityPage.detail.data;
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
    if (
      this.props.DetailUltilityPage.createSuccess !=
        nextProps.DetailUltilityPage.createSuccess &&
      nextProps.DetailUltilityPage.createSuccess
    ) {
      this.setState({ isEditting: false });
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
      if (!isErrorContent) {
        if (_.sum(contentRaw.blocks.map((bl) => bl.text.length)) > 2000) {
          setFields({
            content: {
              value: "111",
              errors: [
                new Error(
                  this.props.intl.formatMessage(messages.contentToLong)
                ),
              ],
            },
          });
          isErrorContent = true;
        }
      }
      if (errors || isErrorContent) {
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
    const { editorState, record, isEditting, editorStateregulation } =
      this.state;
    const { DetailUltilityPage } = this.props;
    const formatMessage = this.props.intl.formatMessage;

    const { getFieldDecorator, getFieldsError } = this.props.form;
    const errorCurrent = getFieldsError(["content"]);

    const { loading, detail } = DetailUltilityPage;
    return (
      <Page noPadding loading={detail.loading}>
        {!detail.loading && !!record && (
          <Row
            gutter={24}
            style={{ marginTop: 24, marginLeft: 0, marginRight: 0 }}
            className={styles.DetailUltilityPage}
          >
            <Col xl={16} lg={15} md={24}>
              <Form {...formItemLayout} onSubmit={this.handleSubmit}>
                <Form.Item
                  label={this.props.intl.formatMessage(messages.nameUtility)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("name", {
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
                    })(<Input style={{ width: "100%" }} maxLength={50} />)
                  ) : (
                    <span style={{ fontWeight: 400 }}>{record.name}</span>
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={`${this.props.intl.formatMessage(
                    messages.nameUtility
                  )} (EN)`}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("name_en", {
                      initialValue: record ? record.name_en : "",
                      rules: [
                        {
                          required: true,
                          message: this.props.intl.formatMessage(
                            messages.emptyUtilityNameEn
                          ),
                          whitespace: true,
                        },
                      ],
                    })(<Input style={{ width: "100%" }} maxLength={50} />)
                  ) : (
                    <span style={{ fontWeight: 400 }}>{record.name_en}</span>
                  )}
                </Form.Item>

                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={this.props.intl.formatMessage(messages.status)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("name_en", {
                      initialValue: "Trạng thái",
                    })(
                      <Select
                        placeholder={this.props.intl.formatMessage(
                          messages.status
                        )}
                        //disabled={!isEditting}
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
                    )
                  ) : (
                    <span style={{ fontWeight: 400 }}>
                      {record.status === 1
                        ? this.props.intl.formatMessage(messages.active)
                        : record.status === 0
                        ? this.props.intl.formatMessage(messages.pause)
                        : this.props.intl.formatMessage(messages.stop)}
                    </span>
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />

                <Form.Item
                  label={this.props.intl.formatMessage(messages.description)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("content", {
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
                    )
                  ) : (
                    <div
                      style={{
                        fontWeight: 400,
                        color: "black",
                        marginTop: 8,
                      }}
                      dangerouslySetInnerHTML={{
                        __html: draftToHtml(
                          convertToRaw(
                            this.state.editorState.getCurrentContent()
                          )
                        ),
                      }}
                    />
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={this.props.intl.formatMessage(messages.regulation)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("regulation", {
                      // rules: [{ required: true, message: 'Quy định không được để trống.' }],
                    })(
                      <div
                        style={{
                          border: errorCurrent.regulation
                            ? "1px solid red"
                            : "",
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
                    )
                  ) : (
                    <div
                      style={{
                        fontWeight: 400,
                        color: "black",
                        marginTop: 8,
                      }}
                      dangerouslySetInnerHTML={{
                        __html: draftToHtml(
                          convertToRaw(
                            this.state.editorStateregulation.getCurrentContent()
                          )
                        ),
                      }}
                    />
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={this.props.intl.formatMessage(messages.type)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("booking_type", {
                      initialValue: record ? String(record.booking_type) : "0",
                    })(
                      <Select
                        placeholder={this.props.intl.formatMessage(
                          messages.selectType
                        )}
                        disabled={!!record || !isEditting}
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
                    )
                  ) : (
                    <span style={{ fontWeight: 400 }}>
                      {this.props.language === "en"
                        ? config.BOOKING_TYPE.find(
                            (tt) => tt.id == record.booking_type
                          ).label_en
                        : config.BOOKING_TYPE.find(
                            (tt) => tt.id == record.booking_type
                          ).label}
                    </span>
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={this.props.intl.formatMessage(messages.timeOpen)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("hours_open", {
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
                    })(
                      <TimePicker
                        style={{ width: "100%" }}
                        format="HH:mm"
                        disabled={!!record || !isEditting}
                      />
                    )
                  ) : (
                    <span style={{ fontWeight: 400 }}>{record.hours_open}</span>
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={this.props.intl.formatMessage(messages.timeClose)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("hours_close", {
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
                    })(
                      <TimePicker
                        style={{ width: "100%" }}
                        format="HH:mm"
                        disabled={!!record || !isEditting}
                      />
                    )
                  ) : (
                    <span style={{ fontWeight: 400 }}>
                      {record.hours_close}
                    </span>
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={this.props.intl.formatMessage(
                    messages.timeWaitApprove2
                  )}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("timeout_pay_request", {
                      initialValue: record ? record.timeout_pay_request : 30,
                      rules: [
                        {
                          required: true,
                          message: this.props.intl.formatMessage(
                            messages.limitTimeToApprove
                          ),
                        },
                      ],
                    })(
                      <InputNumberFormat
                        min={1}
                        style={{ width: "100%" }}
                        maxLength={10}
                      />
                    )
                  ) : (
                    <span style={{ fontWeight: 400 }}>
                      {record.timeout_pay_request}{" "}
                      {formatMessage(messages.minute, {
                        total: record.timeout_pay_request,
                      })}
                    </span>
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={this.props.intl.formatMessage(messages.timeToCancel2)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("timeout_cancel_book", {
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
                    })(
                      <InputNumberFormat
                        style={{ width: "100%" }}
                        maxLength={10}
                        min={1}
                      />
                    )
                  ) : (
                    <span style={{ fontWeight: 400 }}>
                      {record.timeout_cancel_book}{" "}
                      {this.props.intl.formatMessage(messages.minute, {
                        total: record.timeout_cancel_book,
                      })}
                    </span>
                  )}
                </Form.Item>

                <Divider style={{ margin: "8px 0" }} />

                <Form.Item
                  label={this.props.intl.formatMessage(messages.books)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("limit_book_apartment", {
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
                    })(
                      <InputNumberFormat
                        min={1}
                        style={{ width: "100%" }}
                        maxLength={10}
                      />
                    )
                  ) : (
                    <span style={{ fontWeight: 400 }}>
                      {this.props.intl.formatMessage(messages.turn, {
                        total: record.limit_book_apartment,
                      })}
                    </span>
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={this.props.intl.formatMessage(messages.deposit)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("deposit_money", {
                      initialValue: record ? record.deposit_money : 0,
                      rules: [
                        {
                          required: true,
                          message: this.props.intl.formatMessage(
                            messages.emptyDeposit
                          ),
                        },
                      ],
                    })(
                      <InputNumberFormat
                        style={{ width: "100%" }}
                        maxLength={10}
                      />
                    )
                  ) : (
                    <span style={{ fontWeight: 400 }}>
                      {formatPrice(record.deposit_money)} đ
                    </span>
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
                <Form.Item
                  label={this.props.intl.formatMessage(messages.numberContact)}
                  {...formItem}
                >
                  {isEditting ? (
                    getFieldDecorator("hotline", {
                      initialValue: record ? record.hotline : "",
                      rules: [
                        {
                          required: true,
                          message: this.props.intl.formatMessage(
                            messages.emptyNumberContact
                          ),
                        },
                      ],
                    })(
                      <PhoneNumberInput
                        maxLength={11}
                        disabled={!!record || !isEditting}
                      />
                    )
                  ) : (
                    <span style={{ fontWeight: 400 }}>{record.hotline}</span>
                  )}
                </Form.Item>
                <Divider style={{ margin: "8px 0" }} />
              </Form>
            </Col>
            <Col lg={6} md={24} sm={4} style={{ marginBottom: 300 }}>
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
                  {isEditting ? (
                    <Avatar
                      imageUrl={getFullLinkImage(this.state.imageUrl)}
                      onUploaded={(url) => this.setState({ imageUrl: url })}
                      disabled={!isEditting}
                    />
                  ) : this.state.imageUrl ? (
                    <Avatar
                      imageUrl={getFullLinkImage(this.state.imageUrl)}
                      onUploaded={(url) => this.setState({ imageUrl: url })}
                      disabled={!isEditting}
                    />
                  ) : (
                    <Empty
                      style={{
                        alignSelf: "center",
                        width: 200,
                      }}
                      description={"Chưa có ảnh avatar"}
                      image="https://gw.alipayobjects.com/zos/antfincdn/ZHrcdLPrvN/empty.svg"
                    />
                  )}
                </Col>
              </Row>
            </Col>
            <WithRole
              roles={[config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT]}
            >
              <Col lg={16} md={24}>
                <Col>
                  {isEditting && (
                    <>
                      <Button
                        ghost
                        type="danger"
                        style={{ width: 150 }}
                        disabled={loading}
                        onClick={(e) => {
                          Modal.confirm({
                            autoFocusButton: null,
                            title: this.props.intl.formatMessage(
                              messages.confirmCancel
                            ),
                            okText: this.props.intl.formatMessage(
                              messages.agree
                            ),
                            okType: "danger",
                            cancelText: this.props.intl.formatMessage(
                              messages.cancel
                            ),
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
                        ghost
                        loading={loading}
                        type="primary"
                        style={{ width: 150, marginLeft: 10 }}
                        onClick={this._onSave}
                      >
                        {this.props.intl.formatMessage(messages.update)}
                      </Button>
                    </>
                  )}
                  {!isEditting && (
                    <Button
                      ghost
                      loading={loading}
                      type="primary"
                      style={{ width: 150 }}
                      onClick={() =>
                        this.props.history.push(
                          `/main/setting/service/detail/utility-free/edit/${record.id}`,
                          { record }
                        )
                      }
                    >
                      {this.props.intl.formatMessage(messages.edit)}
                    </Button>
                  )}
                </Col>
              </Col>
            </WithRole>
          </Row>
        )}
      </Page>
    );
  }
}

DetailUltilityPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  DetailUltilityPage: makeSelectDetailUltilityPage(),
  utilityFreeServiceContainer: makeSelectUtilityFreeServiceContainer(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "DetailUltilityPage", reducer });
const withSaga = injectSaga({ key: "DetailUltilityPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(DetailUltilityPage)));
