/**
 *
 * NotificationCategory
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import messages from "./messages";

import { Button, Col, Modal, Row, Table, Tooltip } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectNotificationCategory from "./selectors";

import moment from "moment";
import queryString from "query-string";
import WithRole from "../../../components/WithRole";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";
import {
  GLOBAL_COLOR,
  getIconStyle,
  getRowStyle,
} from "../../../utils/constants";
import ModalCreate from "./ModalCreate";
import {
  createCategoryNotificationAction,
  defaultAction,
  deleteCategoryNotificationAction,
  fetchCategoryNotificationAction,
  updateCategoryNotificationAction,
} from "./actions";
import styles from "./index.less";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";

/* eslint-disable react/prefer-stateless-function */
export class NotificationCategory extends React.PureComponent {
  state = {
    current: 1,
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
  }

  reload = (search) => {
    let params = queryString.parse(search);
    try {
      if (params.page) params.page = parseInt(params.page);
      else params.page = 1;
    } catch (error) {
      params.page = 1;
    }

    params.keyword = params.keyword || "";

    this.setState({ current: params.page, keyword: params.keyword }, () => {
      this.props.dispatch(fetchCategoryNotificationAction(params));
    });
  };

  _onEdit = (record) => {
    this.setState({ currentEdit: record }, () => {
      this.setState({ visible: true });
    });
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/setting/notify/category?${queryString.stringify({
          page: this.state.current,
        })}`
      );
    });
  };
  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage({ ...messages.deleteModalTitle }),
      okText: this.props.intl.formatMessage({ ...messages.okText }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({ ...messages.cancelText }),
      onOk: () => {
        this.props.dispatch(
          deleteCategoryNotificationAction({
            // apartment_id: record.apartment_id,
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

  getTypeNotification = (type) => {
    switch (type) {
      case 0:
        return <FormattedMessage {...messages.regularAnnouncement} />;
      case 1:
        return <FormattedMessage {...messages.feeAnnouncement} />;
      case 2:
        return <FormattedMessage {...messages.surveyAnnouncement} />;
      default:
        return "";
    }
  };

  render() {
    const { notificationCategory, auth_group } = this.props;
    const isExitNotificationFee = notificationCategory.data.filter(
      (noti) => noti.type == 1
    );
    const { current } = this.state;
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(
              0,
              notificationCategory.loading ? current - 2 : current - 1
            ) *
              20 +
              index +
              1}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.color} />
          </span>
        ),
        dataIndex: "label_color",
        key: "label_color",
        render: (text, record) => (
          <div
            style={{
              width: 50,
              height: 20,
              borderRadius: 3,
              background: record.label_color,
            }}
          />
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.categoryName} />
          </span>
        ),
        dataIndex: this.props.language === "en" ? "name_en" : "name",
        key: "name",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.categoryType} />
          </span>
        ),
        dataIndex: "type",
        key: "type",
        //TODO: Sua lai type
        render: this.getTypeNotification,
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.countAnnouncement} />
          </span>
        ),
        dataIndex: "count_announcement",
        key: "count_announcement",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.update} />
          </span>
        ),
        dataIndex: "updated_at",
        key: "updated_at",
        render: (text, record) => (
          <span>
            {moment.unix(record.updated_at).format("DD/MM/YYYY - HH:mm")}
          </span>
        ),
      },
      {
        width: 120,
        fixed: "right",
        align: "center",
        title: (
          <span className={styles.nameTable}>
            <FormattedMessage {...messages.action} />
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title={<FormattedMessage {...messages.edit} />}>
              <Row
                type="flex"
                align="middle"
                style={getRowStyle(
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.ANNOUNCE_CATEGORY_CREATE_UPDATE,
                  ])
                )}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.ANNOUNCE_CATEGORY_CREATE_UPDATE,
                  ]) && this._onEdit(record);
                }}
              >
                <i className="fa fa-edit" style={{ fontSize: 18 }}></i>
              </Row>
            </Tooltip>
            &ensp;&ensp;|&ensp;&ensp;
            <Tooltip title={<FormattedMessage {...messages.delete} />}>
              <Row
                type="flex"
                align="middle"
                style={getRowStyle(
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.ANNOUNCE_CATEGORY_CREATE_UPDATE,
                  ]),
                  true
                )}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.ANNOUNCE_CATEGORY_CREATE_UPDATE,
                  ]) && this._onDelete(record);
                }}
              >
                <i className="fa fa-trash" style={{ fontSize: 18 }}></i>
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    return (
      <Page inner>
        <div className={styles.notificationCategoryPage}>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={<FormattedMessage {...messages.reloadPage} />}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={() => {
                  this.reload(this.props.location.search);
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            <WithRole
              roles={[config.ALL_ROLE_NAME.ANNOUNCE_CATEGORY_CREATE_UPDATE]}
            >
              <Tooltip title={<FormattedMessage {...messages.addNew} />}>
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() =>
                    this.setState({ visible: true, currentEdit: undefined })
                  }
                  icon="plus"
                  shape="circle"
                  size="large"
                />
              </Tooltip>
            </WithRole>
          </Row>
          <Row>
            <Col>
              <Table
                rowKey="id"
                loading={
                  notificationCategory.loading || notificationCategory.deleting
                }
                columns={columns}
                dataSource={notificationCategory.data}
                locale={{
                  emptyText: <FormattedMessage {...messages.noData} />,
                }}
                bordered
                scroll={{ x: 1000 }}
                pagination={{
                  pageSize: 20,
                  total: notificationCategory.totalPage,
                  current: this.state.current,
                  showTotal: (total) => (
                    <FormattedMessage {...messages.total} values={{ total }} />
                  ),
                }}
                onChange={this.handleTableChange}
                // onRow={(record, rowIndex) => {
                //   return {
                //     onClick: event => {
                //       this.props.history.push(`/main/resident/detail/${record.id}`, {
                //         record
                //       })
                //     }
                //   };
                // }}
              />
            </Col>
          </Row>
          <ModalCreate
            currentEdit={this.state.currentEdit}
            visible={this.state.visible}
            setState={this.setState.bind(this)}
            creating={
              notificationCategory.creating || notificationCategory.updating
            }
            isExitNotificationFee={!!isExitNotificationFee.length}
            handlerAddMember={(values) => {
              this.props.dispatch(
                createCategoryNotificationAction({
                  ...values,
                  callback: () => {
                    this.setState({ visible: false });
                    this.reload(this.props.location.search);
                  },
                })
              );
            }}
            handlerUpdateMember={(values) => {
              this.props.dispatch(
                updateCategoryNotificationAction({
                  ...values,
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

NotificationCategory.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  notificationCategory: makeSelectNotificationCategory(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "notificationCategory", reducer });
const withSaga = injectSaga({ key: "notificationCategory", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(NotificationCategory));
