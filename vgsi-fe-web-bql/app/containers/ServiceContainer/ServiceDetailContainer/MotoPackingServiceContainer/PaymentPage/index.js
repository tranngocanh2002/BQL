/**
 *
 * PaymentMotoPackingPage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { DatePicker, Modal, Row, Select, Spin, Table } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import messages from "../messages";
import {
  createPayment,
  defaultAction,
  deletePayment,
  fetchAllPayment,
  fetchApartment,
  updatePayment,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectPaymentMotoPackingPage from "./selectors";

import _ from "lodash";
import moment from "moment";
import queryString from "query-string";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../utils";
import { GLOBAL_COLOR } from "../../../../../utils/constants";
import makeSelectMotoPackingServiceContainer from "../selectors";
import ModalCreate from "./ModalCreate";
import styles from "./index.less";

/* eslint-disable react/prefer-stateless-function */
export class PaymentMotoPackingPage extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {},
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
      this.props.paymentMotoPackingPage.creating !=
        nextProps.paymentMotoPackingPage.creating &&
      nextProps.paymentMotoPackingPage.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }

    if (
      this.props.paymentMotoPackingPage.importing !=
        nextProps.paymentMotoPackingPage.importing &&
      nextProps.paymentMotoPackingPage.importingSuccess
    ) {
      this.setState({
        visible: false,
      });
      if (this.state.current == 1) {
        this.reload(this.props.location.search);
      } else {
        this.props.history.push(
          `/main/service/detail/moto-packing/payment?${queryString.stringify({
            ...this.state.filter,
            page: 1,
          })}`
        );
      }
    }
  }

  reload = (search) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }

    params.keyword = params.keyword || "";

    this.setState(
      { current: params.page, keyword: params.keyword, filter: params },
      () => {
        this.props.dispatch(fetchAllPayment(params));
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/service/detail/moto-packing/payment?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onDelete = (record) => {
    console.log("_onDelete::record", record);
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage({
        ...messages.confirmDeletePayment,
      }),
      okText: this.props.intl.formatMessage({ ...messages.agree }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({ ...messages.cancel }),
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
    const {
      paymentMotoPackingPage,
      dispatch,
      motoPackingServiceContainer,
      auth_group,
      intl,
    } = this.props;
    const { current } = this.state;
    const plhMonth = intl.formatMessage({ ...messages.selectMonth });
    const columns = [
      {
        width: 50,
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              paymentMotoPackingPage.loading ? current - 2 : current - 1
            ) *
              20 +
              index +
              1}
          </span>
        ),
      },
      {
        width: 200,
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
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.amountMoney} />
          </span>
        ),
        dataIndex: "price",
        key: "price",
        render: (text) => (
          <span style={{ color: "#1B1B27", fontWeight: "bold" }}>
            {`${formatPrice(text)} `}Đ
          </span>
        ),
      },
      {
        width: 180,
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.condition} />
          </span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text, record) => {
          if (!!record.service_bills && record.service_bills.length > 0) {
            return (
              <span style={{ textAlign: "right" }}>
                <FormattedMessage {...messages.enteredBill} />:{" "}
                {record.service_bills.map((service_bill_id, index) => {
                  return (
                    <>
                      <span
                        style={{
                          cursor: "pointer",
                          fontWeight: "bold",
                          color: GLOBAL_COLOR,
                        }}
                        onClick={() => {
                          this.props.history.push(
                            `/main/finance/bills/detail/${service_bill_id.id}`
                          );
                        }}
                      >
                        {service_bill_id.number}
                      </span>
                      <br />
                    </>
                  );
                })}
              </span>
            );
          }
          return (
            <span className="luci-status-warning">
              {
                (
                  config.STATUS_SERVICE_PAYMENT.find(
                    (ss) => ss.id == record.status
                  ) || {}
                ).name
              }
            </span>
          );
        },
      },
      {
        width: 170,
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.update} />
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text, record, index) => (
          <span>
            {moment.unix(record.updated_at).format("DD/MM/YYYY - HH:mm")}
          </span>
        ),
      },
      {
        width: 120,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.action} />
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record, index) => {
          if (record.service_bill_code) return null;
          return (
            <Row type="flex" align="middle" justify="center">
              {record.status == 0 && (
                <>
                  {/* <Tooltip title="Chỉnh sửa"> */}
                  <Row
                    type="flex"
                    align="middle"
                    style={{ color: GLOBAL_COLOR, cursor: "pointer" }}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      this._onEdit(record);
                    }}
                  >
                    <i
                      className="material-icons"
                      style={{ fontSize: 18, marginRight: 6 }}
                    >
                      edit
                    </i>{" "}
                    <FormattedMessage {...messages.edit} />
                  </Row>
                  {/* </Tooltip>&ensp;&ensp;|
                            &ensp;&ensp; */}
                </>
              )}
              {/* <Tooltip title="Xoá phí">
              <Row type='flex' align='middle' style={{ color: '#F15A29', cursor: 'pointer' }}
                onClick={e => {
                  e.preventDefault();
                  e.stopPropagation();
                  this._onDelete(record);
                }}
              >
                <i className="material-icons" style={{ fontSize: 18, marginRight: 6 }} >
                  delete_outline
          </i>
              </Row>
            </Tooltip> */}
            </Row>
          );
        },
      },
    ];
    const { filter } = this.state;

    if (!auth_group.checkRole([config.ALL_ROLE_NAME.SERVICE_MANAGEMENT])) {
      columns.splice(columns.length - 1, 1);
    }
    return (
      <Row>
        <Row type="flex" align="middle" justify="space-between">
          <Row
            type="flex"
            align="middle"
            style={{ marginTop: 16, marginBottom: 16 }}
          >
            <span>
              <FormattedMessage {...messages.property} />:
            </span>
            <Select
              style={{ minWidth: 200, marginLeft: 4, marginRight: 20 }}
              loading={paymentMotoPackingPage.apartment.loading}
              showSearch
              placeholder={<FormattedMessage {...messages.selectProperty} />}
              optionFilterProp="children"
              notFoundContent={
                paymentMotoPackingPage.apartment.loading ? (
                  <Spin size="small" />
                ) : null
              }
              onSearch={this._onSearch}
              value={this.state.filter.apartment_id}
              allowClear
              onChange={(value) => {
                this.setState(
                  {
                    filter: {
                      ...this.state.filter,
                      apartment_id: value,
                    },
                  },
                  () => {
                    this.props.history.push(
                      `/main/service/detail/moto-packing/payment?${queryString.stringify(
                        {
                          ...this.state.filter,
                          page: 1,
                        }
                      )}`
                    );
                  }
                );
              }}
            >
              {paymentMotoPackingPage.apartment.items.map((gr) => {
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
            <span>
              <FormattedMessage {...messages.month} />:
            </span>
            <DatePicker.MonthPicker
              style={{ marginLeft: 4 }}
              value={
                this.state.filter.from_month
                  ? moment.unix(this.state.filter.from_month)
                  : undefined
              }
              onChange={(date) => {
                this.setState(
                  {
                    filter: {
                      ...this.state.filter,
                      from_month: date
                        ? moment(date).startOf("month").unix()
                        : undefined,
                      to_month: date
                        ? moment(date).endOf("month").unix()
                        : undefined,
                    },
                  },
                  () => {
                    this.props.history.push(
                      `/main/service/detail/moto-packing/payment?${queryString.stringify(
                        {
                          ...this.state.filter,
                          page: 1,
                        }
                      )}`
                    );
                  }
                );
              }}
              format="MM/YYYY"
              placeholder={plhMonth}
            />
          </Row>
        </Row>

        <Table
          rowKey="id"
          loading={
            paymentMotoPackingPage.loading ||
            paymentMotoPackingPage.deleting ||
            paymentMotoPackingPage.importing
          }
          columns={columns}
          dataSource={paymentMotoPackingPage.data}
          locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
          pagination={{
            pageSize: 20,
            total: paymentMotoPackingPage.totalPage,
            current: this.state.current,
            showTotal: (total, range) => (
              <FormattedMessage {...messages.totalFee} values={{ total }} />
            ),
          }}
          expandRowByClick
          expandedRowRender={(record) => (
            <span style={{ whiteSpace: "pre-wrap" }}>{record.description}</span>
          )}
          onChange={this.handleTableChange}
          bordered
        />
        <ModalCreate
          visible={this.state.visible}
          setState={this.setState.bind(this)}
          dispatch={dispatch}
          paymentMotoPackingPage={paymentMotoPackingPage}
          addPayment={(payload) => {
            this.props.dispatch(
              createPayment({
                ...payload,
                service_map_management_id: motoPackingServiceContainer.data.id,
              })
            );
          }}
          updatePayment={(payload) => {
            this.props.dispatch(
              updatePayment({
                ...payload,
                id: this.state.currentEdit.id,
                service_map_management_id: motoPackingServiceContainer.data.id,
              })
            );
          }}
          currentEdit={this.state.currentEdit}
        />
      </Row>
    );
  }
}

PaymentMotoPackingPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  paymentMotoPackingPage: makeSelectPaymentMotoPackingPage(),
  motoPackingServiceContainer: makeSelectMotoPackingServiceContainer(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "paymentMotoPackingPage", reducer });
const withSaga = injectSaga({ key: "paymentMotoPackingPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(PaymentMotoPackingPage));
