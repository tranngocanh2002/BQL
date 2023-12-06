/**
 *
 * DashboardDebt
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectDashboardDebt from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../components/Page";
import {
  defaultAction,
  fetchAllBuildingArea,
  fetchDetailDebtArea,
} from "./actions";
import {
  Carousel,
  Row,
  Col,
  Button,
  Table,
  Tooltip,
  Alert,
  Spin,
  DatePicker,
} from "antd";
import { formatPrice, config } from "../../../utils";
import WithRole from "../../../components/WithRole";
import moment from "moment";
import("./index.less");
import { injectIntl } from "react-intl";
import messages from "../messages";
/* eslint-disable react/prefer-stateless-function */
export class DashboardDebt extends React.PureComponent {
  state = {
    month: moment().startOf("month"),
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchAllBuildingArea());
  }

  _onChange = (index) => {
    this.props.dispatch(
      fetchDetailDebtArea({
        building_area: this.props.dashboardDebt.buildingArea.data[index],
        month: this.state.month.unix(),
      })
    );
  };

  render() {
    const { dashboardDebt } = this.props;
    const { buildingArea, currentAreaSelected, loading, total_count } =
      dashboardDebt;
    let { formatMessage } = this.props.intl;

    const columns = [
      {
        width: 50,
        align: "center",
        title: <span style={{ margin: 16, color: "black" }}>Tầng</span>,
        dataIndex: "name",
        key: "name",
        render: (text) => {
          return {
            children: text,
            props: {
              style: {
                color: "black",
                fontSize: 16,
              },
            },
          };
        },
      },
    ];
    let maxAparment = 0;
    if (!!currentAreaSelected) {
      maxAparment = _.max(
        currentAreaSelected.floors.map((ff) =>
          !!ff.apartments ? ff.apartments.length : 0
        )
      );
    }
    maxAparment = Math.max(maxAparment, 10);
    if (maxAparment != 0) {
      _.range(0, maxAparment).forEach((aaa) => {
        columns.push({
          align: "center",
          width: `${100 / maxAparment}%`,
          title: "",
          dataIndex: `00${aaa}`.slice(-2),
          key: `00${aaa}`.slice(-2),
          render: (text, record) => {
            const apartments = record.apartments || [];
            if (!!!apartments[aaa]) return null;
            return {
              children: (
                <Tooltip
                  title={`${formatMessage(messages.unpaidDebt)}: ${formatPrice(
                    apartments[aaa].receivables
                  )} VND`}
                >
                  <div
                    style={{
                      width: "100%",
                      height: "100%",
                      background: apartments[aaa].status_color,
                      color: "white",
                      fontSize: 16,
                      cursor: "pointer",
                      display: "flex",
                      textAlign: "center",
                      alignItems: "center",
                      justifyContent: "center",
                    }}
                    onClick={() => {
                      this.props.history.push(
                        `/main/setting/apartment/detail/${apartments[aaa].apartment_id}`
                      );
                    }}
                  >
                    <span>{`${apartments[aaa].apartment_name}`}</span>
                  </div>
                </Tooltip>
              ),
            };
          },
        });
      });
    } else {
      columns.push({
        align: "center",
        width: `100%`,
        title: "",
        dataIndex: `00`.slice(-2),
        key: `00`.slice(-2),
        render: (text, record) => {
          return null;
        },
      });
    }

    return (
      <>
        <Page inner style={{ minHeight: 10, marginBottom: 24 }}>
          <Row style={{ display: "flex", alignItems: "stretch" }}>
            <Col span={6}>
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 20, color: "#909090" }}>
                  {formatMessage(messages.openingDebit)}
                </span>
                <br />
                {!(loading || !!!total_count) && (
                  <span
                    style={{ fontSize: 24, fontWeight: "bold", color: "black" }}
                  >{`${formatPrice(total_count.early_debt)} D`}</span>
                )}
                {(loading || !!!total_count) && (
                  <Spin style={{ marginTop: 8 }} />
                )}
              </Row>
            </Col>
            <Col
              span={12}
              style={{
                borderLeft: "1px solid rgba(210, 210, 210, 0.5)",
                borderRight: "1px solid rgba(210, 210, 210, 0.5)",
              }}
            >
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 20, color: "#909090" }}>
                  {formatMessage(messages.arise)}
                </span>
                <br />
                <Col>
                  <Row>
                    <Col span={12}>
                      {!(loading || !!!total_count) && (
                        <span
                          style={{
                            fontSize: 24,
                            fontWeight: "bold",
                            color: "#D85357",
                          }}
                        >{`${formatPrice(total_count.receivables)} D`}</span>
                      )}
                      {(loading || !!!total_count) && (
                        <Spin style={{ marginTop: 8 }} />
                      )}
                      <br />
                      <span style={{ fontSize: 14, color: "#909090" }}>
                        {formatMessage(messages.receivables)}
                      </span>
                    </Col>
                    <Col span={12}>
                      {!(loading || !!!total_count) && (
                        <span
                          style={{
                            fontSize: 24,
                            fontWeight: "bold",
                            color: "#3EA671",
                          }}
                        >{`${formatPrice(total_count.collected)} Đ`}</span>
                      )}
                      {(loading || !!!total_count) && (
                        <Spin style={{ marginTop: 8 }} />
                      )}
                      <br />
                      <span style={{ fontSize: 14, color: "#909090" }}>
                        {formatMessage(messages.collected)}
                      </span>
                    </Col>
                  </Row>
                </Col>
              </Row>
            </Col>
            <Col span={6}>
              <Row style={{ textAlign: "center" }}>
                <span style={{ fontSize: 20, color: "#909090" }}>
                  {formatMessage(messages.closingDebit)}
                </span>
                <br />
                {!(loading || !!!total_count) && (
                  <span
                    style={{ fontSize: 24, fontWeight: "bold", color: "black" }}
                  >{`${formatPrice(total_count.end_debt)} Đ`}</span>
                )}
                {(loading || !!!total_count) && (
                  <Spin style={{ marginTop: 8 }} />
                )}
              </Row>
            </Col>
          </Row>
        </Page>
        <Page inner loading={buildingArea.loading}>
          <Row className="debtPage">
            <Col>
              <Row
                type="flex"
                align="middle"
                justify="center"
                style={{ marginTop: 24 }}
              >
                <Button
                  icon="left"
                  onClick={() => {
                    this._carousel.prev();
                  }}
                ></Button>
                <Col span={6} style={{ minWidth: 200 }}>
                  <Carousel
                    afterChange={this._onChange}
                    dots={false}
                    ref={(_carousel) => (this._carousel = _carousel)}
                  >
                    {buildingArea.data.map((ddd) => {
                      return <span key={`rrr-${ddd.id}`}>{ddd.name}</span>;
                    })}
                  </Carousel>
                </Col>
                <Button
                  icon="right"
                  onClick={() => {
                    this._carousel.next();
                  }}
                ></Button>
              </Row>
            </Col>
            <Col span={24}>
              <Row gutter={24} style={{ paddingBottom: 24, marginTop: 24 }}>
                <Col span={18}>
                  <WithRole
                    roles={[
                      config.ALL_ROLE_NAME.FINANCE_NOTIFICATION_FEE_MANAGER,
                    ]}
                  >
                    <>
                      <Button
                        style={{ marginRight: 8 }}
                        onClick={(e) => {
                          this.props.history.push(
                            `/main/notification/fee?type=1`
                          );
                        }}
                      >
                        {formatMessage(messages.feeNotice)}
                      </Button>
                      <Button
                        style={{ marginRight: 8 }}
                        onClick={(e) => {
                          this.props.history.push(
                            `/main/notification/fee?type=2`
                          );
                        }}
                      >
                        {formatMessage(messages.feeNotice)} 2
                      </Button>
                      <Button
                        onClick={(e) => {
                          this.props.history.push(
                            `/main/notification/fee?type=3`
                          );
                        }}
                      >
                        {formatMessage(messages.feeNotice)} 3
                      </Button>
                    </>
                  </WithRole>
                </Col>
                <Col span={6} style={{ textAlign: "right" }}>
                  <DatePicker.MonthPicker
                    placeholder={formatMessage(messages.choseMonth)}
                    value={this.state.month}
                    onChange={(month) => {
                      if (!!month) {
                        this.setState({ month: month }, () => {
                          this.props.dispatch(
                            fetchDetailDebtArea({
                              building_area: currentAreaSelected,
                              month: this.state.month.unix(),
                            })
                          );
                        });
                      }
                    }}
                    format="MM/YYYY"
                  />
                </Col>
              </Row>
              <Row type="flex" align="middle" style={{ marginBottom: 24 }}>
                <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                  <div
                    style={{
                      width: 20,
                      height: 20,
                      background: "#159C1F",
                      marginRight: 10,
                    }}
                  />
                  {formatMessage(messages.paidDebt)}
                </Row>
                <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                  <div
                    style={{
                      width: 20,
                      height: 20,
                      background: "#CD2C2E",
                      marginRight: 10,
                    }}
                  />
                  {formatMessage(messages.remainingDebt)}
                </Row>
                <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                  <div
                    style={{
                      width: 20,
                      height: 20,
                      background: "#BC0409",
                      marginRight: 10,
                    }}
                  />
                  {formatMessage(messages.debtReminder1)}
                </Row>
                <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                  <div
                    style={{
                      width: 20,
                      height: 20,
                      background: "#97040B",
                      marginRight: 10,
                    }}
                  />
                  {formatMessage(messages.debtReminder2)}
                </Row>
                <Row type="flex" align="middle" style={{ marginRight: 24 }}>
                  <div
                    style={{
                      width: 20,
                      height: 20,
                      background: "#650205",
                      marginRight: 10,
                    }}
                  />
                  {formatMessage(messages.debtReminder3)}
                </Row>
              </Row>
            </Col>
            <Col span={24} style={{ marginTop: 24 }}>
              <Table
                rowKey="id"
                loading={loading}
                bordered
                columns={columns}
                dataSource={
                  !!currentAreaSelected ? currentAreaSelected.floors : []
                }
                locale={{ emptyText: formatMessage(messages.emptyData) }}
                pagination={false}
              />
            </Col>
          </Row>
        </Page>
        <div
          style={{ position: "absolute", right: 0, top: 160, zIndex: 999999 }}
        >
          <Button
            icon="unordered-list"
            size="large"
            style={{
              width: 50,
              borderBottomLeftRadius: 0,
              borderBottomRightRadius: 0,
            }}
            type={
              this.props.location.pathname == "/main/finance/debt-all"
                ? "primary"
                : "default"
            }
            onClick={() => this.props.history.push("/main/finance/debt-all")}
          />
          <br />
          <Button
            icon="appstore"
            size="large"
            style={{
              width: 50,
              borderTopLeftRadius: 0,
              borderTopRightRadius: 0,
            }}
            type={
              this.props.location.pathname == "/main/finance/debt"
                ? "primary"
                : "default"
            }
            onClick={() => this.props.history.push("/main/finance/debt")}
          />
        </div>
      </>
    );
  }
}

DashboardDebt.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  dashboardDebt: makeSelectDashboardDebt(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "dashboardDebt", reducer });
const withSaga = injectSaga({ key: "dashboardDebt", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(DashboardDebt));
