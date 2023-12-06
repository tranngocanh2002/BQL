/**
 *
 * Roles
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import reducer from "./reducer";
import saga from "./saga";

import styles from "./index.less";

import {
  Button,
  Checkbox,
  Form,
  Input,
  Popconfirm,
  Row,
  Select,
  Table,
} from "antd";
import Page from "../../../components/Page/Page";
import WithRole from "../../../components/WithRole";
import { selectAuthGroup } from "../../../redux/selectors";
import { config } from "../../../utils";
import { GLOBAL_COLOR, globalStyles } from "../../../utils/constants";
import {
  defaultAction,
  fetchAllRoles,
  fetchBuildingCluster,
  updateSetting,
} from "./actions";
import messages from "./messages";
import makeSelectSetupNotification from "./selectors";

const EditableContext = React.createContext();

class EditableCell extends React.Component {
  getInput = (inputType) => {
    if (inputType == "boolean") return <Checkbox />;
    return <Input style={{ width: "100%" }} />;
  };

  renderCell = ({ form: { getFieldDecorator, getFieldValue } }) => {
    const {
      editing,
      dataIndex,
      title,
      messageError,
      inputType,
      record,
      index,
      children,
      last,
      next,
      roles,
      ...restProps
    } = this.props;
    return (
      <td {...restProps}>
        {editing ? (
          <Form.Item style={{ margin: 0 }}>
            {getFieldDecorator(dataIndex, {
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage
                      {...messages.errorEmpty}
                      values={{ messageError }}
                    />
                  ),
                },
              ],
              initialValue: record.code,
            })(
              <Select loading={roles.loading} style={{ width: "60%" }}>
                {roles.data.map((role) => {
                  return (
                    <Select.Option value={role.code} key={`${role.id}`}>
                      {role.name}
                    </Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
        ) : (
          children
        )}
      </td>
    );
  };

  render() {
    return (
      <EditableContext.Consumer>{this.renderCell}</EditableContext.Consumer>
    );
  }
}

/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class Roles extends React.PureComponent {
  state = {
    data: [],
    editingKey: "",
  };

  columns = [
    {
      align: "center",
      width: 100,
      title: <span className={styles.nameTable}>#</span>,
      dataIndex: "#",
      key: "#",
      render: (text, record, index) => `${index + 1}`,
    },
    {
      title: (
        <span className={styles.nameTable}>
          {this.props.intl.formatMessage(messages.nameGroup)}
        </span>
      ),
      messageError: this.props.intl.formatMessage(messages.nameGroup),
      dataIndex: "name",
      key: "name",
      editable: true,
    },
    {
      width: 220,
      align: "center",
      title: (
        <span className={styles.nameTable}>
          {this.props.intl.formatMessage(messages.action)}
        </span>
      ),
      dataIndex: "",
      key: "x",
      render: (text, record, index) => {
        const { editingKey } = this.state;
        const editable = this.isEditing(record);
        return editable ? (
          <Row type="flex" align="middle" justify="center">
            <EditableContext.Consumer>
              {(form) => (
                <Row
                  type="flex"
                  align="middle"
                  style={{
                    color: GLOBAL_COLOR,
                    marginRight: 10,
                    cursor: "pointer",
                  }}
                  onClick={() => this.save(form, record)}
                >
                  <i
                    className="material-icons"
                    style={{ fontSize: 18, marginRight: 6 }}
                  >
                    save
                  </i>{" "}
                  {this.props.intl.formatMessage(messages.save)}
                </Row>
              )}
            </EditableContext.Consumer>
            <Popconfirm
              title={this.props.intl.formatMessage(messages.confirmCancel)}
              onConfirm={() => this.cancel(record)}
              cancelText={this.props.intl.formatMessage(messages.cancel)}
              okText={this.props.intl.formatMessage(messages.agree)}
            >
              <Row
                type="flex"
                align="middle"
                style={{ color: "red", cursor: "pointer" }}
              >
                <i className="material-icons" style={{ fontSize: 18 }}>
                  close
                </i>{" "}
                {this.props.intl.formatMessage(messages.cancel)}
              </Row>
            </Popconfirm>
          </Row>
        ) : (
          <Row type="flex" align="middle" justify="center">
            <Row
              type="flex"
              align="middle"
              style={
                this.props.auth_group.checkRole([
                  config.ALL_ROLE_NAME.SETTING_RECEIVE_FINANCE_NOTIFICATION,
                ])
                  ? globalStyles.row
                  : globalStyles.rowDisabled
              }
              onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                this.props.auth_group.checkRole([
                  config.ALL_ROLE_NAME.SETTING_RECEIVE_FINANCE_NOTIFICATION,
                ]) && this.edit(record);
              }}
            >
              <i
                className="fa fa-edit"
                style={
                  this.props.auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_RECEIVE_FINANCE_NOTIFICATION,
                  ])
                    ? globalStyles.icon
                    : globalStyles.iconDisabled
                }
              ></i>
            </Row>
            &ensp;&ensp; | &ensp;&ensp;
            <Popconfirm
              title={this.props.intl.formatMessage(messages.confirmDeleteGroup)}
              onConfirm={() => this._onDelete(record)}
              cancelText={this.props.intl.formatMessage(messages.cancel)}
              okText={this.props.intl.formatMessage(messages.agree)}
              disabled={
                !this.props.auth_group.checkRole([
                  config.ALL_ROLE_NAME.SETTING_RECEIVE_FINANCE_NOTIFICATION,
                ])
              }
            >
              <Row
                type="flex"
                align="middle"
                style={
                  this.props.auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_RECEIVE_FINANCE_NOTIFICATION,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
              >
                <i
                  className="fa fa-trash"
                  style={
                    this.props.auth_group.checkRole([
                      config.ALL_ROLE_NAME.SETTING_RECEIVE_FINANCE_NOTIFICATION,
                    ])
                      ? globalStyles.iconDelete
                      : globalStyles.iconDisabled
                  }
                ></i>
              </Row>
            </Popconfirm>
          </Row>
        );
      },
    },
  ];

  isEditing = (record) => record.id === this.state.editingKey;

  cancel = (record) => {
    if (record.isNew) {
      this.setState({
        editingKey: "",
        data: this.state.data.filter((dd) => !dd.isNew),
      });
      return;
    }
    this.setState({
      editingKey: "",
    });
  };

  componentWillUnmount() {
    // window.cancelAnimationFrame(this.requestRef);
    // window.removeEventListener('resize', this.resize);
    // this.resize.cancel();
    this.props.dispatch(defaultAction());
  }
  componentDidMount() {
    this.props.dispatch(fetchBuildingCluster());
    this.props.dispatch(fetchAllRoles());
  }

  _onDelete = (record) => {
    this.props.dispatch(
      updateSetting({
        ...this.props.setupNotification.data,
        setting_group_receives_notices_financial: this.state.data
          .filter((r) => r.code != record.code)
          .map((r) => {
            let rrr;
            if (r.isNew || r.code == record.name) {
              rrr = this.props.setupNotification.roles.data.find(
                (rr) => rr.code == record.name
              );
            } else {
              rrr = r;
            }
            return rrr.id;
          }),
        messagePrint: this.props.intl.formatMessage(
          messages.deleteGroupSuccess
        ),
      })
    );
  };

  edit(record) {
    this.setState({ editingKey: record.id });
  }
  save(form, record, last, next, index) {
    form.form.validateFields((error, row) => {
      if (error) {
        return;
      }

      this.props.dispatch(
        updateSetting({
          ...this.props.setupNotification.data,
          setting_group_receives_notices_financial: this.state.data.map((r) => {
            let rrr;
            if (r.isNew || record.id == r.id || r.code == row.name) {
              rrr = this.props.setupNotification.roles.data.find(
                (rr) => rr.code == row.name
              );
            } else {
              rrr = r;
            }
            return rrr.id;
          }),
          messagePrint: !record.isNew
            ? this.props.intl.formatMessage(messages.editGroupSuccess)
            : this.props.intl.formatMessage(messages.addGroupSuccess),
        })
      );
    });
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.setupNotification.loading !=
        nextProps.setupNotification.loading &&
      !nextProps.setupNotification.loading
    ) {
      this.setState({
        data: [
          ...(nextProps.setupNotification.data
            .setting_group_receives_notices_financial || []),
        ],
        editingKey: "",
      });
    }
  }

  render() {
    const components = {
      body: {
        cell: EditableCell,
      },
    };

    const columns = this.columns.map((col) => {
      if (!col.editable) {
        return col;
      }
      return {
        ...col,
        onCell: (record, index) => {
          return {
            record,
            inputType: col.dataIndex == "name" ? "selection" : "number",
            dataIndex: col.dataIndex,
            title: col.title,
            messageError: col.messageError,
            editing: this.isEditing(record),
            roles: {
              ...setupNotification.roles,
              data: setupNotification.roles.data.filter(
                (rr) =>
                  rr.code == record.code ||
                  !this.state.data.some((rrr) => rrr.code == rr.code)
              ),
            },
          };
        },
      };
    });

    const { editingKey } = this.state;
    const { setupNotification, auth_group } = this.props;
    const { loading } = setupNotification;

    return (
      <Page inner>
        <div className={styles.rolePage}>
          <Row>
            <WithRole
              roles={[
                config.ALL_ROLE_NAME.SETTING_RECEIVE_FINANCE_NOTIFICATION,
              ]}
            >
              <Button
                icon="plus"
                type="primary"
                ghost
                disabled={editingKey != ""}
                onClick={(e) => {
                  let now = Date.now();
                  this.setState({
                    data: this.state.data.concat([
                      {
                        id: now,
                        isNew: true,
                      },
                    ]),
                    editingKey: now,
                  });
                }}
              >
                {this.props.intl.formatMessage(messages.addGroup)}
              </Button>
            </WithRole>
          </Row>
          <Row
            gutter={24}
            style={{ marginTop: 12, paddingLeft: 12, paddingRight: 12 }}
          >
            <EditableContext.Provider
              value={{
                form: this.props.form,
              }}
            >
              <Table
                rowKey="id"
                components={components}
                dataSource={this.state.data}
                columns={columns}
                rowClassName="editable-row"
                bordered
                pagination={false}
                locale={{
                  emptyText: this.props.intl.formatMessage(messages.noData),
                }}
                loading={loading}
              />
            </EditableContext.Provider>
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
  setupNotification: makeSelectSetupNotification(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "setupNotification", reducer });
const withSaga = injectSaga({ key: "setupNotification", saga });

export default compose(withReducer, withSaga, withConnect)(injectIntl(Roles));
