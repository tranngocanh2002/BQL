import {
  Button,
  Col,
  DatePicker,
  Divider,
  Form,
  InputNumber,
  Modal,
  Row,
  Select,
  Spin,
} from "antd";
import _ from "lodash";
import moment from "moment";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { covertStringMonthYearToTime, formatPrice } from "../../../../../utils";
import messages from "../messages";
import {
  clearCacheModal,
  fetchApartment,
  fetchDescriptionFee,
  fetchLastMonthFee,
} from "./actions";

import("./index.less");
const formItemLayout = {
  labelCol: {
    md: { span: 24 },
    lg: { span: 24 },
    xl: { span: 24 },
    xxl: { span: 7 },
  },
  wrapperCol: {
    md: { span: 24 },
    lg: { span: 24 },
    xl: { span: 24 },
    xxl: { span: 17 },
  },
};

const _fetchDescription = _.debounce((props, changedValues, allValues) => {
  let apartment_id = allValues.apartment_id;
  let lock_time = allValues.lock_time;
  let end_index = allValues.end_index;
  if (
    !!apartment_id &&
    !!lock_time &&
    end_index != undefined &&
    end_index > 0
  ) {
    let id = apartment_id.split(":")[0];
    let status = apartment_id.split(":")[1];
    let lastFee = props.lockFeeTemplateElectricPage.lastMonthFee[`apa-${id}`];
    if (
      !!lastFee &&
      !!lastFee.data &&
      lastFee.data.last_index <= end_index &&
      status == 1
    ) {
      props.dispatch(
        fetchDescriptionFee({
          apartment_id: id,
          lock_time: lock_time.unix(),
          end_index,
          start_index: lastFee.data.last_index,
          service_map_management_id: props.electricServiceContainer.data.id,
        })
      );
    }
  }
}, 600);
/* eslint-disable react/prefer-stateless-function */
@Form.create({
  onValuesChange: (props, changedValues, allValues) => {
    _fetchDescription(props, changedValues, allValues);
  },
})
export class ModelCreate extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      isSaveDraff: false,
      isSave: false,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };

  handlerUpdate = (need_approve) => {
    const { currentEdit, form, lockFeeTemplateElectricPage } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      const { descriptionFee, lastMonthFee } = lockFeeTemplateElectricPage;
      let apartment_id = values.apartment_id.split(":");
      let lastFee = undefined;
      if (apartment_id) {
        lastFee = lastMonthFee[`apa-${apartment_id[0]}`];
      }
      if (currentEdit) {
        this.props.updatePayment &&
          this.props.updatePayment({
            ...values,
            fee_of_month: Number.isInteger(values.fee_of_month)
              ? values.fee_of_month
              : covertStringMonthYearToTime(values.fee_of_month),
            apartment_id: apartment_id[0],
            end_index: values.end_index,
            lock_time: values.lock_time.unix(),
            start_index: lastFee.data.last_index,
            description: `${
              descriptionFee.data.description
            }${`Tổng : ${descriptionFee.data.total_index} (m3)`}\n${`Thành tiền : ${formatPrice(
              descriptionFee.data.total_money
            )} đ`}`,
            total_money: descriptionFee.data.total_money,
            need_approve,
          });
      } else {
        this.props.addPayment &&
          this.props.addPayment({
            ...values,
            fee_of_month: Number.isInteger(values.fee_of_month)
              ? values.fee_of_month
              : covertStringMonthYearToTime(values.fee_of_month),
            apartment_id: apartment_id[0],
            end_index: values.end_index,
            lock_time: values.lock_time.unix(),
            start_index: lastFee.data.last_index,
            description: `${
              descriptionFee.data.description
            }${`Tổng : ${descriptionFee.data.total_index} (m3)`}\n${`Thành tiền : ${formatPrice(
              descriptionFee.data.total_money
            )} đ`}`,
            total_money: descriptionFee.data.total_money,
            need_approve,
          });
      }
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
      if (nextProps.visible && !!nextProps.currentEdit) {
        this._onSearch(nextProps.currentEdit.apartment_name);
        this.props.dispatch(
          fetchLastMonthFee({
            apartment_id: nextProps.currentEdit.apartment_id,
            id: nextProps.currentEdit.id,
            service_map_management_id:
              nextProps.electricServiceContainer.data.id,
          })
        );
        this.props.dispatch(
          fetchDescriptionFee({
            apartment_id: nextProps.currentEdit.apartment_id,
            lock_time: nextProps.currentEdit.lock_time,
            end_index: nextProps.currentEdit.end_index,
            start_index: nextProps.currentEdit.start_index,
            service_map_management_id:
              nextProps.electricServiceContainer.data.id,
          })
        );
      }
      if (!nextProps.visible) {
        this.props.dispatch(clearCacheModal());
        this.setState({ isSaveDraff: false });
        this._onSearch("");
      }
    }
  }

  render() {
    const { isSaveDraff, isSave } = this.state;
    const {
      visible,
      setState,
      lockFeeTemplateElectricPage,
      currentEdit,
      intl,
    } = this.props;
    const { descriptionFee, lastMonthFee } = lockFeeTemplateElectricPage;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    let apartment_id = getFieldValue("apartment_id");
    let lastFee = undefined;
    if (apartment_id) {
      let id = apartment_id.split(":")[0];
      lastFee = lastMonthFee[`apa-${id}`];
    }
    if (lockFeeTemplateElectricPage.success && isSave) {
      this.props.history.push("/main/service/detail/electric/lock");
    }
    return (
      <Modal
        title={
          currentEdit ? (
            <FormattedMessage {...messages.editFee} />
          ) : (
            <FormattedMessage {...messages.createFee} />
          )
        }
        visible={visible}
        onCancel={() => {
          if (lockFeeTemplateElectricPage.creating) return;
          setState({
            visible: false,
          });
        }}
        maskClosable={false}
        width={600}
        footer={
          <Row>
            <Button
              disabled={
                descriptionFee.loading ||
                !lastFee ||
                !lastFee.data ||
                lastFee.loading ||
                getFieldValue("end_index") == undefined ||
                getFieldValue("end_index") == 0 ||
                !getFieldValue("fee_of_month") ||
                lastFee.data.last_index > getFieldValue("end_index")
              }
              onClick={() => {
                this.setState({ isSaveDraff: true });
                this.handlerUpdate(false);
              }}
              loading={lockFeeTemplateElectricPage.creating && isSaveDraff}
            >
              <FormattedMessage {...messages.saveDraft} />
            </Button>
            <Button
              disabled={
                descriptionFee.loading ||
                !lastFee ||
                !lastFee.data ||
                lastFee.loading ||
                getFieldValue("end_index") == undefined ||
                getFieldValue("end_index") == 0 ||
                !getFieldValue("fee_of_month") ||
                lastFee.data.last_index > getFieldValue("end_index")
              }
              onClick={() => {
                this.setState({ isSave: true });
                this.handlerUpdate(true);
              }}
              loading={lockFeeTemplateElectricPage.creating && isSave}
              type="primary"
            >
              <FormattedMessage {...messages.saveAndApprove} />
            </Button>
          </Row>
        }
      >
        <Form {...formItemLayout} className="serviceProviderPage">
          <Form.Item
            label={<FormattedMessage {...messages.property} />}
            className="ant-col"
          >
            {getFieldDecorator("apartment_id", {
              initialValue: currentEdit
                ? `${currentEdit.apartment_id}:1`
                : undefined,
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.errorEmptyProperty} />
                  ),
                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    const form = this.props.form;
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback(
                          <FormattedMessage
                            {...messages.errorEmptyCurrentProperty}
                          />
                        );
                      } else {
                        callback();
                      }
                    } else {
                      callback();
                    }
                  },
                },
              ],
            })(
              <Select
                loading={lockFeeTemplateElectricPage.apartment.loading}
                showSearch
                placeholder={<FormattedMessage {...messages.plhProperty} />}
                optionFilterProp="children"
                notFoundContent={
                  lockFeeTemplateElectricPage.apartment.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                allowClear
                onSearch={this._onSearch}
                onChange={(value, opt) => {
                  if (!opt) {
                    this._onSearch("");
                  }
                }}
                disabled={!!currentEdit}
              >
                {lockFeeTemplateElectricPage.apartment.items.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}:${gr.status}`}
                      onClick={(e) => {
                        this.props.dispatch(
                          fetchLastMonthFee({
                            apartment_id: gr.id,
                            id: currentEdit ? currentEdit.id : undefined,
                            service_map_management_id:
                              this.props.electricServiceContainer.data.id,
                          })
                        );
                      }}
                    >{`${gr.name} (${gr.parent_path})${
                      gr.status == 0
                        ? ` - ${this.props.intl.formatMessage(messages.empty)}`
                        : ""
                    }`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.closeDate} />}
            className="ant-col"
          >
            {getFieldDecorator("lock_time", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.lock_time)
                : undefined,
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.errorEmptyCloseDate} />
                  ),
                  whitespace: true,
                  type: "object",
                },
              ],
            })(
              <DatePicker
                placeholder={intl.formatMessage({
                  ...messages.selectCloseDate,
                })}
                format="DD/MM/YYYY"
                style={{ width: "100%" }}
                disabled={
                  !lastFee ||
                  lastFee.loading ||
                  getFieldValue("apartment_id").slice(-1) === "0"
                }
                onChange={() => {
                  setFieldsValue({
                    fee_of_month: null,
                  });
                }}
                disabledDate={(current) => {
                  // Can not select days before today and today
                  return (
                    current && current > moment().add(1, "days").startOf("day")
                  );
                }}
              />
            )}
          </Form.Item>
          {!!getFieldValue("lock_time") && (
            <Form.Item
              label={<FormattedMessage {...messages.feeOfMonth} />}
              className="ant-col"
            >
              {getFieldDecorator("fee_of_month", {
                initialValue: currentEdit
                  ? moment.unix(currentEdit.fee_of_month).format("MM/YYYY")
                  : moment(getFieldValue("lock_time"))
                      .startOf("month")
                      .subtract("month", 1)
                      .unix(),
              })(
                <Select>
                  {[
                    moment(getFieldValue("lock_time"))
                      .startOf("month")
                      .subtract("month", 1),
                    moment(getFieldValue("lock_time")).startOf("month"),
                  ].map((rr) => {
                    return (
                      <Select.Option key={rr.unix()} value={rr.unix()}>
                        {rr.format("MM/YYYY")}
                      </Select.Option>
                    );
                  })}
                </Select>
              )}
            </Form.Item>
          )}
          <Form.Item
            label={<FormattedMessage {...messages.sdDauKy} />}
            className="ant-col"
          >
            <span style={{ fontWeight: "bold" }}>
              {!!lastFee && !!lastFee.data ? lastFee.data.last_index : 0}
            </span>
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.sdCuoiKy} />}
            className="ant-col"
          >
            {getFieldDecorator("end_index", {
              initialValue: currentEdit ? currentEdit.end_index : 0,
              rules: [
                {
                  required: true,
                  message: <FormattedMessage {...messages.errorSoDu} />,
                  whitespace: true,
                  type: "number",
                },
                // {
                //   required: true,
                //   message: <FormattedMessage {...messages.errorLimitSoDu} />,
                //   whitespace: true,
                //   type: "number",
                //   // max: 10000000,
                // },
              ],
            })(
              <InputNumber
                style={{ width: "100%" }}
                // max={10000000}
                formatter={(value) => value}
                parser={(value) => value.replace(".", "")}
                maxLength={15}
              />
              // <InputNumberFormat max={10000000} style={{ width: "100%" }} />
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.explain} />}
            className="ant-col"
          >
            {}
            {!descriptionFee.loading &&
            !!descriptionFee.data &&
            getFieldValue("fee_of_month") ? (
              <Col style={{ minHeight: 150 }}>
                <FormattedMessage {...messages.electricityMonth} />
                {`: ${
                  Number.isInteger(getFieldValue("fee_of_month"))
                    ? moment.unix(getFieldValue("fee_of_month")).format("MM")
                    : getFieldValue("fee_of_month").split("/")[0]
                }`}
                <br />
                <span style={{ whiteSpace: "pre-wrap" }}>
                  {descriptionFee.data.description}
                </span>
                <span style={{ fontWeight: "bold", fontStyle: "italic" }}>
                  <span style={{ fontWeight: "normal" }}>
                    <FormattedMessage {...messages.consume} /> :{" "}
                  </span>
                  {`${descriptionFee.data.total_index} (kwh)`}
                </span>
                <br />
                <Divider style={{ margin: "8px 0" }} />
                <span style={{ fontWeight: "bold", fontStyle: "italic" }}>
                  <span style={{ fontWeight: "normal" }}>
                    <FormattedMessage {...messages.amount} /> :{" "}
                  </span>
                  {`${formatPrice(descriptionFee.data.total_money)} đ`}
                </span>
              </Col>
            ) : (
              <Row style={{ height: 150 }} />
            )}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModelCreate);
