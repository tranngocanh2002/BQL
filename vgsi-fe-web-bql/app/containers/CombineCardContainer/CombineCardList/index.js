/**
 *
 * CombineCardList
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
import {
  defaultAction,
  deleteCombineCardAction,
  fetchAllCombineCardAction,
  createCombineCardAction,
  updateCombineCardAction,
} from "./actions";
import styles from "./index.less";
import messages, { scope } from "../messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectCombineCardList from "./selectors";
import ModalEditCombineCard from "./ModalEditCombineCard";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { selectBuildingCluster } from "redux/selectors/config";
import { ALL_ROLE_NAME } from "utils/config";
import moment from "moment";

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
export class CombineCardList extends React.PureComponent {
  state = {
    visibleAddCard: false,
    current: 1,
    keyword: "",
    currentEdit: undefined,
    filter: {},
    downloading: false,
    exporting: false,
    apartment_name: "",
    number: "",
    code: "",
    resident_user_name: "",
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this.props.dispatch(fetchAllCombineCardAction());
    this.reload(this.props.location.search);
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }

    if (
      this.props.combineCardList.importing !=
        nextProps.combineCardList.importing &&
      !nextProps.combineCardList.importing
    ) {
      this.props.history.push(
        `/main/merge-card/list?${queryString.stringify({
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
          fetchAllCombineCardAction(reset ? { page: 1 } : params)
        );
        reset && this.props.history.push("/main/merge-card/list");
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/merge-card/list?${queryString.stringify({
          keyword: this.state.keyword,
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onEdit = (record) => {
    this.setState({
      currentEdit: record,
      visibleAddCard: true,
    });
  };

  _onDelete = (record) => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.deleteModalTitle),
      okText: this.props.intl.formatMessage(messages.okText),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancelText),
      onOk: () => {
        this.props.dispatch(
          deleteCombineCardAction({
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
            return window.connection.importCombineCard({
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
    const {
      auth_group,
      combineCardList,
      intl,
      dispatch,
      location,
      buildingCluster,
    } = this.props;
    const { loading, data, totalPage, deleting } = combineCardList;
    const { search } = location;
    let params = queryString.parse(search);
    const formatMessage = intl.formatMessage;
    const { current, filter, downloading, exporting } = this.state;
    const columns = [
      {
        width: 50,
        align: "center",
        fixed: "left",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => <span>{index + 1}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.cardId)}
          </span>
        ),
        dataIndex: "code",
        key: "code",
        fixed: "left",
        width: 160,
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.cardNumber)}
          </span>
        ),
        dataIndex: "number",
        key: "number",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.apartmentName)}
          </span>
        ),
        dataIndex: "apartment_name",
        key: "apartment_name",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.cardOwner)}
          </span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.status)}
          </span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text) => (
          <span>
            {(text === 0 && "Tạo mới") ||
              (text === 1 && "Hoạt động") ||
              (text === 2 && "Khóa") ||
              (text === 3 && "Thu hồi") ||
              (text === 4 && "Hủy")}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.update)}
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text) =>
          text ? moment.unix(text).format("HH:mm, DD/MM/YYYY") : "---",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.createAt)}
          </span>
        ),
        dataIndex: "created_at",
        key: "created_at",
        render: (text) =>
          text ? moment.unix(text).format("HH:mm, DD/MM/YYYY") : "---",
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        width: 200,
        fixed: "right",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title={this.props.intl.formatMessage(messages.cardDetail)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([ALL_ROLE_NAME.CARD_MANAGEMENT_DETAIL])
                    ? { cursor: "pointer" }
                    : { cursor: "not-allowed" }
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    ALL_ROLE_NAME.CARD_MANAGEMENT_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/merge-card/detail/${record.id}`,
                      {
                        record: {
                          ...record,
                        },
                      }
                    );
                }}
              >
                <i
                  className="fa fa-eye"
                  style={getIconStyle(
                    auth_group.checkRole([ALL_ROLE_NAME.CARD_MANAGEMENT_DELETE])
                  )}
                />
              </Row>
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={this.props.intl.formatMessage(messages.editCard)}>
              <Row
                type="flex"
                align="middle"
                style={getRowStyle(
                  auth_group.checkRole([ALL_ROLE_NAME.CARD_MANAGEMENT_UPDATE])
                )}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    ALL_ROLE_NAME.CARD_MANAGEMENT_UPDATE,
                  ]) && this._onEdit(record);
                }}
              >
                <i
                  className="fa fa-edit"
                  style={getIconStyle(
                    auth_group.checkRole([ALL_ROLE_NAME.CARD_MANAGEMENT_UPDATE])
                  )}
                />
              </Row>
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={this.props.intl.formatMessage(messages.deleteCard)}>
              <Row
                type="flex"
                align="middle"
                style={
                  (getRowStyle(
                    auth_group.checkRole([ALL_ROLE_NAME.CARD_MANAGEMENT_DELETE])
                  ),
                  { color: Color.orange })
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    ALL_ROLE_NAME.CARD_MANAGEMENT_DELETE,
                  ]) && this._onDelete(record);
                }}
              >
                <i
                  className="fa fa-trash"
                  style={getIconStyle(
                    auth_group.checkRole([ALL_ROLE_NAME.CARD_MANAGEMENT_DELETE])
                  )}
                />
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    return (
      <Page inner className={styles.combineCardListPage}>
        <Row>
          <Row style={{ paddingBottom: 16 }} gutter={[8, 8]}>
            <Col {...colLayout2}>
              <Input.Search
                maxLength={255}
                value={filter["code"] || ""}
                placeholder={this.props.intl.formatMessage(messages.cardId)}
                prefix={
                  <Tooltip
                    title={this.props.intl.formatMessage(messages.cardId)}
                  >
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    code: e.target.value.replace(/\s+/g, " "),
                    filter: {
                      ...filter,
                      ["code"]: this.state.code.trim(),
                    },
                  });
                }}
              />
            </Col>

            <Col {...colLayout2}>
              <Input.Search
                value={filter["number"] || ""}
                maxLength={255}
                placeholder={this.props.intl.formatMessage(messages.cardNumber)}
                prefix={
                  <Tooltip
                    title={this.props.intl.formatMessage(messages.cardNumber)}
                  >
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    number: e.target.value.replace(/\s+/g, " "),
                    filter: {
                      ...filter,
                      ["number"]: this.state.number.trim(),
                    },
                  });
                }}
              />
            </Col>
            <Col {...colLayout}>
              <Input.Search
                value={filter["apartment_name"] || ""}
                maxLength={255}
                placeholder={this.props.intl.formatMessage(
                  messages.apartmentName
                )}
                prefix={
                  <Tooltip
                    title={this.props.intl.formatMessage(messages.searchName)}
                  >
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    apartment_name: e.target.value.replace(/\s+/g, " "),
                    filter: {
                      ...filter,
                      ["apartment_name"]: e.target.value
                        .replace(/\s+/g, " ")
                        .trim(),
                    },
                  });
                }}
              />
            </Col>
            <Col {...colLayout}>
              <Input.Search
                maxLength={255}
                value={filter["resident_user_name"] || ""}
                placeholder={this.props.intl.formatMessage(messages.cardOwner)}
                prefix={
                  <Tooltip
                    title={this.props.intl.formatMessage(messages.cardOwner)}
                  >
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState({
                    resident_user_name: e.target.value.replace(/\s+/g, " "),
                    filter: {
                      ...filter,
                      ["resident_user_name"]:
                        this.state.resident_user_name.trim(),
                    },
                  });
                }}
              />
            </Col>
            <Col {...colLayout2}>
              <Select
                // showSearch
                style={{ width: "100%" }}
                //loading={apartment_type.loading}
                placeholder={this.props.intl.formatMessage(messages.status)}
                optionFilterProp="children"
                // notFoundContent={
                //   apartment_type.loading ? <Spin size="small" /> : null
                // }
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
                <Select.Option value="0">{"Tạo mới"}</Select.Option>
                <Select.Option value="1">{"Hoạt động"}</Select.Option>
                <Select.Option value="2">{"Khóa"}</Select.Option>
                <Select.Option value="3">{"Thu hồi"}</Select.Option>
                <Select.Option value="4">{"Hủy"}</Select.Option>
              </Select>
            </Col>

            <Col {...colLayout}>
              <Button
                type="primary"
                onClick={(e) => {
                  e.preventDefault();
                  this.props.history.push(
                    `/main/merge-card/list?${queryString.stringify({
                      ...this.state.filter,
                      page: 1,
                      ["apartment_name"]: this.state.apartment_name
                        ? filter["apartment_name"]
                        : undefined,
                      ["number"]: this.state.number
                        ? filter["number"]
                        : undefined,
                      ["code"]: this.state.code ? filter["code"] : undefined,
                      ["resident_user_name"]: this.state.resident_user_name
                        ? filter["resident_user_name"]
                        : undefined,
                    })}`
                  );

                  this.setState({
                    ...this.state,
                    apartment_name: "",
                    number: "",
                    code: "",
                    resident_user_name: "",
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
                  this.setState({
                    ...this.state,
                    apartment_name: "",
                    number: "",
                    code: "",
                    resident_user_name: "",
                  });
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            <WithRole roles={[config.ALL_ROLE_NAME.CARD_MANAGEMENT_CREATE]}>
              <Tooltip
                title={<FormattedMessage {...messages.addCombineCard} />}
              >
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => {
                    this.setState({
                      visibleAddCard: true,
                      currentEdit: undefined,
                    });
                  }}
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </WithRole>
            <WithRole roles={[config.ALL_ROLE_NAME.CARD_MANAGEMENT_IMPORT]}>
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
            <WithRole roles={[config.ALL_ROLE_NAME.CARD_MANAGEMENT_IMPORT]}>
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
                          .downloadTemplateCombineCard({})
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
              <WithRole roles={[config.ALL_ROLE_NAME.CARD_MANAGEMENT_EXPORT]}>
                <Tooltip title={<FormattedMessage {...messages.exportData} />}>
                  <Button
                    //style={{ position: "absolute", right: 0 }}
                    onClick={() => {
                      this.setState(
                        {
                          exporting: true,
                        },
                        () => {
                          window.connection
                            .exportCombineCardData({ ...params })
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
              <WithRole roles={[config.ALL_ROLE_NAME.CARD_MANAGEMENT_EXPORT]}>
                <Tooltip title={<FormattedMessage {...messages.exportData} />}>
                  <Button
                    onClick={() => {
                      this.setState(
                        {
                          exporting: true,
                        },
                        () => {
                          window.connection
                            .exportCombineCardData({ ...params })
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
            locale={{ emptyText: "emptyDataText" }}
            bordered
            scroll={{ x: 1366 }}
            pagination={{
              locale: this.props.language === "vi" ? "vi_VN" : "en_GB",
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total) => (
                <FormattedMessage
                  {...messages.totalCombineCard}
                  values={{ total }}
                />
              ),
            }}
            onChange={this.handleTableChange}
            onRow={(record) => {
              return {
                onClick: () => {
                  auth_group.checkRole([
                    ALL_ROLE_NAME.CARD_MANAGEMENT_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/merge-card/detail/${record.id}`,
                      {
                        record: {
                          ...record,
                        },
                      }
                    );
                },
              };
            }}
          />
          <ModalEditCombineCard
            visible={this.state.visibleAddCard}
            setState={this.setState.bind(this)}
            creating={combineCardList.updating}
            currentEdit={this.state.currentEdit}
            handlerUpdate={(values) => {
              this.props.dispatch(
                updateCombineCardAction({
                  ...values,
                  //buildingCluster: buildingCluster,
                  id: this.state.currentEdit.id,
                  callback: () => {
                    this.setState({ visibleAddCard: false });
                    this.reload(this.props.location.search);
                  },
                })
              );
            }}
            handlerAdd={(values) => {
              this.props.dispatch(
                createCombineCardAction({
                  ...values,
                  callback: () => {
                    this.setState({ visibleAddCard: false });
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

CombineCardList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  combineCardList: makeSelectCombineCardList(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
  buildingCluster: selectBuildingCluster(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "combineCardList", reducer });
const withSaga = injectSaga({ key: "combineCardList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(CombineCardList));
