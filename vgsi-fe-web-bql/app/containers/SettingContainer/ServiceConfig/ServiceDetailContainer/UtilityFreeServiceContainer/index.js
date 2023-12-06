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
import Page from "../../../../../components/Page/Page";
import { Menu, Icon } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";

import InfomationPage from "./InfomationPage/Loadable";
import ListUltilityPage from "./ListUltilityPage/Loadable";
import AddUltilityPage from "./AddUltilityPage/Loadable";
import DetailUltilityPage from "./DetailUltilityPage/Loadable";
import ConfigUtilityPage from "./ConfigUtilityPage/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import { injectIntl } from "react-intl";
import messages from "../../messages";

import("./index.less");

const KEY_MENU = {
  "/main/setting/service/detail/utility-free/infomation":
    "/main/setting/service/detail/utility-free/infomation",
  "/main/setting/service/detail/utility-free/list":
    "/main/setting/service/detail/utility-free/list",
  "/main/setting/service/detail/utility-free/add":
    "/main/setting/service/detail/utility-free/list",
  "/main/setting/service/detail/utility-free/edit":
    "/main/setting/service/detail/utility-free/list",
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
    if (pathname.startsWith("/main/setting/service/detail/utility-free/edit")) {
      pathname = "/main/setting/service/detail/utility-free/edit";
    }

    let isDetailUtility = pathname.startsWith(
      "/main/setting/service/detail/utility-free/detail/"
    );
    let id = "";
    if (isDetailUtility) {
      id = pathname
        .replace("/main/setting/service/detail/utility-free/detail/", "")
        .split("/")[0];
    }

    return (
      <Page className="UtilityFreeServiceContainer">
        {!(loading || !data) &&
          (!isDetailUtility ? (
            <div className="main">
              <Menu
                onClick={this.handleClick}
                selectedKeys={[KEY_MENU[pathname]]}
                mode="vertical-left"
                className="leftmenu"
              >
                <Menu.Item key="/main/setting/service/detail/utility-free/infomation">
                  {this.props.intl.formatMessage(messages.information)}
                </Menu.Item>
                <Menu.Item key="/main/setting/service/detail/utility-free/list">
                  {this.props.intl.formatMessage(messages.list)}
                </Menu.Item>
              </Menu>
              <div className="right">
                <Switch>
                  <Route
                    exact
                    path="/main/setting/service/detail/utility-free/infomation"
                    component={InfomationPage}
                  />
                  <Route
                    exact
                    path="/main/setting/service/detail/utility-free/list"
                    component={ListUltilityPage}
                  />
                  <Route
                    exact
                    path="/main/setting/service/detail/utility-free/add"
                    render={() => <AddUltilityPage key="addItem" />}
                  />
                  <Route
                    exact
                    path="/main/setting/service/detail/utility-free/edit/:id"
                    render={() => <AddUltilityPage key="editItem" />}
                  />
                  <Redirect to="/main/setting/service/detail/utility-free/infomation" />
                </Switch>
              </div>
            </div>
          ) : (
            <div className="main">
              <Menu
                onClick={this.handleClick}
                selectedKeys={[pathname]}
                mode="vertical-left"
                className="leftmenu"
              >
                <Menu.Item
                  key={"/main/setting/service/detail/utility-free/list"}
                >
                  <Icon type="left" />
                  {this.props.intl.formatMessage(messages.back)}
                </Menu.Item>
                <Menu.Item
                  key={`/main/setting/service/detail/utility-free/detail/${id}/info`}
                >
                  {this.props.intl.formatMessage(messages.description)}
                </Menu.Item>
                <Menu.Item
                  key={`/main/setting/service/detail/utility-free/detail/${id}/config`}
                >
                  {this.props.intl.formatMessage(messages.configuration)}
                </Menu.Item>
              </Menu>
              <div className="right">
                <Switch>
                  <Route
                    exact
                    path="/main/setting/service/detail/utility-free/detail/:id/info"
                    component={DetailUltilityPage}
                  />
                  <Route
                    exact
                    path="/main/setting/service/detail/utility-free/detail/:id/config"
                    component={ConfigUtilityPage}
                  />
                </Switch>
              </div>
            </div>
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
)(withRouter(injectIntl(UtilityFreeServiceContainer)));
