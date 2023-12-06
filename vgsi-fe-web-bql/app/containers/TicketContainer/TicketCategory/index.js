/**
 *
 * TicketCategory
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Col, Modal, Row, Table, Tooltip } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import messages from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectTicketCategory from "./selectors";

import Page from "../../../components/Page/Page";
import ModalCreate from "./ModalCreate";
import {
  createCategoryAction,
  defaultAction,
  deleteCategoryAction,
  fetchAuthGroupAction,
  fetchCategoryAction,
  updateCategoryAction,
} from "./actions";
import styles from "./index.less";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import queryString from "query-string";
import WithRole from "../../../components/WithRole";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";
import { globalStyles } from "../../../utils/constants";

/* eslint-disable react/prefer-stateless-function */
export class TicketCategory extends React.PureComponent {
  state = {
    visible: false,
    current: 1,
    currentEdit: undefined,
  };

  componentDidMount() {
    this.props.dispatch(fetchAuthGroupAction());
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
    this.setState({ current: params.page }, () => {
      this.props.dispatch(fetchCategoryAction(params));
    });
  };

  handleTableChange = (pagination, filters, sorter) => {
    let sort = sorter.order == "descend" ? `-${sorter.field}` : sorter.field;
    this.setState({ sort, current: pagination.current }, () => {
      this.props.history.push(
        `/main/setting/ticket/category?${queryString.stringify({
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

  _onEdit = (record) => {
    this.setState({ currentEdit: record }, () => {
      this.setState({ visible: true });
    });
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  render() {
    const { current } = this.state;
    const { ticketCategory, auth_group, language } = this.props;
    const formatMessage = this.props.intl.formatMessage;
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => (
          <span>
            {Math.max(0, ticketCategory.loading ? current - 2 : current - 1) *
              20 +
              index +
              1}
          </span>
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.color)}
          </span>
        ),
        dataIndex: "color",
        key: "color",
        render: (text, record) => (
          <div
            style={{
              width: 50,
              height: 20,
              borderRadius: 3,
              background: record.color,
            }}
          />
        ),
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.nameType)}
          </span>
        ),
        dataIndex: language === "en" ? "name_en" : "name",
        key: "name",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.groupProcess)}
          </span>
        ),
        dataIndex: language === "en" ? "group_process_en" : "group_process",
        key: "group_process",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        width: 200,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "",
        key: "x",
        render: (text, record) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title={formatMessage(messages.edit)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REQUEST_CATEGORY_CREATE_UPDATE,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REQUEST_CATEGORY_CREATE_UPDATE,
                  ]) && this._onEdit(record);
                }}
              >
                <i
                  className="fa fa-edit"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.REQUEST_CATEGORY_CREATE_UPDATE,
                    ])
                      ? globalStyles.icon
                      : globalStyles.iconDisabled
                  }
                ></i>
              </Row>
            </Tooltip>
            &ensp;&ensp; | &ensp;&ensp;
            <Tooltip title={formatMessage(messages.delete)}>
              <Row
                type="flex"
                align="middle"
                style={
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REQUEST_CATEGORY_CREATE_UPDATE,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.REQUEST_CATEGORY_CREATE_UPDATE,
                  ]) && this._onDelete(record);
                }}
              >
                <i
                  className="fa fa-trash"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.REQUEST_CATEGORY_CREATE_UPDATE,
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
      <Page inner>
        <div className={styles.ticketCategoryPage}>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={formatMessage(messages.reload)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={() => {
                  this.reload(this.props.location.search);
                }}
                icon="reload"
                size="large"
              ></Button>
            </Tooltip>
            <WithRole
              roles={[config.ALL_ROLE_NAME.REQUEST_CATEGORY_CREATE_UPDATE]}
            >
              <Tooltip title={formatMessage(messages.addNew)}>
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
                loading={ticketCategory.loading || ticketCategory.deleting}
                // showHeader={false}
                columns={columns}
                dataSource={ticketCategory.data.map((rrrr) => {
                  return {
                    ...rrrr,
                    group_process: rrrr.auth_group_ids
                      .map((rr) => rr.name)
                      .join(", "),
                    group_process_en: rrrr.auth_group_ids
                      .map((rr) => rr.name_en)
                      .join(", "),
                  };
                })}
                locale={{ emptyText: formatMessage(messages.noData) }}
                bordered
                pagination={{
                  pageSize: 20,
                  total: ticketCategory.totalPage,
                  current,
                  showTotal: (total) =>
                    `${formatMessage(messages.total, {
                      total,
                    })}`,
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
            authGroup={ticketCategory.authGroup}
            creating={ticketCategory.creating || ticketCategory.updating}
            handlerAddMember={(values) => {
              this.props.dispatch(
                createCategoryAction({
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
                updateCategoryAction({
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

TicketCategory.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  ticketCategory: makeSelectTicketCategory(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "ticketCategory", reducer });
const withSaga = injectSaga({ key: "ticketCategory", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(TicketCategory));
