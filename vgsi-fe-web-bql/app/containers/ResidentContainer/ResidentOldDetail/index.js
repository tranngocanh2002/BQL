/**
 *
 * ResidentOldDetail
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import dateFormat from "dateformat";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";
import moment from "moment";
import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectResidentOldDetail from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page/Page";
import { Row, Col, Table, Card, Avatar, Divider, Button } from "antd";
import Exception from "ant-design-pro/lib/Exception";
import { defaultAction, fetchDetailOldResidentAction } from "./actions";

import config from "../../../utils/config";
import { Redirect } from "react-router";

import "./index.less";
import { selectAuthGroup } from "../../../redux/selectors";
import { getFullLinkImage } from "../../../connection";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "../messages";

/* eslint-disable react/prefer-stateless-function */
export class ResidentOldDetail extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      record: (props.location.state || {}).record,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    let { params } = this.props.match;
    this.props.dispatch(fetchDetailOldResidentAction({ id: params.id }));
  }

  componentWillReceiveProps(nextProps) {
    if (
      this.props.residentOldDetail.detail.data !=
      nextProps.residentOldDetail.detail.data
    ) {
      this.setState({
        record: nextProps.residentOldDetail.detail.data,
      });
    }
  }

  render() {
    const { residentOldDetail, auth_group } = this.props;
    const { detail } = residentOldDetail;
    const recordResident = this.state.record;
    const formatMessage = this.props.intl.formatMessage;

    if (detail.data == -1) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={formatMessage(messages.notFound)}
            actions={
              <Button
                type="primary"
                onClick={() =>
                  this.props.history.push("/main/resident-old/list")
                }
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
        dataIndex: "apartment_parent_path",
        key: "parent_path",
      },
      {
        title: (
          <span className={"nameTable"}>{formatMessage(messages.role)}</span>
        ),
        dataIndex: "type_name",
        key: "type_name",
      },
      {
        title: (
          <span className={"nameTable"}>{formatMessage(messages.dateIn)}</span>
        ),
        dataIndex: "time_in",
        key: "time_in",
        render: (text, record) => (
          <span>{moment.unix(record.time_in).format("DD/MM/YYYY")}</span>
        ),
      },
      {
        title: (
          <span className={"nameTable"}>{formatMessage(messages.dateOut)}</span>
        ),
        dataIndex: "time_out",
        key: "time_out",
        render: (text, record) => (
          <span>{moment.unix(record.time_out).format("DD/MM/YYYY")}</span>
        ),
      },
    ];

    if (!auth_group.checkRole([config.ALL_ROLE_NAME.RESIDENT_OLD_LIST])) {
      columns.splice(columns.length - 1, 1);
    }

    return (
      <Page loading={detail.loading} inner={detail.loading}>
        <div className="residentOldDetailPage">
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
                minHeight: 700,
              }}
            >
              <Card
                title={formatMessage(messages.personalInformation)}
                bordered={false}
                style={{ height: "100%" }}
              >
                <Row
                  type="flex"
                  gutter={24}
                  style={{ paddingTop: 8, paddingBottom: 24, height: "100%" }}
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
                            {recordResident && recordResident.phone}
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
                                {recordResident.gender == 1
                                  ? formatMessage(messages.male)
                                  : recordResident.gender == 2
                                  ? formatMessage(messages.female)
                                  : formatMessage(messages.other)}
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
                            {recordResident && recordResident.cmtnd}
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
                              ? formatMessage(messages.vietnam)
                              : formatMessage(messages.foreign)}
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
                minHeight: 700,
              }}
            >
              <Card
                title={formatMessage(messages.propertyStayed)}
                bordered={false}
                style={{ minHeight: 729 }}
              >
                <div style={{ height: "100%" }}>
                  <Table
                    rowKey="apartment_map_resident_user_id"
                    columns={columns}
                    dataSource={
                      recordResident &&
                      recordResident.apartments.map((mem) => {
                        return {
                          ...mem,
                          type_name: (
                            config.TYPE_RESIDENT.find(
                              (t) => t.id == mem.type
                            ) || config.TYPE_RESIDENT[1]
                          ).name,
                        };
                      })
                    }
                    locale={{ emptyText: formatMessage(messages.noData) }}
                    pagination={{
                      pageSize: 20,
                      showTotal: (total, range) =>
                        formatMessage(messages.totalProperty, { total }),
                    }}
                    onChange={this.handleTableChange}
                  />
                </div>
              </Card>
            </Col>
          </Row>
        </div>
      </Page>
    );
  }
}

ResidentOldDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  residentOldDetail: makeSelectResidentOldDetail(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "residentOldDetail", reducer });
const withSaga = injectSaga({ key: "residentOldDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(ResidentOldDetail));
