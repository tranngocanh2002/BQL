/**
 *
 * SetupFeeElectricPage
 *
 */

import {
  Button,
  Form,
  Input,
  InputNumber,
  Popconfirm,
  Row,
  Table,
  Tooltip,
} from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import PropTypes from "prop-types";
import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import { globalStyles } from "utils/constants";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import WithRole from "../../../../../../components/WithRole";
import { selectAuthGroup } from "../../../../../../redux/selectors";
import { config, formatPrice } from "../../../../../../utils";
import messages from "../../../messages";
import makeSelectElectricServiceContainer from "../selectors";
import {
  createElectricFeeLevel,
  defaultAction,
  deleteElectricFeeLevel,
  fetchElectricFeeLevel,
  updateElectricFeeLevel,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectSetupFeeElectricPage from "./selectors";
import("./index.less");

const EditableContext = React.createContext();

class EditableCell extends React.Component {
  getInput = (dataIndex, last, next, getFieldValue, index) => {
    let min = 0;
    let max = 999999999;
    if (dataIndex == "from_level") {
      min = last ? last.to_level + 1 : 0;
      max = getFieldValue("to_level");
    }
    if (dataIndex == "to_level") {
      min = last ? last.to_level + 1 : 0;
    }
    if (this.props.inputType === "number") {
      return (
        <InputNumber
          disabled={index == 0 && dataIndex == "from_level"}
          style={{ width: "100%" }}
          min={min}
          max={max}
          maxLength={12}
        />
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
    if (dataIndex == "to_level" && !next) {
      return (
        <td {...restProps}>
          <span style={{ color: "#000" }}>
            <FormattedMessage {...messages.more} />
          </span>
        </td>
      );
    }
    if (dataIndex == "name" && !next && editing) {
      return (
        <td {...restProps}>
          <span>{`Định mức ${index + 1}`}</span>
        </td>
      );
    }
    if (dataIndex == "name_en" && !next && editing) {
      return (
        <td {...restProps}>
          <span>{`Quota ${index + 1}`}</span>
        </td>
      );
    }
    if (dataIndex == "to_level" && !!next) {
      if (!next.isNew)
        return (
          <td {...restProps}>
            <span>{next.from_level - 1}</span>
          </td>
        );
      return (
        <td {...restProps}>
          <span>
            {(getFieldValue("from_level") || record[dataIndex] + 1) - 1}
          </span>
        </td>
      );
    }
    if (dataIndex == "price" && !editing) {
      return (
        <td {...restProps}>
          <span> {formatPrice(record[dataIndex])}</span>
        </td>
      );
    }
    return (
      <td {...restProps}>
        {editing && (dataIndex == "price" || totaldata == index) ? (
          <Form.Item style={{ margin: 0 }}>
            {getFieldDecorator(dataIndex, {
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage
                      {...messages.emptyLevel}
                      values={{ title }}
                    />
                  ),
                },
                {
                  validator(rule, value, callback, source, options) {
                    if (
                      !!value &&
                      !!last &&
                      !!last.to_level &&
                      value <= last.to_level &&
                      dataIndex == "from_level"
                    ) {
                      callback(
                        <FormattedMessage
                          {...messages.leastLevel}
                          values={{ toLevel: last.to_level }}
                        />
                      );
                    }
                    callback();
                  },
                },
              ],
              initialValue: record[dataIndex],
            })(this.getInput(dataIndex, last, next, getFieldValue, index))}
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
export class SetupFeeElectricPage extends React.PureComponent {
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
            {this.props.intl.formatMessage(messages.level)}
          </span>
        ),
        dataIndex: this.props.language == "en" ? "name_en" : "name",
        key: this.props.language == "en" ? "name_en" : "name",
        editable: true,
      },
      {
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.from)} (kWh)
          </span>
        ),
        dataIndex: "from_level",
        editable: true,
        render: (text, record, index) => {
          return <span>{text}</span>;
        },
      },
      {
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.to)} (kWh)
          </span>
        ),
        dataIndex: "to_level",
        editable: true,
      },
      {
        align: "right",
        title: (
          <span className="nameTable">
            {this.props.intl.formatMessage(messages.price)} (đ/kWh)
          </span>
        ),
        dataIndex: "price",
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
                    disabled={
                      this.state.data[index - 1] &&
                      this.state.data[index - 1].to_level &&
                      form.getFieldValue("from_level") <=
                        this.state.data[index - 1].to_level
                    }
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
                placement="topLeft"
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
                  <i
                    className="fa fa-edit"
                    style={
                      props.auth_group.checkRole([
                        config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                      ])
                        ? globalStyles.icon
                        : globalStyles.iconDisabled
                    }
                  />
                </Tooltip>
              </a>
              &ensp;&ensp;|&ensp;&ensp;
              <Popconfirm
                disabled={
                  !props.auth_group.checkRole([
                    config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT,
                  ])
                }
                title={this.props.intl.formatMessage(messages.confirmDelete)}
                cancelText={this.props.intl.formatMessage(messages.cancel)}
                onConfirm={() => {
                  this.props.dispatch(
                    deleteElectricFeeLevel({
                      record,
                      callback: () => {
                        let newData = this.state.data.filter(
                          (a, i) => i != index
                        );
                        if (index == 0) {
                          newData.forEach((item, idx) => {
                            if (idx == 0) {
                              this.props.dispatch(
                                updateElectricFeeLevel({
                                  ...item,
                                  name: `Định mức ${idx + 1}`,
                                  name_en: `Quota ${idx + 1}`,
                                  from_level: 0,
                                })
                              );
                            } else {
                              this.props.dispatch(
                                updateElectricFeeLevel({
                                  ...item,
                                  name: `Định mức ${idx + 1}`,
                                  name_en: `Quota ${idx + 1}`,
                                })
                              );
                            }
                          });
                        } else if (
                          index == this.state.data.length - 1 &&
                          !!newData.length
                        ) {
                          this.props.dispatch(
                            updateElectricFeeLevel({
                              ...newData[newData.length - 1],
                              to_level: 999999999,
                            })
                          );
                        } else {
                          newData.forEach((item, idx) => {
                            if (idx == index - 1) {
                              this.props.dispatch(
                                updateElectricFeeLevel({
                                  ...item,
                                  to_level: record.to_level,
                                })
                              );
                            } else if (idx - index >= 0) {
                              this.props.dispatch(
                                updateElectricFeeLevel({
                                  ...item,
                                  name: `Định mức ${idx + 1}`,
                                  name_en: `Quota ${idx + 1}`,
                                })
                              );
                            }
                          });
                        }
                      },
                    })
                  );
                }}
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
      fetchElectricFeeLevel({
        service_map_management_id: this.props.electricServiceContainer.data.id,
      })
    );
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.SetupFeeElectricPage.success !=
        nextProps.SetupFeeElectricPage.success &&
      nextProps.SetupFeeElectricPage.success
    ) {
      this.setState({
        editingKey: "",
        data: nextProps.SetupFeeElectricPage.items,
      });
    }
    if (
      this.props.SetupFeeElectricPage.loading !=
        nextProps.SetupFeeElectricPage.loading &&
      !nextProps.SetupFeeElectricPage.loading
    ) {
      this.setState({
        editingKey: "",
        data: nextProps.SetupFeeElectricPage.items,
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
          createElectricFeeLevel({
            ...row,
            name: `Định mức ${index + 1}`,
            name_en: `Quota ${index + 1}`,
            description: "description",
            service_map_management_id:
              this.props.electricServiceContainer.data.id,
            service_id: this.props.electricServiceContainer.data.service_id,
            to_level: row.to_level || 999999999,
          })
        );
        if (last) {
          this.props.dispatch(
            updateElectricFeeLevel({
              ...last,
              to_level: row.from_level - 1,
              isNoti: false,
            })
          );
        }
      } else {
        if (!!last && index == this.state.data.length - 1) {
          this.props.dispatch(
            updateElectricFeeLevel({
              ...last,
              to_level: row.from_level ? row.from_level - 1 : last.to_level,
              isNoti: false,
            })
          );
        }
        this.props.dispatch(
          updateElectricFeeLevel({
            ...record,
            to_level: record.to_level,
            isNoti: true,
            price: row.price ? row.price : record.price,
            from_level: row.from_level ? row.from_level : record.from_level,
          })
        );
      }
    });
  }

  render() {
    const { getFieldDecorator, resetFields } = this.props.form;
    const { SetupFeeElectricPage } = this.props;
    const { data } = this.props.electricServiceContainer;
    const { editingKey } = this.state;
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
          inputType: "number",
          dataIndex: col.dataIndex,
          title: col.title,
          editing: this.isEditing(record),
          last: this.state.data[index - 1],
          next: this.state.data[index + 1],
          totaldata: this.state.data.length - 1,
          index,
        }),
      };
    });

    return (
      <Row className="setupFee">
        <WithRole roles={[config.ALL_ROLE_NAME.SETTING_SERVICE_MANAGERMENT]}>
          <Row style={{ paddingBottom: 16 }}>
            <Tooltip title={this.props.intl.formatMessage(messages.refresh)}>
              <Button
                shape="circle-outline"
                style={{ padding: 0, marginRight: 10 }}
                onClick={(e) => {
                  this.props.dispatch(
                    fetchElectricFeeLevel({
                      service_map_management_id:
                        this.props.electricServiceContainer.data.id,
                    })
                  );
                }}
                icon="reload"
                size="large"
              />
            </Tooltip>
            <Tooltip title={this.props.intl.formatMessage(messages.addNewFee)}>
              <Button
                style={{ marginRight: 10 }}
                disabled={editingKey != ""}
                onClick={() => {
                  let key = Date.now();
                  let newData = [...this.state.data];
                  if (newData.length > 0) {
                    newData[newData.length - 1] = {
                      ...newData[newData.length - 1],
                      to_level: newData[newData.length - 1].from_level + 1,
                    };
                    newData.push({
                      id: key,
                      from_level:
                        (newData[newData.length - 1].to_level || 0) + 1,
                      to_level: (newData[newData.length - 1].to_level || 0) + 1,
                      price: 0,
                      isNew: true,
                    });
                  } else {
                    newData.push({
                      id: key,
                      from_level: 0,
                      to_level: 0,
                      price: 0,
                      isNew: true,
                    });
                  }
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
          </Row>
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
              SetupFeeElectricPage.loading || SetupFeeElectricPage.updating
            }
          />
        </EditableContext.Provider>
      </Row>
    );
  }
}

SetupFeeElectricPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  SetupFeeElectricPage: makeSelectSetupFeeElectricPage(),
  electricServiceContainer: makeSelectElectricServiceContainer(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "SetupFeeElectricPage", reducer });
const withSaga = injectSaga({ key: "SetupFeeElectricPage", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(SetupFeeElectricPage));
