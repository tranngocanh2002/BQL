import React from "react";
import {
  Modal,
  TreeSelect,
  Input,
  Form,
  Select,
  DatePicker,
  Checkbox,
  Row,
  Col,
  Tooltip,
  Icon,
  AutoComplete,
  Button,
} from "antd";
import { regexVNCharacter } from "utils/constants";

import config from "../../../utils/config";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "./messages";
import moment from "moment";

import "./index.less";
import NumericInputPositive from "../../../components/NumericInputPositive";
import InputNumberFormat from "../../../components/InputNumberFormat";
import { validateNum, validateText2 } from "utils";
import { regexPhoneNumberVN } from "utils/constants";
const TreeNode = TreeSelect.TreeNode;
const formItemLayout = {
  labelCol: {
    span: 7,
  },
  wrapperCol: {
    span: 13,
    offset: 1,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalEditApartment extends React.PureComponent {
  // componentWillUnmount() {
  // this.props.dispatch(defaultAction());
  // }

  // componentDidMount() {
  // this.props.dispatch(fetchAllResidentByPhoneAction({}));
  // const { id } = this.props.match.params;
  // if (id != undefined && !this.state.record) {
  //   this.props.dispatch(fetchDetailApartmentAction({ id }));
  // }
  // }

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

  handlerUpdate = () => {
    const { form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        console.log("errors", errors);
        return;
      }
      let capacity = Number(values.capacity);
      if (!Number.isInteger(capacity)) {
        capacity = capacity.toFixed(2);
      }

      if (
        this.props.recordApartment.resident_user &&
        this.props.recordApartment.resident_user.phone
      ) {
        this.props.handlerUpdate({
          ...values,
          capacity: capacity,
          set_water_level: values.set_water_level ? 1 : 0,
          date_received: values.date_received
            ? values.date_received.unix()
            : undefined,
          date_delivery: values.date_delivery
            ? values.date_delivery.unix()
            : undefined,
        });
      } else {
        this.props.handlerUpdate({
          ...values,
          capacity: capacity,
          set_water_level: values.set_water_level ? 1 : 0,
          date_received: values.date_received
            ? values.date_received.unix()
            : undefined,
          date_delivery: values.date_delivery
            ? values.date_delivery.unix()
            : undefined,
        });
      }

      // setTimeout(() => {
      this.props.form.resetFields();
      this.props.setState({
        visible: false,
      });
      // }, 1000);
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
      this.props.dispatch(this.props.fetchAllResidentByPhoneAction({}));
    }
  }

  render() {
    const {
      updating,
      visible,
      setState,
      recordApartment,
      tree,
      apartment_type,
      language,
      allResident,
      fetchAllResidentByPhoneAction,
    } = this.props;
    const options = allResident.data
      .map((res) => `0${res.phone.slice(-9)}`)
      .filter((item, index, self) => {
        return index === self.indexOf(item);
      });
    const formatMessage = this.props.intl.formatMessage;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;

    return (
      <Modal
        title={<FormattedMessage {...messages.editInfo} />}
        visible={visible}
        footer={[
          <Button
            key="back"
            disabled={updating}
            onClick={() => {
              if (updating) return;
              setState({
                visible: false,
              });
            }}
          >
            <FormattedMessage {...messages.deleteMemberCancelText} />
          </Button>,
          <Button
            key="submit"
            type="primary"
            loading={updating}
            onClick={() => {
              this.handlerUpdate();
            }}
          >
            <FormattedMessage {...messages.update} />
          </Button>,
        ]}
        closable={visible}
        // maskClosable={false}
        onCancel={() =>
          setState({
            visible: false,
          })
        }
        width={720}
      >
        <Form {...formItemLayout}>
          <Form.Item
            label={<FormattedMessage {...messages.apartmentName} />}
            colon={false}
          >
            {getFieldDecorator("name", {
              initialValue: recordApartment && recordApartment.name,
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.apartmentNameRequired} />
                  ),

                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    if (value && value.trim() != "" && validateText2(value)) {
                      callback(formatMessage(messages.propertyError));
                    } else {
                      callback();
                    }
                  },
                },
              ],
            })(<Input maxLength={10} />)}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.block} />}
            colon={false}
          >
            {getFieldDecorator("building_area_id", {
              initialValue: recordApartment
                ? String(recordApartment.building_area.id)
                : "",
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.apartmentAddressRequired} />
                  ),
                  whitespace: true,
                },
              ],
            })(
              <TreeSelect
                // showSearch
                dropdownStyle={{ maxHeight: 400, overflow: "auto" }}
                placeholder={<FormattedMessage {...messages.chooseAddress} />}
                treeDefaultExpandAll
              >
                {this.loop(tree)}
              </TreeSelect>
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.apartmentType} />}
            colon={false}
          >
            {getFieldDecorator("form_type", {
              initialValue: recordApartment
                ? String(recordApartment.form_type)
                : "0",
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.apartmentTypeRequired} />
                  ),
                  whitespace: true,
                },
              ],
            })(
              <Select
                placeholder={
                  <FormattedMessage {...messages.apartmentTypePlaceholder} />
                }
              >
                {apartment_type.data.map((type, index) => {
                  return (
                    <Select.Option key={index} value={String(index)}>
                      {this.props.language === "vi" ? type.name : type.name_en}
                    </Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.apartmentArea} />}
            colon={false}
          >
            {getFieldDecorator("capacity", {
              initialValue: recordApartment
                ? String(recordApartment.capacity)
                : "",
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.apartmentAreaRequired} />
                  ),
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
                    if (value && value.trim() != "" && !validateNum(value)) {
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
            label={<FormattedMessage {...messages.handoverStatus} />}
            colon={false}
          >
            {getFieldDecorator("handoverStatus", {
              initialValue:
                (!!recordApartment && recordApartment.date_delivery) ||
                getFieldValue("status") == 1
                  ? "1"
                  : "0",
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.handoverStatusRequired} />
                  ),
                  whitespace: true,
                },
              ],
            })(
              <Select
                showSearch
                disabled={!!recordApartment && !!recordApartment.date_delivery}
                placeholder={
                  <FormattedMessage {...messages.handoverStatusPlaceholder} />
                }
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
              >
                {config.STATUS_HANDOVER.map((gr) => {
                  return (
                    <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>
                      {language === "en" ? gr.name_en : gr.name}
                    </Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          {getFieldValue("handoverStatus") === "1" && (
            <Form.Item
              label={<FormattedMessage {...messages.dayHandover} />}
              colon={false}
            >
              {getFieldDecorator("date_delivery", {
                initialValue:
                  !!recordApartment && !!recordApartment.date_delivery
                    ? moment.unix(recordApartment.date_delivery)
                    : recordApartment.date_received
                    ? moment.unix(recordApartment.date_received)
                    : moment(),
                rules: [{ type: "object" }],
              })(
                <DatePicker
                  format="DD/MM/YYYY"
                  style={{ width: "100%" }}
                  disabledDate={(current) => current.isAfter(moment())}
                />
              )}
            </Form.Item>
          )}

          <Form.Item
            label={<FormattedMessage {...messages.status} />}
            colon={false}
          >
            {getFieldDecorator("status", {
              initialValue:
                (!!recordApartment && String(recordApartment.status)) || "0",
              rules: [
                {
                  required: true,
                  message: <FormattedMessage {...messages.statusRequired} />,
                  whitespace: true,
                },
              ],
            })(
              <Select
                showSearch
                disabled={!!recordApartment && !!recordApartment.status}
                placeholder={
                  <FormattedMessage {...messages.statusPlaceholder} />
                }
                optionFilterProp="children"
                onSelect={(e) => {
                  setFieldsValue({
                    status: e,
                  });
                }}
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
              >
                {config.STATUS_APARTMENT.map((gr) => {
                  return (
                    <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>
                      {language === "en" ? gr.name_en : gr.name}
                    </Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          {/* {!!recordApartment && (
            <>
              
            </>
          )} */}
          {!!recordApartment && getFieldValue("status") == 1 && (
            <>
              <Row style={{ marginBottom: 24 }}>
                <Col span={7} style={{ textAlign: "right", paddingRight: 8 }}>
                  <Tooltip title={formatMessage(messages.ownerInfo)}>
                    <span style={{ fontWeight: "bold", color: "#1B1B27" }}>
                      {formatMessage(messages.owner)}
                    </span>
                  </Tooltip>
                </Col>
              </Row>
              <Form.Item label={formatMessage(messages.phone)} colon={false}>
                {getFieldDecorator("resident_phone", {
                  initialValue:
                    recordApartment.resident_user &&
                    recordApartment.resident_user.phone !== ""
                      ? `0${recordApartment.resident_user.phone.slice(-9)}`
                      : "",
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
                    disabled={
                      recordApartment.resident_user &&
                      recordApartment.status == 1
                    }
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
                        resident_name: allResident.data.find(
                          (mm) => `0${mm.phone.slice(-9)}` == e
                        ).first_name,
                      });
                    }}
                  >
                    <Input maxLength={10} />
                  </AutoComplete>
                )}
              </Form.Item>
              <Form.Item label={formatMessage(messages.name)} colon={false}>
                {getFieldDecorator("resident_name", {
                  initialValue:
                    recordApartment.resident_user &&
                    `${recordApartment.resident_user_name || ""}`,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.nameRequired),
                      whitespace: true,
                    },
                    {
                      pattern: regexVNCharacter,
                      message: formatMessage(messages.nameError),
                    },
                  ],
                })(
                  <Input
                    maxLength={50}
                    disabled={!!options.length || recordApartment.status == 1}
                  />
                )}
              </Form.Item>
              <Form.Item
                label={<FormattedMessage {...messages.totalMembers} />}
                colon={false}
              >
                {getFieldDecorator("total_members", {
                  initialValue:
                    !!recordApartment && !!recordApartment.total_members
                      ? recordApartment.total_members
                      : 1,
                  rules: [
                    {
                      message: (
                        <FormattedMessage {...messages.totalMembersRequired} />
                      ),
                      required: true,
                      whitespace: true,
                      type: "number",
                      min: 1,
                    },
                  ],
                })(
                  <InputNumberFormat
                    useDefault
                    maxLength={6}
                    style={{ width: "100%" }}
                  />
                )}
              </Form.Item>
              <Form.Item
                label={<FormattedMessage {...messages.receiveDay} />}
                colon={false}
              >
                {getFieldDecorator("date_received", {
                  initialValue:
                    !!recordApartment && !!recordApartment.date_received
                      ? moment.unix(recordApartment.date_received)
                      : moment(),
                  rules: [{ type: "object" }],
                })(
                  <DatePicker
                    format="DD/MM/YYYY"
                    style={{ width: "100%" }}
                    //disabledDate={(current) => current.isAfter(moment())}
                  />
                )}
              </Form.Item>
              {/* <Form.Item label={`Người bàn giao`} colon={false}>
                  {getFieldDecorator("handover", {
                    initialValue:
                      !!recordApartment && !!recordApartment.handover
                        ? recordApartment.handover
                        : "",
                  })(<Input maxLength={50} />)}
                </Form.Item> */}
            </>
          )}
          <Form.Item label={formatMessage(messages.note)} colon={false}>
            {getFieldDecorator("description", {
              initialValue: recordApartment ? recordApartment.description : "",
            })(<Input.TextArea maxLength={1000} />)}
          </Form.Item>
          <Form.Item
            label={
              <div style={{ paddingRight: 8 }}>
                {/* <FormattedMessage {...messages.trangThaiKhaiBao} />
                <br />
                <FormattedMessage {...messages.dinhMucNuoc} /> */}
              </div>
            }
            colon={false}
            style={{ marginBottom: 0 }}
          >
            {getFieldDecorator("set_water_level", {
              initialValue:
                recordApartment && recordApartment.set_water_level
                  ? recordApartment.set_water_level == 1
                  : false,
              valuePropName: "checked",
            })(
              <Checkbox>
                <span>
                  {" "}
                  <FormattedMessage {...messages.trangThaiKhaiBao} />
                </span>
                <span>
                  {" "}
                  <FormattedMessage {...messages.dinhMucNuoc} />
                </span>
              </Checkbox>
            )}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalEditApartment);
