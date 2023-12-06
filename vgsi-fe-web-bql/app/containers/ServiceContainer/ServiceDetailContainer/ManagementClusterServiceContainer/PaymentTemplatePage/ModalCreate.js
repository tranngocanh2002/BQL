import React from "react";
import { Row, Col, Modal, Button, Form, Select, Spin, Divider } from "antd";
import { formatPrice } from "../../../../../utils";
import {
  fetchApartment,
  fetchDescriptionFee,
  clearCacheModal,
} from "./actions";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "../messages";
import _ from "lodash";

import("./index.less");
const formItemLayout = {
  labelCol: {
    md: { span: 24 },
    lg: { span: 24 },
    xl: { span: 24 },
    xxl: { span: 5 },
  },
  wrapperCol: {
    md: { span: 24 },
    lg: { span: 24 },
    xl: { span: 24 },
    xxl: { span: 18 },
  },
};

const _fetchDescription = _.debounce((props, changedValues, allValues) => {
  let apartment_id = allValues.apartment_id;
  if (apartment_id) {
    let id = apartment_id.split(":")[0];
    props.dispatch(
      fetchDescriptionFee({
        apartment_id: id,
        count_month: allValues.count_month,
        service_map_management_id:
          props.managementClusterServiceContainer.data.id,
      })
    );
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
    };
    this._onSearch = _.debounce(this.onSearch, 300);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };
  componentDidMount() {
    this._onSearch("");
  }

  handlerUpdate = (need_approve) => {
    const { currentEdit, form, paymentTemplateManagementClusterPage } =
      this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      const { descriptionFee } = paymentTemplateManagementClusterPage;
      let apartment_id = values.apartment_id.split(":");
      if (currentEdit) {
        this.props.updatePayment &&
          this.props.updatePayment({
            ...values,
            apartment_id: apartment_id[0],
            service_map_management_id: currentEdit.service_map_management_id,
            count_month: values.count_month,
            description: `${
              descriptionFee.data.description
            }\n${`Thành tiền : ${formatPrice(
              descriptionFee.data.total_money
            )} đ`}`,
            need_approve,
          });
      } else {
        this.props.addPayment &&
          this.props.addPayment({
            ...values,
            apartment_id: apartment_id[0],
            description: `${
              descriptionFee.data.description
            }\n${`Thành tiền : ${formatPrice(
              descriptionFee.data.total_money
            )} đ`}`,
            need_approve,
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
        this.props.dispatch(
          fetchDescriptionFee({
            apartment_id: nextProps.currentEdit.apartment_id,
            count_month: nextProps.currentEdit.count_month,
            service_map_management_id:
              nextProps.managementClusterServiceContainer.data.id,
          })
        );
      }
      if (!nextProps.visible) {
        this.props.dispatch(clearCacheModal());
        this.setState({ isSaveDraff: false });
      }
    }
  }

  render() {
    const {
      visible,
      setState,
      paymentTemplateManagementClusterPage,
      currentEdit,
    } = this.props;
    const formatMessage = this.props.intl.formatMessage;
    const { getFieldDecorator } = this.props.form;
    const { descriptionFee } = paymentTemplateManagementClusterPage;
    const { isSaveDraff, isSave } = this.state;
    if (paymentTemplateManagementClusterPage.success && isSave) {
      this.props.history.push("/main/service/detail/apartment-fee/payment");
    }
    return (
      <Modal
        title={
          currentEdit
            ? formatMessage(messages.editFee)
            : formatMessage(messages.createFee)
        }
        visible={visible}
        // width={600}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (paymentTemplateManagementClusterPage.creating) return;
          setState({
            visible: false,
          });
        }}
        maskClosable={false}
        footer={
          <Row>
            <Button
              disabled={descriptionFee.loading || !descriptionFee.data}
              loading={
                paymentTemplateManagementClusterPage.creating && isSaveDraff
              }
              onClick={() => {
                this.handlerUpdate(false);
                this.setState({ isSaveDraff: true });
              }}
            >
              {formatMessage(messages.draft)}
            </Button>
            <Button
              disabled={descriptionFee.loading || !descriptionFee.data}
              loading={paymentTemplateManagementClusterPage.creating && isSave}
              onClick={() => {
                this.setState({ isSave: true });
                this.handlerUpdate(true);
              }}
              type="primary"
            >
              {formatMessage(messages.saveAndApprove)}
            </Button>
          </Row>
        }
      >
        <Form {...formItemLayout} className="serviceProviderPage">
          <Form.Item
            label={formatMessage(messages.property)}
            className="ant-col"
          >
            {getFieldDecorator("apartment_id", {
              initialValue: currentEdit
                ? `${currentEdit.apartment_id}:1`
                : undefined,
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
                loading={paymentTemplateManagementClusterPage.apartment.loading}
                showSearch
                allowClear
                placeholder={formatMessage(messages.plhProperty)}
                optionFilterProp="children"
                notFoundContent={
                  paymentTemplateManagementClusterPage.apartment.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                onSearch={this._onSearch}
                onChange={(value, opt) => {
                  if (!opt) {
                    this._onSearch("");
                  }
                }}
              >
                {paymentTemplateManagementClusterPage.apartment.items.map(
                  (gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}:${gr.status}`}
                      >{`${gr.name} (${gr.parent_path})${
                        gr.status == 0
                          ? `- ${formatMessage(messages.empty)}`
                          : ""
                      }`}</Select.Option>
                    );
                  }
                )}
              </Select>
            )}
          </Form.Item>
          <Form.Item
            label={formatMessage(messages.numberMonth)}
            className="ant-col"
          >
            {getFieldDecorator("count_month", {
              initialValue: currentEdit ? currentEdit.count_month : 1,
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
          <Form.Item
            label={formatMessage(messages.explain)}
            className="ant-col"
          >
            {descriptionFee &&
            !descriptionFee.loading &&
            !!descriptionFee.data ? (
              <Col style={{ minHeight: 150 }}>
                <span style={{ whiteSpace: "pre-wrap" }}>
                  {this.props.language === "en"
                    ? descriptionFee.data.description_en
                    : descriptionFee.data.description}
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
