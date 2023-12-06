/**
 *
 * BookingDetail
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import reducer from "./reducer";
import saga from "./saga";
import {
  defaultAction,
  fetchDetailBookingAction,
  fetchServiceFreeAction,
} from "./actions";
import Page from "../../../components/Page";
import {
  Col,
  Row,
  Button,
  Modal,
  Tooltip,
  Empty,
  Divider,
  Form,
  Input,
} from "antd";
import Exception from "ant-design-pro/lib/Exception";
import makeSelectBookingDetail from "./selectors";
import moment from "moment";
import styles from "./index.less";
import { notificationBar, formatPrice } from "../../../utils";
import config from "../../../utils/config";
import { selectAuthGroup } from "../../../redux/selectors";
import { getFullLinkImage } from "../../../connection";
import { GLOBAL_COLOR } from "../../../utils/constants";
import { injectIntl } from "react-intl";
import messages from "../messages";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { log } from "lodash-decorators/utils";
/* eslint-disable react/prefer-stateless-function */
const CollectionCreateForm = Form.create({ name: "form_in_modal" })(
  // eslint-disable-next-line
  class extends React.Component {
    render() {
      const { visible, onCancel, onDecline, form, intl } = this.props;
      const { getFieldDecorator } = form;
      const reasonPlaceholderText = intl.formatMessage({
        ...messages.reasonPlaceholder,
      });
      return (
        <Modal
          visible={visible}
          title={intl.formatMessage(messages.cancelTitle)}
          okText={intl.formatMessage(messages.okText)}
          okType="danger"
          cancelText={intl.formatMessage(messages.cancel)}
          onCancel={onCancel}
          onOk={onDecline}
          width={"666px"}
          bodyStyle={{ paddingBottom: 0 }}
        >
          <Form layout="vertical">
            <Form.Item>
              {getFieldDecorator("reason", {
                rules: [
                  {
                    required: true,
                    message: intl.formatMessage(messages.reasonRequest),
                  },
                ],
              })(
                <Input
                  placeholder={reasonPlaceholderText}
                  max={200}
                  type="textarea"
                />
              )}
            </Form.Item>
          </Form>
        </Modal>
      );
    }
  }
);
export class BookingDetail extends React.PureComponent {
  constructor(props) {
    super(props);
    const { record, lst } = props.location.state || {};
    this.state = {
      lst,
      record,
      visible: false,
      visible2: false,
      loading: false,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    const { id } = this.props.match.params;

    if (id != undefined) {
      this.props.dispatch(fetchDetailBookingAction({ id: id }));
      this.props.dispatch(fetchServiceFreeAction());
    }
  }

  componentWillReceiveProps(nextProps) {
    const { id } = this.state;
    const idNextProps =
      nextProps.location.state &&
      nextProps.location.state.record &&
      nextProps.location.state.record.id;

    if (
      this.props.bookingDetail.detail.loading !==
        nextProps.bookingDetail.detail.loading &&
      !nextProps.bookingDetail.detail.loading
    ) {
      this.setState({
        record: nextProps.bookingDetail.detail.data,
      });
    }

    if (id !== idNextProps) {
      this.setState({ record: nextProps.location.state.record });
    }
  }
  saveFormRef = (formRef) => {
    this.formRef = formRef;
  };
  handleOk = async () => {
    try {
      this.setState({
        loading: true,
      });
      let res = await window.connection.changeStatusBookingUtility({
        service_map_management_id: this.state.record.service_map_management_id,
        is_active_all: 0,
        is_active_array: [this.state.record.id],
        title: this.state.record.service_utility_free_name,
      });
      if (res.success) {
        this.setState({
          loading: false,
        });
        notificationBar(this.props.intl.formatMessage(messages.noDataBooking));
        this.setState({
          visible: false,
        });
        this.props.history.push("/main/bookinglist");
      }
    } catch (error) {
      console.log("error", error);
    }
  };
  handleCancel2 = async (values) => {
    const { form } = this.formRef.props;

    try {
      this.setState({
        loading: true,
      });
      let res = await window.connection.cancelBookingUtility({
        id: this.state.record.id,
        reason: values.reason,
        title: this.state.record.service_utility_free_name,
      });
      if (res.success) {
        this.setState({
          loading: false,
        });
        notificationBar(this.props.intl.formatMessage(messages.noDataBooking));
        this.setState({
          visible: false,
          visible2: false,
        });
        form.resetFields();
        this.props.history.push("/main/bookinglist");
      }
    } catch (error) {
      console.log("error", error);
    }
  };
  handleCancel = () => {
    const { form } = this.formRef.props;
    form.validateFields((err, values) => {
      if (err) {
        return;
      }
      this.handleCancel2(values);
    });
  };

  showModal = () => {
    this.setState({
      visible: true,
    });
  };

  closeModal = () => {
    this.setState({
      visible: false,
    });
  };
  showModal2 = () => {
    this.setState({
      visible2: true,
      visible: false,
    });
  };

  closeModal2 = () => {
    this.setState({
      visible2: false,
    });
  };
  render() {
    const { bookingDetail, auth_group, intl } = this.props;
    let { formatMessage } = this.props.intl;
    const { detail, services } = bookingDetail;
    const { listService } = services;
    const { record, loading, lst } = this.state;
    const itemService =
      lst !== undefined
        ? lst.find((item) => item.name === record.service_utility_free_name)
        : listService &&
          record &&
          listService.find(
            (item) => item.name === record.service_utility_free_name
          );
    if (!record && !itemService) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={formatMessage(messages.noDataBooking)}
            actions={
              <Button
                type="primary"
                onClick={() => this.props.history.push("/main/bookinglist")}
              >
                {formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }
    return (
      <Page
        loading={detail.loading}
        inner
        className={styles.detailPage}
        key={"block-info"}
      >
        {!detail.loading && !!record && (
          <div>
            <Row type="flex" justify="space-between">
              <Col span={24} style={{ marginBottom: 16 }}>
                <Row type="flex" align="middle">
                  <span
                    onClick={(e) => {
                      e.preventDefault();
                      this.props.history.goBack();
                    }}
                    style={{
                      display: "flex",
                      alignItems: "center",
                      cursor: "pointer",
                      color: GLOBAL_COLOR,
                    }}
                  >
                    <i className="material-icons">keyboard_backspace</i>
                    &ensp; {formatMessage(messages.back)}
                  </span>
                </Row>
              </Col>
            </Row>

            <Row
              gutter={24}
              style={{ marginTop: 16, marginLeft: 0, marginRight: 0 }}
            >
              <Col
                span={6}
                style={{
                  padding: "0px",
                }}
              >
                {!!itemService &&
                !!itemService.medias &&
                !!itemService.medias.logo ? (
                  <img
                    src={getFullLinkImage(itemService.medias.logo)}
                    width="80%"
                    height={
                      !!itemService.medias && !!itemService.medias.logo
                        ? null
                        : "100%"
                    }
                    style={{
                      paddingBottom: 15,
                    }}
                  />
                ) : (
                  <Empty
                    style={{
                      alignSelf: "center",
                      width: "100%",
                      paddingBottom: 10,
                    }}
                    description={formatMessage(messages.noImage)}
                    image="https://gw.alipayobjects.com/zos/antfincdn/ZHrcdLPrvN/empty.svg"
                  />
                )}
                <Row className="rowItem" style={{ marginTop: 12 }}>
                  <Col span={6}> {formatMessage(messages.utility)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {this.props.language === "vi"
                      ? record.service_utility_free_name
                      : record.service_utility_free_name_en}
                  </Col>
                </Row>
                <Divider style={{ minWidth: "80%", width: "80%" }} />
                <Row className="rowItem">
                  <Col span={6}> {formatMessage(messages.feeType)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {record.price === 0
                      ? formatMessage(messages.free)
                      : formatMessage(messages.notFree)}
                  </Col>
                </Row>
                <Divider style={{ minWidth: "80%", width: "80%" }} />
                <Row className="rowItem">
                  <Col span={6}>{formatMessage(messages.placeName)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {this.props.language === "vi"
                      ? record.service_utility_config_name
                      : record.service_utility_config_name_en}
                  </Col>
                </Row>
                <Divider style={{ minWidth: "80%", width: "80%" }} />
                <Row className="rowItem">
                  <Col span={6}>{formatMessage(messages.evaluate)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {record.service_utility_ratting &&
                      record.service_utility_ratting.star &&
                      Number.isInteger(record.service_utility_ratting.star) &&
                      [...Array(record.service_utility_ratting.star)].map(
                        (e, i) => (
                          <span
                            key={i}
                            style={{
                              color: " rgb(255, 210, 48)",
                            }}
                          >
                            <i className="material-icons">star</i>
                          </span>
                        )
                      )}
                    {record.service_utility_ratting &&
                      record.service_utility_ratting.star &&
                      Number.isInteger(record.service_utility_ratting.star) &&
                      [...Array(5 - record.service_utility_ratting.star)].map(
                        (e, i) => (
                          <span
                            key={i}
                            style={{
                              color: " rgb(255, 210, 48)",
                            }}
                          >
                            <i className="material-icons">star_border</i>
                          </span>
                        )
                      )}
                  </Col>
                </Row>
              </Col>
              <Col span={9}>
                <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.bookingCode)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {record.is_paid === 0 &&
                    record.status === 1 &&
                    record.service_payment_total_ids !== null &&
                    !!record.service_payment_total_ids.length &&
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.FINANCE_CREATE_BILL,
                    ]) ? (
                      <Tooltip title={formatMessage(messages.createReceipts)}>
                        <span
                          onClick={() => {
                            this.props.history.push("/main/finance/reception", {
                              payment_gen_code: record.payment_gen_code
                                ? record.payment_gen_code
                                : undefined,
                              apartment_id: record.apartment_id,
                              ids: record.service_payment_total_ids,
                              limit_payment: true,
                            });
                          }}
                          style={{
                            textDecoration: "underline",
                            cursor: "pointer",
                            color: GLOBAL_COLOR,
                          }}
                        >
                          {record.code}
                        </span>
                      </Tooltip>
                    ) : (
                      <span>{record.code}</span>
                    )}
                  </Col>
                </Row>
                <Divider />
                <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.property)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {record.apartment_name} ({record.apartment_parent_path})
                  </Col>
                </Row>
                <Divider />
                <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.time)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {record.book_time.map((time, i) => {
                      return (
                        <div
                          style={{
                            paddingBottom:
                              i < record.book_time.length - 1 ? 10 : 0,
                          }}
                          key={i}
                        >
                          {moment.unix(time.start_time).format("HH:mm")} -{" "}
                          {moment
                            .unix(time.end_time)
                            .format("HH:mm DD/MM/YYYY")}
                        </div>
                      );
                    })}
                  </Col>
                </Row>
                <Divider />
                {/* <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.peopleNum)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {record.total_adult + record.total_child}
                  </Col>
                </Row>
                <Divider /> */}
                <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.status)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {record.status === 0
                      ? formatMessage(messages.pending)
                      : record.status === 1
                      ? formatMessage(messages.confirmed)
                      : record.status === -1
                      ? formatMessage(messages.residentCancel)
                      : record.status === -2
                      ? formatMessage(messages.denied)
                      : formatMessage(messages.systemCancel)}
                  </Col>
                </Row>
                <Divider />
                {(record.status === -2 ||
                  (record.status === -3 && record && record.reason)) && (
                  <>
                    <Row className="rowItem">
                      <Col span={8}>
                        {formatMessage(messages.reasonReject)}:
                      </Col>
                      <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                        {record.reason}
                      </Col>
                    </Row>
                    <Divider />
                  </>
                )}
                <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.createAt)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {moment.unix(record.created_at).format("HH:mm DD/MM/YYYY")}
                  </Col>
                </Row>
                <Divider />
                <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.updateAt)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {moment.unix(record.updated_at).format("HH:mm DD/MM/YYYY")}
                  </Col>
                </Row>
              </Col>
              <Col span={9}>
                <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.payment)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {record.is_paid === 0
                      ? formatMessage(messages.unpaid)
                      : formatMessage(messages.paid)}
                  </Col>
                </Row>
                <Divider />
                {/* <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.price)}</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {formatPrice(record.price)} đ
                  </Col>
                </Row>

                <Divider /> */}
                <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.deposit)}:</Col>
                  <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                    {formatPrice(record.total_deposit_money)} đ
                  </Col>
                </Row>

                {record.total_incurred_money > 0 && (
                  <>
                    <Divider />
                    <Row className="rowItem">
                      <Col span={8}>{formatMessage(messages.totalAmount)}:</Col>
                      <Col offset={1} span={14} style={{ fontWeight: "bold" }}>
                        {formatPrice(record.total_incurred_money)} đ
                      </Col>
                    </Row>
                  </>
                )}
                <Divider />
                <Row className="rowItem">
                  <Col span={8}>{formatMessage(messages.note)}:</Col>
                  <Col
                    offset={1}
                    span={14}
                    style={{ fontWeight: "bold", wordWrap: "break-word" }}
                  >
                    {record.description}
                  </Col>
                </Row>

                <Divider />
              </Col>
            </Row>
            <Row
              style={{
                display: "flex",
                justifyContent: "center",
                marginTop: 30,
              }}
            >
              {record.status === 0 && (
                <Col offset={0} style={{ paddingLeft: 0 }}>
                  <Button
                    danger
                    type="danger"
                    onClick={(e) => {
                      e.preventDefault();
                      this.showModal2();
                    }}
                  >
                    {formatMessage(messages.reject)}
                  </Button>
                  <Button
                    danger
                    type="primary"
                    style={{ marginLeft: 20 }}
                    onClick={this.handleOk}
                  >
                    {formatMessage(messages.approve)}
                  </Button>
                </Col>
              )}
            </Row>
            {/* <Modal
              centered
              title={formatMessage(messages.actionTitle)}
              visible={this.state.visible}
              onOk={this.handleOk}
              onCancel={this.closeModal}
              okText={formatMessage(messages.approve)}
              cancelText={formatMessage(messages.reject)}
              destroyOnClose={true}
              footer={false}
              width={"35%"}
            >
              <p>{formatMessage(messages.actionContent)}</p>
              <div style={{ textAlign: "right" }}>
                <Button
                  type="danger"
                  style={{ width: 150 }}
                  onClick={this.showModal2}
                >
                  {formatMessage(messages.reject)}
                </Button>
                <Button
                  loading={loading}
                  ghost
                  type="primary"
                  style={{ width: 150, marginLeft: 10 }}
                  onClick={this.handleOk}
                >
                  {formatMessage(messages.approve)}
                </Button>
              </div>
            </Modal> */}
            <CollectionCreateForm
              intl={intl}
              wrappedComponentRef={this.saveFormRef}
              visible={this.state.visible2}
              onCancel={this.closeModal2}
              onDecline={this.handleCancel}
            />
          </div>
        )}
      </Page>
    );
  }
}

BookingDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  bookingDetail: makeSelectBookingDetail(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "bookingDetail", reducer });
const withSaga = injectSaga({ key: "bookingDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(BookingDetail));
