/**
 *
 * SupplierAdd
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page/Page";
import _ from "lodash";
import {
  Row,
  Form,
  Input,
  Col,
  Select,
  Button,
  DatePicker,
  Icon,
  Radio,
} from "antd";
import {
  createSupplierAction,
  fetchSupplierDetailAction,
  updateSupplierAction,
} from "./actions";
import Upload from "../../../components/Uploader";

import { Redirect, withRouter } from "react-router";
import makeSelectSupplierAdd from "./selectors";

import { defaultAction } from "./actions";
import { validateEmail, isVnPhone } from "../../../utils";

import config from "../../../utils/config";
import moment from "moment";
import { injectIntl } from "react-intl";
import messages from "../messages";
import { getFullLinkImage } from "../../../connection";
import PhoneNumberInput from "../../../components/PhoneNumberInput";
import styles from "./index.less";
import { regexVNCharacter } from "utils/constants";
const formItemLayout = {
  labelAlign: "left",
  labelCol: {
    xs: { span: 24 },
    sm: { span: 4 },
    md: { span: 6 },
    lg: { span: 6 },
    xl: { span: 4 },
  },
  wrapperCol: {
    xs: { span: 24 },
    sm: { span: 16 },
    md: { span: 16 },
    lg: { span: 12 },
  },
};

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class SupplierAdd extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      record: (props.location.state || {}).record,
      fileList: [],
      uploadFileError: false,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    if (this.props.match.params.id !== undefined) {
      this.props.dispatch(
        fetchSupplierDetailAction({
          id: this.props.match.params.id,
        })
      );
    }
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.supplierAdd.detail.data != nextProps.supplierAdd.detail.data
    ) {
      this.setState({
        record: nextProps.supplierAdd.detail.data,
        fileList: nextProps.supplierAdd.detail.data.attach.fileList,
      });
    }
  }

  handlerCancel = () => {
    this.props.history.push("/main/contractor/list");
  };

  handlerUpdate = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      console.log("values", {
        ...values,
        id: this.state.record.id,
        attach: {
          fileList: this.state.fileList,
        },
      });
      dispatch(
        updateSupplierAction({
          ...values,
          id: this.state.record.id,
          attach: {
            fileList: this.state.fileList,
          },
        })
      );
    });
  };

  handleOk = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      dispatch(
        createSupplierAction({
          ...values,
          status: 1,
          attach: {
            fileList: this.state.fileList,
          },
        })
      );
    });
  };

  render() {
    const { supplierAdd } = this.props;
    const { getFieldDecorator, getFieldValue } = this.props.form;
    const { creating, updating, detail, success, updateSuccess } = supplierAdd;
    const { record, fileList } = this.state;
    console.log("record", fileList);
    const formatMessage = this.props.intl.formatMessage;
    if (success || updateSuccess) {
      return <Redirect to="/main/contractor/list" />;
    }
    return (
      <Page loading={detail.loading} inner={!detail.loading}>
        <Row gutter={24} style={{ marginTop: 40 }}>
          <Form {...formItemLayout} onSubmit={this.handleSubmit}>
            <Col>
              <Form.Item label={formatMessage(messages.supplierName)}>
                {getFieldDecorator("name", {
                  initialValue: record && record.name,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptySupplierName),
                      whitespace: true,
                    },
                  ],
                })(<Input style={{ width: "100%" }} maxLength={255} />)}
              </Form.Item>
              {record && (
                <Form.Item label={formatMessage(messages.status)}>
                  {getFieldDecorator("status", {
                    initialValue: record && record.status,
                  })(
                    <Radio.Group>
                      <Radio value={0}>
                        {formatMessage(messages.inactive)}
                      </Radio>
                      <Radio value={1}>{formatMessage(messages.active)}</Radio>
                    </Radio.Group>
                  )}
                </Form.Item>
              )}
              <Form.Item label={formatMessage(messages.address)}>
                {getFieldDecorator("address", {
                  initialValue: record ? record.address : "",
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptyAddress),
                      whitespace: true,
                    },
                    {
                      pattern:
                        /^[a-zA-Z0-9ÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹý ,./-]+$/,
                      message: `${formatMessage(
                        messages.address
                      )} ${formatMessage(messages.invalid)}`,
                    },
                  ],
                })(<Input style={{ width: "100%" }} maxLength={255} />)}
              </Form.Item>

              <Form.Item label={formatMessage(messages.description)}>
                {getFieldDecorator("description", {
                  initialValue: record ? record.description : "",
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptyDescription),
                      whitespace: true,
                    },
                  ],
                })(
                  <Input.TextArea
                    style={{ width: "100%" }}
                    maxLength={1000}
                    rows={4}
                  />
                )}
              </Form.Item>

              <Form.Item
                label={formatMessage(messages.attachFile)}
                validateStatus={
                  this.state.uploadFileError ? "error" : "success"
                }
                help={
                  this.state.uploadFileError
                    ? formatMessage(messages.exceedFile)
                    : ""
                }
              >
                {getFieldDecorator("attach", {
                  rules: [{ type: "object" }],
                })(
                  <Upload
                    className="upload-list-inline"
                    showUploadList={{
                      showRemoveIcon: true,
                      showDownloadIcon: false,
                    }}
                    notCheck
                    fileList={fileList}
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
                    onRemove={(file) => {
                      this.setState({
                        fileList: fileList.filter((ff) => ff.uid !== file.uid),
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
                        fileList: fileList.concat([
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
                    <Button disabled={fileList.length >= 5}>
                      <Icon type="upload" /> {formatMessage(messages.upload)}
                    </Button>
                    <span style={{ marginLeft: 8 }}>
                      ({formatMessage(messages.ruleFile)})
                    </span>
                  </Upload>
                )}
              </Form.Item>
              <strong style={{ fontSize: 16 }}>
                {formatMessage(messages.informationContact)}
              </strong>
              <Form.Item
                label={formatMessage(messages.fullName)}
                style={{ marginTop: 16 }}
              >
                {getFieldDecorator("contact_name", {
                  initialValue: record && record.contact_name,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptyFullName),
                      whitespace: true,
                    },
                    {
                      pattern: regexVNCharacter,
                      message: `${formatMessage(
                        messages.fullName
                      )} ${formatMessage(messages.invalid)}`,
                    },
                  ],
                })(<Input style={{ width: "100%" }} maxLength={50} />)}
              </Form.Item>
              <Form.Item label={formatMessage(messages.phone)}>
                {getFieldDecorator("contact_phone", {
                  initialValue: record && `0${record.contact_phone.slice(-9)}`,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptyPhone),
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (value && value.trim() != "" && !isVnPhone(value)) {
                          callback(formatMessage(messages.formatPhone));
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(<PhoneNumberInput maxLength={10} />)}
              </Form.Item>
              <Form.Item label={"Email"}>
                {getFieldDecorator("contact_email", {
                  initialValue: record && record.contact_email,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptyEmail),
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (
                          value &&
                          value.trim() != "" &&
                          !validateEmail(value)
                        ) {
                          callback(formatMessage(messages.formatEmail));
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(<Input style={{ width: "100%" }} maxLength={50} />)}
              </Form.Item>
            </Col>
          </Form>
          <Col offset={8} style={{ paddingLeft: 0 }}>
            <Button
              disabled={updating}
              style={{}}
              ghost
              type="danger"
              onClick={this.handlerCancel}
            >
              {formatMessage(messages.cancel)}
            </Button>
            {!record && (
              <Button
                ghost
                type="primary"
                style={{ marginLeft: 10 }}
                onClick={this.handleOk}
                loading={creating}
              >
                {formatMessage(messages.add)}
              </Button>
            )}
            {!!record && (
              <>
                <Button
                  loading={updating}
                  style={{ marginLeft: 10 }}
                  ghost
                  type="primary"
                  onClick={this.handlerUpdate}
                >
                  {formatMessage(messages.update)}
                </Button>
              </>
            )}
          </Col>
        </Row>
      </Page>
    );
  }
}

SupplierAdd.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  supplierAdd: makeSelectSupplierAdd(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "supplierAdd", reducer });
const withSaga = injectSaga({ key: "supplierAdd", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(SupplierAdd)));
