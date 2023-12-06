/**
 *
 * WaterServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage, injectIntl } from "react-intl";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectWaterServiceContainer from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../../components/Page/Page";
import { Menu, Button, Row } from "antd";
const { SubMenu } = Menu;

import { Switch, Route, withRouter, Redirect } from "react-router-dom";

import InfomationPage from "./InfomationPage/Loadable";
import SetupFeePage from "./SetupFee/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import messages from "../../messages";

import("./index.less");

const KEY_MENU = {
  "/main/setting/service/detail/water/infomation":
    "/main/setting/service/detail/water/infomation",
  "/main/setting/service/detail/water/setup-fee":
    "/main/setting/service/detail/water/setup-fee",
};
/* eslint-disable react/prefer-stateless-function */
export class WaterServiceContainer extends React.PureComponent {
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
    const { waterServiceContainer } = this.props;
    const { loading, data } = waterServiceContainer;
    return (
      // ={loading || !!!data}
      <Page className="WaterServiceContainer">
        {!(loading || !data) && (
          <div className="main">
            {/* <Row type='flex' style={{ width: '100%', position: 'relative' }}  > */}
            <Menu
              onClick={this.handleClick}
              selectedKeys={[KEY_MENU[this.props.location.pathname]]}
              className="leftmenu"
              mode="vertical-left"
            >
              <Menu.Item key="/main/setting/service/detail/water/infomation">
                {this.props.intl.formatMessage(messages.information)}
              </Menu.Item>
              <Menu.Item key="/main/setting/service/detail/water/setup-fee">
                {this.props.intl.formatMessage(messages.settingFee)}
              </Menu.Item>
            </Menu>
            <div className="right">
              <Switch>
                <Route
                  exact
                  path="/main/setting/service/detail/water/infomation"
                  component={InfomationPage}
                />
                <Route
                  exact
                  path="/main/setting/service/detail/water/setup-fee"
                  component={SetupFeePage}
                />
                <Redirect to="/main/setting/service/detail/water/infomation" />
              </Switch>
            </div>
          </div>
        )}
      </Page>
    );
  }
}

WaterServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  waterServiceContainer: makeSelectWaterServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "waterServiceContainer", reducer });
const withSaga = injectSaga({ key: "waterServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(WaterServiceContainer)));
