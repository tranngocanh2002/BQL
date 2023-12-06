/**
 *
 * Dashboard
 *
 */

import { Card, Col, Row } from "antd";
import React from "react";
import Chart from "react-apexcharts";
import { config } from "../../utils";
import messages from "./messages";
export default class extends React.PureComponent {
  constructor(props) {
    super(props);
  }
  render() {
    const { resident, loading, intl, auth_group, history, ...rest } =
      this.props;
    const { data } = resident;
    const amountText = intl.formatMessage({
      ...messages.amount,
    });
    const peopleText = intl.formatMessage({
      ...messages.people,
    });
    const noResidentText = intl.formatMessage({
      ...messages.noResident,
    });

    const categories =
      !!data && !!data.length
        ? data.map((res) => {
            return res.building_area_name;
          })
        : [];
    const series =
      !!data && !!data.length
        ? [amountText].map((res) => {
            return {
              name: res,
              data: data.map((item) => {
                return item.total_count;
              }),
            };
          })
        : [
            {
              name: noResidentText,
              data: [],
            },
          ];

    return (
      <Col {...rest}>
        <Card
          bordered={false}
          bodyStyle={{ height: 352 }}
          loading={loading}
          title={
            <span style={{ fontSize: 20, fontWeight: "bold" }}>
              {peopleText}
            </span>
          }
        >
          <Row>
            <Col span={24}>
              <Chart
                options={{
                  chart: {
                    type: "bar",
                    stacked: true,
                    events: {
                      dataPointSelection: function (
                        event,
                        chartContext,
                        { seriesIndex, dataPointIndex }
                      ) {
                        if (
                          auth_group.checkRole([
                            config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_LIST,
                          ])
                        ) {
                          history.push("/main/apartment/list");
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
                        return val + ` ${peopleText}`;
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
                      columnWidth: "100%",
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
                    text: noResidentText,
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
