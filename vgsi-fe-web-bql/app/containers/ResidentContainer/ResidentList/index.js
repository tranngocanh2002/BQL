/**
 *
 * StaffList
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
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Table,
  Tooltip,
} from "antd";
import queryString from "query-string";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";
import ModalEditResident from "../ResidentDetail/ModalEditResident";
import {
  defaultAction,
  fetchAllResidentAction,
  fetchApartmentOfResidentAction,
  updateDetailAction,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectResidentList from "./selectors";

const confirm = Modal.confirm;

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import { injectIntl } from "react-intl";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import { GLOBAL_COLOR } from "../../../utils/constants";
import messages from "../messages";

const topCol3 = {
  md: 8,
  lg: 6,
  xl: 5,
  xxl: 4,
};
/* eslint-disable react/prefer-stateless-function */
export class ResidentList extends React.PureComponent {
  state = {
    current: 1,
    currentEdit: undefined,
    visible: false,
    filter: {},
    downloading: false,
    exporting: false,
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
      this.props.residentList.importing != nextProps.residentList.importing &&
      !nextProps.residentList.importing
    ) {
      this.props.history.push(
        `/main/resident/list?${queryString.stringify({
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
    this.setState({ current: params.page, filter: reset ? {} : params }, () => {
      this.props.dispatch(fetchAllResidentAction(reset ? { page: 1 } : params));
      reset && this.props.history.push("/main/resident/list");
    });
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/resident/list?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  _onImport = () => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmImportResident),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        window.modalImport.show(
          (url) => {
            return window.connection.importResident({
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

  _onEdit = (record) => {
    this.setState(
      {
        currentEdit: {
          ...record,
        },
      },
      () => {
        this.setState({ visible: true });
      }
    );
  };

  render() {
    const { auth_group, residentList, location } = this.props;
    const { loading, data, totalPage, deleting, updating } = residentList;
    const { current, filter, downloading, exporting } = this.state;
    const formatMessage = this.props.intl.formatMessage;
    const { search } = location;
    let params = queryString.parse(search);
    const columns = [
      {
        width: 50,
        align: "center",
        fixed: "left",
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
        // width: 100,
        // fixed: "left",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.phone)}
          </span>
        ),
        dataIndex: "phone",
        key: "phone",
        render: (text, record) => <span>0{record.phone.slice(-9)}</span>,
      },
      {
        // width: 200,
        // fixed: "left",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.name)}
          </span>
        ),
        dataIndex: "first_name",
        key: "first_name",
      },
      {
        title: <span className={styles.nameTable}>Email</span>,
        dataIndex: "email",
        key: "email",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.birthday)}
          </span>
        ),
        dataIndex: "birthday",
        key: "birthday",
        render: (text, record) => (
          <span>
            {!!record.birthday &&
              moment.unix(record.birthday).format("DD/MM/YYYY")}
          </span>
        ),
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.gender)}
          </span>
        ),
        dataIndex: "gender",
        key: "gender",
        render: (text) => {
          if (text == 2) {
            return (
              <Tooltip title={formatMessage(messages.female)}>
                <i className="fa fa-female" style={{ fontSize: 28 }} />
              </Tooltip>
            );
          } else {
            return (
              <Tooltip title={formatMessage(messages.male)}>
                <i
                  className="fa fa-male"
                  style={{ fontSize: 28, color: GLOBAL_COLOR }}
                />
              </Tooltip>
            );
          }
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.propertyCount)}
          </span>
        ),
        align: "center",
        dataIndex: "total_apartment",
        key: "total_apartment",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.dateCreate)}
          </span>
        ),
        dataIndex: "created_at",
        key: "created_at",
        render: (text) => moment.unix(text).format("HH:mm DD/MM/YYYY"),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.install)}
          </span>
        ),
        dataIndex: "install_app",
        key: "install_app",
        align: "center",
        render: (text) => {
          if (text == 1) {
            return (
              <Tooltip title={formatMessage(messages.installed)}>
                <i className="material-icons" style={{ color: GLOBAL_COLOR }}>
                  mobile_friendly
                </i>
              </Tooltip>
            );
          } else {
            return (
              <Tooltip title={formatMessage(messages.notInstalled)}>
                <i className="material-icons" style={{ color: "#E4E4E4" }}>
                  mobile_off
                </i>
              </Tooltip>
            );
          }
        },
      },
      {
        width: 120,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        fixed: "right",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <Row
              type="flex"
              align="middle"
              style={{ color: GLOBAL_COLOR, cursor: "pointer" }}
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                this.props.history.push(
                  `/main/resident/detail/${record.apartment_map_resident_user_id}`,
                  {
                    record,
                  }
                );
              }}
            >
              {formatMessage(messages.detail)}
            </Row>
          </Row>
        ),
      },
    ];

    if (
      !auth_group.checkRole([config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_DETAIL])
    ) {
      columns.splice(columns.length - 1, 1);
    }

    return (
      <Page inner className={styles.residentListPage}>
        <div>
          <Row style={{ paddingBottom: 16 }} gutter={[24, 16]}>
            <Col {...topCol3}>
              <Input.Search
                value={filter["phone"] || ""}
                placeholder={formatMessage(messages.searchPhone)}
                prefix={
                  <Tooltip title={formatMessage(messages.searchViaPhone)}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["phone"]: e.target.value
                        ? e.target.value.trim()
                        : undefined,
                    },
                  });
                }}
                maxLength={255}
              />
            </Col>
            <Col {...topCol3}>
              <Input.Search
                value={filter["name"] || ""}
                placeholder={formatMessage(messages.searchName)}
                prefix={
                  <Tooltip title={formatMessage(messages.searchViaName)}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["name"]: e.target.value ? e.target.value : undefined,
                    },
                  });
                }}
                maxLength={255}
              />
            </Col>
            {/* <Col {...topCol3}>
              <Input.Search
                value={filter["apartment_name"] || ""}
                placeholder={formatMessage(messages.property)}
                prefix={
                  <Tooltip title={formatMessage(messages.searchViaProperty)}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["apartment_name"]: e.target.value,
                    },
                  });
                }}
              />
            </Col>
            <Col {...topCol3}>
              <Select
                showSearch
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.role)}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
                onChange={(value) => {
                  console.log("value", value);
                  this.setState({
                    filter: {
                      ...filter,
                      ["type"]: value,
                    },
                  });
                }}
                allowClear
                value={filter["type"]}
              >
                {config.TYPE_RESIDENT.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}`}
                    >{`${gr.name}`}</Select.Option>
                  );
                })}
              </Select>
            </Col> */}
            <Col {...topCol3}>
              <Input.Search
                //TODO: Sua danh sach cu dan
                value={filter["total_apartment"] || ""}
                // type="number"
                placeholder={formatMessage(messages.propertyCount)}
                prefix={
                  <Tooltip
                    title={formatMessage(messages.searchViaPropertyCount)}
                  >
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["total_apartment"]: e.target.value
                        ? e.target.value.replace(/\D+/g, "")
                        : undefined,
                    },
                  });
                }}
                maxLength={255}
              />
            </Col>
            <Col {...topCol3}>
              <Select
                // showSearch
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.install)}
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
                onChange={(value) => {
                  this.setState({
                    filter: {
                      ...filter,
                      ["install_app"]: value,
                    },
                  });
                }}
                allowClear
                value={filter["install_app"]}
              >
                {config.INSTALL_APP.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}`}
                    >{`${
                      this.props.language === "vi" ? gr.name : gr.name_en
                    }`}</Select.Option>
                  );
                })}
              </Select>
            </Col>
            <Col {...topCol3}>
              <Button
                type="primary"
                onClick={() => {
                  this.props.history.push(
                    `/main/resident/list?${queryString.stringify({
                      ...this.state.filter,
                      page: 1,
                    })}`
                  );
                }}
              >
                {formatMessage(messages.search)}
              </Button>
            </Col>
          </Row>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={formatMessage(messages.refresh)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={() => {
                  this.reload(this.props.location.search, true);
                  //this.props.history.push("/main/resident/list");
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            <WithRole roles={[config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_CREATE]}>
              <Tooltip title={formatMessage(messages.addNewResident)}>
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => this.props.history.push("/main/resident/add")}
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </WithRole>

            <WithRole roles={[config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_IMPORT]}>
              <Tooltip title={formatMessage(messages.import)}>
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
            <WithRole roles={[config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_IMPORT]}>
              <Tooltip title={formatMessage(messages.export)}>
                <Button
                  onClick={() => {
                    this.setState(
                      {
                        downloading: true,
                      },
                      () => {
                        window.connection
                          .downloadTemplateResident({})
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
            <WithRole roles={[config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_EXPORT]}>
              <Tooltip title={formatMessage(messages.exportResident)}>
                <Button
                  style={{ position: "absolute", right: 0 }}
                  onClick={() => {
                    this.setState(
                      {
                        exporting: true,
                      },
                      () => {
                        window.connection
                          .exportResidentData({ ...params })
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
          </Row>
          <Table
            rowKey="apartment_map_resident_user_id"
            loading={loading || deleting}
            scroll={{ x: 1024 }}
            columns={columns}
            dataSource={data}
            bordered
            locale={{
              emptyText: filter
                ? formatMessage(messages.searchNoData)
                : formatMessage(messages.noData),
            }}
            pagination={{
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total) => formatMessage(messages.totalRes, { total }),
            }}
            onRow={(record) => {
              return {
                onClick: () => {
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/resident/detail/${record.apartment_map_resident_user_id}`,
                      {
                        record,
                      }
                    );
                },
              };
            }}
            onChange={this.handleTableChange}
            expandRowByClick
            onExpand={(expanded, record) => {
              if (expanded) {
                this.props.dispatch(
                  fetchApartmentOfResidentAction({
                    resident_user_id: record.id,
                  })
                );
              }
            }}
          />
          <ModalEditResident
            setState={this.setState.bind(this)}
            updating={updating}
            style={{ cursor: "pointer" }}
            visible={this.state.visible}
            recordResident={this.state.currentEdit}
            handlerUpdate={(values) => {
              this.props.dispatch(
                updateDetailAction({
                  ...values,
                  apartment_map_resident_user_id:
                    this.state.currentEdit.apartment_map_resident_user_id,
                  callback: () => {
                    this.setState({ visible: false });
                    this.reload(this.props.location.search);
                  },
                })
              );
            }}
          />
        </div>
      </Page>
    );
  }
}

ResidentList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  residentList: makeSelectResidentList(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "residentList", reducer });
const withSaga = injectSaga({ key: "residentList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ResidentList));
