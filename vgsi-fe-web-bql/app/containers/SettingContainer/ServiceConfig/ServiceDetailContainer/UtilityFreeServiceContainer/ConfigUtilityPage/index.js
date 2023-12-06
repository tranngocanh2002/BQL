/**
 *
 * ConfigUtilityPage
 *
 */
//TODO: Sua cau hinh tien ich
import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Col, Icon, Modal, Radio, Row, Table, Tooltip } from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import { injectIntl } from "react-intl";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../../../../components/Page/Page";
import WithRole from "../../../../../../components/WithRole";
import { selectAuthGroup } from "../../../../../../redux/selectors";
import config from "../../../../../../utils/config";
import messages from "../../../messages";
import ModalAddPrice from "./ModalAddPrice";
import ModalAddSlot from "./ModalAddSlot";
import {
  createConfig,
  createConfigPrice,
  defaultAction,
  deleteConfigPlace,
  deleteConfigPrice,
  fetchAllConfig,
  fetchConfigPrice,
  updateConfig,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectConfigUtilityPage from "./selectors";

/* eslint-disable react/prefer-stateless-function */
export class ConfigUtilityPage extends React.PureComponent {
  state = {
    isVisible: false,
    isVisibleUpdate: false,
    isVisibleModalPrice: false,
    currentSelected: 0,
    currentSlot: null,
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchAllConfig(this.props.match.params.id));
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.configUtilityPage.createPriceSuccess !=
        nextProps.configUtilityPage.createPriceSuccess &&
      nextProps.configUtilityPage.createPriceSuccess
    ) {
      this.setState({
        isVisibleModalPrice: false,
      });
    }
    if (
      this.props.configUtilityPage.createSuccess !=
        nextProps.configUtilityPage.createSuccess &&
      nextProps.configUtilityPage.createSuccess
    ) {
      this.setState({
        isVisible: false,
      });
    }
    if (
      this.props.configUtilityPage.updateSuccess !=
        nextProps.configUtilityPage.updateSuccess &&
      nextProps.configUtilityPage.updateSuccess
    ) {
      this.setState({
        isVisibleUpdate: false,
      });
    }
    if (
      this.props.configUtilityPage.loading !=
        nextProps.configUtilityPage.loading &&
      !nextProps.configUtilityPage.loading
    ) {
      const currentData =
        nextProps.configUtilityPage.data[this.state.currentSelected];
      if (currentData) {
        this.setState({
          currentSlot: currentData,
        });
        this.props.dispatch(fetchConfigPrice(currentData.id));
      }
    }
  }

  handleSizeChange = (e) => {
    this.setState({ currentSelected: e.target.value });
    const currentData = this.props.configUtilityPage.data[e.target.value];
    if (currentData) {
      this.props.dispatch(fetchConfigPrice(currentData.id));
      this.setState({ currentSlot: currentData });
    }
  };
  render() {
    const { currentSelected, currentSlot } = this.state;
    const { configUtilityPage, auth_group, language } = this.props;
    const { data, creating, updating, creatingPrice } = configUtilityPage;

    const currentData = data[currentSelected];
    let configPrice = null;
    if (currentData) {
      configPrice = configUtilityPage[`config-${currentData.id}`];
    }

    const columns1 = [
      {
        title: this.props.intl.formatMessage(messages.time),
        dataIndex: "time",
        key: "time",
        render: (text, record) => (
          <span>{`${moment(record.start_time, "HH:mm").format(
            "HH:mm"
          )} - ${moment(record.end_time, "HH:mm").format("HH:mm")}`}</span>
        ),
      },
      // {
      //   align: "right",
      //   title: (
      //     <span>{this.props.intl.formatMessage(messages.price)} (VNĐ)</span>
      //   ),
      //   dataIndex: "price_adult",
      //   key: "price",
      //   render: (text) => (
      //     <span>
      //       <span>{formatPrice(text)}</span>
      //       <span>
      //         {currentData.type != 1
      //           ? ` (${this.props.intl.formatMessage(messages.free)})`
      //           : ""}
      //       </span>
      //     </span>
      //   ),
      // },
      {
        align: "center",
        // width: 100,
        title: <span>{this.props.intl.formatMessage(messages.action)}</span>,
        dataIndex: "",
        key: "x",
        render: (text, record) => {
          return (
            <Tooltip title={this.props.intl.formatMessage(messages.delete)}>
              <Row
                type="flex"
                style={{
                  color: auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ])
                    ? "#F15A29"
                    : "#CCC",
                  cursor: auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ])
                    ? "pointer"
                    : "not-allowed",
                  justifyContent: "center",
                }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ]) &&
                    Modal.confirm({
                      autoFocusButton: null,
                      title: this.props.intl.formatMessage(
                        messages.confirmDeleteTime
                      ),
                      okText: this.props.intl.formatMessage(messages.agree),
                      okType: "danger",
                      cancelText: this.props.intl.formatMessage(
                        messages.cancel
                      ),
                      onOk: () => {
                        this.props.dispatch(
                          deleteConfigPrice({
                            service_utility_config_id: currentData.id,
                            id: record.id,
                          })
                        );
                      },
                      onCancel() {},
                    });
                }}
              >
                <i className="fa fa-trash" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
          );
        },
      },
    ];

    const columns2 = [
      {
        title: this.props.intl.formatMessage(messages.time),
        dataIndex: "time",
        key: "time",
        render: (text, record) => (
          <span>{`${moment(record.start_time, "HH:mm").format(
            "HH:mm"
          )} - ${moment(record.end_time, "HH:mm").format("HH:mm")}`}</span>
        ),
      },
      // {
      //   align: "right",
      //   title: (
      //     <span>{this.props.intl.formatMessage(messages.price)} (VNĐ)</span>
      //   ),
      //   dataIndex: "price_hourly",
      //   key: "price",
      //   render: (text, record) => (
      //     <span>
      //       <span>{formatPrice(text)}</span>
      //       <span>{currentData.type != 1 ? " (Miễn phí)" : ""}</span>
      //     </span>
      //   ),
      // },
      {
        align: "center",
        width: 100,
        title: <span>{this.props.intl.formatMessage(messages.action)}</span>,
        dataIndex: "",
        key: "x",
        render: (text, record) => {
          return (
            <Tooltip title={this.props.intl.formatMessage(messages.delete)}>
              <Row
                type="flex"
                style={{
                  color: "#F15A29",
                  cursor: "pointer",
                  justifyContent: "center",
                }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  Modal.confirm({
                    autoFocusButton: null,
                    title: this.props.intl.formatMessage(
                      messages.confirmDeleteTime
                    ),
                    okText: this.props.intl.formatMessage(messages.agree),
                    okType: "danger",
                    width: 500,
                    style: { top: 280 },
                    cancelText: this.props.intl.formatMessage(messages.cancel),
                    onOk: () => {
                      this.props.dispatch(
                        deleteConfigPrice({
                          service_utility_config_id: currentData.id,
                          id: record.id,
                        })
                      );
                    },
                    onCancel() {},
                  });
                }}
              >
                <i className="fa fa-trash" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
          );
        },
      },
    ];

    // if (
    //   !auth_group.checkRole([config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT])
    // ) {
    //   columns1.splice(columns1.length - 1, 1);
    //   columns2.splice(columns2.length - 1, 1);
    // }

    return (
      <Page noPadding>
        <Row key="row1">
          <Col
            style={{
              textAlign: "center",
              display: "flex",
              justifyContent: "center",
            }}
          >
            {data.length > 0 && (
              <Radio.Group
                size="large"
                value={this.state.currentSelected}
                onChange={this.handleSizeChange}
              >
                {data.map((ser, index) => {
                  return (
                    <Radio.Button key={`row-${index}`} value={index}>
                      {language === "en" ? ser.name_en : ser.name}
                    </Radio.Button>
                  );
                })}
              </Radio.Group>
            )}
            <WithRole
              roles={[config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT]}
            >
              <Col
                style={{
                  position: "absolute",
                  right: 0,
                }}
              >
                <Button
                  type="primary"
                  danger
                  onClick={() => {
                    this.setState({ isVisible: true });
                  }}
                >
                  {this.props.intl.formatMessage(messages.addNewSlot)}
                </Button>
              </Col>
            </WithRole>
          </Col>
          {!!currentData && (
            <Col style={{ marginTop: 48 }}>
              <Row>
                <Col span={7} offset={6} style={{ fontSize: 18 }}>
                  {this.props.intl.formatMessage(messages.name)}:{" "}
                  <span
                    style={{
                      color: "black",
                      fontWeight: "bold",
                      fontSize: 18,
                      paddingLeft: 8,
                    }}
                  >
                    {language === "en" ? currentData.name_en : currentData.name}
                  </span>
                </Col>
                <Col span={11} style={{ fontSize: 18 }}>
                  {this.props.intl.formatMessage(messages.address)}:{" "}
                  <span
                    style={{
                      color: "black",
                      fontWeight: "bold",
                      fontSize: 18,
                      paddingLeft: 8,
                    }}
                  >
                    {language === "en"
                      ? currentData.address_en
                      : currentData.address}
                  </span>
                </Col>
                <Col span={7} offset={6} style={{ fontSize: 18 }}>
                  {this.props.intl.formatMessage(messages.Type)}:{" "}
                  <span
                    style={{
                      color: "black",
                      fontWeight: "bold",
                      fontSize: 18,
                      paddingLeft: 8,
                    }}
                  >
                    {currentData.type == 1
                      ? this.props.intl.formatMessage(messages.fee)
                      : this.props.intl.formatMessage(messages.free)}
                  </span>
                </Col>
                <Col span={11} style={{ fontSize: 18 }}>
                  {this.props.intl.formatMessage(messages.slotFree)}:{" "}
                  <span
                    style={{
                      color: "black",
                      fontWeight: "bold",
                      fontSize: 18,
                      paddingLeft: 8,
                    }}
                  >
                    {currentData.total_slot}
                  </span>
                </Col>
              </Row>
            </Col>
          )}
          <Col span={14} offset={5} style={{ marginTop: 16 }}>
            <Row>
              <WithRole
                roles={[config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT]}
              >
                <Col style={{ textAlign: "right" }}>
                  <Tooltip
                    title={this.props.intl.formatMessage(messages.addTime)}
                  >
                    <Button
                      type="link"
                      size="large"
                      disabled={!data.length}
                      onClick={() => {
                        this.setState({
                          isVisibleModalPrice: true,
                        });
                      }}
                    >
                      <Icon type="plus-circle" style={{ fontSize: 24 }} />
                    </Button>
                  </Tooltip>
                </Col>
              </WithRole>

              <Col>
                <Table
                  dataSource={configPrice ? configPrice.data : []}
                  columns={
                    !!currentData && currentData.booking_type == 2
                      ? columns2
                      : columns1
                  }
                  bordered
                  rowKey="id"
                  loading={!!configPrice && configPrice.loading}
                />
              </Col>
            </Row>
          </Col>

          <ModalAddSlot
            visible={this.state.isVisible}
            onCancel={() => {
              if (creating) return;
              this.setState({ isVisible: false });
            }}
            confirmLoading={creating}
            onSave={(values) => {
              this.props.dispatch(
                createConfig({
                  ...values,
                  service_utility_free_id: this.props.match.params.id,
                })
              );
            }}
            maskClosable={!creating}
            okText={this.props.intl.formatMessage(messages.add)}
            title={this.props.intl.formatMessage(messages.addNewSlot)}
          />

          <ModalAddSlot
            visible={this.state.isVisibleUpdate}
            dataSlot={currentSlot}
            onCancel={() => {
              if (creating) return;
              this.setState({ isVisibleUpdate: false });
            }}
            confirmLoading={updating}
            onSave={(values) => {
              this.props.dispatch(
                updateConfig({
                  ...values,
                  id: currentSlot.id,
                  service_utility_free_id: this.props.match.params.id,
                })
              );
            }}
            maskClosable={!updating}
            okText={this.props.intl.formatMessage(messages.update)}
            title={this.props.intl.formatMessage(messages.updateSlot)}
          />
          <ModalAddPrice
            bookingType={currentData ? currentData.booking_type : 0}
            type={2}
            visible={this.state.isVisibleModalPrice}
            onCancel={() => {
              if (creatingPrice) return;

              this.setState({ isVisibleModalPrice: false });
            }}
            confirmLoading={creatingPrice}
            onSave={(values) => {
              this.props.dispatch(
                createConfigPrice({
                  ...values,
                  start_time: values.start_time.format("HH:mm"),
                  end_time: values.end_time.format("HH:mm"),
                  service_utility_config_id: currentData ? currentData.id : 0,
                })
              );
            }}
            maskClosable={!creatingPrice}
          />
        </Row>
        {!!data.length &&
          auth_group.checkRole([
            config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
          ]) && (
            <Row
              key="row2"
              style={{
                textAlign: "center",
                display: "flex",
                justifyContent: "center",
                marginTop: 32,
              }}
            >
              <Button
                type="danger"
                ghost
                style={{
                  marginRight: 8,
                }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  Modal.confirm({
                    autoFocusButton: null,
                    title: this.props.intl.formatMessage(
                      messages.confirmDeleteSlot
                    ),
                    okText: this.props.intl.formatMessage(messages.agree),
                    okType: "danger",
                    cancelText: this.props.intl.formatMessage(messages.cancel),
                    width: 350,
                    style: { top: 280 },
                    onOk: () => {
                      this.props.dispatch(
                        deleteConfigPlace({
                          service_utility_config_id: currentData.id,
                          id: currentSlot.id,
                          configId: this.props.match.params.id,
                        })
                      );
                    },
                    onCancel() {},
                  });
                }}
              >
                {this.props.intl.formatMessage(messages.deleteSlot)}
              </Button>
              <Button
                type="danger"
                ghost
                style={{
                  color: "#1890ff",
                  borderColor: "#1890ff",
                }}
                onClick={() => {
                  this.setState({ isVisibleUpdate: true });
                }}
              >
                {this.props.intl.formatMessage(messages.update)}
              </Button>
            </Row>
          )}
      </Page>
    );
  }
}

ConfigUtilityPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  configUtilityPage: makeSelectConfigUtilityPage(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "configUtilityPage", reducer });
const withSaga = injectSaga({ key: "configUtilityPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ConfigUtilityPage));
