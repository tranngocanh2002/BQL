/**
 *
 * FormList
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import messages from "./messages";

import {
  Button,
  Col,
  Form,
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Table,
  Tooltip,
} from "antd";
import moment from "moment";
import queryString from "query-string";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import {
  defaultAction,
  fetchAllFormAction,
  updateFormStatusAction,
} from "./actions";
import styles from "./index.less";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectFormList from "./selectors";
const confirm = Modal.confirm;

import TextArea from "antd/lib/input/TextArea";
import { selectAuthGroup } from "redux/selectors";
import { ALL_ROLE_NAME } from "utils/config";
import { globalStyles } from "utils/constants";

const topCol3 = {
  md: 8,
  lg: 6,
  xl: 5,
};

const CollectionCreateForm = Form.create({ name: "form_in_modal" })(
  // eslint-disable-next-line
  class extends React.Component {
    render() {
      const { visible, onCancel, onDecline, form, intl } = this.props;
      const { getFieldDecorator } = form;
      const reasonPlaceholderText = intl.formatMessage({
        ...messages.reasonPlaceholder,
      });
      return (
        <Modal
          visible={visible}
          title={<FormattedMessage {...messages.cancelFormContent} />}
          okText={<FormattedMessage {...messages.agree} />}
          okType="danger"
          cancelText={<FormattedMessage {...messages.cancel} />}
          onCancel={onCancel}
          onOk={onDecline}
          destroyOnClose={true}
        >
          <Form layout="vertical">
            <Form.Item style={{ paddingBottom: 0, marginBottom: 0 }}>
              {getFieldDecorator("reason", {
                rules: [
                  {
                    required: true,
                    message: <FormattedMessage {...messages.reasonRequest} />,
                  },
                ],
              })(
                <TextArea
                  placeholder={reasonPlaceholderText}
                  maxLength={200}
                  rows={4}
                  style={{ margin: 0 }}
                />
              )}
            </Form.Item>
          </Form>
        </Modal>
      );
    }
  }
);

// const WrappedNormalLoginForm = Form.create({ name: "myForm" })(NormalLoginForm);

/* eslint-disable react/prefer-stateless-function */
export class FormList extends React.PureComponent {
  state = {
    visible: false,
    current: 1,
    recordId: undefined,
    keyword: "",
    filter: {},
    address: "",
    houseHolder: "",
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
    this._unmounted = true;
  }

  componentDidMount() {
    this.reload(this.props.location.search);

    // this.props.dispatch(fetchAllFormType());
  }

  UNSAFE_componentWillReceiveProps(nextProps) {
    const { search } = nextProps.location;
    if (this.props.location.search != search) {
      this.reload(search);
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
        this.props.dispatch(fetchAllFormAction(reset ? { page: 1 } : params));
        reset && this.props.history.push("/main/service-utility-form/list");
      }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.dispatch(
        fetchAllFormAction({
          keyword: this.state.keyword,
          ...this.state.filter,
          page: this.state.current,
        })
      );
    });
  };

  _onAccept = (record, formatMessage) => {
    confirm({
      autoFocusButton: null,
      title: formatMessage(messages.approveRequest),
      okText: formatMessage(messages.agree),
      okType: "danger",
      cancelText: formatMessage(messages.cancel),
      onOk: () => {
        !this.props.formList.updating &&
          this.props.dispatch(
            updateFormStatusAction({
              id: record.id,
              status: 1,
              callback: () => {
                this.reload(this.props.location.search);
              },
            })
          );
      },
      onCancel() {},
    });
  };

  showModal = () => {
    this.setState({
      visible: true,
    });
  };

  handleCancel = () => {
    this.setState({ visible: false });
  };

  handleDecline = () => {
    const { form } = this.formRef.props;
    form.validateFields((err, values) => {
      if (err) {
        return;
      }
      !this.props.formList.updating &&
        this.props.dispatch(
          updateFormStatusAction({
            id: this.state.recordId,
            status: 2,
            reason: values.reason,
            callback: () => {
              this.reload(this.props.location.search);
            },
          })
        );
      form.resetFields();
      this.setState({ visible: false });
    });
  };

  saveFormRef = (formRef) => {
    this.formRef = formRef;
  };

  render() {
    const { formList, intl, auth_group } = this.props;
    const { loading, data, totalPage, deleting } = formList;
    const { current, filter } = this.state;
    let { formatMessage } = this.props.intl;
    const formType = [
      formatMessage(messages.registerCarCard),
      formatMessage(messages.registerResidentCard),
      formatMessage(messages.registerTransfer),
      formatMessage(messages.registerAccessCard),
    ];
    const columns = [
      {
        title: (
          <span className={styles.nameTable}>
            {<FormattedMessage {...messages.stt} />}
          </span>
        ),
        dataIndex: "index",
        key: "index",
        render: (text, record, index) => <span>{index + 1}</span>,
        fixed: "left",
        width: 100,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {<FormattedMessage {...messages.propertyCode} />}
          </span>
        ),
        dataIndex: "apartment_name",
        key: "apartment_name",
        fixed: "left",
        width: 150,
        // render: (text) => <span className={styles.nameTable}>{text}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {<FormattedMessage {...messages.creator} />}
          </span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
        // render: (text) => <span className={styles.nameTable}>{text}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {<FormattedMessage {...messages.form} />}
          </span>
        ),
        dataIndex: "title",
        key: "title",
        render: (text, record) => <span>{formType[record.type || 0]}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {<FormattedMessage {...messages.createAt} />}
          </span>
        ),
        dataIndex: "created_at",
        key: "created_at",
        render: (text) => (
          <span>{moment.unix(text).format("HH:mm DD/MM/YYYY")}</span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {<FormattedMessage {...messages.status} />}
          </span>
        ),
        dataIndex: "status",
        key: "status",
        render: (text) => (
          <span>
            {text === 0 ? (
              <FormattedMessage {...messages.waitingApprove} />
            ) : text === 1 ? (
              <FormattedMessage {...messages.approve} />
            ) : text === 2 ? (
              <FormattedMessage {...messages.deny} />
            ) : (
              <FormattedMessage {...messages.cancelled} />
            )}
          </span>
        ),
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.action} />
          </span>
        ),
        dataIndex: "",
        key: "x",
        width: 200,
        fixed: "right",
        render: (record) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title={<FormattedMessage {...messages.formDetail} />}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    ALL_ROLE_NAME.MANAGE_FORM_REGISTRATION_SERVICE_UTILITY_FORM_DETAIL,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={
                  auth_group.checkRole([
                    ALL_ROLE_NAME.MANAGE_FORM_REGISTRATION_SERVICE_UTILITY_FORM_DETAIL,
                  ])
                    ? (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        let resident = {};
                        if (record.resident_user) {
                          resident = {
                            resident_name: record.resident_user.name,
                            resident_phone: record.resident_user.phone,
                          };
                        }
                        this.props.history.push(
                          `/main/service-utility-form/detail/${record.id}`,
                          {
                            record: {
                              ...record,
                              ...resident,
                            },
                          }
                        );
                      }
                    : null
                }
              >
                <i className="fa fa-eye" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
            <span> &ensp;&ensp;|&ensp;&ensp;</span>
            <Tooltip title={<FormattedMessage {...messages.approve2} />}>
              <Row
                type="flex"
                align="middle"
                style={
                  record.status === 0 &&
                  auth_group.checkRole([
                    ALL_ROLE_NAME.MANAGE_FORM_REGISTRATION_SERVICE_UTILITY_FORM_CHANGESTATUS,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={
                  auth_group.checkRole([
                    ALL_ROLE_NAME.MANAGE_FORM_REGISTRATION_SERVICE_UTILITY_FORM_CHANGESTATUS,
                  ]) && record.status === 0
                    ? (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this._onAccept(record, formatMessage);
                      }
                    : null
                }
              >
                <i className="fa fa-check" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
            <span> &ensp;&ensp;|&ensp;&ensp;</span>
            <Tooltip title={<FormattedMessage {...messages.deny} />}>
              <Row
                type="flex"
                align="middle"
                style={
                  record.status === 0 &&
                  auth_group.checkRole([
                    ALL_ROLE_NAME.MANAGE_FORM_REGISTRATION_SERVICE_UTILITY_FORM_CHANGESTATUS,
                  ])
                    ? globalStyles.row2
                    : globalStyles.rowDisabled
                }
                onClick={
                  auth_group.checkRole([
                    ALL_ROLE_NAME.MANAGE_FORM_REGISTRATION_SERVICE_UTILITY_FORM_CHANGESTATUS,
                  ]) && record.status === 0
                    ? (e) => {
                        e.preventDefault();
                        e.stopPropagation();
                        this.showModal();
                        this.setState({
                          recordId: record.id,
                        });
                      }
                    : null
                }
              >
                <i className="fa fa-times" style={{ fontSize: 18 }} />
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    return (
      <Page inner className={styles.formListPafe}>
        <div>
          <Row style={{ paddingBottom: 16 }} gutter={[24, 16]}>
            <Col {...topCol3}>
              <Input.Search
                value={this.state.address}
                placeholder={formatMessage(messages.propertyCode)}
                maxLength={255}
                prefix={
                  <Tooltip
                    title={<FormattedMessage {...messages.findApartment} />}
                  >
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
                      ["apartment_name"]: e.target.value
                        .replace(/\s+/g, " ")
                        .trim(),
                    },
                  });
                }}
                onSearch={() => {}}
              />
            </Col>

            <Col {...topCol3}>
              <Input.Search
                value={this.state.houseHolder}
                placeholder={formatMessage(messages.creator)}
                maxLength={255}
                prefix={
                  <Tooltip title={formatMessage(messages.findUserName)}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                onChange={(e) => {
                  this.setState(
                    {
                      houseHolder: e.target.value.replace(/\s+/g, " "),
                      filter: {
                        ...filter,
                        ["resident_user_name"]: e.target.value
                          .replace(/\s+/g, " ")
                          .trim(),
                      },
                    },
                    () => {}
                  );
                }}
                onSearch={() => {}}
              />
            </Col>
            <Col {...topCol3}>
              <Select
                showSearch
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.form)}
                loading={data.loading}
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
                    () => {}
                  );
                }}
                allowClear
                value={filter["type"]}
              >
                <Select.Option value={"0"}>
                  {formatMessage(messages.carRegister)}
                </Select.Option>
                <Select.Option value={"1"}>
                  {formatMessage(messages.residentRegister)}
                </Select.Option>
                <Select.Option value={"2"}>
                  {formatMessage(messages.transferRegister)}
                </Select.Option>
                <Select.Option value={"3"}>
                  {formatMessage(messages.accessRegister)}
                </Select.Option>
              </Select>
            </Col>

            <Col {...topCol3}>
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
                    () => {}
                  );
                }}
                allowClear
                value={filter["status"]}
              >
                <Select.Option value={"0"}>
                  {formatMessage(messages.waitingApprove)}
                </Select.Option>
                <Select.Option value={"1"}>
                  {formatMessage(messages.approve)}
                </Select.Option>
                <Select.Option value={"2"}>
                  {formatMessage(messages.deny)}
                </Select.Option>
                <Select.Option value={"-1"}>
                  {formatMessage(messages.cancelled)}
                </Select.Option>
              </Select>
            </Col>

            <Col span={4}>
              <Button
                type="primary"
                onClick={(e) => {
                  e.preventDefault();
                  this.props.history.push(
                    `/main/service-utility-form/list?${queryString.stringify({
                      ...this.state.filter,
                      page: 1,
                      ["resident_user_name"]: this.state.houseHolder
                        ? this.state.houseHolder.trim().replace(/\s+/g, " ")
                        : filter["resident_user_name"]
                        ? filter["resident_user_name"]
                        : undefined,
                      ["apartment_name"]: this.state.address
                        ? this.state.address.trim().replace(/\s+/g, " ")
                        : filter["apartment_name"]
                        ? filter["apartment_name"]
                        : undefined,
                    })}`
                  );
                  this.setState({
                    address: "",
                    houseHolder: "",
                  });
                }}
              >
                {formatMessage(messages.search)}
              </Button>
            </Col>
          </Row>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={formatMessage(messages.reload)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={() => {
                  this.reload(this.props.location.search, true);
                  this.setState({
                    ...this.state,
                    houseHolder: "",
                    address: "",
                  });
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
          </Row>
          <CollectionCreateForm
            intl={intl}
            wrappedComponentRef={this.saveFormRef}
            visible={this.state.visible}
            onCancel={this.handleCancel}
            onDecline={this.handleDecline}
          />
          <Table
            rowKey="id"
            loading={loading || deleting}
            columns={columns}
            dataSource={data}
            locale={{ emptyText: formatMessage(messages.empty) }}
            bordered
            scroll={{ x: 1366 }}
            className="table1"
            pagination={{
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total) =>
                `${formatMessage(messages.total)} ${total} ${formatMessage(
                  messages.form
                )}`,
            }}
            onChange={this.handleTableChange}
            onRow={(record) => {
              return {
                onClick: () => {
                  let resident = {};
                  if (record.resident_user_name) {
                    resident = {
                      resident_name: record.resident_user_name,
                      resident_phone: record.resident_user_phone,
                    };
                  }
                  auth_group.checkRole([
                    ALL_ROLE_NAME.MANAGE_FORM_REGISTRATION_SERVICE_UTILITY_FORM_DETAIL,
                  ]) &&
                    this.props.history.push(
                      `/main/service-utility-form/detail/${record.id}`,
                      {
                        record: {
                          ...record,
                          ...resident,
                        },
                      }
                    );
                },
              };
            }}
          />
        </div>
      </Page>
    );
  }
}

FormList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  formList: makeSelectFormList(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "formList", reducer });
const withSaga = injectSaga({ key: "formList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(FormList));
