/**
 *
 * TicketList
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import {
  Button,
  Col,
  Icon,
  Input,
  Modal,
  Radio,
  Row,
  Select,
  Spin,
  Table,
  Tooltip,
  Typography,
} from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import _ from "lodash";
import queryString from "query-string";
import { ALL_ROLE_NAME, STATUS_REQUEST } from "utils/config";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import { deleteCategoryAction } from "../TicketCategory/actions";
import {
  defaultAction,
  fetchAllTicketAction,
  fetchApartmentAction,
  fetchCategoryAction,
} from "./actions";
import styles from "./index.less";
import messages from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectTicketList from "./selectors";

import { selectAuthGroup } from "redux/selectors";
import { invertColor, timeFromNow } from "utils/constants";
const { Text } = Typography;

/* eslint-disable react/prefer-stateless-function */
export class TicketList extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      visible: false,
      current: 1,
      filter: {},
      contentSearch: "",
      currentEdit: undefined,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.reload(this.props.location.search);
    this.props.dispatch(fetchCategoryAction());
    this._onSearch("");
  }

  componentWillReceiveProps(nextProps) {
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
    this.setState({ current: params.page, filter: reset ? {} : params }, () => {
      this.props.dispatch(fetchAllTicketAction(reset ? { page: 1 } : params));
      reset && this.props.history.push(`${this.props.location.pathname}`);
    });
  };

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/ticket/list?${queryString.stringify({
          ...this.state.filter,
          page: this.state.current,
        })}`
      );
    });
  };

  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.modalDelete),
      okText: this.props.intl.formatMessage(messages.confirm),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          deleteCategoryAction({
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

  render() {
    const { loading, data, totalPage, apartments, categories } =
      this.props.ticketList;
    const language = this.props.language;
    const formatMessage = this.props.intl.formatMessage;
    const { current, filter, contentSearch } = this.state;
    const { auth_group } = this.props;

    const columns = [
      {
        fixed: "left",
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        width: 30,
        render: (text, record, index) => (
          <span>
            {Math.max(0, loading ? current - 2 : current - 1) * 20 + index + 1}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.status)}
          </span>
        ),
        dataIndex: "status",
        key: "status",
        fixed: "left",
        width: 150,
        render: (text) => {
          const status = STATUS_REQUEST.find((item) => item.id === text);
          return status ? (
            <span
              className={"luci-status-danger"}
              style={{
                backgroundColor: status.color,
                color: invertColor(status.color, true),
              }}
            >
              {language === "en" ? status.name_en : status.name}
            </span>
          ) : (
            <span>Error</span>
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.feedbackCode)}
          </span>
        ),
        dataIndex: "number",
        key: "number",
        width: 130,
        fixed: "left",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.title)}
          </span>
        ),
        dataIndex: "title",
        key: "title",
        width: 320,
        fixed: "left",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.category)}
          </span>
        ),
        width: 150,
        dataIndex: "request_category_name",
        key: "request_category_name",
        render: (text, record) => (
          <span
            className={"luci-status-danger"}
            style={{ backgroundColor: record.request_category_color }}
          >
            {language === "en" ? record.request_category_name_en : text}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.property)}
          </span>
        ),
        dataIndex: "apartment_name",
        key: "apartment_name",
        render: (text, record) =>
          `${record.apartment_name} (${record.building_area_name})`,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.sender)}
          </span>
        ),
        dataIndex: "resident_user_name",
        key: "resident_user_name",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.update)}
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text) => timeFromNow(text),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.dayCreate)}
          </span>
        ),
        dataIndex: "created_at",
        key: "created_at",
        render: (text) => timeFromNow(text),
      },
      {
        fixed: "right",
        align: "center",
        width: 100,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.rate)}
          </span>
        ),
        dataIndex: "rate",
        key: "rate",
        render: (text) => (text ? `${text || 0}/5` : ""),
      },
    ];

    return (
      <Page inner>
        <div className={styles.ticketListPage}>
          <Radio.Group
            style={{ paddingBottom: 16 }}
            value={filter["status"] != undefined ? filter["status"] : "-3"}
            buttonStyle="solid"
            // size='large'
            onChange={(e) => {
              this.setState(
                {
                  filter: {
                    ...filter,
                    ["status"]:
                      e.target.value == -3 ? undefined : e.target.value,
                  },
                },
                () => {
                  this.props.history.push(
                    `/main/ticket/list?${queryString.stringify({
                      ...this.state.filter,
                      page: 1,
                    })}`
                  );
                }
              );
            }}
          >
            <Radio.Button value="-3">
              {formatMessage(messages.all)}
            </Radio.Button>
            <Radio.Button value="0">
              {formatMessage(messages.newFeedback)}
            </Radio.Button>
            <Radio.Button value="-1">
              {formatMessage(messages.pending)}
            </Radio.Button>
            <Radio.Button value="1">
              {formatMessage(messages.processing)}
            </Radio.Button>
            <Radio.Button value="2">
              {formatMessage(messages.processed)}
            </Radio.Button>
            {/* <Radio.Button value="3">
              {formatMessage(messages.processingAgain)}
            </Radio.Button> */}
            <Radio.Button value="4">
              {formatMessage(messages.closed)}
            </Radio.Button>
            <Radio.Button value="-2">
              {formatMessage(messages.cancelFeedback)}
            </Radio.Button>
          </Radio.Group>
          <Row style={{ paddingBottom: 16 }}>
            <Col span={5} style={{ paddingRight: 8 }}>
              <Select
                style={{ width: "100%" }}
                placeholder={formatMessage(messages.category)}
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
                        ["request_category_id"]: value,
                      },
                    },
                    () => {
                      // this.props.history.push(`/main/ticket/list?${queryString.stringify({
                      //   ...this.state.filter,
                      //   page: 1,
                      // })}`)
                    }
                  );
                }}
                allowClear
                value={filter["request_category_id"]}
              >
                {categories.lst.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}`}
                    >{`${
                      language === "en" ? gr.name_en : gr.name
                    }`}</Select.Option>
                  );
                })}
              </Select>
            </Col>
            <Col span={5} style={{ paddingRight: 8 }}>
              <Input.Search
                value={contentSearch}
                placeholder={formatMessage(messages.searchTitle)}
                prefix={
                  <Tooltip title={formatMessage(messages.searchTitleTooltip)}>
                    <Icon
                      type="info-circle"
                      style={{ color: "rgba(0,0,0,.45)" }}
                    />
                  </Tooltip>
                }
                maxLength={255}
                onChange={(e) => {
                  this.setState({
                    contentSearch: e.target.value,
                    filter: {
                      ...filter,
                      ["title"]: contentSearch.trim(),
                    },
                  });
                }}
                onSearch={(text) => {
                  // this.props.history.push(`/main/ticket/list?${queryString.stringify({
                  //   ...this.state.filter,
                  //   page: 1,
                  // })}`)
                }}
              />
            </Col>
            <Col span={5} style={{ paddingRight: 8 }}>
              <Select
                style={{ width: "100%" }}
                showSearch
                loading={apartments.loading}
                placeholder={formatMessage(messages.chooseProperty)}
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
            <Button
              type="primary"
              onClick={(e) => {
                e.preventDefault();
                this.props.history.push(
                  `/main/ticket/list?${queryString.stringify({
                    ...this.state.filter,
                    ["title"]: contentSearch ? contentSearch.trim() : "",
                    page: 1,
                  })}`
                );
                this.setState({
                  ...this.state,
                  contentSearch: "",
                });
              }}
            >
              {formatMessage(messages.search)}
            </Button>
          </Row>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={formatMessage(messages.reload)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={(e) => {
                  e.preventDefault();
                  this.reload(this.props.location.search, true);
                  this.setState({
                    ...this.state,
                    contentSearch: "",
                  });
                }}
                icon="reload"
                size="large"
              ></Button>
            </Tooltip>
          </Row>
          <Row>
            <Table
              rowKey="id"
              loading={loading}
              // showHeader={false}
              columns={columns}
              dataSource={data}
              locale={{ emptyText: formatMessage(messages.noData) }}
              bordered
              pagination={{
                pageSize: 20,
                total: totalPage,
                current,
                showTotal: (total) => formatMessage(messages.total, { total }),
              }}
              onChange={this.handleTableChange}
              onRow={(record) => {
                return {
                  onClick: () => {
                    auth_group.checkRole([ALL_ROLE_NAME.REQUEST_DETAIL]) &&
                      this.props.history.push(
                        `/main/ticket/detail/${record.id}`,
                        {
                          ...record,
                          permission: true,
                        }
                      );
                  },
                };
              }}
              scroll={{ x: 1366 }}
            />
          </Row>
        </div>
      </Page>
    );
  }
}

TicketList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  ticketList: makeSelectTicketList(),
  language: makeSelectLocale(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "ticketList", reducer });
const withSaga = injectSaga({ key: "ticketList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(TicketList));
