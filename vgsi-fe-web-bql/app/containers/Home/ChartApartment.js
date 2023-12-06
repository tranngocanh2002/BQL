/**
 *
 * Dashboard
 *
 */

import React from "react";
import { Col, Card, Row } from "antd";
import Chart from "react-apexcharts";
import config from "../../utils/config";
import { FormattedMessage } from "react-intl";
import messages from "./messages";
export default class extends React.PureComponent {
  render() {
    const { apartment, history, auth_group, intl, dispatch, ...rest } =
      this.props;
    const { data } = apartment;
    const noBuildingText = intl.formatMessage({
      ...messages.noBuilding,
    });
    const houseText = intl.formatMessage({
      ...messages.house,
    });
    const totalText = intl.formatMessage({
      ...messages.total,
    });
    const noHouseText = intl.formatMessage({
      ...messages.noHouse,
    });

    const series =
      !!data && !!data.apartment_form_type && !!data.apartment_form_type.length
        ? data.apartment_form_type.map((apart) => apart.total_count)
        : [0.5];

    const BUILDING_TYPE =
      !!data && !!data.apartment_form_type && !!data.apartment_form_type.length
        ? data.apartment_form_type.map((apart) => {
            return {
              name: apart.form_type_name,
              type: apart.form_type,
            };
          })
        : [];
    const getSeriesDataSumByCategoryIndex = (series, categoryIndex) => {
      return series.reduce(
        (acc, cur) => acc + (cur.data[categoryIndex] || 0),
        0
      );
    };
    const categories =
      !!data && !!data.apartment_by_building_area
        ? data.apartment_by_building_area.map((item) => item.building_area_name)
        : [noBuildingText];
    const addAnnotations = (chart, series) => {
      try {
        categories.forEach((category, index) => {
          const seriesDataSum = getSeriesDataSumByCategoryIndex(series, index);
          chart.addPointAnnotation(
            {
              y: seriesDataSum,
              x: category,
              label: {
                text: `${seriesDataSum} ${houseText}`,
                style: {
                  fontWeight: 600,
                },
              },
            },
            false
          );
        });
      } catch (error) {
        console.log(`Add point annotation error: ${error.message}`);
      }
    };

    return (
      <Col {...rest}>
        <Card
          bordered={false}
          bodyStyle={{ height: 352 }}
          title={
            <span style={{ fontSize: 20, fontWeight: "bold" }}>
              <FormattedMessage {...messages.apartment} />
            </span>
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
                    stacked: true,
                    events: {
                      dataPointSelection: function (
                        event,
                        chartContext,
                        config1
                      ) {
                        if (
                          auth_group.checkRole([
                            config.ALL_ROLE_NAME.REAL_ESTATE_MANAGEMENT_LIST,
                          ])
                        ) {
                          history.push(
                            `/main/apartment/list?page=1&form_type=${
                              BUILDING_TYPE[config1.dataPointIndex].type
                            }`
                          );
                        }
                      },
                      mounted: (chartContext) => {
                        addAnnotations(chartContext, series);
                      },
                      updated: (chartContext, config1) => {
                        setTimeout(() => {
                          addAnnotations(chartContext, config1.config.series);
                        });
                      },
                      mouseMove: function (event) {
                        event.target.style.cursor = "pointer";
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
                  // dataLabels: {
                  //   enabled: true,
                  // },
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
                  labels:
                    !!data &&
                    !!data.apartment_form_type &&
                    !!data.apartment_form_type.length
                      ? data.apartment_form_type.map((o) =>
                          this.props.language === "vi"
                            ? o.form_type_name
                            : o.form_type_name_en
                        )
                      : [noHouseText],
                  noData: {
                    text: noHouseText,
                    align: "center",
                    verticalAlign: "middle",
                  },
                  legend: {
                    position: "right",
                    width: 150,
                    offsetY: 40,
                    itemMargin: {
                      horizontal: 0,
                      vertical: 8,
                    },
                  },
                  colors:
                    !!data &&
                    !!data.apartment_form_type &&
                    !!data.apartment_form_type.length
                      ? config.COLOR_CHART
                      : ["#737373"],
                  fill: {
                    opacity: 1,
                  },
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
