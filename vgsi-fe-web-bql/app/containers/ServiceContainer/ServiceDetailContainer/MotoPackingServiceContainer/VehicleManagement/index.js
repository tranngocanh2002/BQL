/**
 *
 * VihicleManagement
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Col,
  DatePicker,
  Input,
  Modal,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import _ from "lodash";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import WithRole from "../../../../../components/WithRole";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../utils";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectVihicleManagement from "./selectors";

import {
  activeVehicle,
  cancelVehicle,
  createVehicle,
  defaultAction,
  deleteVehicle,
  fetchAllFeeLevel,
  fetchAllVehicle,
  fetchApartment,
  updateVehicle,
} from "./actions";
import styles from "./index.less";

import moment from "moment";
import queryString from "query-string";
import { FormattedMessage, injectIntl } from "react-intl";
import { getFullLinkImage } from "../../../../../connection";
import { GLOBAL_COLOR } from "../../../../../utils/constants";
import messages from "../messages";
import makeSelectMotoPackingServiceContainer from "../selectors";
import ModalCreate from "./ModalCreate";
import ModelActive from "./ModelActive";
const confirm = Modal.confirm;
const colLayout = {
  md: 6,
  lg: 6,
  xl: 5,
};
/* eslint-disable react/prefer-stateless-function */
export class VihicleManagement extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {},
      downloading: false,
    };
    this._onSearch = _.debounce(this.onSearch, 500);
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartment({ name: keyword }));
  };

  componentDidMount() {
    this._onSearch("");
    this.reload(this.props.location.search);
    this.props.dispatch(fetchAllFeeLevel());
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }

    if (
      this.props.vihicleManagement.success !=
        nextProps.vihicleManagement.success &&
      nextProps.vihicleManagement.success
    ) {
      this.setState({
        visible: false,
        visibleActive: false,
      });
      this.reload(this.props.location.search);
    }
    if (
      this.props.vihicleManagement.importSuccess !=
        nextProps.vihicleManagement.importSuccess &&
      nextProps.vihicleManagement.importSuccess
    ) {
      this.setState({
        visible: false,
        visibleActive: false,
      });
      if (this.state.current == 1) {
        this.reload(this.props.location.search);
      } else {
        this.props.history.push(
          `/main/service/detail/moto-packing/vehicle?${queryString.stringify({
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
        this.props.dispatch(fetchAllVehicle(reset ? { page: 1 } : params));
        reset &&
          this.props.history.push(
            `${this.props.location.pathname}?${queryString.stringify({
              page: 1,
            })}`
          );
      }
    );
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage({
        ...messages.confirmDeleteVehicle,
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
          deleteVehicle({
            id: record.id,
          })
        );
      },
      onCancel() {},
    });
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/service/detail/moto-packing/vehicle?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onImport = () => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.titleImportVehicle),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        window.modalImport.show(
          (url) => {
            return window.connection.importVehicle({
              file_path: url,
              service_map_management_id: this.props.currentService.data.id,
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
    const { vihicleManagement, auth_group, intl } = this.props;
    const { downloading } = this.state;
    const plhLicense = intl.formatMessage({ ...messages.enterLicensePlate });
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => <span>{index + 1}</span>,
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
        render: (text, record) => {
          return (
            <span>{`${record.apartment_name} (${record.apartment_parent_path})`}</span>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.bienSo} />
          </span>
        ),
        dataIndex: "number",
        key: "number",
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
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.levelFee} />
          </span>
        ),
        dataIndex: "service_parking_level_price",
        key: "service_parking_level_price",
        render: (text) => {
          return <span>{formatPrice(text)}</span>;
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.startDate} />
          </span>
        ),
        dataIndex: "start_date",
        key: "start_date",
        render: (text) => {
          return <span>{moment.unix(text).format("DD/MM/YYYY")}</span>;
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.endDate} />
          </span>
        ),
        dataIndex: "end_date",
        key: "end_date",
        render: (text) => {
          return <span>{moment.unix(text).format("DD/MM/YYYY")}</span>;
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.dateCancelVehicle} />
          </span>
        ),
        dataIndex: "cancel_date",
        key: "cancel_date",
        render: (text) => {
          return (
            <span>
              {text ? (
                moment.unix(text).format("DD/MM/YYYY")
              ) : (
                <FormattedMessage {...messages.notHave} />
              )}
            </span>
          );
        },
      },
      {
        width: 140,
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.status} />
          </span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text) => {
          if (text == 0) {
            return (
              <span className="luci-status-warning">
                <FormattedMessage {...messages.create} />
              </span>
            );
          }
          if (text == 1) {
            return (
              <span className="luci-status-primary">
                <FormattedMessage {...messages.activated} />
              </span>
            );
          }
          if (text == 2) {
            return (
              <span className="luci-status-danger">
                <FormattedMessage {...messages.canceled} />
              </span>
            );
          }
        },
      },
      {
        width: 200,
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
            <Tooltip title={<FormattedMessage {...messages.edit} />}>
              <Row
                type="flex"
                align="middle"
                style={{ color: GLOBAL_COLOR, cursor: "pointer" }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  this.setState(
                    {
                      currentEdit: record,
                    },
                    () => {
                      this.setState({ visible: true });
                    }
                  );
                }}
              >
                <i className="fa fa-edit" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
            &ensp;&ensp;| &ensp;&ensp;
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
            &ensp;&ensp;| &ensp;&ensp;
            {record.status === 0 ? (
              {}
            ) : record.status === 1 ? (
              <Tooltip title={<FormattedMessage {...messages.cancelVehicle} />}>
                <Row
                  type="flex"
                  align="middle"
                  style={{ color: "#F15A29", cursor: "pointer" }}
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.setState(
                      {
                        currentEdit: record,
                      },
                      () => {
                        this.setState({ visibleActive: true });
                      }
                    );
                  }}
                >
                  <i className="fa fa-lock" style={{ fontSize: 18 }} />
                </Row>
              </Tooltip>
            ) : (
              <Tooltip
                title={<FormattedMessage {...messages.activateVehicle} />}
              >
                <Row
                  type="flex"
                  align="middle"
                  style={{ color: GLOBAL_COLOR, cursor: "pointer" }}
                  onClick={(e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.setState(
                      {
                        currentEdit: record,
                      },
                      () => {
                        this.setState({ visibleActive: true });
                      }
                    );
                  }}
                >
                  <i className="fa fa-unlock-alt" style={{ fontSize: 18 }} />
                </Row>
              </Tooltip>
            )}
          </Row>
        ),
      },
    ];

    if (!auth_group.checkRole([config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER])) {
      columns.splice(columns.length - 1, 1);
    }

    return (
      <Row>
        <Row gutter={[24, 16]} style={{ marginBottom: 16 }}>
          <Col {...colLayout}>
            <Select
              style={{ width: "100%" }}
              loading={vihicleManagement.apartment.loading}
              showSearch
              placeholder={<FormattedMessage {...messages.selectProperty} />}
              optionFilterProp="children"
              notFoundContent={
                vihicleManagement.apartment.loading ? (
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
              {vihicleManagement.apartment.items.map((gr) => {
                return (
                  <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>{`${
                    gr.name
                  } (${gr.parent_path})${
                    gr.status == 0
                      ? ` - ${this.props.intl.formatMessage(messages.empty)}`
                      : ""
                  }`}</Select.Option>
                );
              })}
            </Select>
          </Col>
          <Col md={8} lg={6} xl={6}>
            <Input
              value={this.state.filter.number || ""}
              onChange={(e) => {
                this.setState({
                  filter: {
                    ...this.state.filter,
                    number: e.target.value,
                  },
                });
              }}
              allowClear
              style={{ width: "100%" }}
              placeholder={plhLicense}
            />
          </Col>
          <Col md={8} lg={6} xl={6}>
            <DatePicker
              format="DD/MM/YYYY"
              placeholder={this.props.intl.formatMessage(messages.selectDate)}
              value={
                this.state.filter.end_date
                  ? moment.unix(this.state.filter.end_date)
                  : undefined
              }
              onChange={(value) => {
                this.setState({
                  filter: {
                    ...this.state.filter,
                    end_date: value ? value.startOf("day").unix() : undefined,
                  },
                });
              }}
              style={{ width: "100%" }}
            />
          </Col>
          <Col {...colLayout}>
            <Button
              type="primary"
              onClick={(e) => {
                this.props.history.push(
                  `/main/service/detail/moto-packing/vehicle?${queryString.stringify(
                    {
                      ...this.state.filter,
                      page: 1,
                    }
                  )}`
                );
              }}
            >
              <FormattedMessage {...messages.search} />
            </Button>
          </Col>
        </Row>
        <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER]}>
          <Row style={{ marginBottom: 16 }}>
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
            <Tooltip title={<FormattedMessage {...messages.addVehicle} />}>
              <Button
                style={{ marginRight: 10 }}
                onClick={() => {
                  this.setState({ visible: true, currentEdit: undefined });
                }}
                disabled={vihicleManagement.importing}
                loading={vihicleManagement.approving}
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
                onClick={() => {
                  this.setState(
                    {
                      downloading: true,
                    },
                    () => {
                      window.connection
                        .downloadTemplateManagementVehicle({})
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
          </Row>
        </WithRole>
        <Table
          rowKey="id"
          loading={
            vihicleManagement.loading ||
            vihicleManagement.deleting ||
            vihicleManagement.importing ||
            vihicleManagement.approving
          }
          columns={columns}
          dataSource={vihicleManagement.data}
          expandedRowRender={(record) => <span>{record.description}</span>}
          locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
          bordered
          pagination={{
            pageSize: 20,
            total: vihicleManagement.totalPage,
            current: this.state.current,
            showTotal: (total) => (
              <FormattedMessage {...messages.total} values={{ total }} />
            ),
          }}
          expandRowByClick
          scroll={{ x: 1366 }}
          onChange={this.handleTableChange}
        />

        <ModalCreate
          visible={this.state.visible}
          setState={this.setState.bind(this)}
          dispatch={this.props.dispatch}
          vihicleManagement={vihicleManagement}
          addVehicle={(payload) => {
            this.props.dispatch(
              createVehicle({
                ...payload,
              })
            );
          }}
          updateVehicle={(payload) => {
            this.props.dispatch(
              updateVehicle({
                ...payload,
              })
            );
          }}
          currentEdit={this.state.currentEdit}
        />

        <ModelActive
          visible={this.state.visibleActive}
          setState={this.setState.bind(this)}
          currentEdit={this.state.currentEdit}
          vihicleManagement={vihicleManagement}
          activeVehicle={(payload) => {
            this.props.dispatch(
              activeVehicle({
                ...payload,
              })
            );
          }}
          cancleVehicle={(payload) => {
            this.props.dispatch(
              cancelVehicle({
                ...payload,
              })
            );
          }}
        />
      </Row>
    );
  }
}

VihicleManagement.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  vihicleManagement: makeSelectVihicleManagement(),
  auth_group: selectAuthGroup(),
  currentService: makeSelectMotoPackingServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "vihicleManagement", reducer });
const withSaga = injectSaga({ key: "vihicleManagement", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(VihicleManagement));
