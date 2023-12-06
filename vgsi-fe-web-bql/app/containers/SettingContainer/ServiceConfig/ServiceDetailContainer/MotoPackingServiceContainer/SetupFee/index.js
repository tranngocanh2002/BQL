/**
 *
 * SetupFeeMotoPackingPage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Form, Input, Popconfirm, Row, Table, Tooltip } from "antd";
import { globalStyles } from "utils/constants";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import InputNumberFormat from "../../../../../../components/InputNumberFormat";
import WithRole from "../../../../../../components/WithRole";
import { selectAuthGroup } from "../../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../../utils";
import messages from "../../../messages";
import makeSelectMotoPackingServiceContainer from "../selectors";
import {
  createWaterFeeLevel,
  defaultAction,
  deleteWaterFeeLevel,
  fetchWaterFeeLevel,
  updateWaterFeeLevel,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectSetupFeeMotoPackingPage from "./selectors";
import("./index.less");

const EditableContext = React.createContext();

class EditableCell extends React.Component {
  getInput = (dataIndex, last, next, getFieldValue) => {
    let min = 0;
    if (this.props.inputType === "number") {
      return (
        <InputNumberFormat style={{ width: "100%" }} min={min} maxLength={13} />
      );
    }
    return <Input style={{ width: "100%" }} />;
  };

  renderCell = ({ getFieldDecorator, getFieldValue }) => {
    const {
      editing,
      dataIndex,
      title,
      inputType,
      record,
      index,
      children,
      last,
      next,
      totaldata,
      ...restProps
    } = this.props;
    if (dataIndex == "price" && !editing) {
      return (
        <td {...restProps}>
          <strong>{formatPrice(record[dataIndex])}</strong>
        </td>
      );
    }
    return (
      <td {...restProps}>
        {editing ? (
          <Form.Item style={{ margin: 0 }}>
            {getFieldDecorator(dataIndex, {
              rules: [
                {
                  required: true,
                  message:
                    dataIndex !== "price" ? (
                      <FormattedMessage
                        {...messages.emptyLevel}
                        values={{ title: title.props.children }}
                      />
                    ) : (
                      <FormattedMessage {...messages.priceError} />
                    ),
                },
              ],
              initialValue: record[dataIndex],
            })(this.getInput(dataIndex, last, next, getFieldValue))}
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
export class SetupFeeMotoPackingPage extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      data: [],
      editingKey: "",
    };
    this.columns = [
      {
        align: "center",
        title: <span className="nameTable">#</span>,
        dataIndex: "#",
        width: 50,
        render: (text, record, index) => {
          return <span>{index + 1}</span>;
        },
      },
      {
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.code)}
          </span>
        ),
        width: "12%",
        dataIndex: "code",
        render: (text) => <span>{text}</span>,
      },
      {
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.typeVehicle)}
          </span>
        ),
        dataIndex: "name",
        editable: true,
      },
      {
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.typeVehicle)} (EN)
          </span>
        ),
        dataIndex: "name_en",
        editable: true,
      },
      {
        align: "left",
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.unitPrice)}
          </span>
        ),
        dataIndex: "price",
        width: "15%",
        editable: true,
      },
      {
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.note)}
          </span>
        ),
        dataIndex: "description",
        editable: true,
      },
      {
        width: 120,
        align: "center",
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.action)}
          </span>
        ),
        dataIndex: "operation",
        render: (text, record, index) => {
          const { editingKey } = this.state;
          const editable = this.isEditing(record);
          return editable ? (
            <span>
              <EditableContext.Consumer>
                {(form) => (
                  <a
                    href="javascript:;"
                    onClick={() =>
                      this.save(
                        form,
                        record,
                        this.state.data[index - 1],
                        this.state.data[index + 1],
                        index
                      )
                    }
                    style={{ marginRight: 8 }}
                  >
                    <Tooltip
                      title={this.props.intl.formatMessage(messages.save)}
                    >
                      <i
                        className="material-icons"
                        style={{ fontSize: 18, marginRight: 16 }}
                      >
                        save
                      </i>
                    </Tooltip>
                  </a>
                )}
              </EditableContext.Consumer>
              <Popconfirm
                title={this.props.intl.formatMessage(messages.confirmCancel)}
                cancelText={this.props.intl.formatMessage(messages.cancel)}
                onConfirm={() => this.cancel(record)}
              >
                <a style={{ color: "red" }}>
                  <Tooltip
                    title={this.props.intl.formatMessage(messages.cancel)}
                  >
                    <i className="material-icons" style={{ fontSize: 18 }}>
                      close
                    </i>
                  </Tooltip>
                </a>
              </Popconfirm>
            </span>
          ) : (
            <>
              <a
                disabled={editingKey !== ""}
                onClick={() =>
                  props.auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ]) && this.edit(record.id)
                }
                style={
                  props.auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ])
                    ? globalStyles.row
                    : globalStyles.rowDisabled
                }
              >
                <Tooltip title={this.props.intl.formatMessage(messages.edit)}>
                  <i className="fa fa-edit" style={{ fontSize: 18 }} />
                </Tooltip>
              </a>
              &ensp;&ensp;|&ensp;&ensp;
              <Popconfirm
                title={this.props.intl.formatMessage(messages.confirmDelete)}
                cancelText={this.props.intl.formatMessage(messages.cancel)}
                onConfirm={() => {
                  this.props.dispatch(deleteWaterFeeLevel(record));
                }}
                disabled={
                  !props.auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ])
                }
              >
                <a
                  disabled={editingKey !== ""}
                  style={
                    props.auth_group.checkRole([
                      config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                    ])
                      ? globalStyles.row
                      : globalStyles.rowDisabled
                  }
                >
                  <Tooltip
                    title={this.props.intl.formatMessage(messages.delete)}
                  >
                    <i
                      className="fa fa-trash"
                      style={
                        props.auth_group.checkRole([
                          config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                        ])
                          ? globalStyles.iconDelete
                          : globalStyles.iconDisabled
                      }
                    />
                  </Tooltip>
                </a>
              </Popconfirm>
            </>
          );
        },
      },
    ];
  }

  isEditing = (record) => record.id === this.state.editingKey;

  cancel = (record) => {
    if (record.isNew) {
      this.setState({
        editingKey: "",
        data: this.state.data.filter((ff) => !ff.isNew),
      });
    } else {
      this.setState({ editingKey: "" });
    }
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(
      fetchWaterFeeLevel({
        service_map_management_id:
          this.props.motoPackingServiceContainer.data.id,
      })
    );
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.SetupFeeMotoPackingPage.success !=
        nextProps.SetupFeeMotoPackingPage.success &&
      nextProps.SetupFeeMotoPackingPage.success
    ) {
      this.setState({
        editingKey: "",
        data: nextProps.SetupFeeMotoPackingPage.items,
      });
    }
    if (
      this.props.SetupFeeMotoPackingPage.loading !=
        nextProps.SetupFeeMotoPackingPage.loading &&
      !nextProps.SetupFeeMotoPackingPage.loading
    ) {
      this.setState({
        editingKey: "",
        data: nextProps.SetupFeeMotoPackingPage.items,
      });
    }
  }

  edit(key) {
    this.setState({ editingKey: key });
  }
  save(form, record, last, next, index) {
    form.validateFields((error, row) => {
      if (error) {
        return;
      }
      if (record.isNew) {
        this.props.dispatch(
          createWaterFeeLevel({
            ...row,
            service_map_management_id:
              this.props.motoPackingServiceContainer.data.id,
            service_id: this.props.motoPackingServiceContainer.data.service_id,
          })
        );
      } else {
        if (!!last && index == this.state.data.length - 1) {
          this.props.dispatch(
            updateWaterFeeLevel({
              ...last,
              to_level: row.from_level - 1,
              isNoti: false,
            })
          );
        }
        this.props.dispatch(
          updateWaterFeeLevel({
            ...record,
            ...row,
            to_level: row.to_level || 999999999,
            isNoti: true,
          })
        );
      }
    });
  }

  render() {
    const { getFieldDecorator, resetFields } = this.props.form;
    const { SetupFeeMotoPackingPage } = this.props;
    const { data } = this.props.motoPackingServiceContainer;
    const { editingKey } = this.state;
    const formatMessage = this.props.intl.formatMessage;
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
        onCell: (record, index) => ({
          record,
          inputType: col.dataIndex == "price" ? "number" : "string",
          dataIndex: col.dataIndex,
          title: col.title,
          editing: this.isEditing(record),
        }),
      };
    });

    return (
      <Row className="setupFee">
        <WithRole roles={[config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT]}>
          <Tooltip title={formatMessage(messages.addLevelFee)}>
            <Button
              style={{
                marginRight: 10,
                marginTop: 16,
                marginBottom: 16,
                zIndex: 9,
              }}
              disabled={editingKey != ""}
              onClick={() => {
                let key = Date.now();
                let newData = [...this.state.data];
                newData.push({
                  id: key,
                  code: `MX${this.state.data.length + 1}`,
                  name: "",
                  description: "",
                  price: undefined,
                  isNew: true,
                });
                this.setState({
                  data: newData,
                  editingKey: key,
                });
              }}
              icon="plus"
              shape="circle"
              size="large"
            />
          </Tooltip>
        </WithRole>
        <EditableContext.Provider value={this.props.form}>
          <Table
            rowKey="id"
            style={{ marginTop: 0 }}
            components={components}
            // bordered
            dataSource={this.state.data}
            columns={columns}
            rowClassName="editable-row"
            bordered
            pagination={false}
            loading={
              SetupFeeMotoPackingPage.loading ||
              SetupFeeMotoPackingPage.updating
            }
          />
        </EditableContext.Provider>
      </Row>
    );
  }
}

SetupFeeMotoPackingPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  SetupFeeMotoPackingPage: makeSelectSetupFeeMotoPackingPage(),
  motoPackingServiceContainer: makeSelectMotoPackingServiceContainer(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "SetupFeeMotoPackingPage", reducer });
const withSaga = injectSaga({ key: "SetupFeeMotoPackingPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(SetupFeeMotoPackingPage));
