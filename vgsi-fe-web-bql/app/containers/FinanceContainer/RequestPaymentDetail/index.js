/**
 *
 * RequestPayment
 *
 */

import PropTypes from "prop-types";
import React from "react";
import { connect } from "react-redux";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";

import { Button, Col, Row, Select, Table } from "antd";
import _ from "lodash";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import Page from "../../../components/Page/Page";
import reducer from "./reducer";
import saga from "./saga";

import Avatar from "components/Avatar";
import WithRole from "components/WithRole";
import { getFullLinkImage } from "connection";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import moment from "moment";
import { injectIntl } from "react-intl";
import { selectBuildingCluster } from "redux/selectors/config";
import { formatPrice } from "../../../utils";
import config from "../../../utils/config";
import ModalDeny from "./ModalDeny";
import { defaultAction, deleteRequest } from "./actions";
import messages from "./messages";
import makeSelectRequestPaymentDetail from "./selectors";
const { Option } = Select;

const col6 = {
  md: 24,
  lg: 12,
};

/* eslint-disable react/prefer-stateless-function */
export class RequestPaymentDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      showModalDeny: false,
      record: (props.location.state || {}).record,
    };
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {}

  handleDeny = (record) => {
    const { dispatch } = this.props;
    dispatch(
      deleteRequest({
        apartment_id: record.apartment_id,
        code: record.code,
        callback: () => {
          this.setState({
            showModalDeny: false,
          });
        },
      })
    );
  };

  handleOk = () => {
    this.props.history.push("/main/finance/reception", {
      payment_gen_code: this.state.record.code,
      apartment_id: this.state.record.apartment_id,
      ids: (this.state.record.service_payment_fees || []).map((iii) => iii.id),
    });
  };

  render() {
    const { record, showModalDeny, dispatch } = this.state;
    const { buildingCluster, requestPaymentDetail } = this.props;
    const columns = [
      {
        width: 50,
        title: <span>#</span>,
        align: "center",
        dataIndex: "#",
        key: "#",
        render: (text, record, index) => <span>{index + 1}</span>,
      },
      {
        width: 150,
        title: <strong>{this.props.intl.formatMessage(messages.month)}</strong>,
        align: "center",
        dataIndex: "created_at",
        key: "created_at",
        render: (text) => moment.unix(text).format("DD/MM/YYYY"),
      },
      {
        width: 150,
        title: (
          <strong>{this.props.intl.formatMessage(messages.typeService)}</strong>
        ),
        dataIndex: "service_map_management_service_name",
        key: "service_map_management_service_name",
        align: "center",
        render: (text, record) => (
          <span style={{ textAlign: "left" }}>
            {this.props.language === "en"
              ? record.service_map_management_service_name_en
              : record.service_map_management_service_name}
          </span>
        ),
      },
      {
        width: 150,
        title: (
          <strong>{this.props.intl.formatMessage(messages.amountMoney)}</strong>
        ),
        dataIndex: "price",
        key: "price",
        align: "center",
        render: (text, record) => (
          <span>
            {record.more_money_collecte === 0
              ? formatPrice(record.price)
              : formatPrice(record.more_money_collecte)}{" "}
            đ
          </span>
        ),
      },
    ];
    return (
      <Page className="DetailPaymentPage" inner>
        <div>
          <Row>
            <Col {...col6}>
              <Row
                type="flex"
                gutter={24}
                style={{ paddingTop: 8, paddingBottom: 24, height: "100%" }}
              >
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col
                      span={12}
                      style={{
                        fontWeight: "bold",
                        fontSize: 18,
                        textAlign: "left",
                        marginLeft: 32,
                      }}
                    >
                      {this.props.intl.formatMessage(messages.information)}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(messages.requestDate)}:
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {record &&
                        moment.unix(record.created_at).format("DD/MM/YYYY")}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(messages.requestCode)}:
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {record && record.code}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(messages.property)}:
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {record &&
                        `${record.apartment_name} (${record.apartment_parent_path})`}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(messages.customerName)}:
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {record && record.head_household_name}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 32 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(messages.creator)}:
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {record && record.resident_user_name}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col
                      span={12}
                      style={{
                        fontWeight: "bold",
                        fontSize: 18,
                        textAlign: "left",
                        marginLeft: 32,
                      }}
                    >
                      {this.props.intl.formatMessage(
                        messages.informationPayment
                      )}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(messages.bank)}:
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {buildingCluster && buildingCluster.bank_name}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(messages.bankNumber)}:
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {buildingCluster && buildingCluster.bank_account}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 32 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(
                        messages.bankAccountHolder
                      )}
                      :
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {buildingCluster && buildingCluster.bank_holders}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 32 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(messages.status)}:
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {record && record.status === 0
                        ? this.props.intl.formatMessage(
                            messages.waitConfirmation
                          )
                        : record.status === -1
                        ? this.props.intl.formatMessage(messages.canceled)
                        : record.status === 2
                        ? this.props.intl.formatMessage(messages.denied)
                        : this.props.intl.formatMessage(messages.done)}
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 32 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col span={8} style={{ textAlign: "right" }}>
                      {this.props.intl.formatMessage(messages.reasonDeny)}:
                    </Col>
                    <Col
                      span={12}
                      style={{ fontWeight: "bold", textAlign: "left" }}
                    >
                      {record && record.reason}
                    </Col>
                  </Row>
                </Col>
              </Row>
            </Col>
            <Col {...col6}>
              <Row
                type="flex"
                gutter={24}
                style={{ paddingTop: 8, paddingBottom: 24, height: "100%" }}
              >
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col
                      span={12}
                      style={{
                        fontWeight: "bold",
                        fontSize: 18,
                        textAlign: "left",
                        marginLeft: 32,
                      }}
                    >
                      {this.props.intl.formatMessage(messages.amountMoney)}:{" "}
                      {formatPrice(
                        _.sumBy(
                          record.service_payment_fees || [],
                          (iiii) => iiii.more_money_collecte
                        )
                      )}{" "}
                      đ
                    </Col>
                  </Row>
                </Col>
                <Col
                  span={24}
                  style={{ textAlign: "center", marginBottom: 24 }}
                >
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col
                      span={12}
                      style={{
                        fontSize: 16,
                        textAlign: "left",
                        marginLeft: 32,
                      }}
                    >
                      {this.props.intl.formatMessage(messages.categoryPayment)}
                    </Col>
                  </Row>
                </Col>
                <Col span={23}>
                  <Table
                    rowKey="id"
                    columns={columns}
                    dataSource={record.service_payment_fees}
                    locale={{
                      emptyText: this.props.intl.formatMessage(messages.noData),
                    }}
                    bordered
                    pagination={false}
                    style={{ marginLeft: 32 }}
                    scroll={{ y: 100 }}
                  />
                </Col>
                <Col span={24} style={{ textAlign: "center", marginTop: 24 }}>
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col
                      span={12}
                      style={{
                        fontSize: 16,
                        textAlign: "left",
                        marginLeft: 32,
                      }}
                    >
                      {this.props.intl.formatMessage(messages.contentRequest)}:
                    </Col>
                  </Row>
                </Col>
                <Col span={24} style={{ textAlign: "center", marginTop: 12 }}>
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col
                      span={24}
                      style={{
                        fontSize: 14,
                        textAlign: "left",
                        marginLeft: 48,
                      }}
                    >
                      {record.description}
                    </Col>
                  </Row>
                </Col>
                <Col span={24} style={{ textAlign: "center", marginTop: 32 }}>
                  <Row
                    gutter={24}
                    type="flex"
                    justify="space-between"
                    align="middle"
                  >
                    <Col
                      span={6}
                      style={{
                        fontSize: 14,
                        textAlign: "left",
                        marginLeft: 32,
                      }}
                    >
                      {record.image && (
                        <Avatar
                          imageUrl={getFullLinkImage(record.image, true)}
                          disabled={true}
                        />
                      )}
                    </Col>
                  </Row>
                </Col>
              </Row>
            </Col>
          </Row>
          {record &&
            record.status === 0 &&
            !requestPaymentDetail.deleteSuccess && (
              <Col
                span={24}
                style={{
                  textAlign: "center",
                  marginBottom: 32,
                  marginTop: 32,
                }}
              >
                <Row gutter={24} type="flex" justify="space-between">
                  <Col span={24}>
                    <WithRole
                      roles={[config.ALL_ROLE_NAME.FINANCE_MANAGERMENT_BILL]}
                    >
                      <Button
                        ghost
                        type="danger"
                        onClick={(e) => {
                          e.preventDefault();
                          e.stopPropagation();
                          this.setState({
                            showModalDeny: true,
                          });
                        }}
                      >
                        {this.props.intl.formatMessage(messages.deny)}
                      </Button>
                    </WithRole>
                    <WithRole
                      roles={[config.ALL_ROLE_NAME.FINANCE_CREATE_BILL]}
                    >
                      <Button
                        ghost
                        type="primary"
                        style={{ marginLeft: 10 }}
                        onClick={this.handleOk}
                      >
                        {this.props.intl.formatMessage(messages.createVote)}
                      </Button>
                    </WithRole>
                  </Col>
                </Row>
              </Col>
            )}
          <ModalDeny
            setState={this.setState.bind(this)}
            showModalDeny={showModalDeny}
            handleDeny={this.handleDeny}
            dispatch={dispatch}
            record={record}
          />
        </div>
      </Page>
    );
  }
}

RequestPaymentDetail.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  buildingCluster: selectBuildingCluster(),
  requestPaymentDetail: makeSelectRequestPaymentDetail(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "requestPaymentDetail", reducer });
const withSaga = injectSaga({ key: "requestPaymentDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(RequestPaymentDetail));
