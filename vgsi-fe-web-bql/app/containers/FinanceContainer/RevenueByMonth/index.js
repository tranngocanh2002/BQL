import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";
import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import moment from "moment";
import queryString from "query-string";
import makeSelectRevenueByMonth from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import { Row, Col, DatePicker, Table, Spin, Divider } from "antd";
import { fetchRevenueByMonthAction, defaultAction } from "./actions";
import Page from "../../../components/Page/Page";
import { formatPrice } from "../../../utils";
import styles from "./index.less";
import messages from "./messages";
import { injectIntl } from "react-intl";

const { MonthPicker } = DatePicker;
class RevenueByMonth extends React.PureComponent {
  state = {
    from_month: moment().startOf("month").unix(),
    to_month: moment().endOf("day").unix(),
    type: 1,
  };
  componentDidMount() {
    this.loading(this.props.location.search);
  }
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  loading = (loading) => {
    let params = queryString.parse(loading);
    if (Object.keys(params).length === 0) {
      params = {
        from_month: moment().startOf("month").unix(),
        to_month: moment().endOf("day").unix(),
        type: 1,
      };
    }
    this.setState(
      {
        from_month: params.from_month,
        to_month: params.to_month,
        type: 1,
      },
      () =>
        this.props.dispatch(
          fetchRevenueByMonthAction({
            from_month: params.from_month,
            to_month: params.to_month,
            type: 1,
          })
        )
    );
  };

  render() {
    const { loading, total, total_paid, total_unpaid, data } =
      this.props.revenueByMonth;
    const columns = [
      {
        align: "right",
        title: <span className={styles.nameTable}>#</span>,
        //dataIndex: "id",
        key: "id",
        width: 50,
        render: (record, text, index) => index + 1,
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.day)}
          </span>
        ),
        dataIndex: "month",
        key: "month",
        render: (text, record) => (
          <span>{moment(record.month * 1000).format("DD/MM/YYYY")}</span>
        ),
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.revenue)}
          </span>
        ),
        dataIndex: "total_paid",
        key: "total_paid",
        render: (text, record) => (
          <span>{`${formatPrice(record.total_paid)} đ`}</span>
        ),
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.notCollected)}
          </span>
        ),
        dataIndex: "total_unpaid",
        key: "total_unpaid",
        render: (text, record) => (
          <span>{`${formatPrice(record.total_unpaid)} đ`}</span>
        ),
      },
      {
        align: "center",
        title: (
          <span className={styles.nameTable}>
            {this.props.intl.formatMessage(messages.total)}
          </span>
        ),
        dataIndex: "total",
        key: "total",
        render: (text, record) => (
          <span>{`${formatPrice(record.total)} đ`}</span>
        ),
      },
    ];
    return (
      <Page inner>
        <div className={styles.revenueByMonthPage}>
          <Row style={{ display: "flex", justifyContent: "flex-end" }}>
            <Col
              md={7}
              lg={6}
              xl={5}
              style={{ display: "flex", justifyContent: "flex-end" }}
            >
              <span style={{ marginRight: 8, fontWeight: 600, marginTop: 5 }}>
                {this.props.intl.formatMessage(messages.month)}
              </span>
              <MonthPicker
                allowClear={false}
                format="MM/YYYY"
                placeholder={this.props.intl.formatMessage(
                  messages.selectMonth
                )}
                value={moment.unix(this.state.from_month)}
                disabledDate={(current) =>
                  current && current > moment().endOf("month")
                }
                onChange={(value) => {
                  if (value) {
                    this.setState(
                      {
                        from_month: value.startOf("month").unix(),
                        to_month:
                          value.format("MM/YYYY") == moment().format("MM/YYYY")
                            ? moment().endOf("day").unix()
                            : value.endOf("month").unix(),
                        type: 1,
                      },
                      () => {
                        this.props.history.push(
                          `/main/finance/revenue-by-month?from_month=${value
                            .startOf("month")
                            .unix()}&to_month=${
                            value.format("MM/YYYY") ==
                            moment().format("MM/YYYY")
                              ? moment().endOf("day").unix()
                              : value.endOf("month").unix()
                          }`
                        );

                        this.props.dispatch(
                          fetchRevenueByMonthAction({
                            from_month: value.startOf("month").unix(),
                            to_month:
                              value.format("MM/YYYY") ==
                              moment().format("MM/YYYY")
                                ? moment().endOf("day").unix()
                                : value.endOf("month").unix(),
                            type: 1,
                          })
                        );
                      }
                    );
                  }
                }}
              />
            </Col>
          </Row>
          <Divider />
          <Page inner style={{ minHeight: 10, marginBottom: 16 }}>
            <Row>
              <Col span={8}>
                <Row style={{ textAlign: "center" }}>
                  <span style={{ fontSize: 16, color: "#909090" }}>
                    {" "}
                    {this.props.intl.formatMessage(messages.total)}
                  </span>
                  <br />
                  {(!loading || !total) && (
                    <span
                      style={{ fontSize: 24, fontWeight: "bold" }}
                    >{`${formatPrice(total)} Đ`}</span>
                  )}
                  {loading && <Spin style={{ marginTop: 8 }} />}
                </Row>
              </Col>
              <Col
                span={8}
                style={{
                  borderLeft: "1px solid rgba(210, 210, 210, 0.5)",
                  borderRight: "1px solid rgba(210, 210, 210, 0.5)",
                }}
              >
                <Row style={{ textAlign: "center" }}>
                  <span style={{ fontSize: 16, color: "#909090" }}>
                    {this.props.intl.formatMessage(messages.totalCollected)}
                  </span>
                  <br />
                  {(!loading || !total_paid) && (
                    <span
                      style={{
                        fontSize: 24,
                        fontWeight: "bold",
                        color: "#3EA671",
                      }}
                    >{`${formatPrice(total_paid)} Đ`}</span>
                  )}
                  {loading && <Spin style={{ marginTop: 8 }} />}
                </Row>
              </Col>
              <Col span={8}>
                <Row style={{ textAlign: "center" }}>
                  <span style={{ fontSize: 16, color: "#909090" }}>
                    {this.props.intl.formatMessage(messages.totalNotCollected)}
                  </span>
                  <br />
                  {(!loading || !total_unpaid) && (
                    <span
                      style={{
                        fontSize: 24,
                        fontWeight: "bold",
                        color: "#D85357",
                      }}
                    >{`${formatPrice(total_unpaid)} Đ`}</span>
                  )}
                  {loading && <Spin style={{ marginTop: 8 }} />}
                </Row>
              </Col>
            </Row>
          </Page>
          <Row>
            <Col>
              <Table
                rowKey={(record, index) => index + 1}
                loading={loading}
                columns={columns}
                dataSource={data}
                bordered
                locale={{
                  emptyText: this.props.intl.formatMessage(messages.noData),
                }}
                pagination={{
                  pageSize: 20,
                }}
              />
            </Col>
          </Row>
        </div>
      </Page>
    );
  }
}
RevenueByMonth.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  revenueByMonth: makeSelectRevenueByMonth(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "RevenueByMonth", reducer });
const withSaga = injectSaga({ key: "RevenueByMonth", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(RevenueByMonth));
