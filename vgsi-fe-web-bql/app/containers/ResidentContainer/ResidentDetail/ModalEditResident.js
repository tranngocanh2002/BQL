import {
  Checkbox,
  Col,
  DatePicker,
  Form,
  Input,
  Modal,
  Row,
  Select,
} from "antd";
import moment from "moment";
import React from "react";
import { injectIntl } from "react-intl";
import { regexOnlyTextAndNumber, regexVNCharacter } from "utils/constants";
import PhoneNumberInput from "../../../components/PhoneNumberInput";
import { validateEmail } from "../../../utils";
import messages from "../messages";
import "./index.less";
const formItemLayout = {
  labelCol: {
    span: 9,
  },
  wrapperCol: {
    span: 12,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalEditResident extends React.PureComponent {
  handlerUpdate = () => {
    const { form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.props.handlerUpdate({
        ...values,
        ngay_cap_cmtnd: values.ngay_cap_cmtnd
          ? values.ngay_cap_cmtnd.unix()
          : undefined,
        ngay_het_han_thi_thuc: values.ngay_het_han_thi_thuc
          ? values.ngay_het_han_thi_thuc.unix()
          : undefined,
        ngay_dang_ky_tam_chu: values.ngay_dang_ky_tam_chu
          ? values.ngay_dang_ky_tam_chu.unix()
          : undefined,
        ngay_dang_ky_nhap_khau: values.ngay_dang_ky_nhap_khau
          ? values.ngay_dang_ky_nhap_khau.unix()
          : undefined,
        birthday: values.birthday ? values.birthday.unix() : undefined,
        cmtnd:
          !!values.cmtnd && !!values.cmtnd.trim() ? values.cmtnd : undefined,
        noi_cap_cmtnd:
          !!values.noi_cap_cmtnd && !!values.noi_cap_cmtnd.trim()
            ? values.noi_cap_cmtnd
            : undefined,
        so_thi_thuc:
          !!values.so_thi_thuc && !!values.so_thi_thuc.trim()
            ? values.so_thi_thuc
            : undefined,
        is_check_cmtnd: values.is_check_cmtnd ? 1 : 0,
      });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
    }
  }

  render() {
    const { updating, visible, setState, recordResident, tree } = this.props;
    const { getFieldDecorator, getFieldValue } = this.props.form;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Modal
        title={formatMessage(messages.editInformation)}
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (updating) return;
          setState({
            visible: false,
          });
        }}
        okText={formatMessage(messages.update)}
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: updating }}
        cancelButtonProps={{ disabled: updating }}
        maskClosable={false}
        width={"70%"}
      >
        <Row>
          <Col lg={12} md={24}>
            <Form {...formItemLayout}>
              <Form.Item
                labelAlign="left"
                label={formatMessage(messages.phone)}
              >
                {getFieldDecorator("resident_phone", {
                  initialValue:
                    recordResident && `0${recordResident.phone.slice(-9)}`,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptyPhone),
                      whitespace: true,
                    },
                  ],
                })(
                  <PhoneNumberInput
                    maxLength={10}
                    disabled={
                      recordResident && recordResident.phone ? true : false
                    }
                  />
                )}
              </Form.Item>
              <Form.Item labelAlign="left" label={formatMessage(messages.name)}>
                {getFieldDecorator("resident_name", {
                  initialValue: recordResident && recordResident.first_name,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptyName),
                      whitespace: true,
                    },
                    {
                      pattern: regexVNCharacter,
                      message: `${formatMessage(messages.name)} ${formatMessage(
                        messages.onlyText
                      )}`,
                    },
                  ],
                })(<Input maxLength={50} />)}
              </Form.Item>
              <Form.Item
                labelAlign="left"
                label={formatMessage(messages.birthday)}
              >
                {getFieldDecorator("birthday", {
                  initialValue:
                    recordResident && recordResident.birthday
                      ? moment.unix(recordResident.birthday)
                      : undefined,
                  rules: [
                    {
                      required: true,
                      message: `${formatMessage(
                        messages.birthday
                      )} ${formatMessage(messages.required)}`,
                    },
                    { type: "object" },
                  ],
                })(
                  <DatePicker
                    style={{ width: "100%" }}
                    placeholder={formatMessage(messages.selectDate)}
                    format="DD/MM/YYYY"
                    disabledDate={(current) => {
                      return current > moment().endOf("day");
                    }}
                  />
                )}
              </Form.Item>
              <Form.Item
                labelAlign="left"
                label={formatMessage(messages.gender)}
              >
                {getFieldDecorator("gender", {
                  initialValue:
                    recordResident && recordResident.gender
                      ? String(recordResident.gender)
                      : "1",
                  rules: [{ required: true }],
                })(
                  <Select>
                    <Select.Option key="1">
                      {formatMessage(messages.male)}
                    </Select.Option>
                    <Select.Option key="2">
                      {formatMessage(messages.female)}
                    </Select.Option>
                    {/* <Select.Option key="0">
                      {formatMessage(messages.other)}
                    </Select.Option> */}
                  </Select>
                )}
              </Form.Item>
              <Form.Item labelAlign="left" label={"Email"}>
                {getFieldDecorator("resident_email", {
                  initialValue: recordResident && recordResident.email,
                  rules: [
                    {
                      required: true,
                      message: `Email ${formatMessage(messages.required)}`,
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (
                          value &&
                          value.trim() != "" &&
                          !validateEmail(value)
                        ) {
                          callback(formatMessage(messages.errorFormatEmail));
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(<Input maxLength={50} />)}
              </Form.Item>
              <Form.Item labelAlign="left" label={formatMessage(messages.task)}>
                {getFieldDecorator("work", {
                  initialValue: recordResident && recordResident.work,
                })(<Input maxLength={64} />)}
              </Form.Item>

              <Form.Item
                labelAlign="left"
                label={formatMessage(messages.tempRegisterDate)}
              >
                {getFieldDecorator("ngay_dang_ky_tam_chu", {
                  initialValue:
                    recordResident && recordResident.ngay_dang_ky_tam_chu
                      ? moment.unix(recordResident.ngay_dang_ky_tam_chu)
                      : undefined,
                  rules: [
                    {
                      required: true,
                      message: `${formatMessage(
                        messages.tempRegisterDate
                      )} ${formatMessage(messages.required)}`,
                    },
                    { type: "object" },
                  ],
                })(
                  <DatePicker
                    style={{ width: "100%" }}
                    placeholder={formatMessage(messages.selectDate)}
                    format="DD/MM/YYYY"
                    disabledDate={(current) => current.isAfter(moment())}
                  />
                )}
              </Form.Item>
            </Form>
          </Col>
          <Col lg={12} md={24}>
            <Form {...formItemLayout}>
              <Form.Item
                labelAlign="left"
                label={formatMessage(messages.importDate)}
              >
                {getFieldDecorator("ngay_dang_ky_nhap_khau", {
                  initialValue:
                    recordResident && recordResident.ngay_dang_ky_nhap_khau
                      ? moment.unix(recordResident.ngay_dang_ky_nhap_khau)
                      : undefined,
                  rules: [
                    {
                      required: true,
                      message: `${formatMessage(
                        messages.importDate
                      )} ${formatMessage(messages.required)}`,
                    },
                    { type: "object" },
                  ],
                })(
                  <DatePicker
                    style={{ width: "100%" }}
                    placeholder={formatMessage(messages.selectDate)}
                    format="DD/MM/YYYY"
                    disabledDate={(current) => current.isAfter(moment())}
                  />
                )}
              </Form.Item>
              <Form.Item
                labelAlign="left"
                label={formatMessage(messages.idCard)}
                style={{ marginBottom: 0 }}
                required={true}
              >
                <Form.Item
                  colon={false}
                  style={{
                    display: "inline-block",
                    width: "6%",
                  }}
                >
                  {getFieldDecorator("is_check_cmtnd", {
                    initialValue:
                      recordResident && recordResident.is_check_cmtnd
                        ? recordResident.is_check_cmtnd == 1
                        : false,
                    valuePropName: "checked",
                  })(<Checkbox />)}
                </Form.Item>
                <span style={{ display: "inline-block", width: "2%" }} />
                <Form.Item
                  style={{
                    display: "inline-block",
                    width: "92%",
                  }}
                >
                  {getFieldDecorator("cmtnd", {
                    initialValue: recordResident && recordResident.cmtnd,
                    rules: [
                      {
                        required: true,
                        message: `${formatMessage(
                          messages.idCard
                        )} ${formatMessage(messages.required)}`,
                      },
                      {
                        pattern: regexOnlyTextAndNumber,
                        message: `${formatMessage(
                          messages.idCard
                        )} ${formatMessage(messages.invalid)}`,
                      },
                    ],
                  })(<Input maxLength={20} />)}
                </Form.Item>
              </Form.Item>

              <Form.Item
                labelAlign="left"
                label={formatMessage(messages.dateOfIssued)}
              >
                {getFieldDecorator("ngay_cap_cmtnd", {
                  initialValue:
                    recordResident && recordResident.ngay_cap_cmtnd
                      ? moment.unix(recordResident.ngay_cap_cmtnd)
                      : undefined,
                  rules: [
                    {
                      required: true,
                      message: `${formatMessage(
                        messages.dateOfIssued
                      )} ${formatMessage(messages.required)}`,
                    },
                    { type: "object" },
                  ],
                })(
                  <DatePicker
                    style={{ width: "100%" }}
                    placeholder={formatMessage(messages.selectDate)}
                    format="DD/MM/YYYY"
                    disabledDate={(current) => current.isAfter(moment())}
                  />
                )}
              </Form.Item>
              <Form.Item
                labelAlign="left"
                label={formatMessage(messages.issuedByIdCard)}
              >
                {getFieldDecorator("noi_cap_cmtnd", {
                  initialValue: recordResident && recordResident.noi_cap_cmtnd,
                  rules: [
                    {
                      required: true,
                      message: `${formatMessage(
                        messages.issuedByIdCard
                      )} ${formatMessage(messages.required)}`,
                    },
                    {
                      //regexVNCharacter and , /
                      pattern:
                        /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹ,/\s]+$/,
                      message: `${formatMessage(
                        messages.issuedByIdCard
                      )} ${formatMessage(messages.invalid)}`,
                    },
                  ],
                })(<Input maxLength={200} />)}
              </Form.Item>

              <Form.Item
                labelAlign="left"
                label={formatMessage(messages.nationality)}
              >
                {getFieldDecorator("nationality", {
                  initialValue:
                    recordResident && recordResident.nationality
                      ? recordResident.nationality
                      : "vi",
                  rules: [],
                })(
                  <Select>
                    <Select.Option key="vi">
                      {formatMessage(messages.vietnam)}
                    </Select.Option>
                    <Select.Option key="na">
                      {formatMessage(messages.foreign)}
                    </Select.Option>
                  </Select>
                )}
              </Form.Item>
              {getFieldValue("is_check_cmtnd") === true && (
                <>
                  <Form.Item
                    labelAlign="left"
                    label={formatMessage(messages.visaNumber)}
                  >
                    {getFieldDecorator("so_thi_thuc", {
                      rules: [
                        {
                          required: true,
                          message: `${formatMessage(
                            messages.visaNumber
                          )} ${formatMessage(messages.required)}`,
                        },
                      ],
                      initialValue:
                        recordResident && recordResident.so_thi_thuc,
                    })(<Input maxLength={50} />)}
                  </Form.Item>
                  <Form.Item
                    labelAlign="left"
                    label={formatMessage(messages.visaExpireDate)}
                  >
                    {getFieldDecorator("ngay_het_han_thi_thuc", {
                      initialValue:
                        recordResident && recordResident.ngay_het_han_thi_thuc
                          ? moment.unix(recordResident.ngay_het_han_thi_thuc)
                          : undefined,
                      rules: [
                        {
                          required: true,
                          message: `${formatMessage(
                            messages.visaExpireDate
                          )} ${formatMessage(messages.required)}`,
                        },
                        { type: "object" },
                      ],
                    })(
                      <DatePicker
                        style={{ width: "100%" }}
                        placeholder={formatMessage(messages.selectDate)}
                        format="DD/MM/YYYY"
                      />
                    )}
                  </Form.Item>
                </>
              )}
            </Form>
          </Col>
        </Row>
      </Modal>
    );
  }
}

export default injectIntl(ModalEditResident);
