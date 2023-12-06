/**
 *
 * FeeList
 *
 */

import { Col, Icon, List, Modal, Row, Table, Tooltip, Typography } from "antd";
import moment from "moment";
import PropTypes from "prop-types";
import queryString from "query-string";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { withRouter } from "react-router";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../../components/Page";
import { config, formatPrice } from "../../../../utils";
import { GLOBAL_COLOR } from "../../../../utils/constants";
import ModalEdit from "./ModalEdit";
import {
  defaultAction,
  deleteFeeAction,
  fetchAllFee,
  fetchServiceMapAction,
  updatePayment,
} from "./actions";
import styles from "./index.less";
import messages, { scope } from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectFeeList from "./selectors";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { selectAuthGroup } from "redux/selectors";
const { Text } = Typography;
const confirm = Modal.confirm;

/* eslint-disable react/prefer-stateless-function */
export class FeeList extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {
        apartment_id: props.match.params.id,
      },
      collapse: false,
      currentEdit: undefined,
      visible: false,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.reload(this.props.search);
    this.props.dispatch(fetchServiceMapAction());
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps;
    if (this.props.search != search) {
      this.reload(search);
    }
    if (
      this.props.feeList.updating != nextProps.feeList.updating &&
      nextProps.feeList.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.search);
    }
  }

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/apartment/detail/${
            this.props.match.params.id
          }?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  reload = (search) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }

    params.keyword = params.keyword || "";

    this.setState({ current: params.page, keyword: params.keyword }, () => {
      this.props.dispatch(
        fetchAllFee({
          ...params,
          apartment_id: this.props.match.params.id,
        })
      );
    });
  };

  _onDelete = (record) => {
    if (record.status > 0) {
      Modal.info({
        title: this.props.intl.formatMessage({
          id: `${scope}.modalDeleteInfo`,
        }),
        onOk() {},
      });
      return;
    }
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage({
        id: `${scope}.modalDeleteConfirm`,
      }),
      okText: this.props.intl.formatMessage({
        id: `${scope}.confirm`,
      }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({
        id: `${scope}.cancel`,
      }),
      onOk: () => {
        this.props.dispatch(
          deleteFeeAction({
            id: record.id,
            callback: () => {
              this.reload(this.props.search);
            },
          })
        );
      },
      onCancel() {},
    });
  };

  _onEdit = (record) => {
    if (record.status > 0) {
      Modal.info({
        title: this.props.intl.formatMessage({
          id: `${scope}.modalEditInfo`,
        }),
        onOk() {},
      });
      return;
    }
    this.setState(
      {
        currentEdit: record,
      },
      () => {
        this.setState({ visible: true });
      }
    );
  };

  togglerContent = () => {
    const { collapse } = this.state;
    this.setState({ collapse: !collapse });
  };

  render() {
    const { feeList, dispatch, auth_group } = this.props;
    const { loading, items, updating } = feeList;
    const { current } = this.state;
    const columns = [
      {
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
            <FormattedMessage {...messages.service} />
          </span>
        ),
        dataIndex: "service_map_management_service_name",
        key: "service_map_management_service_name",
        render: (text, record) => (
          <span>
            {this.props.language === "en"
              ? record.service_map_management_service_name_en
              : record.service_map_management_service_name}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.price} />
          </span>
        ),
        dataIndex: "price",
        key: "price",
        // align: "right",
        render: (text, record) => (
          <span>{`${formatPrice(record.price)} đ`}</span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.status} />
          </span>
        ),
        dataIndex: "status",
        key: "status",
        // align: "center",
        render: (text, record) => {
          if (record.status == 0) {
            if (!!record.service_bills && record.service_bills.length > 0) {
              return (
                <span style={{ textAlign: "right" }}>
                  <FormattedMessage {...messages.daVaoDon} />{" "}
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
              <Text className={"luci-status-warning"}>
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
              </Text>
            );
          } else {
            return (
              <Text className={"luci-status-success"}>
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
              </Text>
            );
          }
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
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.dayExpired} />
          </span>
        ),
        dataIndex: "day_expired",
        key: "day_expired",
        // align: "right",
        render: (text, record) => (
          <span>
            {moment.unix(record.day_expired).format("DD/MM/YYYY - HH:mm")}
          </span>
        ),
      },
      {
        // align: "center",
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
            // <Row type="flex" align="middle" justify="center">
            //   <Row
            //     type="flex"
            //     align="middle"
            //     style={{ color: "#009b71", cursor: "pointer" }}
            //     onClick={(e) => {
            //       e.preventDefault();
            //       e.stopPropagation();
            //       this._onEdit(record);
            //     }}
            //   >
            //     <i
            //       className="material-icons"
            //       style={{ fontSize: 18, marginRight: 6 }}
            //     >
            //       edit
            //     </i>
            //   </Row>
            //   |
            //   <Row
            //     type="flex"
            //     align="middle"
            //     style={{ color: "#F15A29", marginLeft: 10, cursor: "pointer" }}
            //     onClick={(e) => {
            //       e.preventDefault();
            //       e.stopPropagation();
            //       this._onDelete(record);
            //     }}
            //   >
            //     <i
            //       className="material-icons"
            //       style={{ fontSize: 18, marginRight: 6 }}
            //     >
            //       delete_outline
            //     </i>
            //   </Row>
            // </Row>
            <Row type="flex" justify="space-between" align="middle">
              {auth_group.checkRole([
                config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
              ]) ? (
                <Tooltip title={<FormattedMessage {...messages.edit} />}>
                  <Icon
                    className={"iconAction"}
                    type="edit"
                    onClick={(e) => {
                      e.stopPropagation();
                      e.preventDefault();
                      this._onEdit(record);
                    }}
                  />
                </Tooltip>
              ) : (
                <Icon
                  style={{ color: "rgba(0, 0, 0, 0.25)" }}
                  type="edit"
                  disabled
                />
              )}
              |
              <Tooltip title={<FormattedMessage {...messages.delete} />}>
                {auth_group.checkRole([
                  config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
                ]) ? (
                  <Icon
                    className={"iconAction"}
                    style={{ color: "red" }}
                    type="delete"
                    onClick={(e) => {
                      e.stopPropagation();
                      e.preventDefault();
                      this._onDelete(record);
                    }}
                  />
                ) : (
                  <Icon
                    style={{ color: "rgba(0, 0, 0, 0.25)" }}
                    type="delete"
                    disabled
                  />
                )}
              </Tooltip>
            </Row>
          );
        },
      },
    ];
    return (
      <Page className="feeListPage" inner loading={loading}>
        <div>
          <ModalEdit
            visible={this.state.visible}
            setState={this.setState.bind(this)}
            updating={updating}
            dispatch={dispatch}
            updatePayment={(payload) => {
              this.props.dispatch(
                updatePayment({
                  ...payload,
                  id: this.state.currentEdit.id,
                  service_map_management_id:
                    this.state.currentEdit.service_map_management_id,
                })
              );
            }}
            currentEdit={this.state.currentEdit}
          />
          <Row gutter={24} style={{ paddingBottom: 8 }}>
            <Col>
              <Table
                rowKey="id"
                loading={feeList.loading}
                columns={columns}
                dataSource={items}
                locale={{
                  emptyText: <FormattedMessage {...messages.noData} />,
                }}
                scroll={{ x: 1300 }}
                onChange={this.handleTableChange}
                bordered
                pagination={{
                  pageSize: 20,
                  total: feeList.totalPage,
                  current: this.state.current,
                  showTotal: (total, range) => (
                    <FormattedMessage
                      {...messages.totalFee}
                      values={{ total }}
                    />
                  ),
                }}
                expandedRowRender={(record) => {
                  let footer = "";
                  if (!record.description) {
                    return;
                  }
                  return (
                    <List
                      size="small"
                      footer={footer}
                      bordered
                      dataSource={[{ content: record.description }]}
                      renderItem={(item) => (
                        <List.Item>
                          <List.Item.Meta
                            description={
                              <p style={{ whiteSpace: "pre-line" }}>
                                {item.content}
                              </p>
                            }
                          />
                        </List.Item>
                      )}
                    />
                  );
                }}
              />
            </Col>
          </Row>
        </div>
      </Page>
    );
  }
}

FeeList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  feeList: makeSelectFeeList(),
  language: makeSelectLocale(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "feeListApartmentDetail", reducer });
const withSaga = injectSaga({ key: "feeListApartmentDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(FeeList)));
