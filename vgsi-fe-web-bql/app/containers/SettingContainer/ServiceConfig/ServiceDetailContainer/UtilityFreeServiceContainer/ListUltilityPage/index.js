/**
 *
 * ListUltilityPage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Avatar, Button, Col, Modal, Row, Table, Tooltip } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import messages from "../../../messages";
import {
  defaultAction,
  deleteListUltility,
  fetchAllListUltility,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectListUltilityPage from "./selectors";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import queryString from "query-string";
import WithRole from "../../../../../../components/WithRole";
import { getFullLinkImage } from "../../../../../../connection";
import { selectAuthGroup } from "../../../../../../redux/selectors";
import { config } from "../../../../../../utils";
import { globalStyles } from "../../../../../../utils/constants";
import makeSelectUtilityFreeServiceContainer from "../selectors";
import styles from "./index.less";

/* eslint-disable react/prefer-stateless-function */
export class ListUltilityPage extends React.PureComponent {
  state = {
    current: 1,
    filter: {},
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
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
      this.props.listUlilityPage.creating !=
        nextProps.listUlilityPage.creating &&
      nextProps.listUlilityPage.success
    ) {
      this.setState({
        visible: false,
      });
      this.reload(this.props.location.search);
    }

    if (
      this.props.listUlilityPage.importing !=
        nextProps.listUlilityPage.importing &&
      nextProps.listUlilityPage.importingSuccess
    ) {
      this.setState({
        visible: false,
      });
      if (this.state.current == 1) {
        this.reload(this.props.location.search);
      } else {
        this.props.history.push(
          `/main/setting/service/detail/utility-free/payment?${queryString.stringify(
            {
              ...this.state.filter,
              page: 1,
            }
          )}`
        );
      }
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

    // params.keyword = params.keyword || "";

    this.setState(
      {
        current: params.page,
        // keyword: params.keyword,
        filter: params,
      },
      () => {
        this.props.dispatch(fetchAllListUltility(params));
      }
    );
  };

  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmDeleteUtility),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          deleteListUltility({
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
    this.props.history.push(
      `/main/setting/service/detail/utility-free/edit/${record.id}`,
      { record }
    );
  };

  handleTableChange = (pagination, filters, sorter) => {
    this.setState(
      {
        current: pagination.current,
      },
      () => {
        this.props.history.push(
          `/main/setting/service/detail/utility-free/list?${queryString.stringify(
            {
              ...this.state.filter,
              page: this.state.current,
            }
          )}`
        );
      }
    );
  };

  render() {
    const { listUlilityPage, auth_group } = this.props;
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => <span>{index + 1}</span>,
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.imageCover)}
          </span>
        ),
        dataIndex: "medias_",
        key: "medias_",
        render: (text, record) => {
          return (
            <Avatar
              shape="square"
              size="large"
              icon="picture"
              src={getFullLinkImage(
                record.medias ? record.medias.logo : undefined
              )}
            />
          );
        },
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.nameUtility)}
          </span>
        ),
        dataIndex: this.props.language === "en" ? "name_en" : "name",
        key: "name",
        render: (text, record) => (
          <span>
            {this.props.language === "vi" ? record.name : record.name_en}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.timeActivation)}
          </span>
        ),
        dataIndex: "name_",
        key: "name_",
        render: (text, record) => (
          <span>{`${record.hours_open} - ${record.hours_close}`}</span>
        ),
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
            {text === 1
              ? this.props.intl.formatMessage(messages.active)
              : text === 0
              ? this.props.intl.formatMessage(messages.pause)
              : this.props.intl.formatMessage(messages.stop)}
          </span>
        ),
      },
      {
        width: 120,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record, index) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip
              title={this.props.intl.formatMessage(messages.editUtility)}
            >
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ]) && this._onEdit(record);
                }}
              >
                <i className="fa fa-edit" style={{ fontSize: 18 }}></i>
              </Row>
            </Tooltip>
            &ensp;&ensp;| &ensp;&ensp;
            <Tooltip
              title={this.props.intl.formatMessage(messages.deleteUtility)}
            >
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ]) && this._onDelete(record);
                }}
              >
                <i
                  className="fa fa-trash"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                    ])
                      ? globalStyles.iconDelete
                      : globalStyles.iconDisabled
                  }
                ></i>
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    return (
      <Row className={styles.ListUltilityPage}>
        <WithRole roles={[config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT]}>
          <Col>
            <Row type="flex" align="middle" style={{ marginBottom: 20 }}>
              <Tooltip title={this.props.intl.formatMessage(messages.refresh)}>
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
              <Tooltip title={this.props.intl.formatMessage(messages.add)}>
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => {
                    this.props.history.push(
                      "/main/setting/service/detail/utility-free/add"
                    );
                  }}
                  disabled={listUlilityPage.importing}
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </Row>
          </Col>
        </WithRole>
        <Col style={{ width: "100%" }}>
          <Table
            rowKey="id"
            loading={
              listUlilityPage.loading ||
              listUlilityPage.deleting ||
              listUlilityPage.importing
            }
            columns={columns}
            dataSource={listUlilityPage.data}
            locale={{
              emptyText: this.props.intl.formatMessage(messages.noData),
            }}
            bordered
            pagination={{
              pageSize: 20,
              total: listUlilityPage.totalPage,
              current: this.state.current,
              showTotal: (total, range) =>
                this.props.intl.formatMessage(messages.totalPage, { total }),
            }}
            onChange={this.handleTableChange}
            // scroll={{ x: 1366 }}
            onRow={(record, rowIndex) => {
              return {
                onClick: (event) => {
                  this.props.history.push(
                    `/main/setting/service/detail/utility-free/detail/${record.id}/info`,
                    {
                      record,
                    }
                  );
                },
              };
            }}
          />
        </Col>
      </Row>
    );
  }
}

ListUltilityPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  listUlilityPage: makeSelectListUltilityPage(),
  utilityFreeServiceContainer: makeSelectUtilityFreeServiceContainer(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "listUlilityPage", reducer });
const withSaga = injectSaga({ key: "listUlilityPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ListUltilityPage));
