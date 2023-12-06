import React from "react";
import "./styles/index.css";
import _ from "lodash";
import { Row, Icon, Select, Col, Button, Input, Spin } from "antd";
import InputPositive from "./InputPositive";
// const VisaCardLogo = require('./svg/CreditCardVisaLogo.svg')
import messages from "../../containers/LucidContainer/LucidList/messages";
class LUCIDCard extends React.Component {
  constructor(props) {
    super(props);

    this.state = {
      creditCardNumber: !!props.card ? props.card.number || "" : "",
      creditCardHolderName: !!props.card
        ? props.card.resident_user_name || ""
        : "",
      id: !!props.card ? props.card.id : undefined,
      status: !!props.card ? props.card.status : undefined,
      map_service: !!props.card
        ? [...props.card.map_service].filter((tt) => tt.type != 0)
        : [],
      residentId: undefined,
      apartment_id: undefined,
      //Props
      frontCardColor: props.frontCardColor || "",
      backCardColor: props.backCardColor || "",
      submitBtnColor: props.submitBtnColor || "",
      submitBtnTxt: props.submitBtnTxt || "",
      loading: true,
    };

    if (props.card)
      !!props.fetchVehicle && props.fetchVehicle(props.card.apartment_id);

    //event handlers
    this.cardNumberOnChange = this.cardNumberOnChange.bind(this);
    this.cardHolderNameOnChange = this.cardHolderNameOnChange.bind(this);
    this.monthOnChange = this.monthOnChange.bind(this);
    this.yearOnChange = this.yearOnChange.bind(this);

    this.handleSubmit = this.handleSubmit.bind(this);

    this._onSearch = _.debounce(this.onSearch, 300);
    this._onSearchApartment = _.debounce(this.onSearchApartment, 300);
  }

  componentDidMount() {
    this._onSearchApartment("");
  }

  onSearch = (keyword) => {
    !!this.props.searchResident &&
      this.props.searchResident({
        name: keyword,
        apartment_id: this.state.apartment_id,
      });
  };

  onSearchApartment = (keyword) => {
    !!this.props.searchApartment && this.props.searchApartment(keyword);
  };
  handleSubmit = (eventOfSubmission) => {
    eventOfSubmission.stopPropagation();
    eventOfSubmission.preventDefault();

    let creditCardData = {
      creditCardNumber: this.state.creditCardNumber,
      creditCardHolderName: this.state.creditCardHolderName,
      creditCardExpMonth: this.state.creditCardExpMonth,
      creditCardExpYear: this.state.creditCardExpYear,
      creditCardCvv: this.state.creditCardCvv,
    };

    _.invoke(this.props, "onSubmit", eventOfSubmission, creditCardData);
  };

  cardNumberOnChange = (eventCreditCardNumberChangeEvent) => {
    let creditCardNumber = eventCreditCardNumberChangeEvent || "";

    this.setState({ creditCardNumber });
  };

  cardHolderNameOnChange = (eventCreditCardHolderNameChangeEvent) => {
    let creditCardHolderName =
      eventCreditCardHolderNameChangeEvent.target.value || "";

    this.setState({ creditCardHolderName });
  };

  monthOnChange = (eventCreditCardMonthChangeEvent) => {
    let creditCardExpMonth = eventCreditCardMonthChangeEvent.target.value || "";

    this.setState({ creditCardExpMonth });
  };

  yearOnChange = (eventCreditCardYearChangeEvent) => {
    let creditCardExpYear = eventCreditCardYearChangeEvent.target.value || "";

    this.setState({ creditCardExpYear });
  };

  componentWillReceiveProps(nextProps) {
    if (nextProps.visibleCard != this.props.visibleCard) {
      if (!!nextProps.card && this.props.card != nextProps.card) {
        this.setState({
          creditCardNumber: nextProps.card.number || "",
          creditCardHolderName: nextProps.card.resident_user_name || "",
          id: nextProps.card.id,
          map_service: nextProps.card.map_service.filter((tt) => tt.type != 0),
          status: nextProps.card.status,
          residentId: undefined,
          apartment_id: undefined,
        });
        !!this.props.fetchVehicle &&
          this.props.fetchVehicle(nextProps.card.apartment_id);
      } else {
        this.setState({
          creditCardNumber: "",
          creditCardHolderName: "",
          id: undefined,
          map_service: [],
          status: -1,
          residentId: undefined,
          apartment_id: undefined,
        });
      }
    }
  }

  render() {
    const {
      id,
      map_service,
      creditCardNumber,
      creditCardHolderName,
      status,
      residentId,
      apartment_id,
    } = this.state;
    const { residents, aparment, vehicle, loading, formatMessage } = this.props;
    const currentResident =
      !!residents && residents.lst.find((ss) => ss.id == residentId);
    const currentApartment =
      !!aparment && aparment.lst.find((ss) => ss.id == apartment_id);
    return (
      <div className="checkout react-fancy-visa-card">
        <div className={"credit-card-box"}>
          <div className="flip">
            <div
              className="front"
              style={{ background: this.state.frontCardColor }}
            >
              <div className="chip">
                {/* <img src={require('../../images/logo.svg')} /> */}
              </div>

              <div className="logo" />

              <div className="number">
                {this.state.creditCardNumber
                  .replace(/(\d{4})/g, "$1 ")
                  .replace(/(^\s+|\s+$)/, "")}
              </div>

              <div className="card-holder">
                <label>{formatMessage(messages.cardholder)} </label>
                <div>
                  {`${
                    !!currentResident
                      ? currentResident.first_name
                      : this.state.creditCardHolderName
                  }`.toUpperCase()}
                </div>
              </div>
              <div className="card-expiration-date">
                <label>{formatMessage(messages.service)}</label>
                <ul style={{ fontSize: 14 }}>
                  {(map_service || [])
                    .filter((ss) => ss.type != 0)
                    .map((ser, index) => {
                      return <li key={`sdfsdfd-${index}`}>{ser.type_name}</li>;
                    })}
                </ul>
              </div>
            </div>
          </div>
        </div>
        <form
          className="form"
          autoComplete="off"
          noValidate
          onSubmitCapture={this.handleSubmit}
        >
          <fieldset>
            <label htmlFor="card-number">
              {formatMessage(messages.cardNum)}
            </label>
            <InputPositive
              onChange={this.cardNumberOnChange}
              maxLength={16}
              id="card-number"
              className="input-cart-number"
              value={creditCardNumber}
            />
          </fieldset>
          <fieldset>
            <label htmlFor="card-holder">
              {formatMessage(messages.apartment)}
            </label>
            {!!aparment && (
              <Select
                style={{ width: "100%" }}
                loading={aparment.loading}
                showSearch
                placeholder={formatMessage(messages.choseApartment)}
                optionFilterProp="children"
                notFoundContent={
                  aparment.loading ? <Spin size="small" /> : null
                }
                onSearch={this._onSearchApartment}
                onChange={(value, opt) => {
                  this.setState(
                    {
                      apartment_id: value,
                      map_service: [],
                      residentId: undefined,
                    },
                    () => {
                      if (!!value) {
                        !!this.props.fetchVehicle &&
                          this.props.fetchVehicle(value);
                        this._onSearch("");
                      }
                    }
                  );
                  if (!opt) {
                    this._onSearchApartment("");
                  }
                }}
                allowClear
                value={apartment_id}
              >
                {aparment.lst.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}`}
                    >{`${gr.name} (${gr.parent_path})`}</Select.Option>
                  );
                })}
              </Select>
            )}
            {!!!aparment && this.props.card && (
              <Input
                onChange={this.cardHolderNameOnChange}
                type="text"
                id="card-holder"
                disabled={true}
                value={`${this.props.card.apartment_name} (${this.props.card.apartment_parent_path})`}
              />
            )}
          </fieldset>
          {(!!!aparment || !!currentApartment) && (
            <fieldset>
              <label htmlFor="card-holder">
                {formatMessage(messages.cardholder)}
              </label>
              {!!residents && (
                <Select
                  style={{ width: "100%" }}
                  loading={residents.loading}
                  showSearch
                  placeholder={formatMessage(messages.choseResident)}
                  optionFilterProp="children"
                  notFoundContent={
                    residents.loading ? <Spin size="small" /> : null
                  }
                  onSearch={this._onSearch}
                  onChange={(value) => {
                    this.setState({
                      residentId: value,
                    });
                  }}
                  allowClear
                  value={residentId}
                >
                  {residents.lst.map((gr) => {
                    return (
                      <Select.Option
                        key={`group-${gr.id}`}
                        value={`${gr.id}`}
                      >{`${gr.first_name} (${gr.phone})`}</Select.Option>
                    );
                  })}
                </Select>
              )}
              {!!!residents && (
                <Input
                  onChange={this.cardHolderNameOnChange}
                  type="text"
                  id="card-holder"
                  disabled={true}
                  value={creditCardHolderName}
                />
              )}
            </fieldset>
          )}
          {(!!apartment_id || !!!aparment) && (
            <fieldset>
              <Row type="flex" justify="space-between">
                <label htmlFor="card-holder">
                  {formatMessage(messages.service)}
                </label>
                {!(map_service || []).some((ss) => ss.type == 1) && (
                  <Icon
                    style={{ cursor: "pointer" }}
                    type="plus-circle"
                    onClick={(e) => {
                      this.setState({
                        map_service: (map_service || []).concat([
                          {
                            type: 1,
                            type_name: formatMessage(messages.parking),
                          },
                        ]),
                      });
                    }}
                  />
                )}
              </Row>
              {(map_service || [])
                .filter((ss) => ss.type != 0)
                .map((ser, index) => {
                  return (
                    <Row
                      type="flex"
                      justify="space-between"
                      key={`ser--${index}`}
                      style={{ marginTop: 8 }}
                    >
                      <Col span={10}>
                        <Select
                          placeholder={formatMessage(messages.serviceType)}
                          style={{ width: "100%" }}
                          value={ser.type}
                        >
                          <Select.Option value={1}>
                            {formatMessage(messages.parking)}
                          </Select.Option>
                        </Select>
                      </Col>
                      <Col span={10} offset={1}>
                        <Select
                          placeholder={formatMessage(messages.licensePlates)}
                          style={{ width: "100%" }}
                          value={ser.service_management_id}
                          onChange={(value) => {
                            let newmap_service = [...map_service];
                            newmap_service[index].service_management_id = value;
                            this.setState(
                              {
                                map_service: newmap_service,
                              },
                              () => {
                                console.log(
                                  `map_service`,
                                  this.state.map_service
                                );
                              }
                            );
                          }}
                        >
                          {vehicle.lst
                            .filter((vv) => {
                              if (vv.is_map_card != 1) return true;
                              if (!!this.props.card) {
                                let current = this.props.card.map_service.find(
                                  (tt) => tt.type == 1
                                );
                                if (
                                  !!current &&
                                  current.service_management_id == vv.id
                                ) {
                                  return true;
                                }
                              }
                              return false;
                            })
                            .map((mm) => {
                              return (
                                <Select.Option
                                  key={`vehicle--${mm.id}`}
                                  value={mm.id}
                                >
                                  {`BS: ${mm.number}`}
                                </Select.Option>
                              );
                            })}
                        </Select>
                      </Col>
                      <Col span={2} offset={1}>
                        <Row
                          style={{ width: "100%", height: "100%" }}
                          type="flex"
                          align="middle"
                          justify="end"
                        >
                          <Icon
                            style={{ cursor: "pointer", color: "red" }}
                            type="close-circle"
                            onClick={() => {
                              let newmap_service = [...map_service];
                              newmap_service.splice(index, 1);
                              this.setState({
                                map_service: newmap_service,
                              });
                            }}
                          />
                        </Row>
                      </Col>
                    </Row>
                  );
                })}
            </fieldset>
          )}
          <Row
            style={{
              marginTop: 16,
            }}
          >
            {id != undefined && status != 1 && (
              <Button
                type="primary"
                block
                size="large"
                onClick={(e) => {
                  e.preventDefault();
                  !!this.props.approve &&
                    this.props.approve({
                      id,
                      map_service,
                      creditCardNumber,
                      creditCardHolderName,
                    });
                }}
                loading={loading}
                disabled={creditCardNumber.length == 0}
              >
                {formatMessage(messages.active)}
              </Button>
            )}
            {id != undefined && status == 1 && (
              <Button
                type="primary"
                block
                size="large"
                onClick={(e) => {
                  e.preventDefault();
                  !!this.props.onUpdate &&
                    this.props.onUpdate({
                      id,
                      map_service,
                      creditCardNumber,
                      creditCardHolderName,
                    });
                }}
                loading={loading}
                disabled={creditCardNumber.length == 0}
              >
                {formatMessage(messages.update)}
              </Button>
            )}
            {!id && (
              <Button
                type="primary"
                block
                size="large"
                onClick={(e) => {
                  e.preventDefault();
                  !!this.props.onCreate &&
                    this.props.onCreate({
                      map_service,
                      creditCardNumber,
                      resident: currentResident,
                      apartment_id,
                    });
                }}
                loading={loading}
                disabled={
                  creditCardNumber.length == 0 ||
                  !currentResident ||
                  !apartment_id
                }
              >
                {formatMessage(messages.createNew)}
              </Button>
            )}
          </Row>
        </form>
      </div>
    );
  }
}
export default LUCIDCard;
