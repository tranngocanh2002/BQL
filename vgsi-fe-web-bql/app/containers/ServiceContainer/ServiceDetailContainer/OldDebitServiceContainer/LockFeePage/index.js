/**
 *
 * LockFeePage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

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
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import { defaultAction, fetchAllPayment, fetchApartment } from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectLockFeePage from "./selectors";

import _ from "lodash";
import moment from "moment";
import queryString from "query-string";
import { injectIntl } from "react-intl";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { formatPrice } from "../../../../../utils";
import messages from "../messages";
import styles from "./index.less";

const { MonthPicker } = DatePicker;

/* eslint-disable react/prefer-stateless-function */
export class LockFeePage extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {
        sort: "-updated_at",
      },
    };
    this._onSearch = _.debounce(this.onSearch, 500);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this._onSearch("");
    this.reload(this.props.location.search);
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
      if (params.page) {
        params.page = parseInt(params.page);
      } else {
        params.page = 1;
      }
      if (!params.sort) {
        params.sort = "-updated_at";
      }
    } catch (error) {
      params.page = 1;
    }

    params.keyword = params.keyword || "";

    this.setState(
      {
        current: params.page,
        keyword: params.keyword,
        filter: reset ? {} : params,
      },
      () => {
        this.props.dispatch(
          fetchAllPayment(reset ? { page: 1, sort: "-updated_at" } : params)
        );
        reset &&
          this.props.history.push(
            `${this.props.location.pathname}?${queryString.stringify({
              sort: "-updated_at",
              page: 1,
            })}`
          );
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/service/detail/old_debit/lock?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  render() {
    const { lockFeePage } = this.props;
    const { current, filter } = this.state;
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        width: 25,
        fixed: "left",
        render: (text, record, index) => (
          <span>
            {Math.max(0, lockFeePage.loading ? current - 2 : current - 1) * 20 +
              index +
              1}
          </span>
        ),
      },
      {
        width: 220,
        fixed: "left",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.property)}
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
            {this.props.intl.formatMessage(messages.month)}
          </span>
        ),
        dataIndex: "fee_of_month",
        key: "fee_of_month",
        render: (text, record) => (
          <span>{moment.unix(record.fee_of_month).format("MM/YYYY")}</span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.amountMoney)}
          </span>
        ),
        dataIndex: "total_money",
        key: "total_money",
        render: (text) => (
          <span style={{ color: "#1B1B27" }}>{`${formatPrice(text)} `}đ</span>
        ),
      },
      {
        width: 200,
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.status)}
          </span>
        ),
        dataIndex: "is_paid",
        key: "is_paid",
        render: (text, record) => {
          if (record.is_paid)
            return (
              <span className="luci-status-success">
                {this.props.intl.formatMessage(messages.paid)}
              </span>
            );

          return (
            <span className="luci-status-warning">
              {this.props.intl.formatMessage(messages.unpaid)}
            </span>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.approveDate)}
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text, record) => (
          <span>
            {moment.unix(record.updated_at).format("DD/MM/YYYY - HH:mm")}
          </span>
        ),
      },
    ];

    return (
      <>
        <Row
          gutter={[24, 16]}
          style={{ marginBottom: 16 }}
          type="flex"
          align="middle"
        >
          <Col span={8}>
            <Select
              style={{ width: "100%" }}
              loading={lockFeePage.apartment.loading}
              showSearch
              placeholder={this.props.intl.formatMessage(
                messages.selectProperty
              )}
              optionFilterProp="children"
              notFoundContent={
                lockFeePage.apartment.loading ? <Spin size="small" /> : null
              }
              onSearch={this._onSearch}
              value={filter.apartment_id}
              allowClear
              onChange={(value, opt) => {
                this.setState({
                  filter: {
                    ...filter,
                    apartment_id: value,
                  },
                });
                if (!opt) {
                  this._onSearch("");
                }
              }}
            >
              {lockFeePage.apartment.items.map((gr) => {
                return (
                  <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>{`${
                    gr.name
                  } (${gr.parent_path})${
                    gr.status == 0
                      ? ` - ${this.props.intl.formatMessage(messages.empty)}`
                      : ""
                  }`}</Select.Option>
                );
              })}
            </Select>
          </Col>
          <Col span={8}>
            <MonthPicker
              style={{ width: "100%" }}
              value={
                filter.from_month ? moment.unix(filter.from_month) : undefined
              }
              onChange={(date) => {
                this.setState({
                  filter: {
                    ...filter,
                    from_month: date
                      ? moment(date).startOf("month").unix()
                      : undefined,
                    to_month: date
                      ? moment(date).endOf("month").unix()
                      : undefined,
                  },
                });
              }}
              format="MM/YYYY"
              placeholder={this.props.intl.formatMessage(messages.selectMonth)}
            />
          </Col>
          <Col>
            <Button
              type="primary"
              onClick={() => {
                this.props.history.push(
                  `/main/service/detail/old_debit/lock?${queryString.stringify({
                    ...this.state.filter,
                    page: 1,
                  })}`
                );
              }}
            >
              {this.props.intl.formatMessage(messages.search)}
            </Button>
          </Col>
        </Row>

        <Row style={{ paddingBottom: 16 }}>
          <Tooltip title={this.props.intl.formatMessage(messages.refreshPage)}>
            <Button
              shape="circle-outline"
              style={{ padding: 0, marginRight: 10 }}
              onClick={() => {
                this.reload(this.props.location.search, true);
              }}
              icon="reload"
              size="large"
            />
          </Tooltip>
        </Row>
        <Table
          rowKey="id"
          loading={lockFeePage.loading}
          columns={columns}
          dataSource={lockFeePage.data}
          locale={{ emptyText: this.props.intl.formatMessage(messages.noData) }}
          bordered
          expandedRowRender={(record) => (
            <span style={{ whiteSpace: "pre-wrap" }}>{record.description}</span>
          )}
          pagination={{
            pageSize: 20,
            total: lockFeePage.totalPage,
            current: this.state.current,
            showTotal: (total) => {
              this.props.intl.formatMessage(messages.totalFee, { total });
            },
          }}
          expandRowByClick
          onChange={this.handleTableChange}
          scroll={{ x: 1000 }}
        />
      </>
    );
  }
}

LockFeePage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  lockFeePage: makeSelectLockFeePage(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "lockFeePage", reducer });
const withSaga = injectSaga({ key: "lockFeePage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(LockFeePage));
