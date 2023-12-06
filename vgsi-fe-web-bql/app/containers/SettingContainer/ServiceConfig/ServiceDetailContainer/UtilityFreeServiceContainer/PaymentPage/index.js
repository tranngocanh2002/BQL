/**
 *
 * PaymentPage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Col, Modal, Row, Table, Tooltip } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import messages from "../../../messages";
import {
  createPayment,
  defaultAction,
  deletePayment,
  fetchAllPayment,
  importPayment,
  updatePayment,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectPaymentPage from "./selectors";

import moment from "moment";
import queryString from "query-string";
import Uploader from "../../../../../../components/Uploader";
import WithRole from "../../../../../../components/WithRole";
import { selectAuthGroup } from "../../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../../utils";
import { GLOBAL_COLOR } from "../../../../../../utils/constants";
import makeSelectUtilityFreeServiceContainer from "../selectors";
import ModalCreate from "./ModalCreate";
import styles from "./index.less";

/* eslint-disable react/prefer-stateless-function */
export class PaymentPage extends React.PureComponent {
  state = {
    current: 1,
    filter: {},
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.reload(this.props.location.search);
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }

    if (
      this.props.paymentPage.creating != nextProps.paymentPage.creating &&
      nextProps.paymentPage.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }

    if (
      this.props.paymentPage.importing != nextProps.paymentPage.importing &&
      nextProps.paymentPage.importingSuccess
    ) {
      this.setState({
        visible: false,
      });
      if (this.state.current == 1) {
        this.reload(this.props.location.search);
      } else {
        this.props.history.push(
          `/main/setting/service/detail/utility-free/payment?${queryString.stringify(
            {
              ...this.state.filter,
              page: 1,
            }
          )}`
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
        `/main/setting/service/detail/utility-free/payment?${queryString.stringify(
          {
            ...this.state.filter,
            page: this.state.current,
          }
        )}`
      );
    });
  };

  _onDelete = (record) => {
    console.log("_onDelete::record", record);
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmDeletePaymentInfo),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
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
    const { paymentPage, dispatch, utilityFreeServiceContainer, auth_group } =
      this.props;
    const { current } = this.state;
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(0, paymentPage.loading ? current - 2 : current - 1) * 20 +
              index +
              1}
          </span>
        ),
      },
      {
        width: 200,
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
            {this.props.intl.formatMessage(messages.description)}
          </span>
        ),
        dataIndex: "description",
        key: "description",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.amountMoney)}
          </span>
        ),
        dataIndex: "price",
        key: "price",
        render: (text) => (
          <span style={{ color: "#1B1B27" }}>{`${formatPrice(text)} `}Đ</span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.status)}
          </span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text, record) => (
          <span>
            {this.props.language === "vi"
              ? (
                  config.STATUS_SERVICE_PAYMENT.find(
                    (ss) => ss.id == record.status
                  ) || {}
                ).name
              : (
                  config.STATUS_SERVICE_PAYMENT.find(
                    (ss) => ss.id == record.status
                  ) || {}
                ).name_en}
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
        width: 170,
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.update)}
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
            {this.props.intl.formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record, index) => (
          <Row type="flex" align="middle" justify="center">
            {record.status == 0 && (
              <>
                <Tooltip title={this.props.intl.formatMessage(messages.edit)}>
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
                    </i>
                  </Row>
                </Tooltip>
                &ensp;&ensp;| &ensp;&ensp;
              </>
            )}
            <Tooltip title={this.props.intl.formatMessage(messages.deleteFee)}>
              <Row
                type="flex"
                align="middle"
                style={{ color: "#F15A29", cursor: "pointer" }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  this._onDelete(record);
                }}
              >
                <i
                  className="material-icons"
                  style={{ fontSize: 18, marginRight: 6 }}
                >
                  delete_outline
                </i>
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    if (
      !auth_group.checkRole([config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT])
    ) {
      columns.splice(columns.length - 1, 1);
    }
    return (
      <Row>
        <WithRole roles={[config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT]}>
          <Col>
            <Row
              type="flex"
              align="middle"
              justify="space-between"
              style={{ marginTop: 20, marginBottom: 20 }}
            >
              <Button
                type="primary"
                ghost
                onClick={() => {
                  this.setState({ visible: true, currentEdit: undefined });
                }}
                disabled={paymentPage.importing}
              >
                {this.props.intl.formatMessage(messages.add)}
              </Button>
              <Uploader
                acceptList={[
                  "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
                  ".xls",
                  ".xlsx",
                ]}
                onUploaded={(url) => {
                  this.props.dispatch(importPayment(url));
                }}
              >
                <Button type="primary" ghost icon="upload">
                  {this.props.intl.formatMessage(messages.import)}
                </Button>
              </Uploader>
            </Row>
          </Col>
        </WithRole>
        <Col>
          <Table
            rowKey="id"
            loading={
              paymentPage.loading ||
              paymentPage.deleting ||
              paymentPage.importing
            }
            columns={columns}
            dataSource={paymentPage.data}
            locale={{
              emptyText: this.props.intl.formatMessage(messages.noData),
            }}
            bordered
            pagination={{
              pageSize: 20,
              total: paymentPage.totalPage,
              current: this.state.current,
              showTotal: (total, range) => `Tổng số ${total} phí`,
            }}
            onChange={this.handleTableChange}
            // onRow={(record, rowIndex) => {
            //   return {
            //     onClick: event => {
            //       this.props.history.push(`/main/setting/service/detail/utility-free/payment/detail/${record.id}`, {
            //         record
            //       })
            //     }
            //   };
            // }}
          />
        </Col>
        <ModalCreate
          visible={this.state.visible}
          setState={this.setState.bind(this)}
          dispatch={dispatch}
          paymentPage={paymentPage}
          addPayment={(payload) => {
            this.props.dispatch(
              createPayment({
                ...payload,
                service_map_management_id: utilityFreeServiceContainer.data.id,
              })
            );
          }}
          updatePayment={(payload) => {
            this.props.dispatch(
              updatePayment({
                ...payload,
                id: this.state.currentEdit.id,
                service_map_management_id: utilityFreeServiceContainer.data.id,
              })
            );
          }}
          currentEdit={this.state.currentEdit}
        />
      </Row>
    );
  }
}

PaymentPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  paymentPage: makeSelectPaymentPage(),
  utilityFreeServiceContainer: makeSelectUtilityFreeServiceContainer(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "paymentPage", reducer });
const withSaga = injectSaga({ key: "paymentPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(PaymentPage));
