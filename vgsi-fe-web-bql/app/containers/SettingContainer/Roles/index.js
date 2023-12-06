/**
 *
 * Roles
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectRoles from "./selectors";

import styles from "./index.less";

import { Button, Col, Modal, Row, Table, Tooltip } from "antd";
import { injectIntl } from "react-intl";
import Page from "../../../components/Page/Page";
import WithRole from "../../../components/WithRole";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";
import { globalStyles } from "../../../utils/constants";
import messages from "../messages";
import { deleteGroup, fetchAllGroup } from "./actions";

/* eslint-disable react/prefer-stateless-function */
export class Roles extends React.PureComponent {
  constructor(props) {
    super(props);
    const { refresh } = props.location.state || false;
    this.state = {
      refresh,
    };
  }
  componentDidMount() {
    this.props.dispatch(fetchAllGroup());
  }

  componentWillReceiveProps(nextProps) {
    const { refresh } = this.state;
    if (refresh) {
      setTimeout(() => {
        this.props.dispatch(fetchAllGroup());
      }, 1000);
      this.setState({
        refresh: false,
      });
    }
  }

  _onEdit = (record) => {
    this.props.history.push(`/main/setting/roles/edit/${record.id}`, {
      group: record,
      isEdit: true,
    });
  };

  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmDeleteRole),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          deleteGroup({
            id: record.id,
          })
        );
      },
      onCancel() {},
    });
  };

  render() {
    const formatMessage = this.props.intl.formatMessage;
    const columns = [
      {
        width: 200,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.nameGroupPermission)}
          </span>
        ),
        dataIndex: "name",
        key: "name",
      },
      {
        width: 250,
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.nameEnGroupPermission)}
          </span>
        ),
        dataIndex: "name_en",
        key: "name_en",
      },
      {
        width: 150,
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.numberHuman)}
          </span>
        ),
        dataIndex: "count_management_user",
        key: "count_management_user",
      },
      {
        title: (
          <span className={styles.nameTable}>
            {formatMessage(messages.description)}
          </span>
        ),
        dataIndex: "description",
        key: "description",
      },
      {
        width: 220,
        fixed: "right",
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
                    config.ALL_ROLE_NAME.RIGHTS_GROUP_UPDATE,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.RIGHTS_GROUP_UPDATE,
                  ]) && this._onEdit(record);
                }}
              >
                <i
                  className="fa fa-edit"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.RIGHTS_GROUP_UPDATE,
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
                    config.ALL_ROLE_NAME.RIGHTS_GROUP_DELETE,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  auth_group.checkRole([
                    config.ALL_ROLE_NAME.RIGHTS_GROUP_DELETE,
                  ]) && this._onDelete(record);
                }}
              >
                <i
                  className="fa fa-trash"
                  style={
                    auth_group.checkRole([
                      config.ALL_ROLE_NAME.RIGHTS_GROUP_DELETE,
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

    const { roles, auth_group } = this.props;
    const { loading, data, deleting } = roles;

    return (
      <Page inner>
        <div className={styles.rolePage}>
          <Row>
            <Col span={12}>
              <span className={styles.numberRole}>
                {formatMessage(messages.decentralizeHr)}
              </span>
            </Col>
            <WithRole roles={[config.ALL_ROLE_NAME.RIGHTS_GROUP_CREATE]}>
              <Col span={12} style={{ textAlign: "right" }}>
                <Button
                  icon="plus"
                  type="primary"
                  ghost
                  onClick={() => {
                    this.props.history.push("/main/setting/roles/create", {
                      isEdit: false,
                    });
                  }}
                >
                  {formatMessage(messages.addGroup)}
                </Button>
              </Col>
            </WithRole>
          </Row>
          <Row gutter={24} style={{ marginTop: 12 }}>
            <Table
              rowKey="code"
              loading={loading || deleting}
              locale={{ emptyText: formatMessage(messages.noData) }}
              bordered
              columns={columns}
              scroll={{ x: 900 }}
              dataSource={data}
            />
          </Row>
        </div>
      </Page>
    );
  }
}

Roles.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  roles: makeSelectRoles(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "roles", reducer });
const withSaga = injectSaga({ key: "roles", saga });

export default compose(withReducer, withSaga, withConnect)(injectIntl(Roles));
