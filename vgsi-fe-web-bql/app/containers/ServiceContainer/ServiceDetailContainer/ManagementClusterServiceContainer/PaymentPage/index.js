/**
 *
 * PaymentManagementClusterPage
 *
 */

import _ from "lodash";
import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Col,
  DatePicker,
  Modal,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import {
  defaultAction,
  deletePayment,
  fetchAllPayment,
  fetchApartment,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectPaymentManagementClusterPage from "./selectors";

import moment from "moment";
import queryString from "query-string";
import { FormattedMessage, injectIntl } from "react-intl";
import { formatPrice } from "../../../../../utils";
import messages from "../messages";
import makeSelectManagementClusterServiceContainer from "../selectors";
import styles from "./index.less";

/* eslint-disable react/prefer-stateless-function */
export class PaymentManagementClusterPage extends React.PureComponent {
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

    if (
      this.props.paymentManagementClusterPage.creating !=
        nextProps.paymentManagementClusterPage.creating &&
      nextProps.paymentManagementClusterPage.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }

    if (
      this.props.paymentManagementClusterPage.importing !=
        nextProps.paymentManagementClusterPage.importing &&
      nextProps.paymentManagementClusterPage.importingSuccess
    ) {
      this.setState({
        visible: false,
      });
      if (this.state.current == 1) {
        this.reload(this.props.location.search);
      } else {
        this.props.history.push(
          `/main/service/detail/apartment-fee/payment?${queryString.stringify({
            ...this.state.filter,
            page: 1,
          })}`
        );
      }
    }
  }

  reload = (search, reset) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
      if (!params.sort) params.sort = "-updated_at";
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
        `/main/service/detail/apartment-fee/payment?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage({
        ...messages.confirmDeletePayment,
      }),
      okText: this.props.intl.formatMessage({ ...messages.okText }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({ ...messages.cancelText }),
      onOk: () => {
        this.props.dispatch(
          deletePayment({
            id: record.id,
            callback: () => {
              this.reload(this.props.location.search);
            },
          })
        );
      },
      onCancel() {},
    });
  };
  _onEdit = (record) => {
    this.setState(
      {
        currentEdit: record,
      },
      () => {
        this.setState({ visible: true });
      }
    );
  };

  render() {
    const { paymentManagementClusterPage, intl } = this.props;
    const { current } = this.state;
    const plhDate = intl.formatMessage({ ...messages.selectMonth });
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        // width: 100,
        // fixed: "left",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              paymentManagementClusterPage.loading ? current - 2 : current - 1
            ) *
              20 +
              index +
              1}
          </span>
        ),
      },
      {
        // width: 200,
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
      // {
      //   title: <span className={styles.nameTable} >Mô tả</span>, dataIndex: 'description', key: 'description',
      //   render: (text) => <span style={{ whiteSpace: 'pre-wrap' }} >{text}</span>
      // },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.cash} />
          </span>
        ),
        dataIndex: "total_money",
        key: "total_money",
        render: (text) => (
          <span style={{ color: "#1B1B27" }}>{`${formatPrice(text)} đ`}</span>
        ),
      },
      {
        // width: 180,
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.status} />
          </span>
        ),
        dataIndex: "is_paid",
        key: "is_paid",
        render: (text, record) => {
          if (record.is_paid)
            return (
              <span className="luci-status-success">
                <FormattedMessage {...messages.paid} />
              </span>
            );

          return (
            <span className="luci-status-warning">
              <FormattedMessage {...messages.unpaid} />
            </span>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.month} />
          </span>
        ),
        dataIndex: "fee_of_month",
        key: "fee_of_month",
        render: (text, record) => (
          <span>{moment.unix(record.fee_of_month).format("MM/YYYY")}</span>
        ),
      },
      {
        // width: 170,
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.update} />
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
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.approvedBy} />
          </span>
        ),
        dataIndex: "updated_name",
        key: "updated_name",
        // render: (text, record, index) => <span >{moment.unix(record.updated_at).format('DD/MM/YYYY - HH:mm')}</span>
      },
    ];

    return (
      <Row>
        <Row gutter={[24, 16]} style={{ marginBottom: 16 }}>
          <Col span={8}>
            <Select
              style={{ width: "100%" }}
              loading={paymentManagementClusterPage.apartment.loading}
              showSearch
              placeholder={<FormattedMessage {...messages.plhProperty} />}
              optionFilterProp="children"
              notFoundContent={
                paymentManagementClusterPage.apartment.loading ? (
                  <Spin size="small" />
                ) : null
              }
              onSearch={this._onSearch}
              value={this.state.filter.apartment_id}
              allowClear
              onChange={(value, opt) => {
                this.setState({
                  filter: {
                    ...this.state.filter,
                    apartment_id: value,
                  },
                });
                if (!opt) {
                  this._onSearch("");
                }
              }}
            >
              {paymentManagementClusterPage.apartment.items.map((gr) => {
                return (
                  <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>{`${
                    gr.name
                  } (${gr.parent_path})${
                    gr.status == 0
                      ? ` - ${intl.formatMessage({ ...messages.empty })}`
                      : ""
                  }`}</Select.Option>
                );
              })}
            </Select>
          </Col>
          <Col span={8}>
            <DatePicker.MonthPicker
              style={{ width: "100%" }}
              value={
                this.state.filter.from_month
                  ? moment.unix(this.state.filter.from_month)
                  : undefined
              }
              onChange={(date) => {
                this.setState({
                  filter: {
                    ...this.state.filter,
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
              placeholder={plhDate}
            />
          </Col>
          <Col span={5}>
            <Button
              type="primary"
              onClick={() => {
                this.props.history.push(
                  `/main/service/detail/apartment-fee/payment?${queryString.stringify(
                    {
                      ...this.state.filter,
                      page: 1,
                    }
                  )}`
                );
              }}
            >
              {<FormattedMessage {...messages.search} />}
            </Button>
          </Col>
        </Row>
        <Row style={{ paddingBottom: 16 }}>
          <Tooltip title={<FormattedMessage {...messages.refreshPage} />}>
            <Button
              shape="circle-outline"
              style={{ padding: 0, marginRight: 10 }}
              onClick={(e) => {
                this.reload(this.props.location.search, true);
              }}
              icon="reload"
              size="large"
            ></Button>
          </Tooltip>
        </Row>
        <Table
          rowKey="id"
          loading={
            paymentManagementClusterPage.loading ||
            paymentManagementClusterPage.deleting ||
            paymentManagementClusterPage.importing
          }
          columns={columns}
          dataSource={paymentManagementClusterPage.data}
          locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
          bordered
          expandRowByClick
          expandedRowRender={(record) => (
            <span style={{ whiteSpace: "pre-wrap" }}>{record.description}</span>
          )}
          pagination={{
            pageSize: 20,
            total: paymentManagementClusterPage.totalPage,
            current: this.state.current,
            showTotal: (total, range) => (
              <FormattedMessage {...messages.totalFee} values={{ total }} />
            ),
          }}
          onChange={this.handleTableChange}
          scroll={{ x: 1366 }}
          // onRow={(record, rowIndex) => {
          //   return {
          //     onClick: event => {
          //       this.props.history.push(`/main/service/detail/apartment-fee/payment/detail/${record.id}`, {
          //         record
          //       })
          //     }
          //   };
          // }}
        />
        {/* <ModalCreate visible={this.state.visible} setState={this.setState.bind(this)}
          dispatch={dispatch}
          paymentManagementClusterPage={paymentManagementClusterPage}
          addPayment={payload => {
            this.props.dispatch(createPayment({ ...payload, service_map_management_id: managementClusterServiceContainer.data.id }))
          }}
          updatePayment={payload => {
            this.props.dispatch(updatePayment({
              ...payload,
              id: this.state.currentEdit.id,
              service_map_management_id: managementClusterServiceContainer.data.id
            }))
          }}
          currentEdit={this.state.currentEdit}
        /> */}
      </Row>
    );
  }
}

PaymentManagementClusterPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  paymentManagementClusterPage: makeSelectPaymentManagementClusterPage(),
  managementClusterServiceContainer:
    makeSelectManagementClusterServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "paymentManagementClusterPage",
  reducer,
});
const withSaga = injectSaga({ key: "paymentManagementClusterPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(PaymentManagementClusterPage));
