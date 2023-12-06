/**
 *
 * MaintainAdd
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";
import Upload from "../../../components/Uploader";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectMaintainAdd from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "../messages";
import Page from "../../../components/Page/Page";
import {
  Row,
  Form,
  Input,
  Col,
  Select,
  Button,
  Modal,
  DatePicker,
  Icon,
} from "antd";

import {
  defaultAction,
  createEquipmentAction,
  updateEquipmentAction,
} from "./actions";
import("./index.less");
const confirm = Modal.confirm;

import { Redirect, withRouter } from "react-router";

import config from "../../../utils/config";
import moment from "moment";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 14,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
class MaintainAdd extends React.PureComponent {
  constructor(props) {
    super(props);
    let record = (props.location.state || {}).record;
    this.state = {
      record,
      imageUrl: record ? record.avatar : undefined,
      fileList:
        record && record.attach && record.attach.fileList
          ? record.attach.fileList
          : [],
      uploadFileError: false,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    const { id } = this.props.match.params;
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.maintainAdd.detail.data != nextProps.maintainAdd.detail.data
    ) {
      this.setState({
        record: nextProps.maintainAdd.detail.data,
        imageUrl: nextProps.maintainAdd.detail.data.avatar,
      });
    }
  }

  handlerCancel = () => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.state.record
        ? this.props.intl.formatMessage(messages.cancelUpdateTitle)
        : this.props.intl.formatMessage(messages.cancelAddTitle),
      okText: this.props.intl.formatMessage(messages.okText),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancelText),
      onOk: () => {
        this.props.history.push("/main/maintain/list", { pos: 1 });
      },
      onCancel() {},
    });
    //this.props.history.push("/main/maintain/list", { pos: 1 });
  };

  handlerUpdate = () => {
    const { dispatch, form, userDetail } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      dispatch(
        updateEquipmentAction({
          ...values,
          id: this.state.record.id,
          attach: {
            fileList: this.state.fileList
              ? this.state.fileList
              : this.state.record.fileList,
          },
          guarantee_time_start: values.timeMaintain[0]
            ? values.timeMaintain[0].unix()
            : this.state.record.guarantee_time_start
            ? null
            : undefined,
          guarantee_time_end: values.timeMaintain[1]
            ? values.timeMaintain[1].unix()
            : this.state.record.guarantee_time_end
            ? null
            : undefined,
          maintenance_time_start: values.maintenance_time_start
            ? values.maintenance_time_start.unix()
            : this.state.record.maintenance_time_start
            ? null
            : undefined,
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
        createEquipmentAction({
          ...values,
          status: 1,
          attach: {
            // fileListName: this.state.fileList.map((item) => item.name),
            fileList: this.state.fileList,
          },
          guarantee_time_start: values.timeMaintain[0]
            ? values.timeMaintain[0].unix()
            : undefined,
          guarantee_time_end: values.timeMaintain[1]
            ? values.timeMaintain[1].unix()
            : undefined,
          maintenance_time_start: values.maintenance_time_start
            ? values.maintenance_time_start.unix()
            : undefined,
        })
      );
    });
  };

  render() {
    const dateFormat = "YYYY/MM/DD";
    const { maintainAdd } = this.props;
    const { formatMessage } = this.props.intl;
    const { getFieldDecorator } = this.props.form;
    const { creating, success, updating, updateSuccess, detail } = maintainAdd;
    const { record } = this.state;
    if (success || updateSuccess) {
      this.props.history.push("/main/maintain/list", { pos: 1 });
      // <Redirect
      //   to="/main/maintain/list"
      //   // push={("/main/maintain/list", { pos: 1 })}
      // />
    }
    // if (detail.data == -1) {
    //   return (
    //     <Page inner>
    //       <Exception
    //         type="404"
    //         desc={formatMessage(messages.device)}
    //         actions={
    //           <Button
    //             type="primary"
    //             onClick={() =>
    //               this.props.history.push("/main/setting/building/staff/list")
    //             }
    //           >
    //             {formatMessage(messages.device)}
    //           </Button>
    //         }
    //       />
    //     </Page>
    //   );
    // }
    return (
      <Page inner loading={detail.loading}>
        <Row gutter={24} style={{ marginTop: 40 }}>
          <Col span={16}>
            <Form {...formItemLayout} onSubmit={this.handleSubmit}>
              <Col>
                <Form.Item
                  label={formatMessage(messages.deviceCode)}
                  labelAlign="left"
                >
                  {getFieldDecorator("code", {
                    initialValue: record && record.code,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.errorEmpty, {
                          field: formatMessage(messages.deviceCode),
                        }),
                        whitespace: true,
                      },
                    ],
                  })(<Input style={{ width: "100%" }} maxLength={64} />)}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.deviceType)}
                  labelAlign="left"
                >
                  {getFieldDecorator("type", {
                    initialValue: record && record.type,

                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.errorEmpty, {
                          field: formatMessage(messages.deviceType),
                        }),
                        whitespace: true,
                        type: "number",
                      },
                    ],
                  })(
                    <Select
                      allowClear
                      placeholder={this.props.intl.formatMessage(
                        messages.deviceType
                      )}
                    >
                      {config.MAINTAIN_DEVICES.map((type) => {
                        return (
                          <Select.Option key={type.id} value={type.id}>
                            {this.props.language === "en"
                              ? type.name_en
                              : type.name}
                          </Select.Option>
                        );
                      })}
                    </Select>
                  )}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.deviceName)}
                  labelAlign="left"
                >
                  {getFieldDecorator("name", {
                    initialValue: record && record.name,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.errorEmpty, {
                          field: formatMessage(messages.deviceName),
                        }),
                        whitespace: true,
                      },
                    ],
                  })(<Input maxLength={50} />)}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.location)}
                  labelAlign="left"
                >
                  {getFieldDecorator("position", {
                    initialValue: record && record.position,
                  })(<Input maxLength={50} />)}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.description)}
                  labelAlign="left"
                >
                  {getFieldDecorator("description", {
                    initialValue: record && record.description,
                  })(
                    <Input.TextArea
                      style={{ width: "100%" }}
                      rows={4}
                      maxLength={250}
                    />
                  )}
                </Form.Item>
                {record && (
                  <Form.Item
                    label={formatMessage(messages.status)}
                    labelAlign="left"
                  >
                    {getFieldDecorator("status", {
                      initialValue: (record && String(record.status)) || "1",
                      rules: [
                        {
                          required: true,
                          message: formatMessage(messages.errorEmpty, {
                            field: formatMessage(messages.status),
                          }),
                          whitespace: true,
                        },
                      ],
                    })(
                      <Select
                        allowClear
                        placeholder={this.props.intl.formatMessage(
                          messages.status
                        )}
                      >
                        <Select.Option value={"0"}>
                          {formatMessage(messages.inActive)}
                        </Select.Option>
                        <Select.Option value={"1"}>
                          {formatMessage(messages.active)}
                        </Select.Option>
                      </Select>
                    )}
                  </Form.Item>
                )}
                <Form.Item
                  label={formatMessage(messages.warrantyPeriod)}
                  labelAlign="left"
                >
                  {getFieldDecorator("timeMaintain", {
                    initialValue:
                      record && record.guarantee_time_start
                        ? [
                            moment(
                              moment.unix(record.guarantee_time_start),
                              dateFormat
                            ),
                            moment(
                              moment.unix(record.guarantee_time_end),
                              dateFormat
                            ),
                          ]
                        : "",
                  })(
                    <DatePicker.RangePicker
                      style={{ width: "100%" }}
                      placeholder={[
                        formatMessage(messages.fromDate),
                        formatMessage(messages.toDate),
                      ]}
                      format="DD/MM/YYYY"
                      // disabledDate={(current) => {
                      //   return current && current < moment().startOf("day");
                      // }}
                    />
                  )}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.timeMaintenance)}
                  labelAlign="left"
                >
                  {getFieldDecorator("maintenance_time_start", {
                    initialValue:
                      record &&
                      record.maintenance_time_start &&
                      moment(
                        moment.unix(record.maintenance_time_start),
                        dateFormat
                      ),
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.errorEmpty, {
                          field: formatMessage(messages.timeMaintenance),
                        }),
                      },
                    ],
                  })(
                    <DatePicker
                      style={{ width: "100%" }}
                      format="DD/MM/YYYY"
                      placeholder={formatMessage(messages.chooseDayPlaceholder)}
                      disabledDate={(current) => {
                        return current && current < moment().startOf("day");
                      }}
                    />
                  )}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.repeatedMaintenance)}
                  labelAlign="left"
                >
                  {getFieldDecorator("cycle", {
                    initialValue: record && record.cycle,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.errorEmpty, {
                          field: formatMessage(messages.repeatedMaintenance),
                        }),
                      },
                    ],
                  })(
                    <Select
                      allowClear
                      placeholder={this.props.intl.formatMessage(
                        messages.repeatedMaintenance
                      )}
                    >
                      {config.MAINTAIN_DEVICES_TERM.map((type) => {
                        return (
                          <Select.Option key={type.id} value={type.id}>
                            {this.props.language === "en"
                              ? type.name_en
                              : type.name}
                          </Select.Option>
                        );
                      })}
                    </Select>
                  )}
                </Form.Item>
                <Form.Item
                  labelAlign="left"
                  wrapperCol={{ span: 14 }}
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
                      className="ant-upload-list"
                      showUploadList={{
                        showDownloadIcon: false,
                      }}
                      fileList={this.state.fileList}
                      acceptList={["image/"]}
                      accept={"image/*"}
                      //multiple
                      listType="picture-card"
                      onRemove={(file) => {
                        this.setState({
                          fileList: this.state.fileList.filter(
                            (ff) => ff.uid != file.uid
                          ),
                        });
                      }}
                      beforeUpload={(file) => {
                        if (file.size / 1024 / 1024 > 10) {
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
                              type: "image/png",
                              size: file.size,
                            },
                          ]),
                        });
                      }}
                    >
                      {this.state.fileList.length < 10 && (
                        <>
                          <Icon type="plus" />
                        </>
                      )}
                    </Upload>
                  )}
                </Form.Item>
              </Col>
            </Form>
          </Col>
          <Col
            span={24}
            style={{
              display: "flex",
              justifyContent: "center",
              marginTop: 12,
            }}
          >
            <Button
              disabled={updating}
              ghost
              type="danger"
              onClick={this.handlerCancel}
            >
              {formatMessage(messages.cancelText)}
            </Button>
            {!record && (
              <Button
                ghost
                type="primary"
                style={{ marginLeft: 10 }}
                onClick={this.handleOk}
                loading={creating}
              >
                {formatMessage(messages.createDevice)}
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
                  {formatMessage(messages.updateInfo)}
                </Button>
              </>
            )}
          </Col>
        </Row>
      </Page>
    );
  }
}

MaintainAdd.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  maintainAdd: makeSelectMaintainAdd(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "maintainAdd", reducer });
const withSaga = injectSaga({ key: "maintainAdd", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(MaintainAdd)));
