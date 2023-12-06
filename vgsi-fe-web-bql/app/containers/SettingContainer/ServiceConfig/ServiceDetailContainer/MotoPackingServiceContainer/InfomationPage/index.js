/**
 *
 * InfomationMotoPackingPage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import makeSelectMotoPackingServiceContainer from "../selectors";

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
import { injectIntl } from "react-intl";
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
  updateServiceDetail,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectInfomationMotoPackingPage from "./selectors";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

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
export class InfomationMotoPackingPage extends React.PureComponent {
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
    const { data } = this.props.motoPackingServiceContainer;
    this.setState({
      imageUrl:
        !!data.medias && !!data.medias.logo ? data.medias.logo : undefined,
    });
  }

  _onUpdate = () => {
    const { dispatch, form } = this.props;
    const { data } = this.props.motoPackingServiceContainer;
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
          dispatch(
            updateServiceDetail({
              ...rest,
              ...values,
              medias: {
                logo: this.state.imageUrl,
              },
              config: {
                is_vat: values.is_vat,
                vat_percent: values.is_vat ? 0 : values.vat_percent,
                auto_create_fee: values.auto_create_fee ? 1 : 0,
                percent: values.percent || 0,
                service_map_management_id:
                  data.config.service_map_management_id,
              },
            })
          );
        },
      });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (
      this.props.infomationMotoPackingPage.success !=
        nextProps.infomationMotoPackingPage.success &&
      nextProps.infomationMotoPackingPage.success
    ) {
      this.setState({
        isEditting: false,
      });
    }
  }

  render() {
    const { isEditting, imageUrl } = this.state;
    const { getFieldDecorator, resetFields, getFieldValue } = this.props.form;
    const { infomationMotoPackingPage } = this.props;
    const { data } = this.props.motoPackingServiceContainer;
    const formatMessage = this.props.intl.formatMessage;
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
                    : (data.config.vat_percent ? data.config.vat_percent : 0) +
                      "%"}
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
          width={600}
          title={formatMessage(messages.editInformationService)}
          okText={formatMessage(messages.update)}
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
              {getFieldDecorator("status", {
                initialValue: `${data.status}`,
                rules: [
                  {
                    required: true,
                    message: formatMessage(messages.emptyStatus),
                    whitespace: true,
                  },
                ],
              })(
                <Select
                  showSearch
                  placeholder={formatMessage(messages.selectStatus)}
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
                      <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>
                        {this.props.language === "en" ? gr.name_en : gr.name}
                      </Select.Option>
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
                  loading={infomationMotoPackingPage.providers.loading}
                  showSearch
                  placeholder={formatMessage(messages.selectSupplier)}
                  optionFilterProp="children"
                  notFoundContent={
                    infomationMotoPackingPage.providers.loading ? (
                      <Spin size="small" />
                    ) : null
                  }
                  onSearch={this._onSearch}
                >
                  {infomationMotoPackingPage.providers.items.map((gr) => {
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
            <Form.Item label={" VAT"} style={{ marginBottom: 8 }} colon={false}>
              {getFieldDecorator("is_vat", {
                initialValue: data.config ? data.config.is_vat : "0",
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
                initialValue: data.config ? data.config.percent : 0,
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
              label={formatMessage(messages.autoCreateFee)}
              style={{ marginBottom: 8 }}
              colon={false}
            >
              {getFieldDecorator("auto_create_fee", {
                valuePropName: "checked",
                initialValue: data.config.auto_create_fee == 1,
              })(<Checkbox />)}
            </Form.Item>
            <Form.Item
              label={formatMessage(messages.introduce)}
              style={{ marginBottom: 0 }}
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

InfomationMotoPackingPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  infomationMotoPackingPage: makeSelectInfomationMotoPackingPage(),
  motoPackingServiceContainer: makeSelectMotoPackingServiceContainer(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "infomationMotoPackingPage",
  reducer,
});
const withSaga = injectSaga({ key: "infomationMotoPackingPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(InfomationMotoPackingPage));
