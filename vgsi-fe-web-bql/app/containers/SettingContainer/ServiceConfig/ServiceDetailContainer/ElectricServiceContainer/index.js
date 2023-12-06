/**
 *
 * ElectricServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectElectricServiceContainer from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../../components/Page/Page";
import { Menu } from "antd";
const { SubMenu } = Menu;

import { Switch, Route, withRouter, Redirect } from "react-router-dom";

import InfomationPage from "./InfomationPage/Loadable";
import SetupFeePage from "./SetupFee/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import messages from "../../messages";
import { injectIntl } from "react-intl";

import("./index.less");

const KEY_MENU = {
  "/main/setting/service/detail/electric/infomation":
    "/main/setting/service/detail/electric/infomation",
  "/main/setting/service/detail/electric/setup-fee":
    "/main/setting/service/detail/electric/setup-fee",
};
/* eslint-disable react/prefer-stateless-function */
export class ElectricServiceContainer extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }
  componentDidMount() {
    console.log(this.props);
    this.props.dispatch(fetchDetailService(`/${this.props.base_url}`));
  }

  handleClick = (e) => {
    console.log(`e.key`, e.key);
    this.props.history.push(e.key);
  };

  render() {
    const { electricServiceContainer } = this.props;
    const { loading, data } = electricServiceContainer;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Page className="ElectricServiceContainer">
        {!(loading || !data) && (
          <div className="main">
            <Menu
              onClick={this.handleClick}
              selectedKeys={[KEY_MENU[this.props.location.pathname]]}
              className="leftmenu"
              mode="vertical-left"
            >
              <Menu.Item key="/main/setting/service/detail/electric/infomation">
                {formatMessage(messages.information)}
              </Menu.Item>
              <Menu.Item key="/main/setting/service/detail/electric/setup-fee">
                {formatMessage(messages.settingFee)}
              </Menu.Item>
            </Menu>

            <div className="right">
              <Switch>
                <Route
                  exact
                  path="/main/setting/service/detail/electric/infomation"
                  component={InfomationPage}
                />
                <Route
                  exact
                  path="/main/setting/service/detail/electric/setup-fee"
                  component={SetupFeePage}
                />
                <Redirect to="/main/setting/service/detail/electric/infomation" />
              </Switch>
            </div>
          </div>
        )}
      </Page>
    );
  }
}

ElectricServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  electricServiceContainer: makeSelectElectricServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "electricServiceContainer", reducer });
const withSaga = injectSaga({ key: "electricServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(ElectricServiceContainer)));
