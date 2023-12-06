/**
 *
 * PaymentTemplateManagementClusterPage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Modal, Row, Select, Spin, Table, Tooltip } from "antd";
import _ from "lodash";
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
import makeSelectPaymentTemplateManagementClusterPage, {
  makeSelectManagementClusterServiceContainer,
} from "./selectors";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import queryString from "query-string";
import { withRouter } from "react-router";
import { globalStyles } from "utils/constants";
import WithRole from "../../../../../components/WithRole";
import { getFullLinkImage } from "../../../../../connection";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../utils";
import messages from "../messages";
import ModalCreate from "./ModalCreate";
import styles from "./index.less";

/* eslint-disable react/prefer-stateless-function */
export class PaymentTemplateManagementClusterPage extends React.PureComponent {
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

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
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
      this.props.paymentTemplateManagementClusterPage.creating !=
        nextProps.paymentTemplateManagementClusterPage.creating &&
      this.state.visible
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }

    if (
      this.props.paymentTemplateManagementClusterPage.importing !=
        nextProps.paymentTemplateManagementClusterPage.importing &&
      nextProps.paymentTemplateManagementClusterPage.importingSuccess
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
              this.props.managementClusterServiceContainer.data.id,
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
    const {
      paymentTemplateManagementClusterPage,
      dispatch,
      history,
      managementClusterServiceContainer,
      auth_group,
      intl,
    } = this.props;
    const { current, rowSelected, downloading, approveAll } = this.state;
    const total = paymentTemplateManagementClusterPage.totalPage;
    const totalFee = rowSelected.length;
    console.log(paymentTemplateManagementClusterPage.data);
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              paymentTemplateManagementClusterPage.loading
                ? current - 2
                : current - 1
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
          <span style={{ color: "#1B1B27" }}>{`${formatPrice(text)} `}đ</span>
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
        // width: 170,
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
        fixed: "right",
        align: "center",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.action} />
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record, index) => (
          <Row type="flex" align="middle" justify="center">
            {/* {
            record.status == 0 && <>
              <Tooltip title="Chỉnh sửa">
                <Row type='flex' align='middle' style={{ color: COLOR_GLOBAL, cursor: 'pointer' }}
                  onClick={e => {
                    e.preventDefault()
                    e.stopPropagation()
                    this._onEdit(record)
                  }}
                >
                  <i className="material-icons" style={{ fontSize: 18, marginRight: 6 }} >
                    edit
                </i>
                </Row>
              </Tooltip>&ensp;&ensp;|
                          &ensp;&ensp;
          </>
          } */}
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
                  className="material-icons"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER,
                    ])
                      ? globalStyles.iconDelete
                      : globalStyles.iconDisabled
                  }
                >
                  delete_outline
                </i>
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
            style={{ minWidth: 300, marginRight: 20 }}
            loading={paymentTemplateManagementClusterPage.apartment.loading}
            showSearch
            placeholder={<FormattedMessage {...messages.plhProperty} />}
            optionFilterProp="children"
            notFoundContent={
              paymentTemplateManagementClusterPage.apartment.loading ? (
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
            {paymentTemplateManagementClusterPage.apartment.items.map((gr) => {
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
            />
          </Tooltip>
          <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER]}>
            <Tooltip title={<FormattedMessage {...messages.addFee} />}>
              <Button
                style={{ marginRight: 10 }}
                onClick={() => {
                  this.setState({ visible: true, currentEdit: undefined });
                }}
                disabled={paymentTemplateManagementClusterPage.importing}
                icon="plus"
                shape="circle"
                size="large"
              />
            </Tooltip>
            {!!managementClusterServiceContainer.data.config &&
              managementClusterServiceContainer.data.config.auto_create_fee ==
                0 && (
                <Tooltip title={<FormattedMessage {...messages.import} />}>
                  <Button
                    style={{ marginRight: 10 }}
                    shape="circle"
                    size="large"
                    onClick={() => {
                      window.modalImport.show(
                        (url) => {
                          return window.connection.importBuildingFee({
                            file_path: url,
                            service_map_management_id:
                              managementClusterServiceContainer.data.id,
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
            {!!managementClusterServiceContainer.data.config &&
              managementClusterServiceContainer.data.config.auto_create_fee ==
                0 && (
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
                            .downloadTemplateBuildingFee({})
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
                            .catch((e) => {
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
                        ? this.props.intl.formatMessage(
                            messages.confirmApproveFee
                          )
                        : this.props.intl.formatMessage(
                            messages.confirmApproveOneFee
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
                            managementClusterServiceContainer.data.id,
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
                  paymentTemplateManagementClusterPage.importing ||
                  rowSelected.length == 0
                }
                loading={
                  paymentTemplateManagementClusterPage.approving && !approveAll
                }
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
                      {
                        ...messages.confirmApprove,
                      },
                      { total }
                    ),
                    okText: intl.formatMessage({
                      ...messages.okText,
                    }),
                    okType: "danger",
                    cancelText: intl.formatMessage({
                      ...messages.cancelText,
                    }),
                    onOk: () => {
                      this.props.dispatch(
                        approvePayment({
                          is_active_all: 1,
                          // is_active_array:
                          //   paymentTemplateManagementClusterPage.data,
                          // service_map_management_id:
                          //   managementClusterServiceContainer.data.id,
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
                  paymentTemplateManagementClusterPage.importing ||
                  paymentTemplateManagementClusterPage.data.length == 0
                }
                loading={
                  paymentTemplateManagementClusterPage.approving && approveAll
                }
              >
                {
                  <i className="material-icons" style={{ fontSize: 14 }}>
                    done_all
                  </i>
                }
              </Button>
            </Tooltip>
            <Tooltip title={<FormattedMessage {...messages.deleteAllFee} />}>
              <Button
                type="danger"
                onClick={() => {
                  Modal.confirm({
                    autoFocusButton: null,
                    title:
                      rowSelected.length > 1
                        ? intl.formatMessage({
                            ...messages.confirmDeleteAllFee,
                          })
                        : intl.formatMessage({
                            ...messages.confirmDeleteOneFee,
                          }),
                    okText: intl.formatMessage({
                      ...messages.okText,
                    }),
                    okType: "danger",
                    cancelText: intl.formatMessage({
                      ...messages.cancelText,
                    }),
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
                  paymentTemplateManagementClusterPage.importing ||
                  rowSelected.length == 0
                }
                loading={paymentTemplateManagementClusterPage.deleting}
                shape="circle"
                size="large"
              >
                {!paymentTemplateManagementClusterPage.deleting && (
                  <i className="material-icons" style={{ fontSize: 14 }}>
                    delete
                  </i>
                )}
              </Button>
            </Tooltip>
          </WithRole>
        </Row>

        <Table
          rowKey="id"
          loading={
            paymentTemplateManagementClusterPage.loading ||
            paymentTemplateManagementClusterPage.deleting ||
            paymentTemplateManagementClusterPage.importing ||
            paymentTemplateManagementClusterPage.approving
          }
          columns={columns}
          dataSource={paymentTemplateManagementClusterPage.data}
          locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
          bordered
          pagination={{
            pageSize: 20,
            total: paymentTemplateManagementClusterPage.totalPage,
            current: this.state.current,
            showTotal: (total) => (
              <FormattedMessage {...messages.totalFee} values={{ total }} />
            ),
          }}
          onChange={this.handleTableChange}
          expandRowByClick
          expandedRowRender={(record) => <span>{record.description}</span>}
          scroll={{ x: 1366 }}
          rowSelection={
            auth_group.checkRole([config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER])
              ? {
                  selectedRowKeys: rowSelected,
                  onChange: (selectedRowKeys) => {
                    this.setState({
                      rowSelected: selectedRowKeys,
                    });
                  },
                  // getCheckboxProps: record => ({
                  //   disabled: record.name === 'Disabled User', // Column configuration not to be checked
                  //   name: record.name,
                  // }),
                }
              : null
          }
        />
        <ModalCreate
          visible={this.state.visible}
          setState={this.setState.bind(this)}
          dispatch={dispatch}
          history={history}
          paymentTemplateManagementClusterPage={
            paymentTemplateManagementClusterPage
          }
          addPayment={(payload) => {
            this.props.dispatch(
              createPayment({
                ...payload,
                service_map_management_id:
                  managementClusterServiceContainer.data.id,
                callback: () => {
                  this.reload(this.props.location.search);
                },
              })
            );
          }}
          updatePayment={(payload) => {
            this.props.dispatch(
              updatePayment({
                ...payload,
                id: this.state.currentEdit.id,
                service_map_management_id:
                  managementClusterServiceContainer.data.id,
              })
            );
          }}
          currentEdit={this.state.currentEdit}
          managementClusterServiceContainer={managementClusterServiceContainer}
          language={this.props.language}
        />
      </Row>
    );
  }
}

PaymentTemplateManagementClusterPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  paymentTemplateManagementClusterPage:
    makeSelectPaymentTemplateManagementClusterPage(),
  managementClusterServiceContainer:
    makeSelectManagementClusterServiceContainer(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "paymentTemplateManagementClusterPage",
  reducer,
});
const withSaga = injectSaga({
  key: "paymentTemplateManagementClusterPage",
  saga,
});

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(PaymentTemplateManagementClusterPage)));
