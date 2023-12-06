import React from "react";
import { injectIntl } from "react-intl";

import { DatePicker, Form, Input, Modal, Select, Spin } from "antd";
import _ from "lodash";
import moment from "moment";
import NumericInput from "../../../../../components/NumericInput";
import messages from "../messages";
import { fetchApartment } from "./actions";
const monthFormat = "MM/YYYY";
const { MonthPicker, RangePicker } = DatePicker;
import("./index.less");
const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 12,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalCreate extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      showPickerColor: false,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };
  componentDidMount() {
    this._onSearch("");
  }

  handlerUpdate = () => {
    const { currentEdit, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      let apartment_id = values.apartment_id.split(":");
      if (currentEdit) {
        this.props.updatePayment &&
          this.props.updatePayment({
            ...values,
            status: 0,
            price: parseInt(values.price),
            fee_of_month: values.fee_of_month.unix(),
            day_expired: values.day_expired.unix(),
            apartment_id: apartment_id[0],
          });
      } else {
        this.props.addPayment &&
          this.props.addPayment({
            ...values,
            status: 0,
            price: parseInt(values.price),
            fee_of_month: values.fee_of_month.unix(),
            day_expired: values.day_expired.unix(),
            apartment_id: apartment_id[0],
          });
      }
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
      if (nextProps.visible && !!nextProps.currentEdit) {
        this._onSearch(
          nextProps.currentEdit ? nextProps.currentEdit.apartment_name : ""
        );
      }
    }
  }

  render() {
    const { showPickerColor } = this.state;
    const { visible, setState, paymentMotoPackingPage, currentEdit } =
      this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    return (
      <Modal
        title={currentEdit ? "Chỉnh sửa phí thanh toán" : "Tạo phí thanh toán"}
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (paymentMotoPackingPage.creating) return;
          setState({
            visible: false,
          });
        }}
        okText={currentEdit ? "Cập nhật" : "Thêm mới"}
        cancelText="Huỷ"
        okButtonProps={{ loading: paymentMotoPackingPage.creating }}
        cancelButtonProps={{ disabled: paymentMotoPackingPage.creating }}
        maskClosable={false}
      >
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item label={"Căn hộ"}>
            {getFieldDecorator("apartment_id", {
              initialValue: currentEdit
                ? `${currentEdit.apartment_id}:1`
                : undefined,
              rules: [
                {
                  required: true,
                  message: "Căn hộ không được để trống.",
                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    const form = this.props.form;
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback("Căn hộ được chọn đang trống.");
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
                loading={paymentMotoPackingPage.apartment.loading}
                showSearch
                placeholder="Chọn căn hộ"
                optionFilterProp="children"
                notFoundContent={
                  paymentMotoPackingPage.apartment.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                onSearch={this._onSearch}
              >
                {paymentMotoPackingPage.apartment.items.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}:${gr.status}`}
                    >{`${gr.name} (${gr.parent_path})${
                      gr.status == 0 ? " - Trống" : ""
                    }`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item label={"Mô tả"}>
            {getFieldDecorator("description", {
              initialValue: currentEdit ? currentEdit.description : undefined,
              rules: [],
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
          <Form.Item label={"Số tiền"}>
            {getFieldDecorator("price", {
              initialValue: currentEdit ? `${currentEdit.price}` : undefined,
              rules: [
                {
                  required: true,
                  message: "Số tiền không được để trống.",
                  whitespace: true,
                },
              ],
            })(<NumericInput maxLength={10} />)}
          </Form.Item>
          <Form.Item label={"Tháng"}>
            {getFieldDecorator("fee_of_month", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.fee_of_month)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: "Tháng thanh toán không được để trống.",
                },
              ],
            })(
              <MonthPicker
                style={{ width: "100%" }}
                placeholder="Chọn tháng"
                format={monthFormat}
              />
            )}
          </Form.Item>
          <Form.Item label={"Hạn thanh toán"}>
            {getFieldDecorator("day_expired", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.day_expired)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: "Hạn thanh toán không được để trống.",
                },
              ],
            })(
              <DatePicker
                style={{ width: "100%" }}
                placeholder="Chọn ngày"
                format={"DD/MM/YYYY"}
              />
            )}
          </Form.Item>
          {/* <Form.Item
                    label={'Trạng thái'}
                >
                    {getFieldDecorator('status', {
                        initialValue: !!currentEdit ? currentEdit.status : 0,
                        rules: [{ required: true, message: 'Trạng thái không được để trống.', whitespace: true, type: 'number' }],
                    })(
                        <Select
                            showSearch
                            placeholder="Chọn trạng thái"
                            optionFilterProp="children"
                            // onChange={onChange}
                            filterOption={(input, option) =>
                                option.props.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                            }
                        >
                            {
                                config.STATUS_SERVICE_PAYMENT.map(gr => {
                                    return <Select.Option key={`group-${gr.id}`} value={gr.id}>{gr.name}</Select.Option>
                                })
                            }
                        </Select>
                    )}
                </Form.Item> */}
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalCreate);
