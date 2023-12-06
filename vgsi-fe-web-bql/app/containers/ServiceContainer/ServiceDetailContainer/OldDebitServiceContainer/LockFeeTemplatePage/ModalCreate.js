import {
  Button,
  DatePicker,
  Form,
  Input,
  Modal,
  Row,
  Select,
  Spin,
} from "antd";
import _ from "lodash";
import moment from "moment";
import React from "react";
import { injectIntl } from "react-intl";
import InputNumberNagative from "../../../../../components/InputNumberNagative";
import messages from "../messages";
import { fetchApartment } from "./actions";
import("./index.less");
const { MonthPicker } = DatePicker;
const formItemLayout = {
  labelCol: {
    span: 7,
  },
  wrapperCol: {
    span: 14,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalCreate extends React.PureComponent {
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
            apartment_id: apartment_id[0],
            fee_of_month: moment(values.fee_of_month).unix(),
            need_approve,
          });
      } else {
        this.props.addPayment &&
          this.props.addPayment({
            ...values,
            apartment_id: apartment_id[0],
            fee_of_month: moment(values.fee_of_month).unix(),
            need_approve,
          });
      }
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
      if (nextProps.visible) {
        this._onSearch(
          nextProps.currentEdit ? nextProps.currentEdit.apartment_name : ""
        );
      } else {
        this.setState({ isSaveDraff: false });
        this._onSearch("");
      }
    }
  }

  render() {
    const { visible, setState, lockFeeTemplateOldDebitPage, currentEdit } =
      this.props;
    const { getFieldDecorator } = this.props.form;
    const { isSaveDraff, isSave } = this.state;
    const formatMessage = this.props.intl.formatMessage;
    if (lockFeeTemplateOldDebitPage.success && isSave) {
      this.props.history.push("/main/service/detail/old_debit/lock");
    }
    return (
      <Modal
        title={
          currentEdit
            ? formatMessage(messages.editFee)
            : formatMessage(messages.createFee)
        }
        visible={visible}
        onCancel={() => {
          if (lockFeeTemplateOldDebitPage.creating) return;
          setState({
            visible: false,
          });
          this.props.form.resetFields();
        }}
        maskClosable={false}
        width={600}
        footer={
          <Row>
            <Button
              loading={lockFeeTemplateOldDebitPage.creating && isSaveDraff}
              onClick={() => {
                this.setState({ isSaveDraff: true });
                this.handlerUpdate(false);
              }}
            >
              {formatMessage(messages.draft)}
            </Button>
            <Button
              loading={lockFeeTemplateOldDebitPage.creating && isSave}
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
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item label={formatMessage(messages.property)}>
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
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback(
                          formatMessage(messages.errorEmptyCurrentProperty),
                          values
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
                loading={lockFeeTemplateOldDebitPage.apartment.loading}
                showSearch
                placeholder={formatMessage(messages.selectProperty)}
                optionFilterProp="children"
                notFoundContent={
                  lockFeeTemplateOldDebitPage.apartment.loading ? (
                    <Spin size="small" />
                  ) : null
                }
                allowClear
                onChange={(value, opt) => {
                  if (!opt) {
                    this._onSearch("");
                  }
                }}
                onSearch={this._onSearch}
                disabled={!!currentEdit}
              >
                {lockFeeTemplateOldDebitPage.apartment.items.map((gr) => {
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
          <Form.Item label={formatMessage(messages.month)}>
            {getFieldDecorator("fee_of_month", {
              initialValue: currentEdit
                ? moment.unix(currentEdit.fee_of_month)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: formatMessage(messages.errorFeeOfMonth),
                },
              ],
            })(
              <MonthPicker
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.selectMonth)}
                format={"MM/YYYY"}
              />
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.money)}>
            {getFieldDecorator("total_money", {
              initialValue: currentEdit ? currentEdit.total_money : undefined,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.emptyMoney),
                  whitespace: true,
                  type: "number",
                },
              ],
            })(
              <InputNumberNagative maxLength={19} style={{ width: "100%" }} />
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.description)}>
            {getFieldDecorator("description", {
              initialValue: currentEdit ? currentEdit.description : undefined,
              rules: [],
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
          <Form.Item label={`${formatMessage(messages.description)} (EN)`}>
            {getFieldDecorator("description_en", {
              initialValue: currentEdit
                ? currentEdit.description_en
                : undefined,
              rules: [],
            })(<Input.TextArea rows={4} maxLength={500} />)}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalCreate);
