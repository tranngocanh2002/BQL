/**
 *
 * ResidentAdd
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  AutoComplete,
  Button,
  Checkbox,
  Col,
  DatePicker,
  Form,
  Input,
  Row,
  Select,
  Spin,
} from "antd";
import _ from "lodash";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { fetchDetailAction, updateStaffAction } from "./actions";
import reducer from "./reducer";
import saga from "./saga";

import { Redirect, withRouter } from "react-router";
import makeSelectResidentAdd from "./selectors";

import {
  createResidentAction,
  defaultAction,
  fetchAllResidentByPhoneAction,
  fetchApartmentAction,
} from "./actions";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import { injectIntl } from "react-intl";
import {
  regexEmail,
  regexOnlyTextAndNumber,
  regexPhoneNumberVN,
  regexVNCharacter,
} from "utils/constants";
import config from "../../../utils/config";
import messages from "../messages";
const formItemLayout = {
  labelAlign: "left",
  labelCol: {
    xs: { span: 24 },
    sm: { span: 8 },
    md: { span: 8 },
    lg: { span: 14 },
    xl: { span: 10 },
    xxl: { span: 10 },
  },
  wrapperCol: {
    xs: { span: 24 },
    sm: { span: 16 },
    md: { span: 16 },
    lg: { span: 10 },
    xl: { span: 14 },
    xxl: { span: 14 },
  },
};

const formColLayout = {
  xs: 24,
  sm: 24,
  md: 24,
  lg: 12,
  xl: 12,
  xxl: 12,
};

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ResidentAdd extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      record: (props.location.state || {}).record,
      addApartment_id: (props.location.state || {}).apartment_id,
      addApartment_name: (props.location.state || {}).apartment_name,
    };

    this._onSearch = _.debounce(this.onSearch, 300);
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    const { id } = this.props.match.params;
    if (id != undefined && !this.state.record) {
      this.props.dispatch(fetchDetailAction({ id }));
    }
    this._onSearch(this.state.addApartment_name || "");
    this.props.dispatch(fetchAllResidentByPhoneAction({}));
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.residentAdd.detail.data != nextProps.residentAdd.detail.data
    ) {
      this.setState({
        record: nextProps.residentAdd.detail.data,
      });
    }
  }

  handerCancel = () => {
    this.props.history.push("/main/resident/list");
  };

  handlerUpdate = () => {
    const { dispatch, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }

      dispatch(
        updateStaffAction({
          ...values,
          type_relationship: values.type_relationship || 0,
          auth_group_id: parseInt(values.auth_group_id),
          id: this.state.record.id,
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
          is_check_cmtnd: values.is_check_cmtnd ? 1 : 0,
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
        createResidentAction({
          ...values,
          type: values.type,
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
          type_relationship: values.type_relationship || 0,
          apartment_id: parseInt(values.apartment_id),
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
          is_check_cmtnd: values.is_check_cmtnd ? 1 : 0,
        })
      );
    });
  };

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
  };

  render() {
    const { residentAdd } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    const { creating, success, updating, updateSuccess, detail, allResident } =
      residentAdd;
    const { loading, lst } = residentAdd.apartments;
    const { record } = this.state;
    const formatMessage = this.props.intl.formatMessage;
    const options = allResident.data.map((res) => `0${res.phone.slice(-9)}`);
    if (success || updateSuccess || detail.data == -1) {
      return <Redirect to="/main/resident/list" />;
    }
    return (
      <Page inner loading={detail.loading}>
        <Row gutter={24} style={{ marginTop: 40 }}>
          <Col span={24}>
            <Form {...formItemLayout} onSubmit={this.handleSubmit}>
              <Col {...formColLayout}>
                <Form.Item label={formatMessage(messages.phone)}>
                  {getFieldDecorator("resident_phone", {
                    initialValue:
                      record && `0${record.resident_phone.slice(-9)}`,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.emptyPhone),
                      },
                      {
                        pattern: regexPhoneNumberVN,
                        message: formatMessage(messages.invalidPhone),
                        whitespace: false,
                      },
                    ],
                  })(
                    <AutoComplete
                      style={{ width: "100%" }}
                      dropdownMenuStyle={{
                        overflowY: "auto",
                        maxHeight: 150,
                      }}
                      dataSource={options}
                      onChange={(e) => {
                        setFieldsValue({
                          resident_name: "",
                        });
                        if (e.trim().length === 10) {
                          const phoneData = allResident.data.find(
                            (mm) => `0${mm.phone.slice(-9)}` == e
                          );
                          if (phoneData) {
                            setFieldsValue({
                              resident_name: phoneData.first_name,
                            });
                          }
                        }

                        if (e.trim().length > 2 && e.trim().length < 14) {
                          this.props.dispatch(
                            fetchAllResidentByPhoneAction({ phone: e })
                          );
                        } else if (e.trim().length == 0) {
                          this.props.dispatch(
                            fetchAllResidentByPhoneAction({ phone: "" })
                          );
                        }
                      }}
                      onSelect={(e) => {
                        setFieldsValue({
                          resident_name: allResident.data.filter(
                            (mm) => `0${mm.phone.slice(-9)}` == e
                          )[0].first_name,
                        });
                      }}
                    />
                  )}
                </Form.Item>
                <Form.Item label={formatMessage(messages.name)}>
                  {getFieldDecorator("resident_name", {
                    initialValue: record && record.resident_name,
                    rules: [
                      {
                        required: true,
                        message: `${formatMessage(
                          messages.name
                        )} ${formatMessage(messages.required)}`,
                        whitespace: true,
                      },
                      {
                        pattern: regexVNCharacter,
                        message: `${formatMessage(
                          messages.name
                        )} ${formatMessage(messages.onlyText)}`,
                      },
                    ],
                  })(
                    <Input
                      style={{ width: "100%" }}
                      maxLength={50}
                      // disabled={!!options.length}
                    />
                  )}
                </Form.Item>
                <Form.Item label={formatMessage(messages.type)}>
                  {getFieldDecorator("type", {
                    initialValue: record ? record.type : 1,
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.emptyGroup),
                        whitespace: true,
                        type: "number",
                      },
                    ],
                  })(
                    <Select
                      // showSearch
                      // allowClear
                      placeholder={formatMessage(messages.selectGroup)}
                      optionFilterProp="children"
                      // onChange={onChange}
                      filterOption={(input, option) =>
                        option.props.children
                          .toLowerCase()
                          .indexOf(input.toLowerCase()) >= 0
                      }
                    >
                      {config.TYPE_RESIDENT.map((gr) => {
                        return (
                          <Select.Option key={`group-${gr.id}`} value={gr.id}>
                            {this.props.language === "vi"
                              ? gr.name
                              : gr.name_en}
                          </Select.Option>
                        );
                      })}
                    </Select>
                  )}
                </Form.Item>
                {getFieldValue("type") != 1 && getFieldValue("type") != 2 && (
                  <Form.Item label={formatMessage(messages.relationship)}>
                    {getFieldDecorator("type_relationship", {
                      initialValue: record ? record.type_relationship : 0,
                      rules: [
                        {
                          required: true,
                          message: formatMessage(
                            messages.errorEmptyRelationship
                          ),
                          whitespace: true,
                          type: "number",
                        },
                      ],
                    })(
                      <Select
                        // allowClear
                        // showSearch
                        placeholder={formatMessage(messages.selectRelationship)}
                        optionFilterProp="children"
                        // onChange={onChange}
                        // filterOption={(input, option) =>
                        //   option.props.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                        // }
                      >
                        {config.RELATIONSHIP_APARTMENT.map((gr) => {
                          return (
                            <Select.Option key={`group-${gr.id}`} value={gr.id}>
                              {this.props.language === "vi"
                                ? gr.title
                                : gr.title_en}
                            </Select.Option>
                          );
                        })}
                      </Select>
                    )}
                  </Form.Item>
                )}
                <Form.Item label={formatMessage(messages.property)}>
                  {getFieldDecorator("apartment_id", {
                    initialValue:
                      (record && String(record.apartment_id)) ||
                      this.state.addApartment_id
                        ? String(this.state.addApartment_id)
                        : undefined,
                    rules: [
                      {
                        required: true,
                        message: `${formatMessage(
                          messages.property
                        )} ${formatMessage(messages.required)}`,
                        whitespace: true,
                      },
                    ],
                  })(
                    <Select
                      allowClear
                      loading={loading}
                      showSearch
                      placeholder={formatMessage(messages.searchProperty)}
                      optionFilterProp="children"
                      notFoundContent={loading ? <Spin size="small" /> : null}
                      onSearch={this._onSearch}
                      onChange={(value, opt) => {
                        if (!opt) {
                          this._onSearch("");
                        }
                      }}
                    >
                      {lst.map((gr) => {
                        return (
                          <Select.Option
                            key={`group-${gr.id}`}
                            value={`${gr.id}`}
                          >{`${gr.name} (${gr.parent_path})`}</Select.Option>
                        );
                      })}
                    </Select>
                  )}
                </Form.Item>
                <Form.Item label={formatMessage(messages.birthday)}>
                  {getFieldDecorator("birthday", {
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
                <Form.Item label={formatMessage(messages.gender)}>
                  {getFieldDecorator("gender", {
                    rules: [{ required: true }],
                    initialValue: "1",
                  })(
                    <Select allowClear>
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
                <Form.Item label={"Email"}>
                  {getFieldDecorator("resident_email", {
                    initialValue: record && record.resident_email,
                    rules: [
                      {
                        required: true,
                        message: `Email ${formatMessage(messages.required)}`,
                      },
                      {
                        pattern: regexEmail,
                        message: `${formatMessage(messages.errorFormatEmail)}`,
                      },
                    ],
                  })(<Input style={{ width: "100%" }} maxLength={50} />)}
                </Form.Item>
                <Form.Item label={formatMessage(messages.task)}>
                  {getFieldDecorator("work", {
                    initialValue: record && record.work,
                  })(<Input style={{ width: "100%" }} maxLength={50} />)}
                </Form.Item>
              </Col>

              <Col {...formColLayout}>
                <Form.Item label={formatMessage(messages.tempRegisterDate)}>
                  {getFieldDecorator("ngay_dang_ky_tam_chu", {
                    initialValue:
                      record && record.ngay_dang_ky_tam_chu
                        ? moment.unix(record.ngay_dang_ky_tam_chu)
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
                      locale={this.props.language}
                      style={{ width: "100%" }}
                      placeholder={formatMessage(messages.selectDate)}
                      format="DD/MM/YYYY"
                      disabledDate={(current) => {
                        return current > moment().endOf("day");
                      }}
                    />
                  )}
                </Form.Item>
                <Form.Item label={formatMessage(messages.importDate)}>
                  {getFieldDecorator("ngay_dang_ky_nhap_khau", {
                    initialValue:
                      record && record.ngay_dang_ky_nhap_khau
                        ? moment.unix(record.ngay_dang_ky_nhap_khau)
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
                      locale={this.props.language}
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
                        record && record.is_check_cmtnd
                          ? record.is_check_cmtnd == 1
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
                      initialValue: record && record.cmtnd,
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

                <Form.Item label={formatMessage(messages.dateOfIssued)}>
                  {getFieldDecorator("ngay_cap_cmtnd", {
                    initialValue:
                      record && record.ngay_cap_cmtnd
                        ? moment.unix(record.ngay_cap_cmtnd)
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
                      locale={this.props.language}
                      style={{ width: "100%" }}
                      placeholder={formatMessage(messages.selectDate)}
                      format="DD/MM/YYYY"
                      disabledDate={(current) => {
                        return current > moment().endOf("day");
                      }}
                    />
                  )}
                </Form.Item>
                <Form.Item label={formatMessage(messages.issuedByIdCard)}>
                  {getFieldDecorator("noi_cap_cmtnd", {
                    initialValue: record && record.noi_cap_cmtnd,
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
                          /^[a-zA-ZÀÁÂÃÈÉÊÌÍÒÓÔÕÙÚĂĐĨŨƠàáâãèéêìíòóôõùúăđĩũơƯĂẠẢẤẦẨẪẬẮẰẲẴẶẸẺẼỀỀỂẾưăạảấầẩẫậắằẳẵặẹẻẽềềểếỄỆỈỊỌỎỐỒỔỖỘỚỜỞỠỢỤỦỨỪễệỉịọỏốồổỗộớờởỡợụủứừỬỮỰỲỴÝỶỸửữựỳỵỷỹy,/\s]+$/,
                        message: `${formatMessage(
                          messages.issuedByIdCard
                        )} ${formatMessage(messages.invalid)}`,
                      },
                    ],
                  })(<Input maxLength={200} />)}
                </Form.Item>
                <Form.Item label={formatMessage(messages.nationality)}>
                  {getFieldDecorator("nationality", {
                    initialValue:
                      record && record.nationality ? record.nationality : "vi",
                    rules: [],
                  })(
                    <Select allowClear>
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
                    <Form.Item label={formatMessage(messages.visaNumber)}>
                      {getFieldDecorator("so_thi_thuc", {
                        rules: [
                          {
                            required: true,
                            message: `${formatMessage(
                              messages.visaNumber
                            )} ${formatMessage(messages.required)}`,
                          },
                        ],
                        initialValue: record && record.so_thi_thuc,
                      })(<Input maxLength={50} />)}
                    </Form.Item>
                    <Form.Item label={formatMessage(messages.visaExpireDate)}>
                      {getFieldDecorator("ngay_het_han_thi_thuc", {
                        initialValue:
                          record && record.ngay_het_han_thi_thuc
                            ? moment.unix(record.ngay_het_han_thi_thuc)
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
                          locale={this.props.language}
                        />
                      )}
                    </Form.Item>
                  </>
                )}
              </Col>
            </Form>
          </Col>
          <Col
            span={24}
            style={{ display: "flex", justifyContent: "center", marginTop: 24 }}
          >
            <Button
              disabled={updating}
              type="danger"
              onClick={this.handerCancel}
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
                {formatMessage(messages.addResident)}
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

ResidentAdd.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  residentAdd: makeSelectResidentAdd(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "residentAdd", reducer });
const withSaga = injectSaga({ key: "residentAdd", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(ResidentAdd)));
