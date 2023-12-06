/**
 *
 * InfomationManagementClusterPage
 *
 */

import {
  Button,
  Checkbox,
  Col,
  Form,
  Input,
  Modal,
  Radio,
  Row,
  Select,
  Spin,
} from "antd";
import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import { config, formatPrice, unescapeHTML } from "../../../../../../utils";
import makeSelectManagementClusterServiceContainer from "../selectors";
import {
  defaultAction,
  fetchServiceProvider,
  updateServiceDetail,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectInfomationManagementClusterPage from "./selectors";

import _ from "lodash";
import { injectIntl } from "react-intl";
import InputNumberFormat from "../../../../../../components/InputNumberFormat";
import UploaderSimple from "../../../../../../components/UploaderSimple";
import WithRole from "../../../../../../components/WithRole";
import { getFullLinkImage } from "../../../../../../connection";
import messages from "../../../messages";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 16,
  },
};

const { Option } = Select;

class PriceInput extends React.Component {
  static getDerivedStateFromProps(nextProps) {
    // Should be a controlled component.
    if ("value" in nextProps) {
      return {
        ...(nextProps.value || {}),
      };
    }
    return null;
  }

  constructor(props) {
    super(props);

    const value = props.value || {};
    this.state = {
      number: value.number || 0,
      currency: value.currency || "rmb",
    };
  }

  handleNumberChange = (e) => {
    const number = parseInt(e || 0, 10);
    if (Number.isNaN(number)) {
      return;
    }
    if (!("value" in this.props)) {
      this.setState({ number });
    }
    this.triggerChange({ number });
  };

  handleCurrencyChange = (currency) => {
    if (!("value" in this.props)) {
      this.setState({ currency });
    }
    this.triggerChange({ currency });
  };

  triggerChange = (changedValue) => {
    // Should provide an event to pass value to Form.
    const onChange = this.props.onChange;
    if (onChange) {
      onChange(Object.assign({}, this.state, changedValue));
    }
  };

  render() {
    const { size, options } = this.props;
    const state = this.state;
    return (
      <span>
        <InputNumberFormat
          value={state.number}
          onChange={this.handleNumberChange}
          style={{ width: "55%", marginRight: "3%" }}
        />
        <Select
          value={state.currency}
          size={size}
          style={{ width: "42%" }}
          onChange={this.handleCurrencyChange}
        >
          {options.map((op) => {
            return (
              <Option value={op.value} key={op.value}>
                {this.props.language === "en" ? op.title_en : op.title}
              </Option>
            );
          })}
        </Select>
      </span>
    );
  }
}

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class InfomationManagementClusterPage extends React.PureComponent {
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
    const { data } = this.props.managementClusterServiceContainer;
    this.setState({
      imageUrl:
        !!data.medias && !!data.medias.logo ? data.medias.logo : undefined,
    });
  }

  _onUpdate = () => {
    const { dispatch, form } = this.props;
    const { imageUrl } = this.state;
    const { data } = this.props.managementClusterServiceContainer;
    const { validateFieldsAndScroll } = form;
    const formatMessage = this.props.intl.formatMessage;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      Modal.confirm({
        autoFocusButton: null,
        title: formatMessage(messages.confirm),
        content: formatMessage(messages.contentEdit),
        okText: formatMessage(messages.continue),
        cancelText: formatMessage(messages.skip),
        onOk: () => {
          const { service_provider_name, config, ...rest } = data;
          const {
            price,
            timeCreate,
            offset_day,
            auto_create_fee,
            percent,
            is_vat,
            vat_percent,
            ...rest2
          } = values;
          dispatch(
            updateServiceDetail({
              ...rest,
              ...rest2,
              medias: {
                logo: imageUrl,
              },
              config: {
                ...config,
                price: price.number,
                unit: price.currency,
                is_vat,
                vat_percent: is_vat ? 0 : vat_percent,
                day: timeCreate.number,
                month_cycle: timeCreate.currency,
                percent,
                offset_day,
                auto_create_fee: auto_create_fee ? 1 : 0,
              },
            })
          );
        },
      });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (
      this.props.infomationManagementClusterPage.success !=
        nextProps.infomationManagementClusterPage.success &&
      nextProps.infomationManagementClusterPage.success
    ) {
      this.setState({
        isEditting: false,
      });
    }
  }

  render() {
    const { isEditting, imageUrl } = this.state;
    const { getFieldDecorator, getFieldValue, resetFields } = this.props.form;
    const { infomationManagementClusterPage } = this.props;
    const { data } = this.props.managementClusterServiceContainer;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Row style={{ marginTop: 48 }}>
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
                {formatMessage(messages.serviceName)}:
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
                {formatMessage(messages.serviceName)} (EN):
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
                {formatMessage(messages.status)}:
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
                {formatMessage(messages.contractor)}:
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
                  : data.service_provider_name_en}
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
                {formatMessage(messages.serviceFee)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                <span style={{ fontWeight: "bold" }}>
                  {formatPrice(data.config.price)} đ
                </span>
                {` / ${
                  (
                    config.UNIT_SERVICE_CONFIG.find(
                      (ii) => ii.value == data.config.unit
                    ) || config.UNIT_SERVICE_CONFIG[0]
                  ).title
                }`}
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
                  {data.config.is_vat
                    ? formatMessage(messages.includedFee)
                    : data.config.vat_percent + "%"}
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
                {formatMessage(messages.otherFee)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                {`${data.config.percent || 0}%`}
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
                {formatMessage(messages.autoCreateFee)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                {data.config.auto_create_fee == 1
                  ? formatMessage(messages.on)
                  : formatMessage(messages.off)}
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
                {formatMessage(messages.timeCreateFee)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                <>
                  {formatMessage(messages.day)}
                  <span style={{ fontWeight: "bold" }}> {data.config.day}</span>
                  {` - ${formatMessage(messages.periodic)}: `}
                  <span style={{ fontWeight: "bold" }}>
                    {this.props.language === "vi"
                      ? (
                          config.MONTH_CYCLE_SERVICE_CONFIG.find(
                            (ii) => ii.value == data.config.month_cycle
                          ) || config.MONTH_CYCLE_SERVICE_CONFIG[0]
                        ).title
                      : (
                          config.MONTH_CYCLE_SERVICE_CONFIG.find(
                            (ii) => ii.value == data.config.month_cycle
                          ) || config.MONTH_CYCLE_SERVICE_CONFIG[0]
                        ).title_en}
                  </span>
                </>
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
                {formatMessage(messages.payExpire)}:
              </Col>
              <Col
                xs={24}
                sm={24}
                md={12}
                lg={14}
                style={{ color: "#1B1B27", fontWeight: "bold", marginTop: 24 }}
              >
                <span style={{ fontWeight: "bold" }}>
                  {`${data.config.offset_day} `}
                </span>
                <span className="ant-form-text">
                  {" "}
                  {formatMessage(messages.day)}
                </span>
              </Col>
            </Row>
          </Row>
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
              {formatMessage(messages.edit)}
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
          title={formatMessage(messages.editInformationService)}
          okText={formatMessage(messages.update)}
          width={600}
          onOk={this._onUpdate}
          onCancel={() => {
            this.setState({
              isEditting: false,
            });
          }}
        >
          <Form {...formItemLayout}>
            <Form.Item
              label={formatMessage(messages.avatar)}
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
              label={formatMessage(messages.serviceName)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("service_name", {
                initialValue: data.service_name,
                rules: [
                  {
                    required: true,
                    message: formatMessage(messages.emptyServiceName),
                    whitespace: true,
                  },
                ],
              })(<Input />)}
            </Form.Item>
            <Form.Item
              label={`${formatMessage(messages.serviceName)} (EN)`}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("service_name_en", {
                initialValue: data.service_name_en,
                rules: [
                  {
                    required: true,
                    message: formatMessage(messages.emptyServiceNameEn),
                    whitespace: true,
                  },
                ],
              })(<Input />)}
            </Form.Item>
            <Form.Item
              label={formatMessage(messages.status)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {!isEditting && (
                <span style={{ fontWeight: "bold" }}>
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
                </span>
              )}
              {isEditting &&
                getFieldDecorator("status", {
                  initialValue: data.status,
                  rules: [
                    {
                      required: true,
                      message: formatMessage(messages.emptyStatus),
                      whitespace: true,
                      type: "number",
                    },
                  ],
                })(
                  <Select
                    showSearch
                    placeholder={formatMessage(messages.selectStatus)}
                    optionFilterProp="children"
                    filterOption={(input, option) =>
                      option.props.children
                        .toLowerCase()
                        .indexOf(input.toLowerCase()) >= 0
                    }
                  >
                    {config.STATUS_SERVICE_PROVIDER.map((gr) => {
                      return (
                        <Select.Option key={`group-${gr.id}`} value={gr.id}>{`${
                          this.props.language === "vi" ? gr.name : gr.name_en
                        }`}</Select.Option>
                      );
                    })}
                  </Select>
                )}
            </Form.Item>
            <Form.Item
              label={formatMessage(messages.contractor)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("service_provider_id", {
                initialValue: data.service_provider_id,
                rules: [
                  {
                    required: true,
                    message: formatMessage(messages.emptySupplier),
                    whitespace: true,
                    type: "number",
                  },
                ],
              })(
                <Select
                  loading={infomationManagementClusterPage.providers.loading}
                  showSearch
                  placeholder={formatMessage(messages.selectSupplier)}
                  optionFilterProp="children"
                  notFoundContent={
                    infomationManagementClusterPage.providers.loading ? (
                      <Spin size="small" />
                    ) : null
                  }
                  onSearch={this._onSearch}
                >
                  {infomationManagementClusterPage.providers.items.map((gr) => {
                    return (
                      <Select.Option
                        disabled={gr.status == 0}
                        key={`group-${gr.id}`}
                        value={gr.id}
                      >
                        {this.props.language === "vi" ? gr.name : gr.name_en}
                        {gr.status == 0 && (
                          <span
                            style={{
                              fontWeight: "lighter",
                              fontSize: 12,
                            }}
                          >{` (${config.STATUS_SERVICE_PROVIDER[0].name})`}</span>
                        )}
                      </Select.Option>
                    );
                  })}
                </Select>
              )}
            </Form.Item>
            <Form.Item
              label={formatMessage(messages.serviceFee)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("price", {
                initialValue: {
                  number: data.config.price,
                  currency: data.config.unit,
                },
                rules: [
                  {
                    required: true,
                    validator: (rule, value, callback) => {
                      if (value.number > 0) {
                        callback();
                        return;
                      }
                      callback(formatMessage(messages.limitFee));
                    },
                  },
                ],
              })(
                <PriceInput
                  options={config.UNIT_SERVICE_CONFIG}
                  language={this.props.language}
                />
              )}
            </Form.Item>
            <Form.Item label={"VAT"} style={{ marginBottom: 8 }} colon={false}>
              {getFieldDecorator("is_vat", {
                initialValue: data.config.is_vat,
              })(
                <Radio.Group style={{ zIndex: 99 }} buttonStyle="solid">
                  <Radio value={1}>{formatMessage(messages.included)}</Radio>
                  <Radio value={0}>{formatMessage(messages.notIncluded)}</Radio>
                </Radio.Group>
              )}{" "}
              {!getFieldValue("is_vat") && (
                <span>
                  {getFieldDecorator("vat_percent", {
                    initialValue: data.config.vat_percent,
                  })(
                    <InputNumberFormat
                      maxLength={10}
                      style={{ width: "30%" }}
                      min={0}
                    />
                  )}
                  <span className="ant-form-text"> %</span>
                </span>
              )}
            </Form.Item>
            <Form.Item
              label={formatMessage(messages.otherFee)}
              style={{ marginBottom: 0 }}
              colon={false}
            >
              {getFieldDecorator("percent", {
                initialValue: data.config.percent,
                rules: [
                  {
                    required: true,
                    message: formatMessage(messages.otherFee),
                    whitespace: true,
                    type: "number",
                  },
                ],
              })(
                <InputNumberFormat
                  maxLength={10}
                  style={{ width: "55%" }}
                  min={0}
                />
              )}
              <span className="ant-form-text"> %</span>
            </Form.Item>
            <Form.Item
              label={
                <span
                  style={{ wordWrap: "break-word", overflowWrap: "break-word" }}
                >
                  {formatMessage(messages.autoCreateFee)}
                </span>
              }
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("auto_create_fee", {
                valuePropName: "checked",
                initialValue: data.config.auto_create_fee == 1,
              })(<Checkbox />)}
            </Form.Item>
            <Form.Item
              label={formatMessage(messages.timeCreateFee)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("timeCreate", {
                initialValue: {
                  number: data.config.day,
                  currency: data.config.month_cycle,
                },
                rules: [
                  {
                    required: true,
                    validator: (rule, value, callback) => {
                      if (value.number > 0 && value.number <= 28) {
                        callback();
                        return;
                      }
                      callback(formatMessage(messages.ruleTimeCreateFee));
                    },
                  },
                ],
              })(
                <PriceInput
                  options={config.MONTH_CYCLE_SERVICE_CONFIG}
                  language={this.props.language}
                />
              )}
            </Form.Item>
            <Form.Item
              label={formatMessage(messages.payExpire)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("offset_day", {
                initialValue: data.config.offset_day,
                rules: [
                  {
                    required: true,
                    message: formatMessage(messages.emptyPayExpire),
                    whitespace: true,
                    type: "number",
                  },
                ],
              })(
                <InputNumberFormat
                  maxLength={10}
                  style={{ width: "55%" }}
                  min={0}
                />
              )}
              <span className="ant-form-text">
                {" "}
                {formatMessage(messages.day)}
              </span>
            </Form.Item>
            <Form.Item
              label={formatMessage(messages.introduce)}
              style={{
                marginBottom: isEditting ? 24 : 0,
                whiteSpace: "pre-wrap",
              }}
              colon={false}
            >
              {getFieldDecorator("service_description", {
                initialValue: data.service_description
                  ? unescapeHTML(data.service_description)
                  : "",
                rules: [],
              })(<Input.TextArea rows={8} />)}
            </Form.Item>
          </Form>
        </Modal>
      </Row>
    );
  }
}

InfomationManagementClusterPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  infomationManagementClusterPage: makeSelectInfomationManagementClusterPage(),
  managementClusterServiceContainer:
    makeSelectManagementClusterServiceContainer(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "infomationManagementClusterPage",
  reducer,
});
const withSaga = injectSaga({ key: "infomationManagementClusterPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(InfomationManagementClusterPage));
