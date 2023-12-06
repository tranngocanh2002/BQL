/**
 *
 * Dashboard
 *
 */

import React from "react";
import { Col, Card, Row, DatePicker } from "antd";
import Chart from "react-apexcharts";
import { config, formatPrice } from "../../utils";
import { fetchBookingRevenue } from "./actions";
import moment from "moment";
const { MonthPicker } = DatePicker;
import { FormattedMessage } from "react-intl";
import messages from "./messages";

export default class extends React.PureComponent {
  render() {
    const { booking, dispatch, intl, language, history, ...rest } = this.props;
    const { data: rawData, month } = booking;
    const data = rawData.item;
    const series = !!data && !!data.length ? data.map((o) => o.price) : [0.5];
    const choseMonthText = intl.formatMessage({
      ...messages.choseMonth,
    });
    const totalText = intl.formatMessage({
      ...messages.total,
    });
    const noServiceText = intl.formatMessage({
      ...messages.noService,
    });
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
                <FormattedMessage {...messages.month} />
              </span>
              <MonthPicker
                format="MM/YYYY"
                placeholder={choseMonthText}
                value={moment.unix(month)}
                onChange={(value) => {
                  if (value) {
                    dispatch(fetchBookingRevenue({ month: value.unix() }));
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
                    type: "donut",
                    toolbar: {
                      show: true,
                    },
                    events: {
                      mouseMove: function (event) {
                        event.target.style.cursor = "pointer";
                      },
                      dataPointSelection: function (
                        event,
                        chartContext,
                        config1
                      ) {
                        const convert_month = moment.unix(month);
                        const month2 = moment(convert_month).format("M");
                        const year = moment(convert_month).format("YYYY");
                        if (
                          data.length &&
                          data[config1.dataPointIndex].service_id
                        ) {
                          history.push(
                            `/main/service/detail/fees?page=1&service_map_management_id=${
                              data[config1.dataPointIndex].service_id
                            }&month=${month2}&year=${year}&sort=-created_at`
                          );
                        } else {
                          history.push(
                            `/main/service/detail/fees?page=1&month=${month2}&year=${year}&sort=-created_at`
                          );
                        }
                      },
                    },
                  },
                  responsive: [
                    {
                      breakpoint: 480,
                      options: {
                        chart: {
                          width: 200,
                        },
                        legend: {
                          position: "bottom",
                        },
                      },
                    },
                  ],
                  plotOptions: {
                    pie: {
                      donut: {
                        labels: {
                          show: true,
                          name: {
                            show: true,
                            fontSize: "18px",
                            fontWeight: 600,
                          },
                          value: {
                            show: true,
                            fontSize: "18px",
                            fontWeight: 400,
                            formatter: function (w) {
                              if (w == 0.5) {
                                return 0 + " VNĐ";
                              }
                              return formatPrice(Number(w)) + " VNĐ";
                            },
                          },
                          total: {
                            show: true,
                            label: totalText,
                            fontSize: "16px",
                            fontWeight: 600,
                            formatter: function (w) {
                              if (
                                w.globals.seriesTotals.length == 1 &&
                                w.globals.seriesTotals[0] == 0.5
                              ) {
                                return 0 + " VNĐ";
                              }
                              return (
                                formatPrice(
                                  w.globals.seriesTotals.reduce((a, b) => {
                                    return a + b;
                                  }, 0)
                                ) + " VNĐ"
                              );
                            },
                          },
                        },
                      },
                    },
                  },
                  tooltip: {
                    y: {
                      formatter: function (value) {
                        if (value == 0.5) {
                          return 0;
                        }
                        return formatPrice(value) + " VNĐ";
                      },
                    },
                  },
                  labels:
                    !!data && !!data.length
                      ? data.map((o) =>
                          language === "en" ? o.service_name_en : o.service_name
                        )
                      : [noServiceText],
                  legend: {
                    position: "right",
                    width: 150,
                    offsetY: 40,
                    itemMargin: {
                      horizontal: 0,
                      vertical: 8,
                    },
                  },
                  fill: {
                    opacity: 1,
                  },
                  colors:
                    !!data && !!data.length
                      ? data.map((o) => o.color)
                      : ["#737373"],
                }}
                series={series}
                type="donut"
                width={"100%"}
                height={250}
              />
            </Col>
          </Row>
        </Card>
      </Col>
    );
  }
}
