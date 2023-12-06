/**
 *
 * LockFeeTemplatePage
 *
 */

import { Button, Modal, Row, Select, Spin, Table, Tooltip } from "antd";
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
  updatePayment,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectLockFeeTemplatePage, {
  makeSelectOldDebitServiceContainer,
} from "./selectors";

import _ from "lodash";
import moment from "moment";
import queryString from "query-string";
import { FormattedMessage, injectIntl } from "react-intl";
import { withRouter } from "react-router";
import WithRole from "../../../../../components/WithRole";
import { getFullLinkImage } from "../../../../../connection";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../utils";
import { globalStyles } from "../../../../../utils/constants";
import messages from "../messages";
import ModalCreate from "./ModalCreate";
import styles from "./index.less";
const confirm = Modal.confirm;

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
      this.props.lockFeeTemplateOldDebitPage.loading !=
        nextProps.lockFeeTemplateOldDebitPage.loading &&
      !nextProps.lockFeeTemplateOldDebitPage.loading
    ) {
      this.setState({
        rowSelected: [],
      });
    }

    if (
      this.props.lockFeeTemplateOldDebitPage.creating !=
        nextProps.lockFeeTemplateOldDebitPage.creating &&
      nextProps.lockFeeTemplateOldDebitPage.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }

    if (
      this.props.lockFeeTemplateOldDebitPage.importing !=
        nextProps.lockFeeTemplateOldDebitPage.importing &&
      nextProps.lockFeeTemplateOldDebitPage.importingSuccess
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
      if (!params.sort) {
        params.sort = "-created_at";
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
          fetchAllPayment({
            service_map_management_id:
              this.props.oldDebitServiceContainer.data.id,
            ...(reset ? { page: 1, sort: "-created_at" } : params),
          })
        );
        reset &&
          this.props.history.push(
            `${this.props.location.pathname}?${queryString.stringify({
              sort: "-created_at",
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
      title: this.props.intl.formatMessage({ ...messages.confirmDelete }),
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

  _onImport = () => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.titleImportOldFeeTemplate),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        window.modalImport.show(
          (url) => {
            return window.connection.importFeeOldDebit({
              file_path: url,
              service_map_management_id:
                this.props.oldDebitServiceContainer.data.id,
              is_validate: 0,
            });
          },
          () => {
            this.reload(this.props.location.search);
          }
        );
      },
      onCancel() {},
    });
  };

  render() {
    const {
      lockFeeTemplateOldDebitPage,
      oldDebitServiceContainer,
      auth_group,
      dispatch,
      history,
      intl,
    } = this.props;
    const { current, rowSelected, downloading, filter, approveAll } =
      this.state;
    const totalData = rowSelected.length;
    const totalPage = lockFeeTemplateOldDebitPage.totalPage;

    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        width: 50,
        // fixed: "left",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              lockFeeTemplateOldDebitPage.loading ? current - 2 : current - 1
            ) *
              20 +
              index +
              1}
          </span>
        ),
      },
      {
        width: 200,
        // fixed: "left",
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
        dataIndex: "total_money",
        key: "total_money",
        render: (text) => (
          <span style={{ color: "#1B1B27" }}>{`${formatPrice(text)} đ`}</span>
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
        width: 170,
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
        width: 120,
        // fixed: "right",
        align: "center",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.action} />
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            {record.status == 0 && (
              <>
                <Tooltip title={<FormattedMessage {...messages.edit} />}>
                  <Row
                    type="flex"
                    align="middle"
                    style={
                      auth_group.checkRole([
                        config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
                      ])
                        ? globalStyles.row
                        : globalStyles.rowDisabled
                    }
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      auth_group.checkRole([
                        config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
                      ]) && this._onEdit(record);
                    }}
                  >
                    <i
                      className="fa fa-edit"
                      style={
                        auth_group.checkRole([
                          config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
                        ])
                          ? globalStyles.icon
                          : globalStyles.iconDisabled
                      }
                    />
                  </Row>
                </Tooltip>
                &ensp;&ensp;|&ensp;&ensp;
              </>
            )}
            <Tooltip title={<FormattedMessage {...messages.deleteFee} />}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
                  ]) && this._onDelete(record);
                }}
              >
                <i
                  className="fa fa-trash"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
                    ])
                      ? globalStyles.iconDelete
                      : globalStyles.iconDisabled
                  }
                />
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    return (
      <Row>
        <Row
          type="flex"
          align="middle"
          style={{
            marginBottom: 16,
          }}
        >
          <Select
            style={{ minWidth: 250, marginRight: 10 }}
            loading={lockFeeTemplateOldDebitPage.apartment.loading}
            showSearch
            placeholder={<FormattedMessage {...messages.selectProperty} />}
            optionFilterProp="children"
            notFoundContent={
              lockFeeTemplateOldDebitPage.apartment.loading ? (
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
            {lockFeeTemplateOldDebitPage.apartment.items.map((gr) => {
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
                disabled={lockFeeTemplateOldDebitPage.importing}
                icon="plus"
                shape="circle"
                size="large"
              />
            </Tooltip>
            <Tooltip title={<FormattedMessage {...messages.import} />}>
              <Button
                style={{ marginRight: 10 }}
                shape="circle"
                size="large"
                onClick={this._onImport}
              >
                <i className="material-icons" style={{ fontSize: 14 }}>
                  cloud_upload
                </i>
              </Button>
            </Tooltip>
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
                        .downloadTemplateFeeOldDebit({})
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
                            oldDebitServiceContainer.data.id,
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
                    onCancel() {},
                  });
                }}
                disabled={
                  lockFeeTemplateOldDebitPage.importing ||
                  rowSelected.length == 0
                }
                loading={lockFeeTemplateOldDebitPage.approving && !approveAll}
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
                            oldDebitServiceContainer.data.id,
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
                  lockFeeTemplateOldDebitPage.importing ||
                  lockFeeTemplateOldDebitPage.data.length == 0
                }
                loading={lockFeeTemplateOldDebitPage.approving && approveAll}
              >
                {
                  <i className="material-icons" style={{ fontSize: 14 }}>
                    done_all
                  </i>
                }
              </Button>
            </Tooltip>
            <Tooltip title={<FormattedMessage {...messages.deleteFee} />}>
              <Button
                type="danger"
                onClick={() => {
                  Modal.confirm({
                    autoFocusButton: null,
                    title:
                      rowSelected.length > 1
                        ? intl.formatMessage({
                            ...messages.confirmDeleteData,
                          })
                        : intl.formatMessage({
                            ...messages.confirmDeleteOneData,
                          }),
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
                  lockFeeTemplateOldDebitPage.importing ||
                  rowSelected.length == 0
                }
                loading={lockFeeTemplateOldDebitPage.deleting}
                shape="circle"
                size="large"
              >
                {!lockFeeTemplateOldDebitPage.deleting && (
                  <i className="material-icons" style={{ fontSize: 14 }}>
                    delete
                  </i>
                )}
              </Button>
            </Tooltip>
          </Row>
        </WithRole>

        <Table
          rowKey="id"
          loading={
            lockFeeTemplateOldDebitPage.loading ||
            lockFeeTemplateOldDebitPage.deleting ||
            lockFeeTemplateOldDebitPage.importing ||
            lockFeeTemplateOldDebitPage.approving
          }
          columns={columns}
          expandedRowRender={(record) => <p>{record.description}</p>}
          dataSource={lockFeeTemplateOldDebitPage.data}
          locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
          bordered
          pagination={{
            pageSize: 20,
            total: lockFeeTemplateOldDebitPage.totalPage,
            current: this.state.current,
            showTotal: (total) => (
              <FormattedMessage {...messages.totalFee} values={{ total }} />
            ),
          }}
          expandRowByClick
          scroll={{ x: 1000 }}
          onChange={this.handleTableChange}
          rowSelection={
            auth_group.checkRole([config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER])
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
        <ModalCreate
          visible={this.state.visible}
          setState={this.setState.bind(this)}
          dispatch={dispatch}
          history={history}
          lockFeeTemplateOldDebitPage={lockFeeTemplateOldDebitPage}
          addPayment={(payload) => {
            this.props.dispatch(
              createPayment({
                ...payload,
                service_map_management_id: oldDebitServiceContainer.data.id,
              })
            );
          }}
          updatePayment={(payload) => {
            this.props.dispatch(
              updatePayment({
                ...payload,
                id: this.state.currentEdit.id,
                service_map_management_id: oldDebitServiceContainer.data.id,
              })
            );
          }}
          currentEdit={this.state.currentEdit}
          oldDebitServiceContainer={oldDebitServiceContainer}
        />
      </Row>
    );
  }
}

LockFeeTemplatePage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  lockFeeTemplateOldDebitPage: makeSelectLockFeeTemplatePage(),
  oldDebitServiceContainer: makeSelectOldDebitServiceContainer(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "lockFeeTemplateOldDebitPage",
  reducer,
});
const withSaga = injectSaga({ key: "lockFeeTemplateOldDebitPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(LockFeeTemplatePage)));
