/**
 *
 * Dashboard
 *
 */

import { Card, Col, Row, DatePicker } from "antd";
import React from "react";
import Chart from "react-apexcharts";
import { config } from "../../utils";
import messages from "./messages";
import moment from "moment";
import { fetchMaintenance } from "./actions";
const { MonthPicker } = DatePicker;
export default class extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      fromMonth: null,
    };
  }
  componentDidMount() {
    const { maintenance } = this.props;
    const { from_month: from } = maintenance;
    const from_month = Number(from);
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
      maintenance,
      loading,
      intl,
      dispatch,
      screenwidth,
      history,
      auth_group,
      ...rest
    } = this.props;
    const { fromMonth } = this.state;
    const { data: raw, to_month: to } = maintenance;
    const to_month = Number(to);
    const data =
      !!raw && !!raw.items && raw.items.length
        ? raw.items.map((item) => {
            const { month, fix, not_fix } = item;
            return {
              month,
              fix: Number(fix),
              not_fix: Number(not_fix),
            };
          })
        : [];
    const formatMessage = intl.formatMessage;
    const amountText = formatMessage(messages.amount);
    const fixedText = formatMessage(messages.fixed);
    const notFixedText = formatMessage(messages.notFixed);

    const monthText = intl.formatMessage({
      ...messages.month,
    });
    const series = [
      {
        name: fixedText,
        data: !!data && !!data.length ? data.map((d) => d.fix) : [],
      },
      {
        name: notFixedText,
        data: !!data && !!data.length ? data.map((d) => d.not_fix) : [],
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
          loading={loading}
          title={
            <span style={{ fontSize: 20, fontWeight: "bold" }}>
              {formatMessage(messages.maintenance)}
            </span>
          }
          extra={
            <Row>
              <span style={{ marginRight: 8, fontWeight: 600 }}>
                {formatMessage(messages.from)}
              </span>
              <MonthPicker
                style={{
                  marginRight: 16,
                  width: screenwidth > 1600 ? null : 100,
                }}
                allowClear={false}
                format="MM/YYYY"
                placeholder={formatMessage(messages.fromMonth)}
                // disabledDate={this.disabledFromMonth}
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
                        fetchMaintenance({
                          from_month: value.startOf("month").unix(),
                          to_month: to_month,
                        })
                      );
                    } else {
                      dispatch(
                        fetchMaintenance({
                          from_month: value.startOf("month").unix(),
                          to_month: moment(value)
                            .add(5, "months")
                            .endOf("month")
                            .unix(),
                        })
                      );
                    }
                  }
                }}
              />
              <span style={{ marginRight: 8, fontWeight: 600 }}>
                {formatMessage(messages.to)}
              </span>
              <MonthPicker
                allowClear={false}
                format="MM/YYYY"
                style={{ width: screenwidth > 1600 ? null : 100 }}
                placeholder={formatMessage(messages.toMonth)}
                // disabledDate={this.disabledToMonth}
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
                        fetchMaintenance({
                          from_month: moment(value)
                            .subtract(5, "months")
                            .startOf("month")
                            .unix(),
                          to_month: value.endOf("month").unix(),
                        })
                      );
                    } else {
                      dispatch(
                        fetchMaintenance({
                          from_month: fromMonth,
                          to_month: value.endOf("month").unix(),
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
                    type: "bar",
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
                    stacked: true,
                    events: {
                      dataPointSelection: function (
                        event,
                        chartContext,
                        { seriesIndex, dataPointIndex }
                      ) {
                        let date = data[dataPointIndex].month;
                        console.log(moment.unix(date).endOf("month").unix());
                        if (
                          auth_group.checkRole([
                            config.ALL_ROLE_NAME
                              .MANAGE_FORM_REGISTRATION_MAINTAIN_DEVICE_LIST_SCHEDULE,
                          ])
                        ) {
                          history.push(
                            `/main/maintain/list?start_time=${moment
                              .unix(date)
                              .startOf("month")
                              .unix()}&end_time=${moment
                              .unix(date)
                              .endOf("month")
                              .unix()}`
                          );
                        }
                      },
                      mouseMove: function (event) {
                        event.target.style.cursor = "pointer";
                      },
                    },
                  },
                  tooltip: {
                    y: {
                      formatter: function (val) {
                        return formatMessage(messages.device, {
                          total: val,
                        });
                      },
                    },
                  },
                  dataLabels: {
                    enabled: true,
                  },
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
                  plotOptions: {
                    bar: {
                      horizontal: false,
                      // barHeight: "60%",
                      columnWidth: "55%",
                    },
                  },
                  stroke: {
                    width: 1,
                    show: true,
                    colors: ["#fff"],
                  },
                  xaxis: {
                    categories: categories,
                    // labels: {
                    //   formatter: function (val) {
                    //     return parseInt(val) + ` ${peopleText}`;
                    //   },
                    // },
                  },
                  yaxis: {
                    title: {
                      text: amountText,
                      style: {
                        fontSize: "14px",
                        fontWeight: 600,
                      },
                    },
                  },
                  noData: {
                    text: formatMessage(messages.noMaintenance),
                    align: "center",
                    verticalAlign: "middle",
                  },
                  legend: {
                    position: window.innerWidth > 1440 ? "right" : "bottom",
                    offsetY: window.innerWidth > 1440 ? 40 : 0,
                    itemMargin: {
                      horizontal: window.innerWidth > 1440 ? 0 : 8,
                      vertical: 8,
                    },
                  },
                  colors: config.COLOR_CHART,
                  fill: {
                    opacity: 1,
                  },
                }}
                series={series}
                type="bar"
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
