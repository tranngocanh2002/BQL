/**
 *
 * ServiceProviderDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Card,
  Col,
  Form,
  Icon,
  Input,
  Row,
  Select,
  Switch,
  Tooltip,
} from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Avatar from "../../../components/Avatar";
import Page from "../../../components/Page/Page";
import { getFullLinkImage } from "../../../connection";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";
import { defaultAction } from "../../NotificationContainer/NotificationAdd/actions";
import { fetchDetailServiceProvider, updateServiceProvider } from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectServiceProviderDetail from "./selectors";

import("./index.less");

const formItemLayout = {
  labelCol: {
    xl: { span: 12 },
    lg: { span: 12 },
    md: { span: 12 },
    sm: { span: 12 },
    xs: { span: 12 },
  },
  wrapperCol: {
    xl: { span: 11 },
    lg: { span: 11 },
    md: { span: 9 },
    sm: { span: 9 },
    xs: { span: 9 },
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ServiceProviderDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    const { record } = props.location.state || {};

    this.state = {
      record: !!record ? { ...record } : undefined,
      editBlock1: false,
      editBlock2: false,
      imageUrl: undefined,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    const { id } = this.props.match.params;
    if (id != undefined && !this.state.record) {
      this.props.dispatch(fetchDetailServiceProvider({ id }));
    }
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.serviceProviderDetail.detail.loading !=
        nextProps.serviceProviderDetail.detail.loading &&
      !nextProps.serviceProviderDetail.detail.loading
    ) {
      this.setState({
        record: !!nextProps.serviceProviderDetail.detail.data
          ? { ...nextProps.serviceProviderDetail.detail.data }
          : undefined,
      });
    }
    if (
      this.props.serviceProviderDetail.updating !=
        nextProps.serviceProviderDetail.updating &&
      !nextProps.serviceProviderDetail.updating
    ) {
      this.setState({
        editBlock1: false,
        editBlock2: false,
        record:
          nextProps.serviceProviderDetail.updatedData || this.state.record,
      });
    }
  }

  _updateClick = () => {
    const { dispatch, form } = this.props;
    const { imageUrl, editBlock1, editBlock2, record } = this.state;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      if (editBlock1)
        dispatch(
          updateServiceProvider({
            ...record,
            name: values.name,
            description: values.description,
            status: parseInt(values.status),
            medias: {
              avatar: imageUrl,
            },
          })
        );
      if (editBlock2)
        dispatch(
          updateServiceProvider({
            ...record,
            billing_info: {
              cash_instruction: this.state.cash_instruction,
              transfer_instruction: this.state.transfer_instruction,
              bank_name: this.state.bank_name,
              bank_number: this.state.bank_number,
              bank_holders: this.state.bank_holders,
            },
            payment_config: {
              merchant_id: this.state.merchant_id,
              merchant_pass: this.state.merchant_pass,
              receiver_account: this.state.receiver_account,
            },
          })
        );
    });
  };

  renderPaymentInfo = (data) => {
    return (
      <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
        <Col span={8} style={{ padding: 8 }}>
          <Row style={{ border: "1px solid gray", height: "100%", padding: 8 }}>
            <Col style={{ textAlign: "center" }}>
              <strong>Tiền mặt</strong>
            </Col>
            <br />
            <span style={{ fontStyle: "italic", fontWeight: "bold" }}>
              Hướng dẫn:
            </span>
            <br />
            {!!data.billing_info && data.billing_info.cash_instruction}
          </Row>
        </Col>
        <Col span={8} style={{ padding: 8 }}>
          <Row style={{ border: "1px solid gray", height: "100%", padding: 8 }}>
            <Col style={{ textAlign: "center" }}>
              <strong>Chuyển khoản</strong>
            </Col>
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Tên ngân hàng:
            </span>
            <strong>
              {!!data.billing_info && data.billing_info.bank_name}
            </strong>
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Số tài khoản:
            </span>
            <strong>
              {!!data.billing_info && data.billing_info.bank_account}
            </strong>
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Chủ tài khoản:
            </span>
            <strong>
              {!!data.billing_info && data.billing_info.bank_holders}
            </strong>
          </Row>
        </Col>
        <Col span={8} style={{ padding: 8 }}>
          <Row style={{ border: "1px solid gray", height: "100%", padding: 8 }}>
            <Col style={{ textAlign: "center" }}>
              <strong>Ngân lượng</strong>
            </Col>
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Tài khoản ngân lượng:
            </span>
            <strong>
              {!!data.payment_config && data.payment_config.receiver_account}
            </strong>
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Merchant ID:
            </span>
            <strong>
              {!!data.payment_config && data.payment_config.merchant_id}
            </strong>
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Merchant Password:
            </span>
            <strong>********</strong>
          </Row>
        </Col>
      </Row>
    );
  };

  renderPaymentEdit = (data) => {
    return (
      <Row type="flex" style={{ alignItems: "stretch", marginTop: 16 }}>
        <Col span={8} style={{ padding: 8 }}>
          <Row style={{ border: "1px solid gray", height: "100%", padding: 8 }}>
            <Col style={{ textAlign: "center" }}>
              <strong>Tiền mặt</strong>
            </Col>
            <br />
            <span style={{ fontStyle: "italic", fontWeight: "bold" }}>
              Hướng dẫn:
            </span>
            <br />
            <Input.TextArea
              rows={12}
              value={this.state.cash_instruction}
              onChange={(e) => {
                this.setState({
                  cash_instruction: e.target.value,
                });
              }}
            />
          </Row>
        </Col>
        <Col span={8} style={{ padding: 8 }}>
          <Row style={{ border: "1px solid gray", height: "100%", padding: 8 }}>
            <Col style={{ textAlign: "center" }}>
              <strong>Chuyển khoản</strong>
            </Col>
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Tên ngân hàng:
            </span>
            <Input
              value={this.state.bank_name}
              onChange={(e) => {
                this.setState({
                  bank_name: e.target.value,
                });
              }}
            />
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Số tài khoản:
            </span>
            <Input
              value={this.state.bank_account}
              onChange={(e) => {
                this.setState({
                  bank_account: e.target.value,
                });
              }}
            />
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Chủ tài khoản:
            </span>
            <Input
              value={this.state.bank_holders}
              onChange={(e) => {
                this.setState({
                  bank_holders: e.target.value,
                });
              }}
            />
          </Row>
        </Col>
        <Col span={8} style={{ padding: 8 }}>
          <Row style={{ border: "1px solid gray", height: "100%", padding: 8 }}>
            <Col style={{ textAlign: "center" }}>
              <strong>Ngân lượng</strong>
            </Col>
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Tài khoản ngân lượng:
            </span>
            <Input
              value={this.state.receiver_account}
              onChange={(e) => {
                this.setState({
                  receiver_account: e.target.value,
                });
              }}
            />
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Merchant ID:
            </span>
            <Input
              value={this.state.merchant_id}
              onChange={(e) => {
                this.setState({
                  merchant_id: e.target.value,
                });
              }}
            />
            <br />
            <span style={{ fontStyle: "italic", marginRight: 8 }}>
              Merchant Password:
            </span>
            <Input
              value={this.state.merchant_pass}
              onChange={(e) => {
                this.setState({
                  merchant_pass: e.target.value,
                });
              }}
            />
          </Row>
        </Col>
      </Row>
    );
  };

  render() {
    const { serviceProviderDetail, auth_group } = this.props;
    const { detail, updating } = serviceProviderDetail;
    const { record, editBlock1, editBlock2 } = this.state;
    const { getFieldDecorator } = this.props.form;

    const canEdit = auth_group.checkRole([
      config.ALL_ROLE_NAME.SERVICE_PROVIDER_DELETE,
      config.ALL_ROLE_NAME.SERVICE_PROVIDER_EDIT,
    ]);

    return (
      <Page>
        {/* <Page inner={detail.loading || !!!record} loading={detail.loading || !!!record} > */}
        <Row className="serviceProviderDetailPage">
          <Card
            title="Thông tin"
            loading={!!!record}
            extra={
              canEdit
                ? !editBlock2 &&
                  (editBlock1 ? (
                    <Row>
                      <Button
                        type="danger"
                        disabled={updating}
                        style={{ marginLeft: 10, width: 100 }}
                        onClick={() =>
                          this.setState({
                            editBlock1: false,
                            editBlock2: false,
                          })
                        }
                      >
                        Huỷ
                      </Button>
                      <Button
                        ghost
                        type="primary"
                        loading={updating}
                        style={{ marginLeft: 10, width: 100 }}
                        onClick={this._updateClick}
                      >
                        Cập nhật
                      </Button>
                    </Row>
                  ) : (
                    <Button
                      onClick={() =>
                        this.setState({
                          editBlock1: true,
                          editBlock2: false,
                          imageUrl: !!record.medias
                            ? record.medias.avatar
                            : undefined,
                        })
                      }
                    >
                      Chỉnh sửa
                    </Button>
                  ))
                : undefined
            }
            style={{
              borderRadius: 4,
              border: "0px solid transparent",
              marginBottom: 16,
            }}
          >
            {!!record && (
              <Form {...formItemLayout}>
                <Row style={{ minHeight: 290 }}>
                  <Col xl={12} lg={24}>
                    <Form.Item
                      label={`Tên nhà cung cấp`}
                      style={{ marginBottom: 0 }}
                    >
                      {!editBlock1 && (
                        <span className="title">{record.name}</span>
                      )}
                      {editBlock1 &&
                        getFieldDecorator("name", {
                          initialValue: record.name,
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
                    <Form.Item
                      label={`Trạng thái`}
                      // style={{ marginBottom: 0 }}
                    >
                      {!editBlock1 && (
                        <span className="title">
                          {
                            (
                              config.STATUS_SERVICE_PROVIDER.find(
                                (rr) => rr.id == record.status
                              ) || {}
                            ).name
                          }
                        </span>
                      )}
                      {editBlock1 &&
                        getFieldDecorator("status", {
                          initialValue: `${record.status}`,
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
                    <Form.Item label={`Mô tả`} style={{ marginBottom: 0 }}>
                      {!editBlock1 && <span>{record.description}</span>}
                      {editBlock1 &&
                        getFieldDecorator("description", {
                          initialValue: record.description,
                          rules: [],
                        })(
                          <Input.TextArea
                            rows={6}
                            maxLength={1000}
                            style={{}}
                          />
                        )}
                    </Form.Item>
                  </Col>
                  <Col xl={10} lg={24}>
                    <Form.Item
                      label={`Ảnh đại diện`}
                      style={{ marginBottom: 0 }}
                    >
                      <Row>
                        <Avatar
                          disabled={!editBlock1}
                          imageUrl={
                            editBlock1
                              ? getFullLinkImage(this.state.imageUrl)
                              : getFullLinkImage(
                                  !!record.medias
                                    ? record.medias.avatar
                                    : undefined
                                )
                          }
                          onUploaded={(url) => {
                            this.setState({
                              imageUrl: url,
                            });
                          }}
                        />
                      </Row>
                    </Form.Item>
                  </Col>
                </Row>
              </Form>
            )}
          </Card>
          <Card
            title="Thanh toán"
            loading={!!!record}
            extra={
              canEdit
                ? !editBlock1 &&
                  (editBlock2 ? (
                    <Row>
                      <Button
                        type="danger"
                        disabled={updating}
                        style={{ marginLeft: 10, width: 100 }}
                        onClick={() =>
                          this.setState({
                            editBlock1: false,
                            editBlock2: false,
                          })
                        }
                      >
                        Huỷ
                      </Button>
                      <Button
                        ghost
                        type="primary"
                        loading={updating}
                        style={{ marginLeft: 10, width: 100 }}
                        onClick={this._updateClick}
                      >
                        Cập nhật
                      </Button>
                    </Row>
                  ) : (
                    <Button
                      onClick={() =>
                        this.setState({
                          editBlock2: true,
                          editBlock1: false,
                          bank_account:
                            (!!record.billing_info
                              ? record.billing_info.bank_account
                              : "") || "",
                          bank_holders:
                            (!!record.billing_info
                              ? record.billing_info.bank_holders
                              : "") || "",
                          bank_name:
                            (!!record.billing_info
                              ? record.billing_info.bank_name
                              : "") || "",
                          cash_instruction:
                            (!!record.billing_info
                              ? record.billing_info.cash_instruction
                              : "") || "",
                          merchant_id:
                            (!!record.payment_config
                              ? record.payment_config.merchant_id
                              : "") || "",
                          merchant_pass:
                            (!!record.payment_config
                              ? record.payment_config.merchant_pass
                              : "") || "",
                          receiver_account:
                            (!!record.payment_config
                              ? record.payment_config.receiver_account
                              : "") || "",
                        })
                      }
                    >
                      Chỉnh sửa
                    </Button>
                  ))
                : undefined
            }
            style={{
              borderRadius: 4,
              border: "0px solid transparent",
              marginBottom: 16,
            }}
          >
            <Row type="flex" justify="center" align="middle">
              Sử dụng tài khoản thanh toán riêng:&ensp;&ensp;
              <Switch
                loading={!record}
                checked={!!record && record.using_bank_cluster == 0}
                onChange={(checked) => {
                  this.props.dispatch(
                    updateServiceProvider({
                      ...record,
                      using_bank_cluster: checked ? 0 : 1,
                    })
                  );
                }}
              />
              <Tooltip title="Hệ thống sẽ sử dụng tài khoản mặc định của BQL">
                <Icon type="info-circle" style={{ marginLeft: 16 }} />
              </Tooltip>
            </Row>
            {!!record &&
              (!editBlock2
                ? this.renderPaymentInfo(record)
                : this.renderPaymentEdit(record))}
          </Card>
        </Row>
      </Page>
    );
  }
}

ServiceProviderDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  serviceProviderDetail: makeSelectServiceProviderDetail(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "serviceProviderDetail", reducer });
const withSaga = injectSaga({ key: "serviceProviderDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(ServiceProviderDetail);
