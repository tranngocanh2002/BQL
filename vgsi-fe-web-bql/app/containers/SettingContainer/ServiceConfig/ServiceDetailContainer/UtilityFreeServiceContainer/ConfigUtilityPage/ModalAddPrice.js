/*
 * Created by duydatpham@gmail.com on 04/02/2020
 * Copyright (c) 2020 duydatpham@gmail.com
 */
import React from "react";
import {
  Modal,
  Form,
  Input,
  Select,
  InputNumber,
  Row,
  Col,
  TimePicker,
} from "antd";
import moment from "moment";
import InputNumberFormat from "../../../../../../components/InputNumberFormat";
import { injectIntl } from "react-intl";
import messages from "../../../messages";
@Form.create()
export class ModalAddPrice extends React.PureComponent {
  _onSave = (status, message) => {
    const { onSave, form } = this.props;
    const { validateFieldsAndScroll, setFields } = form;

    validateFieldsAndScroll((errors, values) => {
      if (errors) return;
      !!onSave && onSave(values);
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible && !nextProps.visible) {
      this.props.form.resetFields();
    }
  }

  render() {
    const { getFieldDecorator } = this.props.form;

    return (
      <Modal
        {...this.props}
        onOk={this._onSave}
        okText={this.props.intl.formatMessage(messages.add)}
      >
        <Form>
          {this.props.type == 1 && (
            <Form.Item
              label={this.props.intl.formatMessage(messages.price)}
              style={{ marginTop: 0, marginBottom: 0 }}
            >
              {getFieldDecorator(
                this.props.bookingType == 2 ? "price_hourly" : "price_adult",
                {
                  initialValue: "",
                  rules: [
                    {
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyPrice
                      ),
                      whitespace: true,
                      type: "number",
                      min: 1,
                    },
                  ],
                }
              )(<InputNumberFormat style={{ width: "100%" }} maxLength={50} />)}
            </Form.Item>
          )}
          <Row>
            <Col span={11}>
              <Form.Item
                label={this.props.intl.formatMessage(messages.timeStart)}
                style={{ marginTop: 0, marginBottom: 0 }}
              >
                {getFieldDecorator("start_time", {
                  initialValue: moment("08:00", "HH:mm"),
                  rules: [
                    {
                      type: "object",
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyTimeStart
                      ),
                    },
                  ],
                })(<TimePicker style={{ width: "100%" }} format="HH:mm" />)}
              </Form.Item>
            </Col>
            <Col span={11} offset={2}>
              <Form.Item
                label={this.props.intl.formatMessage(messages.timeEnd)}
                style={{ marginTop: 0, marginBottom: 0 }}
              >
                {getFieldDecorator("end_time", {
                  initialValue: moment("09:00", "HH:mm"),
                  rules: [
                    {
                      type: "object",
                      required: true,
                      message: this.props.intl.formatMessage(
                        messages.emptyTimeEnd
                      ),
                    },
                  ],
                })(<TimePicker style={{ width: "100%" }} format="HH:mm" />)}
              </Form.Item>
            </Col>
          </Row>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalAddPrice);
