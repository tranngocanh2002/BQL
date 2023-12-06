/**
 *
 * InfoUsage
 *
 */

import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import {
  addInfo,
  defaultAction,
  deleteInfo,
  fetchApartment,
  fetchUsage,
  updateInfo,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectInfoUsage from "./selectors";

import {
  Button,
  Col,
  DatePicker,
  Modal,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import _ from "lodash";
import WithRole from "../../../../../components/WithRole";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { config } from "../../../../../utils";
import makeSelectElectricServiceContainer from "../selectors";

import queryString from "query-string";

import moment from "moment";
import { FormattedMessage, injectIntl } from "react-intl";
import { getFullLinkImage } from "../../../../../connection";
import { GLOBAL_COLOR } from "../../../../../utils/constants";
import messages from "../messages";
import ModalCreate from "./ModalCreate";
import styles from "./index.less";

const confirm = Modal.confirm;

/* eslint-disable react/prefer-stateless-function */
export class InfoUsage extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {
        sort: "-created_at",
      },
      downloading: false,
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
    this._onSearch("");
    this.reload(this.props.location.search);
  }

  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }

    if (
      this.props.infoUsage.success != nextProps.infoUsage.success &&
      nextProps.infoUsage.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }

    if (
      this.props.infoUsage.importing != nextProps.infoUsage.importing &&
      nextProps.infoUsage.importingSuccess
    ) {
      this.setState({
        visible: false,
      });
      if (this.state.current == 1) {
        this.reload(this.props.location.search);
      } else {
        this.props.history.push(
          `/main/service/detail/electric/usage?${queryString.stringify({
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
          fetchUsage({
            service_map_management_id:
              this.props.electricServiceContainer.data.id,
            ...(reset ? { page: 1, sort: "-created_at" } : params),
          })
        );
        reset &&
          this.props.history.push(
            `/main/service/detail/electric/usage?${queryString.stringify({
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
        `/main/service/detail/electric/usage?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
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

  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage({ ...messages.confirmDelete }),
      okText: this.props.intl.formatMessage({ ...messages.okText }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({ ...messages.cancelText }),
      onOk: () => {
        this.props.dispatch(
          deleteInfo({
            id: record.id,
          })
        );
      },
      onCancel() {},
    });
  };

  _onImport = () => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.titleImportUsage),
      okText: this.props.intl.formatMessage(messages.okText),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancelText),
      onOk: () => {
        window.modalImport.show(
          (url) => {
            return window.connection.importElectricInfoUsage({
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
    const { infoUsage, electricServiceContainer, auth_group, intl } =
      this.props;
    const { current, downloading } = this.state;
    const plhDate = intl.formatMessage({ ...messages.endDate });
    const columns = [
      {
        width: 100,
        align: "center",
        fixed: "left",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(0, infoUsage.loading ? current - 2 : current - 1) * 20 +
              index +
              1}
          </span>
        ),
      },
      {
        width: 250,
        fixed: "left",
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
        width: 150,
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.loaiTinhPhi} />
          </span>
        ),
        dataIndex: "service_electric_type_name",
        key: "service_electric_type_name",
      },
      {
        width: 150,
        align: "right",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.soChotCuoi} />
          </span>
        ),
        dataIndex: "end_index",
        key: "end_index",
        render: (text) => <strong>{text}</strong>,
      },
      {
        // width: 150,
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.startDate} />
          </span>
        ),
        align: "center",

        dataIndex: "start_date",
        key: "start_date",
        render: (text) => <span>{moment.unix(text).format("DD/MM/YYYY")}</span>,
      },
      {
        // width: 150,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.endDate} />
          </span>
        ),
        dataIndex: "end_date",
        key: "end_date",
        render: (text) => <span>{moment.unix(text).format("DD/MM/YYYY")}</span>,
      },
      {
        width: 150,
        fixed: "right",
        align: "center",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.action} />
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (_text, record) => (
          <Row type="flex" align="middle" justify="center">
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
            <Tooltip title={<FormattedMessage {...messages.delete} />}>
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
        <Row gutter={[24, 16]} style={{ marginBottom: 16 }} type="flex">
          <Col md={8} lg={6}>
            <Select
              style={{ width: "100%" }}
              loading={infoUsage.apartment.loading}
              showSearch
              placeholder={<FormattedMessage {...messages.plhProperty} />}
              optionFilterProp="children"
              notFoundContent={
                infoUsage.apartment.loading ? <Spin size="small" /> : null
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
              {infoUsage.apartment.items.map((gr) => {
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
          <Col md={8} lg={6}>
            <DatePicker
              format="DD/MM/YYYY"
              value={
                this.state.filter.end_date
                  ? moment.unix(this.state.filter.end_date)
                  : undefined
              }
              placeholder={plhDate}
              onChange={(value) => {
                this.setState(
                  {
                    filter: {
                      ...this.state.filter,
                      end_date: value ? value.startOf("day").unix() : undefined,
                    },
                  },
                  () => {
                    // this.props.history.push(`/main/service/detail/electric/usage?${queryString.stringify({
                    //   ...this.state.filter,
                    //   page: 1,
                    // })}`)
                  }
                );
              }}
              style={{ width: "100%" }}
            />
          </Col>
          <Col>
            <Button
              type="primary"
              onClick={() => {
                this.props.history.push(
                  `/main/service/detail/electric/usage?${queryString.stringify({
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
          <Row style={{ marginBottom: 16 }}>
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
            <Tooltip title={<FormattedMessage {...messages.add} />}>
              <Button
                style={{ marginRight: 10 }}
                onClick={() => {
                  this.setState({ visible: true, currentEdit: undefined });
                }}
                disabled={infoUsage.importing}
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
                        .downloadElectricTemplateInfoUsage({})
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
          </Row>
        </WithRole>
        <Table
          rowKey="id"
          loading={
            infoUsage.loading ||
            infoUsage.deleting ||
            infoUsage.importing ||
            infoUsage.approving
          }
          columns={columns}
          dataSource={infoUsage.data}
          scroll={{ x: 900 }}
          locale={{ emptyText: <FormattedMessage {...messages.noData} /> }}
          bordered
          pagination={{
            pageSize: 20,
            total: infoUsage.totalPage,
            current: this.state.current,
            showTotal: (total) => (
              <FormattedMessage
                {...messages.totalProperty}
                values={{ total }}
              />
            ),
          }}
          onChange={this.handleTableChange}
        />

        <ModalCreate
          infoUsage={infoUsage}
          visible={this.state.visible}
          setState={this.setState.bind(this)}
          dispatch={this.props.dispatch}
          currentEdit={this.state.currentEdit}
          addInfo={(payload) => {
            this.props.dispatch(
              addInfo({
                ...payload,
                service_map_management_id: electricServiceContainer.data.id,
              })
            );
          }}
          updateInfo={(payload) => {
            this.props.dispatch(
              updateInfo({
                ...payload,
                service_map_management_id: electricServiceContainer.data.id,
              })
            );
          }}
        />
      </Row>
    );
  }
}

const mapStateToProps = createStructuredSelector({
  infoUsage: makeSelectInfoUsage(),
  electricServiceContainer: makeSelectElectricServiceContainer(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "infoUsage", reducer });
const withSaga = injectSaga({ key: "infoUsage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(InfoUsage));
