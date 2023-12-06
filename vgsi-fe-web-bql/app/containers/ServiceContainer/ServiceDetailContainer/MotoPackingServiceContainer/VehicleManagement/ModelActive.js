import React from "react";
import { Row, Modal, Button, Form, DatePicker } from "antd";
import { fetchApartment } from "./actions";
import moment from "moment";
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
export default class extends React.PureComponent {
  constructor(props) {
    super(props);
  }

  handlerActive = () => {
    const { currentEdit, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      if (currentEdit.status == 1) {
        this.props.cancleVehicle &&
          this.props.cancleVehicle({
            id: currentEdit.id,
            cancel_date: values.end_date.unix(),
          });
      } else {
        this.props.activeVehicle &&
          this.props.activeVehicle({
            id: currentEdit.id,
            start_date: values.end_date.unix(),
          });
      }
      this.props.form.resetFields();
    });
  };

  render() {
    const { visible, setState, vihicleManagement, currentEdit } = this.props;
    const { getFieldDecorator, getFieldValue } = this.props.form;
    return (
      <Modal
        title={`${
          !!currentEdit && currentEdit.status == 1 ? "Hủy xe" : "Kích hoạt xe"
        } ${!!currentEdit && currentEdit.number} - ${!!currentEdit &&
          currentEdit.apartment_name}`}
        visible={visible}
        onCancel={() => {
          if (vihicleManagement.activing || vihicleManagement.cancling) return;
          setState({
            visibleActive: false,
          }, ()=> {
            this.props.form.resetFields();
          });
          
        }}
        maskClosable={false}
        footer={
          <Row>
            <Button
              loading={vihicleManagement.activing || vihicleManagement.cancling}
              onClick={() => {
                this.handlerActive();
              }}
            >
              {!!currentEdit && currentEdit.status == 1
                ? "Hủy xe"
                : "Kích hoạt xe"}
            </Button>
          </Row>
        }
      >
        <Form {...formItemLayout} className="ticketCategoryPage">
          <Form.Item
            label={
              !!currentEdit && currentEdit.status == 1
                ? "Ngày hủy xe"
                : "Kích hoạt xe sau ngày"
            }
          >
            {getFieldDecorator("end_date", {
              initialValue: !!currentEdit
                ? moment.unix(currentEdit.end_date)
                : undefined,
              rules: [
                {
                  type: "object",
                  required: true,
                  message: `Ngày ${
                    !!currentEdit && currentEdit.status == 1
                      ? "hủy xe"
                      : "kích hoạt xe"
                  }} không được để trống.`,
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
          {!!currentEdit &&
            currentEdit.status != 1 && (
              <div style={{ fontWeight: "bold" }}>
                Chúng tôi sẽ tính phí xe từ ngày
                {": "}
                {`${moment(getFieldValue("end_date"))
                  .add(1, "days")
                  .format("DD/MM/YYYY")}`}
              </div>
            )}
        </Form>
      </Modal>
    );
  }
}