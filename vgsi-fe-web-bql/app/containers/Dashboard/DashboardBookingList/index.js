/**
 *
 * BookingList
 *
 */

import {
  Button,
  Col,
  DatePicker,
  Form,
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import WithRole from "components/WithRole";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import _ from "lodash";
import moment from "moment";
import PropTypes from "prop-types";
import queryString from "query-string";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import { ALL_ROLE_NAME } from "utils/config";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import InputNumberFormat from "../../../components/InputNumberFormat";
import Page from "../../../components/Page/Page";
import { selectAuthGroup } from "../../../redux/selectors";
import { config, formatPrice, notificationBar } from "../../../utils";
import { globalStyles } from "../../../utils/constants";
import messages from "../messages";
import ModalCreate from "./ModalCreate";
import {
  createBooking,
  defaultAction,
  fetchAllBookingAction,
  fetchApartmentAction,
  fetchDetailService,
  fetchServiceFreeAction,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectDashboardBookingList from "./selectors";
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
          <Form>
            <Form.Item>
              {getFieldDecorator("reason", {
                rules: [
                  {
                    required: true,
                    message: intl.formatMessage(messages.reasonRequest),
                  },
                ],
              })(
                <Input.TextArea
                  //style={{ minHeight: "150" }}
                  placeholder={reasonPlaceholderText}
                  maxLength={200}
                />
              )}
            </Form.Item>
          </Form>
        </Modal>
      );
    }
  }
);
/* eslint-disable react/prefer-stateless-function */
const formItemLayout = {
  labelCol: {
    span: 6,
  },
  wrapperCol: {
    span: 18,
  },
};

const topCol3 = {
  md: {
    span: 5,
  },
  lg: {
    span: 3,
  },
};

@Form.create()
export class BookingList extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {},
      visible: false,
      visibleCreate: false,
      currentId: 0,
      visibleIncurredFee: false,
      loading_incurred_fee: false,
      currentBooking: {},
    };
    this._onSearch = _.debounce(this.onSearch, 300);
    this._onSearchService = _.debounce(this.onSearchService, 300);
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this.reload(this.props.location.search);
    this.props.dispatch(fetchApartmentAction());
    this.props.dispatch(fetchServiceFreeAction());
    this.props.dispatch(fetchDetailService("/utility-free"));
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
  }

  reload = (search, reset) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }
    this.setState({ current: params.page, filter: reset ? {} : params }, () => {
      this.props.dispatch(fetchAllBookingAction(reset ? { page: 1 } : params));
      reset && this.props.history.push("/main/bookinglist");
    });
  };

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
  };
  onSearchService = (keyword) => {
    this.props.dispatch(fetchServiceFreeAction({ keyword }));
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/bookinglist?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };
  showModal = (id) => {
    this.setState({
      visible: true,
      currentId: id,
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
  saveFormRef = (formRef) => {
    this.formRef = formRef;
  };
  handleOk = async () => {
    try {
      let res = await window.connection.changeStatusBookingUtility({
        service_map_management_id: this.props.bookingList.servive_data.id,
        is_active_all: 0,
        is_active_array: [this.state.currentId],
        title: this.state.currentBooking.service_utility_free_name,
      });
      if (res.success) {
        notificationBar(this.props.intl.formatMessage(messages.bookingSuccess));
        this.setState({
          visible: false,
        });
        this.reload(this.props.location.search);
      }
    } catch (error) {
      console.log(error);
    }
  };
  handleCancel2 = async (values) => {
    const { form } = this.formRef.props;

    try {
      let res = await window.connection.cancelBookingUtility({
        id: this.state.currentId,
        reason: values.reason,
        title: this.state.currentBooking.service_utility_free_name,
      });
      if (res.success) {
        notificationBar(
          this.props.intl.formatMessage(messages.bookingCancelSuccess)
        );
        this.setState({
          visible: false,
          visible2: false,
        });
        this.reload(this.props.location.search);
        form.resetFields();
      }
    } catch (error) {
      console.log(error);
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
  addFee = async (values) => {
    const { currentId } = this.state;
    try {
      this.setState({
        visibleIncurredFee: true,
        loading_incurred_fee: true,
      });
      let res = await window.connection.addIncurredFee({
        ...values,
        service_utility_booking_id: currentId,
      });
      if (res.success) {
        this.setState({
          loading_incurred_fee: false,
          visibleIncurredFee: false,
        });
        notificationBar(this.props.intl.formatMessage(messages.successMessage));
        this.props.form.resetFields();
        this.reload(this.props.location.search);
      }
    } catch (error) {
      console.log(error);
    }
  };

  handlerAddIncurredMoney = () => {
    const { form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.addFee(values);
    });
  };

  closeModal = () => {
    this.setState({
      visible: false,
    });
  };

  _detailBooking = (record, lst) => {
    this.props.history.push(`/main/bookinglist/detail/${record.id}/info`, {
      record,
      lst,
    });
  };
  _receptionBooking = () => {
    this.props.history.push("/main/finance/reception");
  };

  _addBooking = () => {
    this.props.history.push("/main/bookinglist/add");
  };

  render() {
    const { bookingList, auth_group, intl } = this.props;
    const { apartments, services, loading, data, totalPage } = bookingList;
    const { current, visibleIncurredFee, loading_incurred_fee } = this.state;
    const { getFieldDecorator } = this.props.form;
    const { lst } = services;
    let { formatMessage } = this.props.intl;
    const columns = [
      {
        width: 50,
        align: "center",
        fixed: "left",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(0, loading ? current - 2 : current - 1) * 20 + index + 1}
          </span>
        ),
      },
      {
        width: 150,
        fixed: "left",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.property)}
          </span>
        ),
        dataIndex: "apartment_name",
        key: "apartment_name",
        render: (text, record, index) => (
          <div>
            <span>{`${record.apartment_name} `}</span>
            <span>{`(${record.apartment_parent_path})`}</span>
          </div>
        ),
      },
      {
        width: 150,
        fixed: "left",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.utility)}
          </span>
        ),
        dataIndex: "service_utility_free_name",
        key: "service_utility_free_name",
        render: (text, record, index) => (
          <div>
            <span>
              {this.props.language === "vi"
                ? record.service_utility_free_name
                : record.service_utility_free_name_en}
            </span>
          </div>
        ),
      },
      {
        width: 110,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.placeName)}
          </span>
        ),
        dataIndex: "service_utility_config_name",
        key: "service_utility_config_name",
        render: (text, record, index) => (
          <div>
            <span>
              {this.props.language === "vi"
                ? record.service_utility_config_name
                : record.service_utility_config_name_en}
            </span>
          </div>
        ),
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.time)}
          </span>
        ),
        dataIndex: "book_time",
        key: "book_time",
        render: (text, record) =>
          record.book_time.map((time, i) => {
            return (
              <div
                style={{
                  paddingBottom: i < record.book_time.length - 1 ? 10 : 0,
                }}
                key={i}
              >
                {moment.unix(time.start_time).format("HH:mm")} -{" "}
                {moment.unix(time.end_time).format("HH:mm DD/MM/YYYY")}
              </div>
            );
          }),
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.feeType)}
          </span>
        ),
        dataIndex: "price",
        key: "price",
        render: (text) =>
          text === 0 ? (
            <span>{formatMessage(messages.free)}</span>
          ) : (
            <span>{formatMessage(messages.notFree)}</span>
          ),
      },
      {
        align: "center",
        width: 110,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.bookingCode)}
          </span>
        ),
        dataIndex: "code",
        key: "code",
        render: (text, record) =>
          record.is_paid === 0 &&
          record.status === 1 &&
          record.service_payment_total_ids !== null &&
          !!record.service_payment_total_ids.length &&
          auth_group.checkRole([config.ALL_ROLE_NAME.FINANCE_CREATE_BILL]) ? (
            <Tooltip title={formatMessage(messages.createReceipts)}>
              <Button
                type="link"
                style={{ padding: 0 }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  this.props.history.push("/main/finance/reception", {
                    payment_gen_code: record.payment_gen_code
                      ? record.payment_gen_code
                      : undefined,
                    apartment_id: record.apartment_id,
                    ids: record.service_payment_total_ids,
                    limit_payment: true,
                  });
                }}
              >
                <span style={{ textDecoration: "underline" }}>
                  {record.code}
                </span>
              </Button>
            </Tooltip>
          ) : (
            <span>{record.code}</span>
          ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.payment)}
          </span>
        ),
        dataIndex: "is_paid",
        key: "is_paid",
        render: (text) =>
          text ? (
            <span>{formatMessage(messages.paid)}</span>
          ) : (
            <span>{formatMessage(messages.unpaid)}</span>
          ),
      },
      // {
      //   width: 95,
      //   title: (
      //     <span className={styles.nameTable}>
      //       {formatMessage(messages.amountOfMoney)}
      //     </span>
      //   ),
      //   dataIndex: "price",
      //   key: "price2",
      //   render: (text) => <span>{formatPrice(text)} đ</span>,
      // },
      {
        width: 110,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.deposit)}
          </span>
        ),
        dataIndex: "total_deposit_money",
        key: "total_deposit_money",
        render: (text) => <span>{formatPrice(text)} đ</span>,
      },
      {
        width: 110,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.totalAmount)}
          </span>
        ),
        dataIndex: "total_incurred_money",
        key: "total_incurred_money",
        render: (text) => <span>{formatPrice(text)} đ</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.status)}
          </span>
        ),
        dataIndex: "status",
        key: "status_name",
        render: (text) =>
          text === 0 ? (
            <span>{formatMessage(messages.pending)}</span>
          ) : text === 1 ? (
            <span>{formatMessage(messages.confirmed)}</span>
          ) : text === -1 ? (
            <span>{formatMessage(messages.residentCancel)}</span>
          ) : text === -2 ? (
            <span>{formatMessage(messages.denied)}</span>
          ) : (
            <span>{formatMessage(messages.systemCancel)}</span>
          ),
      },

      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.evaluate)}
          </span>
        ),
        align: "center",
        dataIndex: "service_utility_ratting.star",
        key: "evaluate",
        render: (text) => text > 0 && <span>{text}/5</span>,
        width: 50,
      },
      {
        width: 230,
        align: "center",
        fixed: "right",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip
              title={
                record.status === 0
                  ? formatMessage(messages.actionDo)
                  : record.status === 1
                  ? formatMessage(messages.approved)
                  : formatMessage(messages.cancelled)
              }
            >
              <Row
                type="flex"
                align="middle"
                style={
                  record.status === 0 &&
                  auth_group.checkRole([
                    ALL_ROLE_NAME.SET_WIDGET_SERVICE_UTILITY_BOOKING_CHANGE_STATUS,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  if (record.status === 0) {
                    this.showModal(record.id);
                  }
                  this.setState({
                    currentBooking: record,
                  });
                }}
              >
                <i className="fa fa-check-circle" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
            &ensp;&ensp;| &ensp;&ensp;
            <Tooltip title={formatMessage(messages.detail)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    ALL_ROLE_NAME.SET_WIDGET_SERVICE_UTILITY_BOOKING_DETAIL,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME
                      .SET_WIDGET_SERVICE_UTILITY_BOOKING_DETAIL,
                  ]) && this._detailBooking(record, lst);
                }}
              >
                <i className="fa fa-eye" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
            &ensp;&ensp;| &ensp;&ensp;
            <Tooltip title={formatMessage(messages.genFee)}>
              <Row
                type="flex"
                align="middle"
                style={
                  record.status == 1 &&
                  auth_group.checkRole([
                    ALL_ROLE_NAME.SET_WIDGET_SERVICE_UTILITY_BOOKING_CREATE_INCURRED_FEE,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  if (
                    record.status == 1 &&
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME
                        .SET_WIDGET_SERVICE_UTILITY_BOOKING_CREATE_INCURRED_FEE,
                    ])
                  ) {
                    this.setState({
                      visibleIncurredFee: true,
                      currentId: record.id,
                    });
                  }
                }}
              >
                <i className="fa fa-plus" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
            <WithRole roles={[config.ALL_ROLE_NAME.MANAGE_INVOICE_BILL]}>
              &ensp;&ensp;| &ensp;&ensp;
              <Tooltip title={formatMessage(messages.createVoucher)}>
                <Row
                  type="flex"
                  align="middle"
                  style={{
                    color: "blue",
                    cursor: "pointer",
                  }}
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.props.history.push("/main/finance/invoice-bill", {
                      apartment_id: record.apartment_id,
                      ids: [record.service_payment_fee_deposit_ids],
                    });
                  }}
                >
                  <Icon type="file-done" />
                </Row>
              </Tooltip>
            </WithRole>
          </Row>
        ),
      },
    ];

    const { filter } = this.state;

    return (
      <>
        <Page className={styles.bookingListPage} inner>
          <div>
            <Row gutter={[24, 16]} style={{ paddingBottom: 24 }}>
              <Col {...topCol3}>
                <Select
                  style={{ width: "100%" }}
                  loading={apartments.loading}
                  showSearch
                  placeholder={formatMessage(messages.choseProperty)}
                  optionFilterProp="children"
                  notFoundContent={
                    apartments.loading ? <Spin size="small" /> : null
                  }
                  onSearch={this._onSearch}
                  onChange={(value, opt) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["apartment_id"]: value,
                      },
                    });
                    if (!opt) {
                      this._onSearch("");
                    }
                  }}
                  allowClear
                  value={filter["apartment_id"]}
                >
                  {apartments.lst.map((gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${gr.name} (${gr.parent_path})`}</Select.Option>
                    );
                  })}
                </Select>
              </Col>

              <Col {...topCol3}>
                <Select
                  style={{ width: "100%" }}
                  loading={services.loading}
                  showSearch
                  placeholder={formatMessage(messages.utility)}
                  optionFilterProp="children"
                  notFoundContent={
                    services.loading ? <Spin size="small" /> : null
                  }
                  onSearch={this._onSearchService}
                  onChange={(value) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["service_utility_free_id"]: value,
                      },
                    });
                  }}
                  allowClear
                  value={filter["service_utility_free_id"]}
                >
                  {services.lst.map((gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${
                        this.props.language === "vi" ? gr.name : gr.name_en
                      }`}</Select.Option>
                    );
                  })}
                </Select>
              </Col>
              <Col {...topCol3}>
                <Select
                  // showSearch
                  style={{ width: "100%" }}
                  placeholder={formatMessage(messages.status)}
                  // optionFilterProp="children"
                  // filterOption={(input, option) =>
                  //   option.props.children
                  //     .toLowerCase()
                  //     .indexOf(input.toLowerCase()) >= 0
                  // }
                  onChange={(value) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["status"]: value,
                      },
                    });
                  }}
                  allowClear
                  value={filter["status"]}
                >
                  <Select.Option value="0">
                    {formatMessage(messages.pending)}
                  </Select.Option>
                  <Select.Option value="1">
                    {formatMessage(messages.confirmed)}
                  </Select.Option>
                  <Select.Option value="-1">
                    {formatMessage(messages.residentCancel)}
                  </Select.Option>
                  <Select.Option value="-2">
                    {formatMessage(messages.denied)}
                  </Select.Option>
                  <Select.Option value="-3">
                    {formatMessage(messages.systemCancel)}
                  </Select.Option>
                </Select>
              </Col>
              <Col {...topCol3}>
                <Select
                  // showSearch
                  style={{ width: "100%" }}
                  placeholder={formatMessage(messages.paymentStatus)}
                  // optionFilterProp="children"
                  // filterOption={(input, option) =>
                  //   option.props.children
                  //     .toLowerCase()
                  //     .indexOf(input.toLowerCase()) >= 0
                  // }
                  onChange={(value) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["is_paid"]: value,
                      },
                    });
                  }}
                  allowClear
                  value={filter["is_paid"]}
                >
                  <Select.Option value="0">
                    {formatMessage(messages.unpaid)}
                  </Select.Option>
                  <Select.Option value="1">
                    {formatMessage(messages.paid)}
                  </Select.Option>
                </Select>
              </Col>
              <Col {...topCol3}>
                <DatePicker
                  placeholder={formatMessage(messages.start)}
                  style={{ width: "100%" }}
                  format="DD/MM/YYYY"
                  locale={this.props.language}
                  value={
                    filter["start_time_from"]
                      ? moment.unix(filter["start_time_from"])
                      : undefined
                  }
                  onChange={(start_date) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["start_time_from"]: start_date
                          ? start_date.startOf("day").unix()
                          : undefined,
                      },
                    });
                  }}
                />
              </Col>
              <Col {...topCol3}>
                <DatePicker
                  placeholder={formatMessage(messages.end)}
                  style={{ width: "100%" }}
                  format="DD/MM/YYYY"
                  locale={this.props.language}
                  value={
                    filter["start_time_to"]
                      ? moment.unix(filter["start_time_to"])
                      : undefined
                  }
                  onChange={(end_date) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["start_time_to"]: end_date
                          ? end_date.endOf("day").unix()
                          : undefined,
                      },
                    });
                  }}
                />
              </Col>
              <Col {...topCol3}>
                <Button
                  block
                  type="primary"
                  onClick={(e) => {
                    this.props.history.push(
                      `/main/bookinglist?${queryString.stringify({
                        ...this.state.filter,
                        page: 1,
                      })}`
                    );
                  }}
                >
                  {formatMessage(messages.search)}
                </Button>
              </Col>
            </Row>
            <Row style={{ paddingBottom: 16 }}>
              <Tooltip title={formatMessage(messages.refresh)}>
                <Button
                  shape="circle-outline"
                  style={{ padding: 0, marginRight: 10 }}
                  onClick={(e) => {
                    this.reload(this.props.location.search, true);
                  }}
                  icon="reload"
                  size="large"
                />
              </Tooltip>
              {auth_group.checkRole([
                config.ALL_ROLE_NAME.SET_WIDGET_SERVICE_UTILITY_BOOKING_CREATE,
              ]) && (
                <Tooltip title={formatMessage(messages.addNew)}>
                  <Button
                    style={{ marginRight: 10 }}
                    disabled={
                      !auth_group.checkRole([
                        config.ALL_ROLE_NAME
                          .SET_WIDGET_SERVICE_UTILITY_BOOKING_CREATE,
                      ])
                    }
                    onClick={() => this.setState({ visibleCreate: true })}
                    //onClick={this._addBooking}
                    icon="plus"
                    shape="circle"
                    size="large"
                  />
                </Tooltip>
              )}
              <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_CREATE_BILL]}>
                <Tooltip title={formatMessage(messages.fee)}>
                  <Button
                    onClick={this._receptionBooking}
                    icon="dollar"
                    shape="circle"
                    size="large"
                  />
                </Tooltip>
              </WithRole>
            </Row>

            <Row gutter={24}>
              <Col>
                <Table
                  rowKey="id"
                  loading={loading}
                  scroll={{ x: 1366 }}
                  columns={columns}
                  dataSource={data}
                  locale={{ emptyText: formatMessage(messages.emptyData) }}
                  bordered
                  pagination={{
                    locale: this.props.language === "vi" ? "vi_VN" : "en_GB",
                    pageSize: 20,
                    total: totalPage,
                    current,
                    showTotal: (total, range) =>
                      `${formatMessage(
                        messages.total
                      )} ${total} ${formatMessage(messages.bookNum)}`,
                  }}
                  onRow={(record) => {
                    return {
                      onClick: (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        auth_group.checkRole([
                          config.ALL_ROLE_NAME
                            .SET_WIDGET_SERVICE_UTILITY_BOOKING_DETAIL,
                        ]) &&
                          this.props.history.push(
                            `/main/bookinglist/detail/${record.id}/info`,
                            {
                              record,
                              lst,
                            }
                          );
                      },
                    };
                  }}
                  onChange={this.handleTableChange}
                  expandRowByClick
                />
              </Col>
            </Row>
            <Modal
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
                  ghost
                  type="primary"
                  style={{ width: 150, marginLeft: 10 }}
                  onClick={this.handleOk}
                >
                  {formatMessage(messages.approve)}
                </Button>
              </div>
            </Modal>
            <Modal
              title={formatMessage(messages.costsIncurred)}
              visible={visibleIncurredFee}
              onOk={this.handlerAddIncurredMoney}
              onCancel={() => {
                if (loading_incurred_fee) return;
                this.setState({
                  visibleIncurredFee: false,
                });
                this.props.form.resetFields();
              }}
              okText={formatMessage(messages.addFee)}
              cancelText={formatMessage(messages.cancel)}
              okButtonProps={{ loading: loading_incurred_fee }}
              cancelButtonProps={{ disabled: loading_incurred_fee }}
              maskClosable={false}
            >
              <Form {...formItemLayout}>
                <Form.Item label={formatMessage(messages.price1)} colon={false}>
                  {getFieldDecorator("price", {
                    initialValue: "",
                    rules: [
                      {
                        message: formatMessage(messages.moneyError),
                        required: true,
                        whitespace: true,
                        type: "number",
                        min: 1,
                      },
                      // {
                      //   pattern: regexNumber,
                      //   message: formatMessage(messages.moneyError),
                      // },
                    ],
                  })(
                    <InputNumberFormat
                      style={{ width: "100%" }}
                      maxLength={19}
                    />
                  )}
                </Form.Item>
                <Form.Item
                  label={formatMessage(messages.description)}
                  colon={false}
                >
                  {getFieldDecorator("description", {
                    initialValue: "",
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.descriptionError),
                        whitespace: true,
                      },
                    ],
                  })(
                    <Input.TextArea
                      maxLength={1000}
                      rows={4}
                      spellCheck={false}
                    />
                  )}
                </Form.Item>
                <Form.Item
                  label={`${formatMessage(messages.description)} (EN)`}
                  colon={false}
                >
                  {getFieldDecorator("description_en", {
                    initialValue: "",
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.descriptionEnError),
                        whitespace: true,
                      },
                    ],
                  })(
                    <Input.TextArea
                      maxLength={1000}
                      rows={4}
                      spellCheck={false}
                    />
                  )}
                </Form.Item>
              </Form>
            </Modal>
            <CollectionCreateForm
              intl={intl}
              wrappedComponentRef={this.saveFormRef}
              visible={this.state.visible2}
              onCancel={this.closeModal2}
              onDecline={this.handleCancel}
            />
            <ModalCreate
              bookingList={bookingList}
              language={this.props.language}
              authGroup={bookingList.authGroup}
              dispatch={this.props.dispatch}
              visibleCreate={this.state.visibleCreate}
              setState={this.setState.bind(this)}
              createBooking={(payload) =>
                this.props.dispatch(
                  createBooking({
                    ...payload,
                    callback: () => {
                      this.setState({ visibleCreate: false });
                      this.reload(this.props.location.search);
                    },
                  })
                )
              }
            />
          </div>
        </Page>
      </>
    );
  }
}

BookingList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  bookingList: makeSelectDashboardBookingList(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "dashboardBookingList", reducer });
const withSaga = injectSaga({ key: "dashboardBookingList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(BookingList));
