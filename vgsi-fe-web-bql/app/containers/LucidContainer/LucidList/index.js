/**
 *
 * StaffList
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Modal, Row, Select, Table, Tooltip } from "antd";
import queryString from "query-string";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { selectAuthGroup } from "../../../redux/selectors";
import { config, notificationBar } from "../../../utils";
import {
  defaultAction,
  fetchAllLucid,
  fetchAllResident,
  fetchApartment,
  fetchVehicle,
} from "./actions";
import styles from "./index.less";
import messages from "./messages";
import "./modalstyle.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectLucidList from "./selectors";

const confirm = Modal.confirm;

import moment from "moment";
import LUCIDCard from "../../../components/LUCIDCard";
import WithRole from "../../../components/WithRole";
import { GLOBAL_COLOR } from "../../../utils/constants";
import {
  deleteResidentAction,
  fetchApartmentOfResidentAction,
} from "../../ResidentContainer/ResidentList/actions";

const { Option } = Select;
/* eslint-disable react/prefer-stateless-function */
export class LucidList extends React.PureComponent {
  state = {
    current: 1,
    currentEdit: undefined,
    visible: false,
    filter: {},
    downloading: false,
    loading: false,
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
  }

  reload = (search) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }
    this.setState({ current: params.page, filter: params }, () => {
      this.props.dispatch(fetchAllLucid(params));
    });
  };

  handleTableChange = (pagination, filters, sorter) => {
    console.log("pagination, filters, sorter", pagination, filters, sorter);
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/lucid/list?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  _onEdit = (record) => {
    console.log(record);
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
    console.log(this.state);
  };

  _onDelete = (record) => {
    confirm({
      autoFocusButton: null,
      title: `Bạn chắc chắn muốn xoá ${record.first_name} này ra khỏi căn hộ ${record.apartment_name}(${record.apartment_parent_path}) ?`,
      okText: "Đồng ý",
      okType: "danger",
      cancelText: "Huỷ",
      onOk: () => {
        this.props.dispatch(
          deleteResidentAction({
            apartment_id: record.apartment_id,
            resident_id: record.id,
            callback: () => {
              this.props.dispatch(
                fetchApartmentOfResidentAction({ resident_user_id: record.id })
              );
              this.reload(this.props.location.search);
            },
          })
        );
      },
      onCancel() {},
    });
  };

  _onSearchResident = (payload) => {
    this.props.dispatch(fetchAllResident(payload));
  };

  render() {
    const { auth_group, lucidList } = this.props;
    const { loading, data, totalPage, deleting, updating } = lucidList;
    const { current, filter, downloading } = this.state;
    let { formatMessage } = this.props.intl;
    const confirmText = formatMessage(messages.confirm);
    const confirmContentText = formatMessage(messages.confirmContent);
    const continueText = formatMessage(messages.continue);
    const totalText = formatMessage(messages.total);
    const columns = [
      {
        width: 50,
        align: "center",
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
            <FormattedMessage {...messages.cardNum} />
          </span>
        ),
        dataIndex: "number",
        key: "number",
        // render: (text, record) => <span>{text} ({record.apartment_parent_path})</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.apartment} />
          </span>
        ),
        dataIndex: "apartment_name",
        key: "apartment_name",
        render: (text, record) => {
          return (
            <>
              <span>{text}</span>
              <br />
              <span>({record.apartment_parent_path})</span>
            </>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.resident} />
          </span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
        render: (text, record) => {
          return (
            <>
              <span>{text}</span>
              <br />
              <span>({record.resident_user_phone})</span>
            </>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.service} />
          </span>
        ),
        dataIndex: "birthday",
        key: "birthday",
        render: (text, record) => {
          return (
            <ul>
              {(record.map_service || [])
                .filter((ss) => ss.type != 0)
                .map((ss) => {
                  return (
                    <li key={`ooo-${ss.id}`}>
                      {`${ss.type_name}`} (
                      <span>{ss.service_management_name}</span>)
                    </li>
                  );
                })}
            </ul>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.createAt} />
          </span>
        ),
        dataIndex: "created_at",
        key: "created_at",
        render: (text) => moment.unix(text).format("HH:mm DD/MM/YYYY"),
      },
      {
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
                <FormattedMessage {...messages.notActive} />
              </span>
            );
          }
          if (text == 1) {
            return (
              <span className="luci-status-primary">
                <FormattedMessage {...messages.inActive} />
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
        width: 120,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.action} />
          </span>
        ),
        dataIndex: "",
        fixed: "right",
        key: "x",
        render: (text, record, index) => {
          if (record.status == 0) {
            return (
              <Row type="flex" align="middle" justify="center">
                <Tooltip title={<FormattedMessage {...messages.active} />}>
                  <Row
                    type="flex"
                    align="middle"
                    style={{
                      color: GLOBAL_COLOR,
                      marginRight: 10,
                      cursor: "pointer",
                    }}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      this.setState({
                        card: record,
                        visibleCard: true,
                      });
                    }}
                  >
                    <i className="fa fa-check" style={{ fontSize: 18 }} />
                  </Row>
                </Tooltip>
                |
                <Tooltip title={<FormattedMessage {...messages.lock} />}>
                  <Row
                    type="flex"
                    align="middle"
                    style={{
                      color: "#F15A29",
                      marginLeft: 10,
                      cursor: "pointer",
                    }}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      Modal.confirm({
                        autoFocusButton: null,
                        title: confirmText,
                        content: confirmContentText,
                        okText: continueText,
                        onOk: () => {
                          window.connection
                            .lucidBlockCard({
                              ids: [record.id],
                            })
                            .then((res) => {
                              if (res.success) {
                                notificationBar(
                                  <FormattedMessage {...messages.lockSuccess} />
                                );
                                this.reload(this.props.location.search);
                              }
                            });
                        },
                      });
                    }}
                  >
                    <i className="fa fa-lock" style={{ fontSize: 18 }} />
                  </Row>
                </Tooltip>
              </Row>
            );
          }
          if (record.status == 1) {
            return (
              <Row type="flex" align="middle" justify="center">
                <Tooltip title={<FormattedMessage {...messages.edit} />}>
                  <Row
                    type="flex"
                    align="middle"
                    style={{
                      color: GLOBAL_COLOR,
                      marginRight: 10,
                      cursor: "pointer",
                    }}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      this.setState({
                        card: record,
                        visibleCard: true,
                      });
                    }}
                  >
                    <i className="fa fa-edit" style={{ fontSize: 18 }} />
                  </Row>
                </Tooltip>
                |
                <Tooltip title={<FormattedMessage {...messages.lock} />}>
                  <Row
                    type="flex"
                    align="middle"
                    style={{
                      color: "#F15A29",
                      marginLeft: 10,
                      cursor: "pointer",
                    }}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      Modal.confirm({
                        autoFocusButton: null,
                        title: confirmText,
                        content: confirmContentText,
                        okText: continueText,
                        onOk: () => {
                          window.connection
                            .lucidBlockCard({
                              ids: [record.id],
                            })
                            .then((res) => {
                              if (res.success) {
                                notificationBar(
                                  <FormattedMessage {...messages.lockSuccess} />
                                );
                                this.reload(this.props.location.search);
                              }
                            });
                        },
                      });
                    }}
                  >
                    <i className="fa fa-lock" style={{ fontSize: 18 }} />
                  </Row>
                </Tooltip>
              </Row>
            );
          }
          if (record.status == 2) {
            return (
              <Row type="flex" align="middle" justify="center">
                <Tooltip title={<FormattedMessage {...messages.active} />}>
                  <Row
                    type="flex"
                    align="middle"
                    style={{ color: GLOBAL_COLOR, cursor: "pointer" }}
                    onClick={(e) => {
                      e.preventDefault();
                      e.stopPropagation();
                      this.setState({
                        card: record,
                        visibleCard: true,
                      });
                    }}
                  >
                    <i className="fa fa-check" style={{ fontSize: 18 }} />
                  </Row>
                </Tooltip>
              </Row>
            );
          }
        },
      },
    ];

    if (!auth_group.checkRole([config.ALL_ROLE_NAME.RESIDENT_CREATE_UPDATE])) {
      columns.splice(columns.length - 1, 1);
    }

    return (
      <Page inner className={styles.lucidListPage}>
        <div>
          {/* <Row style={{ paddingBottom: 16 }} type='flex' align='middle' >
            <Col span={4} style={{ paddingRight: 8 }} >
              <Input.Search
                value={filter['phone'] || ''}
                placeholder="Tìm kiếm số điện thoại"
                prefix={
                  <Tooltip title="Tìm kiếm cư dân theo số điện thoại">
                    <Icon type="info-circle" style={{ color: 'rgba(0,0,0,.45)' }} />
                  </Tooltip>
                }
                onChange={e => {
                  this.setState({
                    filter: {
                      ...filter,
                      ['phone']: e.target.value
                    }
                  })
                }}
              />
            </Col>
            <Col span={4} style={{ paddingRight: 8 }} >
              <Input.Search
                value={filter['name'] || ''}
                placeholder="Tìm kiếm tên"
                prefix={
                  <Tooltip title="Tìm kiếm cư dân theo tên">
                    <Icon type="info-circle" style={{ color: 'rgba(0,0,0,.45)' }} />
                  </Tooltip>
                }
                onChange={e => {
                  this.setState({
                    filter: {
                      ...filter,
                      ['name']: e.target.value
                    }
                  })
                }}
              />
            </Col>
            <Col span={4} style={{ paddingRight: 8 }} >
              <Select
                showSearch
                style={{ width: '100%' }}
                placeholder="Vai trò"
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                }
                onChange={value => {
                  console.log('value', value)
                  this.setState({
                    filter: {
                      ...filter,
                      ['type']: value
                    }
                  })
                }}
                allowClear
                value={filter['type']}
              >
                {
                  config.TYPE_RESIDENT.map(gr => {
                    return <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>{`${gr.name}`}</Select.Option>
                  })
                }
              </Select>
            </Col>
            <Col span={4} style={{ paddingRight: 8 }} >
              <Input.Search
                value={filter['apartment_name'] || ''}
                placeholder="Căn hộ"
                prefix={
                  <Tooltip title="Tìm kiếm cư dân theo căn hộ">
                    <Icon type="info-circle" style={{ color: 'rgba(0,0,0,.45)' }} />
                  </Tooltip>
                }
                onChange={e => {
                  this.setState({
                    filter: {
                      ...filter,
                      ['apartment_name']: e.target.value
                    }
                  })
                }}
              />
            </Col>
            <Button type='primary' onClick={e => {
              this.props.history.push(`/main/lucid/list?${queryString.stringify({
                ...this.state.filter,
                page: 1,
              })}`)
            }} >
              Tìm kiếm
                </Button>
          </Row> */}
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={<FormattedMessage {...messages.reload} />}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={(e) => {
                  this.reload(this.props.location.search);
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            <WithRole roles={[config.ALL_ROLE_NAME.RESIDENT_CREATE_UPDATE]}>
              <Tooltip title={<FormattedMessage {...messages.addNew} />}>
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => {
                    this.setState({
                      card: null,
                      visibleCard: true,
                    });
                  }}
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </WithRole>
            {/* <WithRole roles={[config.ALL_ROLE_NAME.RESIDENT_CREATE_UPDATE]} >
              <Tooltip title='Import dữ liệu' >
                <Button style={{ marginRight: 10 }}
                  shape="circle"
                  size='large'
                  onClick={() => {
                    window.modalImport.show(url => {
                      return window.connection.importResident({
                        file_path: url,
                        is_validate: 0
                      })
                    }, () => {
                      this.reload(this.props.location.search)
                    })
                  }}
                >
                  <i className="material-icons" style={{ fontSize: 14 }} >
                    cloud_upload
                    </i>
                </Button>
              </Tooltip>
            </WithRole> */}
            {/* <WithRole roles={[config.ALL_ROLE_NAME.RESIDENT_CREATE_UPDATE]} >
              <Tooltip title='Tải file import mẫu' >
                <Button
                  onClick={() => {
                    this.setState({
                      downloading: true
                    }, () => {
                      window.connection.downloadTemplateResident({}).then(res => {
                        if (this._unmounted)
                          return
                        this.setState({
                          downloading: false
                        }, () => {
                          if (res.success) {
                            window.open(getFullLinkImage(res.data.file_path));
                          }
                        })
                      }).catch(e => {
                        if (this._unmounted)
                          return
                        this.setState({
                          downloading: false
                        })
                      })
                    })
                  }}
                  loading={downloading}
                  shape="circle"
                  size='large'
                >
                  {
                    !downloading && <i className="material-icons" style={{ fontSize: 14 }} >
                      cloud_download
                    </i>
                  }
                </Button>
              </Tooltip>
            </WithRole> */}
          </Row>
          <Table
            rowKey="id"
            loading={loading || deleting}
            columns={columns}
            dataSource={data}
            scroll={{ x: 1366 }}
            locale={{ emptyText: <FormattedMessage {...messages.empty} /> }}
            pagination={{
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total, range) => `${totalText} ${total}`,
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
          <Modal
            visible={this.state.visibleCard}
            footer={null}
            centered
            closable={false}
            bodyStyle={{
              backgroundColor: "transparent",
            }}
            style={{
              backgroundColor: "transparent",
            }}
            wrapClassName="modalcard"
            onCancel={() =>
              this.setState({ visibleCard: false, card: undefined })
            }
          >
            <LUCIDCard
              formatMessage={formatMessage}
              visibleCard={this.state.visibleCard}
              aparment={this.state.card ? undefined : lucidList.apartment}
              residents={this.state.card ? undefined : lucidList.resident}
              loading={this.state.loading}
              onCreate={(card) => {
                this.setState({ loading: true });
                window.connection
                  .lucidCreateCard({
                    apartment_id: card.apartment_id,
                    resident_user_id: card.resident.id,
                    number: card.creditCardNumber,
                    add_map_service: [
                      {
                        type: 0,
                        service_management_id: card.resident.id,
                      },
                    ].concat(
                      card.map_service
                        .filter((tt) => !!tt.service_management_id)
                        .map((tt) => {
                          return {
                            type: 1,
                            service_management_id: tt.service_management_id,
                          };
                        })
                    ),
                  })
                  .then((res) => {
                    if (res.success) {
                      notificationBar(
                        <FormattedMessage {...messages.createCardSuccess} />
                      );
                      this.setState({
                        visibleCard: false,
                      });
                      this.reload(this.props.location.search);
                    }
                    this.setState({ loading: false });
                  });
              }}
              searchResident={this._onSearchResident}
              searchApartment={(keyword) => {
                this.props.dispatch(
                  fetchApartment({
                    name: keyword,
                  })
                );
              }}
              card={this.state.card}
              approve={(card) => {
                this.setState({ loading: true });
                window.connection
                  .lucidApproveCard({
                    cards: [
                      {
                        id: card.id,
                        number: card.creditCardNumber,
                      },
                    ],
                  })
                  .then((res) => {
                    if (res.success) {
                      let serviceAdd = card.map_service.map((ser) => {
                        return {
                          type: ser.type,
                          service_management_id: ser.service_management_id,
                        };
                      });
                      window.connection.lucidUpdateCard({
                        id: card.id,
                        number: card.creditCardNumber,
                        apartment_id: this.state.card.apartment_id,
                        add_map_service: serviceAdd,
                        del_map_service: this.state.card.map_service
                          .filter((tt) => !!tt.service_management_id)
                          .map((ss) => ss.id),
                      });
                      notificationBar(
                        <FormattedMessage {...messages.activeCardSuccess} />
                      );
                      this.setState({
                        visibleCard: false,
                      });
                      this.props.dispatch(
                        fetchApartment({
                          name: "",
                        })
                      );
                      this.reload(this.props.location.search);
                    }
                    this.setState({ loading: false });
                  });
              }}
              onUpdate={(card) => {
                this.setState({ loading: true });
                let serviceAdd = card.map_service.map((ser) => {
                  return {
                    type: ser.type,
                    service_management_id: ser.service_management_id,
                  };
                });
                window.connection
                  .lucidUpdateCard({
                    id: card.id,
                    number: card.creditCardNumber,
                    apartment_id: this.state.card.apartment_id,
                    add_map_service: serviceAdd,
                    del_map_service: this.state.card.map_service
                      .filter((tt) => !!tt.service_management_id)
                      .map((ss) => ss.id),
                  })
                  .then((res) => {
                    if (res.success) {
                      notificationBar(
                        <FormattedMessage {...messages.updateSuccess} />
                      );
                      this.setState({
                        visibleCard: false,
                      });
                      this.reload(this.props.location.search);
                    }
                    this.setState({ loading: false });
                  });
              }}
              fetchVehicle={(apartment_id) => {
                this.props.dispatch(fetchVehicle({ apartment_id }));
              }}
              vehicle={lucidList.vehicle}
            />
          </Modal>
        </div>
      </Page>
    );
  }
}

LucidList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  lucidList: makeSelectLucidList(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "lucidList", reducer });
const withSaga = injectSaga({ key: "lucidList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(LucidList));
