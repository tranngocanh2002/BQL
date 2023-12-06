/**
 *
 * BookingList
 *
 */

import {
  Button,
  Col,
  DatePicker,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import _ from "lodash";
import moment from "moment";
import PropTypes from "prop-types";
import queryString from "query-string";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../utils";
import messages from "../messages";
import {
  defaultAction,
  fetchAllBookingFeeAction,
  fetchApartmentAction,
  fetchDetailService,
  fetchServiceFreeAction,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectServiceBookingFeeList from "./selectors";

const col6 = {
  md: 6,
  lg: 5,
  xl: 4,
};

const col5 = {
  md: 5,
  lg: 4,
  xl: 3,
};

/* eslint-disable react/prefer-stateless-function */
export class ServiceBookingFeeList extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {},
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
      this.props.dispatch(
        fetchAllBookingFeeAction(reset ? { page: 1 } : params)
      );
    });
    reset && this.props.history.push(`${this.props.location.pathname}`);
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
          `/main/service/detail/utility-free/booking-fee-list?${queryString.stringify(
            {
              ...this.state.filter,
              page: this.state.current,
            }
          )}`
        );
      }
    );
  };

  render() {
    const { bookingFeeList, auth_group, intl, language } = this.props;
    const { apartments, services, loading, data, totalPage } = bookingFeeList;
    const { current } = this.state;
    const plhDate = intl.formatMessage({ ...messages.startDate });
    const columns = [
      {
        width: 50,
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
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.utility} />
          </span>
        ),
        render: (text, record) => {
          const ser = services.lst.find(
            (item) => item.id === record.service_utility_free_id
          );
          return (
            <span>
              {ser ? (language === "vi" ? ser.name : ser.name_en) : text}
            </span>
          );
        },
        dataIndex: "service_utility_free_name",
        key: "service_utility_free_name",
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.property} />
          </span>
        ),
        dataIndex: "apartment_name",
        key: "apartment_name",
        render: (text, record) => (
          <span>
            {`${record.apartment_name} `}
            <span>{`(${record.apartment_parent_path})`}</span>
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.codeBooking} />
          </span>
        ),
        dataIndex: "booking_code",
        key: "booking_code",
        render: (text, record) =>
          record.is_paid === 0 &&
          record.service_payment_total_ids !== null &&
          !!record.service_payment_total_ids.length &&
          auth_group.checkRole([config.ALL_ROLE_NAME.FINANCE_CREATE_BILL]) &&
          auth_group.checkRole([
            config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
          ]) ? (
            <Tooltip title={<FormattedMessage {...messages.createReceipt} />}>
              <Button
                type="link"
                style={{ padding: 0 }}
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
              >
                <span style={{ textDecoration: "underline" }}>
                  {record.booking_code}
                </span>
              </Button>
            </Tooltip>
          ) : (
            <span>{record.booking_code}</span>
          ),
      },

      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.status} />
          </span>
        ),
        dataIndex: "is_paid",
        key: "is_paid",
        render: (text) =>
          text ? (
            <span>
              <FormattedMessage {...messages.paid} />
            </span>
          ) : (
            <span>
              <FormattedMessage {...messages.unpaid} />
            </span>
          ),
      },
      // {
      //   title: (
      //     <span className={styles.nameTable}>
      //       <FormattedMessage {...messages.amountMoney} />
      //     </span>
      //   ),
      //   dataIndex: "price",
      //   key: "price",
      //   render: (text) => <span>{formatPrice(text)} đ</span>,
      // },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.deposit} />
          </span>
        ),
        dataIndex: "total_deposit_money",
        key: "total_deposit_money",
        render: (text) => <span>{formatPrice(text)} đ</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.totalMoney} />
          </span>
        ),
        dataIndex: "total_incurred_money",
        key: "total_incurred_money",
        render: (text) => <span>{formatPrice(text)} đ</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.status} />
          </span>
        ),
        dataIndex: "status",
        key: "status_name",
        render: (text) => (
          <span>
            {text === -1
              ? this.props.intl.formatMessage(messages.bookingCancel)
              : text === -2
              ? this.props.intl.formatMessage(messages.canceled)
              : text === -3
              ? this.props.intl.formatMessage(messages.systemCancel)
              : text === 0
              ? this.props.intl.formatMessage(messages.pending)
              : this.props.intl.formatMessage(messages.confirmed)}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.update} />
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text, record) => (
          <span>
            {moment.unix(record.updated_at).format("HH:mm DD/MM/YYYY")}
          </span>
        ),
      },
    ];

    const { filter } = this.state;

    return (
      <>
        <Row gutter={[24, 16]} style={{ marginBottom: 16 }}>
          <Col {...col6}>
            <Select
              style={{ width: "100%" }}
              loading={apartments.loading}
              showSearch
              placeholder={<FormattedMessage {...messages.selectProperty} />}
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

          <Col {...col6}>
            <Select
              style={{ width: "100%" }}
              loading={services.loading}
              showSearch
              placeholder={<FormattedMessage {...messages.selectUtility} />}
              optionFilterProp="children"
              notFoundContent={services.loading ? <Spin size="small" /> : null}
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
                  <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>
                    {language === "vi" ? gr.name : gr.name_en}
                  </Select.Option>
                );
              })}
            </Select>
          </Col>
          <Col {...col6}>
            <Select
              showSearch
              style={{ width: "100%" }}
              placeholder={<FormattedMessage {...messages.condition} />}
              optionFilterProp="children"
              filterOption={(input, option) =>
                option.props.children
                  .toLowerCase()
                  .indexOf(input.toLowerCase()) >= 0
              }
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
                <FormattedMessage {...messages.unpaid} />
              </Select.Option>
              <Select.Option value="1">
                <FormattedMessage {...messages.paid} />
              </Select.Option>
            </Select>
          </Col>
          <Col {...col5} xl={4}>
            <DatePicker
              placeholder={plhDate}
              style={{ width: "100%" }}
              format="DD/MM/YYYY"
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
          <Col {...col6}>
            <DatePicker
              placeholder={intl.formatMessage({ ...messages.endDate })}
              style={{ width: "100%" }}
              format="DD/MM/YYYY"
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
          <Col {...col5} xl={4}>
            <Button
              block
              type="primary"
              onClick={(e) => {
                this.props.history.push(
                  `/main/service/detail/utility-free/booking-fee-list?${queryString.stringify(
                    {
                      ...this.state.filter,
                      page: 1,
                    }
                  )}`
                );
              }}
            >
              <FormattedMessage {...messages.search} />
            </Button>
          </Col>
        </Row>
        <Row style={{ paddingBottom: 16 }}>
          <Tooltip title={<FormattedMessage {...messages.refresh} />}>
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
        </Row>

        <Row>
          <Col span={24}>
            <Table
              rowKey="id"
              loading={loading}
              scroll={{ x: 1024 }}
              columns={columns}
              dataSource={data}
              locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
              bordered
              pagination={{
                pageSize: 20,
                total: totalPage,
                current,
                showTotal: (total, range) => (
                  <FormattedMessage
                    {...messages.totalBook}
                    values={{ total }}
                  />
                ),
              }}
              onChange={this.handleTableChange}
              expandRowByClick
            />
          </Col>
        </Row>
      </>
    );
  }
}

ServiceBookingFeeList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  bookingFeeList: makeSelectServiceBookingFeeList(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "serviceBookingFeeList", reducer });
const withSaga = injectSaga({ key: "serviceBookingFeeList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ServiceBookingFeeList));
