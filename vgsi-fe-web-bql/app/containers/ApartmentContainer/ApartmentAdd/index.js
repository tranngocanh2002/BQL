/**
 *
 * ApartmentAdd
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectApartmentAdd from "./selectors";

import {
  createApartmentAction,
  defaultAction,
  fetchAllApartmentType,
  fetchAllResidentByPhoneAction,
  fetchBuildingAreaAction,
  fetchDetailApartmentAction,
} from "./actions";
import messages from "./messages";

import {
  AutoComplete,
  Button,
  Col,
  DatePicker,
  Form,
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Tooltip,
  TreeSelect,
} from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { updateStaffAction } from "containers/StaffManagementContainer/StaffAdd/actions";
import moment from "moment";
import { injectIntl } from "react-intl";
import { Redirect, withRouter } from "react-router";
import InputNumberFormat from "../../../components/InputNumberFormat";
import NumericInputPositive from "../../../components/NumericInputPositive";
import config from "../../../utils/config";
import { validateName, validateNum, validateText2 } from "utils";
import { regexPhoneNumberVN } from "utils/constants";

const TreeNode = TreeSelect.TreeNode;
const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 9,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ApartmentAdd extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      record: (props.location.state || {}).record,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchBuildingAreaAction());
    this.props.dispatch(fetchAllApartmentType());
    this.props.dispatch(fetchAllResidentByPhoneAction({}));

    const { id } = this.props.match.params;
    if (id != undefined && !this.state.record) {
      this.props.dispatch(fetchDetailApartmentAction({ id }));
    }
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.apartmentAdd.detail.data != nextProps.apartmentAdd.detail.data
    ) {
      const { resident_user, building_area, capacity, ...rest } =
        nextProps.apartmentAdd.detail.data;
      let resident = {};
      if (resident_user) {
        resident = {
          resident_name: resident_user.first_name,
          resident_phone: resident_user.phone,
        };
      }
      this.setState({
        record: {
          ...rest,
          ...resident,
          building_area_id: building_area && `${building_area.id}`,
          capacity: `${capacity}`,
        },
      });
    }
  }

  handerCancelAdd = () => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.cancelAddTitle),
      okText: this.props.intl.formatMessage(messages.confirm),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.history.push("/main/apartment/list");
      },
      onCancel() {},
    });
  };
  handerCancelUpdate = () => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.cancelEditTitle),
      okText: this.props.intl.formatMessage(messages.confirm),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.history.push("/main/apartment/list");
      },
      onCancel() {},
    });
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
          auth_group_id: parseInt(values.auth_group_id),
          id: this.state.record.id,
          date_received: values.date_received
            ? values.date_received.unix()
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
      let capacity = Number(values.capacity);
      if (!Number.isInteger(capacity)) {
        capacity = capacity.toFixed(2);
      }

      dispatch(
        createApartmentAction({
          ...values,
          //handoverStatus: parseInt(values.handoverStatus),

          status: parseInt(values.status),
          building_area_id: parseInt(values.building_area_id),
          capacity: capacity,
          date_received: values.date_received
            ? values.date_received.unix()
            : undefined,
          date_delivery: values.date_delivery
            ? values.date_delivery.unix()
            : undefined,
        })
      );
    });
  };

  loop = (data, preTitle = "", iii = 0) => {
    return data.map((item) => {
      if (item.children) {
        return (
          <TreeNode
            value={item.key}
            key={item.key}
            selectable={!!iii}
            title={iii ? `${preTitle} / ${item.name}` : item.name}
          >
            {this.loop(item.children, item.name, iii + 1)}
          </TreeNode>
        );
      }
      return (
        <TreeNode
          value={item.key}
          key={item.key}
          title={`${preTitle} / ${item.name}`}
        />
      );
    });
  };

  render() {
    const { apartmentAdd, language } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    const {
      creating,
      success,
      updating,
      updateSuccess,
      detail,
      buildingArea,
      apartment_type,
      allResident,
    } = apartmentAdd;

    const { tree } = buildingArea;

    const { record } = this.state;
    const formatMessage = this.props.intl.formatMessage;
    if (success || updateSuccess || detail.data == -1) {
      return <Redirect to="/main/apartment/list" />;
    }
    const options = allResident.data.map((res) => `0${res.phone.slice(-9)}`);
    return (
      <Page inner loading={detail.loading}>
        <Row gutter={24} style={{ marginTop: 40 }}>
          <Col>
            <Row style={{ marginBottom: 24 }}>
              <Col span={8} style={{ textAlign: "right", paddingRight: 8 }}>
                <span style={{ fontWeight: "bold", color: "#1B1B27" }}>
                  {formatMessage(messages.info)}
                </span>
              </Col>
              <Col span={9}>
                <Tooltip title={formatMessage(messages.propertyInfo)}>
                  <Icon style={{ color: "#2994F9" }} type="question-circle-o" />
                </Tooltip>
              </Col>
            </Row>
            <Form {...formItemLayout} onSubmit={this.handleSubmit}>
              <Form.Item
                label={formatMessage(messages.propertyName)}
                colon={false}
              >
                {getFieldDecorator("name", {
                  initialValue: record && record.name,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.propertyNameRequired),
                      whitespace: true,
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (
                          value &&
                          value.trim() != "" &&
                          validateText2(value)
                        ) {
                          callback(formatMessage(messages.propertyError));
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(<Input maxLength={10} />)}
              </Form.Item>
              <Form.Item label={formatMessage(messages.address)} colon={false}>
                {getFieldDecorator("building_area_id", {
                  initialValue: record && record.building_area_id,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.addressRequired),
                      whitespace: true,
                    },
                  ],
                })(
                  <TreeSelect
                    // showSearch
                    dropdownStyle={{ maxHeight: 400, overflow: "auto" }}
                    placeholder={formatMessage(messages.chooseAddress)}
                    allowClear
                    treeDefaultExpandAll
                  >
                    {this.loop(tree)}
                  </TreeSelect>
                )}
              </Form.Item>
              <Form.Item label={formatMessage(messages.propertyType)}>
                {getFieldDecorator("form_type", {
                  initialValue: record ? String(record.form_type) : "0",
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.propertyTypeRequired),
                      whitespace: true,
                    },
                  ],
                })(
                  <Select
                    allowClear
                    placeholder={formatMessage(messages.choosePropertyType)}
                  >
                    {apartment_type.data.map((type, index) => {
                      return (
                        <Select.Option key={index} value={String(index)}>
                          {this.props.language === "vi"
                            ? type.name
                            : type.name_en}
                        </Select.Option>
                      );
                    })}
                  </Select>
                )}
              </Form.Item>
              <Form.Item
                label={formatMessage(messages.propertyArea)}
                colon={false}
              >
                {getFieldDecorator("capacity", {
                  initialValue: record && record.capacity,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.propertyAreaRequired),
                      whitespace: true,
                    },
                    {
                      validator: (rule, value, callback) => {
                        if (
                          value &&
                          value.trim() != "" &&
                          value.length > 5 &&
                          !value.includes(".")
                        ) {
                          callback(formatMessage(messages.propertyAreaError));
                        }
                        if (
                          value &&
                          value.trim() != "" &&
                          !validateNum(value)
                        ) {
                          callback(formatMessage(messages.propertyAreaError));
                        } else {
                          callback();
                        }
                      },
                    },
                  ],
                })(<NumericInputPositive addonAfter={"m2"} maxLength={8} />)}
              </Form.Item>
              <Form.Item
                label={formatMessage(messages.handoverStatus)}
                colon={false}
              >
                {getFieldDecorator("handoverStatus", {
                  initialValue:
                    (record && String(record.status)) ||
                    getFieldValue("status") == 1
                      ? "1"
                      : "0",
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.handoverStatusRequired),
                      whitespace: true,
                    },
                  ],
                })(
                  <Select
                    showSearch
                    //disabled={getFieldValue("status") === "1"}
                    placeholder={formatMessage(messages.chooseHandoverStatus)}
                    optionFilterProp="children"
                    // onChange={onChange}
                    filterOption={(input, option) =>
                      option.props.children
                        .toLowerCase()
                        .indexOf(input.toLowerCase()) >= 0
                    }
                  >
                    {config.STATUS_HANDOVER.map((gr) => {
                      return (
                        <Select.Option
                          key={`group-${gr.id}`}
                          value={`${gr.id}`}
                        >
                          {language == "en" ? gr.name_en : gr.name}
                        </Select.Option>
                      );
                    })}
                  </Select>
                )}
              </Form.Item>
              {getFieldValue("handoverStatus") === "1" && (
                <Form.Item
                  label={formatMessage(messages.dayHandover)}
                  colon={false}
                >
                  {getFieldDecorator("date_delivery", {
                    initialValue:
                      !!record && !!record.date_delivery
                        ? moment.unix(record.date_delivery)
                        : moment(),

                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.dayHandoverError),
                      },
                      { type: "object" },
                    ],
                  })(
                    <DatePicker
                      format="DD/MM/YYYY"
                      style={{ width: "100%" }}
                      disabledDate={(current) => current.isAfter(moment())}
                    />
                  )}
                </Form.Item>
              )}
              <Form.Item label={formatMessage(messages.status)} colon={false}>
                {getFieldDecorator("status", {
                  initialValue: (record && String(record.status)) || "0",
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.statusRequired),
                      whitespace: true,
                    },
                  ],
                })(
                  <Select
                    showSearch
                    onSelect={(e) => {
                      setFieldsValue({
                        status: e,
                      });
                    }}
                    placeholder={formatMessage(messages.chooseStatus)}
                    optionFilterProp="children"
                    // onChange={onChange}
                    filterOption={(input, option) =>
                      option.props.children
                        .toLowerCase()
                        .indexOf(input.toLowerCase()) >= 0
                    }
                  >
                    {config.STATUS_APARTMENT.map((gr) => {
                      return (
                        <Select.Option
                          key={`group-${gr.id}`}
                          value={`${gr.id}`}
                        >
                          {language == "en" ? gr.name_en : gr.name}
                        </Select.Option>
                      );
                    })}
                  </Select>
                )}
              </Form.Item>
              {getFieldValue("status") == 1 && (
                <>
                  <Row style={{ marginBottom: 24 }}>
                    <Col
                      span={8}
                      style={{ textAlign: "right", paddingRight: 8 }}
                    >
                      <span style={{ fontWeight: "bold", color: "#1B1B27" }}>
                        {formatMessage(messages.owner)}
                      </span>
                    </Col>
                    <Col span={9}>
                      <Tooltip title={formatMessage(messages.ownerInfo)}>
                        <Icon
                          style={{ color: "#2994F9" }}
                          type="question-circle-o"
                        />
                      </Tooltip>
                    </Col>
                  </Row>
                  {/* <Form.Item label={`Số điện thoại`} colon={false}>
                    {getFieldDecorator("resident_phone", {
                      initialValue: record && record.resident_phone,
                      rules: [
                        {
                          required: true,
                          message: "Số điện thoại không được để trống.",
                          whitespace: true,
                        },
                      ],
                    })(<PhoneNumberInput maxLength={11} />)}
                  </Form.Item> */}
                  <Form.Item
                    label={formatMessage(messages.phone)}
                    colon={false}
                  >
                    {getFieldDecorator("resident_phone", {
                      initialValue: record && record.resident_phone,

                      rules: [
                        {
                          required: true,
                          message: formatMessage(messages.phoneRequired),
                          whitespace: true,
                        },
                        {
                          pattern: regexPhoneNumberVN,
                          message: formatMessage(messages.invalidPhone),
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
                          //console.log("23131", e);
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
                      >
                        <Input maxLength={10} />
                      </AutoComplete>
                    )}
                  </Form.Item>
                  <Form.Item label={formatMessage(messages.name)} colon={false}>
                    {getFieldDecorator("resident_name", {
                      initialValue: record && record.resident_name,
                      rules: [
                        {
                          required: true,
                          message: formatMessage(messages.nameRequired),
                          whitespace: true,
                        },
                        {
                          validator: (rule, value, callback) => {
                            if (
                              value &&
                              value.trim() != "" &&
                              !validateName(value)
                            ) {
                              callback(formatMessage(messages.nameError));
                            } else {
                              callback();
                            }
                          },
                        },
                      ],
                    })(<Input maxLength={50} disabled={!!options.length} />)}
                  </Form.Item>
                  <Form.Item
                    label={formatMessage(messages.totalMember)}
                    colon={false}
                  >
                    {getFieldDecorator("total_members", {
                      initialValue:
                        !!record && !!record.total_members
                          ? record.total_members
                          : 1,
                      rules: [
                        {
                          message: formatMessage(messages.totalMemberRequired),
                          required: true,
                          whitespace: true,
                          type: "number",
                          min: 1,
                        },
                      ],
                    })(
                      <InputNumberFormat
                        maxLength={6}
                        useDefault
                        style={{ width: "100%" }}
                      />
                    )}
                  </Form.Item>
                  <Form.Item
                    label={formatMessage(messages.dateReceive)}
                    colon={false}
                  >
                    {getFieldDecorator("date_received", {
                      initialValue:
                        !!record && !!record.date_received
                          ? moment.unix(record.date_received)
                          : moment(),
                      rules: [{ type: "object" }],
                    })(
                      <DatePicker
                        format="DD/MM/YYYY"
                        style={{ width: "100%" }}
                      />
                    )}
                  </Form.Item>
                </>
              )}
              <Form.Item label={formatMessage(messages.note)}>
                {getFieldDecorator("description")(
                  <Input.TextArea maxLength={1000} />
                )}
              </Form.Item>
            </Form>

            <Col offset={8} style={{ paddingLeft: 0 }}>
              {!record && (
                <>
                  <Button
                    disabled={creating}
                    style={{ minWidth: 120 }}
                    type="danger"
                    onClick={this.handerCancelAdd}
                  >
                    {formatMessage(messages.cancel)}
                  </Button>
                  <Button
                    ghost
                    type="primary"
                    style={{ minWidth: 120, marginLeft: 10 }}
                    onClick={this.handleOk}
                    loading={creating}
                  >
                    {formatMessage(messages.createProperty)}
                  </Button>
                </>
              )}
              {!!record && (
                <>
                  <Button
                    disabled={updating}
                    style={{ minWidth: 100 }}
                    type="danger"
                    onClick={this.handerCancelUpdate}
                  >
                    {formatMessage(messages.cancel)}
                  </Button>
                  <Button
                    loading={updating}
                    style={{ minWidth: 100, marginLeft: 10 }}
                    ghost
                    type="primary"
                    onClick={this.handlerUpdate}
                  >
                    {formatMessage(messages.update)}
                  </Button>
                </>
              )}
            </Col>
          </Col>
        </Row>
      </Page>
    );
  }
}

ApartmentAdd.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  apartmentAdd: makeSelectApartmentAdd(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "apartmentAdd", reducer });
const withSaga = injectSaga({ key: "apartmentAdd", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(ApartmentAdd)));
