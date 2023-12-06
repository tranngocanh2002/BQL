/**
 *
 * ResidentDetail
 *
 */

import dateFormat from "dateformat";
import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import Exception from "ant-design-pro/lib/Exception";
import {
  Avatar,
  Button,
  Card,
  Col,
  Divider,
  Dropdown,
  Icon,
  Menu,
  Modal,
  Result,
  Row,
  Table,
} from "antd";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import {
  changePhoneAction,
  defaultAction,
  fetchApartmentsAction,
  fetchBuildingAreaAction,
  fetchDetailResidentAction,
  removeApartmentAction,
  updateDetailAction,
  verifyPhoneOtpAction,
} from "./actions";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectResidentDetail from "./selectors";

import ModalEditResident from "./ModalEditResident";

import config from "../../../utils/config";

import moment from "moment";
import { injectIntl } from "react-intl";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import { selectAuthGroup } from "../../../redux/selectors";
import messages from "../messages";
import "./index.less";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import ModalChangePhone from "./ModalChangePhone";
import ModalOtp from "./ModalOtp";

/* eslint-disable react/prefer-stateless-function */
export class ResidentDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      record: (props.location.state || {}).record,
      visible: false,
      visibleAddApartment: false,
      visible2: false,
      value: 0,
      visible3: false,
      visible4: false,
      number: "",
      changePhone: undefined,
      oldPhone: (props.location.state || {}).record.phone,
      typeAuth: undefined,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.reload();
  }

  reload = (phone) => {
    let { params } = this.props.match;
    if (this.state.record || phone) {
      this.props.dispatch(
        fetchApartmentsAction({
          resident_user_phone: phone ? phone : this.state.oldPhone,
          pageSize: 1000,
        })
      );
    }
    this.props.dispatch(fetchDetailResidentAction({ id: params.id }));
    this.props.dispatch(fetchBuildingAreaAction());
  };

  componentWillReceiveProps(nextProps) {
    if (
      this.props.residentDetail.detail.data !=
      nextProps.residentDetail.detail.data
    ) {
      this.setState({
        record: nextProps.residentDetail.detail.data,
        visible: false,
        visibleAddApartment: false,
        visible2: false,
        value: 0,
        visible3: false,
        number: "",
        oldPhone: nextProps.residentDetail.detail.data.phone,
      });
    }
  }

  _onDelete = (record) => {
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmRemoveRes),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          removeApartmentAction({
            resident_phone: record.phone,
            apartment_id: record.apartment_id,
            callback: () => {
              this.props.dispatch(
                fetchApartmentsAction({
                  pageSize: 1000,
                  resident_user_phone: this.state.record.phone,
                })
              );
            },
          })
        );
      },
      onCancel() {},
    });
  };
  handlerUpdate = (values) => {
    const { dispatch } = this.props;

    dispatch(
      updateDetailAction({
        ...values,
        apartment_map_resident_user_id:
          this.state.record.apartment_map_resident_user_id,
        callback: () => {
          return fetchDetailResidentAction({
            id: this.state.record.apartment_map_resident_user_id,
          });
        },
        message: `${this.props.intl.formatMessage(
          messages.updateResident
        )} ${this.props.intl.formatMessage(messages.success)}`,
      })
    );
  };

  handlerUpdate2 = (values) => {
    const { dispatch } = this.props;
    dispatch(
      changePhoneAction({
        oldPhone: this.state.record.phone,
        newPhone: values.resident_phone,
        type_auth: values.type_auth,
        callback: () => {
          this.setState({
            visible3: values.type_auth === 0 ? true : false,
            visible4: values.type_auth === 1 ? true : false,
            visible2: false,
            number: values.resident_phone,
          });
        },
      })
    );
    this.setState({
      changePhone: values.resident_phone,
      oldPhone: this.state.record.phone,
      typeAuth: values.type_auth,
    });
  };

  handlerUpdate5 = () => {
    const { dispatch } = this.props;
    dispatch(
      changePhoneAction({
        oldPhone: this.state.oldPhone,
        newPhone: this.state.changePhone,
        type_auth: this.state.typeAuth,
        // callback: () => {
        //   this.setState({
        //     visible3: values.type_auth === 0 ? true : false,
        //     visible4: values.type_auth === 1 ? true : false,
        //     visible2: false,
        //     number: values.resident_phone,
        //   });
        // },
      })
    );
  };

  handlerUpdate3 = (values) => {
    const { dispatch } = this.props;

    dispatch(
      verifyPhoneOtpAction({
        otp: values.otp,
        oldPhone: this.state.record.phone,
        newPhone: this.state.changePhone,
        // token: values.token,
        callback: () => {
          this.setState({
            visible3: false,
            visible4: true,
          });
        },
      })
    );
  };

  onSuccessful = () => {
    this.setState({ visible4: false }),
      this.reload(`84${this.state.changePhone.slice(-9)}`);
  };

  render() {
    const { residentDetail, auth_group, language } = this.props;
    const { detail, updating, apartments } = residentDetail;
    const ACTION = [
      {
        id: 0,
      },
      {
        id: 1,
      },
    ];
    const dataSource = apartments.lst.sort((a, b) => {
      return a.deleted_at - b.deleted_at;
    });

    const formatMessage = this.props.intl.formatMessage;
    const recordResident = this.state.record;
    if (detail.data == -1) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={formatMessage(messages.notFound)}
            actions={
              <Button
                type="primary"
                onClick={() => this.props.history.push("/main/resident/list")}
              >
                {formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }

    const columns = [
      {
        title: (
          <span className={"nameTable"}>
            {formatMessage(messages.property)}
          </span>
        ),
        dataIndex: "apartment_name",
        key: "name",
      },
      {
        title: (
          <span className={"nameTable"}>
            {formatMessage(messages.blockFloor)}
          </span>
        ),
        width: 140,
        dataIndex: "apartment_parent_path",
        key: "parent_path",
      },
      {
        title: (
          <span className={"nameTable"}>{formatMessage(messages.role)}</span>
        ),
        width: 160,
        render: (text) => {
          return language === "en"
            ? config.TYPE_RESIDENT.find((t) => t.id == text).name_en
            : config.TYPE_RESIDENT.find((t) => t.id == text).name;
        },
        dataIndex: "type",
        key: "type",
      },
      // {
      //   title: (
      //     <span className={"nameTable"}>
      //       {formatMessage(messages.relationship)}
      //     </span>
      //   ),
      //   width: 100,
      //   dataIndex: "type_relationship_name",
      //   key: "type_relationship_name",
      // },
      {
        // align: "center",
        width: 100,
        title: (
          <span className={"nameTable"}>
            {formatMessage(messages.relationship)}
          </span>
        ),
        dataIndex: "type_relationship",
        key: "type_relationship",
        render: (text, record) => {
          const relationship = config.RELATIONSHIP_APARTMENT.find(
            (item) => item.id === text
          );
          if (record.type === 2) {
            return;
          }
          return this.props.language === "en"
            ? relationship.title_en
            : relationship.title;
        },
      },
      {
        title: (
          <span className={"nameTable"}>{formatMessage(messages.dayIn)}</span>
        ),
        width: 130,
        render: (text) => moment.unix(text).format("DD/MM/YYYY"),
        dataIndex: "created_at",
        key: "dayIn",
      },
      {
        title: (
          <span className={"nameTable"}>{formatMessage(messages.dayOut)}</span>
        ),
        width: 130,
        render: (text) => (text ? moment.unix(text).format("DD/MM/YYYY") : ""),
        dataIndex: "deleted_at",
        key: "dayOut",
      },
      {
        align: "center",
        fixed: "right",
        width: 100,
        title: (
          <span className={"nameTable"}>{formatMessage(messages.action)}</span>
        ),
        dataIndex: "deleted_at",
        key: "x",
        render: (text, record) => (
          <Row
            type="flex"
            style={
              text
                ? { color: "rgba(0, 0, 0, 0.25)" }
                : { color: "#F15A29", cursor: "pointer" }
            }
            align="middle"
            justify="center"
            onClick={(e) => {
              e.preventDefault();
              e.stopPropagation();
              !text && this._onDelete(record);
            }}
          >
            <i
              className="material-icons"
              style={{ fontSize: 18, marginRight: 6 }}
            >
              delete_outline
            </i>
            {formatMessage(messages.delete)}
          </Row>
        ),
      },
    ];

    if (
      !auth_group.checkRole([
        config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_DELETE_APARTMENT_RESIDENT,
      ])
    ) {
      columns.splice(columns.length - 1, 1);
    }

    return (
      <Page loading={detail.loading} inner={detail.loading}>
        <div className="residentDetailPage">
          <Row>
            <Col
              xl={24}
              xxl={12}
              lg={24}
              sm={24}
              xs={24}
              style={{
                paddingRight: 8,
                paddingLeft: 8,
                marginBottom: 24,
              }}
            >
              <Card
                title={
                  <span style={{ fontSize: 18 }}>
                    {formatMessage(messages.personalInformation)}
                  </span>
                }
                bordered={false}
                style={{ height: "100%", fontSize: 16 }}
                extra={
                  <WithRole
                    roles={[config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_UPDATE]}
                  >
                    <Dropdown
                      overlay={
                        <Menu
                          onClick={(item) => {
                            if (item.key === "0")
                              this.setState({ visible: true });
                            else {
                              this.setState({ visible2: true });
                            }
                          }}
                        >
                          {ACTION.map((item) => {
                            return (
                              <Menu.Item key={item.id}>
                                {item.id === 0
                                  ? formatMessage(messages.edit)
                                  : formatMessage(messages.phoneTitleChange)}
                              </Menu.Item>
                            );
                          })}
                        </Menu>
                      }
                    >
                      <Button>
                        {formatMessage(messages.optional)}
                        <Icon type="down" />
                      </Button>
                    </Dropdown>
                  </WithRole>
                }
              >
                <Row
                  type="flex"
                  gutter={24}
                  style={{ paddingTop: 8, paddingBottom: 24, minHeight: 757 }}
                >
                  <Col span={24} style={{ textAlign: "center" }}>
                    <Avatar
                      icon="user"
                      size={150}
                      src={getFullLinkImage(
                        recordResident && recordResident.avatar
                      )}
                    />
                  </Col>
                  <Col span={24}>
                    <Row type="flex" justify="space-between">
                      <Col xs={12} style={{ marginTop: 24, paddingRight: 12 }}>
                        <Row gutter={24} type="flex">
                          <Col
                            span={8}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.name)}:
                          </Col>
                          <Col
                            span={16}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident && recordResident.first_name}
                          </Col>
                        </Row>
                        <Divider />
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={8}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.phone)}:
                          </Col>
                          <Col
                            span={16}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident &&
                              `0${recordResident.phone.slice(-9)}`}
                          </Col>
                        </Row>
                        <Divider />
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={8}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            Email:
                          </Col>
                          <Col
                            span={16}
                            style={{
                              color: "#1B1B27",
                              fontWeight: "bold",
                              wordWrap: "break-word",
                            }}
                          >
                            {recordResident && recordResident.email}
                          </Col>
                        </Row>
                        <Divider />
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={8}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.birthday)}:
                          </Col>
                          <Col
                            span={16}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident &&
                              recordResident.birthday &&
                              dateFormat(
                                recordResident.birthday * 1000,
                                "dd/mm/yyyy"
                              )}
                          </Col>
                        </Row>
                        <Divider />
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={8}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.gender)}:
                          </Col>
                          <Col
                            span={16}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident && (
                              <span>
                                {recordResident.gender == 2
                                  ? formatMessage(messages.female)
                                  : formatMessage(messages.male)}
                              </span>
                            )}
                          </Col>
                        </Row>
                        <Divider />
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={8}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.task)}:
                          </Col>
                          <Col
                            span={16}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident && recordResident.work}
                          </Col>
                        </Row>
                        <Divider />
                      </Col>
                      <Col xs={12} style={{ marginTop: 16, paddingLeft: 12 }}>
                        <Row
                          gutter={24}
                          type="flex"
                          style={{
                            marginTop: 8,
                            marginBottom: 0,
                          }}
                        >
                          <Col
                            span={12}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.idCard)}:
                          </Col>
                          <Col
                            span={12}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            <span style={{ wordWrap: "break-word" }}>
                              {recordResident && recordResident.cmtnd}
                            </span>
                          </Col>
                        </Row>
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={12}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.issuedDate)}:
                          </Col>
                          <Col
                            span={12}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident &&
                              recordResident.ngay_cap_cmtnd &&
                              dateFormat(
                                recordResident.ngay_cap_cmtnd * 1000,
                                "dd/mm/yyyy"
                              )}
                          </Col>
                        </Row>
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={12}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.issuedPlace)}:
                          </Col>
                          <Col
                            span={12}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident && recordResident.noi_cap_cmtnd}
                          </Col>
                        </Row>
                        <Divider />
                        <Row
                          gutter={24}
                          type="flex"
                          style={{
                            marginTop: 8,
                            marginBottom: 0,
                          }}
                        >
                          <Col
                            span={12}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.nationality)}:
                          </Col>
                          <Col
                            span={12}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident &&
                            (recordResident.nationality == "vi" ||
                              !recordResident.nationality)
                              ? "Việt Nam"
                              : "Nước ngoài"}
                          </Col>
                        </Row>
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={12}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.visaNumber)}:
                          </Col>
                          <Col
                            span={12}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            <span style={{ wordWrap: "break-word" }}>
                              {recordResident && recordResident.so_thi_thuc}
                            </span>
                          </Col>
                        </Row>
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={12}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.expireDate)}:
                          </Col>
                          <Col
                            span={12}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            <span style={{ wordWrap: "break-word" }}>
                              {recordResident &&
                                recordResident.ngay_het_han_thi_thuc &&
                                dateFormat(
                                  recordResident.ngay_het_han_thi_thuc * 1000,
                                  "dd/mm/yyyy"
                                )}
                            </span>
                          </Col>
                        </Row>
                        <Divider />
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={12}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.tempRegisterDate)}:
                          </Col>
                          <Col
                            span={12}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident &&
                              recordResident.ngay_dang_ky_tam_chu &&
                              dateFormat(
                                recordResident.ngay_dang_ky_tam_chu * 1000,
                                "dd/mm/yyyy"
                              )}
                          </Col>
                        </Row>
                        <Divider />
                        <Row gutter={24} type="flex" style={{ marginTop: 8 }}>
                          <Col
                            span={12}
                            style={{ textAlign: "right", color: "#A4A4AA" }}
                          >
                            {formatMessage(messages.importDate)}:
                          </Col>
                          <Col
                            span={12}
                            style={{ color: "#1B1B27", fontWeight: "bold" }}
                          >
                            {recordResident &&
                              recordResident.ngay_dang_ky_nhap_khau &&
                              dateFormat(
                                recordResident.ngay_dang_ky_nhap_khau * 1000,
                                "dd/mm/yyyy"
                              )}
                          </Col>
                        </Row>
                        <Divider />
                      </Col>
                    </Row>
                  </Col>
                </Row>
              </Card>
            </Col>
            <Col
              xl={24}
              xxl={12}
              lg={24}
              sm={24}
              xs={24}
              style={{
                paddingRight: 8,
                paddingLeft: 8,
                marginBottom: 24,
              }}
            >
              <Card
                title={
                  <span style={{ fontSize: 18 }}>
                    {formatMessage(messages.property)}
                  </span>
                }
                bordered={false}
                style={{ height: "100%", fontSize: 16 }}
                extra={
                  <WithRole
                    roles={[config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_UPDATE]}
                  >
                    <Button
                      ghost
                      disabled
                      style={{ visibility: "hidden" }}
                      type="primary"
                      icon="edit"
                    >
                      {formatMessage(messages.edit)}
                    </Button>
                  </WithRole>
                }
              >
                <div style={{ height: "100%" }}>
                  <Table
                    rowKey="apartment_map_resident_user_id"
                    columns={columns}
                    bordered
                    scroll={{ x: 1000 }}
                    dataSource={dataSource}
                    locale={{ emptyText: formatMessage(messages.noData) }}
                    pagination={{
                      pageSize: 10,
                      showTotal: (total) =>
                        `${formatMessage(messages.total)} ${total}`,
                    }}
                    onChange={this.handleTableChange}
                  />
                </div>
              </Card>
            </Col>
          </Row>
          <ModalEditResident
            setState={this.setState.bind(this)}
            updating={updating}
            visible={this.state.visible}
            recordResident={recordResident}
            handlerUpdate={this.handlerUpdate}
          />
          <ModalChangePhone
            setState={this.setState.bind(this)}
            updating={updating}
            visible={this.state.visible2}
            recordResident={recordResident}
            handlerUpdate={this.handlerUpdate2}
            value={this.state.value}
          />
          <Modal
            visible={this.state.visible4}
            footer={[
              <Button key="submit" type="primary" onClick={this.onSuccessful}>
                {formatMessage(messages.close)}
              </Button>,
            ]}
          >
            <Result
              status="success"
              subTitle={formatMessage(messages.changePhoneSuccessContent)}
            />
          </Modal>
          {this.state.visible3 && (
            <ModalOtp
              setState={this.setState.bind(this)}
              updating={updating}
              visible={this.state.visible3}
              recordResident={recordResident}
              handlerUpdate={this.handlerUpdate3}
              value={this.state.value}
              number={this.state.number}
              handlerResend={this.handlerUpdate5}
            />
          )}
        </div>
      </Page>
    );
  }
}

ResidentDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  residentDetail: makeSelectResidentDetail(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "residentDetail", reducer });
const withSaga = injectSaga({ key: "residentDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ResidentDetail));
