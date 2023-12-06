import React from "react";
import { Row, Col, Modal, Button, Form, Select, Spin, Divider } from "antd";
import {
  fetchDescriptionFee,
  clearCacheModal,
  fetchVehicle,
  fetchApartment,
} from "./actions";
import("./index.less");
import { formatPrice } from "../../../../../utils";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "../messages";
import _ from "lodash";
const formItemLayout = {
  labelCol: {
    span: 7,
  },
  wrapperCol: {
    span: 14,
  },
};

const _fetchDescription = _.debounce((props, changedValues, allValues) => {
  let count_month = allValues.count_month;
  let service_management_vehicle_id = allValues.service_management_vehicle_id;
  if (service_management_vehicle_id) {
    props.dispatch(
      fetchDescriptionFee({
        service_management_vehicle_id: service_management_vehicle_id,
        count_month,
        service_map_management_id: props.motoPackingServiceContainer.data.id,
      })
    );
  } else {
    props.dispatch(fetchDescriptionFee());
  }
}, 300);
/* eslint-disable react/prefer-stateless-function */
@Form.create({
  onValuesChange: (props, changedValues, allValues) => {
    _fetchDescription(props, changedValues, allValues);
  },
})
export class ModalCreate extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      showPickerColor: false,
      isSaveDraff: false,
      isSave: false,
      currentVehicle: undefined,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
    this._onSearchApartment = _.debounce(this.onSearchApartment, 300);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchVehicle({ apartment_id: keyword }));
  };
  onSearchApartment = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };
  componentDidMount() {
    this._onSearchApartment("");
  }

  handlerUpdate = (need_approve) => {
    const { form, lockFeeTemplatePage } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      const { descriptionFee } = lockFeeTemplatePage;

      const { currentVehicle } = this.state;
      let apartment_id = values.apartment_id.split(":");
      this.props.addPayment &&
        this.props.addPayment({
          ...values,
          apartment_id: apartment_id[0],
          service_parking_level_id: currentVehicle.service_parking_level_id,
          description: `${
            descriptionFee.data.description
          }\n${`Thành tiền : ${formatPrice(
            descriptionFee.data.total_money
          )} Đ`}`,
          need_approve,
        });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
      if (!nextProps.visible) {
        this.props.dispatch(clearCacheModal());
        this.setState({
          currentVehicle: undefined,
          isSaveDraff: false,
        });
      }
    }
  }

  render() {
    const { currentVehicle, isSaveDraff, isSave } = this.state;
    const { visible, setState, lockFeeTemplatePage } = this.props;
    const formatMessage = this.props.intl.formatMessage;
    const { descriptionFee } = lockFeeTemplatePage;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    if (lockFeeTemplatePage.success && isSave) {
      this.props.history.push("/main/service/detail/moto-packing/lock");
    }
    return (
      <Modal
        title={formatMessage(messages.createFee)}
        visible={visible}
        onCancel={() => {
          if (lockFeeTemplatePage.creating) return;
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
                getFieldValue("service_management_vehicle_id") == undefined
              }
              loading={lockFeeTemplatePage.creating && isSaveDraff}
              onClick={() => {
                this.handlerUpdate(false);
                this.setState({ isSaveDraff: true, isSave: false });
                setState({ visible: false });
              }}
            >
              {formatMessage(messages.draft)}
            </Button>
            <Button
              disabled={
                descriptionFee.loading ||
                getFieldValue("service_management_vehicle_id") == undefined
              }
              loading={lockFeeTemplatePage.creating && isSave && !isSaveDraff}
              onClick={() => {
                this.setState({ isSave: true, isSaveDraff: false });
                setState({ visible: false });
                this.handlerUpdate(true);
              }}
              type="primary"
            >
              {formatMessage(messages.saveAndApprove)}
            </Button>
          </Row>
        }
      >
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item label={formatMessage(messages.property)}>
            {getFieldDecorator("apartment_id", {
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.errorEmptyProperty),
                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    const form = this.props.form;
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback(
                          formatMessage(messages.errorEmptyCurrentProperty)
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
                loading={lockFeeTemplatePage.apartment.loading}
                showSearch
                allowClear
                placeholder={formatMessage(messages.selectProperty)}
                optionFilterProp="children"
                notFoundContent={
                  lockFeeTemplatePage.apartment.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                onSearch={this._onSearchApartment}
                onSelect={(value) => {
                  setFieldsValue({
                    service_management_vehicle_id: undefined,
                  });
                  let values = value.split(":");
                  this.onSearch(values[0]);
                }}
                onChange={(value, opt) => {
                  if (!opt) {
                    this._onSearchApartment("");
                  }
                }}
              >
                {lockFeeTemplatePage.apartment.items.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}:${gr.status}`}
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

          <Form.Item label={formatMessage(messages.bienSo)}>
            {getFieldDecorator("service_management_vehicle_id", {
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.errorEmptyLicensePlate),
                  whitespace: true,
                  type: "number",
                },
              ],
            })(
              <Select
                loading={lockFeeTemplatePage.vehicles.loading}
                placeholder={formatMessage(messages.selectLicensePlate)}
                optionFilterProp="children"
                notFoundContent={
                  lockFeeTemplatePage.vehicles.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                allowClear
              >
                {lockFeeTemplatePage.vehicles.items.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={gr.id}
                      onClick={(e) => {
                        this.setState({
                          currentVehicle: gr,
                        });
                      }}
                    >
                      {gr.number}
                    </Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.numberMonth)}>
            {getFieldDecorator("count_month", {
              initialValue: 1,
            })(
              <Select>
                {[1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 24].map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr}`}
                      value={gr}
                    >{`${gr} ${formatMessage(messages.thang)}`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.typePaymentFee)}>
            {!!currentVehicle && (
              <span
                style={{ fontWeight: "bold" }}
              >{`${currentVehicle.service_parking_level_name}`}</span>
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.explain)}>
            {!descriptionFee.loading && !!descriptionFee.data ? (
              <Col style={{ minHeight: 150 }}>
                <span style={{ whiteSpace: "pre-wrap" }}>
                  {descriptionFee.data.description}
                </span>
                <Divider style={{ margin: "8px 0" }} />
                <span style={{ fontWeight: "bold", fontStyle: "italic" }}>
                  <span style={{ fontWeight: "normal" }}>
                    {formatMessage(messages.amount)} :{" "}
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
