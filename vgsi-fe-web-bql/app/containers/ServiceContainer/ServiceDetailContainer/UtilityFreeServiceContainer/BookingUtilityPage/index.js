/**
 *
 * BookingUtilityPage
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
import makeSelectBookingUtilityPage from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import messages from "./messages";
import Page from "../../../../../components/Page/Page";
import {
  Row,
  Col,
  Radio,
  Calendar,
  Popover,
  Icon,
  Button,
  Tooltip,
} from "antd";
import {
  defaultAction,
  fetchAllConfig,
  createBooking,
  fetchBooking,
  fetchSlotFree,
} from "./actions";
import styles from "./styles.less";
import moment from "moment";
import ModalAddBooking from "./ModalAddBooking";
import makeSelectUtilityFreeServiceContainer from "../selectors";
import { notificationBar } from "../../../../../utils";
import { GLOBAL_COLOR } from "../../../../../utils/constants";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
/* eslint-disable react/prefer-stateless-function */
export class BookingUtilityPage extends React.PureComponent {
  state = {
    currentSelected: 0,
    currentDate: moment(),
    currentDaySelected: moment(),
  };

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    this.props.dispatch(fetchAllConfig(this.props.match.params.id));
  }

  handleSizeChange = (e) => {
    this.setState({ currentSelected: e.target.value }, this._loadBooking);
  };

  _loadBooking = (data = null) => {
    if (!data) {
      data = this.props.bookingUtilityPage.data;
    }
    const { currentSelected, currentDate } = this.state;
    const currentConfig = data[currentSelected];

    let start_time = moment(currentDate).startOf("month");
    let end_time = moment(currentDate).endOf("month");

    let dates = [];

    while (start_time.unix() <= end_time.unix()) {
      dates.push(start_time.unix());
      start_time = start_time.add("day", 1);
    }

    this.props.dispatch(
      fetchBooking({
        service_utility_config_id: currentConfig.id,
        service_utility_free_id: currentConfig.service_utility_free_id,
        start_time_from: moment().startOf("month").unix(),
        start_time_to: moment().endOf("month").unix(),
        dates,
      })
    );
  };

  dateCellRender = (value, schedules = []) => {
    const { currentDate } = this.state;
    if (
      value.unix() < moment(currentDate).startOf("month").unix() ||
      value.unix() > moment(currentDate).endOf("month").unix()
    ) {
      return null;
    }

    const { data, bookings } = this.props.bookingUtilityPage;
    const currentConfig = data[this.state.currentSelected];
    const key = `book-${currentConfig.service_utility_free_id}-${
      currentConfig.id
    }-${moment(value).startOf("day").unix()}`;
    const booking = bookings[key];
    const isLastTime =
      moment(value).startOf("day").unix() < moment().startOf("day").unix();

    const content =
      !!booking && booking.items.length > 0 ? (
        <div
          style={{
            padding: 16,
            width: 400,
            maxHeight: 400,
            overflowY: "scroll",
          }}
        >
          <div>
            <div style={{ textTransform: "capitalize", color: "#454F63" }}>
              {moment(value).format("dddd")}
            </div>
            <span style={{ color: "#78849E", fontSize: 40 }}>
              {moment(value).format("DD")}
            </span>
          </div>
          {!!booking && booking.items.length > 0 && (
            <ul style={{ paddingLeft: 16, width: "100%" }}>
              {booking.items.map((item, id) => {
                return (
                  // <Popover content={this.renderContentSchedule(item)} placement="rightTop" key={id}
                  //   openClassName={styles.customTextPopover2}>
                  <li
                    key={id}
                    className="customHoverSchedule"
                    style={{
                      position: "relative",
                      width: "100%",
                      paddingRight: 60,
                      marginTop: 24,
                    }}
                  >
                    Phòng:{" "}
                    <span
                      style={{ color: GLOBAL_COLOR, fontWeight: "bold" }}
                    >{`${item.apartment_name} (${item.apartment_parent_path})`}</span>
                    <br />
                    Thời gian:{" "}
                    {item.book_time.map((bb, iiii) => {
                      return (
                        <>
                          {iiii == 0 ? "" : " , "}
                          <span
                            style={{ color: GLOBAL_COLOR, fontWeight: "bold" }}
                          >
                            {moment.unix(bb.start_time).format("HH:mm")}
                          </span>{" "}
                          -{" "}
                          <span
                            style={{ color: GLOBAL_COLOR, fontWeight: "bold" }}
                          >
                            {moment.unix(bb.end_time).format("HH:mm")}
                          </span>
                        </>
                      );
                    })}
                    <br />
                    Số lượng:{" "}
                    <span style={{ color: GLOBAL_COLOR, fontWeight: "bold" }}>
                      {item.total_slot}
                    </span>
                    <br />
                    Trạng thái:{" "}
                    <span
                      style={{ fontWeight: "bold" }}
                      className={
                        item.status == 0
                          ? "luci-status-warning"
                          : item.status == 1
                          ? "luci-status-success"
                          : "luci-status-danger"
                      }
                    >
                      {item.status == 0
                        ? "Chờ xác nhận"
                        : item.status == 1
                        ? "Đã xác nhận"
                        : "Đã huỷ"}
                    </span>
                    {item.status == 0 && (
                      <Button
                        type="danger"
                        ghost
                        style={{
                          position: "absolute",
                          top: 40,
                          right: 0,
                        }}
                        onClick={async (e) => {
                          try {
                            let res =
                              await window.connection.cancelBookingUtility({
                                id: item.id,
                              });

                            if (res.success) {
                              if (this.props.language === "en") {
                                notificationBar("Cancel booking successful.");
                              } else {
                                notificationBar("Hủy đặt chỗ thành công.");
                              }
                              this._loadBooking();
                            }
                          } catch (error) {}
                        }}
                      >
                        Huỷ
                      </Button>
                    )}
                    {item.status == 0 && (
                      <Button
                        type="primary"
                        ghost
                        style={{
                          position: "absolute",
                          top: 0,
                          right: 0,
                        }}
                        onClick={async (e) => {
                          try {
                            let res =
                              await window.connection.changeStatusBookingUtility(
                                {
                                  service_map_management_id:
                                    this.props.utilityFreeServiceContainer.data
                                      .id,
                                  is_active_all: 0,
                                  is_active_array: [item.id],
                                }
                              );

                            if (res.success) {
                              if (this.props.language === "en") {
                                notificationBar("Confirm booking successful.");
                              } else {
                                notificationBar("Xác nhận đặt chỗ thành công.");
                              }
                              this._loadBooking();
                            }
                          } catch (error) {}
                        }}
                      >
                        Xác nhận
                      </Button>
                    )}
                  </li>
                  // </Popover>
                );
              })}
            </ul>
          )}
        </div>
      ) : null;

    return (
      <Popover
        // className={styles.customTextPopover2}
        content={content}
      >
        <div
          style={{
            height: "100%",
            width: "100%",
          }}
        >
          {/* <span style={{ color: 'black' }} > Hiện tại </span>
          <br />
          <span style={{ color: 'black', fontWeight: 'normal', fontSize: 18 }} >
            <span style={{ color: '#009b71', fontWeight: 'bold', fontSize: 18 }}>{!!booking ? _.sumBy(booking.items, ii => ii.total_slot) : 0}</span>
            /{currentConfig.total_slot}
          </span> */}
          <ul style={{ paddingLeft: 20 }}>
            {!!booking &&
              !!booking.statics &&
              booking.statics.map((bb, ii) => {
                return (
                  <li key={`booo-${ii}-${key}`}>
                    <span
                      style={{
                        color: isLastTime ? "rgba(0, 0, 0, 0.25)" : "black",
                      }}
                    >
                      {bb.start_time}
                    </span>{" "}
                    -{" "}
                    <span
                      style={{
                        color: isLastTime ? "rgba(0, 0, 0, 0.25)" : "black",
                      }}
                    >
                      {bb.end_time}
                    </span>
                    :{" "}
                    <span style={{ color: GLOBAL_COLOR, fontWeight: "bold" }}>
                      {bb.total_slot_book}
                    </span>
                    /<span style={{ color: "black" }}>{bb.total_slot}</span>
                  </li>
                );
              })}
          </ul>
          {!isLastTime && (
            <Button
              type="link"
              size="large"
              onClick={(e) => {
                this.props.dispatch(
                  fetchSlotFree({
                    service_utility_config_id: currentConfig.id,
                    current_time: moment(value).startOf("day").unix(),
                  })
                );
                this.setState(
                  {
                    isVisible: true,
                    currentDaySelected: value,
                  },
                  () => {}
                );
              }}
              style={{ position: "absolute", bottom: 0, right: 0 }}
            >
              <Icon type="plus-circle" style={{ fontSize: 24 }} />
            </Button>
          )}
        </div>
      </Popover>
    );
  };

  componentWillReceiveProps(nextProps) {
    if (
      this.props.bookingUtilityPage.create.success !=
        nextProps.bookingUtilityPage.create.success &&
      nextProps.bookingUtilityPage.create.success
    ) {
      this.setState({ isVisible: false });
      this._loadBooking();
    }
    if (
      this.props.bookingUtilityPage.loading !=
        nextProps.bookingUtilityPage.loading &&
      !nextProps.bookingUtilityPage.loading &&
      nextProps.bookingUtilityPage.data.length > 0
    ) {
      this._loadBooking(nextProps.bookingUtilityPage.data);
    }
  }

  render() {
    const { currentSelected, currentDate, currentDaySelected } = this.state;
    const { bookingUtilityPage } = this.props;
    const { loading, data, freeSlot, create, apartments } = bookingUtilityPage;

    const currentConfig = data[currentSelected];

    return (
      <Page noPadding>
        <Row>
          <Col style={{ textAlign: "center" }}>
            {data.length > 0 && (
              <Radio.Group
                size="large"
                value={currentSelected}
                onChange={this.handleSizeChange}
              >
                {data.map((ser, index) => {
                  return (
                    <Radio.Button key={`row-${index}`} value={index}>
                      {ser.name}
                    </Radio.Button>
                  );
                })}
              </Radio.Group>
            )}
          </Col>
          {!!currentConfig && (
            <Col className={styles.customCalender} style={{ marginTop: 48 }}>
              <Row>
                <Col span={12} style={{ marginBottom: 24 }}>
                  {/* <Button type='primary' size='large' >
                    Hôm nay
                  </Button> */}
                  <Button
                    type="link"
                    size="large"
                    icon="left"
                    style={{ marginLeft: 16, marginRight: 16 }}
                    onClick={(e) => {
                      this.setState(
                        {
                          currentDate: moment(this.state.currentDate).add(
                            "month",
                            -1
                          ),
                        },
                        () => {
                          this._loadBooking();
                        }
                      );
                    }}
                  />
                  <span
                    style={{ fontWeight: "bold", fontSize: 24, color: "black" }}
                  >
                    {this.state.currentDate.format("MM/YYYY")}
                  </span>
                  <Button
                    type="link"
                    size="large"
                    icon="right"
                    style={{ marginLeft: 16, marginRight: 16 }}
                    onClick={(e) => {
                      this.setState(
                        {
                          currentDate: moment(this.state.currentDate).add(
                            "month",
                            1
                          ),
                        },
                        () => {
                          this._loadBooking();
                        }
                      );
                    }}
                  />
                </Col>
                <Col span={12} style={{ textAlign: "right", marginBottom: 24 }}>
                  <Tooltip title="Làm mới trang">
                    <Button
                      shape="circle-outline"
                      style={{ padding: 0, marginRight: 10 }}
                      onClick={(e) => {
                        this._loadBooking();
                      }}
                      icon="reload"
                      size="large"
                    />
                  </Tooltip>
                </Col>
                <Col style={{ marginTop: 48 }}>
                  <Calendar
                    dateCellRender={(value) => this.dateCellRender(value)}
                    // dateFullCellRender={value => null}
                    // locale={locale}
                    value={currentDate}
                    onChange={(currentDate) => this.setState({ currentDate })}
                    // disabledDate={(value) => value.month() != time.month()}
                    headerRender={() => null}
                  />
                </Col>
              </Row>
            </Col>
          )}
          {!!currentConfig && (
            <ModalAddBooking
              visible={this.state.isVisible}
              onCancel={() => {
                if (create.loading) return;
                this.setState({ isVisible: false });
              }}
              confirmLoading={create.loading}
              onSave={(values) => {
                try {
                  let { time_use, ...rest } = values;

                  this.props.dispatch(
                    createBooking({
                      ...rest,
                      service_utility_config_id: currentConfig.id,
                      book_time: time_use.map((mm) => {
                        let mmSplit = mm.split("-");
                        return {
                          start_time: moment(
                            `${currentDaySelected.format("YYYY-MM-DD")} ${
                              mmSplit[0]
                            }`,
                            "YYYY-MM-DD HH:mm"
                          ).unix(), // values.start_time.unix(),
                          end_time: moment(
                            `${currentDaySelected.format("YYYY-MM-DD")} ${
                              mmSplit[1]
                            }`,
                            "YYYY-MM-DD HH:mm"
                          ).unix(), // values.start_time.unix(),
                        };
                      }),
                    })
                  );
                } catch (error) {
                  console.log("error", error);
                }
              }}
              title={
                <span>
                  Tạo booking{" "}
                  <span
                    style={{ color: GLOBAL_COLOR, fontWeight: "bold" }}
                  >{` (Ngày ${currentDaySelected.format("DD/MM/YYYY")})`}</span>
                </span>
              }
              apartments={apartments}
              dispatch={this.props.dispatch}
              slots={
                freeSlot[
                  `slot-${currentConfig.id}-${moment(
                    this.state.currentDaySelected
                  )
                    .startOf("day")
                    .unix()}`
                ]
              }
            />
          )}
        </Row>
      </Page>
    );
  }
}

BookingUtilityPage.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  bookingUtilityPage: makeSelectBookingUtilityPage(),
  utilityFreeServiceContainer: makeSelectUtilityFreeServiceContainer(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "bookingUtilityPage", reducer });
const withSaga = injectSaga({ key: "bookingUtilityPage", saga });

export default compose(withReducer, withSaga, withConnect)(BookingUtilityPage);
