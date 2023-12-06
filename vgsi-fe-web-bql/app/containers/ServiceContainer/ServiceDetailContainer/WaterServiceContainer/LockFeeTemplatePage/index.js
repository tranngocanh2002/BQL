/**
 *
 * LockFeeTemplatePage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Col, Modal, Row, Select, Spin, Table, Tooltip } from "antd";
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
  makeSelectWaterServiceContainer,
} from "./selectors";

import _ from "lodash";
import moment from "moment";
import queryString from "query-string";
import { config, formatPrice } from "../../../../../utils";
import ModalCreate from "./ModalCreate";
import styles from "./index.less";

import { FormattedMessage, injectIntl } from "react-intl";
import { withRouter } from "react-router";
import WithRole from "../../../../../components/WithRole";
import { getFullLinkImage } from "../../../../../connection";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { GLOBAL_COLOR } from "../../../../../utils/constants";
import messages from "../messages";
const confirm = Modal.confirm;

/* eslint-disable react/prefer-stateless-function */
export class LockFeeTemplatePage extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {
        sort: "-updated_at",
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
      this.props.lockFeeTemplateWaterPage.loading !=
        nextProps.lockFeeTemplateWaterPage.loading &&
      !nextProps.lockFeeTemplateWaterPage.loading
    ) {
      this.setState({
        rowSelected: [],
      });
    }

    if (
      this.props.lockFeeTemplateWaterPage.creating !=
        nextProps.lockFeeTemplateWaterPage.creating &&
      nextProps.lockFeeTemplateWaterPage.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }

    if (
      this.props.lockFeeTemplateWaterPage.importing !=
        nextProps.lockFeeTemplateWaterPage.importing &&
      nextProps.lockFeeTemplateWaterPage.importingSuccess
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
      if (!params.sort) params.sort = "-updated_at";
    } catch (error) {
      params.page = 1;
    }

    params.keyword = params.keyword || "";

    this.setState(
      {
        current: params.page,
        keyword: params.keyword,
        filter: reset ? { sort: "-updated_at" } : params,
      },
      () => {
        this.props.dispatch(
          fetchAllPayment({
            service_map_management_id: this.props.waterServiceContainer.data.id,
            ...(reset ? { sort: "-updated_at" } : params),
          })
        );
        reset &&
          this.props.history.push(
            `${this.props.location.pathname}?${queryString.stringify({
              page: 1,
              sort: "-updated_at",
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
      title: this.props.intl.formatMessage({
        ...messages.confirmDeletePaymentInformation,
      }),
      okText: this.props.intl.formatMessage({
        ...messages.agree,
      }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({
        ...messages.cancel,
      }),
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
      title: this.props.intl.formatMessage(messages.titleImportWaterTemplate),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        window.modalImport.show(
          (url) => {
            return window.connection.importFeeWater({
              file_path: url,
              service_map_management_id:
                this.props.waterServiceContainer.data.id,
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
      lockFeeTemplateWaterPage,
      dispatch,
      history,
      waterServiceContainer,
      auth_group,
      intl,
    } = this.props;
    const { current, rowSelected, approveAll, downloading, filter } =
      this.state;
    const totalPage = lockFeeTemplateWaterPage.totalPage;
    const totalData = rowSelected.length;
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              lockFeeTemplateWaterPage.loading ? current - 2 : current - 1
            ) *
              20 +
              index +
              1}
          </span>
        ),
      },
      {
        align: "left",
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
        align: "right",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.firstIndex} />
          </span>
        ),
        dataIndex: "start_index",
        key: "start_index",
        render: (text) => <span>{text}</span>,
      },
      {
        align: "right",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.lastIndex} />
          </span>
        ),
        dataIndex: "end_index",
        key: "end_index",
        render: (text) => <span>{text}</span>,
      },
      {
        align: "right",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.usage} />
          </span>
        ),
        dataIndex: "total_index",
        key: "total_index",
        render: (text) => <span>{text}</span>,
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
        align: "right",
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
        width: 150,
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
                    style={{ color: GLOBAL_COLOR, cursor: "pointer" }}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      this._onEdit(record);
                    }}
                  >
                    <i className="fa fa-edit" style={{ fontSize: 18 }} />
                  </Row>
                </Tooltip>
                &ensp;&ensp;|&ensp;&ensp;
              </>
            )}
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
          <Col md={8} lg={6}>
            <Select
              style={{ width: "100%" }}
              loading={lockFeeTemplateWaterPage.apartment.loading}
              showSearch
              placeholder={<FormattedMessage {...messages.selectProperty} />}
              optionFilterProp="children"
              notFoundContent={
                lockFeeTemplateWaterPage.apartment.loading ? (
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
              {lockFeeTemplateWaterPage.apartment.items.map((gr) => {
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
            <Tooltip title={<FormattedMessage {...messages.refresh} />}>
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
            <Tooltip title={<FormattedMessage {...messages.addNewFee} />}>
              <Button
                style={{ marginRight: 10 }}
                onClick={() => {
                  this.setState({ visible: true, currentEdit: undefined });
                }}
                disabled={lockFeeTemplateWaterPage.importing}
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
                        .downloadTemplateFeeWater({})
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
                        ? intl.formatMessage({
                            ...messages.confirmApprove,
                          })
                        : intl.formatMessage(messages.confirmApproveOne),
                    okText: intl.formatMessage({ ...messages.agree }),
                    okType: "danger",
                    cancelText: intl.formatMessage({ ...messages.cancel }),
                    onOk: () => {
                      this.props.dispatch(
                        approvePayment({
                          is_active_all: 0,
                          is_active_array: rowSelected,
                          service_map_management_id:
                            waterServiceContainer.data.id,
                          callback: () => {
                            this.setState({ rowSelected: [] });
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
                  lockFeeTemplateWaterPage.importing || rowSelected.length == 0
                }
                loading={lockFeeTemplateWaterPage.approving && !approveAll}
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
                    title: intl.formatMessage(
                      { ...messages.confirmApproveTotalPage },
                      { totalPage }
                    ),
                    okText: intl.formatMessage({ ...messages.agree }),
                    okType: "danger",
                    cancelText: intl.formatMessage({ ...messages.cancel }),
                    onOk: () => {
                      this.props.dispatch(
                        approvePayment({
                          is_active_all: 1,
                          service_map_management_id:
                            waterServiceContainer.data.id,
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
                shape="circle"
                size="large"
                disabled={
                  lockFeeTemplateWaterPage.importing ||
                  lockFeeTemplateWaterPage.data.length == 0
                }
                loading={lockFeeTemplateWaterPage.approving && approveAll}
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
                            ...messages.confirmDeleteAllData,
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
                  lockFeeTemplateWaterPage.importing || rowSelected.length == 0
                }
                loading={lockFeeTemplateWaterPage.deleting}
                shape="circle"
                size="large"
              >
                {!lockFeeTemplateWaterPage.deleting && (
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
            lockFeeTemplateWaterPage.loading ||
            lockFeeTemplateWaterPage.deleting ||
            lockFeeTemplateWaterPage.importing ||
            lockFeeTemplateWaterPage.approving
          }
          columns={columns}
          dataSource={lockFeeTemplateWaterPage.data}
          locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
          bordered
          pagination={{
            pageSize: 20,
            total: lockFeeTemplateWaterPage.totalPage,
            current: this.state.current,
            showTotal: (total) => (
              <FormattedMessage {...messages.totalFee} values={{ total }} />
            ),
          }}
          expandRowByClick
          scroll={{ x: 1366 }}
          expandedRowRender={(record) => (
            <span style={{ whiteSpace: "pre-wrap" }}>{record.description}</span>
          )}
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
          lockFeeTemplateWaterPage={lockFeeTemplateWaterPage}
          addPayment={(payload) => {
            this.props.dispatch(
              createPayment({
                ...payload,
                service_map_management_id: waterServiceContainer.data.id,
              })
            );
          }}
          updatePayment={(payload) => {
            this.props.dispatch(
              updatePayment({
                ...payload,
                id: this.state.currentEdit.id,
                service_map_management_id: waterServiceContainer.data.id,
                // callback: () => {
                //   if (this.state.current == 1) {
                //     this.reload(this.props.location.search)
                //   } else {
                //     this.props.history.push(`${this.props.location.pathname}?${queryString.stringify({
                //       ...this.state.filter,
                //       page: 1,
                //     })}`)
                //   }
                // }
              })
            );
          }}
          currentEdit={this.state.currentEdit}
          waterServiceContainer={waterServiceContainer}
        />
      </Row>
    );
  }
}

LockFeeTemplatePage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  lockFeeTemplateWaterPage: makeSelectLockFeeTemplatePage(),
  waterServiceContainer: makeSelectWaterServiceContainer(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "lockFeeTemplateWaterPage", reducer });
const withSaga = injectSaga({ key: "lockFeeTemplateWaterPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(LockFeeTemplatePage)));
