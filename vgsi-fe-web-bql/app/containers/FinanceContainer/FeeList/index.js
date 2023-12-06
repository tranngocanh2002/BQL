/**
 *
 * FeeList
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import styles from "./index.less";

import {
  Button,
  Col,
  Icon,
  Modal,
  Popover,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
} from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import _ from "lodash";
import moment from "moment";
import queryString from "query-string";
import { injectIntl } from "react-intl";
import { Link } from "react-router-dom";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import { selectAuthGroup } from "../../../redux/selectors";
import { config, formatPrice } from "../../../utils";
import { globalStyles } from "../../../utils/constants";
import messages from "../messages";
import ModalEdit from "./ModalEdit";
import {
  defaultAction,
  deleteFeeAction,
  fetchAllFee,
  fetchApartmentAction,
  fetchBuildingAreaAction,
  fetchServiceMapAction,
  updatePayment,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectFeeList from "./selectors";
const confirm = Modal.confirm;
const { Option } = Select;
const col3 = {
  md: {
    span: 5,
  },
  lg: {
    span: 3,
  },
};

const col4 = {
  md: {
    span: 6,
  },
  lg: {
    span: 4,
  },
};

/* eslint-disable react/prefer-stateless-function */
export class FeeList extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      current: 1,
      filter: {
        sort: "-created_at",
      },
      collapse: false,
      currentEdit: undefined,
      visible: false,
      downloading: false,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
    this._onSearchBuilding = _.debounce(this.onSearchBuilding, 300);
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this.reload(this.props.location.search);
    this.props.dispatch(fetchApartmentAction());
    this.props.dispatch(fetchServiceMapAction());
    this.props.dispatch(fetchBuildingAreaAction());
  }

  // eslint-disable-next-line react/no-deprecated
  componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
    }
    if (
      this.props.feeList.updating != nextProps.feeList.updating &&
      nextProps.feeList.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }
  }

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState(
      {
        sort,
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/service/detail/fees?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

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
        filter: reset ? { sort: "-created_at" } : params,
      },
      () => {
        this.props.dispatch(
          fetchAllFee(reset ? { sort: "-created_at", page: 1 } : params)
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

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
  };
  onSearchBuilding = (keyword) => {
    this.props.dispatch(fetchBuildingAreaAction({ name: keyword }));
  };

  _onDelete = (record) => {
    if (record.status > 0) {
      Modal.info({
        title: this.props.intl.formatMessage(messages.deleteModalContent),
        onOk() {},
      });
      return;
    }
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.deleteContent),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          deleteFeeAction({
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
    if (record.status > 0) {
      Modal.info({
        title: this.props.intl.formatMessage(messages.deleteModalContent2),
        onOk() {},
      });
      return;
    }
    this.setState(
      {
        currentEdit: record,
      },
      () => {
        this.setState({ visible: true });
      }
    );
  };

  togglerContent = () => {
    const { collapse } = this.state;
    this.setState({ collapse: !collapse });
  };

  render() {
    const { feeList, dispatch, auth_group, location } = this.props;
    const {
      loading,
      items,
      apartments,
      services,
      updating,
      buildingArea,
      total_count,
    } = feeList;
    const { current, downloading } = this.state;
    const { search } = location;
    let params = queryString.parse(search);
    let { formatMessage } = this.props.intl;

    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        width: 50,
        render: (text, record, index) => (
          <span>
            {Math.max(0, loading ? current - 2 : current - 1) * 20 + index + 1}
          </span>
        ),
      },
      {
        width: 40,
        title: <span />,
        dataIndex: "info",
        key: "info",
        render: (_text, record) => {
          return (
            <Popover
              content={
                <span style={{ whiteSpace: "pre-wrap" }}>
                  {this.props.language === "en"
                    ? record.description_en
                    : record.description}
                </span>
              }
              title={formatMessage(messages.detail)}
            >
              <Icon type="info-circle" />
            </Popover>
          );
        },
      },
      {
        width: 40,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.month)}
          </span>
        ),
        dataIndex: "fee_month",
        align: "center",
        key: "fee_month",
        render: (text, record) => (
          <span>{moment.unix(record.fee_of_month).format("MM")}</span>
        ),
      },
      {
        width: 50,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.year)}
          </span>
        ),
        dataIndex: "fee_year",
        key: "fee_year",
        render: (text, record) => (
          <span>{moment.unix(record.fee_of_month).format("YYYY")}</span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.property)}
          </span>
        ),
        width: 160,
        dataIndex: "apartment_name",
        key: "apartment_name",
        render: (text, record) => (
          <span>
            {`${record.apartment_name} (${record.apartment_parent_path})`}
          </span>
        ),
      },
      {
        width: 160,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.customer)}
          </span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        width: 120,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.serviceType)}
          </span>
        ),
        dataIndex:
          this.props.language === "en"
            ? "service_map_management_service_name_en"
            : "service_map_management_service_name",
        key:
          this.props.language === "en"
            ? "service_map_management_service_name_en"
            : "service_map_management_service_name",
      },
      {
        width: 110,
        align: "right",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.receivables)}
          </span>
        ),
        dataIndex: "price",
        key: "price",
        render: (text, record) => (
          <span>{`${formatPrice(record.price)} ${formatMessage(
            messages.vnd2
          )}`}</span>
        ),
      },
      {
        width: 110,
        align: "right",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.collected)}
          </span>
        ),
        dataIndex: "money_collected",
        key: "money_collected",
        render: (text, record) => (
          <span>{`${formatPrice(record.money_collected)} ${formatMessage(
            messages.vnd2
          )}`}</span>
        ),
      },
      {
        width: 120,
        align: "right",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.haveToPay)}
          </span>
        ),
        dataIndex: "more_money_collecte",
        key: "more_money_collecte",
        render: (text, record) => (
          <span>{`${formatPrice(record.more_money_collecte)} ${formatMessage(
            messages.vnd2
          )}`}</span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.createAt)}
          </span>
        ),
        dataIndex: "created_at",
        key: "created_at",
        render: (text, record) => (
          <span>{moment.unix(record.created_at).format("DD/MM/YYYY")}</span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.approvedBy)}
          </span>
        ),
        dataIndex: "approved_by_name",
        key: "approved_by_name",
      },
      {
        width: 230,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.receipts)}
          </span>
        ),
        dataIndex: "service_bills",
        key: "service_bills",
        render: (text, record) => {
          return (
            !!record.service_bills &&
            record.service_bills.map((num) => {
              return (
                <Row key={num.number} style={{ marginTop: 4 }}>
                  <Link
                    to={`/main/finance/bills/detail/${num.id}`}
                    className={
                      num.status == 10
                        ? "luci-status-primary"
                        : num.status == 1
                        ? "luci-status-success"
                        : num.status == 2
                        ? "luci-status-danger"
                        : "luci-status-warning"
                    }
                  >
                    {num.number}
                  </Link>
                </Row>
              );
            })
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.status)}
          </span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text) => (
          <span>
            {text == 0
              ? formatMessage(messages.unpaid)
              : formatMessage(messages.paid)}
          </span>
        ),
      },
      {
        fixed: "right",
        width: 150,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (_text, record) => {
          if (record.service_bill_code) return null;
          return (
            <Row type="flex" align="middle" justify="center">
              <Tooltip title={formatMessage(messages.edit)}>
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
              &ensp;&ensp; | &ensp;&ensp;
              <Tooltip title={formatMessage(messages.delete)}>
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
          );
        },
      },
    ];

    const { filter } = this.state;
    return (
      <>
        <Page style={{ minHeight: 10, marginBottom: 16 }}>
          <Row style={{ marginTop: 30 }}>
            <Col span={8}>
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16, color: "#909090" }}>
                  {formatMessage(messages.totalFeePayable)}
                </span>
                <br />
                {!(loading || !total_count) && (
                  <span
                    style={{ fontSize: 24, fontWeight: "bold" }}
                  >{`${formatPrice(total_count.total_price)} Đ`}</span>
                )}
                {(loading || !total_count) && <Spin style={{ marginTop: 8 }} />}
              </Row>
            </Col>
            <Col
              span={8}
              style={{
                borderLeft: "1px solid rgba(210, 210, 210, 0.5)",
                borderRight: "1px solid rgba(210, 210, 210, 0.5)",
              }}
            >
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16, color: "#909090" }}>
                  {formatMessage(messages.totalFeeCollected)}
                </span>
                <br />
                {!(loading || !total_count) && (
                  <span
                    style={{
                      fontSize: 24,
                      fontWeight: "bold",
                      color: "#3EA671",
                    }}
                  >{`${formatPrice(
                    total_count.total_money_collected
                  )} Đ`}</span>
                )}
                {(loading || !total_count) && <Spin style={{ marginTop: 8 }} />}
              </Row>
            </Col>
            <Col span={8}>
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 16, color: "#909090" }}>
                  {formatMessage(messages.totalFeeLeft)}
                </span>
                <br />
                {!(loading || !total_count) && (
                  <span
                    style={{
                      fontSize: 24,
                      fontWeight: "bold",
                      color: "#D85357",
                    }}
                  >{`${formatPrice(
                    total_count.total_more_money_collecte
                  )} Đ`}</span>
                )}
                {(loading || !total_count) && <Spin style={{ marginTop: 8 }} />}
              </Row>
            </Col>
          </Row>
        </Page>
        <Page className="feeListPage" inner>
          <div>
            <Row gutter={[24, 16]} style={{ paddingBottom: 24 }}>
              <Col {...col3} style={{ paddingRight: 0 }}>
                <Select
                  style={{ width: "100%" }}
                  placeholder={formatMessage(messages.month)}
                  onChange={(value) => {
                    this.setState({
                      filter: {
                        ...filter,
                        month: value,
                      },
                    });
                  }}
                  allowClear
                  value={
                    filter.month != undefined ? String(filter.month) : undefined
                  }
                >
                  {_.range(1, 13, 1).map((rr) => {
                    return (
                      <Select.Option
                        value={`${rr}`}
                        key={`month-${rr}`}
                      >{`${formatMessage(
                        messages.month
                      )} ${rr}`}</Select.Option>
                    );
                  })}
                </Select>
              </Col>
              <Col {...col3} style={{ paddingRight: 0 }}>
                <Select
                  style={{ width: "100%" }}
                  placeholder={formatMessage(messages.year)}
                  onChange={(value) => {
                    this.setState({
                      filter: {
                        ...filter,
                        year: value,
                      },
                    });
                  }}
                  allowClear
                  value={filter.year}
                >
                  {_.range(moment().year() + 5, moment().year() - 15, -1).map(
                    (rr) => {
                      return (
                        <Select.Option
                          value={rr}
                          key={`year-${rr}`}
                        >{`${rr}`}</Select.Option>
                      );
                    }
                  )}
                </Select>
              </Col>
              <Col {...col4} style={{ paddingRight: 0 }}>
                <Select
                  style={{ width: "100%" }}
                  loading={apartments.loading}
                  showSearch
                  placeholder={formatMessage(messages.choseProperty)}
                  optionFilterProp="children"
                  notFoundContent={
                    apartments.loading ? <Spin size="small" /> : null
                  }
                  onSearch={this._onSearch}
                  onChange={(value, opt) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["apartment_id"]: value,
                      },
                    });
                    if (!opt) {
                      this._onSearch("");
                    }
                  }}
                  allowClear
                  value={filter["apartment_id"]}
                >
                  {apartments.lst.map((gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${gr.name} (${gr.parent_path})`}</Select.Option>
                    );
                  })}
                </Select>
              </Col>
              <Col {...col3} style={{ paddingRight: 0 }}>
                <Select
                  style={{ width: "100%" }}
                  loading={buildingArea.loading}
                  showSearch
                  placeholder={formatMessage(messages.choseAddress)}
                  optionFilterProp="children"
                  notFoundContent={
                    buildingArea.loading ? <Spin size="small" /> : null
                  }
                  onSearch={this._onSearchBuilding}
                  onChange={(value, opt) => {
                    this.setState({
                      filter: {
                        ...filter,
                        ["building_area_id"]: value,
                      },
                    });
                    if (!opt) {
                      this._onSearchBuilding("");
                    }
                  }}
                  allowClear
                  value={filter["building_area_id"]}
                >
                  {buildingArea.lst.map((gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${gr.parent_path} / ${gr.name}`}</Select.Option>
                    );
                  })}
                </Select>
              </Col>
              <Col {...col4} style={{ paddingRight: 0 }}>
                <Select
                  style={{ width: "100%" }}
                  loading={services.loading}
                  showSearch
                  placeholder={formatMessage(messages.choseService)}
                  optionFilterProp="children"
                  notFoundContent={
                    services.loading ? <Spin size="small" /> : null
                  }
                  onChange={(value) => {
                    this.setState(
                      {
                        filter: {
                          ...filter,
                          ["service_map_management_id"]: value,
                        },
                      },
                      () => {
                        // this.props.history.push(`/main/finance/fees?${queryString.stringify({
                        //   ...this.state.filter,
                        //   page: 1,
                        // })}`)
                      }
                    );
                  }}
                  allowClear
                  value={filter["service_map_management_id"]}
                >
                  {services.lst.map((gr) => {
                    return (
                      <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>
                        {this.props.language === "en"
                          ? gr.service_name_en
                          : gr.service_name}
                      </Select.Option>
                    );
                  })}
                </Select>
              </Col>
              <Col {...col3} style={{ paddingRight: 0 }}>
                <Select
                  showSearch
                  style={{ width: "100%" }}
                  placeholder={formatMessage(messages.status)}
                  optionFilterProp="children"
                  filterOption={(input, option) =>
                    option.props.children
                      .toLowerCase()
                      .indexOf(input.toLowerCase()) >= 0
                  }
                  onChange={(value) => {
                    this.setState(
                      {
                        filter: {
                          ...filter,
                          ["status"]: value,
                        },
                      },
                      () => {
                        // this.props.history.push(`/main/finance/fees?${queryString.stringify({
                        //   ...this.state.filter,
                        //   page: 1,
                        // })}`)
                      }
                    );
                  }}
                  allowClear
                  value={filter["status"]}
                >
                  <Option value="0">{formatMessage(messages.unpaid)}</Option>
                  <Option value="1">{formatMessage(messages.paid)}</Option>
                  {/* <Option value="-1">Khách</Option> */}
                </Select>
              </Col>
              <Col {...col4}>
                <Button
                  block
                  type="primary"
                  onClick={() => {
                    this.props.history.push(
                      `/main/service/detail/fees?${queryString.stringify({
                        ...this.state.filter,
                        page: 1,
                      })}`
                    );
                  }}
                >
                  {formatMessage(messages.search)}
                </Button>
              </Col>
              <ModalEdit
                formatMessage={formatMessage}
                language={this.props.language}
                visible={this.state.visible}
                setState={this.setState.bind(this)}
                updating={updating}
                dispatch={dispatch}
                apartments={apartments}
                updatePayment={(payload) => {
                  this.props.dispatch(
                    updatePayment({
                      ...payload,
                      id: this.state.currentEdit.id,
                      service_map_management_id:
                        this.state.currentEdit.service_map_management_id,
                    })
                  );
                }}
                currentEdit={this.state.currentEdit}
              />
            </Row>
            <Row style={{ paddingBottom: 16 }}>
              <Tooltip title={formatMessage(messages.refresh)}>
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
              <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_CREATE_BILL]}>
                <Tooltip title={formatMessage(messages.fee)}>
                  <Button
                    disabled={
                      !auth_group.checkRole([
                        config.ALL_ROLE_NAME.FINANCE_CREATE_BILL,
                      ])
                    }
                    shape="circle-outline"
                    style={{ padding: 0, marginRight: 10 }}
                    onClick={() => {
                      this.props.history.push("/main/finance/reception");
                    }}
                    icon="plus"
                    size="large"
                  />
                </Tooltip>
              </WithRole>
              <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_ALL_FEE_MANAGER]}>
                <Tooltip title={formatMessage(messages.export)}>
                  <Button
                    style={{ position: "absolute", right: 0 }}
                    onClick={() => {
                      this.setState(
                        {
                          downloading: true,
                        },
                        () => {
                          window.connection
                            .exportFinanceFeeData({ ...params })
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
                      <i
                        className="material-icons"
                        style={{
                          fontSize: 18,
                          display: "flex",
                          justifyContent: "center",
                        }}
                      >
                        login
                      </i>
                    )}
                  </Button>
                </Tooltip>
              </WithRole>
            </Row>
            <Row gutter={24}>
              <Col>
                <Table
                  rowKey="id"
                  loading={feeList.loading}
                  columns={columns}
                  dataSource={items}
                  locale={{ emptyText: formatMessage(messages.emptyData) }}
                  bordered
                  scroll={{ x: 1366 }}
                  onChange={this.handleTableChange}
                  pagination={{
                    pageSize: 20,
                    total: feeList.totalPage,
                    current: this.state.current,
                    showTotal: (total) =>
                      `${formatMessage(
                        messages.total
                      )} ${total} ${formatMessage(
                        messages.bill
                      ).toLowerCase()}`,
                  }}
                  // expandedRowRender={record => {
                  //   let footer = "";
                  //   if (!record.description) {
                  //     return;
                  //   }
                  //   return <List
                  //     size="small"
                  //     footer={footer}
                  //     bordered
                  //     dataSource={[{ content: record.description }]}
                  //     renderItem={item => (
                  //       <List.Item>
                  //         <List.Item.Meta
                  //           description={<p style={{ whiteSpace: "pre-line" }}>{item.content}</p>}
                  //         />
                  //       </List.Item>
                  //     )}
                  //   />
                  // }}
                />
              </Col>
            </Row>
            {/* <Drawer
              visible={collapse}
              width={500}
              onClose={this.togglerContent}
              placement="right"
              handler={
                <div className={styles.handle} onClick={this.togglerContent}>
                  <Icon
                    type={collapse ? "close" : "dashboard"}
                    style={{
                      color: "#fff",
                      fontSize: 20
                    }}
                  />
                </div>
              }
              style={{
                zIndex: 999
              }}
            >
              <PageHeader
                backIcon={false}
                title="Biểu đồ phí dịch vụ"
                extra={[
                  <MonthPicker key={'month-picker'} placeholder="Chọn tháng" />
                ]}

                footer={
                  <Tabs defaultActiveKey="1">
                    <TabPane tab="Tổng hợp phí" key="1" />
                    <TabPane tab="Tổng hợp thu" key="2" />
                  </Tabs>
                }
              />

            </Drawer> */}
          </div>
        </Page>
      </>
    );
  }
}

FeeList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  feeList: makeSelectFeeList(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "feeList", reducer });
const withSaga = injectSaga({ key: "feeList", saga });

export default compose(withReducer, withSaga, withConnect)(injectIntl(FeeList));
