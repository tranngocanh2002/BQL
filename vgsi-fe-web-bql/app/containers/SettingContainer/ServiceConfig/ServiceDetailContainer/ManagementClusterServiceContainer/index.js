/**
 *
 * ManagementClusterServiceContainer
 *
 */

import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { createStructuredSelector } from "reselect";
import { compose } from "redux";

import injectSaga from "utils/injectSaga";
import injectReducer from "utils/injectReducer";
import makeSelectManagementClusterServiceContainer from "./selectors";
import reducer from "./reducer";
import saga from "./saga";
import Page from "../../../../../components/Page/Page";
import { Menu } from "antd";
import { Switch, Route, withRouter, Redirect } from "react-router-dom";
import InfomationPage from "./InfomationPage/Loadable";
import { defaultAction, fetchDetailService } from "./actions";
import messages from "../../messages";
import { injectIntl } from "react-intl";

import("./index.less");

const KEY_MENU = {
  "/main/setting/service/detail/apartment-fee/infomation":
    "/main/setting/service/detail/apartment-fee/infomation",
};
/* eslint-disable react/prefer-stateless-function */
export class ManagementClusterServiceContainer extends React.PureComponent {
  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }
  componentDidMount() {
    this.props.dispatch(fetchDetailService(`/${this.props.base_url}`));
  }

  handleClick = (e) => {
    this.props.history.push(e.key);
  };

  render() {
    const { managementClusterServiceContainer } = this.props;
    const { loading, data } = managementClusterServiceContainer;
    return (
      <Page className="ManagementClusterServiceContainer">
        {!(loading || !data) && (
          <div className="main">
            <Menu
              onClick={this.handleClick}
              selectedKeys={[KEY_MENU[this.props.location.pathname]]}
              mode="vertical-left"
              className="leftmenu"
            >
              <Menu.Item key="/main/setting/service/detail/apartment-fee/infomation">
                {this.props.intl.formatMessage(messages.information)}
              </Menu.Item>
            </Menu>
            <div className="right">
              <Switch>
                <Route
                  exact
                  path="/main/setting/service/detail/apartment-fee/infomation"
                  component={InfomationPage}
                />
                <Redirect to="/main/setting/service/detail/apartment-fee/infomation" />
              </Switch>
            </div>
          </div>
        )}
      </Page>
    );
  }
}

ManagementClusterServiceContainer.propTypes = {
  dispatch: PropTypes.func.isRequired,
};

const mapStateToProps = createStructuredSelector({
  managementClusterServiceContainer:
    makeSelectManagementClusterServiceContainer(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({
  key: "managementClusterServiceContainer",
  reducer,
});
const withSaga = injectSaga({ key: "managementClusterServiceContainer", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(withRouter(injectIntl(ManagementClusterServiceContainer)));
