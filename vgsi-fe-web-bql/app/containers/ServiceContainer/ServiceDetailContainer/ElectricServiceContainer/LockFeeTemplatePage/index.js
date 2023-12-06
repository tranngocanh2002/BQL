/**
 *
 * LockFeeTemplatePage
 *
 */

import { Button, Col, Modal, Row, Select, Spin, Table, Tooltip } from "antd";
import _ from "lodash";
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
import WithRole from "../../../../../components/WithRole";
import { getFullLinkImage } from "../../../../../connection";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../utils";
import { GLOBAL_COLOR } from "../../../../../utils/constants";
import messages from "../messages";
import ModalCreate from "./ModalCreate";
import {
  approvePayment,
  createPayment,
  defaultAction,
  deletePayment,
  fetchAllPayment,
  fetchApartment,
  updatePayment,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectLockFeeTemplatePage, {
  makeSelectElectricServiceContainer,
} from "./selectors";
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
      this.props.lockFeeTemplateElectricPage.loading !=
        nextProps.lockFeeTemplateElectricPage.loading &&
      !nextProps.lockFeeTemplateElectricPage.loading
    ) {
      this.setState({
        rowSelected: [],
      });
    }

    if (
      this.props.lockFeeTemplateElectricPage.creating !=
        nextProps.lockFeeTemplateElectricPage.creating &&
      nextProps.lockFeeTemplateElectricPage.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }

    if (
      this.props.lockFeeTemplateElectricPage.importing !=
        nextProps.lockFeeTemplateElectricPage.importing &&
      nextProps.lockFeeTemplateElectricPage.importingSuccess
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
        filter: reset ? {} : params,
      },
      () => {
        this.props.dispatch(
          fetchAllPayment({
            service_map_management_id:
              this.props.electricServiceContainer.data.id,
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
      title: this.props.intl.formatMessage({
        ...messages.confirmDeletePayment,
      }),
      okText: this.props.intl.formatMessage({
        ...messages.okText,
      }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({
        ...messages.cancelText,
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
      title: this.props.intl.formatMessage(messages.titleImportFeeTemplate),
      okText: this.props.intl.formatMessage(messages.okText),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancelText),
      onOk: () => {
        window.modalImport.show(
          (url) => {
            return window.connection.importFeeElectric({
              file_path: url,
              service_map_management_id:
                this.props.electricServiceContainer.data.id,
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
      lockFeeTemplateElectricPage,
      dispatch,
      history,
      electricServiceContainer,
      auth_group,
      intl,
    } = this.props;
    const { current, rowSelected, approveAll, downloading, filter } =
      this.state;

    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              lockFeeTemplateElectricPage.loading ? current - 2 : current - 1
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
      {
        align: "right",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.headIndex} />
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
        width: 150,
        align: "center",
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
          style={{
            marginBottom: 16,
          }}
        >
          <Col md={8} lg={6} style={{ marginRight: 10 }}>
            <Select
              style={{ width: "100%" }}
              loading={lockFeeTemplateElectricPage.apartment.loading}
              showSearch
              placeholder={<FormattedMessage {...messages.plhProperty} />}
              optionFilterProp="children"
              notFoundContent={
                lockFeeTemplateElectricPage.apartment.loading ? (
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
              {lockFeeTemplateElectricPage.apartment.items.map((gr) => {
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
            {<FormattedMessage {...messages.search} />}
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
                disabled={lockFeeTemplateElectricPage.importing}
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
                        .downloadTemplateFeeElectric({})
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
            <Tooltip title={this.props.intl.formatMessage(messages.approve)}>
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
                    okText: this.props.intl.formatMessage(messages.okText),
                    okType: "danger",
                    cancelText: this.props.intl.formatMessage(
                      messages.cancelText
                    ),
                    onOk: () => {
                      this.props.dispatch(
                        approvePayment({
                          is_active_all: 0,
                          is_active_array: rowSelected,
                          service_map_management_id:
                            electricServiceContainer.data.id,
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
                  lockFeeTemplateElectricPage.importing ||
                  rowSelected.length == 0
                }
                loading={lockFeeTemplateElectricPage.approving && !approveAll}
                shape="circle"
                size="large"
              >
                <i className="material-icons" style={{ fontSize: 14 }}>
                  done
                </i>
              </Button>
            </Tooltip>
            <Tooltip title={this.props.intl.formatMessage(messages.approveAll)}>
              <Button
                style={{ marginRight: 10 }}
                onClick={() => {
                  this.setState({ approveAll: true });
                  Modal.confirm({
                    autoFocusButton: null,
                    title: this.props.intl.formatMessage(
                      messages.confirmApproveAll
                    ),
                    okText: this.props.intl.formatMessage(messages.okText),
                    okType: "danger",
                    cancelText: this.props.intl.formatMessage(
                      messages.cancelText
                    ),
                    onOk: () => {
                      this.props.dispatch(
                        approvePayment({
                          is_active_all: 1,
                          service_map_management_id:
                            electricServiceContainer.data.id,
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
                  lockFeeTemplateElectricPage.importing ||
                  lockFeeTemplateElectricPage.data.length == 0
                }
                loading={lockFeeTemplateElectricPage.approving && approveAll}
              >
                <i className="material-icons" style={{ fontSize: 14 }}>
                  done_all
                </i>
              </Button>
            </Tooltip>
            <Tooltip title={this.props.intl.formatMessage(messages.deleteFee)}>
              <Button
                type="danger"
                onClick={() => {
                  Modal.confirm({
                    autoFocusButton: null,
                    title: this.props.intl.formatMessage(
                      messages.confirmDeleteAllFee
                    ),
                    okText: this.props.intl.formatMessage(messages.okText),
                    okType: "danger",
                    cancelText: this.props.intl.formatMessage(
                      messages.cancelText
                    ),
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
                  lockFeeTemplateElectricPage.importing ||
                  rowSelected.length == 0
                }
                loading={lockFeeTemplateElectricPage.deleting}
                shape="circle"
                size="large"
              >
                {!lockFeeTemplateElectricPage.deleting && (
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
            lockFeeTemplateElectricPage.loading ||
            lockFeeTemplateElectricPage.deleting ||
            lockFeeTemplateElectricPage.importing ||
            lockFeeTemplateElectricPage.approving
          }
          columns={columns}
          dataSource={lockFeeTemplateElectricPage.data}
          locale={{ emptyText: this.props.intl.formatMessage(messages.noData) }}
          bordered
          pagination={{
            pageSize: 20,
            total: lockFeeTemplateElectricPage.totalPage,
            current: this.state.current,
            showTotal: (total) =>
              this.props.intl.formatMessage(messages.noData, total),
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
          lockFeeTemplateElectricPage={lockFeeTemplateElectricPage}
          addPayment={(payload) => {
            this.props.dispatch(
              createPayment({
                ...payload,
                service_map_management_id: electricServiceContainer.data.id,
              })
            );
          }}
          updatePayment={(payload) => {
            this.props.dispatch(
              updatePayment({
                ...payload,
                id: this.state.currentEdit.id,
                service_map_management_id: electricServiceContainer.data.id,
              })
            );
          }}
          currentEdit={this.state.currentEdit}
          electricServiceContainer={electricServiceContainer}
        />
      </Row>
    );
  }
}

LockFeeTemplatePage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  lockFeeTemplateElectricPage: makeSelectLockFeeTemplatePage(),
  electricServiceContainer: makeSelectElectricServiceContainer(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "lockFeeTemplateElectricPage",
  reducer,
});
const withSaga = injectSaga({ key: "lockFeeTemplateElectricPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(LockFeeTemplatePage)));
