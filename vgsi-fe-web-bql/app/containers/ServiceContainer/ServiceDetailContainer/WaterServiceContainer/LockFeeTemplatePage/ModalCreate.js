import React from "react";
import {
  Row,
  Col,
  Modal,
  Button,
  Form,
  Select,
  Spin,
  DatePicker,
  Divider,
  InputNumber,
} from "antd";
import { formatPrice, covertStringMonthYearToTime } from "../../../../../utils";
import {
  fetchApartment,
  fetchLastMonthFee,
  fetchDescriptionFee,
  clearCacheModal,
} from "./actions";
import moment from "moment";
import InputNumberFormat from "../../../../../components/InputNumberFormat";
import _ from "lodash";
import { injectIntl } from "react-intl";
import messages from "../messages";

import("./index.less");
const formItemLayout = {
  labelCol: {
    md: { span: 24 },
    lg: { span: 7 },
  },
  wrapperCol: {
    md: { span: 24 },
    lg: { span: 14 },
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
    let lastFee = props.lockFeeTemplateWaterPage.lastMonthFee[`apa-${id}`];
    if (!!lastFee && !!lastFee.data && lastFee.data.last_index <= end_index) {
      props.dispatch(
        fetchDescriptionFee({
          apartment_id: id,
          lock_time: lock_time.unix(),
          end_index,
          start_index: lastFee.data.last_index,
          service_map_management_id: props.waterServiceContainer.data.id,
        })
      );
    }
  }
}, 300);
/* eslint-disable react/prefer-stateless-function */
@Form.create({
  onValuesChange: (props, changedValues, allValues) => {
    _fetchDescription(props, changedValues, allValues);
  },
})
class ModalCreate extends React.PureComponent {
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
    const { currentEdit, form, lockFeeTemplateWaterPage } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      const { descriptionFee, lastMonthFee } = lockFeeTemplateWaterPage;
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
            }${this.props.intl.formatMessage(messages.totalWater, {
              total: descriptionFee.data.total_index,
            })}\n${this.props.intl.formatMessage(messages.totalPrice, {
              total: descriptionFee.data.total_money,
            })}`,
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
            }${this.props.intl.formatMessage(messages.totalWater, {
              total: descriptionFee.data.total_index,
            })}\n${this.props.intl.formatMessage(messages.totalPrice, {
              total: descriptionFee.data.total_money,
            })}`,
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
            service_map_management_id: nextProps.waterServiceContainer.data.id,
          })
        );
        this.props.dispatch(
          fetchDescriptionFee({
            apartment_id: nextProps.currentEdit.apartment_id,
            lock_time: nextProps.currentEdit.lock_time,
            end_index: nextProps.currentEdit.end_index,
            start_index: nextProps.currentEdit.start_index,
            service_map_management_id: nextProps.waterServiceContainer.data.id,
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
    const { visible, setState, lockFeeTemplateWaterPage, currentEdit } =
      this.props;
    const formatMessage = this.props.intl.formatMessage;
    const { descriptionFee, lastMonthFee } = lockFeeTemplateWaterPage;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    let apartment_id = getFieldValue("apartment_id");
    let lastFee = undefined;
    if (apartment_id) {
      let id = apartment_id.split(":")[0];
      lastFee = lastMonthFee[`apa-${id}`];
    }
    if (lockFeeTemplateWaterPage.success && isSave) {
      this.props.history.push("/main/service/detail/water/lock");
    }
    return (
      <Modal
        title={
          currentEdit
            ? formatMessage(messages.editPayment)
            : formatMessage(messages.addPayment)
        }
        visible={visible}
        onCancel={() => {
          if (lockFeeTemplateWaterPage.creating) return;
          setState({
            visible: false,
          });
        }}
        maskClosable={false}
        width="35%"
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
              loading={lockFeeTemplateWaterPage.creating && isSaveDraff}
              onClick={() => {
                this.setState({ isSaveDraff: true });
                this.handlerUpdate(false);
              }}
            >
              {formatMessage(messages.saveDraft)}
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
              loading={lockFeeTemplateWaterPage.creating && isSave}
              onClick={() => {
                this.setState({ isSave: true });
                this.handlerUpdate(true);
              }}
              type="primary"
            >
              {formatMessage(messages.save)}
            </Button>
          </Row>
        }
      >
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item label={formatMessage(messages.property)}>
            {getFieldDecorator("apartment_id", {
              initialValue: currentEdit
                ? `${currentEdit.apartment_id}:1`
                : undefined,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.propertyRequired),
                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback(formatMessage(messages.propertyEmpty));
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
                loading={lockFeeTemplateWaterPage.apartment.loading}
                showSearch
                placeholder={formatMessage(messages.selectProperty)}
                optionFilterProp="children"
                notFoundContent={
                  lockFeeTemplateWaterPage.apartment.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                onSearch={this._onSearch}
                allowClear
                onChange={(value, opt) => {
                  if (!opt) {
                    this._onSearch("");
                  }
                }}
                disabled={!!currentEdit}
              >
                {lockFeeTemplateWaterPage.apartment.items.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}:${gr.status}`}
                      onClick={() => {
                        this.props.dispatch(
                          fetchLastMonthFee({
                            apartment_id: gr.id,
                            id: currentEdit ? currentEdit.id : undefined,
                            service_map_management_id:
                              this.props.waterServiceContainer.data.id,
                          })
                        );
                      }}
                    >{`${gr.name} (${gr.parent_path})${
                      gr.status == 0
                        ? ` - ${formatMessage(messages.empty)}`
                        : ""
                    }`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.lockDate)}>
            {getFieldDecorator("lock_time", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.lock_time)
                : undefined,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.lockDateRequired),
                  whitespace: true,
                  type: "object",
                },
              ],
            })(
              <DatePicker
                placeholder={formatMessage(messages.chooseLockDate)}
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
                    current &&
                    current <
                      moment
                        .unix(
                          !!lastFee && !!lastFee.data
                            ? lastFee.data.lock_time
                            : undefined
                        )
                        .endOf("day")
                  );
                }}
              />
            )}
          </Form.Item>
          {!!getFieldValue("lock_time") && (
            <Form.Item label={formatMessage(messages.feeOfMonth)}>
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
          <Form.Item label={formatMessage(messages.duDauKy)}>
            <span style={{ fontWeight: "bold" }}>
              {!!lastFee && !!lastFee.data ? lastFee.data.last_index : ""}
            </span>
          </Form.Item>
          <Form.Item label={formatMessage(messages.duCuoiKy)}>
            {getFieldDecorator("end_index", {
              initialValue: currentEdit ? currentEdit.end_index : 0,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.soDuRequired),
                  whitespace: true,
                  type: "number",
                },
                // {
                //   required: true,
                //   message: formatMessage(messages.soDuMax),
                //   whitespace: true,
                //   type: "number",
                //   max: 10000000,
                // },
              ],
            })(
              <InputNumber style={{ width: "100%" }} maxLength={15} />
              // <InputNumberFormat max={10000000} style={{ width: "100%" }} />
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.description)}>
            {!descriptionFee.loading &&
            !!descriptionFee.data &&
            getFieldValue("fee_of_month") ? (
              <Col style={{ minHeight: 150 }}>
                {`${formatMessage(messages.totalWaterMonth)} ${
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
                    {formatMessage(messages.total)} :{" "}
                  </span>
                  {`      ${descriptionFee.data.total_index} (m3)`}
                </span>
                <Divider style={{ margin: "8px 0" }} />
                <span style={{ fontWeight: "bold", fontStyle: "italic" }}>
                  <span style={{ fontWeight: "normal" }}>
                    {formatMessage(messages.totalPriceRaw)} :{" "}
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

export default injectIntl(ModalCreate);
