/**
 *
 * InfomationWaterPage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import makeSelectWaterServiceContainer from "../selectors";

import {
  Button,
  Col,
  Form,
  Input,
  Modal,
  Radio,
  Row,
  Select,
  Spin,
} from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import _ from "lodash";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import InputNumberFormat from "../../../../../../components/InputNumberFormat";
import UploaderSimple from "../../../../../../components/UploaderSimple";
import WithRole from "../../../../../../components/WithRole";
import { getFullLinkImage } from "../../../../../../connection";
import { config, unescapeHTML } from "../../../../../../utils";
import messages from "../../../messages";
import {
  defaultAction,
  fetchServiceProvider,
  fetchWaterConfig,
  updateServiceDetail,
  updateWaterConfig,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectInfomationWaterPage from "./selectors";

const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 16,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class InfomationWaterPage extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      isEditting: false,
      imageUrl: null,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchServiceProvider({ name: keyword }));
  };

  componentDidMount() {
    this._onSearch("");
    this.props.dispatch(fetchWaterConfig());
    const { data } = this.props.waterServiceContainer;
    this.setState({
      imageUrl:
        !!data.medias && !!data.medias.logo ? data.medias.logo : undefined,
    });
  }

  _onUpdate = () => {
    const { dispatch, form } = this.props;
    const { data } = this.props.waterServiceContainer;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      Modal.confirm({
        autoFocusButton: null,
        title: this.props.intl.formatMessage(messages.confirm),
        content: this.props.intl.formatMessage(messages.confirmChange),
        okText: this.props.intl.formatMessage(messages.continue),
        cancelText: this.props.intl.formatMessage(messages.skip),
        onOk: () => {
          const { service_provider_name, ...rest } = data;
          const {
            type,
            percent,
            is_vat,
            vat_percent,
            environ_percent,
            vat_dvtn,
          } = values;

          dispatch(
            updateWaterConfig({
              type,
              percent,
              service_map_management_id: data.id,
              is_vat,
              vat_percent: is_vat ? 0 : vat_percent,
              environ_percent,
              vat_dvtn,
            })
          );
          dispatch(
            updateServiceDetail({
              ...rest,
              ...values,
              medias: {
                logo: this.state.imageUrl,
              },
            })
          );
        },
      });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (
      this.props.infomationWaterPage.success !=
        nextProps.infomationWaterPage.success &&
      nextProps.infomationWaterPage.success
    ) {
      this.setState({
        isEditting: false,
      });
    }
  }

  render() {
    const { isEditting, imageUrl } = this.state;
    const { getFieldDecorator, resetFields, getFieldValue } = this.props.form;
    const { infomationWaterPage, language } = this.props;
    const { data } = this.props.waterServiceContainer;

    return (
      <Row style={{ padding: 48 }}>
        <Col span={10}>
          <Row>
            <Col>
              <div
                style={{
                  width: "100%",
                  paddingTop: "60%",
                  position: "relative",
                  overflow: "hidden",
                  backgroundImage: `url(${getFullLinkImage(
                    !!data.medias && !!data.medias.logo
                      ? data.medias.logo
                      : undefined
                  )})`,
                  backgroundSize: "cover",
                  backgroundPosition: "center",
                }}
              />
            </Col>
            <Row>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={10}
                style={{ color: "#A4A4AA", marginTop: 24 }}
              >
                {this.props.intl.formatMessage(messages.serviceName)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                {data.service_name}
              </Col>
            </Row>
            <Row>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={10}
                style={{ color: "#A4A4AA", marginTop: 24 }}
              >
                {this.props.intl.formatMessage(messages.serviceName)} (EN):
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                {data.service_name_en}
              </Col>
            </Row>
            <Row>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={10}
                style={{ color: "#A4A4AA", marginTop: 24 }}
              >
                {this.props.intl.formatMessage(messages.status)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                {this.props.language === "vi"
                  ? (
                      config.STATUS_SERVICE_PROVIDER.find(
                        (rr) => rr.id == data.status
                      ) || {}
                    ).name
                  : (
                      config.STATUS_SERVICE_PROVIDER.find(
                        (rr) => rr.id == data.status
                      ) || {}
                    ).name_en}
              </Col>
            </Row>
            <Row>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={10}
                style={{ color: "#A4A4AA", marginTop: 24 }}
              >
                {this.props.intl.formatMessage(messages.contractor)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                {this.props.language === "vi"
                  ? data.service_provider_name
                  : data.service_provider_name_en}{" "}
              </Col>
            </Row>
            <Row>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={10}
                style={{ color: "#A4A4AA", marginTop: 24 }}
              >
                {this.props.intl.formatMessage(messages.typePayFee)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                {/* <Select>
                <Select.Option>Theo căn hộ</Select.Option>
                <Select.Option>Theo cư dân / Căn hộ</Select.Option>
              </Select> */}
                {!!infomationWaterPage.config &&
                  (infomationWaterPage.config.type == 0 ? (
                    <span style={{ fontWeight: "bold" }}>
                      {this.props.intl.formatMessage(messages.viaProperty)}
                    </span>
                  ) : (
                    <span style={{ fontWeight: "bold" }}>
                      {this.props.intl.formatMessage(messages.viaResident)} /{" "}
                      {this.props.intl.formatMessage(messages.property)}
                    </span>
                  ))}
              </Col>
            </Row>
            <Row>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={10}
                style={{ color: "#A4A4AA", marginTop: 24 }}
              >
                VAT:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                <span style={{ fontWeight: "bold" }}>
                  {!!data.config && data.config.is_vat
                    ? this.props.intl.formatMessage(messages.includedFee)
                    : data.config
                    ? data.config.vat_percent + "%"
                    : "0%"}
                </span>
              </Col>
            </Row>
            <Row>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={10}
                style={{ color: "#A4A4AA", marginTop: 24 }}
              >
                {this.props.intl.formatMessage(messages.feeDvtn)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                <span style={{ fontWeight: "bold" }}>
                  {data.config ? data.config.environ_percent + "%" : "0%"}
                </span>
              </Col>
            </Row>
            <Row>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={10}
                style={{ color: "#A4A4AA", marginTop: 24 }}
              >
                {language === "en" ? "VAT drainage water:" : "VAT DVTN:"}
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                <span style={{ fontWeight: "bold" }}>
                  {!!data.config && data.config.is_vat
                    ? this.props.intl.formatMessage(messages.includedFee)
                    : data.config && data.config.vat_dvtn
                    ? data.config.vat_dvtn + "%"
                    : "0%"}
                </span>
              </Col>
            </Row>
            <Row>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={10}
                style={{ color: "#A4A4AA", marginTop: 24 }}
              >
                {this.props.intl.formatMessage(messages.otherFee)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                {infomationWaterPage.config ? (
                  <span style={{ fontWeight: "bold" }}>{`${
                    infomationWaterPage.config.percent || 0
                  }%`}</span>
                ) : (
                  <span style={{ fontWeight: "bold" }} />
                )}
              </Col>
            </Row>
          </Row>
          {/* <Button style={{ width: 100, marginTop: 36 }} onClick={() => {
            this.props.history.push('/main/setting/service/list')
          }} >
            Quay lại
            </Button> */}
          <WithRole roles={[config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT]}>
            <Button
              style={{ width: 100, marginTop: 24 }}
              ghost
              type="primary"
              onClick={() => {
                resetFields();
                this.setState({ isEditting: true });
              }}
            >
              {this.props.intl.formatMessage(messages.edit)}
            </Button>
          </WithRole>
        </Col>

        <Col span={13} offset={1} style={{ whiteSpace: "pre-wrap" }}>
          <div
            dangerouslySetInnerHTML={{ __html: data.service_description }}
            style={{
              whiteSpace: "pre-wrap",
            }}
          />
        </Col>
        <Modal
          visible={isEditting}
          width={700}
          title={this.props.intl.formatMessage(messages.editInformationService)}
          okText={this.props.intl.formatMessage(messages.update)}
          onOk={this._onUpdate}
          onCancel={() => {
            this.setState({
              isEditting: false,
            });
          }}
        >
          <Form {...formItemLayout}>
            <Form.Item
              label={this.props.intl.formatMessage(messages.avatar)}
              colon={false}
              style={{ marginBottom: 0 }}
            >
              <UploaderSimple
                imageUrl={getFullLinkImage(imageUrl)}
                onUploaded={(url) => this.setState({ imageUrl: url })}
                disabled={false}
              />
            </Form.Item>
            <Form.Item
              label={this.props.intl.formatMessage(messages.serviceName)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("service_name", {
                initialValue: data.service_name,
                rules: [
                  {
                    required: true,
                    message: this.props.intl.formatMessage(
                      messages.emptyServiceName
                    ),
                    whitespace: true,
                  },
                ],
              })(<Input />)}
            </Form.Item>
            <Form.Item
              label={`${this.props.intl.formatMessage(
                messages.serviceName
              )} (EN)`}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("service_name_en", {
                initialValue: data.service_name_en,
                rules: [
                  {
                    required: true,
                    message: this.props.intl.formatMessage(
                      messages.emptyServiceNameEn
                    ),
                    whitespace: true,
                  },
                ],
              })(<Input />)}
            </Form.Item>
            <Form.Item
              label={this.props.intl.formatMessage(messages.status)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("status", {
                initialValue: `${data.status}`,
                // rules: [{ required: true, message: 'Trạng thái không được để trống.', whitespace: true }],
              })(
                <Select
                  // showSearch
                  placeholder={this.props.intl.formatMessage(
                    messages.selectStatus
                  )}
                  optionFilterProp="children"
                  // onChange={onChange}
                  // filterOption={(input, option) =>
                  //   option.props.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                  // }
                >
                  {config.STATUS_SERVICE_PROVIDER.map((gr) => {
                    return (
                      <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>
                        {this.props.language === "vi" ? gr.name : gr.name_en}
                      </Select.Option>
                    );
                  })}
                </Select>
              )}
            </Form.Item>
            <Form.Item
              label={this.props.intl.formatMessage(messages.contractor)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("service_provider_id", {
                initialValue: data.service_provider_id,
                rules: [
                  {
                    required: true,
                    message: this.props.intl.formatMessage(
                      messages.emptySupplier
                    ),
                    whitespace: true,
                    type: "number",
                  },
                ],
              })(
                <Select
                  loading={infomationWaterPage.providers.loading}
                  showSearch
                  placeholder={this.props.intl.formatMessage(
                    messages.selectSupplier
                  )}
                  optionFilterProp="children"
                  notFoundContent={
                    infomationWaterPage.providers.loading ? (
                      <Spin size="small" />
                    ) : null
                  }
                  onSearch={this._onSearch}
                >
                  {infomationWaterPage.providers.items.map((gr) => {
                    return (
                      <Select.Option key={`group-${gr.id}`} value={gr.id}>
                        {this.props.language === "vi" ? gr.name : gr.name_en}
                        {gr.status == 0 && (
                          <span
                            style={{ fontWeight: "lighter", fontSize: 12 }}
                          >{` (${
                            this.props.language === "vi"
                              ? config.STATUS_SERVICE_PROVIDER[0].name
                              : config.STATUS_SERVICE_PROVIDER[0].name_en
                          })`}</span>
                        )}
                      </Select.Option>
                    );
                  })}
                </Select>
              )}
            </Form.Item>
            <Form.Item
              label={this.props.intl.formatMessage(messages.typePayFee)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("type", {
                initialValue: infomationWaterPage.config
                  ? infomationWaterPage.config.type
                  : 0,
                rules: [{ type: "number" }],
              })(
                <Select>
                  <Select.Option value={0}>
                    {this.props.intl.formatMessage(messages.viaProperty)}
                  </Select.Option>
                  <Select.Option value={1}>
                    {this.props.intl.formatMessage(messages.viaResident)} /{" "}
                    {this.props.intl.formatMessage(messages.property)}
                  </Select.Option>
                </Select>
              )}
            </Form.Item>
            <Form.Item label={"VAT"} style={{ marginBottom: 8 }} colon={false}>
              {getFieldDecorator("is_vat", {
                initialValue: data.config ? data.config.is_vat : 0,
              })(
                <Radio.Group style={{ zIndex: 99 }} buttonStyle="solid">
                  <Radio value={1}>
                    {this.props.intl.formatMessage(messages.included)}
                  </Radio>
                  <Radio value={0}>
                    {this.props.intl.formatMessage(messages.notIncluded)}
                  </Radio>
                </Radio.Group>
              )}{" "}
              {!getFieldValue("is_vat") && (
                <span>
                  {getFieldDecorator("vat_percent", {
                    initialValue: data.config ? data.config.vat_percent : 0,
                  })(
                    <InputNumberFormat
                      maxLength={10}
                      style={{ width: "22%" }}
                      min={0}
                    />
                  )}
                  <span className="ant-form-text"> %</span>
                </span>
              )}
            </Form.Item>
            <div
              style={{
                display: "flex",
                flexDirection: "row",
                position: "relative",
              }}
            >
              <Form.Item
                label={this.props.intl.formatMessage(messages.feeDvtn)}
                style={{ marginBottom: 8, width: "100%" }}
                colon={false}
              >
                {getFieldDecorator("environ_percent", {
                  initialValue:
                    !!infomationWaterPage && !!infomationWaterPage.config
                      ? infomationWaterPage.config.environ_percent
                      : 0,
                })(
                  <InputNumberFormat
                    maxLength={10}
                    style={{ width: "32%" }}
                    min={0}
                  />
                )}
                <span className="ant-form-text"> %</span>
              </Form.Item>
              <Form.Item
                label={language === "en" ? "VAT drainage water" : "VAT DVTN"}
                style={{
                  marginBottom: 8,
                  width: "100%",
                  position: "absolute",
                  left: 320,
                }}
                colon={false}
              >
                <span>
                  {getFieldDecorator("vat_dvtn", {
                    initialValue:
                      !!infomationWaterPage && !!infomationWaterPage.config
                        ? infomationWaterPage.config.vat_dvtn
                        : 0,
                  })(
                    <InputNumberFormat
                      maxLength={10}
                      style={{ width: "22%" }}
                      min={0}
                    />
                  )}
                  <span className="ant-form-text"> %</span>
                </span>
              </Form.Item>
            </div>
            <Form.Item
              label={this.props.intl.formatMessage(messages.otherFee)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("percent", {
                initialValue: infomationWaterPage.config
                  ? infomationWaterPage.config.percent
                  : 0,
              })(
                <InputNumberFormat
                  maxLength={10}
                  style={{ width: "32%" }}
                  min={0}
                />
              )}
              <span className="ant-form-text"> %</span>
            </Form.Item>
            <Form.Item
              label={this.props.intl.formatMessage(messages.introduce)}
              style={{ marginBottom: 0 }}
              colon={false}
            >
              {getFieldDecorator("service_description", {
                initialValue: data.service_description
                  ? unescapeHTML(data.service_description)
                  : "",
                rules: [],
              })(<Input.TextArea rows={8} style={{}} />)}
            </Form.Item>
          </Form>
        </Modal>
      </Row>
    );
  }
}

InfomationWaterPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  infomationWaterPage: makeSelectInfomationWaterPage(),
  waterServiceContainer: makeSelectWaterServiceContainer(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "infomationWaterPage", reducer });
const withSaga = injectSaga({ key: "infomationWaterPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(InfomationWaterPage));
