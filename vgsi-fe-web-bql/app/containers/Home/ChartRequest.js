/**
 *
 * Dashboard
 *
 */

import React from "react";
import { Col, Card, Row, DatePicker } from "antd";
import Chart from "react-apexcharts";
import { config } from "../../utils";
import { fetchRequest } from "./actions";
import moment from "moment";
const { MonthPicker } = DatePicker;
import { FormattedMessage } from "react-intl";
import messages from "./messages";
import { STATUS_REQUEST } from "utils/config";
import { log } from "lodash-decorators/utils";
export default class extends React.PureComponent {
  render() {
    const { request, dispatch, history, auth_group, intl, language, ...rest } =
      this.props;
    const { data, month } = request;
    const series1 =
      !!data && !!data.status && !!data.status.length
        ? data.status.map((o) => {
            const a = STATUS_REQUEST.find(
              (item) => item.id.toString() === o.status
            );
            return a
              ? {
                  status: a.id.toString(),
                  name: a.name,
                  name_en: a.name_en,
                  color: a.color,
                  total: o.total,
                }
              : o;
          })
        : undefined;
    const series =
      series1 && series1.length ? series1.map((o) => o.total) : [0.5];
    const choseMonthText = intl.formatMessage({
      ...messages.choseMonth,
    });
    const totalText = intl.formatMessage({
      ...messages.total,
    });
    const noFeedbackText = intl.formatMessage({
      ...messages.noFeedback,
    });

    return (
      <Col {...rest}>
        <Card
          bordered={false}
          bodyStyle={{ height: 350 }}
          title={
            <span style={{ fontSize: 20, fontWeight: "bold" }}>
              <FormattedMessage {...messages.feedback} />
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
                    dispatch(fetchRequest({ month: value.unix() }));
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
                        if (
                          data.status.length &&
                          auth_group.checkRole([
                            config.ALL_ROLE_NAME.REQUEST_LIST,
                          ])
                        ) {
                          let convert_month = moment.unix(month);
                          let from_day = moment(convert_month)
                            .startOf("month")
                            .unix();
                          let to_day = moment(convert_month)
                            .endOf("month")
                            .unix();
                          history.push(
                            `/main/ticket/list?page=1&status=${
                              data.status[config1.dataPointIndex].status
                            }&start_time_from=${from_day}&start_time_to=${to_day}`
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
                                return 0;
                              }
                              return w.globals.seriesTotals.reduce((a, b) => {
                                return a + b;
                              }, 0);
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
                        return value;
                      },
                    },
                  },
                  labels:
                    !!data && !!data.status && !!data.status.length
                      ? series1.map((item) =>
                          this.props.language === "vi"
                            ? item.name
                            : item.name_en
                        )
                      : [noFeedbackText],
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
                    !!data && !!data.status && !!data.status.length
                      ? // ? config.COLOR_CHART
                        data.status.map((item) => item.color)
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
