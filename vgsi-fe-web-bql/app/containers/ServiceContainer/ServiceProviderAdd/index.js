/**
 *
 * ServiceProviderAdd
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Card, Form, Input, Modal, Row, Select } from "antd";
import Col from "antd/es/col";
import { Redirect } from "react-router";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Avatar from "../../../components/Avatar";
import Page from "../../../components/Page/Page";
import { getFullLinkImage } from "../../../connection";
import { config } from "../../../utils";
import { createProviderAction, defaultAction } from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectServiceProviderAdd from "./selectors";
import("./index.less");

const formItemLayout = {
  labelCol: {
    xl: { span: 12 },
    lg: { span: 9 },
  },
  wrapperCol: {
    xl: { span: 11 },
    lg: { span: 11 },
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ServiceProviderAdd extends React.PureComponent {
  state = {
    imageUrl: undefined,
  };

  handleOk = () => {
    const { dispatch, form } = this.props;
    const { imageUrl } = this.state;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }

      dispatch(
        createProviderAction({
          name: values.name,
          description: values.description,
          status: parseInt(values.status),
          medias: {
            avatar: imageUrl,
          },
          billing_info: {
            cash_instruction: values.cash_instruction,
            transfer_instruction: values.transfer_instruction,
            bank_name: values.bank_name,
            bank_number: values.bank_number,
            bank_holders: values.bank_holders,
          },
        })
      );
    });
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  render() {
    const { imageUrl } = this.state;
    const { getFieldDecorator } = this.props.form;
    const { serviceProviderAdd } = this.props;
    if (serviceProviderAdd.success) {
      return <Redirect to="/main/service/providers" />;
    }

    return (
      <Page>
        <Row className="serviceProviderAddPage">
          <Card
            title="Thông tin"
            style={{
              borderRadius: 4,
              border: "0px solid transparent",
              marginBottom: 16,
            }}
          >
            <Form {...formItemLayout}>
              <Row>
                <Col xl={12} lg={24}>
                  <Form.Item
                    label={"Tên nhà cung cấp"}
                    style={{ marginBottom: 0 }}
                  >
                    {getFieldDecorator("name", {
                      // initialValue: !!record ? record.title : '',
                      rules: [
                        {
                          required: true,
                          message: "Tên không được để trống.",
                          whitespace: true,
                        },
                      ],
                    })(<Input maxLength={50} />)}
                  </Form.Item>
                </Col>
                <Col xl={10} lg={24}>
                  <Form.Item label={"Trạng thái"}>
                    {getFieldDecorator("status", {
                      initialValue: "1",
                      rules: [
                        {
                          required: true,
                          message:
                            "Trạng thái nhà cung cấp không được để trống.",
                          whitespace: true,
                        },
                      ],
                    })(
                      <Select
                        showSearch
                        placeholder="Chọn trạng thái"
                        optionFilterProp="children"
                        // onChange={onChange}
                        filterOption={(input, option) =>
                          option.props.children
                            .toLowerCase()
                            .indexOf(input.toLowerCase()) >= 0
                        }
                      >
                        {config.STATUS_SERVICE_PROVIDER.map((gr) => {
                          return (
                            <Select.Option
                              key={`group-${gr.id}`}
                              value={`${gr.id}`}
                            >
                              {gr.name}
                            </Select.Option>
                          );
                        })}
                      </Select>
                    )}
                  </Form.Item>
                </Col>
                <Col xl={12} lg={24}>
                  <Form.Item label={"Mô tả"}>
                    {getFieldDecorator("description", {
                      rules: [],
                    })(<Input.TextArea rows={6} maxLength={1000} style={{}} />)}
                  </Form.Item>
                </Col>
                <Col xl={10} lg={24}>
                  <Form.Item label={"Ảnh đại diện"}>
                    <Avatar
                      onUploaded={(url) => {
                        this.setState({
                          imageUrl: url,
                        });
                      }}
                      imageUrl={getFullLinkImage(imageUrl)}
                    />
                  </Form.Item>
                </Col>
              </Row>
            </Form>
          </Card>
          <Card
            title="Thanh toán"
            style={{ borderRadius: 4, border: "0px solid transparent" }}
          >
            <Row>
              <Col xl={12} lg={24}>
                <Form {...formItemLayout}>
                  <Form.Item
                    label={
                      <span style={{ fontWeight: "bold", color: "#1B1B27" }}>
                        Tiền mặt
                      </span>
                    }
                    colon={false}
                    style={{ marginBottom: 0 }}
                  ></Form.Item>
                  <Form.Item label={"Hướng dẫn"}>
                    {getFieldDecorator("cash_instruction", {
                      rules: [
                        {
                          required: true,
                          message:
                            "Hướng dẫn thanh toán tiền mặt không được để trống.",
                          whitespace: true,
                        },
                      ],
                    })(
                      <Input.TextArea maxLength={1000} rows={10} style={{}} />
                    )}
                  </Form.Item>
                </Form>
              </Col>
              <Col xl={10} lg={24}>
                <Form {...formItemLayout}>
                  <Form.Item
                    label={
                      <span style={{ fontWeight: "bold", color: "#1B1B27" }}>
                        Chuyển khoản
                      </span>
                    }
                    colon={false}
                    style={{ marginBottom: 0 }}
                  ></Form.Item>
                  <Form.Item label={"Hướng dẫn"}>
                    {getFieldDecorator("transfer_instruction", {
                      rules: [
                        {
                          required: true,
                          message:
                            "Hướng dẫn chuyển khoản không được để trống.",
                          whitespace: true,
                        },
                      ],
                    })(<Input.TextArea rows={5} maxLength={1000} style={{}} />)}
                  </Form.Item>
                  <Form.Item label={"Ngân hàng"}>
                    {getFieldDecorator("bank_name", {
                      // initialValue: !!record ? record.title : '',
                      rules: [
                        {
                          required: true,
                          message: "Tên ngân hàng không được để trống.",
                          whitespace: true,
                        },
                      ],
                    })(<Input maxLength={100} />)}
                  </Form.Item>
                  <Form.Item label={"Số tài khoản"}>
                    {getFieldDecorator("bank_number", {
                      // initialValue: !!record ? record.title : '',
                      rules: [
                        {
                          required: true,
                          message: "Số tài khoản không được để trống.",
                          whitespace: true,
                        },
                      ],
                    })(<Input maxLength={20} />)}
                  </Form.Item>
                  <Form.Item label={"Chủ tài khoản"}>
                    {getFieldDecorator("bank_holders", {
                      // initialValue: !!record ? record.title : '',
                      rules: [
                        {
                          required: true,
                          message: "Chủ tài khoản không được để trống.",
                          whitespace: true,
                        },
                      ],
                    })(<Input maxLength={50} />)}
                  </Form.Item>
                </Form>
              </Col>
            </Row>
          </Card>
          <Row style={{ paddingTop: 16 }} type="flex" justify="end">
            <Button
              ghost
              type="danger"
              style={{ width: 100, marginLeft: 16 }}
              disabled={serviceProviderAdd.loading}
              onClick={() => {
                Modal.confirm({
                  autoFocusButton: null,
                  title: "Bạn chắc chắn muốn dừng tạo nhà cung cấp?",
                  onOk: () => {
                    this.props.history.goBack();
                  },
                  onCancel() {
                    console.log("Cancel");
                  },
                });
              }}
            >
              Huỷ
            </Button>
            <Button
              ghost
              type="primary"
              style={{ width: 100, marginLeft: 16 }}
              onClick={this.handleOk}
              loading={serviceProviderAdd.loading}
            >
              Thêm
            </Button>
          </Row>
        </Row>
      </Page>
    );
  }
}

ServiceProviderAdd.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  serviceProviderAdd: makeSelectServiceProviderAdd(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "serviceProviderAdd", reducer });
const withSaga = injectSaga({ key: "serviceProviderAdd", saga });

export default compose(withReducer, withSaga, withConnect)(ServiceProviderAdd);
