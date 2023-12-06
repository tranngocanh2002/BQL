/**
 *
 * MaintainPage
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
  DatePicker,
  Form,
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Table,
  Tooltip,
} from "antd";
import { getFullLinkImage } from "connection";
import moment from "moment";
import queryString from "query-string";
import { selectAuthGroup } from "redux/selectors";
import config from "utils/config";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { globalStyles } from "../../../utils/constants";
import messages from "../../MaintainDevices/messages";
import {
  defaultAction,
  deleteMaintainDevicesAction,
  fetchAllMaintainDevicesAction,
  fetchAllMaintainScheduleAction,
  updateMaintainDevicesScheduleAction,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectMaintainList from "./selectors";
import WithRole from "components/WithRole";

const confirm = Modal.confirm;

const colLayout2 = {
  md: 4,
  xxl: 3,
};

const topCol3 = {
  sm: 8,
  md: 8,
  lg: 7,
  xl: 6,
  xxl: 4,
};

const topCol4 = {
  sm: 8,
  md: 4,
  lg: 5,
  xl: 5,
  xxl: 3,
};
const CollectionCreateForm = Form.create({ name: "form_in_modal" })(
  // eslint-disable-next-line
  class extends React.Component {
    render() {
      const { visible, onCancel, onSelectDate, form, formatMessage } =
        this.props;
      const { getFieldDecorator } = form;

      return (
        <Modal
          visible={visible}
          title={formatMessage(messages.maintenanceConfirmation)}
          okText={formatMessage(messages.confirm)}
          okType="danger"
          cancelText={formatMessage(messages.cancelText)}
          onCancel={onCancel}
          onOk={onSelectDate}
          //  destroyOnClose={true}
        >
          <Form layout="inline">
            <Form.Item
              label={formatMessage(messages.chooseDayPlaceholder)}
              labelCol={{ md: 10 }}
              wrapperCol={{ md: 12, lg: 12, xl: 12 }}
            >
              {getFieldDecorator("maintenance_time_last", {
                rules: [
                  {
                    required: true,
                    message: formatMessage(messages.errorEmpty, {
                      field: formatMessage(messages.chooseDayPlaceholder),
                    }),
                  },
                ],
              })(
                <DatePicker
                  placeholder={formatMessage(messages.startDate)}
                  style={{ width: "100%" }}
                  format="DD/MM/YYYY"
                  disabledDate={(current) => {
                    return (
                      current && current.isAfter(moment().subtract(0, "day"))
                    );
                  }}
                />
              )}
            </Form.Item>
          </Form>
        </Modal>
      );
    }
  }
);
/* eslint-disable react/prefer-stateless-function */
export class MaintainList extends React.PureComponent {
  constructor(props) {
    super(props);
    let pos = (props.location.state || {}).pos;

    this.state = {
      // codeD: "",
      // nameD: "",
      visible: false,
      current: 1,
      keyword: "",
      currentEdit: undefined,
      filter: {
        start_time: moment().unix(),
        end_time: moment().add(2, "months").unix(),
        sort: "maintenance_time_next",
        code: "",
        name: "",
      },
      firstTime: true,
      downloading: false,
      exporting: false,
      columnType:
        pos ||
        (this.props.auth_group.checkRole([
          config.ALL_ROLE_NAME
            .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_LIST_SCHEDULE,
        ])
          ? 0
          : 1),
      selected: [],
    };
  }
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
      this.props.MaintainList.importing != nextProps.MaintainList.importing &&
      !nextProps.MaintainList.importing
    ) {
      this.props.history.push(
        `/main/maintain/list?${queryString.stringify({
          ...this.state.filter,
          page: 1,
        })}`
      );
    }
  }

  _onDelete = (record) => {
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.deleteModalTitle),
      okText: this.props.intl.formatMessage(messages.okText),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancelText),
      onOk: () => {
        this.props.dispatch(
          deleteMaintainDevicesAction({
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
            return window.connection.importMaintainList({
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

  reload = (search, reset) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
      if (!params.sort) {
        params.sort =
          this.state.columnType === 0 ? "maintenance_time_next" : "-created_at";
      }
      if (!params.start_time) {
        params.start_time = moment().unix();
        params.end_time = moment().add(2, "months").unix();
      }
    } catch (error) {
      params.page = 1;
    }

    params.keyword = params.keyword || "";

    this.setState(
      {
        current: params.page,
        keyword: params.keyword,
        filter: reset ? {} : params,
        firstTime: false,
      },
      () => {
        if (!reset) {
          if (this.state.columnType === 1) {
            this.props.dispatch(fetchAllMaintainDevicesAction(params));
          } else {
            this.props.dispatch(fetchAllMaintainScheduleAction(params));
          }
        }
        reset &&
          this.props.history.push(
            `/main/maintain/list?${queryString.stringify({
              page: 1,
              sort:
                this.state.columnType === 0
                  ? "maintenance_time_next"
                  : "-created_at",
              start_time: moment().unix(),
              end_time: moment().add(2, "months").unix(),
            })}`
          );
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.dispatch(
        this.state.columnType === 1
          ? fetchAllMaintainDevicesAction({
              keyword: this.state.keyword,
              ...this.state.filter,
              page: this.state.current,
            })
          : fetchAllMaintainScheduleAction({
              keyword: this.state.keyword,
              ...this.state.filter,
              page: this.state.current,
            })
      );
    });
  };

  updateStatus = (id) => {
    this.setState(
      {
        columnType: id,
        firstTime: false,
      },
      () => {
        this.reload(
          {
            page: 1,
            sort:
              this.state.columnType === 0
                ? "maintenance_time_next"
                : "-created_at",
            start_time: moment().unix(),
            end_time: moment().add(2, "months").unix(),
          },
          true
        );
        this.props.history.push(
          `/main/maintain/list?${queryString.stringify({
            ...this.state.filter,
            page: 1,
          })}`
        );
      }
    );
  };

  showModal = () => {
    this.setState({
      visible: true,
    });
  };

  handleCancel = () => {
    this.setState({ visible: false });
  };

  handleSelectDate = () => {
    const { form } = this.formRef.props;
    form.validateFields((err, values) => {
      if (err) {
        return;
      }

      this.props.dispatch(
        updateMaintainDevicesScheduleAction({
          id_array: this.state.selected.map((item) => item.id),
          maintenance_time_last: values.maintenance_time_last.unix(),
          callback: () => {
            this.reload(this.props.location.search);
          },
        })
      );
      form.resetFields();
      this.setState({ visible: false, selected: [] });
      this.reload(this.props.location.search);
    });
  };

  saveFormRef = (formRef) => {
    this.formRef = formRef;
  };
  _onEdit = (record) => {
    this.props.history.push(`/main/maintain/edit/${record.id}`, { record });
  };
  render() {
    const { MaintainList, auth_group } = this.props;
    const { loading, data, totalPage, deleting, loading2, data2, totalPage2 } =
      MaintainList;
    const formatMessage = this.props.intl.formatMessage;
    const { current, filter, exporting, columnType } = this.state;
    const { search } = location;

    let params = queryString.parse(search);
    // if (!data) {
    //   return (
    //     <Page loading={loading} inner={!loading}>
    //       <Exception
    //         type="404"
    //         desc={this.props.intl.formatMessage(messages.emptyData)}
    //         actions={
    //           <Button
    //             type="primary"
    //             onClick={() => this.props.history.push("/main/home")}
    //           >
    //             {this.props.intl.formatMessage(messages.btnBack)}
    //           </Button>
    //         }
    //       />
    //     </Page>
    //   );
    // }
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "index",
        key: "index",
        fixed: "left",
        width: 140,
        render: (text, record, index) => <span>{index + 1}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.deviceCode)}
          </span>
        ),
        dataIndex: "code",
        key: "code",
        render: (text) => <span>{text}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.deviceName)}
          </span>
        ),
        dataIndex: "name",
        key: "name",
        render: (text) => <span>{text}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.deviceType)}
          </span>
        ),
        dataIndex: "type",
        key: "type",
        render: (text) => (
          <span>
            {text === 0
              ? formatMessage(messages.computer)
              : text === 1
              ? formatMessage(messages.fan)
              : text === 2
              ? formatMessage(messages.camera)
              : text === 3
              ? formatMessage(messages.lamp)
              : formatMessage(messages.elevator)}
          </span>
        ),
      },

      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.maintenanceStartDate)}
          </span>
        ),
        dataIndex: "maintenance_time_start",
        key: "maintenance_time_start",
        render: (text) => <span>{moment.unix(text).format("DD/MM/YYYY")}</span>,
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
            {text === 0
              ? formatMessage(messages.inActive)
              : formatMessage(messages.active)}
          </span>
        ),
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.option)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        width: 200,
        fixed: "right",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title={formatMessage(messages.deviceDetails)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME
                      .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_DETAIL,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();

                  auth_group.checkRole([
                    config.ALL_ROLE_NAME
                      .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/maintain/detail/${record.id}`,
                      {
                        record: {
                          ...record,
                        },
                      }
                    );
                }}
              >
                <i className="fa fa-eye" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={formatMessage(messages.deviceEditing)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME
                      .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_UPDATE,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME
                      .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_UPDATE,
                  ]) && this._onEdit(record);
                }}
              >
                <i className="fa fa-edit" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={formatMessage(messages.deleteDevice)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME
                      .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_DELETE,
                  ])
                    ? globalStyles.row2
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME
                      .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_DELETE,
                  ]) && this._onDelete(record);
                }}
              >
                <i className="fa fa-trash" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];
    const columns2 = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        fixed: "left",
        width: 140,
        render: (text, record, index) => {
          let today = moment().format("YYYY-MM-DD");
          let term = moment
            .unix(record.maintenance_time_next)
            .format("YYYY-MM-DD");
          return (
            <span style={term < today ? { color: "red" } : {}}>
              {index + 1}
            </span>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.deviceCode)}
          </span>
        ),

        dataIndex: "code",
        key: "code",
        render: (text, record) => {
          let today = moment().format("YYYY-MM-DD");
          let term = moment
            .unix(record.maintenance_time_next)
            .format("YYYY-MM-DD");
          return (
            <span style={term < today ? { color: "red" } : {}}>{text}</span>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.deviceName)}
          </span>
        ),
        dataIndex: "name",
        key: "name",
        render: (text, record) => {
          let today = moment().format("YYYY-MM-DD");
          let term = moment
            .unix(record.maintenance_time_next)
            .format("YYYY-MM-DD");
          return (
            <span style={term < today ? { color: "red" } : {}}>{text}</span>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.deviceType)}
          </span>
        ),
        dataIndex: "type",
        key: "type",
        render: (text, record) => {
          let today = moment().format("YYYY-MM-DD");
          let term = moment
            .unix(record.maintenance_time_next)
            .format("YYYY-MM-DD");
          return (
            <span style={term < today ? { color: "red" } : {}}>
              {text === 0
                ? formatMessage(messages.computer)
                : text === 1
                ? formatMessage(messages.fan)
                : text === 2
                ? formatMessage(messages.camera)
                : text === 3
                ? formatMessage(messages.lamp)
                : formatMessage(messages.elevator)}
            </span>
          );
        },
      },

      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.lastMaintenanceDate)}
          </span>
        ),
        dataIndex: "maintenance_time_last ",
        key: "maintenance_time_last ",
        render: (text, record) => {
          let today = moment().format("YYYY-MM-DD");
          let term = moment
            .unix(record.maintenance_time_next)
            .format("YYYY-MM-DD");
          return (
            <span style={term < today ? { color: "red" } : {}}>
              {record.maintenance_time_last !== null
                ? moment.unix(record.maintenance_time_last).format("DD/MM/YYYY")
                : ""}
            </span>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.upcomingMaintenanceDay)}
          </span>
        ),
        dataIndex: "maintenance_time_next",
        key: "maintenance_time_next",
        render: (text, record) => {
          let today = moment().format("YYYY-MM-DD");
          let term = moment
            .unix(record.maintenance_time_next)
            .format("YYYY-MM-DD");
          return (
            <span style={term < today ? { color: "red" } : {}}>
              {record.maintenance_time_next !== null
                ? moment.unix(record.maintenance_time_next).format("DD/MM/YYYY")
                : ""}
            </span>
          );
        },
      },
    ];

    //const { tree } = buildingArea;
    // if (!auth_group.checkRole([config.ALL_ROLE_NAME.APARTMENT_CREATE_UPDATE])) {
    //   columns.splice(columns.length - 1, 1);
    // } if (detail === -1) {

    return (
      <Page inner className={styles.maintainPage}>
        <Row>
          <Col style={{ paddingBottom: 12 }}>
            {auth_group.checkRole([
              config.ALL_ROLE_NAME
                .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_LIST_SCHEDULE,
            ]) && (
              <Button
                style={{ borderRadius: 0 }}
                type={columnType === 0 ? "primary" : "ghost"}
                onClick={() => this.updateStatus(0)}
              >
                {formatMessage(messages.maintenanceSchedule)}
              </Button>
            )}
            {auth_group.checkRole([
              config.ALL_ROLE_NAME
                .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_LIST,
            ]) && (
              <Button
                style={{ borderRadius: 0 }}
                type={columnType === 1 ? "primary" : "ghost"}
                onClick={() => this.updateStatus(1)}
              >
                {formatMessage(messages.deviceList)}
              </Button>
            )}
          </Col>
          <Row style={{ paddingBottom: 16 }} gutter={[12, 12]} span={24}>
            <Col {...topCol4} style={{ paddingRight: 8, marginTop: 2 }}>
              <Input.Search
                value={filter.code}
                maxLength={255}
                placeholder={formatMessage(messages.deviceCode)}
                prefix={
                  <Tooltip title={formatMessage(messages.deviceCode)}>
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
                      code: e.target.value.replace(/\s+/g, " ").trim(),
                    },
                  });
                }}
              />
            </Col>
            <Col {...topCol4} style={{ paddingRight: 8, marginTop: 2 }}>
              <Input.Search
                value={filter.name}
                maxLength={255}
                placeholder={formatMessage(messages.deviceName)}
                prefix={
                  <Tooltip title={formatMessage(messages.deviceName)}>
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
                      name: e.target.value.replace(/\s+/g, " ").trim(),
                    },
                  });
                }}
              />
            </Col>

            <Col {...topCol4} style={{ paddingRight: 8, marginTop: 2 }}>
              <Select
                showSearch
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.deviceType)}
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
                        ["type"]: value,
                      },
                    },
                    () => {
                      // this.props.history.push(`/main/apartment/list?${queryString.stringify({
                      //     ...this.state.filter,
                      //     page: 1,
                      // })}`)
                    }
                  );
                }}
                allowClear
                value={filter["type"]}
              >
                <Select.Option value={"0"}>
                  {formatMessage(messages.computer)}
                </Select.Option>
                <Select.Option value={"1"}>
                  {formatMessage(messages.fan)}
                </Select.Option>
                <Select.Option value={"2"}>
                  {formatMessage(messages.camera)}
                </Select.Option>
                <Select.Option value={"3"}>
                  {formatMessage(messages.lamp)}
                </Select.Option>
                <Select.Option value={"4"}>
                  {formatMessage(messages.elevator)}
                </Select.Option>
              </Select>
            </Col>
            {columnType === 1 && (
              <>
                <Col {...colLayout2} style={{ paddingRight: 8, marginTop: 2 }}>
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
                          // this.props.history.push(`/main/apartment/list?${queryString.stringify({
                          //     ...this.state.filter,
                          //     page: 1,
                          // })}`)
                        }
                      );
                    }}
                    allowClear
                    value={filter["status"]}
                  >
                    <Select.Option value={"0"}>
                      <FormattedMessage {...messages.inActive} />
                    </Select.Option>
                    <Select.Option value={"1"}>
                      <FormattedMessage {...messages.active} />
                    </Select.Option>
                  </Select>
                </Col>
                <Col {...topCol4}>
                  <Button
                    type="primary"
                    style={{ marginTop: 2 }}
                    onClick={(e) => {
                      e.preventDefault();
                      this.props.history.push(
                        `/main/maintain/list?${queryString.stringify({
                          ...this.state.filter,
                          page: 1,
                          code: filter["code"] ? filter["code"] : undefined,
                          name: filter["name"] ? filter["name"] : undefined,
                        })}`
                      );
                      // this.setState({
                      //   ...this.state,
                      //   codeD: "",
                      //   nameD: "",
                      // });
                    }}
                  >
                    <FormattedMessage {...messages.search} />
                  </Button>
                </Col>
              </>
            )}
            {columnType === 0 && (
              <>
                <Col {...topCol3} style={{ paddingRight: 8, marginTop: 2 }}>
                  <DatePicker.RangePicker
                    placeholder={[
                      formatMessage(messages.fromDate),
                      formatMessage(messages.toDate),
                    ]}
                    style={{ width: "100%" }}
                    format="DD/MM/YYYY"
                    value={[
                      filter.start_time
                        ? moment.unix(filter.start_time)
                        : undefined,
                      filter.end_time
                        ? moment.unix(filter.end_time)
                        : undefined,
                    ]}
                    onChange={(value1) => {
                      this.setState({
                        filter: {
                          ...filter,
                          ["start_time"]: value1[0]
                            ? value1[0].unix()
                            : undefined,
                          ["end_time"]: value1[1]
                            ? value1[1].unix()
                            : undefined,
                        },
                      });
                    }}
                  />
                </Col>

                <Col {...topCol4}>
                  <Button
                    type="primary"
                    style={{ marginTop: 2 }}
                    onClick={(e) => {
                      e.preventDefault();
                      this.props.history.push(
                        `/main/maintain/list?${queryString.stringify({
                          ...this.state.filter,
                          page: 1,
                          ["code"]: this.state.codeD
                            ? this.state.codeD.trim().replace(/\s+/g, " ")
                            : filter["code"]
                            ? filter["code"]
                            : undefined,
                          ["name"]: this.state.nameD
                            ? this.state.nameD.trim().replace(/\s+/g, " ")
                            : filter["name"]
                            ? filter["name"]
                            : undefined,
                        })}`
                      );
                      this.setState({
                        ...this.state,
                        codeD: "",
                        nameD: "",
                      });
                    }}
                  >
                    <FormattedMessage {...messages.search} />
                  </Button>
                </Col>
              </>
            )}
          </Row>

          <Row style={{ paddingBottom: 16, paddingTop: 12 }} type="flex">
            <Tooltip title={formatMessage(messages.reloadPage)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={(e) => {
                  e.preventDefault();
                  this.reload(this.props.location.search, true);
                  this.setState({
                    ...this.state,
                    codeD: "",
                    nameD: "",
                  });
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            {columnType === 1 &&
              auth_group.checkRole([
                config.ALL_ROLE_NAME
                  .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_CREATE,
              ]) && (
                <Tooltip title={formatMessage(messages.addDevice)}>
                  <Button
                    style={{ marginRight: 10 }}
                    onClick={() => {
                      this.props.history.push("/main/maintain/add");
                    }}
                    icon="plus"
                    shape="circle"
                    size="large"
                  />
                </Tooltip>
              )}
            {columnType === 1 && (
              <WithRole
                roles={[
                  config.ALL_ROLE_NAME
                    .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_IMPORT,
                ]}
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
                            .downloadTemplateMaintainList({})
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
                    loading={this.state.downloading}
                    shape="circle"
                    size="large"
                  >
                    {!this.state.downloading && (
                      <i className="material-icons" style={{ fontSize: 14 }}>
                        cloud_download
                      </i>
                    )}
                  </Button>
                </Tooltip>
              </WithRole>
            )}
            {columnType === 0 &&
              auth_group.checkRole([
                config.ALL_ROLE_NAME
                  .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_CONFIRMATION,
              ]) && (
                <Row style={{ paddingTop: 4 }}>
                  <Button
                    type="primary"
                    disabled={this.state.selected.length == 0}
                    loading={loading}
                    onClick={() => {
                      this.showModal();
                    }}
                  >
                    {formatMessage(messages.maintenanceConfirmation)}
                  </Button>
                </Row>
              )}
            {auth_group.checkRole([
              config.ALL_ROLE_NAME
                .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_EXPORT,
            ]) && (
              <Tooltip title={formatMessage(messages.exportData)}>
                <Button
                  style={{ position: "absolute", right: 0 }}
                  onClick={() => {
                    this.setState(
                      {
                        exporting: true,
                      },
                      () => {
                        columnType === 0
                          ? params.start_time
                            ? window.connection
                                .exportMaintainScheduleList({ ...params })
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
                                })
                            : window.connection
                                .exportMaintainScheduleList({
                                  ...params,
                                  start_time: moment().unix(),
                                  end_time: moment().add(2, "months").unix(),
                                })
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
                                })
                          : window.connection
                              .exportMaintainDevicesList({ ...params })
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
            )}
          </Row>

          {columnType === 1 && (
            <>
              <Table
                rowKey="id"
                loading={loading || deleting}
                columns={columns}
                bordered
                dataSource={data}
                locale={{ emptyText: formatMessage(messages.emptyData) }}
                scroll={{ x: 1366 }}
                pagination={{
                  pageSize: 20,
                  total: totalPage,
                  current,
                  showTotal: (total) =>
                    formatMessage(messages.totalDevice, { total: total }),
                }}
                onChange={this.handleTableChange}
                onRow={(record) => {
                  return {
                    onClick: () => {
                      auth_group.checkRole([
                        config.ALL_ROLE_NAME
                          .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_DETAIL,
                      ]) &&
                        this.props.history.push(
                          `/main/maintain/detail/${record.id}`,
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
            </>
          )}
          {columnType === 0 && (
            <>
              <Table
                bordered
                rowKey="id"
                loading={loading2 || deleting}
                columns={columns2}
                dataSource={data2}
                locale={{ emptyText: formatMessage(messages.emptyData) }}
                scroll={{ x: 1366 }}
                pagination={{
                  pageSize: 20,
                  total: totalPage2,
                  current,
                  showTotal: (total) =>
                    formatMessage(messages.totalDevice, { total: total }),
                }}
                onChange={this.handleTableChange}
                rowSelection={{
                  onSelectAll: (select) => {
                    this.setState({
                      selected: select ? data2.filter((rr) => rr.id) : [],
                    });
                  },
                  selectedRowKeys: this.state.selected.map((ii) => ii.id),
                  onSelect: (record) =>
                    this.setState({
                      selected: this.state.selected.some(
                        (ii) => ii.id === record.id
                      )
                        ? this.state.selected.filter((ii) => ii.id != record.id)
                        : this.state.selected.concat([record]),
                    }),
                  // getCheckboxProps: (record) => ({
                  //   disabled: record.status != 1,
                  // }),
                }}
                // onRow={(record) => {
                //   return {
                //     onClick: () => {
                //       this.props.history.push(
                //         `/main/maintain/detail/${record.id}`,
                //         {
                //           record: {
                //             ...record,
                //           },
                //         }
                //       );
                //     },
                //   };
                // }}
              />
            </>
          )}
          <CollectionCreateForm
            formatMessage={formatMessage}
            wrappedComponentRef={this.saveFormRef}
            visible={this.state.visible}
            onCancel={this.handleCancel}
            onSelectDate={this.handleSelectDate}
          />
        </Row>
      </Page>
    );
  }
}

MaintainList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  MaintainList: makeSelectMaintainList(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "maintainList", reducer });
const withSaga = injectSaga({ key: "maintainList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(MaintainList));
