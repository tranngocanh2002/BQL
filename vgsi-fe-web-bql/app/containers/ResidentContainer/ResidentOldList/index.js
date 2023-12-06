/**
 *
 * ResidentOldList
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page/Page";
import { defaultAction, fetchAllOldResidentAction } from "./actions";
import {
  Table,
  Row,
  Icon,
  Tooltip,
  Modal,
  Input,
  Button,
  Col,
  Select,
} from "antd";
import styles from "./index.less";

import queryString from "query-string";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";

import moment from "moment";
import { GLOBAL_COLOR } from "../../../utils/constants";
import makeSelectResidentOldList from "./selectors";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "../messages";

const { Option } = Select;
/* eslint-disable react/prefer-stateless-function */
export class ResidentOldList extends React.PureComponent {
  state = {
    current: 1,
    filter: {},
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
      this.props.dispatch(fetchAllOldResidentAction(params));
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
          `/main/resident-old/list?${queryString.stringify({
            ...this.state.filter,
            page: this.state.current,
          })}`
        );
      }
    );
  };

  render() {
    const { auth_group, residentOldList } = this.props;
    const { loading, data, totalPage } = residentOldList;
    const { current, filter } = this.state;
    const formatMessage = this.props.intl.formatMessage;
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
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.phone)}
          </span>
        ),
        dataIndex: "phone",
        key: "phone",
      },
      {
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
          if (text == 1) {
            return (
              <Tooltip title={formatMessage(messages.male)}>
                <i
                  className="fa fa-male"
                  style={{ fontSize: 28, color: GLOBAL_COLOR }}
                />
              </Tooltip>
            );
          } else if (text == 2) {
            return (
              <Tooltip title={formatMessage(messages.female)}>
                <i className="fa fa-female" style={{ fontSize: 28 }} />
              </Tooltip>
            );
          }
          return (
            <Tooltip title={formatMessage(messages.other)}>
              <i className="fa fa-question" style={{ fontSize: 21 }} />
            </Tooltip>
          );
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
        render: (text, record, index) => (
          <Row type="flex" align="middle" justify="center">
            <Row
              type="flex"
              align="middle"
              style={{ color: GLOBAL_COLOR, cursor: "pointer" }}
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                this.props.history.push(
                  `/main/resident-old/detail/${record.id}`,
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

    if (!auth_group.checkRole([config.ALL_ROLE_NAME.RESIDENT_OLD_LIST])) {
      columns.splice(columns.length - 1, 1);
    }

    return (
      <Page inner className={styles.residentListPage}>
        <div>
          <Row style={{ paddingBottom: 16 }} type="flex" align="middle">
            <Col xxl={6} lg={8} style={{ paddingRight: 8 }}>
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
                      ["phone"]: e.target.value,
                    },
                  });
                }}
              />
            </Col>
            <Col span={6} style={{ paddingRight: 8 }}>
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
                      ["name"]: e.target.value,
                    },
                  });
                }}
              />
            </Col>
            <Button
              type="primary"
              onClick={(e) => {
                this.props.history.push(
                  `/main/resident-old/list?${queryString.stringify({
                    ...this.state.filter,
                    page: 1,
                  })}`
                );
              }}
            >
              {formatMessage(messages.search)}
            </Button>
          </Row>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={formatMessage(messages.refresh)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={(e) => {
                  this.reload(this.props.location.search);
                }}
                icon="reload"
                size="large"
              ></Button>
            </Tooltip>
          </Row>
          <Table
            rowKey="apartment_map_resident_user_id"
            loading={loading}
            scroll={{ x: 1024 }}
            columns={columns}
            dataSource={data}
            style={{ cursor: "pointer" }}
            locale={{ emptyText: formatMessage(messages.noData) }}
            pagination={{
              pageSize: 20,
              total: totalPage,
              current,
              showTotal: (total, range) =>
                formatMessage(messages.totalRes, { total }),
            }}
            onChange={this.handleTableChange}
            expandRowByClick
            onRow={(record, rowIndex) => {
              return {
                onClick: (event) => {
                  this.props.history.push(
                    `/main/resident-old/detail/${record.id}`,
                    {
                      record,
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

ResidentOldList.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  residentOldList: makeSelectResidentOldList(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "residentOldList", reducer });
const withSaga = injectSaga({ key: "residentOldList", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ResidentOldList));
