/**
 *
 * Dashboard
 *
 */

import React from "react";
import { Col, Card, Row } from "antd";
import { DatePicker } from "antd";
import Chart from "react-apexcharts";
import { config, formatPrice } from "../../utils";
import { fetchFinance } from "./actions";
import moment from "moment";
import { FormattedMessage } from "react-intl";
import messages from "./messages";
const { MonthPicker } = DatePicker;
export default class extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      fromMonth: null,
    };
  }
  componentDidMount() {
    const { finance } = this.props;
    const { from_month } = finance;
    this.setState({
      fromMonth: from_month,
    });
  }
  disabledToMonth = (current) => {
    return current && current > moment().endOf("day");
  };
  disabledFromMonth = (current) => {
    let now = moment().subtract(5, "months").unix();
    return current && current > moment.unix(now);
  };
  render() {
    const {
      finance,
      dispatch,
      history,
      auth_group,
      screenwidth,
      intl,
      ...rest
    } = this.props;
    const { fromMonth } = this.state;
    const { data, to_month } = finance;
    const fromMonthText = intl.formatMessage({
      ...messages.fromMonth,
    });
    const toMonthText = intl.formatMessage({
      ...messages.toMonth,
    });
    const noRevenueText = intl.formatMessage({
      ...messages.noRevenue,
    });
    const totalText = intl.formatMessage({
      ...messages.total,
    });
    const totalReceivedText = intl.formatMessage({
      ...messages.totalReceived,
    });
    const monthText = intl.formatMessage({
      ...messages.month,
    });
    const moneyText = intl.formatMessage({
      ...messages.money,
    });
    const series = [
      {
        name: totalText,
        data: !!data && !!data.length ? data.map((d) => d.total) : [],
      },
      {
        name: totalReceivedText,
        data: !!data && !!data.length ? data.map((d) => d.total_paid) : [],
      },
    ];
    const categories =
      !!data &&
      !!data.length &&
      data.map((d) => monthText + " " + moment.unix(d.month).format("M"));

    return (
      <Col {...rest}>
        <Card
          bordered={false}
          bodyStyle={{ height: 350 }}
          title={
            <span style={{ fontSize: 20, fontWeight: "bold" }}>
              <FormattedMessage {...messages.totalRevenue} />
            </span>
          }
          extra={
            <Row>
              <span style={{ marginRight: 8, fontWeight: 600 }}>
                <FormattedMessage {...messages.from} />
              </span>
              <MonthPicker
                style={{
                  marginRight: 16,
                  width: screenwidth > 1600 ? null : 100,
                }}
                allowClear={false}
                format="MM/YYYY"
                placeholder={fromMonthText}
                disabledDate={this.disabledFromMonth}
                value={moment.unix(fromMonth)}
                onChange={(value) => {
                  if (value) {
                    let diff_month = value.diff(
                      moment.unix(to_month),
                      "months"
                    );
                    this.setState({
                      fromMonth: value.unix(),
                    });
                    if (diff_month > -12 && diff_month < -5) {
                      dispatch(
                        fetchFinance({
                          from_month: value.unix(),
                          to_month: to_month,
                        })
                      );
                    } else {
                      dispatch(
                        fetchFinance({
                          from_month: value.unix(),
                          to_month: moment(value).add(5, "months").unix(),
                        })
                      );
                    }
                  }
                }}
              />
              <span style={{ marginRight: 8, fontWeight: 600 }}>
                <FormattedMessage {...messages.to} />
              </span>
              <MonthPicker
                allowClear={false}
                format="MM/YYYY"
                style={{ width: screenwidth > 1600 ? null : 100 }}
                placeholder={toMonthText}
                disabledDate={this.disabledToMonth}
                value={moment.unix(to_month)}
                onChange={(value) => {
                  if (value) {
                    let diff_month = value.diff(
                      moment.unix(fromMonth),
                      "months"
                    );
                    if (diff_month < 5 || diff_month > 11) {
                      this.setState({
                        fromMonth: moment(value).subtract(5, "months").unix(),
                      });
                      dispatch(
                        fetchFinance({
                          from_month: moment(value)
                            .subtract(5, "months")
                            .unix(),
                          to_month: value.unix(),
                        })
                      );
                    } else {
                      dispatch(
                        fetchFinance({
                          from_month: fromMonth,
                          to_month: value.unix(),
                        })
                      );
                    }
                  }
                }}
              />
            </Row>
          }
        >
          <Row>
            <Col span={24}>
              <Chart
                options={{
                  chart: {
                    type: "area",
                    toolbar: {
                      show: true,
                      offsetX: 0,
                      offsetY: 0,
                      tools: {
                        download: true,
                        selection: false,
                        zoom: false,
                        zoomin: false,
                        zoomout: false,
                        pan: false,
                        reset: false,
                      },
                    },
                    events: {
                      markerClick: function (
                        event,
                        chartContext,
                        { seriesIndex, dataPointIndex }
                      ) {
                        let date = data[dataPointIndex].month;
                        if (
                          auth_group.checkRole([
                            config.ALL_ROLE_NAME.FINANCE_REVENUE_BY_MONTH,
                          ])
                        ) {
                          history.push(
                            `/main/finance/revenue-by-month?from_month=${moment(
                              date * 1000
                            )
                              .startOf("month")
                              .unix()}&to_month=${
                              moment.unix(date).format("MM/YYYY") ==
                              moment().format("MM/YYYY")
                                ? moment().endOf("day").unix()
                                : moment(date * 1000)
                                    .endOf("month")
                                    .unix()
                            }`
                          );
                        }
                      },
                      mouseMove: function (event) {
                        event.target.style.cursor = "pointer";
                      },
                    },
                  },
                  dataLabels: {
                    enabled: false,
                  },
                  stroke: {
                    curve: "smooth",
                  },
                  noData: {
                    text: noRevenueText,
                    align: "center",
                    verticalAlign: "middle",
                    style: {
                      fontSize: "16px",
                    },
                  },
                  xaxis: {
                    categories: categories,
                  },
                  yaxis: {
                    labels: {
                      formatter: (value) => {
                        return formatPrice(value) + " đ";
                      },
                    },
                    title: {
                      text: moneyText,
                      style: {
                        fontSize: 14,
                        fontWeight: 600,
                        fontFamily: "Arial",
                      },
                    },
                  },
                  fill: {
                    type: "solid",
                    opacity: 0.2,
                  },
                  tooltip: {
                    y: {
                      formatter: function (
                        value,
                        { series, seriesIndex, dataPointIndex, w }
                      ) {
                        if (
                          seriesIndex == 0 ||
                          series[0][dataPointIndex] == 0
                        ) {
                          return formatPrice(value) + " đ";
                        } else {
                          let caculator =
                            series[0][dataPointIndex] != 0
                              ? (series[1][dataPointIndex] * 100.0) /
                                series[0][dataPointIndex]
                              : 0;
                          let percent = Number.isInteger(caculator)
                            ? caculator
                            : parseFloat(caculator).toFixed(1);
                          return formatPrice(value) + " đ" + ` (${percent}%)`;
                        }
                      },
                    },
                  },
                  legend: {
                    position: "bottom",
                    itemMargin: {
                      horizontal: 8,
                      vertical: 8,
                    },
                  },
                  colors: config.COLOR_CHART,
                  responsive: [
                    {
                      breakpoint: 480,
                      options: {
                        legend: {
                          position: "bottom",
                          offsetX: -10,
                          offsetY: 0,
                        },
                      },
                    },
                  ],
                }}
                series={series}
                type="area"
                width={"100%"}
                height={300}
              />
            </Col>
          </Row>
        </Card>
      </Col>
    );
  }
}
