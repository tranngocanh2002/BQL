/**
 *
 * UtilityFreeServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectUtilityFreeServiceContainer from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../components/Page/Page";
import { Menu, Icon, Row, Col } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";

import ListUltilityPage from "./ListUltilityPage/Loadable";
import AddUltilityPage from "./AddUltilityPage/Loadable";
import ServiceBookingFeeList from "./ServiceBookingFeeList/Loadable";
import BookingUtilityPage from "./BookingUtilityPage/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import { FormattedMessage } from "react-intl";
import messages from "./messages";

import("./index.less");

const KEY_MENU = {
  "/main/service/detail/utility-free/list":
    "/main/service/detail/utility-free/list",
  "/main/service/detail/utility-free/add":
    "/main/service/detail/utility-free/list",
  "/main/service/detail/utility-free/edit":
    "/main/service/detail/utility-free/list",
  "/main/service/detail/utility-free/booking-fee-list":
    "/main/service/detail/utility-free/booking-fee-list",
};

/* eslint-disable react/prefer-stateless-function */
export class UtilityFreeServiceContainer extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }
  componentDidMount() {
    console.log(this.props);
    this.props.dispatch(fetchDetailService(`/${this.props.base_url}`));
  }

  handleClick = (e) => {
    console.log("e.key", e.key);
    this.props.history.push(e.key);
  };

  render() {
    const { utilityFreeServiceContainer } = this.props;
    const { loading, data } = utilityFreeServiceContainer;
    let pathname = this.props.location.pathname;
    if (pathname.startsWith("/main/service/detail/utility-free/edit")) {
      pathname = "/main/service/detail/utility-free/edit";
    }

    let isDetailUtility = pathname.startsWith(
      "/main/service/detail/utility-free/detail/"
    );
    let id = "";
    if (isDetailUtility) {
      id = pathname
        .replace("/main/service/detail/utility-free/detail/", "")
        .split("/")[0];
    }

    return (
      <Page className="UtilityFreeServiceContainer">
        {!(loading || !data) &&
          (!isDetailUtility ? (
            <Row
              gutter={30}
              style={{
                marginTop: 20,
              }}
            >
              <Col md={5} lg={4}>
                <Menu
                  onClick={this.handleClick}
                  selectedKeys={[KEY_MENU[pathname]]}
                  mode="vertical-left"
                  className="leftmenu"
                >
                  {/* <Menu.Item key="/main/service/detail/utility-free/list">
                  Danh sách
                </Menu.Item> */}
                  <Menu.Item key="/main/service/detail/utility-free/booking-fee-list">
                    <FormattedMessage {...messages.feeList} />
                  </Menu.Item>
                </Menu>
              </Col>

              <Col md={19} lg={20}>
                <Switch>
                  {/* <Route
                    exact
                    path="/main/service/detail/utility-free/list"
                    component={ListUltilityPage}
                  /> */}
                  <Route
                    exact
                    path="/main/service/detail/utility-free/booking-fee-list"
                    component={ServiceBookingFeeList}
                  />
                  <Route
                    exact
                    path="/main/service/detail/utility-free/add"
                    render={() => <AddUltilityPage key="addItem" />}
                  />
                  <Route
                    exact
                    path="/main/service/detail/utility-free/edit/:id"
                    render={() => <AddUltilityPage key="editItem" />}
                  />
                  <Redirect to="/main/service/detail/utility-free/booking-fee-list" />
                </Switch>
              </Col>
            </Row>
          ) : (
            <Row
              gutter={30}
              style={{
                marginTop: 20,
              }}
            >
              <Col md={5} lg={4}>
                <Menu
                  onClick={this.handleClick}
                  selectedKeys={[pathname]}
                  mode="vertical-left"
                  className="leftmenu"
                >
                  <Menu.Item key={"/main/service/detail/utility-free/list"}>
                    <Icon type="left" />
                    <FormattedMessage {...messages.back} />
                  </Menu.Item>
                  <Menu.Item
                    key={`/main/service/detail/utility-free/detail/${id}/booking`}
                  >
                    Booking
                  </Menu.Item>
                </Menu>
              </Col>

              <Col md={19} lg={20}>
                <Switch>
                  <Route
                    exact
                    path="/main/service/detail/utility-free/detail/:id/booking"
                    component={BookingUtilityPage}
                  />
                </Switch>
              </Col>
            </Row>
          ))}
      </Page>
    );
  }
}

UtilityFreeServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  utilityFreeServiceContainer: makeSelectUtilityFreeServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "utilityFreeServiceContainer",
  reducer,
});
const withSaga = injectSaga({ key: "utilityFreeServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(UtilityFreeServiceContainer));
