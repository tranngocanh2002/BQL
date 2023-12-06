/**
 *
 * InvoiceBillDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Col, Modal, Row, Statistic, Table, Typography } from "antd";
import moment from "moment";
import { injectIntl } from "react-intl";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page";
import PrintButton from "../../../components/PrintButton";
import { formatPrice } from "../../../utils";
import styles from "../FeeList/index.less";
import messages from "../messages";
import ModalEditBill from "./ModalEditBill";
import {
  defaultAction,
  deleteDetailBillAction,
  fetchDetailBillAction,
  updateDetailBillAction,
  updateStatusBillAction,
} from "./actions";
import "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectInvoiceBillDetail from "./selectors";
const { Text } = Typography;
const tableFormItemLayout = {
  xs: {
    span: 24,
    offset: 0,
  },
  sm: {
    span: 20,
    offset: 2,
  },
};

/* eslint-disable react/prefer-stateless-function */
export class InvoiceBillDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      record: (props.location.state || {}).record,
      totalMoney: 0,
      visibleEdit: false,
      billTemplateRef: undefined,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    let { params } = this.props.match;
    this.props.dispatch(fetchDetailBillAction({ id: params.id }));
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.invoiceBillDetail.detail.loading !==
        nextProps.invoiceBillDetail.detail.loading &&
      !nextProps.invoiceBillDetail.detail.loading
    ) {
      this.setState({
        record: nextProps.invoiceBillDetail.detail.data,
        totalMoney:
          nextProps.invoiceBillDetail.detail.data.service_bill_items.reduce(
            (total, fee) => {
              return total + parseInt(fee.price);
            },
            0
          ),
      });
    }
  }

  handlerUpdate = (values) => {
    const { dispatch, form } = this.props;

    dispatch(
      updateDetailBillAction({
        ...values,
        id: this.state.record.id,
        callback: () => {
          this.setState({ visibleEdit: false });
          let { params } = this.props.match;
          this.props.dispatch(fetchDetailBillAction({ id: params.id }));
        },
      })
    );
  };

  handleChangeToConfirm = () => {
    let { params } = this.props.match;
    let { record } = this.state;
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.content1),

      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          updateStatusBillAction({
            id: record.id,
            status: 1,
            callback: () => {
              let { params } = this.props.match;
              this.props.dispatch(fetchDetailBillAction({ id: params.id }));
            },
          })
        );
      },
      onCancel() {},
    });
  };

  handleChangeToBlock = () => {
    let { params } = this.props.match;
    let { record } = this.state;
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.content2),

      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          updateStatusBillAction({
            id: record.id,
            status: 10,
            callback: () => {
              let { params } = this.props.match;
              this.props.dispatch(fetchDetailBillAction({ id: params.id }));
            },
          })
        );
      },
      onCancel() {},
    });
  };

  handleDelete = (record) => {
    let { params } = this.props.match;
    Modal.confirm({
      autoFocusButton: null,
      title: "Bạn chắc chắn muốn phiếu thu này?",
      okText: "Đồng ý",
      okType: "danger",
      cancelText: "Huỷ",
      onOk: () => {
        this.props.dispatch(
          deleteDetailBillAction({
            id: record.id,
          })
        );
      },
      onCancel() {},
    });
  };

  handlePrint = () => {};

  render() {
    const { invoiceBillDetail } = this.props;
    const { detail, updating } = invoiceBillDetail;
    const { record, totalMoney, visibleEdit } = this.state;
    let { formatMessage } = this.props.intl;

    if (!record) {
      return <Page loading={true} inner />;
    }
    let statusStyle = "luci-status-warning";
    if (!!record && record.status == 10) {
      statusStyle = "luci-status-primary";
    } else if (!!record && record.status == 1) {
      statusStyle = "luci-status-success";
    }
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => <span>{index + 1}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.description)}
          </span>
        ),
        dataIndex: "description",
        key: "description",
        render: (text, record) => (
          <p style={{ whiteSpace: "pre-line" }}>{record.description}</p>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.service)}
          </span>
        ),
        dataIndex: "service_map_management_name",
        key: "service_map_management_name",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.month)}
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
            {formatMessage(messages.amountOfMoney)}
          </span>
        ),
        dataIndex: "price",
        key: "price",
        render: (text, record) => <span>{formatPrice(record.price)}</span>,
        align: "right",
      },
    ];

    return (
      <Page loading={detail.loading && !updating} inner>
        <div className="invoiceBillDetailPage">
          <Row className="block">
            <Col className="rowInfoTitle">
              <Row type="flex" justify="space-between" align="middle">
                <Col>
                  <span
                    style={{
                      color: "#1B1B27",
                      fontWeight: "bold",
                      fontSize: 14,
                    }}
                  >
                    {formatMessage(messages.receipts)}
                  </span>
                </Col>
                <Col>
                  {!!record && (record.status == 1 || record.status == 10) && (
                    <PrintButton
                      ghost
                      type="primary"
                      icon="printer"
                      style={{ marginRight: 10 }}
                      fetchContentToPrint={() => {
                        return window.connection.fetchToPrintBilling({
                          id: record.id,
                        });
                      }}
                    >
                      {formatMessage(messages.print)}
                    </PrintButton>
                  )}
                  {!!record && record.status == 0 && (
                    <Button
                      ghost
                      type="danger"
                      icon="carry-out"
                      style={{ marginRight: 10 }}
                      onClick={this.handleChangeToConfirm}
                    >
                      {formatMessage(messages.paymentConfirmation)}
                    </Button>
                  )}
                  {!!record && record.status == 1 && (
                    <Button
                      ghost
                      type="danger"
                      icon="carry-out"
                      style={{ marginRight: 10 }}
                      onClick={this.handleChangeToBlock}
                    >
                      {formatMessage(messages.closingEntry)}
                    </Button>
                  )}
                  {!!record && record.status != 10 && (
                    <Button
                      ghost
                      type="primary"
                      icon="edit"
                      style={{ marginRight: 10 }}
                      onClick={() => {
                        this.setState({ visibleEdit: true });
                      }}
                    >
                      {formatMessage(messages.edit)}
                    </Button>
                  )}
                  {/*{ record.status != 10 &&*/}
                  {/*<Button type='danger' icon="delete" style={{ marginRight: 10 }} onClick={this.handleDelete}>Xóa</Button>*/}
                  {/*}*/}
                </Col>
              </Row>
            </Col>
            <ModalEditBill
              formatMessage={formatMessage}
              setState={this.setState.bind(this)}
              updating={updating}
              visibleEdit={visibleEdit}
              recordBill={record}
              handlerUpdate={this.handlerUpdate}
            />
            <Col className="separator" />
            <Row className="rowInfo" gutter={24}>
              <Col lg={12} md={12} sm={24} xs={24}>
                <Row type="flex" align="middle" className="rowmargin">
                  <Col span={11} style={{ textAlign: "right" }}>
                    {formatMessage(messages.property)}:
                  </Col>
                  <Col span={12} offset={1}>
                    {!!record && record.apartment_name} - (
                    {!!record && record.apartment_parent_path})
                  </Col>
                </Row>
              </Col>
              <Col lg={12} md={12} sm={24} xs={24}>
                <Row type="flex" align="middle" className="rowmargin">
                  <Col span={11} style={{ textAlign: "right" }}>
                    {formatMessage(messages.formCode)}:
                  </Col>
                  <Col span={12} offset={1}>
                    {!!record && record.code}
                  </Col>
                </Row>
              </Col>
              <Col lg={12} md={12} sm={24} xs={24}>
                <Row type="flex" align="middle" className="rowmargin">
                  <Col span={11} style={{ textAlign: "right" }}>
                    {formatMessage(messages.owner)}:
                  </Col>
                  <Col span={12} offset={1}>
                    {!!record && record.resident_user_name}
                  </Col>
                </Row>
              </Col>
              <Col lg={12} md={12} sm={24} xs={24}>
                <Row type="flex" align="middle" className="rowmargin">
                  <Col span={11} style={{ textAlign: "right" }}>
                    {formatMessage(messages.voterReview)}:
                  </Col>
                  <Col span={12} offset={1}>
                    {!!record && record.management_user_name}
                  </Col>
                </Row>
              </Col>
              <Col lg={12} md={12} sm={24} xs={24}>
                <Row type="flex" align="middle" className="rowmargin">
                  <Col span={11} style={{ textAlign: "right" }}>
                    {formatMessage(messages.feePayer)}:
                  </Col>
                  <Col span={12} offset={1}>
                    {!!record && record.payer_name}
                  </Col>
                </Row>
              </Col>
              <Col lg={12} md={12} sm={24} xs={24}>
                <Row type="flex" align="middle" className="rowmargin">
                  <Col span={11} style={{ textAlign: "right" }}>
                    {formatMessage(messages.closingTime)}:
                  </Col>
                  <Col span={12} offset={1}>
                    {moment
                      .unix(!!record && record.created_at)
                      .format("DD/MM/YYYY - HH:mm")}
                  </Col>
                </Row>
              </Col>
              <Col lg={12} md={12} sm={24} xs={24}>
                <Row type="flex" align="middle" className="rowmargin">
                  <Col span={11} style={{ textAlign: "right" }}>
                    {formatMessage(messages.form)}:
                  </Col>
                  <Col span={12} offset={1}>
                    {!!record && record.type_payment_name}
                  </Col>
                </Row>
              </Col>
              <Col lg={12} md={12} sm={24} xs={24}>
                <Row type="flex" align="middle" className="rowmargin">
                  <Col span={11} style={{ textAlign: "right" }}>
                    {formatMessage(messages.status)}:
                  </Col>
                  <Col span={12} offset={1}>
                    <Text className={statusStyle}>
                      {!!record && record.status_name}
                    </Text>
                  </Col>
                </Row>
              </Col>
            </Row>
            <Col className="separator" />
            <Row className="rowInfo" gutter={24}>
              <Col {...tableFormItemLayout}>
                <Table
                  rowKey="id"
                  loading={detail.loading}
                  columns={columns}
                  dataSource={!!record && record.service_bill_items}
                  locale={{ emptyText: formatMessage(messages.emptyData) }}
                  pagination={false}
                  bordered
                />
              </Col>
              <Col
                {...tableFormItemLayout}
                style={{ textAlign: "right", marginTop: 10 }}
              >
                <Statistic
                  title={formatMessage(messages.totalAmount)}
                  groupSeparator="."
                  value={totalMoney}
                  suffix=" đ"
                />
              </Col>
            </Row>
          </Row>
        </div>
      </Page>
    );
  }
}

InvoiceBillDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  invoiceBillDetail: makeSelectInvoiceBillDetail(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "invoiceBillDetail", reducer });
const withSaga = injectSaga({ key: "invoiceBillDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(InvoiceBillDetail));
