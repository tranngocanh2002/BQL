/**
 *
 * ApartmentDetail
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import Exception from "ant-design-pro/lib/Exception";
import {
  Button,
  Card,
  Col,
  Icon,
  Modal,
  Row,
  Table,
  Tooltip,
  Typography,
} from "antd";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import { FormattedMessage, injectIntl } from "react-intl";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import WithRole from "../../../components/WithRole";
import { selectAuthGroup } from "../../../redux/selectors";
import config, { TYPE_RESIDENT } from "../../../utils/config";
import FeeList from "./FeeList";
import ModalAddMember from "./ModalAddMember";
import ModalEditApartment from "./ModalEditApartment";
import ModalEditMember from "./ModalEditMember";
import {
  addMemberAction,
  defaultAction,
  fetchAllApartmentType,
  fetchAllResidentByPhoneAction,
  fetchBuildingAreaAction,
  fetchDetailApartmentAction,
  fetchMemberAction,
  removeMemberAction,
  updateDetailAction,
  updateMemberAction,
} from "./actions";
import messages, { scope } from "./messages";
import reducer from "./reducer";
import saga from "./saga";
import makeSelectApartmentDetail from "./selectors";
import Paragraph from "antd/lib/typography/Paragraph";
/* eslint-disable react/prefer-stateless-function */
export class ApartmentDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      record: (props.location.state || {}).record,
      visible: false,
      visibleAddMember: false,
      visibleUpdateMember: false,
      currentEdit: undefined,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    let { params } = this.props.match;
    this.props.dispatch(fetchMemberAction({ apartment_id: params.id }));
    this.props.dispatch(fetchDetailApartmentAction({ id: params.id }));
    this.props.dispatch(fetchAllApartmentType());
    this.props.dispatch(fetchBuildingAreaAction());
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.apartmentDetail.detail.data !=
      nextProps.apartmentDetail.detail.data
    ) {
      this.setState({
        record: nextProps.apartmentDetail.detail.data,
        // visible: false,
        visibleUpdateMember: false,
      });
    }
    if (
      this.props.apartmentDetail.success != nextProps.apartmentDetail.success &&
      nextProps.apartmentDetail.success
    ) {
      this.setState({
        visibleAddMember: false,
        visible: false,
      });
    }
  }

  _onDelete = (record) => {
    let { params } = this.props.match;
    Modal.confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage({
        id: `${scope}.deleteMemberTitle`,
      }),
      okText: this.props.intl.formatMessage({
        id: `${scope}.deleteMemberOkText`,
      }),
      okType: "danger",
      cancelText: this.props.intl.formatMessage({
        id: `${scope}.deleteMemberCancelText`,
      }),
      onOk: () => {
        this.props.dispatch(
          removeMemberAction({
            resident_phone: record.phone,
            apartment_id: params.id,
            callback: () => {
              this.props.dispatch(
                fetchMemberAction({ apartment_id: parseInt(params.id) })
              );
              this.props.dispatch(
                fetchDetailApartmentAction({ id: params.id })
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
        building_area_id: parseInt(values.building_area_id),
        id: this.state.record.id,
        capacity: values.capacity,
        callback: () => {
          let { params } = this.props.match;
          this.props.dispatch(fetchDetailApartmentAction({ id: params.id }));
          this.props.dispatch(fetchMemberAction({ apartment_id: params.id }));
        },
      })
    );
  };

  _onEditType = (record) => {
    this.setState(
      {
        currentEdit: {
          ...record,
        },
      },
      () => {
        this.setState({ visibleUpdateMember: true });
      }
    );
  };

  handlerAddMember = (values) => {
    const { dispatch } = this.props;

    dispatch(
      addMemberAction({
        ...values,
        apartment_id: this.state.record.id,
        type: parseInt(values.type),
        type_relationship: values.type_relationship || 0,
        callback: () => {
          this.setState({
            visibleAddMember: false,
          });
          let { params } = this.props.match;
          this.props.dispatch(fetchMemberAction({ apartment_id: params.id }));
          this.props.dispatch(fetchDetailApartmentAction({ id: params.id }));
        },
      })
    );
  };

  handlerEditMember = (values) => {
    const { dispatch } = this.props;

    dispatch(
      updateMemberAction({
        id: this.state.currentEdit.apartment_map_resident_user_id,
        type: parseInt(values.type),
        type_relationship: values.type_relationship || 0,
        callback: () => {
          this.setState({
            visibleUpdateMember: false,
          });
          let { params } = this.props.match;
          this.props.dispatch(fetchMemberAction({ apartment_id: params.id }));
          this.props.dispatch(fetchDetailApartmentAction({ id: params.id }));
        },
      })
    );
  };

  render() {
    const { apartmentDetail, auth_group, dispatch, intl, language } =
      this.props;
    const {
      members,
      removing,
      updating,
      detail,
      buildingArea,
      updatingMember,
      addingMember,
      apartment_type,
      allResident,
    } = apartmentDetail;
    const recordApartment = this.state.record;

    const dataSource = members.lst.sort((a, b) => {
      return a.deleted_at - b.deleted_at;
    });

    const count = dataSource.reduce((acc, cur) => {
      if (cur.deleted_at) {
        return acc;
      }
      return acc + 1;
    }, 0);

    const actionText = intl.formatMessage({ ...messages.action });
    const editText = intl.formatMessage({ ...messages.edit });

    if (detail.data == -1) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={<FormattedMessage {...messages.noData} />}
            actions={
              <Button
                type="primary"
                onClick={() => this.props.history.push("/main/apartment/list")}
              >
                <FormattedMessage {...messages.btnBack} />
              </Button>
            }
          />
        </Page>
      );
    }

    const columns = [
      {
        // width: 240,
        title: (
          <span className={"nameTable"}>
            <FormattedMessage {...messages.memberName} />
          </span>
        ),
        dataIndex: "first_name",
        key: "full_name",
      },
      {
        align: "left",
        width: 180,
        title: (
          <span className={"nameTable"}>
            <FormattedMessage {...messages.memberPhone} />
          </span>
        ),
        dataIndex: "phone",
        key: "phone",
        render: (text, record) => <span>0{record.phone.slice(-9)}</span>,
      },
      {
        // align: "center",
        width: 200,
        title: (
          <span className={"nameTable"}>
            <FormattedMessage {...messages.memberRole} />
          </span>
        ),
        render: (text) => (
          <span>
            {this.props.language === "en"
              ? TYPE_RESIDENT[text].name_en
              : TYPE_RESIDENT[text].name}
          </span>
        ),
        key: "type",
        dataIndex: "type",
      },
      {
        // align: "center",
        width: 200,
        title: (
          <span className={"nameTable"}>
            <FormattedMessage {...messages.relationship} />
          </span>
        ),
        dataIndex: "type_relationship",
        key: "type_relationship",
        render: (text) => {
          const relationship =
            config.RELATIONSHIP_APARTMENT.find((item) => item.id == text) ||
            config.RELATIONSHIP_APARTMENT[7];
          return this.props.language === "en"
            ? relationship.title_en
            : relationship.title;
        },
      },
      {
        // align: "center",
        width: 170,
        title: (
          <span className={"nameTable"}>
            <FormattedMessage {...messages.dayIn} />
          </span>
        ),
        render: (text) => moment.unix(text).format("DD/MM/YYYY"),
        dataIndex: "created_at",
        key: "created_at",
      },
      {
        // align: "center",
        width: 170,
        title: (
          <span className={"nameTable"}>
            <FormattedMessage {...messages.dayOut} />
          </span>
        ),
        render: (text) => {
          return !text ? "" : moment.unix(text).format("DD/MM/YYYY");
        },
        dataIndex: "deleted_at",
        key: "deleted_at",
      },
      {
        fixed: "right",
        width: 100,
        title: <span className={"nameTable"}>{actionText}</span>,
        dataIndex: "deleted_at",
        key: "x",
        render: (text, record) => {
          return text ? (
            <Row type="flex" justify="space-between" align="middle">
              <Icon
                style={{ color: "rgba(0, 0, 0, 0.25)" }}
                type="edit"
                disabled
              />
              |
              <Icon
                style={{ color: "rgba(0, 0, 0, 0.25)" }}
                type="delete"
                disabled
              />
            </Row>
          ) : (
            <Row type="flex" justify="space-between" align="middle">
              {auth_group.checkRole([
                config.ALL_ROLE_NAME
                  .REAL_ESTATE_MANAGEMENT_UPDATE_RESIDENT_USER,
              ]) ? (
                <Tooltip title={editText}>
                  <Icon
                    className={"iconAction"}
                    type="edit"
                    onClick={(e) => {
                      e.stopPropagation();
                      e.preventDefault();
                      this._onEditType(record);
                    }}
                  />
                </Tooltip>
              ) : (
                <Icon
                  style={{ color: "rgba(0, 0, 0, 0.25)" }}
                  type="edit"
                  disabled
                />
              )}
              |
              <Tooltip title={<FormattedMessage {...messages.deleteMember} />}>
                {auth_group.checkRole([
                  config.ALL_ROLE_NAME
                    .REAL_ESTATE_MANAGEMENT_DELETE_RESIDENT_USER,
                ]) ? (
                  <Icon
                    className={"iconAction"}
                    style={{ color: "red" }}
                    type="delete"
                    onClick={(e) => {
                      e.stopPropagation();
                      e.preventDefault();
                      this._onDelete(record);
                    }}
                  />
                ) : (
                  <Icon
                    style={{ color: "rgba(0, 0, 0, 0.25)" }}
                    type="delete"
                    disabled
                  />
                )}
              </Tooltip>
            </Row>
          );
        },
      },
    ];
    const { tree } = buildingArea;

    return (
      <Page>
        <div className="apartmentDetailPage">
          <Row>
            <Col
              span={24}
              style={{ paddingRight: 8, paddingLeft: 8, marginBottom: 24 }}
            >
              <Card
                title={<FormattedMessage {...messages.apartmentDetail} />}
                bordered={false}
                extra={
                  <WithRole
                    roles={[config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_UPDATE]}
                  >
                    <Button
                      ghost
                      type="primary"
                      onClick={() => {
                        this.setState({ visible: true });
                      }}
                      icon="edit"
                    >
                      {editText}
                    </Button>
                  </WithRole>
                }
              >
                <Row
                  gutter={[24, 16]}
                  style={{ paddingTop: 24, paddingBottom: 24, paddingLeft: 48 }}
                >
                  <Col span={8}>
                    <Row gutter={24} type="flex">
                      <Col
                        span={10}
                        offset={1}
                        style={{
                          padding: 0,
                          color: "#A4A4AA",
                          textAlign: "left",
                        }}
                      >
                        <FormattedMessage {...messages.apartmentCode} />:
                      </Col>
                      <Col
                        span={13}
                        style={{ padding: 0, color: "red", fontWeight: "bold" }}
                      >
                        {recordApartment && recordApartment.code}
                      </Col>
                    </Row>
                  </Col>
                  <Col span={8}>
                    <Row gutter={24} type="flex">
                      <Col
                        span={10}
                        offset={1}
                        style={{
                          padding: 0,
                          color: "#A4A4AA",
                          textAlign: "left",
                        }}
                      >
                        <FormattedMessage {...messages.apartmentArea} />:
                      </Col>
                      <Col
                        span={13}
                        style={{
                          padding: 0,
                          color: "#1B1B27",
                          fontWeight: "bold",
                        }}
                      >
                        {`${
                          (recordApartment && recordApartment.capacity) || "__"
                        } m2`}
                      </Col>
                    </Row>
                  </Col>
                  <Col span={8}>
                    <Row gutter={24} type="flex">
                      <Col
                        span={10}
                        offset={1}
                        style={{
                          padding: 0,
                          color: "#A4A4AA",
                          textAlign: "left",
                        }}
                      >
                        <FormattedMessage {...messages.status} />:
                      </Col>
                      <Col
                        span={13}
                        style={{
                          padding: 0,
                          color: "#1B1B27",
                          fontWeight: "bold",
                        }}
                      >
                        {recordApartment
                          ? language == "en"
                            ? (
                                config.STATUS_APARTMENT.find(
                                  (t) => t.id == recordApartment.status
                                ) || { name: "" }
                              ).name_en
                            : (
                                config.STATUS_APARTMENT.find(
                                  (t) => t.id == recordApartment.status
                                ) || { name: "" }
                              ).name
                          : ""}
                      </Col>
                    </Row>
                  </Col>
                  <Col span={8}>
                    <Row gutter={24} type="flex">
                      <Col
                        span={10}
                        offset={1}
                        style={{
                          padding: 0,
                          color: "#A4A4AA",
                          textAlign: "left",
                        }}
                      >
                        <FormattedMessage {...messages.apartmentName} />:
                      </Col>
                      <Col
                        span={13}
                        style={{
                          padding: 0,
                          color: "#1B1B27",
                          fontWeight: "bold",
                        }}
                      >
                        {recordApartment && recordApartment.name}
                      </Col>
                    </Row>
                  </Col>
                  <Col span={8}>
                    <Row gutter={24} type="flex">
                      <Col
                        span={10}
                        offset={1}
                        style={{
                          padding: 0,
                          color: "#A4A4AA",
                          textAlign: "left",
                        }}
                      >
                        <FormattedMessage {...messages.handoverStatus} />:
                      </Col>
                      <Col
                        span={13}
                        style={{
                          padding: 0,
                          color: "#1B1B27",
                          fontWeight: "bold",
                        }}
                      >
                        {(!!recordApartment &&
                          !!recordApartment.date_delivery) ||
                        recordApartment.status == 1 ? (
                          <FormattedMessage {...messages.homeReceived} />
                        ) : (
                          <FormattedMessage {...messages.homeNotReceived} />
                        )}
                      </Col>
                    </Row>
                  </Col>
                  {/* {!!recordApartment &&
                    recordApartment.status == 1 &&
                    !!recordApartment.date_received && ( */}
                  <Col span={8}>
                    <Row gutter={24} type="flex">
                      <Col
                        span={10}
                        offset={1}
                        style={{
                          padding: 0,
                          color: "#A4A4AA",
                          textAlign: "left",
                        }}
                      >
                        <FormattedMessage {...messages.receiveDay} />:
                      </Col>
                      <Col
                        span={13}
                        style={{
                          padding: 0,
                          color: "red",
                          fontWeight: "bold",
                        }}
                      >
                        {!!recordApartment &&
                        recordApartment.status == 1 &&
                        !!recordApartment.date_received
                          ? moment
                              .unix(recordApartment.date_received)
                              .format("DD/MM/YYYY")
                          : ""}
                      </Col>
                    </Row>
                  </Col>
                  {/* )} */}
                  <Col span={8}>
                    <Row gutter={24} type="flex">
                      <Col
                        span={10}
                        offset={1}
                        style={{
                          padding: 0,
                          color: "#A4A4AA",
                          textAlign: "left",
                        }}
                      >
                        <FormattedMessage {...messages.block} />:
                      </Col>
                      <Col
                        span={13}
                        style={{
                          padding: 0,
                          color: "#1B1B27",
                          fontWeight: "bold",
                        }}
                      >
                        {recordApartment ? recordApartment.parent_path : ""}
                      </Col>
                    </Row>
                  </Col>
                  {/* {
										!!recordApartment && recordApartment.status == 1 && <Col span={12} >
											<Row gutter={24} type='flex' style={{ marginTop: 16 }} >
												<Col span={12} style={{ padding: 0, color: '#A4A4AA', textAlign: 'right' }} >
													Người bàn giao:
                      						</Col>
												<Col span={11} offset={1} style={{ padding: 0, color: 'black', fontWeight: 'bold' }} >
													{recordApartment.handover}
												</Col>
											</Row>
										</Col>
									} */}
                  <Col span={8}>
                    <Row gutter={24} type="flex">
                      <Col
                        span={10}
                        offset={1}
                        style={{
                          padding: 0,
                          color: "#A4A4AA",
                          textAlign: "left",
                        }}
                      >
                        <FormattedMessage {...messages.dayHandover} />:
                      </Col>
                      <Col
                        span={13}
                        style={{ padding: 0, color: "red", fontWeight: "bold" }}
                      >
                        {!!recordApartment && !!recordApartment.date_delivery
                          ? moment
                              .unix(recordApartment.date_delivery)
                              .format("DD/MM/YYYY")
                          : !!recordApartment &&
                            recordApartment.status == 1 &&
                            !!recordApartment.date_received
                          ? moment
                              .unix(recordApartment.date_received)
                              .format("DD/MM/YYYY")
                          : ""}
                      </Col>
                    </Row>
                  </Col>
                  {window.innerWidth > 1100 && (
                    <Col span={8}>
                      <Row gutter={24} type="flex">
                        <Col
                          span={10}
                          offset={1}
                          style={{
                            padding: 0,
                            color: "#A4A4AA",
                            textAlign: "left",
                          }}
                        >
                          <span
                            style={{ color: "white", visibility: "hidden" }}
                          >
                            {"666"}
                          </span>
                        </Col>
                        <Col
                          span={13}
                          style={{
                            padding: 0,
                            color: "red",
                            fontWeight: "bold",
                          }}
                        >
                          <span
                            style={{ color: "white", visibility: "hidden" }}
                          >
                            {"666"}
                          </span>
                        </Col>
                      </Row>
                    </Col>
                  )}
                  <Col span={24} style={{ wordWrap: "break-word" }}>
                    <Row type="flex">
                      <Col span={3}>
                        <Col
                          span={19}
                          offset={1}
                          style={{
                            padding: 0,
                            color: "#A4A4AA",
                            textAlign: "left",
                          }}
                        >
                          <FormattedMessage {...messages.note} />:
                        </Col>
                      </Col>

                      <Col span={19}>
                        <Col
                          span={23}
                          offset={1}
                          style={{
                            padding: 0,
                            position: "relative",
                            left: -16,
                          }}
                        >
                          {!!recordApartment && recordApartment.description ? (
                            <Typography.Paragraph
                              style={{
                                color: "black",
                                fontWeight: "bold",
                              }}
                            >
                              {recordApartment.description}
                            </Typography.Paragraph>
                          ) : (
                            ""
                          )}
                        </Col>
                      </Col>
                    </Row>
                  </Col>
                </Row>
              </Card>
            </Col>
            <WithRole
              roles={[
                config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_LIST_RESIDENT_USER,
              ]}
            >
              <Col
                span={24}
                style={{ paddingRight: 8, paddingLeft: 8, marginBottom: 24 }}
              >
                <Card
                  title={
                    <FormattedMessage
                      {...messages.totalMember}
                      values={{
                        total: count,
                      }}
                    />
                  }
                  bordered={false}
                  extra={
                    <WithRole
                      roles={[
                        config.ALL_ROLE_NAME
                          .REAL_ESTATE_MANAGEMENT_CREATE_RESIDENT_USER,
                      ]}
                    >
                      <Button
                        ghost
                        type="primary"
                        icon="user-add"
                        onClick={() =>
                          this.setState({ visibleAddMember: true })
                        }
                      >
                        <FormattedMessage {...messages.addMember} />
                      </Button>
                    </WithRole>
                  }
                >
                  <div style={{ minHeight: 250 }}>
                    <Table
                      rowKey="apartment_map_resident_user_id"
                      loading={members.loading || removing}
                      columns={columns}
                      dataSource={dataSource}
                      bordered
                      locale={{
                        emptyText: <FormattedMessage {...messages.emptyData} />,
                      }}
                      pagination={false}
                      scroll={{
                        x: 1000,
                        y: dataSource.length > 6 ? 400 : null,
                      }}
                    />
                  </div>
                </Card>
              </Col>
            </WithRole>
            <WithRole roles={[config.ALL_ROLE_NAME.FINANCE_ALL_FEE_VIEW]}>
              <Col span={24} style={{ paddingLeft: 8, paddingRight: 8 }}>
                <FeeList search={this.props.location.search} />
              </Col>
            </WithRole>
          </Row>
          <ModalEditApartment
            language={this.props.language}
            setState={this.setState.bind(this)}
            updating={updating}
            visible={this.state.visible}
            recordApartment={recordApartment}
            tree={tree}
            handlerUpdate={this.handlerUpdate}
            allResident={allResident}
            apartment_type={apartment_type}
            dispatch={dispatch}
            fetchAllResidentByPhoneAction={fetchAllResidentByPhoneAction}
          />
          <ModalAddMember
            setState={this.setState.bind(this)}
            language={this.props.language}
            addingMember={addingMember}
            visibleAddMember={this.state.visibleAddMember}
            recordApartment={recordApartment}
            handlerAddMember={this.handlerAddMember}
            allResident={allResident}
            dispatch={dispatch}
            history={this.props.history}
          />
          <ModalEditMember
            setState={this.setState.bind(this)}
            language={this.props.language}
            updatingMember={updatingMember}
            visibleUpdateMember={this.state.visibleUpdateMember}
            recordApartment={recordApartment}
            record={this.state.currentEdit}
            handlerEditMember={this.handlerEditMember}
          />
        </div>
      </Page>
    );
  }
}

ApartmentDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  apartmentDetail: makeSelectApartmentDetail(),
  auth_group: selectAuthGroup(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "apartmentDetail", reducer });
const withSaga = injectSaga({ key: "apartmentDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ApartmentDetail));
