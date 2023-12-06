/**
 *
 * NotificationAdd
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
  Tag,
  Tooltip,
  TreeSelect,
} from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectNotificationAdd from "./selectors";

import htmlToDraft from "html-to-draftjs";
import { parseTree } from "../../../utils";
import {
  createNotificationAddReminder,
  defaultAction,
  fetchAllAnnouncementFeeTemplate,
  fetchAnnouncementFeeTemplate,
  fetchApartmentSent,
  fetchBuildingAreaAction,
  fetchCategory,
} from "./actions";

import DraftEditor from "components/Editor/Editor";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { ContentState, EditorState, convertToRaw } from "draft-js";
import draftToHtml from "draftjs-to-html";
import _ from "lodash";
import moment from "moment";
import { FormattedMessage, injectIntl } from "react-intl";
import { Redirect } from "react-router";
import Upload from "../../../components/Uploader";
import { selectBuildingCluster } from "../../../redux/selectors";
import { GLOBAL_COLOR, removeAccents } from "../../../utils/constants";
import messages from "../messages";
import { emailReg } from "./constants";
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
    return (
      <TreeSelect
        {...tProps}
        value={this.props.value}
        onChange={(value) => {
          if (value.length > 0) {
            this.props.selectBuildingArea(value);
            this.props.dispatch(
              fetchApartmentSent({
                building_area_ids: value.toString(),
                targets: this.props.sendTargets.toString(),
                page: 1,
                pageSize: 200000,
              })
            );
          }
          this.props.onChange(value);
        }}
      />
    );
  }
}

const RowTreeSelect = injectIntl(RowTree);

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class NotificationAdd extends React.PureComponent {
  constructor(props) {
    super(props);
    const { record } = props.location.state || {};

    this.state = {
      record,
      editorState: EditorState.createEmpty(),
      treeData: parseTree(
        props.buildingCluster.data,
        props.notificationAdd.buildingArea.lst.map((node) => ({
          key: `${node.id}`,
          title: node.name,
          value: `${node.id}`,
          ...node,
          children: [],
        }))
      ),
      current: 1,
      prevType: 1,
      pushType: [],
      formula: 0,
      fileImageList: [],
      fileList: [],
      is_event: false,
      is_survey: false,
      is_send_at: 0,
      buildingAreaList: [],
      phone_list: [],
      inputPhoneVisible: false,
      inputPhoneValue: "",
      editInputPhoneIndex: -1,
      editInputPhoneValue: "",
      email_list: [],
      inputEmailVisible: false,
      inputEmailValue: "",
      editInputEmailIndex: -1,
      editInputEmailValue: "",
      apartment_ids: [],
      selectedRow: [],
      apartment_not_send_ids: [],
      searchText: "",
      searchedColumn: "",
      isLoadingCreate: false,
      isLoadingDraft: false,
      inputEmailValueError: false,
      uploadImageError: false,
      uploadFileError: false,
      showChooseTemplate: false,
      dataSource: [],
      apartmentSearch: "",
      residentSearch: "",
      template_id: 999,
      resident_user_phones: [],
    };
  }

  onEditorStateChange = (editorState) => {
    if (
      draftToHtml(convertToRaw(editorState.getCurrentContent())).includes(
        "https://drive.google.com/file/d/"
      )
    ) {
      let currentContent = draftToHtml(
        convertToRaw(editorState.getCurrentContent())
      )
        .replaceAll(
          "https://drive.google.com/file/d/",
          "https://drive.google.com/uc?export=view&id="
        )
        .replaceAll("/view?usp=sharing", "")
        .replaceAll("/view?usp=drive_link", "");
      let blockArray = htmlToDraft(currentContent);
      this.setState({
        editorState: EditorState.createWithContent(
          ContentState.createFromBlockArray(blockArray.contentBlocks)
        ),
      });
    } else {
      this.setState({
        editorState,
      });
    }
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(
      fetchCategory({
        type: this.props.match.params.id == "6" ? 2 : 0,
        pageSize: 100,
      })
    );
    this.props.dispatch(fetchBuildingAreaAction());
    this.props.dispatch(
      fetchAllAnnouncementFeeTemplate({ type: this.props.match.params.id })
    );
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.notificationAdd.template.loading !=
        nextProps.notificationAdd.template.loading &&
      !nextProps.notificationAdd.template.loading
    ) {
      const { setFields } = this.props.form;
      if (
        nextProps.notificationAdd.template.data &&
        this.state.template_id != 999
      ) {
        let blockArray = htmlToDraft(
          nextProps.notificationAdd.template.data.content_email
        );
        setFields({
          title: {
            value: nextProps.notificationAdd.template.data.name,
          },
          title_en: {
            value: nextProps.notificationAdd.template.data.name_en,
          },
          content_sms: {
            value: nextProps.notificationAdd.template.data.content_sms,
          },
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
          content_sms: {
            value: "",
          },
        });
        this.setState({
          editorState: EditorState.createEmpty(),
        });
      }
    }

    if (
      this.props.notificationAdd.buildingArea.loading !=
        nextProps.notificationAdd.buildingArea.loading &&
      !nextProps.notificationAdd.buildingArea.loading
    ) {
      this.setState({
        treeData: parseTree(
          this.props.buildingCluster.data,
          nextProps.notificationAdd.buildingArea.lst.map((node) => ({
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
      this.props.notificationAdd.apartmentToSend.loading !=
        nextProps.notificationAdd.apartmentToSend.loading &&
      !nextProps.notificationAdd.apartmentToSend.loading
    ) {
      // if (nextProps.notificationAdd.apartmentToSend.data.length) {
      this.setState({
        apartment_ids: nextProps.notificationAdd.apartmentToSend.data.map(
          (row) => {
            return row.apartment_id;
          }
        ),
        selectedRow: nextProps.notificationAdd.apartmentToSend.data.map(
          (row) => {
            return row.id;
          }
        ),
        dataSource: nextProps.notificationAdd.apartmentToSend.data,
      });
      // }
    }
    if (this.props.match.params.id === "6") {
      this.setState({
        is_survey: true,
      });
    } else {
      this.setState({
        is_survey: false,
      });
    }
  }

  getApartmentIds = () => {
    const { notificationAdd } = this.props;
    const { apartment_ids, apartment_not_send_ids } = this.state;
    if (apartment_ids.length === notificationAdd.apartmentToSend.data.length) {
      return [];
    }
    if (apartment_not_send_ids.length >= apartment_ids.length) {
      return apartment_ids;
    }
    return [];
  };

  getApartmentNotSendIds = () => {
    const { notificationAdd } = this.props;

    const { apartment_ids, apartment_not_send_ids } = this.state;
    if (apartment_ids.length === notificationAdd.apartmentToSend.data.length) {
      return [];
    }
    if (apartment_ids.length >= apartment_not_send_ids.length) {
      return apartment_not_send_ids;
    }
    return [];
  };

  handleOk = (status, message) => {
    const { dispatch, form, intl } = this.props;
    const { validateFieldsAndScroll, setFields } = form;
    const modalConfirm1Text = intl.formatMessage({
      ...messages.modalConfirm1,
    });
    const continueText = intl.formatMessage({
      ...messages.continue,
    });
    const cancelText = intl.formatMessage({
      ...messages.cancel,
    });
    const confirmText = intl.formatMessage({
      ...messages.confirm,
    });

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
            new Error(intl.formatMessage({ ...messages.contentRequired })),
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
      const isAfterSendAt =
        this.state.is_send_at == 1 &&
        values.send_at &&
        values.send_at.unix() < moment().unix();
      const isAfterSendEventAt =
        this.state.is_event &&
        values.send_event_at &&
        values.send_event_at.unix() < moment().add(1, "days").unix();
      const isAfterSurveyDeadline =
        this.state.is_survey &&
        values.survey_deadline &&
        values.survey_deadline.unix() < moment().add(1, "days").unix();

      if (isAfterSendAt) {
        setFields({
          send_at: {
            value: values.send_at,
            errors: [
              new Error(intl.formatMessage({ ...messages.isAfterSendAt })),
            ],
          },
        });
      }

      if (isAfterSendEventAt) {
        setFields({
          send_event_at: {
            value: values.send_event_at,
            errors: [
              new Error(intl.formatMessage({ ...messages.isAfterSendEventAt })),
            ],
          },
        });
      }

      if (isAfterSurveyDeadline) {
        setFields({
          survey_deadline: {
            value: values.survey_deadline,
            errors: [
              new Error(
                intl.formatMessage({ ...messages.isAfterSurveyDeadline })
              ),
            ],
          },
        });
      }

      if (
        errors ||
        isErrorContent ||
        isAfterSendAt ||
        isAfterSendEventAt ||
        isAfterSurveyDeadline
      ) {
        return;
      }

      let pushType = {};
      values.pushType.forEach((key) => {
        pushType[key] = 1;
      });

      const formData = {
        ...values,
        type: 0,
        content: draftToHtml(contentRaw),
        status,
        attach: {
          fileImageList: this.state.fileImageList,
          fileList: this.state.fileList,
        },
        is_event: this.state.is_event ? 1 : 0,
        is_survey: this.props.match.params.id === "6" ? 1 : 0,
        survey_deadline: this.state.is_survey
          ? values.survey_deadline.unix()
          : undefined,
        send_at:
          this.state.is_send_at == 1 &&
          values.send_at &&
          values.send_at.unix() > moment().unix()
            ? values.send_at.unix()
            : moment().unix(),
        send_event_at:
          this.state.is_event &&
          values.send_event_at.unix() > moment().add(1, "days").unix()
            ? values.send_event_at.unix()
            : moment().add(1, "days").unix(),
        content_sms: values.pushType.includes("is_send_sms")
          ? values.content_sms
          : "",
        ...pushType,
        pushType: undefined,
        building_area_ids: this.state.buildingAreaList,
        apartment_ids: this.getApartmentIds(),
        apartment_not_send_ids: this.getApartmentNotSendIds(),
        resident_user_phones: this.state.resident_user_phones,
        add_phone_send: values.pushType.includes("is_send_sms")
          ? this.state.phone_list
          : [],
        add_email_send: values.pushType.includes("is_send_email")
          ? this.state.email_list
          : [],
        message,
      };

      if (status == 1) {
        Modal.confirm({
          autoFocusButton: null,
          title: confirmText,
          content: modalConfirm1Text,
          okText: continueText,
          cancelText: cancelText,
          centered: true,
          onOk: () => {
            this.setState({ isLoadingCreate: true });
            dispatch(
              //XXX : 1 Dispatch action to create notification
              createNotificationAddReminder(formData)
              // {
              //   ...formData,
              //   status:
              //     (this.state.is_send_at == 1 &&
              //       values.send_at &&
              //       values.send_at.unix() > moment().unix()) ||
              //     (this.state.is_event &&
              //       values.send_event_at.unix() >
              //         moment().add(1, "days").unix())
              //       ? 2
              //       : 1,
              // }
            );
            setTimeout(() => {
              this.setState({ isLoadingCreate: false });
            }, 3000);
          },
        });
      } else {
        this.setState({ isLoadingDraft: true });
        dispatch(createNotificationAddReminder(formData));
        setTimeout(() => {
          this.setState({ isLoadingDraft: false });
        }, 3000);
      }
    });
  };

  validateEmail = (email_list) => {
    if (email_list.some((email) => !emailReg.test(email))) {
      this.setState({ inputEmailValueError: true });
    } else {
      this.setState({ inputEmailValueError: false });
    }
  };

  handleClosePhone = (removedTag) => {
    const phone_list = this.state.phone_list.filter(
      (tag) => tag !== removedTag
    );
    this.setState({ phone_list });
  };

  handleCloseEmail = (removedTag) => {
    const email_list = this.state.email_list.filter(
      (tag) => tag !== removedTag
    );
    this.validateEmail(email_list);
    this.setState({ email_list });
  };

  showPhoneInput = () => {
    this.setState({ inputPhoneVisible: true }, () => this.input.focus());
  };

  showEmailInput = () => {
    this.setState({ inputEmailVisible: true }, () => this.inputEmail.focus());
  };

  handleInputPhoneChange = (e) => {
    const { value } = e.target;
    const reg = /^-?([0-9][0-9]*)?$/;
    if (
      (!Number.isNaN(value) && reg.test(value)) ||
      value === "" ||
      value === "-"
    ) {
      this.setState({ inputPhoneValue: e.target.value });
    }
  };

  handleInputEmailChange = (e) => {
    this.setState({ inputEmailValue: e.target.value });
  };

  handleInputPhoneConfirm = () => {
    const { inputPhoneValue } = this.state;
    let { phone_list } = this.state;
    if (inputPhoneValue && phone_list.indexOf(inputPhoneValue) === -1) {
      phone_list = [...phone_list, inputPhoneValue];
    }
    this.setState({
      phone_list,
      inputPhoneVisible: false,
      inputPhoneValue: "",
    });
  };

  handleInputEmailConfirm = () => {
    const { inputEmailValue } = this.state;
    let { email_list } = this.state;
    if (inputEmailValue && email_list.indexOf(inputEmailValue) === -1) {
      email_list = [...email_list, inputEmailValue];
    }
    this.setState({
      email_list,
      inputEmailVisible: false,
      inputEmailValue: "",
    });
    this.validateEmail(email_list);
  };

  handleEditInputChange = (e) => {
    this.setState({ editInputPhoneValue: e.target.value });
  };

  handleEditInputEmailChange = (e) => {
    this.setState({ editInputEmailValue: e.target.value });
  };

  handleEditInputConfirm = () => {
    this.setState(
      ({ phone_list, editInputPhoneIndex, editInputPhoneValue }) => {
        const newTags = [...phone_list];
        newTags[editInputPhoneIndex] = editInputPhoneValue;

        return {
          phone_list: newTags,
          editInputPhoneIndex: -1,
          editInputPhoneValue: "",
        };
      }
    );
  };

  handleEditInputEmailConfirm = () => {
    this.setState(
      ({ email_list, editInputEmailIndex, editInputEmailValue }) => {
        const newTags = [...email_list];
        newTags[editInputEmailIndex] = editInputEmailValue;

        return {
          email_list: newTags,
          editInputEmailIndex: -1,
          editInputEmailValue: "",
        };
      }
    );
  };

  saveInputRef = (input) => (this.input = input);
  saveEditInputRef = (input) => (this.editInput = input);

  saveInputEmailRef = (inputEmail) => (this.inputEmail = inputEmail);
  saveEditInputEmailRef = (inputEmail) => (this.editInputEmail = inputEmail);

  onSelectChange = (selectedRowKeys, selectedRows) => {
    const { notificationAdd } = this.props;
    const new_apartment_ids = selectedRows
      .map((row) => {
        return row.apartment_id;
      })
      .filter((value, index, self) => self.indexOf(value) === index);
    this.setState({
      apartment_ids: new_apartment_ids,
      apartment_not_send_ids: notificationAdd.apartmentToSend.data
        .map((opt) => opt.apartment_id)
        .filter(
          (row, index, self) =>
            !new_apartment_ids.includes(row) && self.indexOf(row) === index
        ),
      resident_user_phones: notificationAdd.apartmentToSend.data
        .filter((row) => !selectedRowKeys.includes(row.id))
        .map((opt) => opt.phone),
      selectedRow: selectedRows.map((row) => {
        return row.id;
      }),
    });
  };

  render() {
    const { notificationAdd, intl, dispatch, language } = this.props;
    const notiType = this.props.match.params.id;
    const apartmentText = intl.formatMessage({
      ...messages.property,
    });
    const residentText = intl.formatMessage({
      ...messages.resident,
    });
    const chooseCategoryPlaceholderText = intl.formatMessage({
      ...messages.chooseCategoryPlaceholder,
    });
    const surveyDeadlinePlaceholderText = intl.formatMessage({
      ...messages.surveyDeadlinePlaceholder,
    });
    const saveDraftSuccessText = intl.formatMessage({
      ...messages.saveDraftSuccess,
    });
    const createNotificationSuccessText = intl.formatMessage({
      ...messages.createNotificationSuccess,
    });
    const chooseTimePlaceholderText = intl.formatMessage({
      ...messages.chooseTimePlaceholder,
    });

    if (notificationAdd.createSuccess) {
      return <Redirect to="/main/notification/list" />;
    }
    const { getFieldDecorator, getFieldsError, getFieldValue, setFieldsValue } =
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
              notificationAdd.apartmentToSend.loading
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
        // ...this.getColumnSearchProps("apartment_name"),
      },
      {
        title: (
          <span>
            <FormattedMessage {...messages.resident} />
          </span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
        // ...this.getColumnSearchProps("resident_user_name"),
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
        title: <span>App</span>,
        dataIndex: "app",
        key: "app",
        render: (text, record) => {
          if (!record.app) {
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
      //   title: <span>SMS</span>,
      //   dataIndex: "phone",
      //   key: "phone",
      //   render: (text, record) => {
      //     if (!record.phone) {
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
      //     return text;
      //   },
      // },
    ];

    const { category, template_list, template } = notificationAdd;

    const {
      editorState,
      treeData,
      prevType,
      phone_list,
      inputPhoneVisible,
      inputPhoneValue,
      editInputPhoneIndex,
      editInputPhoneValue,
      email_list,
      inputEmailVisible,
      inputEmailValue,
      editInputEmailIndex,
      editInputEmailValue,
      apartmentSearch,
      residentSearch,
      dataSource,
    } = this.state;

    console.log("this.state", this.state);

    const errorCurrent = getFieldsError(["content"]);
    let announcement_category = category.data.find(
      (cc) => cc.id == getFieldValue("announcement_category_id")
    );
    const apartmentSelection = {
      selectedRowKeys: this.state.selectedRow,
      onChange: this.onSelectChange,
    };
    return (
      <Page inner>
        <Row className="NotificationAdd">
          <Col span={24} style={{ overflowY: "auto" }}>
            <Row
              type="flex"
              justify="center"
              style={{ marginTop: 24, marginBottom: 24 }}
            >
              <Col md={20} lg={16} xl={14}>
                <Steps size="default" current={0}>
                  <Steps.Step
                    title={<FormattedMessage {...messages.createNew} />}
                  />
                  <Steps.Step
                    title={<FormattedMessage {...messages.saveDraft} />}
                  />
                  <Steps.Step
                    title={<FormattedMessage {...messages.public} />}
                  />
                </Steps>
              </Col>
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
                    defaultValue={999}
                    // value={template.data ? template.data.id : undefined}
                    onSelect={(value) => {
                      this.setState({
                        template_id: value,
                      });
                      dispatch(fetchAnnouncementFeeTemplate({ id: value }));
                    }}
                    style={{
                      minWidth: 200,
                    }}
                  >
                    <Select.Option key={999} value={999}>
                      <FormattedMessage {...messages.newsTemplate} />
                    </Select.Option>
                    {template_list.data.map((item) => (
                      <Select.Option
                        key={item.id}
                        value={item.id}
                        // onClick={() => {
                        //   dispatch(
                        //     fetchAnnouncementFeeTemplate({ id: item.id })
                        //   );
                        // }}
                      >
                        {language === "en" ? item.name_en : item.name}
                      </Select.Option>
                    ))}
                  </Select>
                </Row>
                <br />
                <br />
                <Form labelAlign="left" {...formItemLayout}>
                  <Form.Item label={<FormattedMessage {...messages.title} />}>
                    {getFieldDecorator("title", {
                      initialValue: "",
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
                  <Form.Item
                    required
                    label={<FormattedMessage {...messages.content} />}
                  >
                    {getFieldDecorator("content", {
                      initialValue: "",
                    })(
                      <div
                        style={{
                          border: errorCurrent.content ? "1px solid red" : "",
                        }}
                      >
                        <DraftEditor
                          editorState={editorState}
                          onEditorStateChange={this.onEditorStateChange}
                        />
                      </div>
                    )}
                  </Form.Item>
                  <Form.Item
                    label={<FormattedMessage {...messages.category} />}
                  >
                    {getFieldDecorator("announcement_category_id", {
                      rules: [
                        {
                          required: true,
                          message: (
                            <FormattedMessage {...messages.categoryRequired} />
                          ),
                          whitespace: true,
                        },
                      ],
                    })(
                      <Select
                        loading={category.loading}
                        showSearch
                        placeholder={chooseCategoryPlaceholderText}
                        optionFilterProp="children"
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
                              {this.props.language === "vi"
                                ? gr.name
                                : gr.name_en}
                            </Select.Option>
                          );
                        })}
                      </Select>
                    )}
                  </Form.Item>

                  {notiType === "6" && (
                    <Form.Item
                      label={
                        <span>
                          <FormattedMessage {...messages.surveyDeadline} />
                        </span>
                      }
                    >
                      {getFieldDecorator("survey_deadline", {
                        initialValue: moment().add(1, "days"),
                        rules: [
                          {
                            required: true,
                            message: (
                              <FormattedMessage
                                {...messages.surveyDeadlineRequired}
                              />
                            ),
                            type: "object",
                          },
                        ],
                      })(
                        <DatePicker
                          showTime
                          style={{ width: "100%" }}
                          placeholder={surveyDeadlinePlaceholderText}
                          format="HH:mm - DD/MM/YYYY"
                          // disabled={!canCreateOrUpdate || currentStep == 2}
                          disabledDate={(current) => {
                            if (getFieldValue("send_at")) {
                              // Must public it before deadline
                              return (
                                current <
                                moment(getFieldValue("send_at"))
                                  .add(1, "days")
                                  .startOf("day")
                              );
                            }
                            // Can not select days before today and today
                            return (
                              current &&
                              current < moment().add(1, "days").startOf("day")
                            );
                          }}
                          disabledTime={(current) => {
                            if (!current) return {};
                            let now = moment().add(1, "days");
                            if (
                              current > moment().add(1, "days").endOf("day")
                            ) {
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
                  )}
                  {notiType === "0" && (
                    <Form.Item
                      label={
                        <span>
                          <FormattedMessage {...messages.event} />{" "}
                          <Tooltip
                            title={
                              <FormattedMessage {...messages.eventTooltip} />
                            }
                          >
                            <Icon type="info-circle-o" />
                          </Tooltip>
                        </span>
                      }
                    >
                      <Checkbox
                        checked={this.state.is_event}
                        onChange={(value) => {
                          this.setState({
                            is_event: value.target.checked,
                          });
                        }}
                        style={{ marginRight: 10 }}
                      />
                      {this.state.is_event &&
                        getFieldDecorator("send_event_at", {
                          initialValue: moment().add(1, "days"),
                          rules: [
                            {
                              required: true,
                              message: (
                                <FormattedMessage
                                  {...messages.eventDateRequired}
                                />
                              ),
                              type: "object",
                            },
                          ],
                        })(
                          <DatePicker
                            showTime
                            format="HH:mm - DD/MM/YYYY"
                            placeholder={chooseTimePlaceholderText}
                            // disabled={!canCreateOrUpdate || currentStep == 2}
                            disabledDate={(current) => {
                              // Can not select days before today and today
                              return (
                                current &&
                                current < moment().add(1, "days").startOf("day")
                              );
                            }}
                            disabledTime={(current) => {
                              if (!current) return {};
                              let now = moment().add(1, "days");
                              if (
                                current > moment().add(1, "days").endOf("day")
                              ) {
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
                  )}
                  {!this.state.is_event && (
                    <Form.Item
                      label={<FormattedMessage {...messages.public} />}
                    >
                      <Row gutter={16}>
                        <Col md={12} xl={24}>
                          <Select
                            value={this.state.is_send_at}
                            onChange={(e) => {
                              this.setState({
                                is_send_at: e,
                              });
                            }}
                          >
                            {[
                              {
                                id: 0,
                                title: (
                                  <FormattedMessage {...messages.publicNow} />
                                ),
                              },
                              {
                                id: 1,
                                title: (
                                  <FormattedMessage {...messages.publicAt} />
                                ),
                              },
                            ].map((gr) => {
                              return (
                                <Select.Option
                                  key={`group-${gr.id}`}
                                  value={gr.id}
                                >
                                  {gr.title}
                                </Select.Option>
                              );
                            })}
                          </Select>
                        </Col>
                        <Col md={12} xl={24}>
                          {this.state.is_send_at == 1 &&
                            getFieldDecorator("send_at", {
                              initialValue: moment(),
                              rules: [
                                {
                                  required: true,
                                  message: (
                                    <FormattedMessage
                                      {...messages.publicAtRequired}
                                    />
                                  ),
                                  type: "object",
                                },
                              ],
                            })(
                              <DatePicker
                                showTime
                                format="HH:mm - DD/MM/YYYY"
                                placeholder={chooseTimePlaceholderText}
                                style={{ width: "100%" }}
                                disabledDate={(current) => {
                                  if (getFieldValue("survey_deadline")) {
                                    // Must public it before deadline
                                    return (
                                      (current &&
                                        current < moment().startOf("day")) ||
                                      current >
                                        moment(
                                          getFieldValue("survey_deadline")
                                        ).startOf("day")
                                    );
                                  }
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
                        </Col>
                      </Row>
                    </Form.Item>
                  )}
                  <Form.Item
                    validateStatus={
                      this.state.uploadImageError ? "error" : "success"
                    }
                    help={
                      this.state.uploadImageError ? (
                        <FormattedMessage {...messages.imageTooLarge} />
                      ) : (
                        ""
                      )
                    }
                    label={<FormattedMessage {...messages.attachImage} />}
                  >
                    <Upload
                      listType="picture-card"
                      showUploadList={true}
                      disabled={this.state.fileImageList.length >= 10}
                      fileList={this.state.fileImageList}
                      accept={".png, .jpg, .jpeg, .jfif"}
                      acceptList={["jpg", "jpeg", "png", "jfif"]}
                      // multiple
                      onRemove={(file) => {
                        this.setState({
                          fileImageList: this.state.fileImageList.filter(
                            (ff) => ff.uid != file.uid
                          ),
                        });
                      }}
                      beforeUpload={(file) => {
                        if (file.size / 1024 / 1024 > 10) {
                          this.setState({
                            uploadImageError: true,
                          });
                          return false;
                        } else {
                          this.setState({
                            uploadImageError: false,
                          });
                        }
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
                    validateStatus={
                      this.state.uploadFileError ? "error" : "success"
                    }
                    help={
                      this.state.uploadFileError ? (
                        <FormattedMessage {...messages.fileTooLarge} />
                      ) : (
                        ""
                      )
                    }
                    label={<FormattedMessage {...messages.attachFile} />}
                  >
                    <Upload
                      showUploadList={true}
                      fileList={this.state.fileList}
                      accept={
                        ".doc,.docx,pdf,application/pdf,xls,xlsx,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel"
                      }
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
                      // multiple
                      onRemove={(file) => {
                        this.setState({
                          fileList: this.state.fileList.filter(
                            (ff) => ff.uid != file.uid
                          ),
                        });
                      }}
                      beforeUpload={(file) => {
                        if (file.size / 1024 / 1024 > 25) {
                          this.setState({
                            uploadFileError: true,
                          });
                          return false;
                        } else {
                          this.setState({
                            uploadFileError: false,
                          });
                        }
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
                      {this.state.fileList.length < 5 && (
                        <>
                          <Button>
                            <Icon type="upload" />{" "}
                            <FormattedMessage {...messages.fileUpload} />
                          </Button>
                          <span style={{ marginLeft: 8 }}>
                            <FormattedMessage {...messages.fileUploadTooltip} />
                          </span>
                        </>
                      )}
                    </Upload>
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
                              // disabled={this.state.is_survey}
                            >
                              <FormattedMessage {...messages.sendEmail} />
                            </Checkbox>
                            <br />
                            <Checkbox
                              disabled={true}
                              value="is_send_push"
                              style={{ marginTop: 8, marginBottom: 8 }}
                            >
                              <span style={{ color: "rgba(0, 0, 0, 0.65)" }}>
                                <FormattedMessage {...messages.sendApp} />
                              </span>
                            </Checkbox>
                            <br />
                            {/* <Checkbox
                              value="is_send_sms"
                              disabled={this.state.is_survey}
                            >
                              <FormattedMessage {...messages.sendSMS} />
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
                                      fetchApartmentSent({
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
                                {/* <Checkbox
                              value={1}
                              style={{ marginTop: 8, marginBottom: 8 }}
                            >
                              <FormattedMessage {...messages.member} />
                            </Checkbox>
                            <br /> */}
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
                                    fetchApartmentSent({
                                      building_area_ids:
                                        this.state.buildingAreaList.toString() ||
                                        getFieldValue(
                                          "building_area_ids"
                                        ).toString(),
                                      targets:
                                        getFieldValue("targets").toString(),
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
                    {notiType === "6" && (
                      <Col md={12} xl={24}>
                        <Form.Item
                          label={
                            <span>
                              <FormattedMessage {...messages.formula} />{" "}
                              <Tooltip
                                title={
                                  <FormattedMessage
                                    {...messages.formulaTooltip}
                                  />
                                }
                              >
                                <Icon type="info-circle-o" />
                              </Tooltip>
                            </span>
                          }
                        >
                          {getFieldDecorator("type_report", {
                            initialValue: this.state.formula,
                            rules: [
                              {
                                required: true,
                                message: (
                                  <FormattedMessage
                                    {...messages.formulaRequired}
                                  />
                                ),
                              },
                            ],
                          })(
                            <Radio.Group
                              name="formulaGroup"
                              value={this.state.formula}
                              onChange={(e) => {
                                this.setState({
                                  formula: e.target.value,
                                });
                              }}
                            >
                              <Tooltip
                                title={
                                  <FormattedMessage
                                    {...messages.formulaTooltip1}
                                  />
                                }
                              >
                                <Radio
                                  style={{
                                    display: "block",
                                    height: "30px",
                                    lineHeight: "30px",
                                  }}
                                  value={0}
                                >
                                  <FormattedMessage {...messages.formula1} />
                                </Radio>
                              </Tooltip>
                              <Tooltip
                                title={
                                  <FormattedMessage
                                    {...messages.formulaTooltip2}
                                  />
                                }
                              >
                                <Radio
                                  style={{
                                    display: "block",
                                    height: "30px",
                                    lineHeight: "30px",
                                  }}
                                  value={1}
                                >
                                  <FormattedMessage {...messages.formula2} />
                                </Radio>
                              </Tooltip>
                            </Radio.Group>
                          )}
                        </Form.Item>
                      </Col>
                    )}
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
                        dispatch={this.props.dispatch}
                        buildingArea={this.props.notificationAdd.buildingArea}
                        selectBuildingArea={(ids) => {
                          this.setState({
                            buildingAreaList: ids,
                          });
                        }}
                        sendTargets={getFieldValue("targets")}
                      />
                    )}
                  </Form.Item>
                  {/* {(getFieldValue("pushType") || []).some(
                    (ss) => ss == "is_send_sms"
                  ) && (
                    <Form.Item
                      label={<FormattedMessage {...messages.smsContent} />}
                      colon={false}
                    >
                      {getFieldDecorator("content_sms", {
                        initialValue: "",
                        rules: [
                          {
                            required: true,
                            message: (
                              <FormattedMessage
                                {...messages.smsContentRequired}
                              />
                            ),
                          },
                        ],
                      })(
                        <Input.TextArea
                          rows={5}
                          style={{ marginTop: 8, width: "82%" }}
                        />
                      )}
                    </Form.Item>
                  )}
                  {(getFieldValue("pushType") || []).some(
                    (ss) => ss == "is_send_sms"
                  ) && (
                    <Form.Item
                      label={<FormattedMessage {...messages.phoneSend} />}
                      colon={false}
                    >
                      {getFieldDecorator("phone_list", {
                        initialValue: [],
                        rules: [],
                      })(
                        <div>
                          {phone_list.map((tag, index) => {
                            if (editInputPhoneIndex === index) {
                              return (
                                <Input
                                  ref={this.saveEditInputRef}
                                  key={tag}
                                  maxLength={12}
                                  className="tag-input"
                                  value={editInputPhoneValue}
                                  onChange={this.handleEditInputChange}
                                  onBlur={this.handleEditInputConfirm}
                                  onPressEnter={this.handleEditInputConfirm}
                                />
                              );
                            }

                            const isLongTag = tag.length > 20;

                            const tagElem = (
                              <Tag
                                className="edit-tag"
                                key={tag}
                                closable={true}
                                onClose={() => this.handleClosePhone(tag)}
                              >
                                <span
                                  onDoubleClick={(e) => {
                                    this.setState(
                                      {
                                        editInputPhoneIndex: index,
                                        editInputPhoneValue: tag,
                                      },
                                      () => {
                                        this.editInput.focus();
                                      }
                                    );
                                    e.preventDefault();
                                  }}
                                >
                                  {isLongTag ? `${tag.slice(0, 20)}...` : tag}
                                </span>
                              </Tag>
                            );
                            return isLongTag ? (
                              <Tooltip title={tag} key={tag}>
                                {tagElem}
                              </Tooltip>
                            ) : (
                              tagElem
                            );
                          })}
                          {inputPhoneVisible && (
                            <Input
                              ref={this.saveInputRef}
                              type="text"
                              maxLength={12}
                              className="tag-input"
                              value={inputPhoneValue}
                              onChange={this.handleInputPhoneChange}
                              onBlur={this.handleInputPhoneConfirm}
                              onPressEnter={this.handleInputPhoneConfirm}
                            />
                          )}
                          {!inputPhoneVisible && (
                            <Tag
                              className="site-tag-plus"
                              onClick={this.showPhoneInput}
                            >
                              <Icon type="plus" />{" "}
                              <FormattedMessage {...messages.addPhone} />
                            </Tag>
                          )}
                        </div>
                      )}
                    </Form.Item>
                  )} */}
                  {(getFieldValue("pushType") || []).some(
                    (ss) => ss == "is_send_email"
                  ) && (
                    <Form.Item
                      validateStatus={
                        this.state.inputEmailValueError ? "error" : "success"
                      }
                      help={
                        this.state.inputEmailValueError ? (
                          <FormattedMessage {...messages.emailWrongFormat} />
                        ) : (
                          ""
                        )
                      }
                      label={<FormattedMessage {...messages.emailSend} />}
                      colon={false}
                    >
                      {getFieldDecorator("email_list", {
                        initialValue: [],
                        rules: [],
                      })(
                        <div>
                          {email_list.map((tag, index) => {
                            if (editInputEmailIndex === index) {
                              return (
                                <Input
                                  ref={this.saveEditInputEmailRef}
                                  key={tag}
                                  maxLength={50}
                                  type="email"
                                  className="tag-input"
                                  value={editInputEmailValue}
                                  onChange={this.handleEditInputEmailChange}
                                  onBlur={this.handleEditInputEmailConfirm}
                                  onPressEnter={
                                    this.handleEditInputEmailConfirm
                                  }
                                />
                              );
                            }

                            const isLongEmailTag = tag.length > 50;

                            const tagEmailElem = (
                              <Tag
                                className="edit-tag"
                                key={tag}
                                closable={true}
                                onClose={() => this.handleCloseEmail(tag)}
                              >
                                <span
                                  onDoubleClick={(e) => {
                                    this.setState(
                                      {
                                        editInputEmailIndex: index,
                                        editInputEmailValue: tag,
                                      },
                                      () => {
                                        this.editInputEmail.focus();
                                      }
                                    );
                                    e.preventDefault();
                                  }}
                                >
                                  {isLongEmailTag
                                    ? `${tag.slice(0, 50)}...`
                                    : tag}
                                </span>
                              </Tag>
                            );
                            return isLongEmailTag ? (
                              <Tooltip title={tag} key={tag}>
                                {tagEmailElem}
                              </Tooltip>
                            ) : (
                              tagEmailElem
                            );
                          })}
                          {inputEmailVisible && (
                            <Input
                              ref={this.saveInputEmailRef}
                              type="email"
                              maxLength={50}
                              className="tag-input"
                              value={inputEmailValue}
                              onChange={this.handleInputEmailChange}
                              onBlur={this.handleInputEmailConfirm}
                              onPressEnter={this.handleInputEmailConfirm}
                            />
                          )}
                          {!inputEmailVisible && (
                            <Tag
                              className="site-tag-plus"
                              onClick={this.showEmailInput}
                            >
                              <Icon type="plus" />{" "}
                              <FormattedMessage {...messages.addEmail} />
                            </Tag>
                          )}
                        </div>
                      )}
                    </Form.Item>
                  )}
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
                    {prevType == 0 && (
                      <div className={"webPreview"}>
                        <div style={{ height: "100%", overflowY: "scroll" }}>
                          <div
                            className="dangerouslySetInnerHTMLWeb"
                            dangerouslySetInnerHTML={{
                              __html: draftToHtml(
                                convertToRaw(editorState.getCurrentContent())
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
                                convertToRaw(editorState.getCurrentContent())
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
                            {(getFieldValue("content_sms") || "")
                              .replace(/{{RESIDENT_NAME}}/g, "Nguyễn Văn A")
                              .replace(/{{APARTMENT_NAME}}/g, "TSQ.T1007")
                              .replace(/{{TOTAL_FEE}}/g, "2.000.000 VNĐ")}
                          </Row>
                        </div>
                      </div>
                    )} */}
                  </Col>
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
                  {notificationAdd.apartmentToSend.total_count
                    .total_apartment || 0}
                </span>
              </Col>
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>Email</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationAdd.apartmentToSend.total_count.total_email || 0}
                </span>
              </Col>
              <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>App</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationAdd.apartmentToSend.total_count.total_app || 0}
                </span>
              </Col>
              {/* <Col style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16 }}>SMS</span>
                <br />
                <span
                  style={{ fontWeight: "bold", fontSize: 28, lineHeight: 2 }}
                >
                  {notificationAdd.apartmentToSend.total_count.total_sms || 0}
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
                      dataSource: notificationAdd.apartmentToSend.data.filter(
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
              locale={{
                emptyText: <FormattedMessage {...messages.noData} />,
              }}
              loading={notificationAdd.apartmentToSend.loading}
              pagination={{
                pageSize: 10,
                total: notificationAdd.apartmentToSend.totalPage,
                current: this.state.current,
                showTotal: (total) => (
                  <FormattedMessage {...messages.total} values={{ total }} />
                ),
              }}
              scroll={{ x: 1000 }}
              onChange={this.handleTableChange}
              rowSelection={apartmentSelection}
            />
          </Col>
          <Col span={24} style={{ marginTop: 24 }}>
            <Row type="flex" align="middle" justify="center">
              <Button
                size="large"
                style={{ marginRight: 8 }}
                disabled={
                  notificationAdd.apartmentToSend.totalPage == 0 ||
                  !this.state.apartment_ids.length ||
                  this.state.isLoadingCreate ||
                  this.state.isLoadingDraft
                }
                onClick={() => {
                  this.handleOk(0, saveDraftSuccessText);
                }}
                loading={this.state.isLoadingDraft}
              >
                <FormattedMessage {...messages.saveDraft} />
              </Button>
              <Button
                type="primary"
                size="large"
                style={{ marginLeft: 8 }}
                disabled={
                  notificationAdd.apartmentToSend.totalPage == 0 ||
                  !this.state.apartment_ids.length ||
                  this.state.isLoadingCreate ||
                  this.state.isLoadingDraft
                }
                onClick={() => {
                  this.handleOk(1, createNotificationSuccessText);
                }}
                loading={this.state.isLoadingCreate}
              >
                <FormattedMessage {...messages.public} />
              </Button>
            </Row>
            {/* <ChooseNotiTemplate
              templateList={notificationAdd.template_list}
              showChooseTemplate={notificationAdd.showChooseTemplate}
              dispatch={dispatch}
              history={history}
              auth_group={auth_group}
            /> */}
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

NotificationAdd.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notificationAdd: makeSelectNotificationAdd(),
  buildingCluster: selectBuildingCluster(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "notificationAdd", reducer });
const withSaga = injectSaga({ key: "notificationAdd", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(NotificationAdd));
