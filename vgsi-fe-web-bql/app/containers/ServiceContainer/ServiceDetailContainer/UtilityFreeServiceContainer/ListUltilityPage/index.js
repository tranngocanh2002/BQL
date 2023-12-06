/**
 *
 * ListUltilityPage
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Avatar, Button, Col, Modal, Row, Table, Tooltip } from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import {
  defaultAction,
  deleteListUltility,
  fetchAllListUltility,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectListUltilityPage from "./selectors";

import queryString from "query-string";
import WithRole from "../../../../../components/WithRole";
import { getFullLinkImage } from "../../../../../connection";
import { selectAuthGroup } from "../../../../../redux/selectors";
import { config } from "../../../../../utils";
import { GLOBAL_COLOR } from "../../../../../utils/constants";
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
          `/main/service/detail/utility-free/payment?${queryString.stringify({
            ...this.state.filter,
            page: 1,
          })}`
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

    params.keyword = params.keyword || "";

    this.setState(
      { current: params.page, keyword: params.keyword, filter: params },
      () => {
        this.props.dispatch(fetchAllListUltility(params));
      }
    );
  };

  _onDelete = (record) => {
    console.log("_onDelete::record", record);
    Modal.confirm({
      autoFocusButton: null,
      title: "Bạn chắc chắn muốn xoá tiện ích này?",
      okText: "Đồng ý",
      okType: "danger",
      cancelText: "Huỷ",
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
      `/main/service/detail/utility-free/edit/${record.id}`,
      { record }
    );
  };

  render() {
    const {
      listUlilityPage,
      dispatch,
      utilityFreeServiceContainer,
      auth_group,
    } = this.props;
    const { current } = this.state;
    const columns = [
      {
        title: <span className={styles.nameTable}>#</span>,
        dataIndex: "id",
        key: "id",
        render: (text, record, index) => <span>{index + 1}</span>,
      },
      {
        title: <span className={styles.nameTable}>Ảnh cover</span>,
        dataIndex: "medias_",
        key: "medias_",
        render: (text, record, index) => {
          return (
            <Avatar
              shape="square"
              size="large"
              icon="user"
              src={getFullLinkImage(
                record.medias ? record.medias.logo : undefined
              )}
            />
          );
        },
      },
      {
        title: <span className={styles.nameTable}>Tên tiện ích</span>,
        dataIndex: "name",
        key: "name",
        // render: (text) => <span className={styles.nameTable} >{text}</span>
      },
      {
        title: <span className={styles.nameTable}>Giờ hoạt động</span>,
        dataIndex: "name_",
        key: "name_",
        render: (text, record) => (
          <span>{`${record.hours_open} - ${record.hours_close}`}</span>
        ),
      },
      {
        width: 120,
        align: "center",
        title: <span className={styles.nameTable}>Thao tác</span>,
        dataIndex: "",
        key: "x",
        render: (text, record, index) => (
          <Row type="flex" align="middle" justify="center">
            <Tooltip title="Chỉnh sửa tiện ích">
              <Row
                type="flex"
                align="middle"
                style={{ color: GLOBAL_COLOR, cursor: "pointer" }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  this._onEdit(record);
                }}
              >
                <i className="fa fa-edit" style={{ fontSize: 18 }}></i>
              </Row>
            </Tooltip>
            &ensp;&ensp;| &ensp;&ensp;
            <Tooltip title="Xoá tiện ích">
              <Row
                type="flex"
                align="middle"
                style={{ color: "#F15A29", cursor: "pointer" }}
                onClick={(e) => {
                  e.preventDefault();
                  e.stopPropagation();
                  this._onDelete(record);
                }}
              >
                <i className="fa fa-trash" style={{ fontSize: 18 }}></i>
              </Row>
            </Tooltip>
          </Row>
        ),
      },
    ];

    if (!auth_group.checkRole([config.ALL_ROLE_NAME.SERVICE_MANAGEMENT])) {
      columns.splice(columns.length - 1, 1);
    }
    return (
      <Row className={styles.ListUltilityPage}>
        <WithRole roles={[config.ALL_ROLE_NAME.SERVICE_MANAGEMENT]}>
          <Col>
            <Row type="flex" align="middle" style={{ marginBottom: 20 }}>
              <Tooltip title="Làm mới trang">
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
              <Tooltip title="Thêm mới">
                <Button
                  style={{ marginRight: 10 }}
                  onClick={() => {
                    this.props.history.push(
                      "/main/service/detail/utility-free/add"
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
            locale={{ emptyText: "Không có dữ liệu" }}
            bordered
            pagination={false}
            onChange={this.handleTableChange}
            // scroll={{ x: 1366 }}
            onRow={(record, rowIndex) => {
              return {
                onClick: (event) => {
                  this.props.history.push(
                    `/main/service/detail/utility-free/detail/${record.id}/booking`,
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
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "listUlilityPage", reducer });
const withSaga = injectSaga({ key: "listUlilityPage", saga });

export default compose(withReducer, withSaga, withConnect)(ListUltilityPage);
