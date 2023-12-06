/**
 *
 * LockFeeTemplatePage
 *
 */

import { Button, Col, Modal, Row, Select, Spin, Table, Tooltip } from "antd";
import _ from "lodash";
import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import {
  approvePayment,
  createPayment,
  defaultAction,
  deletePayment,
  fetchAllPayment,
  fetchApartment,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectLockFeeTemplatePage, {
  makeSelectMotoPackingServiceContainer,
} from "./selectors";

import moment from "moment";
import queryString from "query-string";
import { FormattedMessage, injectIntl } from "react-intl";
import { withRouter } from "react-router";
import WithRole from "../../../../../components/WithRole";
import { getFullLinkImage } from "../../../../../connection";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../utils";
import messages from "../messages";
import ModalCreate from "./ModalCreate";
import styles from "./index.less";

/* eslint-disable react/prefer-stateless-function */
export class LockFeeTemplatePage extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {
        sort: "-created_at",
      },
      rowSelected: [],
      downloading: false,
      approveAll: false,
      visible: false,
    };

    this._onSearch = _.debounce(this.onSearch, 500);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };

  componentDidMount() {
    this._onSearch("");
    this.reload(this.props.location.search);
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }

    if (
      this.props.lockFeeTemplatePage.loading !=
        nextProps.lockFeeTemplatePage.loading &&
      !nextProps.lockFeeTemplatePage.loading
    ) {
      this.setState({
        rowSelected: [],
      });
    }

    if (
      this.props.lockFeeTemplatePage.creating !=
        nextProps.lockFeeTemplatePage.creating &&
      nextProps.lockFeeTemplatePage.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }
    if (
      this.props.lockFeeTemplatePage.importing !=
        nextProps.lockFeeTemplatePage.importing &&
      nextProps.lockFeeTemplatePage.importingSuccess
    ) {
      this.setState({
        visible: false,
      });
      if (this.state.current == 1) {
        this.reload(this.props.location.search);
      } else {
        this.props.history.push(
          `${this.props.location.pathname}?${queryString.stringify({
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
      if (!params.sort) params.sort = "-created_at";
    } catch (error) {
      params.page = 1;
    }

    params.keyword = params.keyword || "";

    this.setState(
      {
        current: params.page,
        keyword: params.keyword,
        filter: reset ? { sort: "-created_at" } : params,
      },
      () => {
        this.props.dispatch(
          fetchAllPayment({
            service_map_management_id:
              this.props.motoPackingServiceContainer.data.id,
            ...(reset ? { sort: "-created_at" } : params),
          })
        );
        reset && this.props.history.push(this.props.location.pathname);
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `${this.props.location.pathname}?${queryString.stringify({
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
      lockFeeTemplatePage,
      dispatch,
      history,
      motoPackingServiceContainer,
      auth_group,
      intl,
    } = this.props;
    const { current, rowSelected, downloading, approveAll, filter } =
      this.state;
    const totalDelete = rowSelected.length;
    const totalPage = lockFeeTemplatePage.totalPage;
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              lockFeeTemplatePage.loading ? current - 2 : current - 1
            ) *
              20 +
              index +
              1}
          </span>
        ),
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
        align: "left",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.bienSo} />
          </span>
        ),
        dataIndex: "service_management_vehicle_number",
        key: "service_management_vehicle_number",
        render: (text) => <span>{text}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.typeFee} />
          </span>
        ),
        dataIndex: "service_parking_level_name",
        key: "service_parking_level_name",
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
        render: (text) => (
          <span>{`${moment.unix(text).format("MM/YYYY")}`}</span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.amountMoney} />
          </span>
        ),
        dataIndex: "total_money",
        key: "total_money",
        render: (text) => (
          <span style={{ color: "#1B1B27" }}>{`${formatPrice(text)} `}đ</span>
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
            {moment.unix(record.updated_at).format("DD/MM/YYYY - HH:mm")}
          </span>
        ),
      },
      {
        align: "center",
        width: 100,
        // fixed: "right",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.action} />
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title={<FormattedMessage {...messages.deleteFee} />}>
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
                <i className="fa fa-trash" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    if (!auth_group.checkRole([config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER])) {
      columns.splice(columns.length - 1, 1);
    }

    return (
      <Row>
        <Row
          gutter={[24, 16]}
          style={{
            marginBottom: 16,
          }}
          type="flex"
        >
          <Col span={8}>
            <Select
              style={{ width: "100%" }}
              loading={lockFeeTemplatePage.apartment.loading}
              showSearch
              placeholder={<FormattedMessage {...messages.selectProperty} />}
              optionFilterProp="children"
              notFoundContent={
                lockFeeTemplatePage.apartment.loading ? (
                  <Spin size="small" />
                ) : null
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
              {lockFeeTemplatePage.apartment.items.map((gr) => {
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

          <Col>
            <Button
              type="primary"
              onClick={() => {
                this.props.history.push(
                  `${this.props.location.pathname}?${queryString.stringify({
                    ...this.state.filter,
                    page: 1,
                  })}`
                );
              }}
            >
              <FormattedMessage {...messages.search} />
            </Button>
          </Col>
        </Row>
        <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER]}>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={<FormattedMessage {...messages.refreshPage} />}>
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
            <Tooltip title={<FormattedMessage {...messages.addFee} />}>
              <Button
                style={{ marginRight: 10 }}
                onClick={() => {
                  this.setState({ visible: true, currentEdit: undefined });
                }}
                disabled={lockFeeTemplatePage.importing}
                icon="plus"
                shape="circle"
                size="large"
              />
            </Tooltip>
            {!!motoPackingServiceContainer.data.config &&
              motoPackingServiceContainer.data.config.auto_create_fee == 0 && (
                <Tooltip title={<FormattedMessage {...messages.import} />}>
                  <Button
                    style={{ marginRight: 10 }}
                    shape="circle"
                    size="large"
                    onClick={() => {
                      window.modalImport.show(
                        (url) => {
                          return window.connection.importFeeMotoPacking({
                            file_path: url,
                            service_map_management_id:
                              this.props.motoPackingServiceContainer.data.id,
                            is_validate: 0,
                          });
                        },
                        () => {
                          this.reload(this.props.location.search);
                        }
                      );
                    }}
                  >
                    <i className="material-icons" style={{ fontSize: 14 }}>
                      cloud_upload
                    </i>
                  </Button>
                </Tooltip>
              )}
            {!!motoPackingServiceContainer.data.config &&
              motoPackingServiceContainer.data.config.auto_create_fee == 0 && (
                <Tooltip title={<FormattedMessage {...messages.export} />}>
                  <Button
                    style={{ marginRight: 10 }}
                    onClick={() => {
                      this.setState(
                        {
                          downloading: true,
                        },
                        () => {
                          window.connection
                            .downloadTemplateFeeMotoPacking({})
                            .then((res) => {
                              if (this._unmounted) return;
                              this.setState(
                                {
                                  downloading: false,
                                },
                                () => {
                                  if (res.success) {
                                    window.open(
                                      getFullLinkImage(res.data.file_path)
                                    );
                                  }
                                }
                              );
                            })
                            .catch(() => {
                              if (this._unmounted) return;
                              this.setState({
                                downloading: false,
                              });
                            });
                        }
                      );
                    }}
                    loading={downloading}
                    shape="circle"
                    size="large"
                  >
                    {!downloading && (
                      <i className="material-icons" style={{ fontSize: 14 }}>
                        cloud_download
                      </i>
                    )}
                  </Button>
                </Tooltip>
              )}
            <Tooltip title={<FormattedMessage {...messages.approve} />}>
              <Button
                style={{ marginRight: 10 }}
                icon={""}
                onClick={() => {
                  Modal.confirm({
                    autoFocusButton: null,
                    title:
                      rowSelected.length > 1
                        ? this.props.intl.formatMessage(messages.confirmApprove)
                        : this.props.intl.formatMessage(
                            messages.confirmApproveOne
                          ),
                    okText: this.props.intl.formatMessage(messages.agree),
                    okType: "danger",
                    cancelText: this.props.intl.formatMessage(messages.cancel),
                    onOk: () => {
                      this.props.dispatch(
                        approvePayment({
                          is_active_all: 0,
                          is_active_array: rowSelected,
                          service_map_management_id:
                            motoPackingServiceContainer.data.id,
                          callback: () => {
                            this.setState({
                              rowSelected: [],
                              approveAll: false,
                            });
                            this.reload(this.props.location.search);
                          },
                        })
                      );
                    },
                    onCancel: () => {
                      this.setState({ approveAll: false });
                    },
                  });
                }}
                disabled={
                  lockFeeTemplatePage.importing || rowSelected.length == 0
                }
                loading={lockFeeTemplatePage.approving && !approveAll}
                shape="circle"
                size="large"
              >
                {
                  <i className="material-icons" style={{ fontSize: 14 }}>
                    done
                  </i>
                }
              </Button>
            </Tooltip>
            <Tooltip title={<FormattedMessage {...messages.approveAll} />}>
              <Button
                style={{ marginRight: 10 }}
                onClick={() => {
                  this.setState({ approveAll: true });
                  Modal.confirm({
                    autoFocusButton: null,
                    title: intl.formatMessage({
                      ...messages.confirmApproveAll,
                    }),
                    okText: intl.formatMessage({ ...messages.agree }),
                    okType: "danger",
                    cancelText: intl.formatMessage({ ...messages.cancel }),
                    onOk: () => {
                      this.props.dispatch(
                        approvePayment({
                          is_active_all: 1,
                          service_map_management_id:
                            motoPackingServiceContainer.data.id,
                          callback: () => {
                            this.setState({
                              rowSelected: [],
                            });
                            this.reload(this.props.location.search);
                          },
                        })
                      );
                    },
                    onCancel: () => {
                      this.setState({ approveAll: false });
                    },
                  });
                }}
                shape="circle"
                size="large"
                disabled={
                  lockFeeTemplatePage.importing ||
                  lockFeeTemplatePage.data.length == 0
                }
                loading={lockFeeTemplatePage.approving && approveAll}
              >
                {
                  <i className="material-icons" style={{ fontSize: 14 }}>
                    done_all
                  </i>
                }
              </Button>
            </Tooltip>
            <Tooltip title="Xoá phí">
              <Button
                type="danger"
                style={{ marginRight: 10 }}
                onClick={() => {
                  Modal.confirm({
                    autoFocusButton: null,
                    title:
                      rowSelected.length > 1
                        ? intl.formatMessage({ ...messages.confirmDeleteAll })
                        : intl.formatMessage({ ...messages.confirmDeleteOne }),
                    okText: intl.formatMessage({ ...messages.agree }),
                    okType: "danger",
                    cancelText: intl.formatMessage({ ...messages.cancel }),
                    onOk: () => {
                      this.props.dispatch(
                        deletePayment({
                          ids: rowSelected,
                          callback: () => {
                            this.setState({
                              rowSelected: [],
                            });
                            this.reload(this.props.location.search);
                          },
                        })
                      );
                    },
                    onCancel() {},
                  });
                }}
                disabled={
                  lockFeeTemplatePage.importing || rowSelected.length == 0
                }
                loading={lockFeeTemplatePage.deleting}
                shape="circle"
                size="large"
              >
                {!lockFeeTemplatePage.deleting && (
                  <i className="material-icons" style={{ fontSize: 14 }}>
                    delete
                  </i>
                )}
              </Button>
            </Tooltip>
          </Row>
        </WithRole>
        <Col>
          <Table
            rowKey="id"
            loading={
              lockFeeTemplatePage.loading ||
              lockFeeTemplatePage.deleting ||
              lockFeeTemplatePage.importing ||
              lockFeeTemplatePage.approving
            }
            columns={columns}
            dataSource={lockFeeTemplatePage.data}
            expandedRowRender={(record) => <span>{record.description}</span>}
            locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
            bordered
            pagination={{
              pageSize: 20,
              total: lockFeeTemplatePage.totalPage,
              current: this.state.current,
              showTotal: (total) => (
                <FormattedMessage {...messages.total} values={{ total }} />
              ),
            }}
            expandRowByClick
            onChange={this.handleTableChange}
            scroll={{ x: 1366 }}
            rowSelection={
              auth_group.checkRole([
                config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
              ])
                ? {
                    selectedRowKeys: rowSelected,
                    onChange: (selectedRowKeys) => {
                      this.setState({
                        rowSelected: selectedRowKeys,
                      });
                    },
                  }
                : null
            }
          />
        </Col>
        <ModalCreate
          visible={this.state.visible}
          setState={this.setState.bind(this)}
          dispatch={dispatch}
          history={history}
          lockFeeTemplatePage={lockFeeTemplatePage}
          addPayment={(payload) => {
            this.props.dispatch(
              createPayment({
                ...payload,
                service_map_management_id: motoPackingServiceContainer.data.id,
                callback: () => {
                  this.reload(this.props.location.search);
                },
              })
            );
          }}
          motoPackingServiceContainer={motoPackingServiceContainer}
        />
      </Row>
    );
  }
}

LockFeeTemplatePage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  lockFeeTemplatePage: makeSelectLockFeeTemplatePage(),
  motoPackingServiceContainer: makeSelectMotoPackingServiceContainer(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "lockFeeTemplateMotoPackingPage",
  reducer,
});
const withSaga = injectSaga({ key: "lockFeeTemplateMotoPackingPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(LockFeeTemplatePage)));
