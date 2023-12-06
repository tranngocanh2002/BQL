/**
 *
 * ApartmentList
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Col,
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import queryString from "query-string";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import { selectAuthGroup } from "../../../redux/selectors";
import { Color, config } from "../../../utils";
import { getIconStyle, getRowStyle } from "../../../utils/constants";
import ModalEditApartment from "../ApartmentDetail/ModalEditApartment";
import {
  defaultAction,
  deleteApartmentAction,
  fetchAllApartmentAction,
  fetchAllApartmentType,
  fetchAllResidentByPhoneAction,
  fetchBuildingAreaAction,
  updateApartmentAction,
} from "./actions";
import styles from "./index.less";
import messages, { scope } from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectApartmentList from "./selectors";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";

const confirm = Modal.confirm;

const colLayout = {
  md: 6,
  xxl: 3,
};
const colLayout2 = {
  md: 4,
  xxl: 3,
};

/* eslint-disable react/prefer-stateless-function */
export class ApartmentList extends React.PureComponent {
  state = {
    visible: false,
    current: 1,
    keyword: "",
    currentEdit: undefined,
    filter: {},
    downloading: false,
    exporting: false,
    propertyName: "",
    propertyCode: "",
    address: "",
    houseHolder: "",
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this.reload(this.props.location.search);
    this.props.dispatch(fetchAllResidentByPhoneAction({}));
    this.props.dispatch(fetchBuildingAreaAction());
    this.props.dispatch(fetchAllApartmentType());
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }

    if (
      this.props.apartmentList.importing != nextProps.apartmentList.importing &&
      !nextProps.apartmentList.importing
    ) {
      this.props.history.push(
        `/main/apartment/list?${queryString.stringify({
          ...this.state.filter,
          page: 1,
        })}`
      );
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
        this.props.dispatch(
          fetchAllApartmentAction(reset ? { page: 1 } : params)
        );
        reset && this.props.history.push("/main/apartment/list");
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/apartment/list?${queryString.stringify({
          keyword: this.state.keyword,
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onEdit = (record) => {
    this.setState(
      {
        currentEdit: {
          ...record,
          building_area_id:
            record.building_area && `${record.building_area.id}`,
          capacity: record.capacity,
        },
      },
      () => {
        this.setState({ visible: true });
      }
    );
  };

  _onDelete = (record) => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage({ id: `${scope}.deleteModalTitle` }),
      okText: this.props.intl.formatMessage({ id: `${scope}.okText` }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({ id: `${scope}.cancelText` }),
      onOk: () => {
        this.props.dispatch(
          deleteApartmentAction({
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
  _onImport = () => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.importMess),
      okText: this.props.intl.formatMessage(messages.okText),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancelText),
      onOk: () => {
        window.modalImport.show(
          (url) => {
            return window.connection.importApartment({
              file_path: url,
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
    const { auth_group, apartmentList, intl, dispatch, location } = this.props;
    const {
      loading,
      data,
      totalPage,
      deleting,
      updating,
      buildingArea,
      allResident,
      apartment_type,
    } = apartmentList;
    const { search } = location;
    let params = queryString.parse(search);
    console.log("params", filter);
    const formatMessage = intl.formatMessage;

    const apartmentNameText = intl.formatMessage({ ...messages.apartmentName });
    const apartmentIdText = intl.formatMessage({ ...messages.apartmentId });
    const blockText = intl.formatMessage({ ...messages.block });
    const apartmentTypeText = intl.formatMessage({ ...messages.apartmentType });
    const apartmentOwnerText = intl.formatMessage({
      ...messages.apartmentOwner,
    });
    const apartmentAreaText = intl.formatMessage({ ...messages.apartmentArea });
    const statusText = intl.formatMessage({ ...messages.status });
    const actionText = intl.formatMessage({ ...messages.action });
    const apartmentDetailText = intl.formatMessage({
      ...messages.apartmentDetail,
    });
    const editApartmentText = intl.formatMessage({ ...messages.editApartment });
    const deleteApartmentText = intl.formatMessage({
      ...messages.deleteApartment,
    });
    const searchNameText = intl.formatMessage({ ...messages.searchName });
    const searchIdText = intl.formatMessage({ ...messages.searchId });
    const searchBlockText = intl.formatMessage({ ...messages.searchBlock });
    const searchOwnerText = intl.formatMessage({ ...messages.searchOwner });
    const emptyDataText = intl.formatMessage({ ...messages.emptyData });

    const { current, filter, downloading, exporting } = this.state;
    const columns = [
      {
        title: <span className={styles.nameTable}>{apartmentNameText}</span>,
        dataIndex: "name",
        key: "name",
        fixed: "left",
        width: 160,
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: <span className={styles.nameTable}>{apartmentIdText}</span>,
        dataIndex: "code",
        key: "code",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: <span className={styles.nameTable}>{blockText}</span>,
        dataIndex: "parent_path",
        key: "parent_path",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: <span className={styles.nameTable}>{apartmentTypeText}</span>,
        dataIndex: "form_type",
        key: "form_type",
        render: (text, record) => (
          <span>
            {this.props.language === "vi"
              ? record.form_type_name
              : record.form_type_name_en}
          </span>
        ),
      },
      {
        title: <span className={styles.nameTable}>{apartmentOwnerText}</span>,
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        title: <span className={styles.nameTable}>{apartmentAreaText}</span>,
        dataIndex: "capacity",
        key: "capacity",
        render: (text) => (
          <span>
            {text} m<sup>2</sup>
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.handOverStatus)}
          </span>
        ),
        dataIndex: "date_delivery",
        key: "handOverStatus",
        render: (text) => (
          <span>
            {text
              ? formatMessage(messages.handOverComplete)
              : formatMessage(messages.handOverNotComplete)}
          </span>
        ),
      },
      {
        title: <span className={styles.nameTable}>{statusText}</span>,
        dataIndex: "status",
        key: "status_name",
        render: (text) => (
          <span>
            {text === 1
              ? formatMessage(messages.statusLiving)
              : formatMessage(messages.statusEmpty)}
          </span>
        ),
      },
      {
        align: "center",
        title: <span className={styles.nameTable}>{actionText}</span>,
        dataIndex: "",
        key: "x",
        width: 200,
        fixed: "right",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title={apartmentDetailText}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_DETAIL,
                  ])
                    ? { cursor: "pointer" }
                    : { cursor: "not-allowed" }
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  let resident = {};
                  if (record.resident_user) {
                    resident = {
                      resident_name: record.resident_user.first_name,
                      resident_phone: record.resident_user.phone,
                    };
                  }
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/apartment/detail/${record.id}`,
                      {
                        record: {
                          ...record,
                          ...resident,
                          building_area_id:
                            record.building_area &&
                            `${record.building_area.id}`,
                          capacity: record.capacity,
                        },
                      }
                    );
                }}
              >
                <i
                  className="fa fa-eye"
                  style={getIconStyle(
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_DETAIL,
                    ])
                  )}
                />
              </Row>
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={editApartmentText}>
              <Row
                type="flex"
                align="middle"
                style={getRowStyle(
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_UPDATE,
                  ])
                )}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_UPDATE,
                  ]) && this._onEdit(record);
                }}
              >
                <i
                  className="fa fa-edit"
                  style={getIconStyle(
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_UPDATE,
                    ])
                  )}
                />
              </Row>
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={deleteApartmentText}>
              <Row
                type="flex"
                align="middle"
                style={
                  (getRowStyle(
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_DELETE,
                    ])
                  ),
                  { color: Color.orange })
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_DELETE,
                  ]) && this._onDelete(record);
                }}
              >
                <i
                  className="fa fa-trash"
                  style={getIconStyle(
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_DELETE,
                    ])
                  )}
                />
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    const { tree } = buildingArea;
    // if (
    //   !auth_group.checkRole([
    //     config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_DELETE,
    //   ]) &&
    //   !auth_group.checkRole([
    //     config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_DETAIL,
    //   ]) &&
    //   !auth_group.checkRole([config.ALL_ROLE_NAME.CONTRACTOR_MANAGEMENT_UPDATE])
    // ) {
    //   columns.splice(columns.length - 1, 1);
    // }

    return (
      <Page inner className={styles.apartmentListPafe}>
        <Row>
          <Row style={{ paddingBottom: 16 }} gutter={[8, 8]}>
            <Col {...colLayout}>
              <Input.Search
                value={this.state.propertyName}
                maxLength={255}
                placeholder={apartmentNameText}
                prefix={
                  <Tooltip title={searchNameText}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    propertyName: e.target.value.replace(/\s+/g, " "),
                    filter: {
                      ...filter,
                      ["name"]: e.target.value.replace(/\s+/g, " ").trim(),
                    },
                  });
                }}
              />
            </Col>
            <Col {...colLayout2}>
              <Input.Search
                value={this.state.propertyCode}
                maxLength={255}
                placeholder={apartmentIdText}
                prefix={
                  <Tooltip title={searchIdText}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    propertyCode: e.target.value.replace(/\s+/g, " "),
                    filter: {
                      ...filter,
                      ["code"]: this.state.propertyCode.trim(),
                    },
                  });
                }}
              />
            </Col>
            <Col {...colLayout2}>
              <Input.Search
                maxLength={255}
                value={this.state.address}
                placeholder={blockText}
                prefix={
                  <Tooltip title={searchBlockText}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    address: e.target.value.replace(/\s+/g, " "),
                    filter: {
                      ...filter,
                      ["parent_path"]: this.state.address.trim(),
                    },
                  });
                }}
              />
            </Col>
            <Col {...colLayout2}>
              <Select
                // showSearch
                style={{ width: "100%" }}
                loading={apartment_type.loading}
                placeholder={apartmentTypeText}
                optionFilterProp="children"
                notFoundContent={
                  apartment_type.loading ? <Spin size="small" /> : null
                }
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["form_type"]: value,
                    },
                  });
                }}
                allowClear
                value={filter["form_type"]}
              >
                {apartment_type.data.map((type, index) => {
                  return (
                    <Select.Option key={index} value={String(index)}>
                      {this.props.language === "vi" ? type.name : type.name_en}
                    </Select.Option>
                  );
                })}
              </Select>
            </Col>
            <Col {...colLayout}>
              <Input.Search
                maxLength={255}
                value={this.state.houseHolder}
                placeholder={apartmentOwnerText}
                prefix={
                  <Tooltip title={searchOwnerText}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    houseHolder: e.target.value.replace(/\s+/g, " "),
                    filter: {
                      ...filter,
                      ["resident_user_name"]: this.state.houseHolder.trim(),
                    },
                  });
                }}
              />
            </Col>

            <Col {...colLayout2}>
              <Select
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.handOverStatus)}
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["status_delivery"]: value,
                    },
                  });
                }}
                allowClear
                value={filter["status_delivery"]}
              >
                <Select.Option key={"handOverNotComplete"} value={"0"}>
                  <FormattedMessage {...messages.handOverNotComplete} />
                </Select.Option>
                <Select.Option key={"handOverComplete"} value={"1"}>
                  <FormattedMessage {...messages.handOverComplete} />
                </Select.Option>
              </Select>
            </Col>
            <Col {...colLayout2}>
              <Select
                style={{ width: "100%" }}
                placeholder={statusText}
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["status"]: value,
                    },
                  });
                }}
                allowClear
                value={filter["status"]}
              >
                <Select.Option value={"0"}>
                  <FormattedMessage {...messages.statusEmpty} />
                </Select.Option>
                <Select.Option value={"1"}>
                  <FormattedMessage {...messages.statusLiving} />
                </Select.Option>
              </Select>
            </Col>
            <Col {...colLayout}>
              <Button
                type="primary"
                onClick={(e) => {
                  e.preventDefault();
                  this.props.history.push(
                    `/main/apartment/list?${queryString.stringify({
                      ...this.state.filter,
                      page: 1,
                      ["name"]: this.state.propertyName
                        ? filter["name"]
                        : undefined,
                      ["code"]: this.state.propertyCode
                        ? filter["code"]
                        : undefined,
                      ["parent_path"]: this.state.address
                        ? filter["parent_path"]
                        : undefined,
                      ["resident_user_name"]: this.state.houseHolder
                        ? filter["resident_user_name"]
                        : undefined,
                    })}`
                  );

                  this.setState({
                    ...this.state,
                    propertyName: "",
                    propertyCode: "",
                    address: "",
                    houseHolder: "",
                  });
                }}
              >
                <FormattedMessage {...messages.search} />
              </Button>
            </Col>
          </Row>

          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={<FormattedMessage {...messages.reloadPage} />}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={(e) => {
                  e.preventDefault();
                  this.reload(this.props.location.search, true);
                  // this.props.history.push("/main/apartment/list");
                  this.setState({
                    ...this.state,
                    propertyName: "",
                    propertyCode: "",
                    address: "",
                    houseHolder: "",
                  });
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            <WithRole
              roles={[config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_CREATE]}
            >
              <Tooltip title={<FormattedMessage {...messages.addApartment} />}>
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => {
                    this.props.history.push("/main/apartment/add");
                  }}
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </WithRole>
            <WithRole
              roles={[config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_IMPORT]}
            >
              <Tooltip title={<FormattedMessage {...messages.importData} />}>
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
            </WithRole>
            <WithRole
              roles={[config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_IMPORT]}
            >
              <Tooltip
                title={<FormattedMessage {...messages.downloadTemplate} />}
              >
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => {
                    this.setState(
                      {
                        downloading: true,
                      },
                      () => {
                        window.connection
                          .downloadTemplateApartment({})
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
            </WithRole>
            {window.innerWidth > 1440 && (
              <WithRole
                roles={[config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_EXPORT]}
              >
                <Tooltip title={<FormattedMessage {...messages.exportData} />}>
                  <Button
                    style={{ position: "absolute", right: 0 }}
                    onClick={() => {
                      this.setState(
                        {
                          exporting: true,
                        },
                        () => {
                          window.connection
                            .exportApartmentData({ ...params })
                            .then((res) => {
                              if (this._unmounted) return;
                              this.setState(
                                {
                                  exporting: false,
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
                                exporting: false,
                              });
                            });
                        }
                      );
                    }}
                    loading={exporting}
                    shape="circle"
                    size="large"
                  >
                    {!exporting && (
                      <i
                        className="material-icons"
                        style={{
                          fontSize: 14,
                          display: "flex",
                          justifyContent: "center",
                          fontWeight: "bold",
                        }}
                      >
                        login
                      </i>
                    )}
                  </Button>
                </Tooltip>
              </WithRole>
            )}
            {window.innerWidth <= 1440 && (
              <WithRole
                roles={[config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_EXPORT]}
              >
                <Tooltip title={<FormattedMessage {...messages.exportData} />}>
                  <Button
                    onClick={() => {
                      this.setState(
                        {
                          exporting: true,
                        },
                        () => {
                          window.connection
                            .exportApartmentData({ ...params })
                            .then((res) => {
                              if (this._unmounted) return;
                              this.setState(
                                {
                                  exporting: false,
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
                                exporting: false,
                              });
                            });
                        }
                      );
                    }}
                    loading={exporting}
                    shape="circle"
                    size="large"
                  >
                    {!exporting && (
                      <i
                        className="material-icons"
                        style={{
                          fontSize: 14,
                          display: "flex",
                          justifyContent: "center",
                          fontWeight: "bold",
                        }}
                      >
                        login
                      </i>
                    )}
                  </Button>
                </Tooltip>
              </WithRole>
            )}
          </Row>
          <Table
            rowKey="id"
            loading={loading || deleting}
            columns={columns}
            dataSource={data}
            locale={{ emptyText: emptyDataText }}
            bordered
            scroll={{ x: 1366 }}
            pagination={{
              locale: this.props.language === "vi" ? "vi_VN" : "en_GB",
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total) => (
                <FormattedMessage
                  {...messages.totalApartment}
                  values={{ total }}
                />
              ),
            }}
            onChange={this.handleTableChange}
            onRow={(record) => {
              return {
                onClick: () => {
                  let resident = {};
                  if (record.resident_user) {
                    resident = {
                      resident_name: record.resident_user.first_name,
                      resident_phone: record.resident_user.phone,
                    };
                  }
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/apartment/detail/${record.id}`,
                      {
                        record: {
                          ...record,
                          ...resident,
                          building_area_id:
                            record.building_area &&
                            `${record.building_area.id}`,
                          capacity: record.capacity,
                        },
                      }
                    );
                },
              };
            }}
          />
          <ModalEditApartment
            setState={this.setState.bind(this)}
            language={this.props.language}
            updating={updating}
            dispatch={dispatch}
            visible={this.state.visible}
            allResident={allResident}
            recordApartment={this.state.currentEdit}
            tree={tree}
            apartment_type={apartment_type}
            fetchAllResidentByPhoneAction={fetchAllResidentByPhoneAction}
            handlerUpdate={(values) => {
              this.props.dispatch(
                updateApartmentAction({
                  ...values,
                  building_area_id: parseInt(values.building_area_id),
                  id: this.state.currentEdit.id,
                  callback: () => {
                    this.setState({ visible: false });
                    this.reload(this.props.location.search);
                  },
                })
              );
            }}
          />
        </Row>
      </Page>
    );
  }
}

ApartmentList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  apartmentList: makeSelectApartmentList(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "apartmentList", reducer });
const withSaga = injectSaga({ key: "apartmentList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ApartmentList));
