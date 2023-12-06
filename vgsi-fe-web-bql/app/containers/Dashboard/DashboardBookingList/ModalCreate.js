import React from "react";
import { FormattedMessage, injectIntl } from "react-intl";

import messages, { scope } from "./../messages";
import {
  Modal,
  Input,
  Form,
  Select,
  Spin,
  DatePicker,
  Checkbox,
  Col,
  Typography,
} from "antd";

import config from "../../../utils/config";

import {
  fetchAllConfig,
  fetchApartmentAction,
  fetchServiceFreeAction,
  fetchSlotFree,
} from "./actions";
import moment from "moment";
import { formatPrice, notificationBar } from "utils";
import { Row } from "antd/es/grid";
import InputNumberFormat from "components/InputNumberFormat";
import { log } from "lodash-decorators/utils";
import("./index.less");

const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 12,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalCreate extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      currentSelected: 0,
      time_use: [],
      book_time: null,
      total_adult: 0,
      currentService: null,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
    this._onSearchService = _.debounce(this.onSearchService, 300);
    this._onSearchConfig = _.debounce(this.onSearchConfig, 300);
  }

  handlerUpdate = () => {
    const { form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }

      this.props.handlerAddMember && this.props.handlerAddMember(values);
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visibleCreate != nextProps.visibleCreate) {
      this.props.form.resetFields();
    }
  }

  componentDidMount() {
    this.props.dispatch(fetchApartmentAction());
    this.props.dispatch(fetchServiceFreeAction());
  }

  onSearch = (keyword) => {
    this.props.dispatch(fetchApartmentAction({ name: keyword }));
  };
  onSearchService = (keyword) => {
    this.props.dispatch(fetchServiceFreeAction({ keyword }));
  };
  onSearchConfig = (keyword) => {
    this.props.dispatch(fetchAllConfig({ keyword }));
  };

  onSelectService = (value) => {
    this.props.dispatch(fetchAllConfig(value));
  };

  handleChangeConfig = (e) => {
    this.setState({ currentSelected: e });
  };

  onSelectConfig = (value, time) => {
    this.props.dispatch(
      fetchSlotFree({
        service_utility_config_id: value,
        current_time: moment(time).startOf("day").unix(),
      })
    );
  };

  _onSave = () => {
    const { form, intl } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        console.log("errors", errors);
      } else {
        if (!values.time_use) {
          notificationBar(
            intl.formatMessage({ id: `${scope}.bookingValid` }),
            "warning"
          );
        } else {
          let apartment_id = values.apartment_id.split(":");
          let booktime = values.time_use.map((mm) => {
            let mmSplit = mm.split("-");
            return {
              start_time: moment(
                `${values.book_time.format("YYYY-MM-DD")} ${mmSplit[0]}`,
                "YYYY-MM-DD HH:mm"
              ).unix(),
              end_time: moment(
                `${values.book_time.format("YYYY-MM-DD")} ${mmSplit[1]}`,
                "YYYY-MM-DD HH:mm"
              ).unix(),
            };
          });
          this.props.createBooking &&
            this.props.createBooking({
              ...values,
              apartment_id: apartment_id[0],
              book_time: booktime,
            });
        }
      }
    });
  };

  handleDirectReception = async () => {
    const { bookingList } = this.props;
    const { create } = bookingList;
    try {
      if (create.data2) {
        let res = await window.connection.changeStatusBookingUtility({
          service_map_management_id: create.data2.service_map_management_id,
          is_active_all: 0,
          is_active_array: [create.data2.id],
        });
        if (res.success) {
          this.props.history.push("/main/finance/reception", {
            payment_gen_code: undefined,
            apartment_id: create.data2.apartment_id,
            ids: create.data2.service_payment_total_ids,
            limit_payment: true,
          });
        }
      }
    } catch (error) {}
  };

  handleDirectBookingList = async () => {
    const { bookingList } = this.props;
    const { create } = bookingList;
    try {
      if (create.data2) {
        let res = await window.connection.changeStatusBookingUtility({
          service_map_management_id: create.data2.service_map_management_id,
          is_active_all: 0,
          is_active_array: [create.data2.id],
        });
        if (res.success) {
          this.props.history.push("/main/bookinglist");
        }
      }
    } catch (error) {}
  };

  isBefore = (currentTime, startTime) => {
    const { book_time } = this.state;
    if (!book_time.isSame(currentTime, "day")) {
      return true;
    }
    let start = moment(startTime, "H:mm");
    let current = moment(currentTime, "H:mm");
    return current.isBefore(start);
  };

  render() {
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    const { bookingList, intl, visibleCreate, setState } = this.props;
    const { currentSelected, currentService, book_time } = this.state;
    const { apartments, services, freeSlot, create, loading, data2, data } =
      bookingList;

    const currentConfig = data2.find(
      (element) => element.id == currentSelected
    );
    if (create.success) {
      if (currentConfig.type == 1) {
        this.handleDirectReception();
      } else {
        this.handleDirectBookingList();
      }
    }

    const slots =
      freeSlot[
        `slot-${getFieldValue("service_utility_config_id")}-${moment(
          getFieldValue("book_time")
        )
          .startOf("day")
          .unix()}`
      ];
    const choseDateText = intl.formatMessage({
      ...messages.choseDate,
    });
    return (
      <Modal
        title={<FormattedMessage {...messages.utility} />}
        visible={visibleCreate}
        onOk={this._onSave}
        onCancel={() => {
          setState({
            visibleCreate: false,
          });
        }}
        okText={<FormattedMessage {...messages.createNew} />}
        cancelText={<FormattedMessage {...messages.cancel} />}
      >
        <Form {...formItemLayout} className="notificationCategoryPage">
          <Form.Item
            label={<FormattedMessage {...messages.apartment} />}
            style={{ marginTop: 0, marginBottom: 24 }}
          >
            {getFieldDecorator("apartment_id", {
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.emptyApartmentError} />
                  ),
                  whitespace: true,
                },
                {
                  validator: (rule, value, callback) => {
                    const form = this.props.form;
                    if (value) {
                      let values = value.split(":");
                      if (values.length == 2 && values[1] == 0) {
                        callback(
                          <FormattedMessage {...messages.noOwnerApartment} />
                        );
                      } else {
                        callback();
                      }
                    } else {
                      callback();
                    }
                  },
                },
              ],
            })(
              <Select
                style={{ width: "100%" }}
                loading={apartments.loading}
                showSearch
                placeholder={<FormattedMessage {...messages.choseApartment} />}
                optionFilterProp="children"
                notFoundContent={
                  apartments.loading ? <Spin size="small" /> : null
                }
                onSearch={this._onSearch}
                onChange={(value, opt) => {
                  if (!opt) {
                    this._onSearch("");
                  }
                }}
                allowClear
              >
                {apartments.lst.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}:${gr.status}`}
                    >{`${gr.name} (${gr.parent_path})${
                      gr.status == 0
                        ? ` - ${this.props.intl.formatMessage(messages.empty)}`
                        : ""
                    }`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.utilities} />}
            style={{ marginTop: 0, marginBottom: 24 }}
          >
            {getFieldDecorator("service_utility_free_id", {
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.emptyUtilitiesError} />
                  ),
                  whitespace: true,
                },
              ],
            })(
              <Select
                style={{ width: "100%" }}
                loading={services.loading}
                showSearch
                placeholder={<FormattedMessage {...messages.choseUtilities} />}
                optionFilterProp="children"
                notFoundContent={
                  services.loading ? <Spin size="small" /> : null
                }
                onSearch={this._onSearchService}
                onSelect={(value, option) => {
                  this.setState({
                    currentService: services.lst.find(
                      (item) => item.id == value
                    ),
                  });
                  this.onSelectService(value);
                }}
                allowClear
              >
                {services.lst
                  .filter((ser) => ser.status === 1)
                  .map((gr) => {
                    return (
                      <Select.Option
                        key={`service-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${
                        this.props.language === "vi" ? gr.name : gr.name_en
                      }`}</Select.Option>
                    );
                  })}
              </Select>
            )}
          </Form.Item>

          <Form.Item
            label={<FormattedMessage {...messages.date} />}
            style={{ marginTop: 0, marginBottom: 24 }}
          >
            {getFieldDecorator("book_time", {
              rules: [
                {
                  type: "object",
                  required: true,
                  whitespace: true,
                  message: <FormattedMessage {...messages.emptyDateError} />,
                },
              ],
            })(
              <DatePicker
                style={{ width: "100%" }}
                placeholder={choseDateText}
                format="DD/MM/YYYY"
                onChange={(e) => {
                  this.setState({ book_time: e });
                  setFieldsValue({
                    service_utility_config_id: null,
                  });
                }}
                disabledDate={(current) => {
                  // Can not select days before today and today
                  return current && current < moment().startOf("day");
                }}
              />
            )}
          </Form.Item>

          {!!data2.length && book_time && (
            <Form.Item
              label={<FormattedMessage {...messages.area} />}
              style={{ marginTop: 0, marginBottom: 24 }}
            >
              {getFieldDecorator("service_utility_config_id", {
                rules: [
                  {
                    required: true,
                    message: <FormattedMessage {...messages.emptyAreaError} />,
                    whitespace: true,
                  },
                ],
              })(
                <Select
                  style={{ width: "100%" }}
                  loading={loading}
                  showSearch
                  placeholder={<FormattedMessage {...messages.choseArea} />}
                  optionFilterProp="children"
                  notFoundContent={loading ? <Spin size="small" /> : null}
                  onSearch={this._onSearchConfig}
                  onSelect={(value) => {
                    this.onSelectConfig(value, getFieldValue("book_time"));
                  }}
                  onChange={(value) => {
                    setFieldsValue({
                      time_use: [],
                    });
                    this.handleChangeConfig(value);
                  }}
                  allowClear
                >
                  {data2.map((gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${
                        this.props.language === "vi" ? gr.name : gr.name_en
                      }`}</Select.Option>
                    );
                  })}
                </Select>
              )}
            </Form.Item>
          )}

          {!!slots && (
            <Form.Item
              label={<FormattedMessage {...messages.usedTime} />}
              style={{ marginTop: 0, marginBottom: 20 }}
            >
              {getFieldDecorator("time_use", {
                initialValue: [],
                rules: [
                  {
                    type: "array",
                    required: true,
                    message: <FormattedMessage {...messages.usedTimeError} />,
                  },
                ],
              })(
                <Checkbox.Group
                  style={{ width: "100%" }}
                  onChange={(e) => this.setState({ time_use: e })}
                >
                  <Row>
                    {slots.items.length ? (
                      slots.items.map((ii, i) => {
                        return (
                          <Col
                            span={12}
                            key={`row-${i}`}
                            style={{ marginTop: 10 }}
                          >
                            <Checkbox
                              disabled={
                                ii.slot_null == 0 ||
                                !this.isBefore(moment(), ii.start_time)
                              }
                              value={`${ii.start_time}-${ii.end_time}`}
                            >
                              {`${ii.start_time} - ${ii.end_time} `}(
                              <span
                                style={{
                                  fontWeight: "bold",
                                  color: "black",
                                }}
                              >
                                {ii.slot_null}
                              </span>{" "}
                              <FormattedMessage {...messages.empty} />)
                            </Checkbox>
                          </Col>
                        );
                      })
                    ) : (
                      <span>
                        <FormattedMessage {...messages.noTime} />
                      </span>
                    )}
                  </Row>
                </Checkbox.Group>
              )}
            </Form.Item>
          )}

          {/* <Form.Item
            label={<FormattedMessage {...messages.numberPeople} />}
            style={{ marginTop: 0, marginBottom: 24 }}
          >
            {getFieldDecorator("total_adult", {
              initialValue: 1,
              rules: [
                {
                  required: true,
                  message: <FormattedMessage {...messages.numberPeopleError} />,
                  whitespace: true,
                  type: "number",
                  min: 1,
                },
              ],
            })(
              <InputNumberFormat
                style={{ width: "100%" }}
                min={1}
                maxLength={10}
                onChange={(e) => this.setState({ total_adult: e })}
              />
            )}
          </Form.Item> */}
          <Form.Item
            label={<FormattedMessage {...messages.note} />}
            style={{ marginTop: 0, marginBottom: 24 }}
          >
            {getFieldDecorator("description", {
              initialValue: "",
            })(
              <Input.TextArea
                maxLength={1000}
                style={{ width: "100%" }}
                rows={4}
              />
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.explain} />}
            style={{ marginTop: 0, marginBottom: 24 }}
          >
            {/* <Typography style={{ fontWeight: "bold" }}>
                  <FormattedMessage {...messages.totalMoney} /> ={" "}
                  {!!price.data2 ? formatPrice(price.data2.price) : 0} đ
                </Typography> */}
            <Typography style={{ fontWeight: "bold" }}>
              <FormattedMessage {...messages.deposit} /> ={" "}
              {currentService ? formatPrice(currentService.deposit_money) : 0} đ
            </Typography>
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalCreate);
